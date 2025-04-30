<?php

namespace ElementPack\Includes;

/**
 * Element_Pack_WPML class
 */

 if (!defined('ABSPATH')) exit; // Exit if accessed directly
class Element_Pack_WPML {

	/**
	 * A reference to an instance of this class.
	 * @since 3.1.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Constructor for the class
	 */
	public function init() {

		// WPML String Translation plugin exist check
		if ( defined( 'WPML_ST_VERSION' ) ) {
			add_filter( 'wpml_elementor_widgets_to_translate', array( $this, 'add_translatable_nodes' ) );
		}

	}

	/**
	 * Load wpml required repeater class files.
	 * @return void
	 */
	public function load_wpml_modules() {

		include_once( BDTEP_INC_PATH . 'compatiblity/wpml/wpml-module-with-items.php' );

		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-accordion.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-brand-grid.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-business-hours.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-custom-gallery.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-fancy-icons.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-fancy-list.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-icon-mobile-menu.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-image-accordion.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-image-stack.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-logo-grid.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-member.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-open-street-map.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-panel-slider.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-price-list.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-product-carousel.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-product-grid.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-review-card-carousel.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-slider.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-static-carousel.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-static-grid-tab.php' );
	}

	/**
	 * Add element pack translation nodes
	 * @param array $nodes_to_translate
	 * @return array
	 */
	public function add_translatable_nodes( $nodes_to_translate ) {

		$this->load_wpml_modules();

		$nodes_to_translate[ 'bdt-accordion' ] = [
			'conditions' => [ 'widgetType' => 'bdt-accordion' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\\WPML_ElementPack_Accordion',
		];

		$nodes_to_translate[ 'bdt-age-gate' ] = [
			'conditions' => [ 'widgetType' => 'bdt-age-gate' ],
			'fields'     => [
				[
					'field'       => 'form_placeholder',
					'type'        => esc_html__( 'Form Placeholder', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Button Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_text_yes',
					'type'        => esc_html__( 'Button Text (Yes)', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_text_no',
					'type'        => esc_html__( 'Button Text (No)', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'age_invalid_msg',
					'type'        => esc_html__( 'Age Invalid Message', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'header',
					'type'        => esc_html__( 'Header Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'content',
					'type'        => esc_html__( 'ModalContent', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'footer',
					'type'        => esc_html__( 'Footer Text', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
			],
		];

		$nodes_to_translate[ 'bdt-business-hours' ] = [
			'conditions' => [ 'widgetType' => 'bdt-business-hours' ],
			'fields'     => [
				[
					'field'       => 'dynamic_open_day',
					'type'        => esc_html__( 'Open Status', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'dynamic_close_day',
					'type'        => esc_html__( 'Close Status', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
			],
			'integration-class' => __NAMESPACE__ . '\\WPML_ElementPack_Business_Hours',
		];

		$nodes_to_translate[ 'bdt-call-out' ] = [
			'conditions' => [ 'widgetType' => 'bdt-call-out' ],
			'fields'     => [
				[
					'field'       => 'title',
					'type'        => esc_html__( 'Title', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description',
					'type'        => esc_html__( 'Description', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Button Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-contact-form' ] = [
			'conditions' => [ 'widgetType' => 'bdt-contact-form' ],
			'fields'     => [
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'user_name_label',
					'type'        => esc_html__( 'Label', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'user_name_placeholder',
					'type'        => esc_html__( 'Placeholder', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'contact_label',
					'type'        => esc_html__( 'Label', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'contact_placeholder',
					'type'        => esc_html__( 'Placeholder', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'subject_label',
					'type'        => esc_html__( 'Label', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'subject_placeholder',
					'type'        => esc_html__( 'Placeholder', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'email_address_label',
					'type'        => esc_html__( 'Label', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'email_placeholder',
					'type'        => esc_html__( 'Placeholder', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'message_label',
					'type'        => esc_html__( 'Label', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'message_placeholder',
					'type'        => esc_html__( 'Placeholder', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'additional_message',
					'type'        => esc_html__( 'Message', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-cookie-consent' ] = [
			'conditions' => [ 'widgetType' => 'bdt-cookie-consent' ],
			'fields'     => [
				[
					'field'       => 'message',
					'type'        => __( 'Message', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA'
				],
				[
					'field'       => 'learn_more_text',
					'type'        => __( 'Learn More Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'button_text',
					'type'        => __( 'Button Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE'
				],
			],
		];

		$nodes_to_translate[ 'bdt-custom-gallery' ] = [
			'conditions' => [ 'widgetType' => 'bdt-custom-gallery' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\\WPML_ElementPack_Custom_Gallery',
		];

		$nodes_to_translate[ 'bdt-dropbar' ] = [
			'conditions' => [ 'widgetType' => 'bdt-dropbar' ],
			'fields'     => [
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-dual-button' ] = [
			'conditions' => [ 'widgetType' => 'bdt-dual-button' ],
			'fields'     => [
				[
					'field'       => 'middle_text',
					'type'        => esc_html__( 'Middle Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_a_text',
					'type'        => esc_html__( 'Button A Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_a_onclick_event',
					'type'        => esc_html__( 'Button A OnClick Event', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_b_text',
					'type'        => esc_html__( 'Button B Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_b_onclick_event',
					'type'        => esc_html__( 'OnClick Event', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-featured-box' ] = [
			'conditions' => [ 'widgetType' => 'bdt-featured-box' ],
			'fields'     => [
				[
					'field'       => 'title_text',
					'type'        => esc_html__( 'Title', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'sub_title_text',
					'type'        => esc_html__( 'Sub Title', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_text',
					'type'        => esc_html__( 'Content', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'readmore_text',
					'type'        => esc_html__( 'Read More Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'badge_text',
					'type'        => esc_html__( 'Badge Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-step-flow' ] = [
			'conditions' => [ 'widgetType' => 'bdt-step-flow' ],
			'fields'     => [
				[
					'field'       => 'title_text',
					'type'        => esc_html__( 'Title', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_text',
					'type'        => esc_html__( 'Content', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'readmore_text',
					'type'        => esc_html__( 'Read More Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'badge_text',
					'type'        => esc_html__( 'Badge Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-svg-image' ] = [
			'conditions' => [ 'widgetType' => 'bdt-svg-image' ],
			'fields'     => [
				[
					'field'       => 'caption',
					'type'        => esc_html__( 'Custom Caption', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-fancy-icons' ] = [
			'conditions' => [ 'widgetType' => 'bdt-fancy-icons' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\\WPML_ElementPack_Fancy_Icons',
		];

		$nodes_to_translate[ 'bdt-fancy-list' ] = [
			'conditions' => [ 'widgetType' => 'bdt-fancy-list' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\\WPML_ElementPack_Fancy_List',
		];

		$nodes_to_translate[ 'bdt-image-accordion' ] = [
			'conditions' => [ 'widgetType' => 'bdt-image-accordion' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\\WPML_ElementPack_Image_Accordion',
		];

		$nodes_to_translate[ 'bdt-logo-grid' ] = [
			'conditions' => [ 'widgetType' => 'bdt-logo-grid' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\\WPML_ElementPack_Logo_Grid',
		];

		$nodes_to_translate[ 'bdt-flip-box' ] = [
			'conditions' => [ 'widgetType' => 'bdt-flip-box' ],
			'fields'     => [
				[
					'field'       => 'front_title_text',
					'type'        => esc_html__( 'Title', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'front_description_text',
					'type'        => esc_html__( 'Description', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'back_title_text',
					'type'        => esc_html__( 'Title', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'back_description_text',
					'type'        => esc_html__( 'Description', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Button Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-image-compare' ] = [
			'conditions' => [ 'widgetType' => 'bdt-image-compare' ],
			'fields'     => [
				[
					'field'       => 'before_label',
					'type'        => esc_html__( 'Before Label', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'after_label',
					'type'        => esc_html__( 'After Label', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'lightbox' ] = [
			'conditions' => [ 'widgetType' => 'lightbox' ],
			'fields'     => [
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Button Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'content_caption',
					'type'        => esc_html__( 'Content Caption', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-member' ] = [
			'conditions' => [ 'widgetType' => 'bdt-member' ],
			'fields'     => [
				[
					'field'       => 'name',
					'type'        => esc_html__( 'Member Name', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_text',
					'type'        => esc_html__( 'Member Description', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'role',
					'type'        => esc_html__( 'Member Role', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],

			],
			'integration-class' => __NAMESPACE__ . '\\WPML_ElementPack_Team_Member',
		];

		$nodes_to_translate[ 'bdt-open-street-map' ] = [
			'conditions' => [ 'widgetType' => 'bdt-open-street-map' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\\WPML_ElementPack_Open_Street_Map',
		];

		$nodes_to_translate[ 'bdt-panel-slider' ] = [
			'conditions' => [ 'widgetType' => 'bdt-panel-slider' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\\WPML_ElementPack_Panel_Slider',
		];

		$nodes_to_translate[ 'bdt-testimonial-grid' ] = [
			'conditions' => [ 'widgetType' => 'bdt-testimonial-grid' ],
			'fields'     => [
				[
					'field'       => 'filter_custom_text_all',
					'type'        => esc_html__( 'Custom Text (All)', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'filter_custom_text_filter',
					'type'        => esc_html__( 'Custom Text (Filter)', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-price-list' ] = [
			'conditions' => [ 'widgetType' => 'bdt-price-list' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\\WPML_ElementPack_Price_List',
		];

		$nodes_to_translate[ 'bdt-progress-pie' ] = [
			'conditions' => [ 'widgetType' => 'bdt-progress-pie' ],
			'fields'     => [
				[
					'field'       => 'percent',
					'type'        => esc_html__( 'Percent', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'title',
					'type'        => esc_html__( 'Progress Pie Title', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'before',
					'type'        => esc_html__( 'Before Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'text',
					'type'        => esc_html__( 'Middle Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'after',
					'type'        => esc_html__( 'After Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-scroll-button' ] = [
			'conditions' => [ 'widgetType' => 'bdt-scroll-button' ],
			'fields'     => [
				[
					'field'       => 'scroll_button_text',
					'type'        => esc_html__( 'Button Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'section_id',
					'type'        => esc_html__( 'Section ID', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-search' ] = [
			'conditions' => [ 'widgetType' => 'bdt-search' ],
			'fields'     => [
				[
					'field'       => 'placeholder',
					'type'        => esc_html__( 'Placeholder', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Search Button Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-slider' ] = [
			'conditions' => [ 'widgetType' => 'bdt-slider' ],
			'fields'     => [
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Button Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => __NAMESPACE__ . '\\WPML_ElementPack_Slider',
		];

		$nodes_to_translate[ 'bdt-toggle' ] = [
			'conditions' => [ 'widgetType' => 'bdt-toggle' ],
			'fields'     => [
				[
					'field'       => 'toggle_title',
					'type'        => esc_html__( 'Normal Title', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'toggle_open_title',
					'type'        => esc_html__( 'Opened Title', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'toggle_content',
					'type'        => esc_html__( 'Content', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
			],
		];

		$nodes_to_translate[ 'bdt-trailer-box' ] = [
			'conditions' => [ 'widgetType' => 'bdt-trailer-box' ],
			'fields'     => [
				[
					'field'       => 'pre_title',
					'type'        => esc_html__( 'Pre Title', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'title',
					'type'        => esc_html__( 'Title', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'content',
					'type'        => esc_html__( 'Content', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-user-register' ] = [
			'conditions' => [ 'widgetType' => 'bdt-user-register' ],
			'fields'     => [
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'first_name_label',
					'type'        => esc_html__( 'First Name Label', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'first_name_placeholder',
					'type'        => esc_html__( 'First Name Placeholder', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'last_name_label',
					'type'        => esc_html__( 'Last Name Label', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'last_name_placeholder',
					'type'        => esc_html__( 'Last Name Placeholder', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'email_label',
					'type'        => esc_html__( 'Email Label', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'email_placeholder',
					'type'        => esc_html__( 'Email Placeholder', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'additional_message',
					'type'        => esc_html__( 'Additional Message', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-static-carousel' ] = [
			'conditions' => [ 'widgetType' => 'bdt-static-carousel' ],
			'fields'     => [
				[
					'field'       => 'readmore_text',
					'type'        => esc_html__( 'Read More Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => __NAMESPACE__ . '\\WPML_ElementPack_Static_Carousel',
		];

		$nodes_to_translate[ 'bdt-dark-mode' ] = [
			'conditions' => [ 'widgetType' => 'bdt-dark-mode' ],
			'fields'     => [
				[
					'field'       => 'ignore_element',
					'type'        => __( 'Ignore Elements', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA'
				],
			],
		];

		$nodes_to_translate[ 'bdt-image-stack' ] = [
			'conditions' => [ 'widgetType' => 'bdt-image-stack' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\\WPML_ElementPack_Image_Stack',
		];

		$nodes_to_translate[ 'bdt-product-carousel' ] = [
			'conditions' => [ 'widgetType' => 'bdt-product-carousel' ],
			'fields'     => [
				[
					'field'       => 'readmore_text',
					'type'        => esc_html__( 'Read More Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => __NAMESPACE__ . '\\WPML_ElementPack_Product_Carousel',
		];

		$nodes_to_translate[ 'bdt-product-grid' ] = [
			'conditions' => [ 'widgetType' => 'bdt-product-grid' ],
			'fields'     => [
				[
					'field'       => 'readmore_text',
					'type'        => esc_html__( 'Read More Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => __NAMESPACE__ . '\\WPML_ElementPack_Product_Grid',
		];

		$nodes_to_translate[ 'bdt-review-card-carousel' ] = [
			'conditions' => [ 'widgetType' => 'bdt-review-card-carousel' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\\WPML_ElementPack_Review_Card_Carousel',
		];

		$nodes_to_translate[ 'bdt-review-card' ] = [
			'conditions' => [ 'widgetType' => 'bdt-review-card' ],
			'fields'     => [
				[
					'field'       => 'reviewer_name',
					'type'        => esc_html__( 'Reviewer Name', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'reviewer_job_title',
					'type'        => esc_html__( 'Reviewer Job Title', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'review_text',
					'type'        => esc_html__( 'Review Text', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
			],
		];

		$nodes_to_translate[ 'bdt-static-grid-tab' ] = [
			'conditions' => [ 'widgetType' => 'bdt-static-grid-tab' ],
			'fields'     => [
				[
					'field'       => 'title',
					'type'        => esc_html__('Title', 'bdthemes-element-pack'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'text',
					'type'        => esc_html__('Text', 'bdthemes-element-pack'),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'readmore_text',
					'type'        => esc_html__('Read More Text', 'bdthemes-element-pack'),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => __NAMESPACE__ . '\\WPML_ElementPack_Static_Grid_Tab',
		];

		$nodes_to_translate[ 'bdt-reading-timer' ] = [
			'conditions' => [ 'widgetType' => 'bdt-reading-timer' ],
			'fields'     => [
				[
					'field'       => 'reading_timer_minute_text',
					'type'        => esc_html__('Minute Text', 'bdthemes-element-pack'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'reading_timer_seconds_text',
					'type'        => esc_html__('Seconds Text', 'bdthemes-element-pack'),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-icon-mobile-menu' ] = [
			'conditions' => [ 'widgetType' => 'bdt-icon-mobile-menu' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\\WPML_ElementPack_Icon_Mobile_Menu',
		];

		$nodes_to_translate[ 'bdt-facebook-feed' ] = [
			'conditions' => [ 'widgetType' => 'bdt-facebook-feed' ],
			'fields'     => [
				[
					'field'       => 'read_more_text',
					'type'        => esc_html__( 'Read More Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-creative-button' ] = [
			'conditions' => [ 'widgetType' => 'bdt-creative-button' ],
			'fields'     => [
				[
					'field'       => 'text',
					'type'        => esc_html__( 'Button Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-calendly' ] = [
			'conditions' => [ 'widgetType' => 'bdt-calendly' ],
			'fields'     => [
				[
					'field'       => 'calendly_username',
					'type'        => esc_html__( 'Calendly Username', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-brand-grid' ] = [
			'conditions' => [ 'widgetType' => 'bdt-brand-grid' ],
			'fields'     => [],
			'integration-class' => __NAMESPACE__ . '\\WPML_ElementPack_Brand_Grid',
		];

		$nodes_to_translate[ 'bdt-animated-link' ] = [
			'conditions' => [ 'widgetType' => 'bdt-animated-link' ],
			'fields'     => [
				[
					'field'       => 'pre_heading',
					'type'        => esc_html__( 'Prefix Heading', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'animated_heading',
					'type'        => esc_html__( 'Animated Heading', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'post_heading',
					'type'        => esc_html__( 'Post Heading', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
			],
		];

		return $nodes_to_translate;
	}

	/**
	 * Returns the instance.
	 * @since  3.1.0
	 * @return object
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}
