<?php
/**
 * System status report endpoint.
 *
 * GET /element-pack/v1/system
 *
 * @package ElementPack\API\Controllers
 */

namespace ElementPack\API\Controllers;

use ElementPack\Admin\System_Status;
use ElementPack\API\REST_Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class System_Controller extends REST_Controller {

	protected $rest_base = 'system';

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
					'callback'            => [ $this, 'get_system' ],
					'permission_callback' => [ $this, 'permissions_check' ],
				],
			]
		);
	}

	/**
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response
	 */
	public function get_system( $request ) {
		return $this->success( System_Status::get_report() );
	}
}
