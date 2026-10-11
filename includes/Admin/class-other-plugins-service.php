<?php
/**
 * REST-safe wrapper for the Others Plugin manager.
 *
 * @package ElementPack\Admin
 */

namespace ElementPack\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Other_Plugins_Service {

	/**
	 * Fallback blurbs for the companion plugins, used when the remote feed
	 * (wp.org short_description, cached 7 days) has no description for a slug.
	 *
	 * @return array<string,string>
	 */
	private static function get_fallback_descriptions() {
		return [
			'bdthemes-element-pack-lite' => __( 'All-in-one addon for Elementor with advanced widgets, extensions and ready templates.', 'bdthemes-element-pack-lite' ),
			'bdthemes-prime-slider-lite' => __( 'Modern slider builder for Elementor with beautifully designed, ready-made slider layouts.', 'bdthemes-element-pack-lite' ),
			'ultimate-post-kit'          => __( 'Advanced post grids, carousels and magazine blocks to build stunning blogs with Elementor.', 'bdthemes-element-pack-lite' ),
			'ultimate-store-kit'         => __( 'WooCommerce widgets for Elementor — product grids, carousels, carts and store pages.', 'bdthemes-element-pack-lite' ),
			'zoloblocks'                 => __( 'A library of advanced blocks, patterns and page templates for the WordPress block editor.', 'bdthemes-element-pack-lite' ),
			'pixel-gallery'              => __( 'Easy grid, masonry and justified photo galleries for Elementor with lightbox support.', 'bdthemes-element-pack-lite' ),
			'live-copy-paste'            => __( 'Copy any Elementor section or page from one website and paste it into another.', 'bdthemes-element-pack-lite' ),
			'spin-wheel'                 => __( 'Interactive spin-the-wheel popups for coupons, discounts and lead collection.', 'bdthemes-element-pack-lite' ),
			'ai-image'                   => __( 'Generate AI images and import them straight into your WordPress media library.', 'bdthemes-element-pack-lite' ),
			'dark-reader'                => __( 'One-click dark mode toggle for your website visitors.', 'bdthemes-element-pack-lite' ),
			'ar-viewer'                  => __( 'Display interactive 3D and augmented-reality models on your site.', 'bdthemes-element-pack-lite' ),
			'smart-admin-assistant'      => __( 'A smart assistant that helps you manage your WordPress admin faster.', 'bdthemes-element-pack-lite' ),
			'website-accessibility'      => __( 'Accessibility toolbar that makes your website easier for everyone to use.', 'bdthemes-element-pack-lite' ),
		];
	}

	/**
	 * Official product websites per slug. Preferred for the card's Details
	 * link; slugs without a dedicated site fall back to the feed homepage,
	 * then the wp.org directory page.
	 *
	 * @return array<string,string>
	 */
	private static function get_official_sites() {
		return [
			'bdthemes-element-pack-lite' => 'https://elementpack.pro/',
			'bdthemes-prime-slider-lite' => 'https://primeslider.pro/',
			'ultimate-post-kit'          => 'https://postkit.pro/',
			'ultimate-store-kit'         => 'https://storekit.pro/',
			'zoloblocks'                 => 'https://zoloblocks.com/',
			'pixel-gallery'              => 'https://pixelgallery.pro/',
			'live-copy-paste'            => 'https://youtu.be/KWxbZfPIcqU',
			'spin-wheel'                 => 'https://bdthemes.com/plugin/spin-wheel/',
			'dark-reader'                => 'https://bdthemes.com/plugin/dark-reader/',
			'ar-viewer'                  => 'https://wordpress.org/plugins/ar-viewer/',
			'smart-admin-assistant'      => 'https://bdthemes.com/plugin/smart-admin-assistant/',
			'website-accessibility'      => 'https://oneaccessibility.com/',
		];
	}

	/**
	 * Clean display names per slug — the wp.org feed carries long SEO titles
	 * ("Element Pack Addons for Elementor – Best Elementor Addons…") that
	 * truncate badly on the cards.
	 *
	 * @return array<string,string>
	 */
	private static function get_display_names() {
		return [
			'bdthemes-element-pack-lite' => 'Element Pack Lite',
			'bdthemes-prime-slider-lite' => 'Prime Slider',
			'ultimate-post-kit'          => 'Ultimate Post Kit',
			'ultimate-store-kit'         => 'Ultimate Store Kit',
			'zoloblocks'                 => 'ZoloBlocks',
			'pixel-gallery'              => 'Pixel Gallery',
			'live-copy-paste'            => 'Live Copy Paste',
			'spin-wheel'                 => 'Spin Wheel',
			'ai-image'                   => 'Instant AI Image Generator',
			'dark-reader'                => 'Dark Reader',
			'ar-viewer'                  => 'AR Viewer',
			'smart-admin-assistant'      => 'Smart Admin Assistant',
			'website-accessibility'      => 'Website Accessibility',
		];
	}

