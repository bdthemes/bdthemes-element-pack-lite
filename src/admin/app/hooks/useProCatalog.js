/**
 * What Element Pack Pro adds on top of this install, derived from the same two
 * payloads the other screens use: the modules catalog (widgets + extensions,
 * each flagged free/pro) and the settings schema (special features).
 *
 * Everything on the Go Pro page is computed from this, so the numbers shown
 * never drift from what the plugin actually lists.
 */
import { useMemo } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { STORE_NAME } from '../store';
import { useSettings } from './useSettings';
import { categoryKeyFor } from '../utils/widgetCategories';

function split( modules ) {
	const pro = modules.filter( ( m ) => m.widget_type === 'pro' );
	const free = modules.filter( ( m ) => m.widget_type !== 'pro' );
	return { pro, free };
}

export function useProCatalog() {
	const { modules, isLoading, error } = useSelect( ( select ) => {
		const store = select( STORE_NAME );
		return {
			modules: store.getResource( 'modules' ),
			isLoading: store.isLoading( 'modules' ),
			error: store.getError( 'modules' ),
		};
	}, [] );
	const { data: settings } = useSettings();

	return useMemo( () => {
		const tiers = modules && modules.tiers ? modules.tiers : {};
		const core = tiers.core ? tiers.core.modules : [];
		const thirdParty = tiers.third_party ? tiers.third_party.modules : [];
		const extensions = tiers.extensions ? tiers.extensions.modules : [];

		const widgets = split( core.concat( thirdParty ) );
		const exts = split( extensions );

		const featureFields =
			settings && settings.sections && settings.sections.special_features
				? settings.sections.special_features.fields.filter(
						( f ) => f.type === 'checkbox'
				  )
				: [];
		const features = split( featureFields );

		const proWoo = widgets.pro.filter(
			( m ) => categoryKeyFor( m.name ) === 'woocommerce'
		).length;

		return {
			ready: !! modules && !! settings,
			isLoading,
			error,
			isPro: modules ? !! modules.is_pro : false,
			widgets: {
				pro: widgets.pro,
				free: widgets.free,
				total: widgets.pro.length + widgets.free.length,
			},
			extensions: {
				pro: exts.pro,
				free: exts.free,
				total: exts.pro.length + exts.free.length,
			},
			features: {
				pro: features.pro,
				free: features.free,
				total: features.pro.length + features.free.length,
			},
			proWoo,
			proTotal:
				widgets.pro.length + exts.pro.length + features.pro.length,
		};
	}, [ modules, settings, isLoading, error ] );
}

export default useProCatalog;
