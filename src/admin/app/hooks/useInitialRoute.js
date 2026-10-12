/**
 * Sync admin.php query params and legacy hash fragments into HashRouter on load.
 */
import { useEffect } from '@wordpress/element';
import { useNavigate, useLocation } from 'react-router-dom';
import { legacyHashToRoute } from '../utils/legacyHashRoutes';

export function useInitialRoute() {
	const navigate = useNavigate();
	const location = useLocation();

	useEffect( () => {
		const params = new URLSearchParams( window.location.search );
		const route = params.get( 'ep_route' );

		if ( route ) {
			const path = '/' + route.replace( /^\/+/, '' );

			if ( location.pathname !== path ) {
				navigate( path, { replace: true } );
			}

			return;
		}

		const legacyRoute = legacyHashToRoute( window.location.hash );

		if ( ! legacyRoute || location.pathname === legacyRoute ) {
			return;
		}

		navigate( legacyRoute, { replace: true } );

		const base = window.location.pathname + window.location.search;
		window.history.replaceState( null, '', base + '#' + legacyRoute );
	}, [ navigate, location.pathname ] );
}
