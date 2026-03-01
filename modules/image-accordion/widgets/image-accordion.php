<?php
namespace ElementPack\Modules\ImageAccordion\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Repeater;
use ElementPack\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Image_Accordion extends Module_Base {

	public function get_name() {
		return 'bdt-image-accordion';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Image Accordion', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-image-accordion';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'fancy', 'effects', 'image', 'accordion', 'hover', 'slideshow', 'slider', 'box', 'animated boxs' ];
	}

	public function is_reload_preview_required() {
		return false;
	}

	public function get_style_depends() {
		return $this->ep_is_edit_mode() ? [ 'ep-styles' ] : [ 'ep-font', 'ep-image-accordion' ];
	}

	public function get_script_depends() {
		return $this->ep_is_edit_mode() ? [ 'ep-scripts' ] : [ 'ep-image-accordion' ];
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/jQWU4kxXJpM';
	}

	public function has_widget_inner_wrapper(): bool {
        return ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
    }
	protected function is_dynamic_content(): bool {
		return false;
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_accordion_item',
			[
				'label' => __( 'Image Accordion', 'bdthemes-element-pack' ),
			]
		);

		$this->add_responsive_control(
			'skin_type',
			[
				'label'	   => __( 'Style', 'bdthemes-element-pack' ),
				'type' 	   => Controls_Manager::SELECT,
				'options'  => [
					'default' 	=> __( 'Horizontal', 'bdthemes-element-pack' ),
					'vertical' 	=> __( 'Vertical', 'bdthemes-element-pack' ),
					'sliding-box' 	=> __( 'Sliding Box', 'bdthemes-element-pack' ),
				],
				'default'  => 'default',
				'tablet_default'  => 'default',
				'mobile_default'  => 'default',
				'prefix_class' => 'skin-%s-',
				'selectors_dictionary' => [
                    'default' => 'flex-direction: unset;',
                    'vertical' => 'flex-direction: column;',
                    'sliding-box' => 'flex-direction: unset;',
                ],
				'selectors' => [
                    '{{WRAPPER}} .bdt-ep-image-accordion' => '{{VALUE}};',
                ],
                'render_type'     => 'template',
                'style_transfer'  => true,
			]
		);

		$this->add_control(
			'hr_divider',
			[
				'type' 	   => Controls_Manager::DIVIDER,
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'items_tabs_controls' );

		$repeater->start_controls_tab(
			'tab_item_content',
			[
				'label' => __( 'Content', 'bdthemes-element-pack' ),
			]
		);

		$repeater->add_control(
			'image_accordion_title', 
			[
				'label'       => __( 'Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'default'     => __( 'Tab Title' , 'bdthemes-element-pack' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'image_accordion_sub_title', 
			[
				'label'       => __( 'Sub Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'image_accordion_button', 
			[
				'label'       => esc_html__( 'Button Text', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Read More' , 'bdthemes-element-pack' ),
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'button_link', 
			[
				'label'         => esc_html__( 'Button Link', 'bdthemes-element-pack' ),
				'type'          => Controls_Manager::URL,
				'default'       => ['url' => '#'],
				'show_external' => false,
				'dynamic'       => [ 'active' => true ],
				'condition'     => [
					'image_accordion_button!' => ''
				]
			]
		);

		$repeater->add_control(
			'slide_image', 
			[
				'label'   => esc_html__( 'Background Image', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [ 'active' => true ],
				'default' => [
					'url' => BDTEP_ASSETS_URL . 'images/gallery/item-'.rand(1,4).'.svg',
				],
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'tab_item_content_optional',
			[
				'label' => __( 'Optional', 'bdthemes-element-pack' ),
			]
		);

		$repeater->add_control(
			'title_link', 
			[
				'label'         => esc_html__( 'Title Link', 'bdthemes-element-pack' ),
				'type'          => Controls_Manager::URL,
				'default'       => ['url' => ''],
				'show_external' => false,
				'dynamic'       => [ 'active' => true ],
				'condition'     => [
					'image_accordion_title!' => ''
				]
			]
		);

		$repeater->add_control(
			'image_accordion_text', 
			[
				'type'       => Controls_Manager::WYSIWYG,
				'dynamic'    => [ 'active' => true ],
				'default'    => __( 'Box Content', 'bdthemes-element-pack' ),
			]
		);

		$repeater->end_controls_tab();
		
		$repeater->end_controls_tabs();

		$this->add_control(
			'image_accordion_items',
			[
				'label'   => esc_html__( 'Items', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'image_accordion_sub_title'   => __( 'This is a label', 'bdthemes-element-pack' ),
						'image_accordion_title'   	  => __( 'Image Accordion One', 'bdthemes-element-pack' ),
						'image_accordion_text' 	  => __( 'Lorem ipsum dolor sit amet consect voluptate repell endus kilo gram magni illo ea animi.', 'bdthemes-element-pack' ),
						'slide_image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-1.svg']
					],
					[
						'image_accordion_sub_title'   => __( 'This is a label', 'bdthemes-element-pack' ),
						'image_accordion_title'   => __( 'Image Accordion Two', 'bdthemes-element-pack' ),
						'image_accordion_text' => __( 'Lorem ipsum dolor sit amet consect voluptate repell endus kilo gram magni illo ea animi.', 'bdthemes-element-pack' ),
						'slide_image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-2.svg']
					],
					[
						'image_accordion_sub_title'   => __( 'This is a label', 'bdthemes-element-pack' ),
						'image_accordion_title'   => __( 'Image Accordion Three', 'bdthemes-element-pack' ),
						'image_accordion_text' => __( 'Lorem ipsum dolor sit amet consect voluptate repell endus kilo gram magni illo ea animi.', 'bdthemes-element-pack' ),
						'slide_image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-3.svg']
					],
					[
						'image_accordion_sub_title'   => __( 'This is a label', 'bdthemes-element-pack' ),
						'image_accordion_title'   => __( 'Image Accordion Four', 'bdthemes-element-pack' ),
						'image_accordion_text' => __( 'Lorem ipsum dolor sit amet consect voluptate repell endus kilo gram magni illo ea animi.', 'bdthemes-element-pack' ),
						'slide_image' => ['url' => BDTEP_ASSETS_URL . 'images/gallery/item-4.svg']
					],
				],
				'title_field' => '{{{ image_accordion_title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout_hover_box',
			[
				'label' => esc_html__( 'Additional Settings', 'bdthemes-element-pack' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'image_accordion_min_height',
			[
				'label' => esc_html__('Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'vh'],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1200,
					],
					'em' => [
						'min' => 10,
						'max' => 100,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_accordion_width',
			[
				'label' => esc_html__('Content Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion-content' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'         => 'thumbnail_size',
				'label'        => esc_html__( 'Image Size', 'bdthemes-element-pack' ),
				'exclude'      => [ 'custom' ],
				'default'      => 'full',
				'separator' => 'before'
			]
		);

		$this->add_control(
            'image_accordion_event',
            [
                'label'   => __('Select Event', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'mouseover',
                'options' => [
                    'click'     => __('Click', 'bdthemes-element-pack'),
                    'mouseover' => __('Hover', 'bdthemes-element-pack'),
                ],
            ]
		);

		$this->add_control(
			'swiping',
			[
				'label'   => esc_html__('Touch Screen Swiping', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				//'separator' => 'before'
			]
		);

		$this->add_control(
            'divider_hr',
            [
                'type'    => Controls_Manager::DIVIDER,
            ]
		);

		$this->add_responsive_control(
			'items_content_position',
			[
				'label'   => __( 'Content Position', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'row',
				'options' => [
					'row-reverse' => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-left',
					],
					'row' => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion-item' => 'flex-direction: {{VALUE}};',
				],
				'prefix_class' => 'ep-img-position--',
                'render_type'     => 'template',
                'style_transfer'  => true,
				'condition' => [
					'skin_type' => 'sliding-box'
				]
			]
		);

		$this->add_responsive_control(
			'items_content_align',
			[
				'label'   => __( 'Text Alignment', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-stretch',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion-item' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'items_content_vertical_align',
			[
				'label'   => __( 'Vertical Alignment', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => __( 'Top', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'flex-end' => [
						'title' => __( 'Bottom', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion-content' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'active_item',
			[
				'label'   => esc_html__( 'Active Item', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'active_item_number',
			[
				'label'       => __( 'Item Number', 'bdthemes-element-pack' ),
				'description' => __( 'Type your item number', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::NUMBER,
				'default'	  => 1,
				'condition' => [
					'active_item' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'active_item_expand',
			[
				'label' => esc_html__('Active Item Column Expand', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'default' => [
					'size' => 6
				],
				'tablet_default' => [
					'size' => 6,
				],
				'mobile_default' => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion-item.active' => 'flex: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'inactive_item_overlay',
			[
				'label'   => esc_html__( 'In-active Item Overlay', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-ep-inactive-item-overlay-',
				'render_type'  => 'template',
				// 'condition' => [
				// 	'skin_type!' => 'sliding-box'
				// ]
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'   => esc_html__( 'Show Title', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'title_tags',
			[
				'label'   => __( 'Title HTML Tag', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => element_pack_title_tags(),
				'condition' => [
					'show_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_sub_title',
			[
				'label'   => esc_html__( 'Show Sub Title', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_text',
			[
				'label'   => esc_html__( 'Show Text', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_button',
			[
				'label'   => esc_html__( 'Show Button', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		//Lightbox
		$this->add_control(
			'show_lightbox',
			[
				'label'   => esc_html__('Show Lightbox', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'hide_on_mobile_title',
			[
				'label'   => esc_html__( 'Title Hide on Mobile', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'show_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'hide_on_mobile_sub_title',
			[
				'label'   => esc_html__( 'Sub Title Hide on Mobile', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'show_sub_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'hide_on_mobile_text',
			[
				'label'   => esc_html__( 'Text Hide on Mobile', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'show_text' => 'yes'
				]
			]
		);

		$this->add_control(
			'hide_on_mobile_button',
			[
				'label'   => esc_html__( 'Button Hide on Mobile', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'show_button' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		//Lightbox
		$this->start_controls_section(
			'section_accordion_lightbox',
			[
				'label' => __( 'Lightbox', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'show_lightbox' => 'yes',
				]
			]
		);

		$this->add_control(
			'link_type',
			[
				'label'   => esc_html__('Link Type', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => [
					'icon' => esc_html__('Icon', 'bdthemes-element-pack'),
					'text' => esc_html__('Text', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'icon',
			[
				'label'   => esc_html__('Icon', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'plus',
				'options' => [
					'search' => [
						'icon' => 'eicon-search',
					],
					'plus-circle' => [
						'icon' => 'eicon-plus-circle-o',
					],
					'plus' => [
						'icon' => 'eicon-plus',
					],
					'link' => [
						'icon' => 'eicon-link',
					],
					'play-circle' => [
						'icon' => 'eicon-play',
					],
					'play' => [
						'icon' => 'eicon-caret-right',
					],
				],
				'conditions' => [
					'terms'    => [
						[
							'name'     => 'link_type',
							'value'    => 'icon'
						]
					]
				]
			]
		);

		$this->add_control(
			'link_text',
			[
				'label'       => esc_html__('Link Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('ZOOM', 'bdthemes-element-pack'),
				'condition' => [
					'link_type'		=> 'text',
				],
				'label_block' => false
			]
		);

		$this->add_control(
			'lightbox_animation',
			[
				'label'   => esc_html__('Lightbox Animation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'slide' => esc_html__('Slide', 'bdthemes-element-pack'),
					'fade'  => esc_html__('Fade', 'bdthemes-element-pack'),
					'scale' => esc_html__('Scale', 'bdthemes-element-pack'),
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'lightbox_autoplay',
			[
				'label'   => __('Lightbox Autoplay', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				
			]
		);

		$this->add_control(
			'lightbox_pause',
			[
				'label'   => __('Lightbox Pause on Hover', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'lightbox_autoplay' => 'yes'
				],

			]
		);

		$this->add_control(
			'lightbox_placement',
			[
				'label'     => esc_html__( 'Position', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'top-right',
				'options'   => [
					'top-left'    => esc_html__( 'Top Left', 'bdthemes-element-pack' ),
					'top-right'          => esc_html__( 'Top Right', 'bdthemes-element-pack' ),
					'bottom-left' => esc_html__( 'Bottom Left', 'bdthemes-element-pack' ),
					'bottom-right'   => esc_html__( 'Bottom Right', 'bdthemes-element-pack' ),
				],
				'selectors_dictionary' => [
					'top-left' => 'left: 0;',
					'top-right' => 'right: 0;',
					'bottom-left' => 'left: 0; bottom: 0;',
					'bottom-right' => 'right: 0; bottom: 0;',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion-lightbox' => '{{VALUE}};',
				],
				'condition' => [
					'skin_type!' => 'sliding-box'
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_image_accordion_style',
			[
				'label' => __( 'Image Accordion', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'image_accordion_overlay_color',
			[
				'label'     => __( 'Active Item Overlay Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion-item:before'  => 'background: {{VALUE}};',
				],
				'condition' => [
					'skin_type!' => 'sliding-box'
				]
			]
		);
		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'sliding_overlay_background',
				'label' => esc_html__('Background', 'bdthemes-element-pack'),
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}}.skin--sliding-box .bdt-ep-image-accordion-img:before',
				'fields_options' => [
					'background' => [
						'label' => esc_html__('Active Item Overlay Color', 'bdthemes-element-pack'),
					],
				],
				'condition' => [
					'skin_type' => 'sliding-box'
				]
			]
		);

		$this->add_control(
			'image_accordion_inactive_item_overlay_color',
			[
				'label'     => __( 'In-active Item Overlay Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion-item.bdt-inactive:before'  => 'background: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-image-accordion-item.bdt-inactive .bdt-ep-image-accordion-img:before'  => 'background: {{VALUE}};',
				],
				'condition' => [
					'inactive_item_overlay' => 'yes',
				]
			]
		);

		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'content_background',
                'selector' => '{{WRAPPER}} .bdt-ep-image-accordion-item',
				'condition' => [
					'skin_type' => 'sliding-box'
				]
            ]
        );

		$this->add_responsive_control(
			'tabs_content_padding',
			[
				'label'      => __( 'Content Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-image-accordion-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_accordion_divider_heading',
			[
				'label'     => __( 'Divider', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'skin_type!' => 'sliding-box',
					'enable_item_style' => ''
				]
			]
		);

		$this->add_control(
			'image_accordion_divider_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion-item:after'  => 'background: {{VALUE}};',
				],
				'condition' => [
					'skin_type!' => 'sliding-box',
					'enable_item_style' => ''
				]
			]
		);

		$this->add_responsive_control(
			'image_accordion_divider_width',
			[
				'label' => esc_html__('Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion-item:after' => 'width: {{SIZE}}{{UNIT}}; right: calc(-{{SIZE}}{{UNIT}} / 2);',
				],
				'condition' => [
					'skin_type' => [ 'default' ],
					'enable_item_style' => ''
				],
			]
		);

		$this->add_responsive_control(
			'image_accordion_divider_width_skin',
			[
				'label' => esc_html__('Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'condition' => [
					'skin_type' => [ 'vertical' ],
					'enable_item_style' => ''
				],
				'render_type' => 'ui',
                'selectors' => [
                    '{{WRAPPER}} .bdt-ep-image-accordion-item:after' => '--ep-divider-width: {{SIZE}}{{UNIT}}; --ep-divider-bottom: -{{SIZE}}{{UNIT}};'
                ],
			]
		);

		$this->add_responsive_control(
			'enable_item_style',
			[
				'label' => esc_html__('Enable Item Style', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'item_column_gap',
			[
				'label' => esc_html__('Item Gap', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion' => 'grid-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-ep-image-accordion-item:after' => 'width: 0; right: 0; --ep-divider-width: 0; --ep-divider-bottom: -0;',
				],
				'condition' => [
					'enable_item_style' => 'yes',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'item_border',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-ep-image-accordion-item',
				'condition' => [
					'enable_item_style' => 'yes',
				],
			]
        );

        $this->add_responsive_control(
            'item_radius',
            [
                'label'      => __('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-ep-image-accordion-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
				'condition' => [
					'enable_item_style' => 'yes',
				],
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label'     => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => [ 'yes' ],
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion .bdt-ep-image-accordion-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion-title' => 'padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-ep-image-accordion-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'title_text_stroke',
                'label' => __('Text Stroke', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-ep-image-accordion-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_sub_title',
			[
				'label'     => esc_html__( 'Sub Title', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_sub_title' => [ 'yes' ],
				],
			]
		);

		$this->add_control(
			'sub_title_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion .bdt-ep-image-accordion-sub-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'sub_title_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion-sub-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_title_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-ep-image-accordion-sub-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_description',
			[
				'label'     => esc_html__( 'Text', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_text' => [ 'yes' ],
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion .bdt-ep-image-accordion-text, {{WRAPPER}} .bdt-ep-image-accordion .bdt-ep-image-accordion-text *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'description_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion-text' => 'padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-ep-image-accordion-text',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__( 'Button', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_button' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion .bdt-ep-image-accordion-button a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-image-accordion-button a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'selector'    => '{{WRAPPER}} .bdt-ep-image-accordion-button a',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-image-accordion-button a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'border_radius_advanced_show!' => 'yes',
				],
			]
		);

		$this->add_control(
			'border_radius_advanced_show',
			[
				'label' => __( 'Advanced Radius', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_responsive_control(
			'border_radius_advanced',
			[
				'label'       => esc_html__('Radius', 'bdthemes-element-pack'),
				/* translators: %1$s is example value, %2$s is link */
				'description' => sprintf(esc_html__('For example: <b>%1$s</b> or Go <a href="%2$s" target="_blank">this link</a> and copy and paste the radius value.', 'bdthemes-element-pack'), '30% 70% 82% 18% / 46% 62% 38% 54%', 'https://9elements.github.io/fancy-border-radius/'),
				'type'        => Controls_Manager::TEXT,
				'size_units'  => [ 'px', '%' ],
				'separator'   => 'after',
				'default'     => '30% 70% 82% 18% / 46% 62% 38% 54%',
				'selectors'   => [
					'{{WRAPPER}} .bdt-ep-image-accordion-button a'     => 'border-radius: {{VALUE}}; overflow: hidden;',
				],
				'condition' => [
					'border_radius_advanced_show' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-image-accordion-button a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'button_typography',
				'label'     => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector'  => '{{WRAPPER}} .bdt-ep-image-accordion-button a',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-image-accordion-button a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion .bdt-ep-image-accordion-button a:hover'  => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-image-accordion-button a:hover',
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion-button a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_lightbox',
			[
				'label'     => esc_html__('Lightbox', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_lightbox' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_lightbox_style');

		$this->start_controls_tab(
			'tab_lightbox_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'lightbox_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion-lightbox i, {{WRAPPER}} .bdt-ep-image-accordion-lightbox span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'lightbox_background',
                'selector' => '{{WRAPPER}} .bdt-ep-image-accordion-lightbox',
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'lightbox_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-ep-image-accordion-lightbox',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'lightbox_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-image-accordion-lightbox' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'lightbox_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-image-accordion-lightbox' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'lightbox_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-image-accordion-lightbox' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'lightbox_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-image-accordion-lightbox',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'lightbox_typography',
				'selector'  => '{{WRAPPER}} .bdt-ep-image-accordion-lightbox span.bdt-text, {{WRAPPER}} .bdt-ep-image-accordion-lightbox i',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_lightbox_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'lightbox_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion-lightbox:hover span, {{WRAPPER}} .bdt-ep-image-accordion-lightbox:hover i'    => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'lightbox_background_hover_color',
                'selector' => '{{WRAPPER}} .bdt-ep-image-accordion-lightbox:hover',
            ]
        );

		$this->add_control(
			'lightbox_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'lightbox_border_border!' => 'none',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-image-accordion-lightbox:hover' => 'border-color: {{VALUE}};',
				],
				'separator' => 'before'
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	public function render_lightbox( $item, $settings ) {
		if ( empty( $settings['show_lightbox'] ) || $settings['show_lightbox'] !== 'yes' ) {
			return;
		}

		$img_id = isset( $item['slide_image']['id'] ) ? $item['slide_image']['id'] : 0;
		$image_url = $img_id ? wp_get_attachment_image_src( $img_id, 'full' ) : false;
		$item_id = isset( $item['_id'] ) ? $item['_id'] : '';
		$key = 'lightbox-' . esc_attr( $item_id );

		$this->add_render_attribute( $key, 'data-elementor-open-lightbox', 'no' );

		if ( ! $image_url ) {
			$img_url = isset( $item['slide_image']['url'] ) ? $item['slide_image']['url'] : '';
			if ( $img_url !== '' ) {
				$this->add_render_attribute( $key, 'href', esc_url( $img_url ) );
			}
		} else {
			$this->add_render_attribute( $key, 'href', esc_url( $image_url[0] ) );
		}

		$this->add_render_attribute( $key, 'class', 'bdt-ep-image-accordion-lightbox' );

		$caption = isset( $item['image_accordion_title'] ) ? $item['image_accordion_title'] : '';
		if ( $caption !== '' ) {
			$caption = html_entity_decode( $caption, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
			$caption = html_entity_decode( $caption, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
			$caption = wp_kses( $caption, element_pack_allow_tags( 'text' ) );
			$caption = esc_js( $caption );
			$this->add_render_attribute( $key, 'data-caption', $caption );
		}

		$icon = isset( $settings['icon'] ) && $settings['icon'] !== '' ? $settings['icon'] : 'plus';
		$link_type = isset( $settings['link_type'] ) ? $settings['link_type'] : 'icon';
		?>
		<a <?php $this->print_render_attribute_string( $key ); ?>>
			<?php if ( $link_type === 'icon' ) : ?>
				<i class="ep-icon-<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
			<?php elseif ( $link_type === 'text' && ! empty( $settings['link_text'] ) ) : ?>
				<span class="bdt-text"><?php echo esc_html( $settings['link_text'] ); ?></span>
			<?php endif; ?>
		</a>
		<?php
	}

	public function render_accordion_content( $item, $title_key, $button_key, $settings ) {
		$this->add_render_attribute( 'bdt-ep-image-accordion-title', 'class', 'bdt-ep-image-accordion-title', true );
		$this->add_render_attribute( 'bdt-ep-image-accordion-sub-title', 'class', 'bdt-ep-image-accordion-sub-title', true );
		$this->add_render_attribute( 'bdt-ep-image-accordion-text', 'class', 'bdt-ep-image-accordion-text', true );
		$this->add_render_attribute( 'bdt-ep-image-accordion-button', 'class', 'bdt-ep-image-accordion-button', true );

		if ( ! empty( $settings['hide_on_mobile_title'] ) && $settings['hide_on_mobile_title'] === 'yes' ) {
			$this->add_render_attribute( 'bdt-ep-image-accordion-title', 'class', 'bdt-ep-image-accordion-title bdt-visible@s', true );
		}
		if ( ! empty( $settings['hide_on_mobile_sub_title'] ) && $settings['hide_on_mobile_sub_title'] === 'yes' ) {
			$this->add_render_attribute( 'bdt-ep-image-accordion-sub-title', 'class', 'bdt-ep-image-accordion-sub-title bdt-visible@s', true );
		}
		if ( ! empty( $settings['hide_on_mobile_text'] ) && $settings['hide_on_mobile_text'] === 'yes' ) {
			$this->add_render_attribute( 'bdt-ep-image-accordion-text', 'class', 'bdt-ep-image-accordion-text bdt-visible@s', true );
		}
		if ( ! empty( $settings['hide_on_mobile_button'] ) && $settings['hide_on_mobile_button'] === 'yes' ) {
			$this->add_render_attribute( 'bdt-ep-image-accordion-button', 'class', 'bdt-ep-image-accordion-button bdt-visible@s', true );
		}

		$title_link_url = isset( $item['title_link']['url'] ) ? $item['title_link']['url'] : '';
		if ( $title_link_url !== '' ) {
			$this->add_link_attributes( $title_key, isset( $item['title_link'] ) ? $item['title_link'] : [] );
		}

		$button_link_url = isset( $item['button_link']['url'] ) ? $item['button_link']['url'] : '';
		if ( $button_link_url !== '' ) {
			$this->add_link_attributes( $button_key, isset( $item['button_link'] ) ? $item['button_link'] : [] );
		}

		$title_tag = Utils::get_valid_html_tag( isset( $settings['title_tags'] ) ? $settings['title_tags'] : 'h3' );
		?>
		<div class="bdt-ep-image-accordion-content">
			<?php if ( ! empty( $item['image_accordion_sub_title'] ) && ! empty( $settings['show_sub_title'] ) && $settings['show_sub_title'] === 'yes' ) : ?>
				<div <?php $this->print_render_attribute_string( 'bdt-ep-image-accordion-sub-title' ); ?>>
					<?php echo wp_kses( $item['image_accordion_sub_title'], element_pack_allow_tags( 'title' ) ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $item['image_accordion_title'] ) && ! empty( $settings['show_title'] ) && $settings['show_title'] === 'yes' ) : ?>
				<?php if ( $title_link_url !== '' ) : ?>
					<a <?php $this->print_render_attribute_string( $title_key ); ?>>
				<?php endif; ?>
				<<?php echo esc_attr( $title_tag ); ?> <?php $this->print_render_attribute_string( 'bdt-ep-image-accordion-title' ); ?>>
					<?php echo wp_kses( $item['image_accordion_title'], element_pack_allow_tags( 'title' ) ); ?>
				</<?php echo esc_attr( $title_tag ); ?>>
				<?php if ( $title_link_url !== '' ) : ?>
					</a>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( ! empty( $item['image_accordion_text'] ) && ! empty( $settings['show_text'] ) && $settings['show_text'] === 'yes' ) : ?>
				<div <?php $this->print_render_attribute_string( 'bdt-ep-image-accordion-text' ); ?>>
					<?php echo wp_kses_post( $this->parse_text_editor( $item['image_accordion_text'] ) ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $item['image_accordion_button'] ) && ! empty( $settings['show_button'] ) && $settings['show_button'] === 'yes' ) : ?>
				<div <?php $this->print_render_attribute_string( 'bdt-ep-image-accordion-button' ); ?>>
					<?php if ( $button_link_url !== '' ) : ?>
						<a <?php $this->print_render_attribute_string( $button_key ); ?>>
					<?php endif; ?>
					<?php echo wp_kses_post( $item['image_accordion_button'] ); ?>
					<?php if ( $button_link_url !== '' ) : ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	public function render_image( $item, $settings ) {
		$img_id = isset( $item['slide_image']['id'] ) ? $item['slide_image']['id'] : 0;
		$img_url = isset( $item['slide_image']['url'] ) ? $item['slide_image']['url'] : '';
		$alt_text = isset( $item['image_accordion_title'] ) ? $item['image_accordion_title'] : '';
		$size_name = isset( $settings['thumbnail_size_size'] ) ? $settings['thumbnail_size_size'] : 'full';

		$thumb_url = $img_id ? Group_Control_Image_Size::get_attachment_image_src( $img_id, 'thumbnail_size', $settings ) : false;
		?>
		<div class="bdt-ep-image-accordion-img">
			<?php
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
			?>
			<?php $this->render_lightbox( $item, $settings ); ?>
		</div>
		<?php
	}

	public function render() {
		$settings = $this->get_settings_for_display();
		$image_accordion_items = isset( $settings['image_accordion_items'] ) && is_array( $settings['image_accordion_items'] ) ? $settings['image_accordion_items'] : [];

		if ( empty( $image_accordion_items ) ) {
			return;
		}

		$id = esc_attr( $this->get_id() );
		$imageAccordionEvent = ! empty( $settings['image_accordion_event'] ) ? $settings['image_accordion_event'] : false;

		$active_item = ( ! empty( $settings['active_item'] ) && $settings['active_item'] === 'yes' );
		$active_item_number = isset( $settings['active_item_number'] ) ? (int) $settings['active_item_number'] : 1;
		$swiping = isset( $settings['swiping'] ) ? $settings['swiping'] : '';
		$inactive_overlay = isset( $settings['inactive_item_overlay'] ) ? $settings['inactive_item_overlay'] : '';

		$this->add_render_attribute(
			[
				'image-accordion' => [
					'id'            => 'bdt-ep-image-accordion-' . $id,
					'class'         => 'bdt-ep-image-accordion',
					'data-settings' => [
						wp_json_encode( array_filter( [
							'tabs_id'             => 'bdt-ep-image-accordion-' . $id,
							'mouse_event'         => $imageAccordionEvent,
							'activeItem'          => $active_item,
							'activeItemNumber'    => $active_item_number,
							'swiping'             => $swiping,
							'inactiveItemOverlay' => $inactive_overlay,
						] ) ),
					],
				],
			]
		);

		if ( ! empty( $settings['show_lightbox'] ) && $settings['show_lightbox'] === 'yes' ) {
			$lightbox_animation = isset( $settings['lightbox_animation'] ) ? $settings['lightbox_animation'] : 'slide';
			$this->add_render_attribute( 'image-accordion', 'data-bdt-lightbox', 'toggle: .bdt-ep-image-accordion-lightbox; animation:' . esc_attr( $lightbox_animation ) . ';' );
			if ( ! empty( $settings['lightbox_autoplay'] ) && $settings['lightbox_autoplay'] === 'yes' ) {
				$this->add_render_attribute( 'image-accordion', 'data-bdt-lightbox', 'autoplay: 500;' );
				if ( ! empty( $settings['lightbox_pause'] ) && $settings['lightbox_pause'] === 'yes' ) {
					$this->add_render_attribute( 'image-accordion', 'data-bdt-lightbox', 'pause-on-hover: true;' );
				}
			}
		}

		$skin_type = isset( $settings['skin_type'] ) ? $settings['skin_type'] : 'default';
		?>
		<div <?php $this->print_render_attribute_string( 'image-accordion' ); ?>>
			<?php foreach ( $image_accordion_items as $index => $item ) :
				$title_key  = 'title_to_' . $index;
				$button_key = 'button_to_' . $index;

				$img_id = isset( $item['slide_image']['id'] ) ? $item['slide_image']['id'] : 0;
				$slide_image = $img_id ? Group_Control_Image_Size::get_attachment_image_src( $img_id, 'thumbnail_size', $settings ) : false;
				if ( ! $slide_image ) {
					$slide_image = isset( $item['slide_image']['url'] ) ? $item['slide_image']['url'] : '';
				}

				$this->add_render_attribute( 'image-accordion-item', 'class', 'bdt-ep-image-accordion-item', true );
				?>
				<?php if ( $skin_type !== 'sliding-box' ) : ?>
					<div <?php $this->print_render_attribute_string( 'image-accordion-item' ); ?> style="background-image: url('<?php echo esc_url( $slide_image ); ?>');">
						<?php $this->render_lightbox( $item, $settings ); ?>
						<?php $this->render_accordion_content( $item, $title_key, $button_key, $settings ); ?>
					</div>
				<?php else : ?>
					<div <?php $this->print_render_attribute_string( 'image-accordion-item' ); ?>>
						<?php $this->render_image( $item, $settings ); ?>
						<?php $this->render_accordion_content( $item, $title_key, $button_key, $settings ); ?>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
