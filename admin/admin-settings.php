<?php

use ElementPack\Notices;
use ElementPack\Utils;
use ElementPack\Admin\ModuleService;
use ElementPack\Base\Element_Pack_Base;
use Elementor\Modules\Usage\Module;
use Elementor\Tracker;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Element Pack Admin Settings Class
 */

class ElementPack_Admin_Settings {

	public static $modules_list = null;
	public static $modules_names = null;

	public static $modules_list_only_widgets = null;
	public static $modules_names_only_widgets = null;

	public static $modules_list_only_3rdparty = null;
	public static $modules_names_only_3rdparty = null;

	const PAGE_ID = 'element_pack_options';

	private $settings_api;

	public $responseObj;
	public $showMessage = false;
	private $is_activated = false;

	function __construct() {
		$this->settings_api = new ElementPack_Settings_API;

		add_action( 'admin_init', [ $this, 'admin_init' ] );
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 201 );

		// Add AJAX handler for plugin installation
		add_action('wp_ajax_ep_install_plugin', [$this, 'install_plugin_ajax']);

	}

	/**
	 * Get used widgets.
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */
	public static function get_used_widgets() {

		$used_widgets = array();

		if ( ! Tracker::is_allow_track() ) {
			return $used_widgets;
		}

		if ( class_exists( 'Elementor\Modules\Usage\Module' ) ) {

			$module     = Module::instance();
			$elements   = $module->get_formatted_usage( 'raw' );
			$ep_widgets = self::get_ep_widgets_names();

			if ( is_array( $elements ) || is_object( $elements ) ) {

				foreach ( $elements as $post_type => $data ) {
					foreach ( $data['elements'] as $element => $count ) {
						if ( in_array( $element, $ep_widgets, true ) ) {
							if ( isset( $used_widgets[ $element ] ) ) {
								$used_widgets[ $element ] += $count;
							} else {
								$used_widgets[ $element ] = $count;
							}
						}
					}
				}
			}
		}

		return $used_widgets;
	}

	/**
	 * Get used separate widgets.
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_used_only_widgets() {

		$used_widgets = array();

		if ( ! Tracker::is_allow_track() ) {
			return $used_widgets;
		}

		if ( class_exists( 'Elementor\Modules\Usage\Module' ) ) {

			$module     = Module::instance();
			$elements   = $module->get_formatted_usage( 'raw' );
			$ep_widgets = self::get_ep_only_widgets();

			if ( is_array( $elements ) || is_object( $elements ) ) {

				foreach ( $elements as $post_type => $data ) {
					foreach ( $data['elements'] as $element => $count ) {
						if ( in_array( $element, $ep_widgets, true ) ) {
							if ( isset( $used_widgets[ $element ] ) ) {
								$used_widgets[ $element ] += $count;
							} else {
								$used_widgets[ $element ] = $count;
							}
						}
					}
				}
			}
		}

		return $used_widgets;
	}

	/**
	 * Get used only separate 3rdParty widgets.
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_used_only_3rdparty() {

		$used_widgets = array();

		if ( ! Tracker::is_allow_track() ) {
			return $used_widgets;
		}

		if ( class_exists( 'Elementor\Modules\Usage\Module' ) ) {

			$module     = Module::instance();
			$elements   = $module->get_formatted_usage( 'raw' );
			$ep_widgets = self::get_ep_only_3rdparty_names();

			if ( is_array( $elements ) || is_object( $elements ) ) {

				foreach ( $elements as $post_type => $data ) {
					foreach ( $data['elements'] as $element => $count ) {
						if ( in_array( $element, $ep_widgets, true ) ) {
							if ( isset( $used_widgets[ $element ] ) ) {
								$used_widgets[ $element ] += $count;
							} else {
								$used_widgets[ $element ] = $count;
							}
						}
					}
				}
			}
		}

		return $used_widgets;
	}

	/**
	 * Get unused widgets.
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_unused_widgets() {

		if ( ! current_user_can( 'install_plugins' ) ) {
			die();
		}

		$ep_widgets = self::get_ep_widgets_names();

		$used_widgets = self::get_used_widgets();

		$unused_widgets = array_diff( $ep_widgets, array_keys( $used_widgets ) );

		return $unused_widgets;
	}

	/**
	 * Get unused separate widgets.
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_unused_only_widgets() {

		if ( ! current_user_can( 'install_plugins' ) ) {
			die();
		}

		$ep_widgets = self::get_ep_only_widgets();

		$used_widgets = self::get_used_only_widgets();

		$unused_widgets = array_diff( $ep_widgets, array_keys( $used_widgets ) );

		return $unused_widgets;
	}

	/**
	 * Get unused separate 3rdparty widgets.
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_unused_only_3rdparty() {

		if ( ! current_user_can( 'install_plugins' ) ) {
			die();
		}

		$ep_widgets = self::get_ep_only_3rdparty_names();

		$used_widgets = self::get_used_only_3rdparty();

		$unused_widgets = array_diff( $ep_widgets, array_keys( $used_widgets ) );

		return $unused_widgets;
	}

	/**
	 * Get widgets name
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_ep_widgets_names() {
		$names = self::$modules_names;

		if ( null === $names ) {
			$names = array_map(
				function ($item) {
					return isset( $item['name'] ) ? 'bdt-' . str_replace( '_', '-', $item['name'] ) : 'none';
				},
				self::$modules_list
			);
		}

		return $names;
	}

	/**
	 * Get separate widgets name
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_ep_only_widgets() {
		$names = self::$modules_names_only_widgets;

		if ( null === $names ) {
			$names = array_map(
				function ($item) {
					return isset( $item['name'] ) ? 'bdt-' . str_replace( '_', '-', $item['name'] ) : 'none';
				},
				self::$modules_list_only_widgets
			);
		}

		return $names;
	}

	/**
	 * Get separate 3rdParty widgets name
	 *
	 * @access public
	 * @return array
	 * @since 6.0.0
	 *
	 */

	public static function get_ep_only_3rdparty_names() {
		$names = self::$modules_names_only_3rdparty;

		if ( null === $names ) {
			$names = array_map(
				function ($item) {
					return isset( $item['name'] ) ? 'bdt-' . str_replace( '_', '-', $item['name'] ) : 'none';
				},
				self::$modules_list_only_3rdparty
			);
		}

		return $names;
	}

	/**
	 * Get URL with page id
	 *
	 * @access public
	 *
	 */

	public static function get_url() {
		return admin_url( 'admin.php?page=' . self::PAGE_ID );
	}

	/**
	 * Init settings API
	 *
	 * @access public
	 *
	 */

	public function admin_init() {

		//set the settings
		$this->settings_api->set_sections( $this->get_settings_sections() );
		$this->settings_api->set_fields( $this->element_pack_admin_settings() );

		//initialize settings
		$this->settings_api->admin_init();
		$this->ep_redirect_to_upgrade();
	}

	// Redirect to Element Pack Pro pricing page
	public function ep_redirect_to_upgrade() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin screen routing, no state is changed.
		$page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';

		if ($page === self::PAGE_ID . '_upgrade') {
			// phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- Hardcoded external upgrade URL, never user input.
			wp_redirect('https://www.elementpack.pro/pricing/');
			exit;
		}
	}

	/**
	 * Add Plugin Menus
	 *
	 * @access public
	 *
	 */

	public function admin_menu() {
		add_menu_page(
			BDTEP_TITLE . ' ' . esc_html__( 'Dashboard', 'bdthemes-element-pack-lite' ),
			BDTEP_TITLE,
			'manage_options',
			self::PAGE_ID,
			[ $this, 'plugin_page' ],
			$this->element_pack_icon(),
			3
		);

		add_submenu_page(
			self::PAGE_ID,
			esc_html__('Dashboard', 'bdthemes-element-pack-lite'),
			esc_html__('Dashboard', 'bdthemes-element-pack-lite'),
			'manage_options',
			self::PAGE_ID,
			[$this, 'plugin_page'],
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTEP_TITLE,
			esc_html__( 'Core Widgets', 'bdthemes-element-pack-lite' ),
			'manage_options',
			self::PAGE_ID . '#element_pack_active_modules',
			[ $this, 'display_page' ]
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTEP_TITLE,
			esc_html__( '3rd Party Widgets', 'bdthemes-element-pack-lite' ),
			'manage_options',
			self::PAGE_ID . '#element_pack_third_party_widget',
			[ $this, 'display_page' ]
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTEP_TITLE,
			esc_html__( 'Extensions', 'bdthemes-element-pack-lite' ),
			'manage_options',
			self::PAGE_ID . '#element_pack_elementor_extend',
			[ $this, 'display_page' ]
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTEP_TITLE,
			esc_html__( 'Special Features', 'bdthemes-element-pack-lite' ),
			'manage_options',
			self::PAGE_ID . '#element_pack_other_settings',
			[ $this, 'display_page' ]
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTEP_TITLE,
			esc_html__( 'API Settings', 'bdthemes-element-pack-lite' ),
			'manage_options',
			self::PAGE_ID . '#element_pack_api_settings',
			[ $this, 'display_page' ]
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTEP_TITLE,
			esc_html__('System Status', 'bdthemes-element-pack-lite'),
			'manage_options',
			self::PAGE_ID . '#element_pack_analytics_system_req',
			[$this, 'display_page']
		);
		
		add_submenu_page(
			self::PAGE_ID,
			BDTEP_TITLE,
			esc_html__('Other Plugins', 'bdthemes-element-pack-lite'),
			'manage_options',
			self::PAGE_ID . '#element_pack_other_plugins',
			[$this, 'display_page']
		);
	}

	/**
	 * Get SVG Icons of Element Pack
	 *
	 * @access public
	 * @return string
	 */

	public function element_pack_icon() {
		return 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyMy4wLjIsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHdpZHRoPSIyMzAuN3B4IiBoZWlnaHQ9IjI1NC44MXB4IiB2aWV3Qm94PSIwIDAgMjMwLjcgMjU0LjgxIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAyMzAuNyAyNTQuODE7Ig0KCSB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+DQoJLnN0MHtmaWxsOiNGRkZGRkY7fQ0KPC9zdHlsZT4NCjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik02MS4wOSwyMjkuMThIMjguOTVjLTMuMTcsMC01Ljc1LTIuNTctNS43NS01Ljc1bDAtMTkyLjA3YzAtMy4xNywyLjU3LTUuNzUsNS43NS01Ljc1aDMyLjE0DQoJYzMuMTcsMCw1Ljc1LDIuNTcsNS43NSw1Ljc1djE5Mi4wN0M2Ni44MywyMjYuNjEsNjQuMjYsMjI5LjE4LDYxLjA5LDIyOS4xOHoiLz4NCjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0yMDcuNSwzMS4zN3YzMi4xNGMwLDMuMTctMi41Nyw1Ljc1LTUuNzUsNS43NUg5MC4wNGMtMy4xNywwLTUuNzUtMi41Ny01Ljc1LTUuNzVWMzEuMzcNCgljMC0zLjE3LDIuNTctNS43NSw1Ljc1LTUuNzVoMTExLjcyQzIwNC45MywyNS42MiwyMDcuNSwyOC4yLDIwNy41LDMxLjM3eiIvPg0KPHBhdGggY2xhc3M9InN0MCIgZD0iTTIwNy41LDExMS4zM3YzMi4xNGMwLDMuMTctMi41Nyw1Ljc1LTUuNzUsNS43NUg5MC4wNGMtMy4xNywwLTUuNzUtMi41Ny01Ljc1LTUuNzV2LTMyLjE0DQoJYzAtMy4xNywyLjU3LTUuNzUsNS43NS01Ljc1aDExMS43MkMyMDQuOTMsMTA1LjU5LDIwNy41LDEwOC4xNiwyMDcuNSwxMTEuMzN6Ii8+DQo8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMjA3LjUsMTkxLjN2MzIuMTRjMCwzLjE3LTIuNTcsNS43NS01Ljc1LDUuNzVIOTAuMDRjLTMuMTcsMC01Ljc1LTIuNTctNS43NS01Ljc1VjE5MS4zDQoJYzAtMy4xNywyLjU3LTUuNzUsNS43NS01Ljc1aDExMS43MkMyMDQuOTMsMTg1LjU1LDIwNy41LDE4OC4xMywyMDcuNSwxOTEuM3oiLz4NCjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0xNjkuNjIsMjUuNjJoMzIuMTRjMy4xNywwLDUuNzUsMi41Nyw1Ljc1LDUuNzV2MTEyLjFjMCwzLjE3LTIuNTcsNS43NS01Ljc1LDUuNzVoLTMyLjE0DQoJYy0zLjE3LDAtNS43NS0yLjU3LTUuNzUtNS43NVYzMS4zN0MxNjMuODcsMjguMiwxNjYuNDQsMjUuNjIsMTY5LjYyLDI1LjYyeiIvPg0KPC9zdmc+DQo=';
	}

	/**
	 * Get SVG Icons of Element Pack
	 *
	 * @access public
	 * @return array
	 */

	public function get_settings_sections() {
		$sections = [
			[
				'id' => 'element_pack_active_modules',
				'title' => esc_html__('Core Widgets', 'bdthemes-element-pack-lite'),
				'icon' => 'dashicons dashicons-screenoptions',
			],
			[
				'id' => 'element_pack_third_party_widget',
				'title' => esc_html__('3rd Party Widgets', 'bdthemes-element-pack-lite'),
				'icon' => 'dashicons dashicons-screenoptions',
			],
			[
				'id' => 'element_pack_elementor_extend',
				'title' => esc_html__('Extensions', 'bdthemes-element-pack-lite'),
				'icon' => 'dashicons dashicons-screenoptions',
			],
			[
				'id' => 'element_pack_other_settings',
				'title' => esc_html__('Special Features', 'bdthemes-element-pack-lite'),
				'icon' => 'dashicons dashicons-screenoptions',
			],
			[
				'id' => 'element_pack_api_settings',
				'title' => esc_html__('API Settings', 'bdthemes-element-pack-lite'),
				'icon' => 'dashicons dashicons-admin-settings',
			],
		];

		return $sections;
	}

	/**
	 * Merge Admin Settings
	 *
	 * @access protected
	 * @return array
	 */

	protected function element_pack_admin_settings() {

		return ModuleService::get_widget_settings( function ($settings) {
			$settings_fields = $settings['settings_fields'];

			self::$modules_list               = array_merge( $settings_fields['element_pack_active_modules'], $settings_fields['element_pack_third_party_widget'] );
			self::$modules_list_only_widgets  = $settings_fields['element_pack_active_modules'];
			self::$modules_list_only_3rdparty = $settings_fields['element_pack_third_party_widget'];

			return $settings_fields;
		} );
	}

	/**
	 * Get Welcome Panel
	 *
	 * @access public
	 * @return void
	 */

	public function element_pack_welcome() {

		?>

		<div class="ep-dashboard-panel"
			bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">

			<div class="ep-dashboard-welcome-container">

				<div class="ep-dashboard-item ep-dashboard-welcome bdt-card bdt-card-body">
					<h1 class="ep-feature-title ep-dashboard-welcome-title">
						<?php esc_html_e('Welcome to Element Pack!', 'bdthemes-element-pack-lite'); ?>
					</h1>
					<p class="ep-dashboard-welcome-desc">
						<?php esc_html_e('Empower your web creation with powerful widgets, advanced extensions, and 2700+ ready templates and more.', 'bdthemes-element-pack-lite'); ?>
					</p>
					<a href="<?php echo esc_url( admin_url('?ep_setup_wizard=show') ); ?>"
						class="bdt-button bdt-welcome-button bdt-margin-small-top"
						target="_blank"><?php esc_html_e('Setup Element Pack', 'bdthemes-element-pack-lite'); ?></a>

					<div class="ep-dashboard-compare-section">
						<h4 class="ep-feature-sub-title">
							<?php
							/* translators: 1: Opening <strong> tag. 2: Closing <strong> tag. */
							printf(esc_html__('Unlock %1$sPremium Features%2$s', 'bdthemes-element-pack-lite'), '<strong class="ep-highlight-text">', '</strong>');
							?>
						</h4>
						<h1 class="ep-feature-title ep-dashboard-compare-title">
							<?php esc_html_e('Create Your Sleek Website with Element Pack Pro!', 'bdthemes-element-pack-lite'); ?>
						</h1>
						<p><?php esc_html_e('Don\'t need more plugins. This pro addon helps you build complex or professional websites—visually stunning, functional and customizable.', 'bdthemes-element-pack-lite'); ?>
						</p>
						<ul>
							<li><?php esc_html_e('Dynamic Content and Integrations', 'bdthemes-element-pack-lite'); ?></li>
							<li><?php esc_html_e('Enhanced Template Library', 'bdthemes-element-pack-lite'); ?></li>
							<li><?php esc_html_e('Theme Builder', 'bdthemes-element-pack-lite'); ?></li>
							<li><?php esc_html_e('Mega Menu Builder', 'bdthemes-element-pack-lite'); ?></li>
							<li><?php esc_html_e('Powerful Widgets and Advanced Extensions', 'bdthemes-element-pack-lite'); ?>
							</li>
						</ul>
						<div class="ep-dashboard-compare-section-buttons">
							<a href="https://www.elementpack.pro/pricing/#a2a0062"
								class="bdt-button bdt-welcome-button bdt-margin-small-right"
								target="_blank"><?php esc_html_e('Compare Free Vs Pro', 'bdthemes-element-pack-lite'); ?></a>
							<a href="https://www.elementpack.pro/pricing?utm_source=ElementPackLite&utm_medium=PluginPage&utm_campaign=ElementPackLite&coupon=FREETOPRO"
								class="bdt-button bdt-dashboard-sec-btn"
								target="_blank"><?php esc_html_e('Get Premium at Up to 83% OFF', 'bdthemes-element-pack-lite'); ?></a>
						</div>
					</div>
				</div>

				<div class="ep-dashboard-item ep-dashboard-template-quick-access bdt-card bdt-card-body">
					<div class="ep-dashboard-template-section">
						<img src="<?php echo esc_url( BDTEP_ADMIN_URL . 'assets/images/template.jpg' ); ?>"
							alt="Element Pack Dashboard Template">
						<h1 class="ep-feature-title ">
							<?php esc_html_e('Faster Web Creation with Sleek and Ready-to-Use Templates!', 'bdthemes-element-pack-lite'); ?>
						</h1>
						<p><?php esc_html_e('Build your wordpress websites of any niche—not from scratch and in a single click.', 'bdthemes-element-pack-lite'); ?>
						</p>
						<a href="https://www.elementpack.pro/ready-templates/"
							class="bdt-button bdt-dashboard-sec-btn bdt-margin-small-top"
							target="_blank"><?php esc_html_e('View Templates', 'bdthemes-element-pack-lite'); ?></a>
					</div>

					<div class="ep-dashboard-quick-access bdt-margin-medium-top">
						<img src="<?php echo esc_url( BDTEP_ADMIN_URL . 'assets/images/support.svg' ); ?>"
							alt="Element Pack Dashboard Template">
						<h1 class="ep-feature-title">
							<?php esc_html_e('Getting Started with Quick Access', 'bdthemes-element-pack-lite'); ?>
						</h1>
						<ul>
							<li><a href="https://www.elementpack.pro/contact/"
									target="_blank"><?php esc_html_e('Contact Us', 'bdthemes-element-pack-lite'); ?></a></li>
							<li><a href="https://bdthemes.com/support/"
									target="_blank"><?php esc_html_e('Help Centre', 'bdthemes-element-pack-lite'); ?></a></li>
							<li><a href="https://feedback.bdthemes.com/b/6vr2250l/feature-requests/idea/new"
									target="_blank"><?php esc_html_e('Request a Feature', 'bdthemes-element-pack-lite'); ?></a>
							</li>
						</ul>
						<div class="ep-dashboard-support-section">
							<h1 class="ep-feature-title">
								<i class="dashicons dashicons-phone"></i>
								<?php esc_html_e('24/7 Support', 'bdthemes-element-pack-lite'); ?>
							</h1>
							<p><?php esc_html_e('Helping you get real-time solutions related to web creation with WordPress, Elementor, and Element Pack.', 'bdthemes-element-pack-lite'); ?>
							</p>
							<a href="https://bdthemes.com/support/" class="bdt-margin-small-top"
								target="_blank"><?php esc_html_e('Get Your Support', 'bdthemes-element-pack-lite'); ?></a>
						</div>
					</div>
				</div>

				<div class="ep-dashboard-item ep-dashboard-request-feature bdt-card bdt-card-body">
					<h1 class="ep-feature-title ep-dashboard-template-quick-title">
						<?php esc_html_e('What\'s Stacking You?', 'bdthemes-element-pack-lite'); ?>
					</h1>
					<p><?php esc_html_e('We are always here to help you. If you have any feature request, please let us know.', 'bdthemes-element-pack-lite'); ?>
					</p>
					<a href="https://feedback.elementpack.pro/b/3v2gg80n/feature-requests/idea/new"
						class="bdt-button bdt-dashboard-sec-btn bdt-margin-small-top"
						target="_blank"><?php esc_html_e('Request Your Features', 'bdthemes-element-pack-lite'); ?></a>
				</div>

				<a href="https://www.youtube.com/watch?v=-e-kr4Vkh4E&list=PLP0S85GEw7DOJf_cbgUIL20qqwqb5x8KA" target="_blank"
					class="ep-dashboard-item ep-dashboard-footer-item ep-dashboard-video-tutorial bdt-card bdt-card-body bdt-card-small">
					<span class="ep-dashboard-footer-item-icon">
						<i class="dashicons dashicons-video-alt3"></i>
					</span>
					<h1 class="ep-feature-title"><?php esc_html_e('Watch Video Tutorials', 'bdthemes-element-pack-lite'); ?></h1>
					<p><?php esc_html_e('An invaluable resource for mastering WordPress, Elementor, and Web Creation', 'bdthemes-element-pack-lite'); ?>
					</p>
				</a>
				<a href="https://bdthemes.com/all-knowledge-base-of-element-pack/" target="_blank"
					class="ep-dashboard-item ep-dashboard-footer-item ep-dashboard-documentation bdt-card bdt-card-body bdt-card-small">
					<span class="ep-dashboard-footer-item-icon">
						<i class="dashicons dashicons-admin-tools"></i>
					</span>
					</span>
					<h1 class="ep-feature-title"><?php esc_html_e('Read Easy Documentation', 'bdthemes-element-pack-lite'); ?></h1>
					<p><?php esc_html_e('A way to eliminate the challenges you might face', 'bdthemes-element-pack-lite'); ?></p>
				</a>
				<a href="https://www.facebook.com/bdthemes" target="_blank"
					class="ep-dashboard-item ep-dashboard-footer-item ep-dashboard-community bdt-card bdt-card-body bdt-card-small">
					<span class="ep-dashboard-footer-item-icon">
						<i class="dashicons dashicons-admin-users"></i>
					</span>
					<h1 class="ep-feature-title"><?php esc_html_e('Join Our Community', 'bdthemes-element-pack-lite'); ?></h1>
					<p><?php esc_html_e('A platform for the opportunity to network, collaboration and innovation', 'bdthemes-element-pack-lite'); ?>
					</p>
				</a>
				<a href="https://wordpress.org/plugins/bdthemes-element-pack-lite/#reviews" target="_blank"
					class="ep-dashboard-item ep-dashboard-footer-item ep-dashboard-review bdt-card bdt-card-body bdt-card-small">
					<span class="ep-dashboard-footer-item-icon">
						<i class="dashicons dashicons-star-filled"></i>
					</span>
					<h1 class="ep-feature-title"><?php esc_html_e('Show Your Love', 'bdthemes-element-pack-lite'); ?></h1>
					<p><?php esc_html_e('A way of the assessment of code', 'bdthemes-element-pack-lite'); ?></p>
				</a>
			</div>

		</div>

		<?php
	}

	/**
	 * Others Plugin - Using standalone plugin manager
	 */
	public function element_pack_others_plugin() {
		// Include and render the standalone others plugin manager
		require_once BDTEP_INC_PATH . 'setup-wizard/element-pack-others-plugin.php';
		
		// Call the helper function to render the plugin manager
		element_pack_others_plugin();
	}

	/**
	 * Widgets Status
	 */

	public function element_pack_widgets_status() {
		$track_nw_msg = '';
		if (!Tracker::is_allow_track()) {
			$track_nw = esc_html__('This feature is not working because the Elementor Usage Data Sharing feature is Not Enabled.', 'bdthemes-element-pack-lite');
			$track_nw_msg = 'bdt-tooltip="' . $track_nw . '"';
		}
		?>
		<div class="ep-dashboard-widgets-status">
			<div class="bdt-grid bdt-grid-medium" bdt-grid bdt-height-match="target: > div > .bdt-card">
				<div class="bdt-width-1-2@m bdt-width-1-4@xl">
					<div class="ep-widget-status bdt-card bdt-card-body" <?php echo wp_kses_post($track_nw_msg); ?>>

						<?php
						$used_widgets = count(self::get_used_widgets());
						$un_used_widgets = count(self::get_unused_widgets());
						?>

						<div class="ep-count-canvas-wrap">
							<h1 class="ep-feature-title"><?php esc_html_e('All Widgets', 'bdthemes-element-pack-lite'); ?></h1>
							<div class="bdt-flex bdt-flex-between bdt-flex-middle">
								<div class="ep-count-wrap">
									<div class="ep-widget-count"><?php esc_html_e('Used:', 'bdthemes-element-pack-lite'); ?> <b>
											<?php echo esc_html($used_widgets); ?>
										</b></div>
									<div class="ep-widget-count"><?php esc_html_e('Unused:', 'bdthemes-element-pack-lite'); ?> <b>
											<?php echo esc_html($un_used_widgets); ?>
										</b>
									</div>
									<div class="ep-widget-count"><?php esc_html_e('Total:', 'bdthemes-element-pack-lite'); ?>
										<b>
											<?php echo esc_html($used_widgets + $un_used_widgets); ?>
										</b>
									</div>
								</div>

								<div class="ep-canvas-wrap">
									<canvas id="bdt-db-total-status" style="height: 100px; width: 100px;"
										data-label="Total Widgets Status - (<?php echo esc_html($used_widgets + $un_used_widgets); ?>)"
										data-labels="<?php echo esc_attr('Used, Unused'); ?>"
										data-value="<?php echo esc_attr($used_widgets) . ',' . esc_attr($un_used_widgets); ?>"
										data-bg="#FFD166, #fff4d9" data-bg-hover="#0673e1, #e71522"></canvas>
								</div>
							</div>
						</div>

					</div>
				</div>
				<div class="bdt-width-1-2@m bdt-width-1-4@xl">
					<div class="ep-widget-status bdt-card bdt-card-body" <?php echo wp_kses_post($track_nw_msg); ?>>

						<?php
						$used_only_widgets = count(self::get_used_only_widgets());
						$unused_only_widgets = count(self::get_unused_only_widgets());
						?>


						<div class="ep-count-canvas-wrap">
							<h1 class="ep-feature-title"><?php esc_html_e('Core', 'bdthemes-element-pack-lite'); ?></h1>
							<div class="bdt-flex bdt-flex-between bdt-flex-middle">
								<div class="ep-count-wrap">
									<div class="ep-widget-count"><?php esc_html_e('Used:', 'bdthemes-element-pack-lite'); ?> <b>
											<?php echo esc_html($used_only_widgets); ?>
										</b></div>
									<div class="ep-widget-count"><?php esc_html_e('Unused:', 'bdthemes-element-pack-lite'); ?> <b>
											<?php echo esc_html($unused_only_widgets); ?>
										</b></div>
									<div class="ep-widget-count"><?php esc_html_e('Total:', 'bdthemes-element-pack-lite'); ?>
										<b>
											<?php echo esc_html($used_only_widgets + $unused_only_widgets); ?>
										</b>
									</div>
								</div>

								<div class="ep-canvas-wrap">
									<canvas id="bdt-db-only-widget-status" style="height: 100px; width: 100px;"
										data-label="Core Widgets Status - (<?php echo esc_html($used_only_widgets + $unused_only_widgets); ?>)"
										data-labels="<?php echo esc_attr('Used, Unused'); ?>"
										data-value="<?php echo esc_attr($used_only_widgets) . ',' . esc_attr($unused_only_widgets); ?>"
										data-bg="#EF476F, #ffcdd9" data-bg-hover="#0673e1, #e71522"></canvas>
								</div>
							</div>
						</div>

					</div>
				</div>
				<div class="bdt-width-1-2@m bdt-width-1-4@xl">
					<div class="ep-widget-status bdt-card bdt-card-body" <?php echo wp_kses_post($track_nw_msg); ?>>

						<?php
						$used_only_3rdparty = count(self::get_used_only_3rdparty());
						$unused_only_3rdparty = count(self::get_unused_only_3rdparty());
						?>


						<div class="ep-count-canvas-wrap">
							<h1 class="ep-feature-title"><?php esc_html_e('3rd Party', 'bdthemes-element-pack-lite'); ?></h1>
							<div class="bdt-flex bdt-flex-between bdt-flex-middle">
								<div class="ep-count-wrap">
									<div class="ep-widget-count"><?php esc_html_e('Used:', 'bdthemes-element-pack-lite'); ?> <b>
											<?php echo esc_html($used_only_3rdparty); ?>
										</b></div>
									<div class="ep-widget-count"><?php esc_html_e('Unused:', 'bdthemes-element-pack-lite'); ?> <b>
											<?php echo esc_html($unused_only_3rdparty); ?>
										</b></div>
									<div class="ep-widget-count"><?php esc_html_e('Total:', 'bdthemes-element-pack-lite'); ?>
										<b>
											<?php echo esc_html($used_only_3rdparty + $unused_only_3rdparty); ?>
										</b>
									</div>
								</div>

								<div class="ep-canvas-wrap">
									<canvas id="bdt-db-only-3rdparty-status" style="height: 100px; width: 100px;"
										data-label="3rd Party Widgets Status - (<?php echo esc_html($used_only_3rdparty + $unused_only_3rdparty); ?>)"
										data-labels="<?php echo esc_attr('Used, Unused'); ?>"
										data-value="<?php echo esc_attr($used_only_3rdparty) . ',' . esc_attr($unused_only_3rdparty); ?>"
										data-bg="#06D6A0, #B6FFEC" data-bg-hover="#0673e1, #e71522"></canvas>
								</div>
							</div>
						</div>

					</div>
				</div>

				<div class="bdt-width-1-2@m bdt-width-1-4@xl">
					<div class="ep-widget-status bdt-card bdt-card-body" <?php echo wp_kses_post($track_nw_msg); ?>>

						<div class="ep-count-canvas-wrap">
							<h1 class="ep-feature-title"><?php esc_html_e('Active', 'bdthemes-element-pack-lite'); ?></h1>
							<div class="bdt-flex bdt-flex-between bdt-flex-middle">
								<div class="ep-count-wrap">
									<div class="ep-widget-count"><?php esc_html_e('Core:', 'bdthemes-element-pack-lite'); ?> <b
											id="bdt-total-widgets-status-core">0</b></div>
									<div class="ep-widget-count"><?php esc_html_e('3rd Party:', 'bdthemes-element-pack-lite'); ?>
										<b id="bdt-total-widgets-status-3rd">0</b>
									</div>
									<div class="ep-widget-count"><?php esc_html_e('Extensions:', 'bdthemes-element-pack-lite'); ?>
										<b id="bdt-total-widgets-status-extensions">0</b>
									</div>
									<div class="ep-widget-count"><?php esc_html_e('Total:', 'bdthemes-element-pack-lite'); ?> <b
											id="bdt-total-widgets-status-heading">0</b></div>
								</div>

								<div class="ep-canvas-wrap">
									<canvas id="bdt-total-widgets-status" style="height: 100px; width: 100px;"
										data-label="Total Active Widgets Status"
										data-labels="<?php echo esc_attr('Core, 3rd Party, Extensions'); ?>"
										data-value="0,0,0"
										data-bg="#0680d6, #B0EBFF, #E6F9FF" data-bg-hover="#0673e1, #B0EBFF, #b6f9e8">
									</canvas>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>

		<?php if (!Tracker::is_allow_track()): ?>
			<div class="bdt-border-rounded bdt-box-shadow-small bdt-alert-warning" bdt-alert>
				<a href class="bdt-alert-close" bdt-close></a>
				<div class="bdt-text-default">
				<?php
					printf(
						/* translators: 1: Opening <b> tag. 2: Closing <b> tag. */
						esc_html__('To view widgets analytics, Elementor %1$sUsage Data Sharing%2$s feature by Elementor needs to be activated. Please activate the feature to get widget analytics instantly ', 'bdthemes-element-pack-lite'),
						'<b>', '</b>'
					);

					echo ' <a href="' . esc_url(admin_url('admin.php?page=elementor-settings')) . '">' . esc_html__('from here.', 'bdthemes-element-pack-lite') . '</a>';
				?>
				</div>
			</div>
		<?php endif; ?>

		<?php
	}

	/**
	 * Get Pro
	 *
	 * @access public
	 * @return void
	 */

	function element_pack_get_pro() {
		?>
		<div class="ep-dashboard-panel"
			bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">

			<div class="bdt-grid" bdt-grid bdt-height-match="target: > div > .bdt-card"
				style="max-width: 1180px; margin-left: auto; margin-right: auto;">
				<div class="bdt-width-1-1@m ep-comparision bdt-text-center">
					<div class="bdt-flex bdt-flex-between bdt-flex-middle">
						<div class="bdt-text-left">
							<h1 class="bdt-text-bold"><?php echo esc_html__( 'WHY GO WITH PRO?', 'bdthemes-element-pack-lite' ); ?></h1>
							<h2><?php echo esc_html__( 'Just Compare With Element Pack Lite Vs Pro', 'bdthemes-element-pack-lite' ); ?></h2>
						</div>
						<div class="ep-purchase-button">
							<a href="https://elementpack.pro/pricing/" target="_blank"><?php echo esc_html__( 'Purchase Now', 'bdthemes-element-pack-lite' ); ?></a>
						</div>
					</div>

					<div>

						<ul class="bdt-list bdt-list-divider bdt-text-left bdt-text-normal" style="font-size: 15px;">


							<li class="bdt-text-bold">
								<div class="bdt-grid">
									<?php
									echo '<div class="bdt-width-expand@m">' . esc_html__( 'Features', 'bdthemes-element-pack-lite' ) . '</div>';
									echo '<div class="bdt-width-auto@m">' . esc_html__( 'Free', 'bdthemes-element-pack-lite' ) . '</div>';
									echo '<div class="bdt-width-auto@m">' . esc_html__( 'Pro', 'bdthemes-element-pack-lite' ) . '</div>';
									?>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m"><span
											bdt-tooltip="pos: top-left; title: <?php echo esc_html__( 'Lite have 35+ Widgets but Pro have 100+ core widgets', 'bdthemes-element-pack-lite' ); ?>"><?php echo esc_html__( 'Core Widgets', 'bdthemes-element-pack-lite' ); ?></span>
									</div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<?php echo '<div class="bdt-width-expand@m">' . esc_html__( 'Theme Compatibility', 'bdthemes-element-pack-lite' ) . '</div>'; ?>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<?php echo '<div class="bdt-width-expand@m">' . esc_html__( 'Dynamic Content & Custom Fields Capabilities', 'bdthemes-element-pack-lite' ) . '</div>'; ?>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<?php echo '<div class="bdt-width-expand@m">' . esc_html__( 'Proper Documentation', 'bdthemes-element-pack-lite' ) . '</div>'; ?>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<?php echo '<div class="bdt-width-expand@m">' . esc_html__( 'Updates & Support', 'bdthemes-element-pack-lite' ) . '</div>'; ?>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Ready Made Pages', 'bdthemes-element-pack-lite' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Ready Made Blocks', 'bdthemes-element-pack-lite' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Elementor Extended Widgets', 'bdthemes-element-pack-lite' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Asset Manager', 'bdthemes-element-pack-lite' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Live Copy or Paste', 'bdthemes-element-pack-lite' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Template Library (in Editor)', 'bdthemes-element-pack-lite' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'SVG Support', 'bdthemes-element-pack-lite' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<a href="https://www.elementpack.pro/demo/element/dynamic-content/" target="_blank">
											<?php echo esc_html__( 'Dynamic Content', 'bdthemes-element-pack-lite' ); ?>
										</a>
									</div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Smooth Scroller', 'bdthemes-element-pack-lite' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							
							<li class="">
								<div class="bdt-grid">
									<?php echo '<div class="bdt-width-expand@m">' . esc_html__( 'Header & Footer Builder', 'bdthemes-element-pack-lite' ) . '</div>'; ?>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Rooten Theme Pro Features', 'bdthemes-element-pack-lite' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Priority Support', 'bdthemes-element-pack-lite' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'WooCommerce Widgets', 'bdthemes-element-pack-lite' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Ready Made Header & Footer', 'bdthemes-element-pack-lite' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Essential Shortcodes', 'bdthemes-element-pack-lite' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Context Menu', 'bdthemes-element-pack-lite' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
						</ul>

						<div class="ep-more-features bdt-card bdt-card-body bdt-card-default bdt-margin-medium-top bdt-padding-large">
							<ul class="bdt-list bdt-list-divider bdt-text-left bdt-margin-remove" style="font-size: 15px;">
								<li>
									<div class="bdt-grid bdt-grid-small">
										<?php
										echo '<div class="bdt-width-1-3@m"><span class="dashicons dashicons-heart"></span> ' . esc_html__( ' Incredibly Advanced', 'bdthemes-element-pack-lite' ) . '</div>';
										echo '<div class="bdt-width-1-3@m"><span class="dashicons dashicons-heart"></span> ' . esc_html__( ' Refund or Cancel Anytime', 'bdthemes-element-pack-lite' ) . '</div>';
										echo '<div class="bdt-width-1-3@m"><span class="dashicons dashicons-heart"></span> ' . esc_html__( ' Dynamic Content', 'bdthemes-element-pack-lite' ) . '</div>';
										?>
									</div>
								</li>

								<li>
									<div class="bdt-grid bdt-grid-small">
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span>
											<?php echo esc_html__( 'Super-Flexible Widgets', 'bdthemes-element-pack-lite' ); ?>
										</div>
										<div class="bdt-width-1-3@m">
											<span
												class="dashicons dashicons-heart"></span><?php echo esc_html__( ' 24/7 Premium Support', 'bdthemes-element-pack-lite' ); ?>
										</div>
										<div class="bdt-width-1-3@m">
											<span
												class="dashicons dashicons-heart"></span><?php echo esc_html__( ' Third Party Plugins', 'bdthemes-element-pack-lite' ); ?>
										</div>
									</div>
								</li>

								<li>
									<div class="bdt-grid bdt-grid-small">
										<div class="bdt-width-1-3@m">
											<span
												class="dashicons dashicons-heart"></span><?php echo esc_html__( ' Special Discount!', 'bdthemes-element-pack-lite' ); ?>
										</div>
										<div class="bdt-width-1-3@m">
											<span
												class="dashicons dashicons-heart"></span><?php echo esc_html__( ' Custom Field Integration', 'bdthemes-element-pack-lite' ); ?>
										</div>
										<div class="bdt-width-1-3@m">
											<span
												class="dashicons dashicons-heart"></span><?php echo esc_html__( ' With Live Chat Support', 'bdthemes-element-pack-lite' ); ?>
										</div>
									</div>
								</li>

								<li>
									<div class="bdt-grid bdt-grid-small">
										<div class="bdt-width-1-3@m">
											<span
												class="dashicons dashicons-heart"></span><?php echo esc_html__( ' Trusted Payment Methods', 'bdthemes-element-pack-lite' ); ?>
										</div>
										<div class="bdt-width-1-3@m">
											<span
												class="dashicons dashicons-heart"></span><?php echo esc_html__( ' Interactive Effects', 'bdthemes-element-pack-lite' ); ?>
										</div>
										<div class="bdt-width-1-3@m">
											<span
												class="dashicons dashicons-heart"></span><?php echo esc_html__( ' Video Tutorial', 'bdthemes-element-pack-lite' ); ?>
										</div>
									</div>
								</li>
							</ul>

							<div class="ep-purchase-button bdt-margin-medium-top">
								<a href="https://elementpack.pro/pricing/" target="_blank"><?php echo esc_html__( 'Purchase Now', 'bdthemes-element-pack-lite' ); ?></a>
							</div>

						</div>

					</div>
				</div>
			</div>

		</div>
		<?php
	}

	/**
	 * Display System Requirement
	 *
	 * @access public
	 * @return void
	 */

	public function element_pack_system_requirement() {
		$php_version = phpversion();
		$max_execution_time = ini_get('max_execution_time');
		$memory_limit = ini_get('memory_limit');
		$post_limit = ini_get('post_max_size');
		$uploads = wp_upload_dir();
		$upload_path = $uploads['basedir'];
		$yes_icon = '<span class="valid"><i class="dashicons-before dashicons-yes"></i></span>';
		$no_icon = '<span class="invalid"><i class="dashicons-before dashicons-no-alt"></i></span>';

		$environment = Utils::get_environment_info();

		?>
		<ul class="check-system-status bdt-grid bdt-child-width-1-2@m  bdt-grid-small ">
			<li>
				<div>
					<span class="label1"><?php esc_html_e('PHP Version:', 'bdthemes-element-pack-lite'); ?></span>

					<?php
					if (version_compare($php_version, '7.4.0', '<')) {
						echo wp_kses_post($no_icon);
						echo '<span class="label2" title="' . esc_attr__('Min: 7.4 Recommended', 'bdthemes-element-pack-lite') . '" bdt-tooltip>' . esc_html__('Currently:', 'bdthemes-element-pack-lite') . ' ' . esc_html($php_version) . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">' . esc_html__('Currently:', 'bdthemes-element-pack-lite') . ' ' . esc_html($php_version) . '</span>';
					}
					?>
				</div>

			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('Max execution time:', 'bdthemes-element-pack-lite'); ?> </span>
					<?php
					if ($max_execution_time < '90') {
						echo wp_kses_post($no_icon);
						echo '<span class="label2" title="' . esc_attr__('Min: 90 Recommended', 'bdthemes-element-pack-lite') . '" bdt-tooltip>' . esc_html__('Currently:', 'bdthemes-element-pack-lite') . ' ' . esc_html($max_execution_time) . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">' . esc_html__('Currently:', 'bdthemes-element-pack-lite') . ' ' . esc_html($max_execution_time) . '</span>';
					}
					?>
				</div>
			</li>
			<li>
				<div>
					<span class="label1"><?php esc_html_e('Memory Limit:', 'bdthemes-element-pack-lite'); ?> </span>

					<?php
					if (intval($memory_limit) < '512') {
						echo wp_kses_post($no_icon);
						echo '<span class="label2" title="' . esc_attr__('Min: 512M Recommended', 'bdthemes-element-pack-lite') . '" bdt-tooltip>' . esc_html__('Currently:', 'bdthemes-element-pack-lite') . ' ' . esc_html($memory_limit) . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">' . esc_html__('Currently:', 'bdthemes-element-pack-lite') . ' ' . esc_html($memory_limit) . '</span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('Max Post Limit:', 'bdthemes-element-pack-lite'); ?> </span>

					<?php
					if (intval($post_limit) < '32') {
						echo wp_kses_post($no_icon);
						echo '<span class="label2" title="' . esc_attr__('Min: 32M Recommended', 'bdthemes-element-pack-lite') . '" bdt-tooltip>' . esc_html__('Currently:', 'bdthemes-element-pack-lite') . ' ' . wp_kses_post($post_limit) . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">' . esc_html__('Currently:', 'bdthemes-element-pack-lite') . ' ' . wp_kses_post($post_limit) . '</span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('Uploads folder writable:', 'bdthemes-element-pack-lite'); ?></span>

					<?php
					if (!wp_is_writable($upload_path)) {
						echo wp_kses_post($no_icon);
					} else {
						echo wp_kses_post($yes_icon);
					}
					?>
				</div>

			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('GZip Enabled:', 'bdthemes-element-pack-lite'); ?></span>

					<?php
					if ($environment['gzip_enabled']) {
						echo wp_kses_post($yes_icon);
					} else {
						echo wp_kses_post($no_icon);
					}
					?>
				</div>

			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('Debug Mode:', 'bdthemes-element-pack-lite'); ?></span>
					<?php
					if ($environment['wp_debug_mode']) {
						echo wp_kses_post($no_icon);
						echo '<span class="label2">' . esc_html__('Currently Turned On', 'bdthemes-element-pack-lite') . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">' . esc_html__('Currently Turned Off', 'bdthemes-element-pack-lite') . '</span>';
					}
					?>
				</div>

			</li>

		</ul>

		<div class="bdt-admin-alert">
			<strong><?php esc_html_e('Note:', 'bdthemes-element-pack-lite'); ?></strong>
			<?php
			printf(
				/* translators: %s: Plugin name, wrapped in bold tags. */
				esc_html__('If you have multiple addons like %s so you may need to allocate additional memory for other addons as well.', 'bdthemes-element-pack-lite'),
				'<b>Element Pack</b>'
			);
			?>
		</div>

		<?php
	}

	/**
	 * Display Plugin Page
	 *
	 * @access public
	 * @return void
	 */

	public function plugin_page() {

		?>

		<div class="wrap element-pack-dashboard">
			<h1></h1> <!-- don't remove this div, it's used for the notice container -->
		
			<div class="ep-dashboard-wrapper bdt-margin-top">
				<div class="ep-dashboard-header bdt-flex bdt-flex-wrap bdt-flex-between bdt-flex-middle"
					bdt-sticky="offset: 32; animation: bdt-animation-slide-top-small; duration: 300">

					<div class="bdt-flex bdt-flex-wrap bdt-flex-middle">
						<!-- Header Shape Elements -->
						<div class="ep-header-elements">
							<span class="ep-header-element ep-header-circle"></span>
							<span class="ep-header-element ep-header-dots"></span>
							<span class="ep-header-element ep-header-line"></span>
							<span class="ep-header-element ep-header-square"></span>
							<span class="ep-header-element ep-header-wave"></span>
						</div>

						<div class="ep-logo">
							<img src="<?php echo esc_url( BDTEP_URL . 'assets/images/logo-with-text.svg' ); ?>" alt="Element Pack Logo">
						</div>
					</div>

					<div class="ep-dashboard-new-page-wrapper bdt-flex bdt-flex-wrap bdt-flex-middle">
						

						<!-- Always render save button, JavaScript will control visibility -->
						<div class="ep-dashboard-save-btn" style="display: none;">
							<button class="bdt-button bdt-button-primary element-pack-settings-save-btn" type="submit">
								<?php esc_html_e('Save Settings', 'bdthemes-element-pack-lite'); ?>
							</button>
						</div>

						<div class="ep-dashboard-new-page">
							<a class="bdt-flex bdt-flex-middle" href="<?php echo esc_url(admin_url('post-new.php?post_type=page')); ?>" class=""><i class="dashicons dashicons-admin-page"></i>
								<?php echo esc_html__('Create New Page', 'bdthemes-element-pack-lite') ?>
							</a>
						</div>
						
					</div>

				</div>

				<div class="ep-dashboard-container bdt-flex">
					<div class="ep-dashboard-nav-container-wrapper">
						<div class="ep-dashboard-nav-container-inner" bdt-sticky="end: !.ep-dashboard-container; offset: 115; animation: bdt-animation-slide-top-small; duration: 300">

							<!-- Navigation Shape Elements -->
							<div class="ep-nav-elements">
								<span class="ep-nav-element ep-nav-circle"></span>
								<span class="ep-nav-element ep-nav-dots"></span>
								<span class="ep-nav-element ep-nav-line"></span>
								<span class="ep-nav-element ep-nav-square"></span>
								<span class="ep-nav-element ep-nav-triangle"></span>
								<span class="ep-nav-element ep-nav-plus"></span>
								<span class="ep-nav-element ep-nav-wave"></span>
							</div>

							<?php $this->settings_api->show_navigation(); ?>
						</div>
					</div>


					<div class="bdt-switcher bdt-tab-container bdt-container-xlarge bdt-flex-1">
						<div id="element_pack_welcome_page" class="ep-option-page group">
							<?php $this->element_pack_welcome(); ?>
						</div>

						<?php
						$this->settings_api->show_forms();
						?>

						<div id="element_pack_analytics_system_req_page" class="ep-option-page group">
							<?php $this->element_pack_analytics_system_req_content(); ?>
						</div>

						<div id="element_pack_other_plugins_page" class="ep-option-page group">
							<?php $this->element_pack_others_plugin(); ?>
						</div>

						<div id="element_pack_get_pro_page" class="ep-option-page group">
							<?php $this->element_pack_get_pro(); ?>
						</div>

					</div>
				</div>

				<?php $this->footer_info(); ?>
			</div>

		</div>

		<?php

		$this->script();

		?>

		<?php
	}






	/**
	 * Tabbable JavaScript codes & Initiate Color Picker
	 *
	 * This code uses localstorage for displaying active tabs
	 */
	public function script() {
		?>
		<script>
			jQuery(document).ready(function () {
				jQuery('.ep-no-result').removeClass('bdt-animation-shake');
			});

			// Selector of the filter controls that are currently active (Free is active by default).
			function activeFilterSelector($parent) {
				return $parent.find('.ep-widget-filter li.bdt-active')
					.map(function () {
						var control = jQuery(this).attr('bdt-filter-control') || '';
						var matched = control.match(/filter:\s*([^;]+)/);
						return matched ? jQuery.trim(matched[1]) : null;
					})
					.get()
					.join('');
			}

			function filterSearch(e) {
				var parentID = '#' + jQuery(e).data('id');
				var $parent = jQuery(parentID);
				var search = $parent.find('.bdt-search-input').val().toLowerCase();

				// Search runs on top of the active filter and only inside its own tab, so clearing
				// the search (or switching tabs) never falls back to showing every widget.
				var filterSelector = activeFilterSelector($parent);

				$parent.find('.ep-options .ep-option-item').each(function () {
					var name = (jQuery(this).attr('data-widget-name') || '').toLowerCase();
					var matchesSearch = name.indexOf(search) > -1;
					var matchesFilter = !filterSelector || jQuery(this).is(filterSelector);

					jQuery(this).toggle(matchesSearch && matchesFilter);
				});

				if (!search) {
					$parent.find('.bdt-search-input').attr('bdt-filter-control', "");
				} else {
					$parent.find('.bdt-search-input').attr('bdt-filter-control', "filter: [data-widget-name*='" + search + "']");
					$parent.find('.bdt-search-input').removeClass('bdt-active');
				}
			}


			jQuery('.ep-options-parent').each(function (e, item) {
				var eachItem = '#' + jQuery(item).attr('id');
				jQuery(eachItem).on("beforeFilter", function () {
					jQuery(eachItem).find('.ep-no-result').removeClass('bdt-animation-shake');
				});

				jQuery(eachItem).on("afterFilter", function () {
					var isElementVisible = false;
					var i = 0;

					if (jQuery(eachItem).closest(".ep-options-parent").eq(i).is(":visible")) { } else {
						isElementVisible = true;
					}

					while (!isElementVisible && i < jQuery(eachItem).find(".ep-option-item").length) {
						if (jQuery(eachItem).find(".ep-option-item").eq(i).is(":visible")) {
							isElementVisible = true;
						}
						i++;
					}

					if (isElementVisible === false) {
						jQuery(eachItem).find('.ep-no-result').addClass('bdt-animation-shake');
					}

				});
			});

			function clearSearchInputs(context) {
				context.find('.bdt-search-input').val('').attr('bdt-filter-control', '');
			}

			jQuery('.ep-widget-filter-nav li a').on('click', function () {
				// Scroll to top when filter tabs are clicked
				window.scrollTo({
					top: 0,
					behavior: 'smooth'
				});
				
				const wrapper = jQuery(this).closest('.bdt-widget-filter-wrapper');
				clearSearchInputs(wrapper);
			});

			jQuery('.bdt-dashboard-navigation li a').on('click', function () {
				// Scroll to top when main navigation tabs are clicked
				window.scrollTo({
					top: 0,
					behavior: 'smooth'
				});
				
				const tabContainer = jQuery(this).closest('.ep-dashboard-nav-container-wrapper').siblings('.bdt-tab-container');
				clearSearchInputs(tabContainer);
				tabContainer.find('.bdt-search-input').trigger('keyup');
			});

			jQuery(document).ready(function ($) {
				'use strict';

				// Improved hash handler for tab switching
				function hashHandler() {
					if (window.location.hash) {
						var hash = window.location.hash.substring(1);
						
						// Handle different hash formats
						var targetPage = hash;
						if (hash.includes('_page')) {
							targetPage = hash.replace('_page', '');
						}
						
						// Find the navigation tab that corresponds to this hash
						var $navItem = $('.bdt-dashboard-navigation a[href="#' + targetPage + '"], .bdt-dashboard-navigation a[href="#' + hash + '"]').first();
						
						if ($navItem.length > 0) {
							var tabIndex = $navItem.data('tab-index');
							if (typeof tabIndex !== 'undefined') {
								// Use UIkit tab system
								var $tab = $('.element-pack-dashboard .bdt-tab');
								if (typeof bdtUIkit !== 'undefined' && bdtUIkit.tab) {
									bdtUIkit.tab($tab).show(tabIndex);
								}
							}
						}
					}
				}

				// Handle initial page load
				function onWindowLoad() {
					hashHandler();
				}

				// Initialize on document ready and window load
				if (document.readyState === 'complete') {
					onWindowLoad();
				} else {
					$(window).on('load', onWindowLoad);
				}

				// Listen for hash changes
				window.addEventListener("hashchange", hashHandler, true);

				// Handle admin menu clicks (from WordPress admin sidebar)
				$('.toplevel_page_element_pack_options > ul > li > a').on('click', function (event) {
					// Scroll to top when admin sub menu items are clicked
					window.scrollTo({
						top: 0,
						behavior: 'smooth'
					});
					
					$(this).parent().siblings().removeClass('current');
					$(this).parent().addClass('current');
					
					// Extract hash from href and trigger hash change
					var href = $(this).attr('href');
					if (href && href.includes('#')) {
						var hash = href.substring(href.indexOf('#'));
						if (hash && hash.length > 1) {
							window.location.hash = hash;
						}
					}
				});

				// Handle navigation tab clicks
				$('.bdt-dashboard-navigation a').on('click', function(e) {
					// Scroll to top immediately when tab is clicked
					window.scrollTo({
						top: 0,
						behavior: 'smooth'
					});
					
					var href = $(this).attr('href');
					if (href && href.startsWith('#')) {
						// Update URL hash for proper navigation
						window.history.pushState(null, null, href);
						
						// Trigger hash change for proper tab switching
						setTimeout(function() {
							$(window).trigger('hashchange');
						}, 50);
					}
				});

				// Handle filter navigation clicks (All, Free, Pro, etc.)
				$('.ep-widget-filter-nav li a').on('click', function() {
					// Scroll to top when filter tabs are clicked
					window.scrollTo({
						top: 0,
						behavior: 'smooth'
					});
					
					const wrapper = jQuery(this).closest('.bdt-widget-filter-wrapper');
					clearSearchInputs(wrapper);
				});

				// Handle sub-navigation clicks (within widget pages)
				$(document).on('click', '.bdt-subnav a, .ep-widget-filter a', function() {
					// Scroll to top for sub-navigation clicks
					window.scrollTo({
						top: 0,
						behavior: 'smooth'
					});
				});

				// Enhanced tab switching with scroll to top
				$(document).on('click', '.bdt-tab a, .bdt-tab-item', function(e) {
					// Scroll to top for any tab click
					window.scrollTo({
						top: 0,
						behavior: 'smooth'
					});
				});

				// Advanced tab switching for direct URL access
				function switchToTab(targetId) {
					var $navItem = $('.bdt-dashboard-navigation a[href="#' + targetId + '"]');
					if ($navItem.length > 0) {
						var tabIndex = $navItem.data('tab-index');
						if (typeof tabIndex !== 'undefined') {
							var $tab = $('.element-pack-dashboard .bdt-tab');
							if (typeof bdtUIkit !== 'undefined' && bdtUIkit.tab) {
								bdtUIkit.tab($tab).show(tabIndex);
							}
						}
					}
				}

				// Handle direct section navigation from external links
				$(document).on('click', 'a[href*="#element_pack"]', function(e) {
					var href = $(this).attr('href');
					if (href && href.includes('#element_pack')) {
						var hash = href.substring(href.indexOf('#element_pack'));
						var targetId = hash.substring(1);
						
						// Navigate to the tab
						switchToTab(targetId);
						
						// Update URL
						window.history.pushState(null, null, hash);
					}
				});

				// Activate/Deactivate all widgets functionality
				$('#element_pack_active_modules_page a.ep-active-all-widget').on('click', function (e) {
					e.preventDefault();

					$('#element_pack_active_modules_page .ep-option-item:not(.ep-pro-inactive) .checkbox:visible').each(function () {
						$(this).attr('checked', 'checked').prop("checked", true);
					});

					$(this).addClass('bdt-active');
					$('#element_pack_active_modules_page a.ep-deactive-all-widget').removeClass('bdt-active');
					
					// Ensure save button remains visible
					setTimeout(function() {
						$('.ep-dashboard-save-btn').show();
					}, 100);
				});

				$('#element_pack_active_modules_page a.ep-deactive-all-widget').on('click', function (e) {
					e.preventDefault();

					$('#element_pack_active_modules_page .checkbox:visible').each(function () {
						$(this).removeAttr('checked').prop("checked", false);
					});

					$(this).addClass('bdt-active');
					$('#element_pack_active_modules_page a.ep-active-all-widget').removeClass('bdt-active');
					
					// Ensure save button remains visible
					setTimeout(function() {
						$('.ep-dashboard-save-btn').show();
					}, 100);
				});

				$('#element_pack_third_party_widget_page a.ep-active-all-widget').on('click', function (e) {
					e.preventDefault();

					$('#element_pack_third_party_widget_page .ep-option-item:not(.ep-pro-inactive) .checkbox:visible').each(function () {
						$(this).attr('checked', 'checked').prop("checked", true);
					});

					$(this).addClass('bdt-active');
					$('#element_pack_third_party_widget_page a.ep-deactive-all-widget').removeClass('bdt-active');
					
					// Ensure save button remains visible
					setTimeout(function() {
						$('.ep-dashboard-save-btn').show();
					}, 100);
				});

				$('#element_pack_third_party_widget_page a.ep-deactive-all-widget').on('click', function (e) {
					e.preventDefault();

					$('#element_pack_third_party_widget_page .checkbox:visible').each(function () {
						$(this).removeAttr('checked').prop("checked", false);
					});

					$(this).addClass('bdt-active');
					$('#element_pack_third_party_widget_page a.ep-active-all-widget').removeClass('bdt-active');
					
					// Ensure save button remains visible
					setTimeout(function() {
						$('.ep-dashboard-save-btn').show();
					}, 100);
				});

				$('#element_pack_elementor_extend_page a.ep-active-all-widget').on('click', function (e) {
					e.preventDefault();

					$('#element_pack_elementor_extend_page .ep-option-item:not(.ep-pro-inactive) .checkbox:visible').each(function () {
						$(this).attr('checked', 'checked').prop("checked", true);
					});

					$(this).addClass('bdt-active');
					$('#element_pack_elementor_extend_page a.ep-deactive-all-widget').removeClass('bdt-active');
					
					// Ensure save button remains visible
					setTimeout(function() {
						$('.ep-dashboard-save-btn').show();
					}, 100);
				});

				$('#element_pack_elementor_extend_page a.ep-deactive-all-widget').on('click', function (e) {
					e.preventDefault();

					$('#element_pack_elementor_extend_page .checkbox:visible').each(function () {
						$(this).removeAttr('checked').prop("checked", false);
					});

					$(this).addClass('bdt-active');
					$('#element_pack_elementor_extend_page a.ep-active-all-widget').removeClass('bdt-active');
					
					// Ensure save button remains visible
					setTimeout(function() {
						$('.ep-dashboard-save-btn').show();
					}, 100);
				});

				$('#element_pack_active_modules_page, #element_pack_third_party_widget_page, #element_pack_elementor_extend_page, #element_pack_other_settings_page').find('.ep-pro-inactive .checkbox').each(function () {
					$(this).removeAttr('checked');
					$(this).attr("disabled", true);
				});

			});

			// License Renew Redirect
			jQuery(document).ready(function ($) {
				const renewalLink = $('a[href="admin.php?page=element_pack_options_license_renew"]');
				if (renewalLink.length) {
					renewalLink.attr('target', '_blank');
				}
			});

			// License Renew Redirect
			jQuery(document).ready(function ($) {
				const renewalLink = $('a[href="admin.php?page=element_pack_options_license_renew"]');
				if (renewalLink.length) {
					renewalLink.attr('target', '_blank');
				}
			});

			// Dynamic Save Button Control
			jQuery(document).ready(function ($) {
				// Define pages that need save button - only specific settings pages
				const pagesWithSave = [
					'element_pack_active_modules',        // Core widgets
					'element_pack_third_party_widget',    // 3rd party widgets  
					'element_pack_elementor_extend',      // Extensions
					'element_pack_other_settings',        // Special features
					'element_pack_api_settings'           // API settings
				];

				function toggleSaveButton() {
					const currentHash = window.location.hash.substring(1);
					const saveButton = $('.ep-dashboard-save-btn');
					
					// Check if current page should have save button
					if (pagesWithSave.includes(currentHash)) {
						saveButton.fadeIn(200);
					} else {
						saveButton.fadeOut(200);
					}
				}

				// Force save button to be visible for settings pages
				function forceSaveButtonVisible() {
					const currentHash = window.location.hash.substring(1);
					const saveButton = $('.ep-dashboard-save-btn');
					
					if (pagesWithSave.includes(currentHash)) {
						saveButton.show();
					}
				}

				// Initial check
				toggleSaveButton();

				// Listen for hash changes
				$(window).on('hashchange', function() {
					toggleSaveButton();
				});

				// Listen for tab clicks
				$('.bdt-dashboard-navigation a').on('click', function() {
					setTimeout(toggleSaveButton, 100);
				});

				// Also listen for navigation menu clicks (from show_navigation())
				$(document).on('click', '.bdt-tab a, .bdt-subnav a, .ep-dashboard-nav a, [href*="#element_pack"]', function() {
					setTimeout(toggleSaveButton, 100);
				});

				// Listen for bulk active/deactive button clicks to maintain save button visibility
				$(document).on('click', '.ep-active-all-widget, .ep-deactive-all-widget', function() {
					setTimeout(forceSaveButtonVisible, 50);
				});

				// Listen for individual checkbox changes to maintain save button visibility
				$(document).on('change', '#element_pack_third_party_widget_page .checkbox, #element_pack_elementor_extend_page .checkbox, #element_pack_active_modules_page .checkbox', function() {
					setTimeout(forceSaveButtonVisible, 50);
				});

				// Update URL when navigation items are clicked
				$(document).on('click', '.bdt-tab a, .bdt-subnav a, .ep-dashboard-nav a', function(e) {
					const href = $(this).attr('href');
					if (href && href.includes('#')) {
						const hash = href.substring(href.indexOf('#'));
						if (hash && hash.length > 1) {
							// Update browser URL with the hash
							const currentUrl = window.location.href.split('#')[0];
							const newUrl = currentUrl + hash;
							window.history.pushState(null, null, newUrl);
							
							// Trigger hash change event for other listeners
							$(window).trigger('hashchange');
						}
					}
				});

				// Handle save button click
				$(document).on('click', '.element-pack-settings-save-btn', function(e) {
					e.preventDefault();
					
					// Find the active form in the current tab
					const currentHash = window.location.hash.substring(1);
					let targetForm = null;
					
					// Look for forms in the active tab content
					if (currentHash) {
						// Try to find form in the specific tab page
						targetForm = $('#' + currentHash + '_page form.settings-save');
						
						// If not found, try without _page suffix
						if (!targetForm || targetForm.length === 0) {
							targetForm = $('#' + currentHash + ' form.settings-save');
						}
						
						// Try to find any form in the active tab content
						if (!targetForm || targetForm.length === 0) {
							targetForm = $('#' + currentHash + '_page form');
						}
					}
					
					// Fallback to any visible form with settings-save class
					if (!targetForm || targetForm.length === 0) {
						targetForm = $('form.settings-save:visible').first();
					}
					
					// Last fallback - any visible form
					if (!targetForm || targetForm.length === 0) {
						targetForm = $('.bdt-switcher .group:visible form').first();
					}
					
					if (targetForm && targetForm.length > 0) {
						// Show loading notification
						// bdtUIkit.notification({
						// 	message: '<div bdt-spinner></div> <?php //esc_html_e('Please wait, Saving settings...', 'bdthemes-element-pack-lite') ?>',
						// 	timeout: false
						// });

						// Submit form using AJAX (same logic as existing form submission)
						targetForm.ajaxSubmit({
							success: function () {
								bdtUIkit.notification.closeAll();
								bdtUIkit.notification({
									message: '<span class="dashicons dashicons-yes"></span> <?php esc_html_e('Settings Saved Successfully.', 'bdthemes-element-pack-lite') ?>',
									status: 'primary',
									pos: 'top-center'
								});
							},
							error: function (data) {
								bdtUIkit.notification.closeAll();
								bdtUIkit.notification({
									message: '<span bdt-icon=\'icon: warning\'></span> <?php esc_html_e('Unknown error, make sure access is correct!', 'bdthemes-element-pack-lite') ?>',
									status: 'warning'
								});
							}
						});
					} else {
						// Show error if no form found
						bdtUIkit.notification({
							message: '<span bdt-icon="icon: warning"></span> <?php esc_html_e('No settings form found to save.', 'bdthemes-element-pack-lite') ?>',
							status: 'warning'
						});
					}
				});

			});

			// Chart.js initialization for system status canvas charts
			function initElementPackCharts() {
				// Wait for Chart.js to be available
				if (typeof Chart === 'undefined') {
					setTimeout(initElementPackCharts, 500);
					return;
				}

				// Chart instances storage
				window.epChartInstances = window.epChartInstances || {};
				window.epChartsInitialized = false;

				// Function to create a chart
				function createChart(canvasId) {
					var canvas = document.getElementById(canvasId);
					if (!canvas) {
						return;
					}

					var $canvas = jQuery('#' + canvasId);
					var valueStr = $canvas.data('value');
					var labelsStr = $canvas.data('labels');
					var bgStr = $canvas.data('bg');

					if (!valueStr || !labelsStr || !bgStr) {
						return;
					}

					// Parse data
					var values = valueStr.toString().split(',').map(v => parseInt(v.trim()) || 0);
					var labels = labelsStr.toString().split(',').map(l => l.trim());
					var colors = bgStr.toString().split(',').map(c => c.trim());

					// Destroy existing chart using Chart.js built-in method
					var existingChart = Chart.getChart(canvas);
					if (existingChart) {
						existingChart.destroy();
					}

					// Also destroy from our instance storage
					if (window.epChartInstances && window.epChartInstances[canvasId]) {
						window.epChartInstances[canvasId].destroy();
						delete window.epChartInstances[canvasId];
					}

					// Create new chart
					try {
						var newChart = new Chart(canvas, {
							type: 'doughnut',
							data: {
								labels: labels,
								datasets: [{
									data: values,
									backgroundColor: colors,
									borderWidth: 0
								}]
							},
							options: {
								responsive: true,
								maintainAspectRatio: false,
								plugins: {
									legend: { display: false },
									tooltip: { enabled: true }
								},
								cutout: '60%'
							}
						});
						
						// Store in our instance storage
						if (!window.epChartInstances) window.epChartInstances = {};
						window.epChartInstances[canvasId] = newChart;
					} catch (error) {
						// Do nothing
					}
				}

				// Update total widgets status
				function updateTotalStatus() {
					var coreCount = jQuery('#element_pack_active_modules_page input:checked').length;
					var thirdPartyCount = jQuery('#element_pack_third_party_widget_page input:checked').length;
					var extensionsCount = jQuery('#element_pack_elementor_extend_page input:checked').length;

					jQuery('#bdt-total-widgets-status-core').text(coreCount);
					jQuery('#bdt-total-widgets-status-3rd').text(thirdPartyCount);
					jQuery('#bdt-total-widgets-status-extensions').text(extensionsCount);
					jQuery('#bdt-total-widgets-status-heading').text(coreCount + thirdPartyCount + extensionsCount);
					
					jQuery('#bdt-total-widgets-status').attr('data-value', [coreCount, thirdPartyCount, extensionsCount].join(','));
				}

				// Initialize all charts once
				function initAllCharts() {
					// Check if charts already exist and are properly rendered
					if (window.epChartInstances && Object.keys(window.epChartInstances).length >= 4) {
						return;
					}
					
					// Update total status first
					updateTotalStatus();
					
					// Create all charts
					var chartCanvases = [
						'bdt-db-total-status',
						'bdt-db-only-widget-status', 
						'bdt-db-only-3rdparty-status',
						'bdt-total-widgets-status'
					];

					var successfulCharts = 0;
					chartCanvases.forEach(function(canvasId) {
						var canvas = document.getElementById(canvasId);
						if (canvas && canvas.offsetParent !== null) { // Check if canvas is visible
							createChart(canvasId);
							if (window.epChartInstances && window.epChartInstances[canvasId]) {
								successfulCharts++;
							}
						}
					});
				}

				// Check if we're currently on system status tab and initialize
				function checkAndInitIfOnSystemStatus() {
					if (window.location.hash === '#element_pack_analytics_system_req') {
						setTimeout(initAllCharts, 300);
					}
				}

				// Initialize charts when DOM is ready
				jQuery(document).ready(function() {
					// Only initialize if we're on the system status tab
					setTimeout(checkAndInitIfOnSystemStatus, 500);
				});

				// Add click handler for System Status tab to create/refresh charts
				jQuery(document).on('click', 'a[href="#element_pack_analytics_system_req"], a[href*="element_pack_analytics_system_req"]', function() {
					setTimeout(function() {
						// Always recreate charts when tab is clicked to ensure they're visible
						initAllCharts();
					}, 200);
				});
			}

			// Start the chart initialization
			setTimeout(initElementPackCharts, 1000);

			// Handle plugin installation via AJAX
			jQuery(document).on('click', '.ep-install-plugin', function(e) {
				e.preventDefault();
				
				var $button = jQuery(this);
				var pluginSlug = $button.data('plugin-slug');
				var nonce = $button.data('nonce');
				var originalText = $button.text();
				
				// Disable button and show loading state
				$button.prop('disabled', true)
					   .text('<?php echo esc_js(__('Installing...', 'bdthemes-element-pack-lite')); ?>')
					   .addClass('bdt-installing');
				
				// Perform AJAX request
				jQuery.ajax({
					url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
					type: 'POST',
					data: {
						action: 'ep_install_plugin',
						plugin_slug: pluginSlug,
						nonce: nonce
					},
					success: function(response) {
						if (response.success) {
							// Show success message
							$button.text('<?php echo esc_js(__('Installed!', 'bdthemes-element-pack-lite')); ?>')
								   .removeClass('bdt-installing')
								   .addClass('bdt-installed');
							
							// Show success notification
							if (typeof bdtUIkit !== 'undefined' && bdtUIkit.notification) {
								bdtUIkit.notification({
									message: '<span class="dashicons dashicons-yes"></span> ' + response.data.message,
									status: 'success'
								});
							}
							
							// Reload the page after 2 seconds to update button states
							setTimeout(function() {
								window.location.reload();
							}, 2000);
							
						} else {
							// Show error message
							$button.prop('disabled', false)
								   .text(originalText)
								   .removeClass('bdt-installing');
							
							// Show error notification
							if (typeof bdtUIkit !== 'undefined' && bdtUIkit.notification) {
								bdtUIkit.notification({
									message: '<span class="dashicons dashicons-warning"></span> ' + response.data.message,
									status: 'danger'
								});
							}
						}
					},
					error: function() {
						// Handle network/server errors
						$button.prop('disabled', false)
							   .text(originalText)
							   .removeClass('bdt-installing');
						
						// Show error notification
						if (typeof bdtUIkit !== 'undefined' && bdtUIkit.notification) {
							bdtUIkit.notification({
								message: '<span class="dashicons dashicons-warning"></span> <?php echo esc_js(__('Installation failed. Please try again.', 'bdthemes-element-pack-lite')); ?>',
								status: 'danger'
							});
						}
					}
				});
			});

			jQuery(document).ready(function ($) {
                const getProLink = $('a[href="admin.php?page=element_pack_options_upgrade"]');
                if (getProLink.length) {
                    getProLink.attr('target', '_blank');
                }
            });
		</script>
		<?php
	}

	/**
	 * Display Footer
	 *
	 * @access public
	 * @return void
	 */

	public function footer_info() {
		?>
		<div class="element-pack-footer-info bdt-margin-medium-top">
			<div class="bdt-grid ">
				<div class="bdt-width-auto@s ep-setting-save-btn">
				</div>
				<div class="bdt-width-expand@s bdt-text-right">
					<p class="">
						<?php
						/* translators: %1$s: URL link to BdThemes website */
						echo wp_kses(
							sprintf(
								/* translators: %1$s: URL link to BdThemes website */
								__('Element Pack plugin made with love by <a target="_blank" href="%1$s">BdThemes</a> Team.<br>All rights reserved by <a target="_blank" href="%1$s">BdThemes.com</a>.', 'bdthemes-element-pack-lite'),
								esc_url('https://bdthemes.com')
							),
							array(
								'a'  => array( 'href' => array(), 'target' => array() ),
								'br' => array(),
							)
						);
						?>
					</p>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Get all the pages
	 *
	 * @return array page names with key value pairs
	 */
	function get_pages() {
		$pages         = get_pages();
		$pages_options = [];
		if ( $pages ) {
			foreach ( $pages as $page ) {
				$pages_options[ $page->ID ] = $page->post_title;
			}
		}

		return $pages_options;
	}

	/**
	 * Display Analytics and System Requirements
	 *
	 * @access public
	 * @return void
	 */

	public function element_pack_analytics_system_req_content() {
		?>
		<div class="ep-dashboard-panel"
			bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">
			<div class="ep-dashboard-analytics-system">

				<?php $this->element_pack_widgets_status(); ?>

				<div class="bdt-grid bdt-grid-medium bdt-margin-medium-top" bdt-grid
					bdt-height-match="target: > div > .bdt-card">
					<div class="bdt-width-1-1">
						<div class="bdt-card bdt-card-body ep-system-requirement">
							<h1 class="ep-feature-title bdt-margin-small-bottom">
								<?php esc_html_e('System Requirement', 'bdthemes-element-pack-lite'); ?>
							</h1>
							<?php $this->element_pack_system_requirement(); ?>
						</div>
					</div>
				</div>

			</div>
		</div>
		<?php
	}

	/**
	 * Handle AJAX plugin installation
	 * 
	 * @access public
	 * @return void
	 */
	public function install_plugin_ajax() {
		// Check nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ep_install_plugin_nonce' ) ) {
			wp_send_json_error(['message' => __('Security check failed', 'bdthemes-element-pack-lite')]);
		}

		// Check user capability
		if (!current_user_can('install_plugins')) {
			wp_send_json_error(['message' => __('You do not have permission to install plugins', 'bdthemes-element-pack-lite')]);
		}

		$plugin_slug = isset($_POST['plugin_slug']) ? sanitize_key(wp_unslash($_POST['plugin_slug'])) : '';

		if (empty($plugin_slug)) {
			wp_send_json_error(['message' => __('Plugin slug is required', 'bdthemes-element-pack-lite')]);
		}

		// Include necessary WordPress files
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';

		// Get plugin information
		$api = plugins_api('plugin_information', [
			'slug' => $plugin_slug,
			'fields' => [
				'sections' => false,
			],
		]);

		if (is_wp_error($api)) {
			wp_send_json_error(['message' => __('Plugin not found: ', 'bdthemes-element-pack-lite') . $api->get_error_message()]);
		}

		// Install the plugin
		$skin = new \WP_Ajax_Upgrader_Skin();
		$upgrader = new \Plugin_Upgrader($skin);
		$result = $upgrader->install($api->download_link);

		if (is_wp_error($result)) {
			wp_send_json_error(['message' => __('Installation failed: ', 'bdthemes-element-pack-lite') . $result->get_error_message()]);
		} elseif ($skin->get_errors()->has_errors()) {
			wp_send_json_error(['message' => __('Installation failed: ', 'bdthemes-element-pack-lite') . $skin->get_error_messages()]);
		} elseif (is_null($result)) {
			wp_send_json_error(['message' => __('Installation failed: Unable to connect to filesystem', 'bdthemes-element-pack-lite')]);
		}

		// Get installation status
		$install_status = install_plugin_install_status($api);
		
		wp_send_json_success([
			'message' => __('Plugin installed successfully!', 'bdthemes-element-pack-lite'),
			'plugin_file' => $install_status['file'],
			'plugin_name' => $api->name
		]);
	}

	/**
	 * Extract plugin slug from plugin path
	 * 
	 * @param string $plugin_path Plugin file path
	 * @return string Plugin slug
	 */
	private function extract_plugin_slug_from_path($plugin_path) {
		$parts = explode('/', $plugin_path);
		return isset($parts[0]) ? $parts[0] : '';
	}





}

new ElementPack_Admin_Settings();
