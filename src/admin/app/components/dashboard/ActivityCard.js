/**
 * Recent activity timeline — dynamic changelog releases (top 5) parsed from
 * the bundled readme.txt changelog via GET /changelog: version, date, per-section
 * counts and a first-bullet highlight. Falls back to the notifications feed
 * when the changelog payload is empty. Hidden when both are empty.
 */
import { __, sprintf } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { STORE_NAME } from '../../store';
import config from '../../utils/config';

const DOT_COLORS = [
	{ dot: '#22a550', ring: '#ecfdf3' },
	{ dot: '#408efa', ring: '#eff6ff' },
	{ dot: '#8b5cf6', ring: '#f3f0ff' },
	{ dot: '#f59e0b', ring: '#fff7ed' },
	{ dot: '#14b8a6', ring: '#f0fdfa' },
];

const SECTION_TONES = {
	added: 'text-[#15803d] bg-[#ecfdf3] dark:bg-[#0f2318] dark:text-[#4ade80]',
	fixed: 'text-[#408efa] bg-[#eff6ff] dark:bg-[#15233a]',
	updated: 'text-[#b45309] bg-[#fff7ed] dark:bg-[#2a1c0e] dark:text-[#fdba74]',
	removed: 'text-[#be123c] bg-[#fff1f2] dark:bg-[#2a0e14] dark:text-[#fda4af]',
	improved: 'text-[#0f766e] bg-[#f0fdfa] dark:bg-[#0c2724] dark:text-[#5eead4]',
	security: 'text-[#6d28d9] bg-[#f3f0ff] dark:bg-[#1e1636] dark:text-[#c4b5fd]',
	changed: 'text-[#b45309] bg-[#fff7ed] dark:bg-[#2a1c0e] dark:text-[#fdba74]',
	deprecated: 'text-[#71717a] bg-[#f4f4f5] dark:bg-ink-subtle',
};

const SECTION_LABELS = {
	/* translators: %d: number of changelog entries */
	added: ( n ) => sprintf( __( '%d added', 'bdthemes-element-pack-lite' ), n ),
	/* translators: %d: number of changelog entries */
	fixed: ( n ) => sprintf( __( '%d fixed', 'bdthemes-element-pack-lite' ), n ),
	/* translators: %d: number of changelog entries */
	updated: ( n ) => sprintf( __( '%d updated', 'bdthemes-element-pack-lite' ), n ),
	/* translators: %d: number of changelog entries */
	removed: ( n ) => sprintf( __( '%d removed', 'bdthemes-element-pack-lite' ), n ),
	/* translators: %d: number of changelog entries */
	improved: ( n ) => sprintf( __( '%d improved', 'bdthemes-element-pack-lite' ), n ),
	/* translators: %d: number of changelog entries */
	security: ( n ) => sprintf( __( '%d security', 'bdthemes-element-pack-lite' ), n ),
	/* translators: %d: number of changelog entries */
	changed: ( n ) => sprintf( __( '%d changed', 'bdthemes-element-pack-lite' ), n ),
	/* translators: %d: number of changelog entries */
	deprecated: ( n ) => sprintf( __( '%d deprecated', 'bdthemes-element-pack-lite' ), n ),
};

