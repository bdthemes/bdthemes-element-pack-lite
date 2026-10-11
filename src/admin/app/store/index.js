/**
 * @wordpress/data store for the Element Pack admin SPA.
 *
 * Uses thunk actions/resolvers (enabled by default in modern @wordpress/data)
 * to fetch and mutate data through the REST client. Components read state via
 * useSelect and trigger mutations via useDispatch.
 */
import { createReduxStore, register } from '@wordpress/data';
import { apiGet, apiPost } from '../api/client';

export const STORE_NAME = 'element-pack/admin';

/** REST path overrides when store key !== route segment. */
const RESOURCE_PATHS = {};

const DEFAULT_STATE = {
	data: {}, // keyed: dashboard | modules | settings | system | other-plugins | changelog
	loading: {},
	saving: {},
	errors: {},
};

const actions = {
	setData( key, value ) {
		return { type: 'SET_DATA', key, value };
	},
	setLoading( key, value ) {
		return { type: 'SET_LOADING', key, value };
	},
	setSaving( key, value ) {
		return { type: 'SET_SAVING', key, value };
	},
	setError( key, error ) {
		return { type: 'SET_ERROR', key, error: error ? error.message : null };
	},

	/**
	 * Fetch a resource and store it under `key`.
	 */
	fetchResource( key, resource ) {
		return async ( { dispatch } ) => {
			dispatch.setLoading( key, true );
			dispatch.setError( key, null );
			const path = resource || RESOURCE_PATHS[ key ] || key;
			try {
				const value = await apiGet( path );
				dispatch.setData( key, value );
				return value;
			} catch ( error ) {
				dispatch.setError( key, error );
				return null;
			} finally {
				dispatch.setLoading( key, false );
			}
		};
	},

	/**
	 * Persist on/off maps and refresh dashboard counts.
	 *
	 * The endpoint writes one tier per request, so a page covering several of
	 * them (Widgets = core + third_party) posts once per tier and refreshes
	 * only after the last one.
	 *
	 * @param {Object} tiers Map of tier key → { slug: 'on'|'off' }.
	 */
	saveModules( tiers ) {
		return async ( { dispatch } ) => {
			dispatch.setSaving( 'modules', true );
			dispatch.setError( 'modules', null );
			try {
				let result = null;
				for ( const tier of Object.keys( tiers ) ) {
					result = await apiPost( 'modules', {
						tier,
						modules: tiers[ tier ],
					} );
				}
				// Refresh both modules and dashboard so counts stay in sync.
				await dispatch.fetchResource( 'modules', 'modules' );
				await dispatch.fetchResource( 'dashboard', 'dashboard' );
				return result;
			} catch ( error ) {
				dispatch.setError( 'modules', error );
				throw error;
			} finally {
				dispatch.setSaving( 'modules', false );
			}
		};
	},

	/**
	 * Persist allow-listed settings and refresh the settings resource.
	 */
	saveSettings( settings ) {
		return async ( { dispatch } ) => {
			dispatch.setSaving( 'settings', true );
			dispatch.setError( 'settings', null );
			try {
				const result = await apiPost( 'settings', { settings } );
				await dispatch.fetchResource( 'settings', 'settings' );
				return result;
			} catch ( error ) {
				dispatch.setError( 'settings', error );
				throw error;
			} finally {
				dispatch.setSaving( 'settings', false );
			}
		};
	},

	installOtherPlugin( slug, activate = true ) {
		return async ( { dispatch } ) => {
			dispatch.setSaving( 'other-plugins', true );
			dispatch.setError( 'other-plugins', null );
			try {
				const result = await apiPost( 'other-plugins/install', {
					slug,
					activate,
				} );
				await dispatch.fetchResource(
					'other-plugins',
					'other-plugins'
				);
				return result;
			} catch ( error ) {
				dispatch.setError( 'other-plugins', error );
				throw error;
			} finally {
				dispatch.setSaving( 'other-plugins', false );
			}
		};
	},

	activateOtherPlugin( slug, pluginFile = '' ) {
		return async ( { dispatch } ) => {
			dispatch.setSaving( 'other-plugins', true );
			dispatch.setError( 'other-plugins', null );
			try {
				const result = await apiPost( 'other-plugins/activate', {
					slug,
					plugin_file: pluginFile,
				} );
				await dispatch.fetchResource(
					'other-plugins',
					'other-plugins'
				);
				return result;
			} catch ( error ) {
				dispatch.setError( 'other-plugins', error );
				throw error;
			} finally {
				dispatch.setSaving( 'other-plugins', false );
			}
		};
	},
};

const selectors = {
	getResource( state, key ) {
		return state.data[ key ] ?? null;
	},
	/**
	 * Same as getResource but with NO auto-fetch resolver attached. Use this
	 * from hooks that dispatch their own fetches — selecting via getResource
	 * would trigger the resolver's duplicate request, which can resolve last
	 * and clobber the richer payload.
	 */
	peekResource( state, key ) {
		return state.data[ key ] ?? null;
	},
	isLoading( state, key ) {
		return !! state.loading[ key ];
	},
	isSaving( state, key ) {
		return !! state.saving[ key ];
	},
	getError( state, key ) {
		return state.errors[ key ] ?? null;
	},
};

const resolvers = {
	getResource( key ) {
		return async ( { dispatch } ) => {
			const path = RESOURCE_PATHS[ key ] || key;
			await dispatch.fetchResource( key, path );
		};
	},
};

function reducer( state = DEFAULT_STATE, action ) {
	switch ( action.type ) {
		case 'SET_DATA':
			return {
				...state,
				data: { ...state.data, [ action.key ]: action.value },
			};
		case 'SET_LOADING':
			return {
				...state,
				loading: { ...state.loading, [ action.key ]: action.value },
			};
		case 'SET_SAVING':
			return {
				...state,
				saving: { ...state.saving, [ action.key ]: action.value },
			};
		case 'SET_ERROR':
			return {
				...state,
				errors: { ...state.errors, [ action.key ]: action.error },
			};
		default:
			return state;
	}
}

let registered = false;

export function registerStore() {
	if ( registered ) {
		return;
	}
	const store = createReduxStore( STORE_NAME, {
		reducer,
		actions,
		selectors,
		resolvers,
	} );
	register( store );
	registered = true;
}
