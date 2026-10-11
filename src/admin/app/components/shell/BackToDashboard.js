/**
 * Breadcrumb-style back link used by standalone tool pages (Theme Builder,
 * Custom Fonts, Custom Icons) that render outside the dashboard chrome.
 */
import { __ } from '@wordpress/i18n';
import { useNavigate } from 'react-router-dom';
import { ArrowRightIcon } from '../ui/Icons';

export default function BackToDashboard() {
	const navigate = useNavigate();

	return (
		<button
			type="button"
			onClick={ () => navigate( '/' ) }
			className="inline-flex items-center gap-[6px] bg-transparent border-0 p-0 mb-3 text-[12.5px] font-bold text-[#71717a] dark:text-ink-muted cursor-pointer hover:text-brand"
		>
			<span className="rotate-180 grid place-items-center">
				<ArrowRightIcon size={ 13 } />
			</span>
			{ __( 'Back to Dashboard', 'bdthemes-element-pack-lite' ) }
		</button>
	);
}
