/**
 * "New in {version}" promo card — mockup Variant A right column
 * (index.html 470–481): dark gradient, mint radial glow, uppercase eyebrow,
 * ghost pill CTA to the real changelog.
 */
import { __, sprintf } from '@wordpress/i18n';
import config from '../../utils/config';
import { SparklesIcon } from '../ui/Icons';

export default function WhatsNewPromoCard() {
	if ( ! config.whatsNewUrl ) {
		return null;
	}

	return (
		<div className="rounded-card p-5 relative overflow-hidden text-white [background:linear-gradient(135deg,#0b0f1a,#172554)]">
			<div className="absolute inset-0 pointer-events-none [background:radial-gradient(circle_at_85%_0%,rgba(132,237,146,.22),transparent_55%)]" />
			<div className="relative">
				<div className="flex items-center gap-2 text-[11px] font-bold tracking-[.12em] uppercase text-brand-mint">
					<SparklesIcon size={ 14 } />
					{ sprintf(
						/* translators: %s: plugin version */
						__( 'New in %s', 'bdthemes-element-pack-lite' ),
						config.version
					) }
				</div>
				<div className="text-[17px] font-bold mt-[10px] tracking-[-.01em]">
					{ __(
						'Fresh widgets & fixes in the latest release',
						'bdthemes-element-pack-lite'
					) }
				</div>
				<p className="text-[12.5px] text-white/70 mt-2 mb-4 leading-[1.55]">
					{ __(
						'See everything added, fixed and improved in this update.',
						'bdthemes-element-pack-lite'
					) }
				</p>
				<a
					href={ config.whatsNewUrl }
					target="_blank"
					rel="noreferrer"
					className="inline-block bg-white/[.12] border border-white/[.18] text-white py-2 px-[15px] rounded-full text-[12.5px] font-semibold cursor-pointer backdrop-blur transition-colors hover:bg-white/20"
				>
					{ __( 'View changelog', 'bdthemes-element-pack-lite' ) }
				</a>
			</div>
		</div>
	);
}
