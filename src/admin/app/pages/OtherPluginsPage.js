/**
 * BDThemes companion plugins catalog (legacy Other Plugins tab).
 */
import { useState } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import { __, sprintf } from '@wordpress/i18n';
import { decodeEntities } from '@wordpress/html-entities';
import { STORE_NAME } from '../store';
import PageHeader from '../components/shell/PageHeader';
import Spinner from '../components/Spinner';
import ErrorState from '../components/ErrorState';
import EmptyState from '../components/EmptyState';
import Card from '../components/ui/Card';
import Button from '../components/ui/Button';
import Badge from '../components/ui/Badge';
import Alert from '../components/ui/Alert';
import { useToast } from '../components/ui/Toast';
import { PuzzleIcon, ExternalIcon } from '../components/ui/Icons';
import { accentFor } from '../utils/accents';

/**
 * Plugin icon with graceful degradation: the service may point at the
 * predictable wp.org CDN path, which 404s for plugins whose icon uses a
 * different extension — fall back to the tinted puzzle tile on load error.
 */
function PluginIcon( { icon, slug } ) {
	const [ failed, setFailed ] = useState( false );

	if ( icon && ! failed ) {
		return (
			<img
				src={ icon }
				alt=""
				width="48"
				height="48"
				onError={ () => setFailed( true ) }
				className="w-12 h-12 rounded-tile flex-none object-cover"
			/>
		);
	}

	return (
		<span
			className={ `w-12 h-12 rounded-tile flex-none grid place-items-center ${ accentFor(
				slug
			) }` }
			aria-hidden="true"
		>
			<PuzzleIcon size={ 22 } />
		</span>
	);
}

function statusTone( status ) {
	switch ( status ) {
		case 'active':
			return 'success';
		case 'installed':
			return 'info';
		case 'premium':
			return 'warning';
		default:
			return 'neutral';
	}
}

function statusLabel( status ) {
	switch ( status ) {
		case 'active':
			return __( 'Active', 'bdthemes-element-pack-lite' );
		case 'installed':
			return __( 'Installed', 'bdthemes-element-pack-lite' );
		// Sold only as a premium plugin — nothing to install from wp.org, so
		// the card links to the product page instead.
		case 'premium':
			return __( 'Premium', 'bdthemes-element-pack-lite' );
		default:
			return __( 'Not installed', 'bdthemes-element-pack-lite' );
	}
}

