<?php
/**
 * React admin routing helpers — legacy hash mapping and submenu cleanup.
 *
 * @package ElementPack\Admin
 */

namespace ElementPack\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin_Cutover {

	const PAGE_ID = 'element_pack_options';

	/**
	 * Legacy hash fragment (with or without leading #) → React hash route.
	 *
	 * @var array<string,string>
	 */
	private static $hash_to_route = [
		'element_pack_welcome'                   => '/',
		'element_pack_welcome_page'              => '/',
		'element_pack_active_modules'            => '/widgets',
		'element_pack_active_modules_page'       => '/widgets',
		'element_pack_third_party_widget'        => '/widgets',
		'element_pack_third_party_widget_page'   => '/widgets',
		'element_pack_elementor_extend'          => '/extensions',
		'element_pack_elementor_extend_page'     => '/extensions',
		'element_pack_other_settings'            => '/settings',
		'element_pack_other_settings_page'       => '/settings',
		'element_pack_api_settings'              => '/integrations',
		'element_pack_api_settings_page'         => '/integrations',
		'element_pack_analytics_system_req'      => '/system-status',
		'element_pack_analytics_system_req_page' => '/system-status',
		'element_pack_other_plugins'             => '/other-plugins',
		'element_pack_other_plugins_page'        => '/other-plugins',
		'element_pack_get_pro'                   => '/go-pro',
		'element_pack_get_pro_page'              => '/go-pro',
	];

	public function __construct() {
		if ( ! React_App::is_enabled() ) {
			return;
		}

		add_action( 'admin_menu', [ $this, 'prune_legacy_submenus' ], 999 );
		add_action( 'admin_head', [ $this, 'fix_toplevel_menu_link' ], 1 );
		add_action( 'admin_head', [ $this, 'print_go_pro_menu_css' ] );
	}

	public static function is_default(): bool {
		if ( ! React_App::is_enabled() ) {
			return false;
		}

		if ( ! self::build_exists() ) {
			return false;
		}

		return (bool) apply_filters( 'element_pack/admin/react_default', true );
	}

	public static function build_exists(): bool {
		return file_exists( BDTEP_PATH . 'build/admin/index.asset.php' );
	}

	/**
	 * @param string               $route
	 * @param array<string,string> $query
	 */
	public static function app_url( $route = '/', $query = [] ): string {
		$route = '/' . ltrim( (string) $route, '/' );
		$url   = admin_url( 'admin.php?page=' . self::PAGE_ID );

		if ( ! empty( $query ) ) {
			$url = add_query_arg( $query, $url );
		}

		return $url . '#' . $route;
	}

	/**
	 * @param string $route Unused; kept for call-site compatibility.
	 */
	public static function menu_slug_for_route( $route = '/' ): string {
		unset( $route );

		return self::PAGE_ID;
	}

	/**
	 * @param string $hash
	 */
	public static function legacy_hash_to_route( $hash ): string {
		$key = ltrim( (string) $hash, '#' );

		if ( isset( self::$hash_to_route[ $key ] ) ) {
			return self::$hash_to_route[ $key ];
		}

		return '/';
	}

	public function prune_legacy_submenus(): void {
		if ( ! self::is_default() ) {
			return;
		}

		global $submenu;

		if ( empty( $submenu[ self::PAGE_ID ] ) || ! is_array( $submenu[ self::PAGE_ID ] ) ) {
			return;
		}

		$pruned = [];

		foreach ( $submenu[ self::PAGE_ID ] as $item ) {
			$slug = isset( $item[2] ) ? (string) $item[2] : '';

			if ( '' === $slug ) {
				continue;
			}

			// Keep the "Get Pro" upgrade link and any other plugin's CPT screens.
			if ( strpos( $slug, '_upgrade' ) !== false || 0 === strpos( $slug, 'edit.php?post_type=' ) ) {
				$pruned[] = $item;
				continue;
			}

			if ( self::PAGE_ID === $slug ) {
				continue;
			}

			if ( 0 === strpos( $slug, self::PAGE_ID ) ) {
				continue;
			}

			$pruned[] = $item;
		}

		// WordPress points the top-level menu href at submenu[0]. Keep Dashboard first
		// so clicking the root menu opens the React SPA, not Theme Builder / CPT screens.
		$menu_title = __( 'Dashboard', 'bdthemes-element-pack-lite' );
		$page_title = defined( 'BDTEP_TITLE' )
			? BDTEP_TITLE . ' ' . __( 'Dashboard', 'bdthemes-element-pack-lite' )
			: __( 'Dashboard', 'bdthemes-element-pack-lite' );

		array_unshift(
			$pruned,
			[
				$menu_title,
				'manage_options',
				self::PAGE_ID,
				$page_title,
			]
		);

		// SPA routes as real WP submenu entries (mirrors the React tab strip),
		// inserted right after Dashboard, before any other submenu links.
		array_splice( $pruned, 1, 0, self::react_submenu_items() );

		$submenu[ self::PAGE_ID ] = $pruned;
	}

