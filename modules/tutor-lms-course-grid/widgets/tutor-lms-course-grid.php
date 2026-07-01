<?php

namespace ElementPack\Modules\TutorLmsCourseGrid\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;

use ElementPack\Traits\Global_Controls_Functions;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TutorLms_Course_Grid extends Module_Base {
	use Group_Control_Query;
	use Global_Controls_Functions;

	private $_query = null;

	public function get_name() {
		return 'bdt-tutor-lms-course-grid';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Tutor LMS Course Grid', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-tutor-lms-course-grid';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'tutor', 'elearning', 'lms', 'course', 'course grid' ];
	}

	public function get_style_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'ep-styles' ];
		}
		return [ 'ep-tutor-lms' ];
	}

	public function get_script_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'tilt', 'ep-scripts' ];
		}
		return [ 'tilt', 'ep-tutor-lms-course-grid' ];
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
		return 'https://youtu.be/WWCE-_Po1uo';
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

		$this->add_responsive_control(
			'columns',
			[
				'label'              => esc_html__( 'Columns', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '3',
				'tablet_default'     => '2',
				'mobile_default'     => '1',
				'options'            => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label' => esc_html__( 'Pagination', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->register_tutor_lms_course_content_controls( 'grid' );

		$this->add_control(
			'tilt_show',
			[
				'label' => esc_html__( 'Tilt Effect', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'tilt_scale',
			[
				'label'     => esc_html__( 'Zoom on Hover', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'tilt_show' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->register_tutor_lms_price_cart_controls();
		$this->register_tutor_lms_query_controls();
		$this->register_tutor_lms_style_item_controls( 'grid' );
		$this->register_tutor_lms_style_header_controls();
		$this->register_tutor_lms_style_content_area_controls();
		$this->register_tutor_lms_style_footer_area_controls();
		$this->register_tutor_lms_style_cart_button_controls( 'grid' );
		$this->register_tutor_lms_style_pagination_controls();
	}

	public function query_posts( $posts_per_page ) {
		$this->query_tutor_lms_posts( $posts_per_page );
	}

	public function render_header() {
		$settings = $this->get_settings_for_display();
		$id       = 'bdt-tutor-lms-course-grid' . $this->get_id();

		$this->add_render_attribute( 'courses', 'id', esc_attr( $id ) );
		$this->add_render_attribute( 'courses', 'class', [ 'bdt-tutor-lms-course-grid' ] );
		$this->add_render_attribute( 'courses', 'bdt-grid', '' );
		$this->add_render_attribute( 'courses', 'class', [ 'bdt-grid', 'bdt-grid-medium' ] );

		$columns_mobile = isset( $settings['columns_mobile'] ) ? $settings['columns_mobile'] : 1;
		$columns_tablet = isset( $settings['columns_tablet'] ) ? $settings['columns_tablet'] : 2;
		$columns        = isset( $settings['columns'] ) ? $settings['columns'] : 3;

		$this->add_render_attribute( 'courses', 'class', 'bdt-child-width-1-' . esc_attr( $columns_mobile ) );
		$this->add_render_attribute( 'courses', 'class', 'bdt-child-width-1-' . esc_attr( $columns_tablet ) . '@s' );
		$this->add_render_attribute( 'courses', 'class', 'bdt-child-width-1-' . esc_attr( $columns ) . '@m' );

		$this->add_render_attribute(
			[
				'courses' => [
					'data-settings' => [
						wp_json_encode(
							[
								'id'       => '#' . $id,
								'tiltShow' => ( isset( $settings['tilt_show'] ) && 'yes' === $settings['tilt_show'] ),
							]
						),
					],
				],
			]
		);
		?>
		<div <?php $this->print_render_attribute_string( 'courses' ); ?>>
		<?php
	}

	public function render_footer() {
		?>
		</div>
		<?php
	}

	public function render() {
		$settings = $this->get_settings_for_display();
		$this->render_tutor_lms_grid( $settings );
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
		$this->render_tutor_lms_post( 'grid' );
	}
}
