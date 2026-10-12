/**
 * Extensions tier page (Elementor extends — no Elementor usage data applies).
 */
import { __ } from '@wordpress/i18n';
import ModulesPage from './ModulesPage';

export default function Extensions() {
	return (
		<ModulesPage
			tier="extensions"
			title={ __( 'Extensions', 'bdthemes-element-pack-lite' ) }
		/>
	);
}
