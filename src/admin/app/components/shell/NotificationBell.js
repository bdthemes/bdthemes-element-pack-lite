/**
 * Notification bell + dropdown panel.
 *
 * Content mirrors the dashboard Activity card: the top changelog releases
 * (GET /changelog — version, date, per-section count chips, highlight) followed
 * by remote feed news (GET /notifications, minus its duplicate release entry).
 *
 * Unread state is purely client-side: items whose timestamp is newer than the
 * localStorage read-marker count as unread; "Mark all read" bumps the marker.
 */
import { useState, useEffect, useRef, useCallback } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { __, sprintf } from '@wordpress/i18n';
import { STORE_NAME } from '../../store';
import config from '../../utils/config';
import {
	BellIcon,
	ZapIcon,
	BookIcon,
	WarnIcon,
	SparklesIcon,
} from '../ui/Icons';

const READ_KEY = 'epAdminNotifsReadAt';

const TYPE_STYLES = {
	release: {
		tint: 'bg-[#eff6ff] text-brand dark:bg-[#15233a]',
		icon: ZapIcon,
	},
	news: {
		tint: 'bg-[#f3f0ff] text-[#8b5cf6] dark:bg-[#241d3a]',
		icon: SparklesIcon,
	},
	docs: {
		tint: 'bg-[#e6fffb] text-[#14b8a6] dark:bg-[#0e2b28]',
		icon: BookIcon,
	},
	warning: {
		tint: 'bg-[#fff7ed] text-[#f59e0b] dark:bg-[#2a1c0e]',
		icon: WarnIcon,
	},
};

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

const ROW_CLASS =
	'flex gap-3 py-[14px] px-[18px] border-b border-[#f5f5f7] dark:border-ink-border cursor-pointer transition-colors hover:bg-[#fafafb] dark:hover:bg-[#1f242d]';

function getReadAt() {
	const value = Number( window.localStorage.getItem( READ_KEY ) );
	return Number.isFinite( value ) ? value : 0;
}

