<?php

namespace ElementPack\Modules\Tooltip;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use ElementPack;
use ElementPack\Base\Element_Pack_Module_Base;
use ElementPack\Traits\Global_Widget_Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Module extends Element_Pack_Module_Base {
	use Global_Widget_Controls;

	public function __construct() {
		parent::__construct();
		$this->add_actions();
	}

	public function get_name() {
		return 'bdt-tooltip';
	}

	public function register_section( $element ) {
		$element->start_controls_section(
			'section_element_pack_tooltip_controls',
			[ 
				'tab'   => Controls_Manager::TAB_ADVANCED,
				'label' => BDTEP_CP . esc_html__( 'Tooltip', 'bdthemes-element-pack-lite' ),
			]
		);
		$element->end_controls_section();
	}


	public function register_controls( $widget, $args ) {

		$widget->add_control(
			'element_pack_widget_tooltip',
			[ 
				'label'              => esc_html__( 'Use Tooltip?', 'bdthemes-element-pack-lite' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'bdthemes-element-pack-lite' ),
				'label_off'          => esc_html__( 'No', 'bdthemes-element-pack-lite' ),
				'render_type'        => 'template',
				'frontend_available' => true,
			]
		);

		$widget->start_controls_tabs( 'element_pack_widget_tooltip_tabs' );

		$widget->start_controls_tab(
			'element_pack_widget_tooltip_settings_tab',
			[ 
				'label'     => esc_html__( 'Settings', 'bdthemes-element-pack-lite' ),
				'condition' => [ 
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_text',
			[ 
				'label'              => esc_html__( 'Description', 'bdthemes-element-pack-lite' ),
				'type'               => Controls_Manager::TEXTAREA,
				'default'            => esc_html__('This is Tooltip', 'bdthemes-element-pack-lite'),
				'dynamic'            => [ 'active' => true ],
				'condition'          => [ 
					'element_pack_widget_tooltip' => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_placement',
			[ 
				'label'              => esc_html__( 'Placement', 'bdthemes-element-pack-lite' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '',
				'options'            => [ 
					''             => esc_html__( 'Top (Default)', 'bdthemes-element-pack-lite' ),

					'top-start'    => esc_html__( 'Top Start', 'bdthemes-element-pack-lite' ),
					'top-end'      => esc_html__( 'Top End', 'bdthemes-element-pack-lite' ),

					'right'        => esc_html__( 'Right', 'bdthemes-element-pack-lite' ),
					'right-start'  => esc_html__( 'Right Start', 'bdthemes-element-pack-lite' ),
					'right-end'    => esc_html__( 'Right End', 'bdthemes-element-pack-lite' ),

					'bottom'       => esc_html__( 'Bottom', 'bdthemes-element-pack-lite' ),
					'bottom-start' => esc_html__( 'Bottom Start', 'bdthemes-element-pack-lite' ),
					'bottom-end'   => esc_html__( 'Bottom End', 'bdthemes-element-pack-lite' ),

					'left'         => esc_html__( 'Left', 'bdthemes-element-pack-lite' ),
					'left-start'   => esc_html__( 'Left Start', 'bdthemes-element-pack-lite' ),
					'left-end'     => esc_html__( 'Left End', 'bdthemes-element-pack-lite' ),

					'auto'         => esc_html__( 'Auto', 'bdthemes-element-pack-lite' ),
					'auto-start'   => esc_html__( 'Auto Start', 'bdthemes-element-pack-lite' ),
					'auto-end'     => esc_html__( 'Auto End', 'bdthemes-element-pack-lite' ),
				],
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [ 
					'element_pack_widget_tooltip'               => 'yes',
					'element_pack_widget_tooltip_follow_cursor' => ''
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_follow_cursor',
			[ 
				'label'              => esc_html__( 'Follow Cursor', 'bdthemes-element-pack-lite' ) . BDTEP_NC,
				'type'               => Controls_Manager::SWITCHER,
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [ 
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_animation',
			[ 
				'label'              => esc_html__( 'Animation', 'bdthemes-element-pack-lite' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '',
				'options'            => [ 
					'none'         => esc_html__( 'None', 'bdthemes-element-pack-lite' ),
					''             => esc_html__( 'Fade', 'bdthemes-element-pack-lite' ),
					'shift-away'   => esc_html__( 'Shift-Away', 'bdthemes-element-pack-lite' ),
					'shift-toward' => esc_html__( 'Shift-Toward', 'bdthemes-element-pack-lite' ),
					'scale'        => esc_html__( 'Scale', 'bdthemes-element-pack-lite' ),
					'perspective'  => esc_html__( 'Perspective', 'bdthemes-element-pack-lite' ),
					'fill'         => esc_html__( 'Fill Effect', 'bdthemes-element-pack-lite' ),
				],
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [ 
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_trigger',
			[ 
				'label'              => esc_html__( 'Trigger', 'bdthemes-element-pack-lite' ) . BDTEP_NC,
				'type'               => Controls_Manager::SELECT,
				'options'            => [ 
					''       => esc_html__( 'Hover', 'bdthemes-element-pack-lite' ),
					'click'  => esc_html__( 'Click', 'bdthemes-element-pack-lite' ),
					'manual' => esc_html__( 'Custom Trigger', 'bdthemes-element-pack-lite' ),

				],
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [ 
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_custom_trigger',
			[ 
				'label'              => esc_html__( 'Custom Trigger', 'bdthemes-element-pack-lite' ),
				'placeholder'        => '.class-name',
				'type'               => Controls_Manager::TEXT,
				'dynamic'            => [ 'active' => true ],
				'condition'          => [ 
					'element_pack_widget_tooltip'         => 'yes',
					'element_pack_widget_tooltip_trigger' => 'manual',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_x_offset',
			[ 
				'label'              => esc_html__( 'X Offset', 'bdthemes-element-pack-lite' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => [ 'px' ],
				'range'              => [ 
					'px' => [ 
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [ 
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_y_offset',
			[ 
				'label'              => esc_html__( 'Y Offset', 'bdthemes-element-pack-lite' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => [ 'px' ],
				'range'              => [ 
					'px' => [ 
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [ 
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_arrow',
			[ 
				'label'              => esc_html__( 'Arrow', 'bdthemes-element-pack-lite' ),
				'type'               => Controls_Manager::SWITCHER,
				'render_type'        => 'none',
				'frontend_available' => true,
				'condition'          => [ 
					'element_pack_widget_tooltip'            => 'yes',
					'element_pack_widget_tooltip_animation!' => 'fill'
				],
			]
		);

		$widget->end_controls_tab();

		$widget->start_controls_tab(
			'element_pack_widget_tooltip_styles_tab',
			[ 
				'label'     => esc_html__( 'Style', 'bdthemes-element-pack-lite' ),
				'condition' => [ 
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_responsive_control(
			'element_pack_widget_tooltip_width',
			[ 
				'label'      => esc_html__( 'Max Width', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 
					'px',
					'em',
				],
				'range'      => [ 
					'px' => [ 
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors'  => [ 
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'max-width: calc({{SIZE}}{{UNIT}} - 10px) !important;',
				],
				'condition'  => [ 
					'element_pack_widget_tooltip' => 'yes',
				],
				//					'render_type' => 'none',
				//					'frontend_available' => true,
			]
		);


		$widget->add_control(
			'element_pack_widget_tooltip_color',
			[ 
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'color: {{VALUE}}',
				],
				'condition' => [ 
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'      => 'element_pack_widget_tooltip_background',
				'selector'  => '.tippy-box[data-theme="bdt-tippy-{{ID}}"], .tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-backdrop',
				'condition' => [ 
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_arrow_color',
			[ 
				'label'     => esc_html__( 'Arrow Color', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow' => 'color: {{VALUE}}',
				],
				'condition' => [ 
					'element_pack_widget_tooltip' => 'yes',
				],
				'separator' => 'after',
			]
		);

		$widget->add_responsive_control(
			'element_pack_widget_tooltip_padding',
			[ 
				'label'      => __( 'Padding', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [ 
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'element_pack_widget_tooltip_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack-lite' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
				'condition'   => [ 
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_responsive_control(
			'element_pack_widget_tooltip_border_radius',
			[ 
				'label'      => __( 'Border Radius', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [ 
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_control(
			'element_pack_widget_tooltip_text_align',
			[ 
				'label'     => esc_html__( 'Text Alignment', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => [ 
					'left'   => [ 
						'title' => esc_html__( 'Left', 'bdthemes-element-pack-lite' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [ 
						'title' => esc_html__( 'Center', 'bdthemes-element-pack-lite' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [ 
						'title' => esc_html__( 'Right', 'bdthemes-element-pack-lite' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [ 
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'text-align: {{VALUE}};',
				],
				'condition' => [ 
					'element_pack_widget_tooltip' => 'yes',
				],
				'separator' => 'before',
			]
		);

		$widget->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'      => 'element_pack_widget_tooltip_box_shadow',
				'selector'  => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
				'condition' => [ 
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'      => 'element_pack_widget_tooltip_typography',
				'selector'  => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
				'condition' => [ 
					'element_pack_widget_tooltip' => 'yes',
				],
			]
		);

		$widget->end_controls_tab();

		$widget->end_controls_tabs();
	}

	public function enqueue_scripts() {

		wp_register_style( 'tippy', BDTEP_ASSETS_URL . 'css/tippy.css', [], BDTEP_VER );
		wp_register_script( 'popper', BDTEP_ASSETS_URL . 'vendor/js/popper.min.js', [ 'jquery' ], BDTEP_VER, true );
		wp_register_script( 'tippyjs', BDTEP_ASSETS_URL . 'vendor/js/tippy.all.min.js', [ 'jquery' ], BDTEP_VER, true );

		if ( \ElementPack\Element_Pack_Loader::elementor()->preview->is_preview_mode() || \ElementPack\Element_Pack_Loader::elementor()->editor->is_edit_mode() ) {
			wp_enqueue_script( 'popper' );
			wp_enqueue_script( 'tippyjs' );
			wp_enqueue_style( 'tippy' );
		}
	}

	public function should_script_enqueue( $widget ) {
		if ( 'yes' === $widget->get_settings_for_display( 'element_pack_widget_tooltip' ) ) {
			$this->enqueue_scripts();
			wp_enqueue_style( 'tippy' );
			wp_enqueue_script( 'popper' );
			wp_enqueue_script( 'tippyjs' );
			wp_enqueue_script( 'ep-tooltip' );
		}
	}

	protected function add_actions() {

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 9999 );

		add_action( 'elementor/element/common/_section_style/after_section_end', [ $this, 'register_section' ] );

		add_action( 'elementor/element/common/section_element_pack_tooltip_controls/before_section_end', [ $this, 'register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/widget/before_render', [ $this, 'should_script_enqueue' ] );
		add_action( 'elementor/preview/enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}
}
