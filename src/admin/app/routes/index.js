/**
 * Route + navigation definitions.
 *
 * Pages are lazy-loaded for route-based code splitting; webpack emits a
 * separate chunk per page so the initial dashboard load stays lean.
 *
 * `navKey` maps to `window.elementPackAdmin.navVisibility` from PHP.
 * `group` drives where the route surfaces in the shell:
 *   - 'primary'  → tab strip
 *   - 'settings' → Settings hub sub-nav (two-pane layout)
 *   - 'more'     → the "More" dropdown at the end of the tab strip (unused for now;
 *                  tabs that don't fit the width still overflow into it)
 */
import { lazy } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	DashboardIcon,
	GridIcon,
	PuzzleIcon,
	PlugIcon,
	GaugeIcon,
	ShieldIcon,
	StoreIcon,
	ZapIcon,
} from '../components/ui/Icons';

const Dashboard = lazy( () => import( '../pages/Dashboard' ) );
const WidgetsPage = lazy( () => import( '../pages/WidgetsPage' ) );
const Extensions = lazy( () => import( '../pages/Extensions' ) );
const SettingsPage = lazy( () => import( '../pages/SettingsPage' ) );
const ApiSettingsPage = lazy( () => import( '../pages/ApiSettingsPage' ) );
const PerformancePage = lazy( () => import( '../pages/PerformancePage' ) );
const SystemStatusPage = lazy( () => import( '../pages/SystemStatusPage' ) );
const OtherPluginsPage = lazy( () => import( '../pages/OtherPluginsPage' ) );

/**
 * Each item drives both the shell navigation and the router.
 */
export const routes = [
	{
		navKey: 'dashboard',
		path: '/',
		label: __( 'Dashboard', 'bdthemes-element-pack-lite' ),
		icon: DashboardIcon,
		element: Dashboard,
		group: 'primary',
		end: true,
	},
	{
		// Core widgets and 3rd-party integrations share this tab; the page
		// lists both tiers and filters the latter under a "3rd Party" chip.
		navKey: 'widgets',
		path: '/widgets',
		label: __( 'Widgets', 'bdthemes-element-pack-lite' ),
		icon: GridIcon,
		element: WidgetsPage,
		group: 'primary',
	},
	{
		navKey: 'extensions',
		path: '/extensions',
		label: __( 'Extensions', 'bdthemes-element-pack-lite' ),
		icon: PuzzleIcon,
		element: Extensions,
		group: 'primary',
	},
	{
		// Features lives at /settings for legacy-hash compatibility but
		// renders as its own primary tab (after Extensions), outside the
		// Settings hub.
		navKey: 'settings',
		path: '/settings',
		label: __( 'Features', 'bdthemes-element-pack-lite' ),
		icon: ZapIcon,
		element: SettingsPage,
		group: 'primary',
		end: true,
	},
	{
		navKey: 'settings_api',
		path: '/integrations',
		label: __( 'Integrations', 'bdthemes-element-pack-lite' ),
		icon: PlugIcon,
		element: ApiSettingsPage,
		group: 'primary',
	},
	{
		navKey: 'settings_perf',
		path: '/settings/perf',
		label: __( 'Performance', 'bdthemes-element-pack-lite' ),
		icon: GaugeIcon,
		element: PerformancePage,
		group: 'primary',
	},
	{
		navKey: 'system_status',
		path: '/system-status',
		label: __( 'System Status', 'bdthemes-element-pack-lite' ),
		icon: ShieldIcon,
		element: SystemStatusPage,
		group: 'primary',
	},
	{
		navKey: 'other_plugins',
		path: '/other-plugins',
		label: __( 'Other Plugins', 'bdthemes-element-pack-lite' ),
		icon: StoreIcon,
		element: OtherPluginsPage,
		group: 'primary',
	},
];

export const settingsRoutes = routes.filter(
	( route ) => route.group === 'settings'
);

export const primaryRoutes = routes.filter(
	( route ) => route.group === 'primary'
);

export const moreRoutes = routes.filter( ( route ) => route.group === 'more' );

export default routes;