	/**
	 * BdThemes products that are sold only as premium plugins.
	 *
	 * They have no wordpress.org listing, so the remote feed knows nothing
	 * about them and they cannot be installed from here — the card links to
	 * the product page instead. Logos ship with Element Pack.
	 *
	 * @return array<int,array<string,string>>
	 */
	private static function get_premium_plugins() {
		return [
			[
				'slug'        => 'sigma-forms',
				'name'        => 'Sigma Forms',
				'homepage'    => 'https://bdthemes.com/plugin/sigma-forms/',
				'description' => __( 'AI-powered WordPress forms in minutes — describe your form, refine it field by field, and ship without touching code.', 'bdthemes-element-pack-lite' ),
			],
			[
				'slug'        => 'sigma-media-manager',
				'name'        => 'Sigma Media Manager',
				'homepage'    => 'https://bdthemes.com/plugin/sigma-media-manager/',
				'description' => __( 'Folders, filters and drag-and-drop for your WordPress media library — fast even at 10,000+ files.', 'bdthemes-element-pack-lite' ),
			],
			[
				'slug'        => 'sigma-store-locator',
				'name'        => 'Sigma Store Locator',
				'homepage'    => 'https://bdthemes.com/plugin/sigma-store-locator/',
				'description' => __( 'Unlimited-location store finder for WordPress and WooCommerce — Google Maps or OpenStreetMap, radius search, CSV import and local pickup.', 'bdthemes-element-pack-lite' ),
			],
			[
				'slug'        => 'launch-guard',
				'name'        => 'Launch Guard',
				'homepage'    => 'https://bdthemes.com/plugin/launch-guard/',
				'description' => __( 'Coming soon and maintenance mode pages with conversion-focused templates, a countdown and email capture.', 'bdthemes-element-pack-lite' ),
			],
		];
	}

	/**
	 * @return array<string,mixed>
	 */
	public static function get_plugins() {
		self::ensure_dependencies();

		$plugins_data = \ElementPack\SetupWizard\Remote_Data_Handler::get_remote_plugins();

		if ( empty( $plugins_data ) ) {
			\ElementPack\SetupWizard\Remote_Data_Handler::schedule_remote_fetch();

			return [
				'plugins' => [],
				'loading' => true,
				'message' => __( 'Loading plugin data…', 'bdthemes-element-pack-lite' ),
			];
		}

		$normalized = [];

		foreach ( $plugins_data as $slug => $plugin ) {
			if ( ! is_array( $plugin ) ) {
				continue;
			}

			if ( is_string( $slug ) && '' !== $slug ) {
				$plugin['slug'] = $slug;
			}

			$slug = isset( $plugin['slug'] ) ? sanitize_key( $plugin['slug'] ) : '';

			// This dashboard belongs to Element Pack Lite, so listing it here
			// would only offer the plugin that is already running.
			if ( 'bdthemes-element-pack-lite' === $slug ) {
				continue;
			}

			$status = self::get_local_plugin_status( $slug, $plugin );

			$description = isset( $plugin['description'] ) ? trim( wp_strip_all_tags( (string) $plugin['description'] ) ) : '';

			if ( '' === $description ) {
				$fallbacks   = self::get_fallback_descriptions();
				$description = isset( $fallbacks[ $slug ] ) ? $fallbacks[ $slug ] : '';
			}

			// Details link priority: official product site → feed homepage →
			// wp.org directory page.
			$official_sites = self::get_official_sites();
			$homepage       = isset( $official_sites[ $slug ] ) ? $official_sites[ $slug ] : '';

			if ( '' === $homepage ) {
				$homepage = isset( $plugin['homepage'] ) ? esc_url_raw( $plugin['homepage'] ) : '';
			}

			if ( '' === $homepage && '' !== $slug ) {
				$homepage = 'https://wordpress.org/plugins/' . $slug . '/';
			}

			// Prefer the curated display name; otherwise trim the feed title's
			// SEO tail ("Name – tagline…" → "Name").
			$display_names = self::get_display_names();
			$name          = isset( $display_names[ $slug ] ) ? $display_names[ $slug ] : '';

			if ( '' === $name ) {
				$name = isset( $plugin['name'] ) ? html_entity_decode( (string) $plugin['name'], ENT_QUOTES ) : '';
				$name = trim( preg_split( '/\s+[–—|]\s+/u', $name )[0] );
			}

			// wp.org serves plugin icons from a predictable CDN path — use it
			// when the feed has no logo (the React card falls back to a tinted
			// tile if the image 404s).
			$icon = isset( $plugin['logo'] ) ? esc_url_raw( $plugin['logo'] ) : '';

			if ( '' === $icon && '' !== $slug ) {
				$icon = 'https://ps.w.org/' . $slug . '/assets/icon-128x128.png';
			}

			$normalized[] = [
				'slug'            => $slug,
				'name'            => $name,
				'description'     => $description,
				'version'         => isset( $plugin['version'] ) ? (string) $plugin['version'] : '',
				'active_installs' => isset( $plugin['active_installs'] ) ? (string) $plugin['active_installs'] : '',
				'rating'          => isset( $plugin['rating'] ) ? (float) $plugin['rating'] : 0,
				'icon'            => $icon,
				'homepage'        => $homepage,
				'status'          => $status,
				'plugin_file'     => self::resolve_plugin_file( $slug, $plugin ),
				'can_install'     => current_user_can( 'install_plugins' ),
				'can_activate'    => current_user_can( 'activate_plugins' ),
			];
		}

		$listed = wp_list_pluck( $normalized, 'slug' );

		foreach ( self::get_premium_plugins() as $premium ) {
			$slug = $premium['slug'];

			// Skip anything the feed already covers, in case one of these
			// later ships a free version on wordpress.org.
			if ( in_array( $slug, $listed, true ) ) {
				continue;
			}

			$status = self::get_local_plugin_status( $slug, [] );

			$normalized[] = [
				'slug'            => $slug,
				'name'            => $premium['name'],
				'description'     => $premium['description'],
				'version'         => '',
				'active_installs' => '',
				'rating'          => 0,
				'icon'            => \ElementPack\SetupWizard\Plugin_Integration_Helper::plugin_logo_url( $slug ),
				'homepage'        => $premium['homepage'],
				// 'not_installed' would offer an Install button that cannot
				// work — there is nothing to pull from wordpress.org.
				'status'          => 'not_installed' === $status ? 'premium' : $status,
				'plugin_file'     => self::resolve_plugin_file( $slug, [] ),
				'can_install'     => false,
				'can_activate'    => current_user_can( 'activate_plugins' ),
			];
		}

		return [
			'plugins' => $normalized,
			'loading' => false,
			'message' => __( 'Plugin data loaded successfully.', 'bdthemes-element-pack-lite' ),
		];
	}

