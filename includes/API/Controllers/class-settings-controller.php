<?php
/**
 * Generic settings CRUD endpoint (allow-listed options only).
 *
 * GET  /element-pack/v1/settings           Schema + values for settings screens.
 * POST /element-pack/v1/settings           Persist a partial settings update.
 *
 * @package ElementPack\API\Controllers
 */

namespace ElementPack\API\Controllers;

use ElementPack\Admin\Settings_Schema;
use ElementPack\API\REST_Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings_Controller extends REST_Controller {

	protected $rest_base = 'settings';

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
					'callback'            => [ $this, 'get_settings' ],
					'permission_callback' => [ $this, 'permissions_check' ],
				],
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'update_settings' ],
					'permission_callback' => [ $this, 'permissions_check' ],
					'args'                => [
						'settings' => [
							'type'     => 'object',
							'required' => true,
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
	public function get_settings( $request ) {
		return $this->success( Settings_Schema::get_payload() );
	}

	/**
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_settings( $request ) {
		$incoming = $request->get_param( 'settings' );

		if ( ! is_array( $incoming ) ) {
			return $this->error( 'element_pack_invalid_settings', __( 'Invalid settings payload.', 'bdthemes-element-pack-lite' ) );
		}

		$saved = Settings_Schema::save_partial( $incoming );

		if ( empty( $saved ) ) {
			return $this->error( 'element_pack_no_settings_saved', __( 'No valid settings were provided.', 'bdthemes-element-pack-lite' ) );
		}

		$payload = Settings_Schema::get_payload();

		return $this->success(
			[
				'saved'    => true,
				'settings' => $saved,
				'values'   => $payload['values'],
				'asset_optimization_enabled' => $payload['asset_optimization_enabled'],
				'minified_version'           => $payload['minified_version'],
			]
		);
	}
}
