<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- WordPress function stubs for PHPUnit; not production code.
/**
 * Minimal WordPress function stubs for fast, DB-free unit tests.
 *
 * These reproduce just enough of WordPress' behaviour for the code under test:
 *  - an in-memory options store whose update_option()/add_option() fire the same
 *    `added_option` / `updated_option` actions WordPress fires, so cache-invalidation
 *    wiring can be tested for real;
 *  - a unified hook registry backing add_action/add_filter/do_action/apply_filters/has_filter;
 *  - passthrough i18n + escaping helpers;
 *  - inert plugin helpers (get_plugins/is_plugin_active).
 *
 * Tests reset state via the __wp_* helpers at the bottom. Nothing here talks to a
 * database, the filesystem (beyond the real module.info.php files), or the network.
 */

// Prevent direct access (the test bootstrap defines ABSPATH before loading this file).
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if (defined('EP_TEST_WP_STUBS_LOADED')) {
    return;
}
define('EP_TEST_WP_STUBS_LOADED', true);

$GLOBALS['__wp_options']      = [];
$GLOBALS['__wp_site_options'] = [];
$GLOBALS['__wp_transients']   = [];
$GLOBALS['__wp_hooks']        = [];

// WordPress time constants used by transient expiries.
if (!defined('MINUTE_IN_SECONDS')) { define('MINUTE_IN_SECONDS', 60); }
if (!defined('HOUR_IN_SECONDS'))   { define('HOUR_IN_SECONDS', 3600); }
if (!defined('DAY_IN_SECONDS'))    { define('DAY_IN_SECONDS', 86400); }
if (!defined('WEEK_IN_SECONDS'))   { define('WEEK_IN_SECONDS', 604800); }

/* -------------------------------------------------------------------------
 * Options API
 * ---------------------------------------------------------------------- */

function get_option($name, $default = false) {
    return array_key_exists($name, $GLOBALS['__wp_options'])
        ? $GLOBALS['__wp_options'][$name]
        : $default;
}

function add_option($name, $value = '') {
    $existed = array_key_exists($name, $GLOBALS['__wp_options']);
    $GLOBALS['__wp_options'][$name] = $value;
    if (!$existed) {
        do_action('added_option', $name, $value);
    }
    return true;
}

function update_option($name, $value) {
    if (!array_key_exists($name, $GLOBALS['__wp_options'])) {
        return add_option($name, $value);
    }
    $old = $GLOBALS['__wp_options'][$name];
    if ($old === $value) {
        return false; // WordPress short-circuits when the value is unchanged.
    }
    $GLOBALS['__wp_options'][$name] = $value;
    do_action('updated_option', $name, $old, $value);
    return true;
}

function delete_option($name) {
    unset($GLOBALS['__wp_options'][$name]);
    return true;
}

function get_site_option($name, $default = false) {
    return array_key_exists($name, $GLOBALS['__wp_site_options'])
        ? $GLOBALS['__wp_site_options'][$name]
        : $default;
}

function update_site_option($name, $value) {
    $GLOBALS['__wp_site_options'][$name] = $value;
    return true;
}

/* -------------------------------------------------------------------------
 * Transients API (no expiry simulation; expiry is irrelevant to these tests)
 * ---------------------------------------------------------------------- */

function get_transient($key) {
    return array_key_exists($key, $GLOBALS['__wp_transients'])
        ? $GLOBALS['__wp_transients'][$key]
        : false;
}

function set_transient($key, $value, $expiration = 0) {
    $GLOBALS['__wp_transients'][$key] = $value;
    return true;
}

function delete_transient($key) {
    unset($GLOBALS['__wp_transients'][$key]);
    return true;
}

function wp_json_encode($data, $options = 0, $depth = 512) {
    return json_encode($data, $options, $depth);
}

/* -------------------------------------------------------------------------
 * Hooks API (actions + filters share one registry, as in core)
 * ---------------------------------------------------------------------- */

function add_filter($hook, $callback, $priority = 10, $accepted_args = 1) {
    $GLOBALS['__wp_hooks'][$hook][] = $callback;
    return true;
}

function add_action($hook, $callback, $priority = 10, $accepted_args = 1) {
    return add_filter($hook, $callback, $priority, $accepted_args);
}

function has_filter($hook, $callback = false) {
    return !empty($GLOBALS['__wp_hooks'][$hook]);
}

function has_action($hook, $callback = false) {
    return has_filter($hook, $callback);
}

function apply_filters($hook, $value, ...$args) {
    if (empty($GLOBALS['__wp_hooks'][$hook])) {
        return $value;
    }
    foreach ($GLOBALS['__wp_hooks'][$hook] as $callback) {
        $value = $callback($value, ...$args);
    }
    return $value;
}

function do_action($hook, ...$args) {
    if (empty($GLOBALS['__wp_hooks'][$hook])) {
        return;
    }
    foreach ($GLOBALS['__wp_hooks'][$hook] as $callback) {
        $callback(...$args);
    }
}

/* -------------------------------------------------------------------------
 * i18n + escaping (passthrough)
 * ---------------------------------------------------------------------- */

function __($text, $domain = 'default') { return $text; }
function _x($text, $context, $domain = 'default') { return $text; }
function esc_html__($text, $domain = 'default') { return $text; }
function esc_html_x($text, $context, $domain = 'default') { return $text; }
function esc_attr__($text, $domain = 'default') { return $text; }
function esc_html($text) { return $text; }
function esc_attr($text) { return $text; }

/* -------------------------------------------------------------------------
 * Plugin helpers (inert by default; overridable per test)
 * ---------------------------------------------------------------------- */

function get_plugins() {
    return $GLOBALS['__wp_plugins'] ?? [];
}

function is_plugin_active($plugin) {
    return in_array($plugin, $GLOBALS['__wp_active_plugins'] ?? [], true);
}

function wp_doing_ajax() { return false; }
function is_admin() { return false; }

/* -------------------------------------------------------------------------
 * Test reset helpers
 * ---------------------------------------------------------------------- */

/**
 * Reset the options store. Does NOT clear the hook registry, so persistent
 * wiring registered once at bootstrap (e.g. Element Pack's option-cache flush
 * hooks) survives between tests, exactly as it would in a real request.
 */
function __wp_reset_options(array $options = []) {
    $GLOBALS['__wp_options'] = $options;
}

/** Reset site (network) options and the transient store. */
function __wp_reset_site_state(array $site_options = []) {
    $GLOBALS['__wp_site_options'] = $site_options;
    $GLOBALS['__wp_transients']   = [];
}

/** Return the keys currently present in the transient store (for assertions). */
function __wp_transient_keys() {
    return array_keys($GLOBALS['__wp_transients']);
}

/** Remove all callbacks attached to a single hook (for per-test filters). */
function __wp_clear_hook($hook) {
    unset($GLOBALS['__wp_hooks'][$hook]);
}

/** Set the value of an option WITHOUT firing any actions (simulates an external change). */
function __wp_set_option_silently($name, $value) {
    $GLOBALS['__wp_options'][$name] = $value;
}
