<?php

namespace ElementPack\Modules\SvgImage\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use ElementPack\Base\Module_Base;
use ElementPack\Element_Pack_Loader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Exit if accessed directly

class Svg_Image extends Module_Base {

	public function get_name() {
		return 'bdt-svg-image';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'SVG Image', 'bdthemes-element-pack-lite' );
	}

	public function get_icon() {
		return 'bdt-wi-svg-image';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'image', 'svg image', 'svg' ];
	}

		public function get_script_depends() {
		if ( $this->ep_is_edit_mode() ) {
			if ( true == element_pack_is_pro() ) {
				return [ 'draw-svg-plugin-js', 'scroll-trigger', 'ep-scripts' ];
			} else {
				return [ 'ep-scripts' ];
			}
		} else {
			if ( true == element_pack_is_pro() ) {
				return [ 'gsap', 'draw-svg-plugin-js', 'scroll-trigger', 'ep-svg-image' ];
			} else {
				return [];
			}
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/XRbjpcp5dJ0';
	}

	public function has_widget_inner_wrapper(): bool {
        return ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
    }

	protected function register_controls() {

		$this->start_controls_section(
			'section_image',
			[ 
				'label' => esc_html__( 'SVG', 'bdthemes-element-pack-lite' ),
			]
		);

		$this->add_control(
			'image',
			[ 
				'label'   => esc_html__( 'Choose SVG', 'bdthemes-element-pack-lite' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [ 
					'active' => true,
				],
				'default' => [ 
					'url' => BDTEP_ASSETS_URL . 'images/crane.svg',
				],
				'media_type' => 'svg',
			]
		);

		$this->add_responsive_control(
			'align',
			[ 
				'label'     => esc_html__( 'Alignment', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::CHOOSE,
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
					'{{WRAPPER}} .bdt-svg-image' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'caption_source',
			[ 
				'label'   => esc_html__( 'Caption', 'bdthemes-element-pack-lite' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [ 
					'none'       => esc_html__( 'None', 'bdthemes-element-pack-lite' ),
					'attachment' => esc_html__( 'Attachment Caption', 'bdthemes-element-pack-lite' ),
					'custom'     => esc_html__( 'Custom Caption', 'bdthemes-element-pack-lite' ),
				],
				'default' => 'none',
			]
		);

		$this->add_control(
			'caption',
			[ 
				'label'       => esc_html__( 'Custom Caption', 'bdthemes-element-pack-lite' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'Enter your image caption', 'bdthemes-element-pack-lite' ),
				'condition'   => [ 
					'caption_source' => 'custom',
				],
				'dynamic'     => [ 
					'active' => true,
				],
			]
		);

		$this->add_control(
			'link_to',
			[ 
				'label'   => esc_html__( 'Link', 'bdthemes-element-pack-lite' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [ 
					'none'   => esc_html__( 'None', 'bdthemes-element-pack-lite' ),
					'file'   => esc_html__( 'Media File', 'bdthemes-element-pack-lite' ),
					'custom' => esc_html__( 'Custom URL', 'bdthemes-element-pack-lite' ),
				],
			]
		);

		$this->add_control(
			'link',
			[ 
				'label'       => esc_html__( 'Link', 'bdthemes-element-pack-lite' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'bdthemes-element-pack-lite' ),
				'condition'   => [ 
					'link_to' => 'custom',
				],
				'show_label'  => false,
			]
		);

		$this->add_control(
			'view',
			[ 
				'label'   => esc_html__( 'View', 'bdthemes-element-pack-lite' ),
				'type'    => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_svg_additionl',
			[ 
				'label' => esc_html__( 'SVG Animation', 'bdthemes-element-pack-lite' ),
			]
		);

		if ( true === element_pack_is_pro() ) {

			$this->add_control(
				'svg_image_draw',
				[ 
					'label'              => esc_html__( 'Draw SVG', 'bdthemes-element-pack-lite' ) . BDTEP_NC,
					'type'               => Controls_Manager::SWITCHER,
					'label_on'           => esc_html__( 'Yes', 'bdthemes-element-pack-lite' ),
					'label_off'          => esc_html__( 'No', 'bdthemes-element-pack-lite' ),
					'return_value'       => 'yes',
					'frontend_available' => true,
					'render_type'        => 'template',
					'separator'          => 'before'
				]
			);

			$this->add_control(
				'svg_image_drawer_type',
				[ 
					'label'              => esc_html__( 'Drawer Type', 'bdthemes-element-pack-lite' ),
					'type'               => Controls_Manager::SELECT,
					'options'            => [ 
						'hover'     => esc_html__( ' On Hover', 'bdthemes-element-pack-lite' ),
						'viewport'  => esc_html__( 'On Scroll', 'bdthemes-element-pack-lite' ),
						'automatic' => esc_html__( 'Automatic', 'bdthemes-element-pack-lite' ),
					],
					'default'            => 'hover',
					'frontend_available' => true,
					'render_type'        => 'template',
					'condition'          => [ 
						'svg_image_draw' => 'yes'
					]

				]
			);
			$this->add_control(
				'svg_image_animate_trigger',
				[ 
					'label'              => esc_html__( 'When the draw should start?', 'bdthemes-element-pack-lite' ),
					'type'               => Controls_Manager::SELECT,
					'options'            => [ 
						'top'    => esc_html__( 'Top of Viewport Hits The Widget', 'bdthemes-element-pack-lite' ),
						'center' => esc_html__( 'Center of Viewport Hits The Widget', 'bdthemes-element-pack-lite' ),
						'custom' => esc_html__( 'Custom Offset', 'bdthemes-element-pack-lite' ),
					],
					'separator'          => 'before',
					'default'            => 'center',
					'label_block'        => true,
					'condition'          => [ 
						'svg_image_drawer_type' => 'automatic',
						'svg_image_draw'        => 'yes'
					],
					'frontend_available' => true,
				]
			);
			$this->add_control(
				'svg_image_anim_rev',
				[ 
					'label'              => esc_html__( 'Reset Animation on Scroll Up', 'bdthemes-element-pack-lite' ),
					'type'               => Controls_Manager::SWITCHER,
					'render_type'        => 'template',
					'default'            => 'yes',
					'condition'          => [ 
						'svg_image_drawer_type' => 'automatic',
						'svg_image_draw'        => 'yes'
					],
					'frontend_available' => true,
				]
			);
			$this->add_control(
				'svg_image_animate_offset',
				[ 
					'label'              => esc_html__( 'Offset (%)', 'bdthemes-element-pack-lite' ),
					'type'               => Controls_Manager::SLIDER,
					'default'            => [ 
						'size' => 50,
						'unit' => '%',
					],
					'frontend_available' => true,
					'condition'          => [ 
						'svg_image_draw'         => 'yes',
						'svg_image_drawer_type!' => 'hover'
					]
				]
			);

			$this->add_control(
				'svg_image_repeat',
				[ 
					'label'              => esc_html__( 'Repeat', 'bdthemes-element-pack-lite' ),
					'type'               => Controls_Manager::SWITCHER,
					'label_on'           => esc_html__( 'Yes', 'bdthemes-element-pack-lite' ),
					'label_off'          => esc_html__( 'No', 'bdthemes-element-pack-lite' ),
					'separator'          => 'before',
					'default'            => 'yes',
					'frontend_available' => true,
					'condition'          => [ 
						'svg_image_drawer_type!' => 'viewport',
						'svg_image_draw'         => 'yes'
					],
				]
			);
			$this->add_control(
				'svg_image_yoyo',
				[ 
					'label'              => esc_html__( 'Yoyo Effect', 'bdthemes-element-pack-lite' ),
					'type'               => Controls_Manager::SWITCHER,
					'condition'          => [ 
						'svg_image_drawer_type!' => 'viewport',
						'svg_image_draw'         => 'yes'
					],
					'default'            => 'yes',
					'return_value'       => 'yes',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'svg_image_animation_duration',
				[ 
					'label'              => esc_html__( 'Duration', 'bdthemes-element-pack-lite' ),
					'type'               => Controls_Manager::SLIDER,
					'description'        => esc_html__( 'Larger value means longer drawing duration.', 'bdthemes-element-pack-lite' ),
					'range'              => [ 
						'px' => [ 
							'min'  => 0,
							'max'  => 500,
							'step' => 1,
						]
					],
					'default'            => [ 
						'unit' => 'px',
						'size' => 100,
					],
					'condition'          => [ 
						'svg_image_drawer_type!' => 'viewport',
						'svg_image_draw'         => 'yes'
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'svg_image_easing',
				[ 
					'label'              => esc_html__( 'Easing', 'bdthemes-element-pack-lite' ),
					'type'               => Controls_Manager::SELECT,
					'options'            => [ 
						'none'         => esc_html__( 'None (Linear)', 'bdthemes-element-pack-lite' ),
						'power1.out'   => esc_html__( 'Power1 Out', 'bdthemes-element-pack-lite' ),
						'power1.in'    => esc_html__( 'Power1 In', 'bdthemes-element-pack-lite' ),
						'power1.inOut' => esc_html__( 'Power1 InOut', 'bdthemes-element-pack-lite' ),
						'power2.out'   => esc_html__( 'Power2 Out', 'bdthemes-element-pack-lite' ),
						'power2.in'    => esc_html__( 'Power2 In', 'bdthemes-element-pack-lite' ),
						'power2.inOut' => esc_html__( 'Power2 InOut', 'bdthemes-element-pack-lite' ),
						'power3.out'   => esc_html__( 'Power3 Out', 'bdthemes-element-pack-lite' ),
						'power3.in'    => esc_html__( 'Power3 In', 'bdthemes-element-pack-lite' ),
						'power3.inOut' => esc_html__( 'Power3 InOut', 'bdthemes-element-pack-lite' ),
						'power4.out'   => esc_html__( 'Power4 Out', 'bdthemes-element-pack-lite' ),
						'power4.inOut' => esc_html__( 'Power4 InOut', 'bdthemes-element-pack-lite' ),
						'back.out'     => esc_html__( 'Back Out', 'bdthemes-element-pack-lite' ),
						'back.inOut'   => esc_html__( 'Back InOut', 'bdthemes-element-pack-lite' ),
						'elastic.out'  => esc_html__( 'Elastic Out', 'bdthemes-element-pack-lite' ),
						'bounce.out'   => esc_html__( 'Bounce Out', 'bdthemes-element-pack-lite' ),
					],
					'default'            => 'power2.out',
					'condition'          => [ 
						'svg_image_draw' => 'yes'
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'svg_image_stagger',
				[ 
					'label'              => esc_html__( 'Stagger Delay', 'bdthemes-element-pack-lite' ),
					'type'               => Controls_Manager::SLIDER,
					'description'        => esc_html__( 'Delay between each SVG path animation.', 'bdthemes-element-pack-lite' ),
					'range'              => [ 
						'px' => [ 
							'min'  => 0,
							'max'  => 2,
							'step' => 0.05,
						]
					],
					'default'            => [ 
						'unit' => 'px',
						'size' => 0.1,
					],
					'condition'          => [ 
						'svg_image_draw' => 'yes'
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'svg_image_stagger_from',
				[ 
					'label'              => esc_html__( 'Stagger From', 'bdthemes-element-pack-lite' ),
					'type'               => Controls_Manager::SELECT,
					'options'            => [ 
						'start'  => esc_html__( 'Start', 'bdthemes-element-pack-lite' ),
						'center'  => esc_html__( 'Center', 'bdthemes-element-pack-lite' ),
						'end'     => esc_html__( 'End', 'bdthemes-element-pack-lite' ),
						'edges'   => esc_html__( 'Edges', 'bdthemes-element-pack-lite' ),
						'random'  => esc_html__( 'Random', 'bdthemes-element-pack-lite' ),
					],
					'default'            => 'start',
					'condition'          => [ 
						'svg_image_draw' => 'yes'
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'svg_image_draw_live',
				[ 
					'label'              => esc_html__( 'Responsive (Live)', 'bdthemes-element-pack-lite' ),
					'type'               => Controls_Manager::SWITCHER,
					'description'        => esc_html__( 'Recalculate path length on resize for responsive SVGs.', 'bdthemes-element-pack-lite' ),
					'default'            => 'no',
					'condition'          => [ 
						'svg_image_draw' => 'yes'
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'svg_image_scrub',
				[ 
					'label'              => esc_html__( 'Scrub Smoothness', 'bdthemes-element-pack-lite' ),
					'type'               => Controls_Manager::SLIDER,
					'description'        => esc_html__( 'Higher value = smoother scroll-linked animation. 0 = instant.', 'bdthemes-element-pack-lite' ),
					'range'              => [ 
						'px' => [ 
							'min'  => 0,
							'max'  => 3,
							'step' => 0.1,
						]
					],
					'default'            => [ 
						'unit' => 'px',
						'size' => 1,
					],
					'condition'          => [ 
						'svg_image_draw'         => 'yes',
						'svg_image_drawer_type' => 'viewport',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'svg_image_scroll_length',
				[ 
					'label'              => esc_html__( 'Scroll Length (px)', 'bdthemes-element-pack-lite' ),
					'type'               => Controls_Manager::SLIDER,
					'description'        => esc_html__( 'Scroll distance to complete the draw animation.', 'bdthemes-element-pack-lite' ),
					'range'              => [ 
						'px' => [ 
							'min'  => 100,
							'max'  => 1500,
							'step' => 50,
						]
					],
					'default'            => [ 
						'unit' => 'px',
						'size' => 600,
					],
					'condition'          => [ 
						'svg_image_draw'         => 'yes',
						'svg_image_drawer_type' => 'viewport',
					],
					'frontend_available' => true,
				]
			);
			$this->add_control(
				'svg_image_animation_start_point',
				[ 
					'label'              => esc_html__( 'Start Point (%)', 'bdthemes-element-pack-lite' ),
					'type'               => Controls_Manager::SLIDER,
					'description'        => esc_html__( 'Set the point that the SVG should start from.', 'bdthemes-element-pack-lite' ),
					'default'            => [ 
						'unit' => '%',
						'size' => 0,
					],
					'condition'          => [ 
						'svg_image_draw' => 'yes'
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'svg_image_animation_end_point',
				[ 
					'label'              => esc_html__( 'End Point (%)', 'bdthemes-element-pack-lite' ),
					'type'               => Controls_Manager::SLIDER,
					'description'        => esc_html__( 'Set the point that the SVG should end at.', 'bdthemes-element-pack-lite' ),
					'default'            => [ 
						'unit' => '%',
						'size' => 100,
					],
					'condition'          => [ 
						'svg_image_draw' => 'yes'
					],
					'frontend_available' => true,
					'separator'          => 'after'
				]
			);
		}

		if ( true !== element_pack_is_pro() ) {
			$this->add_control(
				'on_hover_animation',
				[ 
					'label'       => esc_html__( 'On Hover Animation', 'bdthemes-element-pack-lite' ),
					'description' => esc_html__( 'Make sure you select a stroke based svg image, otherwise hover animation will not work.', 'bdthemes-element-pack-lite' ),
					'type'        => Controls_Manager::SWITCHER,
					'separator'   => 'before',
					// 'condition' => [
					// 	'svg_image_draw!' => 'yes'
					// ]
				]
			);

			$this->add_control(
				'on_hover_reverse_animation',
				[ 
					'label'     => esc_html__( 'Reverse Animation', 'bdthemes-element-pack-lite' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => [ 
						'on_hover_animation' => 'yes',
					],
					'separator' => 'after',
				]
			);
			$this->add_control(
				'svg_parallax_effects_show',
				[ 
					'label'       => esc_html__( 'Stroke Parallax Animation', 'bdthemes-element-pack-lite' ),
					'description' => esc_html__( 'Make sure you select a stroke based svg image, otherwise parallax stroke animation will not work.', 'bdthemes-element-pack-lite' ),
					'type'        => Controls_Manager::SWITCHER,
					'separator'   => 'before',
					// 'condition' => [
					// 	'svg_image_draw!' => 'yes'
					// ]
				]
			);

			$this->add_control(
				'parallax_effects_stroke_value',
				[ 
					'label'       => esc_html__( 'Stroke Start Point', 'bdthemes-element-pack-lite' ),
					'description' => esc_html__( 'Set your stroke start value where from you start the stroke parallax.', 'bdthemes-element-pack-lite' ),
					'type'        => Controls_Manager::SLIDER,
					'range'       => [ 
						'%' => [ 
							'min'  => 0,
							'max'  => 100,
							'step' => 1,
						],
					],
					'default'     => [ 
						'unit' => '%',
						'size' => 0,
					],
					'condition'   => [ 
						'svg_parallax_effects_show' => 'yes',
					],
				]
			);

			$this->add_control(
				'parallax_effects_viewport_value',
				[ 
					'label'     => esc_html__( 'Viewport', 'bdthemes-element-pack-lite' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [ 
						'px' => [ 
							'min'  => 0.1,
							'max'  => 1,
							'step' => 0.1,
						],
					],
					'default'   => [ 
						'unit' => 'px',
						'size' => 0.7,
					],
					'condition' => [ 
						'svg_parallax_effects_show' => 'yes',
					],
				]
			);
		}

		$this->end_controls_section();


		//Style
		$this->start_controls_section(
			'section_style_image',
			[ 
				'label' => esc_html__( 'SVG', 'bdthemes-element-pack-lite' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'svg_color_preserved',
			[ 
				'label' => esc_html__( 'Preserved Original Color', 'bdthemes-element-pack-lite' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'svg_fill_color',
			[ 
				'label'     => esc_html__( 'Fill Color', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .elementor-image svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'svg_stroke_color',
			[ 
				'label'     => esc_html__( 'Stroke Color', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .elementor-image svg *' => 'stroke: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'width',
			[ 
				'label'          => esc_html__( 'Width', 'bdthemes-element-pack-lite' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [ 
					'unit' => '%',
				],
				'tablet_default' => [ 
					'unit' => '%',
				],
				'mobile_default' => [ 
					'unit' => '%',
				],
				'size_units'     => [ '%', 'px', 'vw' ],
				'range'          => [ 
					'%'  => [ 
						'min' => 1,
						'max' => 100,
					],
					'px' => [ 
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [ 
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'      => [ 
					'{{WRAPPER}} .bdt-svg-image svg' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'space',
			[ 
				'label'          => esc_html__( 'Max Width', 'bdthemes-element-pack-lite' ) . ' (%)',
				'type'           => Controls_Manager::SLIDER,
				'default'        => [ 
					'unit' => '%',
				],
				'tablet_default' => [ 
					'unit' => '%',
				],
				'mobile_default' => [ 
					'unit' => '%',
				],
				'size_units'     => [ '%' ],
				'range'          => [ 
					'%' => [ 
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'      => [ 
					'{{WRAPPER}} .bdt-svg-image svg' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'separator_panel_style',
			[ 
				'type'  => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->start_controls_tabs( 'image_effects' );

		$this->start_controls_tab(
			'normal',
			[ 
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack-lite' ),
			]
		);

		$this->add_control(
			'opacity',
			[ 
				'label'     => esc_html__( 'Opacity', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-svg-image svg' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[ 
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .bdt-svg-image svg',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover',
			[ 
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack-lite' ),
			]
		);

		$this->add_control(
			'opacity_hover',
			[ 
				'label'     => esc_html__( 'Opacity', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-svg-image:hover svg' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[ 
				'name'     => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .bdt-svg-image:hover svg',
			]
		);

		$this->add_control(
			'background_hover_transition',
			[ 
				'label'     => esc_html__( 'Transition Duration', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'max'  => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-svg-image svg' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'      => 'image_border',
				'selector'  => '{{WRAPPER}} .bdt-svg-image svg',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-svg-image svg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'image_box_shadow',
				'exclude'  => [ // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor control option, not WP_Query.
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .bdt-svg-image svg',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_caption',
			[ 
				'label'     => esc_html__( 'Caption', 'bdthemes-element-pack-lite' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'caption_source!' => 'none',
				],
			]
		);

		$this->add_control(
			'caption_align',
			[ 
				'label'     => esc_html__( 'Alignment', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [ 
					'left'    => [ 
						'title' => esc_html__( 'Left', 'bdthemes-element-pack-lite' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [ 
						'title' => esc_html__( 'Center', 'bdthemes-element-pack-lite' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [ 
						'title' => esc_html__( 'Right', 'bdthemes-element-pack-lite' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [ 
						'title' => esc_html__( 'Justified', 'bdthemes-element-pack-lite' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'default'   => '',
				'selectors' => [ 
					'{{WRAPPER}} .widget-image-caption' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_color',
			[ 
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [ 
					'{{WRAPPER}} .widget-image-caption' => 'color: {{VALUE}};',
				],

				// 'scheme' => [

				//     'type' => Schemes\Color::get_type(),

				//     'value' => Schemes\Color::COLOR_3,
				// ],
			]
		);

		$this->add_control(
			'caption_background_color',
			[ 
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .widget-image-caption' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'caption_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack-lite' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .widget-image-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'caption_typography',
				'selector' => '{{WRAPPER}} .widget-image-caption',
				//'scheme' => Schemes\Typography::TYPOGRAPHY_3,
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[ 
				'name'     => 'caption_text_shadow',
				'selector' => '{{WRAPPER}} .widget-image-caption',
			]
		);

		$this->add_responsive_control(
			'caption_space',
			[ 
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack-lite' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .widget-image-caption' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function has_caption( $settings ) {
		return ( ! empty( $settings['caption_source'] ) && 'none' !== $settings['caption_source'] );
	}

	private function get_caption( $settings ) {
		$caption = '';

		if ( ! empty( $settings['caption_source'] ) ) {

			switch ( $settings['caption_source'] ) {
				case 'attachment':
					$caption = wp_get_attachment_caption( $settings['image']['id'] );
					break;
				case 'custom':
					$caption = ! Utils::is_empty( $settings['caption'] ) ? $settings['caption'] : '';
			}
		}

		return $caption;
	}

	/**
	 * Elements permitted in inline SVG output.
	 *
	 * This is a strict allowlist: anything not named here is dropped together
	 * with its subtree, so scripting, external references and embedded content
	 * cannot reach the page even when a new vector appears in a future browser.
	 * Note the deliberate omissions — script, style, foreignObject, use, image,
	 * a, animate, set, handler, audio, video, iframe and embed all either run
	 * code or pull in external resources.
	 *
	 * @return array Lowercase element names.
	 */
	private function allowed_svg_tags() {
		return array(
			'svg', 'g', 'defs', 'symbol', 'title', 'desc', 'metadata', 'switch',
			'path', 'rect', 'circle', 'ellipse', 'line', 'polyline', 'polygon',
			'text', 'tspan',
			'lineargradient', 'radialgradient', 'stop',
			'clippath', 'mask', 'pattern', 'marker',
			'filter', 'fegaussianblur', 'feoffset', 'feblend', 'feflood',
			'femerge', 'femergenode', 'fecolormatrix', 'fecomposite',
			'fedropshadow',
		);
	}

	/**
	 * Attributes permitted on the elements above.
	 *
	 * Presentation and geometry only. Every attribute that can reference an
	 * external document (href, xlink:href, src) is absent, as is every event
	 * handler — they are rejected by not being listed, not by pattern matching.
	 *
	 * @return array Lowercase attribute names.
	 */
	private function allowed_svg_attributes() {
		return array(
			// Identity and layout.
			'id', 'class', 'style', 'transform', 'transform-origin',
			'x', 'y', 'x1', 'y1', 'x2', 'y2', 'cx', 'cy', 'r', 'rx', 'ry',
			'width', 'height', 'd', 'points', 'dx', 'dy', 'rotate',
			'viewbox', 'preserveaspectratio', 'overflow', 'version', 'baseprofile',
			'xmlns', 'xmlns:xlink', 'xml:space',
			// Painting.
			'fill', 'fill-opacity', 'fill-rule',
			'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin',
			'stroke-dasharray', 'stroke-dashoffset', 'stroke-opacity', 'stroke-miterlimit',
			'opacity', 'color', 'display', 'visibility', 'paint-order',
			'vector-effect', 'shape-rendering', 'clip-path', 'clip-rule', 'mask', 'filter',
			// Gradients, patterns, markers, masks.
			'offset', 'stop-color', 'stop-opacity',
			'gradientunits', 'gradienttransform', 'spreadmethod',
			'patternunits', 'patterncontentunits', 'patterntransform',
			'clippathunits', 'maskunits', 'maskcontentunits',
			'markerwidth', 'markerheight', 'markerunits', 'orient', 'refx', 'refy',
			// Filter primitives.
			'filterunits', 'primitiveunits', 'result', 'in', 'in2', 'mode',
			'stddeviation', 'flood-color', 'flood-opacity', 'values', 'type',
			'operator', 'k1', 'k2', 'k3', 'k4',
			// Text.
			'font-family', 'font-size', 'font-weight', 'font-style',
			'text-anchor', 'dominant-baseline', 'letter-spacing', 'word-spacing',
			'writing-mode',
		);
	}

	/**
	 * Parse raw SVG markup and rebuild it from an allowlist of elements and
	 * attributes, then tag the root with the widget's own classes.
	 *
	 * Everything outside the allowlist is discarded rather than patched, so the
	 * output can only ever contain constructs this widget explicitly permits.
	 *
	 * @param string $svg_content Raw SVG string read from the media library.
	 * @return string Sanitized SVG markup, or an empty string if it cannot be trusted.
	 */
	private function sanitize_svg( $svg_content ) {
		if ( ! is_string( $svg_content ) || '' === trim( $svg_content ) || ! class_exists( 'DOMDocument' ) ) {
			return '';
		}

		$previous_errors = libxml_use_internal_errors( true );

		$dom    = new \DOMDocument();
		$loaded = $dom->loadXML( $svg_content, LIBXML_NONET );

		libxml_clear_errors();
		libxml_use_internal_errors( $previous_errors );

		// A DOCTYPE can carry an internal subset declaring entities; refuse the
		// whole document rather than reason about entity expansion.
		if ( ! $loaded || ! $dom->documentElement || $dom->doctype ) {
			return '';
		}

		if ( 'svg' !== strtolower( $dom->documentElement->nodeName ) ) {
			return '';
		}

		$root = $dom->documentElement;

		$this->clean_svg_attributes( $root );
		$this->clean_svg_children( $root );

		$root->setAttribute( 'class', trim( $root->getAttribute( 'class' ) . ' bdt-svg-image-inner' ) );
		$root->setAttribute( 'data-bdt-svg', 'stroke-animation: true' );

		$markup = $dom->saveXML( $root );

		return is_string( $markup ) ? $markup : '';
	}

	/**
	 * Recursively drop every child element outside the allowlist, along with
	 * comments and processing instructions.
	 *
	 * @param \DOMNode $node Node whose children are filtered.
	 * @return void
	 */
	private function clean_svg_children( \DOMNode $node ) {
		$allowed_tags = $this->allowed_svg_tags();

		// Iterate a snapshot: the live NodeList shifts as children are removed.
		foreach ( iterator_to_array( $node->childNodes ) as $child ) {
			if ( XML_COMMENT_NODE === $child->nodeType || XML_PI_NODE === $child->nodeType ) {
				$node->removeChild( $child );
				continue;
			}

			if ( XML_ELEMENT_NODE !== $child->nodeType ) {
				// Text and CDATA are inert once the element allowlist holds.
				continue;
			}

			if ( ! in_array( strtolower( $child->nodeName ), $allowed_tags, true ) ) {
				$node->removeChild( $child );
				continue;
			}

			$this->clean_svg_attributes( $child );
			$this->clean_svg_children( $child );
		}
	}

	/**
	 * Strip every attribute outside the allowlist and reject values that point
	 * anywhere other than this same document.
	 *
	 * @param \DOMElement $element Element to filter in place.
	 * @return void
	 */
	private function clean_svg_attributes( \DOMElement $element ) {
		$allowed_attributes = $this->allowed_svg_attributes();
		$remove             = array();
		$rewrite            = array();

		foreach ( $element->attributes as $attribute ) {
			$name  = strtolower( $attribute->nodeName );
			$value = $attribute->nodeValue;

			if ( ! in_array( $name, $allowed_attributes, true ) ) {
				$remove[] = $attribute->nodeName;
				continue;
			}

			if ( 'style' === $name ) {
				$safe_style = $this->sanitize_svg_style( $value );

				if ( '' === $safe_style ) {
					$remove[] = $attribute->nodeName;
				} elseif ( $safe_style !== $value ) {
					$rewrite[ $attribute->nodeName ] = $safe_style;
				}

				continue;
			}

			// Presentation attributes such as fill or clip-path accept url();
			// only same-document fragments are allowed.
			if ( ! $this->svg_url_references_are_local( $value ) ) {
				$remove[] = $attribute->nodeName;
			}
		}

		foreach ( $rewrite as $name => $value ) {
			$element->setAttribute( $name, $value );
		}

		foreach ( $remove as $name ) {
			$element->removeAttribute( $name );
		}
	}

	/**
	 * Validate an inline style attribute.
	 *
	 * @param string $value Raw style attribute value.
	 * @return string The value if it is safe, otherwise an empty string.
	 */
	private function sanitize_svg_style( $value ) {
		if ( ! is_string( $value ) || '' === trim( $value ) ) {
			return '';
		}

		if ( preg_match( '/(@import|expression\s*\(|behaviou?r\s*:|javascript\s*:|-moz-binding|<)/i', $value ) ) {
			return '';
		}

		if ( ! $this->svg_url_references_are_local( $value ) ) {
			return '';
		}

		return $value;
	}

	/**
	 * Check that every url() in a value is a same-document fragment such as
	 * url(#gradient), never a remote or data URI.
	 *
	 * @param string $value Attribute or declaration value.
	 * @return bool True when no external reference is present.
	 */
	private function svg_url_references_are_local( $value ) {
		if ( ! is_string( $value ) || false === stripos( $value, 'url(' ) ) {
			return true;
		}

		if ( ! preg_match_all( '/url\s*\(\s*([\'"]?)([^)\'"]*)\1\s*\)/i', $value, $matches ) ) {
			// A malformed url() we cannot parse is not one we should trust.
			return false;
		}

		foreach ( $matches[2] as $target ) {
			$target = trim( $target );

			if ( '' === $target || '#' !== $target[0] ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Read an SVG attachment from this site's media library.
	 *
	 * Only local attachments are ever read. Remote URLs are deliberately not
	 * fetched: inlining markup from an address the editor controls would let
	 * arbitrary third-party content execute in the page's origin.
	 *
	 * @param int $attachment_id Media library attachment ID.
	 * @return string File contents, or an empty string when unavailable.
	 */
	private function get_local_svg_contents( $attachment_id ) {
		$attachment_id = absint( $attachment_id );

		if ( ! $attachment_id || 'image/svg+xml' !== get_post_mime_type( $attachment_id ) ) {
			return '';
		}

		$path = get_attached_file( $attachment_id );

		if ( ! $path ) {
			return '';
		}

		$upload_dir = wp_upload_dir();
		$real_path  = realpath( $path );
		$real_base  = empty( $upload_dir['basedir'] ) ? false : realpath( $upload_dir['basedir'] );

		// Confine reads to the uploads directory, whatever the stored path says.
		if ( false === $real_path || false === $real_base
			|| 0 !== strpos( $real_path, $real_base . DIRECTORY_SEPARATOR )
			|| ! is_file( $real_path ) ) {
			return '';
		}

		// Guard against pathological files; inline SVG icons are tiny.
		$max_bytes = (int) apply_filters( 'element_pack_svg_image_max_bytes', 512 * 1024 );

		if ( filesize( $real_path ) > $max_bytes ) {
			return '';
		}

		$contents = file_get_contents( $real_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading a validated local file, not a remote request.

		return is_string( $contents ) ? $contents : '';
	}

	public function render_svg() {
		$settings    = $this->get_settings_for_display();
		$svg_url     = isset( $settings['image']['url'] ) ? $settings['image']['url'] : '';
		$svg_id      = isset( $settings['image']['id'] ) ? $settings['image']['id'] : 0;
		$svg_content = $this->sanitize_svg( $this->get_local_svg_contents( $svg_id ) );

		if ( '' !== $svg_content ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rebuilt from the element/attribute allowlist in sanitize_svg().
			echo $svg_content;

			return;
		}

		// Anything we will not inline still renders as a plain image.
		if ( '' === $svg_url ) {
			return;
		}

		echo '<img src="' . esc_url( $svg_url ) . '" alt="' . esc_attr( get_the_title() ) . '" class="bdt-svg-image-inner" data-bdt-svg="stroke-animation: true" />';
	}

	public function render_image() {
		$settings = $this->get_settings_for_display();

		if ( true !== element_pack_is_pro() ) {
			if ( $settings['on_hover_animation'] ) {
				$this->add_render_attribute( 'svg-image', 'class', 'bdt-animation-stroke' );
				$this->add_render_attribute( 'svg-image', 'data-bdt-svg', 'stroke-animation: true' );
			}

			if ( $settings['on_hover_reverse_animation'] ) {
				$this->add_render_attribute( 'svg-image', 'class', 'bdt-animation-reverse' );
			}
		}

		if ( $settings['svg_color_preserved'] ) {
			$this->add_render_attribute( 'svg-image', 'class', 'bdt-preserve' );
		}

		$this->add_render_attribute( 'svg-image', 'data-bdt-svg', '' );

		if ( $settings['image']['id'] ) {
			$image = wp_get_attachment_image_src( $settings['image']['id'], 'full' );
			printf( '<img %3$s src="%1$s" alt="%2$s">', esc_url( $image[0] ), esc_html( get_the_title() ), wp_kses_post( $this->get_render_attribute_string( 'svg-image' ) ) );
		} else {
			printf( '<img %3$s src="%1$s" alt="%2$s">', esc_url( BDTEP_ASSETS_URL ) . 'images/crane.svg', esc_html( get_the_title() ), wp_kses_post( $this->get_render_attribute_string( 'svg-image' ) ) );
		}
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['image']['url'] ) ) {
			return;
		}
		$has_caption = $this->has_caption( $settings );
		$this->add_render_attribute( 'wrapper', 'class', 'elementor-image bdt-svg-image bdt-animation-toggle' );

		if ( true !== element_pack_is_pro() ) {
			$parallax_stroke   = 100 - ( isset( $settings['parallax_effects_stroke_value']['size'] ) ? $settings['parallax_effects_stroke_value']['size'] : 0 );
			$parallax_viewport = ( isset( $settings['parallax_effects_viewport_value']['size'] ) ? $settings['parallax_effects_viewport_value']['size'] : 0.7 );
			if ( $settings['svg_parallax_effects_show'] ) {
				$this->add_render_attribute( 'wrapper', 'bdt-parallax', 'stroke: ' . $parallax_stroke . '%;' );
				$this->add_render_attribute( 'wrapper', 'bdt-parallax', 'viewport: ' . $parallax_viewport . ';' );
			}
		}

		if ( ! empty( $settings['shape'] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'elementor-image-shape-' . $settings['shape'] );
		}
		$link = $this->get_link_url( $settings );
		if ( $link ) {

			$this->add_render_attribute( 'link', 'data-elementor-open-lightbox', 'no' );

			if ( Element_Pack_Loader::elementor()->editor->is_edit_mode() ) {
				$this->add_render_attribute( 'link', [ 
					'class' => 'elementor-clickable',
				] );
			}

			$this->add_link_attributes( 'link', $link );
		}
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( $has_caption ) : ?>
				<figure class="ep-caption">
				<?php endif; ?>
				<?php if ( $link ) : ?>
					<a <?php $this->print_render_attribute_string( 'link' ); ?>>
					<?php endif; ?>
					<?php if ( isset( $settings['svg_image_draw'] ) && 'yes' === $settings['svg_image_draw'] ) {
						$this->render_svg();
					} else {
						$this->render_image();
					} ?>
					<?php if ( $link ) : ?>
					</a>
				<?php endif; ?>
				<?php
				if ( $has_caption ) : ?>
					<figcaption class="widget-image-caption ep-caption-text">
						<?php echo wp_kses_post( $this->get_caption( $settings ) ); ?>
					</figcaption>
				<?php endif; ?>
				<?php
				if ( $has_caption ) : ?>
				</figure>
			<?php endif; ?>
		</div>
		<?php
	}

	private function get_link_url( $settings ) {

		if ( 'none' === $settings['link_to'] ) {
			return false;
		}

		if ( 'custom' === $settings['link_to'] ) {

			if ( empty( $settings['link']['url'] ) ) {
				return false;
			}

			return $settings['link'];
		}

		return [ 
			'url' => $settings['image']['url'],
		];
	}
}