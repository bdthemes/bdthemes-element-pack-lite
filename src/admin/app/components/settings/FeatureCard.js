/**
 * White feature card (mockup widget-grid item): tinted icon tile, label,
 * PRO badge, docs/video links and an enable toggle. Locked (Pro-only)
 * entries show a lock instead of the toggle.
 */
import { memo } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import Toggle from '../ui/Toggle';
import { ProBadge } from '../ui/Badge';
import { WidgetIcon, LockIcon, ExternalIcon } from '../ui/Icons';
import { accentFor } from '../../utils/accents';

function FeatureCard( { field, checked, onToggle } ) {
	const locked = field.is_locked;

	return (
		<div className="flex items-center gap-[13px] bg-white dark:bg-ink-card border border-[#ececf0] dark:border-ink-border rounded-[14px] p-[14px] transition-all duration-150 hover:border-[#d4d8de] dark:hover:border-[#3a414c] hover:shadow-[0_4px_14px_-8px_rgba(0,0,0,.18)]">
			<span
				className={ `w-10 h-10 rounded-tile flex-none grid place-items-center ${ accentFor(
					field.name
				) }` }
			>
				<WidgetIcon name={ field.name } size={ 20 } />
			</span>

			<div className="min-w-0 flex-1">
				<div className="flex items-center gap-[7px]">
					<span className="text-[13.5px] font-semibold text-[#27272a] dark:text-ink-text whitespace-nowrap overflow-hidden text-ellipsis">
						{ field.label }
					</span>
					{ field.widget_type === 'pro' ? <ProBadge /> : null }
				</div>

				{ field.tooltip ? (
					<div className="text-[11.5px] text-[#a1a1aa] mt-px overflow-hidden text-ellipsis whitespace-nowrap">
						{ field.tooltip }
					</div>
				) : null }

				{ field.demo_url || field.video_url ? (
					<div className="flex items-center gap-[7px] text-[11.5px] text-[#a1a1aa] mt-px">
						{ field.demo_url ? (
							<a
								href={ field.demo_url }
								target="_blank"
								rel="noreferrer"
								className="inline-flex items-center gap-[3px] hover:text-brand"
							>
								{ __( 'Docs', 'bdthemes-element-pack-lite' ) }
								<ExternalIcon size={ 10 } />
							</a>
						) : null }
						{ field.video_url ? (
							<a
								href={ field.video_url }
								target="_blank"
								rel="noreferrer"
								className="inline-flex items-center gap-[3px] hover:text-brand"
							>
								{ __( 'Video', 'bdthemes-element-pack-lite' ) }
								<ExternalIcon size={ 10 } />
							</a>
						) : null }
					</div>
				) : null }
			</div>

			{ locked ? (
				<span
					className="flex-none text-[#a1a1aa]"
					title={ __(
						'Available in Element Pack Pro.',
						'bdthemes-element-pack-lite'
					) }
				>
					<LockIcon size={ 18 } />
				</span>
			) : (
				<Toggle
					size="sm"
					checked={ checked }
					onChange={ () => onToggle( field.name ) }
					label={ field.label }
				/>
			) }
		</div>
	);
}

export default memo( FeatureCard );
