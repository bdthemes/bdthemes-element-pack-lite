<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Element Pack admin bootstrap (React dashboard shell).
 *
 * Registers the top-level WordPress menu, the upgrade redirect and the plugin
 * install AJAX handler the setup wizard uses. All dashboard UI lives in the
 * React app (src/admin/app, compiled to build/admin).
 */
class ElementPack_Admin_Settings {

	const PAGE_ID = 'element_pack_options';

	private $settings_api;

	public function __construct() {
		$this->settings_api = new ElementPack_Settings_API();

		add_action( 'admin_init', [ $this, 'ep_redirect_to_upgrade' ] );
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 201 );

		// Used by the setup wizard's "Other plugins" step.
		add_action( 'wp_ajax_ep_install_plugin', [ $this, 'install_plugin_ajax' ] );
	}

	/**
	 * Dashboard URL.
	 *
	 * @return string
	 */
	public static function get_url() {
		return admin_url( 'admin.php?page=' . self::PAGE_ID );
	}

	/**
	 * Redirect to the Element Pack Pro pricing page.
	 */
	public function ep_redirect_to_upgrade() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin screen routing, no state is changed.
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

		if ( self::PAGE_ID . '_upgrade' === $page ) {
			// phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- Hardcoded external upgrade URL, never user input.
			wp_redirect( 'https://www.elementpack.pro/pricing/' );
			exit;
		}
	}

	/**
	 * Add the plugin menu. The React app pushes its own routes into the submenu
	 * (see \ElementPack\Admin\Admin_Cutover::prune_legacy_submenus()).
	 */
	public function admin_menu() {
		add_menu_page(
			BDTEP_TITLE . ' ' . esc_html__( 'Dashboard', 'bdthemes-element-pack-lite' ),
			BDTEP_TITLE,
			'manage_options',
			self::PAGE_ID,
			[ $this, 'plugin_page' ],
			$this->element_pack_icon(),
			3
		);

		add_submenu_page(
			self::PAGE_ID,
			esc_html__( 'Dashboard', 'bdthemes-element-pack-lite' ),
			esc_html__( 'Dashboard', 'bdthemes-element-pack-lite' ),
			'manage_options',
			self::PAGE_ID,
			[ $this, 'plugin_page' ]
		);
	}

	/**
	 * Menu icon.
	 *
	 * @return string
	 */
	public function element_pack_icon() {
		return 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyMy4wLjIsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHdpZHRoPSIyMzAuN3B4IiBoZWlnaHQ9IjI1NC44MXB4IiB2aWV3Qm94PSIwIDAgMjMwLjcgMjU0LjgxIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAyMzAuNyAyNTQuODE7Ig0KCSB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+DQoJLnN0MHtmaWxsOiNGRkZGRkY7fQ0KPC9zdHlsZT4NCjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik02MS4wOSwyMjkuMThIMjguOTVjLTMuMTcsMC01Ljc1LTIuNTctNS43NS01Ljc1bDAtMTkyLjA3YzAtMy4xNywyLjU3LTUuNzUsNS43NS01Ljc1aDMyLjE0DQoJYzMuMTcsMCw1Ljc1LDIuNTcsNS43NSw1Ljc1djE5Mi4wN0M2Ni44MywyMjYuNjEsNjQuMjYsMjI5LjE4LDYxLjA5LDIyOS4xOHoiLz4NCjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0yMDcuNSwzMS4zN3YzMi4xNGMwLDMuMTctMi41Nyw1Ljc1LTUuNzUsNS43NUg5MC4wNGMtMy4xNywwLTUuNzUtMi41Ny01Ljc1LTUuNzVWMzEuMzcNCgljMC0zLjE3LDIuNTctNS43NSw1Ljc1LTUuNzVoMTExLjcyQzIwNC45MywyNS42MiwyMDcuNSwyOC4yLDIwNy41LDMxLjM3eiIvPg0KPHBhdGggY2xhc3M9InN0MCIgZD0iTTIwNy41LDExMS4zM3YzMi4xNGMwLDMuMTctMi41Nyw1Ljc1LTUuNzUsNS43NUg5MC4wNGMtMy4xNywwLTUuNzUtMi41Ny01Ljc1LTUuNzV2LTMyLjE0DQoJYzAtMy4xNywyLjU3LTUuNzUsNS43NS01Ljc1aDExMS43MkMyMDQuOTMsMTA1LjU5LDIwNy41LDEwOC4xNiwyMDcuNSwxMTEuMzN6Ii8+DQo8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMjA3LjUsMTkxLjN2MzIuMTRjMCwzLjE3LTIuNTcsNS43NS01Ljc1LDUuNzVIOTAuMDRjLTMuMTcsMC01Ljc1LTIuNTctNS43NS01Ljc1VjE5MS4zDQoJYzAtMy4xNywyLjU3LTUuNzUsNS43NS01Ljc1aDExMS43MkMyMDQuOTMsMTg1LjU1LDIwNy41LDE4OC4xMywyMDcuNSwxOTEuM3oiLz4NCjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0xNjkuNjIsMjUuNjJoMzIuMTRjMy4xNywwLDUuNzUsMi41Nyw1Ljc1LDUuNzV2MTEyLjFjMCwzLjE3LTIuNTcsNS43NS01Ljc1LDUuNzVoLTMyLjE0DQoJYy0zLjE3LDAtNS43NS0yLjU3LTUuNzUtNS43NVYzMS4zN0MxNjMuODcsMjguMiwxNjYuNDQsMjUuNjIsMTY5LjYyLDI1LjYyeiIvPg0KPC9zdmc+DQo=';
	}

	/**
	 * Render the dashboard page.
	 */
	public function plugin_page() {
		if ( \ElementPack\Admin\Admin_Cutover::is_default() ) {
			\ElementPack\Admin\React_App::render_root();
			return;
		}

		\ElementPack\Admin\React_App::render_build_missing_notice();
	}

	/**
	 * Handle AJAX plugin installation.
	 *
	 * @access public
	 * @return void
	 */
	public function install_plugin_ajax() {
		// Check nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ep_install_plugin_nonce' ) ) {
			wp_send_json_error(['message' => __('Security check failed', 'bdthemes-element-pack-lite')]);
		}

		// Check user capability
		if (!current_user_can('install_plugins')) {
			wp_send_json_error(['message' => __('You do not have permission to install plugins', 'bdthemes-element-pack-lite')]);
		}

		$plugin_slug = isset($_POST['plugin_slug']) ? sanitize_key(wp_unslash($_POST['plugin_slug'])) : '';

		if (empty($plugin_slug)) {
			wp_send_json_error(['message' => __('Plugin slug is required', 'bdthemes-element-pack-lite')]);
		}

		// Include necessary WordPress files
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';

		// Get plugin information
		$api = plugins_api('plugin_information', [
			'slug' => $plugin_slug,
			'fields' => [
				'sections' => false,
			],
		]);

		if (is_wp_error($api)) {
			wp_send_json_error(['message' => __('Plugin not found: ', 'bdthemes-element-pack-lite') . $api->get_error_message()]);
		}

		// Install the plugin
		$skin = new \WP_Ajax_Upgrader_Skin();
		$upgrader = new \Plugin_Upgrader($skin);
		$result = $upgrader->install($api->download_link);

		if (is_wp_error($result)) {
			wp_send_json_error(['message' => __('Installation failed: ', 'bdthemes-element-pack-lite') . $result->get_error_message()]);
		} elseif ($skin->get_errors()->has_errors()) {
			wp_send_json_error(['message' => __('Installation failed: ', 'bdthemes-element-pack-lite') . $skin->get_error_messages()]);
		} elseif (is_null($result)) {
			wp_send_json_error(['message' => __('Installation failed: Unable to connect to filesystem', 'bdthemes-element-pack-lite')]);
		}

		// Get installation status
		$install_status = install_plugin_install_status($api);
		
		wp_send_json_success([
			'message' => __('Plugin installed successfully!', 'bdthemes-element-pack-lite'),
			'plugin_file' => $install_status['file'],
			'plugin_name' => $api->name
		]);
	}
}

new ElementPack_Admin_Settings();
