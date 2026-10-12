/**
 * Encapsulates everything a module page (Widgets / Extensions) needs: fetching
 * the modules payload, deriving the requested tier(s), holding a local draft of
 * on/off state, toggling, bulk set, dirty detection and saving.
 *
 * `tier` takes a single key or a list of them — the Widgets page passes
 * [ 'core', 'third_party' ] so both catalogs share one screen. Merged rows
 * carry their own `tier`, so a save still writes each option separately.
 *
 * Keeps page components thin and identical across tiers.
 */
import { useState, useEffect, useMemo, useCallback } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import { STORE_NAME } from '../store';
import config from '../utils/config';

export function useModuleTier( tier ) {
	const { fetchResource, saveModules } = useDispatch( STORE_NAME );

	// String key so the memo below isn't invalidated by a fresh array literal
	// on every render.
	const tierKey = Array.isArray( tier ) ? tier.join( ',' ) : String( tier );
	const tiers = useMemo( () => tierKey.split( ',' ), [ tierKey ] );

	const { data, isLoading, isSaving, error } = useSelect( ( select ) => {
		const store = select( STORE_NAME );
		return {
			data: store.getResource( 'modules' ),
			isLoading: store.isLoading( 'modules' ),
			isSaving: store.isSaving( 'modules' ),
			error: store.getError( 'modules' ),
		};
	}, [] );

	// Resolver-backed initial fetch.
	useSelect(
		( select ) => select( STORE_NAME ).getResource( 'modules' ),
		[]
	);

	// Only tiers the payload actually carries — a visibility filter can
	// withhold one, and the page then simply shows the other.
	const tierData = useMemo( () => {
		if ( ! data || ! data.tiers ) {
			return null;
		}
		const present = tiers.filter( ( key ) => data.tiers[ key ] );
		if ( ! present.length ) {
			return null;
		}
		return {
			modules: present.reduce(
				( rows, key ) =>
					rows.concat(
						data.tiers[ key ].modules.map( ( m ) => ( {
							...m,
							tier: key,
						} ) )
					),
				[]
			),
		};
	}, [ data, tiers ] );
	const isPro = data ? !! data.is_pro : config.isPro;
	const trackingAllowed = data ? !! data.tracking_allowed : false;

	// Server truth: { slug: boolean }, derived synchronously so the first
	// render with data already shows the real on/off state (no off→on flash).
	const serverMap = useMemo( () => {
		const map = {};
		if ( tierData ) {
			tierData.modules.forEach( ( m ) => {
				map[ m.name ] = m.enabled;
			} );
		}
		return map;
	}, [ tierData ] );

	// Local edits only; the rendered draft is serverMap + overrides.
	const [ overrides, setOverrides ] = useState( {} );

	// New server data invalidates pending edits (e.g. after a save/reload).
	useEffect( () => {
		setOverrides( {} );
	}, [ serverMap ] );

	const draft = useMemo(
		() => ( { ...serverMap, ...overrides } ),
		[ serverMap, overrides ]
	);

	const toggle = useCallback(
		( name ) => {
			setOverrides( ( current ) => ( {
				...current,
				[ name ]: ! ( name in current
					? current[ name ]
					: serverMap[ name ] ),
			} ) );
		},
		[ serverMap ]
	);

	const setAll = useCallback( ( value, modules ) => {
		setOverrides( ( current ) => {
			const next = { ...current };
			modules.forEach( ( m ) => {
				if ( ! m.is_locked ) {
					next[ m.name ] = value;
				}
			} );
			return next;
		} );
	}, [] );

	const isDirty = useMemo( () => {
		if ( ! tierData ) {
			return false;
		}
		return tierData.modules.some( ( m ) => draft[ m.name ] !== m.enabled );
	}, [ draft, tierData ] );

	const activeCount = useMemo(
		() => Object.values( draft ).filter( Boolean ).length,
		[ draft ]
	);

	const save = useCallback( () => {
		if ( ! tierData ) {
			return Promise.resolve();
		}
		// Each row goes back to the option of the tier it came from.
		const byTier = {};
		tierData.modules.forEach( ( m ) => {
			if ( ! byTier[ m.tier ] ) {
				byTier[ m.tier ] = {};
			}
			byTier[ m.tier ][ m.name ] = draft[ m.name ] ? 'on' : 'off';
		} );
		return saveModules( byTier );
	}, [ draft, tierData, saveModules ] );

	const reset = useCallback( () => {
		setOverrides( {} );
	}, [] );

	const reload = useCallback(
		() => fetchResource( 'modules', 'modules' ),
		[ fetchResource ]
	);

	return {
		tierData,
		isPro,
		trackingAllowed,
		draft,
		toggle,
		setAll,
		isDirty,
		activeCount,
		save,
		reset,
		reload,
		isLoading,
		isSaving,
		error,
	};
}
