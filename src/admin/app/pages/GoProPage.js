/**
 * Go Pro: a landing page for what Element Pack Pro adds.
 *
 * Tabs (kept in the URL as ?tab= so a section can be linked or shared):
 * Overview, Free vs Pro and FAQ. Every number comes from the live catalog, so
 * the page stays correct as the plugin's free/pro split changes.
 */
import { useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useSearchParams } from 'react-router-dom';
import { useDispatch } from '@wordpress/data';
import { STORE_NAME } from '../store';
import { useProCatalog } from '../hooks/useProCatalog';
import PageHeader from '../components/shell/PageHeader';
import Spinner from '../components/Spinner';
import ErrorState from '../components/ErrorState';
import EmptyState from '../components/EmptyState';
import ProHero from '../components/pro/ProHero';
import ProAiHighlight from '../components/pro/ProAiHighlight';
import ProHighlights from '../components/pro/ProHighlights';
import ProCompare from '../components/pro/ProCompare';
import ProFaq from '../components/pro/ProFaq';
import ProCta from '../components/pro/ProCta';
import { UpgradeLink } from '../components/pro/ProLinks';

const TAB_BASE =
	'flex items-center gap-[7px] py-[8px] px-[15px] rounded-full text-[13px] font-bold cursor-pointer whitespace-nowrap transition-all duration-200 border-0 font-sans';
const TAB_ACTIVE =
	'bg-gradient-brand text-white shadow-[0_5px_14px_rgba(64,142,250,.32)]';
const TAB_IDLE =
	'bg-transparent text-[#52525b] dark:text-ink-muted hover:bg-[#eef1f5] dark:hover:bg-[#1f242d] hover:text-[#18181b] dark:hover:text-ink-text';

const TAB_KEYS = [ 'overview', 'compare', 'faq' ];

export default function GoProPage() {
	const catalog = useProCatalog();
	const { fetchResource } = useDispatch( STORE_NAME );
	const [ params, setParams ] = useSearchParams();

	const requested = params.get( 'tab' );
	const tab = TAB_KEYS.includes( requested ) ? requested : 'overview';

	const goTo = useCallback(
		( key ) => {
			setParams( key === 'overview' ? {} : { tab: key }, { replace: true } );
		},
		[ setParams ]
	);

	const retry = () => {
		fetchResource( 'modules', 'modules' );
		fetchResource( 'settings', 'settings' );
	};

	const tabs = [
		{ key: 'overview', label: __( 'Overview', 'bdthemes-element-pack-lite' ) },
		{ key: 'compare', label: __( 'Free vs Pro', 'bdthemes-element-pack-lite' ) },
		{ key: 'faq', label: __( 'FAQ', 'bdthemes-element-pack-lite' ) },
	];

	return (
		<div>
			<PageHeader
				title={ __( 'You may also need', 'bdthemes-element-pack-lite' ) }
				subtitle={ __(
					'See what Element Pack Pro adds to the plugin you already use.',
					'bdthemes-element-pack-lite'
				) }
				actions={ catalog.isPro ? null : <UpgradeLink /> }
			/>

			{ catalog.isLoading && ! catalog.ready && (
				<Spinner
					full
					label={ __( 'Loading…', 'bdthemes-element-pack-lite' ) }
				/>
			) }

			{ catalog.error && ! catalog.ready && (
				<ErrorState error={ catalog.error } onRetry={ retry } />
			) }

			{ catalog.ready && catalog.isPro && (
				<EmptyState
					title={ __(
						'Element Pack Pro is active',
						'bdthemes-element-pack-lite'
					) }
					description={ __(
						'Everything is unlocked. There is nothing to upgrade.',
						'bdthemes-element-pack-lite'
					) }
				/>
			) }

			{ catalog.ready && ! catalog.isPro && (
				<div className="flex flex-col gap-5">
					<nav
						aria-label={ __( 'You may also need sections', 'bdthemes-element-pack-lite' ) }
						className="flex items-center gap-1 overflow-x-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden bg-white dark:bg-ink-card border border-[#e6e6ea] dark:border-ink-border rounded-full p-1 self-start max-w-full"
					>
						{ tabs.map( ( item ) => (
							<button
								key={ item.key }
								type="button"
								onClick={ () => goTo( item.key ) }
								aria-current={ tab === item.key ? 'page' : undefined }
								className={ `${ TAB_BASE } ${
									tab === item.key ? TAB_ACTIVE : TAB_IDLE
								}` }
							>
								{ item.label }
							</button>
						) ) }
					</nav>

					{ tab === 'overview' && (
						<>
							<ProHero
								catalog={ catalog }
								onCompare={ () => goTo( 'compare' ) }
							/>
							<ProAiHighlight />
							<ProHighlights catalog={ catalog } />
						</>
					) }

					{ tab === 'compare' && <ProCompare catalog={ catalog } /> }

					{ tab === 'faq' && <ProFaq /> }

					<ProCta />
				</div>
			) }
		</div>
	);
}