	/**
	 * @param string $plugin_slug
	 * @param bool   $auto_activate Whether to activate after a successful install.
	 * @return array<string,mixed>|\WP_Error
	 */
	public static function install( $plugin_slug, $auto_activate = true ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return new \WP_Error( 'element_pack_forbidden', __( 'You do not have permission to install plugins.', 'bdthemes-element-pack-lite' ), [ 'status' => 403 ] );
		}

		$plugin_slug = sanitize_text_field( $plugin_slug );

		if ( '' === $plugin_slug ) {
			return new \WP_Error( 'element_pack_invalid_slug', __( 'Plugin slug is required.', 'bdthemes-element-pack-lite' ) );
		}

		self::ensure_dependencies();

		// REST requests don't preload the wp-admin includes the upgrader
		// depends on (WP_Filesystem/wp_tempnam live in file.php, get_plugins
		// in plugin.php) — loading them here prevents a fatal mid-install.
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/misc.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';

		$api = plugins_api(
			'plugin_information',
			[
				'slug'   => $plugin_slug,
				'fields' => [ 'sections' => false ],
			]
		);

		if ( is_wp_error( $api ) ) {
			return new \WP_Error(
				'element_pack_plugin_not_found',
				sprintf(
					/* translators: %s: error message */
					__( 'Plugin not found: %s', 'bdthemes-element-pack-lite' ),
					$api->get_error_message()
				)
			);
		}

		$skin     = new \WP_Ajax_Upgrader_Skin();
		$upgrader = new \Plugin_Upgrader( $skin );
		$result   = $upgrader->install( $api->download_link );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( $skin->get_errors()->has_errors() ) {
			return new \WP_Error( 'element_pack_install_failed', implode( ' ', $skin->get_error_messages() ) );
		}

		if ( null === $result ) {
			return new \WP_Error( 'element_pack_install_failed', __( 'Installation failed.', 'bdthemes-element-pack-lite' ) );
		}

		$plugin_file = '';
		$install_status = install_plugin_install_status( $api );

		if ( is_array( $install_status ) && ! empty( $install_status['file'] ) ) {
			$plugin_file = (string) $install_status['file'];
		}

		if ( '' === $plugin_file ) {
			$plugin_file = self::resolve_plugin_file( $plugin_slug, [ 'slug' => $plugin_slug ] );
		}

		$activated = false;

		if ( $auto_activate && '' !== $plugin_file ) {
			$activate_result = self::activate( $plugin_slug, $plugin_file );

			if ( ! is_wp_error( $activate_result ) ) {
				$activated = true;
			}
		}

