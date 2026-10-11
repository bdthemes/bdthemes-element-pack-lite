/**
 * Upgrade-to-Pro call-to-action. Hidden when the license is already active.
 * Styled after the mockup's AI recommendation banner (gradient tile + CTA).
 */
import { __ } from '@wordpress/i18n';
import config from '../utils/config';
import { ZapIcon } from './ui/Icons';

export default function UpgradeNotice( { isPro } ) {
	if ( isPro ) {
		return null;
	}

	return (
		<div className="flex items-center gap-[14px] mb-4 bg-[linear-gradient(135deg,#eef1ff,#e9fbef)] dark:bg-[linear-gradient(135deg,#1b1e3a,#0f2318)] border border-[#d9def9] dark:border-ink-border rounded-panel py-[15px] px-[18px] flex-wrap">
			<span className="w-[38px] h-[38px] rounded-tile flex-none grid place-items-center bg-gradient-brand text-white shadow-[0_5px_14px_rgba(64,142,250,.4)]">
				<ZapIcon size={ 20 } />
			</span>
			<div className="flex-1 min-w-[200px]">
				<div className="text-sm font-extrabold text-[#312e81] dark:text-ink-text tracking-[-.01em]">
					{ __( 'Unlock all Pro widgets', 'bdthemes-element-pack-lite' ) }
				</div>
				<div className="text-[13px] text-[#4b4b63] dark:text-ink-muted mt-[2px] leading-normal">
					{ __(
						'Get 230+ premium widgets, extensions and priority support.',
						'bdthemes-element-pack-lite'
					) }
				</div>
			</div>
			<a
				href={ config.upgradeUrl }
				target="_blank"
				rel="noreferrer"
				className="flex items-center gap-[7px] bg-gradient-brand text-white py-[10px] px-[18px] rounded-full text-[13px] font-extrabold cursor-pointer shadow-glow flex-none transition-transform hover:-translate-y-px"
			>
				{ __( 'Upgrade to Pro', 'bdthemes-element-pack-lite' ) }
			</a>
		</div>
	);
}
