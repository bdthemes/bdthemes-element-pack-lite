<?php
/**
 * Dashboard statistics service.
 *
 * Single, REST-safe source of truth for the admin dashboard widget counts.
 * It deliberately does NOT depend on the admin-only ElementPack_Admin_Settings
 * class (admin/admin-settings.php is only loaded on is_admin() requests). All
 * data is sourced from the canonical ModuleService and the Elementor Usage
 * module, so the same numbers are available to REST requests and the React
 * dashboard.
 *
 * @package ElementPack\Admin
 */

namespace ElementPack\Admin;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

class Dashboard_Stats {

    /**
     * Build the full dashboard statistics payload.
     *
     * @return array
     */
    public static function get_stats() {
        $fields = self::get_settings_fields();

        $core        = self::tier_stats( $fields['element_pack_active_modules'] ?? [], 'element_pack_active_modules' );
        $third_party = self::tier_stats( $fields['element_pack_third_party_widget'] ?? [], 'element_pack_third_party_widget' );
        $extensions  = self::tier_stats( $fields['element_pack_elementor_extend'] ?? [], 'element_pack_elementor_extend' );
        $features    = self::feature_stats( $fields['element_pack_other_settings'] ?? [] );

// Usage data (used vs. unused) — only meaningful when Elementor usage
        // tracking is allowed; mirrors the legacy dashboard behaviour.
        $usage       = self::get_usage_map();
        $core_names  = self::get_widget_handles( 'core' );
        $third_names = self::get_widget_handles( 'third_party' );
        $all_names   = self::get_widget_handles( 'all' );

        $core_used  = count( self::get_used_widgets_map( 'core' ) );
        $third_used = count( self::get_used_widgets_map( 'third_party' ) );
        $all_used   = count( self::get_used_widgets_map( 'all' ) );

        $unused_active = self::unused_active_count( $fields['element_pack_active_modules'] ?? [], 'element_pack_active_modules', $usage )
            + self::unused_active_count( $fields['element_pack_third_party_widget'] ?? [], 'element_pack_third_party_widget', $usage );

        return [
            'tracking_allowed' => self::is_usage_tracking_allowed(),
            'is_pro'           => function_exists( 'element_pack_pro_activated' ) ? (bool) element_pack_pro_activated() : false,
            'asset_optimization_enabled' => function_exists( 'element_pack_is_asset_optimization_enabled' ) ? (bool) element_pack_is_asset_optimization_enabled() : false,
            'widgets'          => [
                'all'         => [
                    'total'         => count( $all_names ),
                    'used'          => $all_used,
                    'unused'        => max( 0, count( $all_names ) - $all_used ),
                    // Enabled yet unused: what disabling unused widgets would switch off.
                    'unused_active' => $unused_active,
                ],
                'core'        => [
                    'total'  => $core['total'],
                    'active' => $core['active'],
                    'used'   => $core_used,
                    'unused' => max( 0, count( $core_names ) - $core_used ),
                ],
                'third_party' => [
                    'total'  => $third_party['total'],
                    'active' => $third_party['active'],
                    'used'   => $third_used,
                    'unused' => max( 0, count( $third_names ) - $third_used ),
                ],
                'extensions'  => [
                    'total'  => $extensions['total'],
                    'active' => $extensions['active'],
                ],
            ],
            'features'         => [
                'total'  => $features['total'],
                'active' => $features['active'],
            ],
            'active_totals'    => [
                'core'        => $core['active'],
                'third_party' => $third_party['active'],
                'extensions'  => $extensions['active'],
                'total'       => $core['active'] + $third_party['active'] + $extensions['active'],
            ],
        ];
    }

    /**
     * Totals for the Features screen — the on/off switches only, so the API
     * keys and select fields sharing that option are not counted, nor the
     * Asset Manager switch, which lives on the Performance screen.
     *
     * @param array $items Settings fields for `element_pack_other_settings`.
     * @return array{total:int, active:int}
     */
    private static function feature_stats( $items ) {
        $options = ModuleService::get_modules_option( 'element_pack_other_settings' );
        $total   = 0;
        $active  = 0;

        foreach ( $items as $item ) {

            if ( empty( $item['name'] ) || 'checkbox' !== ( $item['type'] ?? '' ) || 'asset-manager' === $item['name'] ) {
                continue;
            }

            $total++;

            if ( ModuleService::is_module_active( $item['name'], $options ) ) {
                $active++;
            }
        }

        return [
            'total'  => $total,
            'active' => $active,
        ];
    }

