/**
 * Link-styled buttons for the Go Pro page. The dashboard's Button primitive
 * renders a <button>; these are real anchors (they leave wp-admin) with the
 * same look.
 */
import { __ } from '@wordpress/i18n';
import config from '../../utils/config';
import { ExternalIcon, RocketIcon } from '../ui/Icons';

const BASE =
	'inline-flex items-center justify-center gap-[7px] rounded-full cursor-pointer no-underline transition-all duration-200';

export function UpgradeLink( { size = 'md', children, className = '' } ) {
	const sizing =
		size === 'lg'
			? 'py-[13px] px-[26px] text-[14px]'
			: 'py-[10px] px-[18px] text-[13px]';

	return (
		<a
			href={ config.upgradeUrl }
			target="_blank"
			rel="noreferrer"
			className={ `${ BASE } ${ sizing } bg-gradient-brand text-white font-extrabold shadow-glow hover:-translate-y-px hover:shadow-glow-lg ${ className }` }
		>
			<RocketIcon size={ size === 'lg' ? 17 : 15 } />
			{ children || __( 'Upgrade to Pro', 'bdthemes-element-pack-lite' ) }
		</a>
	);
}

export function OutlineLink( { href, children, className = '' } ) {
	return (
		<a
			href={ href }
			target="_blank"
			rel="noreferrer"
			className={ `${ BASE } py-[10px] px-[18px] text-[13px] bg-white dark:bg-ink-card border border-[#e2e2e6] dark:border-[#2a313c] text-[#3f3f46] dark:text-[#cdd0d6] font-bold hover:border-brand hover:text-brand ${ className }` }
		>
			{ children }
			<ExternalIcon size={ 13 } />
		</a>
	);
}
