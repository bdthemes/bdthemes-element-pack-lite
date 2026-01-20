<?php
/**
 * Element Pack Remote Data Handler
 * 
 * Handles remote API data loading with proper caching and background processing
 * to prevent blocking admin pages.
 */

namespace ElementPack\SetupWizard;

if (!defined('ABSPATH')) {
    exit;
}

// Ensure Plugin_Api_Fetcher is available
require_once __DIR__ . '/class-plugin-api-fetcher.php';

class Remote_Data_Handler {

    /**
     * Cache duration in seconds (12 hours)
     */
    const CACHE_DURATION = 12 * HOUR_IN_SECONDS;

    /**
     * Transient key for remote plugins data
     */
    const CACHE_KEY = 'ep_remote_plugins_data';

    /**
     * Cron hook name for background fetch
     */
    const CRON_HOOK = 'ep_fetch_remote_plugins_cron';

    /**
     * Initialize the remote data handler
     */
    public static function init() {
        add_action('init', [__CLASS__, 'schedule_cron']);
        add_action(self::CRON_HOOK, [__CLASS__, 'cron_fetch_plugins']);
        add_action('wp_ajax_ep_get_plugins', [__CLASS__, 'ajax_get_plugins']);
        add_action('wp_ajax_nopriv_ep_get_plugins', [__CLASS__, 'ajax_get_plugins']);
    }

    /**
     * WP-Cron callback for fetching plugins
     */
    public static function cron_fetch_plugins() {
        // Log that cron is running
        error_log('Element Pack: Running cron fetch for remote plugins');
        self::fetch_remote_plugins_now();
    }

    /**
     * Check if we're on the Element Pack options page
     * 
     * @return bool True if on Element Pack options page
     */
    public static function is_element_pack_page() {
        if (!is_admin()) {
            return false;
        }

        // Check if this is an AJAX request for our plugins
        if (wp_doing_ajax() && isset($_REQUEST['action'])) {
            $action = sanitize_text_field($_REQUEST['action']);
            if (in_array($action, ['ep_get_plugins'])) {
                return true;
            }
        }

        $page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
        return $page === 'element_pack_options';
    }

    /**
     * Get remote plugins data from cache
     * 
     * @return array Cached plugins data or empty array if not available
     */
    public static function get_remote_plugins() {
        $cached_data = get_transient(self::CACHE_KEY);
        
        if ($cached_data !== false) {
            return $cached_data;
        }

        // If no cache exists, schedule background fetch and return empty array
        self::schedule_remote_fetch();
        
        return [];
    }

    /**
     * Schedule a background fetch via WP-Cron
     * 
     * @return bool True if successfully scheduled
     */
    public static function schedule_remote_fetch() {
        // Schedule to run immediately if not already scheduled
        if (!wp_next_scheduled(self::CRON_HOOK)) {
            wp_schedule_single_event(time(), self::CRON_HOOK);
            return true;
        }
        
        return false;
    }

    /**
     * Fetch remote plugins data immediately (for background processing only)
     * 
     * @return array|false Plugins data or false on failure
     */
    public static function fetch_remote_plugins_now() {
        // Define plugin slugs to fetch
        $plugin_slugs = [
            'bdthemes-prime-slider-lite',
            'ultimate-post-kit', 
            'ultimate-store-kit',
            'zoloblocks',
            'pixel-gallery',
            'live-copy-paste',
            'spin-wheel',
            'ai-image',
            'dark-reader',
            'ar-viewer',
            'smart-admin-assistant',
            'website-accessibility',
        ];

        $results = [];
        $errors = [];

        foreach ($plugin_slugs as $slug) {
            // Use the fully qualified class name with bypass_check=true for background fetching
            $data = \ElementPack\SetupWizard\Plugin_Api_Fetcher::get_plugin_data($slug, true);
            if ($data !== false) {
                $results[$slug] = $data;
            } else {
                $errors[] = $slug;
            }
        }

        // Log errors for debugging
        if (!empty($errors)) {
            error_log('Element Pack: Failed to fetch data for plugins: ' . implode(', ', $errors));
        }

        // Cache the results even if some failed
        set_transient(self::CACHE_KEY, $results, self::CACHE_DURATION);
        
        return $results;
    }

