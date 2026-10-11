/**
 * Legacy PHP dashboard hash fragments → React Router paths.
 */
export const LEGACY_HASH_ROUTES = {
	element_pack_welcome: '/',
	element_pack_welcome_page: '/',
	element_pack_active_modules: '/widgets',
	element_pack_active_modules_page: '/widgets',
	element_pack_third_party_widget: '/widgets',
	element_pack_third_party_widget_page: '/widgets',
	element_pack_elementor_extend: '/extensions',
	element_pack_elementor_extend_page: '/extensions',
	element_pack_other_settings: '/settings',
	element_pack_other_settings_page: '/settings',
	element_pack_api_settings: '/integrations',
	element_pack_api_settings_page: '/integrations',
	element_pack_analytics_system_req: '/system-status',
	element_pack_analytics_system_req_page: '/system-status',
	element_pack_other_plugins: '/other-plugins',
	element_pack_other_plugins_page: '/other-plugins',
	element_pack_get_pro: '/go-pro',
	element_pack_get_pro_page: '/go-pro',
};

export function legacyHashToRoute( hash ) {
	const key = String( hash || '' ).replace( /^#/, '' );

	if ( ! key || key.startsWith( '/' ) ) {
		return null;
	}

	return LEGACY_HASH_ROUTES[ key ] || null;
}
