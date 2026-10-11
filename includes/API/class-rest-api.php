<?php
/**
 * REST API bootstrap for the Element Pack admin SPA.
 *
 * Wires up every controller under the `element-pack/v1` namespace on
 * `rest_api_init`. Controllers wrap existing services (ModuleService,
 * Dashboard_Stats, option helpers) — no business logic is duplicated here.
 *
 * @package ElementPack\API
 */

namespace ElementPack\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class REST_API {

	/**
	 * @var REST_Controller[]
	 */
	private $controllers = [];

	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Instantiate and register all controllers.
	 *
	 * @return void
	 */
	public function register_routes() {
		$this->controllers = [
			new Controllers\Dashboard_Controller(),
			new Controllers\Modules_Controller(),
			new Controllers\Settings_Controller(),
			new Controllers\System_Controller(),
			new Controllers\Other_Plugins_Controller(),
			new Controllers\Notifications_Controller(),
		];

		foreach ( $this->controllers as $controller ) {
			$controller->register_routes();
		}
	}
}
