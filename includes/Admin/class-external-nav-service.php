<?php
/**
 * External admin screens linked from the React sidebar (not SPA routes).
 *
 * @package ElementPack\Admin
 */

namespace ElementPack\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class External_Nav_Service {

	/**
	 * Nav items that open classic WP admin screens in the same tab.
	 *
	 * @return array<int,array<string,string>>
	 */
	public static function get_items() {
		// Theme Builder, Custom Fonts and Custom Icons used to live here as
		// classic wp-admin links; they are now native SPA routes
		// (/theme-builder, /custom-fonts, /custom-icons). What remains comes
		// through the filter below — AI Tools adds itself there, and third
		// parties can do the same.
		$items = [];

		/**
		 * Filter external nav items shown in the React admin nav.
		 *
		 * Each item is [ 'key', 'label', 'url', 'icon', 'group' ]; 'group' =>
		 * 'primary' puts it in the tab strip, anything else in "More".
		 *
		 * @param array<int,array<string,string>> $items
		 */
		return apply_filters( 'element_pack/admin/external_nav', $items );
	}
}
