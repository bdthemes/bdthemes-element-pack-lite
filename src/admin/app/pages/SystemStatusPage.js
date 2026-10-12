/**
 * System requirements + environment summary.
 */
import { useSelect, useDispatch } from '@wordpress/data';
import { __, sprintf } from '@wordpress/i18n';
import { STORE_NAME } from '../store';
import PageHeader from '../components/shell/PageHeader';
import Spinner from '../components/Spinner';
import ErrorState from '../components/ErrorState';
import StatusCheckRow from '../components/StatusCheckRow';
import Card, { SectionCard } from '../components/ui/Card';
import Alert from '../components/ui/Alert';
import { GaugeIcon, CodeIcon } from '../components/ui/Icons';

function WidgetStatCard( { title, active, total } ) {
	return (
		<Card className="p-5">
			<div className="text-[12.5px] font-bold text-[#71717a] dark:text-ink-muted">
				{ title }
			</div>
			<div className="flex items-baseline gap-2 mt-2">
				<span className="text-[34px] font-black tracking-[-.03em] text-[#0f1115] dark:text-ink-text">
					{ active }
				</span>
				<span className="text-[12.5px] font-semibold text-[#a1a1aa] dark:text-ink-muted">
					{ sprintf(
						/* translators: %d: total count */
						__( 'of %d active', 'bdthemes-element-pack-lite' ),
						total
					) }
				</span>
			</div>
		</Card>
	);
}

function EnvRow( { label, value } ) {
	return (
		<div className="flex items-center justify-between gap-6 py-[13px] px-7 max-[640px]:px-5 border-t border-[#f3f3f6] dark:border-ink-border">
			<span className="text-[13.5px] font-bold text-[#1a1a1f] dark:text-ink-text">
				{ label }
			</span>
			<span className="font-mono text-[12.5px] text-[#71717a] dark:text-ink-muted text-right break-all">
				{ value }
			</span>
		</div>
	);
}

export default function SystemStatusPage() {
	const { data, isLoading, error } = useSelect( ( select ) => {
		const store = select( STORE_NAME );
		return {
			data: store.getResource( 'system' ),
			isLoading: store.isLoading( 'system' ),
			error: store.getError( 'system' ),
		};
	}, [] );

	useSelect( ( select ) => select( STORE_NAME ).getResource( 'system' ), [] );

	const { fetchResource } = useDispatch( STORE_NAME );
	const reload = () => fetchResource( 'system', 'system' );

	if ( isLoading && ! data ) {
		return (
			<div>
				<PageHeader
					title={ __( 'System Status', 'bdthemes-element-pack-lite' ) }
				/>
				<Spinner
					full
					label={ __(
						'Loading system report…',
						'bdthemes-element-pack-lite'
					) }
				/>
			</div>
		);
	}

	if ( error && ! data ) {
		return (
			<div>
				<PageHeader
					title={ __( 'System Status', 'bdthemes-element-pack-lite' ) }
				/>
				<ErrorState error={ error } onRetry={ reload } />
			</div>
		);
	}

	if ( ! data ) {
		return (
			<div>
				<PageHeader
					title={ __( 'System Status', 'bdthemes-element-pack-lite' ) }
				/>
				<Spinner full />
			</div>
		);
	}

	const widgets = data.widgets;

	return (
		<div>
			<PageHeader
				title={ __( 'System Status', 'bdthemes-element-pack-lite' ) }
				subtitle={ __(
					'Server requirements and environment overview.',
					'bdthemes-element-pack-lite'
				) }
			/>

			<div className="flex flex-col gap-4">
				{ ! data.all_pass && (
					<Alert tone="warning">
						{ sprintf(
							/* translators: %d: number of failed checks */
							__(
								'%d requirement(s) need attention.',
								'bdthemes-element-pack-lite'
							),
							data.failed_count
						) }
					</Alert>
				) }

				{ widgets && (
					<div className="grid grid-cols-3 max-[900px]:grid-cols-1 gap-3">
						<WidgetStatCard
							title={ __(
								'Core widgets',
								'bdthemes-element-pack-lite'
							) }
							active={ widgets.core.active }
							total={ widgets.core.total }
						/>
						<WidgetStatCard
							title={ __( '3rd party', 'bdthemes-element-pack-lite' ) }
							active={ widgets.third_party.active }
							total={ widgets.third_party.total }
						/>
						<WidgetStatCard
							title={ __(
								'Extensions',
								'bdthemes-element-pack-lite'
							) }
							active={ widgets.extensions.active }
							total={ widgets.extensions.total }
						/>
					</div>
				) }

				<SectionCard
					icon={ GaugeIcon }
					title={ __(
						'System requirements',
						'bdthemes-element-pack-lite'
					) }
					count={ data.checks.length }
				>
					{ data.checks.map( ( row ) => (
						<StatusCheckRow key={ row.id } check={ row } />
					) ) }
					{ data.note && (
						<p className="text-[12.5px] text-[#9a9aa3] dark:text-ink-muted py-3 px-7 max-[640px]:px-5 border-t border-[#f3f3f6] dark:border-ink-border m-0">
							{ data.note }
						</p>
					) }
				</SectionCard>

				{ data.environment && (
					<SectionCard
						icon={ CodeIcon }
						iconClassName="bg-[#f3f0ff] text-[#8b5cf6] dark:bg-[#241d3a]"
						title={ __( 'Environment', 'bdthemes-element-pack-lite' ) }
					>
						{ data.environment.wp_version && (
							<EnvRow
								label={ __(
									'WordPress',
									'bdthemes-element-pack-lite'
								) }
								value={ data.environment.wp_version }
							/>
						) }
						{ data.plugin_version && (
							<EnvRow
								label={ __(
									'Element Pack',
									'bdthemes-element-pack-lite'
								) }
								value={ data.plugin_version }
							/>
						) }
						{ data.environment.php_version && (
							<EnvRow
								label={ __( 'PHP', 'bdthemes-element-pack-lite' ) }
								value={ data.environment.php_version }
							/>
						) }
						{ data.environment.server_info && (
							<EnvRow
								label={ __(
									'Server',
									'bdthemes-element-pack-lite'
								) }
								value={ data.environment.server_info }
							/>
						) }
						{ data.environment.language && (
							<EnvRow
								label={ __(
									'Language',
									'bdthemes-element-pack-lite'
								) }
								value={ data.environment.language }
							/>
						) }
					</SectionCard>
				) }
			</div>
		</div>
	);
}
