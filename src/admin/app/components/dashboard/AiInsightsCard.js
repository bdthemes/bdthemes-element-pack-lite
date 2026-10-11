/**
 * Suggestions card — the mockup's insights-card anatomy (index.html 445–468)
 * with real install-state suggestions routing to the matching pages.
 */
import { __, sprintf } from '@wordpress/i18n';
import { useNavigate } from 'react-router-dom';
import IconTile from '../ui/IconTile';
import {
	SparklesIcon,
	GaugeIcon,
	ShieldIcon,
	ArrowRightIcon,
} from '../ui/Icons';

export default function AiInsightsCard( { stats } ) {
	const navigate = useNavigate();
	const w = stats.widgets || {};
	// Only enabled widgets can be disabled; `unused` also counts the off ones.
	const unused =
		stats.tracking_allowed && w.all
			? Math.max( 0, w.all.unused_active ?? w.all.unused )
			: 0;

	const insights = [
		unused > 0 && {
			icon: GaugeIcon,
			accent: 'blue',
			title: __( 'Trim unused widgets', 'bdthemes-element-pack-lite' ),
			text: sprintf(
				/* translators: %d: unused widget count */
				__(
					'%d widgets aren’t used on any page — disabling them makes your pages lighter.',
					'bdthemes-element-pack-lite'
				),
				unused
			),
			cta: __( 'Review widgets', 'bdthemes-element-pack-lite' ),
			onClick: () => navigate( '/widgets' ),
		},
		{
			icon: ShieldIcon,
			accent: 'teal',
			title: __( 'Optimize asset loading', 'bdthemes-element-pack-lite' ),
			text: __(
				'Turn on asset optimization so pages only load the CSS/JS they use.',
				'bdthemes-element-pack-lite'
			),
			cta: __( 'Open performance', 'bdthemes-element-pack-lite' ),
			onClick: () => navigate( '/settings/perf' ),
		},
		{
			icon: SparklesIcon,
			accent: 'indigo',
			title: __( 'Connect integrations', 'bdthemes-element-pack-lite' ),
			text: __(
				'Add API keys to unlock maps, feeds, forms and more widgets.',
				'bdthemes-element-pack-lite'
			),
			cta: __( 'Open integrations', 'bdthemes-element-pack-lite' ),
			onClick: () => navigate( '/integrations' ),
		},
	].filter( Boolean );

	return (
		<div className="bg-white dark:bg-ink-card border border-[#e9e9ed] dark:border-ink-border rounded-card p-5 shadow-resting">
			<div className="flex items-center justify-between mb-[14px]">
				<div className="flex items-center gap-[9px]">
					<span className="w-[30px] h-[30px] rounded-[9px] grid place-items-center [background:linear-gradient(135deg,#eef1ff,#e9fbef)] dark:[background:#12161c]">
						<SparklesIcon size={ 17 } className="text-[#6366f1]" />
					</span>
					<span className="text-[17px] font-extrabold text-[#0f1115] dark:text-ink-text">
						{ __( 'Suggestions', 'bdthemes-element-pack-lite' ) }
					</span>
				</div>
				<span className="text-[10px] font-extrabold tracking-[.05em] text-[#6366f1] bg-[#eef1ff] dark:bg-[#1b1e3a] py-[3px] px-2 rounded-full">
					{ sprintf(
						/* translators: %d: suggestion count */
						__( '%d NEW', 'bdthemes-element-pack-lite' ),
						insights.length
					) }
				</span>
			</div>
			<div className="flex flex-col gap-[10px]">
				{ insights.map( ( insight ) => (
					<div
						key={ insight.title }
						className="flex gap-[11px] p-3 border border-[#eef0f3] dark:border-ink-border rounded-[13px] bg-[#fbfbfd] dark:bg-ink-subtle transition-colors hover:border-[#c7cdfa]"
					>
						<IconTile
							icon={ insight.icon }
							accent={ insight.accent }
							size="sm"
						/>
						<div className="flex-1 min-w-0">
							<div className="text-[13px] font-semibold text-[#27272a] dark:text-ink-text">
								{ insight.title }
							</div>
							<div className="text-[11.5px] text-[#71717a] dark:text-ink-muted mt-[2px] leading-[1.45]">
								{ insight.text }
							</div>
							<button
								type="button"
								onClick={ insight.onClick }
								className="mt-2 inline-flex items-center gap-[5px] bg-transparent border-0 text-[#6366f1] font-sans text-[11.5px] font-bold cursor-pointer p-0"
							>
								{ insight.cta }
								<ArrowRightIcon
									size={ 12 }
									strokeWidth={ 2.4 }
								/>
							</button>
						</div>
					</div>
				) ) }
			</div>
		</div>
	);
}
