/**
 * Horizontal tab strip attached under the app bar.
 *
 * Tabs: primary routes, a Settings hub tab (active for /settings* and
 * /permissions), and a trailing "More" dropdown for secondary pages
 * (System Status, Rollback, …). Mirrors mockup lines 256–263.
 *
 * Responsive overflow: instead of a horizontal scrollbar, tabs that don't fit
 * the available width automatically collapse into the "More" dropdown
 * (priority-plus pattern). Widths are measured from a hidden ghost row and
 * recomputed on container resize.
 */
import {
	useState,
	useEffect,
	useLayoutEffect,
	useRef,
	useCallback,
} from '@wordpress/element';
import { useLocation, useNavigate } from 'react-router-dom';
import { __ } from '@wordpress/i18n';
import { primaryRoutes, settingsRoutes, moreRoutes } from '../../routes';
import { isRouteVisible, useNavVisibility } from '../../utils/navVisibility';
import { getExternalNavItems } from '../../utils/externalNav';
import { SlidersIcon, MoreIcon } from '../ui/Icons';

const TAB_BASE =
	'flex items-center gap-[0.45rem] py-[11px] px-[16px] rounded-full text-sm font-bold cursor-pointer whitespace-nowrap transition-all duration-200';
const TAB_ACTIVE =
	'bg-gradient-brand text-white shadow-[0_6px_16px_rgba(64,142,250,.34)]';
const TAB_IDLE =
	'bg-transparent text-[#52525b] dark:text-ink-muted hover:bg-[#f3f5f8] dark:hover:bg-[#1f242d] hover:text-[#18181b] dark:hover:text-ink-text';

// Horizontal gap between tabs (`gap-1` = 4px) — must match the row below.
const TAB_GAP = 4;

function Tab( { active, onClick, icon: TabIcon, label } ) {
	return (
		<button
			type="button"
			onClick={ onClick }
			className={ `${ TAB_BASE } ${ active ? TAB_ACTIVE : TAB_IDLE }` }
		>
			<span className="w-[18px] h-[18px] grid place-items-center">
				<TabIcon size={ 17 } />
			</span>
			{ label }
		</button>
	);
}

