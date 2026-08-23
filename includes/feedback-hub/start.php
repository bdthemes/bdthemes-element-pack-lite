<?php
/**
 * Main File
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * The `rc_*` names below belong to the shared BdThemes Review Collector SDK,
 * which is vendored identically into several BdThemes plugins. The unprefixed
 * name IS the cross-plugin lock: the first plugin to load defines it and the
 * function_exists() guards stop every other copy from loading, so exactly one
 * review prompt is ever registered. Prefixing them per plugin would defeat that
 * and show the user one review notice per installed BdThemes plugin.
 */
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- Shared SDK entry point; the name is the cross-plugin de-duplication lock.
if ( ! function_exists( 'rc_dynamic_init' ) ) {
	function rc_dynamic_init( $params ) {

		if ( is_admin() ) :

			$menu_slug    = isset( $params['menu']['slug'] ) ? $params['menu']['slug'] : false;
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only screen detection, no state is changed.
			$current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : false;

			/**
			 * Attach SDK to current page
			 */
			$params['current_page'] = $current_page;
			$params['menu_slug']    = $menu_slug;

			/**
			 * Include SDK
			 */
			require_once dirname( __FILE__ ) . '/rc-biggopti.php';
			if ( function_exists( 'rc_sdk_automate' ) ) {
				rc_sdk_automate( $params );
			}

		endif;
	}
}
