<?php

namespace ElementPack\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clears the cached asset data Element Pack depends on.
 *
 * Two things are cached:
 *
 * - Elementor stores the style and script handles each page needs in the
 *   `_elementor_page_assets` post meta the first time the page renders. After an
 *   Element Pack update or a module change that list can be stale, and Elementor
 *   would keep loading it until "Regenerate CSS & Data" is run by hand.
 * - Page_Assets writes one combined CSS and JS file per set of widgets into the
 *   uploads folder.
 *
 * Both are rebuilt automatically on the next page view, so clearing is cheap.
 */
class Asset_Cache {

	const VERSION_OPTION = 'element_pack_assets_version';

	/**
	 * Options whose change can alter which widgets or assets are available.
	 */
	const WATCHED_OPTIONS = [
		'element_pack_active_modules',
		'element_pack_elementor_extend',
		'element_pack_third_party_widget',
		'element_pack_other_settings',
	];

	public static function init() {
		add_action( 'init', [ __CLASS__, 'maybe_clear_after_update' ], 20 );

		foreach ( self::WATCHED_OPTIONS as $option ) {
			add_action( 'update_option_' . $option, [ __CLASS__, 'clear' ] );
			add_action( 'add_option_' . $option, [ __CLASS__, 'clear' ] );
		}

		// Third party widgets appear and disappear with their host plugin.
		add_action( 'activated_plugin', [ __CLASS__, 'clear' ] );
		add_action( 'deactivated_plugin', [ __CLASS__, 'clear' ] );
	}

	/**
	 * Clears once on the first request after the plugin version changes.
	 */
	public static function maybe_clear_after_update() {
		if ( BDTEP_VER === get_option( self::VERSION_OPTION ) ) {
			return;
		}

		self::clear();
		update_option( self::VERSION_OPTION, BDTEP_VER, false );
	}

	/**
	 * Folder that holds the combined per-page files.
	 *
	 * @return string Absolute path with a trailing slash.
	 */
	public static function pages_dir() {
		return trailingslashit( wp_upload_dir()['basedir'] ) . 'element-pack/minified/pages/';
	}

	/**
	 * @return string URL with a trailing slash.
	 */
	public static function pages_url() {
		return trailingslashit( wp_upload_dir()['baseurl'] ) . 'element-pack/minified/pages/';
	}

	public static function clear() {
		static $cleared = false;

		if ( $cleared ) {
			return;
		}

		$cleared = true;

		$meta_key = class_exists( '\Elementor\Core\Base\Elements_Iteration_Actions\Assets' )
			? \Elementor\Core\Base\Elements_Iteration_Actions\Assets::ASSETS_META_KEY
			: '_elementor_page_assets';

		delete_post_meta_by_key( $meta_key );

		self::delete_page_files();
		self::delete_editor_bundles();

		do_action( 'elementpack/assets/cache_cleared' );
	}

	/**
	 * The editor's all-widgets bundles in uploads. Without them the editor falls back
	 * to the copies shipped with the plugin, and the Asset Manager rebuilds them when
	 * the module settings are saved.
	 */
	private static function delete_editor_bundles() {
		$base = trailingslashit( wp_upload_dir()['basedir'] ) . 'element-pack/minified/';

		foreach ( [ 'css/ep-styles.css', 'js/ep-scripts.js' ] as $bundle ) {
			if ( is_file( $base . $bundle ) ) {
				wp_delete_file( $base . $bundle );
			}
		}
	}

	public static function delete_page_files() {
		$files = glob( self::pages_dir() . '*' );

		if ( ! is_array( $files ) ) {
			return;
		}

		foreach ( $files as $file ) {
			if ( is_file( $file ) ) {
				wp_delete_file( $file );
			}
		}
	}
}
