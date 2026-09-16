<?php

namespace ElementPack\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load the Remote Data Handler
require_once __DIR__ . '/class-remote-data-handler.php';

use ElementPack\Admin\ModuleService;
use Elementor\Plugin;
/**
 * Overwrite the feedback method in the WP_Upgrader_Skin
 * to suppress the normal feedback.
 */

require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

class Quiet_Upgrader_Skin extends \WP_Upgrader_Skin {
	/*
	 * Suppress normal upgrader feedback / output
	 */
	public function feedback( $string, ...$args ) {
		/* no output */
	}
}


class Setup_Wizard {

	// Singleton instance
	private static $instance = null;

	// Constructor
	private function __construct() {
		$this->init_hooks();
	}

	// Get instance
	public static function get_instance() {
		if ( self::$instance == null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Newsletter list endpoint the welcome step's opt-in posts to.
	 */
	const SUBSCRIBE_ENDPOINT = 'https://marketing.sigmative.com/newsletter/rui/lists/6a9943aacbe70/embedded-form-subscribe';

	/**
	 * Customer identifier required by the newsletter endpoint.
	 */
	const SUBSCRIBE_CUSTOMER_UID = '6a93d39ce0ebd';

	// Initialize hooks
	private function init_hooks() {
		add_action( 'wp_ajax_ep_setup_wizard_install_plugins', array( $this, 'install_plugins' ) );
		add_action( 'wp_ajax_ep_setup_wizard_subscribe', array( $this, 'ajax_subscribe' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'activate_default_widgets' ) );
		add_action( 'admin_init', array( $this, 'maybe_display_setup_wizard' ) );
		add_action( 'admin_init', array( $this, 'check_manual_wizard_request' ) );

	}

	// Check for manual wizard requests
	public function check_manual_wizard_request() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only screen routing, no state change.
		$is_setup_wizard_request = isset($_GET['ep_setup_wizard']) && 'show' === sanitize_text_field(wp_unslash($_GET['ep_setup_wizard']));
		
		if ( $is_setup_wizard_request ) {
			// Use the same approach as first activation - completely override the page
			add_action('admin_head', function() {
				?>
				<style>
					html, body {
						height: 100%;
						margin: 0;
						padding: 0;
						overflow: hidden;
					}
					#wpwrap, #wpcontent, #wpbody, #wpbody-content {
						height: 100%;
						padding: 0;
						margin: 0;
					}
					#adminmenumain, #wpadminbar {
						display: none;
					}
				</style>
				<script>
					jQuery(document).ready(function($) {
						$('body').addClass('bdt-setup-wizard-active');
					});
				</script>
				<?php
				
				// Display setup wizard using the same method as first activation
				$this->display_page();
			});
		}
	}

	// Display wizard in fullscreen mode
	public function display_wizard_fullscreen() {
		?>
		<style>
			html, body {
				height: 100%;
				margin: 0;
				padding: 0;
				overflow: hidden;
			}
			#wpwrap, #wpcontent, #wpbody, #wpbody-content {
				height: 100%;
				padding: 0;
				margin: 0;
			}
			#adminmenumain, #wpadminbar {
				display: none;
			}
		</style>
		<?php
		// Directly output the wizard content
		add_action('admin_footer', function() {
			echo '<div id="ep-setup-wizard-container">';
			$this->display_page();
			echo '</div>';
			?>
			<script>
				jQuery(document).ready(function($) {
					$('body').addClass('bdt-setup-wizard-active');
					// Hide all other content and show only our wizard
					$('#wpbody-content').html($('#ep-setup-wizard-container').html());
					$('#ep-setup-wizard-container').remove();
				});
			</script>
			<?php
		}, 999);
	}

	// Get wizard HTML content
	public function get_wizard_html() {
		ob_start();
		?>
		<div class="bdt-setup-wizard-overlay ep-setup-wizard">
			<div class="bdt-setup-wizard content-loaded">
				<?php
				require_once plugin_dir_path( BDTEP__FILE__ ) . 'includes/setup-wizard/views/render.php';
				?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	// Check if this is first activation and display setup wizard if needed
	public function maybe_display_setup_wizard() {
		// Only check for first activation here
		if ( get_option( 'bdtep_setup_wizard_completed' ) === false ) {
			// Set the flag so it doesn't run again
			update_option( 'bdtep_setup_wizard_completed', true );
			
			// Add a header to ensure proper full-page display
			add_action('admin_head', function() {
				?>
				<style>
					html, body {
						height: 100%;
						margin: 0;
						padding: 0;
						overflow: hidden;
					}
					#wpwrap, #wpcontent, #wpbody, #wpbody-content {
						height: 100%;
						padding: 0;
						margin: 0;
					}
					#adminmenumain, #wpadminbar {
						display: none;
					}
				</style>
				<script>
					jQuery(document).ready(function($) {
						$('body').addClass('bdt-setup-wizard-active');
					});
				</script>
				<?php
				
				// Display setup wizard
				$this->display_page();
			});
		}
	}

	// Keep the admin_menu method for reference but not hooked
	public function admin_menu() {
		add_submenu_page(
			'element_pack_options',
			esc_html__( 'Setup Wizard', 'bdthemes-element-pack-lite' ),
			esc_html__( 'Setup Wizard', 'bdthemes-element-pack-lite' ),
			'manage_options',
			'element-pack-setup-wizard',
			array( $this, 'display_page' )
		);
	}

	public function display_page() {
		?>
		<div class="bdt-setup-wizard-overlay ep-setup-wizard">
			<div class="bdt-setup-wizard content-loaded">
				<?php
				require_once plugin_dir_path( BDTEP__FILE__ ) . 'includes/setup-wizard/views/render.php';
				?>
			</div>
		</div>
		<?php
	}

	// Enqueue necessary scripts
	public function enqueue_scripts() {
		wp_register_script( 'bdt-setup-wizard', plugins_url( 'assets/js/setup-wizard.min.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
		wp_register_style( 'bdt-setup-wizard', plugins_url( 'assets/css/setup-wizard.css', __FILE__ ), array(), '1.0.0' );

		wp_enqueue_script( 'bdt-setup-wizard' );
		wp_enqueue_style( 'bdt-setup-wizard' );

		wp_localize_script(
			'bdt-setup-wizard',
			'BDT_SetupWizard',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'ep_setup_wizard_nonce' ),
				'is_fullscreen' => true
			)
		);
	}

	public static function get_widget_map() {
		$arr_obj = ModuleService::get_widget_settings(
			function ( $settings ) {
				$core_widgets = $settings['settings_fields']['element_pack_active_modules'];
				return $core_widgets;
			}
		);
		return $arr_obj;
	}

	/**
	 * Handle the newsletter opt-in on the welcome step.
	 *
	 * Opt-in only: nothing is sent unless the administrator ticked the box,
	 * which is unticked by default. The choice is recorded either way so the
	 * wizard can show it again on a re-run. Runs server side so the
	 * cross-origin POST is not subject to CORS.
	 */
	public function ajax_subscribe() {
		check_ajax_referer( 'ep_setup_wizard_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized', 'bdthemes-element-pack-lite' ) ) );
		}

		// Record the choice first, whichever way it went.
		$consent = isset( $_POST['consent'] ) && 'yes' === sanitize_text_field( wp_unslash( $_POST['consent'] ) );

		update_option( 'bdtep_subscribe_optin', $consent ? 'yes' : 'no' );

		if ( ! $consent ) {
			// No opt-in: the choice is stored and nothing leaves the site.
			wp_send_json_success(
				array(
					'subscribed' => false,
					'message'    => esc_html__( 'Preferences saved.', 'bdthemes-element-pack-lite' ),
				)
			);
		}

		// Keep the raw value: sanitize_email() flattens anything malformed to an
		// empty string, which would otherwise be indistinguishable from "left blank".
		$raw_email = isset( $_POST['email'] ) ? sanitize_text_field( wp_unslash( $_POST['email'] ) ) : '';
		$email     = sanitize_email( $raw_email );

		if ( '' === trim( $raw_email ) ) {
			wp_send_json_success(
				array(
					'subscribed' => false,
					'message'    => esc_html__( 'Preferences saved.', 'bdthemes-element-pack-lite' ),
				)
			);
		}

		if ( ! is_email( $email ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Please enter a valid email address.', 'bdthemes-element-pack-lite' ) ) );
		}

		// Never subscribe the same address twice from this site.
		if ( get_option( 'bdtep_subscribed_email' ) === $email ) {
			wp_send_json_success(
				array(
					'subscribed' => true,
					'message'    => esc_html__( 'You are already subscribed.', 'bdthemes-element-pack-lite' ),
				)
			);
		}

		$current_user = wp_get_current_user();

		$body = array(
			'customer_uid' => apply_filters( 'bdtep/setup_wizard/subscribe_customer_uid', self::SUBSCRIBE_CUSTOMER_UID ),
			'EMAIL'        => $email,
			'FIRST_NAME'   => $current_user ? $current_user->first_name : '',
			'LAST_NAME'    => $current_user ? $current_user->last_name : '',
		);

		$response = wp_safe_remote_post(
			apply_filters( 'bdtep/setup_wizard/subscribe_url', self::SUBSCRIBE_ENDPOINT ),
			array(
				'timeout'   => 15,
				'body'      => apply_filters( 'bdtep/setup_wizard/subscribe_body', $body, $email ),
				'headers'   => array( 'Accept' => '*/*' ),
				'sslverify' => true,
			)
		);

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Could not reach the subscription service. Please try again later.', 'bdthemes-element-pack-lite' ) ) );
		}

		$code = wp_remote_retrieve_response_code( $response );

		if ( $code < 200 || $code >= 400 ) {
			wp_send_json_error( array( 'message' => esc_html__( 'The subscription service rejected the request.', 'bdthemes-element-pack-lite' ) ) );
		}

		update_option( 'bdtep_subscribed_email', $email );

		wp_send_json_success(
			array(
				'subscribed' => true,
				'message'    => esc_html__( 'Thanks for subscribing!', 'bdthemes-element-pack-lite' ),
			)
		);
	}

	/**
	 * Validate a caller-supplied plugin file reference.
	 *
	 * Only the "directory/file.php" shape used by WordPress plugin basenames is
	 * accepted. Absolute paths, traversal sequences and anything that is not a
	 * PHP file inside a single plugin directory are rejected outright, so a
	 * request can never point installation or activation at a path of its own
	 * choosing.
	 *
	 * @param mixed $plugin_slug Raw value from the request.
	 * @return string Sanitized plugin basename, or an empty string if invalid.
	 */
	private function sanitize_plugin_basename( $plugin_slug ) {
		if ( ! is_string( $plugin_slug ) ) {
			return '';
		}

		$plugin_slug = sanitize_text_field( wp_unslash( $plugin_slug ) );

		// The integration step posts WordPress.org slugs ("ultimate-post-kit"),
		// because that is all the plugins API reports for something that is not
		// installed yet; the main file is only knowable afterwards. Accept that
		// form as well as a full "dir/file.php" basename, and let
		// get_plugin_file() resolve a slug to its real file.
		$is_basename = (bool) preg_match( '#^[A-Za-z0-9][A-Za-z0-9._-]*/[A-Za-z0-9][A-Za-z0-9._-]*\.php$#', $plugin_slug );
		$is_slug     = (bool) preg_match( '#^[A-Za-z0-9][A-Za-z0-9._-]*$#', $plugin_slug );

		if ( ! $is_basename && ! $is_slug ) {
			return '';
		}

		// Belt and braces: reject any traversal that survived the pattern.
		if ( false !== strpos( $plugin_slug, '..' ) ) {
			return '';
		}

		return $plugin_slug;
	}

	// Install plugins
	public function install_plugins() {
		check_ajax_referer( 'ep_setup_wizard_nonce', 'nonce' );

		// Installing and activating are separate capabilities; this endpoint does both.
		if ( ! current_user_can( 'install_plugins' ) || ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( array( 'message' => 'Unauthorized' ) );
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Each entry is sanitized through sanitize_plugin_basename() below.
		$raw_slugs = isset( $_POST['plugins'] ) ? wp_unslash( $_POST['plugins'] ) : array();

		if ( empty( $raw_slugs ) || ! is_array( $raw_slugs ) ) {
			wp_send_json_error( array( 'message' => 'Invalid plugins array' ) );
		}

		$plugin_slugs = array();
		foreach ( $raw_slugs as $raw_slug ) {
			$clean_slug = $this->sanitize_plugin_basename( $raw_slug );

			if ( '' !== $clean_slug ) {
				$plugin_slugs[] = $clean_slug;
			}
		}

		if ( empty( $plugin_slugs ) ) {
			wp_send_json_error( array( 'message' => 'Invalid plugins array' ) );
		}

		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader-skin.php';
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		// Replace new \Plugin_Installer_Skin with new Quiet_Upgrader_Skin when output needs to be suppressed.
		$skin = new Quiet_Upgrader_Skin();
		// $skin     = new \Plugin_Installer_Skin( array( 'api' => $api ) );
		$upgrader = new \Plugin_Upgrader( $skin );

		// $upgrader = new \Plugin_Upgrader();

		$results = array();

		foreach ( $plugin_slugs as $plugin_slug ) {
            // The request carries a wp.org slug, so the main file is whatever
            // the installed plugin actually uses ('' while not installed).
            $plugin_file = $this->get_plugin_file( $plugin_slug );

            // skip when the plugin is already active
            if ('' !== $plugin_file && is_plugin_active($plugin_file)) {
                $results[] = array(
                    'slug'    => $plugin_slug,
                    'success' => true,
                    'message' => 'Installed and activated successfully',
                );
                continue;
            }

            // Download the plugin if the plugin is not installed
            if ('' === $plugin_file) {
                $slug = explode('/', $plugin_slug)[0];
                $api = plugins_api( 'plugin_information', array( 'slug' => $slug ) );

                if ( is_wp_error( $api ) ) {
                    $results[] = array(
                        'slug'    => $plugin_slug,
                        'success' => false,
                        'message' => $api->get_error_message(),
                    );
                    continue;
                }

                $result = $upgrader->install( $api->download_link );
                if ( is_wp_error( $result ) ) {
                    $results[] = array(
                        'slug'    => $plugin_slug,
                        'success' => false,
                        'message' => $result->get_error_message(),
                    );
                    continue;
                }

                // The plugin list is cached; refresh it so the file that was
                // just written is visible, then resolve the real basename.
                wp_clean_plugins_cache( false );
                $plugin_file = $this->get_plugin_file( $plugin_slug );

                if ( '' === $plugin_file ) {
                    $results[] = array(
                        'slug'    => $plugin_slug,
                        'success' => false,
                        'message' => 'Installed, but the plugin file could not be located.',
                    );
                    continue;
                }
            }

            // active the plugin
            if ( is_plugin_inactive($plugin_file) ) {
                // validate_plugin() confirms the file is a real plugin inside
                // WP_PLUGIN_DIR before we hand it to activate_plugin().
                $is_valid_plugin = validate_plugin( $plugin_file );

                if ( is_wp_error( $is_valid_plugin ) ) {
                    $results[] = array(
                        'slug'    => $plugin_slug,
                        'success' => false,
                        'message' => $is_valid_plugin->get_error_message(),
                    );
                    continue;
                }

                $activation_result = activate_plugin( $plugin_file );
                if ( is_wp_error( $activation_result ) ) {
                    $results[] = array(
                        'slug'    => $plugin_slug,
                        'success' => false,
                        'message' => $activation_result->get_error_message(),
                    );
                    continue;
                }

                $results[] = array(
                    'slug'    => $plugin_slug,
                    'success' => true,
                    'message' => 'Installed and activated successfully',
                );
            }
		}

		ob_clean();
		wp_send_json_success( array( 'results' => $results ) );
		wp_die();
	}

	/**
	 * Resolve a plugin reference to the installed plugin's main file.
	 *
	 * Accepts either a WordPress.org slug ("ultimate-post-kit") or a full
	 * basename ("ultimate-post-kit/ultimate-post-kit.php"). Matching is exact
	 * on the plugin's own directory: a substring match would let "ai-image"
	 * resolve to an unrelated "ai-image-extras/..." that happens to be
	 * installed.
	 *
	 * @param string $slug Plugin slug or basename.
	 * @return string Plugin file path, or '' when the plugin is not installed.
	 */
	private function get_plugin_file( $slug ) {
		$plugins = get_plugins();

		if ( false !== strpos( $slug, '/' ) ) {
			return isset( $plugins[ $slug ] ) ? $slug : '';
		}

		foreach ( $plugins as $file => $plugin ) {
			if ( dirname( $file ) === $slug ) {
				return $file;
			}
		}

		return '';
	}
    
    /**
     * Activate default widgets in setup wizard
     */
    public function activate_default_widgets() {
        // List of widgets to activate by default
        $default_active_widgets = array(
            'accordion',
            'advanced-button',
            'advanced-heading',
            'advanced-icon-box',
            'advanced-image-gallery',
            'audio-player',
            'brand-grid',
            'call-out',
            'carousel',
            'custom-gallery',
            'custom-carousel',
            'contact-form',
            'dropbar',
            'iconnav',
            'lightbox',
            'modal',
            'member',
            'navbar',
            'price-list',
            'price-table',
            'panel-slider',
            'slider',
            'post-grid',
            'post-list',
            'product-grid',
            'search',
            'scroll-button',
            'social-share',
            'tabs',
            'trailer-box',
            'user-login'
        );
        
        // Get current active modules
        $active_modules = get_option('element_pack_active_modules', array());
        
        // Make sure $active_modules is an array
        if (!is_array($active_modules)) {
            $active_modules = array();
        }
        
        // Check if active_modules option exists and is not empty
        // If it's a new installation or option doesn't exist, we'll set our defaults
        $modified = false;
        
        foreach ($default_active_widgets as $widget) {
            // Only set if not already defined (prevents overriding user settings on existing installations)
            if (!isset($active_modules[$widget])) {
                $active_modules[$widget] = 'on';
                $modified = true;
            }
        }
        
        // Update the option if changes were made
        if ($modified) {
            update_option('element_pack_active_modules', $active_modules);
        }
    }
}

