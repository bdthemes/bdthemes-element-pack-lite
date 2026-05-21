<?php

namespace ElementPack\Modules\WrapperLink\Normalizers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Link_Settings_Normalizer {
	private const LEGACY_LINK_CONTROL_KEY = 'element_pack_wrapper_link';
	private const ATOMIC_LINK_PROP_KEY = 'element_pack_wrapper_link_url';

	public function normalize( $widget ) {
		foreach ( $this->get_atomic_link_candidates( $widget ) as $atomic_link ) {
			$normalized = $this->normalize_atomic_link_array( $atomic_link );

			if ( $normalized ) {
				return $normalized;
			}
		}

		return $this->normalize_legacy_link_settings( $widget );
	}

	private function get_atomic_link_candidates( $widget ): array {
		$candidates = [];

		if ( method_exists( $widget, 'get_atomic_setting' ) ) {
			$candidates[] = $widget->get_atomic_setting( self::ATOMIC_LINK_PROP_KEY );
		}

		if ( method_exists( $widget, 'get_settings_for_display' ) ) {
			$candidates[] = $widget->get_settings_for_display( self::ATOMIC_LINK_PROP_KEY );
		}

		if ( method_exists( $widget, 'get_data' ) ) {
			$raw_settings = $widget->get_data( 'settings' );

			if ( is_array( $raw_settings ) && isset( $raw_settings[ self::ATOMIC_LINK_PROP_KEY ] ) ) {
				$candidates[] = $raw_settings[ self::ATOMIC_LINK_PROP_KEY ];
			}
		}

		return $candidates;
	}

	private function normalize_atomic_link_array( $atomic_link ) {
		if ( is_array( $atomic_link ) ) {
			if ( isset( $atomic_link['value'] ) && is_array( $atomic_link['value'] ) ) {
				return $this->normalize_atomic_link_array( $atomic_link['value'] );
			}

			$destination = $atomic_link['destination'] ?? null;
			$destination = $this->unwrap_transformable_value( $destination );
			$atomic_url = $atomic_link['href'] ?? $destination ?? null;
			$atomic_new_tab = false;

			if ( isset( $atomic_link['target'] ) ) {
				$atomic_new_tab = ( '_blank' === $atomic_link['target'] );
			} elseif ( isset( $atomic_link['isTargetBlank'] ) ) {
				$atomic_new_tab = (bool) $atomic_link['isTargetBlank'];
			}

			if ( ! empty( $atomic_url ) ) {
				return [
					'url' => $atomic_url,
					'is_external' => $atomic_new_tab,
					'nofollow' => false,
					'custom_attributes' => '',
				];
			}
		} elseif ( is_string( $atomic_link ) && '' !== $atomic_link ) {
			return [
				'url' => $atomic_link,
				'is_external' => false,
				'nofollow' => false,
				'custom_attributes' => '',
			];
		}

		return null;
	}

	private function unwrap_transformable_value( $value ) {
		if ( is_array( $value ) && array_key_exists( 'value', $value ) && array_key_exists( '$$type', $value ) ) {
			return $this->unwrap_transformable_value( $value['value'] );
		}

		return $value;
	}

	private function normalize_legacy_link_settings( $widget ) {
		$element_link = $widget->get_settings_for_display( self::LEGACY_LINK_CONTROL_KEY );

		$normalized = $this->normalize_legacy_link_array( $element_link );

		if ( $normalized ) {
			return $normalized;
		}

		if ( method_exists( $widget, 'get_data' ) ) {
			$raw_settings = $widget->get_data( 'settings' );

			if ( is_array( $raw_settings ) && isset( $raw_settings[ self::LEGACY_LINK_CONTROL_KEY ] ) ) {
				$normalized = $this->normalize_legacy_link_array( $raw_settings[ self::LEGACY_LINK_CONTROL_KEY ] );

				if ( $normalized ) {
					return $normalized;
				}
			}
		}

		return null;
	}

	private function normalize_legacy_link_array( $element_link ) {
		if ( empty( $element_link ) ) {
			return null;
		}

		if ( is_array( $element_link ) && isset( $element_link['value'] ) && is_array( $element_link['value'] ) ) {
			return $this->normalize_legacy_link_array( $element_link['value'] );
		}

		if ( isset( $element_link['url'] ) ) {
			return $element_link;
		}

		if ( isset( $element_link['href'] ) ) {
			return [
				'url' => $element_link['href'],
				'is_external' => ( isset( $element_link['target'] ) && '_blank' === $element_link['target'] ),
				'nofollow' => false,
				'custom_attributes' => '',
			];
		}

		return null;
	}
}
