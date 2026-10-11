/**
 * Closing call to action shown under every tab of the Go Pro page.
 */
import { __ } from '@wordpress/i18n';
import config from '../../utils/config';
import { UpgradeLink, OutlineLink } from './ProLinks';

export default function ProCta() {
	return (
		<div className="flex items-center justify-between gap-5 flex-wrap rounded-card py-6 px-7 max-[640px]:px-5 bg-[linear-gradient(135deg,#0f1115_0%,#1b2540_100%)] border border-[#262c36]">
			<div className="min-w-[220px] flex-1">
				<div className="text-[19px] font-extrabold tracking-[-.015em] text-white">
					{ __( 'Ready to unlock the rest?', 'bdthemes-element-pack-lite' ) }
				</div>
				<div className="text-[13.5px] leading-[1.55] text-[#a8b0c0] mt-[4px]">
					{ __(
						'Upgrade to Element Pack Pro and keep building in Elementor with nothing locked.',
						'bdthemes-element-pack-lite'
					) }
				</div>
			</div>
			<div className="flex items-center gap-3 flex-wrap">
				<UpgradeLink size="lg" />
				<OutlineLink href={ config.docsUrl }>
					{ __( 'Documentation', 'bdthemes-element-pack-lite' ) }
				</OutlineLink>
			</div>
		</div>
	);
}
