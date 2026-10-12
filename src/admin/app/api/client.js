/**
 * Thin wrapper around @wordpress/api-fetch for the element-pack/v1 namespace.
 *
 * The WordPress core "wp-api-fetch" script (a build dependency) already wires
 * the REST root URL and the X-WP-Nonce middleware, so we only prefix the
 * namespace and unwrap the { success, data } envelope our controllers return.
 */
import apiFetch from '@wordpress/api-fetch';
import config from '../utils/config';

const NS = config.restNamespace || 'element-pack/v1';

function unwrap( response ) {
	if ( response && typeof response === 'object' && 'success' in response ) {
		if ( response.success ) {
			return response.data;
		}
		throw new Error( response.message || 'Request failed.' );
	}
	return response;
}

function normalizeError( error ) {
	let message =
		( error && ( error.message || error.code ) ) || 'Unexpected error.';
	// A fatal on the server returns WP's HTML error page as the message —
	// strip markup so alerts stay readable.
	if ( typeof message === 'string' && message.includes( '<' ) ) {
		const div = document.createElement( 'div' );
		div.innerHTML = message;
		message = ( div.textContent || '' ).trim() || 'Request failed.';
	}
	const normalized = new Error( message );
	normalized.code = error && error.code ? error.code : 'unknown_error';
	return normalized;
}

export function apiGet( resource, query = {} ) {
	const qs = new URLSearchParams( query ).toString();
	const path = qs ? `${ NS }/${ resource }?${ qs }` : `${ NS }/${ resource }`;
	return apiFetch( { path } )
		.then( unwrap )
		.catch( ( e ) => {
			throw normalizeError( e );
		} );
}

export function apiPost( resource, data ) {
	return apiFetch( { path: `${ NS }/${ resource }`, method: 'POST', data } )
		.then( unwrap )
		.catch( ( e ) => {
			throw normalizeError( e );
		} );
}

/**
 * POST without the { success, data } envelope.
 *
 * The AI Tools endpoints answer with their payload beside `success`
 * (`{ success, settings }`, `{ success, password, … }`) rather than inside a
 * `data` key, so unwrapping would throw the response away.
 *
 * @param {string} resource Route below the namespace.
 * @param {Object} data     JSON body.
 * @return {Promise<Object>} The raw response body.
 */
export function apiPostRaw( resource, data ) {
	return apiFetch( {
		path: `${ NS }/${ resource }`,
		method: 'POST',
		data,
	} ).catch( ( e ) => {
		throw normalizeError( e );
	} );
}

export default { apiGet, apiPost, apiPostRaw };
