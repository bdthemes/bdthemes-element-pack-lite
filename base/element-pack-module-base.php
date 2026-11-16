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
    /**
     * Saved settings of the form widget a front-end request names, or null.
     *
     * The page and widget IDs are posted by the browser, so they are only trusted when they name a
     * widget of $widget_type on a page the visitor can view (published and not password
     * protected) or the current user can edit. An Elementor Pro global widget resolves to the
     * template it references.
     *
     * @param int|string $post_id     Page, post or template ID from the request.
     * @param mixed      $widget_id   Elementor element ID from the request.
     * @param string     $widget_type Expected widget name, e.g. 'bdt-contact-form'.
     * @return array|null
     */
    protected function get_request_form_settings($post_id, $widget_id, $widget_type) {
        $post_id   = absint($post_id);
        $widget_id = is_scalar($widget_id) ? (string) $widget_id : '';

        if (!$post_id || '' === $widget_id || !get_post($post_id)) {
            return null;
        }

        $viewable = 'publish' === get_post_status($post_id) && !post_password_required($post_id);
        if (!$viewable && !current_user_can('edit_post', $post_id)) {
            return null;
        }

        $document = Plugin::$instance->documents->get($post_id);
        if (!$document) {
            return null;
        }

        $element = $this->find_element_recursive((array) $document->get_elements_data(), $widget_id);

        if ($element && 'global' === ($element['widgetType'] ?? '') && !empty($element['templateID'])) {
            $global  = Plugin::$instance->documents->get(absint($element['templateID']));
            $data    = $global ? (array) $global->get_elements_data() : [];
            $element = !empty($data[0]) ? array_merge($data[0], ['id' => $widget_id]) : null;
        }

        if (!$element || $widget_type !== ($element['widgetType'] ?? '')) {
            return null;
        }

        $widget = Plugin::$instance->elements_manager->create_element_instance($element);

        return $widget ? (array) $widget->get_settings() : null;
    }

    protected function has_recaptcha_keys() {
        $api_settings = get_option('element_pack_api_settings');

        return !empty($api_settings['recaptcha_site_key']) && !empty($api_settings['recaptcha_secret_key']);
    }

    /**
     * `assets` argument for an extension's on/off switcher control.
     *
     * Elementor records the handles a document needs in `_elementor_page_assets`
     * and enqueues them in the head on later page views. An extension that only
     * enqueues its stylesheet while the element renders gets it printed as a late
     * style at the end of the page instead, i.e. a flash of unstyled content.
     * The render-time enqueue stays as the fallback (e.g. before a page is saved).
     *
     * @param string $control Switcher control name.
     * @param string $handle  Style handle to load while it is on.
     * @return array
     */
    protected static function switcher_style_assets($control, $handle) {
        return self::switcher_assets($control, [$handle]);
    }

    /**
     * `assets` argument for a switcher control that needs several handles, or
     * scripts, while it is on, typically the shared ones (`bdt-uikit`, `ep-helper`)
     * that are not loaded on every page. See switcher_style_assets().
     *
     * @param string   $control Switcher control name.
     * @param string[] $styles  Style handles to load while it is on.
     * @param string[] $scripts Script handles to load while it is on.
     * @param string   $value   The switcher's return value when it is on.
     * @return array
     */
    protected static function switcher_assets($control, array $styles, array $scripts = [], $value = 'yes') {
        $when_on = [
            'terms' => [
                [
                    'name'     => $control,
                    'operator' => '===',
                    'value'    => $value,
                ],
            ],
        ];

        $assets = [];

        foreach (['styles' => $styles, 'scripts' => $scripts] as $type => $handles) {
            foreach ($handles as $handle) {
                $assets[$type][] = [
                    'name'       => $handle,
                    'conditions' => $when_on,
                ];
            }
        }

        return $assets;
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
    protected function is_recaptcha_required($post_id, $widget_id, $setting_key, $widget_type = '') {
        // The page and widget IDs come from the request. When the caller names the form widget
        // being submitted, the IDs are trusted only if they point at a widget of that type on a
        // page the visitor may see (get_request_form_settings()); that form's own reCAPTCHA
        // switch then decides, so a form with reCAPTCHA switched off keeps working on a site
        // that has keys. Anything else (another widget type such as a Heading, an unknown ID, a
        // draft or private page) fails closed while keys are configured.
        if ('' !== $widget_type) {
            $settings = $this->get_request_form_settings($post_id, $widget_id, $widget_type);

            if (null !== $settings) {
                return isset($settings[$setting_key]) && 'yes' === $settings[$setting_key];
            }

            return $this->has_recaptcha_keys();
        }

        // No form type given: fail closed whenever the site has reCAPTCHA keys configured.
        if ($this->has_recaptcha_keys()) {
            return true;
        }

        $settings = $this->get_widget_settings($post_id, $widget_id);

        if (is_array($settings) && !empty($settings)) {
            return isset($settings[$setting_key]) && 'yes' === $settings[$setting_key];
        }

        return $this->has_recaptcha_keys();
    }
}
