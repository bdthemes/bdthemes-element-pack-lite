/**
 * Bootstrap config injected by PHP via wp_localize_script( 'elementPackAdmin' ).
 * Falls back to safe defaults so the app can still render in isolation/tests.
 */
const defaults = {
	restUrl: '/wp-json/element-pack/v1/',
	restNamespace: 'element-pack/v1',
	nonce: '',
	adminUrl: '/wp-admin/',
	pluginUrl: '',
	assetsUrl: '',
	adminAssetsUrl: '',
	version: '0.0.0',
	isPro: false,
	upgradeUrl: 'https://www.elementpack.pro/pricing/',
	capabilities: { manage_options: false },
	navVisibility: null,
	externalNav: [],
	setupWizardUrl: '',
	reactDefault: false,
	docsUrl: 'https://bdthemes.com/knowledge-base/element-pack/',
	whatsNewUrl: 'https://feedback.elementpack.pro/announcements/',
	supportUrl: 'https://bdthemes.com/support/',
	userName: '',
};

const config = { ...defaults, ...( window.elementPackAdmin || {} ) };

export default config;
