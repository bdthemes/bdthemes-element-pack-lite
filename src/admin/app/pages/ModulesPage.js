/**
 * Generic module-management page shared by Widgets / Extensions.
 *
 * Drives filtering (search + type + usage), bulk actions and saving via the
 * useModuleTier hook. The tier pages are thin wrappers that pass `tier` —
 * a single key, or a list of them for a page covering more than one catalog.
 */
import { useMemo, useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { useModuleTier } from '../hooks/useModuleTier';
import { getTierLabels } from '../utils/moduleLabels';
import { CATEGORIES, categoryKeyForModule } from '../utils/widgetCategories';
import PageHeader from '../components/shell/PageHeader';
import Spinner from '../components/Spinner';
import ErrorState from '../components/ErrorState';
import EmptyState from '../components/EmptyState';
import UpgradeNotice from '../components/UpgradeNotice';
import FilterBar from '../components/widgets/FilterBar';
import WidgetGrid from '../components/widgets/WidgetGrid';
import BulkBar from '../components/widgets/BulkBar';
import SaveBar from '../components/ui/SaveBar';
import Alert from '../components/ui/Alert';
import Button from '../components/ui/Button';
import { useToast } from '../components/ui/Toast';

export default function ModulesPage( { tier, title } ) {
	const tiers = Array.isArray( tier ) ? tier : [ tier ];
	const {
		tierData,
		isPro,
		trackingAllowed,
		draft,
		toggle,
		setAll,
		isDirty,
		activeCount,
		save,
		reset,
		reload,
		isLoading,
		isSaving,
		error,
	} = useModuleTier( tier );

	const [ search, setSearch ] = useState( '' );
	const [ type, setType ] = useState( 'all' );
	const [ usage, setUsage ] = useState( 'all' );
	const [ category, setCategory ] = useState( 'all' );
	const showToast = useToast();
	const labels = getTierLabels( tier );

	const modules = useMemo(
		() => ( tierData ? tierData.modules : [] ),
		[ tierData ]
	);
	const showUsage = trackingAllowed && modules.some( ( m ) => m.has_usage );

	const counts = useMemo( () => {
		const c = {
			all: modules.length,
			free: 0,
			pro: 0,
			new: 0,
			used: 0,
			unused: 0,
		};
		modules.forEach( ( m ) => {
			if ( m.widget_type === 'pro' ) {
				c.pro += 1;
			} else {
				c.free += 1;
			}
			if ( /\bnew\b/.test( String( m.content_type || '' ) ) ) {
				c.new += 1;
			}
			if ( m.has_usage ) {
				if ( m.used ) {
					c.used += 1;
				} else {
					c.unused += 1;
				}
			}
		} );
		return c;
	}, [ modules ] );

	// Category chips (mockup's Elements screen) — widget tiers only.
	const showCategories = ! tiers.includes( 'extensions' );

	const categories = useMemo( () => {
		if ( ! showCategories ) {
			return [];
		}
		const tally = {};
		modules.forEach( ( m ) => {
			const key = categoryKeyForModule( m );
			tally[ key ] = ( tally[ key ] || 0 ) + 1;
		} );
		return Object.keys( CATEGORIES )
			.filter( ( key ) => tally[ key ] > 0 )
			.map( ( key ) => ( {
				key,
				label: CATEGORIES[ key ].label,
				count: tally[ key ],
			} ) );
	}, [ modules, showCategories ] );

	const filtered = useMemo( () => {
		const q = search.trim().toLowerCase();
		return modules.filter( ( m ) => {
			if ( type === 'new' ) {
				if ( ! /\bnew\b/.test( String( m.content_type || '' ) ) ) {
					return false;
				}
			} else if ( type !== 'all' && m.widget_type !== type ) {
				return false;
			}
			if (
				showCategories &&
				category !== 'all' &&
				categoryKeyForModule( m ) !== category
			) {
				return false;
			}
			if ( showUsage && usage !== 'all' ) {
				if ( usage === 'used' && ! m.used ) {
					return false;
				}
				if ( usage === 'unused' && m.used ) {
					return false;
				}
			}
			if ( q && ! m.label.toLowerCase().includes( q ) ) {
				return false;
			}
			return true;
		} );
	}, [ modules, type, usage, search, showUsage, category, showCategories ] );

	const onSave = async () => {
		try {
			await save();
			showToast( __( 'Changes saved.', 'bdthemes-element-pack-lite' ) );
		} catch ( e ) {
			// Error surfaces via the store error selector below.
		}
	};

	return (
		<div>
			<PageHeader
				title={ title }
				subtitle={
					tierData
						? sprintf(
								/* translators: 1: active count, 2: total count */
								__(
									'%1$d of %2$d enabled · disable unused items for faster pages.',
									'bdthemes-element-pack-lite'
								),
								activeCount,
								modules.length
						  )
						: ''
				}
				actions={
					tierData ? (
						<BulkBar
							activeCount={ activeCount }
							total={ modules.length }
							// Bulk actions follow the list on screen, so a
							// search or filter narrows what they touch.
							scopeCount={
								filtered.length === modules.length
									? null
									: filtered.length
							}
							isSaving={ isSaving }
							onActivateAll={ () =>
								// Widgets whose dependency plugin is missing
								// can't run — leave them off on Enable all.
								setAll(
									true,
									filtered.filter(
										( m ) => m.plugin_active !== false
									)
								)
							}
							onDeactivateAll={ () => setAll( false, filtered ) }
						/>
					) : null
				}
			/>

			<UpgradeNotice isPro={ isPro } />

			{ isLoading && ! tierData && (
				<Spinner full label={ labels.loading } />
			) }

			{ error && ! tierData && (
				<ErrorState error={ error } onRetry={ reload } />
			) }

			{ tierData && (
				<>
					{ error && (
						<Alert tone="error" className="mb-4">
							{ error.message }
						</Alert>
					) }

					<FilterBar
						search={ search }
						onSearch={ setSearch }
						searchPlaceholder={ labels.searchPlaceholder }
						type={ type }
						onType={ setType }
						usage={ usage }
						onUsage={ setUsage }
						showUsage={ showUsage }
						counts={ counts }
						categories={ categories }
						category={ category }
						onCategory={ setCategory }
					/>

					{ filtered.length === 0 ? (
						<EmptyState
							title={ labels.emptyTitle }
							description={ labels.emptyDescription }
							action={
								search ? (
									<Button
										variant="ghost"
										onClick={ () => setSearch( '' ) }
									>
										{ __(
											'Clear search',
											'bdthemes-element-pack-lite'
										) }
									</Button>
								) : null
							}
						/>
					) : (
						<WidgetGrid
							modules={ filtered }
							draft={ draft }
							onToggle={ toggle }
						/>
					) }

					<SaveBar
						visible={ isDirty }
						saving={ isSaving }
						onSave={ onSave }
						onReset={ reset }
					/>
				</>
			) }
		</div>
	);
}
