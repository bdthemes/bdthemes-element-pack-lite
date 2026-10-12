/**
 * Widget categories for the catalog filter chips.
 *
 * The taxonomy is the one which-addon.com uses to classify Elementor widgets
 * (its ten category archives), so a widget sits where someone comparing addons
 * would expect to find it. Every widget Element Pack ships today is listed in
 * MODULE_CATEGORIES below — which-addon's own Element Pack entries where it has
 * one, and the closest category of the same ten otherwise. RULES only catch
 * slugs that map names (a widget added after this list was written).
 *
 * Accent names map to utils/accents.js so chips and card tiles share color.
 */
import { __ } from '@wordpress/i18n';

export const CATEGORIES = {
	content: { label: __( 'Content', 'bdthemes-element-pack-lite' ), accent: 'blue' },
	creative: {
		label: __( 'Creative', 'bdthemes-element-pack-lite' ),
		accent: 'violet',
	},
	navigation: {
		label: __( 'Menu / Navigation', 'bdthemes-element-pack-lite' ),
		accent: 'indigo',
	},
	dynamic: {
		label: __( 'Posts / Dynamic', 'bdthemes-element-pack-lite' ),
		accent: 'green',
	},
	woocommerce: {
		label: __( 'WooCommerce', 'bdthemes-element-pack-lite' ),
		accent: 'woo',
	},
	global: {
		label: __( 'Global', 'bdthemes-element-pack-lite' ),
		accent: 'teal',
	},
	social: { label: __( 'Social', 'bdthemes-element-pack-lite' ), accent: 'amber' },
	forms: { label: __( 'Forms', 'bdthemes-element-pack-lite' ), accent: 'blue' },
	special: {
		label: __( 'Special Features', 'bdthemes-element-pack-lite' ),
		accent: 'rose',
	},
};

/**
 * Slug → category for everything currently shipping (widgets and extensions).
 * Grouped by category so the assignment stays readable.
 */
export const MODULE_CATEGORIES = {};

const assign = ( category, slugs ) =>
	slugs.forEach( ( slug ) => {
		MODULE_CATEGORIES[ slug ] = category;
	} );

assign( 'content', [
	'accordion',
	'advanced-button',
	'advanced-heading',
	'advanced-icon-box',
	'advanced-gmap',
	'animated-card',
	'animated-heading',
	'animated-link',
	'audio-player',
	'brand-carousel',
	'brand-grid',
	'business-hours',
	'calendly',
	'changelog',
	'circle-info',
	'comparison-list',
	'content-switcher',
	'coupon-code',
	'creative-button',
	'custom-carousel',
	'document-viewer',
	'dual-button',
	'faq',
	'fancy-list',
	'fancy-tabs',
	'featured-box',
	'interactive-tabs',
	'logo-carousel',
	'logo-grid',
	'lottie-icon-box',
	'offcanvas',
	'open-street-map',
	'price-list',
	'price-table',
	'profile-card',
	'static-carousel',
	'static-grid-tab',
	'step-flow',
	'svg-maps',
	'switcher',
	'table',
	'tablepress',
	'tabs',
	'testimonial-carousel',
	'testimonial-grid',
	'testimonial-slider',
	'time-zone',
	'toggle',
	'video-gallery',
	'video-player',
	'weather',
] );

assign( 'creative', [
	'advanced-counter',
	'advanced-divider',
	'advanced-image-gallery',
	'advanced-progress-bar',
	'chart',
	'countdown',
	'custom-gallery',
	'device-slider',
	'fancy-slider',
	'flip-box',
	'horizontal-scroller',
	'hover-box',
	'hover-video',
	'image-compare',
	'image-expand',
	'image-magnifier',
	'image-stack',
	'interactive-card',
	'lightbox',
	'lottie-image',
	'marker',
	'marquee',
	'modal',
	'panel-slider',
	'portfolio-carousel',
	'portfolio-gallery',
	'portfolio-list',
	'progress-pie',
	'revolution-slider',
	'scroll-image',
	'slider',
	'slideshow',
	'stacker',
	'svg-blob',
	'svg-image',
	'total-count',
	'trailer-box',
] );

assign( 'navigation', [
	'breadcrumbs',
	'circle-menu',
	'floating-knowledgebase',
	'helpdesk',
	'icon-mobile-menu',
	'iconnav',
	'mega-menu',
	'navbar',
	'reading-progress',
	'scroll-button',
	'scrollnav',
	'sub-menu',
	'table-of-content',
	'vertical-menu',
] );

assign( 'dynamic', [
	'acf-accordion',
	'acf-gallery',
	'acf-list',
	'acf-slider',
	'acf-tabs',
	'carousel',
	'comment',
	'dynamic-carousel',
	'dynamic-grid',
	'events-calendar-carousel',
	'events-calendar-grid',
	'events-calendar-list',
	'fooevents-calendar',
	'fooevents-calendar-carousel',
	'fooevents-calendar-events',
	'learnpress-carousel',
	'learnpress-grid',
	'news-ticker',
	'reading-timer',
	'tutor-lms-course-carousel',
	'tutor-lms-course-grid',
] );

