/**
 * Features — rendered as its own primary tab (after Extensions) with the
 * same 4-column card grid as the widget/extension pages. All schema groups
 * are flattened into one grid; dependent sub-fields (e.g. Variation
 * Swatches options) surface in a card below while their parent switch is
 * on. Extra Options (custom code injection) lives in the Settings hub
 * (ExtraOptionsPage).
 */
import { useMemo, useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { useSettings, useSettingsDraft } from '../hooks/useSettings';
import PageHeader from '../components/shell/PageHeader';
import Spinner from '../components/Spinner';
import ErrorState from '../components/ErrorState';
import UpgradeNotice from '../components/UpgradeNotice';
import SettingsField from '../components/settings/SettingsField';
import WidgetGrid from '../components/widgets/WidgetGrid';
import { SectionCard } from '../components/ui/Card';
import SearchInput from '../components/ui/SearchInput';
import BulkBar from '../components/widgets/BulkBar';
import Alert from '../components/ui/Alert';
import SaveBar from '../components/ui/SaveBar';
import { useToast } from '../components/ui/Toast';
import { SlidersIcon } from '../components/ui/Icons';
import { accentFor } from '../utils/accents';
import config from '../utils/config';

const OPTION_KEY = 'element_pack_other_settings';

export default function SettingsPage() {
	const { data, isLoading, error, reload } = useSettings();
	const showToast = useToast();
	const [ query, setQuery ] = useState( '' );
	const clear = () => setQuery( '' );

	const settingsDraft = useSettingsDraft( data, OPTION_KEY );

	const isPro = data ? !! data.is_pro : config.isPro;
	const section =
		data && data.sections ? data.sections.special_features : null;
	const fields = useMemo(
		() => ( section ? section.fields : [] ),
		[ section ]
	);

	const q = query.trim().toLowerCase();
	const matchesQuery = ( ...texts ) =>
		! q ||
		texts.some( ( text ) =>
			String( text || '' )
				.toLowerCase()
				.includes( q )
		);

	// One flat grid: every toggle regardless of schema group.
	const toggles = useMemo(
		() => fields.filter( ( field ) => field.type === 'checkbox' ),
		[ fields ]
	);

	const visibleToggles = toggles.filter( ( field ) =>
		matchesQuery( field.label, field.tooltip )
	);

	const activeCount = toggles.filter( ( field ) =>
		isOn( settingsDraft.draft[ field.name ] )
	).length;

	const setAllFeatures = ( value ) => {
		toggles.forEach( ( field ) =>
			settingsDraft.setField( field.name, value ? 'on' : 'off' )
		);
	};

	// Dependent sub-fields grouped by their parent switch; each bundle
	// renders in a "<Parent> Options" card while the parent is on.
	const optionBundles = useMemo( () => {
		const bundles = new Map();
		fields.forEach( ( field ) => {
			if (
				field.type === 'checkbox' ||
				field.type === 'start_group' ||
				field.type === 'end_group' ||
				! field.name
			) {
				return;
			}
			const parent = field.depends_on || '';
			if ( ! bundles.has( parent ) ) {
				bundles.set( parent, [] );
			}
			bundles.get( parent ).push( field );
		} );
		return Array.from( bundles, ( [ parent, bundleFields ] ) => ( {
			parent,
			fields: bundleFields,
		} ) );
	}, [ fields ] );

	const parentLabel = ( name ) => {
		const parent = toggles.find( ( field ) => field.name === name );
		return parent ? parent.label : name;
	};

	const onSave = async () => {
		try {
			await settingsDraft.save();
			showToast( __( 'Changes saved.', 'bdthemes-element-pack-lite' ) );
		} catch ( e ) {
			// Error shown via store.
		}
	};

	const toggleFeature = ( name ) => {
		const current = isOn( settingsDraft.draft[ name ] );
		settingsDraft.setField( name, current ? 'off' : 'on' );
	};

	// WidgetGrid expects module-shaped items and boolean draft values.
	const gridDraft = useMemo( () => {
		const out = {};
		Object.keys( settingsDraft.draft ).forEach( ( key ) => {
			out[ key ] = isOn( settingsDraft.draft[ key ] );
		} );
		return out;
	}, [ settingsDraft.draft ] );

	const toGridModule = ( field ) => ( {
		name: field.name,
		label: field.label,
		widget_type: field.widget_type,
		is_locked: field.is_locked,
		demo_url: field.demo_url,
		video_url: field.video_url,
		description: field.tooltip,
	} );

	return (
		<div>
			<PageHeader
				title={ __( 'Features', 'bdthemes-element-pack-lite' ) }
				subtitle={ __(
					'Enable bundled features that extend Element Pack beyond individual widgets.',
					'bdthemes-element-pack-lite'
				) }
				actions={
					data ? (
						<BulkBar
							activeCount={ activeCount }
							total={ toggles.length }
							isSaving={ settingsDraft.isSaving }
							onActivateAll={ () => setAllFeatures( true ) }
							onDeactivateAll={ () => setAllFeatures( false ) }
						/>
					) : null
				}
			/>

			<UpgradeNotice isPro={ isPro } />

			{ isLoading && ! data && (
				<Spinner
					full
					label={ __( 'Loading settings…', 'bdthemes-element-pack-lite' ) }
				/>
			) }

			{ error && ! data && (
				<ErrorState error={ error } onRetry={ reload } />
			) }

			{ data && (
				<div className="flex flex-col gap-4">
					{ /* Toolbar card — matches FilterBar on the widget pages. */ }
					<div className="bg-white dark:bg-ink-card border border-[#e9e9ed] dark:border-ink-border rounded-card py-[13px] px-[16px] flex items-center justify-between gap-3 flex-wrap">
						<SearchInput
							value={ query }
							onChange={ setQuery }
							placeholder={ __(
								'Search features…',
								'bdthemes-element-pack-lite'
							) }
							className="flex-1 min-w-[220px] max-w-[380px]"
						/>
						<span className="text-[12.5px] font-semibold text-[#a1a1aa]">
							{ sprintf(
								/* translators: %d: feature count */
								__( '%d features', 'bdthemes-element-pack-lite' ),
								visibleToggles.length
							) }
						</span>
					</div>

					{ error && (
						<Alert tone="error" onDismiss={ reload }>
							{ error.message }
						</Alert>
					) }

					{ visibleToggles.length > 0 && (
						<WidgetGrid
							modules={ visibleToggles.map( toGridModule ) }
							draft={ gridDraft }
							onToggle={ toggleFeature }
						/>
					) }

					{ ! q &&
						optionBundles.map( ( bundle ) =>
							bundle.parent &&
							! isOn(
								settingsDraft.draft[ bundle.parent ]
							) ? null : (
								<SectionCard
									key={ bundle.parent || 'general' }
									icon={ SlidersIcon }
									iconClassName={ accentFor(
										bundle.parent || 'features'
									) }
									title={
										bundle.parent
											? sprintf(
													/* translators: %s: parent feature label */
													__(
														'%s Options',
														'bdthemes-element-pack-lite'
													),
													parentLabel(
														bundle.parent
													)
											  )
											: __(
													'Options',
													'bdthemes-element-pack-lite'
											  )
									}
								>
									{ bundle.fields.map( ( field ) => (
										<SettingsField
											key={ field.name || field.label }
											field={ field }
											value={
												settingsDraft.draft[
													field.name
												]
											}
											onChange={ settingsDraft.setField }
										/>
									) ) }
								</SectionCard>
							)
						) }

					{ q && ! visibleToggles.length && (
						<div className="text-center py-[60px] px-5 bg-white dark:bg-ink-card border border-[#e9e9ed] dark:border-ink-border rounded-card">
							<div className="text-[15px] font-bold text-[#71717a] dark:text-ink-muted">
								{ sprintf(
									/* translators: %s: search query */
									__(
										'No features match “%s”',
										'bdthemes-element-pack-lite'
									),
									query
								) }
							</div>
							<button
								type="button"
								onClick={ clear }
								className="mt-3 bg-transparent border-0 text-brand font-bold cursor-pointer font-sans text-[13px]"
							>
								{ __( 'Clear search', 'bdthemes-element-pack-lite' ) }
							</button>
						</div>
					) }

					<SaveBar
						visible={ settingsDraft.isDirty }
						saving={ settingsDraft.isSaving }
						onSave={ onSave }
						onReset={ settingsDraft.reset }
					/>
				</div>
			) }
		</div>
	);
}

function isOn( value ) {
	return value === 'on' || value === true || value === 1 || value === '1';
}