function ReleaseRow( { release, unread } ) {
	return (
		<a
			href={ config.whatsNewUrl }
			target="_blank"
			rel="noreferrer"
			className={ ROW_CLASS }
		>
			<span
				className={ `w-9 h-9 rounded-btn flex-none grid place-items-center ${ TYPE_STYLES.release.tint }` }
			>
				<ZapIcon size={ 17 } />
			</span>
			<div className="flex-1 min-w-0">
				<div className="flex items-center gap-[7px] flex-wrap">
					<span className="text-[13.5px] font-extrabold text-[#1a1a1f] dark:text-ink-text">
						v{ release.version }
					</span>
					{ release.is_current && (
						<span className="text-[9px] font-extrabold tracking-[.05em] uppercase text-white bg-gradient-brand py-[2px] px-[6px] rounded-full">
							{ __( 'Current', 'bdthemes-element-pack-lite' ) }
						</span>
					) }
					{ unread ? (
						<span className="w-[7px] h-[7px] rounded-full bg-brand flex-none" />
					) : null }
					<span className="text-[10.5px] text-[#b4b4bb] font-semibold ml-auto">
						{ release.date }
					</span>
				</div>

				{ release.highlights?.[ 0 ] ? (
					<div className="text-xs text-[#71717a] dark:text-ink-muted mt-[3px] leading-[1.45] overflow-hidden [display:-webkit-box] [-webkit-line-clamp:2] [-webkit-box-orient:vertical]">
						{ release.highlights[ 0 ] }
					</div>
				) : null }

				<div className="flex items-center gap-1 flex-wrap mt-[6px]">
					{ Object.entries( release.sections || {} ).map(
						( [ section, count ] ) =>
							SECTION_LABELS[ section ] ? (
								<span
									key={ section }
									className={ `text-[10px] font-bold py-[2px] px-[7px] rounded-full ${
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

export default function NotificationBell( { buttonClassName } ) {
	const [ open, setOpen ] = useState( false );
	const [ readAt, setReadAt ] = useState( getReadAt );
	const wrapRef = useRef( null );

	const releases = useSelect( ( select ) => {
		const data = select( STORE_NAME ).getResource( 'changelog' );
		return Array.isArray( data?.releases ) ? data.releases : [];
	}, [] );

	// Remote feed only — the changelog releases above replace the feed's own
	// single "What's new in vX" release entry.
	const newsItems = useSelect( ( select ) => {
		const data = select( STORE_NAME ).getResource( 'notifications' );
		const items = Array.isArray( data?.items ) ? data.items : [];
		return items.filter( ( item ) => item.type !== 'release' );
	}, [] );

	const unreadCount =
		releases.filter( ( release ) => ( release.timestamp || 0 ) > readAt )
			.length +
		newsItems.filter( ( item ) => ( item.timestamp || 0 ) > readAt ).length;

	const close = useCallback( () => setOpen( false ), [] );

	useEffect( () => {
		if ( ! open ) {
			return undefined;
		}
		const onDocClick = ( event ) => {
			if (
				wrapRef.current &&
				! wrapRef.current.contains( event.target )
			) {
				close();
			}
		};
		const onKey = ( event ) => {
			if ( event.key === 'Escape' ) {
				close();
			}
		};
		document.addEventListener( 'mousedown', onDocClick );
		document.addEventListener( 'keydown', onKey );
		return () => {
			document.removeEventListener( 'mousedown', onDocClick );
			document.removeEventListener( 'keydown', onKey );
		};
	}, [ open, close ] );

	const markAllRead = () => {
		const now = Math.floor( Date.now() / 1000 );
		window.localStorage.setItem( READ_KEY, String( now ) );
		setReadAt( now );
	};

	const isEmpty = releases.length === 0 && newsItems.length === 0;

	return (
		<div className="relative" ref={ wrapRef }>
			<button
				type="button"
				className={ buttonClassName + ' relative' }
				onClick={ () => setOpen( ( value ) => ! value ) }
				aria-label={ __( 'Notifications', 'bdthemes-element-pack-lite' ) }
			>
				<BellIcon size={ 19 } />
				{ unreadCount > 0 ? (
					<span className="absolute -top-[5px] -right-[5px] min-w-[18px] h-[18px] px-[5px] rounded-full [background:linear-gradient(135deg,#fb7185,#e11d48)] text-white text-[10px] font-extrabold leading-none grid place-items-center ring-2 ring-white dark:ring-ink-card shadow-[0_2px_6px_rgba(225,29,72,.45)] animate-ep-pop">
						{ unreadCount > 9 ? '9+' : unreadCount }
					</span>
				) : null }
			</button>

			{ open ? (
				<div className="absolute top-[50px] right-0 w-[380px] max-w-[88vw] bg-white dark:bg-ink-card border border-[#e6e6ea] dark:border-ink-border rounded-panel shadow-pop z-50 overflow-hidden animate-ep-pop">
					<div className="flex items-center justify-between py-[15px] px-[18px] border-b border-[#f1f1f4] dark:border-ink-border">
						<span className="text-[15px] font-extrabold text-[#0f1115] dark:text-ink-text">
							{ __( 'Notifications', 'bdthemes-element-pack-lite' ) }
						</span>
						<button
							type="button"
							onClick={ markAllRead }
							className="text-brand text-[12.5px] font-bold cursor-pointer"
						>
							{ __( 'Mark all read', 'bdthemes-element-pack-lite' ) }
						</button>
					</div>

					<div className="max-h-[340px] overflow-y-auto">
						{ isEmpty ? (
							<div className="py-8 px-[18px] text-center text-[13px] text-[#71717a] dark:text-ink-muted">
								{ __(
									'No notifications yet.',
									'bdthemes-element-pack-lite'
								) }
							</div>
						) : (
							<>
								{ releases.map( ( release ) => (
									<ReleaseRow
										key={ release.version }
										release={ release }
										unread={
											( release.timestamp || 0 ) > readAt
										}
									/>
								) ) }

								{ newsItems.map( ( item ) => {
									const styles =
										TYPE_STYLES[ item.type ] ||
										TYPE_STYLES.news;
									const TypeIcon = styles.icon;
									const unread =
										( item.timestamp || 0 ) > readAt;
									const body = (
										<>
											<span
												className={ `w-9 h-9 rounded-btn flex-none grid place-items-center ${ styles.tint }` }
											>
												<TypeIcon size={ 17 } />
											</span>
											<div className="flex-1 min-w-0">
												<div className="flex items-center gap-[7px]">
													<span className="text-[13.5px] font-bold text-[#1a1a1f] dark:text-ink-text">
														{ item.title }
													</span>
													{ unread ? (
														<span className="w-[7px] h-[7px] rounded-full bg-brand flex-none" />
													) : null }
												</div>
												{ item.text ? (
													<div className="text-xs text-[#71717a] dark:text-ink-muted mt-[2px] leading-[1.45]">
														{ item.text }
													</div>
												) : null }
												{ item.time ? (
													<div className="text-[11px] text-[#b4b4bb] mt-1 font-semibold">
														{ item.time }
													</div>
												) : null }
											</div>
										</>
									);

									return item.url ? (
										<a
											key={ item.id }
											href={ item.url }
											target="_blank"
											rel="noreferrer"
											className={ ROW_CLASS }
										>
											{ body }
										</a>
									) : (
										<div
											key={ item.id }
											className={ ROW_CLASS }
										>
											{ body }
										</div>
									);
								} ) }
							</>
						) }
					</div>
				</div>
			) : null }
		</div>
	);
}
