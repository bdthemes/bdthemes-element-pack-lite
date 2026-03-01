<?php

namespace ElementPack\Modules\TestimonialGrid\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;
use ElementPack\Traits\Global_Widget_Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Testimonial_Grid extends Module_Base {
	use Group_Control_Query;
	use Global_Widget_Controls;
	private $_query = null;
	public function get_name() {
		return 'bdt-testimonial-grid';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Testimonial Grid', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-testimonial-grid';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'testimonial', 'grid' ];
	}

	public function get_style_depends() {
		return $this->ep_is_edit_mode() ? [ 'ep-styles' ] : [ 'ep-font', 'ep-testimonial-grid' ];
	}

	public function get_script_depends() {
		return $this->ep_is_edit_mode() ? [ 'ep-scripts' ] : [ 'ep-text-read-more-toggle' ];
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/pYMTXyDn8g4';
	}

	public function get_query() {
		return $this->_query;
	}

	public function has_widget_inner_wrapper(): bool {
		return ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	public function register_controls() {

		$this->start_controls_section(
			'section_content_layout',
			[ 
				'label' => esc_html__( 'Layout', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'layout',
			[ 
				'label'   => esc_html__( 'Layout', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [ 
					'1' => esc_html__( 'Default', 'bdthemes-element-pack' ),
					'2' => esc_html__( 'Top Avatar', 'bdthemes-element-pack' ),
					'3' => esc_html__( 'Bottom Avatar', 'bdthemes-element-pack' ),
				],
			]
		);

		$this->add_responsive_control(
			'columns',
			[ 
				'label'              => esc_html__( 'Columns', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '2',
				'tablet_default'     => '2',
				'mobile_default'     => '1',
				'options'            => [ 
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
				'frontend_available' => true,
				'selectors'       => [ 
					'{{WRAPPER}} .bdt-testimonial-grid-default' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
					'{{WRAPPER}} .bdt-testimonial-grid-masonry' => 'columns: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'show_pagination',
			[ 
				'label' => esc_html__( 'Pagination', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[ 
				'label'     => esc_html__( 'Column Gap', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ 
					'size' => 35,
				],
				'range'     => [ 
					'px' => [ 
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-grid-default' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-testimonial-grid-masonry' => 'column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[ 
				'label'     => esc_html__( 'Row Gap', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ 
					'size' => 35,
				],
				'range'     => [ 
					'px' => [ 
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-grid-default' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-testimonial-grid-masonry .bdt-testimonial-grid-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'show_image',
			[ 
				'label'     => esc_html__( 'Testimonial Image', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'show_title',
			[ 
				'label'   => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_address',
			[ 
				'label'   => esc_html__( 'Address', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'meta_multi_line',
			[ 
				'label'   => esc_html__( 'Meta Multiline', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_comma',
			[ 
				'label' => esc_html__( 'Show Comma After Title', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
				'condition' => [
					'show_title' => 'yes',
					'show_address' => 'yes',
					'meta_multi_line!' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_text',
			[ 
				'label'   => esc_html__( 'Text', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'text_limit',
			[ 
				'label'       => esc_html__( 'Text Limit', 'bdthemes-element-pack' ),
				'description' => esc_html__( 'It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 25,
				'condition'   => [ 
					'show_text' => 'yes',
				],
			]
		);
		$this->add_control(
            'ellipsis',
            [
                'label' => esc_html__('Ellipsis', 'bdthemes-element-pack') . BDTEP_NC,
                'type' => Controls_Manager::TEXT,
                'dynamic' => ['active' => true],
                'condition' => [
                    'show_text' => 'yes',
					'text_limit!' => [0, ''],
					'text_read_more_toggle' => ''
                ],
				'ai' => [
                    'active' => false,
                ],
            ]
        );

		$this->add_control(
			'strip_shortcode',
			[ 
				'label'     => esc_html__( 'Strip Shortcode', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [ 
					'show_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'text_read_more_toggle',
			[ 
				'label' => esc_html__( 'Text Read More Toggle', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
				'condition'   => [ 
					'show_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_rating',
			[ 
				'label'   => esc_html__( 'Rating', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'show_rating_above_text',
			[ 
				'label'   => esc_html__( 'Rating (Above Text)', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [ 
					'show_rating' => 'yes',
					'layout'    => '1',
				],
			]
		);

		$this->add_control(
			'show_review_platform',
			[ 
				'label' => esc_html__( 'Review Platform', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'item_match_height',
			[ 
				'label' => esc_html__( 'Item Match Height', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'item_masonry',
			[ 
				'label' => esc_html__( 'Masonry', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
				'render_type' => 'template'
			]
		);

		$this->add_control(
			'schema_rich_results',
			[ 
				'label'       => esc_html__( 'Google Rich Results (Schema)', 'bdthemes-element-pack' ) . BDTEP_NC,
				'description' => esc_html__( 'Improves compliance with Google Review structured data. Set the item being reviewed (e.g. your business or product).', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'schema_item_reviewed_name',
			[ 
				'label'       => esc_html__( 'Item Reviewed Name', 'bdthemes-element-pack' ),
				'description' => esc_html__( 'The name of the product, organization, or service being reviewed (e.g. your company or product name).', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => get_bloginfo( 'name' ),
				'condition'   => [ 
					'schema_rich_results' => 'yes',
				],
			]
		);

		$this->add_control(
			'schema_item_reviewed_type',
			[ 
				'label'     => esc_html__( 'Item Reviewed Type', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'Organization',
				'options'   => [ 
					'Organization'  => esc_html__( 'Organization', 'bdthemes-element-pack' ),
					'Product'       => esc_html__( 'Product', 'bdthemes-element-pack' ),
					'LocalBusiness' => esc_html__( 'Local Business', 'bdthemes-element-pack' ),
				],
				'condition' => [ 
					'schema_rich_results' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		//New Query Builder Settings
		$this->start_controls_section(
			'section_post_query_builder',
			[ 
				'label' => __( 'Query', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->register_query_builder_controls();

		$this->update_control(
			'posts_source',
			[ 
				'label'   => __( 'Source', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->getGroupControlQueryPostTypes(),
				'default' => 'bdthemes-testimonial',

			]
		);
		$this->update_control(
			'posts_per_page',
			[ 
				'default' => 4,
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'filter_bar',
			[ 
				'label' => esc_html__( 'Filter Bar', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'show_filter_bar',
			[ 
				'label'     => esc_html__( 'Filter Bar', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before'
			]
		);

		$post_types = $this->getGroupControlQueryPostTypes();

		foreach ( $post_types as $key => $post_type ) {
			$taxonomies = $this->get_taxonomies( $key );
			if ( ! $taxonomies[ $key ] ) {
				continue;
			}
			$this->add_control(
				'taxonomy_' . $key,
				[ 
					'label'     => __( 'Taxonomies', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => $taxonomies[ $key ],
					'default'   => key( $taxonomies[ $key ] ),
					'condition' => [ 
						'posts_source'    => $key,
						'show_filter_bar' => 'yes'
					],
				]
			);
		}

		$this->add_control(
			'filter_custom_text',
			[ 
				'label'     => esc_html__( 'Custom Text', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [ 
					'show_filter_bar' => 'yes',
				],
				'description' => esc_html__( 'If you active this option. You can change (All) text without translator plugin. If you wish you can use translator plugin also.', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'filter_custom_text_all',
			[ 
				'label'   => esc_html__( 'Custom Text (All)', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true ],
				'condition'  => [
					'show_filter_bar' => 'yes',
					'filter_custom_text'     => 'yes',
				],
				'default' => esc_html__( 'All', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'filter_custom_text_filter',
			[ 
				'label'     => __( 'Custom Text (Filter)', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => [ 'active' => true ],
				'default'   => __( 'Filter', 'bdthemes-element-pack' ),
				'condition' => [ 
					'show_filter_bar'    => 'yes',
					'filter_custom_text' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_item',
			[ 
				'label' => esc_html__( 'Item', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_item_style' );

		$this->start_controls_tab(
			'tab_item_normal',
			[ 
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'item_background',
			[ 
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-item-inner' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'item_border',
				'label'       => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-item-inner',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'item_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-item-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-item-inner',
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-item-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[ 
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'item_hover_background',
			[ 
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-item-inner:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_hover_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'item_border_border!' => '',
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-item-inner:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'item_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-item-inner:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			[ 
				'label'     => esc_html__( 'Image', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_image' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'image_border',
				'label'       => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-img-wrapper',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'image_hover_border_color',
			[ 
				'label'     => esc_html__( 'Hover Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'image_border_border!' => '',
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-img-wrapper:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-img-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'image_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-img-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_size',
			[ 
				'label'     => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-img-wrapper' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
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
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_address',
			[ 
				'label'     => esc_html__( 'Address', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_address' => 'yes',
				],
			]
		);

		$this->add_control(
			'address_color',
			[ 
				'label'     => esc_html__( 'Company Name/Address Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-address' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'address_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-address' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'address_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-address',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_text',
			[ 
				'label'     => esc_html__( 'Text', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'text_color',
			[ 
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'text_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-text',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_rating',
			[ 
				'label'     => esc_html__( 'Rating', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_control(
			'original_color',
			[ 
				'label'     => esc_html__( 'Enable Original Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [ 
					'show_review_platform' => 'yes'
				]
			]
		);

		$this->add_control(
			'rating_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e7e7e7',
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-rating .bdt-rating-item' => 'color: {{VALUE}};',
				],
				'condition' => [ 
					'original_color' => ''
				]
			]
		);

		$this->add_control(
			'active_rating_color',
			[ 
				'label'     => esc_html__( 'Active Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFCC00',
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-rating.bdt-rating-1 .bdt-rating-item:nth-of-type(1)'    => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-rating.bdt-rating-2 .bdt-rating-item:nth-of-type(-n+2)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-rating.bdt-rating-3 .bdt-rating-item:nth-of-type(-n+3)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-rating.bdt-rating-4 .bdt-rating-item:nth-of-type(-n+4)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-rating.bdt-rating-5 .bdt-rating-item:nth-of-type(-n+5)' => 'color: {{VALUE}};',
				],
				'condition' => [ 
					'original_color' => ''
				]
			]
		);

		$this->add_responsive_control(
			'rating_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rating_size',
			[ 
				'label'     => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-rating .bdt-rating-item' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rating_spacing',
			[ 
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-rating .bdt-rating-item' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// FILTER Bar Style
		$this->register_style_controls_filter();

		$this->start_controls_section(
			'section_style_review_platform',
			[ 
				'label'     => __( 'Review Platform', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_review_platform' => 'yes'
				],
			]
		);

		$this->start_controls_tabs( 'tabs_platform_style' );

		$this->start_controls_tab(
			'tab_platform_normal',
			[ 
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'platform_text_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-review-platform i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'platform_background_color',
				'selector' => '{{WRAPPER}} .bdt-review-platform',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'platform_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-review-platform',
			]
		);

		$this->add_responsive_control(
			'platform_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-review-platform' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'platform_text_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-review-platform' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'platform_text_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-review-platform' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'platform_shadow',
				'selector' => '{{WRAPPER}} .bdt-review-platform',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'platform_typography',
				'selector' => '{{WRAPPER}} .bdt-review-platform',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_platform_hover',
			[ 
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'platform_hover_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-review-platform:hover i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'platform_background_hover_color',
				'selector' => '{{WRAPPER}} .bdt-review-platform:hover',

			]
		);

		$this->add_control(
			'platform_hover_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'platform_border_border!' => '',
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-review-platform:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_pagination',
			[ 
				'label'     => esc_html__( 'Pagination', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_pagination' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_pagination_style' );

		$this->start_controls_tab(
			'tab_pagination_normal',
			[ 
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'pagination_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} ul.bdt-pagination li a, {{WRAPPER}} ul.bdt-pagination li span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'      => 'pagination_background',
				'selector'  => '{{WRAPPER}} ul.bdt-pagination li a',
				'separator' => 'after',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'     => 'pagination_border',
				'label'    => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} ul.bdt-pagination li a',
			]
		);

		$this->add_responsive_control(
			'pagination_offset',
			[ 
				'label'     => esc_html__( 'Offset', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-pagination' => 'margin-top: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_space',
			[ 
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-pagination'     => 'margin-left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-pagination > *' => 'padding-left: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_padding',
			[ 
				'label'     => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [ 
					'{{WRAPPER}} ul.bdt-pagination li a' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_radius',
			[ 
				'label'     => esc_html__( 'Radius', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [ 
					'{{WRAPPER}} ul.bdt-pagination li a' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_arrow_size',
			[ 
				'label'     => esc_html__( 'Arrow Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} ul.bdt-pagination li a svg' => 'height: {{SIZE}}px; width: auto;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'pagination_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} ul.bdt-pagination li a, {{WRAPPER}} ul.bdt-pagination li span',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_pagination_hover',
			[ 
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'pagination_hover_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} ul.bdt-pagination li a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_hover_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} ul.bdt-pagination li a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'pagination_hover_background',
				'selector' => '{{WRAPPER}} ul.bdt-pagination li a:hover',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_pagination_active',
			[ 
				'label' => esc_html__( 'Active', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'pagination_active_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} ul.bdt-pagination li.bdt-active a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_active_border_color',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} ul.bdt-pagination li.bdt-active a' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'pagination_active_background',
				'selector' => '{{WRAPPER}} ul.bdt-pagination li.bdt-active a',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_gb_words_limit_style',
			[ 
				'label'     => esc_html__( 'Text Read More Toggle', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'text_read_more_toggle' => 'yes',
				]
			]
		);

		$this->gloabl_read_more_link_style_controls();

		$this->end_controls_section();

		/**
		 * Addintional Style Tab
		 */
		$this->start_controls_section(
			'section_style_additional',
			[ 
				'label' => esc_html__( 'Additional Style', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_rating_above_text' => 'yes',
					'layout'    => '1',
				],
			]
		);

		$this->add_control(
			'content_heading',
			[ 
				'label' => esc_html__( 'Content', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'content_background',
				'selector' => '{{WRAPPER}} .bdt-testimonial-text-rating-wrapper',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'     => 'content_border',
				'selector' => '{{WRAPPER}} .bdt-testimonial-text-rating-wrapper',
			]
		);
		$this->add_responsive_control(
			'content_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-text-rating-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'content_padding',		
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-text-rating-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'content_margin',
			[
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-text-rating-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->end_controls_section();
	}


	public function get_taxonomies( $post_type = '' ) {
		$result = [];
		if ( $post_type ) {
			$taxonomies = get_taxonomies( [ 'public' => true, 'object_type' => [ $post_type ] ], 'object' );
			$tax       = array_diff_key( wp_list_pluck( $taxonomies, 'label', 'name' ), [] );
			$result[ $post_type ] = $tax !== [] ? $tax : '';
		}
		return $result;
	}

	public function filter_menu_terms( $settings ) {
		$source   = $settings['posts_source'] ?? '';
		$tax_key  = 'taxonomy_' . $source;
		$taxonomy = $settings[ $tax_key ] ?? '';
		if ( $taxonomy === '' ) {
			return '';
		}
		$categories = get_the_terms( get_the_ID(), $taxonomy );
		if ( ! is_array( $categories ) ) {
			return '';
		}
		$slugs = [];
		foreach ( $categories as $term ) {
			$slugs[ $term->slug ] = strtolower( $term->slug );
		}
		return implode( ' ', $slugs );
	}

	protected function filter_menu_categories( $settings ) {
		$post_options = [];
		$source       = $settings['posts_source'] ?? '';
		$tax_key      = 'taxonomy_' . $source;
		if ( ! isset( $settings[ $tax_key ] ) ) {
			return $post_options;
		}
		$taxonomy = $settings[ $tax_key ];
		$params   = [
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'include'    => $settings['posts_include_term_ids'] ?? [],
			'exclude'    => $settings['posts_exclude_term_ids'] ?? [],
		];
		$terms = get_terms( $params );
		if ( is_wp_error( $terms ) || ! is_array( $terms ) ) {
			return $post_options;
		}
		foreach ( $terms as $term ) {
			$post_options[ $term->slug ] = $term->name;
		}
		return $post_options;
	}


	public function render_query( $posts_per_page ) {
		$raw     = $this->get_settings();
		$args    = [
			'posts_per_page' => $posts_per_page,
		];
		if ( ! empty( $raw['show_pagination'] ) ) {
			$args['paged'] = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
		}
		$args         = array_merge( $this->getGroupControlQueryArgs(), $args );
		$this->_query = new \WP_Query( $args );
		return $this->_query;
	}

	public function render_image( $image_id, $settings ) {

		if ( ! isset( $settings['show_image'] ) || $settings['show_image'] !== 'yes' ) {
			return;
		}
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $image_id ), 'medium' );
		$src   = ( $thumb && isset( $thumb[0] ) ) ? $thumb[0] : BDTEP_ASSETS_URL . 'images/member.svg';
		$title = get_the_title( $image_id );
		?>
		<div class="bdt-flex bdt-position-relative">
			<div class="bdt-testimonial-grid-img-wrapper bdt-overflow-hidden bdt-border-circle bdt-background-cover">
				<img src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $title ); ?>" />
			</div>
			<?php $this->render_review_platform( $image_id, $settings ); ?>
		</div>
		<?php
	}

	public function render_title( $post_id, $settings ) {

		if ( $settings['show_title'] !== 'yes' ) {
			return;
		}
		$company_name = get_post_meta( $post_id, 'bdthemes_tm_company_name', true );
		$author_name  = get_the_title( $post_id );
		$use_schema   = $settings['schema_rich_results'] === 'yes';
		$show_comma   = $settings['show_comma'] === 'yes'
			&& $settings['show_address'] === 'yes'
			&& $company_name !== '';
		?>
		<h4 class="bdt-testimonial-grid-title bdt-margin-remove-bottom bdt-margin-remove-top">
			<?php if ( $use_schema ) : ?>
				<span itemprop="author" itemscope itemtype="https://schema.org/Person">
					<span itemprop="name"><?php echo esc_html( $author_name ); ?></span>
				</span><?php if ( $show_comma ) { echo ', '; } ?>
			<?php else : ?>
				<?php echo esc_html( $author_name ); ?><?php if ( $show_comma ) { echo ', '; } ?>
			<?php endif; ?>
		</h4>
		<?php
	}

	public function render_address( $post_id, $settings ) {

		if ( $settings['show_address'] !== 'yes' ) {
			return;
		}
		$company = get_post_meta( $post_id, 'bdthemes_tm_company_name', true );
		?>
		<p class="bdt-testimonial-grid-address bdt-text-meta bdt-margin-remove">
			<?php echo wp_kses_post( $company ); ?>
		</p>
		<?php
	}

	public function render_excerpt( $settings ) {

		if ( $settings['show_text'] !== 'yes' ) {
			return;
		}
		$strip_shortcode = $settings['strip_shortcode'] === 'yes';
		$this->add_render_attribute( 'text-wrap', 'class', 'bdt-testimonial-grid-text', true );
		if ( $settings['schema_rich_results'] === 'yes' ) {
			$this->add_render_attribute( 'text-wrap', 'itemprop', 'description', true );
		}
		$read_more = $settings['text_read_more_toggle'] === 'yes';
		if ( $read_more && $settings['text_limit'] > 0 ) {
			$this->add_render_attribute( 'text-wrap', 'class', 'bdt-ep-read-more-text', true );
			$this->add_render_attribute( 'text-wrap', 'data-read-more', wp_json_encode( [ 'words_length' => $settings['text_limit'] ] ), true );
			$text_limit = 0;
		} else {
			$text_limit = $settings['text_limit'];
		}
		$ellipsis = ! empty( $settings['ellipsis'] ) ? $settings['ellipsis'] : '';
		?>
		<div <?php $this->print_render_attribute_string( 'text-wrap' ); ?>>
			<?php
			if ( has_excerpt() ) {
				the_excerpt();
			} else {
				echo wp_kses_post( element_pack_custom_excerpt( $text_limit, $strip_shortcode, $ellipsis ) );
			}
			?>
		</div>
		<?php
	}

	public function render_review_platform( $post_id, $settings ) {

		if ( $settings['show_review_platform'] !== 'yes' ) {
			return;
		}
		$platform    = get_post_meta( $post_id, 'bdthemes_tm_platform', true ) ?: 'self';
		$review_link = get_post_meta( $post_id, 'bdthemes_tm_review_link', true ) ?: '#';
		?>
		<a href="<?php echo esc_url( $review_link ); ?>" class="bdt-review-platform bdt-flex-inline" bdt-tooltip="<?php echo esc_attr( $platform ); ?>">
			<i class="ep-icon-<?php echo esc_attr( strtolower( $platform ) ); ?> bdt-platform-icon bdt-flex bdt-flex-middle bdt-flex-center" aria-hidden="true"></i>
		</a>
		<?php
	}

	protected function get_sanitized_rating( $post_id ) {
		$raw = get_post_meta( $post_id, 'bdthemes_tm_rating', true );
		$num = intval( $raw );
		if ( $num < 1 && is_string( $raw ) && preg_match( '/\d+/', $raw, $m ) ) {
			$num = intval( $m[0] );
		}
		return max( 1, min( 5, $num ) );
	}

	public function render_schema_item_reviewed( $settings ) {

		if ( $settings['schema_rich_results'] !== 'yes' ) {
			return;
		}
		$name = ! empty( $settings['schema_item_reviewed_name'] ) ? $settings['schema_item_reviewed_name'] : get_bloginfo( 'name' );
		$type = $settings['schema_item_reviewed_type'] ?? 'Organization';
		$type = in_array( $type, [ 'Organization', 'Product', 'LocalBusiness' ], true ) ? $type : 'Organization';
		$hidden_style = 'position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;';
		?>
		<span class="bdt-ep-schema-item-reviewed" style="<?php echo esc_attr( $hidden_style ); ?>" itemprop="itemReviewed" itemscope itemtype="https://schema.org/<?php echo esc_attr( $type ); ?>">
			<meta itemprop="name" content="<?php echo esc_attr( $name ); ?>">
		</span>
		<?php
	}

	public function render_rating_schema_only( $post_id, $settings ) {
		if ( $settings['schema_rich_results'] !== 'yes' ) {
			return;
		}
		if ( $settings['show_rating'] === 'yes' ) {
			return;
		}
		$rating   = $this->get_sanitized_rating( $post_id );
		$date     = get_the_date( 'c', $post_id );
		$hidden   = 'position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;';
		?>
		<meta itemprop="datePublished" content="<?php echo esc_attr( $date ); ?>">
		<span style="<?php echo esc_attr( $hidden ); ?>" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
			<meta itemprop="worstRating" content="1">
			<meta itemprop="ratingValue" content="<?php echo absint( $rating ); ?>">
			<meta itemprop="bestRating" content="5">
		</span>
		<?php
	}

	public function render_rating( $post_id, $settings ) {
		if ( $settings['show_rating'] !== 'yes' ) {
			return;
		}
		$rating = $this->get_sanitized_rating( $post_id );
		$date   = get_the_date( 'c', $post_id );
		?>
		<div class="bdt-testimonial-grid-rating">
			<meta itemprop="datePublished" content="<?php echo esc_attr( $date ); ?>">
			<ul class="bdt-rating bdt-rating-<?php echo absint( $rating ); ?> bdt-grid bdt-grid-collapse" data-bdt-grid itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
				<meta itemprop="worstRating" content="1">
				<meta itemprop="ratingValue" content="<?php echo absint( $rating ); ?>">
				<meta itemprop="bestRating" content="5">
				<li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
				<li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
				<li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
				<li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
				<li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
			</ul>
		</div>
		<?php
	}

	public function render_filter_menu( $settings ) {
		$testi_categories = $this->filter_menu_categories( $settings );
		$custom_text      = $settings['filter_custom_text'] === 'yes';
		$filter_label     = $custom_text ? ( $settings['filter_custom_text_filter'] ) : __( 'Filter', 'bdthemes-element-pack' );
		$all_label        = $custom_text && ! empty( $settings['filter_custom_text_all'] ) ? $settings['filter_custom_text_all'] : __( 'All', 'bdthemes-element-pack' );
		$show_all_item    = $custom_text ? ! empty( $settings['filter_custom_text_all'] ) : true;
		?>
		<div class="bdt-ep-grid-filters-wrapper">
			<button class="bdt-button bdt-button-default bdt-hidden@m" type="button"><?php echo esc_html( $filter_label ); ?></button>
			<div data-bdt-dropdown="mode: click;" class="bdt-dropdown bdt-margin-remove-top bdt-margin-remove-bottom bdt-hidden@m">
				<ul class="bdt-nav bdt-dropdown-nav">
					<?php if ( $show_all_item ) : ?>
						<li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control>
							<a href="#"><?php echo esc_html( $all_label ); ?></a>
						</li>
					<?php endif; ?>
					<?php foreach ( $testi_categories as $slug => $name ) : ?>
						<li class="bdt-ep-grid-filter" data-bdt-filter-control="[data-filter*='<?php echo esc_attr( strtolower( $slug ) ); ?>']">
							<a href="#"><?php echo esc_html( $name ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<ul class="bdt-ep-grid-filters bdt-visible@m" data-bdt-margin>
				<?php if ( $show_all_item ) : ?>
					<li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control>
						<a href="#"><?php echo esc_html( $all_label ); ?></a>
					</li>
				<?php endif; ?>
				<?php foreach ( $testi_categories as $slug => $name ) : ?>
					<li class="bdt-ep-grid-filter" data-bdt-filter-control="[data-filter*='<?php echo esc_attr( strtolower( $slug ) ); ?>']">
						<a href="#"><?php echo esc_html( $name ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}

	public function render_header( $settings ) {
		$this->add_render_attribute( 'testimonial-grid-wrapper', 'class', [
			'bdt-testimonial-grid-layout-' . $settings['layout'],
			'bdt-testimonial-grid',
			'bdt-ep-grid-filter-container',
		] );
		if ( $settings['show_filter_bar'] === 'yes' ) {
			$this->add_render_attribute( 'testimonial-grid-wrapper', 'data-bdt-filter', 'target: #bdt-testimonial-grid-' . $this->get_id() );
		}
		?>
		<div <?php $this->print_render_attribute_string( 'testimonial-grid-wrapper' ); ?>>
			<?php if ( $settings['show_filter_bar'] === 'yes' ) : ?>
				<?php $this->render_filter_menu( $settings ); ?>
			<?php endif; ?>
		<?php
	}

	public function render_footer() {
		?>
		</div>
		<?php
	}



	public function render_loop_item( $settings ) {
		$widget_id = $this->get_id();
		$per_page  = isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 4;
		$wp_query  = $this->render_query( $per_page );

		if ( ! empty( $settings['item_match_height'] ) ) {
			$this->add_render_attribute( 'testimonial-grid', 'data-bdt-height-match', 'div > .bdt-testimonial-grid-item-inner' );
		}
		$grid_class = ! empty( $settings['item_masonry'] ) ? 'bdt-testimonial-grid-masonry' : 'bdt-testimonial-grid-default';
		$this->add_render_attribute( 'testimonial-grid', 'class', $grid_class );

		if ( ! $wp_query->have_posts() ) {
			echo '<div class="bdt-alert-warning" bdt-alert>' . esc_html_x( 'Oppps!! There is no post, please select actual post or categories.', 'Frontend', 'bdthemes-element-pack' ) . '</div>';
			return;
		}
		?>
		<div id="bdt-testimonial-grid-<?php echo esc_attr( $widget_id ); ?>" <?php $this->print_render_attribute_string( 'testimonial-grid' ); ?>>
			<?php
			$layout        = $settings['layout'];
			$columns       = $settings['columns'];
			$show_filter   = $settings['show_filter_bar'] === 'yes';
			$schema_on     = $settings['schema_rich_results'] === 'yes';
			$show_rating   = $settings['show_rating'] === 'yes';
			$meta_multi    = ! empty( $settings['meta_multi_line'] );
			$rating_above  = ! empty( $settings['show_rating_above_text'] );
			$show_title    = $settings['show_title'] === 'yes';
			$show_address  = $settings['show_address'] === 'yes';

			while ( $wp_query->have_posts() ) :
				$wp_query->the_post();
				$post_id  = get_the_ID();
				$platform = get_post_meta( $post_id, 'bdthemes_tm_platform', true );
				$platform = $platform !== '' ? strtolower( $platform ) : '';

				$item_key = 'testimonial-grid-item' . $post_id;
				$this->add_render_attribute( $item_key, 'class', 'bdt-testimonial-grid-item bdt-review-' . $platform );
				if ( $schema_on ) {
					$this->add_render_attribute( $item_key, 'itemprop', 'review' );
					$this->add_render_attribute( $item_key, 'itemscope', '' );
					$this->add_render_attribute( $item_key, 'itemtype', 'https://schema.org/Review' );
				}
				if ( $show_filter ) {
					$this->add_render_attribute( $item_key, 'data-filter', $this->filter_menu_terms( $settings ) );
				}
				?>
				<div <?php $this->print_render_attribute_string( $item_key ); ?>>
					<?php $this->render_schema_item_reviewed( $settings ); ?>
					<?php if ( $schema_on && ! $show_rating ) : ?>
						<?php $this->render_rating_schema_only( $post_id, $settings ); ?>
					<?php endif; ?>

					<?php if ( $layout === '1' ) : ?>
						<div class="bdt-testimonial-grid-item-inner">
							<div class="bdt-grid bdt-position-relative bdt-grid-small bdt-flex-middle" data-bdt-grid>
								<?php $this->render_image( $post_id, $settings ); ?>
								<?php if ( $show_title || $show_address ) : ?>
									<div class="bdt-testimonial-grid-title-address <?php echo $meta_multi ? 'bdt-meta-multi-line' : ''; ?>">
										<?php
										$this->render_title( $post_id, $settings );
										$this->render_address( $post_id, $settings );
										if ( ! $rating_above && $show_rating ) :
											if ( (int) $columns >= 3 ) :
												$this->render_rating( $post_id, $settings );
											elseif ( (int) $columns <= 2 ) : ?>
												<div class="bdt-position-center-right bdt-text-right">
													<?php $this->render_rating( $post_id, $settings ); ?>
												</div>
											<?php endif;
										endif;
										?>
									</div>
								<?php endif; ?>
							</div>
							<?php if ( $rating_above || $settings['show_text'] === 'yes' ) : ?>
								<div class="bdt-testimonial-text-rating-wrapper bdt-margin-top">
									<?php if ( $rating_above ) : ?>
										<?php $this->render_rating( $post_id, $settings ); ?>
									<?php endif; ?>
									<?php $this->render_excerpt( $settings ); ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( $layout === '2' ) : ?>
						<div class="bdt-testimonial-grid-item-inner bdt-position-relative bdt-text-center">
							<div class="bdt-position-relative bdt-flex-inline">
								<?php $this->render_image( $post_id, $settings ); ?>
							</div>
							<?php if ( $show_title || $show_address ) : ?>
								<div class="bdt-testimonial-grid-title-address <?php echo $meta_multi ? 'bdt-meta-multi-line' : ''; ?>">
									<?php
									$this->render_title( $post_id, $settings );
									$this->render_address( $post_id, $settings );
									?>
								</div>
							<?php endif; ?>
							<?php $this->render_excerpt( $settings ); ?>
							<?php $this->render_rating( $post_id, $settings ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $layout === '3' ) : ?>
						<div class="bdt-testimonial-grid-item-inner">
							<?php $this->render_excerpt( $settings ); ?>
							<div class="bdt-grid bdt-position-relative bdt-grid-small bdt-flex-middle" data-bdt-grid>
								<?php $this->render_image( $post_id, $settings ); ?>
								<?php if ( $show_title || $show_address ) : ?>
									<div class="bdt-testimonial-grid-title-address <?php echo $meta_multi ? 'bdt-meta-multi-line' : ''; ?>">
										<?php
										$this->render_title( $post_id, $settings );
										$this->render_address( $post_id, $settings );
										if ( $show_rating ) :
											if ( (int) $columns >= 3 ) :
												$this->render_rating( $post_id, $settings );
											elseif ( (int) $columns <= 2 ) : ?>
												<div class="bdt-position-center-right bdt-text-right">
													<?php $this->render_rating( $post_id, $settings ); ?>
												</div>
											<?php endif;
										endif;
										?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endwhile; ?>
		</div>
		<?php
		if ( $settings['show_pagination'] === 'yes' ) :
			?>
			<div class="ep-pagination">
				<?php element_pack_post_pagination( $wp_query ); ?>
			</div>
			<?php
		endif;
		wp_reset_postdata();
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		$this->render_header( $settings );
		$this->render_loop_item( $settings );
		$this->render_footer();
	}
}
