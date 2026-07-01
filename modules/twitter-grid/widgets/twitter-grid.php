<?php

namespace ElementPack\Modules\TwitterGrid\Widgets;

use ElementPack\Base\Module_Base;
use ElementPack\Traits\Global_Controls_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Twitter_Grid extends Module_Base {
	use Global_Controls_Functions;

	private $_query = null;

	public function get_name() {
		return 'bdt-twitter-grid';
	}

	public function get_title() {
		return BDTEP . __( 'Twitter Grid', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-twitter-grid';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'twitter', 'carousel', 'grid' ];
	}

	public function get_style_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'ep-styles' ];
		}

		return [ 'ep-font', 'ep-twitter-grid' ];
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
		return 'https://youtu.be/cYqDPiDpsEY';
	}

	public function has_widget_inner_wrapper(): bool {
		return ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	protected function register_controls() {
		$this->register_twitter_query_section_controls( 'grid' );
	}

	public function getTwitterAuth2Data( $consumerKey, $consumerSecret, $username ) {
		return $this->get_twitter_auth2_data( $consumerKey, $consumerSecret, $username );
	}

	public function getTwitterAuth1Data( $consumerKey, $consumerSecret, $accessToken, $accessTokenSecret, $twitter_name ) {
		return $this->get_twitter_auth1_data( $consumerKey, $consumerSecret, $accessToken, $accessTokenSecret, $twitter_name );
	}

	public function render_loop_twitter( $consumerKey, $consumerSecret, $accessToken, $accessTokenSecret, $twitter_name ) {
		$this->render_twitter_loop( 'grid', $consumerKey, $consumerSecret, $accessToken, $accessTokenSecret, $twitter_name );
	}

	public function render() {
		$this->render_twitter( 'grid' );
	}

	public function render_loop_header() {
		$this->render_twitter_loop_header( 'grid' );
	}

	public function render_footer() {
		$this->render_twitter_footer( 'grid' );
	}
}
