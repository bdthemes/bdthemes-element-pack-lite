/**
 * Free vs Pro comparison table. Counts come from the live catalog; the yes/no
 * rows mirror the long-standing Lite-vs-Pro list.
 */
import { __, sprintf } from '@wordpress/i18n';
import { CheckIcon, XIcon } from '../ui/Icons';
import { UpgradeLink } from './ProLinks';
import { categoryKeyFor } from '../../utils/widgetCategories';

function Cell( { value, pro = false } ) {
	if ( value === true ) {
		return (
			<span
				className={ `w-[22px] h-[22px] rounded-full grid place-items-center ${
					pro
						? 'bg-gradient-brand text-white'
						: 'bg-[#ecfdf3] text-[#15803d] dark:bg-[#0f2318] dark:text-[#4ade80]'
				}` }
			>
				<CheckIcon size={ 13 } strokeWidth={ 3 } />
			</span>
		);
	}

	if ( value === false ) {
		return (
			<span className="w-[22px] h-[22px] rounded-full grid place-items-center bg-[#f4f4f6] text-[#a1a1aa] dark:bg-ink-subtle">
				<XIcon size={ 12 } />
			</span>
		);
	}

	return (
		<span
			className={ `text-[14px] font-extrabold ${
				pro ? 'text-brand' : 'text-[#52525b] dark:text-ink-muted'
			}` }
		>
			{ value }
		</span>
	);
}

function Row( { label, free, pro, note } ) {
	return (
		<div className="grid grid-cols-[minmax(0,1fr)_110px_110px] max-[560px]:grid-cols-[minmax(0,1fr)_64px_64px] items-center py-[13px] px-5 max-[560px]:px-3 border-t border-[#f1f1f4] dark:border-ink-border first:border-t-0">
			<div className="min-w-0">
				<div className="text-[13.5px] font-semibold text-[#27272a] dark:text-ink-text">
					{ label }
				</div>
				{ note ? (
					<div className="text-[11.5px] text-[#a1a1aa] dark:text-ink-muted mt-[2px]">
						{ note }
					</div>
				) : null }
			</div>
			<div className="flex justify-center">
				<Cell value={ free } />
			</div>
			<div className="flex justify-center rounded-[10px] bg-[rgba(64,142,250,.05)] dark:bg-[rgba(64,142,250,.08)] py-[6px]">
				<Cell value={ pro } pro />
			</div>
		</div>
	);
}

function Group( { title, children } ) {
	return (
		<div>
			<div className="py-[10px] px-5 max-[560px]:px-3 text-[10.5px] font-extrabold tracking-[.1em] uppercase text-[#a1a1aa] bg-[#fafbfc] dark:bg-ink-subtle border-y border-[#f1f1f4] dark:border-ink-border">
				{ title }
			</div>
			{ children }
		</div>
	);
}

