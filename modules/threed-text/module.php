<?php

namespace ElementPack\Modules\ThreedText;

use Elementor\Controls_Manager;
use ElementPack\Base\Element_Pack_Module_Base;
use ElementPack\Traits\Global_Widget_Controls;
use ElementPack\Modules\ThreedText\Atomic\Support as Atomic_Support;
use ElementPack\Modules\ThreedText\Normalizers\Settings_Normalizer;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Module extends Element_Pack_Module_Base {
	use Global_Widget_Controls;

	private $atomic_support = null;
	private $settings_normalizer = null;

	public function __construct() {
		parent::__construct();
		$this->add_actions();
	}

	public function get_name() {
		return 'bdt-threed-text';
	}

	public function register_section($element) {
		$element->start_controls_section(
			'section_element_pack_threed_text_controls',
			[
				'tab'   => Controls_Manager::TAB_CONTENT,
				'label' => BDTEP_CP . esc_html__('3D Text', 'bdthemes-element-pack'),
			]
		);
		$element->end_controls_section();
	}


	public function register_controls($widget, $args) {

		$widget->add_control(
			'ep_threed_text_active',
			[
				'label'              => esc_html__('3D Text', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SWITCHER,
				'render_type'        => 'template',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'ep_threed_text_depth',
			[
				'label'     => __('Depth', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'frontend_available' => true,
				'size_units' => ['px', 'em', 'rem', '%'],
				'default' => [
					'size' => 30,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 10,
					],
					'rem' => [
						'min' => 0,
						'max' => 10,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'ep_threed_text_active' => 'yes',
				],
				'render_type' => 'template'
			]
		);

		$widget->add_control(
			'ep_threed_text_layers',
			[
				'label' => esc_html__('Layers', 'bdthemes-element-pack'),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'default' => 8,
				'frontend_available' => true,
				'condition' => [
					'ep_threed_text_active' => 'yes'
				],
			]
		);

		$widget->add_control(
			'ep_threed_text_depth_color',
			[
				'label'     => esc_html__('Depth Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'frontend_available' => true,
				'condition' => [
					'ep_threed_text_active' => 'yes'
				],
			]
		);

		$widget->add_control(
			'ep_threed_text_perspective',
			[
				'label'     => __('Perspective', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'frontend_available' => true,
				'size_units' => ['px'],
				'default' => [
					'size' => 500,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'condition' => [
					'ep_threed_text_active' => 'yes',
				],
			]
		);

		$widget->add_control(
			'ep_threed_text_fade',
			[
				'label'       => esc_html__('Fade', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'condition'   => [
					'ep_threed_text_active' => 'yes'
				],
				'frontend_available' => true,
			]
		);

		// $widget->add_control(
		// 	'ep_threed_text_direction',
		// 	[
		// 		'label'   => esc_html__('Direction', 'bdthemes-element-pack'),
		// 		'type'    => Controls_Manager::SELECT,
		// 		'options' => [
		// 			'both'      => esc_html__('Both', 'bdthemes-element-pack'),
		// 			'backwards' => esc_html__('Backwards', 'bdthemes-element-pack'),
		// 			'forwards'  => esc_html__('Forwards', 'bdthemes-element-pack'),
		// 		],
		// 		'default' => 'both',
		// 		'frontend_available' => true,
		// 		'condition' => [
		// 			'ep_threed_text_active' => 'yes'
		// 		],
		// 	]
		// );

		// $widget->add_control(
		// 	'ep_threed_text_bg_color',
		// 	[
		// 		'label'     => esc_html__( 'Direction Background', 'bdthemes-element-pack' ),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'frontend_available' => true,
		// 		'condition' => [
		// 			'ep_threed_text_active' => 'yes'
		// 		],
		// 	]
		// );

		$widget->add_control(
			'ep_threed_text_event',
			[
				'label'   => esc_html__('Event', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'none'    => esc_html__('None', 'bdthemes-element-pack'),
					'pointer' => esc_html__('Pointer', 'bdthemes-element-pack'),
					'scroll'  => esc_html__('Scroll', 'bdthemes-element-pack'),
					'scrollX' => esc_html__('ScrollX', 'bdthemes-element-pack'),
					'scrollY' => esc_html__('ScrollY', 'bdthemes-element-pack'),
				],
				'default' => 'none',
				'frontend_available' => true,
				'condition' => [
					'ep_threed_text_active' => 'yes'
				],
			]
		);

		$widget->add_control(
			'ep_threed_text_event_rotation',
			[
				'label'     => __('Event Rotation', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 35,
				],
				'range' => [
					'px' => [
						'max' => 360,
						'min' => -360,
					],
				],
				'condition' => [
					'ep_threed_text_active' => 'yes',
					'ep_threed_text_event!' => 'none',
				],
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'ep_threed_text_event_direction',
			[
				'label'   => esc_html__('Event Direction', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default'  => esc_html__('Default', 'bdthemes-element-pack'),
					'reverse'  => esc_html__('Reverse', 'bdthemes-element-pack'),
				],
				'default' => 'default',
				'frontend_available' => true,
				'condition' => [
					'ep_threed_text_active' => 'yes',
					'ep_threed_text_event!' => 'none',
				],
			]
		);
	}

	public function enqueue_scripts() {
		wp_register_script('ztext-js', BDTEP_ASSETS_URL . 'vendor/js/ztext.min.js', ['jquery'], '0.0.2');
		wp_register_script( 'ep-threed-text', BDTEP_ASSETS_URL . 'js/modules/ep-threed-text.min.js', [ 'jquery', 'ztext-js' ], BDTEP_VER, true );

		if (\ElementPack\Element_Pack_Loader::elementor()->preview->is_preview_mode() || \ElementPack\Element_Pack_Loader::elementor()->editor->is_edit_mode()) {
			wp_enqueue_script('ztext-js');
		}
	}

	public function enqueue_editor_scripts() {
		wp_register_script( 'ztext-js', BDTEP_ASSETS_URL . 'vendor/js/ztext.min.js', [ 'jquery' ], '0.0.2', true );

		wp_enqueue_script(
			'ep-threed-text-editor',
			BDTEP_ASSETS_URL . 'js/modules/ep-threed-text-editor.min.js',
			[ 'elementor-editor', 'ztext-js' ],
			BDTEP_VER,
			true
		);
	}
	
	public function should_script_enqueue($widget) {
		if ($this->get_settings_normalizer()->normalize($widget)) {
			$this->enqueue_scripts();
			wp_enqueue_script( 'ztext-js' );
			wp_enqueue_script('ep-threed-text');
		}
	}

	public function register_atomic_schema($schema) {
		return $this->get_atomic_support()->register_schema($schema);
	}

	public function register_atomic_controls($controls, $element) {
		$element_type = method_exists($element, 'get_element_type') ? $element::get_element_type() : '';

		if ('e-heading' !== $element_type) {
			return $controls;
		}

		return $this->get_atomic_support()->register_controls($controls);
	}

	public function maybe_register_atomic_hooks() {
		if (!class_exists('\Elementor\Plugin')) {
			return;
		}

		if (has_filter('elementor/atomic-widgets/props-schema', [$this, 'register_atomic_schema'])) {
			return;
		}

		add_filter('elementor/atomic-widgets/props-schema', [$this, 'register_atomic_schema'], 20);
		add_filter('elementor/atomic-widgets/controls', [$this, 'register_atomic_controls'], 30, 2);
	}

	public function inject_atomic_threed_text_data( $widget_content, $widget ) {
		if ( ! $this->is_atomic_heading( $widget ) ) {
			return $widget_content;
		}

		if ( ! is_string( $widget_content ) || '' === $widget_content ) {
			return $widget_content;
		}

		$settings = $this->get_settings_normalizer()->normalize( $widget );

		if ( ! $settings ) {
			return $widget_content;
		}

		if ( ! method_exists( $widget, 'get_atomic_settings' ) || false !== strpos( $widget_content, 'data-ep-threed-text=' ) ) {
			return $widget_content;
		}

		$encoded_settings = wp_json_encode( $settings );
		$atomic_classes   = [ 'bdt-threed-text-yes' ];

		if ( class_exists( '\WP_HTML_Tag_Processor' ) ) {
			$processor = new \WP_HTML_Tag_Processor( $widget_content );

			if ( $processor->next_tag() ) {
				$processor->set_attribute( 'data-ep-threed-text', $encoded_settings );
				$existing_classes = $processor->get_attribute( 'class' );

				if ( is_string( $existing_classes ) ) {
					$processor->set_attribute( 'class', $this->merge_atomic_classes( $existing_classes, $atomic_classes ) );
				} else {
					$processor->set_attribute( 'class', implode( ' ', $atomic_classes ) );
				}

				return $processor->get_updated_html();
			}
		}

		return preg_replace_callback(
			'/<([a-zA-Z][^\\s>]*)([^>]*)>/',
			function ( $matches ) use ( $encoded_settings, $atomic_classes ) {
				$tag_name   = $matches[1];
				$attributes = $matches[2];

				if ( preg_match( '/\sclass=(["\'])(.*?)\1/', $attributes, $class_matches ) ) {
					$merged_classes = $this->merge_atomic_classes( $class_matches[2], $atomic_classes );
					$attributes     = preg_replace(
						'/\sclass=(["\'])(.*?)\1/',
						' class="' . esc_attr( $merged_classes ) . '"',
						$attributes,
						1
					);
				} else {
					$attributes .= ' class="' . esc_attr( implode( ' ', $atomic_classes ) ) . '"';
				}

				return '<' . $tag_name . $attributes . ' data-ep-threed-text="' . esc_attr( $encoded_settings ) . '">';
			},
			$widget_content,
			1
		) ?: $widget_content;
	}

	private function merge_atomic_classes( string $existing_classes, array $atomic_classes ) {
		$classes = preg_split( '/\s+/', trim( $existing_classes ) );

		if ( ! is_array( $classes ) ) {
			$classes = [];
		}

		return implode( ' ', array_unique( array_merge( array_filter( $classes ), $atomic_classes ) ) );
	}

	private function is_atomic_heading( $widget ): bool {
		if ( ! method_exists( $widget, 'get_atomic_settings' ) ) {
			return false;
		}

		$element_type = method_exists( $widget, 'get_element_type' ) ? $widget::get_element_type() : '';

		return 'e-heading' === $element_type;
	}

	private function get_atomic_support() {
		if (null === $this->atomic_support) {
			$this->atomic_support = new Atomic_Support();
		}

		return $this->atomic_support;
	}

	private function get_settings_normalizer() {
		if (null === $this->settings_normalizer) {
			$this->settings_normalizer = new Settings_Normalizer();
		}

		return $this->settings_normalizer;
	}

	protected function add_actions() {

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_action('elementor/element/heading/section_title_style/before_section_start', [$this, 'register_section']);
		add_action('elementor/element/bdt-advanced-heading/section_style_sub_heading/before_section_start', [$this, 'register_section']);
		add_action('elementor/element/heading/section_element_pack_threed_text_controls/before_section_end', [$this, 'register_controls'], 10, 2);
		add_action('elementor/element/bdt-advanced-heading/section_element_pack_threed_text_controls/before_section_end', [$this, 'register_controls'], 10, 2);

		add_action('elementor/frontend/widget/before_render', [$this, 'should_script_enqueue']);
		add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_scripts']);
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );
		add_filter( 'elementor/widget/render_content', [ $this, 'inject_atomic_threed_text_data' ], 10, 2 );

		if (did_action('elementor/init')) {
			$this->maybe_register_atomic_hooks();
		} else {
			add_action('elementor/init', [$this, 'maybe_register_atomic_hooks']);
		}
	}
}