assign( 'woocommerce', [
	'product-carousel',
	'product-grid',
	'threesixty-product-viewer',
	'wc-add-to-cart',
	'wc-carousel',
	'wc-categories',
	'wc-elements',
	'wc-mini-cart',
	'wc-products',
	'wc-slider',
] );

assign( 'global', [
	'adblock-detector',
	'background-expand',
	'animated-gradient-background',
	'backdrop-filter',
	'background-image-parallax',
	'background-overlay',
	'background-parallax',
	'confetti-effects',
	'cursor-effects',
	'custom-js',
	'equal-height',
	'floating-effects',
	'grid-line',
	'global-badge',
	'hash-link',
	'image-hover-effects',
	'image-masking',
	'image-parallax',
	'notation',
	'parallax-effects',
	'particles',
	'realistic-image-shadow',
	'remote-arrows',
	'remote-fraction',
	'remote-pagination',
	'remote-thumbs',
	'reveal-effects',
	'ripple-effects',
	'scroll-box',
	'scroll-fill-effect',
	'section-sticky',
	'shape-builder',
	'shape-divider',
	'sound-effects',
	'text-gradient-background',
	'threed-text',
	'tile-scroll',
	'tooltip',
	'transform-effects',
	'wrapper-link',
] );

assign( 'social', [
	'facebook-feed',
	'facebook-feed-carousel',
	'fancy-icons',
	'google-reviews',
	'instagram',
	'instagram-feed',
	'review-card',
	'review-card-carousel',
	'review-card-grid',
	'social-proof',
	'social-share',
	'twitter-carousel',
	'twitter-grid',
	'twitter-slider',
] );

assign( 'forms', [
	'advanced-calculator',
	'contact-form',
	'contact-form-7',
	'fluent-forms',
	'formidable-forms',
	'forminator-forms',
	'gravity-forms',
	'mailchimp',
	'mailchimp-for-wp',
	'ninja-forms',
	'quform',
	'search',
	'the-newsletter',
	'user-login',
	'user-register',
	'we-forms',
	'webhook-form',
	'wp-forms',
] );

assign( 'special', [
	'barcode',
	'content-protector',
	'cookie-consent',
	'iframe',
	'notification',
	'protected-content',
	'qrcode',
	'source-code',
	'visibility-controls',
] );

/**
 * Fallback rules for slugs the map does not know yet — first match wins,
 * "Content" is the catch-all, exactly as which-addon treats it.
 */
const RULES = [
	// Shop widgets are all wc-/edd- prefixed; a loose "product|cart|shop"
	// match wrongly pulled generic widgets (product-grid, product-carousel,
	// threesixty-product-viewer) into WooCommerce.
	[ 'woocommerce', /^wc-|^edd-|woocommerce/ ],
	[
		'forms',
		/form|subscribe|mailchimp|login|register|newsletter|coupon-form|calculator|search/,
	],
	[
		'dynamic',
		/post|blog|news|article|timeline|archive|author|acf|dynamic|query|repeater|meta|course|event/,
	],
	[ 'navigation', /menu|nav|breadcrumb|table-of-content|scroll-button/ ],
	[
		'social',
		/social|facebook|twitter|instagram|share|feed|whatsapp|telegram|discord|tiktok|review/,
	],
	[ 'special', /protect|visibilit|cookie|qr|barcode|iframe|source-code/ ],
	[
		'global',
		/effect|parallax|particle|sticky|tooltip|global|badge|background-|section|column|equal-height|wrapper|grid-line/,
	],
	[
		'creative',
		/image|gallery|slider|carousel|slideshow|lottie|svg|shape|animated|3d|counter|countdown|progress|chart|divider|flip|hover|marquee|magnifier|lightbox|modal/,
	],
];

/**
 * @param {string} slug Module slug, e.g. "advanced-image-gallery".
 * @return {string} Category key from CATEGORIES.
 */
export function categoryKeyFor( slug ) {
	const value = String( slug || '' ).toLowerCase();

	if ( MODULE_CATEGORIES[ value ] ) {
		return MODULE_CATEGORIES[ value ];
	}

	for ( const [ key, pattern ] of RULES ) {
		if ( pattern.test( value ) ) {
			return key;
		}
	}

	return 'content';
}

export function categoryFor( slug ) {
	return CATEGORIES[ categoryKeyFor( slug ) ];
}

/**
 * Category of a module row. Third-party widgets are classified by what they do,
 * like everything else — the same widget belongs in the same chip whether
 * Element Pack or another plugin provides the data behind it.
 *
 * @param {Object} module Module row from the modules endpoint.
 * @return {string} Category key from CATEGORIES.
 */
export function categoryKeyForModule( module ) {
	return categoryKeyFor( module ? module.name : '' );
}
