/**
 * Classic wp-admin screens surfaced inside the React nav.
 *
 * PHP supplies them through the `element_pack/admin/external_nav` filter
 * (window.elementPackAdmin.externalNav). They are plain links rather than SPA
 * routes, so each item carries an `href` instead of a `path` and never takes
 * an active state — following one leaves the app.
 *
 * `group` decides where an item surfaces: 'primary' puts it in the tab strip,
 * anything else (the default) leaves it in the "More" menu. An optional
 * `after` names the navKey it should follow in the strip.
 */
import config from './config';
import { isNavVisible } from './navVisibility';
import { SparklesIcon, ExternalIcon } from '../components/ui/Icons';

const ICONS = {
	sparkles: SparklesIcon,
};

/**
 * @param {string} group 'primary' for the tab strip, 'more' for the dropdown.
 * @return {Array} Tab descriptors — { key, icon, label, href, active }.
 */
export function getExternalNavItems( group = 'more' ) {
	return ( config.externalNav || [] )
		.filter(
			( item ) =>
				item &&
				item.url &&
				item.label &&
				( item.group || 'more' ) === group &&
				isNavVisible( item.key )
		)
		.map( ( item ) => ( {
			key: `external:${ item.key }`,
			icon: ICONS[ item.icon ] || ExternalIcon,
			label: item.label,
			href: item.url,
			after: item.after || '',
			active: false,
		} ) );
}
