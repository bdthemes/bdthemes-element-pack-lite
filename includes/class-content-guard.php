<?php

namespace ElementPack\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Keeps UIkit component attributes out of content written by users who may not
 * publish arbitrary HTML.
 *
 * The bundled UIkit (assets/js/bdt-uikit.min.js) boots a component from any element
 * that carries a `bdt-<component>` or `data-bdt-<component>` attribute, wherever that
 * element sits in the document. wp_kses_post(), which WordPress and Elementor apply
 * to content saved by users without `unfiltered_html`, lets every `data-*`
 * attribute through, so a Contributor could place `data-bdt-svg="src: ..."` or
 * `data-bdt-lightbox-panel="..."` in a post, submit it for review, and have the
 * component run when an administrator opens the post on a page that loads the
 * bundle (one Element Pack widget in the same document is enough).
 *
 * This guard removes those attributes from markup on its way into the database
 * whenever the saving user lacks `unfiltered_html`: Elementor document saves (the
 * editor's widgets, including the Text Editor), classic and block editor content,
 * and comments. Users with `unfiltered_html` are trusted by WordPress to write any
 * HTML and are left alone, and the plugin's own PHP-rendered markup (which uses
 * `data-bdt-*` freely, see includes/helper.php) never passes through these filters.
 * The bundle itself also refuses non-http(s) SVG and lightbox iframe sources and
 * drops scripts, frames, forms, event handlers and script URLs from every fragment
 * it parses, so content stored before this guard existed cannot run script either.
 */
final class Content_Guard {

	/**
	 * A UIkit component attribute, with or without the `data-` prefix, inside a tag.
	 *
	 * @deprecated No longer used: stripping attributes with it was an XSS vector
	 * (its `[^\s>]+` branch can eat an enclosing attribute's closing quote).
	 * Kept only so anything that referenced the constant keeps working.
	 */
	const ATTRIBUTE_PATTERN = '/[\s\/]+(?:data-)?bdt-[^\s\/>=]*(?:\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+))?/i';

	public static function init() {
		// Runs before Elementor's own wp_kses_post() pass in Document::save().
		add_filter( 'elementor/document/save/data', [ __CLASS__, 'filter_elementor_save_data' ], 5 );

		// Classic and block editor content, before kses (priority 10).
		add_filter( 'content_save_pre', [ __CLASS__, 'filter_untrusted_content' ], 5 );
		add_filter( 'excerpt_save_pre', [ __CLASS__, 'filter_untrusted_content' ], 5 );

		// Comments, before kses (priority 10).
		add_filter( 'pre_comment_content', [ __CLASS__, 'filter_untrusted_content' ], 5 );
	}

	/**
	 * Whether the current user may write arbitrary HTML.
	 *
	 * @return bool
	 */
	public static function current_user_is_trusted() {
		return current_user_can( 'unfiltered_html' );
	}

	/**
	 * `elementor/document/save/data`: strip component attributes from every string
	 * in the document data (elements, their settings, page settings) for users
	 * without `unfiltered_html`.
	 *
	 * @param mixed $data Document data.
	 * @return mixed
	 */
	public static function filter_elementor_save_data( $data ) {
		if ( self::current_user_is_trusted() ) {
			return $data;
		}

		return map_deep( $data, [ __CLASS__, 'strip_from_value' ] );
	}

	/**
	 * `content_save_pre`, `excerpt_save_pre`, `pre_comment_content`.
	 *
	 * @param mixed $content Content being saved.
	 * @return mixed
	 */
	public static function filter_untrusted_content( $content ) {
		if ( self::current_user_is_trusted() || ! is_string( $content ) ) {
			return $content;
		}

		// These filters carry slashed data, like wp_filter_post_kses() they run next to.
		return addslashes( self::strip_from_value( stripslashes( $content ) ) );
	}