    /**
     * Enabled modules of a tier whose widgets no Elementor content uses.
     *
     * @param array  $items       Settings fields for the tier.
     * @param string $option_name Option key holding the on/off map.
     * @param array  $usage       Usage map from get_usage_map().
     * @return int
     */
    private static function unused_active_count( $items, $option_name, $usage ) {
        if ( ! self::is_usage_tracking_allowed() ) {
            return 0;
        }

        $options = ModuleService::get_modules_option( $option_name );
        $count   = 0;

        foreach ( $items as $item ) {

            if ( empty( $item['name'] ) || ! ModuleService::is_module_active( $item['name'], $options ) ) {
                continue;
            }

            $used = false;

            foreach ( ModuleService::get_module_widget_names( $item['name'] ) as $handle ) {
                if ( isset( $usage[ $handle ] ) ) {
                    $used = true;
                    break;
                }
            }

            if ( ! $used ) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Per-tier totals and active counts honouring per-module defaults.
     *
     * @param array  $items       Settings fields for the tier.
     * @param string $option_name Option key holding the on/off map.
     * @return array{total:int, active:int}
     */
    private static function tier_stats( $items, $option_name ) {
        $options = ModuleService::get_modules_option( $option_name );
        $total   = 0;
        $active  = 0;

        foreach ( $items as $item ) {

            if ( empty( $item['name'] ) ) {
                continue;
            }

            $total++;

            if ( ModuleService::is_module_active( $item['name'], $options ) ) {
                $active++;
            }

        }

        return [
            'total'  => $total,
            'active' => $active,
        ];
    }

    /**
     * Map tier module names to their Elementor widget handles (usually
     * bdt-<slug>; see ModuleService::get_module_widget_names()).
     *
     * @param array $items
     * @return string[]
     */
    private static function tier_widget_handles( $items ) {
        $handles = [];

        foreach ( $items as $item ) {

            if ( empty( $item['name'] ) ) {
                continue;
            }

            foreach ( ModuleService::get_module_widget_names( $item['name'] ) as $handle ) {
                $handles[] = $handle;
            }
        }

        return $handles;
    }

    /**
     * Elementor widget handles for a dashboard tier.
     *
     * @param string $tier `all`, `core`, or `third_party`.
     * @return string[]
     */
    public static function get_widget_handles( $tier = 'all' ) {
        $fields = self::get_settings_fields();

        if ( 'core' === $tier ) {
            return self::tier_widget_handles( $fields['element_pack_active_modules'] ?? [] );
        }

        if ( 'third_party' === $tier ) {
            return self::tier_widget_handles( $fields['element_pack_third_party_widget'] ?? [] );
        }

        return array_merge(
            self::tier_widget_handles( $fields['element_pack_active_modules'] ?? [] ),
            self::tier_widget_handles( $fields['element_pack_third_party_widget'] ?? [] )
        );
    }

    /**
     * Used widgets map (handle => usage count) for a tier.
     *
     * @param string $tier `all`, `core`, or `third_party`.
     * @return array<string,int>
     */
    public static function get_used_widgets_map( $tier = 'all' ) {
        if ( ! self::is_usage_tracking_allowed() ) {
            return [];
        }

        $handles = array_fill_keys( self::get_widget_handles( $tier ), true );
        $usage   = self::get_usage_map();
        $used    = [];

        foreach ( $usage as $handle => $count ) {
            if ( ! isset( $handles[ $handle ] ) ) {
                continue;
            }

            $used[ $handle ] = isset( $used[ $handle ] ) ? $used[ $handle ] + $count : $count;
        }

        return $used;
    }

    /**
     * Unused widget handles for a tier.
     *
     * @param string $tier `all`, `core`, or `third_party`.
     * @return string[]
     */
    public static function get_unused_widget_handles( $tier = 'all' ) {
        $handles = self::get_widget_handles( $tier );
        $used    = self::get_used_widgets_map( $tier );

        return array_values( array_diff( $handles, array_keys( $used ) ) );
    }

    /**
     * Flattened Elementor usage map: widget handle => total count.
     *
     * Public so REST controllers (e.g. Modules_Controller) can flag individual
     * modules as used/unused without re-implementing the lookup.
     *
     * @return array
     */
    public static function get_usage_map() {
        $map = [];

        if ( !self::is_usage_tracking_allowed() ) {
            return $map;
        }

        if ( !class_exists( '\Elementor\Modules\Usage\Module' ) ) {
            return $map;
        }

        $module = \Elementor\Modules\Usage\Module::instance();

        $old_error_level = error_reporting();
        error_reporting( E_ALL & ~E_WARNING );
        $elements = $module->get_formatted_usage( 'raw' );
        error_reporting( $old_error_level );

        if ( is_array( $elements ) || is_object( $elements ) ) {

            foreach ( $elements as $data ) {

                if ( empty( $data['elements'] ) ) {
                    continue;
                }

                foreach ( $data['elements'] as $element => $count ) {
                    $map[$element] = isset( $map[$element] ) ? $map[$element] + $count : $count;
                }

            }

        }

        return $map;
    }

    /**
     * Whether Elementor usage tracking is enabled (gate for used/unused stats).
     *
     * @return bool
     */
    public static function is_usage_tracking_allowed() {
        return class_exists( '\Elementor\Tracker' ) && \Elementor\Tracker::is_allow_track();
    }

    /**
     * The canonical three-tier settings fields array from ModuleService.
     *
     * @return array
     */
    private static function get_settings_fields() {

        if ( !class_exists( '\ElementPack\Admin\ModuleService' ) ) {
            return [];
        }

        return ModuleService::get_widget_settings(
            function ( $settings ) {
                return isset( $settings['settings_fields'] ) && is_array( $settings['settings_fields'] )
                ? $settings['settings_fields']
                : [];
            }
        );
    }

}
