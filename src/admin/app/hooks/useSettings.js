/**
 * Shared settings state: fetch payload, hold a draft for one or more option
 * keys, detect dirty state, save and reset.
 */
import { useState, useEffect, useMemo, useCallback } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import { STORE_NAME } from '../store';

export function useSettings() {
	const { fetchResource, saveSettings } = useDispatch( STORE_NAME );

	const { data, isLoading, isSaving, error } = useSelect( ( select ) => {
		const store = select( STORE_NAME );
		return {
			data: store.getResource( 'settings' ),
			isLoading: store.isLoading( 'settings' ),
			isSaving: store.isSaving( 'settings' ),
			error: store.getError( 'settings' ),
		};
	}, [] );

	useSelect(
		( select ) => select( STORE_NAME ).getResource( 'settings' ),
		[]
	);

	const reload = useCallback(
		() => fetchResource( 'settings', 'settings' ),
		[ fetchResource ]
	);

	return {
		data,
		isLoading,
		isSaving,
		error,
		reload,
		saveSettings,
	};
}

/**
 * Draft editor for a single option key (array map) plus optional scalar keys.
 *
 * @param {object|null} data Settings GET payload.
 * @param {string} optionKey Option name, e.g. element_pack_other_settings.
 * @param {string[]} [scalarKeys] Additional scalar keys bundled in the same save.
 */
export function useSettingsDraft( data, optionKey, scalarKeys = [] ) {
	const { saveSettings, isSaving, error, reload } = useSettings();
	const scalarKeyList = useMemo(
		() => scalarKeys,
		[ scalarKeys.join( '|' ) ]
	);
	// Local edits only; rendered values are server state + overrides so the
	// first render with data already shows real values (no off→on flash).
	const [ overrides, setOverrides ] = useState( {} );
	const [ scalarOverrides, setScalarOverrides ] = useState( {} );

	// New server data invalidates pending edits (e.g. after a save/reload).
	useEffect( () => {
		setOverrides( {} );
		setScalarOverrides( {} );
	}, [ data ] );

	const serverValues = data && data.values ? data.values : {};
	const serverOption = serverValues[ optionKey ] || {};

	const draft = useMemo( () => {
		const base = isArrayMap( serverOption ) ? { ...serverOption } : {};
		return { ...base, ...overrides };
	}, [ serverOption, overrides ] );

	const scalars = useMemo( () => {
		const base = {};
		scalarKeyList.forEach( ( key ) => {
			base[ key ] =
				serverValues[ key ] ??
				( key === 'ep_excluded_pages' ? [] : '' );
		} );
		return { ...base, ...scalarOverrides };
	}, [ serverValues, scalarKeyList, scalarOverrides ] );

	const isDirty = useMemo( () => {
		if ( ! data ) {
			return false;
		}

		const optionDirty = Object.keys( draft ).some(
			( key ) =>
				normalizeValue( draft[ key ] ) !==
				normalizeValue( serverOption[ key ] )
		);

		if ( optionDirty ) {
			return true;
		}

		return scalarKeyList.some( ( key ) => {
			const a = scalars[ key ];
			const b = serverValues[ key ];
			if ( key === 'ep_excluded_pages' ) {
				return ! arraysEqual( a || [], b || [] );
			}
			return ( a ?? '' ) !== ( b ?? '' );
		} );
	}, [ draft, scalars, data, serverOption, serverValues, scalarKeyList ] );

	const setField = useCallback( ( name, value ) => {
		setOverrides( ( current ) => ( { ...current, [ name ]: value } ) );
	}, [] );

	const setScalar = useCallback( ( name, value ) => {
		setScalarOverrides( ( current ) => ( {
			...current,
			[ name ]: value,
		} ) );
	}, [] );

	const reset = useCallback( () => {
		setOverrides( {} );
		setScalarOverrides( {} );
	}, [] );

	const isFieldDirty = useCallback(
		( name ) =>
			normalizeValue( draft[ name ] ) !==
			normalizeValue( serverOption[ name ] ),
		[ draft, serverOption ]
	);

	const save = useCallback( async () => {
		const payload = {
			[ optionKey ]: draft,
		};
		scalarKeyList.forEach( ( key ) => {
			payload[ key ] = scalars[ key ];
		} );
		return saveSettings( payload );
	}, [ draft, scalars, optionKey, scalarKeyList, saveSettings ] );

	return {
		draft,
		scalars,
		setField,
		setScalar,
		isDirty,
		isFieldDirty,
		save,
		reset,
		isSaving,
		error,
		reload,
	};
}

function isArrayMap( value ) {
	return value && typeof value === 'object' && ! Array.isArray( value );
}

function normalizeValue( value ) {
	if ( value === true || value === 1 || value === '1' || value === 'on' ) {
		return 'on';
	}
	if (
		value === false ||
		value === 0 ||
		value === '0' ||
		value === 'off' ||
		value === null ||
		value === undefined
	) {
		return 'off';
	}
	return String( value );
}

function arraysEqual( a, b ) {
	if ( a.length !== b.length ) {
		return false;
	}
	const sortedA = [ ...a ].map( Number ).sort();
	const sortedB = [ ...b ].map( Number ).sort();
	return sortedA.every( ( val, i ) => val === sortedB[ i ] );
}
