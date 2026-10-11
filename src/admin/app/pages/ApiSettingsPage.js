/**
 * Integrations & API keys — mirrors the mockup's Integrations tab
 * (element-pack-dashboard-markup, index.html 1124–1191 + config modal
 * 1408–1507): summary tiles, search + category chips, one row per service
 * with brand tile and connection pill, and a Configure modal holding the
 * credential fields. Data still flows through the settings schema's grouped
 * key fields — no separate integrations backend.
 */
import { useMemo, useState, useEffect } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { useSettings, useSettingsDraft } from '../hooks/useSettings';
import PageHeader from '../components/shell/PageHeader';
import Spinner from '../components/Spinner';
import ErrorState from '../components/ErrorState';
import SettingsField from '../components/settings/SettingsField';
import Alert from '../components/ui/Alert';
import SaveBar from '../components/ui/SaveBar';
import ProgressBar from '../components/ui/ProgressBar';
import { useToast } from '../components/ui/Toast';
import {
	CheckIcon,
	GaugeIcon,
	SearchIcon,
	XIcon,
} from '../components/ui/Icons';

const OPTION_KEY = 'element_pack_api_settings';

/**
 * Real service metadata keyed by a match on the schema group's key: mockup
 * category chips + brand tile colors.
 */
const SERVICE_META = [
	{ match: 'instagram', cat: 'Social', color: '#e1306c' },
	{ match: 'twitter', cat: 'Social', color: '#111111' },
	{ match: 'fb_social', cat: 'Social', color: '#1877f2' },
	{ match: 'yelp', cat: 'Social', color: '#d32323' },
	{ match: 'social_login', cat: 'Social', color: '#6366f1' },
	{ match: 'recaptcha', cat: 'Forms', color: '#1a73e8' },
	{ match: 'google_sheets', cat: 'Forms', color: '#34a853' },
	{ match: 'contact_form', cat: 'Forms', color: '#408efa' },
	{ match: 'mailchimp', cat: 'Marketing', color: '#ff9c00' },
	{ match: 'weather', cat: 'Data', color: '#eb6e4b' },
];

const FALLBACK_COLORS = [ '#4285f4', '#22a550', '#f59e0b', '#8b5cf6', '#14b8a6', '#0ea5e9' ];

function serviceMeta( key ) {
	const found = SERVICE_META.find( ( meta ) =>
		String( key ).includes( meta.match )
	);
	if ( found ) {
		return found;
	}
	let hash = 0;
	const value = String( key || '' );
	for ( let i = 0; i < value.length; i++ ) {
		hash = ( hash * 31 + value.charCodeAt( i ) ) % 997;
	}
	return {
		cat: __( 'Other', 'bdthemes-element-pack-lite' ),
		color: FALLBACK_COLORS[ hash % FALLBACK_COLORS.length ],
	};
}

function isFilled( value ) {
	return String( value ?? '' ).trim() !== '';
}

const STATUS_STYLES = {
	connected: {
		color: 'text-[#15803d] bg-[#ecfdf3] dark:bg-[#0f2318] dark:text-[#4ade80]',
		dot: 'bg-[#22c55e]',
	},
	partial: {
		color: 'text-[#b45309] bg-[#fff7ed] dark:bg-[#2a1c0e] dark:text-[#fdba74]',
		dot: 'bg-[#f59e0b]',
	},
	empty: {
		color: 'text-[#a1a1aa] bg-[#f4f4f5] dark:bg-ink-subtle',
		dot: 'bg-[#d4d4d8]',
	},
};

function statusLabel( status ) {
	if ( status === 'connected' ) {
		return __( 'Connected', 'bdthemes-element-pack-lite' );
	}
	if ( status === 'partial' ) {
		return __( 'Incomplete', 'bdthemes-element-pack-lite' );
	}
	return __( 'Not set', 'bdthemes-element-pack-lite' );
}

function StatusPill( { status } ) {
	const style = STATUS_STYLES[ status ] || STATUS_STYLES.empty;
	return (
		<span
			className={ `flex items-center gap-[6px] text-[9.5px] font-extrabold py-[2px] px-[9px] rounded-full ${ style.color }` }
		>
			<span className={ `w-[6px] h-[6px] rounded-full ${ style.dot }` } />
			{ statusLabel( status ) }
		</span>
	);
}