	/**
	 * Strip component attributes from a value if it is a string holding markup.
	 *
	 * @param mixed $value Any value; only strings containing a tag are changed.
	 * @return mixed
	 */
	public static function strip_from_value( $value ) {
		if ( ! is_string( $value ) || false === strpos( $value, '<' ) ) {
			return $value;
		}

		return self::strip_component_attributes( $value );
	}

	/**
	 * Remove `bdt-*` and `data-bdt-*` attributes from the tags in a piece of HTML.
	 *
	 * The markup is walked with WordPress's HTML tag processor, the same parser
	 * wp_kses_post() uses to normalise a tag, so every attribute form it accepts is
	 * seen here too, including an attribute that follows a `/` with no whitespace
	 * (`<span class="a"/data-bdt-svg="...">`), which kses would otherwise turn into a
	 * clean `data-bdt-svg` attribute. Only attribute names are compared: the words
	 * of a paragraph and other attributes are never changed. The whitespace left
	 * where an attribute stood is tidied by kses on the same save.
	 *
	 * @param string $html Markup.
	 * @return string
	 */
	public static function strip_component_attributes( $html ) {
		if ( false === stripos( $html, 'bdt-' ) && false === stripos( $html, 'attrs' ) ) {
			return $html;
		}

		if ( ! class_exists( '\WP_HTML_Tag_Processor' ) ) {
			return self::strip_component_attributes_by_pattern( $html );
		}

		$processor = new \WP_HTML_Tag_Processor( $html );

		while ( $processor->next_tag() ) {
			foreach ( (array) $processor->get_attribute_names_with_prefix( '' ) as $name ) {
				if ( self::is_removable_attribute( $name ) ) {
					$processor->remove_attribute( $name );
				}
			}
		}

		return $processor->get_updated_html();
	}

	/**
	 * Whether an attribute should be removed from untrusted markup.
	 *
	 * Either a `bdt-*` component attribute or UIkit's generic `attrs` carrier,
	 * which applies its value as arbitrary attributes on the element. Neither is
	 * ever emitted by a widget into content that passes through these filters.
	 *
	 * @param string $name Attribute name.
	 * @return bool
	 */
	public static function is_removable_attribute( $name ) {
		if ( self::is_component_attribute( $name ) ) {
			return true;
		}

		return function_exists( 'element_pack_uikit_attrs_attribute_name' )
			&& \element_pack_uikit_attrs_attribute_name( $name );
	}

	/**
	 * Whether an attribute name boots a UIkit component.
	 *
	 * UIkit drops a leading `data-` and then matches the `bdt-` prefix, so both
	 * forms count. Compared case-insensitively, as HTML attribute names are.
	 *
	 * @param string $name Attribute name.
	 * @return bool
	 */
	public static function is_component_attribute( $name ) {
		$name = strtolower( (string) $name );

		if ( 0 === strpos( $name, 'data-' ) ) {
			$name = substr( $name, 5 );
		}

		return 0 === strpos( $name, 'bdt-' );
	}

	/**
	 * Fallback for an environment without the HTML API (WordPress < 6.2, below this
	 * plugin's minimum): leave the markup unchanged.
	 *
	 * This used to strip attributes with ATTRIBUTE_PATTERN, but no pattern can tell
	 * where a browser considers an attribute value to start and end. Its `[^\s>]+`
	 * branch could eat an enclosing attribute's closing quote, collapsing the
	 * attribute boundary and promoting inert text into a live event handler: the
	 * same defect that made the `attrs=` pattern in element-pack-filters.php an XSS
	 * vector. A hand-written quote-aware scanner was tried in its place, and fuzzing
	 * it against WP_HTML_Tag_Processor still found inputs it re-armed. An unchanged
	 * document can never gain an event handler, and kses still runs on the save.
	 *
	 * Kept as a method so the call site and its tests stay stable.
	 *
	 * @param string $html Markup.
	 * @return string
	 */
	private static function strip_component_attributes_by_pattern( $html ) {
		return $html;
	}
}
