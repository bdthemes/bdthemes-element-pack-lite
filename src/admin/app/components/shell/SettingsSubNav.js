/**
 * Second-level nav for the Settings hub (none yet: the
 * hub pages live in the "More" menu), rendered attached under the main TabStrip inside
 * the app-bar card. Only appears while a settings-hub route is active —
 * AdminLayout gates it via isSettingsHubRoute().
 */
import { useLocation, useNavigate } from 'react-router-dom';
import { settingsRoutes } from '../../routes';
import { isRouteVisible, useNavVisibility } from '../../utils/navVisibility';

const TAB_BASE =
	'flex items-center gap-[7px] py-[8px] px-[15px] rounded-full text-[13px] font-bold cursor-pointer whitespace-nowrap transition-all duration-200 border-0 font-sans';
const TAB_ACTIVE =
	'bg-gradient-brand text-white shadow-[0_5px_14px_rgba(64,142,250,.32)]';
const TAB_IDLE =
	'bg-transparent text-[#52525b] dark:text-ink-muted hover:bg-[#eef1f5] dark:hover:bg-[#1f242d] hover:text-[#18181b] dark:hover:text-ink-text';

/** Whether the current path belongs to the Settings hub. */
export function isSettingsHubRoute( pathname ) {
	return settingsRoutes.some( ( route ) =>
		pathname.startsWith( route.path )
	);
}

export default function SettingsSubNav() {
	const location = useLocation();
	const navigate = useNavigate();

	// Re-render when the visibility map is refreshed at runtime.
	useNavVisibility();

	const routePages = settingsRoutes.filter( isRouteVisible );

	if ( ! routePages.length ) {
		return null;
	}

	return (
		<nav
			aria-label="Settings"
			className="hidden min-[901px]:flex items-center gap-1 flex-wrap py-[7px] px-4 border-t border-[#eef0f3] dark:border-ink-border bg-white dark:bg-ink-card rounded-b-card"
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
						className={ `${ TAB_BASE } ${
							active ? TAB_ACTIVE : TAB_IDLE
						}` }
					>
						<span className="w-4 h-4 grid place-items-center">
							<RouteIcon size={ 15 } />
						</span>
						{ route.label }
					</button>
				);
			} ) }
		</nav>
	);
}
