/**
 * Variant B "Focus" hero — mockup index.html 551–581: dark 22px-radius panel
 * with animated grid + glow, big active-widget count, CTA pair, and a mini
 * bar chart. The mockup's fake weekly chart is replaced with real per-tier
 * activation bars.
 */
import { __, sprintf } from '@wordpress/i18n';
import { useNavigate } from 'react-router-dom';

export default function FocusHero( { stats } ) {
	const navigate = useNavigate();
	const w = stats.widgets || {};
	const totals = stats.active_totals || {};
	const total = ( w.all?.total || 0 ) + ( w.extensions?.total || 0 );
	// "Widgets active" headline counts widgets only — extensions are not
	// widgets (they're counted in totals.total / the tier panel).
	const widgetsActive =
		( w.core?.active || 0 ) + ( w.third_party?.active || 0 );

	const bars = [
		{
			label: __( 'Core', 'bdthemes-element-pack-lite' ),
			active: w.core?.active || 0,
			total: w.core?.total || 0,
			fill: 'linear-gradient(180deg,#84ed92,#408efa)',
		},
		{
			label: __( '3rd party', 'bdthemes-element-pack-lite' ),
			active: w.third_party?.active || 0,
			total: w.third_party?.total || 0,
			fill: 'linear-gradient(180deg,#5eead4,#14b8a6)',
		},
		{
			label: __( 'Extensions', 'bdthemes-element-pack-lite' ),
			active: w.extensions?.active || 0,
			total: w.extensions?.total || 0,
			fill: 'linear-gradient(180deg,#c4b5fd,#8b5cf6)',
		},
	];

	return (
		<div className="rounded-sheet p-7 mt-[18px] relative overflow-hidden text-white [background:linear-gradient(135deg,#0b0f1a_0%,#162a52_55%,#0b0f1a_100%)]">
			<div className="absolute inset-0 opacity-50 animate-ep-grid-shift [background-image:linear-gradient(rgba(255,255,255,.05)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,.05)_1px,transparent_1px)] [background-size:40px_40px]" />
			<div className="absolute -top-[60px] -right-10 w-[280px] h-[280px] rounded-full animate-ep-float [background:radial-gradient(circle,rgba(64,142,250,.35),transparent_65%)]" />
			<div className="relative grid grid-cols-[1.2fr_1fr] max-[1024px]:grid-cols-1 gap-[30px] items-center">
				<div>
					<div className="text-[11px] font-bold tracking-[.14em] uppercase text-brand-mint">
						{ __( 'Your install', 'bdthemes-element-pack-lite' ) }
					</div>
					<div className="text-[40px] font-extrabold tracking-[-.03em] mt-2 leading-none">
						{ widgetsActive }{ ' ' }
						{ __( 'widgets', 'bdthemes-element-pack-lite' ) }{ ' ' }
						<span className="text-white/45">
							{ __( 'active', 'bdthemes-element-pack-lite' ) }
						</span>
					</div>
					<p className="text-[13.5px] text-white/[.68] mt-[10px] mb-[18px] max-w-[380px]">
						{ sprintf(
							/* translators: 1: active count, 2: total count */
							__(
								'%1$d of %2$d features enabled — disable what you don’t use for lighter pages.',
								'bdthemes-element-pack-lite'
							),
							totals.total || 0,
							total
						) }
					</p>
					<div className="flex gap-[10px]">
						<button
							type="button"
							onClick={ () => navigate( '/widgets' ) }
							className="bg-gradient-brand border-0 text-white py-[10px] px-5 rounded-full font-sans text-[13px] font-bold cursor-pointer shadow-[0_6px_20px_rgba(64,142,250,.4)] transition-all duration-200 hover:-translate-y-px hover:shadow-[0_8px_24px_rgba(64,142,250,.55)]"
						>
							{ __( 'Manage widgets', 'bdthemes-element-pack-lite' ) }
						</button>
						<button
							type="button"
							onClick={ () => navigate( '/extensions' ) }
							className="bg-white/10 border border-white/20 text-white py-[10px] px-5 rounded-full font-sans text-[13px] font-semibold cursor-pointer transition-colors duration-200 hover:bg-white/20 hover:border-white/35"
						>
							{ __(
								'Browse extensions',
								'bdthemes-element-pack-lite'
							) }
						</button>
					</div>
				</div>

				<div className="bg-white/[.06] border border-white/10 rounded-panel p-[18px]">
					<div className="flex items-center justify-between mb-[14px]">
						<span className="text-[12.5px] font-semibold text-white/80">
							{ __(
								'Activation by tier',
								'bdthemes-element-pack-lite'
							) }
						</span>
						<span className="text-[11.5px] font-bold text-brand-mint">
							{ sprintf(
								/* translators: %d: total active count */
								__( '%d on', 'bdthemes-element-pack-lite' ),
								totals.total || 0
							) }
						</span>
					</div>
					<div className="flex items-end gap-2 h-28">
						{ bars.map( ( bar ) => {
							const pct = bar.total
								? Math.max(
										8,
										Math.round(
											( bar.active / bar.total ) * 100
										)
								  )
								: 0;
							return (
								<div
									key={ bar.label }
									className="flex-1 flex flex-col items-center gap-[6px] justify-end h-full"
								>
									<span className="text-[10.5px] font-bold text-white/75 leading-none">
										{ bar.total
											? `${ Math.round(
													( bar.active / bar.total ) *
														100
											  ) }%`
											: '—' }
									</span>
									<div
										className="w-full rounded-[5px_5px_2px_2px] transition-[height] duration-500 ease-ep"
										style={ {
											height: `${ pct }%`,
											background: bar.fill,
										} }
									/>
									<span className="text-[10px] text-white/45 leading-none">
										{ bar.label }
										<span className="text-white/30">
											{ ` ${ bar.active }/${ bar.total }` }
										</span>
									</span>
								</div>
							);
						} ) }
					</div>
				</div>
			</div>
		</div>
	);
}