// Initialize the Setup Wizard
Setup_Wizard::get_instance();

use Elementor\TemplateLibrary\Source_Local;

add_action('wp_ajax_ep_setup_wizard_import_template', function () {
		check_ajax_referer( 'ep_setup_wizard_nonce', 'nonce' );

		// Capability check - only administrators can import templates
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Unauthorized', 'bdthemes-element-pack-lite' ) ] );
			wp_die();
		}

		$json_url = isset( $_POST['import_url'] ) ? esc_url_raw( wp_unslash( $_POST['import_url'] ) ) : '';

        $response = wp_safe_remote_get($json_url, array(
            'timeout'   => 60,
            'sslverify' => false
        ));

        if (is_wp_error($response)) {
            wp_send_json_error([
                'message' => sprintf(
                    /* translators: %s: error reported by the HTTP request. */
                    esc_html__('Failed to fetch template from URL: %s', 'bdthemes-element-pack-lite'),
                    esc_html($response->get_error_message())
                ),
            ]);
            wp_die();
        }

        $response_code = (int) wp_remote_retrieve_response_code($response);

        if (200 !== $response_code) {
            wp_send_json_error([
                'message' => sprintf(
                    /* translators: 1: HTTP status code, 2: template URL. */
                    esc_html__('Failed to fetch template from URL (HTTP %1$d): %2$s', 'bdthemes-element-pack-lite'),
                    $response_code,
                    esc_html($json_url)
                ),
            ]);
            wp_die();
        }

        $sourceData = wp_remote_retrieve_body($response);
        $sourceData2 = json_decode($sourceData, true);

        if (!$sourceData2 || !is_array($sourceData2)) {
            wp_send_json_error(['message' => esc_html__('The template URL did not return valid template JSON.', 'bdthemes-element-pack-lite')]);
            wp_die();
        }

        $temp_file = wp_upload_dir()['path'] . '/elementor_import_' . time() . '.json';
        file_put_contents($temp_file, $sourceData);

        // Initialize Elementor's Template Importer
        if (!class_exists('\Elementor\TemplateLibrary\Source_Local')) {
            wp_delete_file($temp_file);
            wp_send_json_error(['message' => esc_html__('Elementor is not installed or activated!', 'bdthemes-element-pack-lite')]);
            wp_die();
        }

        $manager = new Source_Local();
        $templateData = $manager->import_template('elementor_template', $temp_file);
        wp_delete_file($temp_file); // Delete temp file after import

        if (is_wp_error($templateData) || !is_array($templateData) || empty($templateData[0]['template_id'])) {
            wp_send_json_error(['message' => esc_html__('Failed to import template!', 'bdthemes-element-pack-lite')]);
            wp_die();
        }

        $template_id = $templateData[0]['template_id'];
        $metaData = get_post_meta($template_id);

        $page_title = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : esc_html__("No Title", 'bdthemes-element-pack-lite');

        // Validate Elementor Data
        if (!isset($metaData['_elementor_data'][0])) {
            wp_send_json_error(['message' => esc_html__('Elementor data not found in template.', 'bdthemes-element-pack-lite')]);
            wp_die();
        }

        $_elementor_data = wp_slash($metaData['_elementor_data'][0]);

        // Create New Page
        $new_post_id = wp_insert_post([
            'post_type'    => 'page',
            'post_status'  => empty($page_title) ? 'draft' : 'publish',
            'post_title'   => $page_title,
            'post_content' => '',
        ]);

        if (is_wp_error($new_post_id)) {
            wp_send_json_error(['message' => esc_html__('Failed to create page!', 'bdthemes-element-pack-lite')]);
            wp_die();
        }

        // Assign Elementor Template Data
        update_post_meta($new_post_id, '_elementor_data', $_elementor_data);

        // Import Page Settings if available
        if (isset($metaData['_elementor_page_settings'][0])) {
            $_elementor_page_settings = is_serialized($metaData['_elementor_page_settings'][0])
                ? unserialize($metaData['_elementor_page_settings'][0], ['allowed_classes' => false])
                : $metaData['_elementor_page_settings'][0];
            update_post_meta($new_post_id, '_elementor_page_settings', $_elementor_page_settings);
        }

        update_post_meta($new_post_id, '_elementor_template_type', $sourceData2['type'] ?? '');
        update_post_meta($new_post_id, '_elementor_edit_mode', 'builder');
//        update_post_meta($new_post_id, '_wp_page_template', !empty($pageTemplate) ? $pageTemplate : 'elementor_header_footer');

        wp_send_json_success([
            'message'   => esc_html__('The template was imported successfully.', 'bdthemes-element-pack-lite'),
            'ids'       => $new_post_id,
            'edit_link' => admin_url('post.php?post=' . $new_post_id . '&action=elementor'),
        ]);
	}
);


