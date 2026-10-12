/**
 * System status summary card — mockup Variant A (index.html 483–491). First
 * few checks from the real system report as dot rows + link to the full page.
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { Link } from 'react-router-dom';
import { STORE_NAME } from '../../store';
import StatusDotRow from '../ui/StatusDotRow';

export default function SystemStatusCard() {
	const data = useSelect(
		( select ) => select( STORE_NAME ).getResource( 'system' ),
		[]
	);

	const checks = Array.isArray( data?.checks ) ? data.checks.slice( 0, 4 ) : [];

	if ( ! checks.length ) {
		return null;
	}

	return (
		<div className="bg-white dark:bg-ink-card border border-[#e9e9ed] dark:border-ink-border rounded-card p-5 shadow-resting">
			<div className="flex items-center justify-between mb-[14px]">
				<div className="text-[17px] font-extrabold text-[#0f1115] dark:text-ink-text">
					{ __( 'System status', 'bdthemes-element-pack-lite' ) }
				</div>
				<Link
					to="/system-status"
					className="text-[12px] font-bold text-brand"
				>
					{ __( 'View full report', 'bdthemes-element-pack-lite' ) }
				</Link>
			</div>
			{ checks.map( ( row ) => (
				<StatusDotRow
					key={ row.id }
					label={ row.label }
					value={
						row.value ||
						( row.pass
							? __( 'OK', 'bdthemes-element-pack-lite' )
							: __( 'Check', 'bdthemes-element-pack-lite' ) )
					}
					tone={ row.pass ? 'good' : 'bad' }
				/>
			) ) }
		</div>
	);
}
