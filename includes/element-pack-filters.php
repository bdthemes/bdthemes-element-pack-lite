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

/**
 * DOM-based XSS hardening for the bundled UIkit build.
 *
 * UIkit initialises components from `data-bdt-*` / `data-attrs` attributes and
 * re-parses those values in the browser. Because the payload lives inside an
 * attribute value it survives `wp_kses_post()` — WordPress allows `data-*` on
 * every tag via `_wp_add_global_attributes()` — and escaping on output does not
 * help either, since the browser hands UIkit the decoded string regardless.
 *
 * UIkit is vendored and deliberately left untouched; we filter what reaches it:
 *
 *   - `attrs` / `data-attrs` are dropped outright. UIkit reads them off the
 *     lightbox toggle and applies each pair with setAttribute(), so
 *     `data-attrs="onload: alert(1)"` becomes a real event handler. No widget
 *     ever emits this attribute, so removing it costs us nothing.
 *   - `template` is stripped from `data-bdt-*` option strings. It is a String
 *     prop on the lightbox panel (merged into the lightbox component's own props
 *     by its install()) and is injected as raw HTML. Our widgets only ever emit
 *     `animation`, `toggle` and similar scalar options.
 */
if (!function_exists('element_pack_unsafe_uikit_option_keys')) {
    function element_pack_unsafe_uikit_option_keys() {
        return apply_filters('elementpack/security/unsafe_uikit_option_keys', ['template']);
    }
}

if (!function_exists('element_pack_strip_unsafe_uikit_keys')) {
    function element_pack_strip_unsafe_uikit_keys($options) {
        // Without a `key: value` pair the whole string is shorthand for the
        // component's primary prop (e.g. `data-bdt-lightbox="animation"`).
        if (false === strpos($options, ':')) {
            return $options;
        }

        $unsafe  = element_pack_unsafe_uikit_option_keys();
        $kept    = [];
        $removed = false;

        foreach (explode(';', $options) as $pair) {
            $parts = explode(':', $pair, 2);

            if (2 === count($parts)) {
                // Normalise before comparing so case- or padding-obfuscated
                // spellings (`TEMPLATE`, ` template `) cannot slip through.
                $key = strtolower(trim($parts[0]));
                $key = preg_replace('/[^a-z0-9_-]/', '', $key);

                if (in_array($key, $unsafe, true) || 0 === strpos($key, 'on')) {
                    $removed = true;
                    continue;
                }
            }

            $kept[] = $pair;
        }

        // Leave clean values byte-identical, so trailing separators and spacing
        // in widget-generated markup are never needlessly rewritten.
        return $removed ? implode(';', $kept) : $options;
    }
}

if (!function_exists('element_pack_sanitize_uikit_attribute_match')) {
    function element_pack_sanitize_uikit_attribute_match($matches) {
        // Parse what the browser will hand UIkit, not the encoded source, so a
        // payload written as `template&#58;&lt;img onerror=…&gt;` is recognised.
        $decoded  = html_entity_decode($matches[3], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $filtered = element_pack_strip_unsafe_uikit_keys($decoded);

        if ($filtered === $decoded) {
            return $matches[0]; // Nothing unsafe — leave the original encoding alone.
        }

        return $matches[1] . $matches[2] . esc_attr($filtered) . $matches[2];
    }
}

if (!function_exists('element_pack_sanitize_uikit_attributes')) {
    function element_pack_sanitize_uikit_attributes($content) {
        if (!is_string($content) || '' === $content) {
            return $content;
        }

        // The overwhelming majority of content carries neither attribute.
        if (false === stripos($content, 'data-bdt-') && false === stripos($content, 'attrs')) {
            return $content;
        }

        // UIkit's data() helper reads both the bare and the `data-` prefixed form.
        $stripped = preg_replace(
            '/\s(?:data-)?attrs\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i',
            '',
            $content
        );

        // PCRE returns null if it hits the backtrack/recursion limit on very
        // large documents; keep the original content rather than blanking it.
        if (null !== $stripped) {
            $content = $stripped;
        }

        $filtered = preg_replace_callback(
            '/(\sdata-bdt-[\w-]+\s*=\s*)(["\'])(.*?)\2/is',
            'element_pack_sanitize_uikit_attribute_match',
            $content
        );

        return (null === $filtered) ? $content : $filtered;
    }
}

if (!function_exists('element_pack_sanitize_uikit_attributes_on_save')) {
    function element_pack_sanitize_uikit_attributes_on_save($content) {
        // Users who may post unfiltered HTML are already trusted with <script>;
        // rewriting their stored content would be destructive, so only clean
        // content coming from authors WordPress itself runs through kses.
        if (function_exists('current_user_can') && current_user_can('unfiltered_html')) {
            return $content;
        }

        // content_save_pre hands us slashed content, so `foo="bar"` arrives as
        // `foo=\"bar\"`. Matching that directly would miss the quoted form and
        // shred the markup, so round-trip through wp_unslash()/wp_slash().
        return wp_slash(element_pack_sanitize_uikit_attributes(wp_unslash($content)));
    }
}

if (!function_exists('element_pack_register_uikit_content_guard')) {
    function element_pack_register_uikit_content_guard() {
        // Late on the_content so Elementor's rendered widget markup is covered
        // too. Our own `data-bdt-lightbox="animation: …"` output is unaffected
        // because only the unsafe keys are removed, never the whole attribute.
        add_filter('the_content', 'element_pack_sanitize_uikit_attributes', PHP_INT_MAX);
        add_filter('widget_text', 'element_pack_sanitize_uikit_attributes', PHP_INT_MAX);

        // Defence in depth: also clean payloads at rest, so already-stored
        // content stops being a liability outside the_content (feeds, REST).
        add_filter('content_save_pre', 'element_pack_sanitize_uikit_attributes_on_save', 11);
    }
    add_action('init', 'element_pack_register_uikit_content_guard');
}
