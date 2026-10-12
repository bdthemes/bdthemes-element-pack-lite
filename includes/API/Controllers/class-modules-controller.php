<?php
/**
 * Modules (widgets / extensions / third-party) endpoint.
 *
 * GET  /element-pack/v1/modules         List all modules per tier with state.
 * POST /element-pack/v1/modules         Bulk-save the on/off map for a tier.
 *
 * Reads the canonical labelled fields from ModuleService and persists through
 * the same option keys + update_option path the legacy AJAX save uses, so both
 * UIs stay in perfect sync. The `updated_option` hook in element-pack-filters
 * flushes ModuleService's per-request cache automatically.
 *
 * @package ElementPack\API\Controllers
 */

namespace ElementPack\API\Controllers;

use ElementPack\Admin\Dashboard_Stats;
use ElementPack\Admin\ModuleService;
use ElementPack\API\REST_Controller;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

class Modules_Controller extends REST_Controller {

    protected $rest_base = 'modules';

    /**
     * Tier option keys this controller is allowed to read/write.
     *
     * @var array<string,string>
     */
    private $tiers = [
        'core'        => 'element_pack_active_modules',
        'third_party' => 'element_pack_third_party_widget',
        'extensions'  => 'element_pack_elementor_extend',
    ];

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
                    'callback'            => [$this, 'get_modules'],
                    'permission_callback' => [$this, 'permissions_check'],
                ],
                [
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'save_modules'],
                    'permission_callback' => [$this, 'permissions_check'],
                    'args'                => [
                        'tier'    => [
                            'type'              => 'string',
                            'required'          => true,
                            'enum'              => array_keys( $this->tiers ),
                            'sanitize_callback' => 'sanitize_text_field',
                        ],
                        'modules' => [
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
     * @return \WP_REST_Response|\WP_Error
     */
    public function get_modules( $request ) {

        if ( !class_exists( '\ElementPack\Admin\ModuleService' ) ) {
            return $this->error( 'element_pack_modules_unavailable', __( 'Module data is unavailable.', 'bdthemes-element-pack-lite' ), 500 );
        }

        $fields = ModuleService::get_widget_settings(
            function ( $settings ) {
                return isset( $settings['settings_fields'] ) && is_array( $settings['settings_fields'] )
                ? $settings['settings_fields']
                : [];
            }
        );

        $is_pro = function_exists( 'element_pack_pro_activated' ) ? (bool) element_pack_pro_activated() : false;

// Usage data lets the UI offer a Used/Unused filter. Extensions are not
        // Elementor widgets, so usage only applies to core + third-party tiers.
        $usage            = class_exists( '\ElementPack\Admin\Dashboard_Stats' ) ? Dashboard_Stats::get_usage_map() : [];
        $tracking_allowed = class_exists( '\ElementPack\Admin\Dashboard_Stats' ) ? Dashboard_Stats::is_usage_tracking_allowed() : false;

        $result = [];

        foreach ( $this->tiers as $tier => $option_name ) {
            $options        = ModuleService::get_modules_option( $option_name );
            $items          = isset( $fields[$option_name] ) && is_array( $fields[$option_name] ) ? $fields[$option_name] : [];
            $tier_has_usage = 'extensions' !== $tier;
            $list           = [];

            foreach ( $items as $item ) {

                if ( empty( $item['name'] ) ) {
                    continue;
                }

                $usage_count = 0;
                $in_use      = false;

                foreach ( ModuleService::get_module_widget_names( $item['name'] ) as $handle ) {
                    if ( isset( $usage[$handle] ) ) {
                        $in_use       = true;
                        $usage_count += (int) $usage[$handle];
                    }
                }

                // Third-party widgets only load at runtime when their
                // dependency plugin is active (modules-manager) — surface that
                // so the UI can disable the toggle and explain why.
                $plugin_path   = isset( $item['plugin_path'] ) ? $item['plugin_path'] : '';
                $plugin_active = true;

                if ( '' !== $plugin_path ) {
                    if ( !function_exists( 'is_plugin_active' ) ) {
                        include_once ABSPATH . 'wp-admin/includes/plugin.php';
                    }
                    $plugin_active = is_plugin_active( $plugin_path );
                }

                $list[] = [
                    'name'            => $item['name'],
                    'label'           => isset( $item['label'] ) ? $item['label'] : ucwords( str_replace( '-', ' ', $item['name'] ) ),
                    'widget_type'     => isset( $item['widget_type'] ) ? $item['widget_type'] : 'free',
                    'content_type'    => isset( $item['content_type'] ) ? $item['content_type'] : 'others',
                    'demo_url'        => isset( $item['demo_url'] ) ? esc_url_raw( $item['demo_url'] ) : '',
                    'video_url'       => isset( $item['video_url'] ) ? esc_url_raw( $item['video_url'] ) : '',
                    'enabled'         => (bool) ModuleService::is_module_active( $item['name'], $options ),
                    'is_locked'       => ( 'pro' === ( $item['widget_type'] ?? 'free' ) ) && !$is_pro,
                    'plugin_active'   => (bool) $plugin_active,
                    'requires_plugin' => '' !== $plugin_path ? self::pretty_plugin_name( $plugin_path ) : '',
                    'plugin_slug'     => '' !== $plugin_path ? dirname( $plugin_path ) : '',
                    // Premium dependencies (BdThemes zips, ACF Pro, Gravity
                    // Forms, CodeCanyon…) carry their official download URL in
                    // the settings fields' 'paid' key — wp.org search would
                    // find nothing for them.
                    'install_url'     => !empty( $item['paid'] ) ? esc_url_raw( $item['paid'] ) : '',
                    'has_usage'       => $tier_has_usage,
                    'used'            => $tier_has_usage && $in_use,
                    'usage_count'     => $tier_has_usage ? $usage_count : 0,
                ];
            }

            $result[$tier] = [
                'option_name' => $option_name,
                'modules'     => $list,
                'total'       => count( $list ),
                'active'      => count(
                    array_filter(
                        $list,
                        static function ( $m ) {
                            return $m['enabled'];
                        }
                    )
                ),
            ];
        }

        return $this->success(
            [
                'is_pro'           => $is_pro,
                'tracking_allowed' => $tracking_allowed,
                'tiers'            => $result,
            ]
        );
    }

    /**
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response|\WP_Error
     */
    public function save_modules( $request ) {
        $tier    = $request->get_param( 'tier' );
        $modules = $request->get_param( 'modules' );

        if ( !isset( $this->tiers[$tier] ) ) {
            return $this->error( 'element_pack_invalid_tier', __( 'Invalid module tier.', 'bdthemes-element-pack-lite' ) );
        }

        if ( !is_array( $modules ) ) {
            return $this->error( 'element_pack_invalid_payload', __( 'Invalid modules payload.', 'bdthemes-element-pack-lite' ) );
        }

        $option_name = $this->tiers[$tier];

// Build a clean {slug => 'on'|'off'} map. Only known module slugs from this
        // tier are accepted; everything else is sanitised to 'on'/'off'.
        $valid_slugs = $this->get_tier_slugs( $option_name );
        $sanitised   = [];

        foreach ( $modules as $slug => $value ) {
            $slug = sanitize_key( $slug );

            if ( !in_array( $slug, $valid_slugs, true ) ) {
                continue;
            }

            $sanitised[$slug] = ( 'on' === $value || true === $value || 1 === $value || '1' === $value ) ? 'on' : 'off';
        }

        update_option( $option_name, $sanitised );

// Mirror the legacy save: regenerate optimized assets when the asset

// optimizer is active so toggling here matches toggling in the old UI.
        if ( function_exists( 'element_pack_is_asset_optimization_enabled' ) && element_pack_is_asset_optimization_enabled() ) {
            if ( class_exists( '\ElementPack\Admin\AssetMinifier\Asset_Minifier' ) ) {
                $optimizer = new \ElementPack\Admin\AssetMinifier\Asset_Minifier();
                if ( method_exists( $optimizer, 'minifyCss' ) ) {
                    $optimizer->minifyCss();
                }

                if ( method_exists( $optimizer, 'minifyJs' ) ) {
                    $optimizer->minifyJs();
                }

                update_option( 'element-pack-minified-asset-manager-version', time() );
            }

        } else {
            delete_option( 'element-pack-minified-asset-manager-version' );
        }

        return $this->success(
            [
                'tier'    => $tier,
                'saved'   => true,
                'modules' => $sanitised,
                'active'  => count(
                    array_filter(
                        $sanitised,
                        static function ( $v ) {
                            return 'on' === $v;
                        }
                    )
                ),
            ]
        );
    }

    /**
     * Human-readable name for a dependency plugin path
     * ('woocommerce/woocommerce.php' → 'WooCommerce').
     *
     * @param string $plugin_path
     * @return string
     */
    private static function pretty_plugin_name( $plugin_path ) {
        $known = [
            'woocommerce'                => 'WooCommerce',
            'bbpress'                    => 'bbPress',
            'buddypress'                 => 'BuddyPress',
            'advanced-custom-fields'     => 'ACF',
            'advanced-custom-fields-pro' => 'ACF Pro',
        ];

        $slug = dirname( (string) $plugin_path );

        if ( isset( $known[$slug] ) ) {
            return $known[$slug];
        }

        return ucwords( str_replace( [ '-', '_' ], ' ', $slug ) );
    }

    /**
     * Valid module slugs for a tier (from the canonical settings fields).
     *
     * @param string $option_name
     * @return string[]
     */
    private function get_tier_slugs( $option_name ) {
        $fields = ModuleService::get_widget_settings(
            function ( $settings ) {
                return isset( $settings['settings_fields'] ) ? $settings['settings_fields'] : [];
            }
        );

        $items = isset( $fields[$option_name] ) && is_array( $fields[$option_name] ) ? $fields[$option_name] : [];
        $slugs = [];
        foreach ( $items as $item ) {
            if ( !empty( $item['name'] ) ) {
                $slugs[] = sanitize_key( $item['name'] );
            }

        }

        return $slugs;
    }

}
