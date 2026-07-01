<?php

namespace ElementPack\Modules\FacebookFeed\Widgets;

use ElementPack\Base\Module_Base;
use ElementPack\Traits\Global_Controls_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Facebook_Feed extends Module_Base {
	use Global_Controls_Functions;

	public function get_name() {
		return 'bdt-facebook-feed';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Facebook Feed', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-facebook-feed';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'facebook', 'feed' ];
	}

	public function get_style_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'ep-styles' ];
		} else {
			return [ 'ep-facebook-feed' ];
		}
	}

	public function has_widget_inner_wrapper(): bool {
		return ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	protected function register_controls() {
		$this->register_facebook_feed_controls( 'grid' );
	}

	public function feed_data() {
		return $this->facebook_feed_data();
	}

	public function get_transient_expire( $settings ) {
		return $this->facebook_feed_get_transient_expire( $settings );
	}

	public function data_remote_request( $url ) {
		return $this->facebook_feed_data_remote_request( $url );
	}

	public function transient_feed_data( $raw_data = [] ) {
		return $this->facebook_feed_transient_feed_data( $raw_data );
	}

	public function _error_notice( $message ) {
		$this->facebook_feed_error_notice( $message );
	}

	public function cleanup_old_images( $max_age_days = 30 ) {
		$this->facebook_feed_cleanup_old_images( $max_age_days );
	}

	protected function render_read_more( $data, $settings ) {
		$this->render_facebook_feed_read_more( $data, $settings );
	}

	protected function render_main_share( $data, $settings ) {
		$this->render_facebook_feed_main_share( $data, $settings );
	}

	protected function render_date( $data, $settings ) {
		$this->render_facebook_feed_date( $data, $settings );
	}

	protected function render_author_image( $data, $settings ) {
		$this->render_facebook_feed_author_image( $data, $settings );
	}

	protected function render_author_name( $data, $settings ) {
		$this->render_facebook_feed_author_name( $data, $settings );
	}

	protected function render_feature_image( $data, $settings ) {
		$this->render_facebook_feed_feature_image( $data, $settings );
	}

	protected function render_single_image( $data, $settings ) {
		$this->render_facebook_feed_single_image( $data, $settings );
	}

	protected function render_image_album( $subattachments, $settings, $data ) {
		$this->render_facebook_feed_image_album( $subattachments, $settings, $data );
	}

	protected function render_desc( $data, $settings ) {
		$this->render_facebook_feed_desc( $data, $settings );
	}

	protected function render_like( $data, $settings ) {
		$this->render_facebook_feed_like( $data, $settings );
	}

	protected function render_comments( $data, $settings ) {
		$this->render_facebook_feed_comments( $data, $settings );
	}

	protected function render() {
		$this->render_facebook_feed( 'grid' );
	}
}
