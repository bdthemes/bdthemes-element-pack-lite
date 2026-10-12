/**
 * ⌘K command palette — grouped commands (navigation + quick actions) with
 * keyboard navigation. Mirrors mockup lines 1556–1594.
 */
import {
	useState,
	useEffect,
	useMemo,
	useRef,
	useCallback,
} from '@wordpress/element';
import { useNavigate } from 'react-router-dom';
import { __, sprintf, _n } from '@wordpress/i18n';
import { useColorScheme } from '../../hooks/useColorScheme';
import { useNavVisibility } from '../../utils/navVisibility';
import { buildCommands, filterCommands } from '../../utils/commands';
import { SearchIcon, ChevronRightIcon } from '../ui/Icons';

function Key( { children } ) {
	return (
		<kbd className="text-[10px] font-extrabold bg-white dark:bg-ink-subtle border border-[#e2e2e6] dark:border-ink-border rounded-[5px] py-[2px] px-[6px] text-[#71717a] dark:text-ink-muted">
			{ children }
		</kbd>
	);
}

export default function CommandPalette( { open, onClose } ) {
	const [ query, setQuery ] = useState( '' );
	const [ selected, setSelected ] = useState( 0 );
	const inputRef = useRef( null );
	const navigate = useNavigate();
	const { toggle: toggleTheme } = useColorScheme();

	// Rebuild when the visibility map is refreshed at runtime.
	const navVisibility = useNavVisibility();

	const groups = useMemo(
		() => buildCommands( { navigate, toggleTheme } ),
		[ navigate, toggleTheme, navVisibility ]
	);
	const visible = useMemo(
		() => filterCommands( groups, query ),
		[ groups, query ]
	);
	const flat = useMemo(
		() => visible.flatMap( ( group ) => group.commands ),
		[ visible ]
	);

	useEffect( () => {
		if ( open ) {
			setQuery( '' );
			setSelected( 0 );
			window.setTimeout( () => inputRef.current?.focus(), 10 );
		}
	}, [ open ] );

	useEffect( () => {
		setSelected( 0 );
	}, [ query ] );

	const runCommand = useCallback(
		( command ) => {
			onClose();
			command.run();
		},
		[ onClose ]
	);

	useEffect( () => {
		if ( ! open ) {
			return undefined;
		}
		const onKey = ( event ) => {
			if ( event.key === 'Escape' ) {
				event.preventDefault();
				onClose();
			} else if ( event.key === 'ArrowDown' ) {
				event.preventDefault();
				setSelected( ( value ) =>
					Math.min( value + 1, flat.length - 1 )
				);
			} else if ( event.key === 'ArrowUp' ) {
				event.preventDefault();
				setSelected( ( value ) => Math.max( value - 1, 0 ) );
			} else if ( event.key === 'Enter' ) {
				event.preventDefault();
				if ( flat[ selected ] ) {
					runCommand( flat[ selected ] );
				}
			}
		};
		document.addEventListener( 'keydown', onKey );
		return () => document.removeEventListener( 'keydown', onKey );
	}, [ open, flat, selected, onClose, runCommand ] );

	if ( ! open ) {
		return null;
	}

	let flatIndex = -1;

	return (
		/* Escape/arrow keys are handled by the document-level listener above. */
		/* eslint-disable-next-line jsx-a11y/no-noninteractive-element-interactions, jsx-a11y/click-events-have-key-events */
		<div
			className="fixed inset-0 z-[80] bg-[rgba(9,9,11,.5)] backdrop-blur flex items-start justify-center pt-[90px] px-6 pb-6"
			onClick={ ( event ) => {
				if ( event.target === event.currentTarget ) {
					onClose();
				}
			} }
			role="presentation"
		>
			<div
				className="w-full max-w-[600px] bg-white dark:bg-ink-card rounded-card overflow-hidden shadow-sheet border border-transparent dark:border-ink-border animate-ep-pop"
				role="dialog"
				aria-modal="true"
				aria-label={ __( 'Command palette', 'bdthemes-element-pack-lite' ) }
			>
				<div className="flex items-center gap-3 py-4 px-5 border-b border-[#f1f1f4] dark:border-ink-border">
					<SearchIcon size={ 19 } className="text-[#a1a1aa]" />
					<input
						ref={ inputRef }
						value={ query }
						onChange={ ( event ) => setQuery( event.target.value ) }
						placeholder={ __(
							'Search pages and actions…',
							'bdthemes-element-pack-lite'
						) }
						className="flex-1 border-none outline-none text-base bg-transparent text-[#18181b] dark:text-ink-text placeholder:text-[#a1a1aa]"
					/>
					<kbd className="text-[11px] font-bold bg-[#f4f4f6] dark:bg-ink-subtle border border-[#e2e2e6] dark:border-ink-border rounded-md py-[3px] px-[7px] text-[#a1a1aa]">
						ESC
					</kbd>
				</div>

				<div className="max-h-[380px] overflow-y-auto p-2">
					{ flat.length === 0 ? (
						<div className="text-center py-9 px-5 text-sm font-semibold text-[#a1a1aa]">
							{ __(
								'No matches — try a different search',
								'bdthemes-element-pack-lite'
							) }
						</div>
					) : (
						visible.map( ( group ) => (
							<div key={ group.label }>
								<div className="text-[10.5px] font-extrabold tracking-[.1em] uppercase text-[#a1a1aa] pt-3 px-[13px] pb-[5px]">
									{ group.label }
								</div>
								{ group.commands.map( ( command ) => {
									flatIndex += 1;
									const index = flatIndex;
									const isSelected = index === selected;
									const CommandIcon =
										command.icon || ChevronRightIcon;
									return (
										<button
											type="button"
											key={ command.id }
											onClick={ () =>
												runCommand( command )
											}
											onMouseEnter={ () =>
												setSelected( index )
											}
											tabIndex={ -1 }
											className={ `w-full text-left flex items-center gap-[13px] py-[10px] px-3 rounded-tile cursor-pointer border transition-colors ${
												isSelected
													? 'border-[#dbeafe] bg-[#eff6ff] dark:border-[#1e3a5f] dark:bg-[#15233a]'
													: 'border-transparent bg-transparent'
											}` }
										>
											<span
												className={ `w-[34px] h-[34px] rounded-[9px] flex-none grid place-items-center transition-colors ${
													isSelected
														? 'bg-gradient-brand text-white'
														: 'bg-[#f4f4f6] dark:bg-ink-subtle text-[#71717a] dark:text-ink-muted'
												}` }
											>
												<CommandIcon size={ 16 } />
											</span>
											<div className="flex-1 min-w-0">
												<div className="text-sm font-semibold text-[#27272a] dark:text-ink-text whitespace-nowrap overflow-hidden text-ellipsis">
													{ command.label }
												</div>
											</div>
											<span className="text-[11px] font-bold text-[#a1a1aa] bg-[#f4f4f6] dark:bg-ink-subtle py-[3px] px-[9px] rounded-full">
												{ command.hint }
											</span>
										</button>
									);
								} ) }
							</div>
						) )
					) }
				</div>

				<div className="flex items-center gap-4 py-[10px] px-[18px] border-t border-[#f1f1f4] dark:border-ink-border bg-[#fafafb] dark:bg-ink-subtle">
					<span className="flex items-center gap-[6px] text-[11px] font-semibold text-[#a1a1aa]">
						<Key>↑↓</Key>
						{ __( 'navigate', 'bdthemes-element-pack-lite' ) }
					</span>
					<span className="flex items-center gap-[6px] text-[11px] font-semibold text-[#a1a1aa]">
						<Key>↵</Key>
						{ __( 'run', 'bdthemes-element-pack-lite' ) }
					</span>
					<span className="flex items-center gap-[6px] text-[11px] font-semibold text-[#a1a1aa]">
						<Key>esc</Key>
						{ __( 'close', 'bdthemes-element-pack-lite' ) }
					</span>
					<span className="ml-auto text-[11px] font-semibold text-[#c4c4cc]">
						{ sprintf(
							/* translators: %d: number of commands */
							_n(
								'%d command',
								'%d commands',
								flat.length,
								'bdthemes-element-pack-lite'
							),
							flat.length
						) }
					</span>
				</div>
			</div>
		</div>
	);
}