export default function OtherPluginsPage() {
	const { fetchResource, installOtherPlugin, activateOtherPlugin } =
		useDispatch( STORE_NAME );
	const showToast = useToast();
	const [ busySlug, setBusySlug ] = useState( '' );
	const [ busyAction, setBusyAction ] = useState( '' );

	const { data, isLoading, error } = useSelect( ( select ) => {
		const store = select( STORE_NAME );
		return {
			data: store.getResource( 'other-plugins' ),
			isLoading: store.isLoading( 'other-plugins' ),
			error: store.getError( 'other-plugins' ),
		};
	}, [] );

	useSelect(
		( select ) => select( STORE_NAME ).getResource( 'other-plugins' ),
		[]
	);

	const reload = () => fetchResource( 'other-plugins', 'other-plugins' );

	if ( isLoading && ! data ) {
		return (
			<div>
				<PageHeader
					title={ __( 'Other Plugins', 'bdthemes-element-pack-lite' ) }
				/>
				<Spinner
					full
					label={ __( 'Loading plugins…', 'bdthemes-element-pack-lite' ) }
				/>
			</div>
		);
	}

	if ( error && ! data ) {
		return (
			<div>
				<PageHeader
					title={ __( 'Other Plugins', 'bdthemes-element-pack-lite' ) }
				/>
				<ErrorState error={ error } onRetry={ reload } />
			</div>
		);
	}

	const plugins = data && data.plugins ? data.plugins : [];

	const onInstall = async ( slug ) => {
		setBusySlug( slug );
		setBusyAction( 'install' );
		try {
			const result = await installOtherPlugin( slug, true );
			showToast(
				result.message ||
					__( 'Plugin installed.', 'bdthemes-element-pack-lite' )
			);
			await reload();
		} catch ( e ) {
			// Error via store.
		} finally {
			setBusySlug( '' );
			setBusyAction( '' );
		}
	};

	const onActivate = async ( plugin ) => {
		setBusySlug( plugin.slug );
		setBusyAction( 'activate' );
		try {
			const result = await activateOtherPlugin(
				plugin.slug,
				plugin.plugin_file || ''
			);
			showToast(
				result.message ||
					__( 'Plugin activated.', 'bdthemes-element-pack-lite' )
			);
			await reload();
		} catch ( e ) {
			// Error via store.
		} finally {
			setBusySlug( '' );
			setBusyAction( '' );
		}
	};

	return (
		<div>
			<PageHeader
				title={ __( 'Other Plugins', 'bdthemes-element-pack-lite' ) }
				subtitle={ __(
					'Install companion plugins from BDThemes to extend your stack.',
					'bdthemes-element-pack-lite'
				) }
			/>

			<div className="flex flex-col gap-4">
				{ data && data.loading && (
					<Alert tone="info">
						{ data.message ||
							__(
								'Fetching latest plugin data…',
								'bdthemes-element-pack-lite'
							) }
					</Alert>
				) }

				{ error && (
					<Alert tone="error" onDismiss={ reload }>
						{ error }
					</Alert>
				) }

				{ ! data?.loading && plugins.length === 0 ? (
					<EmptyState
						title={ __(
							'No plugins available',
							'bdthemes-element-pack-lite'
						) }
						description={ __(
							'Try again in a moment.',
							'bdthemes-element-pack-lite'
						) }
						action={
							<Button variant="outline" onClick={ reload }>
								{ __( 'Retry', 'bdthemes-element-pack-lite' ) }
							</Button>
						}
					/>
				) : (
					<div className="grid grid-cols-3 max-[1280px]:grid-cols-2 max-[760px]:grid-cols-1 gap-3">
						{ plugins.map( ( plugin ) => (
							<Card
								key={ plugin.slug }
								className="group p-5 flex flex-col gap-3 transition-all duration-200 hover:border-[#c7dbfb] dark:hover:border-[#2f4368] hover:shadow-[0_10px_24px_-14px_rgba(64,142,250,.35)] hover:-translate-y-[2px]"
							>
								<div className="flex items-center gap-3">
									<PluginIcon
										icon={ plugin.icon }
										slug={ plugin.slug }
									/>
									<div className="min-w-0">
										<h3 className="m-0 text-[15px] font-extrabold text-[#0f1115] dark:text-ink-text tracking-[-.01em] truncate transition-colors group-hover:text-brand">
											{ plugin.homepage ? (
												<a
													href={ plugin.homepage }
													target="_blank"
													rel="noreferrer"
													className="text-inherit hover:underline"
												>
													{ decodeEntities(
														plugin.name
													) }
												</a>
											) : (
												decodeEntities( plugin.name )
											) }
										</h3>
										{ plugin.version && (
											<span className="text-[12px] font-semibold text-[#a1a1aa] dark:text-ink-muted">
												{ sprintf(
													/* translators: %s: version number */
													__(
														'v%s',
														'bdthemes-element-pack-lite'
													),
													plugin.version
												) }
											</span>
										) }
									</div>
								</div>

								{ plugin.description && (
									<p className="m-0 text-[13px] leading-relaxed text-[#71717a] dark:text-ink-muted flex-1 overflow-hidden [display:-webkit-box] [-webkit-line-clamp:3] [-webkit-box-orient:vertical]">
										{ decodeEntities(
											plugin.description
										) }
									</p>
								) }

								<div className="flex items-center gap-2 flex-wrap pt-3 mt-auto border-t border-[#f3f3f6] dark:border-ink-border">
									<Badge tone={ statusTone( plugin.status ) }>
										{ statusLabel( plugin.status ) }
									</Badge>

									<span className="ml-auto flex items-center gap-2">
										{ plugin.status === 'not_installed' &&
											plugin.can_install && (
												<Button
													variant="gradient"
													size="sm"
													disabled={ !! busySlug }
													onClick={ () =>
														onInstall( plugin.slug )
													}
												>
													{ busySlug ===
														plugin.slug &&
													busyAction === 'install'
														? __(
																'Installing…',
																'bdthemes-element-pack-lite'
														  )
														: __(
																'Install',
																'bdthemes-element-pack-lite'
														  ) }
												</Button>
											) }

										{ plugin.status === 'installed' &&
											plugin.can_activate && (
												<Button
													variant="outline"
													size="sm"
													disabled={ !! busySlug }
													onClick={ () =>
														onActivate( plugin )
													}
												>
													{ busySlug ===
														plugin.slug &&
													busyAction === 'activate'
														? __(
																'Activating…',
																'bdthemes-element-pack-lite'
														  )
														: __(
																'Activate',
																'bdthemes-element-pack-lite'
														  ) }
												</Button>
											) }

										{ plugin.homepage && (
											<a
												href={ plugin.homepage }
												target="_blank"
												rel="noreferrer"
												className="inline-flex items-center gap-1 text-[13px] font-bold text-brand hover:underline"
											>
												{ plugin.status === 'premium'
													? __(
															'Get plugin',
															'bdthemes-element-pack-lite'
													  )
													: __(
															'Details',
															'bdthemes-element-pack-lite'
													  ) }
												<ExternalIcon size={ 13 } />
											</a>
										) }
									</span>
								</div>
							</Card>
						) ) }
					</div>
				) }
			</div>
		</div>
	);
}
