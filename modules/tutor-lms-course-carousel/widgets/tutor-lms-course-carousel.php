<?php

namespace ElementPack\Modules\TutorLmsCourseCarousel\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;

use ElementPack\Traits\Global_Swiper_Controls;
use ElementPack\Traits\Global_Controls_Functions;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TutorLms_Course_Carousel extends Module_Base {
	use Global_Swiper_Controls;
	use Group_Control_Query;
	use Global_Controls_Functions;

	private $_query = null;

	public function get_name() {
		return 'bdt-tutor-lms-course-carousel';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Tutor LMS Course Carousel', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-tutor-lms-course-carousel';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'tutor', 'elearning', 'lms', 'course', 'course carousel', 'carousel' ];
	}

	public function get_style_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'swiper', 'ep-styles' ];
		}
		return [ 'swiper', 'ep-font', 'ep-tutor-lms' ];
	}

	public function get_script_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'swiper', 'ep-scripts' ];
		}
		return [ 'swiper', 'ep-tutor-lms-course-carousel' ];
	}

	public function on_import( $element ) {
		if ( ! get_post_type_object( $element['settings']['posts_post_type'] ) ) {
			$element['settings']['posts_post_type'] = 'post';
		}
		return $element;
	}

	public function get_query() {
		return $this->_query;
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/VYrIYQESjXs';
	}

	public function has_widget_inner_wrapper(): bool {
		return ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	public function register_controls() {
		$this->register_section_controls();
	}

	private function register_section_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__( 'Layout', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->register_carousel_column_gap_controls();
		$this->register_tutor_lms_course_content_controls( 'carousel' );
		$this->end_controls_section();

		$this->register_tutor_lms_price_cart_controls();
		$this->register_tutor_lms_query_controls();

		$this->start_controls_section(
			'section_content_navigation',
			[
				'label' => __( 'Navigation', 'bdthemes-element-pack' ),
			]
		);
		$this->register_navigation_controls();
		$this->end_controls_section();

		$this->register_carousel_settings_controls();

		$this->register_tutor_lms_style_item_controls( 'carousel' );
		$this->register_tutor_lms_style_header_controls();
		$this->register_tutor_lms_style_content_area_controls();
		$this->register_tutor_lms_style_footer_area_controls();
		$this->register_tutor_lms_style_cart_button_controls( 'carousel' );

		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'      => __( 'Navigation', 'bdthemes-element-pack' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'navigation',
							'operator' => '!=',
							'value'    => 'none',
						],
						[
							'name'  => 'show_scrollbar',
							'value' => 'yes',
						],
					],
				],
			]
		);
		$this->register_navigation_style_controls( 'swiper-carousel' );
		$this->end_controls_section();
	}

	public function query_posts( $posts_per_page ) {
		$this->query_tutor_lms_posts( $posts_per_page );
	}

	public function render_header() {
		$this->render_swiper_header_attribute( 'tutor-course' );
		$this->add_render_attribute( 'carousel', 'class', [ 'bdt-tutor-lms-course-carousel', 'bdt-tutor-course' ] );
		$this->add_render_attribute( 'tutor-courses-wrapper', 'class', 'swiper-wrapper' );
		?>
		<div <?php $this->print_render_attribute_string( 'carousel' ); ?>>
			<div <?php $this->print_render_attribute_string( 'swiper' ); ?>>
				<div <?php $this->print_render_attribute_string( 'tutor-courses-wrapper' ); ?>>
		<?php
	}

	public function render() {
		$settings = $this->get_settings_for_display();
		$this->render_tutor_lms_carousel( $settings );
	}

	public function render_thumbnail() {
		$this->render_tutor_lms_thumbnail();
	}

	public function render_title() {
		$this->render_tutor_lms_title();
	}

	public function render_meta() {
		$this->render_tutor_lms_meta();
	}

	public function render_rating() {
		$this->render_tutor_lms_rating();
	}

	public function render_price() {
		$this->render_tutor_lms_price();
	}

	public function render_desc() {
		$this->render_tutor_lms_desc();
	}

	public function render_post() {
		$this->render_tutor_lms_post( 'carousel' );
	}
}
