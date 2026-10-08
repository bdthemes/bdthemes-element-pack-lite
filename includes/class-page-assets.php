<?php

namespace ElementPack\Includes;

use Elementor\Plugin;
use ElementPack\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Decides when Element Pack's shared UIkit/helper assets load, and combines the
 * per-widget assets of a page into a single CSS and a single JS file.
 *
 * Shared assets: UIkit (CSS + JS), the helper CSS and the helper JS used to load on
 * every page of the site. They now load only when an Element Pack widget is on the
 * page: widget handles depend on them, and widgets that do not declare their own
 * handles request them as they render. The Elementor editor and preview always load
 * them.
 *
 * Combined files (Asset Manager on): each widget registers its own small CSS/JS
 * file. Just before WordPress prints a batch of styles or scripts, the Element Pack
 * widget files in that batch are replaced by one file built from exactly those
 * widgets. The file is named after its contents, so every page that uses the same
 * widgets shares one cached file.
 */
class Page_Assets {

	/**
	 * Shared stylesheets that stay separate so one cached copy serves every page.
	 */
	const STYLE_EXCLUDE = [ 'ep-helper.css', 'ep-styles.css', 'ep-editor.css', 'ep-preview.css' ];

	const MAX_FILES = 200;

	public static function init() {
		add_action( 'elementor/frontend/widget/before_render', [ __CLASS__, 'enqueue_base_on_render' ] );

		if ( function_exists( 'element_pack_is_asset_optimization_enabled' ) && element_pack_is_asset_optimization_enabled() ) {
			add_filter( 'print_styles_array', [ __CLASS__, 'combine_styles' ], 99 );
			add_filter( 'print_scripts_array', [ __CLASS__, 'combine_scripts' ], 99 );
		}
	}

	/**
	 * True inside the Elementor editor and its preview iframe.
	 */
	public static function is_editor() {
		$elementor = Plugin::$instance;

		if ( ! $elementor ) {
			return false;
		}

		return $elementor->preview->is_preview_mode() || $elementor->editor->is_edit_mode();
	}

	/**
	 * Enqueue UIkit and the helper assets. Safe to call repeatedly.
	 */
	public static function enqueue_base() {
		wp_enqueue_style( 'bdt-uikit' );
		wp_enqueue_style( 'ep-helper' );
		wp_enqueue_script( 'bdt-uikit' );
		wp_enqueue_script( 'element-pack-helper' );
	}

	/**
	 * Safety net for widgets that do not declare a style or script handle of their
	 * own: the first Element Pack widget to render pulls in the shared assets.
	 *
	 * @param \Elementor\Widget_Base $widget
	 */
	public static function enqueue_base_on_render( $widget ) {
		static $done = false;

		if ( $done || ! $widget instanceof Module_Base ) {
			return;
		}

		$done = true;
		self::enqueue_base();
	}

	/* ---------------------------------------------------------------------
	 * Combining
	 * ------------------------------------------------------------------ */

	/**
	 * Only act while WordPress prints the front end: the filters also run when other
	 * code resolves dependencies, and combining marks handles as printed.
	 */
	private static function is_printing_front_end() {
		if ( is_admin() || ! ( doing_action( 'wp_head' ) || doing_action( 'wp_footer' ) ) ) {
			return false;
		}

		return ! self::is_editor();
	}

	public static function combine_styles( $handles ) {
		global $wp_styles;

		if ( ! is_array( $handles ) || ! $wp_styles instanceof \WP_Styles || ! self::is_printing_front_end() ) {
			return $handles;
		}

		$items = [];

		foreach ( $handles as $handle ) {
			$path = self::style_path( $wp_styles->registered[ $handle ] ?? null );

			if ( $path ) {
				$items[ $handle ] = $path;
			}
		}

		if ( count( $items ) < 2 ) {
			return $handles;
		}

		$combined = self::build( 'css', $items );

		if ( ! $combined ) {
			return $handles;
		}

		if ( ! isset( $wp_styles->registered[ $combined['handle'] ] ) ) {
			wp_register_style( $combined['handle'], $combined['url'], [], null );
		}

		self::merge_inline_data( $wp_styles, $combined['handle'], array_keys( $items ), [ 'after' ] );

		// Cascade order matters for CSS, so the combined file takes the first slot.
		return self::swap( $handles, array_keys( $items ), $combined['handle'], $wp_styles, false );
	}

