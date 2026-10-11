/**
 * Dashboard overview — full mockup parity (element-pack-dashboard-markup,
 * index.html 266–620) on live data, including both layout variants:
 *
 * - Variant A "Command": stat row, health strip, quick actions, get-started
 *   checklist, suggestions, what's-new promo, system status, and the
 *   top-widgets / activity / resources insight row.
 * - Variant B "Focus": dark hero with per-tier activation chart, widgets-in-
 *   use grid, activity timeline.
 *
 * The Command/Focus switcher persists in localStorage. All values come from
 * real endpoints.
 */
import { useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import { STORE_NAME } from '../store';
import Spinner from '../components/Spinner';
import ErrorState from '../components/ErrorState';
import EmptyState from '../components/EmptyState';
import UpgradeNotice from '../components/UpgradeNotice';
import DashboardStatCard from '../components/dashboard/DashboardStatCard';
import HealthStrip from '../components/dashboard/HealthStrip';
import AiInsightsCard from '../components/dashboard/AiInsightsCard';
import ActivityCard from '../components/dashboard/ActivityCard';
import WhatsNewPromoCard from '../components/dashboard/WhatsNewPromoCard';
import QuickActions from '../components/dashboard/QuickActions';
import GetStartedCard from '../components/dashboard/GetStartedCard';
import SystemStatusCard from '../components/dashboard/SystemStatusCard';
import MostUsedCard from '../components/dashboard/MostUsedCard';
import ResourcesCard from '../components/dashboard/ResourcesCard';
import FocusHero from '../components/dashboard/FocusHero';
import RecentWidgetsGrid from '../components/dashboard/RecentWidgetsGrid';
import {
	GridIcon,
	PuzzleIcon,
	ZapIcon,
	SlidersIcon,
	GaugeIcon,
	PlugIcon,
} from '../components/ui/Icons';
import config from '../utils/config';
import { isNavVisible, useNavVisibility } from '../utils/navVisibility';

const VARIANT_KEY = 'epDashVariant';

function percent( part, total ) {
	if ( ! total ) {
		return 0;
	}
	return Math.round( ( part / total ) * 100 );
}

export default function Dashboard() {
	const { fetchResource } = useDispatch( STORE_NAME );
	const [ variant, setVariant ] = useState(
		() => window.localStorage.getItem( VARIANT_KEY ) || 'A'
	);

	const pickVariant = ( value ) => {
		setVariant( value );
		window.localStorage.setItem( VARIANT_KEY, value );
	};

	const { stats, isLoading, error } = useSelect( ( select ) => {
		const store = select( STORE_NAME );
		return {
			stats: store.getResource( 'dashboard' ),
			isLoading: store.isLoading( 'dashboard' ),
			error: store.getError( 'dashboard' ),
		};
	}, [] );

	// Resolver-backed initial load; getResource('dashboard') triggers it.
	useSelect(
		( select ) => select( STORE_NAME ).getResource( 'dashboard' ),
		[]
	);

	const trackingAllowed = !! stats?.tracking_allowed;

	// Modules payload is only needed for the usage leaderboards.
	const modules = useSelect(
		( select ) =>
			trackingAllowed
				? select( STORE_NAME ).getResource( 'modules' )
				: null,
		[ trackingAllowed ]
	);

	const retry = () => fetchResource( 'dashboard', 'dashboard' );

	const isPro = stats ? stats.is_pro : config.isPro;

	const welcome = config.userName
		? sprintf(
				/* translators: %s: user display name */
				__( 'Welcome back, %s', 'bdthemes-element-pack-lite' ),
				config.userName
		  )
		: __( 'Welcome back', 'bdthemes-element-pack-lite' );

	const switcherBtn = ( value, label, SwitchIcon ) => {
		const active = variant === value;
		return (
			<button
				type="button"
				onClick={ () => pickVariant( value ) }
				aria-pressed={ active }
				className={ `flex items-center gap-[6px] border-0 rounded-full py-[6px] px-[14px] font-sans text-[12.5px] font-semibold cursor-pointer transition-colors ${
					active
						? 'bg-[#18181b] text-white dark:bg-brand'
						: 'bg-transparent text-[#71717a] dark:text-ink-muted hover:bg-[#f3f5f8] dark:hover:bg-[#1f242d] hover:text-[#18181b] dark:hover:text-ink-text'
				}` }
			>
				<SwitchIcon size={ 13 } />
				{ label }
			</button>
		);
	};

	return (
		<div>
			<div className="flex items-end justify-between gap-4 flex-wrap mb-[22px]">
				<div>
					<h1 className="text-[31px] leading-[1.15] font-extrabold tracking-[-.025em] text-[#0f1115] dark:text-ink-text m-0">
						{ welcome }
					</h1>
					<p className="text-[14.5px] text-[#71717a] dark:text-ink-muted mt-[6px] mb-0">
						{ __(
							"Here's what's happening across your install.",
							'bdthemes-element-pack-lite'
						) }
					</p>
				</div>
				<div
					role="group"
					aria-label={ __(
						'Dashboard layout',
						'bdthemes-element-pack-lite'
					) }
					className="flex items-center gap-1 bg-white dark:bg-ink-card border border-[#e6e6ea] dark:border-ink-border rounded-full p-1"
				>
					{ switcherBtn(
						'A',
						__( 'Command', 'bdthemes-element-pack-lite' ),
						GridIcon
					) }
					{ switcherBtn(
						'B',
						__( 'Focus', 'bdthemes-element-pack-lite' ),
						GaugeIcon
					) }
				</div>
			</div>

			<UpgradeNotice isPro={ isPro } />

			{ isLoading && ! stats && (
				<Spinner
					full
					label={ __(
						'Loading dashboard…',
						'bdthemes-element-pack-lite'
					) }
				/>
			) }

			{ error && ! stats && (
				<ErrorState error={ error } onRetry={ retry } />
			) }

			{ ! isLoading && ! error && stats && (
				<Overview
					stats={ stats }
					modules={ modules }
					variant={ variant }
				/>
			) }
		</div>
	);
}

function Overview( { stats, modules, variant } ) {
	const w = stats.widgets || {};

	// Re-render when the visibility map is refreshed at runtime.
	useNavVisibility();

	if ( ! w.all || w.all.total === 0 ) {
		return (
			<EmptyState
				title={ __( 'No widget data yet', 'bdthemes-element-pack-lite' ) }
				description={ __(
					'Once you start using Element Pack widgets, usage insights will appear here.',
					'bdthemes-element-pack-lite'
				) }
			/>
		);
	}

	const tracking = !! stats.tracking_allowed;
	const extensions = w.extensions || { total: 0, active: 0 };
	const features = stats.features || { total: 0, active: 0 };
	const widgetsTotal = w.all.total;
	const widgetsActive =
		( w.core?.active || 0 ) + ( w.third_party?.active || 0 );
	const assetManagerOn = !! stats.asset_optimization_enabled;

	/* translators: 1: count, 2: total */
	const ofTotal = __( '%1$d of %2$d', 'bdthemes-element-pack-lite' );

	const usedWidgets = ( () => {
		if ( ! tracking || ! modules?.tiers ) {
			return [];
		}
		const pool = [
			...( modules.tiers.core?.modules || [] ),
			...( modules.tiers.third_party?.modules || [] ),
		];
		return pool
			.filter( ( m ) => m.usage_count > 0 )
			.sort( ( a, b ) => b.usage_count - a.usage_count );
	} )();

	const trackingNote = ! tracking
		? __(
				'Enable Elementor usage tracking to see Used vs. Unused counts.',
				'bdthemes-element-pack-lite'
		  )
		: null;

	if ( variant === 'B' ) {
		return (
			<>
				<FocusHero stats={ stats } />
				<div className="grid grid-cols-3 max-[1024px]:grid-cols-1 gap-4 mt-4 items-start">
					<RecentWidgetsGrid widgets={ usedWidgets } />
					<ActivityCard
						title={ __( 'Activity', 'bdthemes-element-pack-lite' ) }
					/>
					{ /* Without usage data the widgets grid is hidden — fill
					     the remaining columns so the row never sits 2/3 empty. */ }
					{ ! usedWidgets.length && (
						<>
							<SystemStatusCard />
							<ResourcesCard />
						</>
					) }
				</div>
				{ trackingNote && (
					<p className="text-[12.5px] text-[#71717a] dark:text-ink-muted mt-3 mb-0">
						{ trackingNote }
					</p>
				) }
			</>
		);
	}

	const healthMetrics = [
		tracking
			? {
					label: __( 'Widgets in use', 'bdthemes-element-pack-lite' ),
					value: `${ percent( w.all.used, widgetsTotal ) }%`,
					percent: percent( w.all.used, widgetsTotal ),
					sub: sprintf( ofTotal, w.all.used, widgetsTotal ),
					icon: GaugeIcon,
					accent: 'green',
					barColor: '#22a550',
			  }
			: {
					label: __( 'Widgets enabled', 'bdthemes-element-pack-lite' ),
					value: `${ percent( widgetsActive, widgetsTotal ) }%`,
					percent: percent( widgetsActive, widgetsTotal ),
					sub: sprintf( ofTotal, widgetsActive, widgetsTotal ),
					icon: GaugeIcon,
					accent: 'green',
					barColor: '#22a550',
			  },
		// Core and 3rd-party share one catalog on the Widgets tab, so they
		// count as one figure here too. With tracking off the metric above
		// already reports it, and a second identical card would be noise.
		tracking && {
			label: __( 'Widgets active', 'bdthemes-element-pack-lite' ),
			value: `${ percent( widgetsActive, widgetsTotal ) }%`,
			percent: percent( widgetsActive, widgetsTotal ),
			sub: sprintf( ofTotal, widgetsActive, widgetsTotal ),
			icon: ZapIcon,
			accent: 'blue',
			barColor: '#408efa',
		},
		{
			label: __( 'Extensions active', 'bdthemes-element-pack-lite' ),
			value: `${ percent( extensions.active, extensions.total ) }%`,
			percent: percent( extensions.active, extensions.total ),
			sub: sprintf( ofTotal, extensions.active, extensions.total ),
			icon: PuzzleIcon,
			accent: 'violet',
			barColor: '#8b5cf6',
		},
		{
			label: __( 'Features active', 'bdthemes-element-pack-lite' ),
			value: `${ percent( features.active, features.total ) }%`,
			percent: percent( features.active, features.total ),
			sub: sprintf( ofTotal, features.active, features.total ),
			icon: SlidersIcon,
			accent: 'amber',
			barColor: '#f59e0b',
		},
	].filter( Boolean );

	const quickActions = [
		{
			to: '/widgets',
			navKey: 'widgets',
			icon: GridIcon,
			accent: 'blue',
			label: __( 'Widgets', 'bdthemes-element-pack-lite' ),
			sub: sprintf(
				/* translators: %d: widget count */
				__( 'Toggle %d elements', 'bdthemes-element-pack-lite' ),
				// The Widgets tab lists both catalogs, so count both.
				( w.core?.total || 0 ) + ( w.third_party?.total || 0 )
			),
		},
		{
			to: '/integrations',
			navKey: 'settings_api',
			icon: PlugIcon,
			accent: 'teal',
			label: __( 'Integrations', 'bdthemes-element-pack-lite' ),
			sub: __( 'Connect APIs', 'bdthemes-element-pack-lite' ),
		},
		{
			to: '/extensions',
			navKey: 'extensions',
			icon: PuzzleIcon,
			accent: 'violet',
			label: __( 'Extensions', 'bdthemes-element-pack-lite' ),
			sub: sprintf(
				/* translators: %d: extension count */
				__( 'Toggle %d extensions', 'bdthemes-element-pack-lite' ),
				extensions.total
			),
		},
		{
			to: '/settings',
			navKey: 'settings',
			icon: ZapIcon,
			accent: 'amber',
			label: __( 'Features', 'bdthemes-element-pack-lite' ),
			sub: __( 'Special features', 'bdthemes-element-pack-lite' ),
		},
		{
			to: '/settings/perf',
			navKey: 'settings_perf',
			icon: GaugeIcon,
			accent: 'green',
			label: __( 'Performance', 'bdthemes-element-pack-lite' ),
			sub: __( 'Asset Manager', 'bdthemes-element-pack-lite' ),
		},
	].filter( ( action ) => isNavVisible( action.navKey ) );

	const steps = [
		{
			to: '/widgets',
			label: __(
				'Enable your favourite widgets',
				'bdthemes-element-pack-lite'
			),
			done: widgetsActive > 0,
		},
		{
			to: '/extensions',
			label: __( 'Turn on extensions', 'bdthemes-element-pack-lite' ),
			done: extensions.active > 0,
		},
		{
			to: '/settings/perf',
			label: __( 'Turn on the Asset Manager', 'bdthemes-element-pack-lite' ),
			done: assetManagerOn,
		},
	].filter( Boolean );

	const mostUsed = usedWidgets.slice( 0, 5 );

	return (
		<>
			<div className="grid grid-cols-4 max-[1024px]:grid-cols-2 max-[640px]:grid-cols-1 gap-4">
				<DashboardStatCard
					icon={ GridIcon }
					accent="blue"
					value={ widgetsTotal }
					label={ __( 'Total widgets', 'bdthemes-element-pack-lite' ) }
					pill={
						tracking
							? sprintf(
									/* translators: %d: used widget count */
									__( '%d used', 'bdthemes-element-pack-lite' ),
									w.all.used
							  )
							: null
					}
					pillTone="green"
				/>
				<DashboardStatCard
					icon={ ZapIcon }
					accent="green"
					value={ widgetsActive }
					label={ __( 'Widgets enabled', 'bdthemes-element-pack-lite' ) }
					pill={ __( 'live', 'bdthemes-element-pack-lite' ) }
					pillTone="blue"
				/>
				<DashboardStatCard
					icon={ PuzzleIcon }
					accent="violet"
					value={ extensions.active }
					label={ __(
						'Extensions active',
						'bdthemes-element-pack-lite'
					) }
					pill={ sprintf(
						/* translators: %d: total extension count */
						__( 'of %d', 'bdthemes-element-pack-lite' ),
						extensions.total
					) }
					pillTone="neutral"
				/>
				<DashboardStatCard
					icon={ SlidersIcon }
					accent="amber"
					value={ features.active }
					label={ __( 'Features active', 'bdthemes-element-pack-lite' ) }
					pill={ sprintf(
						/* translators: %d: total feature count */
						__( 'of %d', 'bdthemes-element-pack-lite' ),
						features.total
					) }
					pillTone="neutral"
				/>
			</div>

			<HealthStrip metrics={ healthMetrics } />

			{ /* Single two-column grid: each column stacks its cards, so the
			     shorter column flows straight into the next card with the same
			     gap — no dead space between "rows" of unequal height. */ }
			<div className="grid grid-cols-[1.6fr_1fr] max-[1024px]:grid-cols-1 gap-4 mt-4 items-start">
				<div className="flex flex-col gap-4">
					<QuickActions actions={ quickActions } />
					<GetStartedCard steps={ steps } />
					<MostUsedCard widgets={ mostUsed } />
					<ActivityCard />
				</div>
				<div className="flex flex-col gap-4">
					<AiInsightsCard stats={ stats } />
					<WhatsNewPromoCard />
					<SystemStatusCard />
					<ResourcesCard />
				</div>
			</div>

			{ trackingNote && (
				<p className="text-[12.5px] text-[#71717a] dark:text-ink-muted mt-3 mb-0">
					{ trackingNote }
				</p>
			) }
		</>
	);
}
