/**
 * Mobile navigation (≤900px) — mockup lines 1367–1407: fixed bottom tab bar
 * with four primary tabs + a "More" button that opens a bottom sheet listing
 * every section, plus the theme toggle.
 */
import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useLocation, useNavigate } from 'react-router-dom';
import { routes, settingsRoutes } from '../../routes';
import { isRouteVisible, useNavVisibility } from '../../utils/navVisibility';
import { getExternalNavItems } from '../../utils/externalNav';
import { useColorScheme } from '../../hooks/useColorScheme';
import { MoreIcon, SunIcon, MoonIcon } from '../ui/Icons';

const BAR_KEYS = [ 'dashboard', 'widgets', 'extensions', 'settings' ];

export default function MobileNav() {
	const location = useLocation();
	const navigate = useNavigate();
	const [ moreOpen, setMoreOpen ] = useState( false );
	const { theme, toggle } = useColorScheme();
	const isDark = theme === 'dark';

	// Re-render when the visibility map is refreshed at runtime.
	useNavVisibility();

	// Close the sheet whenever the route changes.
	useEffect( () => {
		setMoreOpen( false );
	}, [ location.pathname ] );

	useEffect( () => {
		if ( ! moreOpen ) {
			return undefined;
		}
		const onKey = ( event ) => {
			if ( event.key === 'Escape' ) {
				setMoreOpen( false );
			}
		};
		document.addEventListener( 'keydown', onKey );
		return () => document.removeEventListener( 'keydown', onKey );
	}, [ moreOpen ] );

	const visible = routes.filter( isRouteVisible );
	const barTabs = BAR_KEYS.map( ( key ) =>
		visible.find( ( route ) => route.navKey === key )
	).filter( Boolean );

	const isActive = ( route ) =>
		route.end
			? location.pathname === route.path
			: location.pathname.startsWith( route.path );

	// Hub pages (/settings/*) have no bar tab of their own —
	// they surface via the "More" sheet. '/settings' exact is the Special
	// Features tab.
	const tabActive = ( route ) => isActive( route );

	const barPaths = barTabs.map( ( route ) => route.path );
	const moreActive = ! visible.some(
		( route ) => barPaths.includes( route.path ) && isActive( route )
	);

	// Sheet lists everything: primary routes + settings hub pages + the
	// secondary "more" pages (Performance, System Status, …), followed by the
	// classic wp-admin screens (AI Tools, …), which are links rather than
	// routes and so carry an href.
	const sheetItems = [
		...visible
			.filter(
				( route ) =>
					route.group === 'primary' ||
					route.group === 'settings' ||
					route.group === 'more'
			)
			.map( ( route ) => ( {
				key: route.path,
				icon: route.icon,
				label: route.label,
				path: route.path,
				active: isActive( route ),
			} ) ),
		...getExternalNavItems( 'primary' ),
		...getExternalNavItems( 'more' ),
	];

	const go = ( item ) => {
		if ( item.href ) {
			window.location.href = item.href;
			return;
		}
		navigate( item.path );
	};

	return (
		<>
			<div className="hidden max-[900px]:flex fixed left-0 right-0 bottom-0 z-40 bg-white/[.93] dark:bg-ink-card/[.93] backdrop-blur-[16px] border-t border-[#ececf0] dark:border-ink-border items-stretch justify-around py-[7px] px-[6px] [padding-bottom:calc(7px+env(safe-area-inset-bottom))]">
				{ barTabs.map( ( route ) => {
					const RouteIcon = route.icon;
					const active = tabActive( route );
					return (
						<button
							key={ route.path }
							type="button"
							onClick={ () => navigate( route.path ) }
							className={ `flex-1 flex flex-col items-center gap-1 bg-transparent border-0 cursor-pointer font-sans py-[5px] px-[2px] transition-colors ${
								active
									? 'text-brand font-extrabold'
									: 'text-[#71717a] dark:text-ink-muted font-semibold'
							}` }
						>
							<span className="grid place-items-center w-6 h-6">
								<RouteIcon size={ 20 } />
							</span>
							<span className="text-[10.5px] tracking-[.01em]">
								{ route.label }
							</span>
						</button>
					);
				} ) }
				<button
					type="button"
					onClick={ () => setMoreOpen( true ) }
					className={ `flex-1 flex flex-col items-center gap-1 bg-transparent border-0 cursor-pointer font-sans py-[5px] px-[2px] transition-colors ${
						moreActive
							? 'text-brand font-extrabold'
							: 'text-[#71717a] dark:text-ink-muted font-semibold'
					}` }
				>
					<span className="grid place-items-center w-6 h-6">
						<MoreIcon size={ 22 } />
					</span>
					<span className="text-[10.5px]">
						{ __( 'More', 'bdthemes-element-pack-lite' ) }
					</span>
				</button>
			</div>

			{ moreOpen ? (
				<div
					className="fixed inset-0 z-50 bg-[rgba(9,9,11,.5)] backdrop-blur-sm flex items-end justify-center"
					onClick={ ( event ) => {
						if ( event.target === event.currentTarget ) {
							setMoreOpen( false );
						}
					} }
					role="presentation"
				>
					<div className="w-full max-w-[560px] bg-white dark:bg-ink-card rounded-t-sheet max-h-[84vh] overflow-y-auto shadow-[0_-10px_50px_rgba(0,0,0,.32)] animate-ep-pop">
						<div className="pt-[9px] pb-[2px]">
							<div className="w-[38px] h-[5px] rounded-full bg-[#cfcfd6] dark:bg-ink-border mx-auto" />
						</div>
						<div className="pt-[6px] px-[18px] pb-[22px]">
							<div className="flex items-center justify-between py-2 px-1 pb-3">
								<span className="text-xs font-extrabold tracking-[.08em] uppercase text-[#a1a1aa]">
									{ __(
										'All sections',
										'bdthemes-element-pack-lite'
									) }
								</span>
								<button
									type="button"
									onClick={ toggle }
									className="flex items-center gap-[7px] bg-[#f4f5f7] dark:bg-ink-subtle border border-[#e6e6ea] dark:border-ink-border text-[#52525b] dark:text-ink-muted py-[7px] px-[13px] rounded-full font-sans text-[12.5px] font-bold cursor-pointer"
								>
									{ isDark ? (
										<SunIcon size={ 15 } />
									) : (
										<MoonIcon size={ 15 } />
									) }
									{ isDark
										? __(
												'Light mode',
												'bdthemes-element-pack-lite'
										  )
										: __(
												'Dark mode',
												'bdthemes-element-pack-lite'
										  ) }
								</button>
							</div>
							<div className="grid grid-cols-2 gap-[9px]">
								{ sheetItems.map( ( item ) => {
									const RouteIcon = item.icon;
									const active = item.active;
									return (
										<button
											key={ item.key }
											type="button"
											onClick={ () => go( item ) }
											className={ `flex items-center gap-[11px] py-[13px] px-[13px] border rounded-[14px] cursor-pointer font-sans text-left transition-colors ${
												active
													? 'border-brand bg-[#f0f7ff] dark:bg-[#15233a] text-brand'
													: 'border-[#ececf0] dark:border-ink-border bg-transparent text-[#3f3f46] dark:text-ink-text'
											}` }
										>
											<span
												className={ `w-8 h-8 rounded-[9px] flex-none grid place-items-center bg-white dark:bg-ink-card border border-[#ececf0] dark:border-ink-border ${
													active
														? 'text-brand'
														: 'text-[#71717a] dark:text-ink-muted'
												}` }
											>
												<RouteIcon size={ 16 } />
											</span>
											<span className="text-[13.5px] font-bold whitespace-nowrap overflow-hidden text-ellipsis">
												{ item.label }
											</span>
										</button>
									);
								} ) }
							</div>
						</div>
					</div>
				</div>
			) : null }
		</>
	);
}
