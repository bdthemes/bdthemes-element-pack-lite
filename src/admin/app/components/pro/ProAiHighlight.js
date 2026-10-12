/**
 * Featured Pro capability: Elementor MCP & AI Abilities.
 *
 * Facts here come from the Pro release notes (MCP server endpoint, abilities
 * registered with the WordPress Abilities API, supported clients, per-ability
 * switches and read-only mode). The ability count is the Pro figure at the time
 * of writing; update it with the Pro release.
 */
import { __ } from '@wordpress/i18n';
import { UpgradeLink } from './ProLinks';
import { SparklesIcon, PlugIcon, LayoutIcon, SlidersIcon } from '../ui/Icons';

const ABILITY_COUNT = 31;

const CLIENTS = [ 'Claude', 'ChatGPT', 'Cursor', 'VS Code', 'Angie' ];

function Point( { icon: PointIcon, title, text } ) {
	return (
		<div className="flex gap-3">
			<span className="w-[34px] h-[34px] rounded-[10px] flex-none grid place-items-center bg-white/10 text-[#c4b5fd]">
				<PointIcon size={ 18 } />
			</span>
			<div className="min-w-0">
				<div className="text-[14px] font-extrabold text-white tracking-[-.005em]">
					{ title }
				</div>
				<p className="text-[13px] leading-[1.55] text-[#b7bfd6] m-0 mt-[2px]">
					{ text }
				</p>
			</div>
		</div>
	);
}

export default function ProAiHighlight() {
	return (
		<div className="relative overflow-hidden rounded-card border border-[#2a2f55] bg-[linear-gradient(135deg,#14122e_0%,#1d1b4b_55%,#102a43_100%)] p-8 max-[640px]:p-5">
			<span
				aria-hidden="true"
				className="absolute -top-20 -left-16 w-[300px] h-[300px] rounded-full bg-[radial-gradient(circle,rgba(139,92,246,.35),transparent_70%)]"
			/>
			<span
				aria-hidden="true"
				className="absolute -bottom-24 right-0 w-[320px] h-[320px] rounded-full bg-[radial-gradient(circle,rgba(14,165,233,.28),transparent_70%)]"
			/>

			<div className="relative grid grid-cols-[1.15fr_1fr] max-[980px]:grid-cols-1 gap-8">
				<div>
					<div className="flex items-center gap-2 flex-wrap">
						<span className="inline-flex items-center gap-[6px] text-[10.5px] font-extrabold tracking-[.12em] uppercase text-white bg-gradient-brand py-[5px] px-[12px] rounded-full">
							<SparklesIcon size={ 12 } />
							{ __( 'New in Pro', 'bdthemes-element-pack-lite' ) }
						</span>
						<span className="text-[10.5px] font-extrabold tracking-[.12em] uppercase text-[#c4b5fd] bg-white/10 py-[5px] px-[12px] rounded-full">
							{ __( 'AI & MCP', 'bdthemes-element-pack-lite' ) }
						</span>
					</div>

					<h3 className="text-[28px] max-[640px]:text-[22px] leading-[1.18] font-extrabold tracking-[-.025em] text-white mt-[14px] mb-0">
						{ __(
							'Build Elementor pages with your favourite AI.',
							'bdthemes-element-pack-lite'
						) }
					</h3>
					<p className="text-[14.5px] leading-[1.6] text-[#c3cae0] mt-[10px] mb-0 max-w-[560px]">
						{ __(
							'Element Pack Pro ships an MCP server and AI abilities, so tools like Claude, ChatGPT, Cursor and VS Code can work with your Elementor site directly.',
							'bdthemes-element-pack-lite'
						) }
					</p>

					<div className="flex items-center gap-[7px] flex-wrap mt-5">
						{ CLIENTS.map( ( client ) => (
							<span
								key={ client }
								className="text-[12px] font-bold text-white bg-white/10 border border-white/15 py-[5px] px-[12px] rounded-full"
							>
								{ client }
							</span>
						) ) }
					</div>

					<div className="mt-5 max-w-[460px]">
						<div className="text-[10.5px] font-extrabold tracking-[.1em] uppercase text-[#8f98b8] mb-[6px]">
							{ __( 'MCP endpoint', 'bdthemes-element-pack-lite' ) }
						</div>
						<code className="block font-mono text-[12.5px] text-[#a5f3fc] bg-black/30 border border-white/10 rounded-[10px] py-[10px] px-[14px] overflow-x-auto whitespace-nowrap">
							/wp-json/element-pack/mcp
						</code>
					</div>

					<div className="flex items-center gap-3 flex-wrap mt-6">
						<UpgradeLink size="lg" />
					</div>
					<p className="text-[11.5px] leading-[1.5] text-[#8f98b8] mt-[14px] mb-0 max-w-[520px]">
						{ __(
							'AI tool support depends on your Elementor version and the integrations you connect. Abilities use the WordPress Abilities API (WordPress 6.9+).',
							'bdthemes-element-pack-lite'
						) }
					</p>
				</div>

				<div className="flex flex-col gap-5 justify-center">
					<div className="flex items-end gap-3">
						<span className="text-[56px] leading-none font-extrabold tracking-[-.04em] text-white">
							{ ABILITY_COUNT }
						</span>
						<span className="text-[13.5px] font-bold leading-[1.35] text-[#c3cae0] pb-[6px] max-w-[220px]">
							{ __(
								'abilities for widgets, pages, integrations, design system and maintenance',
								'bdthemes-element-pack-lite'
							) }
						</span>
					</div>

					<Point
						icon={ PlugIcon }
						title={ __( 'Built-in MCP server', 'bdthemes-element-pack-lite' ) }
						text={ __(
							'Connect a compatible client to your site with a ready connection snippet.',
							'bdthemes-element-pack-lite'
						) }
					/>
					<Point
						icon={ SlidersIcon }
						title={ __( 'You stay in control', 'bdthemes-element-pack-lite' ) }
						text={ __(
							'Master switches, per-ability toggles and a read-only mode decide what AI can touch.',
							'bdthemes-element-pack-lite'
						) }
					/>
					<Point
						icon={ LayoutIcon }
						title={ __( 'Works where you build', 'bdthemes-element-pack-lite' ) }
						text={ __(
							'Angie integration brings the same abilities into the Elementor editor.',
							'bdthemes-element-pack-lite'
						) }
					/>
				</div>
			</div>
		</div>
	);
}
