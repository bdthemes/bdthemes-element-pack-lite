/**
 * Contextual copy for module tier pages (widgets / extensions).
 */
import { __ } from '@wordpress/i18n';

export const TIER_LABELS = {
	core: {
		searchPlaceholder: __( 'Search widgets…', 'bdthemes-element-pack-lite' ),
		loading: __( 'Loading widgets…', 'bdthemes-element-pack-lite' ),
		emptyTitle: __(
			'No widgets match your filters',
			'bdthemes-element-pack-lite'
		),
		emptyDescription: __(
			'Try clearing the search or changing the filters.',
			'bdthemes-element-pack-lite'
		),
	},
	third_party: {
		searchPlaceholder: __(
			'Search integrations…',
			'bdthemes-element-pack-lite'
		),
		loading: __( 'Loading integrations…', 'bdthemes-element-pack-lite' ),
		emptyTitle: __(
			'No integrations match your filters',
			'bdthemes-element-pack-lite'
		),
		emptyDescription: __(
			'Try clearing the search or changing the filters.',
			'bdthemes-element-pack-lite'
		),
	},
	extensions: {
		searchPlaceholder: __( 'Search extensions…', 'bdthemes-element-pack-lite' ),
		loading: __( 'Loading extensions…', 'bdthemes-element-pack-lite' ),
		emptyTitle: __(
			'No extensions match your filters',
			'bdthemes-element-pack-lite'
		),
		emptyDescription: __(
			'Try clearing the search or changing the filters.',
			'bdthemes-element-pack-lite'
		),
	},
};

export function getTierLabels( tier ) {
	// A page may cover several tiers (Widgets = core + third_party); the core
	// copy already speaks of "widgets", so it doubles as the merged wording.
	const key = Array.isArray( tier ) ? tier[ 0 ] : tier;
	return TIER_LABELS[ key ] || TIER_LABELS.core;
}
