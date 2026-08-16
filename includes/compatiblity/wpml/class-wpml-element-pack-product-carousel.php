<?php
namespace ElementPack\Includes\WPML;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Product Carousel widget integration
 */
class WPML_ElementPack_Product_Carousel extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'product_items';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [
			'title',
			'price',
			'text',
			'time',
			'rating_count',
			'badge_text',
		];
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'title':
				return esc_html__( 'Title', 'bdthemes-element-pack-lite' );

			case 'price':
				return esc_html__( 'Price', 'bdthemes-element-pack-lite' );

			case 'text':
				return esc_html__( 'Text', 'bdthemes-element-pack-lite' );

			case 'time':
				return esc_html__( 'Time', 'bdthemes-element-pack-lite' );

			case 'rating_count':
				return esc_html__( 'Rating Count', 'bdthemes-element-pack-lite' );

			case 'badge_text':
				return esc_html__( 'Badge Text', 'bdthemes-element-pack-lite' );

			default:
				return '';
		}
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'title':
			case 'price':
			case 'time':
			case 'rating_count':
			case 'badge_text':
				return 'LINE';

			case 'text':
				return 'AREA';

			default:
				return '';
		}
	}
} 