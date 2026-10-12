/**
 * Tailwind config for the React admin SPA (src/admin/app) only.
 *
 * - `important: '#ep-admin-app'` scopes every utility under the SPA mount so
 *   nothing leaks into the rest of wp-admin. Consequence: utilities placed on
 *   the mount node itself will not match — the app renders an inner `.ep-root`
 *   wrapper that carries root-level utilities.
 * - Preflight is disabled (a global reset would restyle wp-admin); a scoped
 *   mini-reset lives in src/admin/app/styles/index.scss.
 * - Design tokens mirror the approved mockup in element-pack-dashboard-markup.
 */
module.exports = {
	content: [ './src/admin/app/**/*.js' ],
	important: '#ep-admin-app',
	corePlugins: { preflight: false },
	darkMode: [ 'selector', '[data-ep-theme="dark"]' ],
	theme: {
		extend: {
			colors: {
				brand: {
					DEFAULT: '#408efa',
					mint: '#84ed92',
				},
				ink: {
					page: '#0b0e13',
					card: '#161a21',
					subtle: '#12161c',
					border: '#262c36',
					text: '#edeef0',
					muted: '#9aa0a8',
				},
			},
			fontFamily: {
				sans: [
					'"IBM Plex Sans"',
					'system-ui',
					'-apple-system',
					'BlinkMacSystemFont',
					'"Segoe UI"',
					'Roboto',
					'sans-serif',
				],
			},
			borderRadius: {
				btn: '10px',
				tile: '11px',
				panel: '16px',
				card: '18px',
				sheet: '22px',
			},
			boxShadow: {
				// Cards render flat (border only) — resting/card shadows removed.
				resting: 'none',
				card: 'none',
				pop: '0 24px 60px -16px rgba(0,0,0,.32)',
				sheet: '0 30px 80px -20px rgba(0,0,0,.6)',
				glow: '0 6px 18px rgba(64,142,250,.36)',
				'glow-lg': '0 8px 24px rgba(64,142,250,.48)',
			},
			backgroundImage: {
				'gradient-brand': 'linear-gradient(135deg,#408efa,#84ed92)',
			},
			transitionTimingFunction: {
				ep: 'cubic-bezier(.16,1,.3,1)',
			},
			keyframes: {
				epPop: {
					from: { opacity: '0', transform: 'translateY(6px) scale(.98)' },
					to: { opacity: '1', transform: 'none' },
				},
				epFadeUp: {
					from: { opacity: '0', transform: 'translateY(10px)' },
					to: { opacity: '1', transform: 'none' },
				},
				epSpin: {
					to: { transform: 'rotate(360deg)' },
				},
				epShimmer: {
					from: { backgroundPosition: '200% 0' },
					to: { backgroundPosition: '-200% 0' },
				},
				epFloat: {
					'0%, 100%': { transform: 'translateY(0)' },
					'50%': { transform: 'translateY(-7px)' },
				},
				epGridShift: {
					from: { backgroundPosition: '0 0' },
					to: { backgroundPosition: '40px 40px' },
				},
				epRise: {
					from: { opacity: '0', transform: 'translateY(8px)' },
					to: { opacity: '1', transform: 'none' },
				},
				epFade: {
					from: { opacity: '0' },
					to: { opacity: '1' },
				},
			},
			animation: {
				'ep-pop': 'epPop .25s cubic-bezier(.16,1,.3,1) both',
				'ep-fade-up': 'epFadeUp .34s cubic-bezier(.16,1,.3,1) both',
				'ep-spin': 'epSpin .8s linear infinite',
				'ep-shimmer': 'epShimmer 1.6s linear infinite',
				'ep-float': 'epFloat 8s ease-in-out infinite',
				'ep-float-fast': 'epFloat .9s ease-in-out infinite',
				'ep-grid-shift': 'epGridShift 14s linear infinite',
				'ep-rise': 'epRise .35s ease both',
				'ep-pulse': 'epFade 1.6s ease-in-out infinite alternate',
			},
		},
	},
};
