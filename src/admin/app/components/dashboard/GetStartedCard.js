/**
 * "Get started" checklist — mockup Variant A (index.html 422–442). Steps are
 * derived from real install state; the whole card hides once complete.
 */
import { __, sprintf } from '@wordpress/i18n';
import { useNavigate } from 'react-router-dom';
import ProgressBar from '../ui/ProgressBar';
import { CheckIcon, ChevronRightIcon } from '../ui/Icons';

export default function GetStartedCard( { steps } ) {
	const navigate = useNavigate();
	const doneCount = steps.filter( ( step ) => step.done ).length;

	if ( ! steps.length || doneCount === steps.length ) {
		return null;
	}

	const pct = Math.round( ( doneCount / steps.length ) * 100 );

	return (
		<div className="bg-white dark:bg-ink-card border border-[#e9e9ed] dark:border-ink-border rounded-card p-5 shadow-resting">
			<div className="flex items-center justify-between mb-[6px]">
				<div className="text-[17px] font-extrabold text-[#0f1115] dark:text-ink-text">
					{ __( 'Get started', 'bdthemes-element-pack-lite' ) }
				</div>
				<span className="text-xs font-bold text-brand">
					{ sprintf(
						/* translators: %d: completion percentage */
						__( '%d%% complete', 'bdthemes-element-pack-lite' ),
						pct
					) }
				</span>
			</div>
			<ProgressBar percent={ pct } height={ 7 } className="mb-4" />
			<div className="flex flex-col gap-1">
				{ steps.map( ( step ) => (
					<button
						key={ step.to }
						type="button"
						onClick={ () => navigate( step.to ) }
						className="flex items-center gap-3 py-[9px] px-2 rounded-btn cursor-pointer text-left transition-colors hover:bg-[#f7f8fa] dark:hover:bg-[#1a1f27] bg-transparent border-0"
					>
						<span
							className={ `w-[22px] h-[22px] rounded-full flex-none grid place-items-center text-white border-2 ${
								step.done
									? 'bg-[#22a550] border-[#22a550]'
									: 'bg-white dark:bg-ink-card border-[#d4d4d8] dark:border-ink-border'
							}` }
						>
							{ step.done ? (
								<CheckIcon size={ 12 } strokeWidth={ 3 } />
							) : null }
						</span>
						<span
							className={ `text-[13.5px] font-medium ${
								step.done
									? 'text-[#a1a1aa] dark:text-ink-muted line-through'
									: 'text-[#27272a] dark:text-ink-text'
							}` }
						>
							{ step.label }
						</span>
						<ChevronRightIcon
							size={ 16 }
							className="ml-auto text-[#c4c4cc]"
						/>
					</button>
				) ) }
			</div>
		</div>
	);
}
