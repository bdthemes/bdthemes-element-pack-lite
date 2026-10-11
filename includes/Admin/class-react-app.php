<?php
/**
 * React admin SPA mount + asset loader.
 *
 * Renders on {@see React_App::MENU_SLUG} when the compiled build exists.
 * Disable with: define( 'BDTEP_DISABLE_REACT_ADMIN', true );
 *
 * @package ElementPack\Admin
 */

namespace ElementPack\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class React_App {

	const MENU_SLUG     = 'element_pack_options';
	const SCRIPT_HANDLE = 'element-pack-admin-app';

	public function __construct() {
		if ( ! self::is_enabled() ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_filter( 'admin_body_class', [ $this, 'filter_admin_body_class' ] );
	}

	/**
	 * Whether the React admin is active.
	 */
	public static function is_enabled(): bool {
		if ( defined( 'BDTEP_DISABLE_REACT_ADMIN' ) && BDTEP_DISABLE_REACT_ADMIN ) {
			return false;
		}

		return (bool) apply_filters( 'element_pack/admin/react_enabled', true );
	}

	private function build_path(): string {
		return BDTEP_PATH . 'build/admin/';
	}

	private function build_url(): string {
		return BDTEP_URL . 'build/admin/';
	}

	/**
	 * Print the SPA mount node (called from the admin page callback).
	 */
	public static function render_root(): void {
		echo '<div class="wrap ep-react-wrap"><div id="ep-admin-app" class="ep-admin-app">';
		echo '<noscript>' . esc_html__( 'This dashboard requires JavaScript to be enabled.', 'bdthemes-element-pack-lite' ) . '</noscript>';
		echo '</div></div>';
	}

	/**
	 * Show a build-missing notice when the SPA bundle is unavailable.
	 */
	public static function render_build_missing_notice(): void {
		echo '<div class="wrap"><div class="notice notice-error"><p>';
		echo esc_html__( 'The Element Pack admin app has not been built. Run "npm run build:admin" in the plugin folder.', 'bdthemes-element-pack-lite' );
		echo '</p></div></div>';
	}

	/**
	 * @param string $classes
	 * @return string
	 */
	public function filter_admin_body_class( $classes ) {
		if ( ! self::is_admin_screen() ) {
			return $classes;
		}

		return $classes . ' ep-react-admin';
	}

	/**
	 * @param string $hook
	 */
	public function enqueue_assets( $hook ): void {
		unset( $hook );

		if ( ! self::is_admin_screen() ) {
			return;
		}

		$asset_file = $this->build_path() . 'index.asset.php';

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$asset = require $asset_file;

		$deps = isset( $asset['dependencies'] ) ? $asset['dependencies'] : [];
		$ver  = isset( $asset['version'] ) ? $asset['version'] : BDTEP_VER;

		wp_enqueue_script(
			self::SCRIPT_HANDLE,
			$this->build_url() . 'index.js',
			$deps,
			$ver,
			true
		);

		foreach ( [ 'style-index.css', 'index.css' ] as $css ) {
			if ( file_exists( $this->build_path() . $css ) ) {
				wp_enqueue_style(
					self::SCRIPT_HANDLE . '-' . sanitize_key( $css ),
					$this->build_url() . $css,
					[ 'wp-components' ],
					$ver
				);
			}
		}

		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( self::SCRIPT_HANDLE, 'bdthemes-element-pack-lite' );
		}

		wp_localize_script( self::SCRIPT_HANDLE, 'elementPackAdmin', $this->get_bootstrap_config() );
	}

	/**
	 * @return bool
	 */
	public static function is_admin_screen(): bool {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only screen check, no state is changed.
		if ( ! isset( $_GET['page'] ) ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only screen check, no state is changed.
		return self::MENU_SLUG === sanitize_key( wp_unslash( $_GET['page'] ) );
	}

	/**
	 * @return array<string,mixed>
	 */
	private function get_bootstrap_config(): array {
		return [
			'restUrl'        => esc_url_raw( rest_url( 'element-pack/v1/' ) ),
			'restNamespace'  => 'element-pack/v1',
			'nonce'          => wp_create_nonce( 'wp_rest' ),
			'adminUrl'       => esc_url_raw( admin_url() ),
			'pageSlug'       => self::MENU_SLUG,
			'reactDefault'   => Admin_Cutover::is_default(),
			'assetsUrl'      => defined( 'BDTEP_ASSETS_URL' ) ? esc_url_raw( BDTEP_ASSETS_URL ) : '',
			'adminAssetsUrl' => defined( 'BDTEP_ADMIN_URL' ) ? esc_url_raw( BDTEP_ADMIN_URL . 'assets/images/' ) : '',
			'pluginUrl'      => esc_url_raw( BDTEP_URL ),
			'version'        => BDTEP_VER,
			'isPro'          => function_exists( 'element_pack_pro_activated' ) ? (bool) element_pack_pro_activated() : false,
			'upgradeUrl'     => 'https://www.elementpack.pro/pricing/',
			'capabilities'   => [
				'manage_options' => current_user_can( 'manage_options' ),
			],
			'navVisibility'  => Menu_Visibility_Service::get_nav_visibility(),
			'externalNav'    => External_Nav_Service::get_items(),
			'setupWizardUrl' => esc_url_raw( admin_url( '?ep_setup_wizard=show' ) ),
			'docsUrl'        => 'https://bdthemes.com/knowledge-base/element-pack/',
			'whatsNewUrl'    => 'https://feedback.elementpack.pro/announcements/',
			'supportUrl'     => 'https://bdthemes.com/support/',
			'userName'       => wp_get_current_user()->display_name,
		];
	}
}
