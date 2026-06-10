<?php

/**
 * Element Pack widget filters
 * @since 5.7.4
 */

use ElementPack\Admin\ModuleService;


if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Settings Filters
if (!function_exists('ep_is_dashboard_enabled')) {
    function ep_is_dashboard_enabled() {
        return apply_filters('elementpack/settings/dashboard', true);
    }
}

if (!function_exists('element_pack_is_widget_enabled')) {
    function element_pack_is_widget_enabled($widget_id, $options = []) {

        if(!$options){
            $options = ModuleService::get_modules_option('element_pack_active_modules');
        }

        if( ModuleService::is_module_active($widget_id, $options)){
            $hook = 'elementpack/widget/' . str_replace('-', '_', $widget_id);
            // Skip the filter machinery unless something is actually listening;
            // these per-widget hooks are almost never used, but stay overridable.
            return has_filter($hook) ? apply_filters($hook, true) : true;
        }
    }
}

if (!function_exists('element_pack_is_extend_enabled')) {
    function element_pack_is_extend_enabled($widget_id, $options = []) {

        if(!$options){
            $options = ModuleService::get_modules_option('element_pack_elementor_extend');
        }

        if( ModuleService::is_module_active($widget_id, $options)){
            $hook = 'elementpack/extend/' . str_replace('-', '_', $widget_id);
            return has_filter($hook) ? apply_filters($hook, true) : true;
        }
    }
}

if (!function_exists('element_pack_is_third_party_enabled')) {
    function element_pack_is_third_party_enabled($widget_id, $options = []) {

        if(!$options){
            $options = ModuleService::get_modules_option('element_pack_third_party_widget');
        }

        if( ModuleService::is_module_active($widget_id, $options)){
            $hook = 'elementpack/widget/' . str_replace('-', '_', $widget_id);
            return has_filter($hook) ? apply_filters($hook, true) : true;
        }
    }
}

// Keep the per-request option cache correct if a settings save happens mid-request.
if (!function_exists('element_pack_flush_modules_option_cache')) {
    function element_pack_flush_modules_option_cache($option) {
        if (in_array($option, [
            'element_pack_active_modules',
            'element_pack_elementor_extend',
            'element_pack_third_party_widget',
        ], true)) {
            ModuleService::flush_modules_option_cache($option);
        }
    }
    add_action('added_option', 'element_pack_flush_modules_option_cache');
    add_action('updated_option', 'element_pack_flush_modules_option_cache');
}

if (!function_exists('element_pack_is_asset_optimization_enabled')) {
    function element_pack_is_asset_optimization_enabled() {
        $asset_manager = element_pack_option('asset-manager', 'element_pack_other_settings', 'off');
        if( $asset_manager == 'on'){
            return apply_filters("elementpack/optimization/asset_manager", true);
        }
    }
}


