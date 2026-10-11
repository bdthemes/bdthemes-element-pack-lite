/**
 * Curated Pro highlights: the features people ask about most, each with a short
 * description and (where one exists) a link to its demo or documentation.
 * Counts come from the live catalog.
 */
import { __, sprintf } from '@wordpress/i18n';
import { accentClass } from '../../utils/accents';
import {
	LayoutIcon,
	DatabaseIcon,
	StoreIcon,
	ZapIcon,
	CodeIcon,
	SparklesIcon,
	HeadphonesIcon,
	PuzzleIcon,
	ExternalIcon,
} from '../ui/Icons';

function Highlight( { icon: HighlightIcon, accent, title, text, href } ) {
	return (
		<div className="flex flex-col gap-[10px] bg-white dark:bg-ink-card border border-[#ececf0] dark:border-ink-border rounded-panel p-5 transition-all duration-200 hover:border-[#c7dbfb] dark:hover:border-[#2f4368] hover:-translate-y-[1px] hover:shadow-[0_8px_20px_-12px_rgba(64,142,250,.35)]">
			<span
				className={ `w-[42px] h-[42px] rounded-[12px] grid place-items-center flex-none ${ accentClass(
					accent
				) }` }
			>
				<HighlightIcon size={ 22 } />
			</span>
			<div className="text-[15px] font-extrabold tracking-[-.01em] text-[#0f1115] dark:text-ink-text">
				{ title }
			</div>
			<p className="text-[13px] leading-[1.55] text-[#71717a] dark:text-ink-muted m-0 flex-1">
				{ text }
			</p>
			{ href ? (
				<a
					href={ href }
					target="_blank"
					rel="noreferrer"
					className="inline-flex items-center gap-[4px] text-[12.5px] font-bold text-brand no-underline hover:underline"
				>
					{ __( 'Learn more', 'bdthemes-element-pack-lite' ) }
					<ExternalIcon size={ 11 } />
				</a>
			) : null }
		</div>
	);
}

export default function ProHighlights( { catalog } ) {
	const items = [
		{
			icon: LayoutIcon,
			accent: 'indigo',
			title: __( 'Mega Menu', 'bdthemes-element-pack-lite' ),
			text: __(
				'Build oversized menus with full custom layouts: images, columns, sliders, icons, forms and buttons.',
				'bdthemes-element-pack-lite'
			),
			href: 'https://bdthemes.com/knowledge-base/mega-menu-widget/',
		},
		{
			icon: DatabaseIcon,
			accent: 'green',
			title: __( 'Dynamic Content', 'bdthemes-element-pack-lite' ),
			text: __(
				'Populate Elementor widgets automatically with real-time data from WordPress.',
				'bdthemes-element-pack-lite'
			),
			href: 'https://elementpack.pro/demo/element/dynamic-content/',
		},
		{
			icon: LayoutIcon,
			accent: 'teal',
			title: __( 'Header & Footer Builder', 'bdthemes-element-pack-lite' ),
			text: __(
				'Design headers, footers and archive pages visually, without writing code.',
				'bdthemes-element-pack-lite'
			),
		},
		{
			icon: StoreIcon,
			accent: 'woo',
			title: __( 'WooCommerce widgets', 'bdthemes-element-pack-lite' ),
			text: sprintf(
				/* translators: %d: number of Pro WooCommerce widgets */
				__(
					'%d WooCommerce widgets for shop and product layouts, plus Variation Swatches for colour, image and button choices.',
					'bdthemes-element-pack-lite'
				),
				catalog.proWoo
			),
			href: 'https://elementpack.pro/demo/element/variation-swatches/',
		},
		{
			icon: PuzzleIcon,
			accent: 'violet',
			title: __( 'Motion & layout extensions', 'bdthemes-element-pack-lite' ),
			text: sprintf(
				/* translators: %d: number of Pro extensions */
				__(
					'%d extensions that work on any element: parallax, particles, reveal effects, sticky, shape dividers and more.',
					'bdthemes-element-pack-lite'
				),
				catalog.extensions.pro.length
			),
		},
		{
			icon: ZapIcon,
			accent: 'amber',
			title: __( 'Smooth Scroller', 'bdthemes-element-pack-lite' ),
			text: __(
				'Seamless, polished scrolling and content navigation across your whole site.',
				'bdthemes-element-pack-lite'
			),
		},
		{
			icon: CodeIcon,
			accent: 'sky',
			title: __( 'Essential Shortcodes & Context Menu', 'bdthemes-element-pack-lite' ),
			text: __(
				'Extra shortcodes for content you can’t build with widgets alone, and a right-click menu inside the Elementor editor.',
				'bdthemes-element-pack-lite'
			),
			href: 'https://bdthemes.com/knowledge-base/context-menu/',
		},
		{
			icon: HeadphonesIcon,
			accent: 'rose',
			title: __( 'Priority support', 'bdthemes-element-pack-lite' ),
			text: __(
				'Get help from the Element Pack team when you need it, with updates included.',
				'bdthemes-element-pack-lite'
			),
		},
	];

	return (
		<div>
			<div className="flex items-center gap-[10px] mb-[14px]">
				<span className="w-[30px] h-[30px] rounded-[9px] grid place-items-center bg-[#f3f0ff] text-[#8b5cf6] dark:bg-[#241d3a]">
					<SparklesIcon size={ 17 } />
				</span>
				<h3 className="text-[17px] font-extrabold text-[#0f1115] dark:text-ink-text m-0">
					{ __( 'Pro highlights', 'bdthemes-element-pack-lite' ) }
				</h3>
			</div>
			<div className="grid grid-cols-4 max-[1180px]:grid-cols-3 max-[860px]:grid-cols-2 max-[560px]:grid-cols-1 gap-4">
				{ items.map( ( item ) => (
					<Highlight key={ item.title } { ...item } />
				) ) }
			</div>
		</div>
	);
}
