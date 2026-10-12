/**
 * Command palette registry. Navigation entries derive from the shared routes
 * (respecting navVisibility); action entries cover common shortcuts.
 */
import { __ } from '@wordpress/i18n';
import { routes } from '../routes';
import { isRouteVisible, isNavVisible } from './navVisibility';
import config from './config';

/**
 * Build the grouped command list.
 *
 * @param {Object}   handlers             Action callbacks from the shell.
 * @param {Function} handlers.navigate    react-router navigate().
 * @param {Function} handlers.toggleTheme Theme toggle.
 * @return {Array} groups of { label, commands: [ { id, label, hint, run } ] }
 */
export function buildCommands( { navigate, toggleTheme } ) {
	const navCommands = routes.filter( isRouteVisible ).map( ( route ) => ( {
		id: `nav:${ route.path }`,
		label: route.label,
		hint: __( 'Go to', 'bdthemes-element-pack-lite' ),
		icon: route.icon,
		run: () => navigate( route.path ),
	} ) );

	const actionCommands = [
		{
			id: 'action:theme',
			label: __( 'Toggle dark mode', 'bdthemes-element-pack-lite' ),
			hint: __( 'Action', 'bdthemes-element-pack-lite' ),
			run: toggleTheme,
		},
		config.docsUrl && {
			id: 'action:docs',
			label: __( 'Open documentation', 'bdthemes-element-pack-lite' ),
			hint: __( 'Action', 'bdthemes-element-pack-lite' ),
			run: () => window.open( config.docsUrl, '_blank', 'noreferrer' ),
		},
		config.setupWizardUrl && {
			id: 'action:wizard',
			label: __( 'Run setup wizard', 'bdthemes-element-pack-lite' ),
			hint: __( 'Action', 'bdthemes-element-pack-lite' ),
			run: () => {
				window.location.href = config.setupWizardUrl;
			},
		},
	].filter( Boolean );

	const externalCommands = ( config.externalNav || [] )
		.filter( ( item ) => isNavVisible( item.key ) )
		.map( ( item ) => ( {
			id: `external:${ item.key }`,
			label: item.label,
			hint: __( 'Open', 'bdthemes-element-pack-lite' ),
			run: () => {
				window.location.href = item.url;
			},
		} ) );

	return [
		{
			label: __( 'Navigation', 'bdthemes-element-pack-lite' ),
			commands: navCommands,
		},
		{
			label: __( 'Quick actions', 'bdthemes-element-pack-lite' ),
			commands: [ ...actionCommands, ...externalCommands ],
		},
	];
}

/**
 * Flatten + filter groups against a query string.
 *
 * @param {Array}  groups Command groups from buildCommands().
 * @param {string} query  Search text.
 * @return {Array} groups with non-matching commands removed.
 */
export function filterCommands( groups, query ) {
	const text = query.trim().toLowerCase();

	if ( ! text ) {
		return groups;
	}

	return groups
		.map( ( group ) => ( {
			...group,
			commands: group.commands.filter( ( command ) =>
				command.label.toLowerCase().includes( text )
			),
		} ) )
		.filter( ( group ) => group.commands.length > 0 );
}
