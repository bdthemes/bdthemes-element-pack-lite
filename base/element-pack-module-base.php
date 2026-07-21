<?php

namespace ElementPack\Base;

use Elementor\Core\Base\Module;
use Elementor\Plugin;
use ElementPack\Element_Pack_Loader;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

abstract class Element_Pack_Module_Base extends Module {

    public function get_widgets() {
        return [];
    }

    public function __construct() {
        add_action('elementor/widgets/register', [$this, 'init_widgets']);
    }


    public function init_widgets() {

        $widget_manager = Element_Pack_Loader::elementor()->widgets_manager;

        foreach ($this->get_widgets() as $widget) {
            $class_name = $this->get_reflection()->getNamespaceName() . '\Widgets\\' . $widget;

            $widget_manager->register(new $class_name());
        }
    }

    private function find_element_recursive($elements, $form_id) {

        foreach ($elements as $element) {
            if ($form_id === $element['id']) {
                return $element;
            }

            if (!empty($element['elements'])) {
                $element = $this->find_element_recursive($element['elements'], $form_id);

                if ($element) {
                    return $element;
                }
            }
        }

        return false;
    }

    /**
     * @param $post_id | page id or post id
     * @param $widget_id | elementor widget ids
     */
    public function get_widget_settings($post_id, $widget_id) {
        if (!$post_id || !$widget_id) {
            return "Invalid request";
        }

        $elementor = Plugin::$instance;
        $pageMeta  = $elementor->documents->get($post_id);

        if (!$pageMeta) {
            return "Invalid Post or Page ID";
        }
        $metaData = $pageMeta->get_elements_data();
        if (!$metaData) {
            return "Page page is not under elementor";
        }

        $widget_data = $this->find_element_recursive($metaData, $widget_id);
        $settings    = [];


        if (is_array($widget_data)) {
            $widget   = $elementor->elements_manager->create_element_instance($widget_data);
            $settings = $widget->get_settings();
        }

        return $settings;
    }

    /**
     * Whether reCAPTCHA credentials are configured site wide.
     *
     * @return bool
     */
    protected function has_recaptcha_keys() {
        $api_settings = get_option('element_pack_api_settings');

        return !empty($api_settings['recaptcha_site_key']) && !empty($api_settings['recaptcha_secret_key']);
    }

    /**
     * Whether the reCAPTCHA gate must run for the current form submission.
     *
     * get_widget_settings() yields an error string or an empty array whenever the
     * widget cannot be located. That happens in two very different situations:
     * an unauthenticated caller posting a bogus widget_id, and the legitimate case
     * of a form rendered inside a template, popup or theme-builder location, where
     * the posted page_id is the viewed post rather than the document that owns the
     * widget.
     *
     * Reading the flag straight off that unresolved result made both cases
     * indistinguishable from "reCAPTCHA disabled", so the check was skipped
     * entirely. An unresolved lookup now falls back to whether the site has
     * reCAPTCHA set up at all: sites with keys get the check, sites without keys
     * behave exactly as before.
     *
     * @param int    $post_id     Posted page id.
     * @param string $widget_id   Posted widget id.
     * @param string $setting_key Widget control holding the on/off flag.
     * @return bool
     */
    protected function is_recaptcha_required($post_id, $widget_id, $setting_key) {
        $settings = $this->get_widget_settings($post_id, $widget_id);

        if (is_array($settings) && !empty($settings)) {
            return isset($settings[$setting_key]) && 'yes' === $settings[$setting_key];
        }

        return $this->has_recaptcha_keys();
    }
}
