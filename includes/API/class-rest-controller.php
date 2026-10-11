<?php
/**
 * Base REST controller for the Element Pack admin API.
 *
 * Centralises capability checks, nonce-backed cookie auth, and response
 * helpers so every endpoint enforces the same security policy. apiFetch on the
 * client sends the logged-in user's cookies plus the `X-WP-Nonce` header
 * (wp-api-fetch nonce middleware), which WordPress validates before our
 * permission_callback runs.
 *
 * @package ElementPack\API
 */

namespace ElementPack\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class REST_Controller extends \WP_REST_Controller {

	/**
	 * Shared REST namespace for the plugin.
	 *
	 * @var string
	 */
	protected $namespace = 'element-pack/v1';

	/**
	 * The capability required to access admin endpoints.
	 *
	 * @var string
	 */
	protected $capability = 'manage_options';

	/**
	 * Default permission check used by read/write admin endpoints.
	 *
	 * @return bool|\WP_Error
	 */
	public function permissions_check() {
		if ( ! current_user_can( $this->capability ) ) {
			return new \WP_Error(
				'element_pack_rest_forbidden',
				__( 'You do not have permission to access this resource.', 'bdthemes-element-pack-lite' ),
				[ 'status' => rest_authorization_required_code() ]
			);
		}

		return true;
	}

	/**
	 * Standard success envelope.
	 *
	 * @param mixed $data
	 * @param int   $status
	 * @return \WP_REST_Response
	 */
	protected function success( $data, $status = 200 ) {
		return new \WP_REST_Response(
			[
				'success' => true,
				'data'    => $data,
			],
			$status
		);
	}

	/**
	 * Standard error envelope.
	 *
	 * @param string $code
	 * @param string $message
	 * @param int    $status
	 * @return \WP_Error
	 */
	protected function error( $code, $message, $status = 400 ) {
		return new \WP_Error( $code, $message, [ 'status' => $status ] );
	}
}
