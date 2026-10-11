<?php
/**
 * Element Pack — React admin bootstrap.
 *
 * Loads the REST API layer and the React dashboard shown on `element_pack_options`.
 *
 * @package ElementPack
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// REST API layer and the services behind it. Frontend page views use none of
// these, so they are autoloaded instead of required on every request: REST
// requests still register every route (REST_API hooks rest_api_init), and any
// class a request does touch is loaded on first use.
spl_autoload_register(
	static function ( $class ) {
		static $map = [
			'ElementPack\\API\\REST_Controller'                       => 'API/class-rest-controller.php',
			'ElementPack\\API\\REST_API'                              => 'API/class-rest-api.php',
			'ElementPack\\API\\Controllers\\Dashboard_Controller'      => 'API/Controllers/class-dashboard-controller.php',
			'ElementPack\\API\\Controllers\\Modules_Controller'        => 'API/Controllers/class-modules-controller.php',
			'ElementPack\\API\\Controllers\\Settings_Controller'       => 'API/Controllers/class-settings-controller.php',
			'ElementPack\\API\\Controllers\\System_Controller'         => 'API/Controllers/class-system-controller.php',
			'ElementPack\\API\\Controllers\\Other_Plugins_Controller'  => 'API/Controllers/class-other-plugins-controller.php',
			'ElementPack\\API\\Controllers\\Notifications_Controller'  => 'API/Controllers/class-notifications-controller.php',
			'ElementPack\\Admin\\Dashboard_Stats'                     => 'Admin/class-dashboard-stats.php',
			'ElementPack\\Admin\\Settings_Schema'                     => 'Admin/class-settings-schema.php',
			'ElementPack\\Admin\\System_Status'                       => 'Admin/class-system-status.php',
			'ElementPack\\Admin\\Menu_Visibility_Service'             => 'Admin/class-menu-visibility-service.php',
			'ElementPack\\Admin\\Other_Plugins_Service'               => 'Admin/class-other-plugins-service.php',
			'ElementPack\\Admin\\Admin_Cutover'                       => 'Admin/class-admin-cutover.php',
			'ElementPack\\Admin\\External_Nav_Service'                => 'Admin/class-external-nav-service.php',
		];

		if ( isset( $map[ $class ] ) ) {
			require_once BDTEP_INC_PATH . $map[ $class ];
		}
	}
);

new \ElementPack\API\REST_API();

// React mount + asset loader (admin only).
if ( is_admin() ) {
	require_once BDTEP_INC_PATH . 'Admin/class-react-app.php';
	new \ElementPack\Admin\React_App();
	new \ElementPack\Admin\Admin_Cutover();
}
