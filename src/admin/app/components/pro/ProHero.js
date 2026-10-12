/**
 * Hero of the Go Pro page: what Pro adds, in numbers taken from the live
 * catalog, with the primary call to action.
 */
import { __, sprintf } from '@wordpress/i18n';
import { UpgradeLink } from './ProLinks';
import { CheckIcon } from '../ui/Icons';

function Stat( { value, label } ) {
	return (
		<div className="flex-1 min-w-[120px] bg-white/70 dark:bg-white/[.04] border border-white/60 dark:border-white/10 rounded-panel py-[14px] px-[18px] backdrop-blur-sm">
			<div className="text-[28px] leading-none font-extrabold tracking-[-.02em] text-[#0f1115] dark:text-ink-text">
				{ value }
			</div>
			<div className="text-[12.5px] font-semibold text-[#52525b] dark:text-ink-muted mt-[6px]">
				{ label }
			</div>
		</div>
	);
}

export default function ProHero( { catalog, onCompare } ) {
	const { widgets, extensions, features, proTotal } = catalog;

	const points = [
		__( 'Every widget, extension and feature unlocked', 'bdthemes-element-pack-lite' ),
		__( 'Priority support from the Element Pack team', 'bdthemes-element-pack-lite' ),
		__( 'Your existing Lite settings carry over', 'bdthemes-element-pack-lite' ),
	];

	return (
		<div className="relative overflow-hidden rounded-card border border-[#d9def9] dark:border-ink-border bg-[linear-gradient(135deg,#eef1ff_0%,#e9fbef_100%)] dark:bg-[linear-gradient(135deg,#1b1e3a_0%,#0f2318_100%)] p-8 max-[640px]:p-5">
			<span
				aria-hidden="true"
				className="absolute -top-16 -right-16 w-[260px] h-[260px] rounded-full bg-[radial-gradient(circle,rgba(64,142,250,.25),transparent_70%)]"
			/>
			<span
				aria-hidden="true"
				className="absolute -bottom-20 left-1/3 w-[240px] h-[240px] rounded-full bg-[radial-gradient(circle,rgba(132,237,146,.28),transparent_70%)]"
			/>

			<div className="relative">
				<span className="inline-block text-[10.5px] font-extrabold tracking-[.12em] uppercase text-white bg-gradient-brand py-[5px] px-[12px] rounded-full shadow-[0_3px_10px_rgba(64,142,250,.3)]">
					{ __( 'Element Pack Pro', 'bdthemes-element-pack-lite' ) }
				</span>

				<h2 className="text-[34px] max-[640px]:text-[26px] leading-[1.15] font-extrabold tracking-[-.03em] text-[#0f1115] dark:text-ink-text mt-[14px] mb-0 max-w-[720px]">
					{ __(
						'Everything in Lite, plus the full toolkit.',
						'bdthemes-element-pack-lite'
					) }
				</h2>
				<p className="text-[15px] leading-[1.6] text-[#4b4b63] dark:text-ink-muted mt-[10px] mb-0 max-w-[640px]">
					{ sprintf(
						/* translators: 1: pro widget count, 2: pro extension count, 3: pro feature count */
						__(
							'Pro adds %1$d more widgets, %2$d extensions and %3$d features to the ones you already have, so you can build whole sites without reaching for another addon.',
							'bdthemes-element-pack-lite'
						),
						widgets.pro.length,
						extensions.pro.length,
						features.pro.length
					) }
				</p>

				<ul className="list-none p-0 m-0 mt-5 flex flex-col gap-[9px]">
					{ points.map( ( point ) => (
						<li
							key={ point }
							className="flex items-center gap-[10px] text-[13.5px] font-semibold text-[#27272a] dark:text-ink-text"
						>
							<span className="w-[20px] h-[20px] rounded-full flex-none grid place-items-center bg-[#22a550] text-white">
								<CheckIcon size={ 12 } strokeWidth={ 3 } />
							</span>
							{ point }
						</li>
					) ) }
				</ul>

				<div className="flex items-center gap-3 flex-wrap mt-6">
					<UpgradeLink size="lg" />
					<button
						type="button"
						onClick={ onCompare }
						className="bg-transparent border-0 p-0 text-[13.5px] font-bold text-brand cursor-pointer hover:underline"
					>
						{ __( 'Compare Free vs Pro', 'bdthemes-element-pack-lite' ) } →
					</button>
				</div>

				<div className="flex gap-3 flex-wrap mt-7">
					<Stat
						value={ proTotal }
						label={ __( 'Pro-only items', 'bdthemes-element-pack-lite' ) }
					/>
					<Stat
						value={ widgets.pro.length }
						label={ __( 'Pro widgets', 'bdthemes-element-pack-lite' ) }
					/>
					<Stat
						value={ extensions.pro.length }
						label={ __( 'Pro extensions', 'bdthemes-element-pack-lite' ) }
					/>
					<Stat
						value={ features.pro.length }
						label={ __( 'Pro features', 'bdthemes-element-pack-lite' ) }
					/>
				</div>
			</div>
		</div>
	);
}
