/**
 * Single module card: category-tinted icon tile, name + PRO badge, a quiet
 * meta line (colored category label · usage · demo/video links), and an
 * enable toggle. The short description shows as a dark tooltip on hover.
 * Pro modules are locked (lock icon instead of toggle) on the Lite build.
 */
import { memo } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import Toggle from '../ui/Toggle';
import { ProBadge } from '../ui/Badge';
import {
	WidgetIcon,
	LockIcon,
	ExternalIcon,
	DownloadIcon,
} from '../ui/Icons';
import config from '../../utils/config';
import { accentClass } from '../../utils/accents';
import { categoryFor } from '../../utils/widgetCategories';
import { descriptionFor } from '../../utils/widgetDescriptions';

function WidgetCard( { module: mod, checked, onToggle } ) {
	const locked = mod.is_locked;
	// Third-party widget whose dependency plugin isn't active — the widget
	// wouldn't load anyway, so the toggle is disabled with an explainer chip.
	const pluginMissing = mod.plugin_active === false;
	// Freshly added widgets carry 'new' as a word inside content_type.
	const isNew = /\bnew\b/.test( String( mod.content_type || '' ) );
	const category = categoryFor( mod.name );
	const description = mod.description || descriptionFor( mod.name );

	return (
		<div className="relative group grid grid-cols-[44px_minmax(0,1fr)_auto] items-center gap-[14px] bg-white dark:bg-ink-card border border-[#ececf0] dark:border-ink-border rounded-panel py-4 px-[16px] transition-all duration-200 hover:border-[#c7dbfb] dark:hover:border-[#2f4368] hover:shadow-[0_8px_20px_-12px_rgba(64,142,250,.35)] hover:-translate-y-[1px]">
			{ /* Description tooltip on hover — slight show-delay so it doesn't
			     flicker while the cursor sweeps across the grid; hides
			     instantly. Springs up with the ep ease curve. */ }
			<div
				role="tooltip"
				className="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-[10px] w-max max-w-[300px] text-left [background:linear-gradient(180deg,#1c212b,#0f1115)] text-white/95 text-[12px] font-medium leading-[1.55] rounded-[10px] py-[9px] px-[13px] ring-1 ring-white/10 shadow-[0_12px_28px_-8px_rgba(0,0,0,.45)] z-20 origin-bottom opacity-0 translate-y-[6px] scale-[.94] transition-all duration-200 ease-ep delay-0 group-hover:opacity-100 group-hover:translate-y-0 group-hover:scale-100 group-hover:delay-200"
			>
				{ description }
				<span className="absolute top-full left-1/2 -translate-x-1/2 -mt-px border-[6px] border-transparent border-t-[#0f1115]" />
			</div>

			<span
				className={ `w-11 h-11 rounded-[12px] flex-none grid place-items-center ${ accentClass(
					category.accent
				) }` }
			>
				<WidgetIcon name={ mod.name } size={ 24 } />
			</span>

			<div className="min-w-0">
				<div className="text-[14px] font-bold text-[#1a1a1f] dark:text-ink-text tracking-[-.01em] whitespace-nowrap overflow-hidden text-ellipsis">
					{ mod.label }
				</div>
				{ /* Wraps rather than clipping when the line is too long (a
				     PRO badge narrows the card); each separator travels with
				     the item after it so no line ends on a dangling dot. */ }
				<div className="flex flex-wrap items-center gap-x-[6px] gap-y-[2px] text-[11.5px] text-[#a1a1aa] mt-[3px] whitespace-nowrap">
					<span className="font-medium text-[#9a9aa3] dark:text-ink-muted">
						{ category.label }
					</span>
					{ mod.has_usage && mod.used ? (
						<span className="inline-flex items-center gap-[6px]">
							<span className="text-[#d4d4d8]">·</span>
							<span className="text-[#15803d] dark:text-[#4ade80] font-bold">
								{ mod.usage_count > 0
									? `${ __(
											'Used',
											'bdthemes-element-pack-lite'
									  ) } ${ mod.usage_count }`
									: __( 'Used', 'bdthemes-element-pack-lite' ) }
							</span>
						</span>
					) : null }
					{ mod.demo_url ? (
						<span className="inline-flex items-center gap-[6px]">
							<span className="text-[#d4d4d8]">·</span>
							<a
								href={ mod.demo_url }
								target="_blank"
								rel="noreferrer"
								className="inline-flex items-center gap-[3px] font-semibold text-[#9a9aa3] hover:text-brand"
							>
								{ __( 'Demo', 'bdthemes-element-pack-lite' ) }
								<ExternalIcon size={ 10 } />
							</a>
						</span>
					) : null }
					{ mod.video_url ? (
						<span className="inline-flex items-center gap-[6px]">
							<span className="text-[#d4d4d8]">·</span>
							<a
								href={ mod.video_url }
								target="_blank"
								rel="noreferrer"
								className="inline-flex items-center gap-[3px] font-semibold text-[#9a9aa3] hover:text-brand"
							>
								{ __( 'Video', 'bdthemes-element-pack-lite' ) }
								<ExternalIcon size={ 10 } />
							</a>
						</span>
					) : null }
				</div>
			</div>

			<div className="flex items-center gap-[10px] flex-none">
				{ isNew && (
					<span className="text-[8.5px] font-extrabold tracking-[.08em] uppercase text-[#e11d48] bg-[#fff1f2] dark:bg-[#2a0e14] dark:text-[#fda4af] py-[2px] px-[7px] rounded-full flex-none">
						{ __( 'New', 'bdthemes-element-pack-lite' ) }
					</span>
				) }
				{ mod.widget_type === 'pro' ? <ProBadge /> : null }
				{ locked ? (
					<span
						className="text-[#a1a1aa]"
						role="img"
						aria-label={ __(
							'Available in Element Pack Pro.',
							'bdthemes-element-pack-lite'
						) }
						title={ __(
							'Available in Element Pack Pro.',
							'bdthemes-element-pack-lite'
						) }
					>
						<LockIcon size={ 18 } />
					</span>
				) : pluginMissing ? (
					<a
						href={
							// Premium dependencies link to their official
							// download page; free ones to the wp.org search.
							mod.install_url ||
							`${ config.adminUrl }plugin-install.php?s=${ encodeURIComponent(
								mod.plugin_slug || mod.requires_plugin || ''
							) }&tab=search&type=term`
						}
						target="_blank"
						rel="noreferrer"
						aria-label={ sprintf(
							/* translators: %s: dependency plugin name */
							__(
								'Install and activate %s to use this widget.',
								'bdthemes-element-pack-lite'
							),
							mod.requires_plugin ||
								__(
									'the required plugin',
									'bdthemes-element-pack-lite'
								)
						) }
						title={ sprintf(
							/* translators: %s: dependency plugin name */
							__(
								'Install and activate %s to use this widget.',
								'bdthemes-element-pack-lite'
							),
							mod.requires_plugin ||
								__(
									'the required plugin',
									'bdthemes-element-pack-lite'
								)
						) }
						className="w-[30px] h-[30px] rounded-full flex-none grid place-items-center bg-[#fff1f2] text-[#e11d48] dark:bg-[#2a0e14] dark:text-[#fda4af] transition-colors hover:bg-[#e11d48] hover:text-white"
					>
						<DownloadIcon size={ 15 } />
					</a>
				) : (
					<Toggle
						size="sm"
						checked={ checked }
						onChange={ () => onToggle( mod.name ) }
						label={ mod.label }
					/>
				) }
			</div>
		</div>
	);
}

export default memo( WidgetCard );
