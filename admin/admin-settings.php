<?php

use ElementPack\Notices;
use ElementPack\Utils;
use ElementPack\Admin\ModuleService;
use ElementPack\Base\Element_Pack_Base;
use Elementor\Modules\Usage\Module;
use Elementor\Tracker;

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

		/**
		 * black_friday_notice
		 * Will be not show after 2024-12-06 00:00:00
		 */
		$current_date = date( 'Y-m-d H:i:s' );
		$end_date     = '2024-12-06 00:00:00';

		if ( strtotime( $current_date ) < strtotime( $end_date ) ) {
			add_action( 'admin_notices', [ $this, 'black_friday_notice' ], 10, 3 );
		}

		// Add custom CSS/JS functionality
		$this->init_custom_code_functionality();

		// Add AJAX handler for plugin installation
		add_action('wp_ajax_ep_install_plugin', [$this, 'install_plugin_ajax']);
	}

	/**
	 * Initialize Custom Code Functionality
	 * 
	 * @access public
	 * @return void
	 */
	public function init_custom_code_functionality() {
		
		// Admin scripts (admin only)
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_custom_code_scripts' ] );
		
	}

	/**
	 * Enqueue scripts for custom code editor
	 * 
	 * @access public
	 * @return void
	 */
	public function enqueue_custom_code_scripts( $hook ) {
		if ( $hook !== 'toplevel_page_element_pack_options' ) {
			return;
		}

		// Enqueue WordPress built-in CodeMirror 
		wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
		wp_enqueue_code_editor( array( 'type' => 'application/javascript' ) );
		
		// Enqueue WordPress media library scripts
		wp_enqueue_media();
		
		// Enqueue the admin script if it exists
		$admin_script_path = BDTEP_ASSETS_PATH . 'js/ep-admin.js';
		if ( file_exists( $admin_script_path ) ) {
			wp_enqueue_script( 
				'ep-admin-script', 
				BDTEP_ASSETS_URL . 'js/ep-admin.js', 
				[ 'jquery', 'media-upload', 'media-views', 'code-editor' ], 
				BDTEP_VER, 
				true 
			);
			
			// Localize script with AJAX data
			wp_localize_script( 'ep-admin-script', 'ep_admin_ajax', [
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'ep_custom_code_nonce' ),
				'white_label_nonce' => wp_create_nonce( 'ep_white_label_nonce' )
			] );
		} else {
			// Fallback: localize to jquery if the admin script doesn't exist
			wp_localize_script( 'jquery', 'ep_admin_ajax', [
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'ep_custom_code_nonce' ),
				'white_label_nonce' => wp_create_nonce( 'ep_white_label_nonce' )
			] );
		}
	}

	/**
	 *Black Friday Notice
	 *
	 * @access public
	 */
	public function black_friday_notice() {
		Notices::add_notice(
			[ 
				'id'               => 'black-friday',
				'type'             => 'success',
				'dismissible'      => true,
				'dismissible-time' => HOUR_IN_SECONDS * 72,
				'html_message'     => $this->black_friday_offer_notice_message(),
			]
		);
	}

	public function black_friday_offer_notice_message() {
		$plugin_icon  = BDTEP_ASSETS_URL . 'images/logo.svg';
		$plugin_title = __( 'Best Savings On Black Friday Deals - ⚡Up To 85% Off🔥', 'bdthemes-element-pack' );
		ob_start();
		?>
				<div class="bdt-license-notice-global element_pack_pro">
					<div class="bdt-license-notice-content">
						<h3>
							<?php echo wp_kses_post( $plugin_title ); ?>
						</h3>
						<div class="bdt-license-notice-button-wrap">
							<a href="https://bdthemes.com/black-friday/" target="_blank" class="bdt-button bdt-button-allow">
								<?php esc_html_e( 'Get Deals Now', 'bdthemes-element-pack' ); ?>
							</a>
						</div>
					</div>
					<a href="https://bdthemes.com/black-friday/" target="_blank" class="bdt-link-btn"></a>
				</div>
				<?php
				return ob_get_clean();
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
		if (isset($_GET['page']) && $_GET['page'] === self::PAGE_ID . '_upgrade') {
			wp_redirect('https://store.bdthemes.com/element-pack?utm_source=ElementPackLite&utm_medium=PluginPage&utm_campaign=ElementPackLite&coupon=FREETOPRO');
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
			BDTEP_TITLE . ' ' . esc_html__( 'Dashboard', 'bdthemes-element-pack' ),
			BDTEP_TITLE,
			'manage_options',
			self::PAGE_ID,
			[ $this, 'plugin_page' ],
			$this->element_pack_icon(),
			58
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTEP_TITLE,
			esc_html__( 'Core Widgets', 'bdthemes-element-pack' ),
			'manage_options',
			self::PAGE_ID . '#element_pack_active_modules',
			[ $this, 'display_page' ]
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTEP_TITLE,
			esc_html__( '3rd Party Widgets', 'bdthemes-element-pack' ),
			'manage_options',
			self::PAGE_ID . '#element_pack_third_party_widget',
			[ $this, 'display_page' ]
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTEP_TITLE,
			esc_html__( 'Extensions', 'bdthemes-element-pack' ),
			'manage_options',
			self::PAGE_ID . '#element_pack_elementor_extend',
			[ $this, 'display_page' ]
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTEP_TITLE,
			esc_html__( 'Special Features', 'bdthemes-element-pack' ),
			'manage_options',
			self::PAGE_ID . '#element_pack_other_settings',
			[ $this, 'display_page' ]
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTEP_TITLE,
			esc_html__( 'API Settings', 'bdthemes-element-pack' ),
			'manage_options',
			self::PAGE_ID . '#element_pack_api_settings',
			[ $this, 'display_page' ]
		);

		add_submenu_page(
			self::PAGE_ID,
			BDTEP_TITLE,
			esc_html__('Extra Options', 'bdthemes-element-pack'),
			'manage_options',
			self::PAGE_ID . '#element_pack_extra_options',
			[$this, 'display_page']
		);
		
		add_submenu_page(
			self::PAGE_ID,
			BDTEP_TITLE,
			esc_html__('System Status', 'bdthemes-element-pack'),
			'manage_options',
			self::PAGE_ID . '#element_pack_analytics_system_req',
			[$this, 'display_page']
		);
		
		add_submenu_page(
			self::PAGE_ID,
			BDTEP_TITLE,
			esc_html__('Other Plugins', 'bdthemes-element-pack'),
			'manage_options',
			self::PAGE_ID . '#element_pack_other_plugins',
			[$this, 'display_page']
		);
		
		add_submenu_page(
			self::PAGE_ID,
			BDTEP_TITLE,
			esc_html__('Get 50% Payout', 'bdthemes-element-pack'),
			'manage_options',
			self::PAGE_ID . '#element_pack_affiliate',
			[$this, 'display_page']
		);

		if ( ! defined( 'BDTEP_LO' ) ) {
			add_submenu_page(
				self::PAGE_ID,                    
				BDTEP_TITLE,                     
				esc_html__( 'Upgrade For 30% Off!', 'bdthemes-element-pack' ),  
				'manage_options',                 
				self::PAGE_ID . '_upgrade',
				[ $this, 'display_page' ]
			);
		}
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
				'title' => esc_html__('Core Widgets', 'bdthemes-element-pack'),
				'icon' => 'dashicons dashicons-screenoptions',
			],
			[
				'id' => 'element_pack_third_party_widget',
				'title' => esc_html__('3rd Party Widgets', 'bdthemes-element-pack'),
				'icon' => 'dashicons dashicons-screenoptions',
			],
			[
				'id' => 'element_pack_elementor_extend',
				'title' => esc_html__('Extensions', 'bdthemes-element-pack'),
				'icon' => 'dashicons dashicons-screenoptions',
			],
			[
				'id' => 'element_pack_other_settings',
				'title' => esc_html__('Special Features', 'bdthemes-element-pack'),
				'icon' => 'dashicons dashicons-screenoptions',
			],
			[
				'id' => 'element_pack_api_settings',
				'title' => esc_html__('API Settings', 'bdthemes-element-pack'),
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
						<?php esc_html_e('Welcome to Element Pack!', 'bdthemes-element-pack'); ?>
					</h1>
					<p class="ep-dashboard-welcome-desc">
						<?php esc_html_e('Empower your web creation with powerful widgets, advanced extensions, and 2700+ ready templates and more.', 'bdthemes-element-pack'); ?>
					</p>
					<a href="<?php echo admin_url('?ep_setup_wizard=show'); ?>"
						class="bdt-button bdt-welcome-button bdt-margin-small-top"
						target="_blank"><?php esc_html_e('Setup Element Pack', 'bdthemes-element-pack'); ?></a>

					<div class="ep-dashboard-compare-section">
						<h4 class="ep-feature-sub-title">
							<?php printf(esc_html__('Unlock %sPremium Features%s', 'bdthemes-element-pack'), '<strong class="ep-highlight-text">', '</strong>'); ?>
						</h4>
						<h1 class="ep-feature-title ep-dashboard-compare-title">
							<?php esc_html_e('Create Your Sleek Website with Element Pack Pro!', 'bdthemes-element-pack'); ?>
						</h1>
						<p><?php esc_html_e('Don\'t need more plugins. This pro addon helps you build complex or professional websites—visually stunning, functional and customizable.', 'bdthemes-element-pack'); ?>
						</p>
						<ul>
							<li><?php esc_html_e('Dynamic Content and Integrations', 'bdthemes-element-pack'); ?></li>
							<li><?php esc_html_e('Enhanced Template Library', 'bdthemes-element-pack'); ?></li>
							<li><?php esc_html_e('Theme Builder', 'bdthemes-element-pack'); ?></li>
							<li><?php esc_html_e('Mega Menu Builder', 'bdthemes-element-pack'); ?></li>
							<li><?php esc_html_e('Powerful Widgets and Advanced Extensions', 'bdthemes-element-pack'); ?>
							</li>
						</ul>
						<div class="ep-dashboard-compare-section-buttons">
							<a href="https://www.elementpack.pro/pricing/#a2a0062"
								class="bdt-button bdt-welcome-button bdt-margin-small-right"
								target="_blank"><?php esc_html_e('Compare Free Vs Pro', 'bdthemes-element-pack'); ?></a>
							<a href="https://store.bdthemes.com/element-pack?utm_source=ElementPackLite&utm_medium=PluginPage&utm_campaign=ElementPackLite&coupon=FREETOPRO"
								class="bdt-button bdt-dashboard-sec-btn"
								target="_blank"><?php esc_html_e('Get Premium at 30% OFF', 'bdthemes-element-pack'); ?></a>
						</div>
					</div>
				</div>

				<div class="ep-dashboard-item ep-dashboard-template-quick-access bdt-card bdt-card-body">
					<div class="ep-dashboard-template-section">
						<img src="<?php echo BDTEP_ADMIN_URL . 'assets/images/template.jpg'; ?>"
							alt="Element Pack Dashboard Template">
						<h1 class="ep-feature-title ">
							<?php esc_html_e('Faster Web Creation with Sleek and Ready-to-Use Templates!', 'bdthemes-element-pack'); ?>
						</h1>
						<p><?php esc_html_e('Build your wordpress websites of any niche—not from scratch and in a single click.', 'bdthemes-element-pack'); ?>
						</p>
						<a href="https://www.elementpack.pro/ready-templates/"
							class="bdt-button bdt-dashboard-sec-btn bdt-margin-small-top"
							target="_blank"><?php esc_html_e('View Templates', 'bdthemes-element-pack'); ?></a>
					</div>

					<div class="ep-dashboard-quick-access bdt-margin-medium-top">
						<img src="<?php echo BDTEP_ADMIN_URL . 'assets/images/support.svg'; ?>"
							alt="Element Pack Dashboard Template">
						<h1 class="ep-feature-title">
							<?php esc_html_e('Getting Started with Quick Access', 'bdthemes-element-pack'); ?>
						</h1>
						<ul>
							<li><a href="https://www.elementpack.pro/contact/"
									target="_blank"><?php esc_html_e('Contact Us', 'bdthemes-element-pack'); ?></a></li>
							<li><a href="https://bdthemes.com/support/"
									target="_blank"><?php esc_html_e('Help Centre', 'bdthemes-element-pack'); ?></a></li>
							<li><a href="https://feedback.bdthemes.com/b/6vr2250l/feature-requests/idea/new"
									target="_blank"><?php esc_html_e('Request a Feature', 'bdthemes-element-pack'); ?></a>
							</li>
						</ul>
						<div class="ep-dashboard-support-section">
							<h1 class="ep-feature-title">
								<i class="dashicons dashicons-phone"></i>
								<?php esc_html_e('24/7 Support', 'bdthemes-element-pack'); ?>
							</h1>
							<p><?php esc_html_e('Helping you get real-time solutions related to web creation with WordPress, Elementor, and Element Pack.', 'bdthemes-element-pack'); ?>
							</p>
							<a href="https://bdthemes.com/support/" class="bdt-margin-small-top"
								target="_blank"><?php esc_html_e('Get Your Support', 'bdthemes-element-pack'); ?></a>
						</div>
					</div>
				</div>

				<div class="ep-dashboard-item ep-dashboard-request-feature bdt-card bdt-card-body">
					<h1 class="ep-feature-title ep-dashboard-template-quick-title">
						<?php esc_html_e('What\'s Stacking You?', 'bdthemes-element-pack'); ?>
					</h1>
					<p><?php esc_html_e('We are always here to help you. If you have any feature request, please let us know.', 'bdthemes-element-pack'); ?>
					</p>
					<a href="https://feedback.elementpack.pro/b/3v2gg80n/feature-requests/idea/new"
						class="bdt-button bdt-dashboard-sec-btn bdt-margin-small-top"
						target="_blank"><?php esc_html_e('Request Your Features', 'bdthemes-element-pack'); ?></a>
				</div>

				<a href="https://www.youtube.com/watch?v=-e-kr4Vkh4E&list=PLP0S85GEw7DOJf_cbgUIL20qqwqb5x8KA" target="_blank"
					class="ep-dashboard-item ep-dashboard-footer-item ep-dashboard-video-tutorial bdt-card bdt-card-body bdt-card-small">
					<span class="ep-dashboard-footer-item-icon">
						<i class="dashicons dashicons-video-alt3"></i>
					</span>
					<h1 class="ep-feature-title"><?php esc_html_e('Watch Video Tutorials', 'bdthemes-element-pack'); ?></h1>
					<p><?php esc_html_e('An invaluable resource for mastering WordPress, Elementor, and Web Creation', 'bdthemes-element-pack'); ?>
					</p>
				</a>
				<a href="https://bdthemes.com/all-knowledge-base-of-element-pack/" target="_blank"
					class="ep-dashboard-item ep-dashboard-footer-item ep-dashboard-documentation bdt-card bdt-card-body bdt-card-small">
					<span class="ep-dashboard-footer-item-icon">
						<i class="dashicons dashicons-admin-tools"></i>
					</span>
					</span>
					<h1 class="ep-feature-title"><?php esc_html_e('Read Easy Documentation', 'bdthemes-element-pack'); ?></h1>
					<p><?php esc_html_e('A way to eliminate the challenges you might face', 'bdthemes-element-pack'); ?></p>
				</a>
				<a href="https://www.facebook.com/bdthemes" target="_blank"
					class="ep-dashboard-item ep-dashboard-footer-item ep-dashboard-community bdt-card bdt-card-body bdt-card-small">
					<span class="ep-dashboard-footer-item-icon">
						<i class="dashicons dashicons-admin-users"></i>
					</span>
					<h1 class="ep-feature-title"><?php esc_html_e('Join Our Community', 'bdthemes-element-pack'); ?></h1>
					<p><?php esc_html_e('A platform for the opportunity to network, collaboration and innovation', 'bdthemes-element-pack'); ?>
					</p>
				</a>
				<a href="https://wordpress.org/plugins/bdthemes-element-pack-lite/#reviews" target="_blank"
					class="ep-dashboard-item ep-dashboard-footer-item ep-dashboard-review bdt-card bdt-card-body bdt-card-small">
					<span class="ep-dashboard-footer-item-icon">
						<i class="dashicons dashicons-star-filled"></i>
					</span>
					<h1 class="ep-feature-title"><?php esc_html_e('Show Your Love', 'bdthemes-element-pack'); ?></h1>
					<p><?php esc_html_e('A way of the assessment of code', 'bdthemes-element-pack'); ?></p>
				</a>
			</div>

		</div>

		<?php
	}

	/**
	 * Others Plugin
	 */

	public function element_pack_others_plugin() {
		// Define plugins with their paths and install URLs
		$plugins = [
			'prime_slider' => [
				'path' => 'bdthemes-prime-slider-lite/bdthemes-prime-slider.php',
				'install_url' => 'https://wordpress.org/plugins/bdthemes-prime-slider-lite/',
				'website_url' => 'https://primeslider.pro/'
			],
			'ultimate_post_kit' => [
				'path' => 'ultimate-post-kit/ultimate-post-kit.php', 
				'install_url' => 'https://wordpress.org/plugins/ultimate-post-kit/',
				'website_url' => 'https://postkit.pro/'
			],
			'ultimate_store_kit' => [
				'path' => 'ultimate-store-kit/ultimate-store-kit.php',
				'install_url' => 'https://wordpress.org/plugins/ultimate-store-kit/',
				'website_url' => 'https://storekit.pro/'
			],
			'pixel_gallery' => [
				'path' => 'pixel-gallery/pixel-gallery.php',
				'install_url' => 'https://wordpress.org/plugins/pixel-gallery/',
				'website_url' => 'https://pixelgallery.pro/'
			],
			'live_copy_paste' => [
				'path' => 'live-copy-paste/live-copy-paste.php',
				'install_url' => 'https://wordpress.org/plugins/live-copy-paste/',
				'website_url' => 'https://www.youtube.com/watch?v=KWxbZfPIcqU'
			],
			'zoloblocks' => [
				'path' => 'zoloblocks/zoloblocks.php',
				'install_url' => 'https://wordpress.org/plugins/zoloblocks/',
				'website_url' => 'https://zoloblocks.com/'
			],
			'spin_wheel' => [
				'path' => 'spin-wheel/spin-wheel.php',
				'install_url' => 'https://wordpress.org/plugins/spin-wheel/',
				'website_url' => 'https://spinwheel.bdthemes.com/'
			],
			'ai_image' => [
				'path' => 'ai-image/ai-image.php',
				'install_url' => 'https://wordpress.org/plugins/ai-image/',
				'website_url' => 'https://www.youtube.com/watch?v=cGmPFU_ju4s'
			],
			'dark_reader' => [
				'path' => 'dark-reader/dark-reader.php',
				'install_url' => 'https://wordpress.org/plugins/dark-reader/',
				'website_url' => 'https://wordpress.org/plugins/dark-reader/'
			],
			'ar_viewer' => [
				'path' => 'ar-viewer/ar-viewer.php',
				'install_url' => 'https://wordpress.org/plugins/ar-viewer/',
				'website_url' => 'https://wordpress.org/plugins/ar-viewer/'
			]
		];
		?>
		<div class="ep-dashboard-panel"
			bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">
			<div class="ep-dashboard-others-plugin">
				<!-- Prime Slider -->
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle ">
						<img src="<?php echo BDTEP_ADMIN_URL . 'assets/images/prime-slider.svg'; ?>" alt="Prime Slider">
						<div class="bdt-others-plugin-content-text">
							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="ep-feature-title "><?php _e('Prime Slider', 'bdthemes-element-pack'); ?></h1>
								<span class="bdt-others-plugin-user"><?php esc_html_e('100k+ active users', 'bdthemes-element-pack'); ?></span>
							</div>
							
							<p><?php _e('The revolutionary slider builder addon for Elementor with next-gen superb interface. It\'s Free! Download it.', 'bdthemes-element-pack'); ?></p>

							<div class="bdt-others-plugin-rating bdt-margin-small-top bdt-flex bdt-flex-middle">
								<span class="bdt-others-plugin-rating-stars">
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-half"></i>
								</span>
								<span class="bdt-others-plugin-rating-text bdt-margin-small-left">
									<?php _e('4.5 out of 5 stars.', 'bdthemes-element-pack'); ?>
								</span>
							</div>
						</div>
						
					</div>
				
					<div class="bdt-others-plugins-link">
				    	<?php echo $this->get_plugin_action_button($plugins['prime_slider']['path'], $plugins['prime_slider']['install_url']); ?>
						<a class="bdt-button bdt-dashboard-sec-btn" target="_blank"
							href="<?php echo esc_url($plugins['prime_slider']['website_url']); ?>">
							<?php _e('View Website', 'bdthemes-element-pack'); ?>
						</a>
					</div>

					
				</div>
				<!-- Ultimate Post Kit -->
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle ">
						<img src="<?php echo BDTEP_ADMIN_URL . 'assets/images/ultimate-post-kit.svg'; ?>" alt="zoloblocks">
						<div class="bdt-others-plugin-content-text">
							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="ep-feature-title "><?php _e('Ultimate Post Kit', 'bdthemes-element-pack'); ?></h1>
								<span class="bdt-others-plugin-user"><?php esc_html_e('30k+ active users', 'bdthemes-element-pack'); ?></span>
							</div>
							
							<p><?php _e('Best blogging addon for building quality blogging website with fine-tuned features and widgets. It\'s Free! Download it.', 'bdthemes-element-pack'); ?></p>

							<div class="bdt-others-plugin-rating bdt-margin-small-top bdt-flex bdt-flex-middle">
								<span class="bdt-others-plugin-rating-stars">
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
								</span>
								<span class="bdt-others-plugin-rating-text bdt-margin-small-left">
									<?php _e('4.8 out of 5 stars.', 'bdthemes-element-pack'); ?>
								</span>
							</div>

						</div>
					</div>
				
					<div class="bdt-others-plugins-link">
				     	<?php echo $this->get_plugin_action_button($plugins['ultimate_post_kit']['path'], $plugins['ultimate_post_kit']['install_url']); ?>
						<a class="bdt-button bdt-dashboard-sec-btn" target="_blank"
							href="<?php echo esc_url($plugins['ultimate_post_kit']['website_url']); ?>">
							<?php _e('View Website', 'bdthemes-element-pack'); ?>
						</a>
					</div>
				</div>
				<!-- Ultimate Store Kit -->
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle ">
						<img src="<?php echo BDTEP_ADMIN_URL . 'assets/images/ultimate-store-kit.svg'; ?>" alt="zoloblocks">
						<div class="bdt-others-plugin-content-text">
							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="ep-feature-title "><?php _e('Ultimate Store Kit', 'bdthemes-element-pack'); ?></h1>
								<span class="bdt-others-plugin-user"><?php esc_html_e('1000+ active users', 'bdthemes-element-pack'); ?></span>
							</div>
							<p><?php _e('The only eCommmerce addon for answering all your online store design problems in one package. It\'s Free! Download it.', 'bdthemes-element-pack'); ?></p>

							<div class="bdt-others-plugin-rating bdt-margin-small-top bdt-flex bdt-flex-middle">
								<span class="bdt-others-plugin-rating-stars">
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-half"></i>
								</span>
								<span class="bdt-others-plugin-rating-text bdt-margin-small-left">
									<?php _e('4.4 out of 5 stars.', 'bdthemes-element-pack'); ?>
								</span>
							</div>

						</div>
					</div>
				
					<div class="bdt-others-plugins-link">
					    <?php echo $this->get_plugin_action_button($plugins['ultimate_store_kit']['path'], $plugins['ultimate_store_kit']['install_url']); ?>
						<a class="bdt-button bdt-dashboard-sec-btn" target="_blank"
							href="<?php echo esc_url($plugins['ultimate_store_kit']['website_url']); ?>">
							<?php _e('View Website', 'bdthemes-element-pack'); ?>
						</a>
					</div>
				</div>
				<!-- Pixel Gallery -->
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle ">
						<img src="<?php echo BDTEP_ADMIN_URL . 'assets/images/pixel-gallery.svg'; ?>" alt="Pixel Gallery">
						<div class="bdt-others-plugin-content-text">
							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="ep-feature-title "><?php _e('Pixel Gallery', 'bdthemes-element-pack'); ?></h1>
								<span class="bdt-others-plugin-user"><?php esc_html_e('3000+ active users', 'bdthemes-element-pack'); ?></span>
							</div>
							<p><?php _e('Pixel Gallery provides more than 30+ essential elements for everyday applications to simplify the whole web building process. It\'s Free! Download it.', 'bdthemes-element-pack'); ?></p>

							<div class="bdt-others-plugin-rating bdt-margin-small-top bdt-flex bdt-flex-middle">
								<span class="bdt-others-plugin-rating-stars">
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
								</span>
								<span class="bdt-others-plugin-rating-text bdt-margin-small-left">
									<?php _e('5 out of 5 stars.', 'bdthemes-element-pack'); ?>
								</span>
							</div>

						</div>
					</div>
				
					<div class="bdt-others-plugins-link">
						<?php echo $this->get_plugin_action_button($plugins['pixel_gallery']['path'], $plugins['pixel_gallery']['install_url']); ?>
						<a class="bdt-button bdt-dashboard-sec-btn" target="_blank"
							href="<?php echo esc_url($plugins['pixel_gallery']['website_url']); ?>">
							<?php _e('View Website', 'bdthemes-element-pack'); ?>
						</a>
					</div>
				</div>
				<!-- Live Copy Paste -->
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle ">
						<img src="<?php echo BDTEP_ADMIN_URL . 'assets/images/live-copy-paste.svg'; ?>" alt="live copy paste">
						<div class="bdt-others-plugin-content-text">
							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="ep-feature-title "><?php _e('Live Copy Paste', 'bdthemes-element-pack'); ?></h1>
								<span class="bdt-others-plugin-user"><?php esc_html_e('3000+ active users', 'bdthemes-element-pack'); ?></span>
							</div>
							<p><?php _e('Superfast cross-domain copy-paste mechanism for WordPress websites with true UI copy experience. It\'s Free! Download it.', 'bdthemes-element-pack'); ?></p>

							<div class="bdt-others-plugin-rating bdt-margin-small-top bdt-flex bdt-flex-middle">
								<span class="bdt-others-plugin-rating-stars">
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-half"></i>
								</span>
								<span class="bdt-others-plugin-rating-text bdt-margin-small-left">
									<?php _e('4.3 out of 5 stars.', 'bdthemes-element-pack'); ?>
								</span>
							</div>

						</div>
					</div>
				
					<div class="bdt-others-plugins-link">
				        <?php echo $this->get_plugin_action_button($plugins['live_copy_paste']['path'], $plugins['live_copy_paste']['install_url']); ?>

						<a class="bdt-button bdt-dashboard-sec-btn" target="_blank"
							href="<?php echo esc_url($plugins['live_copy_paste']['website_url']); ?>">
							<?php _e('Video Tutorial', 'bdthemes-element-pack'); ?>
						</a>
					</div>
				</div>
				<!-- ZoloBlocks -->
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle ">
						<img src="<?php echo BDTEP_ADMIN_URL . 'assets/images/zoloblocks.svg'; ?>" alt="zoloblocks">
						<div class="bdt-others-plugin-content-text">
							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="ep-feature-title "><?php _e('ZoloBlocks', 'bdthemes-element-pack'); ?></h1>
								<span class="bdt-others-plugin-user"><?php esc_html_e('300+ active users', 'bdthemes-element-pack'); ?></span>
							</div>
							<p><?php _e('ZoloBlocks is a collection of blocks for the new WordPress block editor (Gutenberg). It\'s Free! Download it.', 'bdthemes-element-pack'); ?></p>

							<div class="bdt-others-plugin-rating bdt-margin-small-top bdt-flex bdt-flex-middle">
								<span class="bdt-others-plugin-rating-stars">
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
								</span>
								<span class="bdt-others-plugin-rating-text bdt-margin-small-left">
									<?php _e('5 out of 5 stars.', 'bdthemes-element-pack'); ?>
								</span>
							</div>

						</div>
					</div>
				
					<div class="bdt-others-plugins-link">
						<?php echo $this->get_plugin_action_button($plugins['zoloblocks']['path'], $plugins['zoloblocks']['install_url']); ?>
						<a class="bdt-button bdt-dashboard-sec-btn" target="_blank"
							href="<?php echo esc_url($plugins['zoloblocks']['website_url']); ?>">
							<?php _e('View Website', 'bdthemes-element-pack'); ?>
						</a>
					</div>
				</div>
				<!-- Spin Wheel -->
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle ">
						<img src="<?php echo BDTEP_ADMIN_URL . 'assets/images/spin-wheel.svg'; ?>" alt="spin wheel">
						<div class="bdt-others-plugin-content-text">
							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="ep-feature-title "><?php _e('Spin Wheel', 'bdthemes-element-pack'); ?></h1>
								<span class="bdt-others-plugin-user"><?php esc_html_e('100+ active users', 'bdthemes-element-pack'); ?></span>
							</div>
							<p><?php _e('Add a fun, interactive spin wheel to offer instant coupons, boost engagement, and grow your email list. It\'s free!.', 'bdthemes-element-pack'); ?></p>
						</div>
					</div>
				
					<div class="bdt-others-plugins-link">
				        <?php echo $this->get_plugin_action_button($plugins['spin_wheel']['path'], $plugins['spin_wheel']['install_url']); ?>

						<a class="bdt-button bdt-dashboard-sec-btn" target="_blank"
							href="<?php echo esc_url($plugins['spin_wheel']['website_url']); ?>">
							<?php _e('View Website', 'bdthemes-element-pack'); ?>
						</a>
					</div>
				</div>

				<!-- Instant Image Generator -->
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle ">
						<img src="<?php echo BDTEP_ADMIN_URL . 'assets/images/instant-image-generator.svg'; ?>" alt="instant image generator">
						<div class="bdt-others-plugin-content-text">
							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="ep-feature-title "><?php _e('Instant Image Generator', 'bdthemes-element-pack'); ?></h1>
								<span class="bdt-others-plugin-user"><?php esc_html_e('100+ active users', 'bdthemes-element-pack'); ?></span>
							</div>
							<p><?php _e('Instant Image Generator (One Click Image Uploads from Pixabay, Pexels and OpenAI). It\'s Free! Download it.', 'bdthemes-element-pack'); ?></p>
						</div>
					</div>
				
					<div class="bdt-others-plugins-link">
				        <?php echo $this->get_plugin_action_button($plugins['ai_image']['path'], $plugins['ai_image']['install_url']); ?>

						<a class="bdt-button bdt-dashboard-sec-btn" target="_blank"
							href="<?php echo esc_url($plugins['ai_image']['website_url']); ?>">
							<?php _e('Video Tutorial', 'bdthemes-element-pack'); ?>
						</a>
					</div>
				</div>

				<!-- Dark Reader -->
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle ">
						<img src="<?php echo BDTEP_ADMIN_URL . 'assets/images/dark-reader.svg'; ?>" alt="dark reader">
						<div class="bdt-others-plugin-content-text">
							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="ep-feature-title "><?php _e('Dark Reader', 'bdthemes-element-pack'); ?></h1>
								<span class="bdt-others-plugin-user"><?php esc_html_e('New', 'bdthemes-element-pack'); ?></span>
							</div>
							<p><?php _e('Add beautiful dark mode to your WordPress site with customizable settings. Reduce eye strain and improve accessibility. It\'s Free! Download it.', 'bdthemes-element-pack'); ?></p>
						</div>
					</div>
				
					<div class="bdt-others-plugins-link">
				        <?php echo $this->get_plugin_action_button($plugins['dark_reader']['path'], $plugins['dark_reader']['install_url']); ?>

						<a class="bdt-button bdt-dashboard-sec-btn" target="_blank"
							href="<?php echo esc_url($plugins['dark_reader']['website_url']); ?>">
							<?php _e('View Website', 'bdthemes-element-pack'); ?>
						</a>
					</div>
				</div>

				<!-- AR Viewer -->
				<div class="bdt-card bdt-card-body bdt-flex bdt-flex-middle bdt-flex-between">
					<div class="bdt-others-plugin-content bdt-flex bdt-flex-middle ">
						<img src="<?php echo BDTEP_ADMIN_URL . 'assets/images/ar-viewer.svg'; ?>" alt="ar viewer">
						<div class="bdt-others-plugin-content-text">
							<div class="bdt-others-plugin-user-wrap bdt-flex bdt-flex-middle">
								<h1 class="ep-feature-title "><?php _e('AR Viewer', 'bdthemes-element-pack'); ?></h1>
								<span class="bdt-others-plugin-user"><?php esc_html_e('60+ active users', 'bdthemes-element-pack'); ?></span>
							</div>
							<p><?php _e('Augmented Reality Viewer – 3D Model Viewer. It\'s Free! Download it.', 'bdthemes-element-pack'); ?></p>
						</div>
					</div>
				
					<div class="bdt-others-plugins-link">
				        <?php echo $this->get_plugin_action_button($plugins['ar_viewer']['path'], $plugins['ar_viewer']['install_url']); ?>

						<a class="bdt-button bdt-dashboard-sec-btn" target="_blank"
							href="<?php echo esc_url($plugins['ar_viewer']['website_url']); ?>">
							<?php _e('View Website', 'bdthemes-element-pack'); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Widgets Status
	 */

	public function element_pack_widgets_status() {
		$track_nw_msg = '';
		if (!Tracker::is_allow_track()) {
			$track_nw = esc_html__('This feature is not working because the Elementor Usage Data Sharing feature is Not Enabled.', 'bdthemes-element-pack');
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
							<h1 class="ep-feature-title"><?php esc_html_e('All Widgets', 'bdthemes-element-pack'); ?></h1>
							<div class="bdt-flex bdt-flex-between bdt-flex-middle">
								<div class="ep-count-wrap">
									<div class="ep-widget-count"><?php esc_html_e('Used:', 'bdthemes-element-pack'); ?> <b>
											<?php echo esc_html($used_widgets); ?>
										</b></div>
									<div class="ep-widget-count"><?php esc_html_e('Unused:', 'bdthemes-element-pack'); ?> <b>
											<?php echo esc_html($un_used_widgets); ?>
										</b>
									</div>
									<div class="ep-widget-count"><?php esc_html_e('Total:', 'bdthemes-element-pack'); ?>
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
							<h1 class="ep-feature-title"><?php esc_html_e('Core', 'bdthemes-element-pack'); ?></h1>
							<div class="bdt-flex bdt-flex-between bdt-flex-middle">
								<div class="ep-count-wrap">
									<div class="ep-widget-count"><?php esc_html_e('Used:', 'bdthemes-element-pack'); ?> <b>
											<?php echo esc_html($used_only_widgets); ?>
										</b></div>
									<div class="ep-widget-count"><?php esc_html_e('Unused:', 'bdthemes-element-pack'); ?> <b>
											<?php echo esc_html($unused_only_widgets); ?>
										</b></div>
									<div class="ep-widget-count"><?php esc_html_e('Total:', 'bdthemes-element-pack'); ?>
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
							<h1 class="ep-feature-title"><?php esc_html_e('3rd Party', 'bdthemes-element-pack'); ?></h1>
							<div class="bdt-flex bdt-flex-between bdt-flex-middle">
								<div class="ep-count-wrap">
									<div class="ep-widget-count"><?php esc_html_e('Used:', 'bdthemes-element-pack'); ?> <b>
											<?php echo esc_html($used_only_3rdparty); ?>
										</b></div>
									<div class="ep-widget-count"><?php esc_html_e('Unused:', 'bdthemes-element-pack'); ?> <b>
											<?php echo esc_html($unused_only_3rdparty); ?>
										</b></div>
									<div class="ep-widget-count"><?php esc_html_e('Total:', 'bdthemes-element-pack'); ?>
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
							<h1 class="ep-feature-title"><?php esc_html_e('Active', 'bdthemes-element-pack'); ?></h1>
							<div class="bdt-flex bdt-flex-between bdt-flex-middle">
								<div class="ep-count-wrap">
									<div class="ep-widget-count"><?php esc_html_e('Core:', 'bdthemes-element-pack'); ?> <b
											id="bdt-total-widgets-status-core">0</b></div>
									<div class="ep-widget-count"><?php esc_html_e('3rd Party:', 'bdthemes-element-pack'); ?>
										<b id="bdt-total-widgets-status-3rd">0</b>
									</div>
									<div class="ep-widget-count"><?php esc_html_e('Extensions:', 'bdthemes-element-pack'); ?>
										<b id="bdt-total-widgets-status-extensions">0</b>
									</div>
									<div class="ep-widget-count"><?php esc_html_e('Total:', 'bdthemes-element-pack'); ?> <b
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
						esc_html__('To view widgets analytics, Elementor %1$sUsage Data Sharing%2$s feature by Elementor needs to be activated. Please activate the feature to get widget analytics instantly ', 'bdthemes-element-pack'),
						'<b>', '</b>'
					);

					echo ' <a href="' . esc_url(admin_url('admin.php?page=elementor-settings')) . '">' . esc_html__('from here.', 'bdthemes-element-pack') . '</a>';
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
							<h1 class="bdt-text-bold"><?php echo esc_html__( 'WHY GO WITH PRO?', 'bdthemes-element-pack' ); ?></h1>
							<h2><?php echo esc_html__( 'Just Compare With Element Pack Lite Vs Pro', 'bdthemes-element-pack' ); ?></h2>
						</div>
						<div class="ep-purchase-button">
							<a href="https://elementpack.pro/pricing/" target="_blank"><?php echo esc_html__( 'Purchase Now', 'bdthemes-element-pack' ); ?></a>
						</div>
					</div>

					<div>

						<ul class="bdt-list bdt-list-divider bdt-text-left bdt-text-normal" style="font-size: 15px;">


							<li class="bdt-text-bold">
								<div class="bdt-grid">
									<?php
									echo '<div class="bdt-width-expand@m">' . esc_html__( 'Features', 'bdthemes-element-pack' ) . '</div>';
									echo '<div class="bdt-width-auto@m">' . esc_html__( 'Free', 'bdthemes-element-pack' ) . '</div>';
									echo '<div class="bdt-width-auto@m">' . esc_html__( 'Pro', 'bdthemes-element-pack' ) . '</div>';
									?>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m"><span
											bdt-tooltip="pos: top-left; title: <?php echo esc_html__( 'Lite have 35+ Widgets but Pro have 100+ core widgets', 'bdthemes-element-pack' ); ?>"><?php echo esc_html__( 'Core Widgets', 'bdthemes-element-pack' ); ?></span>
									</div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<?php echo '<div class="bdt-width-expand@m">' . esc_html__( 'Theme Compatibility', 'bdthemes-element-pack' ) . '</div>'; ?>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<?php echo '<div class="bdt-width-expand@m">' . esc_html__( 'Dynamic Content & Custom Fields Capabilities', 'bdthemes-element-pack' ) . '</div>'; ?>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<?php echo '<div class="bdt-width-expand@m">' . esc_html__( 'Proper Documentation', 'bdthemes-element-pack' ) . '</div>'; ?>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<?php echo '<div class="bdt-width-expand@m">' . esc_html__( 'Updates & Support', 'bdthemes-element-pack' ) . '</div>'; ?>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Ready Made Pages', 'bdthemes-element-pack' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Ready Made Blocks', 'bdthemes-element-pack' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Elementor Extended Widgets', 'bdthemes-element-pack' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Asset Manager', 'bdthemes-element-pack' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Live Copy or Paste', 'bdthemes-element-pack' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Template Library (in Editor)', 'bdthemes-element-pack' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'SVG Support', 'bdthemes-element-pack' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<a href="https://www.elementpack.pro/demo/element/dynamic-content/" target="_blank">
											<?php echo esc_html__( 'Dynamic Content', 'bdthemes-element-pack' ); ?>
										</a>
									</div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Smooth Scroller', 'bdthemes-element-pack' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							
							<li class="">
								<div class="bdt-grid">
									<?php echo '<div class="bdt-width-expand@m">' . esc_html__( 'Header & Footer Builder', 'bdthemes-element-pack' ) . '</div>'; ?>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Rooten Theme Pro Features', 'bdthemes-element-pack' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Priority Support', 'bdthemes-element-pack' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'WooCommerce Widgets', 'bdthemes-element-pack' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Ready Made Header & Footer', 'bdthemes-element-pack' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Essential Shortcodes', 'bdthemes-element-pack' ); ?></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-no"></span></div>
									<div class="bdt-width-auto@m"><span class="dashicons dashicons-yes"></span></div>
								</div>
							</li>
							<li class="">
								<div class="bdt-grid">
									<div class="bdt-width-expand@m">
										<?php echo esc_html__( 'Context Menu', 'bdthemes-element-pack' ); ?></div>
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
										echo '<div class="bdt-width-1-3@m"><span class="dashicons dashicons-heart"></span> ' . esc_html__( ' Incredibly Advanced', 'bdthemes-element-pack' ) . '</div>';
										echo '<div class="bdt-width-1-3@m"><span class="dashicons dashicons-heart"></span> ' . esc_html__( ' Refund or Cancel Anytime', 'bdthemes-element-pack' ) . '</div>';
										echo '<div class="bdt-width-1-3@m"><span class="dashicons dashicons-heart"></span> ' . esc_html__( ' Dynamic Content', 'bdthemes-element-pack' ) . '</div>';
										?>
									</div>
								</li>

								<li>
									<div class="bdt-grid bdt-grid-small">
										<div class="bdt-width-1-3@m">
											<span class="dashicons dashicons-heart"></span>
											<?php echo esc_html__( 'Super-Flexible Widgets', 'bdthemes-element-pack' ); ?>
										</div>
										<div class="bdt-width-1-3@m">
											<span
												class="dashicons dashicons-heart"></span><?php echo esc_html__( ' 24/7 Premium Support', 'bdthemes-element-pack' ); ?>
										</div>
										<div class="bdt-width-1-3@m">
											<span
												class="dashicons dashicons-heart"></span><?php echo esc_html__( ' Third Party Plugins', 'bdthemes-element-pack' ); ?>
										</div>
									</div>
								</li>

								<li>
									<div class="bdt-grid bdt-grid-small">
										<div class="bdt-width-1-3@m">
											<span
												class="dashicons dashicons-heart"></span><?php echo esc_html__( ' Special Discount!', 'bdthemes-element-pack' ); ?>
										</div>
										<div class="bdt-width-1-3@m">
											<span
												class="dashicons dashicons-heart"></span><?php echo esc_html__( ' Custom Field Integration', 'bdthemes-element-pack' ); ?>
										</div>
										<div class="bdt-width-1-3@m">
											<span
												class="dashicons dashicons-heart"></span><?php echo esc_html__( ' With Live Chat Support', 'bdthemes-element-pack' ); ?>
										</div>
									</div>
								</li>

								<li>
									<div class="bdt-grid bdt-grid-small">
										<div class="bdt-width-1-3@m">
											<span
												class="dashicons dashicons-heart"></span><?php echo esc_html__( ' Trusted Payment Methods', 'bdthemes-element-pack' ); ?>
										</div>
										<div class="bdt-width-1-3@m">
											<span
												class="dashicons dashicons-heart"></span><?php echo esc_html__( ' Interactive Effects', 'bdthemes-element-pack' ); ?>
										</div>
										<div class="bdt-width-1-3@m">
											<span
												class="dashicons dashicons-heart"></span><?php echo esc_html__( ' Video Tutorial', 'bdthemes-element-pack' ); ?>
										</div>
									</div>
								</li>
							</ul>

							<div class="ep-purchase-button bdt-margin-medium-top">
								<a href="https://elementpack.pro/pricing/" target="_blank"><?php echo esc_html__( 'Purchase Now', 'bdthemes-element-pack' ); ?></a>
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
					<span class="label1"><?php esc_html_e('PHP Version:', 'bdthemes-element-pack'); ?></span>

					<?php
					if (version_compare($php_version, '7.4.0', '<')) {
						echo wp_kses_post($no_icon);
						echo '<span class="label2" title="' . esc_attr__('Min: 7.4 Recommended', 'bdthemes-element-pack') . '" bdt-tooltip>' . esc_html__('Currently:', 'bdthemes-element-pack') . ' ' . esc_html($php_version) . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">' . esc_html__('Currently:', 'bdthemes-element-pack') . ' ' . esc_html($php_version) . '</span>';
					}
					?>
				</div>

			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('Max execution time:', 'bdthemes-element-pack'); ?> </span>
					<?php
					if ($max_execution_time < '90') {
						echo wp_kses_post($no_icon);
						echo '<span class="label2" title="Min: 90 Recommended" bdt-tooltip>Currently: ' . esc_html($max_execution_time) . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">Currently: ' . esc_html($max_execution_time) . '</span>';
					}
					?>
				</div>
			</li>
			<li>
				<div>
					<span class="label1"><?php esc_html_e('Memory Limit:', 'bdthemes-element-pack'); ?> </span>

					<?php
					if (intval($memory_limit) < '512') {
						echo wp_kses_post($no_icon);
						echo '<span class="label2" title="Min: 512M Recommended" bdt-tooltip>Currently: ' . esc_html($memory_limit) . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">Currently: ' . esc_html($memory_limit) . '</span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('Max Post Limit:', 'bdthemes-element-pack'); ?> </span>

					<?php
					if (intval($post_limit) < '32') {
						echo wp_kses_post($no_icon);
						echo '<span class="label2" title="Min: 32M Recommended" bdt-tooltip>Currently: ' . wp_kses_post($post_limit) . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">Currently: ' . wp_kses_post($post_limit) . '</span>';
					}
					?>
				</div>
			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('Uploads folder writable:', 'bdthemes-element-pack'); ?></span>

					<?php
					if (!is_writable($upload_path)) {
						echo wp_kses_post($no_icon);
					} else {
						echo wp_kses_post($yes_icon);
					}
					?>
				</div>

			</li>

			<li>
				<div>
					<span class="label1"><?php esc_html_e('GZip Enabled:', 'bdthemes-element-pack'); ?></span>

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
					<span class="label1"><?php esc_html_e('Debug Mode:', 'bdthemes-element-pack'); ?></span>
					<?php
					if ($environment['wp_debug_mode']) {
						echo wp_kses_post($no_icon);
						echo '<span class="label2">' . esc_html__('Currently Turned On', 'bdthemes-element-pack') . '</span>';
					} else {
						echo wp_kses_post($yes_icon);
						echo '<span class="label2">' . esc_html__('Currently Turned Off', 'bdthemes-element-pack') . '</span>';
					}
					?>
				</div>

			</li>

		</ul>

		<div class="bdt-admin-alert">
			<strong><?php esc_html_e('Note:', 'bdthemes-element-pack'); ?></strong>
			<?php
			/* translators: %s: Plugin name 'Element Pack' */
			printf(
				esc_html__('If you have multiple addons like %s so you may need to allocate additional memory for other addons as well.', 'bdthemes-element-pack'),
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
							<img src="<?php echo BDTEP_URL . 'assets/images/logo-with-text.svg'; ?>" alt="Element Pack Logo">
						</div>
					</div>

					<div class="ep-dashboard-new-page-wrapper bdt-flex bdt-flex-wrap bdt-flex-middle">
						

						<!-- Always render save button, JavaScript will control visibility -->
						<div class="ep-dashboard-save-btn" style="display: none;">
							<button class="bdt-button bdt-button-primary element-pack-settings-save-btn" type="submit">
								<?php esc_html_e('Save Settings', 'bdthemes-element-pack'); ?>
							</button>
						</div>

						<div class="ep-dashboard-new-page">
							<a class="bdt-flex bdt-flex-middle" href="<?php echo esc_url(admin_url('post-new.php?post_type=page')); ?>" class=""><i class="dashicons dashicons-admin-page"></i>
								<?php echo esc_html__('Create New Page', 'bdthemes-element-pack') ?>
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

						<div id="element_pack_extra_options_page" class="ep-option-page group">
							<?php $this->element_pack_extra_options(); ?>
						</div>

						<div id="element_pack_analytics_system_req_page" class="ep-option-page group">
							<?php $this->element_pack_analytics_system_req_content(); ?>
						</div>

						<div id="element_pack_other_plugins_page" class="ep-option-page group">
							<?php $this->element_pack_others_plugin(); ?>
						</div>

						<div id="element_pack_affiliate_page" class="ep-option-page group">
							<?php $this->element_pack_affiliate_content(); ?>
						</div>

						<div id="element_pack_get_pro_page" class="ep-option-page group">
							<?php $this->element_pack_get_pro(); ?>
						</div>

					</div>
				</div>

				<?php if (!defined('BDTEP_WL') ) {
					$this->footer_info();
				} ?>
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

			function filterSearch(e) {
				var parentID = '#' + jQuery(e).data('id');
				var search = jQuery(parentID).find('.bdt-search-input').val().toLowerCase();


				jQuery(".ep-options .ep-option-item").filter(function () {
					jQuery(this).toggle(jQuery(this).attr('data-widget-name').toLowerCase().indexOf(search) > -1)
				});

				if (!search) {
					jQuery(parentID).find('.bdt-search-input').attr('bdt-filter-control', "");
					jQuery(parentID).find('.ep-widget-all').trigger('click');
				} else {
					// if (search.length < 3) {
					//     return;
					// }
					jQuery(parentID).find('.bdt-search-input').attr('bdt-filter-control', "filter: [data-widget-name*='" + search + "']");
					jQuery(parentID).find('.bdt-search-input').removeClass('bdt-active');
				}
				jQuery(parentID).find('.bdt-search-input').trigger('click');

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
				
				const tabContainer = jQuery(this).closest('.bdt-dashboard-navigation').siblings('.bdt-tab-container');
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
						// 	message: '<div bdt-spinner></div> <?php //esc_html_e('Please wait, Saving settings...', 'bdthemes-element-pack') ?>',
						// 	timeout: false
						// });

						// Submit form using AJAX (same logic as existing form submission)
						targetForm.ajaxSubmit({
							success: function () {
								bdtUIkit.notification.closeAll();
								bdtUIkit.notification({
									message: '<span class="dashicons dashicons-yes"></span> <?php esc_html_e('Settings Saved Successfully.', 'bdthemes-element-pack') ?>',
									status: 'primary',
									pos: 'top-center'
								});
							},
							error: function (data) {
								bdtUIkit.notification.closeAll();
								bdtUIkit.notification({
									message: '<span bdt-icon=\'icon: warning\'></span> <?php esc_html_e('Unknown error, make sure access is correct!', 'bdthemes-element-pack') ?>',
									status: 'warning'
								});
							}
						});
					} else {
						// Show error if no form found
						bdtUIkit.notification({
							message: '<span bdt-icon="icon: warning"></span> <?php esc_html_e('No settings form found to save.', 'bdthemes-element-pack') ?>',
							status: 'warning'
						});
					}
				});

				// White Label Settings Functionality
				// Check if ep_admin_ajax is available
				if (typeof ep_admin_ajax === 'undefined') {
					window.ep_admin_ajax = {
						ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
						white_label_nonce: '<?php echo wp_create_nonce('ep_white_label_nonce'); ?>'
					};
				}				
				
				// Initialize CodeMirror editors for custom code
				var codeMirrorEditors = {};
				
				function initializeCodeMirrorEditors() {
					// CSS Editor 1
					if (document.getElementById('ep-custom-css')) {
						codeMirrorEditors['ep-custom-css'] = wp.codeEditor.initialize('ep-custom-css', {
							type: 'text/css',
							codemirror: {
								lineNumbers: true,
								mode: 'css',
								theme: 'default',
								lineWrapping: true,
								autoCloseBrackets: true,
								matchBrackets: true,
								lint: false
							}
						});
					}
					
					// JavaScript Editor 1
					if (document.getElementById('ep-custom-js')) {
						codeMirrorEditors['ep-custom-js'] = wp.codeEditor.initialize('ep-custom-js', {
							type: 'application/javascript',
							codemirror: {
								lineNumbers: true,
								mode: 'javascript',
								theme: 'default',
								lineWrapping: true,
								autoCloseBrackets: true,
								matchBrackets: true,
								lint: false
							}
						});
					}
					
					// CSS Editor 2
					if (document.getElementById('ep-custom-css-2')) {
						codeMirrorEditors['ep-custom-css-2'] = wp.codeEditor.initialize('ep-custom-css-2', {
							type: 'text/css',
							codemirror: {
								lineNumbers: true,
								mode: 'css',
								theme: 'default',
								lineWrapping: true,
								autoCloseBrackets: true,
								matchBrackets: true,
								lint: false
							}
						});
					}
					
					// JavaScript Editor 2
					if (document.getElementById('ep-custom-js-2')) {
						codeMirrorEditors['ep-custom-js-2'] = wp.codeEditor.initialize('ep-custom-js-2', {
							type: 'application/javascript',
							codemirror: {
								lineNumbers: true,
								mode: 'javascript',
								theme: 'default',
								lineWrapping: true,
								autoCloseBrackets: true,
								matchBrackets: true,
								lint: false
							}
						});
					}
					
					// Refresh all editors after a short delay to ensure proper rendering
					setTimeout(function() {
						refreshAllCodeMirrorEditors();
					}, 100);
				}
				
				// Function to refresh all CodeMirror editors
				function refreshAllCodeMirrorEditors() {
					Object.keys(codeMirrorEditors).forEach(function(editorKey) {
						if (codeMirrorEditors[editorKey] && codeMirrorEditors[editorKey].codemirror) {
							codeMirrorEditors[editorKey].codemirror.refresh();
						}
					});
				}
				
				// Function to refresh editors when tab becomes visible
				function refreshEditorsOnTabShow() {
					// Listen for tab changes (UIkit tab switching)
					if (typeof bdtUIkit !== 'undefined' && bdtUIkit.tab) {
						// When tab becomes active, refresh editors
						bdtUIkit.util.on(document, 'shown', '.bdt-tab', function() {
							setTimeout(function() {
								refreshAllCodeMirrorEditors();
							}, 50);
						});
					}
					
					// Also listen for direct tab clicks
					$('.bdt-tab a').on('click', function() {
						setTimeout(function() {
							refreshAllCodeMirrorEditors();
						}, 100);
					});
					
					// Listen for switcher changes (UIkit switcher)
					if (typeof bdtUIkit !== 'undefined' && bdtUIkit.switcher) {
						bdtUIkit.util.on(document, 'shown', '.bdt-switcher', function() {
							setTimeout(function() {
								refreshAllCodeMirrorEditors();
							}, 50);
						});
					}
				}
				
				// Initialize editors when page loads - with delay for better rendering
				setTimeout(function() {
					initializeCodeMirrorEditors();
				}, 100);
				
				// Setup tab switching handlers
				setTimeout(function() {
					refreshEditorsOnTabShow();
				}, 100);
				
				// Handle window resize events
				$(window).on('resize', function() {
					setTimeout(function() {
						refreshAllCodeMirrorEditors();
					}, 100);
				});
				
				// Handle page visibility changes (when switching browser tabs)
				document.addEventListener('visibilitychange', function() {
					if (!document.hidden) {
						setTimeout(function() {
							refreshAllCodeMirrorEditors();
						}, 200);
					}
				});
				
				// Force refresh when clicking on the Custom CSS & JS tab specifically
				$('a[href="#"]').on('click', function() {
					var tabText = $(this).text().trim();
					if (tabText === 'Custom CSS & JS') {
						setTimeout(function() {
							refreshAllCodeMirrorEditors();
						}, 150);
					}
				});

				// Toggle white label fields visibility
				$('#ep-white-label-enabled').on('change', function() {
					if ($(this).is(':checked')) {
						$('.ep-white-label-fields').slideDown(300);
					} else {
						$('.ep-white-label-fields').slideUp(300);
					}
				});

				// WordPress Media Library Integration for Icon Upload
				var mediaUploader;
				
				$('#ep-upload-icon').on('click', function(e) {
					e.preventDefault();
					
					// If the uploader object has already been created, reopen the dialog
					if (mediaUploader) {
						mediaUploader.open();
						return;
					}
					
					// Create the media frame
					mediaUploader = wp.media.frames.file_frame = wp.media({
						title: 'Select Icon',
						button: {
							text: 'Use This Icon'
						},
						library: {
							type: ['image/jpeg', 'image/jpg', 'image/png', 'image/svg+xml']
						},
						multiple: false
					});
					
					// When an image is selected, run a callback
					mediaUploader.on('select', function() {
						var attachment = mediaUploader.state().get('selection').first().toJSON();
						
						// Set the hidden inputs
						$('#ep-white-label-icon').val(attachment.url);
						$('#ep-white-label-icon-id').val(attachment.id);
						
						// Update preview
						$('#ep-icon-preview-img').attr('src', attachment.url);
						$('.ep-icon-preview-container').show();
					});
					
					// Open the uploader dialog
					mediaUploader.open();
				});
				
				// Remove icon functionality
				$('#ep-remove-icon').on('click', function(e) {
					e.preventDefault();
					
					// Clear the hidden inputs
					$('#ep-white-label-icon').val('');
					$('#ep-white-label-icon-id').val('');
					
					// Hide preview
					$('.ep-icon-preview-container').hide();
					$('#ep-icon-preview-img').attr('src', '');
				});

				// BDTEP_HIDE Warning when checkbox is enabled
				$('#ep-white-label-bdtep-hide').on('change', function() {
					if ($(this).is(':checked')) {
						// Show warning modal/alert
						var warningMessage = '⚠️ WARNING: ADVANCED FEATURE\n\n' +
							'Enabling BDTEP_HIDE will activate advanced white label mode that:\n\n' +
							'• Hides ALL Element Pack branding and menus\n' +
							'• Makes these settings difficult to access later\n' +
							'• Requires the special access link to return\n' +
							'• Is intended for client/agency use only\n\n' +
							'An email with access instructions will be sent if you proceed.\n\n' +
							'Are you sure you want to enable this advanced mode?';
						
						if (!confirm(warningMessage)) {
							// User cancelled, uncheck the box
							$(this).prop('checked', false);
							return false;
						}
						
						// Show additional info message
						if ($('#ep-bdtep-hide-info').length === 0) {
							$(this).closest('.ep-option-item').after(
								'<div id="ep-bdtep-hide-info" class="bdt-alert bdt-alert-warning bdt-margin-small-top">' +
								'<p><strong>BDTEP_HIDE Mode Enabled</strong></p>' +
								'<p>When you save these settings, an email will be sent with instructions to access white label settings in the future.</p>' +
								'</div>'
							);
						}
					} else {
						// Remove info message when unchecked
						$('#ep-bdtep-hide-info').remove();
					}
				});

				// Save white label settings with confirmation
				$('#ep-save-white-label').on('click', function(e) {
					e.preventDefault();
					
					// Check if button is disabled (no license or no white label eligible license)
					if ($(this).prop('disabled')) {
						var buttonText = $(this).text().trim();
						var alertMessage = '';
						
						if (buttonText.includes('License Not Activated')) {
							alertMessage = '<div class="bdt-alert bdt-alert-danger" bdt-alert>' +
								'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
								'<p><strong>License Not Activated</strong><br>You need to activate your Element Pack license to access White Label functionality. Please activate your license first.</p>' +
								'</div>';
						} else {
							alertMessage = '<div class="bdt-alert bdt-alert-warning" bdt-alert>' +
								'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
								'<p><strong>Eligible License Required</strong><br>White Label functionality is available for Agency, Extended, Developer, AppSumo Lifetime, and other eligible license holders. Please upgrade your license to access these features.</p>' +
								'</div>';
						}
						
						$('#ep-white-label-message').html(alertMessage).show();
						return false;
					}
					
					// Check if white label mode is being enabled
					var whiteLabelEnabled = $('#ep-white-label-enabled').is(':checked');
					var bdtepHideEnabled = $('#ep-white-label-bdtep-hide').is(':checked');
					
					// Only show confirmation dialog if white label is enabled AND BDTEP_HIDE is enabled
					if (whiteLabelEnabled && bdtepHideEnabled) {
						var confirmMessage = '🔒 FINAL CONFIRMATION\n\n' +
							'You are about to save settings with BDTEP_HIDE enabled.\n\n' +
							'This will:\n' +
							'• Hide Element Pack from WordPress admin immediately\n' +
							'• Send access instructions to your email addresses\n' +
							'• Require the special link to modify these settings\n\n' +
							'Email will be sent to:\n' +
							'• License email: <?php echo esc_js('farid@bdthemes.com'); ?>\n' +
							'• Admin email: <?php echo esc_js(get_bloginfo('admin_email')); ?>\n\n' +
							'Are you absolutely sure you want to proceed?';
						
						if (!confirm(confirmMessage)) {
							return false;
						}
					}
					
					var $button = $(this);
					var originalText = $button.html();
					
					// Show loading state
					$button.html('<span class="dashicons dashicons-update-alt"></span> Saving...');
					$button.prop('disabled', true);
					
					// Collect form data
					var formData = {
						action: 'ep_save_white_label',
						nonce: ep_admin_ajax.white_label_nonce,
						white_label_enabled: $('#ep-white-label-enabled').is(':checked') ? 1 : 0,
						white_label_title: $('#ep-white-label-title').val(),
						white_label_icon: $('#ep-white-label-icon').val(),
						white_label_icon_id: $('#ep-white-label-icon-id').val(),
						hide_license: $('#ep-white-label-hide-license').is(':checked') ? 1 : 0,
						bdtep_hide: $('#ep-white-label-bdtep-hide').is(':checked') ? 1 : 0
					};
					
					// Send AJAX request
					$.post(ep_admin_ajax.ajax_url, formData)
						.done(function(response) {
							if (response.success) {
								// Show success message with countdown
								var countdown = 2;
								var successMessage = response.data.message;
								
								// Add email notification info if BDTEP_HIDE was enabled
								if (response.data.bdtep_hide && response.data.email_sent) {
									successMessage += '<br><br><strong>📧 Access Email Sent!</strong><br>Check your email for the access link to modify these settings in the future.';
								} else if (response.data.bdtep_hide && !response.data.email_sent && response.data.access_url) {
									// Localhost scenario - show the access URL directly
									successMessage += '<br><br><strong>📧 Localhost Email Notice:</strong><br>Email functionality is not available on localhost.<br><strong>Your Access URL:</strong><br><a href="' + response.data.access_url + '" target="_blank">Click here to access white label settings</a><br><small>Save this URL - you\'ll need it to modify settings when BDTEP_HIDE is active.</small>';
								} else if (response.data.bdtep_hide && !response.data.email_sent) {
									successMessage += '<br><br><strong>⚠️ Email Notice:</strong><br>There was an issue sending the access email. Please check your email settings or contact support.';
								}
								
								$('#ep-white-label-message').html(
									'<div class="bdt-alert bdt-alert-success" bdt-alert>' +
									'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
									'<p>' + successMessage + ' <span id="ep-reload-countdown">Reloading in ' + countdown + ' seconds...</span></p>' +
									'</div>'
								).show();
								
								// Update button text
								$button.html('<span class="dashicons dashicons-update-alt"></span> Reloading...');
								
								// Countdown timer
								var countdownInterval = setInterval(function() {
									countdown--;
									if (countdown > 0) {
										$('#ep-reload-countdown').text('Reloading in ' + countdown + ' seconds...');
									} else {
										$('#ep-reload-countdown').text('Reloading now...');
										clearInterval(countdownInterval);
									}
								}, 1000);
								
								// Check if BDTEP_HIDE is enabled and redirect accordingly
								setTimeout(function() {
									if (response.data.bdtep_hide) {
										// Redirect to admin dashboard if BDTEP_HIDE is enabled
										window.location.href = '<?php echo admin_url('index.php'); ?>';
									} else {
										// Reload current page if BDTEP_HIDE is not enabled
										window.location.reload();
									}
								}, 1500);
							} else {
								// Show error message
								$('#ep-white-label-message').html(
									'<div class="bdt-alert bdt-alert-danger" bdt-alert>' +
									'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
									'<p>Error: ' + (response.data.message || 'Unknown error occurred') + '</p>' +
									'</div>'
								).show();
								
								// Restore button state for error case
								$button.html(originalText);
								$button.prop('disabled', false);
							}
						})
						.fail(function(xhr, status, error) {
							// Show error message
							$('#ep-white-label-message').html(
								'<div class="bdt-alert bdt-alert-danger" bdt-alert>' +
								'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
								'<p>Error: Failed to save settings. Please try again. (' + status + ')</p>' +
								'</div>'
							).show();
							
							// Restore button state for failure case
							$button.html(originalText);
							$button.prop('disabled', false);
						});
				});

				// Save custom code functionality (updated for CodeMirror)
				$('#ep-save-custom-code').on('click', function(e) {
					e.preventDefault();
					
					var $button = $(this);
					var originalText = $button.html();
					
					// Prevent multiple simultaneous saves
					if ($button.prop('disabled') || $button.hasClass('ep-saving')) {
						return;
					}
					
					// Mark as saving
					$button.addClass('ep-saving');
					
					// Get content from CodeMirror editors
					function getCodeMirrorContent(elementId) {
						if (codeMirrorEditors[elementId] && codeMirrorEditors[elementId].codemirror) {
							return codeMirrorEditors[elementId].codemirror.getValue();
						} else {
							// Fallback to textarea value
							return $('#' + elementId).val() || '';
						}
					}
					
					var cssContent = getCodeMirrorContent('ep-custom-css');
					var jsContent = getCodeMirrorContent('ep-custom-js');
					var css2Content = getCodeMirrorContent('ep-custom-css-2');
					var js2Content = getCodeMirrorContent('ep-custom-js-2');
					
					// Show loading state
					$button.html('<span class="dashicons dashicons-update-alt"></span> Saving...');
					$button.prop('disabled', true);
					
					// Timeout safeguard - if AJAX doesn't complete in 30 seconds, restore button
					var timeoutId = setTimeout(function() {
						$button.removeClass('ep-saving');
						$button.html(originalText);
						$button.prop('disabled', false);
						$('#ep-custom-code-message').html(
							'<div class="bdt-alert bdt-alert-warning" bdt-alert>' +
							'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
							'<p>Save operation timed out. Please try again.</p>' +
							'</div>'
						).show();
					}, 30000);
					
					// Collect form data
					var formData = {
						action: 'ep_save_custom_code',
						nonce: ep_admin_ajax.nonce,
						custom_css: cssContent,
						custom_js: jsContent,
						custom_css_2: css2Content,
						custom_js_2: js2Content,
						excluded_pages: $('#ep-excluded-pages').val() || []
					};
					
					
					// Verify we have some content before sending (optional check)
					var totalContentLength = cssContent.length + jsContent.length + css2Content.length + js2Content.length;
					if (totalContentLength === 0) {
						var confirmEmpty = confirm('No content detected in any editor. Do you want to save empty content (this will clear all custom code)?');
						if (!confirmEmpty) {
							// Restore button state
							$button.html(originalText);
							$button.prop('disabled', false);
							return;
						}
					}
					
					// Send AJAX request
					$.post(ep_admin_ajax.ajax_url, formData)
						.done(function(response) {
							if (response.success) {
								// Show success message
								var successMessage = response.data.message;
								if (response.data.excluded_count) {
									successMessage += ' (' + response.data.excluded_count + ' pages excluded)';
								}
								
								$('#ep-custom-code-message').html(
									'<div class="bdt-alert bdt-alert-success" bdt-alert>' +
									'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
									'<p>' + successMessage + '</p>' +
									'</div>'
								).show();
								
								// Auto-hide message after 5 seconds
								setTimeout(function() {
									$('#ep-custom-code-message').fadeOut();
								}, 5000);
								
							} else {
								// Show error message
								$('#ep-custom-code-message').html(
									'<div class="bdt-alert bdt-alert-danger" bdt-alert>' +
									'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
									'<p>Error: ' + (response.data.message || 'Unknown error occurred') + '</p>' +
									'</div>'
								).show();
							}
						})
						.fail(function(xhr, status, error) {
							// Show error message
							$('#ep-custom-code-message').html(
								'<div class="bdt-alert bdt-alert-danger" bdt-alert>' +
								'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
								'<p>Error: Failed to save custom code. Please try again. (' + status + ')</p>' +
								'</div>'
							).show();
						})
						.always(function() {
							
							// Clear the timeout since AJAX completed
							clearTimeout(timeoutId);
							
							try {
								$button.removeClass('ep-saving');
								$button.html(originalText);
								$button.prop('disabled', false);
							} catch (e) {
								// Fallback: force button restoration
								$('#ep-save-custom-code').removeClass('ep-saving').html('<span class="dashicons dashicons-yes"></span> Save Custom Code').prop('disabled', false);
							}
						});
				});

				// Reset custom code functionality (updated for CodeMirror)
				$('#ep-reset-custom-code').on('click', function(e) {
					e.preventDefault();
					
					if (confirm('Are you sure you want to reset all custom code? This action cannot be undone.')) {
						// Clear CodeMirror editors
						function clearCodeMirrorEditor(elementId) {
							if (codeMirrorEditors[elementId] && codeMirrorEditors[elementId].codemirror) {
								codeMirrorEditors[elementId].codemirror.setValue('');
							} else {
								// Fallback to clearing textarea
								$('#' + elementId).val('');
							}
						}
						
						// Clear all editors
						clearCodeMirrorEditor('ep-custom-css');
						clearCodeMirrorEditor('ep-custom-js');
						clearCodeMirrorEditor('ep-custom-css-2');
						clearCodeMirrorEditor('ep-custom-js-2');
						
						// Clear exclusions
						$('#ep-excluded-pages').val([]).trigger('change');
						
						$('#ep-custom-code-message').html(
							'<div class="bdt-alert bdt-alert-warning" bdt-alert>' +
							'<a href="#" class="bdt-alert-close" onclick="$(this).parent().parent().hide(); return false;">&times;</a>' +
							'<p>All custom code has been cleared. Don\'t forget to save changes!</p>' +
							'</div>'
						).show();
						
						// Auto-hide message after 3 seconds
						setTimeout(function() {
							$('#ep-custom-code-message').fadeOut();
						}, 3000);
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
					   .text('<?php echo esc_js(__('Installing...', 'bdthemes-element-pack')); ?>')
					   .addClass('bdt-installing');
				
				// Perform AJAX request
				jQuery.ajax({
					url: '<?php echo admin_url('admin-ajax.php'); ?>',
					type: 'POST',
					data: {
						action: 'ep_install_plugin',
						plugin_slug: pluginSlug,
						nonce: nonce
					},
					success: function(response) {
						if (response.success) {
							// Show success message
							$button.text('<?php echo esc_js(__('Installed!', 'bdthemes-element-pack')); ?>')
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
								message: '<span class="dashicons dashicons-warning"></span> <?php echo esc_js(__('Installation failed. Please try again.', 'bdthemes-element-pack')); ?>',
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
						echo sprintf(
							__('Element Pack plugin made with love by <a target="_blank" href="%1$s">BdThemes</a> Team.<br>All rights reserved by <a target="_blank" href="%1$s">BdThemes.com</a>.', 'bdthemes-element-pack'),
							esc_url('https://bdthemes.com')
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
	 * Display Affiliate Content
	 *
	 * @access public
	 * @return void
	 */

	public function element_pack_affiliate_content() {
		?>
		<div class="ep-dashboard-panel"
			bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">
			<div class="ep-dashboard-affiliate">
				<div class="bdt-card bdt-card-body">
					<h1 class="ep-feature-title">
						<?php printf(esc_html__('Earn %s as an Affiliate', 'bdthemes-element-pack'), '<strong class="ep-highlight-text">50% Commission</strong>'); ?>
					</h1>
					<p>
						<?php esc_html_e('Join our affiliate program and earn a 50% commission on every sale you refer. It\'s a great way to earn passive income while promoting high-quality WordPress plugins.', 'bdthemes-element-pack'); ?>
					</p>
					<div class="ep-affiliate-features">
						<h3 class="ep-affiliate-sub-title"><?php esc_html_e('Benefits of joining our affiliate program:', 'bdthemes-element-pack'); ?></h3>
						<ul>
							<li><?php esc_html_e('50% commission on all sales', 'bdthemes-element-pack'); ?></li>
							<li><?php esc_html_e('Real-time tracking of referrals and sales', 'bdthemes-element-pack'); ?></li>
							<li><?php esc_html_e('Dedicated affiliate support', 'bdthemes-element-pack'); ?></li>
							<li><?php esc_html_e('Marketing materials provided', 'bdthemes-element-pack'); ?></li>
							<li><?php esc_html_e('Monthly payments via PayPal', 'bdthemes-element-pack'); ?></li>
						</ul>
					</div>
					<a href="https://bdthemes.com/affiliate/?utm_sourcce=ep_wp_dashboard&utm_medium=affiliate_payout&utm_campaign=affiliate_onboarding" target="_blank"
						class="bdt-button bdt-welcome-button bdt-margin-small-top"><?php esc_html_e('Join Our Affiliate Program', 'bdthemes-element-pack'); ?></a>
				</div>
			</div>
		</div>
		<?php
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
								<?php esc_html_e('System Requirement', 'bdthemes-element-pack'); ?>
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
	 * Extra Options Start Here
	 */

	public function element_pack_extra_options() {
		?>
		<div class="ep-dashboard-panel"
			bdt-scrollspy="target: > div > div > .bdt-card; cls: bdt-animation-slide-bottom-small; delay: 300">
			<div class="ep-dashboard-extra-options">
				<div class="bdt-card bdt-card-body">
					<h1 class="ep-feature-title"><?php esc_html_e('Extra Options', 'bdthemes-element-pack'); ?></h1>

					<div class="ep-extra-options-tabs">
						<ul class="bdt-tab" bdt-tab="connect: #ep-extra-options-tab-content; animation: bdt-animation-fade">
							<li class="bdt-active"><a
									href="#"><?php esc_html_e('Custom CSS & JS', 'bdthemes-element-pack'); ?></a></li>
							<li><a href="#"><?php esc_html_e('White Label', 'bdthemes-element-pack'); ?></a></li>
						</ul>

						<div id="ep-extra-options-tab-content" class="bdt-switcher">
							<!-- Custom CSS & JS Tab -->
							<div>
								<?php $this->render_custom_css_js_section(); ?>
							</div>
							
							<!-- White Label Tab -->
							<div>
								<?php $this->render_white_label_section(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Extra Options Start Here
	 */

	/**
	 * Render Custom CSS & JS Section
	 * 
	 * @access public
	 * @return void
	 */
	public function render_custom_css_js_section() {
		?>
		<div class="ep-custom-code-section">
			<!-- Header Section -->
			<div class="ep-code-section-header">
				<h2 class="ep-section-title"><?php esc_html_e('Header Code Injection', 'bdthemes-element-pack'); ?></h2>
				<p class="ep-section-description"><?php esc_html_e('Code added here will be injected into the &lt;head&gt; section of your website.', 'bdthemes-element-pack'); ?></p>
			</div>
			<div class="ep-code-row bdt-grid bdt-grid-small" bdt-grid>
				<div class="bdt-width-1-2@m">
					<div class="ep-code-editor-wrapper">
						<h3 class="ep-code-editor-title"><?php esc_html_e('CSS', 'bdthemes-element-pack'); ?></h3>
						<p class="ep-code-editor-description"><?php esc_html_e('Enter raw CSS code without &lt;style&gt; tags.', 'bdthemes-element-pack'); ?></p>
						<div class="ep-codemirror-editor-container">
							<textarea id="ep-custom-css" name="ep_custom_css" class="ep-code-editor" data-mode="css" placeholder=".example {&#10;    background: red;&#10;    border-radius: 5px;&#10;    padding: 15px;&#10;}&#10;&#10;"><?php echo esc_textarea(get_option('ep_custom_css', '')); ?></textarea>
						</div>
					</div>
				</div>
				<div class="bdt-width-1-2@m">
					<div class="ep-code-editor-wrapper">
						<h3 class="ep-code-editor-title"><?php esc_html_e('JS', 'bdthemes-element-pack'); ?></h3>
						<p class="ep-code-editor-description"><?php esc_html_e('Enter raw JavaScript code without &lt;script&gt; tags.', 'bdthemes-element-pack'); ?></p>
						<div class="ep-codemirror-editor-container">
							<textarea id="ep-custom-js" name="ep_custom_js" class="ep-code-editor" data-mode="javascript" placeholder="alert('Hello, Element Pack!');"><?php echo esc_textarea(get_option('ep_custom_js', '')); ?></textarea>
						</div>
					</div>
				</div>
			</div>

			<!-- Footer Section -->
			<div class="ep-code-section-header bdt-margin-medium-top">
				<h2 class="ep-section-title"><?php esc_html_e('Footer Code Injection', 'bdthemes-element-pack'); ?></h2>
				<p class="ep-section-description"><?php esc_html_e('Code added here will be injected before the closing &lt;/body&gt; tag of your website.', 'bdthemes-element-pack'); ?></p>
			</div>
			<div class="ep-code-row bdt-grid bdt-grid-small bdt-margin-small-top" bdt-grid>
				<div class="bdt-width-1-2@m">
					<div class="ep-code-editor-wrapper">
						<h3 class="ep-code-editor-title"><?php esc_html_e('CSS', 'bdthemes-element-pack'); ?></h3>
						<p class="ep-code-editor-description"><?php esc_html_e('Enter raw CSS code without &lt;style&gt; tags.', 'bdthemes-element-pack'); ?></p>
						<div class="ep-codemirror-editor-container">
							<textarea id="ep-custom-css-2" name="ep_custom_css_2" class="ep-code-editor" data-mode="css" placeholder=".example {&#10;    background: green;&#10;}&#10;&#10;"><?php echo esc_textarea(get_option('ep_custom_css_2', '')); ?></textarea>
						</div>
					</div>
				</div>
				<div class="bdt-width-1-2@m">
					<div class="ep-code-editor-wrapper">
						<h3 class="ep-code-editor-title"><?php esc_html_e('JS', 'bdthemes-element-pack'); ?></h3>
						<p class="ep-code-editor-description"><?php esc_html_e('Enter raw JavaScript code without &lt;script&gt; tags.', 'bdthemes-element-pack'); ?></p>
						<div class="ep-codemirror-editor-container">
							<textarea id="ep-custom-js-2" name="ep_custom_js_2" class="ep-code-editor" data-mode="javascript" placeholder="console.log('Hello, Element Pack!');"><?php echo esc_textarea(get_option('ep_custom_js_2', '')); ?></textarea>
						</div>
					</div>
				</div>
			</div>

			<!-- Page Exclusion Section -->
			<div class="ep-code-section-header bdt-margin-medium-top">
				<h2 class="ep-section-title"><?php esc_html_e('Page & Post Exclusion Settings', 'bdthemes-element-pack'); ?></h2>
				<p class="ep-section-description"><?php esc_html_e('Select pages and posts where you don\'t want any custom code to be injected. This applies to all sections above.', 'bdthemes-element-pack'); ?></p>
			</div>
			<div class="ep-page-exclusion-wrapper">
				<label for="ep-excluded-pages" class="ep-exclusion-label">
					<?php esc_html_e('Exclude Pages & Posts:', 'bdthemes-element-pack'); ?>
				</label>
				<select id="ep-excluded-pages" name="ep_excluded_pages[]" multiple class="ep-page-select">
					<option value=""><?php esc_html_e('-- Select pages/posts to exclude --', 'bdthemes-element-pack'); ?></option>
					<?php
					$excluded_pages = get_option('ep_excluded_pages', array());
					if (!is_array($excluded_pages)) {
						$excluded_pages = array();
					}
					
					// Get all published pages
					$pages = get_pages(array(
						'sort_order' => 'ASC',
						'sort_column' => 'post_title',
						'post_status' => 'publish'
					));
					
					// Get recent posts (last 50)
					$posts = get_posts(array(
						'numberposts' => 50,
						'post_status' => 'publish',
						'post_type' => 'post',
						'orderby' => 'date',
						'order' => 'DESC'
					));
					
					// Display pages first
					if (!empty($pages)) {
						echo '<optgroup label="' . esc_attr__('Pages', 'bdthemes-element-pack') . '">';
						foreach ($pages as $page) {
							$selected = in_array($page->ID, $excluded_pages) ? 'selected' : '';
							echo '<option value="' . esc_attr($page->ID) . '" ' . $selected . '>' . esc_html($page->post_title) . '</option>';
						}
						echo '</optgroup>';
					}
					
					// Then display posts
					if (!empty($posts)) {
						echo '<optgroup label="' . esc_attr__('Recent Posts', 'bdthemes-element-pack') . '">';
						foreach ($posts as $post) {
							$selected = in_array($post->ID, $excluded_pages) ? 'selected' : '';
							$post_date = date('M j, Y', strtotime($post->post_date));
							echo '<option value="' . esc_attr($post->ID) . '" ' . $selected . '>' . esc_html($post->post_title) . ' (' . $post_date . ')</option>';
						}
						echo '</optgroup>';
					}
					?>
				</select>
				<p class="ep-exclusion-help">
					<?php esc_html_e('Hold Ctrl (or Cmd on Mac) to select multiple items. Selected pages and posts will not load any custom CSS or JavaScript code. The list shows all pages and the 50 most recent posts.', 'bdthemes-element-pack'); ?>
				</p>
			</div>

			<!-- Save Button Section -->
			<div class="ep-code-save-section bdt-margin-medium-top bdt-text-center">
				<button type="button" id="ep-save-custom-code" class="bdt-button bdt-btn-blue bdt-margin-small-right" bdt-tooltip="Upgrade to Element Pack Pro to use this feature." disabled>
					<span class="dashicons dashicons-yes"></span>
					<?php esc_html_e('Save Custom Code', 'bdthemes-element-pack'); ?>
				</button>
				<button type="button" id="ep-reset-custom-code" class="bdt-button bdt-btn-grey">
					<span class="dashicons dashicons-update"></span>
					<?php esc_html_e('Reset Code', 'bdthemes-element-pack'); ?>
				</button>
			</div>

			<!-- Success/Error Messages -->
			<div id="ep-custom-code-message" class="ep-code-message bdt-margin-small-top" style="display: none;">
				<div class="bdt-alert bdt-alert-success" bdt-alert>
					<a href class="bdt-alert-close" bdt-close></a>
					<p><?php esc_html_e('Custom code saved successfully!', 'bdthemes-element-pack'); ?></p>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render White Label Section
	 * 
	 * @access public
	 * @return void
	 */
	public function render_white_label_section() {
		?>
		<div class="ep-white-label-section">
			<h1 class="ep-feature-title"><?php esc_html_e('White Label Settings', 'bdthemes-element-pack'); ?></h1>
			<p><?php esc_html_e('Enable white label mode to hide Element Pack branding from the admin interface and widgets.', 'bdthemes-element-pack'); ?></p>

			<div class="bdt-alert bdt-alert-danger bdt-margin-medium-top" bdt-alert>
				<p><strong><?php esc_html_e('License Not Activated', 'bdthemes-element-pack'); ?></strong></p>
				<p><?php esc_html_e('You need to activate your Element Pack license to access White Label functionality. Please activate your license first.', 'bdthemes-element-pack'); ?></p>
				<div class="bdt-margin-small-top">
					<a href="https://elementpack.pro/pricing/" target="_blank" class="bdt-button bdt-btn-blue">
						<?php esc_html_e('Get License', 'bdthemes-element-pack'); ?>
					</a>
				</div>
			</div>

			<!-- White Label Options -->
			<div class="ep-white-label-options ep-white-label-locked">
				<div class="ep-option-item ">
					<div class="ep-option-item-inner bdt-card">
						<div class="bdt-flex bdt-flex-between bdt-flex-middle">
							<div>
								<h3 class="ep-option-title"><?php esc_html_e('Enable White Label Mode', 'bdthemes-element-pack'); ?></h3>
								<p class="ep-option-description">
									<?php esc_html_e('This feature requires an eligible license (Agency, Extended, Developer, AppSumo Lifetime, etc.). Upgrade your license to access white label functionality.', 'bdthemes-element-pack'); ?>
								</p>
							</div>
							<div class="ep-option-switch">
								<?php
								$white_label_enabled = false;
								// Convert to boolean to ensure proper comparison
								$white_label_enabled = (bool) $white_label_enabled;
								?>
								<label class="switch">
									<input type="checkbox" 
										   id="ep-white-label-enabled" 
										   name="ep_white_label_enabled" 
										   <?php checked($white_label_enabled, true); ?>
										   <?php disabled(true); ?>>
									<span class="slider"></span>
								</label>
							</div>
						</div>
					</div>
				</div>

				<!-- Save Button Section -->
				<div class="ep-white-label-save-section bdt-margin-small-top bdt-text-center">
					<button type="button" 
							id="ep-save-white-label" 
							class="bdt-button bdt-btn-blue"
							<?php disabled(true); ?>>
						<span class="dashicons dashicons-yes"></span>
						<?php esc_html_e('Eligible License Required', 'bdthemes-element-pack'); ?>
					</button>
				</div>

				<!-- Success/Error Messages -->
				<div id="ep-white-label-message" class="ep-white-label-message bdt-margin-small-top" style="display: none;">
					<div class="bdt-alert bdt-alert-success" bdt-alert>
						<a href class="bdt-alert-close" bdt-close></a>
						<p><?php esc_html_e('White label settings saved successfully!', 'bdthemes-element-pack'); ?></p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Check plugin status (installed, active, or not installed)
	 * 
	 * @param string $plugin_path Plugin file path
	 * @return string 'active', 'installed', or 'not_installed'
	 */
	private function get_plugin_status($plugin_path) {
		// Check if plugin is active
		if (is_plugin_active($plugin_path)) {
			return 'active';
		}
		
		// Check if plugin is installed but not active
		$installed_plugins = get_plugins();
		if (isset($installed_plugins[$plugin_path])) {
			return 'installed';
		}
		
		// Plugin is not installed
		return 'not_installed';
	}

	/**
	 * Get plugin action button HTML based on plugin status
	 * 
	 * @param string $plugin_path Plugin file path
	 * @param string $install_url Plugin installation URL
	 * @param string $plugin_slug Plugin slug for activation
	 * @return string Button HTML
	 */
	private function get_plugin_action_button($plugin_path, $install_url, $plugin_slug = '') {
		$status = $this->get_plugin_status($plugin_path);
		
		switch ($status) {
			case 'active':
				return '';
				
			case 'installed':
				$activate_url = wp_nonce_url(
					add_query_arg([
						'action' => 'activate',
						'plugin' => $plugin_path
					], admin_url('plugins.php')),
					'activate-plugin_' . $plugin_path
				);
				return '<a class="bdt-button bdt-welcome-button" href="' . esc_url($activate_url) . '">' . 
				       __('Activate', 'bdthemes-element-pack') . '</a>';
				
			case 'not_installed':
			default:
				$plugin_slug = $this->extract_plugin_slug_from_path($plugin_path);
				$nonce = wp_create_nonce('ep_install_plugin_nonce');
				return '<a class="bdt-button bdt-welcome-button ep-install-plugin" 
				          data-plugin-slug="' . esc_attr($plugin_slug) . '" 
				          data-nonce="' . esc_attr($nonce) . '" 
				          href="#">' . 
				       __('Install', 'bdthemes-element-pack') . '</a>';
		}
	}

	/**
	 * Handle AJAX plugin installation
	 * 
	 * @access public
	 * @return void
	 */
	public function install_plugin_ajax() {
		// Check nonce
		if (!wp_verify_nonce($_POST['nonce'], 'ep_install_plugin_nonce')) {
			wp_send_json_error(['message' => __('Security check failed', 'bdthemes-element-pack')]);
		}

		// Check user capability
		if (!current_user_can('install_plugins')) {
			wp_send_json_error(['message' => __('You do not have permission to install plugins', 'bdthemes-element-pack')]);
		}

		$plugin_slug = sanitize_text_field($_POST['plugin_slug']);

		if (empty($plugin_slug)) {
			wp_send_json_error(['message' => __('Plugin slug is required', 'bdthemes-element-pack')]);
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
			wp_send_json_error(['message' => __('Plugin not found: ', 'bdthemes-element-pack') . $api->get_error_message()]);
		}

		// Install the plugin
		$skin = new \WP_Ajax_Upgrader_Skin();
		$upgrader = new \Plugin_Upgrader($skin);
		$result = $upgrader->install($api->download_link);

		if (is_wp_error($result)) {
			wp_send_json_error(['message' => __('Installation failed: ', 'bdthemes-element-pack') . $result->get_error_message()]);
		} elseif ($skin->get_errors()->has_errors()) {
			wp_send_json_error(['message' => __('Installation failed: ', 'bdthemes-element-pack') . $skin->get_error_messages()]);
		} elseif (is_null($result)) {
			wp_send_json_error(['message' => __('Installation failed: Unable to connect to filesystem', 'bdthemes-element-pack')]);
		}

		// Get installation status
		$install_status = install_plugin_install_status($api);
		
		wp_send_json_success([
			'message' => __('Plugin installed successfully!', 'bdthemes-element-pack'),
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
