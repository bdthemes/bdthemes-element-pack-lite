<?php
/**
 * Other Plugins catalog + install endpoints.
 *
 * GET  /element-pack/v1/other-plugins
 * POST /element-pack/v1/other-plugins/install
 *
 * @package ElementPack\API\Controllers
 */

namespace ElementPack\API\Controllers;

use ElementPack\Admin\Other_Plugins_Service;
use ElementPack\API\REST_Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Other_Plugins_Controller extends REST_Controller {

	protected $rest_base = 'other-plugins';

	/**
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_plugins' ],
					'permission_callback' => [ $this, 'permissions_check' ],
				],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/install',
			[
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'post_install' ],
					'permission_callback' => [ $this, 'permissions_check' ],
					'args'                => [
						'slug' => [
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_key',
						],
						'activate' => [
							'type'    => 'boolean',
							'default' => true,
						],
					],
				],
			]
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/activate',
			[
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'post_activate' ],
					'permission_callback' => [ $this, 'permissions_check' ],
					'args'                => [
						'slug' => [
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_key',
						],
						'plugin_file' => [
							'type'              => 'string',
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						],
					],
				],
			]
		);
	}

	/**
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response
	 */
	public function get_plugins( $request ) {
		return $this->success( Other_Plugins_Service::get_plugins() );
	}

	/**
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function post_install( $request ) {
		$result = Other_Plugins_Service::install(
			$request->get_param( 'slug' ),
			(bool) $request->get_param( 'activate' )
		);

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $this->success( $result );
	}

	/**
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function post_activate( $request ) {
		$result = Other_Plugins_Service::activate(
			(string) $request->get_param( 'slug' ),
			(string) $request->get_param( 'plugin_file' )
		);

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $this->success( $result );
	}
}
