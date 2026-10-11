/**
 * Short, honest answers to the questions people have before upgrading. Taken
 * from the plugin's own FAQ so the two never disagree.
 */
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import config from '../../utils/config';
import { ChevronDownIcon } from '../ui/Icons';

function Item( { question, answer, open, onToggle } ) {
	return (
		<div className="border-t border-[#f1f1f4] dark:border-ink-border first:border-t-0">
			<button
				type="button"
				onClick={ onToggle }
				aria-expanded={ open }
				className="w-full flex items-center justify-between gap-4 text-left bg-transparent border-0 py-[16px] px-5 cursor-pointer"
			>
				<span className="text-[14px] font-bold text-[#18181b] dark:text-ink-text">
					{ question }
				</span>
				<span
					className={ `flex-none text-[#a1a1aa] transition-transform duration-200 ${
						open ? 'rotate-180' : ''
					}` }
				>
					<ChevronDownIcon size={ 16 } />
				</span>
			</button>
			{ open ? (
				<div className="px-5 pb-[18px] -mt-1 text-[13.5px] leading-[1.65] text-[#52525b] dark:text-ink-muted">
					{ answer }
				</div>
			) : null }
		</div>
	);
}

export default function ProFaq() {
	const [ openIndex, setOpenIndex ] = useState( 0 );

	const items = [
		{
			question: __(
				'What happens to my settings when I upgrade?',
				'bdthemes-element-pack-lite'
			),
			answer: __(
				'You get an update, that’s all. No user settings are affected by upgrading, and you unlock the additional Pro widgets, extensions and features.',
				'bdthemes-element-pack-lite'
			),
		},
		{
			question: __(
				'What is Elementor MCP & AI Abilities?',
				'bdthemes-element-pack-lite'
			),
			answer: __(
				'Element Pack Pro includes an MCP server and a set of AI abilities, so compatible tools such as Claude, ChatGPT, Cursor, VS Code or Angie can build and manage Elementor content on your site. You choose what is exposed with per-ability switches and a read-only mode. Support depends on your Elementor version and the integrations you connect.',
				'bdthemes-element-pack-lite'
			),
		},
		{
			question: __(
				'Does Element Pack work with Elementor Pro?',
				'bdthemes-element-pack-lite'
			),
			answer: __(
				'Yes. Element Pack works with Elementor Pro, and it needs Elementor itself to run.',
				'bdthemes-element-pack-lite'
			),
		},
		{
			question: __(
				'Will it work with my theme?',
				'bdthemes-element-pack-lite'
			),
			answer: __(
				'Element Pack supports any theme that supports Elementor.',
				'bdthemes-element-pack-lite'
			),
		},
		{
			question: __(
				'Will an update break my existing layouts?',
				'bdthemes-element-pack-lite'
			),
			answer: __(
				'No. Updates are built not to change or break layouts you have already made.',
				'bdthemes-element-pack-lite'
			),
		},
		{
			question: __(
				'Where can I get help before I buy?',
				'bdthemes-element-pack-lite'
			),
			answer: (
				<>
					{ __(
						'Browse the live demos on each item, read the documentation, or ask the team directly:',
						'bdthemes-element-pack-lite'
					) }{ ' ' }
					<a
						href={ config.supportUrl }
						target="_blank"
						rel="noreferrer"
						className="font-bold text-brand no-underline hover:underline"
					>
						{ __( 'contact support', 'bdthemes-element-pack-lite' ) }
					</a>
					.
				</>
			),
		},
	];

	return (
		<div className="bg-white dark:bg-ink-card border border-[#e9e9ed] dark:border-ink-border rounded-card overflow-hidden">
			{ items.map( ( item, index ) => (
				<Item
					key={ item.question }
					question={ item.question }
					answer={ item.answer }
					open={ openIndex === index }
					onToggle={ () =>
						setOpenIndex( ( value ) => ( value === index ? -1 : index ) )
					}
				/>
			) ) }
		</div>
	);
}