/** Config modal — mockup 1408–1507: 22px sheet, brand-tile header, fields. */
function ConfigModal( { group, draft, onClose, onSave } ) {
	useEffect( () => {
		const onKey = ( event ) => {
			if ( event.key === 'Escape' ) {
				onClose();
			}
		};
		document.addEventListener( 'keydown', onKey );
		return () => document.removeEventListener( 'keydown', onKey );
	}, [ onClose ] );

	if ( ! group ) {
		return null;
	}

	const meta = serviceMeta( group.key );

	return (
		<div
			className="fixed inset-0 z-50 grid place-items-center p-5 bg-[rgba(9,9,11,.6)] backdrop-blur-sm animate-ep-fade-up"
			onClick={ ( event ) => {
				if ( event.target === event.currentTarget ) {
					onClose();
				}
			} }
			role="presentation"
		>
			<div className="w-full max-w-[640px] max-h-[86vh] overflow-y-auto bg-white dark:bg-ink-card rounded-sheet shadow-sheet animate-ep-pop">
				<div className="flex items-center gap-[15px] py-5 px-6 border-b border-[#f1f1f4] dark:border-ink-border sticky top-0 bg-white dark:bg-ink-card z-10">
					<span
						className="w-[46px] h-[46px] rounded-[12px] flex-none grid place-items-center text-white font-extrabold text-[17px] shadow-[inset_0_0_0_1px_rgba(255,255,255,.14)]"
						style={ { background: meta.color } }
					>
						{ group.label.charAt( 0 ).toUpperCase() }
					</span>
					<div className="flex-1 min-w-0">
						<div className="flex items-center gap-2 flex-wrap">
							<span className="text-[16px] font-extrabold text-[#0f1115] dark:text-ink-text">
								{ group.label }
							</span>
							<StatusPill status={ group.status } />
						</div>
						{ group.desc ? (
							<p
								className="ep-rich-desc text-[12.5px] text-[#71717a] dark:text-ink-muted mt-1 mb-0 leading-[1.5]"
								dangerouslySetInnerHTML={ {
									__html: group.desc,
								} }
							/>
						) : null }
					</div>
					<button
						type="button"
						onClick={ onClose }
						aria-label={ __( 'Close', 'bdthemes-element-pack-lite' ) }
						className="w-9 h-9 rounded-full flex-none grid place-items-center bg-[#f4f5f7] dark:bg-ink-subtle border-0 text-[#52525b] dark:text-ink-muted cursor-pointer hover:text-[#0f1115] dark:hover:text-ink-text"
					>
						<XIcon size={ 16 } />
					</button>
				</div>

				<div>
					{ group.fields.map( ( field, index ) => (
						<SettingsField
							key={ field.name || `field-${ index }` }
							field={ field }
							value={ draft.draft[ field.name ] }
							onChange={ draft.setField }
							secret
						/>
					) ) }
				</div>

				<div className="flex items-center justify-between gap-3 py-4 px-6 border-t border-[#f1f1f4] dark:border-ink-border sticky bottom-0 bg-white dark:bg-ink-card">
					<span className="text-[11.5px] text-[#a1a1aa] dark:text-ink-muted">
						{ sprintf(
							/* translators: 1: filled count, 2: total fields */
							__(
								'%1$d of %2$d fields filled',
								'bdthemes-element-pack-lite'
							),
							group.filled,
							group.fieldCount
						) }
					</span>
					<div className="flex gap-[10px]">
						<button
							type="button"
							onClick={ onClose }
							className="bg-white dark:bg-ink-card border border-[#e2e2e6] dark:border-ink-border text-[#3f3f46] dark:text-ink-text py-[10px] px-[18px] rounded-full font-sans text-[13px] font-bold cursor-pointer hover:border-brand hover:text-brand"
						>
							{ __( 'Close', 'bdthemes-element-pack-lite' ) }
						</button>
						<button
							type="button"
							disabled={ ! draft.isDirty || draft.isSaving }
							onClick={ onSave }
							className="flex items-center gap-[7px] bg-gradient-brand border-0 text-white py-[10px] px-[22px] rounded-full font-sans text-[13px] font-extrabold cursor-pointer shadow-[0_5px_16px_rgba(64,142,250,.4)] disabled:opacity-50 disabled:cursor-not-allowed"
						>
							<CheckIcon size={ 14 } strokeWidth={ 2.4 } />
							{ draft.isSaving
								? __( 'Saving…', 'bdthemes-element-pack-lite' )
								: __( 'Save changes', 'bdthemes-element-pack-lite' ) }
						</button>
					</div>
				</div>
			</div>
		</div>
	);
}