	public static function combine_scripts( $handles ) {
		global $wp_scripts;

		// Module scripts live in the footer, so only the footer batch is combined.
		if ( ! is_array( $handles ) || ! $wp_scripts instanceof \WP_Scripts || ! doing_action( 'wp_footer' ) || ! self::is_printing_front_end() ) {
			return $handles;
		}

		$items = [];

		foreach ( $handles as $handle ) {
			$path = self::script_path( $wp_scripts->registered[ $handle ] ?? null );

			if ( $path ) {
				$items[ $handle ] = $path;
			}
		}

		if ( count( $items ) < 2 ) {
			return $handles;
		}

		// The helper has to run before the widget scripts that use it.
		uksort( $items, static function ( $a, $b ) {
			return ( 'element-pack-helper' === $b ) <=> ( 'element-pack-helper' === $a );
		} );

		$combined = self::build( 'js', $items );

		if ( ! $combined ) {
			return $handles;
		}

		if ( ! isset( $wp_scripts->registered[ $combined['handle'] ] ) ) {
			wp_register_script( $combined['handle'], $combined['url'], [ 'jquery', 'bdt-uikit' ], null, true );
		}

		self::merge_inline_data( $wp_scripts, $combined['handle'], array_keys( $items ), [ 'before', 'after', 'data' ] );

		// Scripts may depend on libraries that load between them, so the combined
		// file takes the last slot.
		return self::swap( $handles, array_keys( $items ), $combined['handle'], $wp_scripts, true );
	}

	/**
	 * @param \_WP_Dependency|null $dependency
	 * @return string|null Absolute path of a combinable Element Pack stylesheet.
	 */
	private static function style_path( $dependency ) {
		if ( ! $dependency || ! is_string( $dependency->src ) || 'all' !== $dependency->args || ! empty( $dependency->extra['conditional'] ) ) {
			return null;
		}

		$src = strtok( $dependency->src, '?' );

		if ( 0 !== strpos( $src, BDTEP_URL . 'assets/css/ep-' ) || '.css' !== substr( $src, -4 ) ) {
			return null;
		}

		if ( in_array( basename( $src ), self::STYLE_EXCLUDE, true ) ) {
			return null;
		}

		return self::local_path( $src );
	}

	/**
	 * @param \_WP_Dependency|null $dependency
	 * @return string|null Absolute path of a combinable Element Pack script.
	 */
	private static function script_path( $dependency ) {
		if ( ! $dependency || ! is_string( $dependency->src ) || ( $dependency->extra['group'] ?? 0 ) < 1 || ! empty( $dependency->extra['strategy'] ) ) {
			return null;
		}

		$src = strtok( $dependency->src, '?' );

		$is_module = 0 === strpos( $src, BDTEP_URL . 'assets/js/modules/ep-' ) && '.min.js' === substr( $src, -7 );
		$is_helper = BDTEP_URL . 'assets/js/common/helper.min.js' === $src;

		if ( ! $is_module && ! $is_helper ) {
			return null;
		}

		return self::local_path( $src );
	}

	private static function local_path( $url ) {
		$path = BDTEP_PATH . substr( $url, strlen( BDTEP_URL ) );

		return is_readable( $path ) ? $path : null;
	}

