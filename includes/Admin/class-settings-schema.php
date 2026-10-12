<?php
/**
 * Settings schema + persistence for the React dashboard.
 *
 * Exposes the Features, Integrations (API) and Performance settings that
 * `ModuleService::get_widget_settings()` defines, and saves them through the same
 * option keys the rest of the plugin reads.
 *
 * @package ElementPack\Admin
 */

namespace ElementPack\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings_Schema {

	public const OPTION_OTHER = 'element_pack_other_settings';
	public const OPTION_API   = 'element_pack_api_settings';

	/**
	 * Full payload for GET /settings.
	 *
	 * @return array<string,mixed>
	 */
	public static function get_payload() {
		$is_pro = function_exists( 'element_pack_pro_activated' ) ? (bool) element_pack_pro_activated() : false;

		$special_fields = self::normalize_fields(
			self::get_raw_fields( self::OPTION_OTHER ),
			$is_pro,
			[ 'asset-manager' ]
		);

		$perf_fields = self::normalize_fields(
			self::get_raw_fields( self::OPTION_OTHER ),
			$is_pro,
			null,
			[ 'asset-manager' ]
		);

		$api_fields = self::normalize_fields(
			self::get_raw_fields( self::OPTION_API ),
			$is_pro
		);

		return [
			'is_pro'                     => $is_pro,
			'asset_optimization_enabled' => function_exists( 'element_pack_is_asset_optimization_enabled' )
				? (bool) element_pack_is_asset_optimization_enabled()
				: false,
			'minified_version'           => get_option( 'element-pack-minified-asset-manager-version', '' ),
			'values'                     => self::get_all_values(),
			'sections'                   => [
				'special_features' => [
					'option' => self::OPTION_OTHER,
					'fields' => $special_fields,
				],
				'api'              => [
					'option' => self::OPTION_API,
					'fields' => $api_fields,
				],
				'performance'      => [
					'option' => self::OPTION_OTHER,
					'fields' => $perf_fields,
				],
			],
		];
	}

	/**
	 * Current raw values for every option the dashboard may edit.
	 *
	 * @return array<string,mixed>
	 */
	public static function get_all_values() {
		$values = [
			self::OPTION_OTHER => get_option( self::OPTION_OTHER, [] ),
			self::OPTION_API   => get_option( self::OPTION_API, [] ),
		];

		foreach ( $values as $key => $value ) {
			if ( ! is_array( $value ) ) {
				$values[ $key ] = [];
			}
		}

		return $values;
	}

	/**
	 * Persist a partial update. Unknown keys are ignored.
	 *
	 * @param array<string,mixed> $incoming
	 * @return array<string,mixed> The values that were saved, keyed by option.
	 */
	public static function save_partial( array $incoming ) {
		$saved = [];

		foreach ( $incoming as $key => $value ) {
			if ( ! self::is_allowed_key( $key ) ) {
				continue;
			}

			$clean = self::sanitize_option( $key, $value );
			update_option( $key, $clean );
			$saved[ $key ] = $clean;
		}

		if ( ! empty( $saved ) ) {
			self::maybe_regenerate_assets();
		}

		return $saved;
	}

	public static function is_allowed_key( $key ) {
		return in_array( $key, self::get_allowed_keys(), true );
	}

	/**
	 * @return string[]
	 */
	public static function get_allowed_keys() {
		return [ self::OPTION_OTHER, self::OPTION_API ];
	}

