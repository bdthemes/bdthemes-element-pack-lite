<?php

namespace ElementPack\Modules\AnimatedGradientBackground;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use ElementPack\Base\Element_Pack_Module_Base;
use ElementPack\Traits\Global_Widget_Controls;

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {
	use Global_Widget_Controls;

	public function __construct() {
		parent::__construct();
		$this->add_actions();
	}

	public function get_name() {
		return 'bdt-animated-gradient-background';
	}

	public function register_section( $element ) {

		$element->start_controls_section(
			'element_pack_agbg_section',
			[ 
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => BDTEP_CP . esc_html__( 'Animated Gradient Background', 'bdthemes-element-pack-lite' ),
			]
		);
		$element->end_controls_section();
	}

	public function register_controls( $section, $args ) {

		$section->add_control(
			'element_pack_agbg_show',
			[ 
				'label'              => esc_html__( 'Use Animated Gradient BG', 'bdthemes-element-pack-lite' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'render_type'        => 'template',
				'prefix_class'       => 'element-pack-agbg-',
				'return_value'       => 'yes',
				'default'            => '',
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'start_color',
			[ 
				'label'   => esc_html__( 'Start Color', 'bdthemes-element-pack-lite' ),
				'default' => '#0591F9',
				'type'    => Controls_Manager::COLOR,
			]
		);
		$repeater->add_control(
			'end_color',
			[ 
				'label'   => esc_html__( 'End Color', 'bdthemes-element-pack-lite' ),
				'default' => '#fefefe',
				'type'    => Controls_Manager::COLOR,
			]
		);

		$section->add_control(
			'element_pack_agbg_color_list',
			[ 
				'label'              => esc_html__( 'Color List', 'bdthemes-element-pack-lite' ),
				'type'               => Controls_Manager::REPEATER,
				'fields'             => $repeater->get_controls(),
				'frontend_available' => true,
				'render_type'        => 'none',
				'default'            => [ 
					[ 
						'start_color' => esc_html__( '#0591F9', 'bdthemes-element-pack-lite' ),
						'end_color'   => esc_html__( '#fefefe', 'bdthemes-element-pack-lite' ),
					],
					[ 
						'start_color' => esc_html__( '#567445', 'bdthemes-element-pack-lite' ),
						'end_color'   => esc_html__( '#1D1BE0', 'bdthemes-element-pack-lite' ),
					],
				],
				'title_field'        => '{{start_color}}',
				'condition'          => [ 
					'element_pack_agbg_show' => 'yes'
				]
			]
		);

		$section->add_control(
			'element_pack_agbg_blending_mode',
			[ 
				'label'      => esc_html__( 'Blend Mode', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'hue',
				'options'    => [ 
					'multiply'    => esc_html__( 'Multiply', 'bdthemes-element-pack-lite' ),
					'screen'      => esc_html__( 'Screen', 'bdthemes-element-pack-lite' ),
					'normal'      => esc_html__( 'Normal', 'bdthemes-element-pack-lite' ),
					'overlay'     => esc_html__( 'Overlay', 'bdthemes-element-pack-lite' ),
					'darken'      => esc_html__( 'Darken', 'bdthemes-element-pack-lite' ),
					'lighten'     => esc_html__( 'Lighten', 'bdthemes-element-pack-lite' ),
					'color-dodge' => esc_html__( 'Color Dodge', 'bdthemes-element-pack-lite' ),
					'color-burn'  => esc_html__( 'Color Burn', 'bdthemes-element-pack-lite' ),
					'hard-light'  => esc_html__( 'Hard Light', 'bdthemes-element-pack-lite' ),
					'soft-light'  => esc_html__( 'Soft Light', 'bdthemes-element-pack-lite' ),
					'difference'  => esc_html__( 'Difference', 'bdthemes-element-pack-lite' ),
					'exclusion'   => esc_html__( 'Exclusion', 'bdthemes-element-pack-lite' ),
					'hue'         => esc_html__( 'Hue', 'bdthemes-element-pack-lite' ),
					'saturation'  => esc_html__( 'Saturation', 'bdthemes-element-pack-lite' ),
					'color'       => esc_html__( 'Color', 'bdthemes-element-pack-lite' ),
					'luminosity'  => esc_html__( 'Luminosity', 'bdthemes-element-pack-lite' ),
				],
				'selectors'  => [ 
					'{{WRAPPER}}.element-pack-agbg-yes .bdt-animated-gradient-background' => 'mix-blend-mode:{{VALUE}}'
				],
				'conditions' => [ 
					'relation' => 'and',
					'terms'    => [ 
						[ 
							'name'     => 'background_background',
							'operator' => '!==',
							'value'    => '',
						],
						[ 
							'name'     => 'element_pack_agbg_show',
							'operator' => '===',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$section->add_control(
			'element_pack_agbg_direction',
			[ 
				'label'              => esc_html__( 'Direction', 'bdthemes-element-pack-lite' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'diagonal',
				'separator'          => 'before',
				'frontend_available' => true,
				'render_type'        => 'none',
				'options'            => [ 
					'diagonal'   => esc_html__( 'Diagonal', 'bdthemes-element-pack-lite' ),
					'left-right' => esc_html__( 'Left Right', 'bdthemes-element-pack-lite' ),
					'top-bottom' => esc_html__( 'Top Bottom', 'bdthemes-element-pack-lite' ),
					'radial'     => esc_html__( 'Radial', 'bdthemes-element-pack-lite' ),
				],
				'condition'          => [ 
					'element_pack_agbg_show' => 'yes'
				]
			]
		);

		$section->add_control(
			'element_pack_agbg_transitionSpeed',
			[ 
				'label'              => esc_html__( 'Transition Speed', 'bdthemes-element-pack-lite' ),
				'type'               => Controls_Manager::SLIDER,
				'frontend_available' => true,
				'render_type'        => 'none',
				'range'              => [ 
					'px' => [ 
						'min'  => 100,
						'max'  => 10000,
						'step' => 100,
					]
				],
				'condition'          => [ 
					'element_pack_agbg_show' => 'yes'
				]
			]
		);
	}

	public function enqueue_scripts() {
		wp_register_script( 'granim', BDTEP_ASSETS_URL . 'vendor/js/granim.min.js', [], 'v2.0.0', true );

		if ( \ElementPack\Element_Pack_Loader::elementor()->preview->is_preview_mode() || \ElementPack\Element_Pack_Loader::elementor()->editor->is_edit_mode() ) {
			wp_enqueue_script( 'granim' );
		}
	}
	public function should_script_enqueue( $section ) {
		if ( 'yes' === $section->get_settings_for_display( 'element_pack_agbg_show' ) ) {
			$this->enqueue_scripts();
			wp_enqueue_script( 'granim' );
			wp_enqueue_style( 'ep-animated-gradient-background' );
			wp_enqueue_script( 'ep-animated-gradient-background' );
		}
	}

	protected function add_actions() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		add_action( 'elementor/element/section/section_background/after_section_end', [ $this, 'register_section' ] );
		add_action( 'elementor/element/section/element_pack_agbg_section/before_section_end', [ $this, 'register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/section/before_render', [ $this, 'should_script_enqueue' ] );

		add_action( 'elementor/element/container/section_background/after_section_end', [ $this, 'register_section' ] );
		add_action( 'elementor/element/container/element_pack_agbg_section/before_section_end', [ $this, 'register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/container/before_render', [ $this, 'should_script_enqueue' ] );

		add_action( 'elementor/preview/enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}
}
