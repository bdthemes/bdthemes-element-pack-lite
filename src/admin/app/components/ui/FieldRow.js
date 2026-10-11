/**
 * Settings row inside a SectionCard: label (+ EDITED badge), muted
 * description, and a control slot on the right. Mockup lines 1028–1049.
 */
import { __ } from '@wordpress/i18n';

export default function FieldRow( {
	label,
	description,
	edited = false,
	children,
	stacked = false,
} ) {
	return (
		<div
			className={ `${
				stacked
					? 'flex flex-col gap-3'
					: 'flex flex-wrap items-center justify-between gap-x-6 gap-y-3'
			} py-[18px] px-7 max-[640px]:px-5 border-t border-[#f3f3f6] dark:border-ink-border transition-colors hover:bg-[#fbfbfd] dark:hover:bg-[#1a1f27]` }
		>
			{ /* The label keeps a readable width; a control too wide to sit
			     beside it on a narrow screen wraps below instead of
			     squeezing the label to one word per line. */ }
			<div className="flex-1 min-w-[200px]">
				<div className="flex items-center gap-[9px]">
					<span className="text-[15.5px] font-bold text-[#1a1a1f] dark:text-ink-text tracking-[-.01em]">
						{ label }
					</span>
					{ edited ? (
						<span className="text-[9.5px] font-extrabold tracking-[.05em] text-[#b45309] bg-[#fff7ed] dark:bg-[#2a1c0e] py-[2px] px-[7px] rounded-full">
							{ __( 'EDITED', 'bdthemes-element-pack-lite' ) }
						</span>
					) : null }
				</div>
				{ description ? (
					<div className="ep-rich-desc text-[13px] text-[#9a9aa3] dark:text-ink-muted mt-[3px]">
						{ description }
					</div>
				) : null }
			</div>
			{ children ? (
				<div className={ stacked ? '' : 'flex-none' }>{ children }</div>
			) : null }
		</div>
	);
}
