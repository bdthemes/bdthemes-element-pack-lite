<?php
/**
 * PHPUnit bootstrap for Element Pack Pro unit tests.
 *
 * Loads the dev autoloader, defines the handful of plugin/WordPress constants the
 * code under test reads, installs the WordPress stub layer, then requires the real
 * plugin source files so tests exercise the actual implementation (not mocks of it).
 */

// phpcs:ignore WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting, PluginCheck.CodeAnalysis.PHPErrorReporting.DirectErrorReportingCall -- PHPUnit bootstrap; this file is excluded from the release build.
error_reporting(E_ALL);

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- PHPUnit bootstrap path variable.
$root = dirname(__DIR__);

require $root . '/vendor/autoload.php';

// --- Plugin / WordPress constants used by the files under test -------------
if (!defined('ABSPATH')) {
    // Tests never read from here; defining it satisfies the `if (!defined('ABSPATH')) exit;`
    // guards at the top of every plugin file (and inside each module.info.php).
    define('ABSPATH', $root . '/');
}
if (!defined('BDTEP_MODULES_PATH')) {
    define('BDTEP_MODULES_PATH', $root . '/modules/');
}
if (!defined('BDTEP_VER')) {
    define('BDTEP_VER', 'test');
}
if (!defined('BDTEP_URL')) {
    define('BDTEP_URL', 'http://example.test/wp-content/plugins/bdthemes-element-pack/');
}
if (!defined('BDTEP_ASSETS_URL')) {
    define('BDTEP_ASSETS_URL', BDTEP_URL . 'assets/');
}

// --- WordPress stub layer (must precede the plugin source) -----------------
require __DIR__ . '/wp-stubs.php';

// --- Real plugin source under test -----------------------------------------
// ModuleService first: element-pack-filters.php `use`s it and calls its methods.
require $root . '/admin/module-settings.php';
require $root . '/includes/element-pack-filters.php';