	/**
	 * Build (or reuse) the combined file for a set of source files.
	 *
	 * @param string $type  'css' or 'js'.
	 * @param array  $items Handle => absolute source path, in output order.
	 * @return array|null {handle, url}, or null when the file could not be written.
	 */
	private static function build( $type, array $items ) {
		$fingerprint = BDTEP_VER;

		foreach ( $items as $handle => $path ) {
			$fingerprint .= '|' . $handle . ':' . filemtime( $path );
		}

		$name = 'ep-' . substr( md5( $fingerprint ), 0, 16 ) . '.' . $type;
		$file = Asset_Cache::pages_dir() . $name;
		$ok   = file_exists( $file ) || self::write( $type, $file, array_values( $items ) );

		if ( ! $ok ) {
			return null;
		}

		return [
			'handle' => 'ep-page-' . $type . '-' . substr( $name, 3, 16 ),
			'url'    => Asset_Cache::pages_url() . $name,
		];
	}

	private static function write( $type, $file, array $paths ) {
		$dir = dirname( $file );

		if ( ! wp_mkdir_p( $dir ) ) {
			return false;
		}

		// Combinations accumulate as widgets change; start over rather than grow.
		$existing = glob( $dir . '/ep-*' );

		if ( is_array( $existing ) && count( $existing ) > self::MAX_FILES ) {
			Asset_Cache::delete_page_files();
		}

		$temp = $file . '.' . wp_generate_password( 8, false ) . '.tmp';

		if ( 'css' === $type ) {
			if ( ! class_exists( '\MatthiasMullie\Minify\CSS' ) ) {
				return false;
			}

			// Rewrites relative url() references for the new location.
			$minifier = new \MatthiasMullie\Minify\CSS();

			foreach ( $paths as $path ) {
				$minifier->add( $path );
			}

			$minifier->minify( $temp );
		} else {
			// Module scripts ship minified already, so they are only joined.
			$parts = [];

			foreach ( $paths as $path ) {
				$parts[] = trim( (string) file_get_contents( $path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reads a bundled local asset.
			}

			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_put_contents_file_put_contents -- Writes a generated asset into uploads.
			file_put_contents( $temp, implode( ";\n", $parts ) . "\n" );
		}

		if ( ! file_exists( $temp ) || ! @rename( $temp, $file ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.rename_rename -- Atomic publish; a lost race just reuses the other request's identical file.
			wp_delete_file( $temp );

			return file_exists( $file );
		}

		return true;
	}

	/**
	 * Carry inline code and localized data of the replaced handles over to the
	 * combined handle so nothing attached to them is lost.
	 *
	 * @param \WP_Dependencies $registry
	 * @param string           $target
	 * @param string[]         $sources
	 * @param string[]         $keys
	 */
	private static function merge_inline_data( $registry, $target, array $sources, array $keys ) {
		foreach ( $keys as $key ) {
			$merged = [];

			foreach ( $sources as $source ) {
				$value = $registry->get_data( $source, $key );

				if ( empty( $value ) ) {
					continue;
				}

				$merged = is_array( $value ) ? array_merge( $merged, $value ) : array_merge( $merged, [ $value ] );
			}

			if ( $merged ) {
				$registry->add_data( $target, $key, 'data' === $key ? implode( "\n", $merged ) : $merged );
			}
		}
	}

	/**
	 * Replace the combined handles in a print list with the single combined handle
	 * and mark the originals as printed so later batches skip them.
	 */
	private static function swap( array $handles, array $removed, $combined, $registry, $use_last_slot ) {
		$registry->done = array_merge( $registry->done, $removed );

		$positions = array_keys( array_intersect( $handles, $removed ) );
		$slot      = $use_last_slot ? end( $positions ) : reset( $positions );
		$result    = [];

		foreach ( $handles as $index => $handle ) {
			if ( $index === $slot ) {
				$result[] = $combined;
			}

			if ( ! in_array( $handle, $removed, true ) ) {
				$result[] = $handle;
			}
		}

		return $result;
	}
}