/**
 * Map an import URL back to a starter kit that ships inside this plugin.
 *
 * Full builds carry the .zip kits under includes/setup-wizard/assets/templates/.
 * wordpress.org builds cannot ship compressed files, so those kits are fetched
 * from the remote host instead. Returns the absolute path for a bundled kit and
 * null for anything else, which is then downloaded over HTTP.
 *
 * @param string $file_url Import URL supplied by the wizard.
 * @return string|null
 */
function element_pack_setup_wizard_bundled_kit_path( $file_url ) {
	$templates_url  = plugins_url( 'includes/setup-wizard/assets/templates/', BDTEP__FILE__ );
	$templates_path = BDTEP_INC_PATH . 'setup-wizard/assets/templates/';

	// The site may be reached over either scheme, so compare without one.
	$strip_scheme = static function ( $url ) {
		return preg_replace( '#^https?://#i', '', (string) $url );
	};

	if ( 0 !== strpos( $strip_scheme( $file_url ), $strip_scheme( $templates_url ) ) ) {
		return null;
	}

	$file_name = sanitize_file_name( wp_basename( (string) wp_parse_url( $file_url, PHP_URL_PATH ) ) );

	if ( '' === $file_name || 'zip' !== strtolower( pathinfo( $file_name, PATHINFO_EXTENSION ) ) ) {
		return null;
	}

	$real_base = realpath( $templates_path );
	$real_file = realpath( $templates_path . $file_name );

	// Confine reads to the bundled templates directory, whatever the URL says.
	if ( false === $real_base || false === $real_file
		|| 0 !== strpos( $real_file, $real_base . DIRECTORY_SEPARATOR )
		|| ! is_file( $real_file ) ) {
		return null;
	}

	return $real_file;
}

