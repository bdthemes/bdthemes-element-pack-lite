/**
 * "Most used widgets" — the mockup's top-widgets bar list (index.html
 * 497–511) driven by real Elementor usage counts. Rendered only when usage
 * tracking is on and counts exist; the fake weekly-trend pill is omitted.
 */
import { __ } from '@wordpress/i18n';
import ProgressBar from '../ui/ProgressBar';

const BAR_COLORS = [ '#408efa', '#6366f1', '#22a550', '#f59e0b', '#14b8a6' ];

export default function MostUsedCard( { widgets } ) {
	if ( ! widgets.length ) {
		return null;
	}

	const max = Math.max( ...widgets.map( ( w ) => w.usage_count ) );

	return (
		<div className="bg-white dark:bg-ink-card border border-[#e9e9ed] dark:border-ink-border rounded-card p-5 shadow-resting">
			<div className="text-[17px] font-extrabold text-[#0f1115] dark:text-ink-text mb-4">
				{ __( 'Most used widgets', 'bdthemes-element-pack-lite' ) }
			</div>
			<div className="flex flex-col gap-[13px]">
				{ widgets.map( ( widget, index ) => (
					<div key={ widget.name }>
						<div className="flex items-center justify-between mb-[6px]">
							<span className="text-[13px] font-semibold text-[#3f3f46] dark:text-ink-text">
								{ widget.label }
							</span>
							<span className="text-[12px] font-extrabold text-[#a1a1aa]">
								{ widget.usage_count }
							</span>
						</div>
						<ProgressBar
							percent={
								max > 0
									? ( widget.usage_count / max ) * 100
									: 0
							}
							fill={ BAR_COLORS[ index % BAR_COLORS.length ] }
							height={ 8 }
						/>
					</div>
				) ) }
			</div>
		</div>
	);
}
