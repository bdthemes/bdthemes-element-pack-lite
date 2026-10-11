/**
 * Renders API / sub-option fields as Tailwind FieldRows inside a SectionCard.
 * Text fields support an optional `secret` reveal toggle (API keys).
 */
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import FieldRow from '../ui/FieldRow';
import {
	ChevronDownIcon,
	ExternalIcon,
	EyeIcon,
	EyeOffIcon,
} from '../ui/Icons';

export const INPUT_CLASS =
	'w-full py-[10px] px-[14px] border border-[#e2e2e6] dark:border-[#2a313c] rounded-btn text-sm bg-white dark:bg-ink-card text-[#18181b] dark:text-ink-text outline-none placeholder:text-[#a1a1aa] focus:border-brand focus:shadow-[0_0_0_3px_rgba(64,142,250,.15)]';

export const SELECT_CLASS = `${ INPUT_CLASS } max-w-full appearance-none pr-9 font-bold text-[13.5px] bg-[#f7f8fa] dark:bg-ink-subtle cursor-pointer`;

export const TEXTAREA_CLASS = `${ INPUT_CLASS } font-mono text-[13px] leading-relaxed min-h-[140px]`;

function fieldDescription( field ) {
	if ( field.desc ) {
		return <span dangerouslySetInnerHTML={ { __html: field.desc } } />;
	}
	return field.tooltip || null;
}

export default function SettingsField( { field, value, onChange, secret } ) {
	const [ revealed, setRevealed ] = useState( false );

	if (
		field.type === 'start_group' ||
		field.type === 'end_group' ||
		field.type === 'separator' ||
		field.type === 'checkbox'
	) {
		return null;
	}

	if ( field.type === 'html' ) {
		return (
			<FieldRow label={ field.label } description={ field.html } stacked>
				<a
					href={ field.legacy_url || '#' }
					className="inline-flex items-center gap-[5px] text-[13px] font-bold text-brand hover:underline"
				>
					{ __(
						'Open classic API settings',
						'bdthemes-element-pack-lite'
					) }
					<ExternalIcon size={ 12 } />
				</a>
			</FieldRow>
		);
	}

	if ( field.type === 'select' && field.options ) {
		const options = Object.entries( field.options );

		return (
			<FieldRow
				label={ field.label }
				description={ fieldDescription( field ) }
			>
				<div className="relative w-[220px] max-w-full">
					<select
						value={ value ?? field.default ?? '' }
						onChange={ ( event ) =>
							onChange( field.name, event.target.value )
						}
						className={ SELECT_CLASS }
					>
						{ options.map( ( [ optValue, label ] ) => (
							<option key={ optValue } value={ optValue }>
								{ label }
							</option>
						) ) }
					</select>
					<span className="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[#a1a1aa]">
						<ChevronDownIcon size={ 14 } />
					</span>
				</div>
			</FieldRow>
		);
	}

	if ( field.type === 'textarea' ) {
		return (
			<FieldRow
				label={ field.label }
				description={ fieldDescription( field ) }
				stacked
			>
				<textarea
					value={ value ?? '' }
					placeholder={ field.placeholder }
					onChange={ ( event ) =>
						onChange( field.name, event.target.value )
					}
					className={ TEXTAREA_CLASS }
				/>
			</FieldRow>
		);
	}

	if ( field.type === 'number' ) {
		return (
			<FieldRow
				label={ field.label }
				description={ fieldDescription( field ) }
			>
				<input
					type="number"
					value={ value ?? field.default ?? '' }
					onChange={ ( event ) =>
						onChange( field.name, event.target.value )
					}
					className={ `${ INPUT_CLASS } !w-[220px] max-w-full` }
				/>
			</FieldRow>
		);
	}

	return (
		<FieldRow
			label={ field.label }
			description={ fieldDescription( field ) }
			stacked
		>
			<div className="relative w-full">
				<input
					type={ secret && ! revealed ? 'password' : 'text' }
					value={ value ?? '' }
					placeholder={ field.placeholder }
					autoComplete={ secret ? 'off' : undefined }
					onChange={ ( event ) =>
						onChange( field.name, event.target.value )
					}
					className={ `${ INPUT_CLASS }${ secret ? ' pr-11' : '' }` }
				/>
				{ secret ? (
					<button
						type="button"
						onClick={ () => setRevealed( ! revealed ) }
						aria-label={
							revealed
								? __( 'Hide value', 'bdthemes-element-pack-lite' )
								: __( 'Show value', 'bdthemes-element-pack-lite' )
						}
						className="absolute right-[12px] top-1/2 -translate-y-1/2 text-[#a1a1aa] cursor-pointer hover:text-[#52525b] dark:hover:text-ink-text"
					>
						{ revealed ? (
							<EyeOffIcon size={ 16 } />
						) : (
							<EyeIcon size={ 16 } />
						) }
					</button>
				) : null }
			</div>
		</FieldRow>
	);
}
