/**
 * Root application component.
 *
 * Sets up hash-based routing (no server rewrite needed inside wp-admin),
 * the persistent admin layout (app bar + tab strip), the ⌘K command palette,
 * and a Suspense boundary for lazy route chunks.
 */
import { Suspense, useState, useEffect, useCallback } from '@wordpress/element';
import {
	HashRouter,
	Routes,
	Route,
	Navigate,
	useLocation,
} from 'react-router-dom';
import AdminLayout from './layouts/AdminLayout';
import SettingsLayout from './layouts/SettingsLayout';
import CommandPalette from './components/shell/CommandPalette';
import { ToastProvider } from './components/ui/Toast';
import Spinner from './components/Spinner';
import { routes } from './routes';
import { useColorScheme } from './hooks/useColorScheme';
import { useInitialRoute } from './hooks/useInitialRoute';

function RouteSync() {
	useInitialRoute();
	return null;
}

/**
 * Highlight the matching WP sidebar submenu entry as the SPA route changes —
 * WordPress can't track hash routes on its own.
 */
function WpSubmenuSync() {
	const location = useLocation();

	useEffect( () => {
		const items = document.querySelectorAll(
			'#toplevel_page_element_pack_options .wp-submenu li'
		);
		items.forEach( ( li ) => {
			const link = li.querySelector( 'a' );
			if ( ! link ) {
				return;
			}
			const href = link.getAttribute( 'href' ) || '';
			// Only manage links into the SPA — leave Theme Builder / CPT
			// entries to WordPress's own current-item handling.
			if ( ! href.includes( 'page=element_pack_options' ) ) {
				return;
			}
			const hashIndex = href.indexOf( '#' );
			const route =
				hashIndex === -1 ? '/' : href.slice( hashIndex + 1 ) || '/';
			const active =
				route === '/'
					? location.pathname === '/'
					: location.pathname === route ||
					  location.pathname.startsWith( `${ route }/` );
			li.classList.toggle( 'current', active );
			link.classList.toggle( 'current', active );
			if ( active ) {
				link.setAttribute( 'aria-current', 'page' );
			} else {
				link.removeAttribute( 'aria-current' );
			}
		} );
	}, [ location.pathname ] );

	return null;
}

function toRoute( route ) {
	const Page = route.element;
	return (
		<Route key={ route.path } path={ route.path } element={ <Page /> } />
	);
}

export default function App() {
	// Initialise dark-mode-ready data attribute on the app root.
	useColorScheme();

	const [ paletteOpen, setPaletteOpen ] = useState( false );
	const openPalette = useCallback( () => setPaletteOpen( true ), [] );
	const closePalette = useCallback( () => setPaletteOpen( false ), [] );

	useEffect( () => {
		const onKey = ( event ) => {
			if ( ( event.metaKey || event.ctrlKey ) && event.key === 'k' ) {
				event.preventDefault();
				setPaletteOpen( ( value ) => ! value );
			}
		};
		document.addEventListener( 'keydown', onKey );
		return () => document.removeEventListener( 'keydown', onKey );
	}, [] );

	const settingsGroup = routes.filter(
		( route ) => route.group === 'settings'
	);
	const plainGroup = routes.filter( ( route ) => route.group !== 'settings' );

	return (
		<HashRouter>
			<RouteSync />
			<WpSubmenuSync />
			<ToastProvider>
				<AdminLayout onOpenPalette={ openPalette }>
					<Suspense fallback={ <Spinner full /> }>
						<Routes>
							{ /* 3rd Party Widgets merged into the Widgets tab. */ }
						<Route
							path="/third-party"
							element={ <Navigate to="/widgets" replace /> }
						/>
						{ /* Old API Settings URL → Integrations tab. */ }
							<Route
								path="/settings/api"
								element={ <Navigate to="/integrations" replace /> }
							/>
							{ plainGroup.map( toRoute ) }
							<Route element={ <SettingsLayout /> }>
								{ settingsGroup.map( toRoute ) }
							</Route>
						</Routes>
					</Suspense>
				</AdminLayout>
				<CommandPalette open={ paletteOpen } onClose={ closePalette } />
			</ToastProvider>
		</HashRouter>
	);
}
