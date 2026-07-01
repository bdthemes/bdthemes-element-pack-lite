<?php

namespace ElementPack\Modules\TestimonialGrid\Widgets;

use ElementPack\Base\Module_Base;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;
use ElementPack\Traits\Global_Controls_Functions;
use ElementPack\Traits\Global_Widget_Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Testimonial_Grid extends Module_Base {
	use Group_Control_Query;
	use Global_Widget_Controls;
	use Global_Controls_Functions;

	private $_query = null;

	public function get_name() {
		return 'bdt-testimonial-grid';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Testimonial Grid', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-testimonial-grid';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'testimonial', 'grid' ];
	}

	public function get_style_depends() {
		return $this->ep_is_edit_mode() ? [ 'ep-styles' ] : [ 'ep-font', 'ep-testimonial-grid' ];
	}

	public function get_script_depends() {
		return $this->ep_is_edit_mode() ? [ 'ep-scripts' ] : [ 'ep-text-read-more-toggle' ];
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/pYMTXyDn8g4';
	}

	public function get_query() {
		return $this->_query;
	}

	public function has_widget_inner_wrapper(): bool {
		return ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	public function register_controls() {
		$this->register_testimonial_controls( 'grid' );
	}

	public function get_taxonomies( $post_type = '' ) {
		return $this->get_testimonial_taxonomies( $post_type );
	}

	public function filter_menu_terms( $settings ) {
		return $this->get_testimonial_filter_menu_terms( $settings );
	}

	public function render_query( $posts_per_page ) {
		return $this->query_testimonial_posts( 'grid', $posts_per_page );
	}

	public function render_image( $image_id, $settings ) {
		$this->render_testimonial_image( 'grid', $image_id, $settings );
	}

	public function render_title( $post_id, $settings ) {
		$this->render_testimonial_title( 'grid', $post_id, $settings );
	}

	public function render_address( $post_id, $settings ) {
		$this->render_testimonial_address( 'grid', $post_id, $settings );
	}

	public function render_designation( $post_id, $settings ) {
		$this->render_testimonial_designation( 'grid', $post_id, $settings );
	}

	public function render_excerpt( $settings ) {
		$this->render_testimonial_excerpt( 'grid', $settings );
	}

	public function render_review_platform( $post_id, $settings ) {
		$this->render_testimonial_review_platform( $post_id, $settings );
	}

	public function render_schema_item_reviewed( $settings ) {
		$this->render_testimonial_schema_item_reviewed( $settings );
	}

	public function render_rating_schema_only( $post_id, $settings ) {
		$this->render_testimonial_rating_schema_only( $post_id, $settings );
	}

	public function render_rating( $post_id, $settings ) {
		$this->render_testimonial_rating( 'grid', $post_id, $settings );
	}

	public function render_filter_menu( $settings ) {
		$this->render_testimonial_filter_menu( $settings );
	}

	public function render_header( $settings ) {
		$this->render_testimonial_grid_header( $settings );
	}

	public function render_footer() {
		$this->render_testimonial_grid_footer();
	}

	public function render_loop_item( $settings ) {
		$this->render_testimonial_grid_loop_item( $settings );
	}

	public function render() {
		$this->render_testimonial_grid();
	}
}
