<?php

use ElementPack\Admin\AssetMinifier\Asset_Minifier;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'ElementPack_Settings_API' ) ) :

	/**
	 * Minimal settings API: the setup wizard's module save and Asset Manager
	 * cache busting. The dashboard itself saves through the REST API
	 * (includes/API/Controllers).
	 */
	class ElementPack_Settings_API {

		public function __construct() {
			add_action( 'wp_ajax_element_pack_settings_save', [ $this, 'element_pack_settings_save' ] );
			add_action( 'elementor/core/files/clear_cache', [ $this, 'clear_cache' ] );
		}

		/**
		 * Rebuild the Asset Manager bundle when Elementor's cache is cleared.
		 */
		public function clear_cache() {
			if ( element_pack_is_asset_optimization_enabled() ) {
				$optimize_assets = new Asset_Minifier();
				$optimize_assets->minifyCss();
				$optimize_assets->minifyJs();
				update_option( 'element-pack-minified-asset-manager-version', time() );
			} else {
				delete_option( 'element-pack-minified-asset-manager-version' );
			}

			return rest_ensure_response( [ 'success' => true ] );
		}

		/**
		 * AJAX handler for the setup wizard module activation step.
		 */
		public function element_pack_settings_save() {
			if ( ! check_ajax_referer( 'element-pack-settings-save-nonce' ) ) {
				wp_send_json_error();
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error();
			}

			$module_id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';

			// Only the module on/off maps are saved here, never an arbitrary option.
			$allowed = [ 'element_pack_active_modules', 'element_pack_third_party_widget', 'element_pack_elementor_extend' ];

			if ( ! in_array( $module_id, $allowed, true ) ) {
				wp_send_json_error();
			}

			unset( $_POST['id'] );

			if ( isset( $_POST[ $module_id ] ) && is_array( $_POST[ $module_id ] ) ) {
				$modules = [];

				foreach ( wp_unslash( $_POST[ $module_id ] ) as $module => $state ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Keys go through sanitize_key(), values are mapped to on/off.
					$modules[ sanitize_key( $module ) ] = 'on' === $state ? 'on' : 'off';
				}

				update_option( $module_id, $modules );
			}

			if ( element_pack_is_asset_optimization_enabled() ) {
				$optimize_assets = new Asset_Minifier();
				$optimize_assets->minifyCss();
				$optimize_assets->minifyJs();
				update_option( 'element-pack-minified-asset-manager-version', time() );
			} else {
				delete_option( 'element-pack-minified-asset-manager-version' );
			}

			wp_send_json_success();
		}
	}

endif;
