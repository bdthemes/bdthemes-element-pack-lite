<?php

namespace ElementPack\Modules\ThreedText\Normalizers;

use ElementPack\Modules\ThreedText\Atomic\Support;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings_Normalizer {
	public function normalize( $widget ) {
		$atomic_settings = $this->normalize_atomic_settings( $widget );

		if ( $atomic_settings ) {
			return $atomic_settings;
		}

		return $this->normalize_legacy_settings( $widget );
	}

	private function normalize_atomic_settings( $widget ) {
		$candidates = [];

		if ( method_exists( $widget, 'get_data' ) ) {
			try {
				$raw_settings = $widget->get_data( 'settings' );
			} catch ( \Throwable $e ) {
				$raw_settings = null;
			}

			if ( is_array( $raw_settings ) ) {
				$candidates[] = $raw_settings;
			}
		}

		if ( method_exists( $widget, 'get_atomic_setting' ) ) {
			$candidates[] = [
				Support::ACTIVE_KEY => $this->get_atomic_setting( $widget, Support::ACTIVE_KEY ),
				Support::DEPTH_KEY => $this->get_atomic_setting( $widget, Support::DEPTH_KEY ),
				Support::LAYERS_KEY => $this->get_atomic_setting( $widget, Support::LAYERS_KEY ),
				Support::DEPTH_COLOR_KEY => $this->get_atomic_setting( $widget, Support::DEPTH_COLOR_KEY ),
				Support::PERSPECTIVE_KEY => $this->get_atomic_setting( $widget, Support::PERSPECTIVE_KEY ),
				Support::FADE_KEY => $this->get_atomic_setting( $widget, Support::FADE_KEY ),
				Support::EVENT_KEY => $this->get_atomic_setting( $widget, Support::EVENT_KEY ),
				Support::EVENT_ROTATION_KEY => $this->get_atomic_setting( $widget, Support::EVENT_ROTATION_KEY ),
				Support::EVENT_DIRECTION_KEY => $this->get_atomic_setting( $widget, Support::EVENT_DIRECTION_KEY ),
			];
		}

		foreach ( $candidates as $candidate ) {
			$normalized = $this->normalize_settings_array( $candidate, true );

			if ( $normalized ) {
				return $normalized;
			}
		}

		return null;
	}

	private function get_atomic_setting( $widget, string $key ) {
		try {
			return $widget->get_atomic_setting( $key );
		} catch ( \Throwable $e ) {
			return null;
		}
	}

	private function normalize_legacy_settings( $widget ) {
		if ( ! method_exists( $widget, 'get_settings_for_display' ) ) {
			return null;
		}

		$settings = [
			Support::ACTIVE_KEY => $widget->get_settings_for_display( Support::ACTIVE_KEY ),
			Support::DEPTH_KEY => $widget->get_settings_for_display( Support::DEPTH_KEY ),
			Support::LAYERS_KEY => $widget->get_settings_for_display( Support::LAYERS_KEY ),
			Support::DEPTH_COLOR_KEY => $widget->get_settings_for_display( Support::DEPTH_COLOR_KEY ),
			Support::PERSPECTIVE_KEY => $widget->get_settings_for_display( Support::PERSPECTIVE_KEY ),
			Support::FADE_KEY => $widget->get_settings_for_display( Support::FADE_KEY ),
			Support::EVENT_KEY => $widget->get_settings_for_display( Support::EVENT_KEY ),
			Support::EVENT_ROTATION_KEY => $widget->get_settings_for_display( Support::EVENT_ROTATION_KEY ),
			Support::EVENT_DIRECTION_KEY => $widget->get_settings_for_display( Support::EVENT_DIRECTION_KEY ),
		];

		return $this->normalize_settings_array( $settings, false );
	}

	private function normalize_settings_array( array $settings, bool $unwrap_transformable ) {
		$active = $this->extract_value( $settings[ Support::ACTIVE_KEY ] ?? null, $unwrap_transformable );

		if ( ! $this->is_truthy( $active ) ) {
			return null;
		}

		return [
			'active' => 'yes',
			'depth' => [ 'size' => $this->float_value( $settings[ Support::DEPTH_KEY ] ?? 30, $unwrap_transformable, 30 ) ],
			'layers' => $this->int_value( $settings[ Support::LAYERS_KEY ] ?? 8, $unwrap_transformable, 8 ),
			'depth_color' => $this->string_value( $settings[ Support::DEPTH_COLOR_KEY ] ?? '', $unwrap_transformable, '' ),
			'perspective' => [ 'size' => $this->float_value( $settings[ Support::PERSPECTIVE_KEY ] ?? 500, $unwrap_transformable, 500 ) ],
			'fade' => $this->bool_to_yes_no( $settings[ Support::FADE_KEY ] ?? true, $unwrap_transformable ),
			'event' => $this->string_value( $settings[ Support::EVENT_KEY ] ?? 'none', $unwrap_transformable, 'none' ),
			'event_rotation' => [ 'size' => $this->float_value( $settings[ Support::EVENT_ROTATION_KEY ] ?? 35, $unwrap_transformable, 35 ) ],
			'event_direction' => $this->string_value( $settings[ Support::EVENT_DIRECTION_KEY ] ?? 'default', $unwrap_transformable, 'default' ),
		];
	}

	private function extract_value( $value, bool $unwrap_transformable ) {
		if ( $unwrap_transformable && is_array( $value ) && array_key_exists( 'value', $value ) ) {
			return $this->extract_value( $value['value'], $unwrap_transformable );
		}

		return $value;
	}

	private function is_truthy( $value ): bool {
		return true === $value || 'yes' === $value || 1 === $value || '1' === $value;
	}

	private function float_value( $value, bool $unwrap_transformable, float $fallback ): float {
		$value = $this->extract_value( $value, $unwrap_transformable );

		if ( is_array( $value ) && isset( $value['size'] ) ) {
			$value = $value['size'];
		}

		return is_numeric( $value ) ? (float) $value : $fallback;
	}

	private function int_value( $value, bool $unwrap_transformable, int $fallback ): int {
		$value = $this->extract_value( $value, $unwrap_transformable );

		return is_numeric( $value ) ? (int) $value : $fallback;
	}

	private function string_value( $value, bool $unwrap_transformable, string $fallback ): string {
		$value = $this->extract_value( $value, $unwrap_transformable );

		return is_string( $value ) && '' !== $value ? $value : $fallback;
	}

	private function bool_to_yes_no( $value, bool $unwrap_transformable ): string {
		$value = $this->extract_value( $value, $unwrap_transformable );

		return $this->is_truthy( $value ) ? 'yes' : '';
	}
}
