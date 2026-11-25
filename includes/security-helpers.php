<?php
/**
 * Small, dependency-free helpers that guard widget-controlled input.
 *
 * Kept out of helper.php (which defines the plugin constants on load) so the unit
 * suite can load them directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'element_pack_can_render_template' ) ) {
	/**
	 * Whether the current viewer may see the Elementor template/post $id rendered inline.
	 *
	 * Template IDs come from widget settings and shortcode attributes, which any
	 * Elementor user can set to any number. Elementor's renderer only skips
	 * password-protected posts, so without this check a Contributor could render
	 * someone else's private page or draft. Published content is fine for everyone;
	 * anything else needs read access for the person viewing the page.
	 *
	 * @param int|string $id Post ID.
	 * @return bool
	 */
	function element_pack_can_render_template( $id ) {
		$id = absint( $id );

		if ( ! $id || ! get_post( $id ) ) {
			return false;
		}

		if ( 'publish' === get_post_status( $id ) && ! post_password_required( $id ) ) {
			return true;
		}

		return (bool) apply_filters( 'element_pack/template/can_render', current_user_can( 'read_post', $id ), $id );
	}
}

if ( ! function_exists( 'element_pack_render_template' ) ) {
	/**
	 * Render an Elementor template/post by ID, if the viewer may see it.
	 *
	 * Drop-in for `Plugin::$instance->frontend->get_builder_content_for_display()` wherever
	 * the ID comes from a widget setting or a shortcode attribute.
	 *
	 * @param int|string $id       Post ID.
	 * @param bool       $with_css Whether to print the template's CSS inline.
	 * @return string
	 */
	function element_pack_render_template( $id, $with_css = true ) {
		if ( ! element_pack_can_render_template( $id ) ) {
			return '';
		}

		return \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( absint( $id ), $with_css );
	}
}

if ( ! function_exists( 'element_pack_divider_svg' ) ) {
	/**
	 * Read one of the bundled divider SVGs (assets/images/divider/<name>.svg).
	 *
	 * The name comes from a widget's separator-type setting. Elementor does not check a
	 * SELECT value against its options when a document is saved, so the name is caller
	 * controlled: only a bare file name is accepted, the resolved path must stay inside
	 * the divider folder, and the file is read, never included as PHP.
	 *
	 * @param string $name Divider name, e.g. 'wave'.
	 * @return string SVG markup, or '' when the name is not a bundled divider.
	 */
	function element_pack_divider_svg( $name ) {
		if ( ! is_string( $name ) || '' === $name || preg_match( '/[^a-zA-Z0-9_-]/', $name ) ) {
			return '';
		}

		$base = realpath( BDTEP_ASSETS_PATH . 'images/divider' );
		$path = realpath( BDTEP_ASSETS_PATH . 'images/divider/' . $name . '.svg' );

		if ( false === $base || false === $path || 0 !== strpos( $path, $base . DIRECTORY_SEPARATOR ) || ! is_file( $path ) ) {
			return '';
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local bundled asset.
		$svg = file_get_contents( $path );

		return is_string( $svg ) ? $svg : '';
	}
}

if ( ! function_exists( 'element_pack_escape_shortcode_brackets' ) ) {
	/**
	 * Turn the square brackets in visitor-supplied text into HTML entities.
	 *
	 * Elementor output is part of the_content, and core runs do_shortcode() on it
	 * afterwards, including inside quoted attribute values. Text taken from the
	 * request (the search term, a URL parameter) that is printed into that output
	 * must therefore not keep a literal [ or ], or a visitor can run any registered
	 * shortcode, e.g. ?s=[gallery include=123]. The entities display as the same
	 * characters, and esc_attr()/esc_html() leave them as they are.
	 *
	 * @param string $text Text from the request.
	 * @return string
	 */
	function element_pack_escape_shortcode_brackets( $text ) {
		return str_replace( [ '[', ']' ], [ '&#91;', '&#93;' ], (string) $text );
	}
}
