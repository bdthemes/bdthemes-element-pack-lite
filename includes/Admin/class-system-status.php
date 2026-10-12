<?php
/**
 * REST-safe system status / requirements report.
 *
 * Mirrors element_pack_system_requirement() in admin-settings.php without
 * depending on the admin UI class.
 *
 * @package ElementPack\Admin
 */

namespace ElementPack\Admin;

use ElementPack\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class System_Status {

	/**
	 * Full read-only system report for GET /system.
	 *
	 * @return array<string,mixed>
	 */
	public static function get_report() {
		$environment = class_exists( '\ElementPack\Utils' )
			? Utils::get_environment_info()
			: [];

		$uploads      = wp_upload_dir();
		$upload_path  = isset( $uploads['basedir'] ) ? $uploads['basedir'] : '';
		$php_version  = phpversion();
		$max_exec     = ini_get( 'max_execution_time' );
		$memory_limit = ini_get( 'memory_limit' );
		$post_limit   = ini_get( 'post_max_size' );

		$checks = [
			self::check(
				'php_version',
				__( 'PHP Version', 'bdthemes-element-pack-lite' ),
				sprintf( __( 'Currently: %s', 'bdthemes-element-pack-lite' ), $php_version ),
				version_compare( $php_version, '7.4.0', '>=' ),
				__( 'Min: 7.4 recommended', 'bdthemes-element-pack-lite' )
			),
			self::check(
				'max_execution_time',
				__( 'Max execution time', 'bdthemes-element-pack-lite' ),
				sprintf( __( 'Currently: %s', 'bdthemes-element-pack-lite' ), $max_exec ),
				(int) $max_exec >= 90,
				__( 'Min: 90 recommended', 'bdthemes-element-pack-lite' )
			),
			self::check(
				'memory_limit',
				__( 'Memory limit', 'bdthemes-element-pack-lite' ),
				sprintf( __( 'Currently: %s', 'bdthemes-element-pack-lite' ), $memory_limit ),
				true,
				''
			),
			self::check(
				'post_max_size',
				__( 'Max post limit', 'bdthemes-element-pack-lite' ),
				sprintf( __( 'Currently: %s', 'bdthemes-element-pack-lite' ), $post_limit ),
				(int) $post_limit >= 32,
				__( 'Min: 32M recommended', 'bdthemes-element-pack-lite' )
			),
			self::check(
				'uploads_writable',
				__( 'Uploads folder writable', 'bdthemes-element-pack-lite' ),
				$upload_path ? $upload_path : '',
				'' !== $upload_path && is_writable( $upload_path ),
				''
			),
			self::check(
				'multisite',
				__( 'MultiSite', 'bdthemes-element-pack-lite' ),
				! empty( $environment['wp_multisite'] )
					? __( 'MultiSite enabled', 'bdthemes-element-pack-lite' )
					: __( 'Single site', 'bdthemes-element-pack-lite' ),
				true,
				''
			),
			self::check(
				'gzip',
				__( 'GZip enabled', 'bdthemes-element-pack-lite' ),
				! empty( $environment['gzip_enabled'] )
					? __( 'Enabled', 'bdthemes-element-pack-lite' )
					: __( 'Disabled', 'bdthemes-element-pack-lite' ),
				! empty( $environment['gzip_enabled'] ),
				''
			),
			self::check(
				'debug_mode',
				__( 'Debug mode', 'bdthemes-element-pack-lite' ),
				! empty( $environment['wp_debug_mode'] )
					? __( 'Currently turned on', 'bdthemes-element-pack-lite' )
					: __( 'Currently turned off', 'bdthemes-element-pack-lite' ),
				empty( $environment['wp_debug_mode'] ),
				__( 'Should be off in production', 'bdthemes-element-pack-lite' )
			),
		];

		$failed = count(
			array_filter(
				$checks,
				static function ( $row ) {
					return ! $row['pass'];
				}
			)
		);

		$widgets = class_exists( '\ElementPack\Admin\Dashboard_Stats' )
			? Dashboard_Stats::get_stats()
			: null;

		return [
			'plugin_version' => defined( 'BDTEP_VER' ) ? BDTEP_VER : '',
			'environment'    => self::public_environment( $environment ),
			'checks'         => $checks,
			'all_pass'       => 0 === $failed,
			'failed_count'   => $failed,
			'widgets'        => $widgets ? $widgets['widgets'] : null,
			'tracking_allowed' => $widgets ? $widgets['tracking_allowed'] : false,
			'note'           => __(
				'If you run multiple addons you may need to allocate additional memory for each one.',
				'bdthemes-element-pack-lite'
			),
		];
	}

	/**
	 * @param string $id
	 * @param string $label
	 * @param string $value
	 * @param bool   $pass
	 * @param string $recommendation
	 * @return array<string,mixed>
	 */
	private static function check( $id, $label, $value, $pass, $recommendation = '' ) {
		return [
			'id'              => $id,
			'label'           => $label,
			'value'           => $value,
			'pass'            => (bool) $pass,
			'recommendation'  => $recommendation,
		];
	}

	/**
	 * Strip server paths from environment payload.
	 *
	 * @param array<string,mixed> $environment
	 * @return array<string,mixed>
	 */
	private static function public_environment( array $environment ) {
		$keys = [
			'home_url',
			'site_url',
			'version',
			'wp_version',
			'wp_multisite',
			'wp_memory_limit',
			'wp_debug_mode',
			'language',
			'server_info',
			'php_version',
			'php_post_max_size',
			'php_max_execution_time',
			'gzip_enabled',
			'mbstring_enabled',
		];

		$out = [];
		foreach ( $keys as $key ) {
			if ( isset( $environment[ $key ] ) ) {
				$out[ $key ] = $environment[ $key ];
			}
		}

		return $out;
	}
}