	/**
	 * WP sidebar submenu entries for the SPA routes, honoring the same
	 * visibility rules as the React navigation.
	 *
	 * @return array<int,array<int,string>>
	 */
	private static function react_submenu_items(): array {
		$visibility = class_exists( Menu_Visibility_Service::class )
			? Menu_Visibility_Service::get_nav_visibility()
			: null;

		$is_visible = static function ( $key ) use ( $visibility ) {
			if ( ! is_array( $visibility ) || ! isset( $visibility[ $key ] ) ) {
				return true;
			}
			return false !== $visibility[ $key ];
		};

		$routes = [
			[ 'widgets', '/widgets', __( 'Widgets', 'bdthemes-element-pack-lite' ) ],
			[ 'extensions', '/extensions', __( 'Extensions', 'bdthemes-element-pack-lite' ) ],
			[ 'settings', '/settings', __( 'Features', 'bdthemes-element-pack-lite' ) ],
			[ 'settings_api', '/integrations', __( 'Integrations', 'bdthemes-element-pack-lite' ) ],
			[ 'settings_perf', '/settings/perf', __( 'Performance', 'bdthemes-element-pack-lite' ) ],
			[ 'system_status', '/system-status', __( 'System Status', 'bdthemes-element-pack-lite' ) ],
			[ 'other_plugins', '/other-plugins', __( 'Other Plugins', 'bdthemes-element-pack-lite' ) ],
			[ 'go_pro', '/go-pro', __( 'You may also need', 'bdthemes-element-pack-lite' ) ],
		];

		$items = [];

		foreach ( $routes as $route ) {
			list( $nav_key, $path, $label ) = $route;

			if ( ! $is_visible( $nav_key ) ) {
				continue;
			}

			$items[] = [
				$label,
				'manage_options',
				'admin.php?page=' . self::PAGE_ID . '#' . $path,
			];
		}

		return $items;
	}

	/**
	 * After wp-admin/includes/menu.php rewrites the parent slug, force the root
	 * menu link back to the React dashboard page.
	 */
	/**
	 * Make the "You may also need" (Go Pro) sidebar entry stand out with a brand
	 * gradient pill. It styles that one link on every admin
	 * screen, since the menu is visible everywhere, and only while the entry is
	 * offered.
	 */
	public function print_go_pro_menu_css(): void {
		$visibility = Menu_Visibility_Service::get_nav_visibility();

		if ( empty( $visibility['go_pro'] ) ) {
			return;
		}

		?>
		<style id="ep-go-pro-menu">
			#adminmenu .wp-submenu li a[href$="#/go-pro"] {
				/* Pill inset so the label still lines up with the other entries. */
				margin: 3px 6px;
				padding: 5px 6px;
				border-radius: 8px;
				background: linear-gradient(90deg, rgba(64, 142, 250, .28), rgba(132, 237, 146, .16));
				color: #9af0b5;
				font-weight: 700;
			}
			#adminmenu .wp-submenu li a[href$="#/go-pro"]:hover,
			#adminmenu .wp-submenu li a[href$="#/go-pro"]:focus,
			#adminmenu .wp-submenu li a[href$="#/go-pro"]:active,
			#adminmenu .wp-submenu li.current a[href$="#/go-pro"] {
				background: linear-gradient(90deg, rgba(64, 142, 250, .5), rgba(132, 237, 146, .3));
				color: #fff;
			}
			/* WP draws a light glow/outline on hover and focus; keep the pill clean. */
			#adminmenu .wp-submenu li a[href$="#/go-pro"],
			#adminmenu .wp-submenu li a[href$="#/go-pro"]:hover,
			#adminmenu .wp-submenu li a[href$="#/go-pro"]:focus,
			#adminmenu .wp-submenu li a[href$="#/go-pro"]:active {
				box-shadow: none;
				outline: 0;
				border: 0;
			}
			/* Active page: accent line on the left, so the left corners go square. */
			#adminmenu .wp-submenu li.current a.current[href$="#/go-pro"] {
				border-left: 3px solid #9af0b5;
				border-radius: 0 8px 8px 0;
				padding-left: 3px;
				color: #fff;
			}
			#adminmenu .wp-submenu li:has(> a[href$="#/go-pro"]),
			#adminmenu .wp-submenu li:has(> a[href$="#/go-pro"]):hover {
				background: transparent;
				box-shadow: none;
			}
		</style>
		<?php
	}

	public function fix_toplevel_menu_link(): void {
		if ( ! self::is_default() ) {
			return;
		}

		global $menu, $submenu;

		foreach ( $menu as $key => $item ) {
			if ( ! isset( $item[2] ) || self::PAGE_ID !== $item[2] ) {
				continue;
			}

			$menu[ $key ][2] = self::PAGE_ID;
			break;
		}

		if ( ! empty( $submenu[ self::PAGE_ID ][0][2] ) ) {
			$submenu[ self::PAGE_ID ][0][2] = self::PAGE_ID;
		}
	}

}