add_action('wp_ajax_ep_setup_wizard_import_bundle', function () {
    check_ajax_referer('ep_setup_wizard_nonce', 'nonce');

    // Capability check - only administrators can import templates
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( [ 'message' => esc_html__( 'Unauthorized', 'bdthemes-element-pack-lite' ) ] );
        wp_die();
    }

    $file_url = isset($_POST['import_url']) ? esc_url_raw(wp_unslash($_POST['import_url'])) : '';

    if (!filter_var($file_url, FILTER_VALIDATE_URL) || 0 !== strpos($file_url, 'http')) {
        wp_send_json_error(['message' => esc_html__('Invalid import URL', 'bdthemes-element-pack-lite')]);
    }

    // A kit that ships with the plugin is read from disk. Pulling it over HTTP
    // would make the site request its own URL, which many hosts block.
    $local_kit = element_pack_setup_wizard_bundled_kit_path($file_url);

    if ($local_kit) {
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading a validated file inside this plugin.
        $kit_body = file_get_contents($local_kit);

        if (false === $kit_body) {
            wp_send_json_error(['message' => esc_html__('The bundled template could not be read.', 'bdthemes-element-pack-lite')]);
        }
    } else {
        $remote_zip_request = wp_safe_remote_get($file_url, array(
            'timeout'   => 60,
            'sslverify' => false,
        ));

        if (is_wp_error($remote_zip_request)) {
            wp_send_json_error([
                'message' => sprintf(
                    /* translators: %s: error reported by the HTTP request. */
                    esc_html__('Failed to fetch template from URL: %s', 'bdthemes-element-pack-lite'),
                    esc_html($remote_zip_request->get_error_message())
                ),
            ]);
        }

        $response_code = (int) wp_remote_retrieve_response_code($remote_zip_request);

        if (200 !== $response_code) {
            wp_send_json_error([
                'message' => sprintf(
                    /* translators: 1: HTTP status code, 2: template URL. */
                    esc_html__('Failed to fetch template from URL (HTTP %1$d): %2$s', 'bdthemes-element-pack-lite'),
                    $response_code,
                    esc_html($file_url)
                ),
            ]);
        }

        $kit_body = wp_remote_retrieve_body($remote_zip_request);
    }

    $kit_zip_path = Plugin::$instance->uploads_manager->create_temp_file($kit_body, 'kit.zip');

    $app = Plugin::$instance->app;
    if (!$app) {
        wp_send_json_error(['message' => esc_html__('Elementor app not available', 'bdthemes-element-pack-lite')]);
    }

    $import_export_module = $app->get_component('import-export');

    try {
        $result = $import_export_module->upload_kit($kit_zip_path, 'local');
        $manifest = $result['manifest'] ?? [];
        // A kit may legitimately declare no plugins; every other manifest key
        // below is read defensively, and an unguarded read here turned such a
        // kit into "Import failed: foreach() argument must be of type array".
        $plugins = $manifest['plugins'] ?? [];

        $missingPlugins = [];
        foreach ($plugins as $plugin) {
            $pluginSlug = $plugin['plugin'].".php";
            if (is_plugin_inactive($pluginSlug)) {
                $missingPlugins[] = $plugin;
            }
        }

        if (count($missingPlugins)) {
            wp_send_json_error([
                'plugins' => $missingPlugins,
                'message' => esc_html__('Missing plugins', 'bdthemes-element-pack-lite'),
            ]);
        }

        $tmp_folder_id = $result['session'] ?? '';

        if ('' === $tmp_folder_id) {
            wp_send_json_error(['message' => esc_html__('Import failed: the uploaded kit returned no session.', 'bdthemes-element-pack-lite')]);
        }
        $includes = [];
        $selectedCustomPostTypes = [];

        if (isset($manifest['templates'])) {
            $includes[] = 'templates';
        }

        if (isset($manifest['content'])) {
            $includes[] = 'content';
        }

        if (isset($manifest['site-settings'])) {
            $includes[] = 'settings';
        }

        if (isset($manifest['custom-post-type-title'])) {
            $selectedCustomPostTypes = array_keys($manifest['custom-post-type-title']);
        }

        $settings = [
            'id'                      => '',
            'session'                 => $tmp_folder_id,
            'include'                 => $includes,
            'overrideConditions'      => [],
            'selectedCustomPostTypes' => $selectedCustomPostTypes,
        ];

        $import = $import_export_module->import_kit($tmp_folder_id, $settings, true);

        Plugin::$instance->uploads_manager->enable_unfiltered_files_upload();

        wp_send_json_success($import);
    } catch (\Throwable $e) {
        wp_send_json_error(['message' => esc_html__('Import failed: ', 'bdthemes-element-pack-lite') . esc_html($e->getMessage())]);
    }
});

