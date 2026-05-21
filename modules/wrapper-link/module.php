<?php

namespace ElementPack\Modules\WrapperLink;

use Elementor\Controls_Manager;
use Elementor\Modules\AtomicWidgets\Controls\Types\Link_Control;
use ElementPack\Base\Element_Pack_Module_Base;
use ElementPack\Modules\WrapperLink\Atomic\Wrapper_Link_Prop_Type;
use ElementPack\Modules\WrapperLink\Normalizers\Link_Settings_Normalizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Module extends Element_Pack_Module_Base {
	private const LEGACY_LINK_CONTROL_KEY = 'element_pack_wrapper_link';
	private const ATOMIC_LINK_PROP_KEY = 'element_pack_wrapper_link_url';
	private const WRAPPER_SECTION_KEY = 'section_element_pack_wrapper_link';
	private const SCRIPT_HANDLE = 'ep-wrapper-link';
	private $link_settings_normalizer = null;

	public function __construct() {
		parent::__construct();
		$this->add_actions();
	}

	public function get_name() {
		return 'bdt-wrapper-link';
	}

	public function register_section( $element ) {

		if ( 'section' === $element->get_name() || 'column' === $element->get_name() || 'container' === $element->get_name() ) {
			$tabs = Controls_Manager::TAB_LAYOUT;
		} else {
			$tabs = Controls_Manager::TAB_CONTENT;
		}

		$element->start_controls_section(
			self::WRAPPER_SECTION_KEY,
			[ 
				'tab' => $tabs,
				'label' => BDTEP_CP . esc_html__( 'Wrapper Link', 'bdthemes-element-pack' ),
			]
		);

		$element->end_controls_section();
	}


	public function register_controls( $widget, $args ) {
		$this->register_legacy_controls( $widget );
	}

	private function register_legacy_controls( $widget ) {
		$widget->add_control(
			self::LEGACY_LINK_CONTROL_KEY,
			[ 
				'label' => esc_html__( 'Link', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://example.com', 'bdthemes-element-pack' ),
				'show_external' => true,
				'default' => [ 'url' => '' ],
				'dynamic' => [ 'active' => true ],
				'render_type' => 'none',
			]
		);
	}

	public function register_atomic_schema( $schema ) {
		if ( ! class_exists( '\Elementor\Modules\AtomicWidgets\PropTypes\Base\Object_Prop_Type' ) ) {
			return $schema;
		}

		if ( ! isset( $schema[ self::ATOMIC_LINK_PROP_KEY ] ) ) {
			$schema[ self::ATOMIC_LINK_PROP_KEY ] = Wrapper_Link_Prop_Type::make();
		}

		return $schema;
	}

	public function register_atomic_controls( $controls, $element ) {
		if ( ! class_exists( '\Elementor\Modules\AtomicWidgets\Controls\Types\Link_Control' ) ) {
			return $controls;
		}

		$schema = method_exists( $element, 'get_props_schema' ) ? $element::get_props_schema() : [];

		if ( ! isset( $schema[ self::ATOMIC_LINK_PROP_KEY ] ) ) {
			return $controls;
		}
		
		foreach ( $controls as $item ) {
			if ( ! ( $item instanceof \Elementor\Modules\AtomicWidgets\Controls\Section ) ) {
				continue;
			}

			if ( 'settings' !== $item->get_id() ) {
				continue;
			}

			$item->add_item(
				Link_Control::bind_to( self::ATOMIC_LINK_PROP_KEY )
					->set_placeholder( esc_html__( 'Type or paste a URL', 'bdthemes-element-pack' ) )
					->set_label( esc_html__( 'Wrapper Link', 'bdthemes-element-pack' ) )
					->set_meta(
						[
							'topDivider' => true,
						]
					)
					->set_description( esc_html__( 'If the URL is not a link, the link will be opened in a new tab.', 'bdthemes-element-pack' ) )
			);
			break;
		}

		return $controls;
	}

	private function normalize_wrapper_link_settings( $widget ) {
		return $this->get_link_settings_normalizer()->normalize( $widget );
	}

	private function get_link_settings_normalizer() {
		if ( null === $this->link_settings_normalizer ) {
			$this->link_settings_normalizer = new Link_Settings_Normalizer();
		}

		return $this->link_settings_normalizer;
	}

	private function get_sanitized_wrapper_link_settings( $widget ) {
		$element_link = $this->normalize_wrapper_link_settings( $widget );

		if ( ! $element_link || empty( $element_link['url'] ) ) {
			return null;
		}

		$element_link['url'] = esc_url( $element_link['url'], [ 'http', 'https', 'tel', 'mailto', 'sms' ] );

		return $element_link;
	}

	public function wrapper_link_before_render( $widget ) {
		$element_link = $this->get_sanitized_wrapper_link_settings( $widget );

		if ( $element_link ) {
			$widget->add_render_attribute(
				'_wrapper',
				[
					'data-ep-wrapper-link' => wp_json_encode( $element_link, true ),
					'style' => 'cursor: pointer',
					'class' => 'bdt-element-link',
				]
			);
		}
	}

	public function inject_wrapper_link_into_atomic_markup( $widget_content, $widget ) {
		$element_link = $this->get_sanitized_wrapper_link_settings( $widget );

		if ( ! $element_link || ! is_string( $widget_content ) || '' === $widget_content ) {
			return $widget_content;
		}

		if ( ! method_exists( $widget, 'get_atomic_settings' ) ) {
			return $widget_content;
		}

		if ( false !== strpos( $widget_content, 'data-ep-wrapper-link=' ) ) {
			return $widget_content;
		}

		$encoded_link = wp_json_encode( $element_link );

		if ( class_exists( '\WP_HTML_Tag_Processor' ) ) {
			$processor = new \WP_HTML_Tag_Processor( $widget_content );

			if ( $processor->next_tag() ) {
				$processor->set_attribute( 'data-ep-wrapper-link', $encoded_link );
				$processor->add_class( 'bdt-element-link' );

				$existing_style = (string) $processor->get_attribute( 'style' );
				$cursor_style = 'cursor: pointer;';
				$style = trim( $existing_style );

				if ( false === stripos( $style, 'cursor:' ) ) {
					$style = $style ? rtrim( $style, ';' ) . '; ' . $cursor_style : $cursor_style;
					$processor->set_attribute( 'style', $style );
				}

				return $processor->get_updated_html();
			}
		}

		$attributes = ' data-ep-wrapper-link="' . $encoded_link . '" class="bdt-element-link" style="cursor: pointer"';

		return preg_replace( '/<([a-zA-Z][^\\s>]*)([^>]*)>/', '<$1$2' . $attributes . '>', $widget_content, 1 ) ?: $widget_content;
	}

	public function enqueue_scripts() {
		wp_enqueue_script( self::SCRIPT_HANDLE );
	}

	public function should_script_enqueue( $widget ) {
		$element_link = $this->get_sanitized_wrapper_link_settings( $widget );

		if ( $element_link ) {
			$this->enqueue_scripts();
		}
	}

	public function maybe_register_atomic_hooks() {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}

		// Prevent duplicate callbacks if this method is called more than once.
		if ( has_filter( 'elementor/atomic-widgets/props-schema', [ $this, 'register_atomic_schema' ] ) ) {
			return;
		}

		add_filter( 'elementor/atomic-widgets/props-schema', [ $this, 'register_atomic_schema' ], 20 );
		add_filter( 'elementor/atomic-widgets/controls', [ $this, 'register_atomic_controls' ], 30, 2 );
	}

	protected function add_actions() {

		// Add container settings
		add_action( 'elementor/element/container/section_layout_container/after_section_end', [ $this, 'register_section' ] );
		add_action( 'elementor/element/container/' . self::WRAPPER_SECTION_KEY . '/before_section_end', [ $this, 'register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/container/after_render', [ $this, 'should_script_enqueue' ] );


		// Add section settings
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'register_section' ] );
		add_action( 'elementor/element/section/' . self::WRAPPER_SECTION_KEY . '/before_section_end', [ $this, 'register_controls' ], 10, 2 );

		// Add column settings
		add_action( 'elementor/element/column/section_advanced/after_section_end', [ $this, 'register_section' ] );
		add_action( 'elementor/element/column/' . self::WRAPPER_SECTION_KEY . '/before_section_end', [ $this, 'register_controls' ], 10, 2 );

		// Add widget settings
		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'register_section' ] );
		add_action( 'elementor/element/common/' . self::WRAPPER_SECTION_KEY . '/before_section_end', [ $this, 'register_controls' ], 10, 2 );


		add_action( 'elementor/frontend/before_render', [ $this, 'wrapper_link_before_render' ], 10, 1 );
		add_action( 'elementor/frontend/before_render', [ $this, 'should_script_enqueue' ] );
		// Twig-based atomic widgets render their own visible root tag, so we also
		// inject the wrapper-link payload into the final widget HTML.
		add_filter( 'elementor/widget/render_content', [ $this, 'inject_wrapper_link_into_atomic_markup' ], 10, 2 );

		// Register now if Elementor is already initialized, otherwise wait.
		if ( did_action( 'elementor/init' ) ) {
			$this->maybe_register_atomic_hooks();
		} else {
			add_action( 'elementor/init', [ $this, 'maybe_register_atomic_hooks' ] );
		}
	}
}