export default function TabStrip( { flushBottom = false } ) {
	const location = useLocation();
	const navigate = useNavigate();
	const path = location.pathname;
	const [ moreOpen, setMoreOpen ] = useState( false );
	// Dropdown offset from the wrapper's left edge, measured from the More
	// button so the panel opens under the button (the tabs don't fill the
	// strip, so a right-anchored panel would float far away from it).
	const [ moreLeft, setMoreLeft ] = useState( null );
	const moreRef = useRef( null );
	const moreBtnRef = useRef( null );

	// Re-render when the visibility map is refreshed at runtime.
	useNavVisibility();

	const visiblePrimary = primaryRoutes.filter( isRouteVisible );
	const visibleSettings = settingsRoutes.filter( isRouteVisible );
	const visibleMore = moreRoutes.filter( isRouteVisible );

	const isActive = ( route ) =>
		route.end ? path === route.path : path.startsWith( route.path );

	// Tabs point either at an SPA route or, for an external item, at a classic
	// wp-admin screen.
	const go = useCallback(
		( item ) => {
			if ( item.href ) {
				window.location.href = item.href;
				return;
			}
			navigate( item.path );
		},
		[ navigate ]
	);

	const closeMore = useCallback( () => setMoreOpen( false ), [] );

	// Close the dropdown on route change, outside click, or Escape.
	useEffect( () => {
		closeMore();
	}, [ path, closeMore ] );

	useEffect( () => {
		if ( ! moreOpen ) {
			return undefined;
		}
		const onDocClick = ( event ) => {
			if ( moreRef.current && ! moreRef.current.contains( event.target ) ) {
				closeMore();
			}
		};
		const onKey = ( event ) => {
			if ( event.key === 'Escape' ) {
				closeMore();
			}
		};
		document.addEventListener( 'mousedown', onDocClick );
		document.addEventListener( 'keydown', onKey );
		return () => {
			document.removeEventListener( 'mousedown', onDocClick );
			document.removeEventListener( 'keydown', onKey );
		};
	}, [ moreOpen, closeMore ] );

	// '/settings' exact is the Features tab; any hub page lights the Settings tab.
	const settingsActive = visibleSettings.some( isActive );

	// Ordered list of the tabs that render inline in the strip. Each descriptor
	// is normalised to { key, icon, label, path, active } so the same item can
	// be rendered either as an inline Tab or as a "More" dropdown row when it
	// overflows.
	const inlineTabs = [
		...visiblePrimary.map( ( route ) => ( {
			key: route.path,
			navKey: route.navKey,
			icon: route.icon,
			label: route.label,
			path: route.path,
			active: isActive( route ) && ! settingsActive,
		} ) ),
		...( visibleSettings.length > 0
			? [
					{
						key: '__settings',
						navKey: '__settings',
						icon: SlidersIcon,
						label: __( 'Settings', 'bdthemes-element-pack-lite' ),
						path: visibleSettings[ 0 ].path,
						active: settingsActive,
					},
			  ]
			: [] ),
	];

	// Classic wp-admin screens slot in by name, or at the end of the strip when
	// they don't name a tab.
	getExternalNavItems( 'primary' ).forEach( ( item ) => {
		const after = item.after
			? inlineTabs.findIndex( ( tab ) => tab.navKey === item.after )
			: -1;
		if ( after !== -1 ) {
			inlineTabs.splice( after + 1, 0, item );
			return;
		}
		inlineTabs.push( item );
	} );

	// Secondary routes that always live in "More".
	const secondaryItems = [
		...visibleMore.map( ( route ) => ( {
			key: route.path,
			icon: route.icon,
			label: route.label,
			path: route.path,
			active: isActive( route ),
		} ) ),
		...getExternalNavItems( 'more' ),
	];

	// --- Responsive overflow measurement ------------------------------------
	const rowRef = useRef( null );
	const ghostRef = useRef( null );
	const itemWidthsRef = useRef( [] );
	const moreWidthRef = useRef( 0 );
	const [ visibleCount, setVisibleCount ] = useState( inlineTabs.length );

	const recompute = useCallback( () => {
		const row = rowRef.current;
		const widths = itemWidthsRef.current;

		if ( ! row || ! widths.length ) {
			return;
		}

		const styles = window.getComputedStyle( row );
		const padX =
			parseFloat( styles.paddingLeft ) +
			parseFloat( styles.paddingRight );
		const available = row.clientWidth - padX;
		const moreW = moreWidthRef.current || 0;
		const total = widths.length;

		// Full width if every inline tab is shown (no More button).
		let allWidth = 0;
		widths.forEach( ( w, i ) => {
			allWidth += w + ( i > 0 ? TAB_GAP : 0 );
		} );

		// Everything fits and there are no permanent secondary items → show all.
		if ( allWidth <= available && secondaryItems.length === 0 ) {
			setVisibleCount( ( prev ) => ( prev === total ? prev : total ) );
			return;
		}

		// Otherwise reserve room for the More button and greedily fit tabs.
		const budget = available - moreW - TAB_GAP;
		let used = 0;
		let count = 0;
		for ( let i = 0; i < total; i++ ) {
			const add = widths[ i ] + ( count > 0 ? TAB_GAP : 0 );
			if ( used + add <= budget ) {
				used += add;
				count += 1;
			} else {
				break;
			}
		}

		setVisibleCount( ( prev ) => ( prev === count ? prev : count ) );
	}, [ secondaryItems.length ] );

	// Measure per-tab + More button widths from the hidden ghost row. Runs
	// after every render; setVisibleCount bails when unchanged so it settles.
	useLayoutEffect( () => {
		const ghost = ghostRef.current;
		if ( ! ghost ) {
			return;
		}
		const kids = Array.from( ghost.children );
		const moreEl = kids.pop();
		itemWidthsRef.current = kids.map(
			( el ) => el.getBoundingClientRect().width
		);
		moreWidthRef.current = moreEl
			? moreEl.getBoundingClientRect().width
			: 0;
		recompute();
	} );

	// Recompute when the strip is resized (window/sidebar/collapse changes).
	useEffect( () => {
		const row = rowRef.current;
		if ( ! row || typeof window.ResizeObserver === 'undefined' ) {
			return undefined;
		}
		const observer = new window.ResizeObserver( () => recompute() );
		observer.observe( row );
		return () => observer.disconnect();
	}, [ recompute ] );

	const shownTabs = inlineTabs.slice( 0, visibleCount );
	const overflowTabs = inlineTabs.slice( visibleCount );
	const moreItems = [ ...overflowTabs, ...secondaryItems ];
	const moreActive = moreItems.some( ( item ) => item.active ) || moreOpen;

	return (
		<div
			className={ `hidden min-[901px]:block relative border-t border-[#eef0f3] dark:border-ink-border bg-[#fafbfc] dark:bg-ink-subtle ${
				flushBottom ? '' : 'rounded-b-card'
			}` }
			ref={ moreRef }
		>
			{ /* Hidden ghost row: renders every inline tab + a More button so
			     their natural widths can be measured regardless of what is
			     currently shown. Kept off the a11y tree.

			     `invisible` hides it but keeps it in layout, so the clipper
			     below is what stops the wider-than-viewport row from adding
			     to the page's scroll width. The clipper can't go on the
			     strip wrapper itself — the More dropdown hangs below it and
			     would be cut off — and the row keeps `w-max` so its tabs
			     never shrink and mis-measure. */ }
			<div
				aria-hidden="true"
				className="absolute inset-x-0 top-0 overflow-hidden pointer-events-none"
			>
				<div
					ref={ ghostRef }
					className="flex w-max gap-1 py-2 px-4 invisible"
				>
					{ inlineTabs.map( ( item ) => (
						<Tab
							key={ item.key }
							active={ false }
							onClick={ () => {} }
							icon={ item.icon }
							label={ item.label }
						/>
					) ) }
					<Tab
						active={ false }
						onClick={ () => {} }
						icon={ MoreIcon }
						label={ __( 'More', 'bdthemes-element-pack-lite' ) }
					/>
				</div>
			</div>

			<div ref={ rowRef } className="flex flex-nowrap gap-1 py-2 px-4 overflow-hidden">
				{ shownTabs.map( ( item ) => (
					<Tab
						key={ item.key }
						active={ item.active }
						onClick={ () => go( item ) }
						icon={ item.icon }
						label={ item.label }
					/>
				) ) }

				{ moreItems.length > 0 ? (
					<span ref={ moreBtnRef } className="inline-flex">
						<Tab
							active={ moreActive }
							onClick={ () => {
								if ( ! moreOpen && moreBtnRef.current && moreRef.current ) {
									const btn = moreBtnRef.current.getBoundingClientRect();
									const wrap = moreRef.current.getBoundingClientRect();
									// Keep the 220px panel inside the strip.
									setMoreLeft(
										Math.max(
											8,
											Math.min(
												btn.left - wrap.left,
												wrap.width - 228
											)
										)
									);
								}
								setMoreOpen( ( value ) => ! value );
							} }
							icon={ MoreIcon }
							label={ __( 'More', 'bdthemes-element-pack-lite' ) }
						/>
					</span>
				) : null }
			</div>

			{ moreOpen ? (
				<div
					style={ moreLeft !== null ? { left: moreLeft } : undefined }
					className={ `absolute top-full mt-1 min-w-[220px] bg-white dark:bg-ink-card border border-[#e6e6ea] dark:border-ink-border rounded-panel shadow-pop z-50 overflow-hidden animate-ep-pop py-1.5 ${
						moreLeft === null ? 'right-4' : ''
					}` }>
					{ moreItems.map( ( item ) => {
						const RouteIcon = item.icon;
						return (
							<button
								key={ item.key }
								type="button"
								onClick={ () => go( item ) }
								className={ `w-full flex items-center gap-[10px] py-[10px] px-[14px] text-left text-[13.5px] font-bold cursor-pointer transition-colors ${
									item.active
										? 'text-brand bg-[#f0f7ff] dark:bg-[#15233a]'
										: 'text-[#3f3f46] dark:text-ink-text hover:bg-[#f3f5f8] dark:hover:bg-[#1f242d]'
								}` }
							>
								<span
									className={ `w-[18px] h-[18px] grid place-items-center ${
										item.active
											? 'text-brand'
											: 'text-[#71717a] dark:text-ink-muted'
									}` }
								>
									<RouteIcon size={ 16 } />
								</span>
								{ item.label }
							</button>
						);
					} ) }
				</div>
			) : null }
		</div>
	);
}