    /**
     * AJAX handler for getting plugins data
     */
    public static function ajax_get_plugins() {
        // Verify nonce for security
        if (!check_ajax_referer('ep_get_plugins_nonce', 'nonce', false)) {
            wp_die(__('Security check failed.', 'bdthemes-element-pack'));
        }

        // Get cached data
        $plugins_data = self::get_remote_plugins();
        
        // If cache is empty, try to fetch immediately (but don't block)
        if (empty($plugins_data)) {
            // Schedule background fetch if not already done
            self::schedule_remote_fetch();
            
            // Return empty response with flag indicating data is loading
            wp_send_json_success([
                'plugins' => [],
                'loading' => true,
                'message' => __('Loading plugin data...', 'bdthemes-element-pack')
            ]);
        }

        // Format the response for frontend use
        $formatted_plugins = [];
        foreach ($plugins_data as $slug => $data) {
            // Check plugin status
            $plugin_status = self::get_plugin_status_by_slug($slug);
            $plugin_file = self::get_plugin_file_by_slug($slug);
            
            // Format the last updated date
            $last_updated_formatted = '';
            if (!empty($data['last_updated'])) {
                $last_updated_formatted = self::format_last_updated($data['last_updated']);
            }
            
            $formatted_plugins[] = [
                'name' => $data['name'] ?? '',
                'slug' => $data['slug'] ?? '',
                'description' => $data['description'] ?? '',
                'logo' => $data['logo'] ?? '',
                'rating' => $data['rating'] ?? 0,
                'rating_percentage' => $data['rating_percentage'] ?? 0,
                'num_ratings' => $data['num_ratings'] ?? 0,
                'active_installs' => $data['active_installs'] ?? '0',
                'active_installs_count' => $data['active_installs_count'] ?? 0,
                'downloaded' => $data['downloaded'] ?? 0,
                'downloaded_formatted' => $data['downloaded_formatted'] ?? '',
                'version' => $data['version'] ?? '',
                'tested' => $data['tested'] ?? '',
                'last_updated' => $data['last_updated'] ?? '',
                'last_updated_formatted' => $last_updated_formatted,
                'homepage' => $data['homepage'] ?? '',
                'status' => $plugin_status,
                'plugin_file' => $plugin_file,
                'activate_nonce' => $plugin_file ? wp_create_nonce('activate-plugin_' . $plugin_file) : ''
            ];
        }

        wp_send_json_success([
            'plugins' => $formatted_plugins,
            'loading' => false,
            'message' => __('Plugin data loaded successfully.', 'bdthemes-element-pack')
        ]);
    }

    /**
     * Schedule the cron job on init
     */
    public static function schedule_cron() {
        // Make sure the cron hook is registered
        if (!wp_next_scheduled(self::CRON_HOOK)) {
            // Don't schedule immediately, only when needed
        }
    }

    /**
     * Clear the remote plugins cache
     */
    public static function clear_cache() {
        delete_transient(self::CACHE_KEY);
    }

    /**
     * Force refresh of remote data
     */
    public static function force_refresh() {
        self::clear_cache();
        return self::fetch_remote_plugins_now();
    }

    /**
     * Get plugin status by slug
     * 
     * @param string $slug Plugin slug
     * @return string Plugin status: 'active', 'installed', 'not_installed'
     */
    private static function get_plugin_status_by_slug($slug) {
        if (!function_exists('is_plugin_active')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        // Get all installed plugins
        $installed_plugins = get_plugins();
        
        // Find the plugin file for this slug
        $plugin_file = self::get_plugin_file_by_slug($slug);
        
        if ($plugin_file && is_plugin_active($plugin_file)) {
            return 'active';
        } elseif ($plugin_file && isset($installed_plugins[$plugin_file])) {
            return 'installed';
        }
        
        return 'not_installed';
    }

    /**
     * Get plugin file path by slug
     * 
     * @param string $slug Plugin slug
     * @return string|null Plugin file path or null if not found
     */
    private static function get_plugin_file_by_slug($slug) {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $installed_plugins = get_plugins();
        
        // Look for the plugin file that matches the slug
        foreach ($installed_plugins as $plugin_file => $plugin_data) {
            $plugin_slug = dirname($plugin_file);
            if ($plugin_slug === $slug) {
                return $plugin_file;
            }
        }
        
        return null;
    }

    /**
     * Format date in human-readable format
     * 
     * @param string $date_string Date string to format
     * @return string Formatted date string
     */
    private static function format_last_updated($date_string) {
        if (empty($date_string)) {
            return __('Unknown', 'bdthemes-element-pack');
        }
        
        $date = strtotime($date_string);
        if (!$date) {
            return __('Unknown', 'bdthemes-element-pack');
        }
        
        $diff = current_time('timestamp') - $date;
        
        if ($diff < 60) {
            return __('Just now', 'bdthemes-element-pack');
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return sprintf(_n('%d minute ago', '%d minutes ago', $minutes, 'bdthemes-element-pack'), $minutes);
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return sprintf(_n('%d hour ago', '%d hours ago', $hours, 'bdthemes-element-pack'), $hours);
        } elseif ($diff < 2592000) { // 30 days
            $days = floor($diff / 86400);
            return sprintf(_n('%d day ago', '%d days ago', $days, 'bdthemes-element-pack'), $days);
        } elseif ($diff < 31536000) { // 1 year
            $months = floor($diff / 2592000);
            return sprintf(_n('%d month ago', '%d months ago', $months, 'bdthemes-element-pack'), $months);
        } else {
            $years = floor($diff / 31536000);
            return sprintf(_n('%d year ago', '%d years ago', $years, 'bdthemes-element-pack'), $years);
        }
    }
}

// Initialize the handler
add_action('init', function() {
    Remote_Data_Handler::init();
});

// Global functions for backward compatibility and ease of use
if (!function_exists('ep_is_element_pack_page')) {
    function ep_is_element_pack_page() {
        return \ElementPack\SetupWizard\Remote_Data_Handler::is_element_pack_page();
    }
}

if (!function_exists('ep_get_remote_plugins')) {
    function ep_get_remote_plugins() {
        return \ElementPack\SetupWizard\Remote_Data_Handler::get_remote_plugins();
    }
}

if (!function_exists('ep_schedule_remote_fetch')) {
    function ep_schedule_remote_fetch() {
        return \ElementPack\SetupWizard\Remote_Data_Handler::schedule_remote_fetch();
    }
}

if (!function_exists('ep_fetch_remote_plugins_now')) {
    function ep_fetch_remote_plugins_now() {
        return \ElementPack\SetupWizard\Remote_Data_Handler::fetch_remote_plugins_now();
    }
}
