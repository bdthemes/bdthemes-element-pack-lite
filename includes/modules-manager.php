<?php

namespace ElementPack;

use ElementPack\Admin\ModuleService;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

final class Manager
{


    public function register_module_and_assets()
    {

        // The loader only needs each module's name (+ plugin_path for third-party
        // integrations). Pull the lean, transient-cached runtime lists instead of
        // rebuilding the full ~4,200-line settings array (hundreds of esc_html__()
        // calls + a get_plugins() directory scan) on every front-end request.
        $lists = ModuleService::get_runtime_module_lists();

        /**
         * Our Widget
         */
        foreach ($lists['element_pack_active_modules'] as $widget) {
            if (element_pack_is_widget_enabled($widget['name'])) {
                $this->load_module_instance($widget);
            }
        }

        /**
         * Extension
         */
        foreach ($lists['element_pack_elementor_extend'] as $extension) {
            if (element_pack_is_extend_enabled($extension['name'])) {
                $this->load_module_instance($extension);
            }
        }

        /**
         * Third Party Widget
         */
        foreach ($lists['element_pack_third_party_widget'] as $widget) {
            if (element_pack_is_third_party_enabled($widget['name'])) {
                if (isset($widget['plugin_path']) && ModuleService::is_plugin_active($widget['plugin_path'])) {
                    $this->load_module_instance($widget);
                }
            }
        }

        // Static module if need
        $this->load_module_instance(['name' => 'elementor']);
    }

    public function load_module_instance($module)
    {

        if (isset($_GET['page']) && 'element_pack_options' == $_GET['page']) {
            return;
        }

        $direction = is_rtl() ? '.rtl' : '';
        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

        $module_id = $module['name'];
        $class_name = str_replace('-', ' ', $module_id);
        $class_name = str_replace(' ', '', ucwords($class_name));
        $class_name = __NAMESPACE__ . '\\Modules\\' . $class_name . '\\Module';

        if (!element_pack_is_asset_optimization_enabled()) {
            if (!element_pack_is_preview()) {
                // register widgets css
                // if (ModuleService::has_module_style($module_id)) {
                //     wp_register_style('ep-' . $module_id, BDTEP_URL . 'assets/css/ep-' . $module_id . $direction . '.css', ['bdt-uikit', 'ep-helper'], BDTEP_VER);
                // }
                if (ModuleService::has_module_style($module_id)) {
                    wp_register_style('ep-' . $module_id, BDTEP_URL . 'assets/css/ep-' . $module_id . '.css', ['bdt-uikit', 'ep-helper'], BDTEP_VER);
                }
                // register widget JS
                if (ModuleService::has_module_script($module_id)) {
                    wp_register_script('ep-' . $module_id, BDTEP_URL . 'assets/js/modules/ep-' . $module_id . '.min.js', ['jquery', 'bdt-uikit'], BDTEP_VER, true);
                }
            }
        }

        if (class_exists($class_name)) {
            $class_name::instance();
        }
    }

    public function __construct()
    {

        $this->register_module_and_assets();
    }
}
