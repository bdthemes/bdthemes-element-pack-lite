<?php
/**
 * Which navigation entries the React dashboard shows.
 *
 * @package ElementPack\Admin
 */

namespace ElementPack\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Menu_Visibility_Service {

	/**
	 * Map of navKey => bool, exposed to the SPA as `elementPackAdmin.navVisibility`.
	 *
	 * Element Pack Lite has no role-based permission manager, so every screen it
	 * ships is visible to anyone who can reach the dashboard (`manage_options`).
	 * The `element_pack/admin/nav_visibility` filter lets an integration hide
	 * individual entries.
	 *
	 * @return array<string,bool>
	 */
	public static function get_nav_visibility() {
		$visibility = [
			'dashboard'     => true,
			'widgets'       => true,
			'core_widgets'  => true,
			'third_party'   => true,
			'extensions'    => true,
			'settings'      => true,
			'settings_api'  => true,
			'settings_perf' => true,
			'system_status' => true,
			'other_plugins' => true,
		];

		return (array) apply_filters( 'element_pack/admin/nav_visibility', $visibility );
	}
}