		return [
			'success'     => true,
			'message'     => $activated
				? __( 'Plugin installed and activated successfully.', 'bdthemes-element-pack-lite' )
				: __( 'Plugin installed successfully.', 'bdthemes-element-pack-lite' ),
			'slug'        => $plugin_slug,
			'plugin_file' => $plugin_file,
			'activated'   => $activated,
		];
	}

	/**
	 * @param string $plugin_slug
	 * @param string $plugin_file Optional plugin basename under wp-content/plugins.
	 * @return array<string,mixed>|\WP_Error
	 */
	public static function activate( $plugin_slug, $plugin_file = '' ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return new \WP_Error(
				'element_pack_forbidden',
				__( 'You do not have permission to activate plugins.', 'bdthemes-element-pack-lite' ),
				[ 'status' => 403 ]
			);
		}

		$plugin_slug = sanitize_key( $plugin_slug );

		if ( '' === $plugin_slug && '' === $plugin_file ) {
			return new \WP_Error(
				'element_pack_invalid_slug',
				__( 'Plugin slug is required.', 'bdthemes-element-pack-lite' )
			);
		}

		if ( '' === $plugin_file ) {
			self::ensure_dependencies();

			$plugins_data = \ElementPack\SetupWizard\Remote_Data_Handler::get_remote_plugins();
			$plugin       = is_array( $plugins_data ) && isset( $plugins_data[ $plugin_slug ] )
				? $plugins_data[ $plugin_slug ]
				: [ 'slug' => $plugin_slug ];

			$plugin_file = self::resolve_plugin_file( $plugin_slug, is_array( $plugin ) ? $plugin : [] );
		}

		$plugin_file = self::sanitize_plugin_file( $plugin_file );

		if ( '' === $plugin_file ) {
			return new \WP_Error(
				'element_pack_plugin_not_installed',
				__( 'Plugin is not installed.', 'bdthemes-element-pack-lite' ),
				[ 'status' => 404 ]
			);
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all = get_plugins();

		if ( ! isset( $all[ $plugin_file ] ) ) {
			return new \WP_Error(
				'element_pack_plugin_not_installed',
				__( 'Plugin is not installed.', 'bdthemes-element-pack-lite' ),
				[ 'status' => 404 ]
			);
		}

		if ( is_plugin_active( $plugin_file ) ) {
			return [
				'success'     => true,
				'message'     => __( 'Plugin is already active.', 'bdthemes-element-pack-lite' ),
				'slug'        => $plugin_slug,
				'plugin_file' => $plugin_file,
				'activated'   => true,
			];
		}

		if ( ! function_exists( 'activate_plugin' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$result = activate_plugin( $plugin_file );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return [
			'success'     => true,
			'message'     => __( 'Plugin activated successfully.', 'bdthemes-element-pack-lite' ),
			'slug'        => $plugin_slug,
			'plugin_file' => $plugin_file,
			'activated'   => true,
		];
	}

	/**
	 * @param string              $slug
	 * @param array<string,mixed> $plugin
	 * @return string active|installed|not_installed
	 */
	private static function get_local_plugin_status( $slug, array $plugin ) {
		$file = self::resolve_plugin_file( $slug, $plugin );

		if ( '' === $file ) {
			return 'not_installed';
		}

		if ( is_plugin_active( $file ) ) {
			return 'active';
		}

		return 'installed';
	}

	/**
	 * @param string              $slug
	 * @param array<string,mixed> $plugin
	 * @return string
	 */
	private static function resolve_plugin_file( $slug, array $plugin ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$file = isset( $plugin['file'] ) ? (string) $plugin['file'] : '';

		if ( '' === $file && '' !== $slug ) {
			$file = $slug . '/' . $slug . '.php';
		}

		$all = get_plugins();

		if ( isset( $all[ $file ] ) ) {
			return $file;
		}

		if ( '' !== $slug ) {
			foreach ( array_keys( $all ) as $path ) {
				if ( 0 === strpos( $path, $slug . '/' ) ) {
					return $path;
				}
			}
		}

		return '';
	}

	/**
	 * @param string $plugin_file
	 * @return string
	 */
	private static function sanitize_plugin_file( $plugin_file ) {
		$plugin_file = str_replace( '\\', '/', trim( (string) $plugin_file ) );

		if ( '' === $plugin_file || false !== strpos( $plugin_file, '..' ) ) {
			return '';
		}

		return $plugin_file;
	}

	/**
	 * @return void
	 */
	private static function ensure_dependencies() {
		foreach ( [ 'class-remote-data-handler.php', 'class-plugin-integration-helper.php' ] as $file ) {
			$path = BDTEP_INC_PATH . 'setup-wizard/' . $file;

			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}
}
