/**
 * Error state with optional retry.
 */
import { __ } from '@wordpress/i18n';
import Alert from './ui/Alert';
import Button from './ui/Button';

export default function ErrorState( { error, onRetry } ) {
	const message =
		( error && error.message ) ||
		__(
			'Something went wrong while loading this data.',
			'bdthemes-element-pack-lite'
		);

	return (
		<div className="flex flex-col items-start gap-3">
			<Alert tone="error" className="w-full">
				{ message }
			</Alert>
			{ onRetry ? (
				<Button variant="outline" onClick={ onRetry }>
					{ __( 'Try again', 'bdthemes-element-pack-lite' ) }
				</Button>
			) : null }
		</div>
	);
}