	/**
	 * Valid field slugs for an option, from the canonical settings fields.
	 *
	 * @param string $option_name
	 * @return string[]
	 */
	public static function get_valid_slugs( $option_name ) {
		$fields = self::get_raw_fields( $option_name );
		$slugs  = [];

		foreach ( $fields as $item ) {
			if ( empty( $item['name'] ) ) {
				continue;
			}

			$type = isset( $item['type'] ) ? $item['type'] : 'text';

			if ( in_array( $type, [ 'start_group', 'end_group', 'html', 'separator' ], true ) ) {
				continue;
			}

			$slugs[] = sanitize_key( $item['name'] );
		}

		return $slugs;
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 * @return mixed
	 */
	public static function sanitize_option( $key, $value ) {
		return self::sanitize_option_array( $key, $value );
	}

	/**
	 * Merge posted field values into the stored option, field by field, so a
	 * partial payload never wipes the fields it does not mention.
	 */
	private static function sanitize_option_array( $option_name, $value ) {
		$existing = get_option( $option_name, [] );
		$existing = is_array( $existing ) ? $existing : [];

		if ( ! is_array( $value ) ) {
			return $existing;
		}

		$valid_slugs = self::get_valid_slugs( $option_name );
		$field_meta  = self::index_fields_by_name( self::get_raw_fields( $option_name ) );

		foreach ( $value as $slug => $raw ) {
			$slug = sanitize_key( $slug );

			if ( ! in_array( $slug, $valid_slugs, true ) ) {
				continue;
			}

			$type = isset( $field_meta[ $slug ]['type'] ) ? $field_meta[ $slug ]['type'] : 'text';

			if ( 'checkbox' === $type ) {
				$existing[ $slug ] = ( 'on' === $raw || true === $raw || 1 === $raw || '1' === $raw ) ? 'on' : 'off';
				continue;
			}

			if ( 'number' === $type ) {
				$existing[ $slug ] = is_numeric( $raw ) ? (string) absint( $raw ) : '0';
				continue;
			}

			if ( 'textarea' === $type ) {
				$existing[ $slug ] = is_string( $raw ) ? sanitize_textarea_field( wp_unslash( $raw ) ) : '';
				continue;
			}

			$existing[ $slug ] = is_string( $raw ) ? sanitize_text_field( wp_unslash( $raw ) ) : sanitize_text_field( (string) $raw );
		}

		return $existing;
	}

	/**
	 * Rebuild (or drop) the Asset Manager's all-widgets bundle after a change.
	 */
	public static function maybe_regenerate_assets() {
		if ( ! function_exists( 'element_pack_is_asset_optimization_enabled' ) || ! element_pack_is_asset_optimization_enabled() ) {
			delete_option( 'element-pack-minified-asset-manager-version' );
			return;
		}

		if ( ! class_exists( '\ElementPack\Admin\AssetMinifier\Asset_Minifier' ) ) {
			return;
		}

		$optimizer = new \ElementPack\Admin\AssetMinifier\Asset_Minifier();

		if ( method_exists( $optimizer, 'minifyCss' ) ) {
			$optimizer->minifyCss();
		}

		if ( method_exists( $optimizer, 'minifyJs' ) ) {
			$optimizer->minifyJs();
		}

		update_option( 'element-pack-minified-asset-manager-version', time() );
	}

	/**
	 * @param string $option_name
	 * @return array<int,array<string,mixed>>
	 */
	private static function get_raw_fields( $option_name ) {
		if ( ! class_exists( '\ElementPack\Admin\ModuleService' ) ) {
			return [];
		}

		$fields = ModuleService::get_widget_settings(
			static function ( $settings ) {
				return isset( $settings['settings_fields'] ) && is_array( $settings['settings_fields'] )
					? $settings['settings_fields']
					: [];
			}
		);

		return isset( $fields[ $option_name ] ) && is_array( $fields[ $option_name ] )
			? $fields[ $option_name ]
			: [];
	}

	/**
	 * Flatten a settings field list into the shape the SPA renders.
	 *
	 * @param array<int,array<string,mixed>> $items
	 * @param bool                           $is_pro
	 * @param string[]|null                  $exclude_names Skip these fields.
	 * @param string[]|null                  $only_names    Keep only these fields.
	 * @return array<int,array<string,mixed>>
	 */
	private static function normalize_fields( array $items, $is_pro, $exclude_names = null, $only_names = null ) {
		$out           = [];
		$group_depth   = 0;
		$skip_group    = false;
		$parent_switch = null;

		foreach ( $items as $item ) {
			if ( empty( $item['name'] ) && empty( $item['type'] ) ) {
				continue;
			}

			$type = isset( $item['type'] ) ? $item['type'] : 'text';
			$name = isset( $item['name'] ) ? $item['name'] : '';

			if ( 'start_group' === $type ) {
				++$group_depth;
				$skip_group = false;
			}

			if ( 'end_group' === $type ) {
				if ( ! $skip_group ) {
					$out[] = self::normalize_field( $item, $is_pro, $parent_switch );
				}
				--$group_depth;
				$skip_group = false;
				continue;
			}

			if ( $exclude_names && in_array( $name, $exclude_names, true ) ) {
				if ( $group_depth > 0 ) {
					$skip_group = true;
				}
				continue;
			}

			if ( $only_names && $name && ! in_array( $name, $only_names, true ) ) {
				if ( 'start_group' === $type ) {
					$skip_group = true;
				}
				continue;
			}

			if ( $skip_group && 'start_group' !== $type ) {
				continue;
			}

			if ( ! empty( $item['ep_parent_switcher'] ) ) {
				$parent_switch = $name;
			}

			$out[] = self::normalize_field( $item, $is_pro, $parent_switch );
		}

		return $out;
	}

	/**
	 * @param array<string,mixed> $item
	 * @param bool                $is_pro
	 * @param string|null         $depends_on
	 * @return array<string,mixed>
	 */
	private static function normalize_field( array $item, $is_pro, $depends_on = null ) {
		$widget_type = isset( $item['widget_type'] ) ? $item['widget_type'] : 'free';
		$type        = isset( $item['type'] ) ? $item['type'] : 'text';

		$field = [
			'name'        => isset( $item['name'] ) ? sanitize_key( $item['name'] ) : '',
			'label'       => isset( $item['label'] ) ? wp_strip_all_tags( $item['label'] ) : '',
			'type'        => $type,
			'default'     => isset( $item['default'] ) ? $item['default'] : '',
			'desc'        => isset( $item['desc'] ) ? wp_kses_post( $item['desc'] ) : '',
			'tooltip'     => isset( $item['tooltip'] ) ? wp_strip_all_tags( $item['tooltip'] ) : '',
			'placeholder' => isset( $item['placeholder'] ) ? sanitize_text_field( $item['placeholder'] ) : '',
			'widget_type' => $widget_type,
			'demo_url'    => isset( $item['demo_url'] ) ? esc_url_raw( $item['demo_url'] ) : '',
			'video_url'   => isset( $item['video_url'] ) ? esc_url_raw( $item['video_url'] ) : '',
			'is_locked'   => ( 'pro' === $widget_type ) && ! $is_pro,
			'depends_on'  => $depends_on,
			'is_parent'   => ! empty( $item['ep_parent_switcher'] ),
		];

		if ( isset( $item['options'] ) && is_array( $item['options'] ) ) {
			$field['options'] = [];
			foreach ( $item['options'] as $opt_key => $opt_label ) {
				$field['options'][ sanitize_key( (string) $opt_key ) ] = wp_strip_all_tags( (string) $opt_label );
			}
		}

		return $field;
	}

	/**
	 * @param array<int,array<string,mixed>> $items
	 * @return array<string,array<string,mixed>>
	 */
	private static function index_fields_by_name( array $items ) {
		$index = [];

		foreach ( $items as $item ) {
			if ( ! empty( $item['name'] ) ) {
				$index[ sanitize_key( $item['name'] ) ] = $item;
			}
		}

		return $index;
	}
}
