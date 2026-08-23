<?php
namespace ElementPack\Modules\ImageStack\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use ElementPack\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Image_Stack extends Module_Base {

	public function get_name() {
		return 'bdt-image-stack';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Image Stack', 'bdthemes-element-pack-lite' );
	}

	public function get_icon() {
		return 'bdt-wi-image-stack';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'image', 'icon', 'stack', 'group' ];
	}

	public function get_style_depends() {
		return $this->ep_is_edit_mode() ? [ 'ep-styles' ] : [ 'ep-image-stack', 'tippy' ];
	}

	public function get_script_depends() {
		return $this->ep_is_edit_mode() ? [ 'popper', 'tippyjs', 'ep-scripts' ] : [ 'popper', 'tippyjs', 'ep-image-stack' ];
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/maLIlug2RwM';
	}

	public function has_widget_inner_wrapper(): bool {
        return ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
    }
	protected function is_dynamic_content(): bool {
		return false;
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_image_stack',
			[ 
				'label' => __( 'Image Stack', 'bdthemes-element-pack-lite' ),
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'item_content_tabs' );

		$repeater->start_controls_tab(
			'tab_item_content',
			[ 
				'label' => __( 'Content', 'bdthemes-element-pack-lite' )
			]
		);

		$repeater->add_control(
			'media_type',
			[ 
				'label'       => esc_html__( 'Media Type', 'bdthemes-element-pack-lite' ),
				'type'        => Controls_Manager::CHOOSE,
				'toggle'      => false,
				'default'     => 'image',
				'render_type' => 'template',
				'options'     => [ 
					'image' => [ 
						'title' => esc_html__( 'Image', 'bdthemes-element-pack-lite' ),
						'icon'  => 'far fa-image'
					],
					'icon'  => [ 
						'title' => esc_html__( 'Icon', 'bdthemes-element-pack-lite' ),
						'icon'  => 'fas fa-star'
					]
				]
			]
		);

		$repeater->add_control(
			'selected_icon',
			[ 
				'label'       => __( 'Icon', 'bdthemes-element-pack-lite' ),
				'type'        => Controls_Manager::ICONS,
				'default'     => [ 
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'render_type' => 'template',
				'condition'   => [ 
					'media_type' => 'icon',
				],
				'label_block' => false,
				'skin'        => 'inline'
			]
		);

		$repeater->add_control(
			'image',
			[ 
				'label'       => __( 'Image', 'bdthemes-element-pack-lite' ),
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => ['active' => true],
				'render_type' => 'template',
				'default'     => [ 
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition'   => [ 
					'media_type' => 'image'
				]
			]
		);

		$repeater->add_control(
			'link_url',
			[ 
				'label'       => __( 'Link', 'bdthemes-element-pack-lite' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => 'http://your-link.com',
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'tab_tooltip_text',
			[ 
				'label' => __( 'Tooltip', 'bdthemes-element-pack-lite' )
			]
		);

		$repeater->add_control(
			'tooltip_text',
			[ 
				'label'   => __( 'Text', 'bdthemes-element-pack-lite' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [ 
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'tooltip_placement',
			[ 
				'label'     => __( 'Placement', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'top',
				'options'   => [ 
					'top'    => __( 'Top', 'bdthemes-element-pack-lite' ),
					'bottom' => __( 'Bottom', 'bdthemes-element-pack-lite' ),
					'left'   => __( 'Left', 'bdthemes-element-pack-lite' ),
					'right'  => __( 'Right', 'bdthemes-element-pack-lite' ),
				],
				'condition' => [ 
					'tooltip_text!' => '',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'tab_item_style',
			[ 
				'label' => __( 'Style', 'bdthemes-element-pack-lite' )
			]
		);

		$repeater->add_control(
			'icon_color',
			[ 
				'label'     => __( 'Color', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-image-stack .bdt-ep-image-stack-item{{CURRENT_ITEM}}'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-image-stack .bdt-ep-image-stack-item{{CURRENT_ITEM}} svg' => 'fill: {{VALUE}};',
				],
				'condition' => [ 
					'media_type' => 'icon'
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'      => 'icon_background',
				'types'     => [ 'classic', 'gradient' ],
				'exclude'   => [ 'image' ], // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor control option, not WP_Query.
				'selector'  => '{{WRAPPER}} .bdt-image-stack .bdt-ep-image-stack-item{{CURRENT_ITEM}} span, {{WRAPPER}} .bdt-image-stack .bdt-ep-image-stack-item{{CURRENT_ITEM}} a',
				'condition' => [ 
					'media_type' => 'icon'
				],
			]
		);

		$repeater->add_control(
			'border_color_item',
			[ 
				'label'     => __( 'Border Color', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-image-stack .bdt-ep-image-stack-item{{CURRENT_ITEM}} span, {{WRAPPER}} .bdt-ep-image-stack-item{{CURRENT_ITEM}} a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();

		$this->add_control(
			'image_stack_items',
			[ 
				'show_label'  => false,
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [ 
					[ 
						'image' => [ 'url' => Utils::get_placeholder_image_src() ],
					],
					[ 
						'image' => [ 'url' => Utils::get_placeholder_image_src() ],
					],
					[ 
						'image' => [ 'url' => Utils::get_placeholder_image_src() ],
					],
					[ 
						'image' => [ 'url' => Utils::get_placeholder_image_src() ],
					],
				],
				'title_field' => '{{{ tooltip_text }}}',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[ 
				'name'    => 'thumbnail_size',
				'default' => 'medium',
			]
		);

		$this->add_responsive_control(
			'alignment',
			[ 
				'label'     => __( 'Alignment', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [ 
					'left'   => [ 
						'title' => __( 'Left', 'bdthemes-element-pack-lite' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [ 
						'title' => __( 'Center', 'bdthemes-element-pack-lite' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [ 
						'title' => __( 'Right', 'bdthemes-element-pack-lite' ),
						'icon'  => 'eicon-text-align-right',
					]
				],
				'default'   => 'center',
				'selectors' => [ 
					'{{WRAPPER}}.elementor-widget-bdt-image-stack' => 'text-align: {{VALUE}};'
				]
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_items',
			[ 
				'label' => __( 'Items', 'bdthemes-element-pack-lite' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'item_style_tabs' );

		$this->start_controls_tab(
			'tab_item_normal',
			[ 
				'label' => __( 'Normal', 'bdthemes-element-pack-lite' )
			]
		);

		$this->add_control(
			'icon_color',
			[ 
				'label'     => __( 'Icon Color', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-ep-image-stack-item'     => 'color: {{VALUE}}',
					'{{WRAPPER}} .bdt-ep-image-stack-item svg' => 'fill: {{VALUE}}',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'icon_background_color',
				'types'    => [ 'classic', 'gradient' ],
				'exclude'  => [ 'image' ], // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor control option, not WP_Query.
				'selector' => '{{WRAPPER}} .bdt-ep-image-stack-item span, {{WRAPPER}} .bdt-ep-image-stack-item a'
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'           => 'item_border',
				'label'          => esc_html__( 'Border', 'bdthemes-element-pack-lite' ),
				'fields_options' => [ 
					'border' => [ 
						'default' => 'solid',
					],
					'width'  => [ 
						'default' => [ 
							'top'      => '3',
							'right'    => '3',
							'bottom'   => '3',
							'left'     => '3',
							'isLinked' => false,
						],
					],
					'color'  => [ 
						'default' => '#fff',
					],
				],
				'selector'       => '{{WRAPPER}} .bdt-ep-image-stack-item span, {{WRAPPER}} .bdt-ep-image-stack-item a',
				'separator'      => 'before',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[ 
				'label'      => __( 'Border Radius', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-ep-image-stack-item,{{WRAPPER}}  .bdt-ep-image-stack-item span, {{WRAPPER}} .bdt-ep-image-stack-item a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_size',
			[ 
				'label'      => __( 'Item Size', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 
					'px' => [ 
						'min' => 10,
						'max' => 200,
					],
				],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-ep-image-stack-item span, {{WRAPPER}} .bdt-ep-image-stack-item a' => 'width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[ 
				'label'     => __( 'Icon Size', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-ep-image-stack-item span, {{WRAPPER}} .bdt-ep-image-stack-item a' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_spacing',
			[ 
				'label'     => __( 'Spacing', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-ep-image-stack-item:not(:last-child) span, {{WRAPPER}} .bdt-ep-image-stack-item:not(:last-child) a' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_stack_spacing',
			[ 
				'label'     => __( 'Stack Spacing', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-ep-image-stack-item:not(:first-child) a, {{WRAPPER}} .bdt-ep-image-stack-item:not(:first-child) span' => 'margin-left: -{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'item_box_shadow',
				'label'    => __( 'Box Shadow', 'bdthemes-element-pack-lite' ),
				'selector' => '{{WRAPPER}} .bdt-ep-image-stack-item span, {{WRAPPER}} .bdt-ep-image-stack-item a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[ 
				'label' => __( 'Hover', 'bdthemes-element-pack-lite' )
			]
		);

		$this->add_control(
			'icon_color_hover',
			[ 
				'label'     => __( 'Icon Color', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-ep-image-stack-item:hover'     => 'color: {{VALUE}}',
					'{{WRAPPER}} .bdt-ep-image-stack-item:hover svg' => 'fill: {{VALUE}}',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'icon_background_color_hover',
				'types'    => [ 'classic', 'gradient' ],
				'exclude'  => [ 'image' ], // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor control option, not WP_Query.
				'selector' => '{{WRAPPER}} .bdt-ep-image-stack-item:hover span, {{WRAPPER}} .bdt-ep-image-stack-item:hover a'
			]
		);

		$this->add_control(
			'item_hover_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'item_border_border!' => '',
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-ep-image-stack-item:hover span, {{WRAPPER}} .bdt-ep-image-stack-item:hover a' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'item_box_shadow_hover',
				'label'    => __( 'Box Shadow', 'bdthemes-element-pack-lite' ),
				'selector' => '{{WRAPPER}} .bdt-ep-image-stack-item:hover span, {{WRAPPER}} .bdt-ep-image-stack-item:hover a',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_stack_animations',
			[ 
				'label' => __( 'Stack Animations', 'bdthemes-element-pack-lite' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'stack_style_tabs' );

		$this->start_controls_tab(
			'tab_stack_item_hover',
			[ 
				'label' => __( 'Item Hover', 'bdthemes-element-pack-lite' )
			]
		);

		$this->add_control(
			'item_translate_toggle_hover',
			[ 
				'label'        => __( 'Translate', 'bdthemes-element-pack-lite' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			]
		);

		$this->start_popover();


		$this->add_responsive_control(
			'item_effect_transx_hover',
			[ 
				'label'      => esc_html__( 'Translate X', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 
					'px' => [ 
						'min' => -100,
						'max' => 100,
					],
				],
				'condition'  => [ 
					'item_translate_toggle_hover' => 'yes',
				],
				'selectors'  => [ 
					'{{WRAPPER}}' => '--ep-item-trans-x-hover: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'item_effect_transy_hover',
			[ 
				'label'      => esc_html__( 'Translate Y', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 
					'px' => [ 
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors'  => [ 
					'{{WRAPPER}}' => '--ep-item-trans-y-hover: {{SIZE}}px;'
				],
				'condition'  => [ 
					'item_translate_toggle_hover' => 'yes',
				],
			]
		);

		$this->end_popover();

		$this->add_control(
			'item_rotate_toggle_hover',
			[ 
				'label'        => __( 'Rotate', 'bdthemes-element-pack-lite' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'item_effect_rotatex_hover',
			[ 
				'label'      => esc_html__( 'Rotate X', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 
					'px' => [ 
						'min' => -180,
						'max' => 180,
					],
				],
				'condition'  => [ 
					'item_rotate_toggle_hover' => 'yes',
				],
				'selectors'  => [ 
					'{{WRAPPER}}' => '--ep-item-rotate-x-hover: {{SIZE||0}}deg;'
				],
			]
		);

		$this->add_responsive_control(
			'item_effect_rotatey_hover',
			[ 
				'label'      => esc_html__( 'Rotate Y', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 
					'px' => [ 
						'min' => -180,
						'max' => 180,
					],
				],
				'condition'  => [ 
					'item_rotate_toggle_hover' => 'yes',
				],
				'selectors'  => [ 
					'{{WRAPPER}}' => '--ep-item-rotate-y-hover: {{SIZE||0}}deg;'
				],
			]
		);

		$this->add_responsive_control(
			'item_effect_rotatez_hover',
			[ 
				'label'      => __( 'Rotate Z', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 
					'px' => [ 
						'min' => -180,
						'max' => 180,
					],
				],
				'selectors'  => [ 
					'{{WRAPPER}}' => '--ep-item-rotate-z-hover: {{SIZE||0}}deg;'
				],
				'condition'  => [ 
					'item_rotate_toggle_hover' => 'yes',
				],
			]
		);

		$this->end_popover();

		$this->add_control(
			'item_scale_hover',
			[ 
				'label'        => __( 'Scale', 'bdthemes-element-pack-lite' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'item_effect_scalex_hover',
			[ 
				'label'      => esc_html__( 'Scale X', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 
					'px' => [ 
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1
					],
				],
				'condition'  => [ 
					'item_scale_hover' => 'yes',
				],
				'selectors'  => [ 
					'{{WRAPPER}}' => '--ep-item-scale-x-hover: {{SIZE}};'
				],
			]
		);

		$this->add_responsive_control(
			'item_effect_scaley_hover',
			[ 
				'label'      => esc_html__( 'Scale Y', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 
					'px' => [ 
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1
					],
				],
				'condition'  => [ 
					'item_scale_hover' => 'yes',
				],
				'selectors'  => [ 
					'{{WRAPPER}}' => '--ep-item-scale-y-hover: {{SIZE}};'
				],
			]
		);

		$this->end_popover();

		$this->add_control(
			'item_skew_hover',
			[ 
				'label'        => __( 'Skew', 'bdthemes-element-pack-lite' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'item_effect_skewx_hover',
			[ 
				'label'      => esc_html__( 'Skew X', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 
					'px' => [ 
						'min' => -180,
						'max' => 180,
					],
				],
				'condition'  => [ 
					'item_skew_hover' => 'yes',
				],
				'selectors'  => [ 
					'{{WRAPPER}}' => '--ep-item-skew-x-hover: {{SIZE}}deg;'
				],
			]
		);

		$this->add_responsive_control(
			'item_effect_skewy_hover',
			[ 
				'label'      => esc_html__( 'Skew Y', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 
					'px' => [ 
						'min' => -180,
						'max' => 180,
					],
				],
				'condition'  => [ 
					'item_skew_hover' => 'yes',
				],
				'selectors'  => [ 
					'{{WRAPPER}}' => '--ep-item-skew-y-hover: {{SIZE}}deg;'
				],
			]
		);

		$this->end_popover();

		$this->add_control(
			'item_effect_transition',
			[ 
				'label'       => __( 'Transition', 'bdthemes-element-pack-lite' ),
				'type'        => Controls_Manager::POPOVER_TOGGLE,
				'render_type' => 'none',
			]
		);

		$this->start_popover();

		$this->add_control(
			'item_effect_transition_duration',
			[ 
				'label'     => esc_html__( 'Duration (ms)', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => [ 'active' => true ],
				'default'   => '300',
				'condition' => [ 
					'item_effect_transition' => 'yes',
				],
				'selectors' => [ 
					'{{WRAPPER}}' => '--ep-item-transition-duration: {{VALUE}}ms;',
				],
			]
		);

		$this->add_control(
			'item_effect_transition_delay',
			[ 
				'label'     => esc_html__( 'Delay (ms)', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => [ 'active' => true ],
				'condition' => [ 
					'item_effect_transition' => 'yes',
				],
				'selectors' => [ 
					'{{WRAPPER}}' => '--ep-item-transition-delay: {{VALUE}}ms;',
				],
			]
		);

		$this->add_control(
			'item_effect_transition_easing',
			[ 
				'label'       => esc_html__( 'Easing', 'bdthemes-element-pack-lite' ),
/* translators: 1: Opening anchor tag, 2: Closing anchor tag */
				'description' => sprintf( __( 'If you want use Cubic Bezier easing, Go %1$s HERE %2$s', 'bdthemes-element-pack-lite' ), '<a href="https://cubic-bezier.com/" target="_blank">', '</a>' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'default'     => 'ease-out',
				'condition'   => [ 
					'item_effect_transition' => 'yes',
				],
				'selectors'   => [ 
					'{{WRAPPER}}' => '--ep-item-transition-easing: {{VALUE}};',
				],
			]
		);

		$this->end_popover();

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_stack_hover',
			[ 
				'label' => __( 'Stack Hover', 'bdthemes-element-pack-lite' )
			]
		);

		$this->add_control(
			'stack_translate_toggle_normal',
			[ 
				'label'        => __( 'Translate', 'bdthemes-element-pack-lite' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'stack_effect_transx_normal',
			[ 
				'label'      => esc_html__( 'Translate X', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 
					'px' => [ 
						'min' => -100,
						'max' => 100,
					],
				],
				'condition'  => [ 
					'stack_translate_toggle_normal' => 'yes',
				],
				'selectors'  => [ 
					'{{WRAPPER}}' => '--ep-stack-trans-x-normal: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'stack_effect_transy_normal',
			[ 
				'label'      => esc_html__( 'Translate Y', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 
					'px' => [ 
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors'  => [ 
					'{{WRAPPER}}' => '--ep-stack-trans-y-normal: {{SIZE}}px;'
				],
				'condition'  => [ 
					'stack_translate_toggle_normal' => 'yes',
				],
			]
		);

		$this->end_popover();

		$this->add_control(
			'stack_rotate_toggle_normal',
			[ 
				'label'        => __( 'Rotate', 'bdthemes-element-pack-lite' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'stack_effect_rotatex_normal',
			[ 
				'label'      => esc_html__( 'Rotate X', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 
					'px' => [ 
						'min' => -180,
						'max' => 180,
					],
				],
				'condition'  => [ 
					'stack_rotate_toggle_normal' => 'yes',
				],
				'selectors'  => [ 
					'{{WRAPPER}}' => '--ep-stack-rotate-x-normal: {{SIZE||0}}deg;'
				],
			]
		);

		$this->add_responsive_control(
			'stack_effect_rotatey_normal',
			[ 
				'label'      => esc_html__( 'Rotate Y', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 
					'px' => [ 
						'min' => -180,
						'max' => 180,
					],
				],
				'condition'  => [ 
					'stack_rotate_toggle_normal' => 'yes',
				],
				'selectors'  => [ 
					'{{WRAPPER}}' => '--ep-stack-rotate-y-normal: {{SIZE||0}}deg;'
				],
			]
		);


		$this->add_responsive_control(
			'stack_effect_rotatez_normal',
			[ 
				'label'      => __( 'Rotate Z', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 
					'px' => [ 
						'min' => -180,
						'max' => 180,
					],
				],
				'selectors'  => [ 
					'{{WRAPPER}}' => '--ep-stack-rotate-z-normal: {{SIZE||0}}deg;'
				],
				'condition'  => [ 
					'stack_rotate_toggle_normal' => 'yes',
				],
			]
		);

		$this->end_popover();

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_tooltip',
			[ 
				'label' => __( 'Tooltip', 'bdthemes-element-pack-lite' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'tooltip_width',
			[ 
				'label'       => esc_html__( 'Width', 'bdthemes-element-pack-lite' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 
					'px', 'em',
				],
				'range'       => [ 
					'px' => [ 
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors'   => [ 
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'width: {{SIZE}}{{UNIT}};',
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'tooltip_text_align',
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
			]
		);

		$this->add_control(
			'tooltip_color',
			[ 
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'color: {{VALUE}}',
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'tooltip_arrow_color',
			[ 
				'label'     => esc_html__( 'Arrow Color', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'tooltip_background',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"], .tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-backdrop',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'tooltip_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack-lite' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->add_responsive_control(
			'tooltip_border_radius',
			[ 
				'label'      => __( 'Border Radius', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tooltip_padding',
			[ 
				'label'       => __( 'Padding', 'bdthemes-element-pack-lite' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => [ 'px', '%' ],
				'selectors'   => [ 
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'render_type' => 'template',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'tooltip_typography',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'tooltip_box_shadow',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Sanitize user supplied tooltip text before it is printed into an attribute.
	 *
	 * Entities are decoded first (repeatedly, so multi-encoded payloads collapse)
	 * because `esc_attr()` does not double encode existing entities. Without the
	 * decode, a payload such as `&#X3C;img src=1 ONError&#x3d;alert(1)&#X3E;`
	 * survives `wp_strip_all_tags()` untouched and is decoded back into live
	 * markup by the browser when it parses the attribute.
	 *
	 * @param string $text Raw tooltip text.
	 * @return string Plain text, safe for `esc_attr()`.
	 */
	protected function sanitize_tooltip_text( $text ) {
		$text = (string) $text;

		// Three passes is plenty for any realistic multi-encoded payload.
		for ( $i = 0; $i < 3; $i++ ) {
			$decoded = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
			if ( $decoded === $text ) {
				break;
			}
			$text = $decoded;
		}

		return wp_strip_all_tags( $text );
	}

	protected function render_media( $item, $settings ) {
		$media_type = isset( $item['media_type'] ) ? $item['media_type'] : 'image';
		if ( $media_type === 'icon' ) {
			if ( ! empty( $item['selected_icon']['value'] ) ) {
				Icons_Manager::render_icon( $item['selected_icon'], [ 'aria-hidden' => 'true' ] );
			}
		} elseif ( $media_type === 'image' ) {
			$img_id = isset( $item['image']['id'] ) ? $item['image']['id'] : 0;
			$img_url = isset( $item['image']['url'] ) ? $item['image']['url'] : '';
			$alt_text = $this->sanitize_tooltip_text( isset( $item['tooltip_text'] ) ? $item['tooltip_text'] : '' );
			$size_name = isset( $settings['thumbnail_size_size'] ) ? $settings['thumbnail_size_size'] : 'full';

			$thumb_url = $img_id ? Group_Control_Image_Size::get_attachment_image_src( $img_id, 'thumbnail_size', $settings ) : false;
			if ( $thumb_url && $img_id ) {
				echo wp_get_attachment_image(
					$img_id,
					$size_name,
					false,
					[ 'alt' => esc_attr( $alt_text ) ]
				);
			} elseif ( $img_url !== '' ) {
				printf( '<img src="%1$s" alt="%2$s">', esc_url( $img_url ), esc_attr( $alt_text ) );
			}
		}
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$image_stack_items = isset( $settings['image_stack_items'] ) && is_array( $settings['image_stack_items'] ) ? $settings['image_stack_items'] : [];

		if ( empty( $image_stack_items ) ) {
			return;
		}
		?>
		<div class="bdt-image-stack">
			<?php foreach ( $image_stack_items as $index => $item ) :
				$item_id = isset( $item['_id'] ) ? $item['_id'] : '';
				$this->add_render_attribute( 'stack-item', 'class', 'bdt-ep-image-stack-item elementor-repeater-item-' . esc_attr( $item_id ), true );

				$tooltip = '';
				$tooltip_text = isset( $item['tooltip_text'] ) ? $item['tooltip_text'] : '';
				if ( $tooltip_text !== '' ) {
					$this->add_render_attribute( 'stack-item', 'class', [
						'bdt-ep-image-stack-item',
						'elementor-repeater-item-' . esc_attr( $item_id ),
						'bdt-tippy-tooltip',
					], true );
					$this->add_render_attribute( 'stack-item', 'data-tippy', '', true );
					$this->add_render_attribute( 'stack-item', 'data-tippy-arrow', 'true', true );
					$tooltip_placement = isset( $item['tooltip_placement'] ) ? $item['tooltip_placement'] : 'top';
					$this->add_render_attribute( 'stack-item', 'data-tippy-placement', esc_attr( $tooltip_placement ), true );

					$tooltip = $this->sanitize_tooltip_text( $tooltip_text );
				}

				$link_url = isset( $item['link_url']['url'] ) ? $item['link_url']['url'] : '';
				if ( $link_url !== '' ) {
					$this->add_link_attributes( 'link-wrap' . $index, isset( $item['link_url'] ) ? $item['link_url'] : [] );
				}
				?>
				<div <?php $this->print_render_attribute_string( 'stack-item' ); ?> data-tippy-content="<?php echo esc_attr( $tooltip ); ?>">
					<?php if ( $link_url !== '' ) : ?>
						<a <?php $this->print_render_attribute_string( 'link-wrap' . $index ); ?>>
							<?php $this->render_media( $item, $settings ); ?>
						</a>
					<?php else : ?>
						<span>
							<?php $this->render_media( $item, $settings ); ?>
						</span>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	protected function content_template() {
		?>
		<div class="bdt-image-stack">
			<# _.each( settings.image_stack_items, function( item ) {
				var itemId = item._id || '';
				var mediaType = item.media_type || 'image';
				var tooltipText = item.tooltip_text || '';
				var tooltipPlacement = item.tooltip_placement || 'top';
				var linkUrl = ( item.link_url && item.link_url.url ) ? item.link_url.url : '';
				var itemClasses = 'bdt-ep-image-stack-item elementor-repeater-item-' + itemId;
				var tooltipAttrs = '';
				if ( tooltipText ) {
					itemClasses += ' bdt-tippy-tooltip';
					tooltipAttrs = 'data-tippy="" data-tippy-arrow="true" data-tippy-placement="' + _.escape( tooltipPlacement ) + '" data-tippy-content="' + _.escape( tooltipText ) + '"';
				}
				var iconHTML = elementor.helpers.renderIcon( view, item.selected_icon, { 'aria-hidden': 'true' }, 'i', 'object' );
				var imageUrl = ( item.image && item.image.url ) ? item.image.url : '';
			#>
			<div class="{{ itemClasses }}" {{{ tooltipAttrs }}}>
				<# if ( linkUrl ) { #>
				<a href="{{ linkUrl }}">
				<# } else { #>
				<span>
				<# } #>
					<# if ( mediaType === 'icon' && iconHTML && iconHTML.rendered ) { #>
						{{{ iconHTML.value }}}
					<# } else if ( mediaType === 'image' && imageUrl ) { #>
						<img src="{{ imageUrl }}" alt="{{ tooltipText }}">
					<# } #>
				<# if ( linkUrl ) { #>
				</a>
				<# } else { #>
				</span>
				<# } #>
			</div>
			<# } ); #>
		</div>
		<?php
	}
}
