/**
 * Settings hub wrapper. On desktop the hub's sub-nav renders inside the
 * app-bar card (SettingsSubNav via AdminLayout); here we only keep a
 * mobile fallback chip row (the app-bar strip is hidden under 901px) and
 * the routed page outlet.
 */
import { useLocation, useNavigate, Outlet } from 'react-router-dom';
import { settingsRoutes } from '../routes';
import { isRouteVisible, useNavVisibility } from '../utils/navVisibility';

export default function SettingsLayout() {
	const location = useLocation();
	const navigate = useNavigate();

	// Re-render when the visibility map is refreshed at runtime.
	useNavVisibility();

	const routePages = settingsRoutes.filter( isRouteVisible );

	return (
		<div>
			<nav
				aria-label="Settings"
				className="min-[901px]:hidden flex items-center gap-2 flex-wrap mb-4"
			>
				{ routePages.map( ( route ) => {
					const RouteIcon = route.icon;
					const active = location.pathname.startsWith( route.path );
					return (
						<button
							key={ route.path }
							type="button"
							onClick={ () => navigate( route.path ) }
							aria-current={ active ? 'page' : undefined }
							className={ `flex items-center gap-[6px] py-[7px] px-[13px] rounded-full text-[12.5px] font-bold cursor-pointer border font-sans transition-colors ${
								active
									? 'bg-brand border-brand text-white'
									: 'bg-white dark:bg-ink-card border-[#e2e2e6] dark:border-ink-border text-[#52525b] dark:text-ink-muted'
							}` }
						>
							<RouteIcon size={ 14 } />
							{ route.label }
						</button>
					);
				} ) }
			</nav>

			<Outlet />
		</div>
	);
}
