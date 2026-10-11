/**
 * Catalog descriptions for widgets/extensions, shown in the card hover
 * tooltip. The module metadata has no description field, so these are
 * curated blurbs for well-known modules with a category-based fallback
 * for the rest.
 */
import { __ } from '@wordpress/i18n';
import { categoryKeyFor } from './widgetCategories';

const DESCRIPTIONS = {
	accordion:
		'Organize long content into collapsible panels with icons, custom triggers, nested layouts and full styling for titles, content and active states.',
	'advanced-button':
		'Eye-catching buttons with hover effects, icons, badges, tooltips and animated states — far beyond the default Elementor button.',
	'advanced-calculator':
		'Build interactive calculation forms — mortgage, loan, BMI or custom formulas — with live results and styled input fields.',
	'advanced-counter':
		'Animate numbers from zero to a target value with prefixes, suffixes, icons and duration control — perfect for stats and milestones.',
	'advanced-divider':
		'Replace plain separators with decorative dividers: icons, text labels, gradients, patterns and custom SVG shapes between sections.',
	'advanced-gmap':
		'Google Maps with multiple markers, custom pins, info windows, snazzy map themes and search — API-key powered and fully styleable.',
	'advanced-heading':
		'Compose multi-part headings that mix styles, colors and backgrounds in one title, with separate typography for each segment.',
	'advanced-icon-box':
		'Feature boxes pairing an icon or image with title, text and link — hover effects, badges and global link support included.',
	'advanced-image-gallery':
		'Filterable image galleries with masonry or grid layouts, category tabs, lightbox previews and hover overlay styles.',
	'advanced-progress-bar':
		'Animated progress and skill bars in multiple shapes and styles, with percentage labels, stripes and gradient fills.',
	'adblock-detector':
		'Detect visitors using ad blockers and show a dismissible notice or modal asking them to whitelist your site.',
	'animated-card':
		'Content cards with entrance animations and interactive hover motion — tilt, lift and reveal effects for portfolios and features.',
	'animated-gradient-background':
		'Animate smooth multi-color gradient backgrounds on any section or column — direction, speed and color stops are configurable.',
	'animated-heading':
		'Headlines with rotating or typed animated words — swap highlighted terms with fade, slide, clip or typewriter effects.',
	'animated-link':
		'Navigation and inline links with animated hover styles — underline sweeps, fills, brackets and more than a dozen effects.',
	'asset-manager':
		'Control which CSS/JS assets load on each page so visitors only download what the page actually uses — better scores, faster loads.',
	'audio-player':
		'A styled audio player for music tracks and podcasts with playlists, cover art, autoplay, loop and custom control skins.',
	'backdrop-filter':
		'Apply blur, brightness, saturation and other CSS backdrop filters behind sections for frosted-glass style effects.',
	'background-expand':
		'Section backgrounds that start boxed and expand to full width as the visitor scrolls — a subtle, modern reveal.',
	'background-image-parallax':
		'Give section background images smooth parallax movement on scroll, with speed and direction control.',
	'background-overlay':
		'Layer colors, gradients, patterns or images over section backgrounds with blend modes and opacity control.',
	'background-parallax':
		'Build multi-layer parallax scenes where background layers move at different speeds for real depth.',
	barcode:
		'Generate scannable barcodes in common formats (CODE128, EAN, UPC and more) from any text or number, styled to match your design.',
	breadcrumbs:
		'SEO-friendly breadcrumb navigation trails with separators, icons and schema markup, matching your site hierarchy automatically.',
	'brand-carousel':
		'Showcase client and partner logos in a smooth autoplay carousel with grayscale/hover effects and per-logo links.',
	'brand-grid':
		'Present client and partner logos in tidy responsive grids with hover states, tooltips and optional links.',
	'business-hours':
		'Opening-hours tables with per-day rows, highlight for the current day, closed labels and flexible styling.',
	calendly:
		'Embed Calendly booking widgets inline or as popup buttons so visitors can schedule meetings without leaving the page.',
	carousel:
		'A multi-purpose carousel for any content — cards, images, testimonials — with autoplay, loop, breakpoints and navigation styles.',
	changelog:
		'Present product release notes as styled changelog lists with version badges, dates and categorized change types.',
	chart:
		'Draw bar, line, pie, doughnut, radar and polar charts from your own data with legends, tooltips and animation.',
	'circle-info':
		'Circular info graphics that arrange points around a central image — ideal for process overviews and feature wheels.',
	'circle-menu':
		'A floating action button that expands into a circular menu of icon links — position, radius and animation are configurable.',
	comment:
		'Replace default comments with custom-styled comment forms and lists, including Disqus support.',
	'comparison-list':
		'Side-by-side feature comparison tables for plans or products, with check/cross icons, highlights and responsive stacking.',
	'confetti-effects':
		'Fire celebration confetti bursts on load, click or scroll — shapes, colors, spread and timing all configurable.',
	'contact-form':
		'A simple styled contact form with name, email and message fields, spam protection and email delivery.',
	'contact-form-7':
		'Take full styling control of Contact Form 7 forms — fields, labels, buttons and validation messages match your design.',
	'content-protector':
		'Protect page content behind a password or user role — visitors see a styled unlock prompt instead of the content.',
	'content-switcher':
		'Toggle between two or more content variants (e.g. monthly/yearly pricing) with an animated switch control.',
	'context-menu':
		'Replace the browser right-click menu with a custom context menu of your own links and actions on chosen elements.',
	'cookie-consent':
		'A configurable cookie notice and consent bar with accept/decline actions, expiry control and full styling.',
	countdown:
		'Countdown timers for launches, sales and events — evergreen or fixed-date, with actions on expiry like redirect or hide.',
	'coupon-code':
		'Reveal-style coupon boxes: visitors click to uncover and copy a discount code, with peel and pop animations.',
	'creative-button':
		'Buttons with playful, creative hover effects — morphing shapes, sliding fills and animated icon movement.',
	'cursor-effects':
		'Replace the mouse cursor with custom shapes, icons or images and add smooth trailing effects across chosen sections.',
	'custom-carousel':
		'Build free-form carousels from any content — each slide is its own template or block, with full slider controls.',
	'custom-gallery':
		'Design galleries with fully custom item layouts, hover content, filters and lightbox support.',
	'custom-js':
		'Attach custom JavaScript to a page or section directly from the editor — handy for tracking or small behaviors.',
	'device-slider':
		'Show app or site screenshots inside phone, tablet and laptop frames with sliding transitions.',
	'document-viewer':
		'Embed PDFs, Word, Excel and PowerPoint files in an in-page viewer instead of forcing downloads.',
	'dual-button':
		'Two paired call-to-action buttons with a connecting label (like “or”) — perfect for signup/demo choices.',
	duplicator:
		'One-click duplication of posts, pages and Elementor templates from the WordPress admin lists.',
	'dynamic-carousel':
		'Carousels populated automatically from dynamic queries — posts, custom post types or ACF data.',
	'dynamic-content':
		'Populate widget fields with live site data — post meta, ACF fields, user info, site options — via dynamic tags.',
	'dynamic-grid':
		'Responsive grids populated from dynamic queries with ordering, filtering and pagination.',
	'equal-height':
		'Force selected columns or widgets in a row to share the same height for tidy, balanced layouts.',
	'essential-shortcodes':
		'A set of handy utility shortcodes — current year, site info, login/logout links and more — usable anywhere.',
	faq: 'FAQ lists with expandable answers and built-in FAQ schema markup for rich search results.',
	'fancy-icons':
		'Decorative animated icons with entrance effects, infinite motion and gradient/duotone styling.',
	'fancy-list':
		'Rich list layouts with numbered markers, icons, connectors and per-item styling — beyond plain bullets.',
	'fancy-slider':
		'A bold showcase slider with split content/image layouts, big typography and dramatic transitions.',
	'fancy-tabs':
		'Tabs with creative navigation styles — icon tabs, pills, vertical rails — and animated panel switching.',
	'featured-box':
		'Highlight a key feature with an image/icon plus text in layered, offset layouts that draw the eye.',
	'flip-box':
		'Two-sided boxes that flip or fade on hover, revealing a back side with its own content and CTA.',
	'floating-effects':
		'Give any element a gentle floating motion — vertical drift, rotation, scale pulses — with fine timing control.',
	'floating-knowledgebase':
		'A floating help panel that opens docs, FAQs and support links from a corner launcher button.',
	'global-badge':
		'Attach “New”, “Sale” or custom badges to any widget site-wide with positioning and animation options.',
	'google-reviews':
		'Pull and display your Google Business reviews in grids or carousels with ratings, avatars and dates.',
	'grid-line':
		'Show a layout grid overlay while designing so spacing and alignment stay consistent across sections.',
	'hash-link':
		'Smooth-scroll anchor links with offset control — great for one-page navigation and jump menus.',
	helpdesk:
		'A floating contact and support hub combining chat links, contact channels and FAQs behind one launcher.',
	'horizontal-scroller':
		'Turn vertical scrolling into a horizontal journey — sections slide sideways as the visitor scrolls down.',
	'hover-box':
		'Image boxes that reveal titles, text and links with animated overlays on hover.',
	'hover-video':
		'Videos that start playing when hovered and pause on leave — ideal for portfolio and product previews.',
	'icon-mobile-menu':
		'A mobile-friendly bottom or side icon bar for primary navigation on small screens.',
	iconnav:
		'Icon-based navigation bars with labels, tooltips and active states — vertical or horizontal.',
	iframe:
		'Embed external pages via iframe with lazy loading, custom height, scrolling control and security attributes.',
	'image-compare':
		'Before/after image comparison with a draggable slider handle — horizontal or vertical, with labels.',
	'image-expand':
		'Click-to-expand image strips: a compact row where each image opens out to reveal content.',
	'image-hover-effects':
		'A library of hover effects for images — zooms, slides, blurs, duotones — applied with one click.',
	'image-magnifier':
		'A zoom lens that magnifies image detail on hover — perfect for product photography.',
	'image-masking':
		'Mask images into shapes — blobs, circles, custom SVGs — without editing the original file.',
	'image-parallax':
		'Give images subtle parallax depth that responds to scroll or mouse movement.',
	'image-stack':
		'Stacked, overlapping image piles that fan out on hover — a playful way to preview galleries.',
	instagram:
		'Display your Instagram feed in grids or carousels with likes/comments counts, lightbox and caching.',
	'interactive-card':
		'Cards that respond to the mouse with 3D tilt and glare effects, plus animated content reveals.',
	'interactive-tabs':
		'Tabs with animated, interactive switching — background sweeps, sliding indicators and hover previews.',
	lightbox:
		'Open images, videos, iframes or custom content in styled lightbox popups from any trigger element.',
	'live-copy':
		'Copy ready-made demo layouts from elementpack.pro straight into your own pages with one click.',
	'logo-carousel':
		'Logo carousels with autoplay, grayscale hover, per-logo links and responsive column control.',
	'logo-grid':
		'Responsive logo grids with borders, hover states and optional tooltips for each brand.',
	'lottie-icon-box':
		'Icon boxes powered by Lottie animations that play on load, hover or scroll.',
	'lottie-image':
		'Use lightweight Lottie JSON animations anywhere an image would go, with playback triggers and speed control.',
	mailchimp:
		'Mailchimp subscribe forms wired to your audience lists, with merge fields, GDPR checkbox and styled inputs.',
	marker:
		'Highlight words with hand-drawn marker strokes — circles, underlines, boxes — that animate as you scroll.',
	marquee:
		'Continuously scrolling marquee strips of text or logos with speed, direction and pause-on-hover control.',
	'mega-menu':
		'Build full-width mega menus for WordPress navigation with Elementor-designed dropdown panels, badges and icons.',
	modal:
		'Popups triggered by click, page load, exit intent or delay — put any template inside, with animation and cookie control.',
	navbar:
		'Custom navigation bars built from WordPress menus with dropdown styling, sticky options and hover effects.',
	'news-ticker':
		'Scrolling or fading headline tickers pulling from posts or custom text, with label and controls.',
	notation:
		'Annotate text with animated hand-sketched effects — underlines, circles, brackets, highlights — as visitors scroll.',
	notification:
		'Site-wide notification bars for announcements, with positions, dismiss memory and scheduling.',
	offcanvas:
		'Slide-in offcanvas panels from any edge for menus, filters or custom content, triggered by button or icon.',
	'open-street-map':
		'Interactive maps powered by OpenStreetMap — no API key required — with markers, popups and custom tiles.',
	'panel-slider':
		'Panel-style sliders where content cards slide within a framed layout — compact and elegant.',
	'parallax-effects':
		'Compose multi-layer parallax scenes inside a section — each layer moves at its own speed with mouse or scroll.',
	particles:
		'Animated particle backgrounds — dots, lines, snow, custom shapes — with density, speed and interactivity settings.',
	'portfolio-carousel':
		'Portfolio project carousels with hover overlays, category badges and lightbox or link actions.',
	'portfolio-gallery':
		'Filterable portfolio galleries with category tabs, masonry layouts and hover detail reveals.',
	'portfolio-list':
		'Editorial portfolio list layouts alternating image and project details, with per-item links.',
	'price-list':
		'Menu-style price lists for restaurants and services with images, dotted leaders and price alignment.',
	'price-table':
		'Pricing tables with feature lists, ribbons, tooltips and CTA buttons in multiple design presets.',
	'product-carousel':
		'WooCommerce product carousels with add-to-cart, ratings, badges and query controls.',
	'product-grid':
		'WooCommerce product grids with quick view, badges, ratings and flexible query filtering.',
	'profile-card':
		'Personal profile cards with cover image, avatar, stats and social links.',
	'progress-pie':
		'Circular progress indicators with percentage counters, gradients and stroke styling.',
	'protected-content':
		'Show or hide content by password or user role, with a styled unlock form for restricted visitors.',
	qrcode:
		'Generate QR codes from URLs or text with color, logo overlay and size options.',
	'reading-progress':
		'A slim progress bar showing how far the visitor has read — top, bottom or custom placement.',
	'reading-timer':
		'Display estimated reading time for articles, calculated automatically from content length.',
	'reveal-effects':
		'Reveal elements on scroll with curtain, wipe and fade animations — direction and easing configurable.',
	'review-card':
		'Customer review cards with rating stars, avatar, source icon and quote styling.',
	'review-card-carousel':
		'Sliding carousels of customer review cards with autoplay and navigation.',
	'review-card-grid':
		'Responsive grids of customer review cards with ratings and source badges.',
	'ripple-effects':
		'Material-style ripple feedback on clicks for buttons, images or whole sections.',
	'scroll-box':
		'Fixed-height scrollable content boxes with custom scrollbar styling — fit long content in tight spaces.',
	'scroll-button':
		'Buttons that smooth-scroll to any section, with offset, easing and optional back-to-top behavior.',
	'scroll-image':
		'Tall screenshots that auto-scroll inside a frame on hover — ideal for showing full landing pages.',
	scrollnav:
		'Dot navigation fixed at the screen edge that tracks and jumps between page sections.',
	search:
		'Styled site search forms — inline, expanding icon or full-screen overlay — with live suggestions.',
	'section-sticky':
		'Make sections or widgets stick while scrolling, with offset, active breakpoints and unstick rules.',
	'shape-builder':
		'Draw custom SVG shapes — polygons, blobs, lines — directly in the editor and layer them behind content.',
	'shape-divider':
		'Decorative shape dividers between sections — waves, tilts, curves — beyond Elementor’s built-in set.',
	slider:
		'Full-width hero sliders with layered content, Ken Burns and parallax effects, and rich navigation styling.',
	slideshow:
		'Background slideshows that rotate behind fixed headline content — with transitions and overlay control.',
	'smooth-scroller':
		'Replace default scrolling with a buttery smooth, eased scroll experience site-wide.',
	'social-proof':
		'Recent-activity popups (“Someone just bought…”) that build trust with configurable timing and sources.',
	'social-share':
		'Social sharing button sets for all major networks with counts, labels and shape presets.',
	'sound-effects':
		'Play subtle UI sounds on clicks, hovers or page events, with volume and per-element triggers.',
	'source-code':
		'Display syntax-highlighted code blocks with language detection, line numbers and copy button.',
	stacker:
		'Full-screen stacked section decks where each section slides or scales over the last as you scroll.',
	'step-flow':
		'Numbered process steps with connector arrows and direction control — explain how it works in style.',
	'sub-menu':
		'Standalone dropdown submenus from any WordPress menu — useful in headers built with Elementor.',
	'svg-blob':
		'Animated organic blob shapes that morph continuously — as backgrounds or image masks.',
	'svg-image':
		'Render SVGs inline so you can style fills, strokes and animation directly — not just as static images.',
	'svg-maps':
		'Interactive SVG region maps (world, countries) with clickable, colorable regions and tooltips.',
	'svg-support':
		'Enable safe SVG uploads to the media library with sanitization.',
	switcher:
		'Toggle between two content templates with an animated switch — pricing, feature or theme comparisons.',
	table:
		'Data tables from CSV files or manual content, with sorting, searching, pagination and responsive modes.',
	'table-of-content':
		'Auto-generated table of contents from page headings, with smooth scroll, collapsible tree and sticky option.',
	tablepress:
		'Take styling control of TablePress tables — headers, rows, borders and hover states match your design.',
	tabs: 'Tabbed content sections with horizontal/vertical navigation, icons and swipeable mobile behavior.',
	'template-library':
		'Insert ready-made page templates and blocks from the Element Pack library directly in the editor.',
	'testimonial-carousel':
		'Testimonial carousels with ratings, avatars and multiple navigation styles.',
	'testimonial-grid':
		'Responsive testimonial grids with ratings, avatars and filter support.',
	testimonials:
		'Customer testimonial layouts fed by the built-in testimonial post type or custom content.',
	'time-zone':
		'World clocks for multiple time zones — analog or digital — with city labels and DST awareness.',
	toggle:
		'Show/hide a content area behind a styled toggle button — for optional details and spoilers.',
	tooltip:
		'Attach rich tooltips to any element with custom placement, animation and HTML content.',
	'total-count':
		'Display live site totals — posts, users, comments, products — as animated counters.',
	'trailer-box':
		'Movie-style trailer showcases pairing a poster, synopsis and play button that opens a video lightbox.',
	transform:
		'Apply CSS transforms — rotate, scale, skew, translate — to any widget, normal and hover states.',
	'twitter-carousel':
		'Sliding carousels of tweets from your timeline with avatars, media and action links.',
	'twitter-grid':
		'Responsive grids of tweets from your timeline with media previews.',
	'twitter-slider':
		'One-at-a-time tweet sliders — a compact way to feature social mentions.',
	'user-login':
		'Front-end login forms with redirects, remember-me, lost-password links and full styling — no wp-admin look.',
	'user-register':
		'Front-end registration forms with custom fields, role assignment and email verification support.',
	'video-gallery':
		'Filterable video galleries for YouTube/Vimeo with category tabs, thumbnails and inline or lightbox playback.',
	'video-player':
		'A custom player for self-hosted or streamed video with poster, controls styling and lazy load.',
	'weather-widget':
		'Live local weather displays with current conditions, forecast days and icon sets.',
	'wc-add-to-cart':
		'WooCommerce add-to-cart buttons for any product with quantity, variation and redirect options.',
	'wc-carousel':
		'WooCommerce product carousels with ratings, prices, badges and add-to-cart actions.',
	'wc-categories':
		'Showcase WooCommerce product categories in grids or carousels with images and counts.',
	'wc-mini-cart':
		'A styled WooCommerce mini cart with slide-in drawer, item management and totals.',
	'wc-products':
		'Flexible WooCommerce product layouts with query controls, badges, quick view and pagination.',
	wipe: 'Wipe transition effects that sweep a colored layer across elements as they enter the viewport.',
	'wrapper-link':
		'Make an entire section, column or widget clickable by wrapping it in a link.',
};

