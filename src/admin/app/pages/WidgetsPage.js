/**
 * Widgets page — Element Pack's own widgets and the 3rd-party integrations in
 * one catalog. They used to be two tabs; third-party rows are now reachable
 * through the "3rd Party" category chip.
 *
 * A visibility filter can hide one catalog and not the other, so only the
 * tiers the user may manage are listed.
 */
import { useMemo } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import ModulesPage from './ModulesPage';
import { isNavVisible, useNavVisibility } from '../utils/navVisibility';

export default function WidgetsPage() {
	// Re-render when the visibility map is refreshed at runtime.
	const visibility = useNavVisibility();

	const tiers = useMemo( () => {
		const list = [];
		if ( isNavVisible( 'core_widgets' ) ) {
			list.push( 'core' );
		}
		if ( isNavVisible( 'third_party' ) ) {
			list.push( 'third_party' );
		}
		return list.length ? list : [ 'core' ];
	}, [ visibility ] );

	return (
		<ModulesPage
			tier={ tiers }
			title={ __( 'Widgets', 'bdthemes-element-pack-lite' ) }
		/>
	);
}