export default function ProCompare( { catalog } ) {
	const { widgets, extensions, features, proWoo } = catalog;

	const freeWoo = widgets.free.filter(
		( m ) => categoryKeyFor( m.name ) === 'woocommerce'
	).length;

	return (
		<div className="bg-white dark:bg-ink-card border border-[#e9e9ed] dark:border-ink-border rounded-card overflow-hidden">
			<div className="grid grid-cols-[minmax(0,1fr)_110px_110px] max-[560px]:grid-cols-[minmax(0,1fr)_64px_64px] items-center py-4 px-5 max-[560px]:px-3">
				<div className="text-[15px] font-extrabold text-[#0f1115] dark:text-ink-text">
					{ __( 'Element Pack Lite vs Pro', 'bdthemes-element-pack-lite' ) }
				</div>
				<div className="text-center text-[12.5px] font-extrabold text-[#52525b] dark:text-ink-muted">
					{ __( 'Free', 'bdthemes-element-pack-lite' ) }
				</div>
				<div className="text-center text-[12.5px] font-extrabold text-brand">
					{ __( 'Pro', 'bdthemes-element-pack-lite' ) }
				</div>
			</div>

			<Group title={ __( 'What you get', 'bdthemes-element-pack-lite' ) }>
				<Row
					label={ __( 'Widgets', 'bdthemes-element-pack-lite' ) }
					free={ widgets.free.length }
					pro={ widgets.total }
				/>
				<Row
					label={ __( 'Extensions', 'bdthemes-element-pack-lite' ) }
					free={ extensions.free.length }
					pro={ extensions.total }
				/>
				<Row
					label={ __( 'Special features', 'bdthemes-element-pack-lite' ) }
					free={ features.free.length }
					pro={ features.total }
				/>
				<Row
					label={ __( 'WooCommerce widgets', 'bdthemes-element-pack-lite' ) }
					free={ freeWoo }
					pro={ freeWoo + proWoo }
				/>
			</Group>

			<Group title={ __( 'Builder & content', 'bdthemes-element-pack-lite' ) }>
				<Row
					label={ __( 'Dynamic Content', 'bdthemes-element-pack-lite' ) }
					free={ false }
					pro={ true }
				/>
				<Row
					label={ __( 'Mega Menu', 'bdthemes-element-pack-lite' ) }
					free={ false }
					pro={ true }
				/>
				<Row
					label={ __( 'Header & Footer Builder', 'bdthemes-element-pack-lite' ) }
					free={ false }
					pro={ true }
				/>
				<Row
					label={ __( 'Ready-made header & footer', 'bdthemes-element-pack-lite' ) }
					free={ false }
					pro={ true }
				/>
				<Row
					label={ __( 'Variation Swatches', 'bdthemes-element-pack-lite' ) }
					free={ false }
					pro={ true }
				/>
				<Row
					label={ __( 'Smooth Scroller', 'bdthemes-element-pack-lite' ) }
					free={ false }
					pro={ true }
				/>
			</Group>

			<Group title={ __( 'AI', 'bdthemes-element-pack-lite' ) }>
				<Row
					label={ __( 'Elementor MCP & AI Abilities', 'bdthemes-element-pack-lite' ) }
					note={ __(
						'Claude, ChatGPT, Cursor, VS Code and Angie',
						'bdthemes-element-pack-lite'
					) }
					free={ false }
					pro={ true }
				/>
				<Row
					label={ __( 'AI Tools screen with per-ability controls', 'bdthemes-element-pack-lite' ) }
					free={ false }
					pro={ true }
				/>
			</Group>

			<Group title={ __( 'Editor tools', 'bdthemes-element-pack-lite' ) }>
				<Row
					label={ __( 'Template Library (in Editor)', 'bdthemes-element-pack-lite' ) }
					free={ true }
					pro={ true }
				/>
				<Row
					label={ __( 'Live Copy / Paste', 'bdthemes-element-pack-lite' ) }
					free={ true }
					pro={ true }
				/>
				<Row
					label={ __( 'Duplicator', 'bdthemes-element-pack-lite' ) }
					free={ true }
					pro={ true }
				/>
				<Row
					label={ __( 'SVG Support', 'bdthemes-element-pack-lite' ) }
					free={ true }
					pro={ true }
				/>
				<Row
					label={ __( 'Asset Manager', 'bdthemes-element-pack-lite' ) }
					free={ true }
					pro={ true }
				/>
				<Row
					label={ __( 'Essential Shortcodes', 'bdthemes-element-pack-lite' ) }
					free={ false }
					pro={ true }
				/>
				<Row
					label={ __( 'Context Menu', 'bdthemes-element-pack-lite' ) }
					free={ false }
					pro={ true }
				/>
			</Group>

			<Group title={ __( 'Help & updates', 'bdthemes-element-pack-lite' ) }>
				<Row
					label={ __( 'Documentation', 'bdthemes-element-pack-lite' ) }
					free={ true }
					pro={ true }
				/>
				<Row
					label={ __( 'Updates & support', 'bdthemes-element-pack-lite' ) }
					free={ true }
					pro={ true }
				/>
				<Row
					label={ __( 'Priority support', 'bdthemes-element-pack-lite' ) }
					free={ false }
					pro={ true }
				/>
			</Group>

			<div className="flex items-center justify-between gap-4 flex-wrap py-[18px] px-5 border-t border-[#f1f1f4] dark:border-ink-border bg-[#fafbfc] dark:bg-ink-subtle">
				<span className="text-[13px] font-semibold text-[#52525b] dark:text-ink-muted">
					{ sprintf(
						/* translators: %d: number of Pro-only items */
						__(
							'%d Pro-only widgets, extensions and features are waiting for you.',
							'bdthemes-element-pack-lite'
						),
						catalog.proTotal
					) }
				</span>
				<UpgradeLink />
			</div>
		</div>
	);
}
