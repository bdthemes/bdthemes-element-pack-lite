<?php
/**
 * Backwards-compatible aliases for renamed global functions.
 *
 * Every public global function in this plugin now carries the `element_pack_`
 * prefix. The old, unprefixed names are kept here as thin forwarding wrappers so
 * Element Pack Pro, child themes and third-party integrations that still call
 * them keep working. They are deprecated: new code should call the prefixed
 * function directly, and this file can be dropped in a future major release.
 *
 * Each wrapper is guarded with function_exists() so it never collides with a
 * definition supplied by another BdThemes plugin.
 *
 * @package Element Pack
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- Deprecated aliases; the prefixed functions they forward to are the canonical API.

if ( ! function_exists( 'is_ep_pro' ) ) {
	/**
	 * @deprecated Use element_pack_is_pro() instead.
	 */
	function is_ep_pro() {
		return element_pack_is_pro();
	}
}

if ( ! function_exists( 'ep_is_dashboard_enabled' ) ) {
	/**
	 * @deprecated Use element_pack_is_dashboard_enabled() instead.
	 */
	function ep_is_dashboard_enabled() {
		return element_pack_is_dashboard_enabled();
	}
}

if ( ! function_exists( 'bdt_get_widget_badge' ) ) {
	/**
	 * @deprecated Use element_pack_get_widget_badge() instead.
	 */
	function bdt_get_widget_badge( $widget_name ) {
		return element_pack_get_widget_badge( $widget_name );
	}
}

if ( ! function_exists( 'bdt_license_validation' ) ) {
	/**
	 * @deprecated Use element_pack_license_validation() instead.
	 */
	function bdt_license_validation() {
		return element_pack_license_validation();
	}
}

if ( ! function_exists( 'ep_crypto' ) ) {
	/**
	 * @deprecated Use element_pack_crypto() instead.
	 */
	function ep_crypto() {
		return element_pack_crypto();
	}
}

if ( ! function_exists( 'ep_crypto_data' ) ) {
	/**
	 * @deprecated Use element_pack_crypto_data() instead.
	 */
	function ep_crypto_data() {
		return element_pack_crypto_data();
	}
}

if ( ! function_exists( 'ep_display_quantity_minus' ) ) {
	/**
	 * @deprecated Use element_pack_display_quantity_minus() instead.
	 */
	function ep_display_quantity_minus() {
		return element_pack_display_quantity_minus();
	}
}

if ( ! function_exists( 'ep_display_quantity_plus' ) ) {
	/**
	 * @deprecated Use element_pack_display_quantity_plus() instead.
	 */
	function ep_display_quantity_plus() {
		return element_pack_display_quantity_plus();
	}
}

if ( ! function_exists( 'ep_add_cart_quantity_plus_minus' ) ) {
	/**
	 * @deprecated Use element_pack_add_cart_quantity_plus_minus() instead.
	 */
	function ep_add_cart_quantity_plus_minus() {
		return element_pack_add_cart_quantity_plus_minus();
	}
}

if ( ! function_exists( 'ep_setup_quantity_buttons' ) ) {
	/**
	 * @deprecated Use element_pack_setup_quantity_buttons() instead.
	 */
	function ep_setup_quantity_buttons() {
		return element_pack_setup_quantity_buttons();
	}
}

if ( ! function_exists( 'ep_is_main_site' ) ) {
	/**
	 * @deprecated Use element_pack_is_main_site() instead.
	 */
	function ep_is_main_site() {
		return element_pack_is_main_site();
	}
}

if ( ! function_exists( 'ep_get_subsites' ) ) {
	/**
	 * @deprecated Use element_pack_get_subsites() instead.
	 */
	function ep_get_subsites() {
		return element_pack_get_subsites();
	}
}

if ( ! function_exists( 'ep_get_subsite_activation_source' ) ) {
	/**
	 * @deprecated Use element_pack_get_subsite_activation_source() instead.
	 */
	function ep_get_subsite_activation_source( $site_id ) {
		return element_pack_get_subsite_activation_source( $site_id );
	}
}

// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
