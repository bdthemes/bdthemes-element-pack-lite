/**
 * "Widgets in use" tile grid — mockup Variant B (index.html 584–598): 4-col
 * centered tiles with tinted icon squares. Real data: the most-used widgets
 * by Elementor usage count. Hidden when usage tracking is off.
 */
import { __ } from '@wordpress/i18n';
import { useNavigate } from 'react-router-dom';
import { WidgetIcon } from '../ui/Icons';
import { accentFor } from '../../utils/accents';

export default function RecentWidgetsGrid( { widgets } ) {
	const navigate = useNavigate();

	if ( ! widgets.length ) {
		return null;
	}

	return (
		<div className="col-span-2 max-[1024px]:col-span-1 bg-white dark:bg-ink-card border border-[#e9e9ed] dark:border-ink-border rounded-card p-5 shadow-resting">
			<div className="flex items-center justify-between mb-[14px]">
				<div className="text-[17px] font-extrabold text-[#0f1115] dark:text-ink-text">
					{ __( 'Widgets in use', 'bdthemes-element-pack-lite' ) }
				</div>
				<button
					type="button"
					onClick={ () => navigate( '/widgets' ) }
					className="text-[12.5px] font-semibold text-brand bg-transparent border-0 cursor-pointer font-sans"
				>
					{ __( 'View all', 'bdthemes-element-pack-lite' ) }
				</button>
			</div>
			<div className="grid grid-cols-4 max-[640px]:grid-cols-2 gap-3">
				{ widgets.slice( 0, 8 ).map( ( widget ) => (
					<button
						key={ widget.name }
						type="button"
						onClick={ () => navigate( '/widgets' ) }
						className="border border-[#ededf1] dark:border-ink-border bg-transparent rounded-[13px] p-[13px] text-center transition-all duration-200 cursor-pointer hover:border-brand hover:-translate-y-[2px] hover:shadow-[0_8px_18px_-10px_rgba(64,142,250,.4)]"
					>
						<span
							className={ `w-[38px] h-[38px] rounded-tile grid place-items-center mx-auto ${ accentFor(
								widget.name
							) }` }
						>
							<WidgetIcon name={ widget.name } size={ 18 } />
						</span>
						<div className="text-xs font-semibold text-[#3f3f46] dark:text-ink-text mt-[9px] whitespace-nowrap overflow-hidden text-ellipsis">
							{ widget.label }
						</div>
					</button>
				) ) }
			</div>
		</div>
	);
}
