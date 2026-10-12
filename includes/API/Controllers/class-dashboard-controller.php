<?php
/**
 * Dashboard statistics endpoint.
 *
 * GET /element-pack/v1/dashboard
 *
 * Returns widget counts (total/active/used/unused per tier) plus environment
 * flags used by the React dashboard overview. Sources all data from the
 * REST-safe Dashboard_Stats service.
 *
 * @package ElementPack\API\Controllers
 */

namespace ElementPack\API\Controllers;

use ElementPack\API\REST_Controller;
use ElementPack\Admin\Dashboard_Stats;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Dashboard_Controller extends REST_Controller {

	protected $rest_base = 'dashboard';

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
					'callback'            => [ $this, 'get_dashboard' ],
					'permission_callback' => [ $this, 'permissions_check' ],
				],
				'schema' => [ $this, 'get_public_item_schema' ],
			]
		);
	}

	/**
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_dashboard( $request ) {
		if ( ! class_exists( '\ElementPack\Admin\Dashboard_Stats' ) ) {
			return $this->error(
				'element_pack_stats_unavailable',
				__( 'Dashboard statistics are unavailable.', 'bdthemes-element-pack-lite' ),
				500
			);
		}

		return $this->success( Dashboard_Stats::get_stats() );
	}

	/**
	 * @return array
	 */
	public function get_item_schema() {
		return [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'element_pack_dashboard',
			'type'       => 'object',
			'properties' => [
				'tracking_allowed' => [ 'type' => 'boolean' ],
				'is_pro'           => [ 'type' => 'boolean' ],
				'widgets'          => [ 'type' => 'object' ],
				'active_totals'    => [ 'type' => 'object' ],
			],
		];
	}
}
