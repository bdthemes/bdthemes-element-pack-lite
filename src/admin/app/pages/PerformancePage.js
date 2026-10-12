/**
 * Asset optimization (Asset Manager toggle + status).
 */
import { __ } from '@wordpress/i18n';
import { useSettings, useSettingsDraft } from '../hooks/useSettings';
import PageHeader from '../components/shell/PageHeader';
import Spinner from '../components/Spinner';
import ErrorState from '../components/ErrorState';
import UpgradeNotice from '../components/UpgradeNotice';
import Toggle from '../components/ui/Toggle';
import Alert from '../components/ui/Alert';
import SaveBar from '../components/ui/SaveBar';
import { useToast } from '../components/ui/Toast';
import { GaugeIcon, LockIcon } from '../components/ui/Icons';
import { accentClass } from '../utils/accents';
import config from '../utils/config';

const OPTION_KEY = 'element_pack_other_settings';

export default function PerformancePage() {
	const { data, isLoading, error, reload } = useSettings();
	const draft = useSettingsDraft( data, OPTION_KEY );
	const showToast = useToast();

	const isPro = data ? !! data.is_pro : config.isPro;
	const section = data && data.sections ? data.sections.performance : null;
	const fields = section
		? section.fields.filter( ( f ) => f.type === 'checkbox' )
		: [];
	const assetField = fields[ 0 ];

	const enabled = assetField
		? isOn( draft.draft[ assetField.name ] )
		: !! data?.asset_optimization_enabled;

	const onSave = async () => {
		try {
			await draft.save();
			showToast(
				__( 'Performance settings saved.', 'bdthemes-element-pack-lite' )
			);
		} catch ( e ) {
			// Error shown via store.
		}
	};

	const toggle = () => {
		if ( ! assetField ) {
			return;
		}
		draft.setField( assetField.name, enabled ? 'off' : 'on' );
	};

	return (
		<div>
			<PageHeader
				title={ __( 'Asset Optimization', 'bdthemes-element-pack-lite' ) }
				subtitle={ __(
					'Load only the widgets each page uses, combined into one CSS and one JS file stored in your uploads folder.',
					'bdthemes-element-pack-lite'
				) }
			/>

			<UpgradeNotice isPro={ isPro } />

			{ isLoading && ! data && (
				<Spinner
					full
					label={ __( 'Loading…', 'bdthemes-element-pack-lite' ) }
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

					{ /* Hero feature card: icon + copy + toggle, with a status
					     strip of quick facts along the bottom. */ }
					<div className="bg-white dark:bg-ink-card border border-[#e9e9ed] dark:border-ink-border rounded-card overflow-hidden">
						<div className="flex items-center gap-[18px] p-6 max-[640px]:p-5 flex-wrap">
							<span
								className={ `w-[52px] h-[52px] rounded-[14px] grid place-items-center flex-none ${ accentClass(
									'teal'
								) }` }
							>
								<GaugeIcon size={ 26 } />
							</span>
							<div className="flex-1 min-w-[220px]">
								<div className="flex items-center gap-2 flex-wrap">
									<span className="text-[16px] font-extrabold text-[#0f1115] dark:text-ink-text tracking-[-.01em]">
										{ assetField
											? assetField.label
											: __(
													'Asset Manager',
													'bdthemes-element-pack-lite'
											  ) }
									</span>
									{ assetField &&
									draft.isFieldDirty( assetField.name ) ? (
										<span className="text-[10px] font-extrabold uppercase tracking-[.05em] text-[#b45309] bg-[#fff7ed] dark:bg-[#2a1c0e] dark:text-[#fdba74] py-[2px] px-[8px] rounded-full">
											{ __(
												'Edited',
												'bdthemes-element-pack-lite'
											) }
										</span>
									) : null }
								</div>
								<p className="text-[13px] text-[#71717a] dark:text-ink-muted mt-[5px] mb-0 leading-[1.55] max-w-[560px]">
									{ assetField
										? assetField.tooltip
										: __(
												'Combine widget CSS and JS into single optimized files.',
												'bdthemes-element-pack-lite'
										  ) }
								</p>
							</div>
							{ assetField &&
								( assetField.is_locked ? (
									<span
										className="flex-none text-[#a1a1aa]"
										title={ __(
											'Available in Element Pack Pro.',
											'bdthemes-element-pack-lite'
										) }
									>
										<LockIcon size={ 20 } />
									</span>
								) : (
									<Toggle
										checked={ enabled }
										onChange={ toggle }
										label={ assetField.label }
									/>
								) ) }
						</div>

						<div className="grid grid-cols-3 max-[900px]:grid-cols-1 border-t border-[#f1f1f4] dark:border-ink-border divide-x divide-[#f1f1f4] dark:divide-ink-border max-[900px]:divide-x-0 max-[900px]:divide-y bg-[#fafbfc] dark:bg-ink-subtle">
							<div className="py-[14px] px-6 max-[640px]:px-5">
								<div className="text-[10.5px] font-extrabold tracking-[.1em] uppercase text-[#a1a1aa]">
									{ __( 'Status', 'bdthemes-element-pack-lite' ) }
								</div>
								<div className="flex items-center gap-[7px] mt-[6px] text-[13px] font-bold text-[#27272a] dark:text-ink-text">
									<span
										className={ `w-2 h-2 rounded-full ${
											data.asset_optimization_enabled
												? 'bg-[#22c55e] shadow-[0_0_0_3px_rgba(34,197,94,.2)]'
												: 'bg-[#d4d4d8]'
										}` }
									/>
									{ data.asset_optimization_enabled
										? __(
												'Enabled',
												'bdthemes-element-pack-lite'
										  )
										: __(
												'Disabled',
												'bdthemes-element-pack-lite'
										  ) }
								</div>
							</div>
							<div className="py-[14px] px-6 max-[640px]:px-5">
								<div className="text-[10.5px] font-extrabold tracking-[.1em] uppercase text-[#a1a1aa]">
									{ __(
										'Last rebuilt',
										'bdthemes-element-pack-lite'
									) }
								</div>
								<div className="mt-[6px]">
									{ data.asset_optimization_enabled &&
									data.minified_version ? (
										<span className="text-[13px] font-semibold text-[#52525b] dark:text-ink-muted">
											{ formatTimestamp( data.minified_version ) }
										</span>
									) : (
										<span className="text-[13px] font-semibold text-[#a1a1aa]">
											—
										</span>
									) }
								</div>
							</div>
							<div className="py-[14px] px-6 max-[640px]:px-5">
								<div className="text-[10.5px] font-extrabold tracking-[.1em] uppercase text-[#a1a1aa]">
									{ __(
										'Auto-regenerate',
										'bdthemes-element-pack-lite'
									) }
								</div>
								<div className="text-[12.5px] text-[#71717a] dark:text-ink-muted mt-[6px] leading-[1.5]">
									{ __(
										'Combined files rebuild automatically after an update and whenever widget, feature or plugin changes are saved.',
										'bdthemes-element-pack-lite'
									) }
								</div>
							</div>
						</div>
					</div>

					<SaveBar
						visible={ draft.isDirty }
						saving={ draft.isSaving }
						onSave={ onSave }
						onReset={ draft.reset }
					/>
				</div>
			) }
		</div>
	);
}

function formatTimestamp( value ) {
	const seconds = Number( value );
	if ( ! seconds ) {
		return String( value );
	}
	return new Date( seconds * 1000 ).toLocaleString();
}

function isOn( value ) {
	return value === 'on' || value === true || value === 1 || value === '1';
}