add_action('wp_ajax_ep_setup_wizard_import_bundle_runner', function () {
    check_ajax_referer('ep_setup_wizard_nonce', 'nonce');

    // Capability check - only administrators can import templates
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( [ 'message' => esc_html__( 'Unauthorized', 'bdthemes-element-pack-lite' ) ] );
        wp_die();
    }

    $runner = isset($_POST['runner']) ? sanitize_text_field(wp_unslash($_POST['runner'])) : '';
    $sessionId = isset($_POST['sessionId']) ? sanitize_text_field(wp_unslash($_POST['sessionId'])) : '';

    if (!$runner || !$sessionId) {
        wp_send_json_error(['message' => esc_html__('Required Param Is Missing.', 'bdthemes-element-pack-lite')]);
    }

    $app = Plugin::$instance->app;
    if (!$app) {
        wp_send_json_error(['message' => esc_html__('Elementor app not available.', 'bdthemes-element-pack-lite')]);
    }

    try {
        // phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged -- Long-running kit import needs a larger budget than the default.
        @ini_set('max_execution_time', 60 * 5);

        $import_export_module = $app->get_component('import-export');
        $import = $import_export_module->import_kit_by_runner($sessionId, $runner);

        do_action('elementor/import-export/import-kit/runner/after-run', $import);
        wp_send_json_success($import);
    } catch (\Throwable $throwable) {
        wp_send_json_error(['message' => $throwable->getMessage()]);
    }
});
