<?php

/**
 * Element Pack widget filters
 * @since 5.7.4
 */

use ElementPack\Admin\ModuleService;


if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Settings Filters
if (!function_exists('element_pack_is_dashboard_enabled')) {
    function element_pack_is_dashboard_enabled() {
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
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- $hook is built above and always starts with the "elementpack/" prefix.
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
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- $hook is built above and always starts with the "elementpack/" prefix.
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
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- $hook is built above and always starts with the "elementpack/" prefix.
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
 *   - `template` and `attributes` are stripped from `bdt-*` / `data-bdt-*`
 *     option strings, along with every `on*` key and every value that carries a
 *     javascript:, vbscript: or data: scheme (the svg component's `src`). The
 *     string is parsed the way UIkit's parseOptions() does: a value starting
 *     with `{` is JSON, anything else is `key: value;` pairs. Our widgets only
 *     ever emit `animation`, `toggle`, `stroke-animation` and similar scalar
 *     options, and JSON objects without any of those keys, so their markup
 *     passes through byte-identical.
 */
if (!function_exists('element_pack_unsafe_uikit_option_keys')) {
    function element_pack_unsafe_uikit_option_keys() {
        return apply_filters('elementpack/security/unsafe_uikit_option_keys', ['template', 'attributes']);
    }
}

if (!function_exists('element_pack_uikit_option_key_is_unsafe')) {
    function element_pack_uikit_option_key_is_unsafe($key) {
        // Normalise before comparing so case- or padding-obfuscated spellings
        // (`TEMPLATE`, ` template `) cannot slip through.
        $key = strtolower(trim((string) $key));
        $key = preg_replace('/[^a-z0-9_-]/', '', $key);

        return in_array($key, element_pack_unsafe_uikit_option_keys(), true) || 0 === strpos($key, 'on');
    }
}

if (!function_exists('element_pack_uikit_option_value_is_unsafe')) {
    function element_pack_uikit_option_value_is_unsafe($value) {
        // A script or data: URL in any option (`src` of bdt-svg, `source` of a
        // lightbox item) is never something our widgets emit.
        return is_string($value) && 1 === preg_match('/^\s*(?:javascript|vbscript|data)\s*:/i', $value);
    }
}

if (!function_exists('element_pack_strip_unsafe_uikit_json_keys')) {
    function element_pack_strip_unsafe_uikit_json_keys($options) {
        $decoded = json_decode($options, true);

        // UIkit JSON.parse()s a value that starts with `{`; one PHP cannot parse
        // is dropped rather than kept, so a parser difference cannot be abused.
        if (!is_array($decoded)) {
            return '';
        }

        $removed = false;

        foreach ($decoded as $key => $value) {
            if (element_pack_uikit_option_key_is_unsafe($key) || element_pack_uikit_option_value_is_unsafe($value)) {
                unset($decoded[$key]);
                $removed = true;
            }
        }

        return $removed ? wp_json_encode((object) $decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $options;
    }
}

if (!function_exists('element_pack_strip_unsafe_uikit_keys')) {
    function element_pack_strip_unsafe_uikit_keys($options) {
        // UIkit's parseOptions(): a value starting with `{` is a JSON object.
        $trimmed = ltrim($options);
        if ('' !== $trimmed && '{' === $trimmed[0]) {
            return element_pack_strip_unsafe_uikit_json_keys($trimmed);
        }

        // Without a `key: value` pair the whole string is shorthand for the
        // component's primary prop (e.g. `data-bdt-lightbox="animation"`).
        if (false === strpos($options, ':')) {
            return $options;
        }

        $kept    = [];
        $removed = false;

        foreach (explode(';', $options) as $pair) {
            $parts = explode(':', $pair, 2);

            if (2 === count($parts) && (element_pack_uikit_option_key_is_unsafe($parts[0]) || element_pack_uikit_option_value_is_unsafe($parts[1]))) {
                $removed = true;
                continue;
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

if (!function_exists('element_pack_uikit_attrs_attribute_name')) {
    /**
     * Whether an attribute name is UIkit's generic option carrier.
     *
     * UIkit's data() helper drops a leading `data-` before looking the option up,
     * so both spellings count. Compared case-insensitively, as HTML attribute
     * names are.
     */
    function element_pack_uikit_attrs_attribute_name($name) {
        $name = strtolower((string) $name);

        if (0 === strpos($name, 'data-')) {
            $name = substr($name, 5);
        }

        return 'attrs' === $name;
    }
}

if (!function_exists('element_pack_remove_uikit_attrs_attributes')) {
    /**
     * Remove UIkit's generic `attrs` / `data-attrs` option attribute from markup.
     *
     * The plugin's own widgets never emit either spelling, so dropping the whole
     * attribute breaks nothing, and UIkit applies its value as arbitrary
     * attributes on the component's element.
     *
     * Parsed, never pattern-matched. The previous implementation deleted
     * ` attrs=…` with a quote-blind regular expression whose `[^\s>]+` branch
     * consumed the closing quote of an enclosing attribute when the text
     * `attrs=` appeared inside that attribute's value:
     *
     *     <img src="x" alt="A attrs=y" border=" onerror=alert(1) z">
     *
     * Deleting that match collapsed the attribute boundary and promoted the
     * inert text in `border` into a real event handler, turning markup
     * wp_kses_post() had already made safe back into stored XSS. Here only real
     * attributes of a tag are removed, never text inside another value.
     */
    function element_pack_remove_uikit_attrs_attributes($content) {
        if (!is_string($content) || false === stripos($content, 'attrs')) {
            return $content;
        }

        // Without WordPress's HTML API (WordPress < 6.2, below this plugin's
        // minimum) there is no safe way to remove an attribute: a pattern cannot
        // tell where a browser will consider an attribute value to start and end,
        // and getting that wrong is exactly what re-armed content here before. An
        // unchanged document can never gain an event handler, so leave it alone.
        if (!class_exists('\WP_HTML_Tag_Processor')) {
            return $content;
        }

        $processor = new \WP_HTML_Tag_Processor($content);

        while ($processor->next_tag()) {
            foreach ((array) $processor->get_attribute_names_with_prefix('') as $name) {
                if (element_pack_uikit_attrs_attribute_name($name)) {
                    $processor->remove_attribute($name);
                }
            }
        }

        return $processor->get_updated_html();
    }
}

if (!function_exists('element_pack_sanitize_uikit_attributes')) {
    function element_pack_sanitize_uikit_attributes($content) {
        if (!is_string($content) || '' === $content) {
            return $content;
        }

        // The overwhelming majority of content carries neither attribute.
        if (false === stripos($content, 'bdt-') && false === stripos($content, 'attrs')) {
            return $content;
        }

        // UIkit's data() helper reads both the bare and the `data-` prefixed form.
        // Removed as real attributes of a tag, never by pattern: see
        // element_pack_remove_uikit_attrs_attributes() for why a quote-blind
        // pattern here was itself an XSS vector.
        $content = element_pack_remove_uikit_attrs_attributes($content);

        $filtered = preg_replace_callback(
            '/(\s(?:data-)?bdt-[\w-]+\s*=\s*)(["\'])(.*?)\2/is',
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

        // Hand-written excerpts are stored with the same kses rules as content
        // and rendered on home, archive and search pages.
        add_filter('get_the_excerpt', 'element_pack_sanitize_uikit_attributes', PHP_INT_MAX);
        add_filter('the_excerpt', 'element_pack_sanitize_uikit_attributes', PHP_INT_MAX);

        // Defence in depth: also clean payloads at rest, so already-stored
        // content stops being a liability outside the_content (feeds, REST).
        //
        // Priority 5, i.e. BEFORE wp_filter_post_kses() at 10, and alongside
        // Content_Guard. A sanitizer that rewrites markup must never run after
        // kses on a save: whatever it changes is persisted with no further
        // checking, so a bug here (as in the quote-blind `attrs=` pattern this
        // replaced) re-arms content kses had already made safe.
        add_filter('content_save_pre', 'element_pack_sanitize_uikit_attributes_on_save', 5);
        add_filter('excerpt_save_pre', 'element_pack_sanitize_uikit_attributes_on_save', 5);
    }
    add_action('init', 'element_pack_register_uikit_content_guard');
}