function ReleaseRow( { release, colors, isLast } ) {
	return (
		<a
			href={ config.whatsNewUrl }
			target="_blank"
			rel="noreferrer"
			className="group flex gap-[11px]"
		>
			{ /* Timeline dot + connector line. */ }
			<span className="flex flex-col items-center flex-none">
				<span
					className="w-[9px] h-[9px] rounded-full flex-none mt-[5px]"
					style={ {
						background: colors.dot,
						boxShadow: `0 0 0 3px ${ colors.ring }`,
					} }
				/>
				{ ! isLast && (
					<span className="w-px flex-1 mt-[6px] bg-[#ececf0] dark:bg-ink-border" />
				) }
			</span>

			<div className={ `min-w-0 flex-1 ${ isLast ? '' : 'pb-4' }` }>
				<div className="flex items-center gap-2 flex-wrap">
					<span className="text-[13px] font-extrabold text-[#18181b] dark:text-ink-text transition-colors group-hover:text-brand">
						v{ release.version }
					</span>
					{ release.is_current && (
						<span className="text-[9.5px] font-extrabold tracking-[.05em] uppercase text-white bg-gradient-brand py-[2px] px-[7px] rounded-full">
							{ __( 'Current', 'bdthemes-element-pack-lite' ) }
						</span>
					) }
					<span className="text-[11px] text-[#a1a1aa] font-medium ml-auto">
						{ release.date }
					</span>
				</div>

				{ release.highlights?.[ 0 ] ? (
					<div className="text-[12px] text-[#71717a] dark:text-ink-muted leading-[1.45] mt-[3px] overflow-hidden [display:-webkit-box] [-webkit-line-clamp:2] [-webkit-box-orient:vertical]">
						{ release.highlights[ 0 ] }
					</div>
				) : null }

				<div className="flex items-center gap-[5px] flex-wrap mt-[6px]">
					{ Object.entries( release.sections || {} ).map(
						( [ section, count ] ) =>
							SECTION_LABELS[ section ] ? (
								<span
									key={ section }
									className={ `text-[10.5px] font-bold py-[2px] px-[8px] rounded-full ${
										SECTION_TONES[ section ] ||
										'text-[#a1a1aa] bg-[#f4f4f5] dark:bg-ink-subtle'
									}` }
								>
									{ SECTION_LABELS[ section ]( count ) }
								</span>
							) : null
					) }
				</div>
			</div>
		</a>
	);
}

export default function ActivityCard( { title } ) {
	const releases = useSelect( ( select ) => {
		const data = select( STORE_NAME ).getResource( 'changelog' );
		return Array.isArray( data?.releases ) ? data.releases : [];
	}, [] );

	// Fallback: the remote notifications feed (release/news items).
	const feedItems = useSelect( ( select ) => {
		const data = select( STORE_NAME ).getResource( 'notifications' );
		return Array.isArray( data?.items ) ? data.items : [];
	}, [] );

	if ( ! releases.length && ! feedItems.length ) {
		return null;
	}

	return (
		<div className="bg-white dark:bg-ink-card border border-[#e9e9ed] dark:border-ink-border rounded-card p-5 shadow-resting">
			<div className="flex items-center justify-between mb-[14px]">
				<div className="text-[17px] font-extrabold text-[#0f1115] dark:text-ink-text">
					{ title ||
						__( 'Recent activity', 'bdthemes-element-pack-lite' ) }
				</div>
				<a
					href={ config.whatsNewUrl }
					target="_blank"
					rel="noreferrer"
					className="text-[12.5px] font-semibold text-brand"
				>
					{ __( 'View all', 'bdthemes-element-pack-lite' ) }
				</a>
			</div>

			{ releases.length ? (
				<div className="flex flex-col">
					{ releases.map( ( release, index ) => (
						<ReleaseRow
							key={ release.version }
							release={ release }
							colors={ DOT_COLORS[ index % DOT_COLORS.length ] }
							isLast={ index === releases.length - 1 }
						/>
					) ) }
				</div>
			) : (
				<div className="flex flex-col">
					{ feedItems.slice( 0, 4 ).map( ( item, index ) => {
						const colors = DOT_COLORS[ index % DOT_COLORS.length ];
						const row = (
							<div className="flex gap-[11px] pb-[15px]">
								<span
									className="w-[9px] h-[9px] rounded-full flex-none mt-1"
									style={ {
										background: colors.dot,
										boxShadow: `0 0 0 3px ${ colors.ring }`,
									} }
								/>
								<div className="min-w-0">
									<div className="text-[12.5px] text-[#27272a] dark:text-ink-text leading-[1.4]">
										<strong className="font-semibold">
											{ item.title }
										</strong>
										{ item.text ? ` ${ item.text }` : '' }
									</div>
									{ item.time ? (
										<div className="text-[11px] text-[#a1a1aa] mt-[2px]">
											{ item.time }
										</div>
									) : null }
								</div>
							</div>
						);

						return item.url ? (
							<a
								key={ item.id }
								href={ item.url }
								target="_blank"
								rel="noreferrer"
								className="hover:opacity-80"
							>
								{ row }
							</a>
						) : (
							<div key={ item.id }>{ row }</div>
						);
					} ) }
				</div>
			) }
		</div>
	);
}
