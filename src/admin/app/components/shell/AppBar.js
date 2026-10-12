/**
 * Unified app bar — logo, version, ⌘K search, notifications, theme toggle,
 * docs, upgrade and what's-new actions.
 */
import { __ } from '@wordpress/i18n';
import config from '../../utils/config';
import { useColorScheme } from '../../hooks/useColorScheme';
import NotificationBell from './NotificationBell';
import { SearchIcon, SunIcon, MoonIcon, BookIcon, ZapIcon } from '../ui/Icons';
import logoUrl from '../../assets/element-pack-logo.svg';
import logoWhiteUrl from '../../assets/element-pack-logo-white.svg';

export default function AppBar( { onOpenPalette } ) {
	const { theme, toggle } = useColorScheme();
	const isDark = theme === 'dark';
	const iconBtn =
		'w-[42px] h-[42px] rounded-full bg-white border border-[#e6e6ea] text-[#52525b] dark:bg-ink-card dark:border-[#2a313c] dark:text-[#cdd0d6] cursor-pointer grid place-items-center transition-colors hover:border-brand hover:text-brand';

	return (
		<div className="py-[18px] px-[26px] flex items-center justify-between gap-5 flex-wrap max-[900px]:py-[13px] max-[900px]:px-[15px] max-[900px]:gap-3">
			<div className="flex items-center gap-4">
				<img
					src={ isDark ? logoWhiteUrl : logoUrl }
					alt={ __( 'Element Pack', 'bdthemes-element-pack-lite' ) }
					className="h-8 block"
				/>
				{ config.isPro ? (
					<span className="text-[11px] font-extrabold tracking-[.06em] text-white bg-gradient-brand py-[5px] px-[11px] rounded-full shadow-[0_3px_10px_rgba(64,142,250,.3)]">
						{ __( 'PRO', 'bdthemes-element-pack-lite' ) }
					</span>
				) : null }
				<span className="text-[12.5px] text-[#71717a] dark:text-ink-muted font-bold">
					v{ config.version }
				</span>
			</div>

			<div className="flex items-center gap-[10px]">
				<button
					type="button"
					onClick={ onOpenPalette }
					className="flex items-center gap-[9px] bg-[#f4f5f7] border border-[#e6e6ea] text-[#71717a] dark:bg-ink-card dark:border-[#2a313c] dark:text-[#cdd0d6] py-[9px] px-[13px] rounded-full text-[13px] font-semibold cursor-pointer transition-colors hover:border-brand hover:text-brand"
				>
					<SearchIcon size={ 15 } />
					{ __( 'Search', 'bdthemes-element-pack-lite' ) }
					<span className="hidden lg:flex gap-[2px] ml-[2px]">
						<kbd className="text-[10.5px] font-bold bg-white dark:bg-ink-subtle border border-[#e2e2e6] dark:border-ink-border rounded-[5px] py-px px-[5px] text-[#a1a1aa]">
							⌘
						</kbd>
						<kbd className="text-[10.5px] font-bold bg-white dark:bg-ink-subtle border border-[#e2e2e6] dark:border-ink-border rounded-[5px] py-px px-[5px] text-[#a1a1aa]">
							K
						</kbd>
					</span>
				</button>

				<NotificationBell buttonClassName={ iconBtn } />

				<button
					type="button"
					onClick={ toggle }
					className={ iconBtn }
					aria-label={ __( 'Toggle theme', 'bdthemes-element-pack-lite' ) }
				>
					{ isDark ? (
						<SunIcon size={ 18 } />
					) : (
						<MoonIcon size={ 18 } />
					) }
				</button>

				<a
					href={ config.docsUrl }
					target="_blank"
					rel="noreferrer"
					className="hidden lg:flex items-center gap-[6px] bg-white border-[1.5px] border-[#e2e2e6] text-[#27272a] dark:bg-ink-card dark:border-[#2a313c] dark:text-[#cdd0d6] py-[10px] px-[17px] rounded-full text-[13.5px] font-bold cursor-pointer transition-colors hover:border-brand hover:text-brand"
				>
					<BookIcon size={ 14 } />
					{ __( 'Docs', 'bdthemes-element-pack-lite' ) }
				</a>

				{ ! config.isPro ? (
					<a
						href={ config.upgradeUrl }
						target="_blank"
						rel="noreferrer"
						className="hidden lg:flex items-center gap-[6px] bg-white border-[1.5px] border-brand text-brand dark:bg-ink-card py-[10px] px-[17px] rounded-full text-[13.5px] font-extrabold cursor-pointer transition-colors hover:bg-brand hover:text-white"
					>
						{ __( 'Upgrade to Pro', 'bdthemes-element-pack-lite' ) }
					</a>
				) : null }

				<a
					href={ config.whatsNewUrl }
					target="_blank"
					rel="noreferrer"
					className="hidden lg:flex items-center gap-[7px] bg-gradient-brand text-white py-[11px] px-[22px] rounded-full text-[13.5px] font-extrabold cursor-pointer shadow-glow transition-all duration-200 hover:-translate-y-px hover:shadow-glow-lg"
				>
					<ZapIcon size={ 14 } />
					{ __( "What's new", 'bdthemes-element-pack-lite' ) }
				</a>
			</div>
		</div>
	);
}
