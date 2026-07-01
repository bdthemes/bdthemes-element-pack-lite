<?php

namespace ElementPack\Modules\EventsCalendarGrid\Widgets;

use ElementPack\Base\Module_Base;
use ElementPack\Modules\EventsCalendarGrid\Skins;
use ElementPack\Traits\Global_Controls_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Events_Calendar_Grid extends Module_Base {
	use Global_Controls_Functions;

	public $_query = null;

	public function get_name() {
		return 'bdt-event-grid';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Events Calendar Grid', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-events-calendar-grid';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'events', 'gallery', 'calendar', 'grid', 'the' ];
	}

	public function get_style_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'ep-styles' ];
		}
		return [ 'ep-events-calendar-grid', 'ep-font' ];
	}

	public function register_skins() {
		$this->add_skin( new Skins\Skin_Annal( $this ) );
		$this->add_skin( new Skins\Skin_Acara( $this ) );
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/QeqrcDx1Vus';
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

	public function has_widget_inner_wrapper(): bool {
		return ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	public function register_controls() {
		$this->register_events_calendar_layout_controls( 'grid' );
		$this->register_events_calendar_image_controls( 'grid' );
		$this->register_events_calendar_query_controls( 'grid' );
		$this->register_events_calendar_style_item_controls( 'grid' );
		$this->register_events_calendar_style_image_controls( 'grid' );
		$this->register_events_calendar_style_title_controls( 'grid' );
		$this->register_events_calendar_style_date_controls( 'grid' );
		$this->register_events_calendar_style_time_controls( 'grid' );
		$this->register_events_calendar_style_excerpt_controls( 'grid' );
		$this->register_events_calendar_style_meta_controls( 'grid' );
		$this->register_events_calendar_style_meta_price_controls( 'grid' );
		$this->register_events_calendar_style_address_controls( 'grid' );
	}

	public function render() {
		$this->render_events_calendar_grid();
	}

	public function render_image() {
		$this->render_events_calendar_image();
	}

	public function render_title() {
		$this->render_events_calendar_title();
	}

	public function render_date() {
		$this->render_events_calendar_date( 'grid' );
	}

	public function render_time() {
		$this->render_events_calendar_time();
	}

	public function render_excerpt( $post ) {
		$this->render_events_calendar_excerpt( $post );
	}

	public function render_meta() {
		$this->render_events_calendar_meta( 'grid' );
	}

	public function render_header( $skin_name = 'default' ) {
		$this->render_events_calendar_grid_header( $skin_name );
	}

	public function render_footer() {
		$this->render_events_calendar_grid_footer();
	}

	public function render_loop_item( $post ) {
		$this->render_events_calendar_grid_loop_item( $post );
	}
}
