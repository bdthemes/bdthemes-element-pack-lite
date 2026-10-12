/**
 * Nav visibility backed by `window.elementPackAdmin.navVisibility` (built
 * server-side by Menu_Visibility_Service), with a tiny reactive layer so the
 * map can be refreshed at runtime without reloading. Shared by the tab strip, mobile nav,
 * settings sub-nav, dashboard shortcuts and the command palette.
 */
import { useSyncExternalStore } from '@wordpress/element';
import config from './config';

let liveVisibility = config.navVisibility;
const listeners = new Set();

function subscribe( listener ) {
	listeners.add( listener );
	return () => listeners.delete( listener );
}

/** Replace the visibility map at runtime and re-render subscribers. */
export function updateNavVisibility( next ) {
	if ( ! next || typeof next !== 'object' ) {
		return;
	}
	liveVisibility = next;
	listeners.forEach( ( listener ) => listener() );
}

/**
 * Subscribe a component to visibility changes. Returns the current map;
 * calling this is what makes isNavVisible()/isRouteVisible() results live.
 */
export function useNavVisibility() {
	return useSyncExternalStore( subscribe, () => liveVisibility );
}

export function isNavVisible( key ) {
	const visibility = liveVisibility;

	if ( ! visibility || typeof visibility !== 'object' ) {
		return true;
	}

	if ( ! key ) {
		return true;
	}

	return visibility[ key ] !== false;
}

export function isRouteVisible( route ) {
	return isNavVisible( route.navKey );
}