const FALLBACKS = {
	woocommerce: __(
		'Commerce widget for your WooCommerce store — product display, cart and checkout elements styled to match your design.',
		'bdthemes-element-pack-lite'
	),
	forms: __(
		'Form widget with field controls, validation and submission handling, styled to match your design.',
		'bdthemes-element-pack-lite'
	),
	dynamic: __(
		'Dynamic content widget that pulls posts, terms or custom fields with query, layout and pagination controls.',
		'bdthemes-element-pack-lite'
	),
	navigation: __(
		'Navigation widget for menus, in-page jumps and wayfinding, with responsive and sticky behaviour.',
		'bdthemes-element-pack-lite'
	),
	global: __(
		'Global extension that adds behaviour to any element — configure triggers, timing and style.',
		'bdthemes-element-pack-lite'
	),
	special: __(
		'Special-purpose widget for site utilities such as access rules, codes and embedded tools.',
		'bdthemes-element-pack-lite'
	),
	social: __(
		'Social content widget — display feeds, reviews or sharing actions from social platforms on your pages.',
		'bdthemes-element-pack-lite'
	),
	creative: __(
		'Creative visual widget that adds motion and polish to your pages — configure triggers, timing and style.',
		'bdthemes-element-pack-lite'
	),
	content: __(
		'Flexible content layout widget with rich styling controls for structure, typography and spacing.',
		'bdthemes-element-pack-lite'
	),
};


/**
 * @param {string} slug Module slug.
 * @return {string} Catalog description.
 */
export function descriptionFor( slug ) {
	return (
		DESCRIPTIONS[ slug ] ||
		FALLBACKS[ categoryKeyFor( slug ) ] ||
		FALLBACKS.content
	);
}