export default function ApiSettingsPage() {
	const { data, isLoading, error, reload } = useSettings();
	const draft = useSettingsDraft( data, OPTION_KEY );
	const showToast = useToast();
	const [ search, setSearch ] = useState( '' );
	const [ cat, setCat ] = useState( 'All' );
	const [ openKey, setOpenKey ] = useState( null );

	const section = data && data.sections ? data.sections.api : null;
	const allFields = section ? section.fields : [];

	const fields = useMemo(
		() =>
			allFields.filter(
				( f ) =>
					f.name !== 'google_oauth_connection' && f.type !== 'html'
			),
		[ allFields ]
	);

	// Group on the schema's start_group / end_group markers so each service
	// gets its own row; stray fields fall into a default row.
	const groups = useMemo( () => {
		const out = [];
		let current = null;
		fields.forEach( ( field ) => {
			if ( field.type === 'start_group' ) {
				current = {
					key: field.name || field.label || `group-${ out.length }`,
					label:
						field.label ||
						__( 'API Keys', 'bdthemes-element-pack-lite' ),
					desc: field.desc || '',
					fields: [],
				};
				out.push( current );
				return;
			}
			if ( field.type === 'end_group' ) {
				current = null;
				return;
			}
			if ( ! current ) {
				current = {
					key: 'api-keys',
					label: __( 'API Keys', 'bdthemes-element-pack-lite' ),
					desc: '',
					fields: [],
				};
				out.push( current );
			}
			current.fields.push( field );
		} );
		return out.filter( ( group ) => group.fields.length > 0 );
	}, [ fields ] );

	// Connection status per service from the live draft values.
	const withStatus = groups.map( ( group ) => {
		const inputFields = group.fields.filter(
			( f ) =>
				! [ 'start_group', 'end_group', 'separator' ].includes( f.type )
		);
		const filled = inputFields.filter( ( f ) =>
			isFilled( draft.draft[ f.name ] )
		).length;
		const status =
			inputFields.length > 0 && filled === inputFields.length
				? 'connected'
				: filled > 0
				? 'partial'
				: 'empty';
		return {
			...group,
			filled,
			fieldCount: inputFields.length,
			status,
			meta: serviceMeta( group.key ),
		};
	} );

	const connectedCount = withStatus.filter(
		( g ) => g.status === 'connected'
	).length;
	const totalFields = withStatus.reduce( ( n, g ) => n + g.fieldCount, 0 );
	const filledFields = withStatus.reduce( ( n, g ) => n + g.filled, 0 );
	const health = totalFields
		? Math.round( ( filledFields / totalFields ) * 100 )
		: 0;

	// Category chips with counts (mockup toolbar).
	const cats = useMemo( () => {
		const counts = {};
		withStatus.forEach( ( g ) => {
			counts[ g.meta.cat ] = ( counts[ g.meta.cat ] || 0 ) + 1;
		} );
		return [
			{ label: 'All', count: withStatus.length },
			...Object.keys( counts ).map( ( label ) => ( {
				label,
				count: counts[ label ],
			} ) ),
		];
	}, [ withStatus ] );

	const q = search.trim().toLowerCase();
	const visible = withStatus.filter( ( g ) => {
		if ( cat !== 'All' && g.meta.cat !== cat ) {
			return false;
		}
		if (
			q &&
			! g.label.toLowerCase().includes( q ) &&
			! g.fields.some( ( f ) =>
				String( f.label || '' )
					.toLowerCase()
					.includes( q )
			)
		) {
			return false;
		}
		return true;
	} );

	const openGroup = openKey
		? withStatus.find( ( g ) => g.key === openKey ) || null
		: null;

	const onSave = async () => {
		try {
			await draft.save();
			showToast( __( 'API settings saved.', 'bdthemes-element-pack-lite' ) );
		} catch ( e ) {
			// Error shown via store.
		}
	};

	return (
		<div>
			<PageHeader
				title={ __(
					'Integrations & API keys',
					'bdthemes-element-pack-lite'
				) }
				subtitle={ __(
					'Connect third-party services to power maps, forms, feeds and more.',
					'bdthemes-element-pack-lite'
				) }
			/>

			{ isLoading && ! data && (
				<Spinner
					full
					label={ __(
						'Loading integrations…',
						'bdthemes-element-pack-lite'
					) }
				/>
			) }

			{ error && ! data && (
				<ErrorState error={ error } onRetry={ reload } />
			) }

			{ data && (
				<div className="flex flex-col gap-4">
					{ error && (
						<Alert tone="error" onDismiss={ reload }>
							{ error.message }
						</Alert>
					) }

					<div className="grid grid-cols-3 max-[900px]:grid-cols-1 gap-[14px]">
						<div className="bg-white dark:bg-ink-card border border-[#e9e9ed] dark:border-ink-border rounded-panel p-[18px] shadow-resting">
							<div className="flex items-center gap-[9px]">
								<span className="w-[34px] h-[34px] rounded-[10px] grid place-items-center bg-[#ecfdf3] text-[#22a550] dark:bg-[#0f2318]">
									<CheckIcon size={ 18 } />
								</span>
								<span className="text-[13px] font-bold text-[#71717a] dark:text-ink-muted">
									{ __(
										'Connected',
										'bdthemes-element-pack-lite'
									) }
								</span>
							</div>
							<div className="text-[30px] font-black tracking-[-.03em] text-[#0f1115] dark:text-ink-text mt-[10px]">
								{ connectedCount }
								<span className="text-base font-bold text-[#a1a1aa]">
									{ ' / ' }
									{ withStatus.length }
								</span>
							</div>
						</div>
						<div className="bg-white dark:bg-ink-card border border-[#e9e9ed] dark:border-ink-border rounded-panel p-[18px] shadow-resting">
							<div className="flex items-center gap-[9px]">
								<span className="w-[34px] h-[34px] rounded-[10px] grid place-items-center bg-[#eff6ff] text-brand dark:bg-[#15233a]">
									<GaugeIcon size={ 18 } />
								</span>
								<span className="text-[13px] font-bold text-[#71717a] dark:text-ink-muted">
									{ __(
										'Setup health',
										'bdthemes-element-pack-lite'
									) }
								</span>
							</div>
							<div className="flex items-center gap-3 mt-[14px]">
								<ProgressBar
									percent={ health }
									height={ 9 }
									className="flex-1"
								/>
								<span className="text-[15px] font-extrabold text-brand tabular-nums">
									{ health }%
								</span>
							</div>
						</div>
						<div className="rounded-panel p-[18px] text-white relative overflow-hidden [background:linear-gradient(135deg,#0b0f1a,#172554)]">
							<div className="absolute -top-10 -right-5 w-[150px] h-[150px] rounded-full [background:radial-gradient(circle,rgba(132,237,146,.25),transparent_65%)]" />
							<div className="relative">
								<div className="text-xs font-bold text-white/60">
									{ __(
										'Need a key?',
										'bdthemes-element-pack-lite'
									) }
								</div>
								<div className="text-sm font-bold mt-[6px] leading-[1.4]">
									{ __(
										'Every key is stored in your own database & never shared.',
										'bdthemes-element-pack-lite'
									) }
								</div>
							</div>
						</div>
					</div>

					{ /* Toolbar card — matches FilterBar on the widget pages:
					     search row on top, category chip strip below. */ }
					<div className="bg-white dark:bg-ink-card border border-[#e9e9ed] dark:border-ink-border rounded-card overflow-hidden">
						<div className="flex items-center justify-between gap-3 flex-wrap py-[13px] px-[16px]">
							<div className="relative flex-1 min-w-[220px] max-w-[380px]">
								<span className="absolute left-[14px] top-1/2 -translate-y-1/2 text-[#a1a1aa] pointer-events-none">
									<SearchIcon size={ 16 } />
								</span>
								<input
									type="text"
									value={ search }
									onChange={ ( event ) =>
										setSearch( event.target.value )
									}
									placeholder={ __(
										'Search integrations…',
										'bdthemes-element-pack-lite'
									) }
									className="w-full box-border py-[11px] pr-[16px] pl-10 border border-[#e2e2e6] dark:border-ink-border rounded-full font-sans text-sm bg-white dark:bg-ink-card text-[#1a1a1f] dark:text-ink-text outline-none transition-shadow focus:border-brand focus:shadow-[0_0_0_3px_rgba(64,142,250,.15)]"
								/>
							</div>
							<span className="text-[12.5px] font-semibold text-[#a1a1aa]">
								{ sprintf(
									/* translators: %d: integration count */
									__(
										'%d integrations',
										'bdthemes-element-pack-lite'
									),
									visible.length
								) }
							</span>
						</div>
						<div className="flex gap-2 flex-wrap items-center py-[11px] px-[16px] border-t border-[#f1f1f4] dark:border-ink-border bg-[#fafbfc] dark:bg-ink-subtle">
							{ cats.map( ( chip ) => (
								<button
									key={ chip.label }
									type="button"
									onClick={ () => setCat( chip.label ) }
									className={ `py-[7px] px-[14px] rounded-full font-sans text-[13px] font-bold cursor-pointer border transition-all duration-150 ${
										cat === chip.label
											? 'bg-brand border-brand text-white'
											: 'bg-white dark:bg-ink-card border-[#e2e2e6] dark:border-ink-border text-[#52525b] dark:text-ink-muted hover:border-brand hover:text-brand'
									}` }
								>
									{ chip.label }{ ' ' }
									<span className="opacity-70">
										{ chip.count }
									</span>
								</button>
							) ) }
						</div>
					</div>

					<div className="flex flex-col gap-3">
						{ visible.map( ( group ) => (
							<div
								key={ group.key }
								className={ `bg-white dark:bg-ink-card border rounded-panel overflow-hidden shadow-[0_1px_3px_rgba(0,0,0,.04)] transition-colors ${
									group.status === 'connected'
										? 'border-[#c6f0d4] dark:border-[#1d4030]'
										: 'border-[#e9e9ed] dark:border-ink-border'
								}` }
							>
								<div className="flex items-center gap-[15px] py-4 px-[18px]">
									<span
										className="w-[46px] h-[46px] rounded-[12px] flex-none grid place-items-center text-white font-extrabold text-[17px] shadow-[inset_0_0_0_1px_rgba(255,255,255,.14)]"
										style={ {
											background: group.meta.color,
										} }
									>
										{ group.label
											.charAt( 0 )
											.toUpperCase() }
									</span>
									<div className="flex-1 min-w-0">
										<div className="flex items-center gap-2 flex-wrap">
											<span className="text-[15px] font-bold text-[#1a1a1f] dark:text-ink-text">
												{ group.label }
											</span>
											<span className="text-[9.5px] font-extrabold text-[#71717a] bg-[#f4f4f6] dark:bg-ink-subtle dark:text-ink-muted py-[2px] px-2 rounded-full">
												{ group.meta.cat }
											</span>
											<StatusPill
												status={ group.status }
											/>
										</div>
										{ group.desc ? (
											<p
												className="ep-rich-desc text-[12.5px] text-[#71717a] dark:text-ink-muted mt-1 mb-0 leading-[1.5] line-clamp-1"
												dangerouslySetInnerHTML={ {
													__html: group.desc,
												} }
											/>
										) : null }
									</div>
									<span className="text-[11px] text-[#a1a1aa] font-semibold flex-none max-[640px]:hidden">
										{ sprintf(
											/* translators: 1: filled, 2: total fields */
											__(
												'%1$d / %2$d fields',
												'bdthemes-element-pack-lite'
											),
											group.filled,
											group.fieldCount
										) }
									</span>
									<button
										type="button"
										onClick={ () =>
											setOpenKey( group.key )
										}
										className={ `flex items-center gap-[7px] py-[9px] px-[15px] rounded-full font-sans text-[12.5px] font-bold cursor-pointer flex-none border transition-all duration-150 ${
											group.status === 'connected'
												? 'bg-white dark:bg-ink-card border-[#e2e2e6] dark:border-ink-border text-[#3f3f46] dark:text-ink-text hover:border-brand hover:text-brand'
												: 'bg-brand border-brand text-white hover:opacity-90'
										}` }
									>
										{ group.status === 'connected'
											? __(
													'Configure',
													'bdthemes-element-pack-lite'
											  )
											: __(
													'Connect',
													'bdthemes-element-pack-lite'
											  ) }
									</button>
								</div>
							</div>
						) ) }
					</div>

					{ ! visible.length && (
						<div className="text-center py-[60px] px-5 bg-white dark:bg-ink-card border border-[#e9e9ed] dark:border-ink-border rounded-card">
							<div className="text-[15px] font-bold text-[#71717a] dark:text-ink-muted">
								{ __(
									'No integrations match your filters',
									'bdthemes-element-pack-lite'
								) }
							</div>
							<button
								type="button"
								onClick={ () => {
									setSearch( '' );
									setCat( 'All' );
								} }
								className="mt-3 bg-transparent border-0 text-brand font-bold cursor-pointer font-sans text-[13px]"
							>
								{ __(
									'Clear filters',
									'bdthemes-element-pack-lite'
								) }
							</button>
						</div>
					) }

					<ConfigModal
						group={ openGroup }
						draft={ draft }
						onClose={ () => setOpenKey( null ) }
						onSave={ onSave }
					/>

					<SaveBar
						visible={ draft.isDirty && ! openGroup }
						saving={ draft.isSaving }
						onSave={ onSave }
						onReset={ draft.reset }
					/>
				</div>
			) }
		</div>
	);
}
