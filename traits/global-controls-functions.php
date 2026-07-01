<?php

namespace ElementPack\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use ElementPack\Utils;

defined( 'ABSPATH' ) || die();

trait Global_Controls_Functions {

	/**
	 * Build a brand widget CSS selector.
	 *
	 * @param string $widget_prefix e.g. 'brand-grid' or 'brand-carousel'.
	 * @param string $element       e.g. 'item', 'image', 'name'.
	 */
	protected function get_brand_selector( $widget_prefix, $element ) {
		return '{{WRAPPER}} .bdt-ep-' . $widget_prefix . '-' . $element;
	}

	/**
	 * Register brand items repeater controls.
	 */
	protected function register_brand_items_controls() {
		$this->start_controls_section(
			'ep_section_brand',
			[
				'label' => esc_html__( 'Brand Items', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label'   => esc_html__( 'Brand Image', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [ 'active' => true ],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'brand_name',
			[
				'label'       => esc_html__( 'Brand Name', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Brand Name', 'bdthemes-element-pack' ),
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'         => esc_html__( 'Website Url', 'bdthemes-element-pack' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'bdthemes-element-pack' ),
				'show_external' => true,
				'default'       => [
					'url'         => '#',
					'is_external' => true,
					'nofollow'    => true,
				],
				'label_block'   => true,
				'dynamic'       => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'website_link_text',
			[
				'label'       => esc_html__( 'Website Url Text', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'www.example.com', 'bdthemes-element-pack' ),
				'placeholder' => esc_html__( 'Paste URL Text or Type', 'bdthemes-element-pack' ),
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'brand_items',
			[
				'show_label'  => false,
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ brand_name }}}',
				'default'     => [
					[ 'image' => [ 'url' => Utils::get_placeholder_image_src() ] ],
					[ 'image' => [ 'url' => Utils::get_placeholder_image_src() ] ],
					[ 'image' => [ 'url' => Utils::get_placeholder_image_src() ] ],
					[ 'image' => [ 'url' => Utils::get_placeholder_image_src() ] ],
					[ 'image' => [ 'url' => Utils::get_placeholder_image_src() ] ],
					[ 'image' => [ 'url' => Utils::get_placeholder_image_src() ] ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumbnail',
				'default'   => 'medium',
				'separator' => 'before',
				'exclude'   => [ 'custom' ],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register shared brand display settings controls.
	 */
	protected function register_brand_common_settings_controls() {
		$this->add_control(
			'show_brand_name',
			[
				'label'     => esc_html__( 'Show Brand Name', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'brand_html_tag',
			[
				'label'     => esc_html__( 'Name HTML Tag', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h3',
				'options'   => element_pack_title_tags(),
				'condition' => [
					'show_brand_name' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_website_link',
			[
				'label'     => esc_html__( 'Show Link Text', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'brand_event',
			[
				'label'     => esc_html__( 'Select Event ', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'hover-icon',
				'options'   => [
					'click'      => esc_html__( 'Click', 'bdthemes-element-pack' ),
					'hover-icon' => esc_html__( 'Icon Hover', 'bdthemes-element-pack' ),
					'hover-item' => esc_html__( 'Item Hover', 'bdthemes-element-pack' ),
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'icon_position',
			[
				'label'        => esc_html__( 'Icon Position', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'bottom-left',
				'options'      => [
					'top-left'      => esc_html__( 'Top Start', 'bdthemes-element-pack' ),
					'top-right'     => esc_html__( 'Top End', 'bdthemes-element-pack' ),
					'bottom-left'   => esc_html__( 'Bottom Start', 'bdthemes-element-pack' ),
					'bottom-right'  => esc_html__( 'Bottom End', 'bdthemes-element-pack' ),
					'center-center' => esc_html__( 'Center Center', 'bdthemes-element-pack' ),
				],
				'prefix_class' => 'bdt-ep-icon--',
			]
		);
	}

	/**
	 * Register brand items style controls.
	 *
	 * @param string $widget_prefix e.g. 'brand-grid' or 'brand-carousel'.
	 * @param array  $args {
	 *     @type bool $show_shadow_padding Show carousel match padding control.
	 * }
	 */
	protected function register_brand_style_items_controls( $widget_prefix, $args = [] ) {
		$args               = wp_parse_args( $args, [ 'show_shadow_padding' => false ] );
		$item_selector      = $this->get_brand_selector( $widget_prefix, 'item' );
		$image_selector     = $this->get_brand_selector( $widget_prefix, 'image' ) . ' img';
		$item_hover_selector = $item_selector . ':hover';

		$this->start_controls_section(
			'section_style_items',
			[
				'label' => esc_html__( 'Items', 'bdthemes-element-pack' ),
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

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_background',
				'selector' => $item_selector,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'item_border',
				'selector'  => $item_selector,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$item_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$item_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => $item_selector,
			]
		);

		if ( $args['show_shadow_padding'] ) {
			$this->add_responsive_control(
				'item_shadow_padding',
				[
					'label'       => esc_html__( 'Match Padding', 'bdthemes-element-pack' ),
					'description' => esc_html__( 'You have to add padding for matching overlaping normal/hover box shadow when you used Box Shadow option.', 'bdthemes-element-pack' ),
					'type'        => Controls_Manager::SLIDER,
					'range'       => [
						'px' => [
							'min'  => 0,
							'step' => 1,
							'max'  => 50,
						],
					],
					'selectors'   => [
						'{{WRAPPER}} .swiper-carousel' => 'padding: {{SIZE}}{{UNIT}}; margin: 0 -{{SIZE}}{{UNIT}};',
					],
				]
			);
		}

		$this->add_control(
			'image_heading',
			[
				'label'     => esc_html__( 'Image', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'brand_image_size',
			[
				'label'     => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 500,
					],
				],
				'selectors' => [
					$image_selector => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; object-fit: cover;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'css_filters',
				'selector' => $image_selector,
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
			'item_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'item_border_border!' => '',
				],
				'selectors' => [
					$item_hover_selector => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_hover_box_shadow',
				'selector' => $item_hover_selector,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register brand icon style controls.
	 *
	 * @param string $widget_prefix e.g. 'brand-grid' or 'brand-carousel'.
	 * @param array  $args {
	 *     @type bool $icon_radius_responsive Use responsive border radius control.
	 * }
	 */
	protected function register_brand_style_icon_controls( $widget_prefix, $args = [] ) {
		$args = wp_parse_args( $args, [ 'icon_radius_responsive' => false ] );

		$icon_selector    = $this->get_brand_selector( $widget_prefix, 'icon' );
		$toggle_selector  = $this->get_brand_selector( $widget_prefix, 'checkbox' ) . ', ' . $this->get_brand_selector( $widget_prefix, 'content' );

		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => esc_html__( 'Icon', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$icon_selector => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'icon_background',
				'selector' => $toggle_selector,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'icon_border',
				'selector' => $toggle_selector,
			]
		);

		if ( $args['icon_radius_responsive'] ) {
			$this->add_responsive_control(
				'iamge_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						$toggle_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
		} else {
			$this->add_control(
				'iamge_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						$toggle_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
		}

		$this->add_responsive_control(
			'iamge_margin',
			[
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$toggle_selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'     => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					$toggle_selector => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_font_size',
			[
				'label'     => esc_html__( 'Font Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					$icon_selector => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_shadow',
				'selector' => $toggle_selector,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register brand name style controls.
	 *
	 * @param string $widget_prefix e.g. 'brand-grid' or 'brand-carousel'.
	 */
	protected function register_brand_style_name_controls( $widget_prefix ) {
		$name_selector = $this->get_brand_selector( $widget_prefix, 'name' );

		$this->start_controls_section(
			'section_style_name',
			[
				'label'     => esc_html__( 'Name', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_brand_name' => 'yes',
				],
			]
		);

		$this->add_control(
			'name_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$name_selector => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'name_typography',
				'selector' => $name_selector,
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'name_shadow',
				'label'    => esc_html__( 'Text Shadow', 'bdthemes-element-pack' ),
				'selector' => $name_selector,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register brand website link style controls.
	 *
	 * @param string $widget_prefix e.g. 'brand-grid' or 'brand-carousel'.
	 */
	protected function register_brand_style_website_link_controls( $widget_prefix ) {
		$link_selector = $this->get_brand_selector( $widget_prefix, 'link' );
		$text_selector = $this->get_brand_selector( $widget_prefix, 'text' );

		$this->start_controls_section(
			'section_style_website_link',
			[
				'label'     => esc_html__( 'Text', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_website_link' => 'yes',
				],
			]
		);

		$this->add_control(
			'website_link_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$link_selector => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'website_link_hover_color',
			[
				'label'     => esc_html__( 'Hover Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$link_selector . ':hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'website_link_top_space',
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
					$text_selector => 'padding-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'website_link_typography',
				'selector' => $link_selector,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render brand item overlay content (icon, name, link).
	 *
	 * @param array  $item          Repeater item data.
	 * @param int    $index         Item index.
	 * @param array  $settings      Widget settings.
	 * @param string $widget_prefix e.g. 'brand-grid' or 'brand-carousel'.
	 * @param array  $args {
	 *     @type string $item_key_prefix Render attribute key prefix.
	 *     @type bool   $merge_attributes Merge render attributes instead of unique keys.
	 *     @type string $brand_html_tag   Pre-validated HTML tag for brand name.
	 * }
	 */
	protected function render_brand_item_content( $item, $index, $settings, $widget_prefix, $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'item_key_prefix'  => 'item-wrap',
				'merge_attributes' => false,
				'brand_html_tag'   => Utils::get_valid_html_tag( $settings['brand_html_tag'] ),
			]
		);

		$prefix       = 'bdt-ep-' . $widget_prefix;
		$name_key     = $args['merge_attributes'] ? 'name-wrap' : 'name-wrap' . $index;
		$link_key     = 'link_' . $index;
		$brand_event  = $settings['brand_event'];
		$checkbox_cls = $prefix . '-checkbox';

		if ( 'click' === $brand_event ) {
			$aria_label = 'brand-grid' === $widget_prefix ? ' aria-label="' . esc_attr( $item['brand_name'] ) . '"' : '';
			printf( '<input class="%1$s" type="checkbox"%2$s>', esc_attr( $checkbox_cls ), $aria_label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
		<div class="<?php echo esc_attr( $prefix . '-content' ); ?>">
			<div class="<?php echo esc_attr( $prefix . '-icon' ); ?>">
				<i class="ep-icon-plus-2" aria-hidden="true"></i>
			</div>
			<div class="<?php echo esc_attr( $prefix . '-inner' ); ?>">
				<?php if ( $item['brand_name'] && $settings['show_brand_name'] ) : ?>
					<?php
					$this->add_render_attribute( $name_key, 'class', $prefix . '-name', $args['merge_attributes'] );
					?>
					<<?php echo esc_attr( $args['brand_html_tag'] ); ?> <?php $this->print_render_attribute_string( $name_key ); ?>>
						<?php echo wp_kses( $item['brand_name'], element_pack_allow_tags( 'brand_name' ) ); ?>
					</<?php echo esc_attr( $args['brand_html_tag'] ); ?>>
				<?php endif; ?>

				<?php if ( ! empty( $item['link']['url'] ) && $settings['show_website_link'] ) : ?>
					<?php
					$this->add_render_attribute( $link_key, 'class', $prefix . '-link', $args['merge_attributes'] );
					$this->add_link_attributes( $link_key, $item['link'] );
					?>
					<div class="<?php echo esc_attr( $prefix . '-text' ); ?>">
						<a <?php $this->print_render_attribute_string( $link_key ); ?>>
							<?php echo esc_html( $item['website_link_text'] ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render brand item image for grid layout.
	 *
	 * @param array  $item     Repeater item data.
	 * @param array  $settings Widget settings.
	 * @param string $widget_prefix e.g. 'brand-grid'.
	 */
	protected function render_brand_grid_image( $item, $settings, $widget_prefix ) {
		$prefix = 'bdt-ep-' . $widget_prefix;
		?>
		<div class="<?php echo esc_attr( $prefix . '-image' ); ?>">
			<?php
			if ( ! empty( $item['image']['id'] ) ) {
				echo wp_get_attachment_image(
					$item['image']['id'],
					$settings['thumbnail_size'],
					false,
					[
						'alt'   => esc_html( $item['brand_name'] ),
						'class' => 'bdt-brand-image',
					]
				);
			} elseif ( ! empty( $item['image']['url'] ) ) {
				printf(
					'<img src="%1$s" alt="%2$s">',
					esc_url( $item['image']['url'] ),
					esc_html( $item['brand_name'] )
				);
			}
			?>
		</div>
		<?php
	}

	/**
	 * Render brand item image for carousel layout.
	 *
	 * @param array  $item     Repeater item data.
	 * @param array  $settings Widget settings.
	 * @param string $widget_prefix e.g. 'brand-carousel'.
	 */
	protected function render_brand_carousel_image( $item, $settings, $widget_prefix ) {
		$prefix = 'bdt-ep-' . $widget_prefix;
		?>
		<div class="<?php echo esc_attr( $prefix . '-image' ); ?>">
			<?php
			$thumb_url = Group_Control_Image_Size::get_attachment_image_src( $item['image']['id'], 'thumbnail', $settings );
			if ( ! $thumb_url ) {
				printf(
					'<img src="%1$s" alt="%2$s">',
					esc_url( $item['image']['url'] ),
					esc_html( $item['brand_name'] )
				);
			} else {
				print wp_get_attachment_image(
					$item['image']['id'],
					$settings['thumbnail_size'],
					false,
					[
						'alt' => esc_html( $item['brand_name'] ),
					]
				);
			}
			?>
		</div>
		<?php
	}

	/**
	 * Get brand item CSS classes.
	 *
	 * @param array  $settings      Widget settings.
	 * @param string $widget_prefix e.g. 'brand-grid' or 'brand-carousel'.
	 * @param array  $extra_classes Additional classes e.g. ['swiper-slide'].
	 */
	protected function get_brand_item_classes( $settings, $widget_prefix, $extra_classes = [] ) {
		$classes = [ 'bdt-ep-' . $widget_prefix . '-item' ];

		if ( 'hover-item' === $settings['brand_event'] ) {
			$classes[] = 'bdt-ep-' . $widget_prefix . '-item-hover';
		}

		if ( ! empty( $extra_classes ) ) {
			$classes = array_merge( $classes, $extra_classes );
		}

		return implode( ' ', $classes );
	}

	/**
	 * Build a logo widget CSS selector.
	 *
	 * @param string $widget_type e.g. 'grid' or 'carousel'.
	 * @param string $element     e.g. 'figure', 'img', 'item'.
	 */
	protected function get_logo_selector( $widget_type, $element ) {
		return '{{WRAPPER}} .bdt-logo-' . $widget_type . '-' . $element;
	}

	/**
	 * Register logo items repeater controls.
	 *
	 * @param array $args {
	 *     @type string $section_id       Section ID.
	 *     @type string $section_label    Section label.
	 *     @type array  $default_items    Default repeater items.
	 *     @type bool   $tooltip_frontend Add frontend_available to tooltip control.
	 * }
	 */
	protected function register_logo_items_controls( $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'section_id'        => 'ep_section_logo',
				'section_label'     => esc_html__( 'Logo Grid Items', 'bdthemes-element-pack' ),
				'default_items'     => array_fill( 0, 8, [ 'image' => [ 'url' => Utils::get_placeholder_image_src() ] ] ),
				'tooltip_frontend'  => false,
			]
		);

		$this->start_controls_section(
			$args['section_id'],
			[
				'label' => $args['section_label'],
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label'   => esc_html__( 'Logo Image', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [ 'active' => true ],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'   => esc_html__( 'Website Url', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::URL,
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'name',
			[
				'label'   => esc_html__( 'Brand Name', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Brand Name', 'bdthemes-element-pack' ),
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'description',
			[
				'label'   => esc_html__( 'Description', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXTAREA,
				'dynamic' => [ 'active' => true ],
				'default' => esc_html__( 'Brand Short Description Type Here.', 'bdthemes-element-pack' ),
			]
		);

		$tooltip_control = [
			'label' => esc_html__( 'Tooltip', 'bdthemes-element-pack' ),
			'type'  => Controls_Manager::SWITCHER,
		];

		if ( $args['tooltip_frontend'] ) {
			$tooltip_control['render_type']        = 'template';
			$tooltip_control['frontend_available'] = true;
		}

		$repeater->add_control( 'logo_tooltip', $tooltip_control );

		$repeater->add_control(
			'logo_tooltip_placement',
			[
				'label'     => esc_html__( 'Placement', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'top',
				'options'   => [
					'top-start'    => esc_html__( 'Top Left', 'bdthemes-element-pack' ),
					'top'          => esc_html__( 'Top', 'bdthemes-element-pack' ),
					'top-end'      => esc_html__( 'Top Right', 'bdthemes-element-pack' ),
					'bottom-start' => esc_html__( 'Bottom Left', 'bdthemes-element-pack' ),
					'bottom'       => esc_html__( 'Bottom', 'bdthemes-element-pack' ),
					'bottom-end'   => esc_html__( 'Bottom Right', 'bdthemes-element-pack' ),
					'left'         => esc_html__( 'Left', 'bdthemes-element-pack' ),
					'right'        => esc_html__( 'Right', 'bdthemes-element-pack' ),
				],
				'condition' => [
					'logo_tooltip' => 'yes',
				],
			]
		);

		$this->add_control(
			'logo_list',
			[
				'show_label'  => false,
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => $args['default_items'],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register logo tooltip settings controls.
	 *
	 * @param array $args {
	 *     @type string $x_offset_label X offset control label.
	 *     @type string $y_offset_label Y offset control label.
	 *     @type bool   $animation_render_type Add render_type template to animation control.
	 * }
	 */
	protected function register_logo_tooltip_settings_controls( $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'x_offset_label'          => esc_html__( 'X Offset', 'bdthemes-element-pack' ),
				'y_offset_label'          => esc_html__( 'Y Offset', 'bdthemes-element-pack' ),
				'animation_render_type'   => false,
			]
		);

		$this->start_controls_section(
			'section_tooltip_settings',
			[
				'label' => esc_html__( 'Tooltip Settings', 'bdthemes-element-pack' ),
			]
		);

		$animation_control = [
			'label'   => esc_html__( 'Animation', 'bdthemes-element-pack' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'shift-toward',
			'options' => [
				'shift-away'   => esc_html__( 'Shift-Away', 'bdthemes-element-pack' ),
				'shift-toward' => esc_html__( 'Shift-Toward', 'bdthemes-element-pack' ),
				'fade'         => esc_html__( 'Fade', 'bdthemes-element-pack' ),
				'scale'        => esc_html__( 'Scale', 'bdthemes-element-pack' ),
				'perspective'  => esc_html__( 'Perspective', 'bdthemes-element-pack' ),
			],
		];

		if ( $args['animation_render_type'] ) {
			$animation_control['render_type'] = 'template';
		}

		$this->add_control( 'logo_tooltip_animation', $animation_control );

		$this->add_control(
			'logo_tooltip_x_offset',
			[
				'label'   => $args['x_offset_label'],
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
			]
		);

		$this->add_control(
			'logo_tooltip_y_offset',
			[
				'label'   => $args['y_offset_label'],
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
			]
		);

		$this->add_control(
			'logo_tooltip_arrow',
			[
				'label' => esc_html__( 'Arrow', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'logo_tooltip_trigger',
			[
				'label'       => esc_html__( 'Trigger on Click', 'bdthemes-element-pack' ),
				'description' => esc_html__( 'Don\'t set yes when you set lightbox image with marker.', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SWITCHER,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register logo tooltip style controls.
	 *
	 * @param array $args {
	 *     @type string $widget_type e.g. 'grid' or 'carousel'.
	 * }
	 */
	protected function register_logo_tooltip_style_controls( $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'widget_type' => 'grid',
			]
		);

		$is_grid = 'grid' === $args['widget_type'];

		$this->start_controls_section(
			'section_style_tooltip',
			[
				'label' => esc_html__( 'Tooltip', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'logo_tooltip_width',
			[
				'label'       => esc_html__( 'Width', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px', 'em' ],
				'range'       => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors'   => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => $is_grid
						? 'max-width: calc({{SIZE}}{{UNIT}} - 10px) !important;'
						: 'width: {{SIZE}}{{UNIT}};',
				],
				'render_type' => 'template',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'logo_tooltip_typography',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		if ( $is_grid ) {
			$this->add_control(
				'logo_tooltip_title_color',
				[
					'label'     => esc_html__( 'Title Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .bdt-title' => 'color: {{VALUE}}',
					],
				]
			);
		}

		$this->add_control(
			'logo_tooltip_color',
			[
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'logo_tooltip_text_align',
			[
				'label'     => esc_html__( 'Text Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'logo_tooltip_background',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"], .tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-backdrop',
			]
		);

		$arrow_selectors = $is_grid
			? [ '.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow' => 'color: {{VALUE}}' ]
			: [
				'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow' => 'border-left-color: {{VALUE}}',
				'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow' => 'border-right-color: {{VALUE}}',
				'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow' => 'border-top-color: {{VALUE}}',
				'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow' => 'border-bottom-color: {{VALUE}}',
				'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow' => 'color: {{VALUE}}',
			];

		$this->add_control(
			'logo_tooltip_arrow_color',
			[
				'label'     => esc_html__( 'Arrow Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $arrow_selectors,
			]
		);

		$this->add_responsive_control(
			'logo_tooltip_padding',
			[
				'label'       => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => [ 'px', '%' ],
				'selectors'   => [
					$is_grid
						? '.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-content'
						: '.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'render_type' => 'template',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'logo_tooltip_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->add_responsive_control(
			'logo_tooltip_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'logo_tooltip_box_shadow',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Get parsed logo tooltip settings from widget settings.
	 *
	 * @param array $settings Widget settings.
	 */
	protected function get_logo_tooltip_config( $settings ) {
		return [
			'animation' => isset( $settings['logo_tooltip_animation'] ) ? sanitize_key( $settings['logo_tooltip_animation'] ) : '',
			'x_offset'  => isset( $settings['logo_tooltip_x_offset']['size'] ) ? (int) $settings['logo_tooltip_x_offset']['size'] : 0,
			'y_offset'  => isset( $settings['logo_tooltip_y_offset']['size'] ) ? (int) $settings['logo_tooltip_y_offset']['size'] : 0,
			'arrow'     => ! empty( $settings['logo_tooltip_arrow'] ) && 'yes' === $settings['logo_tooltip_arrow'],
			'trigger'   => ! empty( $settings['logo_tooltip_trigger'] ) && 'yes' === $settings['logo_tooltip_trigger'],
		];
	}

	/**
	 * Build logo tooltip content HTML.
	 *
	 * @param array  $item   Repeater item.
	 * @param string $format e.g. 'grid' or 'carousel'.
	 */
	protected function get_logo_tooltip_content( $item, $format = 'grid' ) {
		$item_name        = isset( $item['name'] ) ? $item['name'] : '';
		$item_description = isset( $item['description'] ) ? $item['description'] : '';

		if ( 'carousel' === $format ) {
			return '<div><strong>' . esc_html( $item_name ) . '</strong></div>' . esc_html( $item_description );
		}

		$tooltip_content = '<span class="bdt-title">' . esc_html( $item_name ) . '</span>' . esc_html( $item_description );
		$allowed_tags    = '<p><b><strong><em><u><span><br><i>';

		return strip_tags( $tooltip_content, $allowed_tags );
	}

	/**
	 * Add tooltip render attributes to a logo item.
	 *
	 * @param string $key      Render attribute key.
	 * @param array  $item     Repeater item.
	 * @param array  $settings Widget settings.
	 * @param array  $args {
	 *     @type string $format e.g. 'grid' or 'carousel'.
	 *     @type bool   $overwrite Overwrite existing attributes.
	 * }
	 */
	protected function add_logo_tooltip_attributes( $key, $item, $settings, $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'format'    => 'grid',
				'overwrite' => true,
			]
		);

		$item_name         = isset( $item['name'] ) ? $item['name'] : '';
		$item_description  = isset( $item['description'] ) ? $item['description'] : '';
		$item_logo_tooltip = ! empty( $item['logo_tooltip'] ) && 'yes' === $item['logo_tooltip'];
		$tooltip_config    = $this->get_logo_tooltip_config( $settings );
		$overwrite         = $args['overwrite'];
		$has_tooltip       = ! empty( $item_name ) && ! empty( $item_description ) && $item_logo_tooltip;

		if ( 'grid' === $args['format'] || $has_tooltip ) {
			$this->add_render_attribute( $key, 'data-tippy-content', wp_kses_post( $this->get_logo_tooltip_content( $item, $args['format'] ) ), $overwrite );
		}

		if ( ! $has_tooltip ) {
			return;
		}

		$this->add_render_attribute( $key, 'class', 'bdt-tippy-tooltip', false );
		$this->add_render_attribute( $key, 'data-tippy', '', $overwrite );

		if ( ! empty( $item['logo_tooltip_placement'] ) ) {
			$this->add_render_attribute( $key, 'data-tippy-placement', esc_attr( $item['logo_tooltip_placement'] ), $overwrite );
		}

		if ( ! empty( $tooltip_config['animation'] ) ) {
			$this->add_render_attribute( $key, 'data-tippy-animation', esc_attr( $tooltip_config['animation'] ), $overwrite );
		}

		if ( 0 !== $tooltip_config['x_offset'] || 0 !== $tooltip_config['y_offset'] ) {
			$this->add_render_attribute(
				$key,
				'data-tippy-offset',
				'[' . esc_js( (string) $tooltip_config['x_offset'] ) . ',' . esc_js( (string) $tooltip_config['y_offset'] ) . ']',
				$overwrite
			);
		}

		$this->add_render_attribute( $key, 'data-tippy-arrow', $tooltip_config['arrow'] ? 'true' : 'false', $overwrite );

		if ( $tooltip_config['trigger'] ) {
			$this->add_render_attribute( $key, 'data-tippy-trigger', 'click', $overwrite );
		}
	}

	/**
	 * Get logo image alt text.
	 *
	 * @param array $item Repeater item.
	 */
	protected function get_logo_image_alt( $item ) {
		$item_name        = isset( $item['name'] ) ? $item['name'] : '';
		$item_description = isset( $item['description'] ) ? $item['description'] : '';

		return $item_name . ' : ' . $item_description;
	}
	/**
	 * Build a review card widget CSS selector.
	 *
	 * @param string $widget_prefix e.g. 'review-card', 'review-card-grid', 'review-card-carousel'.
	 * @param string $element       e.g. 'item', 'image', 'name', 'job-title', 'text', 'rating', 'content'.
	 */
	protected function get_review_card_selector( $widget_prefix, $element ) {
		return '{{WRAPPER}} .bdt-ep-' . $widget_prefix . '-' . $element;
	}

	/**
	 * Get normalized review card rating value.
	 *
	 * @param array $item Repeater item or widget settings.
	 * @param float $min  Minimum rating value.
	 */
	protected function get_review_card_rating_value( $item, $min = 0 ) {
		if ( ! isset( $item['rating_number'] ) ) {
			return (float) $min;
		}

		$rating = $item['rating_number'];

		if ( is_array( $rating ) && isset( $rating['size'] ) && '' !== $rating['size'] ) {
			$rating = $rating['size'];
		}

		return min( 5.0, max( (float) $min, (float) $rating ) );
	}

	/**
	 * Get review card star icon classes for each of the five stars.
	 *
	 * @param array $item Repeater item or widget settings.
	 * @param float $min  Minimum rating value.
	 * @return string[]
	 */
	protected function get_review_card_rating_star_icons( $item, $min = 0 ) {
		$rating     = $this->get_review_card_rating_value( $item, $min );
		$full_stars = (int) floor( $rating );
		$has_half   = ( $rating - $full_stars ) >= 0.495;
		$icons      = [];

		for ( $i = 1; $i <= 5; $i++ ) {
			if ( $i <= $full_stars ) {
				$icons[] = 'ep-icon-star-full';
			} elseif ( $has_half && $i === ( $full_stars + 1 ) ) {
				$icons[] = 'ep-icon-star-half';
			} else {
				$icons[] = 'ep-icon-star-empty';
			}
		}

		return $icons;
	}

	/**
	 * Calculate review card star rating score string.
	 *
	 * @param array $item Repeater item or widget settings.
	 * @param float $min  Minimum rating value.
	 */
	protected function get_review_card_rating_score( $item, $min = 0 ) {
		$rating     = $this->get_review_card_rating_value( $item, $min );
		$full_stars = (int) floor( $rating );
		$half       = ( $rating - $full_stars ) >= 0.495 ? 5 : 0;

		return $full_stars . '-' . $half;
	}

	/**
	 * Register single review card content controls.
	 */
	protected function register_review_card_single_content_controls() {
		$this->start_controls_section(
			'section_reviewer_content',
			[
				'label' => __( 'Review Card', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'image',
			[
				'label'       => __( 'Image', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::MEDIA,
				'render_type' => 'template',
				'dynamic'     => [
					'active' => true,
				],
				'default'     => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_control(
			'reviewer_name',
			[
				'label'       => __( 'Name', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => __( 'Adam Smith', 'bdthemes-element-pack' ),
				'placeholder' => __( 'Enter reviewer name', 'bdthemes-element-pack' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'reviewer_job_title',
			[
				'label'       => __( 'Job Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => __( 'SEO Expert', 'bdthemes-element-pack' ),
				'placeholder' => __( 'Enter reviewer job title', 'bdthemes-element-pack' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'rating_number',
			[
				'label'      => __( 'Rating', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => 4.5,
				],
				'range'      => [
					'px' => [
						'min'  => .5,
						'max'  => 5,
						'step' => .5,
					],
				],
				'dynamic'    => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'review_text',
			[
				'label'       => __( 'Review Text', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::WYSIWYG,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bdthemes-element-pack' ),
				'placeholder' => __( 'Enter review text', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'review_words_length',
			[
				'label'       => __( 'Limit Words', 'bdthemes-element-pack' ),
				'description' => __( 'Leave blank to show full text.', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::NUMBER,
				'condition'   => [
					'review_text!' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register review card repeater content controls for grid/carousel.
	 *
	 * @param array $args {
	 *     @type string $section_label Section label.
	 *     @type float  $rating_min    Minimum rating slider value.
	 * }
	 */
	protected function register_review_card_repeater_content_controls( $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'section_label' => __( 'Review Card Items', 'bdthemes-element-pack' ),
				'rating_min'    => 0,
			]
		);

		$this->start_controls_section(
			'section_reviewer_content',
			[
				'label' => $args['section_label'],
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label'       => __( 'Image', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::MEDIA,
				'render_type' => 'template',
				'dynamic'     => [
					'active' => true,
				],
				'default'     => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'reviewer_name',
			[
				'label'       => __( 'Name', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => __( 'Adam Smith', 'bdthemes-element-pack' ),
				'placeholder' => __( 'Enter reviewer name', 'bdthemes-element-pack' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'reviewer_job_title',
			[
				'label'       => __( 'Job Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => __( 'SEO Expert', 'bdthemes-element-pack' ),
				'placeholder' => __( 'Enter reviewer job title', 'bdthemes-element-pack' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'rating_number',
			[
				'label'      => __( 'Rating', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => 4.5,
				],
				'range'      => [
					'px' => [
						'min'  => $args['rating_min'],
						'max'  => 5,
						'step' => .5,
					],
				],
				'dynamic'    => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'review_text',
			[
				'label'       => __( 'Review Text', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::WYSIWYG,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bdthemes-element-pack' ),
				'placeholder' => __( 'Enter review text', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'review_items',
			[
				'show_label'  => false,
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ reviewer_name }}}',
				'default'     => [
					[
						'reviewer_name'      => __( 'Adam Smith', 'bdthemes-element-pack' ),
						'reviewer_job_title' => __( 'SEO Expert', 'bdthemes-element-pack' ),
					],
					[
						'reviewer_name'      => __( 'Jhon Deo', 'bdthemes-element-pack' ),
						'reviewer_job_title' => __( 'Web Desiger', 'bdthemes-element-pack' ),
					],
					[
						'reviewer_name'      => __( 'Maria Mak', 'bdthemes-element-pack' ),
						'reviewer_job_title' => __( 'Web Expert', 'bdthemes-element-pack' ),
					],
					[
						'reviewer_name'      => __( 'Jackma Kalin', 'bdthemes-element-pack' ),
						'reviewer_job_title' => __( 'Elementor Expert', 'bdthemes-element-pack' ),
					],
					[
						'reviewer_name'      => __( 'Amily Moalin', 'bdthemes-element-pack' ),
						'reviewer_job_title' => __( 'WP Officer', 'bdthemes-element-pack' ),
					],
					[
						'reviewer_name'      => __( 'Enagol Ame', 'bdthemes-element-pack' ),
						'reviewer_job_title' => __( 'WP Developer', 'bdthemes-element-pack' ),
					],
				],
			]
		);

		$this->add_control(
			'review_words_length',
			[
				'label'       => __( 'Limit Words', 'bdthemes-element-pack' ),
				'description' => __( 'Leave blank to show full text.', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::NUMBER,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register shared review card additional settings controls.
	 *
	 * @param string $widget_prefix e.g. 'review-card', 'review-card-grid', 'review-card-carousel'.
	 * @param array  $args {
	 *     @type bool   $is_single              Single widget field conditions.
	 *     @type string $image_position_labels  'left_right' or 'start_end'.
	 *     @type bool   $section_wrapper        Start/end the controls section.
	 * }
	 */
	protected function register_review_card_additional_settings_controls( $widget_prefix, $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'is_single'              => false,
				'image_position_labels'  => 'left_right',
				'section_wrapper'        => true,
			]
		);

		$item_selector  = $this->get_review_card_selector( $widget_prefix, 'item' );
		$image_selector = $this->get_review_card_selector( $widget_prefix, 'image' );

		$image_position_options = [
			'top' => [
				'title' => __( 'Top', 'bdthemes-element-pack' ),
				'icon'  => 'eicon-v-align-top',
			],
		];

		if ( 'start_end' === $args['image_position_labels'] ) {
			$image_position_options['left']  = [
				'title' => __( 'Start', 'bdthemes-element-pack' ),
				'icon'  => 'eicon-h-align-left',
			];
			$image_position_options['right'] = [
				'title' => __( 'End', 'bdthemes-element-pack' ),
				'icon'  => 'eicon-h-align-right',
			];
		} else {
			$image_position_options['left']  = [
				'title' => __( 'Left', 'bdthemes-element-pack' ),
				'icon'  => 'eicon-h-align-left',
			];
			$image_position_options['right'] = [
				'title' => __( 'Right', 'bdthemes-element-pack' ),
				'icon'  => 'eicon-h-align-right',
			];
		}

		$show_name_condition = $args['is_single'] ? [ 'reviewer_name!' => '' ] : [];
		$name_tag_condition  = $args['is_single']
			? [
				'show_reviewer_name' => 'yes',
				'reviewer_name!'     => '',
			]
			: [
				'show_reviewer_name' => 'yes',
			];
		$show_job_condition  = $args['is_single'] ? [ 'reviewer_job_title!' => '' ] : [];
		$show_text_condition = $args['is_single'] ? [ 'review_text!' => '' ] : [];

		if ( $args['section_wrapper'] ) {
			$this->start_controls_section(
				'section_review_additional_settings',
				[
					'label' => __( 'Additional Settings', 'bdthemes-element-pack' ),
					'tab'   => Controls_Manager::TAB_CONTENT,
				]
			);
		}

		$this->add_control(
			'show_reviewer_name',
			array_merge(
				[
					'label'   => __( 'Show Name', 'bdthemes-element-pack' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				],
				$args['is_single'] ? [ 'condition' => $show_name_condition ] : [ 'separator' => 'before' ]
			)
		);

		$this->add_control(
			'review_name_tag',
			[
				'label'     => __( 'Name HTML Tag', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h3',
				'options'   => element_pack_title_tags(),
				'condition' => $name_tag_condition,
			]
		);

		$this->add_control(
			'show_reviewer_job_title',
			array_merge(
				[
					'label'     => __( 'Show Job Title', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'separator' => 'before',
				],
				$args['is_single'] ? [ 'condition' => $show_job_condition ] : []
			)
		);

		$this->add_control(
			'show_rating',
			[
				'label'     => __( 'Show Rating', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'rating_type',
			[
				'label'     => __( 'Rating Type', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'star',
				'options'   => [
					'star'   => __( 'Star', 'bdthemes-element-pack' ),
					'number' => __( 'Number', 'bdthemes-element-pack' ),
				],
				'condition' => [
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_control(
			'rating_position',
			[
				'label'     => __( 'Rating Position', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'before',
				'options'   => [
					'before' => __( 'Before Review Text', 'bdthemes-element-pack' ),
					'after'  => __( 'After Review Text', 'bdthemes-element-pack' ),
				],
				'condition' => [
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_review_text',
			array_merge(
				[
					'label'     => __( 'Show Review Text', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'separator' => 'before',
				],
				$args['is_single'] ? [ 'condition' => $show_text_condition ] : []
			)
		);

		$this->add_control(
			'show_reviewer_image',
			[
				'label'     => __( 'Show Image', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumbnail_size',
				'default'   => 'medium',
				'condition' => [
					'show_reviewer_image' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'iamge_position',
			[
				'label'                => __( 'Image Position', 'bdthemes-element-pack' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'top',
				'toggle'               => false,
				'options'              => $image_position_options,
				'condition'            => [
					'show_reviewer_image' => 'yes',
				],
				'selectors_dictionary' => [
					'left'  => 'display: flex; align-items: center; flex-direction: row;',
					'right' => 'display: flex; align-items: center; flex-direction: row-reverse; text-align: right;',
					'top'   => 'display: flex; flex-direction: column; text-align: left;',
				],
				'selectors'            => [
					$item_selector => '{{VALUE}};',
				],
				'render_type'          => 'template',
			]
		);

		$this->add_control(
			'image_inline',
			[
				'label'        => esc_html__( 'Image Inline', 'bdthemes-element-pack' ),
				'description'  => esc_html__( 'This option only works for left and right image position and it\'s not working on responsive mode.', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'condition'    => [
					'iamge_position!'     => 'top',
					'show_reviewer_image' => 'yes',
				],
				'prefix_class' => 'bdt-review-img-inline--',
				'render_type'  => 'template',
			]
		);

		$this->add_responsive_control(
			'iamge_alignment',
			[
				'label'     => __( 'Image Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'flex-start',
				'options'   => [
					'flex-start' => [
						'title' => __( 'Start', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center'     => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'flex-end'   => [
						'title' => __( 'End', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					$image_selector => 'align-self: {{VALUE}};',
				],
				'condition' => [
					'image_inline!'       => 'yes',
					'show_reviewer_image' => 'yes',
				],
			]
		);

		$this->add_control(
			'image_mask_popover',
			[
				'label'        => esc_html__( 'Image Mask', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'render_type'  => 'template',
				'return_value' => 'yes',
				'condition'    => [
					'show_reviewer_image' => 'yes',
				],
			]
		);

		$this->register_image_mask_controls();

		$this->add_responsive_control(
			'text_align',
			[
				'label'     => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'start',
				'options'   => [
					'start'   => [
						'title' => __( 'Start', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'end'     => [
						'title' => __( 'End', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					$item_selector => 'text-align: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		if ( $args['section_wrapper'] ) {
			$this->end_controls_section();
		}
	}

	/**
	 * Register review card item style controls.
	 *
	 * @param string $widget_prefix e.g. 'review-card', 'review-card-grid', 'review-card-carousel'.
	 * @param array  $args {
	 *     @type bool $is_single            Single widget uses card_item_* control names.
	 *     @type bool $show_shadow_padding  Carousel match padding control.
	 * }
	 */
	protected function register_review_card_style_item_controls( $widget_prefix, $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'is_single'           => false,
				'show_shadow_padding' => false,
			]
		);

		$item_selector       = $this->get_review_card_selector( $widget_prefix, 'item' );
		$item_hover_selector = $item_selector . ':hover';

		if ( $args['is_single'] ) {
			$this->start_controls_section(
				'section_style_card_item',
				[
					'label' => __( 'Item', 'bdthemes-element-pack' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'card_item_background',
					'selector' => $item_selector,
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'     => 'card_item_border',
					'selector' => $item_selector,
				]
			);

			$this->add_responsive_control(
				'card_item_border_radius',
				[
					'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						$item_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'card_item_box_shadow',
					'selector' => $item_selector,
				]
			);

			$this->add_responsive_control(
				'card_item_padding',
				[
					'label'      => __( 'Padding', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						$item_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'card_item_margin',
				[
					'label'      => __( 'Margin', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						$item_selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_section();

			return;
		}

		$this->start_controls_section(
			'section_style_review_items',
			[
				'label' => esc_html__( 'Items', 'bdthemes-element-pack' ),
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

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_background',
				'selector' => $item_selector,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'item_border',
				'label'          => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => false,
						],
					],
					'color'  => [
						'default' => '#eee',
					],
				],
				'selector'       => $item_selector,
				'separator'      => 'before',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$item_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$item_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => $item_selector,
			]
		);

		if ( $args['show_shadow_padding'] ) {
			$this->add_responsive_control(
				'item_shadow_padding',
				[
					'label'       => __( 'Match Padding', 'bdthemes-element-pack' ),
					'description' => __( 'You have to add padding for matching overlaping normal/hover box shadow when you used Box Shadow option.', 'bdthemes-element-pack' ),
					'type'        => Controls_Manager::SLIDER,
					'range'       => [
						'px' => [
							'min'  => 0,
							'step' => 1,
							'max'  => 50,
						],
					],
					'selectors'   => [
						'{{WRAPPER}} .swiper-carousel' => 'padding: {{SIZE}}{{UNIT}}; margin: 0 -{{SIZE}}{{UNIT}};',
					],
				]
			);
		}

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_hover_background',
				'selector' => $item_hover_selector,
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
					$item_hover_selector => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_hover_box_shadow',
				'selector' => $item_hover_selector,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register review card image style controls.
	 *
	 * @param string $widget_prefix e.g. 'review-card', 'review-card-grid', 'review-card-carousel'.
	 * @param array  $args {
	 *     @type bool $is_single         Single widget uses iamge_* control names and image margin.
	 *     @type bool $advanced_size_nc  Append BDTEP_NC to advanced size label.
	 * }
	 */
	protected function register_review_card_style_image_controls( $widget_prefix, $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'is_single'        => false,
				'advanced_size_nc' => false,
			]
		);

		$image_selector     = $this->get_review_card_selector( $widget_prefix, 'image' );
		$image_img_selector = $image_selector . ' img';
		$item_selector      = $this->get_review_card_selector( $widget_prefix, 'item' );
		$radius_control     = $args['is_single'] ? 'iamge_radius' : 'image_radius';
		$padding_control    = $args['is_single'] ? 'iamge_padding' : 'image_padding';
		$offset_var_prefix  = $widget_prefix;

		$this->start_controls_section(
			'section_style_image',
			[
				'label'     => __( 'Image', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_reviewer_image' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'image_border',
				'selector' => $image_img_selector,
			]
		);

		$this->add_responsive_control(
			$radius_control,
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$image_img_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			$padding_control,
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$image_img_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		if ( $args['is_single'] ) {
			$this->add_responsive_control(
				'iamge_margin',
				[
					'label'      => __( 'Margin', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						$image_selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
		}

		$this->add_responsive_control(
			'image_size',
			[
				'label'     => __( 'Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 10,
						'max' => 500,
					],
				],
				'selectors' => [
					$image_selector => 'height: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'image_size_popover!' => 'yes',
				],
			]
		);

		$advanced_size_label = esc_html__( 'Advanced Size', 'bdthemes-element-pack' );
		if ( $args['advanced_size_nc'] ) {
			$advanced_size_label .= BDTEP_NC;
		}

		$this->add_control(
			'image_size_popover',
			[
				'label'        => $advanced_size_label,
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'render_type'  => 'ui',
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'image_height',
			[
				'label'       => __( 'Height', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min' => 10,
						'max' => 500,
					],
				],
				'selectors'   => [
					$image_selector => 'height: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
				],
				'condition'   => [
					'image_size_popover' => 'yes',
				],
				'render_type' => 'ui',
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label'       => __( 'Width', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min' => 10,
						'max' => 500,
					],
				],
				'selectors'   => [
					$image_selector => 'width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
				],
				'condition'   => [
					'image_size_popover' => 'yes',
				],
				'render_type' => 'ui',
			]
		);

		$this->end_popover();

		$this->add_responsive_control(
			'image_spacing',
			[
				'label'     => __( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 15,
				],
				'selectors' => [
					$item_selector . ', {{WRAPPER}}.bdt-review-img-inline--yes .bdt-ep-img-inline' => 'grid-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'css_filters',
				'selector' => $image_img_selector,
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_shadow',
				'selector' => $image_img_selector,
			]
		);

		$this->add_control(
			'image_offset_toggle',
			[
				'label'        => __( 'Offset', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => __( 'None', 'bdthemes-element-pack' ),
				'label_on'     => __( 'Custom', 'bdthemes-element-pack' ),
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'image_horizontal_offset',
			[
				'label'          => __( 'Horizontal', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range'          => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'condition'      => [
					'image_offset_toggle' => 'yes',
				],
				'render_type'    => 'ui',
				'selectors'      => [
					'{{WRAPPER}}' => '--ep-' . $offset_var_prefix . '-image-h-offset: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'image_vertical_offset',
			[
				'label'          => __( 'Vertical', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range'          => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'condition'      => [
					'image_offset_toggle' => 'yes',
				],
				'render_type'    => 'ui',
				'selectors'      => [
					'{{WRAPPER}}' => '--ep-' . $offset_var_prefix . '-image-v-offset: {{SIZE}}px;',
				],
			]
		);

		$this->end_popover();

		$this->end_controls_section();
	}

	/**
	 * Register review card name style controls.
	 *
	 * @param string $widget_prefix e.g. 'review-card', 'review-card-grid', 'review-card-carousel'.
	 * @param array  $args {
	 *     @type bool $is_single Single widget without hover color and field conditions.
	 * }
	 */
	protected function register_review_card_style_name_controls( $widget_prefix, $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'is_single' => false,
			]
		);

		$name_selector       = $this->get_review_card_selector( $widget_prefix, 'name' );
		$item_selector       = $this->get_review_card_selector( $widget_prefix, 'item' );
		$section_condition   = $args['is_single']
			? [
				'show_reviewer_name' => 'yes',
				'reviewer_name!'     => '',
			]
			: [
				'show_reviewer_name' => 'yes',
			];

		$this->start_controls_section(
			'section_style_name',
			[
				'label'     => __( 'Name', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => $section_condition,
			]
		);

		$this->add_control(
			'name_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$name_selector => 'color: {{VALUE}};',
				],
			]
		);

		if ( ! $args['is_single'] ) {
			$this->add_control(
				'name_hover_color',
				[
					'label'     => __( 'Hover Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						$item_selector . ':hover .bdt-ep-' . $widget_prefix . '-name' => 'color: {{VALUE}};',
					],
				]
			);
		}

		$this->add_responsive_control(
			'name_bottom_space',
			[
				'label'     => __( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					$name_selector => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'name_typography',
				'selector' => $name_selector,
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'name_shadow',
				'label'    => __( 'Text Shadow', 'bdthemes-element-pack' ),
				'selector' => $name_selector,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register review card job title style controls.
	 *
	 * @param string $widget_prefix e.g. 'review-card', 'review-card-grid', 'review-card-carousel'.
	 * @param array  $args {
	 *     @type bool $is_single Single widget without hover color and field conditions.
	 * }
	 */
	protected function register_review_card_style_job_title_controls( $widget_prefix, $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'is_single' => false,
			]
		);

		$job_title_selector = $this->get_review_card_selector( $widget_prefix, 'job-title' );
		$item_selector      = $this->get_review_card_selector( $widget_prefix, 'item' );
		$section_condition  = $args['is_single']
			? [
				'show_reviewer_job_title' => 'yes',
				'reviewer_job_title!'     => '',
			]
			: [
				'show_reviewer_job_title' => 'yes',
			];

		$this->start_controls_section(
			'section_style_job_title',
			[
				'label'     => __( 'Job Title', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => $section_condition,
			]
		);

		$this->add_control(
			'job_title_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$job_title_selector => 'color: {{VALUE}};',
				],
			]
		);

		if ( ! $args['is_single'] ) {
			$this->add_control(
				'job_title_hover_color',
				[
					'label'     => __( 'Hover Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						$item_selector . ':hover .bdt-ep-' . $widget_prefix . '-job-title' => 'color: {{VALUE}};',
					],
				]
			);
		}

		$this->add_responsive_control(
			'job_title_bottom_space',
			[
				'label'     => __( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					$job_title_selector => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'job_title_typography',
				'selector' => $job_title_selector,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register review card text style controls.
	 *
	 * @param string $widget_prefix e.g. 'review-card', 'review-card-grid', 'review-card-carousel'.
	 * @param array  $args {
	 *     @type bool $is_single       Single widget field conditions.
	 *     @type bool $text_margin_nc               Append BDTEP_NC to margin label.
	 *     @type bool $text_margin_before_typography Margin control before typography (carousel).
	 * }
	 */
	protected function register_review_card_style_text_controls( $widget_prefix, $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'is_single'                     => false,
				'text_margin_nc'                => false,
				'text_margin_before_typography' => false,
			]
		);

		$text_selector     = $this->get_review_card_selector( $widget_prefix, 'text' );
		$item_selector     = $this->get_review_card_selector( $widget_prefix, 'item' );
		$section_condition = $args['is_single']
			? [
				'show_review_text' => 'yes',
				'review_text!'     => '',
			]
			: [
				'show_review_text' => 'yes',
			];

		$this->start_controls_section(
			'section_style_text',
			[
				'label'     => __( 'Text', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => $section_condition,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$text_selector => 'color: {{VALUE}};',
				],
			]
		);

		if ( ! $args['is_single'] ) {
			$this->add_control(
				'text_hover_color',
				[
					'label'     => __( 'Hover Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						$item_selector . ':hover .bdt-ep-' . $widget_prefix . '-text' => 'color: {{VALUE}};',
					],
				]
			);
		}

		$margin_label = __( 'Margin', 'bdthemes-element-pack' );
		if ( $args['text_margin_nc'] ) {
			$margin_label .= BDTEP_NC;
		}

		$text_margin_control = [
			'label'      => $margin_label,
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors'  => [
				$text_selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		];

		if ( $args['text_margin_before_typography'] ) {
			$this->add_responsive_control( 'text_margin', $text_margin_control );
		}

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'selector' => $text_selector,
			]
		);

		if ( ! $args['text_margin_before_typography'] ) {
			$this->add_responsive_control( 'text_margin', $text_margin_control );
		}

		$this->end_controls_section();
	}

	/**
	 * Register review card rating style controls.
	 *
	 * @param string $widget_prefix e.g. 'review-card', 'review-card-grid', 'review-card-carousel'.
	 */
	protected function register_review_card_style_rating_controls( $widget_prefix ) {
		$rating_selector = $this->get_review_card_selector( $widget_prefix, 'rating' );

		$space_between_selectors = [
			$rating_selector . ' i + i' => 'margin-left: {{SIZE}}{{UNIT}};',
		];

		if ( 'review-card-carousel' === $widget_prefix ) {
			$space_between_selectors[ $rating_selector . ' span.epsc-rating span' ] = 'margin-right: {{SIZE}}{{UNIT}};';
		} else {
			$space_between_selectors[ $rating_selector . ' span' ] = 'margin-right: {{SIZE}}{{UNIT}};';
		}

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
			'rating_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e7e7e7',
				'selectors' => [
					'{{WRAPPER}} .epsc-rating-item .ep-icon-star-empty' => 'color: {{VALUE}};',
				],
				'condition' => [
					'rating_type' => 'star',
				],
			]
		);

		$this->add_control(
			'active_rating_color',
			[
				'label'     => esc_html__( 'Active Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFCC00',
				'selectors' => [
					'{{WRAPPER}} .epsc-rating-item .ep-icon-star-full, {{WRAPPER}} .epsc-rating-item .ep-icon-star-half' => 'color: {{VALUE}};',
				],
				'condition' => [
					'rating_type' => 'star',
				],
			]
		);

		$this->add_control(
			'rating_number_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					$rating_selector => 'color: {{VALUE}};',
				],
				'condition' => [
					'rating_type' => 'number',
				],
			]
		);

		$this->add_control(
			'rating_background_color',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1e87f0',
				'selectors' => [
					$rating_selector => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'rating_type' => 'number',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'rating_border',
				'selector'  => $rating_selector,
				'condition' => [
					'rating_type' => 'number',
				],
			]
		);

		$this->add_responsive_control(
			'rating_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$rating_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'rating_type' => 'number',
				],
			]
		);

		$this->add_responsive_control(
			'rating_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$rating_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'rating_type' => 'number',
				],
			]
		);

		$this->add_responsive_control(
			'rating_margin',
			[
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$rating_selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rating_size',
			[
				'label'     => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					$rating_selector => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rating_space_between',
			[
				'label'     => esc_html__( 'Space Between', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'selectors' => $space_between_selectors,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register review card additional style controls.
	 *
	 * @param string $widget_prefix e.g. 'review-card', 'review-card-grid', 'review-card-carousel'.
	 */
	protected function register_review_card_style_additional_controls( $widget_prefix ) {
		$content_selector = $this->get_review_card_selector( $widget_prefix, 'content' );

		$this->start_controls_section(
			'section_additional_style',
			[
				'label' => esc_html__( 'Additional Style', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'additional_margin',
			[
				'label'      => esc_html__( 'Content Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					$content_selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'additional_padding',
			[
				'label'      => esc_html__( 'Content Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					$content_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register review card read more link style controls.
	 */
	protected function register_review_card_read_more_style_controls() {
		$this->start_controls_section(
			'section_gb_words_limit_style',
			[
				'label'     => esc_html__( 'Read More Link', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'review_words_length!' => '',
				],
			]
		);

		$this->gloabl_read_more_link_style_controls();

		$this->end_controls_section();
	}

	/**
	 * Render review card image.
	 *
	 * @param array  $item          Repeater item or widget settings.
	 * @param array  $settings      Widget settings.
	 * @param string $widget_prefix e.g. 'review-card', 'review-card-grid', 'review-card-carousel'.
	 * @param bool   $overwrite     Overwrite render attributes.
	 */
	protected function render_review_card_image( $item, $settings, $widget_prefix, $overwrite = true ) {
		if ( empty( $settings['show_reviewer_image'] ) || empty( $item['image']['url'] ) ) {
			return;
		}

		$image_mask    = ! empty( $settings['image_mask_popover'] ) && 'yes' === $settings['image_mask_popover'] ? ' bdt-image-mask' : '';
		$attachment_id = ! empty( $item['image']['id'] ) ? (int) $item['image']['id'] : 0;
		$thumb_url     = $attachment_id ? Group_Control_Image_Size::get_attachment_image_src( $attachment_id, 'thumbnail_size', $settings ) : '';
		$alt           = ! empty( $item['reviewer_name'] ) ? esc_attr( $item['reviewer_name'] ) : '';

		$this->add_render_attribute( 'image-wrap', 'class', 'bdt-ep-' . $widget_prefix . '-image' . $image_mask, $overwrite );

		?>
		<div <?php $this->print_render_attribute_string( 'image-wrap' ); ?>>
			<?php
			if ( empty( $thumb_url ) ) {
				printf( '<img src="%1$s" alt="%2$s">', esc_url( $item['image']['url'] ), $alt );
			} else {
				print wp_get_attachment_image(
					$attachment_id,
					$settings['thumbnail_size_size'],
					false,
					[
						'alt' => $alt,
					]
				);
			}
			?>
		</div>
		<?php
	}

	/**
	 * Render review card name.
	 *
	 * @param array  $item          Repeater item or widget settings.
	 * @param array  $settings      Widget settings.
	 * @param string $widget_prefix e.g. 'review-card', 'review-card-grid', 'review-card-carousel'.
	 * @param bool   $overwrite     Overwrite render attributes.
	 */
	protected function render_review_card_name( $item, $settings, $widget_prefix, $overwrite = true ) {
		if ( empty( $settings['show_reviewer_name'] ) || empty( $item['reviewer_name'] ) ) {
			return;
		}

		$this->add_render_attribute( 'review-name', 'class', 'bdt-ep-' . $widget_prefix . '-name', $overwrite );
		$tag = Utils::get_valid_html_tag( $settings['review_name_tag'] ?? 'h3' );

		?>
		<<?php echo esc_attr( $tag ); ?> <?php $this->print_render_attribute_string( 'review-name' ); ?>>
			<?php echo wp_kses( $item['reviewer_name'], element_pack_allow_tags( 'title' ) ); ?>
		</<?php echo esc_attr( $tag ); ?>>
		<?php
	}

	/**
	 * Render review card job title.
	 *
	 * @param array  $item          Repeater item or widget settings.
	 * @param array  $settings      Widget settings.
	 * @param string $widget_prefix e.g. 'review-card', 'review-card-grid', 'review-card-carousel'.
	 */
	protected function render_review_card_job_title( $item, $settings, $widget_prefix ) {
		if ( empty( $settings['show_reviewer_job_title'] ) || empty( $item['reviewer_job_title'] ) ) {
			return;
		}

		?>
		<div class="bdt-ep-<?php echo esc_attr( $widget_prefix ); ?>-job-title">
			<?php echo esc_html( $item['reviewer_job_title'] ); ?>
		</div>
		<?php
	}

	/**
	 * Render review card text.
	 *
	 * @param array  $item          Repeater item or widget settings.
	 * @param array  $settings      Widget settings.
	 * @param string $widget_prefix e.g. 'review-card', 'review-card-grid', 'review-card-carousel'.
	 * @param bool   $overwrite     Overwrite render attributes.
	 */
	protected function render_review_card_text( $item, $settings, $widget_prefix, $overwrite = true ) {
		if ( empty( $settings['show_review_text'] ) || empty( $item['review_text'] ) ) {
			return;
		}

		$text_class = 'bdt-ep-' . $widget_prefix . '-text';

		$this->add_render_attribute( 'review-text', 'class', $text_class, $overwrite );

		if ( ! empty( $settings['review_words_length'] ) ) {
			if ( 'review-card' === $widget_prefix ) {
				$this->add_render_attribute( 'review-text', 'class', 'bdt-ep-read-more-text' );
			} else {
				$this->add_render_attribute( 'review-text', 'class', 'bdt-ep-read-more-text ' . $text_class, $overwrite );
			}
			$this->add_render_attribute(
				'review-text',
				'data-read-more',
				wp_json_encode( [ 'words_length' => $settings['review_words_length'] ] ),
				$overwrite
			);
		}

		?>
		<div <?php $this->print_render_attribute_string( 'review-text' ); ?>>
			<?php echo wp_kses_post( $item['review_text'] ); ?>
		</div>
		<?php
	}

	/**
	 * Render review card rating.
	 *
	 * @param array  $item          Repeater item or widget settings.
	 * @param array  $settings      Widget settings.
	 * @param string $widget_prefix e.g. 'review-card', 'review-card-grid', 'review-card-carousel'.
	 * @param array  $args {
	 *     @type float $rating_min   Minimum rating value.
	 *     @type bool  $wrap_outer   Wrap rating in extra div (single widget).
	 *     @type bool  $flex_inline  Add bdt-flex-inline classes (grid/carousel).
	 * }
	 */
	protected function render_review_card_rating( $item, $settings, $widget_prefix, $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'rating_min'  => 0,
				'wrap_outer'  => false,
				'flex_inline' => false,
			]
		);

		if ( empty( $settings['show_rating'] ) ) {
			return;
		}

		$rating_type    = $settings['rating_type'] ?? 'star';
		$rating_pos     = $settings['rating_position'] ?? 'before';
		$rating_display = $this->get_review_card_rating_value( $item, $args['rating_min'] );

		$rating_classes = 'bdt-ep-' . $widget_prefix . '-rating bdt-' . esc_attr( $rating_type ) . ' bdt-' . esc_attr( $rating_pos );
		if ( $args['flex_inline'] ) {
			$rating_classes .= ' bdt-flex-inline bdt-flex-middle';
		}

		if ( $args['wrap_outer'] ) {
			echo '<div>';
		}

		?>
		<div class="<?php echo esc_attr( $rating_classes ); ?>">
			<?php if ( 'number' === $rating_type ) : ?>
				<span><?php echo esc_html( (string) $rating_display ); ?></span>
				<i class="ep-icon-star-full" aria-hidden="true"></i>
			<?php else : ?>
				<span class="epsc-rating">
					<?php foreach ( $this->get_review_card_rating_star_icons( $item, $args['rating_min'] ) as $icon_class ) : ?>
						<span class="epsc-rating-item">
							<i class="<?php echo esc_attr( $icon_class ); ?>" aria-hidden="true"></i>
						</span>
					<?php endforeach; ?>
				</span>
			<?php endif; ?>
		</div>
		<?php

		if ( $args['wrap_outer'] ) {
			echo '</div>';
		}
	}

	/**
	 * Render a single review card item.
	 *
	 * @param array  $item          Repeater item or widget settings.
	 * @param array  $settings      Widget settings.
	 * @param string $widget_prefix e.g. 'review-card', 'review-card-grid', 'review-card-carousel'.
	 * @param array  $args {
	 *     @type float  $rating_min        Minimum rating value.
	 *     @type bool   $wrap_rating       Wrap rating in extra div (single widget).
	 *     @type bool   $flex_inline_rating Add bdt-flex-inline classes on rating.
	 *     @type array  $extra_item_classes Additional item classes e.g. ['swiper-slide'].
	 *     @type bool   $overwrite         Overwrite render attributes.
	 * }
	 */
	protected function render_review_card_item( $item, $settings, $widget_prefix, $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'rating_min'         => 0,
				'wrap_rating'        => false,
				'flex_inline_rating' => false,
				'extra_item_classes' => [],
				'overwrite'          => true,
			]
		);

		$item_classes = array_merge( [ 'bdt-ep-' . $widget_prefix . '-item' ], $args['extra_item_classes'] );

		$this->add_render_attribute( 'review-item', 'class', implode( ' ', $item_classes ), $args['overwrite'] );

		$image_position = $settings['iamge_position'] ?? 'top';
		$image_inline   = $settings['image_inline'] ?? '';
		$rating_pos     = $settings['rating_position'] ?? 'before';

		if ( 'right' === $image_position ) {
			$this->add_render_attribute( 'image-inline', 'class', 'bdt-ep-img-inline bdt-flex bdt-flex-row-reverse', $args['overwrite'] );
		} else {
			$this->add_render_attribute( 'image-inline', 'class', 'bdt-ep-img-inline bdt-flex', $args['overwrite'] );
		}

		$rating_args = [
			'rating_min'  => $args['rating_min'],
			'wrap_outer'  => $args['wrap_rating'],
			'flex_inline' => $args['flex_inline_rating'],
		];

		?>
		<div <?php $this->print_render_attribute_string( 'review-item' ); ?>>
			<?php if ( 'yes' !== $image_inline ) : ?>
				<?php $this->render_review_card_image( $item, $settings, $widget_prefix, $args['overwrite'] ); ?>
			<?php endif; ?>

			<div class="bdt-ep-<?php echo esc_attr( $widget_prefix ); ?>-content">
				<?php if ( 'yes' === $image_inline ) : ?>
					<div <?php $this->print_render_attribute_string( 'image-inline' ); ?>>
						<?php $this->render_review_card_image( $item, $settings, $widget_prefix, $args['overwrite'] ); ?>
						<div class="bdt-flex bdt-flex-column bdt-flex-center">
							<?php $this->render_review_card_name( $item, $settings, $widget_prefix, $args['overwrite'] ); ?>
							<?php $this->render_review_card_job_title( $item, $settings, $widget_prefix ); ?>
							<?php if ( 'before' === $rating_pos ) : ?>
								<?php $this->render_review_card_rating( $item, $settings, $widget_prefix, $rating_args ); ?>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( 'yes' !== $image_inline ) : ?>
					<?php $this->render_review_card_name( $item, $settings, $widget_prefix, $args['overwrite'] ); ?>
					<?php $this->render_review_card_job_title( $item, $settings, $widget_prefix ); ?>
					<?php if ( 'before' === $rating_pos ) : ?>
						<?php $this->render_review_card_rating( $item, $settings, $widget_prefix, $rating_args ); ?>
					<?php endif; ?>
				<?php endif; ?>

				<?php $this->render_review_card_text( $item, $settings, $widget_prefix, $args['overwrite'] ); ?>

				<?php if ( 'after' === $rating_pos ) : ?>
					<?php $this->render_review_card_rating( $item, $settings, $widget_prefix, $rating_args ); ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
	/**
	 * Build a product widget CSS selector.
	 *
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 * @param string $element       e.g. 'item', 'image', 'title', 'price', 'content'.
	 */
	protected function get_product_selector( $widget_prefix, $element ) {
		return '{{WRAPPER}} .bdt-ep-' . $widget_prefix . '-' . $element;
	}

	/**
	 * Calculate product star rating score string.
	 *
	 * @param array $item Repeater item.
	 */
	protected function get_product_rating_score( $item ) {
		$rating_number = isset( $item['rating_number']['size'] ) ? $item['rating_number']['size'] : 0;

		if ( preg_match( '/\./', $rating_number ) ) {
			$rating_value = explode( '.', $rating_number );
			$first_val    = ( $rating_value[0] <= 5 ) ? $rating_value[0] : 5;
			$second_val   = ( $rating_value[1] < 5 ) ? 0 : 5;
		} else {
			$first_val  = ( $rating_number <= 5 ) ? $rating_number : 5;
			$second_val = 0;
		}

		return $first_val . '-' . $second_val;
	}

	/**
	 * Register product items repeater controls.
	 *
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 * @param array  $args {
	 *     @type bool $repeater_style_tabs Include per-item style color tab (grid only).
	 * }
	 */
	protected function register_product_items_controls( $widget_prefix, $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'repeater_style_tabs' => false,
			]
		);

		$this->start_controls_section(
			'ep_section_product',
			[
				'label' => __( 'Product Items', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		if ( $args['repeater_style_tabs'] ) {
			$repeater->start_controls_tabs( 'tabs_repeater_item_style' );
			$repeater->start_controls_tab(
				'tab_repeater_item_content',
				[
					'label' => __( 'Content', 'bdthemes-element-pack' ),
				]
			);
		}

		$repeater->add_control(
			'image',
			[
				'label'   => __( 'Image', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => __( 'Title', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'product title here', 'bdthemes-element-pack' ),
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'price',
			[
				'label'       => __( 'Price', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => '$204',
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'text',
			[
				'label'       => __( 'Text', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::WYSIWYG,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bdthemes-element-pack' ),
				'placeholder' => __( 'Enter your text', 'bdthemes-element-pack' ),
			]
		);

		$repeater->add_control(
			'readmore_link',
			[
				'label'       => esc_html__( 'Link', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => 'http://your-link.com',
				'default'     => [
					'url' => '#',
				],
			]
		);

		$repeater->add_control(
			'rating_number',
			[
				'label'      => __( 'Rating', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => 4.5,
				],
				'range'      => [
					'px' => [
						'min'  => .5,
						'max'  => 5,
						'step' => .5,
					],
				],
				'dynamic'    => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'rating_count',
			[
				'label'       => __( 'Rating Count', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => '(10,678)',
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'time',
			[
				'label'       => __( 'Time', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => __( '1 hour 10 mins', 'bdthemes-element-pack' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'badge_text',
			[
				'label'       => __( 'Badge Text', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Sale', 'bdthemes-element-pack' ),
				'placeholder' => __( 'Type Badge text', 'bdthemes-element-pack' ),
				'dynamic'     => [ 'active' => true ],
			]
		);

		if ( $args['repeater_style_tabs'] ) {
			$repeater->end_controls_tab();
			$repeater->start_controls_tab(
				'tab_repeater_item_style',
				[
					'label' => __( 'Style', 'bdthemes-element-pack' ),
				]
			);

			$repeater->add_control(
				'current_item_title_color',
				[
					'label'     => esc_html__( 'Title Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .bdt-ep-' . $widget_prefix . '-title' => 'color: {{VALUE}};',
					],
				]
			);
			$repeater->add_control(
				'current_item_price_color',
				[
					'label'     => esc_html__( 'Price Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .bdt-ep-' . $widget_prefix . '-price' => 'color: {{VALUE}};',
					],
				]
			);
			$repeater->add_control(
				'current_item_text_color',
				[
					'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .bdt-ep-' . $widget_prefix . '-text' => 'color: {{VALUE}};',
					],
				]
			);
			$repeater->add_control(
				'current_item_readmore_color',
				[
					'label'     => esc_html__( 'Read More Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .bdt-ep-' . $widget_prefix . '-readmore' => 'color: {{VALUE}};',
					],
					'separator' => 'before',
				]
			);
			$repeater->add_control(
				'current_item_readmore_background',
				[
					'label'     => esc_html__( 'Read More Background Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .bdt-ep-' . $widget_prefix . '-readmore' => 'background: {{VALUE}};',
					],
				]
			);
			$repeater->add_control(
				'current_item_rating_color',
				[
					'label'     => esc_html__( 'Rating Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .bdt-ep-' . $widget_prefix . '-rating' => 'color: {{VALUE}};',
					],
					'separator' => 'before',
				]
			);
			$repeater->add_control(
				'current_item_rating_text_color',
				[
					'label'     => esc_html__( 'Rating Text Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .bdt-ep-' . $widget_prefix . '-rating-count' => 'color: {{VALUE}};',
					],
				]
			);
			$repeater->add_control(
				'current_item_time_color',
				[
					'label'     => esc_html__( 'Time Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .bdt-ep-' . $widget_prefix . '-time' => 'color: {{VALUE}};',
					],
					'separator' => 'before',
				]
			);
			$repeater->add_control(
				'current_item_badge_color',
				[
					'label'     => esc_html__( 'Badge Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .bdt-ep-' . $widget_prefix . '-badge .bdt-badge' => 'color: {{VALUE}};',
					],
					'separator' => 'before',
				]
			);
			$repeater->add_control(
				'current_item_badge_background',
				[
					'label'     => esc_html__( 'Badge Background Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .bdt-ep-' . $widget_prefix . '-badge .bdt-badge' => 'background: {{VALUE}};',
					],
				]
			);
			$repeater->end_controls_tab();
			$repeater->end_controls_tabs();
		}

		$this->add_control(
			'product_items',
			[
				'show_label'  => false,
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => [
					[ 'title' => __( 'Pizza', 'bdthemes-element-pack' ) ],
					[ 'title' => __( 'Burger', 'bdthemes-element-pack' ) ],
					[ 'title' => __( 'Chicken', 'bdthemes-element-pack' ) ],
					[ 'title' => __( 'Milkshake', 'bdthemes-element-pack' ) ],
					[ 'title' => __( 'Ice Tea', 'bdthemes-element-pack' ) ],
					[ 'title' => __( 'Pasta', 'bdthemes-element-pack' ) ],
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register shared product display settings controls.
	 *
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 * @param array  $args {
	 *     @type string $readmore_wrapper_option 'wrapper' (grid) or 'item' (carousel).
	 *     @type string $section_label           Section label text.
	 *     @type bool   $section_wrapper         Start/end the controls section.
	 * }
	 */
	protected function register_product_common_settings_controls( $widget_prefix, $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'readmore_wrapper_option' => 'item',
				'section_label'           => __( 'Additional Settings', 'bdthemes-element-pack' ),
				'section_wrapper'         => true,
			]
		);

		$item_selector = $this->get_product_selector( $widget_prefix, 'item' );

		$readmore_options = [
			'button' => __( 'Button', 'bdthemes-element-pack' ),
			'title'  => __( 'Title', 'bdthemes-element-pack' ),
			'image'  => __( 'Image', 'bdthemes-element-pack' ),
		];

		if ( 'wrapper' === $args['readmore_wrapper_option'] ) {
			$readmore_options['wrapper'] = __( 'Wrapper Item', 'bdthemes-element-pack' ) . BDTEP_LOCK;
		} else {
			$readmore_options['item'] = __( 'Item Wrapper', 'bdthemes-element-pack' );
		}

		$readmore_link_control = [
			'label'   => __( 'Link to', 'bdthemes-element-pack' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'button',
			'options' => $readmore_options,
		];

		if ( 'wrapper' === $args['readmore_wrapper_option'] ) {
			$readmore_link_control['classes'] = BDTEP_LOCK_CLASS;
		}

		if ( $args['section_wrapper'] ) {
			$this->start_controls_section(
				'section_additional_settings',
				[
					'label' => $args['section_label'],
					'tab'   => Controls_Manager::TAB_CONTENT,
				]
			);
		}

		$this->add_control(
			'show_title',
			[
				'label'     => __( 'Show Name', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'     => __( 'Title HTML Tag', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h3',
				'options'   => element_pack_title_tags(),
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_price',
			[
				'label'     => __( 'Show Price', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_time',
			[
				'label'     => __( 'Show Time', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_text',
			[
				'label'     => __( 'Show Text', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control( 'readmore_link_to', $readmore_link_control );

		$this->add_control(
			'show_rating',
			[
				'label'     => __( 'Show Rating', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'rating_type',
			[
				'label'     => __( 'Rating Type', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'number',
				'options'   => [
					'star'   => __( 'Star', 'bdthemes-element-pack' ),
					'number' => __( 'Number', 'bdthemes-element-pack' ),
				],
				'condition' => [
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_control(
			'badge',
			[
				'label'     => __( 'Badge', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_image',
			[
				'label'     => __( 'Show Image', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumbnail_size',
				'default'   => 'medium',
				'condition' => [
					'show_image' => 'yes',
				],
			]
		);

		$this->add_control(
			'image_mask_popover',
			[
				'label'        => esc_html__( 'Image Mask', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'render_type'  => 'template',
				'return_value' => 'yes',
				'condition'    => [
					'show_image' => 'yes',
				],
			]
		);

		$this->register_image_mask_controls();

		$this->add_responsive_control(
			'text_align',
			[
				'label'     => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'    => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					$item_selector => 'text-align: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		if ( $args['section_wrapper'] ) {
			$this->end_controls_section();
		}
	}

	/**
	 * Register product read more content controls.
	 *
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 */
	protected function register_product_readmore_content_controls( $widget_prefix ) {
		$readmore_selector = $this->get_product_selector( $widget_prefix, 'readmore' );

		$this->start_controls_section(
			'section_content_readmore',
			[
				'label'     => esc_html__( 'Read More', 'bdthemes-element-pack' ),
				'condition' => [
					'readmore_link_to' => 'button',
				],
			]
		);

		$this->add_control(
			'readmore_text',
			[
				'label'       => esc_html__( 'Read More Text', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Read More', 'bdthemes-element-pack' ),
				'placeholder' => esc_html__( 'Read More', 'bdthemes-element-pack' ),
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'readmore_icon',
			[
				'label'       => esc_html__( 'Icon', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline',
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label'     => esc_html__( 'Icon Position', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'right',
				'toggle'    => false,
				'options'   => [
					'left'  => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'condition' => [
					'readmore_icon[value]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'icon_indent',
			[
				'label'     => esc_html__( 'Icon Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 8,
				],
				'range'     => [
					'px' => [
						'max' => 50,
					],
				],
				'condition' => [
					'readmore_icon[value]!' => '',
				],
				'selectors' => [
					$readmore_selector => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register product badge content controls.
	 */
	protected function register_product_badge_content_controls() {
		$this->start_controls_section(
			'section_content_badge',
			[
				'label'     => __( 'Badge', 'bdthemes-element-pack' ),
				'condition' => [
					'badge' => 'yes',
				],
			]
		);

		$this->add_control(
			'badge_position',
			[
				'label'   => esc_html__( 'Position', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top-right',
				'options' => element_pack_position(),
			]
		);

		$this->add_control(
			'badge_offset_toggle',
			[
				'label'        => __( 'Offset', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => __( 'None', 'bdthemes-element-pack' ),
				'label_on'     => __( 'Custom', 'bdthemes-element-pack' ),
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'badge_horizontal_offset',
			[
				'label'          => __( 'Horizontal Offset', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range'          => [
					'px' => [
						'min'  => -300,
						'step' => 1,
						'max'  => 300,
					],
				],
				'condition'      => [
					'badge_offset_toggle' => 'yes',
				],
				'render_type'    => 'ui',
				'selectors'      => [
					'{{WRAPPER}}' => '--ep-badge-h-offset: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'badge_vertical_offset',
			[
				'label'          => __( 'Vertical Offset', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range'          => [
					'px' => [
						'min'  => -300,
						'step' => 1,
						'max'  => 300,
					],
				],
				'condition'      => [
					'badge_offset_toggle' => 'yes',
				],
				'render_type'    => 'ui',
				'selectors'      => [
					'{{WRAPPER}}' => '--ep-badge-v-offset: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'badge_rotate',
			[
				'label'          => esc_html__( 'Rotate', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range'          => [
					'px' => [
						'min'  => -360,
						'max'  => 360,
						'step' => 5,
					],
				],
				'condition'      => [
					'badge_offset_toggle' => 'yes',
				],
				'render_type'    => 'ui',
				'selectors'      => [
					'{{WRAPPER}}' => '--ep-badge-rotate: {{SIZE}}deg;',
				],
			]
		);

		$this->end_popover();

		$this->end_controls_section();
	}

	/**
	 * Register product items style controls.
	 *
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 * @param array  $args {
	 *     @type bool $show_shadow_padding Show carousel match padding control.
	 * }
	 */
	protected function register_product_style_items_controls( $widget_prefix, $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'show_shadow_padding' => false,
			]
		);

		$item_selector       = $this->get_product_selector( $widget_prefix, 'item' );
		$item_hover_selector = $item_selector . ':hover';
		$content_selector    = $this->get_product_selector( $widget_prefix, 'content' );

		$this->start_controls_section(
			'section_style_carousel_items',
			[
				'label' => esc_html__( 'Items', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => esc_html__( 'Content Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$content_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_item_style' );

		$this->start_controls_tab(
			'tab_item_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_background',
				'selector' => $item_selector,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'item_border',
				'selector'  => $item_selector,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$item_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$item_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => $item_selector,
			]
		);

		if ( $args['show_shadow_padding'] ) {
			$this->add_responsive_control(
				'item_shadow_padding',
				[
					'label'       => __( 'Match Padding', 'bdthemes-element-pack' ),
					'description' => __( 'You have to add padding for matching overlaping normal/hover box shadow when you used Box Shadow option.', 'bdthemes-element-pack' ),
					'type'        => Controls_Manager::SLIDER,
					'range'       => [
						'px' => [
							'min'  => 0,
							'step' => 1,
							'max'  => 50,
						],
					],
					'selectors'   => [
						'{{WRAPPER}} .swiper-carousel' => 'padding: {{SIZE}}{{UNIT}}; margin: 0 -{{SIZE}}{{UNIT}};',
					],
				]
			);
		}

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_hover_background',
				'selector' => $item_hover_selector,
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
					$item_hover_selector => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_hover_box_shadow',
				'selector' => $item_hover_selector,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register product image style controls.
	 *
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 * @param array  $args {
	 *     @type bool $full_tabs Grid uses normal/hover tabs; carousel uses flat controls.
	 * }
	 */
	protected function register_product_style_image_controls( $widget_prefix, $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'full_tabs' => false,
			]
		);

		$image_selector           = $this->get_product_selector( $widget_prefix, 'image' );
		$image_img_selector       = $image_selector . ' img';
		$image_img_hover_selector = $this->get_product_selector( $widget_prefix, 'item' ) . ':hover .bdt-ep-' . $widget_prefix . '-image img';

		$this->start_controls_section(
			'section_style_image',
			[
				'label'     => __( 'Image', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_image' => 'yes',
				],
			]
		);

		if ( $args['full_tabs'] ) {
			$this->start_controls_tabs( 'tabs_image_style' );
			$this->start_controls_tab(
				'tab_image_normal',
				[
					'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'image_background',
					'selector' => $image_img_selector,
				]
			);
		}

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'image_border',
				'selector'  => $image_img_selector,
				'separator' => $args['full_tabs'] ? 'before' : '',
			]
		);

		$this->add_control(
			'iamge_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$image_img_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'iamge_padding',
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$image_img_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_spacing',
			[
				'label'     => __( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					$image_selector => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'css_filters',
				'selector' => $image_img_selector,
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_shadow',
				'selector' => $image_img_selector,
			]
		);

		if ( $args['full_tabs'] ) {
			$this->end_controls_tab();
			$this->start_controls_tab(
				'tab_image_hover',
				[
					'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'image_hover_background',
					'selector' => $image_img_hover_selector,
				]
			);

			$this->add_control(
				'image_hover_border_color',
				[
					'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'condition' => [
						'image_border_border!' => '',
					],
					'selectors' => [
						$image_img_hover_selector => 'border-color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Css_Filter::get_type(),
				[
					'name'     => 'image_css_filters_hover',
					'selector' => $image_img_hover_selector,
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'image_hover_box_shadow',
					'selector' => $image_img_hover_selector,
				]
			);

			$this->end_controls_tab();
			$this->end_controls_tabs();
		}

		$this->end_controls_section();
	}

	/**
	 * Register product title style controls.
	 *
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 */
	protected function register_product_style_title_controls( $widget_prefix ) {
		$is_grid            = 'product-grid' === $widget_prefix;
		$title_selector     = $this->get_product_selector( $widget_prefix, 'title' );
		$title_price_selector = $this->get_product_selector( $widget_prefix, 'title-price' );
		$title_hover_selector = $this->get_product_selector( $widget_prefix, 'item' ) . ':hover .bdt-ep-' . $widget_prefix . '-title';

		$this->start_controls_section(
			'section_style_title',
			[
				'label'     => __( 'Title', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$title_selector => 'color: {{VALUE}};',
				],
			]
		);

		if ( $is_grid ) {
			$this->add_control(
				'title_hover_color',
				[
					'label'     => __( 'Hover Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						$title_hover_selector => 'color: {{VALUE}};',
					],
				]
			);
		}

		$title_spacing_selectors = $is_grid
			? [ $title_price_selector => 'margin-bottom: {{SIZE}}{{UNIT}};' ]
			: [ $title_selector => 'padding-bottom: {{SIZE}}{{UNIT}};' ];

		$this->add_responsive_control(
			'title_bottom_space',
			[
				'label'     => __( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => $title_spacing_selectors,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => $title_selector,
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'title_shadow',
				'label'    => __( 'Text Shadow', 'bdthemes-element-pack' ),
				'selector' => $title_selector,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register product price style controls.
	 *
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 */
	protected function register_product_style_price_controls( $widget_prefix ) {
		$is_grid         = 'product-grid' === $widget_prefix;
		$price_selector      = $this->get_product_selector( $widget_prefix, 'price' );
		$price_hover_selector = $this->get_product_selector( $widget_prefix, 'item' ) . ':hover .bdt-ep-' . $widget_prefix . '-price';

		$this->start_controls_section(
			'section_style_price',
			[
				'label'     => __( 'Price', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_price' => 'yes',
				],
			]
		);

		$this->add_control(
			'price_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$price_selector => 'color: {{VALUE}};',
				],
			]
		);

		if ( $is_grid ) {
			$this->add_control(
				'price_hover_color',
				[
					'label'     => __( 'Hover Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						$price_hover_selector => 'color: {{VALUE}};',
					],
				]
			);
		}

		$this->add_responsive_control(
			'price_bottom_space',
			[
				'label'     => __( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					$price_selector => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'price_typography',
				'selector' => $price_selector,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register product text style controls.
	 *
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 */
	protected function register_product_style_text_controls( $widget_prefix ) {
		$is_grid        = 'product-grid' === $widget_prefix;
		$text_selector      = $this->get_product_selector( $widget_prefix, 'text' );
		$text_hover_selector = $this->get_product_selector( $widget_prefix, 'item' ) . ':hover .bdt-ep-' . $widget_prefix . '-text';

		$this->start_controls_section(
			'section_style_text',
			[
				'label'     => __( 'Text', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$text_selector => 'color: {{VALUE}};',
				],
			]
		);

		if ( $is_grid ) {
			$this->add_control(
				'text_hover_color',
				[
					'label'     => __( 'Hover Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						$text_hover_selector => 'color: {{VALUE}};',
					],
				]
			);
		}

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'selector' => $text_selector,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register product read more style controls.
	 *
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 */
	protected function register_product_style_readmore_controls( $widget_prefix ) {
		$readmore_selector = $this->get_product_selector( $widget_prefix, 'readmore' );

		$this->start_controls_section(
			'section_style_readmore',
			[
				'label'     => esc_html__( 'Read More', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'readmore_link_to' => 'button',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_readmore_style' );

		$this->start_controls_tab(
			'tab_readmore_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'readmore_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$readmore_selector          => 'color: {{VALUE}};',
					$readmore_selector . ' svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'readmore_background',
				'selector' => $readmore_selector,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'readmore_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => $readmore_selector,
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'readmore_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$readmore_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'readmore_box_shadow',
				'selector' => $readmore_selector,
			]
		);

		$this->add_responsive_control(
			'readmore_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$readmore_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'readmore_margin',
			[
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$readmore_selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'readmore_typography',
				'selector' => $readmore_selector,
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_readmore_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'readmore_hover_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$readmore_selector . ':hover'     => 'color: {{VALUE}};',
					$readmore_selector . ':hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'readmore_hover_background',
				'selector' => $readmore_selector . ':hover',
			]
		);

		$this->add_control(
			'readmore_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'readmore_border_border!' => '',
				],
				'selectors' => [
					$readmore_selector . ':hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'readmore_hover_animation',
			[
				'label' => esc_html__( 'Animation', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register product rating style controls.
	 *
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 */
	protected function register_product_style_rating_controls( $widget_prefix ) {
		$rating_selector      = $this->get_product_selector( $widget_prefix, 'rating' );
		$rating_count_selector = $this->get_product_selector( $widget_prefix, 'rating-count' );
		$rating_time_selector = $this->get_product_selector( $widget_prefix, 'rating-time' );

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
			'rating_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e7e7e7',
				'selectors' => [
					'{{WRAPPER}} .epsc-rating-item' => 'color: {{VALUE}};',
				],
				'condition' => [
					'rating_type' => 'star',
				],
			]
		);

		$this->add_control(
			'active_rating_color',
			[
				'label'     => esc_html__( 'Active Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFCC00',
				'selectors' => [
					'{{WRAPPER}} .epsc-rating[class*=" epsc-rating-0"] .epsc-rating-item:nth-child(1) i:after, {{WRAPPER}} .epsc-rating[class*=" epsc-rating-1"] .epsc-rating-item:nth-child(-n+1) i:after, {{WRAPPER}} .epsc-rating[class*=" epsc-rating-2"] .epsc-rating-item:nth-child(-n+2) i:after, {{WRAPPER}} .epsc-rating[class*=" epsc-rating-3"] .epsc-rating-item:nth-child(-n+3) i:after, {{WRAPPER}} .epsc-rating[class*=" epsc-rating-4"] .epsc-rating-item:nth-child(-n+4) i:after, {{WRAPPER}} .epsc-rating[class*=" epsc-rating-5"] .epsc-rating-item:nth-child(-n+5) i:after, .epsc-rating.epsc-rating-0-5 .epsc-rating-item:nth-child(1) i:after, {{WRAPPER}} .epsc-rating.epsc-rating-1-5 .epsc-rating-item:nth-child(2) i:after, {{WRAPPER}} .epsc-rating.epsc-rating-2-5 .epsc-rating-item:nth-child(3) i:after, {{WRAPPER}} .epsc-rating.epsc-rating-3-5 .epsc-rating-item:nth-child(4) i:after, {{WRAPPER}} .epsc-rating.epsc-rating-4-5 .epsc-rating-item:nth-child(5) i:after' => 'color: {{VALUE}};',
				],
				'condition' => [
					'rating_type' => 'star',
				],
			]
		);

		$this->add_control(
			'rating_number_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFCC00',
				'selectors' => [
					$rating_selector => 'color: {{VALUE}};',
				],
				'condition' => [
					'rating_type' => 'number',
				],
			]
		);

		$this->add_control(
			'rating_background_color',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$rating_selector => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'rating_type' => 'number',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'rating_border',
				'selector'  => $rating_selector,
				'condition' => [
					'rating_type' => 'number',
				],
			]
		);

		$this->add_responsive_control(
			'rating_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$rating_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'rating_type' => 'number',
				],
			]
		);

		$this->add_responsive_control(
			'rating_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$rating_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'rating_type' => 'number',
				],
			]
		);

		$this->add_responsive_control(
			'rating_margin',
			[
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$rating_time_selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rating_size',
			[
				'label'     => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					$rating_selector => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rating_space_between',
			[
				'label'     => esc_html__( 'Space Between', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'selectors' => [
					$rating_selector . ' i + i' => 'margin-left: {{SIZE}}{{UNIT}};',
					$rating_selector . ' span'   => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'rating_count_color',
			[
				'label'     => esc_html__( 'Count Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$rating_count_selector => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'rating_count_typography',
				'label'    => esc_html__( 'Count Text Typography', 'bdthemes-element-pack' ),
				'selector' => $rating_count_selector,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register product time style controls.
	 *
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 */
	protected function register_product_style_time_controls( $widget_prefix ) {
		$is_grid       = 'product-grid' === $widget_prefix;
		$time_selector      = $this->get_product_selector( $widget_prefix, 'time' );
		$time_hover_selector = $this->get_product_selector( $widget_prefix, 'item' ) . ':hover .bdt-ep-' . $widget_prefix . '-time';

		$this->start_controls_section(
			'section_style_time',
			[
				'label'     => __( 'Time', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_time' => 'yes',
				],
			]
		);

		$this->add_control(
			'time_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$time_selector => 'color: {{VALUE}};',
				],
			]
		);

		if ( $is_grid ) {
			$this->add_control(
				'time_hover_color',
				[
					'label'     => __( 'Hover Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						$time_hover_selector => 'color: {{VALUE}};',
					],
				]
			);
		}

		$this->add_responsive_control(
			'time_bottom_space',
			[
				'label'     => __( 'Space Between', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'selectors' => [
					$time_selector . ' i' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'time_typography',
				'selector' => $time_selector,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register product badge style controls.
	 *
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 */
	protected function register_product_style_badge_controls( $widget_prefix ) {
		$badge_selector = $this->get_product_selector( $widget_prefix, 'badge' );

		$this->start_controls_section(
			'section_style_badge',
			[
				'label'     => __( 'Badge', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'badge' => 'yes',
				],
			]
		);

		$this->add_control(
			'badge_text_color',
			[
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$badge_selector . ' span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'badge_background',
				'selector' => $badge_selector . ' span',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'badge_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => $badge_selector . ' span',
			]
		);

		$this->add_responsive_control(
			'badge_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$badge_selector . ' span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'badge_shadow',
				'selector' => $badge_selector . ' span',
			]
		);

		$this->add_responsive_control(
			'badge_padding',
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$badge_selector . ' span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_margin',
			[
				'label'      => __( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-interactive-card .bdt-ep-' . $widget_prefix . '-badge.bdt-position-small' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'badge_typography',
				'selector' => $badge_selector . ' span',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render product image.
	 *
	 * @param array  $item          Repeater item.
	 * @param string $image_key     Render attribute key for image link.
	 * @param array  $settings      Widget settings.
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 * @param bool   $overwrite     Overwrite render attributes.
	 */
	protected function render_product_image( $item, $image_key, $settings, $widget_prefix, $overwrite = true ) {
		if ( empty( $settings['show_image'] ) ) {
			return;
		}

		$link_class = 'bdt-ep-' . $widget_prefix . '-link bdt-position-z-index';

		$this->add_render_attribute( $image_key, 'class', $link_class, $overwrite );
		if ( ! empty( $item['readmore_link'] ) ) {
			$this->add_link_attributes( $image_key, $item['readmore_link'] );
		}

		$image_mask = ! empty( $settings['image_mask_popover'] ) && 'yes' === $settings['image_mask_popover'] ? ' bdt-image-mask' : '';
		$this->add_render_attribute( 'image-wrap', 'class', 'bdt-ep-' . $widget_prefix . '-image bdt-flex-inline' . $image_mask, $overwrite );

		$thumb_url     = Group_Control_Image_Size::get_attachment_image_src( $item['image']['id'], 'thumbnail_size', $settings );
		$attachment_id = ! empty( $item['image']['id'] ) ? (int) $item['image']['id'] : 0;

		?>
		<div <?php $this->print_render_attribute_string( 'image-wrap' ); ?>>
			<?php
			if ( 'product-grid' === $widget_prefix ) {
				if ( ! $thumb_url ) {
					printf( '<img src="%1$s" alt="%2$s">', esc_url( $item['image']['url'] ), esc_html( $item['title'] ) );
				} else {
					print wp_get_attachment_image(
						$attachment_id,
						$settings['thumbnail_size_size'],
						false,
						[
							'alt' => esc_html( $item['title'] ),
						]
					);
				}
			} else {
				$carousel_thumb = $thumb_url ? $thumb_url : ( $item['image']['url'] ?? '' );
				if ( ! empty( $carousel_thumb ) ) {
					printf( '<img src="%1$s" alt="%2$s">', esc_url( $carousel_thumb ), esc_attr( $item['title'] ) );
				}
			}
			?>

			<?php if ( 'image' === $settings['readmore_link_to'] ) : ?>
				<a <?php $this->print_render_attribute_string( $image_key ); ?>></a>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render product title.
	 *
	 * @param array  $item          Repeater item.
	 * @param string $title_key     Render attribute key for title link.
	 * @param array  $settings      Widget settings.
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 * @param bool   $overwrite     Overwrite render attributes.
	 */
	protected function render_product_title( $item, $title_key, $settings, $widget_prefix, $overwrite = true ) {
		if ( empty( $settings['show_title'] ) || empty( $item['title'] ) ) {
			return;
		}

		$this->add_render_attribute( $title_key, 'class', 'bdt-ep-' . $widget_prefix . '-link', $overwrite );
		if ( ! empty( $item['readmore_link'] ) ) {
			$this->add_link_attributes( $title_key, $item['readmore_link'] );
		}

		$this->add_render_attribute( 'title-wrap', 'class', 'bdt-ep-' . $widget_prefix . '-title', $overwrite );
		$title_tag = Utils::get_valid_html_tag( $settings['title_tag'] );

		?>
		<<?php echo esc_attr( $title_tag ); ?> <?php $this->print_render_attribute_string( 'title-wrap' ); ?>>
			<?php echo wp_kses( $item['title'], element_pack_allow_tags( 'title' ) ); ?>
			<?php if ( 'title' === $settings['readmore_link_to'] ) : ?>
				<a <?php $this->print_render_attribute_string( $title_key ); ?>></a>
			<?php endif; ?>
		</<?php echo esc_attr( $title_tag ); ?>>
		<?php
	}

	/**
	 * Render product price.
	 *
	 * @param array  $item          Repeater item.
	 * @param array  $settings      Widget settings.
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 * @param bool   $overwrite     Overwrite render attributes.
	 */
	protected function render_product_price( $item, $settings, $widget_prefix, $overwrite = true ) {
		if ( empty( $settings['show_price'] ) || empty( $item['price'] ) ) {
			return;
		}

		$this->add_render_attribute( 'price-wrap', 'class', 'bdt-ep-' . $widget_prefix . '-price', $overwrite );

		?>
		<div <?php $this->print_render_attribute_string( 'price-wrap' ); ?>>
			<?php echo wp_kses( $item['price'], element_pack_allow_tags( 'price' ) ); ?>
		</div>
		<?php
	}

	/**
	 * Render product time.
	 *
	 * @param array  $item          Repeater item.
	 * @param array  $settings      Widget settings.
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 * @param bool   $overwrite     Overwrite render attributes.
	 */
	protected function render_product_time( $item, $settings, $widget_prefix, $overwrite = true ) {
		if ( empty( $settings['show_time'] ) || empty( $item['time'] ) ) {
			return;
		}

		$this->add_render_attribute( 'time-wrap', 'class', 'bdt-ep-' . $widget_prefix . '-time', $overwrite );

		?>
		<div <?php $this->print_render_attribute_string( 'time-wrap' ); ?>>
			<i class="ep-icon-clock-o" aria-hidden="true"></i>
			<?php echo wp_kses( $item['time'], element_pack_allow_tags( 'time' ) ); ?>
		</div>
		<?php
	}

	/**
	 * Render product text.
	 *
	 * @param array  $item          Repeater item.
	 * @param array  $settings      Widget settings.
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 */
	protected function render_product_text( $item, $settings, $widget_prefix ) {
		if ( empty( $settings['show_text'] ) || empty( $item['text'] ) ) {
			return;
		}

		?>
		<div class="bdt-ep-<?php echo esc_attr( $widget_prefix ); ?>-text">
			<?php echo wp_kses_post( $item['text'] ); ?>
		</div>
		<?php
	}

	/**
	 * Render product read more button.
	 *
	 * @param array  $item          Repeater item.
	 * @param string $readmore_key  Render attribute key for read more link.
	 * @param array  $settings      Widget settings.
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 * @param bool   $overwrite     Overwrite render attributes.
	 */
	protected function render_product_readmore( $item, $readmore_key, $settings, $widget_prefix, $overwrite = true ) {
		if ( empty( $item['readmore_link']['url'] ) || 'button' !== ( $settings['readmore_link_to'] ?? '' ) ) {
			return;
		}

		$icon_align      = $settings['icon_align'] ?? 'right';
		$readmore_text   = $settings['readmore_text'] ?? __( 'Read More', 'bdthemes-element-pack' );
		$animation_class = ! empty( $settings['readmore_hover_animation'] ) ? 'elementor-animation-' . $settings['readmore_hover_animation'] : '';

		$this->add_render_attribute(
			[
				$readmore_key => [
					'class' => array_filter(
						[
							'bdt-ep-' . $widget_prefix . '-readmore',
							$animation_class,
						]
					),
				],
			],
			'',
			'',
			$overwrite
		);

		if ( ! empty( $item['readmore_link'] ) ) {
			$this->add_link_attributes( $readmore_key, $item['readmore_link'] );
		}

		?>
		<div class="bdt-ep-<?php echo esc_attr( $widget_prefix ); ?>-readmore-wrap">
			<a <?php $this->print_render_attribute_string( $readmore_key ); ?>>
				<?php if ( ! empty( $settings['readmore_icon']['value'] ) && 'left' === $icon_align ) : ?>
					<span class="bdt-button-icon-align-left">
						<?php \Elementor\Icons_Manager::render_icon( $settings['readmore_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] ); ?>
					</span>
				<?php endif; ?>

				<?php echo esc_html( $readmore_text ); ?>

				<?php if ( ! empty( $settings['readmore_icon']['value'] ) && 'right' === $icon_align ) : ?>
					<span class="bdt-button-icon-align-right">
						<?php \Elementor\Icons_Manager::render_icon( $settings['readmore_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] ); ?>
					</span>
				<?php endif; ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Render product rating.
	 *
	 * @param array  $item          Repeater item.
	 * @param array  $settings      Widget settings.
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 */
	protected function render_product_rating( $item, $settings, $widget_prefix ) {
		if ( empty( $settings['show_rating'] ) ) {
			return;
		}

		$score       = $this->get_product_rating_score( $item );
		$rating_type = $settings['rating_type'] ?? 'number';

		?>
		<div>
			<div class="bdt-ep-<?php echo esc_attr( $widget_prefix ); ?>-rating bdt-flex-inline bdt-flex-middle bdt-<?php echo esc_attr( $rating_type ); ?>">
				<?php if ( 'number' === $rating_type ) : ?>
					<span>
						<?php echo esc_html( $item['rating_number']['size'] ); ?>
					</span>
					<i class="ep-icon-star-full" aria-hidden="true"></i>
				<?php else : ?>
					<span class="epsc-rating epsc-rating-<?php echo esc_attr( $score ); ?>">
						<span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
						<span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
						<span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
						<span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
						<span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
					</span>
				<?php endif; ?>
			</div>
			<span class="bdt-ep-<?php echo esc_attr( $widget_prefix ); ?>-rating-count">
				<?php echo esc_html( $item['rating_count'] ?? '' ); ?>
			</span>
		</div>
		<?php
	}

	/**
	 * Render product badge.
	 *
	 * @param array  $item          Repeater item.
	 * @param array  $settings      Widget settings.
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 */
	protected function render_product_badge( $item, $settings, $widget_prefix ) {
		if ( empty( $settings['badge'] ) || '' === ( $item['badge_text'] ?? '' ) ) {
			return;
		}

		$badge_position = $settings['badge_position'] ?? 'top-right';
		?>
		<div class="bdt-ep-<?php echo esc_attr( $widget_prefix ); ?>-badge bdt-position-small bdt-position-<?php echo esc_attr( $badge_position ); ?>">
			<span class="bdt-badge bdt-padding-small">
				<?php echo esc_html( $item['badge_text'] ); ?>
			</span>
		</div>
		<?php
	}

	/**
	 * Render a single product item.
	 *
	 * @param array  $item          Repeater item.
	 * @param int    $index         Item index.
	 * @param array  $settings      Widget settings.
	 * @param string $widget_prefix e.g. 'product-grid' or 'product-carousel'.
	 * @param array  $args {
	 *     @type string $wrapper_link_key   'wrapper' for grid, 'item' for carousel.
	 *     @type array  $extra_item_classes Additional item classes e.g. ['swiper-slide'].
	 *     @type bool   $overwrite          Overwrite render attributes.
	 * }
	 */
	protected function render_product_item( $item, $index, $settings, $widget_prefix, $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'wrapper_link_key'   => 'item',
				'extra_item_classes' => [],
				'overwrite'          => true,
			]
		);

		$item_classes = array_merge(
			[
				'bdt-ep-' . $widget_prefix . '-item',
				'bdt-flex',
				'bdt-flex-column',
			],
			$args['extra_item_classes']
		);

		if ( 'product-grid' === $widget_prefix && ! empty( $item['_id'] ) ) {
			$item_classes[] = 'elementor-repeater-item-' . esc_attr( $item['_id'] );
		}

		$this->add_render_attribute( 'item-wrap-' . $index, 'class', $item_classes, $args['overwrite'] );

		$title_price_classes = 'bdt-ep-' . $widget_prefix . '-title-price';
		if ( 'product-carousel' === $widget_prefix || ( ! empty( $settings['show_price'] ) && ! empty( $settings['show_title'] ) ) ) {
			$title_price_classes .= ' bdt-flex bdt-flex-middle bdt-flex-between';
		}
		$this->add_render_attribute( 'title-price-' . $index, 'class', $title_price_classes, $args['overwrite'] );

		$wrapper_key = 'item-link-' . $index;
		if ( $args['wrapper_link_key'] === ( $settings['readmore_link_to'] ?? '' ) ) {
			$this->add_render_attribute( $wrapper_key, 'class', 'bdt-ep-' . $widget_prefix . '-link bdt-position-z-index', $args['overwrite'] );
			if ( ! empty( $item['readmore_link'] ) ) {
				$this->add_link_attributes( $wrapper_key, $item['readmore_link'] );
			}
		}

		?>
		<div <?php $this->print_render_attribute_string( 'item-wrap-' . $index ); ?>>
			<?php $this->render_product_image( $item, 'image_' . $index, $settings, $widget_prefix, $args['overwrite'] ); ?>
			<div class="bdt-ep-<?php echo esc_attr( $widget_prefix ); ?>-content bdt-flex bdt-flex-column bdt-flex-between">
				<div>
					<div <?php $this->print_render_attribute_string( 'title-price-' . $index ); ?>>
						<?php $this->render_product_title( $item, 'title_' . $index, $settings, $widget_prefix, $args['overwrite'] ); ?>
						<?php $this->render_product_price( $item, $settings, $widget_prefix, $args['overwrite'] ); ?>
					</div>
					<?php $this->render_product_text( $item, $settings, $widget_prefix ); ?>
					<?php $this->render_product_readmore( $item, 'link_' . $index, $settings, $widget_prefix, $args['overwrite'] ); ?>
				</div>
				<div class="bdt-ep-<?php echo esc_attr( $widget_prefix ); ?>-rating-time bdt-flex bdt-flex-middle bdt-flex-between bdt-flex-wrap">
					<?php $this->render_product_rating( $item, $settings, $widget_prefix ); ?>
					<?php $this->render_product_time( $item, $settings, $widget_prefix, $args['overwrite'] ); ?>
				</div>
			</div>
			<?php $this->render_product_badge( $item, $settings, $widget_prefix ); ?>
			<?php if ( $args['wrapper_link_key'] === ( $settings['readmore_link_to'] ?? '' ) ) : ?>
				<a <?php $this->print_render_attribute_string( $wrapper_key ); ?>></a>
			<?php endif; ?>
		</div>
		<?php
	}
/**
 * Portfolio shared trait methods for Global_Controls_Functions.
 * Append before closing `}` of traits/global-controls-functions.php
 */

	/**
	 * Get portfolio widget CSS wrapper class.
	 *
	 * @param string $widget_prefix e.g. 'portfolio-gallery', 'portfolio-carousel', 'portfolio-list'.
	 */
	protected function get_portfolio_wrapper_class( $widget_prefix ) {
		$classes = [
			'portfolio-gallery'  => 'bdt-portfolio-gallery',
			'portfolio-carousel' => 'bdt-portfolio-carousel',
			'portfolio-list'     => 'bdt-portfolio-list',
		];

		return $classes[ $widget_prefix ] ?? 'bdt-' . $widget_prefix;
	}

	/**
	 * Build a portfolio widget CSS selector.
	 *
	 * @param string $widget_prefix e.g. 'portfolio-gallery'.
	 * @param string $suffix        CSS selector suffix after wrapper class.
	 */
	protected function get_portfolio_selector( $widget_prefix, $suffix ) {
		return '{{WRAPPER}} .' . $this->get_portfolio_wrapper_class( $widget_prefix ) . ' ' . $suffix;
	}

	/**
	 * Resolve portfolio widget prefix from widget name.
	 */
	protected function get_portfolio_widget_prefix() {
		$name = $this->get_name();

		if ( 0 === strpos( $name, 'bdt-' ) ) {
			return substr( $name, 4 );
		}

		return $name;
	}

	/**
	 * Register shared portfolio thumbnail image size control.
	 */
	protected function register_portfolio_thumbnail_size_control() {
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'         => 'thumbnail_size',
				'label'        => esc_html__( 'Image Size', 'bdthemes-element-pack' ),
				'exclude'      => [ 'custom' ],
				'default'      => 'medium',
				'prefix_class' => 'bdt-portfolio--thumbnail-size-',
			]
		);
	}

	/**
	 * Register portfolio query section controls.
	 *
	 * @param array $args {
	 *     @type int $posts_per_page_default Default posts per page (9 gallery/carousel, 8 list).
	 * }
	 */
	protected function register_portfolio_query_section_controls( $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'posts_per_page_default' => 9,
			]
		);

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
				'type'    => Controls_Manager::SELECT,
				'default' => 'portfolio',
			]
		);

		$this->update_control(
			'posts_per_page',
			[
				'default' => $args['posts_per_page_default'],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register portfolio additional options controls.
	 *
	 * @param array $args {
	 *     @type bool   $include_link_controls    Include show_link/lightbox controls (gallery/carousel).
	 *     @type int    $excerpt_limit_default    Default excerpt limit.
	 *     @type string $show_excerpt_default     Default show_excerpt value ('' or 'yes').
	 * }
	 */
	protected function register_portfolio_additional_options_controls( $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'include_link_controls' => true,
				'excerpt_limit_default' => 10,
				'show_excerpt_default'  => '',
			]
		);

		$this->start_controls_section(
			'section_layout_additional',
			[
				'label' => esc_html__( 'Additional Options', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
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
			'title_tag',
			[
				'label'     => esc_html__( 'Title HTML Tag', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => element_pack_title_tags(),
				'default'   => 'h4',
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$show_excerpt_args = [
			'label' => esc_html__( 'Show Text', 'bdthemes-element-pack' ),
			'type'  => Controls_Manager::SWITCHER,
		];
		if ( '' !== $args['show_excerpt_default'] ) {
			$show_excerpt_args['default'] = $args['show_excerpt_default'];
		}
		$this->add_control( 'show_excerpt', $show_excerpt_args );

		$this->add_control(
			'excerpt_limit',
			[
				'label'       => esc_html__( 'Text Limit', 'bdthemes-element-pack' ),
				'description' => esc_html__( 'It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => $args['excerpt_limit_default'],
				'condition'   => [
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_control(
			'ellipsis',
			[
				'label'     => esc_html__( 'Ellipsis', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => [ 'active' => true ],
				'condition' => [
					'show_excerpt'   => 'yes',
					'excerpt_limit!' => [ 0, '' ],
				],
				'ai'        => [
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
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_category',
			[
				'label' => esc_html__( 'Show Category', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		if ( $args['include_link_controls'] ) {
			$this->add_control(
				'show_link',
				[
					'label'   => esc_html__( 'Show Link', 'bdthemes-element-pack' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'both',
					'options' => [
						'post'     => esc_html__( 'Details Link', 'bdthemes-element-pack' ),
						'lightbox' => esc_html__( 'Lightbox Link', 'bdthemes-element-pack' ),
						'both'     => esc_html__( 'Both', 'bdthemes-element-pack' ),
						'none'     => esc_html__( 'None', 'bdthemes-element-pack' ),
					],
				]
			);

			$external_link_terms = [
				[
					'name'  => 'show_title',
					'value' => 'yes',
				],
				[
					'name'  => 'show_link',
					'value' => 'post',
				],
				[
					'name'  => 'show_link',
					'value' => 'both',
				],
			];

			if ( 'portfolio-carousel' === $this->get_portfolio_widget_prefix() ) {
				$external_link_terms = [
					[
						'name'     => 'show_title',
						'operator' => '==',
						'value'    => 'yes',
					],
					[
						'name'     => 'show_link',
						'operator' => '==',
						'value'    => [ 'post', 'both' ],
					],
				];
			}

			$this->add_control(
				'external_link',
				[
					'label'      => esc_html__( 'Show in new Tab (Details Link/Title)', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::SWITCHER,
					'conditions' => [
						'relation' => 'or',
						'terms'    => $external_link_terms,
					],
				]
			);

			$this->add_control(
				'link_type',
				[
					'label'     => esc_html__( 'Link Type', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'icon',
					'options'   => [
						'icon' => esc_html__( 'Icon', 'bdthemes-element-pack' ),
						'text' => esc_html__( 'Text', 'bdthemes-element-pack' ),
					],
					'condition' => [
						'show_link!' => 'none',
					],
				]
			);

			$this->add_control(
				'post_link_text',
				[
					'label'       => esc_html__( 'Details Link Text', 'bdthemes-element-pack' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => [ 'active' => true ],
					'default'     => esc_html__( 'VIEW', 'bdthemes-element-pack' ),
					'condition'   => [
						'show_link' => [ 'post', 'both' ],
						'link_type' => 'text',
					],
					'label_block' => false,
				]
			);

			$this->add_control(
				'lightbox_link_text',
				[
					'label'       => esc_html__( 'Lightbox Link Text', 'bdthemes-element-pack' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => [ 'active' => true ],
					'default'     => esc_html__( 'ZOOM', 'bdthemes-element-pack' ),
					'condition'   => [
						'show_link' => [ 'lightbox', 'both' ],
						'link_type' => 'text',
					],
					'label_block' => false,
				]
			);

			if ( 'portfolio-gallery' === $this->get_portfolio_widget_prefix() ) {
				$this->add_control(
					'tilt_show',
					[
						'label' => esc_html__( 'Tilt Effect', 'bdthemes-element-pack' ),
						'type'  => Controls_Manager::SWITCHER,
					]
				);

				$this->add_control(
					'tilt_scale',
					[
						'label'     => esc_html__( 'Zoom on Hover', 'bdthemes-element-pack' ),
						'type'      => Controls_Manager::SWITCHER,
						'condition' => [
							'tilt_show' => 'yes',
						],
					]
				);
			}

			$this->add_control(
				'lightbox_animation',
				[
					'label'     => esc_html__( 'Lightbox Animation', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'slide',
					'options'   => [
						'slide' => esc_html__( 'Slide', 'bdthemes-element-pack' ),
						'fade'  => esc_html__( 'Fade', 'bdthemes-element-pack' ),
						'scale' => esc_html__( 'Scale', 'bdthemes-element-pack' ),
					],
					'condition' => [
						'show_link' => [ 'both', 'lightbox' ],
					],
					'separator' => 'before',
				]
			);

			$this->add_control(
				'lightbox_autoplay',
				[
					'label'     => __( 'Lightbox Autoplay', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => [
						'show_link' => [ 'both', 'lightbox' ],
					],
				]
			);

			$this->add_control(
				'lightbox_pause',
				[
					'label'     => __( 'Lightbox Pause on Hover', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => [
						'show_link'         => [ 'both', 'lightbox' ],
						'lightbox_autoplay' => 'yes',
					],
				]
			);
		} else {
			$this->add_control(
				'external_link',
				[
					'label'     => esc_html__( 'Show in new Tab (Details Link/Title)', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => [
						'show_title' => 'yes',
					],
				]
			);
		}

		if ( 'portfolio-gallery' === $this->get_portfolio_widget_prefix() ) {
			$this->add_control(
				'grid_animation_type',
				[
					'label'     => esc_html__( 'Grid Entrance Animation', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '',
					'options'   => element_pack_transition_options(),
					'separator' => 'before',
				]
			);

			$this->add_control(
				'grid_anim_delay',
				[
					'label'      => esc_html__( 'Animation delay', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'ms', '' ],
					'range'      => [
						'ms' => [
							'min'  => 0,
							'max'  => 1000,
							'step' => 5,
						],
					],
					'default'    => [
						'unit' => 'ms',
						'size' => 300,
					],
					'condition'  => [
						'grid_animation_type!' => '',
					],
				]
			);
		}

		$this->end_controls_section();
	}


	/**
	 * Register portfolio filter bar content controls (gallery only).
	 */
	protected function register_portfolio_filter_bar_controls() {
		$this->start_controls_section(
			'filter_bar',
			[
				'label' => esc_html__( 'Filter Bar', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'show_filter_bar',
			[
				'label' => esc_html__( 'Show', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
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
						'show_filter_bar' => 'yes',
					],
				]
			);
		}

		$this->add_control(
			'show_filter_item_count',
			[
				'label'        => esc_html__( 'Filter Item Count', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'bdthemes-element-pack' ),
				'label_off'    => esc_html__( 'Hide', 'bdthemes-element-pack' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => [
					'show_filter_bar' => 'yes',
				],
			]
		);

		$this->add_control(
			'active_hash',
			[
				'label'     => esc_html__( 'Hash Location', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'no',
				'condition' => [
					'show_filter_bar' => 'yes',
				],
			]
		);

		$this->add_control(
			'hash_top_offset',
			[
				'label'      => esc_html__( 'Top Offset ', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '' ],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 1000,
						'step' => 5,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 70,
				],
				'condition'  => [
					'active_hash'     => 'yes',
					'show_filter_bar' => 'yes',
				],
			]
		);

		$this->add_control(
			'hash_scrollspy_time',
			[
				'label'      => esc_html__( 'Scrollspy Time', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'ms', '' ],
				'range'      => [
					'px' => [
						'min'  => 500,
						'max'  => 5000,
						'step' => 1000,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 1000,
				],
				'condition'  => [
					'active_hash'     => 'yes',
					'show_filter_bar' => 'yes',
				],
			]
		);

		$this->add_control(
			'filter_custom_text',
			[
				'label'       => esc_html__( 'Custom Text', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SWITCHER,
				'condition'   => [
					'show_filter_bar' => 'yes',
				],
				'description' => esc_html__( 'If you active this option. You can change (All) text without translator plugin. If you wish you can use translator plugin also.', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'filter_custom_text_all',
			[
				'label'     => esc_html__( 'Custom Text (All)', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => [ 'active' => true ],
				'condition' => [
					'show_filter_bar'    => 'yes',
					'filter_custom_text' => 'yes',
				],
				'default'   => esc_html__( 'All', 'bdthemes-element-pack' ),
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
	}


	/**
	 * Register portfolio items style controls.
	 *
	 * @param string $widget_prefix e.g. 'portfolio-gallery', 'portfolio-carousel', 'portfolio-list'.
	 * @param array  $args          Widget-specific style args.
	 */
	protected function register_portfolio_style_items_controls( $widget_prefix, $args = [] ) {
		if ( 'portfolio-list' === $widget_prefix ) {
			$this->register_portfolio_style_list_items_controls( $widget_prefix );
			return;
		}

		$wrapper = $this->get_portfolio_wrapper_class( $widget_prefix );
		$is_gallery = 'portfolio-gallery' === $widget_prefix;

		$this->start_controls_section(
			'section_design_layout',
			[
				'label' => esc_html__( 'Items', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_portfolio_style_overlay_controls( $widget_prefix );

		$this->add_control(
			'portfolio_content_style_headline',
			[
				'label'     => esc_html__( 'Content', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'content_width',
			[
				'label'     => esc_html__( 'Content Width(%)', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .' . $wrapper . '.skin-janes .bdt-gallery-item .bdt-portfolio-inner .bdt-portfolio-desc' => 'right: calc(100% - {{SIZE}}%);',
				],
				'condition' => [
					'_skin' => 'bdt-janes',
				],
			]
		);

		$this->add_responsive_control(
			'portfolio_content_alignment',
			[
				'label'        => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'      => 'center',
				'prefix_class' => 'bdt-custom-gallery-skin-fedara-style-',
				'selectors'    => [
					'{{WRAPPER}} .' . $wrapper . ' .bdt-portfolio-desc, {{WRAPPER}} .' . $wrapper . ' .bdt-portfolio-skin-fedara-desc' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'desc_background_color',
				'selector'  => '{{WRAPPER}} .' . $wrapper . ' .bdt-portfolio-desc, {{WRAPPER}} .' . $wrapper . ' .bdt-portfolio-skin-fedara-desc',
				'condition' => [
					'_skin!' => 'bdt-abetis',
				],
			]
		);

		$this->add_responsive_control(
			'desc__padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .' . $wrapper . ' .bdt-portfolio-desc, {{WRAPPER}} .' . $wrapper . ' .bdt-portfolio-skin-fedara-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .' . $wrapper . ' .bdt-portfolio-desc, {{WRAPPER}} .' . $wrapper . ' .bdt-portfolio-skin-fedara-desc' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .' . $wrapper . '.skin-janes .bdt-gallery-item .bdt-gallery-item-tags' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_category' => 'yes',
					'_skin'         => 'bdt-janes',
				],
			]
		);

		$this->add_control(
			'portfolio_item_headline',
			[
				'label'     => esc_html__( 'Item', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		if ( $is_gallery ) {
			$this->add_responsive_control(
				'item_gap',
				[
					'label'     => esc_html__( 'Column Gap', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => [
						'size' => 30,
					],
					'range'     => [
						'px' => [
							'min'  => 0,
							'max'  => 100,
							'step' => 5,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .' . $wrapper . '.bdt-grid'     => 'margin-left: -{{SIZE}}px',
						'{{WRAPPER}} .' . $wrapper . '.bdt-grid > *' => 'padding-left: {{SIZE}}px',
					],
				]
			);

			$this->add_responsive_control(
				'row_gap',
				[
					'label'     => esc_html__( 'Row Gap', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => [
						'size' => 30,
					],
					'range'     => [
						'px' => [
							'min'  => 0,
							'max'  => 100,
							'step' => 5,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .' . $wrapper . '.bdt-grid'     => 'margin-top: -{{SIZE}}px',
						'{{WRAPPER}} .' . $wrapper . '.bdt-grid > *' => 'margin-top: {{SIZE}}px',
					],
				]
			);
		}

		$item_border_selector    = $is_gallery ? '{{WRAPPER}} .' . $wrapper . ' .bdt-portfolio-inner' : '{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item';
		$item_radius_selector    = $is_gallery ? '{{WRAPPER}} .' . $wrapper . ' .bdt-portfolio-inner' : '{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item, {{WRAPPER}} .' . $wrapper . ' .swiper-carousel';
		$item_hover_border_sel   = $is_gallery ? '{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item:hover .bdt-portfolio-inner' : '{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item:hover';
		$item_hover_shadow_sel = $is_gallery ? '{{WRAPPER}} .' . $wrapper . ':hover .bdt-gallery-item .bdt-portfolio-inner' : '{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item:hover';

		$this->start_controls_tabs( 'tabs_item_style' );

		$this->start_controls_tab(
			'tab_item_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'item_border',
				'label'       => __( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => $item_border_selector,
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$item_radius_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'_skin!' => 'bdt-janes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_shadow',
				'selector' => $item_border_selector,
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[
				'label' => __( 'hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'item_hover_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'item_border_border!' => '',
				],
				'selectors' => [
					$item_hover_border_sel => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_hover_shadow',
				'selector' => $item_hover_shadow_sel,
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	/**
	 * Register portfolio list items style controls.
	 *
	 * @param string $widget_prefix e.g. 'portfolio-list'.
	 */
	protected function register_portfolio_style_list_items_controls( $widget_prefix ) {
		$wrapper = $this->get_portfolio_wrapper_class( $widget_prefix );

		$this->start_controls_section(
			'section_design_layout',
			[
				'label' => esc_html__( 'Items', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'   => esc_html__( 'Column Gap', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range'   => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .' . $wrapper . '.bdt-grid'     => 'margin-left: -{{SIZE}}px',
					'{{WRAPPER}} .' . $wrapper . '.bdt-grid > *' => 'padding-left: {{SIZE}}px',
				],
				'condition' => [
					'show_horizontal_layout' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'   => esc_html__( 'Row Gap', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range'   => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .' . $wrapper . '.bdt-grid'     => 'margin-top: -{{SIZE}}px',
					'{{WRAPPER}} .' . $wrapper . '.bdt-grid > *' => 'margin-top: {{SIZE}}px',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_border',
				'label'    => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .' . $wrapper . ' .bdt-portfolio-inner',
			]
		);

		$this->add_control(
			'item_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .' . $wrapper . ' .bdt-portfolio-inner, {{WRAPPER}} .' . $wrapper . ' .bdt-gallery-thumbnail img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_headline',
			[
				'label'     => esc_html__( 'Content', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'portfolio_content_alignment',
			[
				'label'   => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'   => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'left',
				'selectors' => [
					'{{WRAPPER}} .' . $wrapper . ' .bdt-portfolio-desc' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'desc_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .' . $wrapper . ' .bdt-portfolio-inner' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'desc__padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .' . $wrapper . ' .bdt-portfolio-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}


	/**
	 * Register portfolio overlay style controls (gallery/carousel).
	 *
	 * @param string $widget_prefix e.g. 'portfolio-gallery' or 'portfolio-carousel'.
	 */
	protected function register_portfolio_style_overlay_controls( $widget_prefix ) {
		$wrapper    = $this->get_portfolio_wrapper_class( $widget_prefix );
		$is_gallery = 'portfolio-gallery' === $widget_prefix;
		$overlay_bg_name = $is_gallery ? 'overlay_skin_background' : 'overlay_skin_abetis_background';

		$this->add_control(
			'overlay_style_headline',
			[
				'label'     => esc_html__( 'Overlay', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'_skin!' => [ 'bdt-janes', 'bdt-trosia' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => $overlay_bg_name,
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .' . $wrapper . '.skin-abetis .bdt-portfolio-inner:before, {{WRAPPER}} .' . $wrapper . '.skin-fedara .bdt-portfolio-inner:before',
				'condition' => [
					'_skin' => [ 'bdt-abetis', 'bdt-fedara' ],
				],
			]
		);

		$this->add_control(
			'overlay_primary_background',
			[
				'label'     => esc_html__( 'Primary Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .' . $wrapper . '.skin-default .bdt-portfolio-content-inner:before' => 'background: {{VALUE}};',
				],
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'overlay_secondary_background',
			[
				'label'     => esc_html__( 'Secondary Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .' . $wrapper . '.skin-default .bdt-portfolio-content-inner:after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'_skin' => '',
				],
			]
		);
	}

	/**
	 * Register portfolio title style controls.
	 *
	 * @param string $widget_prefix e.g. 'portfolio-gallery'.
	 */
	protected function register_portfolio_style_title_controls( $widget_prefix ) {
		$wrapper = $this->get_portfolio_wrapper_class( $widget_prefix );
		$is_list = 'portfolio-list' === $widget_prefix;

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
					'{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item .bdt-gallery-item-title' => 'color: {{VALUE}};',
				],
			]
		);

		if ( ! $is_list ) {
			$this->add_control(
				'title_hover_color',
				[
					'label'     => esc_html__( 'Hover Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item .bdt-gallery-item-title:hover' => 'color: {{VALUE}};',
					],
				]
			);
		}

		if ( $is_list ) {
			$this->add_control(
				'title_spacing',
				[
					'label'   => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
					'type'    => Controls_Manager::SLIDER,
					'range'   => [
						'px' => [
							'max' => 50,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item .bdt-gallery-item-title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);
		}

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-gallery-item .bdt-gallery-item-title',
			]
		);

		if ( ! $is_list ) {
			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				[
					'name'     => 'title_shadow',
					'label'    => __( 'Text Shadow', 'bdthemes-element-pack' ),
					'selector' => '{{WRAPPER}} .bdt-gallery-item .bdt-gallery-item-title',
				]
			);
		}

		$this->end_controls_section();
	}

	/**
	 * Register portfolio excerpt/text style controls.
	 *
	 * @param string $widget_prefix e.g. 'portfolio-gallery'.
	 */
	protected function register_portfolio_style_excerpt_controls( $widget_prefix ) {
		$wrapper = $this->get_portfolio_wrapper_class( $widget_prefix );
		$is_list = 'portfolio-list' === $widget_prefix;

		$this->start_controls_section(
			'section_style_excerpt',
			[
				'label'     => esc_html__( 'Text', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_control(
			'excerpt_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .' . $wrapper . ' .bdt-portfolio-excerpt' => 'color: {{VALUE}};',
				],
			]
		);

		if ( ! $is_list ) {
			$this->add_responsive_control(
				'excerpt_margin',
				[
					'label'     => esc_html__( 'Margin', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => [
						'{{WRAPPER}} .' . $wrapper . ' .bdt-portfolio-excerpt' => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
					],
				]
			);
		}

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'excerpt_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .' . $wrapper . ' .bdt-portfolio-excerpt',
			]
		);

		$this->end_controls_section();
	}


	/**
	 * Register portfolio category style controls.
	 *
	 * @param string $widget_prefix e.g. 'portfolio-gallery'.
	 */
	protected function register_portfolio_style_category_controls( $widget_prefix ) {
		$wrapper = $this->get_portfolio_wrapper_class( $widget_prefix );
		$is_list = 'portfolio-list' === $widget_prefix;

		$this->start_controls_section(
			'section_style_category',
			[
				'label'     => esc_html__( 'Category', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_category' => 'yes',
				],
			]
		);

		$this->add_control(
			'category_color',
			[
				'label'     => $is_list ? esc_html__( 'Color', 'bdthemes-element-pack' ) : esc_html__( 'Category Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-tags' => 'color: {{VALUE}};',
				],
			]
		);

		if ( $is_list ) {
			$this->add_control(
				'category_spacing',
				[
					'label'   => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
					'type'    => Controls_Manager::SLIDER,
					'range'   => [
						'px' => [
							'max' => 50,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-tags' => 'padding-top: {{SIZE}}{{UNIT}};',
					],
				]
			);
		} else {
			$this->add_control(
				'category_separator_color',
				[
					'label'     => esc_html__( 'Separator Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-tags .bdt-gallery-item-tag-separator' => 'border-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'category_background',
				[
					'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-tags' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'      => 'category_border',
					'label'     => esc_html__( 'Border', 'bdthemes-element-pack' ),
					'selector'  => '{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-tags',
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'category_border_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-tags' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'category_padding',
				[
					'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-tags' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'category_box_shadow',
					'selector' => '{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-tags',
				]
			);
		}

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-tag',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register portfolio link/button style controls (gallery/carousel).
	 *
	 * @param string $widget_prefix e.g. 'portfolio-gallery'.
	 */
	protected function register_portfolio_style_link_controls( $widget_prefix ) {
		$wrapper = $this->get_portfolio_wrapper_class( $widget_prefix );

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__( 'Button', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_link!' => 'none',
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
					'{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-link, {{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-link i, {{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-link span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-link' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-link',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-link',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
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

		$this->add_control(
			'border_radius_advanced',
			[
				'label'       => esc_html__( 'Radius', 'bdthemes-element-pack' ),
				'description' => sprintf( __( 'For example: <b>%1s</b> or Go <a href="%2s" target="_blank">this link</a> and copy and paste the radius value.', 'bdthemes-element-pack' ), '30% 70% 82% 18% / 46% 62% 38% 54%', 'https://9elements.github.io/fancy-border-radius/' ),
				'type'        => Controls_Manager::TEXT,
				'size_units'  => [ 'px', '%' ],
				'default'     => '30% 70% 82% 18% / 46% 62% 38% 54%',
				'selectors'   => [
					'{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-link' => 'border-radius: {{VALUE}}; overflow: hidden;',
				],
				'condition'   => [
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
					'{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-link span, {{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-link i',
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
			'hover_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-link:hover i'    => 'color: {{VALUE}};',
					'{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-link:hover span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_hover_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-link:hover, {{WRAPPER}} .' . $wrapper . '.skin-abetis .bdt-gallery-item-link:before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .' . $wrapper . ' .bdt-gallery-item-link.bdt-link-icon:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	/**
	 * Register portfolio filter item count style controls (gallery only).
	 * Call register_style_controls_filter() separately for filter bar styles.
	 */
	protected function register_portfolio_style_filter_controls() {
		$this->start_controls_section(
			'section_filter_count',
			[
				'label'     => esc_html__( 'Filter Item Count', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_filter_item_count' => 'yes',
				],
			]
		);

		$this->add_control(
			'filter_badge_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-grid-filter span' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'filter_badge_bg_color',
			[
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-grid-filter span' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'filter_badge_border',
				'label'    => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-ep-grid-filter span',
			]
		);

		$this->add_responsive_control(
			'filter_badge_radius',
			[
				'label'      => esc_html__( 'Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-grid-filter span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'filter_badge_postion_x',
			[
				'label'     => esc_html__( 'Position (X axis)', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => -50,
						'max'  => 50,
						'step' => 1,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => -22,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-grid-filter span' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'filter_badge_postion_y',
			[
				'label'     => esc_html__( 'Position (Y axis)', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => -50,
						'max'  => 50,
						'step' => 1,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => -16,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-grid-filter span' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'filter_badge_width',
			[
				'label'      => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-grid-filter span' => 'width: {{SIZE}}{{UNIT}}; line-height:{{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'filter_badge_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}}  .bdt-ep-grid-filter span',
			]
		);

		$this->end_controls_section();
	}


	/**
	 * Query portfolio posts.
	 *
	 * @param int $posts_per_page Posts per page.
	 */
	public function query_posts( $posts_per_page ) {
		$settings       = $this->get_settings();
		$widget_prefix  = $this->get_portfolio_widget_prefix();
		$posts_per_page = isset( $posts_per_page ) ? (int) $posts_per_page : 0;

		if ( 'portfolio-carousel' === $widget_prefix ) {
			$args = [];
			if ( $posts_per_page ) {
				$args['posts_per_page'] = $posts_per_page;
				$args['paged']          = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
			}

			$default = $this->getGroupControlQueryArgs();
			$args    = array_merge( $default, $args );

			$this->_query = new \WP_Query( $args );
			return;
		}

		$args             = $this->getGroupControlQueryArgs();
		$is_current_query = ( ! empty( $settings['posts_source'] ) && $settings['posts_source'] === 'current_query' );

		if ( $is_current_query ) {
			unset( $args['offset'] );
			unset( $args['no_found_rows'] );
			$posts_per_page = 0;
		}

		if ( $posts_per_page > 0 ) {
			$args['posts_per_page'] = $posts_per_page;
		} else {
			$args['posts_per_page'] = (int) get_option( 'posts_per_page', 10 );
		}

		$show_pagination = $settings['show_pagination'] ?? '';
		if ( 'portfolio-list' === $widget_prefix ) {
			$show_pagination = ( $settings['show_pagination'] === 'yes' ) ? 'yes' : $show_pagination;
		}

		if ( $show_pagination ) {
			$args['paged'] = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
		}

		$this->_query = new \WP_Query( $args );
	}

	/**
	 * Get taxonomies for portfolio filter/query.
	 *
	 * @param string $post_type Post type slug.
	 */
	public function get_taxonomies( $post_type = '' ) {
		if ( 'portfolio-list' === $this->get_portfolio_widget_prefix() && '' === $post_type ) {
			$taxonomies = get_taxonomies( [ 'show_in_nav_menus' => true ], 'objects' );
			$options    = [ '' => '' ];
			foreach ( $taxonomies as $taxonomy ) {
				$options[ $taxonomy->name ] = $taxonomy->label;
			}
			return $options;
		}

		$_taxonomies = [];
		if ( $post_type ) {
			$taxonomies                = get_taxonomies( [ 'public' => true, 'object_type' => [ $post_type ] ], 'object' );
			$tax                       = array_diff_key( wp_list_pluck( $taxonomies, 'label', 'name' ), [] );
			$_taxonomies[ $post_type ] = count( $tax ) !== 0 ? $tax : '';
		}
		return $_taxonomies;
	}

	/**
	 * Get filter menu term slugs for current post (gallery only).
	 */
	public function filter_menu_terms() {
		$settings    = $this->get_settings_for_display();
		$taxonomy    = $settings[ 'taxonomy_' . $settings['posts_source'] ];
		$categories  = get_the_terms( get_the_ID(), $taxonomy );
		$_categories = [];
		if ( $categories ) {
			foreach ( $categories as $category ) {
				$_categories[ $category->slug ] = $category->slug;
			}
		}
		return implode( ' ', $_categories );
	}

	/**
	 * Get filter menu categories (gallery only).
	 */
	protected function filter_menu_categories() {
		$settings           = $this->get_settings_for_display();
		$include_Categories = $settings['posts_include_term_ids'];
		$exclude_Categories = $settings['posts_exclude_term_ids'];
		$post_options       = [];
		if ( isset( $settings[ 'taxonomy_' . $settings['posts_source'] ] ) ) {
			$taxonomy        = $settings[ 'taxonomy_' . $settings['posts_source'] ];
			$params          = [
				'taxonomy'   => $taxonomy,
				'hide_empty' => true,
				'include'    => $include_Categories,
				'exclude'    => $exclude_Categories,
			];
			$post_categories = get_terms( $params );
			if ( is_wp_error( $post_categories ) ) {
				return $post_options;
			}
			if ( false !== $post_categories && is_array( $post_categories ) ) {
				foreach ( $post_categories as $category ) {
					$post_options[ $category->slug ] = $category->name;
				}
			}
		}

		return $post_options;
	}

	public function render_thumbnail( $settings ) {
		$thumb_settings                     = $settings;
		$thumb_settings['thumbnail_size']   = [ 'id' => get_post_thumbnail_id() ];
		$thumbnail_html                     = Group_Control_Image_Size::get_attachment_image_html( $thumb_settings, 'thumbnail_size' );
		$placeholder_img_src                = Utils::get_placeholder_image_src();
		$widget_prefix                      = $this->get_portfolio_widget_prefix();
		$thumb_size                         = $thumb_settings['thumbnail_size_size'] ?? ( $settings['thumbnail_size_size'] ?? 'medium' );

		if ( ! $thumbnail_html ) {
			printf(
				'<div class="bdt-gallery-thumbnail"><img src="%1$s" alt="%2$s"></div>',
				esc_url( $placeholder_img_src ),
				esc_attr( get_the_title() )
			);
		} else {
			echo '<div class="bdt-gallery-thumbnail">';
			echo wp_get_attachment_image(
				get_post_thumbnail_id(),
				$thumb_size,
				false,
				[ 'alt' => esc_attr( get_the_title() ) ]
			);
			echo '</div>';
		}
	}

	public function render_title( $settings ) {
		$widget_prefix = $this->get_portfolio_widget_prefix();

		if ( 'portfolio-list' === $widget_prefix ) {
			if ( $settings['show_title'] !== 'yes' ) {
				return;
			}
			$tag    = Utils::get_valid_html_tag( $settings['title_tag'] );
			$target = $settings['external_link'] === 'yes' ? '_blank' : '_self';
			?>
			<a href="<?php echo esc_url( get_permalink() ); ?>" target="<?php echo esc_attr( $target ); ?>">
				<<?php echo esc_attr( $tag ); ?> class="bdt-gallery-item-title bdt-margin-remove">
					<?php echo esc_html( get_the_title() ); ?>
				</<?php echo esc_attr( $tag ); ?>>
			</a>
			<?php
			return;
		}

		if ( empty( $settings['show_title'] ) ) {
			return;
		}

		$tag = Utils::get_valid_html_tag( $settings['title_tag'] ?? 'h4' );

		if ( 'portfolio-carousel' === $widget_prefix ) {
			$target = ! empty( $settings['external_link'] ) ? ' target="_blank"' : '';
			?>
			<a href="<?php echo esc_url( get_the_permalink() ); ?>"<?php echo $target; ?>>
				<<?php echo esc_attr( $tag ); ?> class="bdt-gallery-item-title bdt-margin-remove">
					<?php echo esc_html( get_the_title() ); ?>
				</<?php echo esc_attr( $tag ); ?>>
			</a>
			<?php
			return;
		}

		$target = ! empty( $settings['external_link'] ) ? '_blank' : '_self';
		?>
		<a href="<?php echo esc_url( get_the_permalink() ); ?>" target="<?php echo esc_attr( $target ); ?>">
			<<?php echo esc_attr( $tag ); ?> class="bdt-gallery-item-title bdt-margin-remove">
				<?php echo esc_html( get_the_title() ); ?>
			</<?php echo esc_attr( $tag ); ?>>
		</a>
		<?php
	}

	public function render_excerpt( $settings ) {
		$widget_prefix = $this->get_portfolio_widget_prefix();

		if ( 'portfolio-list' === $widget_prefix ) {
			if ( $settings['show_excerpt'] !== 'yes' ) {
				return;
			}
			$strip_shortcode = $settings['strip_shortcode'] === 'yes';
			$excerpt_limit   = $settings['excerpt_limit'];
			$ellipsis        = $settings['ellipsis'];
		} else {
			if ( empty( $settings['show_excerpt'] ) ) {
				return;
			}
			$strip_shortcode = ! empty( $settings['strip_shortcode'] );
			$ellipsis        = $settings['ellipsis'] ?? '';
			$excerpt_limit   = isset( $settings['excerpt_limit'] ) ? (int) $settings['excerpt_limit'] : 10;
		}
		?>
		<div class="bdt-portfolio-excerpt">
			<?php
			if ( has_excerpt() ) {
				the_excerpt();
			} else {
				echo wp_kses_post( element_pack_custom_excerpt( $excerpt_limit, $strip_shortcode, $ellipsis ) );
			}
			?>
		</div>
		<?php
	}

	public function render_categories_names( $settings, $args = [] ) {
		$widget_prefix = $this->get_portfolio_widget_prefix();
		$args          = wp_parse_args(
			$args,
			[
				'separator' => 'portfolio-list' === $widget_prefix
					? '<span class="bdt-gallery-item-tag-separator">, </span>'
					: '<span class="bdt-gallery-item-tag-separator"></span>',
			]
		);

		if ( 'portfolio-list' === $widget_prefix ) {
			if ( $settings['show_category'] !== 'yes' ) {
				return;
			}
		} elseif ( empty( $settings['show_category'] ) ) {
			return;
		}

		$this->add_render_attribute( 'portfolio-category', 'class', 'bdt-gallery-item-tags', true );

		global $post;

		$item_filters = get_the_terms( $post->ID, 'portfolio_filter' );
		$tags_array   = [];

		if ( 'portfolio-list' === $widget_prefix ) {
			if ( ! is_wp_error( $item_filters ) && is_array( $item_filters ) ) {
				foreach ( $item_filters as $item_filter ) {
					$tags_array[] = '<span class="bdt-gallery-item-tag">' . esc_html( $item_filter->name ) . '</span>';
				}
			}
		} else {
			if ( ! is_array( $item_filters ) ) {
				$item_filters = [];
			}
			foreach ( $item_filters as $item_filter ) {
				$tags_array[] = '<span class="bdt-gallery-item-tag">' . esc_html( $item_filter->name ) . '</span>';
			}
		}
		?>
		<div <?php $this->print_render_attribute_string( 'portfolio-category' ); ?>>
			<?php echo wp_kses_post( implode( $args['separator'], $tags_array ) ); ?>
		</div>
		<?php
	}


	public function render_overlay( $settings ) {
		$this->add_render_attribute(
			[
				'content-position' => [
					'class' => [ 'bdt-position-center' ],
				],
			],
			'',
			'',
			true
		);

		$placeholder_img_src = Utils::get_placeholder_image_src();
		$img_url             = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
		$img_url             = $img_url ? $img_url[0] : $placeholder_img_src;

		$widget_prefix = $this->get_portfolio_widget_prefix();
		$link_type     = $settings['link_type'] ?? 'icon';
		$show_link     = $settings['show_link'] ?? 'none';
		$link_class    = ( $link_type === 'icon' ) ? 'bdt-link-icon' : 'bdt-link-text';

		$this->add_render_attribute(
			[
				'lightbox-settings' => [
					'class'                        => [ 'bdt-gallery-item-link', 'bdt-gallery-lightbox-item', $link_class ],
					'data-elementor-open-lightbox' => 'no',
					'data-caption'                 => wp_kses_post( get_the_title() ),
					'href'                         => esc_url( $img_url ),
				],
			],
			'',
			'',
			true
		);
		?>
		<div <?php $this->print_render_attribute_string( 'content-position' ); ?>>
			<div class="bdt-portfolio-content">
				<div class="bdt-gallery-content-inner">
					<?php if ( $show_link !== 'none' ) : ?>
						<div class="bdt-flex-inline bdt-gallery-item-link-wrapper">
							<?php if ( $show_link === 'lightbox' || $show_link === 'both' ) : ?>
								<a <?php $this->print_render_attribute_string( 'lightbox-settings' ); ?>>
									<?php if ( $link_type === 'icon' ) : ?>
										<i class="ep-icon-search" aria-hidden="true"></i>
									<?php elseif ( $link_type === 'text' ) : ?>
										<?php
										$lightbox_text_ok = 'portfolio-gallery' === $widget_prefix
											? ( $settings['lightbox_link_text'] !== '' )
											: ! empty( $settings['lightbox_link_text'] );
										if ( $lightbox_text_ok ) :
											?>
											<span><?php echo esc_html( $settings['lightbox_link_text'] ); ?></span>
										<?php endif; ?>
									<?php endif; ?>
								</a>
							<?php endif; ?>

							<?php if ( $show_link === 'post' || $show_link === 'both' ) : ?>
								<?php $target = ! empty( $settings['external_link'] ) ? '_blank' : '_self'; ?>
								<a class="bdt-gallery-item-link <?php echo esc_attr( $link_type === 'icon' ? 'bdt-link-icon' : 'bdt-link-text' ); ?>"
									href="<?php echo esc_url( get_permalink() ); ?>" target="<?php echo esc_attr( $target ); ?>">
									<?php if ( $link_type === 'icon' ) : ?>
										<i class="ep-icon-link" aria-hidden="true"></i>
									<?php elseif ( ! empty( $settings['post_link_text'] ) ) : ?>
										<span><?php echo esc_html( $settings['post_link_text'] ); ?></span>
									<?php endif; ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	public function render_desc( $settings, $args = [] ) {
		?>
		<div class="bdt-portfolio-desc">
			<?php
			$this->render_title( $settings );
			$this->render_excerpt( $settings );
			if ( 'portfolio-list' === $this->get_portfolio_widget_prefix() ) {
				$this->render_categories_names( $settings, $args );
			}
			?>
		</div>
		<?php
	}

	public function render_filter_menu( $settings ) {
		$portfolio_categories = $this->filter_menu_categories();

		$this->add_render_attribute(
			[
				'portfolio-gallery-hash-data' => [
					'data-hash-settings' => [
						wp_json_encode( array_filter( [
							'id'                => 'bdt-portfolio-gallery-' . $this->get_id(),
							'activeHash'        => $settings['active_hash'] ?? '',
							'hashTopOffset'     => $settings['hash_top_offset']['size'] ?? 70,
							'hashScrollspyTime' => $settings['hash_scrollspy_time']['size'] ?? 1000,
						] ) ),
					],
				],
			]
		);

		$portfolio_gallery_id = 'bdt-portfolio-gallery-' . $this->get_id();
		?>
		<div class="bdt-ep-grid-filters-wrapper" id="<?php echo esc_attr( $portfolio_gallery_id ); ?>" <?php $this->print_render_attribute_string( 'portfolio-gallery-hash-data' ); ?>>

			<button class="bdt-button bdt-button-default bdt-hidden@m" type="button">
				<?php if ( $settings['filter_custom_text'] !== 'yes' ) : ?>
					<?php esc_html_e( 'Filter', 'bdthemes-element-pack' ); ?>
				<?php else : ?>
					<?php echo esc_html( $settings['filter_custom_text_filter'] ); ?>
				<?php endif; ?>
			</button>

			<div data-bdt-dropdown="mode: click;" class="bdt-dropdown bdt-margin-remove-top bdt-margin-remove-bottom bdt-drop">
				<ul class="bdt-nav bdt-dropdown-nav">

					<?php if ( $settings['filter_custom_text'] === 'yes' && $settings['filter_custom_text_all'] !== '' ) : ?>
						<li class="bdt-active bdt-ep-grid-filter" data-bdt-filter-control>
							<a href="#"><?php echo esc_html( $settings['filter_custom_text_all'] ); ?></a>
							<?php if ( $settings['show_filter_item_count'] === 'yes' ) : ?>
								<span class="bdt-all-count"></span>
							<?php endif; ?>
						</li>
					<?php else : ?>
						<li class="bdt-active bdt-ep-grid-filter" data-bdt-filter-control>
							<a href="#"><?php esc_html_e( 'All', 'bdthemes-element-pack' ); ?></a>
							<?php if ( $settings['show_filter_item_count'] === 'yes' ) : ?>
								<span class="bdt-all-count"></span>
							<?php endif; ?>
						</li>
					<?php endif; ?>

					<?php foreach ( $portfolio_categories as $slug => $category ) : ?>
						<li class="bdt-ep-grid-filter" data-bdt-target="<?php echo esc_attr( trim( $slug ) ); ?>"
							data-bdt-filter-control="[data-filter*='<?php echo esc_attr( trim( $slug ) ); ?>']">
							<a href="#"><?php echo esc_html( $category ); ?></a>
							<?php if ( $settings['show_filter_item_count'] === 'yes' ) : ?>
								<span class="bdt-count"></span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<ul id="bdt-ep-grid-filters<?php echo esc_attr( $this->get_id() ); ?>" class="bdt-ep-grid-filters bdt-visible@m"
				data-bdt-margin>

				<?php if ( $settings['filter_custom_text'] === 'yes' && $settings['filter_custom_text_all'] !== '' ) : ?>
					<li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control>
						<a href="#"><?php echo esc_html( $settings['filter_custom_text_all'] ); ?></a>
						<?php if ( $settings['show_filter_item_count'] === 'yes' ) : ?>
							<span class="bdt-all-count"></span>
						<?php endif; ?>
					</li>
				<?php else : ?>
					<li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control>
						<a href="#"><?php esc_html_e( 'All', 'bdthemes-element-pack' ); ?></a>
						<?php if ( $settings['show_filter_item_count'] === 'yes' ) : ?>
							<span class="bdt-all-count"></span>
						<?php endif; ?>
					</li>
				<?php endif; ?>

				<?php foreach ( $portfolio_categories as $slug => $category ) : ?>
					<li class="bdt-ep-grid-filter" data-bdt-target="<?php echo esc_attr( trim( $slug ) ); ?>"
						data-bdt-filter-control="[data-filter*='<?php echo esc_attr( trim( $slug ) ); ?>']">
						<a href="#"><?php echo esc_html( $category ); ?></a>
						<?php if ( $settings['show_filter_item_count'] === 'yes' ) : ?>
							<span class="bdt-count"></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Render portfolio gallery/list footer closing markup.
	 *
	 * @param string $widget_prefix e.g. 'portfolio-gallery' or 'portfolio-list'.
	 */
	public function render_portfolio_footer( $widget_prefix = '' ) {
		if ( '' === $widget_prefix ) {
			$widget_prefix = $this->get_portfolio_widget_prefix();
		}

		if ( 'portfolio-carousel' === $widget_prefix ) {
			return;
		}
		?>
			</div>
		</div>
		<?php
	}

	/**
	 * Build LearnPress widget CSS wrapper selector.
	 *
	 * @param string $type 'grid' or 'carousel'.
	 */
	protected function get_learnpress_wrapper_selector( $type ) {
		return '{{WRAPPER}} .ep-learnpress-' . $type;
	}

	/**
	 * Resolve LearnPress widget type from widget name.
	 */
	protected function get_learnpress_widget_type() {
		return false !== strpos( $this->get_name(), 'carousel' ) ? 'carousel' : 'grid';
	}

	/**
	 * Register shared LearnPress image size control.
	 */
	protected function register_learnpress_image_size_control() {
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'label'     => esc_html__( 'Image Size', 'bdthemes-element-pack' ),
				'exclude'   => [ 'custom' ],
				'default'   => 'medium',
			]
		);
	}

	protected function register_learnpress_query_controls() {
	    $this->start_controls_section(
	        'section_post_query_builder',
	        [
	            'label' => __('Query', 'bdthemes-element-pack'),
	            'tab' => Controls_Manager::TAB_CONTENT,
	        ]
	    );

	    $this->register_query_builder_controls();

	    $this->update_control(
	        'posts_source',
	        [
	            'type'      => Controls_Manager::SELECT,
	            'default'   => 'lp_course',
	            'options' => [
	                'lp_course' => "LearnPress Courses",
	                'manual_selection'   => __('Manual Selection', 'bdthemes-element-pack'),
	                'current_query'      => __('Current Query', 'bdthemes-element-pack'),
	                '_related_post_type' => __('Related', 'bdthemes-element-pack'),
	            ],
	        ]
	    );

	    $this->update_control(
	        'posts_selected_ids',
	        [
	            'query_args'  => [
	                'query' => 'posts',
	                'post_type' => 'lp_course'
	            ],
	        ]
	    );
	    $this->update_control(
	        'posts_offset',
	        [
	            'label' => __('Offset', 'bdthemes-element-pack'),
	            'type'  => Controls_Manager::NUMBER,
	            'default'   => 0,

	        ]
	    );

	    $this->end_controls_section();
	}
	/**
	 * Register LearnPress additional content controls.
	 *
	 * @param array $args {
	 *     @type string $section_id Section id (grid: section_edd_additional, carousel: section_additional).
	 * }
	 */
	protected function register_learnpress_additional_controls( $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'section_id' => 'section_additional',
			]
		);

		$this->start_controls_section(
			$args['section_id'],
			[
				'label' => esc_html__( 'Additional', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'show_categories',
			[
				'label'     => esc_html__( 'Categories', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			]
		);
		$this->add_control(
			'show_instructor',
			[
				'label'     => esc_html__( 'Instructor', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
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
			'title_tags',
			[
				'label'     => __( 'Title HTML Tag', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h2',
				'options'   => element_pack_title_tags(),
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_meta',
			[
				'label'     => esc_html__( 'Meta', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_level',
			[
				'label'     => esc_html__( 'Level', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);
		$this->add_control(
			'show_lessons',
			[
				'label'     => esc_html__( 'Lessons', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);
		$this->add_control(
			'show_quizzes',
			[
				'label'     => esc_html__( 'Quizzes', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);
		$this->add_control(
			'show_student',
			[
				'label'     => esc_html__( 'Student', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_duration',
			[
				'label'     => esc_html__( 'Duration', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_price',
			[
				'label'   => esc_html__( 'Price', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
		$this->end_controls_section();
	}
	/**
	 * Register LearnPress alignment control.
	 *
	 * @param string $type 'grid' or 'carousel'.
	 */
	protected function register_learnpress_alignment_control( $type ) {
		$selector = ( 'grid' === $type )
			? '{{WRAPPER}} .ep-learnpress-content-wrap'
			: '{{WRAPPER}} .ep-learnpress-carousel .ep-learnpress-content-wrap';

		$this->add_responsive_control(
			'alignment',
			[
				'label'     => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'   => 'left',
				'selectors' => [
					$selector => 'text-align:{{VALUE}}',
				],
			]
		);
	}
	protected function register_learnpress_filter_controls() {
	    $this->start_controls_section(
			'filter_bar',
			[ 
				'label' => esc_html__( 'Filter Bar', 'bdthemes-element-pack' ),
			]
		);

	    $this->add_control(
	        'show_filter_bar',
	        [
	            'label' => esc_html__('Show Filter', 'bdthemes-element-pack'),
	            'type'  => Controls_Manager::SWITCHER,
	            'separator' => 'before',
	            'default' => 'yes'
	        ]
	    );
	    $this->add_control(
	        'active_hash',
	        [
	            'label'       => esc_html__('Hash Location', 'bdthemes-element-pack'),
	            'type'        => Controls_Manager::SWITCHER,
	            'default'     => 'no',
	            'condition' => [
	                'show_filter_bar' => 'yes',
	            ],
	        ]
	    );

	    $this->add_control(
	        'hash_top_offset',
	        [
	            'label'     => esc_html__('Top Offset ', 'bdthemes-element-pack'),
	            'type'      => Controls_Manager::SLIDER,
	            'size_units' => ['px', ''],
	            'range' => [
	                'px' => [
	                    'min' => 1,
	                    'max' => 1000,
	                    'step' => 5,
	                ],

	            ],
	            'default' => [
	                'unit' => 'px',
	                'size' => 70,
	            ],
	            'condition' => [
	                'active_hash' => 'yes',
	                'show_filter_bar' => 'yes',
	            ],
	        ]
	    );

	    $this->add_control(
	        'hash_scrollspy_time',
	        [
	            'label'     => esc_html__('Scrollspy Time', 'bdthemes-element-pack'),
	            'type'      => Controls_Manager::SLIDER,
	            'size_units' => ['ms', ''],
	            'range' => [
	                'px' => [
	                    'min' => 500,
	                    'max' => 5000,
	                    'step' => 1000,
	                ],
	            ],
	            'default'   => [
	                'unit' => 'px',
	                'size' => 1000,
	            ],
	            'condition' => [
	                'active_hash' => 'yes',
	                'show_filter_bar' => 'yes',
	            ],
	        ]
	    );

	    $this->add_control(
			'filter_custom_text',
			[ 
				'label'     => esc_html__( 'Custom Text', 'bdthemes-element-pack' ) . BDTEP_NC,
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
				'default' => esc_html__( 'All Courses', 'bdthemes-element-pack' ),
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
	}
	/**
	 * Register LearnPress item style controls.
	 *
	 * @param string $type 'grid' or 'carousel'.
	 */
	protected function register_learnpress_style_item_controls( $type ) {
		$w       = $this->get_learnpress_wrapper_selector( $type );
		$is_grid = ( 'grid' === $type );

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

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_background',
				'label'    => __( 'Background', 'bdthemes-element-pack' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => $w . ' .ep-learnpress-item',
			]
		);

		if ( $is_grid ) {
			$this->add_responsive_control(
				'item_padding',
				[
					'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						$w . ' .ep-learnpress-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
		}

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'item_border',
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'selector'  => $w . ' .ep-learnpress-item',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'item_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$w . ' .ep-learnpress-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_shadow',
				'selector' => $w . ' .ep-learnpress-item',
			]
		);

		if ( ! $is_grid ) {
			$this->add_responsive_control(
				'item_shadow_padding',
				[
					'label'       => __( 'Match Padding', 'bdthemes-element-pack' ),
					'description' => __( 'You have to add padding for matching overlaping normal/hover box shadow when you used Box Shadow option.', 'bdthemes-element-pack' ),
					'type'        => Controls_Manager::SLIDER,
					'range'       => [
						'px' => [
							'min'  => 0,
							'step' => 1,
							'max'  => 50,
						],
					],
					'selectors'   => [
						'{{WRAPPER}} .swiper-carousel' => 'padding: {{SIZE}}{{UNIT}}; margin: 0 -{{SIZE}}{{UNIT}};',
					],
				]
			);
			$this->add_responsive_control(
				'item_padding',
				[
					'label'      => esc_html__( 'Item Padding', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						$w . ' .ep-learnpress-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_responsive_control(
				'content_padding',
				[
					'label'      => esc_html__( 'Content Padding', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						$w . ' .ep-learnpress-content-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
		}

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_hover_background',
				'label'    => __( 'Background', 'bdthemes-element-pack' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => $w . ' .ep-learnpress-item:hover',
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
					$w . ' .ep-learnpress-item:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_hover_shadow',
				'selector' => $w . ' .ep-learnpress-item:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}
	/**
	 * Register LearnPress category style controls.
	 *
	 * @param string $type 'grid' or 'carousel'.
	 */
	protected function register_learnpress_style_category_controls( $type ) {
		$w       = $this->get_learnpress_wrapper_selector( $type );
		$is_grid = ( 'grid' === $type );

		$this->start_controls_section(
			'section_style_category',
			[
				'label'     => esc_html__( 'Category', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_categories' => 'yes',
				],
			]
		);
		$this->start_controls_tabs(
			'category_tabs'
		);
		$this->start_controls_tab(
			'category_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);
		$this->add_control(
			'category_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$w . ' .ep-learnpress-category a' => 'color: {{VALUE}};',
				],
			]
		);

		if ( $is_grid ) {
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'category_background_color',
					'label'    => __( 'Background', 'bdthemes-element-pack' ),
					'types'    => [ 'classic', 'gradient' ],
					'selector' => $w . ' .ep-learnpress-lms-wrap .ep-learnpress-category a',
				]
			);
		} else {
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'category_bg_color',
					'label'    => __( 'Background', 'bdthemes-element-pack' ),
					'types'    => [ 'classic', 'gradient' ],
					'selector' => $w . ' .ep-learnpress-category a',
				]
			);
		}

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'category_border',
				'label'     => __( 'Border', 'bdthemes-element-pack' ),
				'selector'  => $w . ' .ep-learnpress-category a',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'category_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					$w . ' .ep-learnpress-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'category_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$w . ' .ep-learnpress-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'category_margin',
			[
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$w . ' .ep-learnpress-category' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => $w . ' .ep-learnpress-category a',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => $w . ' .ep-learnpress-category a',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'category_tab_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);
		$this->add_control(
			'hover_category_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$w . ' .ep-learnpress-category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		if ( $is_grid ) {
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'hover_category_bg_color',
					'label'    => __( 'Background', 'bdthemes-element-pack' ),
					'types'    => [ 'classic', 'gradient' ],
					'selector' => $w . ' .ep-learnpress-lms-wrap .ep-learnpress-category a:hover',
				]
			);
		} else {
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'hover_category_bg_color',
					'label'    => __( 'Background', 'bdthemes-element-pack' ),
					'types'    => [ 'classic', 'gradient' ],
					'selector' => $w . ' .ep-learnpress-category a:hover',
				]
			);
		}

		$this->add_control(
			'hover_category_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$w . ' .ep-learnpress-category a:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'category_border_border!' => '',
				],
				'separator' => 'before',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}
	/**
	 * Register LearnPress title style controls.
	 *
	 * @param string $type 'grid' or 'carousel'.
	 */
	protected function register_learnpress_style_title_controls( $type ) {
		$w = $this->get_learnpress_wrapper_selector( $type );

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
					$w . ' .ep-learnpress-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_title_color',
			[
				'label'     => esc_html__( 'Hover Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$w . ' .ep-learnpress-title a:hover' => 'color: {{VALUE}};',
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
					$w . ' .ep-learnpress-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => $w . ' .ep-learnpress-title a',
			]
		);

		$this->end_controls_section();
	}
	/**
	 * Register LearnPress instructor style controls.
	 *
	 * @param string $type 'grid' or 'carousel'.
	 */
	protected function register_learnpress_style_instructor_controls( $type ) {
		$w = $this->get_learnpress_wrapper_selector( $type );

		$this->start_controls_section(
			'section_style_instructor',
			[
				'label'     => esc_html__( 'Instructor', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_instructor' => 'yes',
				],
			]
		);

		$this->add_control(
			'instructor_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$w . ' .ep-learnpress-instructor a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_instructor_color',
			[
				'label'     => esc_html__( 'Hover Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$w . ' .ep-learnpress-instructor a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'instructor_margin',
			[
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$w . ' .ep-learnpress-instructor' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'instructor_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => $w . ' .ep-learnpress-instructor a',
			]
		);

		$this->end_controls_section();
	}
	/**
	 * Register LearnPress meta style controls.
	 *
	 * @param string $type 'grid' or 'carousel'.
	 */
	protected function register_learnpress_style_meta_controls( $type ) {
		$w = $this->get_learnpress_wrapper_selector( $type );

		$this->start_controls_section(
			'section_style_meta',
			[
				'label'     => esc_html__( 'Meta', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);
		$this->add_control(
			'meta_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$w . ' .ep-learnpress-meta-item' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'meta_separator_color',
			[
				'label'     => esc_html__( 'Separator Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$w . ' .ep-learnpress-item .bdt-divider' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'meta_column_spacing',
			[
				'label'      => __( 'Column Spacing', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
				],
				'selectors'  => [
					$w . ' .ep-learnpress-meta-wrap' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'meta_row_spacing',
			[
				'label'      => __( 'Row Spacing', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
				],
				'selectors'  => [
					$w . ' .ep-learnpress-meta-wrap' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'meta_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => $w . ' .ep-learnpress-meta-item',
			]
		);
		$this->end_controls_section();
	}
	/**
	 * Register LearnPress price style controls.
	 *
	 * @param string $type 'grid' or 'carousel'.
	 */
	protected function register_learnpress_style_price_controls( $type ) {
		$w       = $this->get_learnpress_wrapper_selector( $type );
		$is_grid = ( 'grid' === $type );

		$this->start_controls_section(
			'section_style_price',
			[
				'label'     => esc_html__( 'Price', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_price' => 'yes',
				],
			]
		);

		if ( $is_grid ) {
			$this->start_controls_tabs(
				'tabs_style_price'
			);
			$this->start_controls_tab(
				'tab_style_price_normal',
				[
					'label' => __( 'Normal', 'bdthemes-element-pack' ),
				]
			);
		}

		$this->add_control(
			'price_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$w . ' .ep-learnpress-price' => 'color: {{VALUE}};',
				],
			]
		);

		if ( $is_grid ) {
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'price_background',
					'label'    => __( 'Background', 'bdthemes-element-pack' ),
					'types'    => [ 'classic', 'gradient' ],
					'selector' => $w . ' .ep-learnpress-price',
				]
			);

			$this->add_responsive_control(
				'price_padding',
				[
					'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						$w . ' .ep-learnpress-price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator'  => 'before',
				]
			);
		}

		$this->add_responsive_control(
			'price_margin',
			[
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$w . ' .ep-learnpress-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		if ( ! $is_grid ) {
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'price_typography',
					'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
					'selector' => $w . ' .ep-learnpress-price',
				]
			);
		}

		if ( $is_grid ) {
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'     => 'price_border',
					'label'    => __( 'Border', 'bdthemes-element-pack' ),
					'selector' => $w . ' .ep-learnpress-price',
				]
			);

			$this->add_responsive_control(
				'price_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						$w . ' .ep-learnpress-price' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'price_typography',
					'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
					'selector' => $w . ' .ep-learnpress-price',
				]
			);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'tab_style_price_hover',
				[
					'label' => __( 'Hover', 'bdthemes-element-pack' ),
				]
			);
			$this->add_control(
				'price_hover_color',
				[
					'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						$w . ' .ep-learnpress-price:hover' => 'color: {{VALUE}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'price_hover_background',
					'label'    => __( 'Background', 'bdthemes-element-pack' ),
					'types'    => [ 'classic', 'gradient' ],
					'selector' => $w . ' .ep-learnpress-price:hover',
				]
			);
			$this->end_controls_tab();
			$this->end_controls_tabs();
		}

		$this->end_controls_section();
	}
	protected function register_learnpress_style_pagination_controls() {
	    $this->start_controls_section(
	        'section_style_pagination',
	        [
	            'label'     => esc_html__('Pagination', 'bdthemes-element-pack'),
	            'tab'       => Controls_Manager::TAB_STYLE,
	            'condition' => [
	                'show_pagination' => 'yes',
	            ],
	        ]
	    );
	    $this->add_responsive_control(
	        'pagination_spacing',
	        [
	            'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
	            'type'      => Controls_Manager::SLIDER,
	            'selectors' => [
	                '{{WRAPPER}} ul.bdt-pagination'    => 'margin-top: {{SIZE}}px;',
	                '{{WRAPPER}} .dataTables_paginate' => 'margin-top: {{SIZE}}px;',
	            ],
	        ]
	    );
	    $this->add_control(
	        'pagination_color',
	        [
	            'label'     => esc_html__('Color', 'bdthemes-element-pack'),
	            'type'      => Controls_Manager::COLOR,
	            'selectors' => [
	                '{{WRAPPER}} ul.bdt-pagination li a'    => 'color: {{VALUE}};',
	                '{{WRAPPER}} ul.bdt-pagination li span' => 'color: {{VALUE}};',
	                '{{WRAPPER}} .paginate_button'          => 'color: {{VALUE}} !important;',
	            ],
	        ]
	    );
	    $this->add_control(
	        'active_pagination_color',
	        [
	            'label'     => esc_html__('Active Color', 'bdthemes-element-pack'),
	            'type'      => Controls_Manager::COLOR,
	            'selectors' => [
	                '{{WRAPPER}} ul.bdt-pagination li.bdt-active a' => 'color: {{VALUE}};',
	                '{{WRAPPER}} .paginate_button.current'          => 'color: {{VALUE}} !important;',
	            ],
	        ]
	    );
	    $this->add_responsive_control(
	        'pagination_margin',
	        [
	            'label'     => esc_html__('Margin', 'bdthemes-element-pack'),
	            'type'      => Controls_Manager::DIMENSIONS,
	            'selectors' => [
	                '{{WRAPPER}} ul.bdt-pagination li a'    => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
	                '{{WRAPPER}} ul.bdt-pagination li span' => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
	                '{{WRAPPER}} .paginate_button'          => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
	            ],
	        ]
	    );

	    $this->add_responsive_control(
	        'pagination_arrow_size',
	        [
	            'label'     => esc_html__('Arrow Size', 'bdthemes-element-pack'),
	            'type'      => Controls_Manager::SLIDER,
	            'selectors' => [
	                '{{WRAPPER}} ul.bdt-pagination li a svg' => 'height: {{SIZE}}px; width: auto;',
	            ]
	        ]
	    );

	    $this->add_group_control(
	        Group_Control_Typography::get_type(),
	        [
	            'name'     => 'pagination_typography',
	            'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
	            'selector' => '{{WRAPPER}} ul.bdt-pagination li a, {{WRAPPER}} ul.bdt-pagination li span, {{WRAPPER}} .dataTables_paginate',
	        ]
	    );
	    $this->end_controls_section();
	}
	protected function render_learnpress_filter_menu() {
	    $settings           = $this->get_settings_for_display();
	    $id                 = $this->get_id();
	    $product_categories = [];
	    $this->query_product();
	    $wp_query = $this->get_query();

	    while ($wp_query->have_posts()) : $wp_query->the_post();
	        $terms = get_the_terms(get_the_ID(), 'course_category');
	        if (!empty($terms)) {
	            foreach ($terms as $term) {
	                $product_categories[] = esc_attr($term->slug);
	            };
	        }
	    endwhile;

	    wp_reset_postdata();

	    $product_categories = array_unique($product_categories);
	    $this->add_render_attribute(
	        [
	            'portfolio-gallery-hash-data' => [
	                'data-hash-settings' => [
	                    wp_json_encode(
	                        array_filter([
	                            "id"       => 'bdt-products-' . $id,
	                            'activeHash'          => $settings['active_hash'],
	                            'hashTopOffset'      => isset($settings['hash_top_offset']['size']) ? $settings['hash_top_offset']['size'] : 70,
	                            'hashScrollspyTime' => isset($settings['hash_scrollspy_time']['size']) ? $settings['hash_scrollspy_time']['size'] : 1000,
	                        ])
	                    ),
	                ],
	            ],
	        ]
	    ); ?>

	    <div class="bdt-ep-grid-filters-wrapper" id="<?php echo 'bdt-products-' . esc_attr($id); ?>" <?php $this->print_render_attribute_string('portfolio-gallery-hash-data'); ?>>
	        <button class="bdt-button bdt-button-default bdt-hidden@m" type="button">
	            <?php if ( isset( $settings['filter_custom_text'] ) && ( $settings['filter_custom_text'] != 'yes' ) ) : ?>
					<?php esc_html_e( 'Filter', 'bdthemes-element-pack' ); ?>
				<?php else : ?>
					<?php echo esc_html( $settings['filter_custom_text_filter'] ); ?>
				<?php endif; ?>
	        </button>
	        <div data-bdt-dropdown="mode: click; boundary: !.bdt-ep-grid-filters-wrapper; flip:false;" class="bdt-dropdown bdt-margin-remove-top bdt-margin-remove-bottom">
	            <ul class="bdt-nav bdt-dropdown-nav">

	                <?php if ( $settings['filter_custom_text']) : ?>
						<?php if ( ! empty($settings['filter_custom_text_all']) ) : ?>
							<li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control>
								<a href="#"><?php echo esc_html( $settings['filter_custom_text_all'] ); ?></a>
							</li>
						<?php endif; ?>
					<?php else : ?>
						<li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control>
							<a href="#"><?php esc_html_e( 'All Courses', 'bdthemes-element-pack' ); ?></a>
						</li>
					<?php endif; ?>

	                <?php foreach ($product_categories as $product_category => $value) : ?>
	                    <?php $filter_name = get_term_by('slug', $value, 'course_category'); ?>
	                    <li class="bdt-ep-grid-filter" data-bdt-filter-control="[data-filter*='bdtf-<?php echo esc_attr(trim($value)); ?>']">
	                        <a href="#"><?php echo esc_html($filter_name->name); ?></a>
	                    </li>
	                <?php endforeach; ?>

	            </ul>
	        </div>


	        <ul class="bdt-ep-grid-filters bdt-visible@m" data-bdt-margin>

	            <?php if ( $settings['filter_custom_text']) : ?>
					<?php if ( ! empty($settings['filter_custom_text_all']) ) : ?>
						<li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control>
							<a href="#"><?php echo esc_html( $settings['filter_custom_text_all'] ); ?></a>
						</li>
					<?php endif; ?>
				<?php else : ?>
					<li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control>
						<a href="#"><?php esc_html_e( 'All Courses', 'bdthemes-element-pack' ); ?></a>
					</li>
				<?php endif; ?>

	            <?php foreach ($product_categories as $product_category => $value) : ?>
	                <?php $filter_name = get_term_by('slug', $value, 'course_category'); ?>
	                <li class="bdt-ep-grid-filter" data-bdt-filter-control="[data-filter*='bdtf-<?php echo esc_attr(trim($value)); ?>']">
	                    <a href="#"><?php echo esc_html($filter_name->name); ?></a>
	                </li>
	            <?php endforeach; ?>
	        </ul>
	    </div>
	<?php
	}
	/**
	 * Render a single LearnPress course item.
	 *
	 * @param array $settings Widget settings.
	 * @param array $args {
	 *     @type array $extra_item_classes Extra CSS classes for item wrapper.
	 *     @type bool  $filter_enabled     Whether to add data-filter attribute (grid).
	 * }
	 */
	protected function render_learnpress_course_item( $settings, $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'extra_item_classes' => [],
				'filter_enabled'     => false,
			]
		);

		$course   = learn_press_get_course( get_the_ID() );
		$lessons  = $course->count_items( LP_LESSON_CPT );
		$quizzes  = $course->count_items( LP_QUIZ_CPT );
		$students = $course->count_students();
		$level    = learn_press_get_post_level( get_the_ID() );

		$item_classes = array_merge( [ 'ep-learnpress-item' ], $args['extra_item_classes'] );
		$this->add_render_attribute( 'learnpress-item', 'class', $item_classes, true );

		if ( $args['filter_enabled'] ) {
			$terms              = get_the_terms( get_the_ID(), 'course_category' );
			$product_filter_cat = [];
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$product_filter_cat[] = 'bdtf-' . esc_attr( $term->slug );
				}
			}
			$this->add_render_attribute( 'learnpress-item', 'data-filter', implode( ' ', $product_filter_cat ), true );
		}
		?>
		<div <?php $this->print_render_attribute_string( 'learnpress-item' ); ?>>
			<a class="ep-learnpress-image-wrap" href="<?php the_permalink(); ?>">
				<img src="<?php echo esc_url( wp_get_attachment_image_url( get_post_thumbnail_id(), $settings['image_size'] ) ); ?>" alt="<?php echo esc_html( get_the_title() ); ?>">
			</a>

			<div class="ep-learnpress-content-wrap">
				<?php if ( $settings['show_categories'] ) : ?>
					<div class="ep-learnpress-category">
						<?php echo get_the_term_list( get_the_ID(), 'course_category' ); ?>
					</div>
				<?php endif; ?>
				<?php if ( $settings['show_instructor'] ) : ?>
					<div class="ep-learnpress-instructor">
						<?php echo wp_kses_post( $course->get_instructor_html() ); ?>
					</div>
				<?php endif; ?>
				<?php if ( 'yes' === $settings['show_title'] ) :
					printf(
						'<%1$s class="ep-learnpress-title"><a href="%2$s">%3$s</a></%1$s>',
						esc_attr( Utils::get_valid_html_tag( $settings['title_tags'] ) ),
						esc_url( get_the_permalink() ),
						esc_html( get_the_title() )
					);
				endif; ?>

				<?php if ( 'yes' === $settings['show_meta'] ) : ?>
					<div class="ep-learnpress-meta-wrap">
						<?php if ( 'yes' === $settings['show_level'] ) : ?>
							<div class="ep-learnpress-meta-item ep-learnpress-meta-item-level">
								<span>
									<i class="ep-learnpress-meta-icon ep-icon-bar"></i>
									<?php echo esc_html( $level ); ?>
								</span>
							</div>
						<?php endif; ?>
						<?php if ( 'yes' === $settings['show_lessons'] ) : ?>
							<div class="ep-learnpress-meta-item ep-learnpress-meta-item-lesson">
								<span class="ep-learnpress-meta-number">
									<i class="ep-learnpress-meta-icon ep-icon-copy"></i>
									<?php echo esc_html( $lessons . '&nbsp;lessions' ); ?>
								</span>
							</div>
						<?php endif; ?>

						<?php if ( 'yes' === $settings['show_quizzes'] ) : ?>
							<div class="ep-learnpress-meta-item ep-learnpress-meta-item-quiz">
								<span class="ep-learnpress-meta-number">
									<i class="ep-learnpress-meta-icon ep-icon-puzzle"></i>
									<?php echo esc_html( $quizzes . '&nbsp;quizzes' ); ?>
								</span>
							</div>
						<?php endif; ?>

						<?php if ( 'yes' === $settings['show_student'] ) : ?>
							<div class="ep-learnpress-meta-item ep-learnpress-meta-item-student">
								<span class="ep-learnpress-meta-number">
									<i class="ep-learnpress-meta-icon ep-icon-graduation"></i>
									<?php echo esc_html( $students . '&nbsp;students' ); ?>
								</span>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="bdt-divider"></div>
				<div class="bdt-bottom-content">
					<?php if ( 'yes' === $settings['show_duration'] ) : ?>
						<div class="ep-learnpress-meta-item bdt-clock">
							<i class="ep-icon-clock"></i>
							<span><?php echo esc_html( learn_press_get_post_translated_duration( get_the_ID(), esc_html__( 'Lifetime access', 'bdthemes-element-pack' ) ) ); ?></span>
						</div>
					<?php endif; ?>

					<?php if ( 'yes' === $settings['show_price'] ) :
						printf( '<div class="ep-learnpress-price">%1$s</div>', wp_kses_post( $course->get_course_price_html() ) );
					endif; ?>

				</div>
			</div>

		</div>
		<?php
	}

	/**
	 * Render LearnPress grid loop with wrapper and pagination.
	 *
	 * @param array $settings Widget settings.
	 */
	protected function render_learnpress_grid_loop( $settings ) {
		$id       = 'ep-learnpress-grid-' . $this->get_id();
		$wp_query = $this->get_query();

		if ( ! $wp_query->have_posts() ) {
			return;
		}

		$this->add_render_attribute(
			[
				'learnpress-wrapper' => [
					'class' => [
						'ep-learnpress-lms-wrap',
						'ep-learnpress-lms-' . $settings['layout_type'],
					],
					'id'    => esc_attr( $id ),
				],
			]
		);
		?>
		<div <?php $this->print_render_attribute_string( 'learnpress-wrapper' ); ?>>
			<?php
			while ( $wp_query->have_posts() ) {
				$wp_query->the_post();
				$this->render_learnpress_course_item(
					$settings,
					[
						'filter_enabled' => ( 'yes' === $settings['show_filter_bar'] ),
					]
				);
			}
			?>
		</div>
		<?php
		if ( $settings['show_pagination'] ) {
			?>
			<div class="ep-pagination">
				<?php element_pack_post_pagination( $wp_query ); ?>
			</div>
			<?php
			wp_reset_postdata();
		}
	}

	/**
	 * Render LearnPress carousel loop inside swiper wrapper.
	 *
	 * @param array $settings Widget settings.
	 */
	protected function render_learnpress_carousel_loop( $settings ) {
		$wp_query = $this->get_query();

		if ( ! $wp_query->have_posts() ) {
			return;
		}

		while ( $wp_query->have_posts() ) {
			$wp_query->the_post();
			$this->render_learnpress_course_item(
				$settings,
				[
					'extra_item_classes' => [ 'swiper-slide' ],
				]
			);
			wp_reset_postdata();
		}
	}

	/**
	 * Build LearnPress course query.
	 */
	public function query_product() {
		$settings = $this->get_settings_for_display();
		$type     = $this->get_learnpress_widget_type();

		if ( 'carousel' === $type ) {
			$args                     = [];
			$default                  = $this->getGroupControlQueryArgs();
			$args['post_type']        = 'lp_course';
			$args['posts_per_page']   = $settings['posts_per_page'];
			$default                  = $this->getGroupControlQueryArgs();
			$args                     = array_merge( $default, $args );
			$this->_query             = new \WP_Query( $args );
			return;
		}

		$posts_per_page   = isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 0;
		$args             = $this->getGroupControlQueryArgs();
		$is_current_query = ( ! empty( $settings['posts_source'] ) && $settings['posts_source'] === 'current_query' );

		if ( $is_current_query ) {
			unset( $args['offset'] );
			unset( $args['no_found_rows'] );
			$posts_per_page = 0;
		}

		if ( $posts_per_page > 0 ) {
			$args['posts_per_page'] = $posts_per_page;
		} else {
			$args['posts_per_page'] = (int) get_option( 'posts_per_page', 10 );
		}

		$args['post_type'] = 'lp_course';

		if ( $settings['show_pagination'] ) {
			$args['paged'] = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
		}

		$this->_query = new \WP_Query( $args );
	}
	protected function register_tutor_lms_course_content_controls( $type ) {
		$this->add_group_control(
					Group_Control_Image_Size::get_type(),
					[
						'name'         => 'thumbnail_size',
						'label'        => esc_html__('Image Size', 'bdthemes-element-pack'),
						'exclude'      => ['custom'],
						'default'      => 'medium',
						'prefix_class' => 'grid' === $type ? 'bdt-portfolio--thumbnail-size-' : 'bdt-tutor--thumbnail-size-',
					]
				);

				$this->add_control(
					'item_ratio',
					[
						'label'   => esc_html__('Item Height', 'bdthemes-element-pack'),
						'type'    => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'min'  => 50,
								'max'  => 500,
								'step' => 5,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course-item .bdt-tutor-course-header a img' => 'height: {{SIZE}}px',
						],
					]
				);

				$this->add_control(
					'show_meta_label',
					[
						'label'   => esc_html__('Show Meta Label', 'bdthemes-element-pack'),
						'type'    => Controls_Manager::SWITCHER,
						'default' => 'yes',
					]
				);

				$this->add_control(
					'show_meta_wishlist',
					[
						'label'   => esc_html__('Show Wishlist Meta', 'bdthemes-element-pack'),
						'type'    => Controls_Manager::SWITCHER,
						'default' => 'yes',
					]
				);

				$this->add_control(
					'show_rating',
					[
						'label'   => esc_html__('Show Rating', 'bdthemes-element-pack'),
						'type'    => Controls_Manager::SWITCHER,
						'default' => 'yes',
					]
				);

				$this->add_control(
					'show_title',
					[
						'label'   => esc_html__('Show Title', 'bdthemes-element-pack'),
						'type'    => Controls_Manager::SWITCHER,
						'default' => 'yes',
					]
				);

				$this->add_control(
					'show_user_clock',
					[
						'label'   => esc_html__('Show User Meta', 'bdthemes-element-pack'),
						'type'    => Controls_Manager::SWITCHER,
						'default' => 'yes',
					]
				);

				$this->add_control(
					'show_author_avatar',
					[
						'label'   => esc_html__('Show Avatar', 'bdthemes-element-pack'),
						'type'    => Controls_Manager::SWITCHER,
						'default' => 'yes',
					]
				);

				$this->add_control(
					'show_author_name',
					[
						'label'   => esc_html__('Show Author Name', 'bdthemes-element-pack'),
						'type'    => Controls_Manager::SWITCHER,
						'default' => 'yes',
					]
				);

				$this->add_control(
					'show_category',
					[
						'label'   => esc_html__('Show Category', 'bdthemes-element-pack'),
						'type'    => Controls_Manager::SWITCHER,
						'default' => 'yes',
					]
				);

				$this->add_control(
					'show_cart_btn_price',
					[
						'label'   => esc_html__('Show Price/Cart Button', 'bdthemes-element-pack'),
						'type'    => Controls_Manager::SWITCHER,
						'default' => 'yes',
					]
				);
	}

	protected function register_tutor_lms_price_cart_controls() {
		$this->start_controls_section(
					'section_tlms_price_cart',
					[
						'label'     => esc_html__('Price & Cart', 'bdthemes-element-pack'),
						'tab'       => Controls_Manager::TAB_CONTENT,
						'condition' => [
							'show_cart_btn_price' => 'yes',
						],
					]
				);

				$this->add_control(
					'free_course_label',
					[
						'label'       => esc_html__('Free Course Label', 'bdthemes-element-pack'),
						'type'        => Controls_Manager::TEXT,
						'default'     => esc_html__( 'Free', 'bdthemes-element-pack' ),
						'label_block' => true,
					]
				);

				$this->add_control(
					'free_enroll_button_text',
					[
						'label'       => esc_html__('Free Course Button Text', 'bdthemes-element-pack'),
						'type'        => Controls_Manager::TEXT,
						'default'     => esc_html__( 'Get Enrolled', 'bdthemes-element-pack' ),
						'label_block' => true,
					]
				);

				$this->add_control(
					'ajax_add_to_cart',
					[
						'label'        => esc_html__('AJAX Add to Cart', 'bdthemes-element-pack'),
						'type'         => Controls_Manager::SWITCHER,
						'label_on'     => esc_html__( 'Yes', 'bdthemes-element-pack' ),
						'label_off'    => esc_html__( 'No', 'bdthemes-element-pack' ),
						'return_value' => 'yes',
						'default'      => '',
						'description'  => esc_html__( 'Disable (default) for a more reliable cart in Elementor and carousels; enable for add-to-cart without a full page reload.', 'bdthemes-element-pack' ),
					]
				);

				$this->end_controls_section();
	}

	protected function register_tutor_lms_query_controls() {
		$this->start_controls_section(
					'section_post_query_builder',
					[
						'label' => __('Query', 'bdthemes-element-pack'),
						'tab' => Controls_Manager::TAB_CONTENT,
					]
				);

				$this->register_query_builder_controls();

				$this->update_control(
					'posts_per_page',
					[
						'default' => 9,
					]
				);

				$this->update_control(
					'posts_source',
					[
						'default' => 'courses',
					]
				);

				$this->end_controls_section();
	}

	protected function register_tutor_lms_style_item_controls( $type ) {
		if ( 'grid' === $type ) {
			$this->start_controls_section(
						'section_tlms_cg_item_style',
						[
							'label' => esc_html__('Item', 'bdthemes-element-pack'),
							'tab'   => Controls_Manager::TAB_STYLE,
						]
					);

					$this->add_responsive_control(
						'item_gap',
						[
							'label'   => esc_html__('Column Gap', 'bdthemes-element-pack'),
							'type'    => Controls_Manager::SLIDER,
							'default' => [
								'size' => 30,
							],
							'range' => [
								'px' => [
									'min'  => 0,
									'max'  => 100,
									'step' => 5,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-lms-course-grid.bdt-grid'     => 'margin-left: -{{SIZE}}px',
								'{{WRAPPER}} .bdt-tutor-lms-course-grid.bdt-grid > *' => 'padding-left: {{SIZE}}px',
							],
						]
					);

					$this->add_responsive_control(
						'row_gap',
						[
							'label'   => esc_html__('Row Gap', 'bdthemes-element-pack'),
							'type'    => Controls_Manager::SLIDER,
							'default' => [
								'size' => 30,
							],
							'range' => [
								'px' => [
									'min'  => 0,
									'max'  => 100,
									'step' => 5,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-lms-course-grid.bdt-grid'     => 'margin-top: -{{SIZE}}px',
								'{{WRAPPER}} .bdt-tutor-lms-course-grid.bdt-grid > *' => 'margin-top: {{SIZE}}px',
							],
						]
					);

					$this->start_controls_tabs('tlms_cg_item_tabs');

					$this->start_controls_tab(
						'tlms_cg_item_tabs_normal',
						[
							'label' => __('Normal', 'bdthemes-element-pack'),
						]
					);

					$this->add_control(
						'tlms_cg_item_background',
						[
							'label'     => __('Background', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item' => 'background: {{VALUE}};',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						[
							'name' => 'tlms_cg_item_border',
							'selector' => '{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item',
						]
					);

					$this->add_responsive_control(
						'tlms_cg_item_radius',
						[
							'label' => __('Radius', 'bdthemes-element-pack'),
							'type' => Controls_Manager::DIMENSIONS,
							'size_units' => ['px', '%'],
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'tlms_cg_item_shadow',
							'selector' => '{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item',
						]
					);

					$this->end_controls_tab();

					$this->start_controls_tab(
						'tlms_cg_item_tabs_hover',
						[
							'label' => __('Hover', 'bdthemes-element-pack'),
						]
					);

					$this->add_control(
						'tlms_cg_item_hover_background',
						[
							'label'     => __('Background', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item:hover' => 'background: {{VALUE}};',
							],
						]
					);

					$this->add_control(
						'tlms_cg_item_hover_border_color',
						[
							'label'     => __('Border Color', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item:hover' => 'border-color: {{VALUE}};',
							],
							'condition' => [
								'tlms_cg_item_border_border!' => '',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'tlms_cg_item_hover_shadow',
							'selector' => '{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item:hover',
						]
					);

					$this->end_controls_tab();

					$this->end_controls_tabs();

					$this->end_controls_section();
		} else {
			$this->start_controls_section(
						'section_tlms_cg_item_style',
						[
							'label' => esc_html__('Item', 'bdthemes-element-pack'),
							'tab'   => Controls_Manager::TAB_STYLE,
						]
					);

					$this->start_controls_tabs('tlms_cg_item_tabs');

					$this->start_controls_tab(
						'tlms_cg_item_tabs_normal',
						[
							'label' => __('Normal', 'bdthemes-element-pack'),
						]
					);

					$this->add_control(
						'tlms_cg_item_background',
						[
							'label'     => __('Background', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item' => 'background: {{VALUE}};',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						[
							'name' => 'tlms_cg_item_border',
							'selector' => '{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item',
						]
					);

					$this->add_responsive_control(
						'tlms_cg_item_radius',
						[
							'label' => __('Radius', 'bdthemes-element-pack'),
							'type' => Controls_Manager::DIMENSIONS,
							'size_units' => ['px', '%'],
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item, {{WRAPPER}} .bdt-tutor-lms-course-carousel .swiper-carousel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'tlms_cg_item_shadow',
							'selector' => '{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item',
						]
					);

					$this->end_controls_tab();

					$this->start_controls_tab(
						'tlms_cg_item_tabs_hover',
						[
							'label' => __('Hover', 'bdthemes-element-pack'),
						]
					);

					$this->add_control(
						'tlms_cg_item_hover_background',
						[
							'label'     => __('Background', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item:hover' => 'background: {{VALUE}};',
							],
						]
					);

					$this->add_control(
						'tlms_cg_item_hover_border_color',
						[
							'label'     => __('Border Color', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item:hover' => 'border-color: {{VALUE}};',
							],
							'condition' => [
								'tlms_cg_item_border_border!' => '',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						[
							'name' => 'tlms_cg_item_hover_shadow',
							'selector' => '{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item:hover',
						]
					);

					$this->add_responsive_control(
						'tlms_cg_item_shadow_padding',
						[
							'label'       => __('Match Padding', 'bdthemes-element-pack'),
							'description' => __('You have to add padding for matching overlaping hover shadow', 'bdthemes-element-pack'),
							'type'        => Controls_Manager::SLIDER,
							'range'       => [
								'px' => [
									'min'  => 0,
									'step' => 1,
									'max'  => 50,
								]
							],
							'default' => [
								'size' => 10
							],
							'selectors' => [
								'{{WRAPPER}} .swiper-carousel' => 'padding: {{SIZE}}{{UNIT}}; margin: 0 -{{SIZE}}{{UNIT}};'
							]
						]
					);

					$this->end_controls_tab();

					$this->end_controls_tabs();

					$this->end_controls_section();
		}
	}

	protected function register_tutor_lms_style_header_controls() {
		$this->start_controls_section(
					'section_tlms_cg_header_style',
					[
						'label' => esc_html__('Header', 'bdthemes-element-pack'),
						'tab'   => Controls_Manager::TAB_STYLE,
					]
				);

				$this->add_control(
					'tlms_cg_header_image_heading',
					[
						'label' => esc_html__('Image', 'bdthemes-element-pack'),
						'type'  => Controls_Manager::HEADING,
					]
				);

				$this->add_responsive_control(
					'tlms_cg_image_radius',
					[
						'label' => __('Radius', 'bdthemes-element-pack'),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => ['px', '%'],
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item .bdt-tutor-course-header a, {{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item .bdt-tutor-course-header a img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'tlms_cg_header_meta_heading',
					[
						'label' => esc_html__('Meta Label', 'bdthemes-element-pack'),
						'type'  => Controls_Manager::HEADING,
						'separator'	=> 'before',
						'condition'	=> [
							'show_meta_label'	=> 'yes',
						],
					]
				);

				$this->start_controls_tabs('tlms_cg_header_meta_label_tabs');

				$this->start_controls_tab(
					'tlms_cg_header_meta_label_normal',
					[
						'label' => __('Normal', 'bdthemes-element-pack'),
						'condition'	=> [
							'show_meta_label'	=> 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_header_meta_label_color',
					[
						'label'     => __('Color', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level' => 'color: {{VALUE}};',
						],
						'condition'	=> [
							'show_meta_label'	=> 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_header_meta_label_background',
					[
						'label'     => __('Background', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level' => 'background: {{VALUE}};',
						],
						'condition'	=> [
							'show_meta_label'	=> 'yes',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'tlms_cg_header_meta_label_border',
						'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level',
						'condition'	=> [
							'show_meta_label'	=> 'yes',
						],
					]
				);

				$this->add_responsive_control(
					'tlms_cg_header_meta_label_radius',
					[
						'label' => __('Radius', 'bdthemes-element-pack'),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => ['px', '%'],
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition'	=> [
							'show_meta_label'	=> 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_header_meta_label_padding',
					[
						'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => ['px', 'em', '%'],
						'selectors'  => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition'	=> [
							'show_meta_label'	=> 'yes',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name'      => 'tlms_cg_header_meta_label_typography',
						'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
						'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level',
						'condition'	=> [
							'show_meta_label'	=> 'yes',
						],
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tlms_cg_header_meta_label_hover',
					[
						'label' => __('Hover', 'bdthemes-element-pack'),
						'condition'	=> [
							'show_meta_label'	=> 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_header_meta_label_hover_color',
					[
						'label'     => __('Color', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level:hover' => 'color: {{VALUE}};',
						],
						'condition'	=> [
							'show_meta_label'	=> 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_header_meta_label_hover_background',
					[
						'label'     => __('Background', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level:hover' => 'background: {{VALUE}};',
						],
						'condition'	=> [
							'show_meta_label'	=> 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_header_meta_label_hover_border_color',
					[
						'label'     => __('Border Color', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level:hover' => 'border-color: {{VALUE}};',
						],
						'condition'	=> [
							'show_meta_label'	=> 'yes',
						],
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_control(
					'tlms_cg_header_meta_wishlist_heading',
					[
						'label' => esc_html__('Wishlist Meta', 'bdthemes-element-pack'),
						'type'  => Controls_Manager::HEADING,
						'separator'	=> 'before',
						'condition'	=> [
							'show_meta_wishlist' => 'yes',
						],
					]
				);

				$this->start_controls_tabs('tlms_cg_header_meta_wishlist_tabs');

				$this->start_controls_tab(
					'tlms_cg_header_meta_wishlist_normal',
					[
						'label' => __('Normal', 'bdthemes-element-pack'),
						'condition'	=> [
							'show_meta_wishlist' => 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_header_meta_wishlist_color',
					[
						'label'     => __('Color', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist a' => 'color: {{VALUE}};',
						],
						'condition'	=> [
							'show_meta_wishlist' => 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_header_meta_wishlist_background',
					[
						'label'     => __('Background', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist' => 'background: {{VALUE}};',
						],
						'condition'	=> [
							'show_meta_wishlist' => 'yes',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'tlms_cg_header_meta_wishlist_border',
						'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist',
						'condition'	=> [
							'show_meta_wishlist' => 'yes',
						],
					]
				);

				$this->add_responsive_control(
					'tlms_cg_header_meta_wishlist_radius',
					[
						'label' => __('Radius', 'bdthemes-element-pack'),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => ['px', '%'],
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition'	=> [
							'show_meta_wishlist' => 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_header_meta_wishlist_padding',
					[
						'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => ['px', 'em', '%'],
						'selectors'  => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition'	=> [
							'show_meta_wishlist' => 'yes',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name'      => 'tlms_cg_header_meta_wishlist_typography',
						'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
						'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist',
						'condition'	=> [
							'show_meta_wishlist' => 'yes',
						],
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tlms_cg_header_meta_wishlist_hover',
					[
						'label' => __('Hover', 'bdthemes-element-pack'),
						'condition'	=> [
							'show_meta_wishlist' => 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_header_meta_wishlist_hover_color',
					[
						'label'     => __('Color', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist:hover a' => 'color: {{VALUE}};',
						],
						'condition'	=> [
							'show_meta_wishlist' => 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_header_meta_wishlist_hover_background',
					[
						'label'     => __('Background', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist:hover' => 'background: {{VALUE}};',
						],
						'condition'	=> [
							'show_meta_wishlist' => 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_header_meta_wishlist_hover_border_color',
					[
						'label'     => __('Border Color', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist:hover' => 'border-color: {{VALUE}};',
						],
						'conditions'   => [
							'relation'	=> 'or',
							'terms' => [
								[
									'name'  => 'tlms_cg_header_meta_wishlist_border_border!',
									'value' => '',
								],
								[
									'name'  => 'show_meta_wishlist',
									'value' => 'yes',
								],
							],
						],
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
	}

	protected function register_tutor_lms_style_content_area_controls() {
		$this->start_controls_section(
					'section_tlms_cg_content_area_style',
					[
						'label' => esc_html__('Content', 'bdthemes-element-pack'),
						'tab'   => Controls_Manager::TAB_STYLE,
						'conditions'   => [
							'relation'	=> 'or',
							'terms' => [
								[
									'name'  => 'show_rating',
									'value' => 'yes',
								],
								[
									'name'  => 'show_title',
									'value' => 'yes',
								],
								[
									'name'  => 'show_user_clock',
									'value' => 'yes',
								],
								[
									'name'     => 'show_category',
									'value'    => 'yes',
								],
								[
									'name'     => 'show_author_avatar',
									'value'    => 'yes',
								],
								[
									'name'     => 'show_author_name',
									'value'    => 'yes',
								],
							],
						],
					]
				);

				$this->add_control(
					'tlms_cg_content_background',
					[
						'label'     => __('Background', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-course-container' => 'background: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'tlms_cg_content_padding',
					[
						'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => ['px', 'em', '%'],
						'selectors'  => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-course-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->start_controls_tabs('tlms_cg_content_tabs_style');

				$this->start_controls_tab(
					'tlms_cg_content_rating',
					[
						'label' => __('Rating', 'bdthemes-element-pack'),
						'condition'	=> [
							'show_rating' => 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_content_rating_color',
					[
						'label'     => __('Color', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							// Tutor renders stars inside .tutor-ratings-stars (<i> or <span>); .tutor-star-rating-group is not used here.
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-rating-wrap .tutor-ratings-stars > i,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-rating-wrap .tutor-ratings-stars > span' => 'color: {{VALUE}};',
						],
						'condition'	=> [
							'show_rating' => 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_content_rating_icon_indent',
					[
						'label'   => esc_html__('Icon Spacing', 'bdthemes-element-pack'),
						'type'    => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'max' => 50,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-rating-wrap .tutor-ratings-stars > i:not(:last-of-type),{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-rating-wrap .tutor-ratings-stars > span:not(:last-of-type)'  => 'margin-inline-end: {{SIZE}}{{UNIT}};',
						],
						'condition'	=> [
							'show_rating' => 'yes',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name'      => 'tlms_cg_content_rating_typography',
						'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
						'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-rating-wrap .bdt-tutor-rating-count, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-rating-wrap .tutor-ratings-stars',
						'condition'	=> [
							'show_rating' => 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_content_rating_spacing',
					[
						'label'   => esc_html__('Spacing', 'bdthemes-element-pack'),
						'type'    => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'max' => 50,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-rating-wrap .tutor-ratings-stars'  => 'margin-block-end: {{SIZE}}{{UNIT}};',
						],
						'condition'	=> [
							'show_rating' => 'yes',
						],
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tlms_cg_content_title',
					[
						'label' => __('Title', 'bdthemes-element-pack'),
						'condition'	=> [
							'show_title' => 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_content_title_color',
					[
						'label'     => __('Color', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-title h2 a' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'tlms_cg_content_title_hover_color',
					[
						'label'     => __('Hover Color', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-title h2 a:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name'      => 'tlms_cg_content_title_typography',
						'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
						'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-title h2',
					]
				);

				$this->add_control(
					'tlms_cg_content_title_spacing',
					[
						'label'   => esc_html__('Spacing', 'bdthemes-element-pack'),
						'type'    => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'max' => 50,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-title h2'  => 'margin-bottom: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tlms_cg_content_meta',
					[
						'label' => __('User Meta', 'bdthemes-element-pack'),
						'condition'	=> [
							'show_user_clock' => 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_content_meta_color',
					[
						'label'     => __('Color', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-meta' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name'      => 'tlms_cg_content_meta_typography',
						'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
						'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-meta',
					]
				);

				$this->add_control(
					'tlms_cg_content_meta_spacing',
					[
						'label'   => esc_html__('Spacing', 'bdthemes-element-pack'),
						'type'    => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'max' => 50,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-meta'  => 'margin-bottom: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tlms_cg_content_author',
					[
						'label' => __('Author', 'bdthemes-element-pack'),
						'conditions'   => [
							'relation'	=> 'or',
							'terms' => [
								[
									'name'  => 'show_author_name',
									'value' => 'yes',
								],
								[
									'name'     => 'show_category',
									'value'    => 'yes',
								],
								[
									'name'     => 'show_author_avatar',
									'value'    => 'yes',
								],
							],
						],
					]
				);

				$this->add_control(
					'tlms_cg_content_author_heading',
					[
						'label' => esc_html__('Avatar', 'bdthemes-element-pack'),
						'type'  => Controls_Manager::HEADING,
						'condition'	=> [
							'show_author_avatar' => 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_content_author_avatar_color',
					[
						'label'     => __('Color', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar .tutor-avatar .tutor-avatar-text' => 'color: {{VALUE}} !important;',
						],
						'condition'	=> [
							'show_author_avatar' => 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_content_author_avatar_background',
					[
						'label'     => __('Background', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar .tutor-avatar .tutor-avatar-text' => 'background-color: {{VALUE}} !important;',
						],
						'condition'	=> [
							'show_author_avatar' => 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_content_author_avatar_height_width',
					[
						'label'   => esc_html__('Width', 'bdthemes-element-pack'),
						'type'    => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'max' => 50,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar .tutor-avatar' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar .tutor-avatar img' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
						],
						'condition'	=> [
							'show_author_avatar' => 'yes',
						],
					]
				);

				$this->add_responsive_control(
					'tlms_cg_content_author_avatar_radius',
					[
						'label' => __('Radius', 'bdthemes-element-pack'),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => ['px', '%'],
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar .tutor-avatar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar .tutor-avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar .tutor-avatar .tutor-avatar-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition'	=> [
							'show_author_avatar' => 'yes',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name'      => 'tlms_cg_content_author_avatar_typography',
						'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
						'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar .tutor-avatar .tutor-avatar-text',
						'condition'	=> [
							'show_author_avatar' => 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_content_author_avatar_spacing',
					[
						'label'   => esc_html__('Spacing', 'bdthemes-element-pack'),
						'type'    => Controls_Manager::SLIDER,
						'range' => [
							'px' => [
								'max' => 50,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar' => 'margin-inline-end: {{SIZE}}{{UNIT}};',
						],
						'condition'	=> [
							'show_author_avatar' => 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_content_category_heading',
					[
						'label' => esc_html__('Author Name / Category', 'bdthemes-element-pack'),
						'type'  => Controls_Manager::HEADING,
						'separator'	=> 'before',
						'conditions'   => [
							'relation'	=> 'or',
							'terms' => [
								[
									'name'  => 'show_author_name',
									'value' => 'yes',
								],
								[
									'name'     => 'show_category',
									'value'    => 'yes',
								],
							],
						],
					]
				);

				$this->add_control(
					'tlms_cg_content_author_category_color',
					[
						'label'     => __('Color', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author > div a' => 'color: {{VALUE}}',
						],
						'conditions'   => [
							'relation'	=> 'or',
							'terms' => [
								[
									'name'  => 'show_author_name',
									'value' => 'yes',
								],
								[
									'name'     => 'show_category',
									'value'    => 'yes',
								],
							],
						],
					]
				);

				$this->add_control(
					'tlms_cg_content_author_category_hover_color',
					[
						'label'     => __('Hover Color', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author > div a:hover' => 'color: {{VALUE}}',
						],
						'conditions'   => [
							'relation'	=> 'or',
							'terms' => [
								[
									'name'  => 'show_author_name',
									'value' => 'yes',
								],
								[
									'name'     => 'show_category',
									'value'    => 'yes',
								],
							],
						],
					]
				);

				$this->add_control(
					'tlms_cg_content_category_sub_text_color',
					[
						'label'     => __('Sub Text Color', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author > div span' => 'color: {{VALUE}}',
						],
						'conditions'   => [
							'relation'	=> 'or',
							'terms' => [
								[
									'name'  => 'show_author_name',
									'value' => 'yes',
								],
								[
									'name'     => 'show_category',
									'value'    => 'yes',
								],
							],
						],
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name'      => 'tlms_cg_content_category_typography',
						'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
						'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author',
						'conditions'   => [
							'relation'	=> 'or',
							'terms' => [
								[
									'name'  => 'show_author_name',
									'value' => 'yes',
								],
								[
									'name'     => 'show_category',
									'value'    => 'yes',
								],
							],
						],
					]
				);

				$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->end_controls_section();
	}

	protected function register_tutor_lms_style_footer_area_controls() {
		$this->start_controls_section(
					'section_tlms_cg_footer_area_style',
					[
						'label' => esc_html__('Footer', 'bdthemes-element-pack'),
						'tab'   => Controls_Manager::TAB_STYLE,
						'condition'	=> [
							'show_cart_btn_price'	=> 'yes',
						],
					]
				);

				$this->add_control(
					'tlms_cg_footer_area_background',
					[
						'label'     => __('Background', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-course-footer' => 'background: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'tlms_cg_footer_area_border',
						'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-course-footer',
					]
				);

				$this->add_control(
					'tlms_cg_footer_area_padding',
					[
						'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => ['px', 'em', '%'],
						'selectors'  => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-course-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'tlms_cg_footer_area_margin',
					[
						'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => ['px', 'em', '%'],
						'selectors'  => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-course-footer' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'tlms_cg_footer_area_box_shadow',
						'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-course-footer',
					]
				);

				$this->add_control(
					'tlms_cg_footer_price_row_heading',
					[
						'label'     => esc_html__('Price Row', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_responsive_control(
					'tlms_cg_footer_loop_price_margin',
					[
						'label'      => esc_html__('Outer Margin', 'bdthemes-element-pack'),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => ['px', 'em', '%'],
						'selectors'  => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'tlms_cg_footer_price_inner_background',
					[
						'label'     => esc_html__('Inner Background', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'     => 'tlms_cg_footer_price_inner_border',
						'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price',
					]
				);

				$this->add_responsive_control(
					'tlms_cg_footer_price_inner_padding',
					[
						'label'      => esc_html__('Inner Padding', 'bdthemes-element-pack'),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => ['px', 'em', '%'],
						'selectors'  => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'tlms_cg_footer_price_row_gap',
					[
						'label'      => esc_html__('Space Between Price & Button', 'bdthemes-element-pack'),
						'type'       => Controls_Manager::SLIDER,
						'size_units' => ['px', 'em'],
						'range'      => [
							'px' => [
								'min' => 0,
								'max' => 80,
							],
							'em' => [
								'min' => 0,
								'max' => 5,
							],
						],
						'selectors'  => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price' => 'gap: {{SIZE}}{{UNIT}}; column-gap: {{SIZE}}{{UNIT}}; row-gap: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'tlms_cg_footer_price_inner_box_shadow',
						'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price',
					]
				);

				$this->add_control(
					'tlms_cg_footer_price_heading',
					[
						'label'     => esc_html__('Price Text', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name'     => 'tlms_cg_footer_price_typography',
						'label'    => esc_html__('Current Price Typography', 'bdthemes-element-pack'),
						'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price ins, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price ins .woocommerce-Price-amount, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price > span.woocommerce-Price-amount, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price > span.amount, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .subscription-details',
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name'     => 'tlms_cg_footer_price_old_typography',
						'label'    => esc_html__('Old Price Typography', 'bdthemes-element-pack'),
						'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price del, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price del .woocommerce-Price-amount',
					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name'     => 'tlms_cg_footer_price_free_typography',
						'label'    => esc_html__('Free Label Typography', 'bdthemes-element-pack'),
						'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .bdt-tutor-price-free',
					]
				);

				$this->add_control(
					'tlms_cg_footer_price_colors_heading',
					[
						'label'     => esc_html__('Price Colors', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					'tlms_cg_footer_price_color',
					[
						'label'     => esc_html__('Current Price', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							// Avoid `.price *` and avoid `del` amounts so “Old Price” control can style sales.
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price' => 'color: {{VALUE}};',
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price ins' => 'color: {{VALUE}};',
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price ins .woocommerce-Price-amount' => 'color: {{VALUE}};',
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price > span.woocommerce-Price-amount' => 'color: {{VALUE}};',
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price > span.amount' => 'color: {{VALUE}};',
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .subscription-details' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'tlms_cg_footer_price_old_color',
					[
						'label'     => esc_html__('Old Price', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price del' => 'color: {{VALUE}};',
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price del .woocommerce-Price-amount' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'tlms_cg_footer_price_free_color',
					[
						'label'     => esc_html__('Free Label', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .bdt-tutor-price-free' => 'color: {{VALUE}};',
						],
					]
				);

				$this->end_controls_section();
	}

	protected function register_tutor_lms_style_cart_button_controls( $type ) {
		if ( 'grid' === $type ) {
			$this->start_controls_section(
						'section_tlms_add_to_cart_button_style',
						[
							'label'     => esc_html__('Cart/Enroll Button', 'bdthemes-element-pack'),
							'tab'       => Controls_Manager::TAB_STYLE,
							'condition' => [
								'show_cart_btn_price' => 'yes',
							],
						]
					);

					$this->add_control(
						'tlms_cg_footer_cart_intro',
						[
							'raw'       => esc_html__('Styles WooCommerce/Tutor cart links inside the footer price row: Add to cart, View cart, free enroll.', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::RAW_HTML,
							'content_classes' => 'elementor-descriptor',
						]
					);

					$this->add_group_control(
						Group_Control_Typography::get_type(),
						[
							'name'     => 'tlms_cg_footer_cart_typography',
							'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
							'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a .cart-text,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.added_to_cart, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-btn',
						]
					);

					$this->start_controls_tabs('tlms_cg_footer_cart_tabs');

					$this->start_controls_tab(
						'tlms_cg_footer_cart_normal',
						[
							'label' => __('Normal', 'bdthemes-element-pack'),
						]
					);

					$this->add_control(
						'tlms_cg_footer_cart_color',
						[
							'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a .cart-text,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.added_to_cart, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-btn' => 'color: {{VALUE}};',
							],
						]
					);

					$this->add_control(
						'tlms_cg_footer_cart_icon_color',
						[
							'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:not(.add_to_cart_button):not(.added_to_cart)::before' => 'color: {{VALUE}};',
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a .tutor-icon-cart-line::before,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button .tutor-icon-cart-line::before' => 'color: {{VALUE}};',
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a .tutor-icon-cart-line,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button .tutor-icon-cart-line' => 'color: {{VALUE}};',
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a [class*="tutor-icon"],{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button [class*="tutor-icon"]' => 'color: {{VALUE}};',
							],
						]
					);

					$this->add_responsive_control(
						'tlms_cg_footer_cart_icon_size',
						[
							'label'      => esc_html__('Icon Size', 'bdthemes-element-pack'),
							'type'       => Controls_Manager::SLIDER,
							'size_units' => ['px', 'em'],
							'range'      => [
								'px' => [
									'min' => 8,
									'max' => 48,
								],
								'em' => [
									'min' => 0.5,
									'max' => 3,
								],
							],
							'selectors'  => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:not(.add_to_cart_button):not(.added_to_cart)::before' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a .tutor-icon-cart-line,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button .tutor-icon-cart-line' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-btn .tutor-icon-cart-line::before' => 'font-size: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'tlms_cg_footer_cart_icon_gap',
						[
							'label'      => esc_html__('Space After Icon', 'bdthemes-element-pack'),
							'type'       => Controls_Manager::SLIDER,
							'size_units' => ['px', 'em'],
							'range'      => [
								'px' => [
									'min' => 0,
									'max' => 40,
								],
							],
							'selectors'  => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:not(.add_to_cart_button):not(.added_to_cart)::before' => 'margin-inline-end: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a .tutor-icon-cart-line,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button .tutor-icon-cart-line,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-btn .tutor-icon-cart-line::before' => 'margin-inline-end: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'tlms_cg_footer_cart_background',
						[
							'label'     => __('Background', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.added_to_cart, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-btn' => 'background: {{VALUE}};',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						[
							'name'     => 'tlms_cg_footer_cart_border',
							'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.added_to_cart, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-btn',
						]
					);

					$this->add_responsive_control(
						'tlms_cg_footer_cart_radius',
						[
							'label'      => __('Radius', 'bdthemes-element-pack'),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => ['px', '%'],
							'selectors'  => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.added_to_cart, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'tlms_cg_footer_cart_padding',
						[
							'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => ['px', 'em', '%'],
							'selectors'  => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.added_to_cart, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						[
							'name'     => 'tlms_cg_footer_cart_box_shadow',
							'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.added_to_cart, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-btn',
						]
					);

					$this->end_controls_tab();

					$this->start_controls_tab(
						'tlms_cg_footer_cart_hover',
						[
							'label' => __('Hover', 'bdthemes-element-pack'),
						]
					);

					$this->add_control(
						'tlms_cg_footer_cart_hover_color',
						[
							'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:hover,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:hover .cart-text,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button:hover,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.added_to_cart:hover, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-btn:hover' => 'color: {{VALUE}};',
							],
						]
					);

					$this->add_control(
						'tlms_cg_footer_cart_icon_hover_color',
						[
							'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:not(.add_to_cart_button):not(.added_to_cart):hover::before' => 'color: {{VALUE}};',
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:hover .tutor-icon-cart-line::before,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button:hover .tutor-icon-cart-line::before' => 'color: {{VALUE}};',
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:hover .tutor-icon-cart-line,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button:hover .tutor-icon-cart-line' => 'color: {{VALUE}};',
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:hover [class*="tutor-icon"],{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button:hover [class*="tutor-icon"]' => 'color: {{VALUE}};',
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-btn:hover .tutor-icon-cart-line::before' => 'color: {{VALUE}};',
							],
						]
					);

					$this->add_control(
						'tlms_cg_footer_cart_hover_background',
						[
							'label'     => __('Background', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:hover,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button:hover,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.added_to_cart:hover, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-btn:hover' => 'background: {{VALUE}};',
							],
						]
					);

					$this->add_control(
						'tlms_cg_footer_cart_hover_border_color',
						[
							'label'     => __('Border Color', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:hover,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button:hover,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.added_to_cart:hover, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-btn:hover' => 'border-color: {{VALUE}};',
							],
							'condition' => [
								'tlms_cg_footer_cart_border_border!' => '',
							],
						]
					);

					$this->end_controls_tab();

					$this->end_controls_tabs();

					$this->end_controls_section();
		} else {
			$this->start_controls_section(
						'section_tlms_add_to_cart_button_style',
						[
							'label'     => esc_html__('Add to Cart Button', 'bdthemes-element-pack'),
							'tab'       => Controls_Manager::TAB_STYLE,
							'condition' => [
								'show_cart_btn_price' => 'yes',
							],
						]
					);

					$this->add_control(
						'tlms_cg_footer_cart_intro',
						[
							'raw'       => esc_html__('Styles WooCommerce/Tutor cart links inside the footer price row: Add to cart, View cart, free enroll.', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::RAW_HTML,
							'content_classes' => 'elementor-descriptor',
						]
					);

					$this->add_group_control(
						Group_Control_Typography::get_type(),
						[
							'name'     => 'tlms_cg_footer_cart_typography',
							'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
							'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a .cart-text,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.added_to_cart',
						]
					);

					$this->start_controls_tabs('tlms_cg_footer_cart_tabs');

					$this->start_controls_tab(
						'tlms_cg_footer_cart_normal',
						[
							'label' => __('Normal', 'bdthemes-element-pack'),
						]
					);

					$this->add_control(
						'tlms_cg_footer_cart_color',
						[
							'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a .cart-text,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.added_to_cart' => 'color: {{VALUE}};',
							],
						]
					);

					$this->add_control(
						'tlms_cg_footer_cart_icon_color',
						[
							'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:not(.add_to_cart_button):not(.added_to_cart)::before' => 'color: {{VALUE}};',
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a .tutor-icon-cart-line::before,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button .tutor-icon-cart-line::before' => 'color: {{VALUE}};',
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a .tutor-icon-cart-line,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button .tutor-icon-cart-line' => 'color: {{VALUE}};',
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a [class*="tutor-icon"],{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button [class*="tutor-icon"]' => 'color: {{VALUE}};',
							],
						]
					);

					$this->add_responsive_control(
						'tlms_cg_footer_cart_icon_size',
						[
							'label'      => esc_html__('Icon Size', 'bdthemes-element-pack'),
							'type'       => Controls_Manager::SLIDER,
							'size_units' => ['px', 'em'],
							'range'      => [
								'px' => [
									'min' => 8,
									'max' => 48,
								],
								'em' => [
									'min' => 0.5,
									'max' => 3,
								],
							],
							'selectors'  => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:not(.add_to_cart_button):not(.added_to_cart)::before' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a .tutor-icon-cart-line,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button .tutor-icon-cart-line' => 'font-size: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'tlms_cg_footer_cart_icon_gap',
						[
							'label'      => esc_html__('Space After Icon', 'bdthemes-element-pack'),
							'type'       => Controls_Manager::SLIDER,
							'size_units' => ['px', 'em'],
							'range'      => [
								'px' => [
									'min' => 0,
									'max' => 40,
								],
							],
							'selectors'  => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:not(.add_to_cart_button):not(.added_to_cart)::before' => 'margin-inline-end: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a .tutor-icon-cart-line,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button .tutor-icon-cart-line' => 'margin-inline-end: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'tlms_cg_footer_cart_background',
						[
							'label'     => __('Background', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.added_to_cart' => 'background: {{VALUE}};',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						[
							'name'     => 'tlms_cg_footer_cart_border',
							'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.added_to_cart',
						]
					);

					$this->add_responsive_control(
						'tlms_cg_footer_cart_radius',
						[
							'label'      => __('Radius', 'bdthemes-element-pack'),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => ['px', '%'],
							'selectors'  => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.added_to_cart' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_control(
						'tlms_cg_footer_cart_padding',
						[
							'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => ['px', 'em', '%'],
							'selectors'  => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.added_to_cart' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						[
							'name'     => 'tlms_cg_footer_cart_box_shadow',
							'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.added_to_cart',
						]
					);

					$this->end_controls_tab();

					$this->start_controls_tab(
						'tlms_cg_footer_cart_hover',
						[
							'label' => __('Hover', 'bdthemes-element-pack'),
						]
					);

					$this->add_control(
						'tlms_cg_footer_cart_hover_color',
						[
							'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:hover,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:hover .cart-text,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button:hover,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.added_to_cart:hover' => 'color: {{VALUE}};',
							],
						]
					);

					$this->add_control(
						'tlms_cg_footer_cart_icon_hover_color',
						[
							'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:not(.add_to_cart_button):not(.added_to_cart):hover::before' => 'color: {{VALUE}};',
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:hover .tutor-icon-cart-line::before,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button:hover .tutor-icon-cart-line::before' => 'color: {{VALUE}};',
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:hover .tutor-icon-cart-line,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button:hover .tutor-icon-cart-line' => 'color: {{VALUE}};',
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:hover [class*="tutor-icon"],{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button:hover [class*="tutor-icon"]' => 'color: {{VALUE}};',
							],
						]
					);

					$this->add_control(
						'tlms_cg_footer_cart_hover_background',
						[
							'label'     => __('Background', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:hover,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button:hover,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.added_to_cart:hover' => 'background: {{VALUE}};',
							],
						]
					);

					$this->add_control(
						'tlms_cg_footer_cart_hover_border_color',
						[
							'label'     => __('Border Color', 'bdthemes-element-pack'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price .tutor-loop-cart-btn-wrap a:hover,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.add_to_cart_button:hover,{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price a.added_to_cart:hover' => 'border-color: {{VALUE}};',
							],
							'condition' => [
								'tlms_cg_footer_cart_border_border!' => '',
							],
						]
					);

					$this->end_controls_tab();

					$this->end_controls_tabs();

					$this->end_controls_section();
		}
	}

	protected function register_tutor_lms_style_pagination_controls() {
		$this->start_controls_section(
					'section_style_pagination',
					[
						'label'     => esc_html__('Pagination', 'bdthemes-element-pack'),
						'tab'       => Controls_Manager::TAB_STYLE,
						'condition' => [
							'show_pagination' => 'yes',
						],
					]
				);

				$this->start_controls_tabs('tabs_pagination_style');

				$this->start_controls_tab(
					'tab_pagination_normal',
					[
						'label' => esc_html__('Normal', 'bdthemes-element-pack'),
					]
				);

				$this->add_control(
					'pagination_color',
					[
						'label'     => esc_html__('Color', 'bdthemes-element-pack'),
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
						'label'    => esc_html__('Border', 'bdthemes-element-pack'),
						'selector' => '{{WRAPPER}} ul.bdt-pagination li a',
					]
				);

				$this->add_responsive_control(
					'pagination_offset',
					[
						'label'     => esc_html__('Offset', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::SLIDER,
						'selectors' => [
							'{{WRAPPER}} .bdt-pagination' => 'margin-top: {{SIZE}}px;',
						],
					]
				);

				$this->add_responsive_control(
					'pagination_space',
					[
						'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
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
						'label'     => esc_html__('Padding', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::DIMENSIONS,
						'selectors' => [
							'{{WRAPPER}} ul.bdt-pagination li a' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
						],
					]
				);

				$this->add_responsive_control(
					'pagination_radius',
					[
						'label'     => esc_html__('Radius', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::DIMENSIONS,
						'selectors' => [
							'{{WRAPPER}} ul.bdt-pagination li a' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
						],
					]
				);

				$this->add_responsive_control(
					'pagination_arrow_size',
					[
						'label'     => esc_html__('Arrow Size', 'bdthemes-element-pack'),
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
						'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
						//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
						'selector' => '{{WRAPPER}} ul.bdt-pagination li a, {{WRAPPER}} ul.bdt-pagination li span',
					]
				);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_pagination_hover',
					[
						'label' => esc_html__('Hover', 'bdthemes-element-pack'),
					]
				);

				$this->add_control(
					'pagination_hover_color',
					[
						'label'     => esc_html__('Color', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} ul.bdt-pagination li a:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'pagination_hover_border_color',
					[
						'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
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
						'label' => esc_html__('Active', 'bdthemes-element-pack'),
					]
				);

				$this->add_control(
					'pagination_active_color',
					[
						'label'     => esc_html__('Color', 'bdthemes-element-pack'),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} ul.bdt-pagination li.bdt-active a' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'pagination_active_border_color',
					[
						'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
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
	}

	public function render_tutor_lms_thumbnail() {

		$settings = $this->get_settings_for_display();

		$course_id = get_the_ID();

		$settings['thumbnail_size'] = [
			'id' => get_post_thumbnail_id(),
		];

		$thumbnail_html      = Group_Control_Image_Size::get_attachment_image_html($settings, 'thumbnail_size');
		$placeholder_img_src = Utils::get_placeholder_image_src();
		$img_url             = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');

		if (!$thumbnail_html) {
			$thumbnail_html = '<img src="' . esc_url($placeholder_img_src) . '" alt="' . esc_html(get_the_title()) . '">';
		}

		?>

		<div class="bdt-tutor-course-header">
			<a href="<?php the_permalink(); ?>"> <?php echo wp_kses_post($thumbnail_html); ?> </a>
			<div class="bdt-tutor-course-loop-header-meta">
				<?php
				$is_wishlisted = tutor_utils()->is_wishlisted($course_id);
				$wishlist_icon_class = $is_wishlisted ? 'tutor-icon-bookmark-bold' : 'tutor-icon-bookmark-line';

				if ('yes' == $settings['show_meta_label']) {
					echo '<span class="bdt-tutor-course-loop-level">' . esc_html(get_tutor_course_level()) . '</span>';
				}

				if ('yes' == $settings['show_meta_wishlist']) {
					echo '<span class="bdt-tutor-course-wishlist"><a href="javascript:void(0);" class="tutor-course-wishlist-btn" data-course-id="' . esc_attr($course_id) . '"><i class="' . esc_attr($wishlist_icon_class) . '" aria-hidden="true"></i></a> </span>';
				}

				?>
			</div>
		</div>


	<?php
	
	}

	public function render_tutor_lms_title() {

		$settings = $this->get_settings_for_display();

		if (!$settings['show_title']) {
			return;
		}

	?>
		<div class="bdt-tutor-course-loop-title">
			<h2>
				<a href="<?php the_permalink(); ?>">
					<?php the_title() ?>
				</a>
			</h2>
		</div>

	<?php
	
	}

	public function render_tutor_lms_meta() {

		$settings = $this->get_settings_for_display();

		global $post, $authordata;
		$profile_url = tutor_utils()->profile_url($authordata->ID);
	?>

		<?php if ('yes' == $settings['show_user_clock']) : ?>
			<div class="bdt-tutor-course-loop-meta">
				<?php
				$course_duration = get_tutor_course_duration_context();
				$course_students = tutor_utils()->count_enrolled_users_by_course();
				?>
				<div class="bdt-tutor-single-loop-meta">
					<i class='tutor-icon-user'></i><span><?php echo wp_kses_post($course_students); ?></span>
				</div>
				<?php
				if (!empty($course_duration)) { ?>
					<div class="bdt-tutor-single-loop-meta">
						<i class='tutor-icon-clock'></i> <span><?php echo wp_kses_post($course_duration); ?></span>
					</div>
				<?php } ?>
			</div>
		<?php endif; ?>

		<div class="bdt-tutor-loop-author">

			<?php if ('yes' == $settings['show_author_avatar']) : ?>
				<div class="bdt-tutor-single-course-avatar">
					<a href="<?php echo esc_url($profile_url); ?>"> <?php echo wp_kses_post(tutor_utils()->get_tutor_avatar($post->post_author)); ?></a>
				</div>
			<?php endif; ?>

			<?php if ('yes' == $settings['show_author_name']) : ?>
				<div class="bdt-tutor-single-course-author-name">
					<span><?php esc_html_e('by', 'bdthemes-element-pack'); ?></span>
					<a href="<?php echo esc_url($profile_url); ?>"><?php echo get_the_author(); ?></a>
				</div>
			<?php endif; ?>

			<?php if ('yes' == $settings['show_category']) : ?>
				<div class="bdt-tutor-course-lising-category">
					<?php
					$course_categories = get_tutor_course_categories();
					if (!empty($course_categories) && is_array($course_categories) && count($course_categories)) {
					?>
						<span><?php esc_html_e('In', 'bdthemes-element-pack') ?></span>
					<?php
						foreach ($course_categories as $course_category) {
							$category_name = $course_category->name;
							$category_link = get_term_link($course_category->term_id);
							echo "<a href='" . esc_url($category_link) . "'>" . esc_html($category_name) . "</a>";
						}
					}
					?>
				</div>
			<?php endif; ?>

		</div>

	<?php
	
	}

	public function render_tutor_lms_rating() {

		$settings = $this->get_settings_for_display();

		if (!$settings['show_rating']) {
			return;
		}

	?>

		<div class="bdt-tutor-loop-rating-wrap">
			<?php $course_rating = tutor_utils()->get_course_rating(); ?>
			<div class="tutor-ratings-stars">
				<?php tutor_utils()->star_rating_generator_course( $course_rating->rating_avg ); ?>
			</div>
			<span class="bdt-tutor-rating-count">
				<?php
				if ($course_rating->rating_avg > 0) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo apply_filters('tutor_course_rating_average', $course_rating->rating_avg);
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '<i>(' . apply_filters('tutor_course_rating_count', $course_rating->rating_count) . ')</i>';
				}
				?>
			</span>
		</div>

	<?php
	
	}

	public function render_tutor_lms_price() {

		$settings = $this->get_settings_for_display();

		if (!$settings['show_cart_btn_price']) {
			return;
		}

	?>
		<div class="bdt-tutor-loop-course-footer">
			<div class="bdt-tutor-course-loop-price">
				<?php
				$course_id  = get_the_ID();
				$free_label = ! empty( $settings['free_course_label'] ) ? $settings['free_course_label'] : esc_html__( 'Free', 'bdthemes-element-pack' );
				$free_enroll = ! empty( $settings['free_enroll_button_text'] ) ? $settings['free_enroll_button_text'] : esc_html__( 'Get Enrolled', 'bdthemes-element-pack' );
				$use_ajax    = ! empty( $settings['ajax_add_to_cart'] ) && 'yes' === $settings['ajax_add_to_cart'];

				$enroll_btn = '<div class="tutor-loop-cart-btn-wrap"><a href="' . esc_url( get_the_permalink() ) . '">' . esc_html( $free_enroll ) . '</a></div>';

				$monetization = tutor_utils()->get_option( 'monetize_by' );

				// Must pass $course_id — bare is_course_purchasable() can read the wrong post in custom queries.
				$show_monetized = tutor_utils()->is_course_purchasable( $course_id );

				// Tutor marks subscription / some WC setups as non-purchasable; WooCommerce filter only allows price_type "paid" + product.
				if ( ! $show_monetized && 'wc' === $monetization && tutor_utils()->has_wc() && function_exists( 'wc_get_product' ) ) {
					$product_id = tutor_utils()->get_course_product_id( $course_id );
					$product    = $product_id ? wc_get_product( $product_id ) : false;
					if ( $product && $product->exists() ) {
						$ptype = tutor_utils()->price_type( $course_id );
						if ( class_exists( '\TUTOR\Course' ) ) {
							if ( \TUTOR\Course::PRICE_TYPE_SUBSCRIPTION === $ptype || \TUTOR\Course::PRICE_TYPE_PAID === $ptype ) {
								$show_monetized = true;
							} elseif ( '' !== $ptype && \TUTOR\Course::PRICE_TYPE_FREE !== $ptype ) {
								$show_monetized = true;
							}
						}
						if ( ! $show_monetized && ( ! class_exists( '\TUTOR\Course' ) || \TUTOR\Course::PRICE_TYPE_FREE !== $ptype ) ) {
							$raw_price = $product->get_price();
							if ( '' !== $raw_price && is_numeric( $raw_price ) && (float) $raw_price > 0 ) {
								$show_monetized = true;
							}
						}
						if ( function_exists( 'tutor' ) && ! empty( tutor()->bundle_post_type ) && tutor()->bundle_post_type === get_post_type( $course_id ) ) {
							$show_monetized = true;
						}
					}
				}

				if ( ! $show_monetized && tutor_utils()->is_monetize_by_tutor() ) {
					$ptype = tutor_utils()->price_type( $course_id );
					if ( class_exists( '\TUTOR\Course' ) ) {
						if ( \TUTOR\Course::PRICE_TYPE_PAID === $ptype || \TUTOR\Course::PRICE_TYPE_SUBSCRIPTION === $ptype ) {
							$show_monetized = true;
						} elseif ( '' !== $ptype && \TUTOR\Course::PRICE_TYPE_FREE !== $ptype ) {
							$show_monetized = true;
						}
					}
				}

				if ( $show_monetized ) {
					if ( ! $use_ajax ) {
						$wc_loop_no_ajax_cart = static function ( $args, $product ) {
							if ( ! empty( $args['class'] ) && is_string( $args['class'] ) ) {
								$args['class'] = trim( preg_replace( '/\sajax_add_to_cart\b/', ' ', $args['class'] ) );
								$args['class'] = preg_replace( '/\s{2,}/', ' ', $args['class'] );
							}
							return $args;
						};
						add_filter( 'woocommerce_loop_add_to_cart_args', $wc_loop_no_ajax_cart, 99, 2 );

						$enroll_btn = tutor_course_loop_add_to_cart( false );

						remove_filter( 'woocommerce_loop_add_to_cart_args', $wc_loop_no_ajax_cart, 99 );
					} else {
						$enroll_btn = tutor_course_loop_add_to_cart( false );
					}

					$enroll_btn = trim( (string) $enroll_btn );
					if ( '' !== $enroll_btn && false === strpos( $enroll_btn, 'tutor-loop-cart-btn-wrap' ) ) {
						$enroll_btn = '<div class="tutor-loop-cart-btn-wrap">' . $enroll_btn . '</div>';
					}

					$display_price = '';

					if ( 'wc' === $monetization && tutor_utils()->has_wc() && function_exists( 'wc_get_product' ) ) {
						$product_id = tutor_utils()->get_course_product_id( $course_id );
						$product    = wc_get_product( $product_id );

						if ( $product ) {
							$display_price = apply_filters( 'tutor_loop_wc_price_html', $product->get_price_html(), $product );
						}
					}

					if ( ! $display_price ) {
						$display_price = tutor_utils()->get_course_price( $course_id );
					}

					if ( ! $display_price && 'wc' === $monetization && tutor_utils()->has_wc() && function_exists( 'wc_get_product' ) ) {
						$fallback_id = tutor_utils()->get_course_product_id( $course_id );
						$fallback_pd = $fallback_id ? wc_get_product( $fallback_id ) : false;
						if ( $fallback_pd ) {
							$display_price = $fallback_pd->get_price_html();
						}
					}

					if ( ! $display_price && tutor_utils()->is_monetize_by_tutor() && function_exists( 'tutor_get_course_formatted_price_html' ) ) {
						$display_price = tutor_get_course_formatted_price_html( $course_id, false );
					}

					echo '<div class="price"> ';
					if ( $display_price ) {
						echo wp_kses_post( $display_price );
					} else {
						echo '<span class="bdt-tutor-price-free">' . esc_html( $free_label ) . '</span>';
					}
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Tutor/WooCommerce loop cart markup (must keep data-* for WC scripts).
					echo $enroll_btn;
					echo ' </div>';
				} else {
					echo '<div class="price"> <span class="bdt-tutor-price-free">' . esc_html( $free_label ) . '</span>' . $enroll_btn . ' </div>';
				}
				?>
			</div>
		</div>

	<?php
	
	}

	public function render_tutor_lms_desc() {

	?>
		<div class="bdt-tutor-loop-course-container">
			<?php
			$this->render_tutor_lms_rating();
			$this->render_tutor_lms_title();
			$this->render_tutor_lms_meta();
			?>
		</div>

		<?php $this->render_tutor_lms_price(); ?>
	<?php
	
	}

	public function render_tutor_lms_post( $type ) {
		if ( 'grid' === $type ) {

		$settings = $this->get_settings_for_display();
		global $post;

		$element_key = 'course-item-' . $post->ID;

		if ($settings['tilt_show']) {
			$this->add_render_attribute('tutor-course-item', 'data-tilt', '', true);
			if ($settings['tilt_scale']) {
				$this->add_render_attribute('tutor-course-item', 'data-tilt-scale', '1.2', true);
			}
		}

		$this->add_render_attribute('tutor-course-item', 'class', 'bdt-tutor-course bdt-tutor-course-item', true);

	?>
		<div <?php $this->print_render_attribute_string($element_key); ?>>
			<div <?php $this->print_render_attribute_string('tutor-course-item'); ?>>
				<?php $this->render_tutor_lms_thumbnail(); ?>
				<?php $this->render_tutor_lms_desc(); ?>
			</div>
		</div>
<?php
	
		} else {

				$settings = $this->get_settings_for_display();

				$this->add_render_attribute('tutor-course-item', 'class', 'bdt-tutor-course bdt-tutor-course-item swiper-slide', true);

				?>
					<div <?php $this->print_render_attribute_string('tutor-course-item'); ?>>
						<?php $this->render_tutor_lms_thumbnail(); ?>
						<?php $this->render_tutor_lms_desc(); ?>
					</div>
			<?php
			
		}
	}

	public function query_tutor_lms_posts( $posts_per_page ) {
		$settings       = $this->get_settings();
		$posts_per_page = isset( $posts_per_page ) ? (int) $posts_per_page : 0;
		$is_carousel    = false !== strpos( $this->get_name(), 'carousel' );

		if ( $is_carousel ) {
			$args = [];
			if ( $posts_per_page ) {
				$args['posts_per_page'] = $posts_per_page;
				$args['paged']          = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
			}

			$default      = $this->getGroupControlQueryArgs();
			$args         = array_merge( $default, $args );
			$this->_query = new \WP_Query( $args );
			return;
		}

		$args             = $this->getGroupControlQueryArgs();
		$is_current_query = ( ! empty( $settings['posts_source'] ) && $settings['posts_source'] === 'current_query' );

		if ( $is_current_query ) {
			unset( $args['offset'] );
			unset( $args['no_found_rows'] );
			$posts_per_page = 0;
		}

		if ( $posts_per_page > 0 ) {
			$args['posts_per_page'] = $posts_per_page;
		} else {
			$args['posts_per_page'] = (int) get_option( 'posts_per_page', 10 );
		}

		if ( ! empty( $settings['show_pagination'] ) ) {
			$args['paged'] = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
		}

		$this->_query = new \WP_Query( $args );
	}

	public function render_tutor_lms_grid( $settings ) {
		$this->query_tutor_lms_posts( $settings['posts_per_page'] );
		$wp_query = $this->get_query();

		if ( ! $wp_query->found_posts ) {
			return;
		}

		$this->render_header();

		while ( $wp_query->have_posts() ) {
			$wp_query->the_post();
			$this->render_tutor_lms_post( 'grid' );
		}

		$this->render_footer();

		if ( $settings['show_pagination'] ) { ?>
			<div class="ep-pagination">
				<?php element_pack_post_pagination( $wp_query ); ?>
			</div>
		<?php
		}

		wp_reset_postdata();
	}

	public function render_tutor_lms_carousel( $settings ) {
		$this->query_tutor_lms_posts( $settings['posts_per_page'] );
		$wp_query = $this->get_query();

		if ( ! $wp_query->found_posts ) {
			return;
		}

		$this->render_header();

		while ( $wp_query->have_posts() ) {
			$wp_query->the_post();
			$this->render_tutor_lms_post( 'carousel' );
		}

		$this->render_footer();

		wp_reset_postdata();
	}


	/**
	 * Build EDD category widget CSS wrapper selector.
	 *
	 * @param string $type 'grid' or 'carousel'.
	 */
	protected function get_edd_category_wrapper_selector( $type ) {
		return 'grid' === $type
			? '{{WRAPPER}} .ep-edd-category-grid'
			: '{{WRAPPER}} .bdt-edd-category-carousel';
	}

	/**
	 * Build EDD category image element class (without wrapper).
	 *
	 * @param string $type 'grid' or 'carousel'.
	 */
	protected function get_edd_category_image_class( $type ) {
		return 'grid' === $type ? 'ep-edd-category-grid-image' : 'bdt-edd-category-carousel-image';
	}

	/**
	 * Build EDD product widget CSS wrapper selector.
	 *
	 * @param string $type 'grid' or 'carousel'.
	 */
	protected function get_edd_product_wrapper_selector( $type ) {
		return 'grid' === $type
			? '{{WRAPPER}} .ep-edd-product'
			: '{{WRAPPER}} .bdt-edd-product-carousel';
	}

	/**
	 * Register EDD category layout controls.
	 *
	 * @param string $type 'grid' or 'carousel'.
	 */
	protected function register_edd_category_layout_controls( $type ) {
		$w = $this->get_edd_category_wrapper_selector( $type );
		$img_class = $this->get_edd_category_image_class( $type );
		$img = $w . ' .' . $img_class;

		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'skin_layout',
			[
				'label'      => __('Skin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'style-1',
				'options'    => [
					'style-1'  => __('Style 1', 'bdthemes-element-pack'),
					'style-2'  => __('Style 2', 'bdthemes-element-pack'),
					'style-3'  => __('Style 3', 'bdthemes-element-pack'),
					'style-4'  => __('Style 4', 'bdthemes-element-pack'),
					'style-5'  => __('Style 5', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => __('Columns', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SELECT,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'options'        => [
					1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6',
				],
				'selectors' => 'grid' === $type ? [
					$w => 'grid-template-columns:repeat({{VALUE}}, 1fr)'
				] : [],
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'   => __('Item Gap', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => ['size' => 20],
				'selectors' => [$w => 'gap: {{SIZE}}{{UNIT}};'],
			]
		);

		$this->add_responsive_control(
			'item_height',
			[
				'label'   => esc_html__('Item Height(px)', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => ['px' => ['min' => 0, 'max' => 1000]],
				'selectors' => [
					$w . ' .edd-item' => 'height: {{SIZE}}{{UNIT}};',
					$img => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => ['skin_layout!' => 'style-3']
			]
		);

		$this->add_responsive_control(
			'item_height_skin_3',
			[
				'label'   => esc_html__('Item Height(px)', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => ['px' => ['min' => 0, 'max' => 1000]],
				'selectors' => [$img => 'height: {{SIZE}}{{UNIT}};'],
				'condition' => ['skin_layout' => 'style-3']
			]
		);

		$this->add_control(
			'is_use_image',
			[
				'label'     => esc_html__('Use Static Image', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'category_image',
			[
				'label'     => __('Select Image', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => ['active' => true],
				'default'   => ['url' => Utils::get_placeholder_image_src()],
				'condition' => ['is_use_image' => 'yes']
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'    => 'category_thumbnail',
				'exclude' => ['custom'],
				'default' => 'medium',
				'condition' => ['is_use_image' => 'yes']
			]
		);

		$this->add_control(
			'show_count',
			[
				'label'     => esc_html__('Show Count', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register EDD category style controls (item, content, category, count).
	 *
	 * @param string $type 'grid' or 'carousel'.
	 */
	protected function register_edd_category_style_controls( $type ) {
		$w = $this->get_edd_category_wrapper_selector( $type );
		$img_class = $this->get_edd_category_image_class( $type );
		$img = $w . ' .' . $img_class;

		$this->start_controls_section(
						'section_style_item',
						[
								'label' => esc_html__('Item', 'bdthemes-element-pack'),
								'tab'   => Controls_Manager::TAB_STYLE,
						]
				);
				$this->start_controls_tabs(
						'item_tabs'
				);
				$this->start_controls_tab(
						'item_tab_normal',
						[
								'label' => esc_html__('Normal', 'bdthemes-element-pack'),
						]
				);
				$this->add_group_control(
						Group_Control_Background::get_type(),
						[
								'name'      => 'items_background',
								'label'     => esc_html__('Backgrund', 'bdthemes-element-pack'),
								'types'     => ['classic', 'gradient'],
								'selector'  => $w . ' .edd-item',
						]
				);
				$this->add_control(
						'item_overlay',
						[
								'label'     => esc_html__('Overlay Color', 'bdthemes-element-pack'),
								'type'      => Controls_Manager::COLOR,
								'selectors' => [
										$w . ' .edd-item-overlay' => 'background: {{VALUE}}',
								],
								'condition' => [
										'skin_layout!' => ['style-3']
								]
						]
				);
				$this->add_control(
						'item_overlay_blur_effect',
						[
								'label'       => esc_html__('Glassmorphism', 'bdthemes-element-pack'),
								'type'        => Controls_Manager::SWITCHER,
								'description' => sprintf(__('This feature will not work in the Firefox browser untill you enable browser compatibility so please %1s look here %2s', 'bdthemes-element-pack'), '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter#Browser_compatibility" target="_blank">', '</a>'),
								'default'     => 'yes',
								'condition' => [
										'skin_layout' => [
												'style-5',
										]
								]
						]
				);

				$this->add_control(
						'item_overlay_blur_level',
						[
								'label'     => __('Blur Level', 'bdthemes-element-pack'),
								'type'      => Controls_Manager::SLIDER,
								'range'     => [
										'px' => [
												'min'  => 0,
												'step' => 1,
												'max'  => 50,
										]
								],
								'default'   => [
										'size' => 10
								],
								'selectors' => [
										$w . '.style-5 .' . $img_class . ':before' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
								],
								'condition' => [
										'item_overlay_blur_effect' => 'yes',
										'skin_layout' => [
												'style-5'
										]
								]
						]
				);

				$this->add_group_control(
						Group_Control_Background::get_type(),
						[
								'name'     => 'item_background',
								'selector' => $w . '.style-5 .' . $img_class . ':before',
								'condition' => [
										'skin_layout' => [
												'style-5'
										]
								]
						]
				);
				$this->add_responsive_control(
						'item_padding',
						[
								'label'                 => esc_html__('Padding', 'bdthemes-element-pack'),
								'type'                  => Controls_Manager::DIMENSIONS,
								'size_units'            => ['px', '%', 'em'],
								'selectors'             => [
										$w . ' .edd-item'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
								],
						]
				);
				$this->add_responsive_control(
						'item_margin',
						[
								'label'                 => esc_html__('Margin', 'bdthemes-element-pack'),
								'type'                  => Controls_Manager::DIMENSIONS,
								'size_units'            => ['px', '%', 'em'],
								'selectors'             => [
										$w . ' .edd-item'    => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
								],
						]
				);
				$this->add_group_control(
						Group_Control_Border::get_type(),
						[
								'name'      => 'item_border',
								'label'     => esc_html__('Border', 'bdthemes-element-pack'),
								'selector'  => $w . ' .edd-item',
						]
				);
				$this->add_responsive_control(
						'item_radius',
						[
								'label'                 => esc_html__('Radius', 'bdthemes-element-pack'),
								'type'                  => Controls_Manager::DIMENSIONS,
								'size_units'            => ['px', '%', 'em'],
								'selectors'             => [
										$w . ' .edd-item'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
								],
						]
				);
				$this->end_controls_tab();
				$this->start_controls_tab(
						'item_tab_hover',
						[
								'label' => esc_html__('Hover', 'bdthemes-element-pack'),
						]
				);
				$this->add_group_control(
						Group_Control_Background::get_type(),
						[
								'name'      => 'items_hover_background',
								'label'     => esc_html__('Backgrund', 'bdthemes-element-pack'),
								'types'     => ['classic', 'gradient'],
								'selector'  => '{{WRAPPER}} .ep-edd-category-grid .edd-item:hover .ep-edd-category-grid-image',
								'condition' => [
										'skin_layout!' => 'style-5'
								]
						]
				);
				$this->add_control(
						'item_overlay_hover',
						[
								'label'     => esc_html__('Overlay Color', 'bdthemes-element-pack'),
								'type'      => Controls_Manager::COLOR,
								'selectors' => [
										$w . ' .edd-item:hover .edd-item-overlay' => 'background: {{VALUE}}',
								],
								'condition' => [
										'skin_layout!' => ['style-3']
								]
						]
				);
				$this->add_control(
						'item_hover_border_color',
						[
								'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
								'type'      => Controls_Manager::COLOR,
								'selectors' => [
										$w . ' .edd-item:hover' => 'border-color: {{VALUE}}',
								],
						]
				);
				$this->end_controls_tab();
				$this->end_controls_tabs();
				$this->end_controls_section();

				$this->start_controls_section(
						'section_style_content',
						[
								'label' => esc_html__('Content', 'bdthemes-element-pack'),
								'tab'   => Controls_Manager::TAB_STYLE,
								'condition' => [
										'skin_layout' => [
												'style-1',
												'style-3'
										]
								]
						]
				);
				$this->start_controls_tabs(
						'content_tabs'
				);
				$this->start_controls_tab(
						'content_tab_normal',
						[
								'label' => esc_html__('Normal', 'bdthemes-element-pack'),
						]
				);

				$this->add_group_control(
						Group_Control_Background::get_type(),
						[
								'name'     => 'content_background',
								'selector' => $w . ' .edd-item .edd-content',
								'condition' => [
										'skin_layout' => ['style-1', 'style-3']
								]
						]
				);

				$this->add_responsive_control(
						'content_padding',
						[
								'label'                 => esc_html__('Padding', 'bdthemes-element-pack'),
								'type'                  => Controls_Manager::DIMENSIONS,
								'size_units'            => ['px', '%', 'em'],
								'selectors'             => [
										$w . ' .edd-item .edd-content'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
								],
						]
				);
				$this->add_responsive_control(
						'content_margin',
						[
								'label'                 => esc_html__('Margin', 'bdthemes-element-pack'),
								'type'                  => Controls_Manager::DIMENSIONS,
								'size_units'            => ['px', '%', 'em'],
								'selectors'             => [
										$w . ' .edd-item .edd-content'    => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
								],
						]
				);
				$this->add_group_control(
						Group_Control_Border::get_type(),
						[
								'name'      => 'content_border',
								'label'     => esc_html__('Border', 'bdthemes-element-pack'),
								'selector'  => $w . ' .edd-item .edd-content',
						]
				);
				$this->add_responsive_control(
						'content_radius',
						[
								'label'                 => esc_html__('Radius', 'bdthemes-element-pack'),
								'type'                  => Controls_Manager::DIMENSIONS,
								'size_units'            => ['px', '%', 'em'],
								'selectors'             => [
										$w . ' .edd-item .edd-content'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
								],
						]
				);

				$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						[
								'name'     => 'content_shadow',
								'selector' => $w . ' .edd-item .edd-content',
						]
				);
				$this->end_controls_tab();
				$this->start_controls_tab(
						'content_tab_hover',
						[
								'label' => esc_html__('Hover', 'bdthemes-element-pack'),
						]
				);
				$this->add_control(
						'content_hover_border_color',
						[
								'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
								'type'      => Controls_Manager::COLOR,
								'selectors' => [
										$w . ' .edd-item:hover .edd-content' => 'border-color: {{VALUE}}',
								],
						]
				);
				$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						[
								'name'     => 'content_hover_shadow',
								'selector' => $w . ' .edd-item:hover .edd-content',
						]
				);
				$this->end_controls_tab();
				$this->end_controls_tabs();
				$this->end_controls_section();
				$this->start_controls_section(
						'section_style_category',
						[
								'label' => esc_html__('Category', 'bdthemes-element-pack'),
								'tab'   => Controls_Manager::TAB_STYLE,
						]
				);
				$this->start_controls_tabs(
						'category_tabs'
				);
				$this->start_controls_tab(
						'category_tab_normal',
						[
								'label' => esc_html__('Normal', 'bdthemes-element-pack'),
						]
				);
				$this->add_control(
						'category_color',
						[
								'label'     => esc_html__('Color', 'bdthemes-element-pack'),
								'type'      => Controls_Manager::COLOR,
								'selectors' => [
										$w . ' .edd-item .edd-content .title' => 'color: {{VALUE}}',
								],
						]
				);
				$this->add_group_control(
						Group_Control_Background::get_type(),
						[
								'name'      => 'style_5_category_bg',
								'label'     => esc_html__('Backgorund', 'bdthemes-element-pack'),
								'types'     => ['classic', 'gradient'],
								'selector'  => $w . ' .edd-item .edd-content .title',
								'condition' => [
										'skin_layout' => [
												'style-5'
										]
								]
						]
				);
				$this->add_group_control(
						Group_Control_Background::get_type(),
						[
								'name'      => 'style_6_category_bg',
								'label'     => esc_html__('Background', 'bdthemes-element-pack'),
								'types'     => ['classic', 'gradient'],
								'selector'  => $w . '.style-5 .' . $img_class . ':before',
								'condition' => [
										'skin_layout' => [
												'style-6'
										]
								]
						]
				);
				$this->add_responsive_control(
						'category_margin',
						[
								'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
								'type'       => Controls_Manager::DIMENSIONS,
								'size_units' => ['px', '%'],
								'selectors'  => [
										$w . ' .edd-item .edd-content .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
								],
						]
				);
				$this->add_responsive_control(
						'category_padding',
						[
								'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
								'type'       => Controls_Manager::DIMENSIONS,
								'size_units' => ['px', '%'],
								'selectors'  => [
										$w . ' .edd-item .edd-content .title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
								],
								'condition' => [
										'skin_layout' => [
												'style-4'
										]
								]
						]
				);

				$this->add_group_control(
						Group_Control_Typography::get_type(),
						[
								'name'     => 'category_typography',
								'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
								'exclude' => ['line_height'],
								'selector' => $w . ' .edd-item .edd-content .title',
						]
				);
				$this->end_controls_tab();
				$this->start_controls_tab(
						'category_tab_hover',
						[
								'label' => esc_html__('Hover', 'bdthemes-element-pack'),
								'condition' => [
										'skin_layout!' => 'style-5'
								]
						]
				);
				$this->add_control(
						'hover_category_color',
						[
								'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
								'type'      => Controls_Manager::COLOR,
								'selectors' => [
										$w . ' .edd-item:hover .edd-content .title' => 'color: {{VALUE}};',
								],
						]
				);
				$this->end_controls_tab();
				$this->end_controls_tabs();
				$this->end_controls_section();
				$this->start_controls_section(
						'section_style_count',
						[
								'label' => esc_html__('Count', 'bdthemes-element-pack'),
								'tab'   => Controls_Manager::TAB_STYLE,
						]
				);
				$this->start_controls_tabs(
						'count_tabs'
				);
				$this->start_controls_tab(
						'count_tab_normal',
						[
								'label' => esc_html__('Normal', 'bdthemes-element-pack'),
								'condition' => [
										'skin_layout!' => [
												'style-5'
										]
								]
						]
				);
				$this->add_control(
						'count_color',
						[
								'label'     => esc_html__('Color', 'bdthemes-element-pack'),
								'type'      => Controls_Manager::COLOR,
								'selectors' => [
										$w . ' .edd-item .edd-content .edd-category-count > *' => 'color: {{VALUE}};',
								],
						]
				);
				$this->add_group_control(
						Group_Control_Background::get_type(),
						[
								'name'      => 'count_background',
								'label'     => esc_html__('Background', 'bdthemes-element-pack'),
								'types'     => ['classic', 'gradient'],
								'selector'  => $w . ' .edd-item .edd-content .edd-category-count > *',
								'condition' => [
										'skin_layout' => [
												'style-2'
										]
								]
						]
				);
				$this->add_responsive_control(
						'count_number_size',
						[
								'label'         => esc_html__('Size', 'bdthemes-element-pack'),
								'type'          => Controls_Manager::SLIDER,
								'size_units'    => ['px'],
								'default'       => [
										'unit'      => 'px',
										'size'      => 30,
								],
								'selectors' => [
										$w . ' .edd-item .edd-content .edd-category-count > *' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
								],
								'condition' => [
										'skin_layout' => [
												'style-2'
										]
								]
						]
				);

				$this->add_group_control(
						Group_Control_Typography::get_type(),
						[
								'name'     => 'count_typography',
								'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
								'selector' => $w . ' .edd-item .edd-content .edd-category-count > *',
						]
				);
				$this->end_controls_tab();
				$this->start_controls_tab(
						'count_tab_hover',
						[
								'label' => esc_html__('Hover', 'bdthemes-element-pack'),
						]
				);
				$this->add_control(
						'count_color_hover',
						[
								'label'     => esc_html__('Color', 'bdthemes-element-pack'),
								'type'      => Controls_Manager::COLOR,
								'selectors' => [
										$w . ' .edd-item:hover .edd-content .edd-category-count > *' => 'color: {{VALUE}};',
								],
						]
				);
				$this->add_group_control(
						Group_Control_Background::get_type(),
						[
								'name'      => 'count_hover_background',
								'label'     => esc_html__('Background', 'bdthemes-element-pack'),
								'types'     => ['classic', 'gradient'],
								'selector'  => $w . ' .edd-item:hover .edd-content .edd-category-count > *',
								'condition' => [
										'skin_layout' => [
												'style-2'
										]
								]
						]
				);
				$this->end_controls_tab();
				$this->end_controls_tabs();
				$this->end_controls_section();
	}

	/**
	 * Register EDD category carousel navigation controls.
	 */
	protected function register_edd_category_carousel_navigation_controls() {
		$this->start_controls_section(
						'section_content_navigation',
						[
								'label' => __('Navigation', 'bdthemes-element-pack'),
						]
				);

				//Global Navigation Controls
				$this->register_navigation_controls();

				$this->end_controls_section();

				//Global Carousel Settings Controls
				$this->register_carousel_settings_controls();
	}

	/**
	 * Register EDD category carousel navigation style controls.
	 */
	protected function register_edd_category_carousel_navigation_style_controls() {
		$this->start_controls_section(
						'section_style_navigation',
						[
								'label'      => __('Navigation', 'bdthemes-element-pack'),
								'tab'        => Controls_Manager::TAB_STYLE,
								'conditions' => [
										'relation' => 'or',
										'terms'    => [
												[
														'name'     => 'navigation',
														'operator' => '!=',
														'value'    => 'none',
												],
												[
														'name'  => 'show_scrollbar',
														'value' => 'yes',
												],
										],
								],
						]
				);

				//Global Navigation Style Controls
				$this->register_navigation_style_controls('swiper-carousel');

				$this->end_controls_section();
	}

	/**
	 * Query EDD download categories.
	 */
	protected function render_edd_category_query() {
		$settings = $this->get_settings_for_display();
				$args = [
						'orderby'    => isset($settings['orderby']) ? $settings['orderby'] : 'name',
						'order'      => isset($settings['order']) ? $settings['order'] : 'ASC',
						'hide_empty' => isset($settings['hide_empty']) && ($settings['hide_empty'] == 'yes') ? 1 : 0,
				];


				switch ($settings['display_category']) {
						case 'all':
								if (isset($settings['cats_include_by_id']) && !empty($settings['cats_include_by_id'])) {
										$args['include'] = $settings['cats_include_by_id'];
								}
								if (isset($settings['cats_exclude_by_id']) && !empty($settings['cats_exclude_by_id'])) {
										$args['exclude'] = $settings['cats_exclude_by_id'];
								}
								break;
						case 'child':
								if ($settings['parent_cats'] != 'none' &&  !empty($settings['parent_cats'])) {
										$args['child_of'] = $settings['parent_cats'];
								}
								break;
						case 'parents':
								$args['parent'] = 0;
								break;
				}
				$categories = get_terms('download_category', $args);
				return $categories;
	}

	/**
	 * Render EDD category image.
	 *
	 * @param string $type 'grid' or 'carousel'.
	 */
	protected function render_edd_category_image( $type ) {
		$settings = $this->get_settings_for_display();
		$image_src = Utils::get_placeholder_image_src();
		$img_class = $this->get_edd_category_image_class( $type );
		?>
		<div class="<?php echo esc_attr( $img_class ); ?>">
			<?php if ( $settings['is_use_image'] ) :
				$thumb_url = Group_Control_Image_Size::get_attachment_image_src( $settings['category_image']['id'], 'category_thumbnail', $settings );
				if ( ! empty( $thumb_url ) ) {
					$image_src = $settings['category_image']['url'];
				}
			?>
				<img src="<?php echo esc_url( $image_src ); ?>" alt="">
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render EDD category loop items.
	 *
	 * @param string $type 'grid' or 'carousel'.
	 */
	protected function render_edd_category_loop_item( $type ) {
		$settings   = $this->get_settings_for_display();
		$categories = $this->render_edd_category_query();
		$is_carousel = ( 'carousel' === $type );
		$item_class  = $is_carousel ? ['edd-item swiper-slide', 'category-link'] : ['edd-item', 'category-link'];

		if ( ! empty( $categories ) ) {
			foreach ( $categories as $index => $category ) :
				$this->add_render_attribute( 'edd-category-item', 'class', $item_class, true );
				$this->add_render_attribute( 'edd-category-item', 'href', get_term_link( $category->term_id, 'download_category' ), true );
				?>
				<a <?php $this->print_render_attribute_string( 'edd-category-item' ); ?>>
					<?php $this->render_edd_category_image( $type ); ?>
					<div class="edd-content">
						<?php printf( '<h3 class="title">%s</h3>', esc_html( $category->name ) ); ?>
						<?php if ( $settings['show_count'] ) :
							$count = $is_carousel ? esc_html( $category->count ) : esc_attr( $category->count );
							printf( '<p class="edd-category-count"><span class="edd-count-number">%s</span><span class="edd-count-text">products</span></p>', $count );
						endif; ?>
					</div>
					<div class="edd-item-overlay"></div>
				</a>
				<?php
			endforeach;
		} else {
			printf( '<span class="bdt-warning">%s</span>', esc_html__( 'Opps, Nothing found to display', 'bdthemes-element-pack' ) );
		}
	}

	/**
	 * Render EDD category grid.
	 */
	protected function render_edd_category_grid() {
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute( 'ep-edd-category-grid', 'class', [ 'ep-edd-category-grid', $settings['skin_layout'] ] );
		?>
		<div <?php $this->print_render_attribute_string( 'ep-edd-category-grid' ); ?>>
			<?php $this->render_edd_category_loop_item( 'grid' ); ?>
		</div>
		<?php
	}

	/**
	 * Render EDD category carousel header.
	 */
	protected function render_edd_category_carousel_header() {
		$settings = $this->get_settings_for_display();
		$this->render_swiper_header_attribute( 'edd-category-carousel' );
		$this->add_render_attribute( 'carousel', 'class', [ 'bdt-edd-category-carousel', $settings['skin_layout'] ] );
		?>
		<div <?php $this->print_render_attribute_string( 'carousel' ); ?>>
			<div <?php $this->print_render_attribute_string( 'swiper' ); ?>>
				<div class="swiper-wrapper">
			<?php
	}

	/**
	 * Render EDD category carousel.
	 */
	protected function render_edd_category_carousel() {
		$this->render_edd_category_carousel_header();
		$this->render_edd_category_loop_item( 'carousel' );
		$this->render_footer();
	}

	/**
	 * Register EDD product grid layout controls.
	 */
	protected function register_edd_product_grid_layout_controls() {
		$w = $this->get_edd_product_wrapper_selector( 'grid' );
		$this->start_controls_section(
            'section_woocommerce_layout',
            [
                'label' => esc_html__('Layout', 'bdthemes-element-pack'),
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label'          => esc_html__('Columns', 'bdthemes-element-pack'),
                'type'           => Controls_Manager::SELECT,
                'default'        => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options'        => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'selectors' => [
                    $w . ' .ep-edd-product-wrapper' => 'grid-template-columns: repeat({{VALUE}}, 1fr)'
                ]
            ]
        );

        $this->add_responsive_control(
            'items_columns_gap',
            [
                'label'     => esc_html__('Columns Gap', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 30,
                ],
                'selectors' => [
                    $w . ' .ep-edd-product-wrapper' => 'grid-column-gap: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_responsive_control(
            'items_row_gap',
            [
                'label'     => esc_html__('Row Gap', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 30,
                ],
                'selectors' => [
                    $w . ' .ep-edd-product-wrapper' => 'grid-row-gap: {{SIZE}}px;',
                ],
            ]
        );
        $this->add_control(
            'alignment',
            [
                'label'         => __('Alignment', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::CHOOSE,
                'options'       => [
                    'left'      => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'center'    => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-center',
                    ],
                    'right'     => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-right',
                    ],
                ],
                'default'       => 'center',
                'selectors' => [
                    $w . ' .ep-edd-content' => 'text-align:{{VALUE}}'
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'      => 'image',
                'label'     => esc_html__('Image Size', 'bdthemes-element-pack'),
                'exclude'   => ['custom'],
                'default'   => 'medium',
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label'     => esc_html__('Pagination', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
            ]
        );
        $this->end_controls_section();
	}

	/**
	 * Register EDD product carousel layout controls.
	 */
	protected function register_edd_product_carousel_layout_controls() {
		$w = $this->get_edd_product_wrapper_selector( 'carousel' );
		$this->start_controls_section(
            'section_content_layout',
            [
                'label' => esc_html__('Layout', 'bdthemes-element-pack'),
            ]
        );

        //swiper carousel columns & item gap controls
				$this->register_carousel_column_gap_controls();

        $this->add_control(
            'alignment',
            [
                'label'         => __('Alignment', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::CHOOSE,
                'options'       => [
                    'left'      => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'center'    => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-center',
                    ],
                    'right'     => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-right',
                    ],
                ],
                'default'       => 'center',
                'selectors' => [
                    $w . ' .ep-edd-content' => 'text-align:{{VALUE}}'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'    => 'image',
                'label'   => esc_html__('Image Size', 'bdthemes-element-pack'),
                'exclude' => ['custom'],
                'default' => 'medium',
            ]
        );
        $this->add_control(
            'show_categories',
            [
                'label'     => esc_html__('Categories', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'default'  => 'yes',
                'separator' => 'before'
            ]
        );
        $this->add_control(
            'show_title',
            [
                'label'   => esc_html__('Title', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'title_tags',
            [
                'label'   => __('Title HTML Tag', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'h2',
                'options' => element_pack_title_tags(),
                'condition' => [
                    'show_title' => 'yes'
                ]
            ]
        );


        $this->add_control(
            'show_price',
            [
                'label'   => esc_html__('Price', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        $this->end_controls_section();
	}

	/**
	 * Register EDD product additional content controls.
	 */
	protected function register_edd_product_additional_controls() {
		$this->start_controls_section(
            'section_edd_additional',
            [
                'label' => esc_html__('Additional', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'show_categories',
            [
                'label'     => esc_html__('Categories', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'show_title',
            [
                'label'   => esc_html__('Title', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'title_tags',
            [
                'label'   => __('Title HTML Tag', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'h2',
                'options' => element_pack_title_tags(),
                'condition' => [
                    'show_title' => 'yes'
                ]
            ]
        );


        $this->add_control(
            'show_price',
            [
                'label'   => esc_html__('Price', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
	}

	/**
	 * Register EDD product query controls.
	 */
	protected function register_edd_product_query_controls() {
		$this->start_controls_section(
            'section_post_query_builder',
            [
                'label' => __('Query', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->register_query_builder_controls();

        $this->update_control(
            'posts_source',
            [
                'type'      => Controls_Manager::SELECT,
                'default'   => 'download',
                'options' => [
                    'download' => "Download",
                    'manual_selection'   => __('Manual Selection', 'bdthemes-element-pack'),
                    'current_query'      => __('Current Query', 'bdthemes-element-pack'),
                    '_related_post_type' => __('Related', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->update_control(
            'posts_selected_ids',
            [
                'query_args'  => [
                    'query' => 'posts',
                    'post_type' => 'download'
                ],
            ]
        );
        $this->update_control(
            'posts_offset',
            [
                'label' => __('Offset', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::NUMBER,
                'default'   => 0,

            ]
        );

        $this->end_controls_section();
	}

	/**
	 * Register EDD product filter bar controls.
	 */
	protected function register_edd_product_filter_controls() {
		$this->start_controls_section(
						'filter_bar',
						[ 
								'label' => esc_html__( 'Filter Bar', 'bdthemes-element-pack' ),
						]
				);

        $this->add_control(
            'show_filter_bar',
            [
                'label' => esc_html__('Show Filter', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::SWITCHER,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'active_hash',
            [
                'label'       => esc_html__('Hash Location', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SWITCHER,
                'default'     => 'no',
                'condition' => [
                    'show_filter_bar' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'hash_top_offset',
            [
                'label'     => esc_html__('Top Offset ', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'size_units' => ['px', ''],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                        'step' => 5,
                    ],

                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 70,
                ],
                'condition' => [
                    'active_hash' => 'yes',
                    'show_filter_bar' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'hash_scrollspy_time',
            [
                'label'     => esc_html__('Scrollspy Time', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'size_units' => ['ms', ''],
                'range' => [
                    'px' => [
                        'min' => 500,
                        'max' => 5000,
                        'step' => 1000,
                    ],
                ],
                'default'   => [
                    'unit' => 'px',
                    'size' => 1000,
                ],
                'condition' => [
                    'active_hash' => 'yes',
                    'show_filter_bar' => 'yes',
                ],
            ]
        );

        $this->add_control(
						'filter_custom_text',
						[ 
								'label'     => esc_html__( 'Custom Text', 'bdthemes-element-pack' ) . BDTEP_NC,
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
								'default' => esc_html__( 'All Products', 'bdthemes-element-pack' ),
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
	}

	/**
	 * Register EDD product item style controls.
	 *
	 * @param string $type 'grid' or 'carousel'.
	 */
	protected function register_edd_product_style_item_controls( $type ) {
		$w = $this->get_edd_product_wrapper_selector( $type );
		if ( 'grid' === $type ) {
			$this->start_controls_section(
            'section_style_item',
            [
                'label'     => esc_html__('Item', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_item_style');

        $this->start_controls_tab(
            'tab_item_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'item_background',
                'label'     => __('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => $w . ' .ep-edd-product-item',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'item_border',
                'label'       => esc_html__('Border Color', 'bdthemes-element-pack'),
                'selector'    => $w . ' .ep-edd-product-item',
                'separator'   => 'before',
            ]
        );

        $this->add_responsive_control(
            'item_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    $w . ' .ep-edd-product-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_shadow',
                'selector' => $w . ' .ep-edd-product-item',
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label'      => esc_html__('Item Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    $w . ' .ep-edd-product-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label'      => esc_html__('Content Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    $w . ' .ep-edd-product-item .ep-edd-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_item_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'item_hover_background',
                'label'     => __('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => $w . ' .ep-edd-product-item:hover',
            ]
        );

        $this->add_control(
            'item_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'item_border_border!' => '',
                ],
                'selectors' => [
                    $w . ' .ep-edd-product-item:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_hover_shadow',
                'selector' => $w . ' .ep-edd-product-item:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
		} else {
			$this->start_controls_section(
            'section_style_item',
            [
                'label'     => esc_html__('Item', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_item_style');

        $this->start_controls_tab(
            'tab_item_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'item_background',
                'label'     => __('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => $w . ' .ep-edd-product-item',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'item_border',
                'label'       => esc_html__('Border Color', 'bdthemes-element-pack'),
                'selector'    => $w . ' .ep-edd-product-item',
                'separator'   => 'before',
            ]
        );

        $this->add_responsive_control(
            'item_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    $w . ' .ep-edd-product-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_shadow',
                'selector' => $w . ' .ep-edd-product-item',
            ]
        );
        $this->add_responsive_control(
            'item_shadow_padding',
            [
                'label'       => __('Match Padding', 'bdthemes-element-pack'),
                'description' => __('You have to add padding for matching overlaping normal/hover box shadow when you used Box Shadow option.', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SLIDER,
                'range'       => [
                    'px' => [
                        'min'  => 0,
                        'step' => 1,
                        'max'  => 50,
                    ]
                ],
                'selectors'   => [
                    '{{WRAPPER}} .swiper-carousel' => 'padding: {{SIZE}}{{UNIT}}; margin: 0 -{{SIZE}}{{UNIT}};'
                ],
            ]
        );
        $this->add_responsive_control(
            'item_padding',
            [
                'label'      => esc_html__('Item Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    $w . ' .ep-edd-product-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'content_padding',
            [
                'label'      => esc_html__('Content Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    $w . ' .ep-edd-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_item_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'item_hover_background',
                'label'     => __('Background', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => $w . ' .ep-edd-product-item:hover',
            ]
        );

        $this->add_control(
            'item_hover_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'item_border_border!' => '',
                ],
                'selectors' => [
                    $w . ' .ep-edd-product-item:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'item_hover_shadow',
                'selector' => $w . ' .ep-edd-product-item:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
		}
	}

	/**
	 * Register EDD product title style controls.
	 *
	 * @param string $type 'grid' or 'carousel'.
	 */
	protected function register_edd_product_style_title_controls( $type ) {
		$w = $this->get_edd_product_wrapper_selector( $type );
		$this->start_controls_section(
            'section_style_title',
            [
                'label'     => esc_html__('Title', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    $w . ' .ep-edd-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hover_title_color',
            [
                'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    $w . ' .ep-edd-title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    $w . ' .ep-edd-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => $w . ' .ep-edd-title a',
            ]
        );

        $this->end_controls_section();
	}

	/**
	 * Register EDD product category style controls.
	 *
	 * @param string $type 'grid' or 'carousel'.
	 */
	protected function register_edd_product_style_category_controls( $type ) {
		$w = $this->get_edd_product_wrapper_selector( $type );
		$this->start_controls_section(
            'section_style_category',
            [
                'label'     => esc_html__('Category', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_categories' => 'yes',
                ],
            ]
        );
        $this->start_controls_tabs(
            'category_tabs'
        );
        $this->start_controls_tab(
            'category_tab_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'category_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    $w . ' .ep-edd-category a' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'category_bg_color',
                'selector'  => $w . ' .ep-edd-category a',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'           => 'category_border',
                'label'          => __('Border', 'bdthemes-element-pack'),
                'selector'       => $w . ' .ep-edd-category a',
                'separator' => 'before'
            ]
        );
        $this->add_responsive_control(
            'category_radius',
            [
                'label'                 => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%', 'em'],
                'selectors'             => [
                    $w . ' .ep-edd-category a'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'category_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    $w . ' .ep-edd-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'category_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    $w . ' .ep-edd-category' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'category_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => $w . ' .ep-edd-category a',
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'category_shadow',
                'selector' => $w . ' .ep-edd-category a',
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'category_tab_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'hover_category_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    $w . ' .ep-edd-category a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'hover_category_bg_color',
                'selector'  => $w . ' .ep-edd-category a:hover',
            ]
        );
        $this->add_control(
            'hover_category_border_color',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    $w . ' .ep-edd-category a:hover' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'category_border_border!' => ''
                ],
                'separator' => 'before'
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
	}

	/**
	 * Register EDD product action_btn style controls.
	 *
	 * @param string $type 'grid' or 'carousel'.
	 */
	protected function register_edd_product_style_action_btn_controls( $type ) {
		$w = $this->get_edd_product_wrapper_selector( $type );
		if ( 'grid' === $type ) {
			$this->start_controls_section(
            'style_action_btn',
            [
                'label' => esc_html__('Action Button', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->start_controls_tabs(
            'action_btn_tabs'
        );
        $this->start_controls_tab(
            'view_details_tab',
            [
                'label' => esc_html__('View Details', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'view_details_normal_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    $w . ' .ep-details-button a' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'view_details_bg',
                'label'     => __('Title', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => $w . ' .ep-details-button a',
            ]
        );
        $this->add_control(
            'heading_view_details_hover',
            [
                'label'     => esc_html__('Hover', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'view_details_hover_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    $w . ' .ep-details-button a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'view_details_hover_bg',
                'label'     => __('Title', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => $w . ' .ep-details-button a:hover',
                'separator' => 'after'
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'purchase_btn_tab',
            [
                'label' => esc_html__('Purchase', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'purchase_btn_normal_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    $w . ' .ep-action-button .blue' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'purchase_btn_bg',
                'label'     => __('Title', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => $w . ' .ep-action-button .blue',
            ]
        );
        $this->add_control(
            'heading_purchase_btn_hover',
            [
                'label'     => esc_html__('Hover', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'purchase_btn_hover_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    $w . ' .ep-action-button .blue:hover' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'purchase_btn_hover_bg',
                'label'     => __('Title', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => $w . ' .ep-action-button .blue:hover',
                'separator' => 'after'
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'action_btn_border',
                'label'     => esc_html__('Border', 'bdthemes-element-pack'),
                'selector'  => $w . ' .ep-action-button a',
                'separator' => 'before'
            ]
        );
        $this->add_responsive_control(
            'action_btn_radius',
            [
                'label'                 => esc_html__('Radius', 'bdthemes-element-pack'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%', 'em'],
                'selectors'             => [
                    $w . ' .ep-action-button a'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'action_btn_padding',
            [
                'label'                 => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%', 'em'],
                'selectors'             => [
                    $w . ' .ep-action-button a'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'action_btn_margin',
            [
                'label'                 => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%', 'em'],
                'selectors'             => [
                    $w . ' .ep-action-button'    => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'action_btn_space_between',
            [
                'label'         => __('Space Between', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px'],
                'range'         => [
                    'px'        => [
                        'min'   => 0,
                        'max'   => 50,
                        'step'  => 1,
                    ]
                ],
                'selectors' => [
                    $w . ' .ep-edd-product-item .ep-action-button' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'action_btn_typography',
                'label'     => __('Typography', 'bdthemes-element-pack'),
                'selector'  => $w . ' .ep-action-button a',
            ]
        );
        $this->end_controls_section();
		} else {
			$this->start_controls_section(
            'style_action_btn',
            [
                'label' => esc_html__('Action Button', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->start_controls_tabs(
            'action_btn_tabs'
        );
        $this->start_controls_tab(
            'view_details_tab',
            [
                'label' => esc_html__('View Details', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'view_details_normal_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    $w . ' .ep-details-button a' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'view_details_bg',
                'label'     => __('Title', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => $w . ' .ep-details-button a',
            ]
        );
        $this->add_control(
            'heading_view_details_hover',
            [
                'label'     => esc_html__('Hover', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'view_details_hover_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    $w . ' .ep-details-button a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'view_details_hover_bg',
                'label'     => __('Title', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => $w . ' .ep-details-button a:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'      => 'action_btn_border',
                'label'     => esc_html__('Border', 'bdthemes-element-pack'),
                'selector'  => $w . ' .ep-edd-action-button a',
                'separator' => 'before'
            ]
        );
        $this->add_responsive_control(
            'action_btn_radius',
            [
                'label'                 => esc_html__('Radius', 'bdthemes-element-pack'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%', 'em'],
                'selectors'             => [
                    $w . ' .ep-edd-action-button a'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'action_btn_padding',
            [
                'label'                 => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'                  => Controls_Manager::DIMENSIONS,
                'size_units'            => ['px', '%', 'em'],
                'selectors'             => [
                    $w . ' .ep-edd-action-button a'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'action_btn_space_between',
            [
                'label'         => __('Space Between', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => ['px'],
                'range'         => [
                    'px'        => [
                        'min'   => 0,
                        'max'   => 50,
                        'step'  => 1,
                    ]
                ],
                'selectors' => [
                    $w . ' .ep-edd-product-item .ep-edd-action-button' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'action_btn_typography',
                'label'     => __('Typography', 'bdthemes-element-pack'),
                'selector'  => $w . ' .ep-edd-action-button a',
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'purchase_btn_tab',
            [
                'label' => esc_html__('Purchase', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'purchase_btn_normal_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    $w . ' .ep-edd-action-button .blue' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'purchase_btn_bg',
                'label'     => __('Title', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => $w . ' .ep-edd-action-button .blue',
            ]
        );
        $this->add_control(
            'heading_purchase_btn_hover',
            [
                'label'     => esc_html__('Hover', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'purchase_btn_hover_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    $w . ' .ep-edd-action-button .blue:hover' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'purchase_btn_hover_bg',
                'label'     => __('Title', 'bdthemes-element-pack'),
                'types'     => ['classic', 'gradient'],
                'selector'  => $w . ' .ep-edd-action-button .blue:hover',
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
		}
	}

	/**
	 * Register EDD product price style controls.
	 *
	 * @param string $type 'grid' or 'carousel'.
	 */
	protected function register_edd_product_style_price_controls( $type ) {
		$w = $this->get_edd_product_wrapper_selector( $type );
		if ( 'grid' === $type ) {
			$this->start_controls_section(
            'section_style_price',
            [
                'label'     => esc_html__('Price', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_price' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    $w . ' .ep-edd-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'price_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    $w . ' .ep-edd-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'price_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
                'selector' => $w . ' .ep-edd-price',
            ]
        );
        $this->end_controls_section();
		} else {
			$this->start_controls_section(
            'section_style_price',
            [
                'label'     => esc_html__('Price', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_price' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    $w . ' .ep-edd-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'price_margin',
            [
                'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    $w . ' .ep-edd-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'price_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => $w . ' .ep-edd-price',
            ]
        );
        $this->end_controls_section();
		}
	}

	/**
	 * Register EDD product pagination style controls.
	 */
	protected function register_edd_product_style_pagination_controls() {
		$this->start_controls_section(
            'section_style_pagination',
            [
                'label'     => esc_html__('Pagination', 'bdthemes-element-pack'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_pagination' => 'yes',
                ],
            ]
        );
        $this->add_responsive_control(
            'pagination_spacing',
            [
                'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} ul.bdt-pagination'    => 'margin-top: {{SIZE}}px;',
                    '{{WRAPPER}} .dataTables_paginate' => 'margin-top: {{SIZE}}px;',
                ],
            ]
        );
        $this->add_control(
            'pagination_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ul.bdt-pagination li a'    => 'color: {{VALUE}};',
                    '{{WRAPPER}} ul.bdt-pagination li span' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .paginate_button'          => 'color: {{VALUE}} !important;',
                ],
            ]
        );
        $this->add_control(
            'active_pagination_color',
            [
                'label'     => esc_html__('Active Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ul.bdt-pagination li.bdt-active a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .paginate_button.current'          => 'color: {{VALUE}} !important;',
                ],
            ]
        );
        $this->add_responsive_control(
            'pagination_margin',
            [
                'label'     => esc_html__('Margin', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} ul.bdt-pagination li a'    => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                    '{{WRAPPER}} ul.bdt-pagination li span' => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                    '{{WRAPPER}} .paginate_button'          => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );

        $this->add_responsive_control(
            'pagination_arrow_size',
            [
                'label'     => esc_html__('Arrow Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} ul.bdt-pagination li a svg' => 'height: {{SIZE}}px; width: auto;',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'pagination_typography',
                'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} ul.bdt-pagination li a, {{WRAPPER}} ul.bdt-pagination li span, {{WRAPPER}} .dataTables_paginate',
            ]
        );
        $this->end_controls_section();
	}

	/**
	 * Register EDD product carousel navigation controls.
	 */
	protected function register_edd_product_carousel_navigation_controls() {
		$this->start_controls_section(
            'section_content_navigation',
            [
                'label' => __('Navigation', 'bdthemes-element-pack'),
            ]
        );

        //Global Navigation Controls
        $this->register_navigation_controls();

        $this->end_controls_section();

        //Global Carousel Settings Controls
        $this->register_carousel_settings_controls();

        //Navigation Style
        $this->start_controls_section(
            'section_style_navigation',
            [
                'label'      => __('Navigation', 'bdthemes-element-pack'),
                'tab'        => Controls_Manager::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms'    => [
                        [
                            'name'     => 'navigation',
                            'operator' => '!=',
                            'value'    => 'none',
                        ],
                        [
                            'name'  => 'show_scrollbar',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        //Global Navigation Style Controls
        $this->register_navigation_style_controls('swiper-carousel');

        $this->end_controls_section();
	}

	/**
	 * Query EDD download products.
	 */
	protected function query_edd_product() {
		$settings         = $this->get_settings_for_display();
            $posts_per_page   = isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 0;
            $args             = $this->getGroupControlQueryArgs();
            $is_current_query = ( ! empty( $settings['posts_source'] ) && $settings['posts_source'] === 'current_query' );

            if ( $is_current_query ) {
                unset( $args['offset'] );
                unset( $args['no_found_rows'] );
                $posts_per_page = 0;
            }

            if ( $posts_per_page > 0 ) {
                $args['posts_per_page'] = $posts_per_page;
            } else {
                $args['posts_per_page'] = (int) get_option( 'posts_per_page', 10 );
            }

            $args['post_type'] = 'download';

            if ( ! empty( $settings['show_pagination'] ) ) {
                $args['paged'] = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
            }

            $this->_query = new \WP_Query( $args );
	}

	/**
	 * Render EDD product filter menu.
	 */
	protected function render_edd_product_filter_menu() {
		$settings           = $this->get_settings_for_display();
        $product_categories = [];
        $this->query_edd_product();
        $wp_query = $this->get_query();

        while ($wp_query->have_posts()) : $wp_query->the_post();
            $terms = get_the_terms(get_the_ID(), 'download_category');
            foreach ($terms as $term) {
                $product_categories[] = esc_attr($term->slug);
            };
        endwhile;

        wp_reset_postdata();

        $product_categories = array_unique($product_categories);
        $this->add_render_attribute(
            [
                'portfolio-gallery-hash-data' => [
                    'data-hash-settings' => [
                        wp_json_encode(
                            array_filter([
                                "id"       => 'bdt-products-' . $this->get_id(),
                                'activeHash'          => $settings['active_hash'],
                                'hashTopOffset'      => isset($settings['hash_top_offset']['size']) ? $settings['hash_top_offset']['size'] : 70,
                                'hashScrollspyTime' => isset($settings['hash_scrollspy_time']['size']) ? $settings['hash_scrollspy_time']['size'] : 1000,
                            ])
                        ),
                    ],
                ],
            ]
        ); ?>

        <div class="bdt-ep-grid-filters-wrapper" id="<?php echo esc_attr('bdt-products-' . $this->get_id()); ?>" <?php $this->print_render_attribute_string('portfolio-gallery-hash-data'); ?>>
            <button class="bdt-button bdt-button-default bdt-hidden@m" type="button">
                <?php if ( isset( $settings['filter_custom_text'] ) && ( $settings['filter_custom_text'] != 'yes' ) ) : ?>
										<?php esc_html_e( 'Filter', 'bdthemes-element-pack' ); ?>
								<?php else : ?>
										<?php echo esc_html( $settings['filter_custom_text_filter'] ); ?>
								<?php endif; ?>
            </button>
            <div data-bdt-dropdown="mode: click;" class="bdt-dropdown bdt-margin-remove-top bdt-margin-remove-bottom bdt-hidden@m">
                <ul class="bdt-nav bdt-dropdown-nav">
                    <?php if ( $settings['filter_custom_text']) : ?>
												<?php if ( ! empty($settings['filter_custom_text_all']) ) : ?>
														<li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control>
																<a href="#"><?php echo esc_html( $settings['filter_custom_text_all'] ); ?></a>
														</li>
												<?php endif; ?>
										<?php else : ?>
												<li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control>
														<a href="#"><?php esc_html_e( 'All Products', 'bdthemes-element-pack' ); ?></a>
												</li>
										<?php endif; ?>

                    <?php foreach ($product_categories as $product_category => $value) : ?>
                        <?php $filter_name = get_term_by('slug', $value, 'download_category'); ?>
                        <li class="bdt-ep-grid-filter" data-bdt-filter-control="[data-filter*='bdtf-<?php echo esc_attr(trim($value)); ?>']">
                            <a href="#"><?php echo esc_html($filter_name->name); ?></a>
                        </li>
                    <?php endforeach; ?>

                </ul>
            </div>

            <ul class="bdt-ep-grid-filters bdt-visible@m" data-bdt-margin>

                <?php if ( $settings['filter_custom_text']) : ?>
										<?php if ( ! empty($settings['filter_custom_text_all']) ) : ?>
												<li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control>
														<a href="#"><?php echo esc_html( $settings['filter_custom_text_all'] ); ?></a>
												</li>
										<?php endif; ?>
								<?php else : ?>
										<li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control>
												<a href="#"><?php esc_html_e( 'All Products', 'bdthemes-element-pack' ); ?></a>
										</li>
								<?php endif; ?>

                <?php foreach ($product_categories as $product_category => $value) : ?>
                    <?php $filter_name = get_term_by('slug', $value, 'download_category'); ?>
                    <li class="bdt-ep-grid-filter" data-bdt-filter-control="[data-filter*='bdtf-<?php echo esc_attr(trim($value)); ?>']">
                        <a href="#"><?php echo esc_html($filter_name->name); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php
	}

	/**
	 * Render EDD product grid header.
	 */
	protected function render_edd_product_grid_header() {
		$settings = $this->get_settings_for_display();
        $alignment = isset($settings['alignment']) ? $settings['alignment'] : 'center';
        $this->add_render_attribute('ep-edd-product-grid', 'class', ['ep-edd-product', 'ep-edd-content-position-' . $alignment], true);
        if ($settings['show_filter_bar']) {
            $this->add_render_attribute('ep-edd-product-grid', 'data-bdt-filter', 'target: #bdt-edd-product-' . $this->get_id());
        } ?>

        <div <?php $this->print_render_attribute_string('ep-edd-product-grid'); ?>>
            <?php if ($settings['show_filter_bar']) {
                $this->render_filter_menu();
            }
	}

	/**
	 * Render EDD product grid footer.
	 */
	protected function render_edd_product_grid_footer() {
		?>
        </div>
        <?php
	}

	/**
	 * Render EDD product grid loop.
	 */
	protected function render_edd_product_grid_loop() {
		$settings = $this->get_settings_for_display();
		$settings = $this->get_settings_for_display();
            $id       = 'bdt-edd-product-' . $this->get_id();
            $this->query_edd_product();
            $wp_query = $this->get_query();
            if ($wp_query->have_posts()) {

                $this->add_render_attribute(
                    [
                        'edd-products-wrapper' => [
                            'class' => [
                                'ep-edd-product-wrapper'
                            ],
                            'id' => esc_attr($id),
                        ],
                    ]
                );
        ?>
            <div <?php $this->print_render_attribute_string('edd-products-wrapper'); ?>>
                <?php
                while ($wp_query->have_posts()) {
                    $wp_query->the_post();
                    if ($settings['show_filter_bar']) {
                        $terms = get_the_terms(get_the_ID(), 'download_category');
                        $product_filter_cat = [];
                        foreach ($terms as $term) {
                            $product_filter_cat[] = 'bdtf-' . esc_attr($term->slug);
                        };
                        $this->add_render_attribute('edd-product-item', 'data-filter', implode(' ', $product_filter_cat), true);
                    }
                    $this->add_render_attribute('edd-product-item', 'class', 'ep-edd-product-item', true);
                ?>
                    <div <?php $this->print_render_attribute_string('edd-product-item'); ?>>
                        <div class="ep-edd-product-image-wrapper">
                            <div class="ep-edd-product-image">
                                <a href="<?php the_permalink(); ?>">
                                    <img src="<?php echo esc_url(wp_get_attachment_image_url(get_post_thumbnail_id(), $settings['image_size'])); ?>" alt="<?php echo esc_html(get_the_title()); ?>">
                                </a>
                                <div class="ep-action-button">
                                    <?php if (function_exists('edd_price')) { ?>
                                        <?php if (!edd_has_variable_prices(get_the_ID())) { ?>
                                            <?php echo esc_url(edd_get_purchase_link(get_the_ID(), 'Add to Cart', 'button')); ?>
                                        <?php } ?>
                                    <?php } ?>
                                    <div class="ep-details-button">
                                        <a href="<?php the_permalink(); ?>"><span><?php esc_html_e('View Details', 'bdthemes-element-pack'); ?></span></a>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="ep-edd-content">
                            <?php
                            if ($settings['show_categories']) :
                                $category_list = wp_get_post_terms(get_the_ID(), 'download_category');
                                foreach ($category_list as $term) {
                                    $term_link = get_term_link($term);
                                    echo '<span class="ep-edd-category"><a href="' . esc_url($term_link) . '">' . esc_html($term->name) . '</a></span> ';
                                }
                            endif;

                            if ($settings['show_title']) :
                                printf(
                                    '<%1$s class="ep-edd-title"><a href="%2$s">%3$s</a></%1$s>', 
                                    esc_attr( Utils::get_valid_html_tag( $settings['title_tags'] ) ), 
                                    esc_url( get_the_permalink() ), 
                                    esc_html( get_the_title() )
                                );
                            endif;

                            if ($settings['show_price']) : ?>
                                <div class="ep-edd-price">
                                    <?php if (edd_has_variable_prices(get_the_ID())) {
                                        esc_html_e('Starting at: ', 'bdthemes-element-pack');
                                        edd_price(get_the_ID());
                                    } else {
                                        edd_price(get_the_ID());
                                    }
                                    ?>
                                </div>
                            <?php
                            endif; ?>

                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
            <?php
                if ($settings['show_pagination']) {
            ?>
                <div class="ep-pagination">
                    <?php element_pack_post_pagination($wp_query); ?>
                </div>
<?php
                    wp_reset_postdata();
                }
            }
	}

	/**
	 * Render EDD product grid.
	 */
	protected function render_edd_product_grid() {
		$this->render_edd_product_grid_header();
		$this->render_edd_product_grid_loop();
		$this->render_edd_product_grid_footer();
	}

	/**
	 * Render EDD product carousel header.
	 */
	protected function render_edd_product_carousel_header() {
		$this->render_swiper_header_attribute('edd-product-carousel');
        $this->add_render_attribute('carousel', 'class', ['bdt-edd-product-carousel']); ?>
        <div <?php $this->print_render_attribute_string('carousel'); ?>>
            <div <?php $this->print_render_attribute_string('swiper'); ?>>
                <div class="swiper-wrapper">
                    <?php
	}

	/**
	 * Render EDD product carousel loop.
	 */
	protected function render_edd_product_carousel_loop() {
		$settings = $this->get_settings_for_display();
		$settings = $this->get_settings_for_display();
                    $id       = 'bdt-edd-product-' . $this->get_id();
                    $this->query_edd_product();
                    $wp_query = $this->get_query();
                    if ($wp_query->have_posts()) {
                        $this->add_render_attribute(
                            [
                                'edd-products-wrapper' => [
                                    'class' => [
                                        'ep-edd-product-wrapper '
                                    ],
                                    'id' => esc_attr($id),
                                ],
                            ]
                        );
                    ?>
                        <?php
                        while ($wp_query->have_posts()) {
                            $wp_query->the_post();
                            $this->add_render_attribute('edd-product-item', 'class', ['ep-edd-product-item', 'swiper-slide'], true);
                        ?>
                            <div <?php $this->print_render_attribute_string('edd-product-item'); ?>>
                                <div class="ep-edd-image-wrapper">
                                    <div class="ep-edd-image">
                                        <a href="<?php the_permalink(); ?>">
                                            <img src="<?php echo esc_url(wp_get_attachment_image_url(get_post_thumbnail_id(), $settings['image_size'])); ?>" alt="<?php echo esc_html(get_the_title()); ?>">
                                        </a>
                                    </div>

                                    <div class="ep-edd-action-button">
                                        <?php if (function_exists('edd_price')) { ?>
                                            <?php if (!edd_has_variable_prices(get_the_ID())) { ?>
                                                <?php echo wp_kses_post(edd_get_purchase_link(get_the_ID(), 'Add to Cart', 'button')); ?>
                                            <?php } ?>
                                        <?php } ?>
                                        <div class="ep-details-button">
                                            <a href="<?php the_permalink(); ?>"><span>View Details</span></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="ep-edd-content">
                                    <?php
                                    if ($settings['show_categories']) :
                                        $category_list = wp_get_post_terms(get_the_ID(), 'download_category');
                                        foreach ($category_list as $term) {
                                            $term_link = get_term_link($term);
                                            echo '<span class="ep-edd-category"><a href="' . esc_url($term_link) . '">' . esc_html($term->name) . '</a></span> ';
                                        }
                                    endif;

                                    if ($settings['show_title']) :
                                        printf(
                                            '<%1$s class="ep-edd-title"><a href="%2$s">%3$s</a></%1$s>', 
                                            esc_attr( Utils::get_valid_html_tag( $settings['title_tags'] ) ), 
                                            esc_url( get_the_permalink() ), 
                                            esc_html( get_the_title() )
                                        );
                                    endif;

                                    if ($settings['show_price']) : ?>
                                        <div class="ep-edd-price">
                                            <?php if (edd_has_variable_prices(get_the_ID())) {
                                                esc_html_e('Starting at: ', 'bdthemes-element-pack');
                                                edd_price(get_the_ID());
                                            } else {
                                                edd_price(get_the_ID());
                                            }
                                            ?>
                                        </div>
                                    <?php
                                    endif; ?>

                                </div>
                            </div>
                        <?php
                        }
                        wp_reset_postdata(); ?>
            <?php
                    }
	}

	/**
	 * Render EDD product carousel.
	 */
	protected function render_edd_product_carousel() {
		$this->render_edd_product_carousel_header();
		$this->render_edd_product_carousel_loop();
		$this->render_footer();
	}

	protected function get_events_calendar_wrapper_selector( $type ) {
		return '{{WRAPPER}} .bdt-event-calendar';
	}

	protected function get_events_calendar_selector( $type, $element ) {
		return $this->get_events_calendar_wrapper_selector( $type ) . ' ' . ltrim( $element, ' ' );
	}

	protected function register_events_calendar_layout_controls( $type ) {
		if ( 'grid' === $type ) {
		$this->start_controls_section(
				'section_content_layout',
				[
					'label' => __('Layout', 'bdthemes-element-pack'),
				]
			);

			$this->add_responsive_control(
				'columns',
				[
					'label'          => esc_html__('Columns', 'bdthemes-element-pack'),
					'type'           => Controls_Manager::SELECT,
					'default'        => '3',
					'tablet_default' => '2',
					'mobile_default' => '1',
					'options'        => [
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					],
				]
			);

			$this->add_control(
				'column_gap',
				[
					'label'   => esc_html__('Column Gap', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'medium',
					'options' => [
						'small'    => esc_html__('Small', 'bdthemes-element-pack'),
						'medium'   => esc_html__('Medium', 'bdthemes-element-pack'),
						'large'    => esc_html__('Large', 'bdthemes-element-pack'),
						'collapse' => esc_html__('Collapse', 'bdthemes-element-pack'),
					],
				]
			);

			$this->add_responsive_control(
				'row_gap',
				[
					'label' => esc_html__('Row Gap', 'bdthemes-element-pack'),
					'type'  => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-item, .bdt-event-calendar .bdt-event-item-inner' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'show_image',
				[
					'label'   => __('Show Image', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);

			$this->add_control(
				'show_title',
				[
					'label'   => __('Show Title', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);


			$this->add_control(
				'show_date',
				[
					'label'   => __('Show Date', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);

			$this->add_control(
				'show_time',
				[
					'label'   => __('Show Time', 'bdthemes-element-pack') . BDTEP_NC,
					'type'    => Controls_Manager::SWITCHER,
				]
			);


			$this->add_control(
				'show_excerpt',
				[
					'label'   => __('Show Excerpt', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);

			$this->add_control(
				'excerpt_length',
				[
					'label'     => __('Excerpt Length', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 15,
					'condition' => [
						'show_excerpt' => 'yes'
					]
				]
			);

			$this->add_control(
				'show_meta',
				[
					'label'   => __('Show Meta', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'condition'	=> [
						'_skin!'	=> 'acara',
					],
				]
			);

			$this->add_control(
				'show_meta_cost',
				[
					'label'   => __('Show Cost', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);

			$this->add_control(
				'show_meta_website',
				[
					'label'   => __('Show Website', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);

			$this->add_control(
				'show_meta_location',
				[
					'label'   => __('Show Location', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);

			$this->add_control(
				'show_meta_more_btn',
				[
					'label'   => __('Show More Button', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'condition' => [
						'_skin' => 'annal',
					],
				]
			);

			$this->add_control(
				'anchor_link',
				[
					'label'   => __('Anchor Link', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);

			$this->add_control(
				'match_height',
				[
					'label' => __('Item Match Height', 'bdthemes-element-pack') . BDTEP_NC,
					'type' => Controls_Manager::SWITCHER,
				]
			);
			$this->end_controls_section();
		}
		if ( 'carousel' === $type ) {
		$this->start_controls_section(
		'section_content_layout',
		[
		'label' => __('Layout', 'bdthemes-element-pack'),
		]
		);

		//swiper carousel columns & item gap controls
			$this->register_carousel_column_gap_controls();

		$this->add_control(
		'show_image',
		[
		'label' => __('Show Image', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SWITCHER,
		'default' => 'yes',
		]
		);

		$this->add_control(
		'show_title',
		[
		'label' => __('Show Title', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SWITCHER,
		'default' => 'yes',
		]
		);

		$this->add_control(
		'show_date',
		[
		'label' => __('Show Date', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SWITCHER,
		'default' => 'yes',
		]
		);

		// Time
		$this->add_control(
		'show_time',
		[
		'label' => __('Show Time', 'bdthemes-element-pack') .BDTEP_NC,
		'type' => Controls_Manager::SWITCHER,
		'default' => 'no',
		]
		);

		$this->add_control(
		'show_excerpt',
		[
		'label' => __('Show Excerpt', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SWITCHER,
		'default' => 'yes',
		]
		);

		$this->add_control(
		'excerpt_length',
		[
		'label' => __('Excerpt Length', 'bdthemes-element-pack'),
		'type' => Controls_Manager::NUMBER,
		'default' => 15,
		'condition' => [
		'show_excerpt' => 'yes',
		],
		]
		);

		$this->add_control(
		'show_meta',
		[
		'label' => __('Show Meta', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SWITCHER,
		'default' => 'yes',
		'condition' => [
		'_skin!' => 'altra',
		],
		]
		);

		$this->add_control(
		'show_meta_cost',
		[
		'label' => __('Show Cost', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SWITCHER,
		'default' => 'yes',
		]
		);

		$this->add_control(
		'show_meta_website',
		[
		'label' => __('Show Website', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SWITCHER,
		'default' => 'yes',
		]
		);

		$this->add_control(
		'show_meta_location',
		[
		'label' => __('Show Location', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SWITCHER,
		'default' => 'yes',
		]
		);

		$this->add_control(
		'show_meta_more_btn',
		[
		'label' => __('Show More Button', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SWITCHER,
		'default' => 'yes',
		'condition' => [
		'_skin' => 'fable',
		],
		]
		);

		$this->add_control(
		'anchor_link',
		[
		'label' => __('Anchor Link', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SWITCHER,
		'default' => 'yes',
		]
		);

		$this->add_control(
		'match_height',
		[
		'label' => __('Item Match Height', 'bdthemes-element-pack') . BDTEP_NC,
		'type' => Controls_Manager::SWITCHER,
		'condition' => [
		'_skin' => '',
		],
		]
		);

		$this->add_control(
		'skin_match_height',
		[
		'label' => __('Item Match Height', 'bdthemes-element-pack') . BDTEP_NC,
		'type' => Controls_Manager::SWITCHER,
		'condition' => [
		'_skin' => ['altra', 'fable'],
		],
		]
		);
			$this->end_controls_section();
		}
		if ( 'list' === $type ) {
		$this->start_controls_section(
				'section_content_layout',
				[
					'label' => __('Layout', 'bdthemes-element-pack'),
				]
			);

			$this->add_control(
				'show_horizontal',
				[
					'label' => esc_html__('Horizontal Layout', 'bdthemes-element-pack'),
					'type'  => Controls_Manager::SWITCHER,
				]
			);

			$this->add_control(
				'column',
				[
					'label'       => esc_html__('Column', 'bdthemes-element-pack'),
					'type'        => Controls_Manager::SELECT,
					'default'     => '2',
					'description' => 'For good looking set it 1 for default skin and 2 for another skin',
					'options'     => [
						'2' => esc_html__('Two', 'bdthemes-element-pack'),
						'3' => esc_html__('Three', 'bdthemes-element-pack'),
						'4' => esc_html__('Four', 'bdthemes-element-pack'),
					],
					'condition' => [
						'show_horizontal' => 'yes',
					],
				]
			);

			$this->add_responsive_control(
				'item_gap',
				[
					'label'   => esc_html__('Column Gap', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SLIDER,
					'default' => [
						'size' => 35,
					],
					'range' => [
						'px' => [
							'min'  => 0,
							'max'  => 100,
							'step' => 5,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar > .bdt-grid'     => 'margin-left: -{{SIZE}}px',
						'{{WRAPPER}} .bdt-event-calendar > .bdt-grid > *' => 'padding-left: {{SIZE}}px',
					],
					'condition' => [
						'show_horizontal' => 'yes',
					],
				]
			);

			$this->add_responsive_control(
				'row_gap',
				[
					'label'   => esc_html__('Row Gap', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SLIDER,
					'default' => [
						'size' => 35,
					],
					'range' => [
						'px' => [
							'min'  => 0,
							'max'  => 100,
							'step' => 5,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar > .bdt-grid'     => 'margin-top: -{{SIZE}}px',
						'{{WRAPPER}} .bdt-event-calendar > .bdt-grid > *' => 'margin-top: {{SIZE}}px',
					],
					'condition' => [
						'show_horizontal' => 'yes',
					],
				]
			);


			$this->add_responsive_control(
				'list_row_gap',
				[
					'label' => esc_html__('Row Gap', 'bdthemes-element-pack'),
					'type'  => Controls_Manager::SLIDER,
					'default' => [
						'size' => 35,
					],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-item, .bdt-event-calendar .bdt-event-item-inner' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'show_horizontal' => '',
					],
				]
			);

			$this->add_control(
				'show_image',
				[
					'label'   => __('Show Image', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);

			$this->add_control(
				'show_title',
				[
					'label'   => __('Show Title', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);


			$this->add_control(
				'show_date',
				[
					'label'   => __('Show Date', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);

			$this->add_control(
				'show_excerpt',
				[
					'label'   => __('Show Excerpt', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);

			$this->add_control(
				'excerpt_length',
				[
					'label'     => __('Excerpt Length', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 15,
					'condition' => [
						'show_excerpt' => 'yes'
					]
				]
			);

			$this->add_control(
				'show_meta',
				[
					'label'   => __('Show Meta', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);

			$this->add_control(
				'show_meta_cost',
				[
					'label'   => __('Show Cost', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);

			$this->add_control(
				'show_meta_website',
				[
					'label'   => __('Show Website', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);

			$this->add_control(
				'show_meta_location',
				[
					'label'   => __('Show Location', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);

			$this->add_control(
				'anchor_link',
				[
					'label'   => __('Anchor Link', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				]
			);
			$this->end_controls_section();
		}
	}

	protected function register_events_calendar_image_controls( $type ) {
		if ( 'grid' === $type ) {
		$this->start_controls_section(
				'section_content_image',
				[
					'label' => __('Image', 'bdthemes-element-pack'),
				]
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name'    => 'image',
					'label'   => esc_html__('Image Size', 'bdthemes-element-pack'),
					'exclude' => ['custom'],
					'default' => 'medium',
				]
			);

			$this->add_responsive_control(
				'image_width',
				[
					'label' => __('Image Width', 'bdthemes-element-pack'),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 100,
						'unit' => '%',
					],
					'tablet_default' => [
						'unit' => '%',
					],
					'mobile_default' => [
						'unit' => '%',
					],
					'size_units' => ['%'],
					'range' => [
						'%' => [
							'min' => 5,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-image' => 'width: {{SIZE}}{{UNIT}};margin-left: auto;margin-right: auto;',
					],
					'condition' => [
						'show_image' => 'yes',
						'_skin!'	=> 'acara',
					],
				]
			);

			$this->add_responsive_control(
				'image_ratio',
				[
					'label'   => __('Image Ratio', 'bdthemes-element-pack') . BDTEP_NC,
					'type'    => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min'  => 0.1,
							'max'  => 2,
							'step' => 0.01,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-image'       => 'padding-bottom: calc( {{SIZE}} * 100% ); top: 0; left: 0; right: 0; bottom: 0;',
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-image:after' => 'content: "{{SIZE}}"; position: absolute; color: transparent;',
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-image img'   => 'height: 100%; width: auto; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); font-size: {{SIZE}}; object-fit: cover;',
					],
					'condition' => [
						'show_image' => 'yes',
					],
				]
			);
			$this->end_controls_section();
		}
		if ( 'carousel' === $type ) {
		$this->start_controls_section(
		'section_content_image',
		[
		'label' => __('Image', 'bdthemes-element-pack'),
		]
		);

		$this->add_group_control(
		Group_Control_Image_Size::get_type(),
		[
		'name' => 'image',
		'label' => esc_html__('Image Size', 'bdthemes-element-pack'),
		'exclude' => ['custom'],
		'default' => 'medium',
		]
		);

		$this->add_responsive_control(
		'image_width',
		[
		'label' => __('Image Width', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SLIDER,
		'default' => [
		'size' => 100,
		'unit' => '%',
		],
		'tablet_default' => [
		'unit' => '%',
		],
		'mobile_default' => [
		'unit' => '%',
		],
		'size_units' => ['%'],
		'range' => [
		'%' => [
		'min' => 5,
		'max' => 100,
		],
		],
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-image' => 'width: {{SIZE}}{{UNIT}};margin-left: auto;margin-right: auto;',
		],
		'condition' => [
		'show_image' => 'yes',
		'_skin!' => 'altra',
		],
		]
		);

		$this->add_responsive_control(
		'image_ratio',
		[
		'label' => __('Image Ratio', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SLIDER,
		'range' => [
		'px' => [
		'min' => 0.1,
		'max' => 2,
		'step' => 0.01,
		],
		],
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-image' => 'padding-bottom: calc( {{SIZE}} * 100% ); top: 0; left: 0; right: 0; bottom: 0;',
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-image:after' => 'content: "{{SIZE}}"; position: absolute; color: transparent;',
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-image img' => 'height: 100%; width: auto; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); font-size: {{SIZE}}; object-fit: cover;',
		],
		'condition' => [
		'show_image' => 'yes',
		],
		]
		);
			$this->end_controls_section();
		}
		if ( 'list' === $type ) {
		$this->start_controls_section(
				'section_content_image',
				[
					'label' => __('Image', 'bdthemes-element-pack'),
				]
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name'    => 'image',
					'label'   => esc_html__('Image Size', 'bdthemes-element-pack'),
					'exclude' => ['custom'],
					'default' => 'thumbnail',
				]
			);

			$this->add_responsive_control(
				'image_height',
				[
					'label'   => esc_html__('Image Width', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 100,
							'max' => 500,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-image img' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'show_image' => 'yes',
					],
				]
			);
			$this->end_controls_section();
		}
	}

	protected function register_events_calendar_query_controls( $type ) {
		if ( 'grid' === $type ) {
		$this->start_controls_section(
				'section_content_query',
				[
					'label' => __('Query', 'bdthemes-element-pack'),
					'tab'   => Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_control(
				'source',
				[
					'label'   => _x('Source', 'Posts Query Control', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SELECT,
					'options' => [
						''                => esc_html__('Show All', 'bdthemes-element-pack'),
						'upcoming_events' => esc_html__('Upcoming Events', 'bdthemes-element-pack'),
						'by_name'         => esc_html__('Manual Selection', 'bdthemes-element-pack'),
					],
					'label_block' => true,
				]
			);

			$this->add_control(
				'event_categories',
				[
					'label'       => esc_html__('Categories', 'bdthemes-element-pack'),
					'type'        => Controls_Manager::SELECT2,
					'options'     => element_pack_get_terms('tribe_events_cat'),
					'default'     => [],
					'label_block' => true,
					'multiple'    => true,
					'condition'   => [
						'source'    => 'by_name',
					],
				]
			);


			$this->add_control(
				'start_date',
				[
					'label'   => esc_html__('Start Date', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SELECT,
					'options' => [
						''           => esc_html__('Any Time', 'bdthemes-element-pack'),
						'now'        => esc_html__('Now', 'bdthemes-element-pack'),
						'today'      => esc_html__('Today', 'bdthemes-element-pack'),
						'last month' => esc_html__('Last Month', 'bdthemes-element-pack'),
						'custom'     => esc_html__('Custom', 'bdthemes-element-pack'),
					],
					'label_block' => true,
				]
			);

			$this->add_control(
				'custom_start_date',
				[
					'label'   => esc_html__('Custom Start Date', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::DATE_TIME,
					'condition' => [
						'start_date' => 'custom'
					]
				]
			);

			$this->add_control(
				'end_date',
				[
					'label'   => esc_html__('End Date', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SELECT,
					'options' => [
						''           => esc_html__('Any Time', 'bdthemes-element-pack'),
						'now'        => esc_html__('Now', 'bdthemes-element-pack'),
						'today'      => esc_html__('Today', 'bdthemes-element-pack'),
						'next month' => esc_html__('Last Month', 'bdthemes-element-pack'),
						'custom'     => esc_html__('Custom', 'bdthemes-element-pack'),
					],
					'label_block' => true,
				]
			);

			$this->add_control(
				'custom_end_date',
				[
					'label'   => esc_html__('Custom End Date', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::DATE_TIME,
					'condition' => [
						'end_date' => 'custom'
					]
				]
			);

			$this->add_control(
				'limit',
				[
					'label'   => esc_html__('Limit', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::NUMBER,
					'default' => 6,
				]
			);

			$this->add_control(
				'orderby',
				[
					'label'   => esc_html__('Order by', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'event_date',
					'options' => [
						'event_date' => esc_html__('Event Date', 'bdthemes-element-pack'),
						'title'      => esc_html__('Title', 'bdthemes-element-pack'),
						'category'   => esc_html__('Category', 'bdthemes-element-pack'),
						'rand'       => esc_html__('Random', 'bdthemes-element-pack'),
					],
				]
			);

			$this->add_control(
				'order',
				[
					'label'   => esc_html__('Order', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'DESC',
					'options' => [
						'DESC' => esc_html__('Descending', 'bdthemes-element-pack'),
						'ASC'  => esc_html__('Ascending', 'bdthemes-element-pack'),
					],
				]
			);



			$this->end_controls_section();

			// Style Section
			$this->end_controls_section();
		}
		if ( 'carousel' === $type ) {
		$this->start_controls_section(
		'section_content_query',
		[
		'label' => __('Query', 'bdthemes-element-pack'),
		'tab' => Controls_Manager::TAB_CONTENT,
		]
		);

		$this->add_control(
		'source',
		[
		'label' => _x('Source', 'Posts Query Control', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SELECT,
		'options' => [
		'' => esc_html__('Show All', 'bdthemes-element-pack'),
		'by_name' => esc_html__('Manual Selection', 'bdthemes-element-pack'),
		],
		'label_block' => true,
		]
		);

		$this->add_control(
		'event_categories',
		[
		'label' => esc_html__('Categories', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SELECT2,
		'options' => element_pack_get_terms('tribe_events_cat'),
		'default' => [],
		'label_block' => true,
		'multiple' => true,
		'condition' => [
		'source' => 'by_name',
		],
		]
		);

		$this->add_control(
		'start_date',
		[
		'label' => esc_html__('Start Date', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SELECT,
		'options' => [
		'' => esc_html__('Any Time', 'bdthemes-element-pack'),
		'now' => esc_html__('Now', 'bdthemes-element-pack'),
		'today' => esc_html__('Today', 'bdthemes-element-pack'),
		'last month' => esc_html__('Last Month', 'bdthemes-element-pack'),
		'custom' => esc_html__('Custom', 'bdthemes-element-pack'),
		],
		'label_block' => true,
		]
		);

		$this->add_control(
		'custom_start_date',
		[
		'label' => esc_html__('Custom Start Date', 'bdthemes-element-pack'),
		'type' => Controls_Manager::DATE_TIME,
		'condition' => [
		'start_date' => 'custom',
		],
		]
		);

		$this->add_control(
		'end_date',
		[
		'label' => esc_html__('End Date', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SELECT,
		'options' => [
		'' => esc_html__('Any Time', 'bdthemes-element-pack'),
		'now' => esc_html__('Now', 'bdthemes-element-pack'),
		'today' => esc_html__('Today', 'bdthemes-element-pack'),
		'next month' => esc_html__('Last Month', 'bdthemes-element-pack'),
		'custom' => esc_html__('Custom', 'bdthemes-element-pack'),
		],
		'label_block' => true,
		]
		);

		$this->add_control(
		'custom_end_date',
		[
		'label' => esc_html__('Custom End Date', 'bdthemes-element-pack'),
		'type' => Controls_Manager::DATE_TIME,
		'condition' => [
		'end_date' => 'custom',
		],
		]
		);

		$this->add_control(
		'limit',
		[
		'label' => esc_html__('Limit', 'bdthemes-element-pack'),
		'type' => Controls_Manager::NUMBER,
		'default' => 6,
		]
		);

		$this->add_control(
		'orderby',
		[
		'label' => esc_html__('Order by', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SELECT,
		'default' => 'event_date',
		'options' => [
		'event_date' => esc_html__('Event Date', 'bdthemes-element-pack'),
		'title' => esc_html__('Title', 'bdthemes-element-pack'),
		'category' => esc_html__('Category', 'bdthemes-element-pack'),
		'rand' => esc_html__('Random', 'bdthemes-element-pack'),
		],
		]
		);

		$this->add_control(
		'order',
		[
		'label' => esc_html__('Order', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SELECT,
		'default' => 'DESC',
		'options' => [
		'DESC' => esc_html__('Descending', 'bdthemes-element-pack'),
		'ASC' => esc_html__('Ascending', 'bdthemes-element-pack'),
		],
		]
		);

		$this->end_controls_section();

		//Navigation Controls
			$this->end_controls_section();
		}
		if ( 'list' === $type ) {
		$this->start_controls_section(
				'section_content_query',
				[
					'label' => __('Query', 'bdthemes-element-pack'),
					'tab'   => Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_control(
				'source',
				[
					'label'   => _x('Source', 'Posts Query Control', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SELECT,
					'options' => [
						''        => esc_html__('Show All', 'bdthemes-element-pack'),
						'by_name' => esc_html__('Manual Selection', 'bdthemes-element-pack'),
					],
					'label_block' => true,
				]
			);

			$this->add_control(
				'event_categories',
				[
					'label'       => esc_html__('Categories', 'bdthemes-element-pack'),
					'type'        => Controls_Manager::SELECT2,
					'options'     => element_pack_get_terms('tribe_events_cat'),
					'default'     => [],
					'label_block' => true,
					'multiple'    => true,
					'condition'   => [
						'source'    => 'by_name',
					],
				]
			);


			$this->add_control(
				'start_date',
				[
					'label'   => esc_html__('Start Date', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SELECT,
					'options' => [
						''           => esc_html__('Any Time', 'bdthemes-element-pack'),
						'now'        => esc_html__('Now', 'bdthemes-element-pack'),
						'today'      => esc_html__('Today', 'bdthemes-element-pack'),
						'last month' => esc_html__('Last Month', 'bdthemes-element-pack'),
						'custom'     => esc_html__('Custom', 'bdthemes-element-pack'),
					],
					'label_block' => true,
				]
			);

			$this->add_control(
				'custom_start_date',
				[
					'label'   => esc_html__('Custom Start Date', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::DATE_TIME,
					'condition' => [
						'start_date' => 'custom'
					]
				]
			);

			$this->add_control(
				'end_date',
				[
					'label'   => esc_html__('End Date', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SELECT,
					'options' => [
						''           => esc_html__('Any Time', 'bdthemes-element-pack'),
						'now'        => esc_html__('Now', 'bdthemes-element-pack'),
						'today'      => esc_html__('Today', 'bdthemes-element-pack'),
						'next month' => esc_html__('Last Month', 'bdthemes-element-pack'),
						'custom'     => esc_html__('Custom', 'bdthemes-element-pack'),
					],
					'label_block' => true,
				]
			);

			$this->add_control(
				'custom_end_date',
				[
					'label'   => esc_html__('Custom End Date', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::DATE_TIME,
					'condition' => [
						'end_date' => 'custom'
					]
				]
			);

			$this->add_control(
				'limit',
				[
					'label'   => esc_html__('Limit', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::NUMBER,
					'default' => 6,
				]
			);

			$this->add_control(
				'orderby',
				[
					'label'   => esc_html__('Order by', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'event_date',
					'options' => [
						'event_date' => esc_html__('Event Date', 'bdthemes-element-pack'),
						'title'      => esc_html__('Title', 'bdthemes-element-pack'),
						'category'   => esc_html__('Category', 'bdthemes-element-pack'),
						'rand'       => esc_html__('Random', 'bdthemes-element-pack'),
					],
				]
			);

			$this->add_control(
				'order',
				[
					'label'   => esc_html__('Order', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'DESC',
					'options' => [
						'DESC' => esc_html__('Descending', 'bdthemes-element-pack'),
						'ASC'  => esc_html__('Ascending', 'bdthemes-element-pack'),
					],
				]
			);



			$this->end_controls_section();

			// Style Section
			$this->end_controls_section();
		}
	}

	protected function register_events_calendar_style_item_controls( $type ) {
		if ( 'grid' === $type ) {
		$this->start_controls_section(
				'section_style_item',
				[
					'label'     => __('Items', 'bdthemes-element-pack'),
					'tab'       => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'item_content_background',
				[
					'label'     => __('Content Background', 'bdthemes-element-pack') . BDTEP_NC,
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .skin-annal .bdt-event-content' => 'background-color: {{VALUE}};',
					],
					'condition' => [
						'_skin' => ['annal'],
					],
				]
			);

			$this->add_responsive_control(
				'content_padding',
				[
					'label'      => __('Content Padding', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					],
					'condition' => [
						'_skin!' => ['annal'],
					],
				]
			);

			$this->add_control(
				'item_hover_before_style_background',
				[
					'label'     => __('Hover Style', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner:before' => 'background-color: {{VALUE}};',
					],
					'condition' => [
						'_skin!' => ['annal', 'acara'],
					],
				]
			);

			$this->add_responsive_control(
				'item_hover_before_style_radius',
				[
					'label'      => __('Border Radius', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'_skin!' => ['annal', 'acara'],
					],
				]
			);

			$this->start_controls_tabs('tabs_item_style');

			$this->start_controls_tab(
				'tab_item_normal',
				[
					'label' => __('Normal', 'bdthemes-element-pack'),
				]
			);

			$this->add_control(
				'item_background',
				[
					'label'     => __('Background', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'item_shadow',
					'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner',
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'        => 'item_border',
					'label'       => __('Border', 'bdthemes-element-pack'),
					'placeholder' => '1px',
					'default'     => '1px',
					'selector'    => '{{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner',
				]
			);

			$this->add_responsive_control(
				'item_border_radius',
				[
					'label'      => __('Border Radius', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_item_hover',
				[
					'label' => __('Hover', 'bdthemes-element-pack'),
				]
			);

			$this->add_control(
				'item_hover_background',
				[
					'label'     => __('Background', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner:hover' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'item_hover_border_color',
				[
					'label'     => __('Border Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'condition' => [
						'item_border_border!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner:hover' => 'border-color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'item_hover_shadow',
					'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner:hover',
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();
			$this->end_controls_section();
		}
		if ( 'carousel' === $type ) {
		$this->start_controls_section(
		'section_style_item',
		[
		'label' => __('Items', 'bdthemes-element-pack'),
		'tab' => Controls_Manager::TAB_STYLE,
		]
		);

		$this->add_responsive_control(
		'content_padding',
		[
		'label' => __('Content Padding', 'bdthemes-element-pack'),
		'type' => Controls_Manager::DIMENSIONS,
		'size_units' => ['px', '%', 'em'],
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
		],
		'condition' => [
		'_skin!' => ['fable'],
		],
		]
		);

		$this->add_control(
		'item_content_background',
		[
		'label' => __('Content Background', 'bdthemes-element-pack') . BDTEP_NC,
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .skin-fable .bdt-event-content' => 'background-color: {{VALUE}};',
		],
		'condition' => [
		'_skin' => ['fable'],
		],
		]
		);

		$this->add_control(
		'item_hover_before_style_background',
		[
		'label' => __('Hover Style', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-item:before' => 'background-color: {{VALUE}};',
		],
		'condition' => [
		'_skin!' => ['fable', 'altra'],
		],
		]
		);

		$this->add_control(
		'item_hover_before_style_radius',
		[
		'label' => __('Border Radius', 'bdthemes-element-pack'),
		'type' => Controls_Manager::DIMENSIONS,
		'size_units' => ['px', '%'],
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-item:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		],
		'condition' => [
		'_skin!' => ['fable', 'altra'],
		],
		]
		);

		$this->start_controls_tabs('tabs_item_style');

		$this->start_controls_tab(
		'tab_item_normal',
		[
		'label' => __('Normal', 'bdthemes-element-pack'),
		]
		);

		$this->add_control(
		'item_background',
		[
		'label' => __('Background', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		// 'default'   => '#ffffff',
		'selectors' => [
		'{{WRAPPER}} .skin-default .bdt-event-item, {{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner' => 'background-color: {{VALUE}};',
		],
		]
		);

		$this->add_group_control(
		Group_Control_Box_Shadow::get_type(),
		[
		'name' => 'item_shadow',
		'selector' => '{{WRAPPER}} .skin-default .bdt-event-item, {{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner',
		]
		);

		$this->add_group_control(
		Group_Control_Border::get_type(),
		[
		'name' => 'item_border',
		'label' => __('Border', 'bdthemes-element-pack'),
		'placeholder' => '1px',
		'default' => '1px',
		'selector' => '{{WRAPPER}} .skin-default .bdt-event-item, {{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner',
		]
		);

		$this->add_responsive_control(
		'item_border_radius',
		[
		'label' => __('Border Radius', 'bdthemes-element-pack'),
		'type' => Controls_Manager::DIMENSIONS,
		'size_units' => ['px', '%'],
		'selectors' => [
		'{{WRAPPER}} .skin-default .bdt-event-item, {{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner, {{WRAPPER}} .bdt-event-calendar .swiper-carousel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		],
		]
		);

		$this->add_control(
		'item_opacity',
		[
		'label' => esc_html__('Opacity', 'bdthemes-element-pack') . BDTEP_NC,
		'type' => Controls_Manager::SLIDER,
		'range' => [
		'px' => [
		'min' => 0,
		'step' => 0.1,
		'max' => 1,
		],
		],
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-item' => 'opacity: {{SIZE}};',
		],
		]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
		'tab_item_hover',
		[
		'label' => __('Hover', 'bdthemes-element-pack'),
		]
		);

		$this->add_control(
		'item_hover_background',
		[
		'label' => __('Background', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .skin-default .bdt-event-item:hover, {{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner:hover' => 'background-color: {{VALUE}};',
		],
		]
		);

		$this->add_control(
		'item_hover_border_color',
		[
		'label' => __('Border Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'condition' => [
		'item_border_border!' => '',
		],
		'selectors' => [
		'{{WRAPPER}} .skin-default .bdt-event-item:hover, {{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner:hover' => 'border-color: {{VALUE}};',
		],
		]
		);

		$this->add_group_control(
		Group_Control_Box_Shadow::get_type(),
		[
		'name' => 'item_hover_shadow',
		'selector' => '{{WRAPPER}} .skin-default .bdt-event-item:hover, {{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner:hover',
		]
		);

		$this->add_responsive_control(
		'item_shadow_padding',
		[
		'label' => __('Match Padding', 'bdthemes-element-pack'),
		'description' => __('You have to add padding for matching overlaping hover shadow', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SLIDER,
		'range' => [
		'px' => [
		'min' => 0,
		'step' => 1,
		'max' => 50,
		],
		],
		'default' => [
		'size' => 10,
		],
		'selectors' => [
		'{{WRAPPER}} .swiper-carousel' => 'padding: {{SIZE}}{{UNIT}}; margin: 0 -{{SIZE}}{{UNIT}};',
		],
		]
		);

		$this->add_control(
		'item_hover_opacity',
		[
		'label' => esc_html__('Opacity', 'bdthemes-element-pack') . BDTEP_NC,
		'type' => Controls_Manager::SLIDER,
		'range' => [
		'px' => [
		'min' => 0,
		'step' => 0.1,
		'max' => 1,
		],
		],
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-item:hover' => 'opacity: {{SIZE}};',
		],
		]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
		'tab_item_active',
		[
		'label' => __('Active', 'bdthemes-element-pack') . BDTEP_NC,
		]
		);

		$this->add_control(
		'item_active_background',
		[
		'label' => __('Background', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .skin-default .bdt-event-item.swiper-slide-active, {{WRAPPER}} .bdt-event-calendar .bdt-event-item.swiper-slide-active .bdt-event-item-inner' => 'background-color: {{VALUE}};',
		],
		]
		);

		$this->add_control(
		'item_active_border_color',
		[
		'label' => __('Border Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'condition' => [
		'item_border_border!' => '',
		],
		'selectors' => [
		'{{WRAPPER}} .skin-default .bdt-event-item.swiper-slide-active, {{WRAPPER}} .bdt-event-calendar .bdt-event-item.swiper-slide-active .bdt-event-item-inner' => 'border-color: {{VALUE}};',
		],
		]
		);

		$this->add_group_control(
		Group_Control_Box_Shadow::get_type(),
		[
		'name' => 'item_active_shadow',
		'selector' => '{{WRAPPER}} .skin-default .bdt-event-item.swiper-slide-active, {{WRAPPER}} .bdt-event-calendar .bdt-event-item.swiper-slide-active .bdt-event-item-inner',
		]
		);

		$this->add_control(
		'item_active_opacity',
		[
		'label' => esc_html__('Opacity', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SLIDER,
		'range' => [
		'px' => [
		'min' => 0,
		'step' => 0.1,
		'max' => 1,
		],
		],
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-item.swiper-slide-active' => 'opacity: {{SIZE}};',
		],
		]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
			$this->end_controls_section();
		}
		if ( 'list' === $type ) {
		$this->start_controls_section(
				'section_style_item',
				[
					'label'     => __('Items', 'bdthemes-element-pack'),
					'tab'       => Controls_Manager::TAB_STYLE,
				]
			);

			$this->start_controls_tabs('tabs_item_style');

			$this->start_controls_tab(
				'tab_item_normal',
				[
					'label' => __('Normal', 'bdthemes-element-pack'),
				]
			);

			$this->add_control(
				'item_background',
				[
					'label'     => __('Background', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-list-item' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'item_shadow',
					'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-list-item',
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'        => 'item_border',
					'label'       => __('Border', 'bdthemes-element-pack'),
					'placeholder' => '1px',
					'default'     => '1px',
					'selector'    => '{{WRAPPER}} .bdt-event-calendar .bdt-event-list-item',
				]
			);

			$this->add_control(
				'item_border_radius',
				[
					'label'      => __('Border Radius', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-list-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_item_hover',
				[
					'label' => __('Hover', 'bdthemes-element-pack'),
				]
			);

			$this->add_control(
				'item_hover_background',
				[
					'label'     => __('Background', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-list-item:hover' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'item_hover_border_color',
				[
					'label'     => __('Border Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'condition' => [
						'border_border!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-list-item:hover' => 'border-color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'item_hover_shadow',
					'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-list-item:hover',
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();
			$this->end_controls_section();
		}
	}

	protected function register_events_calendar_style_image_controls( $type ) {
		if ( 'grid' === $type ) {
		$this->start_controls_section(
				'section_style_image',
				[
					'label'     => esc_html__('Image', 'bdthemes-element-pack'),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_image' => ['yes'],
					],
				]
			);

			$this->add_responsive_control(
				'image_padding',
				[
					'label'      => __('Padding', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					],
				]
			);

			$this->add_responsive_control(
				'image_margin',
				[
					'label'      => __('Margin', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					],
				]
			);

			$this->add_control(
				'image_border_radius',
				[
					'label'      => __('Image Radius', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'image_opacity',
				[
					'label'   => __('Opacity (%)', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SLIDER,
					'default' => [
						'size' => 1,
					],
					'range' => [
						'px' => [
							'max'  => 1,
							'min'  => 0.10,
							'step' => 0.01,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-image img' => 'opacity: {{SIZE}};',
					],
				]
			);

			$this->add_control(
				'image_hover_opacity',
				[
					'label'   => __('Hover Opacity (%)', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SLIDER,
					'default' => [
						'size' => 1,
					],
					'range' => [
						'px' => [
							'max'  => 1,
							'min'  => 0.10,
							'step' => 0.01,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner:hover .bdt-event-image img' => 'opacity: {{SIZE}};',
					],
				]
			);
			$this->end_controls_section();
		}
		if ( 'carousel' === $type ) {
		$this->start_controls_section(
		'section_style_image',
		[
		'label' => esc_html__('Image', 'bdthemes-element-pack'),
		'tab' => Controls_Manager::TAB_STYLE,
		'condition' => [
		'show_image' => ['yes'],
		],
		]
		);

		$this->add_responsive_control(
		'image_padding',
		[
		'label' => __('Padding', 'bdthemes-element-pack'),
		'type' => Controls_Manager::DIMENSIONS,
		'size_units' => ['px', '%', 'em'],
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
		],
		]
		);

		$this->add_responsive_control(
		'image_margin',
		[
		'label' => __('Margin', 'bdthemes-element-pack'),
		'type' => Controls_Manager::DIMENSIONS,
		'size_units' => ['px', '%', 'em'],
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
		],
		]
		);

		$this->add_control(
		'image_border_radius',
		[
		'label' => __('Image Radius', 'bdthemes-element-pack'),
		'type' => Controls_Manager::DIMENSIONS,
		'size_units' => ['px', '%'],
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		],
		]
		);

		$this->add_control(
		'image_opacity',
		[
		'label' => __('Opacity (%)', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SLIDER,
		'default' => [
		'size' => 1,
		],
		'range' => [
		'px' => [
		'max' => 1,
		'min' => 0.10,
		'step' => 0.01,
		],
		],
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-image img' => 'opacity: {{SIZE}};',
		],
		]
		);

		$this->add_control(
		'image_hover_opacity',
		[
		'label' => __('Hover Opacity (%)', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SLIDER,
		'default' => [
		'size' => 1,
		],
		'range' => [
		'px' => [
		'max' => 1,
		'min' => 0.10,
		'step' => 0.01,
		],
		],
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-item:hover .bdt-event-image img' => 'opacity: {{SIZE}};',
		],
		]
		);
			$this->end_controls_section();
		}
		if ( 'list' === $type ) {
		$this->start_controls_section(
				'section_style_image',
				[
					'label'     => esc_html__('Image', 'bdthemes-element-pack'),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_image' => ['yes'],
					],
				]
			);

			$this->add_responsive_control(
				'image_padding',
				[
					'label'      => __('Padding', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					],
				]
			);

			$this->add_responsive_control(
				'image_margin',
				[
					'label'      => __('Margin', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					],
				]
			);

			$this->add_control(
				'image_border_radius',
				[
					'label'      => __('Image Radius', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'image_opacity',
				[
					'label'   => __('Opacity (%)', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SLIDER,
					'default' => [
						'size' => 1,
					],
					'range' => [
						'px' => [
							'max'  => 1,
							'min'  => 0.10,
							'step' => 0.01,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-image img' => 'opacity: {{SIZE}};',
					],
				]
			);

			$this->add_control(
				'image_hover_opacity',
				[
					'label'   => __('Hover Opacity (%)', 'bdthemes-element-pack'),
					'type'    => Controls_Manager::SLIDER,
					'default' => [
						'size' => 1,
					],
					'range' => [
						'px' => [
							'max'  => 1,
							'min'  => 0.10,
							'step' => 0.01,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner:hover .bdt-event-image img' => 'opacity: {{SIZE}};',
					],
				]
			);
			$this->end_controls_section();
		}
	}

	protected function register_events_calendar_style_title_controls( $type ) {
		if ( 'grid' === $type ) {
		$this->start_controls_section(
				'section_style_title',
				[
					'label'     => esc_html__('Title', 'bdthemes-element-pack'),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_title' => ['yes'],
					],
				]
			);

			$this->add_control(
				'title_color',
				[
					'label'     => esc_html__('Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-title' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'title_hover_color',
				[
					'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-title:hover' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'title_typography',
					'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
					'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-title-wrap',
				]
			);

			$this->add_control(
				'title_separator_color',
				[
					'label'     => esc_html__('Separator Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-intro .bdt-event-title-wrap, {{WRAPPER}} .bdt-event-calendar .bdt-event-intro' => 'border-color: {{VALUE}};',
					],
					'condition' => [
						'_skin!' => 'annal',
					],
				]
			);
			$this->end_controls_section();
		}
		if ( 'carousel' === $type ) {
		$this->start_controls_section(
		'section_style_title',
		[
		'label' => esc_html__('Title', 'bdthemes-element-pack'),
		'tab' => Controls_Manager::TAB_STYLE,
		'condition' => [
		'show_title' => ['yes'],
		],
		]
		);

		$this->add_control(
		'title_color',
		[
		'label' => esc_html__('Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-title' => 'color: {{VALUE}};',
		],
		]
		);

		$this->add_control(
		'title_hover_color',
		[
		'label' => esc_html__('Hover Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-title:hover' => 'color: {{VALUE}};',
		],
		]
		);

		$this->add_group_control(
		Group_Control_Typography::get_type(),
		[
		'name' => 'title_typography',
		'label' => esc_html__('Typography', 'bdthemes-element-pack'),
		'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-title-wrap',
		]
		);

		$this->add_control(
		'title_separator_color',
		[
		'label' => esc_html__('Separator Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-intro .bdt-event-title-wrap, {{WRAPPER}} .bdt-event-calendar .bdt-event-intro' => 'border-color: {{VALUE}};',
		],
		'condition' => [
		'_skin!' => 'fable',
		],
		]
		);
			$this->end_controls_section();
		}
		if ( 'list' === $type ) {
		$this->start_controls_section(
				'section_style_title',
				[
					'label'     => esc_html__('Title', 'bdthemes-element-pack'),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_title' => ['yes'],
					],
				]
			);

			$this->add_control(
				'title_color',
				[
					'label'     => esc_html__('Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-title' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'title_hover_color',
				[
					'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-title:hover' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'title_typography',
					'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
					'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-title-wrap',
				]
			);

			$this->add_responsive_control(
				'title_spacing',
				[
					'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
					'type'  => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-intro, {{WRAPPER}} .bdt-event-grid-skin-annal .bdt-event-title-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);
			$this->end_controls_section();
		}
	}

	protected function register_events_calendar_style_date_controls( $type ) {
		if ( 'grid' === $type ) {
		$this->start_controls_section(
				'section_style_date',
				[
					'label'     => esc_html__('Date', 'bdthemes-element-pack'),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_date' => ['yes'],
					],
				]
			);

			$this->add_control(
				'day_color',
				[
					'label'     => esc_html__('Day Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-date a .bdt-event-day' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'day_typography',
					'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
					'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-date a .bdt-event-day',
				]
			);

			$this->add_control(
				'date_color',
				[
					'label'     => esc_html__('Month Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-date a' => 'color: {{VALUE}};',
					],
					'condition' => [
						'_skin!' => ['annal'],
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'date_typography',
					'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
					'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-date',
					'condition' => [
						'_skin!' => ['annal'],
					],
				]
			);
			$this->end_controls_section();
		}
		if ( 'carousel' === $type ) {
		$this->start_controls_section(
		'section_style_date',
		[
		'label' => esc_html__('Date', 'bdthemes-element-pack'),
		'tab' => Controls_Manager::TAB_STYLE,
		'condition' => [
		'show_date' => ['yes'],
		],
		]
		);

		$this->add_control(
		'day_color',
		[
		'label' => esc_html__('Day Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-date a .bdt-event-day' => 'color: {{VALUE}};',
		],
		]
		);

		$this->add_group_control(
		Group_Control_Typography::get_type(),
		[
		'name' => 'day_typography',
		'label' => esc_html__('Typography', 'bdthemes-element-pack'),
		'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-date a .bdt-event-day',
		]
		);

		$this->add_control(
		'date_color',
		[
		'label' => esc_html__('Month Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-date a' => 'color: {{VALUE}};',
		],
		'condition' => [
		'_skin!' => ['fable'],
		],
		]
		);

		$this->add_group_control(
		Group_Control_Typography::get_type(),
		[
		'name' => 'date_typography',
		'label' => esc_html__('Typography', 'bdthemes-element-pack'),
		'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-date',
		'condition' => [
		'_skin!' => ['fable'],
		],
		]
		);

		$this->end_controls_section();

		// Time Style controller
			$this->end_controls_section();
		}
		if ( 'list' === $type ) {
		$this->start_controls_section(
				'section_style_date',
				[
					'label'     => esc_html__('Date', 'bdthemes-element-pack'),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_date' => ['yes'],
					],
				]
			);

			$this->add_control(
				'day_color',
				[
					'label'     => esc_html__('Day Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-date a .bdt-event-day' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'day_typography',
					'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
					'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-date a .bdt-event-day',
				]
			);
			$this->end_controls_section();
		}
	}

	protected function register_events_calendar_style_time_controls( $type ) {
		if ( 'grid' === $type ) {
		$this->start_controls_section(
				'section_style_time',
				[
					'label'     => esc_html__('Time', 'bdthemes-element-pack'),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_time' => ['yes'],
					],
				]
			);

			$this->add_control(
				'time_color',
				[
					'label'     => esc_html__('Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-time' => 'color: {{VALUE}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'time_typography',
					'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
					'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-time',
				]
			);

			$this->add_responsive_control(
				'time_margin',
				[
					'label'      => __('Margin', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-time' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					],
				]
			);
			$this->end_controls_section();
		}
		if ( 'carousel' === $type ) {
		$this->start_controls_section(
		'section_style_time',
		[
		'label' => esc_html__('Time', 'bdthemes-element-pack'),
		'tab' => Controls_Manager::TAB_STYLE,
		'condition' => [
		'show_time' => ['yes'],
		],
		]
		);


		$this->add_control(
		'time_color',
		[
		'label' => esc_html__('Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-time ' => 'color: {{VALUE}};',
		],
		'condition' => [
		'_skin!' => ['fable'],
		],
		]
		);

		$this->add_group_control(
		Group_Control_Typography::get_type(),
		[
		'name' => 'time_typography',
		'label' => esc_html__('Typography', 'bdthemes-element-pack'),
		'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-time',
		'condition' => [
		'_skin!' => ['fable'],
		],
		]
		);

		$this->add_responsive_control(
		'time_margin',
		[
		'label' => __('Margin', 'bdthemes-element-pack'),
		'type' => Controls_Manager::DIMENSIONS,
		'size_units' => ['px', '%', 'em'],
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-time' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
		],
		]
		);
			$this->end_controls_section();
		}
	}

	protected function register_events_calendar_style_excerpt_controls( $type ) {
		if ( 'grid' === $type ) {
		$this->start_controls_section(
				'section_style_excerpt',
				[
					'label'     => esc_html__('Excerpt', 'bdthemes-element-pack'),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_excerpt' => ['yes'],
					],
				]
			);

			$this->add_control(
				'excerpt_color',
				[
					'label'     => esc_html__('Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-excerpt' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'excerpt_typography',
					'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
					'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-excerpt',
				]
			);

			// Margin control added here
			$this->add_responsive_control(
				'title_spacing',
				[
					'label'      => __('Margin', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-excerpt' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					],
				]
			);
			$this->end_controls_section();
		}
		if ( 'carousel' === $type ) {
		$this->start_controls_section(
		'section_style_excerpt',
		[
		'label' => esc_html__('Excerpt', 'bdthemes-element-pack'),
		'tab' => Controls_Manager::TAB_STYLE,
		'condition' => [
		'show_excerpt' => ['yes'],
		],
		]
		);

		$this->add_control(
		'excerpt_color',
		[
		'label' => esc_html__('Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-excerpt' => 'color: {{VALUE}};',
		],
		]
		);

		$this->add_group_control(
		Group_Control_Typography::get_type(),
		[
		'name' => 'excerpt_typography',
		'label' => esc_html__('Typography', 'bdthemes-element-pack'),
		'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-excerpt',
		]
		);

		$this->add_responsive_control(
				'title_spacing',
		[
		'label' => __('Margin', 'bdthemes-element-pack'),
		'type' => Controls_Manager::DIMENSIONS,
		'size_units' => ['px', '%', 'em'],
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-excerpt' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
		],
		]
		);
			$this->end_controls_section();
		}
		if ( 'list' === $type ) {
		$this->start_controls_section(
				'section_style_excerpt',
				[
					'label'     => esc_html__('Excerpt', 'bdthemes-element-pack'),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_excerpt' => ['yes'],
					],
				]
			);

			$this->add_control(
				'excerpt_color',
				[
					'label'     => esc_html__('Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-excerpt' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'excerpt_typography',
					'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
					'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-excerpt',
				]
			);
			$this->end_controls_section();
		}
	}

	protected function register_events_calendar_style_meta_controls( $type ) {
		if ( 'grid' === $type ) {
		$this->start_controls_section(
				'section_style_meta',
				[
					'label'     => esc_html__('Meta', 'bdthemes-element-pack'),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_meta' => ['yes'],
						'_skin!'	=> 'acara',
					],
				]
			);

			$this->add_control(
				'meta_color',
				[
					'label'     => esc_html__('Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-meta .bdt-event-price a' => 'color: {{VALUE}};',
					],
					'condition' => [
						'show_meta_cost' => ['yes'],
					],
				]
			);

			$this->add_control(
				'meta_icon_color',
				[
					'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-meta .bdt-address-website-icon a, {{WRAPPER}} .skin-annal .bdt-event-meta .bdt-more-icon a' => 'color: {{VALUE}};',
					],
					'condition' => [
						'show_meta_more_btn' => ['yes'],
					],
				]
			);

			$this->add_control(
				'meta_icon_border_color',
				[
					'label'     => esc_html__('Icon Border Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .skin-annal .bdt-event-meta .bdt-more-icon a' => 'border-color: {{VALUE}};',
					],
					'condition' => [
						'_skin!' => [''],
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'meta_typography',
					'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
					'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-meta a',
				]
			);

			$this->add_responsive_control(
				'meta_padding',
				[
					'label'      => __('Meta Padding', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-meta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					],
				]
			);

			$this->add_control(
				'meta_border_top_color',
				[
					'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-meta' => 'border-color: {{VALUE}};',
					],
				]
			);
			$this->end_controls_section();
		}
		if ( 'carousel' === $type ) {
		$this->start_controls_section(
		'section_style_meta',
		[
		'label' => esc_html__('Meta', 'bdthemes-element-pack'),
		'tab' => Controls_Manager::TAB_STYLE,
		'condition' => [
		'show_meta' => ['yes'],
		'_skin!' => 'altra',
		],
		]
		);

		$this->add_control(
		'meta_color',
		[
		'label' => esc_html__('Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-meta .bdt-event-price a' => 'color: {{VALUE}};',
		],
		'condition' => [
		'show_meta_cost' => ['yes'],
		],
		]
		);

		$this->add_control(
		'meta_icon_color',
		[
		'label' => esc_html__('Icon Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-meta .bdt-address-website-icon a, {{WRAPPER}} .skin-fable .bdt-event-meta .bdt-more-icon a' => 'color: {{VALUE}};',
		],
		'condition' => [
		'show_meta_more_btn' => ['yes'],
		],
		]
		);

		$this->add_control(
		'meta_icon_border_color',
		[
		'label' => esc_html__('Icon Border Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .skin-fable .bdt-event-meta .bdt-more-icon a' => 'border-color: {{VALUE}};',
		],
		'condition' => [
		'_skin!' => [''],
		],
		]
		);

		$this->add_group_control(
		Group_Control_Typography::get_type(),
		[
		'name' => 'meta_typography',
		'label' => esc_html__('Typography', 'bdthemes-element-pack'),
		'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-meta a',
		]
		);

		$this->add_responsive_control(
		'meta_padding',
		[
		'label' => __('Meta Padding', 'bdthemes-element-pack'),
		'type' => Controls_Manager::DIMENSIONS,
		'size_units' => ['px', '%', 'em'],
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-meta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
		],
		]
		);

		$this->add_control(
		'meta_border_top_color',
		[
		'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-meta' => 'border-color: {{VALUE}};',
		],
		]
		);
			$this->end_controls_section();
		}
		if ( 'list' === $type ) {
		$this->start_controls_section(
				'section_style_meta',
				[
					'label'     => esc_html__('Meta', 'bdthemes-element-pack'),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_meta' => ['yes'],
					],
				]
			);

			$this->add_control(
				'meta_color',
				[
					'label'     => esc_html__('Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-meta .bdt-event-price a' => 'color: {{VALUE}};',
					],
					'condition' => [
						'show_meta_cost' => ['yes'],
					],
				]
			);

			$this->add_control(
				'meta_icon_color',
				[
					'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-meta .bdt-address-website-icon a, {{WRAPPER}} .bdt-event-grid-skin-annal .bdt-event-meta .bdt-more-icon a' => 'color: {{VALUE}};',
					],
					'conditions' => [
						'relation' => 'or',
						'terms' => [
							[
								'name'     => 'show_meta_website',
								'value'    => 'yes',
							],
							[
								'name'     => 'show_meta_location',
								'value'    => 'yes',
							],
						],
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'meta_typography',
					'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
					'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-meta a',
				]
			);

			$this->add_responsive_control(
				'meta_padding',
				[
					'label'      => __('Meta Padding', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-meta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					],
				]
			);
			$this->end_controls_section();
		}
	}

	protected function register_events_calendar_style_meta_price_controls( $type ) {
		if ( 'grid' === $type ) {
		$this->start_controls_section(
				'section_style_meta_price',
				[
					'label'     => esc_html__('Meta Price', 'bdthemes-element-pack'),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_meta_cost' => ['yes'],
						'_skin'	=> 'acara',
					],
				]
			);

			$this->start_controls_tabs('tabs_meta_price_style');

			$this->start_controls_tab(
				'tab_meta_price_normal',
				[
					'label' => __('Normal', 'bdthemes-element-pack'),
				]
			);

			$this->add_control(
				'meta_price_icon_color',
				[
					'label'     => esc_html__('Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-price a svg *' => 'fill: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'meta_price_icon_background_color',
				[
					'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-price a' => 'background: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'        => 'meta_price_border',
					'label'       => __('Border', 'bdthemes-element-pack'),
					'selector'    => '{{WRAPPER}} .bdt-event-calendar .bdt-event-price a',
				]
			);

			$this->add_control(
				'meta_price_border_radius',
				[
					'label'      => __('Border Radius', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-price a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'meta_price_padding',
				[
					'label'      => __('Padding', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-price a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					],
				]
			);

			$this->add_responsive_control(
				'meta_price_icon_size',
				[
					'label'       => __('Icon Size', 'bdthemes-element-pack'),
					'type'        => Controls_Manager::SLIDER,
					'range'       => [
						'px' => [
							'min'  => 0,
							'max'  => 50,
						]
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-price a svg' => 'width: {{SIZE}}{{UNIT}};'
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_meta_price_hover',
				[
					'label' => __('Hover', 'bdthemes-element-pack'),
				]
			);

			$this->add_control(
				'meta_price_hover_color',
				[
					'label'     => esc_html__('Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-price a .bdt-price-amount' => 'color: {{VALUE}};',
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner:hover .bdt-event-price a svg *' => 'fill: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'meta_price_hover_background_color',
				[
					'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner:hover .bdt-event-price a' => 'background: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'price_border_hover_color',
				[
					'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner:hover .bdt-event-price a' => 'border-color: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'meta_price_padding_right',
				[
					'label'       => __('Match Padding', 'bdthemes-element-pack'),
					'type'        => Controls_Manager::SLIDER,
					'range'       => [
						'px' => [
							'min'  => 0,
							'max'  => 100,
						]
					],
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner:hover .bdt-event-price a' => 'padding-right: {{SIZE}}{{UNIT}};'
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'meta_price_typography',
					'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
					'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-price a .bdt-price-amount',
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();
			$this->end_controls_section();
		}
		if ( 'carousel' === $type ) {
		$this->start_controls_section(
		'section_style_meta_price',
		[
		'label' => esc_html__('Meta Price', 'bdthemes-element-pack'),
		'tab' => Controls_Manager::TAB_STYLE,
		'condition' => [
		'show_meta_cost' => ['yes'],
		'_skin' => 'altra',
		],
		]
		);

		$this->start_controls_tabs('tabs_meta_price_style');

		$this->start_controls_tab(
		'tab_meta_price_normal',
		[
		'label' => __('Normal', 'bdthemes-element-pack'),
		]
		);

		$this->add_control(
		'meta_price_icon_color',
		[
		'label' => esc_html__('Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-price a svg *' => 'fill: {{VALUE}};',
		],
		]
		);

		$this->add_control(
		'meta_price_icon_background_color',
		[
		'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-price a' => 'background: {{VALUE}};',
		],
		]
		);

		$this->add_group_control(
		Group_Control_Border::get_type(),
		[
		'name' => 'meta_price_border',
		'label' => __('Border', 'bdthemes-element-pack'),
		'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-price a',
		]
		);

		$this->add_control(
		'meta_price_border_radius',
		[
		'label' => __('Border Radius', 'bdthemes-element-pack'),
		'type' => Controls_Manager::DIMENSIONS,
		'size_units' => ['px', '%'],
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-price a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		],
		]
		);

		$this->add_responsive_control(
		'meta_price_padding',
		[
		'label' => __('Padding', 'bdthemes-element-pack'),
		'type' => Controls_Manager::DIMENSIONS,
		'size_units' => ['px', '%', 'em'],
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-price a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
		],
		]
		);

		$this->add_responsive_control(
		'meta_price_icon_size',
		[
		'label' => __('Icon Size', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SLIDER,
		'range' => [
		'px' => [
		'min' => 0,
		'max' => 50,
		],
		],
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-price a svg' => 'width: {{SIZE}}{{UNIT}};',
		],
		]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
		'tab_meta_price_hover',
		[
		'label' => __('Hover', 'bdthemes-element-pack'),
		]
		);

		$this->add_control(
		'meta_price_hover_color',
		[
		'label' => esc_html__('Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-price a .bdt-price-amount' => 'color: {{VALUE}};',
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner:hover .bdt-event-price a svg *' => 'fill: {{VALUE}};',
		],
		]
		);

		$this->add_control(
		'meta_price_hover_background_color',
		[
		'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner:hover .bdt-event-price a' => 'background: {{VALUE}};',
		],
		]
		);

		$this->add_control(
		'price_border_hover_color',
		[
		'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner:hover .bdt-event-price a' => 'border-color: {{VALUE}};',
		],
		]
		);

		$this->add_responsive_control(
		'meta_price_padding_right',
		[
		'label' => __('Match Padding', 'bdthemes-element-pack'),
		'type' => Controls_Manager::SLIDER,
		'range' => [
		'px' => [
		'min' => 0,
		'max' => 100,
		],
		],
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-event-item-inner:hover .bdt-event-price a' => 'padding-right: {{SIZE}}{{UNIT}};',
		],
		]
		);

		$this->add_group_control(
		Group_Control_Typography::get_type(),
		[
		'name' => 'meta_price_typography',
		'label' => esc_html__('Typography', 'bdthemes-element-pack'),
		'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-event-price a .bdt-price-amount',
		]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
			$this->end_controls_section();
		}
	}

	protected function register_events_calendar_style_address_controls( $type ) {
		if ( 'grid' === $type ) {
		$this->start_controls_section(
				'section_style_address_website',
				[
					'label'     => esc_html__('Address', 'bdthemes-element-pack'),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'_skin' => ['annal', 'acara'],
					],
				]
			);

			$this->add_control(
				'address_website_icon_color',
				[
					'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-address-website-icon a' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'address_website_icon_hover_color',
				[
					'label'     => esc_html__('Icon Hover Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-address-website-icon a:hover' => 'color: {{VALUE}};',
					],
					'condition' => [
						'_skin' => ['acara'],
					],
				]
			);

			$this->add_control(
				'address_website_icon_background_color',
				[
					'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-address-website-icon a' => 'background-color: {{VALUE}};',
					],
					'condition' => [
						'_skin!' => ['acara'],
					],
				]
			);

			$this->add_control(
				'address_website_icon_border_color',
				[
					'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-address-website-icon a' => 'border-color: {{VALUE}};',
					],
					'condition' => [
						'_skin!' => ['acara'],
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'address_website_typography',
					'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
					'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-address-website-icon a',
				]
			);

			$this->add_responsive_control(
				'address_website_padding',
				[
					'label'      => __('Padding', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-event-calendar .bdt-address-website-icon a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					],
					'condition' => [
						'_skin!' => ['acara'],
					],
				]
			);

			$this->end_controls_section();
			$this->end_controls_section();
		}
		if ( 'carousel' === $type ) {
		$this->start_controls_section(
		'section_style_address_website',
		[
		'label' => esc_html__('Address', 'bdthemes-element-pack'),
		'tab' => Controls_Manager::TAB_STYLE,
		'condition' => [
		'_skin!' => [''],
		],
		]
		);

		$this->add_control(
		'address_website_icon_color',
		[
		'label' => esc_html__('Icon Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-address-website-icon a' => 'color: {{VALUE}};',
		],
		]
		);

		$this->add_control(
		'address_website_icon_hover_color',
		[
		'label' => esc_html__('Icon Hover Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .bdt-event-calendar .bdt-address-website-icon a:hover' => 'color: {{VALUE}};',
		],
		'condition' => [
		'_skin' => ['altra'],
		],
		]
		);

		$this->add_control(
		'address_website_icon_background_color',
		[
		'label' => esc_html__('Background Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .skin-fable .bdt-address-website-icon a' => 'background-color: {{VALUE}};',
		],
		'condition' => [
		'_skin!' => ['altra'],
		],
		]
		);

		$this->add_control(
		'address_website_icon_border_color',
		[
		'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
		'type' => Controls_Manager::COLOR,
		'selectors' => [
		'{{WRAPPER}} .skin-fable .bdt-address-website-icon a' => 'border-color: {{VALUE}};',
		],
		'condition' => [
		'_skin!' => ['altra'],
		],
		]
		);

		$this->add_group_control(
		Group_Control_Typography::get_type(),
		[
		'name' => 'address_website_typography',
		'label' => esc_html__('Typography', 'bdthemes-element-pack'),
		'selector' => '{{WRAPPER}} .bdt-event-calendar .bdt-address-website-icon a',
		]
		);

		$this->add_responsive_control(
		'address_website_padding',
		[
		'label' => __('Padding', 'bdthemes-element-pack'),
		'type' => Controls_Manager::DIMENSIONS,
		'size_units' => ['px', '%', 'em'],
		'selectors' => [
		'{{WRAPPER}} .skin-fable .bdt-address-website-icon a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
		],
		'condition' => [
		'_skin!' => ['altra'],
		],
		]
		);

		$this->end_controls_section();

		//Navigation Style
			$this->end_controls_section();
		}
		if ( 'list' === $type ) {
		$this->start_controls_section(
				'section_style_address_website',
				[
					'label'     => esc_html__('Address', 'bdthemes-element-pack'),
					'tab'       => Controls_Manager::TAB_STYLE,
					'condition' => [
						'_skin!' => [''],
					],
				]
			);

			$this->add_control(
				'address_website_icon_color',
				[
					'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-grid-skin-annal .bdt-address-website-icon a' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'address_website_icon_background_color',
				[
					'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-grid-skin-annal .bdt-address-website-icon a' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'address_website_icon_border_color',
				[
					'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-event-grid-skin-annal .bdt-address-website-icon a' => 'border-color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'address_website_typography',
					'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
					'selector' => '{{WRAPPER}} .bdt-event-grid-skin-annal .bdt-address-website-icon a',
				]
			);

			$this->add_responsive_control(
				'address_website_padding',
				[
					'label'      => __('Padding', 'bdthemes-element-pack'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%', 'em'],
					'selectors'  => [
						'{{WRAPPER}} .bdt-event-grid-skin-annal .bdt-address-website-icon a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					],
				]
			);

			$this->end_controls_section();
			$this->end_controls_section();
		}
	}

	protected function get_events_calendar_events( $settings, $args = [] ) {
		$start_date = ( 'custom' == $settings['start_date'] ) ? $settings['custom_start_date'] : $settings['start_date'];
		$end_date   = ( 'custom' == $settings['end_date'] ) ? $settings['custom_end_date'] : $settings['end_date'];
		$query_args = array_filter( [
			'start_date' => $start_date, 'end_date' => $end_date,
			'orderby' => $settings['orderby'], 'order' => $settings['order'],
			'eventDisplay' => ( 'custom' == $settings['start_date'] or 'custom' == $settings['end_date'] ) ? 'custom' : 'all',
			'posts_per_page' => $settings['limit'],
		] );
		if ( ! empty( $args['include_upcoming'] ) && 'upcoming_events' === $settings['source'] ) {
			$context = tribe( 'context' );
			$display = $context->get( 'event_display' );
			$date_pivot_key = 'past' === $display ? 'starts_before' : 'starts_after';
			$query_args[ $date_pivot_key ] = 'now';
		}
		if ( 'by_name' === $settings['source'] && ! empty( $settings['event_categories'] ) ) {
			$query_args['event_category'] = $settings['event_categories'];
		}
		return tribe_get_events( $query_args );
	}

	protected function render_events_calendar_image() {

		$settings = $this->get_settings_for_display();

		if (!$this->get_settings('show_image')) {
			return;
		}

		$settings['image'] = [
			'id' => get_post_thumbnail_id(),
		];

		$image_html        = Group_Control_Image_Size::get_attachment_image_html($settings, 'image');
		$placeholder_image_src = Utils::get_placeholder_image_src();

		if (!$image_html) {
			$image_html = '<img src="' . esc_url($placeholder_image_src) . '" alt="' . get_the_title() . '">';
		}

		?>

		<div class="bdt-event-image bdt-background-cover">
			<a href="<?php echo ($settings['anchor_link'] == 'yes') ? esc_url(the_permalink()) : 'javascript:void(0);'; ?>" 
			title="<?php echo esc_html(get_the_title()); ?>">
				<img src="<?php echo esc_url(wp_get_attachment_image_url(get_post_thumbnail_id(), $settings['image_size'])); ?>" 
				alt="<?php echo esc_html(get_the_title()); ?>">
			</a>
		</div>
	<?php
	}

	protected function render_events_calendar_title() {

		$settings = $this->get_settings_for_display();
		if (!$this->get_settings('show_title')) {
			return;
		}
	?>

		<h3 class="bdt-event-title-wrap">
			<a href="<?php echo ($settings['anchor_link'] == 'yes') ? esc_url( get_permalink()) : 'javascript:void(0);'; ?>" class="bdt-event-title">
				<?php the_title() ?>
			</a>
		</h3>
	<?php
	}

	protected function render_events_calendar_time() {

		if (!$this->get_settings('show_time')) {
			return;
		}

		$start_time = tribe_get_start_date(null, false, 'g:i a');
		$end_time = tribe_get_end_date(null, false, 'g:i a');

	?>
		<div class="bdt-event-time" title="<?php esc_html_e('Start Time:', 'bdthemes-element-pack'); echo esc_html($start_time); ?>  - <?php esc_html_e('End Time:', 'bdthemes-element-pack'); ?> <?php	echo esc_html($end_time);?> ">
			<?php echo esc_html($start_time); ?> - <?php echo esc_html($end_time); ?>
		</div>
		<?php
	}

	protected function render_events_calendar_excerpt( $post ) {

		if (!$this->get_settings('show_excerpt')) {
			return;
		}

	?>
		<div class="bdt-event-excerpt">
			<?php

			if (!$post->post_excerpt) {
				echo wp_kses_post(strip_shortcodes(wp_trim_words($post->post_content, $this->get_settings('excerpt_length'))));
			} else {
				echo wp_kses_post(strip_shortcodes(wp_trim_words($post->post_excerpt, $this->get_settings('excerpt_length'))));
			}

			?>

		</div>
	<?php
	}

	protected function render_events_calendar_date( $type = 'grid' ) {
		if ( 'list' === $type ) {

			if (!$this->get_settings('show_date')) {
				return;
			}

			$start_datetime = tribe_get_start_date();
			$end_datetime = tribe_get_end_date();

			$event_date = tribe_get_start_date(null, false);

		?>
			<div class="bdt-event-date">
				<a href="javascript:void(0);" title="<?php esc_html_e('Start Date:', 'bdthemes-element-pack');
									echo esc_html($start_datetime); ?>  - <?php esc_html_e('End Date:', 'bdthemes-element-pack');
																																	echo esc_html($end_datetime); ?>">
					<span class="bdt-event-day">
						<?php echo esc_html($event_date); ?>
					</span>
				</a>
			</div>
		<?php
			return;
		}

		if (!$this->get_settings('show_date')) {
			return;
		}

		$start_datetime = tribe_get_start_date();
		$end_datetime = tribe_get_end_date();

		$event_day = tribe_get_start_date(null, false, 'j');
		$event_month = tribe_get_start_date(null, false, 'M');

	?>
		<span class="bdt-event-date">
			<a href="javascript:void(0);" title="<?php esc_html_e('Start Date:', 'bdthemes-element-pack');
								echo esc_html($start_datetime); ?>  - <?php esc_html_e('End Date:', 'bdthemes-element-pack');
																		echo esc_html($end_datetime); ?>">
				<span class="bdt-event-day">
					<?php echo esc_html(str_pad($event_day, 2, '0', STR_PAD_LEFT)); ?>
				</span>
				<span>
					<?php echo esc_html($event_month); ?>
				</span>
			</a>
		</span>
	<?php
	}

	protected function render_events_calendar_meta( $type = 'grid' ) {
		if ( 'list' === $type ) {

			$settings = $this->get_settings_for_display();
			if (!$this->get_settings('show_meta')) {
				return;
			}

			$cost    = ($settings['show_meta_cost']) ? tribe_get_formatted_cost() : '';

			$address = ($settings['show_meta_location']) ? tribe_address_exists() : '';

			$website = ($settings['show_meta_website']) ? tribe_get_event_website_url() : '';


		?>

			<?php if (!empty($cost) or $address or !empty($website)) : ?>
				<div class="bdt-event-meta bdt-grid">

					<?php if (!empty($cost)) : ?>
						<div class="bdt-width-auto">
							<div class="bdt-event-price">
								<a href="javascript:void(0);"><?php esc_html_e('Cost:', 'bdthemes-element-pack'); ?></a>
								<a href="javascript:void(0);"><?php echo esc_html($cost); ?></a>
							</div>
						</div>
					<?php endif; ?>

					<?php if (!empty($website) or $address) : ?>
						<div class="bdt-width-expand bdt-text-right">
							<div class="bdt-address-website-icon">

								<?php if (!empty($website)) : ?>
									<a href="<?php echo esc_url($website); ?>" target="_blank" class="ep-icon-earth" aria-hidden="true"></a>
								<?php endif; ?>

								<?php if ($address) : ?>
									<a href="javascript:void(0);" bdt-tooltip="<?php echo esc_html(tribe_get_full_address()); ?>" class="ep-icon-location" aria-hidden="true"></a>
								<?php endif; ?>

							</div>

						</div>
					<?php endif; ?>

				</div>
			<?php endif; ?>

		<?php
			return;
		}

		$settings = $this->get_settings_for_display();
		if (!$this->get_settings('show_meta')) {
			return;
		}

		$cost    = ($settings['show_meta_cost']) ? tribe_get_formatted_cost() : '';

		$address = ($settings['show_meta_location']) ? tribe_address_exists() : '';

		$website = ($settings['show_meta_website']) ? tribe_get_event_website_url() : '';


	?>

		<?php if (!empty($cost) or $address or !empty($website)) : ?>
			<div class="bdt-event-meta bdt-grid">

				<?php if (!empty($cost)) : ?>
					<div class="bdt-width-auto bdt-padding-remove">
						<div class="bdt-event-price">
							<a href="javascript:void(0);"><?php esc_html_e('Cost:', 'bdthemes-element-pack'); ?></a>
							<a href="javascript:void(0);"><?php echo esc_html($cost); ?></a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (!empty($website) or $address) : ?>
					<div class="bdt-width-expand bdt-text-right">
						<div class="bdt-address-website-icon">

							<?php if (!empty($website)) : ?>
								<a href="<?php echo esc_url($website); ?>" target="_blank" class="ep-icon-earth" aria-hidden="true"></a>
							<?php endif; ?>

							<?php if ($address) : ?>
								<a href="javascript:void(0);" bdt-tooltip="<?php echo esc_html(tribe_get_full_address()); ?>" class="ep-icon-location" aria-hidden="true"></a>
							<?php endif; ?>

						</div>

					</div>
				<?php endif; ?>

			</div>
		<?php endif; ?>

	<?php
	}

	protected function render_events_calendar_grid_header( $skin_name = 'default' ) {


		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

		$desktop_cols = isset($settings['columns']) ? $settings['columns'] : 3;
		$tablet_cols  = isset($settings['columns_tablet']) ? $settings['columns_tablet'] : 2;
		$mobile_cols  = isset($settings['columns_mobile']) ? $settings['columns_mobile'] : 1;

		$this->add_render_attribute('event-grid', 'id', $id);
		$this->add_render_attribute('event-grid', 'class', ['bdt-event-grid', 'bdt-event-calendar', 'skin-' . $skin_name]);

		if ('yes' == $settings['match_height']) {
			$this->add_render_attribute('event-grid', 'bdt-height-match', 'target: .bdt-event-item-inner');
		}

	?>
		<div <?php $this->print_render_attribute_string('event-grid'); ?>>
			<div class="bdt-grid bdt-grid-<?php echo esc_attr($settings['column_gap']); ?> bdt-child-width-1-<?php echo esc_attr($mobile_cols); ?> bdt-child-width-1-<?php echo esc_attr($tablet_cols); ?>@s bdt-child-width-1-<?php echo esc_attr($desktop_cols); ?>@l" bdt-grid>

			<?php
	}

	protected function render_events_calendar_grid_footer() {

			$settings = $this->get_settings_for_display();

			?>

			</div>
		</div>
	<?php
	}

	protected function render_events_calendar_grid_loop_item( $post ) {

			$settings = $this->get_settings_for_display();

	?>
		<div class="bdt-event-grid-item">

			<div class="bdt-event-item-inner">

				<?php $this->render_events_calendar_image(); ?>

				<div class="bdt-event-content">
					<div class="bdt-event-intro">

						<?php $this->render_events_calendar_date(); ?>
						<!-- Added New Header Wrapper -->
						<div class="bdt-event-header">
							<?php $this->render_events_calendar_title(); ?>
							<?php $this->render_events_calendar_time(); ?>
						</div>	

					</div>

					<?php $this->render_events_calendar_excerpt( $post ); ?>

				</div>

				<?php $this->render_events_calendar_meta(); ?>

			</div>

		</div>
	<?php
	}

	protected function render_events_calendar_carousel_header( $skin_name = 'default' ) {

	$settings = $this->get_settings_for_display();

	//Global Function
	$this->render_swiper_header_attribute('event-carousel');

	$this->add_render_attribute('carousel', 'class', ['bdt-event-carousel', 'bdt-event-calendar', 'skin-' . $skin_name]);

	$this->add_render_attribute('event-carousel-wrapper', 'class', 'swiper-wrapper');

	if ('yes' == $settings['match_height']) {
	$this->add_render_attribute('event-carousel-wrapper', 'bdt-height-match', 'target: .bdt-event-content-wrap');
	}

	if ('yes' == $settings['skin_match_height']) {
	$this->add_render_attribute('event-carousel-wrapper', 'bdt-height-match', 'target: .bdt-event-excerpt');
	}

	?>
		<div <?php $this->print_render_attribute_string('carousel'); ?>>
			<div <?php $this->print_render_attribute_string('swiper'); ?>>
				<div <?php $this->print_render_attribute_string('event-carousel-wrapper'); ?>>
				<?php
	}

	protected function render_events_calendar_carousel_loop_item( $post ) {

	$settings = $this->get_settings_for_display();

	?>
				<div class="bdt-event-item swiper-slide">

					<div class="bdt-event-content-wrap">

						<?php $this->render_events_calendar_image();?>

						<div class="bdt-event-content">
							<div class="bdt-event-intro">

								<?php $this->render_events_calendar_date();?>
								<!-- Added New Header Wrapper -->
								<div class="bdt-event-header">
								<?php $this->render_events_calendar_title(); ?>
								<?php $this->render_events_calendar_time(); ?>
								</div>	

							</div>

							<?php $this->render_events_calendar_excerpt( $post );?>

						</div>

						<?php $this->render_events_calendar_meta();?>

					</div>
				</div>
			<?php
	}

	protected function render_events_calendar_list_header() {


		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

		if ($settings['show_horizontal']) {
			$this->add_render_attribute('list-wrapper', 'bdt-grid', '');
			$this->add_render_attribute('list-wrapper', 'class', ['bdt-grid', 'bdt-child-width-1-' . $settings['column'] . '@m']);
		} else {
			$this->add_render_attribute('list-wrapper', 'class', ['bdt-list', 'bdt-list-large']);
		}

	?>
		<div id="bdt-event-<?php echo esc_attr($id); ?>" class="bdt-event-list bdt-event-calendar">
			<div <?php $this->print_render_attribute_string('list-wrapper'); ?>>

			<?php
	}

	protected function render_events_calendar_list_footer() {

			$settings = $this->get_settings_for_display();

			?>

			</div>
		</div>
	<?php
	}

	protected function render_events_calendar_list_loop_item( $post ) {

			$settings = $this->get_settings_for_display();

	?>
		<div>
			<div class="bdt-event-list-item">
				<div class="bdt-event-item-inner bdt-grid bdt-flex bdt-flex-middle">

					<div class="bdt-width-auto">
						<?php $this->render_events_calendar_image(); ?>
					</div>

					<div class="bdt-width-expand">
						<div class="bdt-event-content">
							<div class="bdt-event-intro">

								<?php $this->render_events_calendar_date( 'list' ); ?>

								<?php $this->render_events_calendar_title(); ?>

							</div>

							<?php $this->render_events_calendar_excerpt( $post ); ?>

							<?php $this->render_events_calendar_meta( 'list' ); ?>
						</div>
					</div>


				</div>
			</div>
		</div>
	<?php
	}

	protected function render_events_calendar_grid() {
		global $post;
		$query_args = $this->get_events_calendar_events( $this->get_settings_for_display(), [ 'include_upcoming' => true ] );
		$this->render_events_calendar_grid_header();
		if ( ! empty( $query_args ) ) { foreach ( $query_args as $post ) { $this->render_events_calendar_grid_loop_item( $post ); } }
		else { echo '<div class="bdt-alert bdt-alert-warning">' . esc_html__( 'No events!', 'bdthemes-element-pack' ) . '</div>'; }
		$this->render_events_calendar_grid_footer(); wp_reset_postdata();
	}
	protected function render_events_calendar_carousel() {
		global $post;
		$query_args = $this->get_events_calendar_events( $this->get_settings_for_display() );
		$this->render_events_calendar_carousel_header();
		if ( ! empty( $query_args ) ) { foreach ( $query_args as $post ) { $this->render_events_calendar_carousel_loop_item( $post ); } }
		else { echo '<div class="bdt-alert bdt-alert-warning">' . esc_html__( 'No events!', 'bdthemes-element-pack' ) . '</div>'; }
		$this->render_footer(); wp_reset_postdata();
	}
	protected function render_events_calendar_list() {
		global $post;
		$query_args = $this->get_events_calendar_events( $this->get_settings_for_display() );
		$this->render_events_calendar_list_header();
		if ( ! empty( $query_args ) ) { foreach ( $query_args as $post ) { $this->render_events_calendar_list_loop_item( $post ); } }
		else { echo '<div class="bdt-alert bdt-alert-warning">' . esc_html__( 'No events!', 'bdthemes-element-pack' ) . '</div>'; }
		$this->render_events_calendar_list_footer(); wp_reset_postdata();
	}

/**
 * Twitter widget shared controls and render methods.
 */

	protected function get_twitter_wrapper_class( $type ) {
		$map = [
			'grid'     => 'bdt-twitter-grid',
			'carousel' => 'bdt-twitter-carousel',
			'slider'   => 'bdt-twitter-slider',
		];
		return $map[ $type ] ?? 'bdt-twitter-grid';
	}

	protected function register_twitter_query_section_controls( $type ) {
		$this->register_twitter_layout_controls( $type );
		if ( 'carousel' === $type ) {
		$this->start_controls_section(
			'section_content_navigation',
			[ 
				'label' => __( 'Navigation', 'bdthemes-element-pack' ),
			]
		);

		//Global Navigation Controls
		$this->register_navigation_controls();

		$this->end_controls_section();

		//Global Carousel Settings Controls
		$this->register_carousel_settings_controls();

		} elseif ( 'slider' === $type ) {
		$this->start_controls_section(
			'section_content_navigation',
			[
				'label' => __('Navigation', 'bdthemes-element-pack'),
			]
		);

		//Global Navigation Controls
		$this->register_navigation_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_slider_settins',
			[
				'label' => esc_html__('Slider Settings', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'   => esc_html__('Auto Play', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__('Autoplay Speed', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pauseonhover',
			[
				'label' => esc_html__('Pause on Hover', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'speed',
			[
				'label'   => __('Animation Speed (ms)', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 500,
				],
				'range'   => [
					'px' => [
						'min'  => 100,
						'max'  => 5000,
						'step' => 50,
					],
				],
			]
		);

		$this->add_control(
			'loop',
			[
				'label'   => esc_html__('Loop', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'transition',
			[
				'label'   => esc_html__('Transition', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'slide'     => esc_html__('Slide', 'bdthemes-element-pack'),
					'fade'      => esc_html__('Fade', 'bdthemes-element-pack'),
					'cube'      => esc_html__('Cube', 'bdthemes-element-pack'),
					'coverflow' => esc_html__('Coverflow', 'bdthemes-element-pack'),
					'flip'      => esc_html__('Flip', 'bdthemes-element-pack'),
				],
			]
		);

		$this->end_controls_section();
		}
		if ( 'grid' === $type ) {
		$this->start_controls_section(
		    'section_style_layout',
		    [
		        'label' => __('Items', 'bdthemes-element-pack'),
		        'tab'   => Controls_Manager::TAB_STYLE,
		    ]
		);

		$this->start_controls_tabs('tabs_item_style');

		$this->start_controls_tab(
		    'tab_item_normal',
		    [
		        'label' => __('Normal', 'bdthemes-element-pack'),
		    ]
		);

		$this->add_control(
		    'item_color',
		    [
		        'label'     => __('Color', 'bdthemes-element-pack'),
		        'type'      => Controls_Manager::COLOR,
		        'selectors' => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-grid-item .bdt-twitter-text,
					{{WRAPPER}} .bdt-twitter-grid .bdt-grid-item .bdt-twitter-text *' => 'color: {{VALUE}};',
		        ],
		    ]
		);

		$this->add_control(
		    'item_background',
		    [
		        'label'     => __('Background', 'bdthemes-element-pack'),
		        'type'      => Controls_Manager::COLOR,
		        'default'   => '#ffffff',
		        'selectors' => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-grid-item' => 'background-color: {{VALUE}};',
		        ],
		    ]
		);

		$this->add_group_control(
		    Group_Control_Box_Shadow::get_type(),
		    [
		        'name'     => 'item_shadow',
		        'selector' => '{{WRAPPER}} .bdt-twitter-grid .bdt-grid-item',
		    ]
		);

		$this->add_group_control(
		    Group_Control_Border::get_type(),
		    [
		        'name'        => 'item_border',
		        'label'       => __('Border', 'bdthemes-element-pack'),
		        'placeholder' => '1px',
		        'default'     => '1px',
		        'selector'    => '{{WRAPPER}} .bdt-twitter-grid .bdt-grid-item',
		    ]
		);

		$this->add_responsive_control(
		    'item_border_radius',
		    [
		        'label'      => __('Border Radius', 'bdthemes-element-pack'),
		        'type'       => Controls_Manager::DIMENSIONS,
		        'size_units' => ['px', '%'],
		        'selectors'  => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-grid-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		        ],
		    ]
		);

		$this->add_responsive_control(
		    'item_padding',
		    [
		        'label'      => __('Padding', 'bdthemes-element-pack'),
		        'type'       => Controls_Manager::DIMENSIONS,
		        'size_units' => ['px', '%', 'em'],
		        'default'    => [
		            'top'    => '40',
		            'bottom' => '40',
		            'left'   => '40',
		            'right'  => '40',
		            'unit'   => 'px',
		        ],
		        'selectors'  => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-card-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
		        ],
		    ]
		);

		$this->add_responsive_control(
		    'content_align',
		    [
		        'label'     => __('Alignment', 'bdthemes-element-pack'),
		        'type'      => Controls_Manager::CHOOSE,
		        'options'   => [
		            'left'   => [
		                'title' => __('Left', 'bdthemes-element-pack'),
		                'icon'  => 'eicon-text-align-left',
		            ],
		            'center' => [
		                'title' => __('Center', 'bdthemes-element-pack'),
		                'icon'  => 'eicon-text-align-center',
		            ],
		            'right'  => [
		                'title' => __('Right', 'bdthemes-element-pack'),
		                'icon'  => 'eicon-text-align-right',
		            ],
		        ],
		        'selectors' => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-grid-item .bdt-card-body' => 'text-align: {{VALUE}};',
		        ],
		    ]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
		    'tab_item_hover',
		    [
		        'label' => __('Hover', 'bdthemes-element-pack'),
		    ]
		);

		$this->add_control(
		    'item_hover_color',
		    [
		        'label'     => __('Color', 'bdthemes-element-pack') . BDTEP_NC,
		        'type'      => Controls_Manager::COLOR,
		        'selectors' => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-grid-item:hover .bdt-twitter-text,
					{{WRAPPER}} .bdt-twitter-grid .bdt-grid-item:hover .bdt-twitter-text *' => 'color: {{VALUE}};',
		        ],
		    ]
		);

		$this->add_control(
		    'item_hover_background',
		    [
		        'label'     => __('Background', 'bdthemes-element-pack'),
		        'type'      => Controls_Manager::COLOR,
		        'selectors' => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-grid-item:hover' => 'background-color: {{VALUE}};',
		        ],
		    ]
		);

		$this->add_control(
		    'item_hover_border_color',
		    [
		        'label'     => __('Border Color', 'bdthemes-element-pack'),
		        'type'      => Controls_Manager::COLOR,
		        'condition' => [
		            'item_border_border!' => '',
		        ],
		        'selectors' => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-grid-item:hover' => 'border-color: {{VALUE}};',
		        ],
		    ]
		);

		$this->add_group_control(
		    Group_Control_Box_Shadow::get_type(),
		    [
		        'name'     => 'item_hover_shadow',
		        'selector' => '{{WRAPPER}} .bdt-twitter-grid .bdt-grid-item:hover',
		    ]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
		    'section_style_avatar',
		    [
		        'label'     => __('Avatar', 'bdthemes-element-pack'),
		        'tab'       => Controls_Manager::TAB_STYLE,
		        'condition' => [
		            'show_avatar' => 'yes',
		        ],
		    ]
		);

		$this->add_control(
		    'avatar_background',
		    [
		        'label'     => __('Background', 'bdthemes-element-pack'),
		        'type'      => Controls_Manager::COLOR,
		        'selectors' => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-twitter-thumb-wrapper' => 'background-color: {{VALUE}};',
		        ],
		    ]
		);

		$this->add_group_control(
		    Group_Control_Border::get_type(),
		    [
		        'name'        => 'avatar_border',
		        'label'       => __('Border', 'bdthemes-element-pack'),
		        'placeholder' => '1px',
		        'default'     => '1px',
		        'selector'    => '{{WRAPPER}} .bdt-twitter-grid .bdt-twitter-thumb-wrapper',
		    ]
		);

		$this->add_responsive_control(
		    'avatar_border_radius',
		    [
		        'label'      => __('Border Radius', 'bdthemes-element-pack'),
		        'type'       => Controls_Manager::DIMENSIONS,
		        'size_units' => ['px', '%'],
		        'selectors'  => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-twitter-thumb-wrapper, {{WRAPPER}} .bdt-twitter-grid .bdt-twitter-thumb-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
		        ],
		    ]
		);

		$this->add_responsive_control(
		    'avatar_padding',
		    [
		        'label'      => __('Padding', 'bdthemes-element-pack'),
		        'type'       => Controls_Manager::DIMENSIONS,
		        'size_units' => ['px', '%', 'em'],
		        'selectors'  => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-twitter-thumb-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
		        ],
		    ]
		);

		$this->add_responsive_control(
		    'avatar_margin',
		    [
		        'label'      => __('Margin', 'bdthemes-element-pack'),
		        'type'       => Controls_Manager::DIMENSIONS,
		        'size_units' => ['px', '%', 'em'],
		        'selectors'  => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-twitter-thumb-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
		        ],
		    ]
		);

		$this->add_responsive_control(
		    'avatar_width',
		    [
		        'label'     => __('Size', 'bdthemes-element-pack'),
		        'type'      => Controls_Manager::SLIDER,
		        'range'     => [
		            'px' => [
		                'max' => 48,
		                'min' => 15,
		            ],
		        ],
		        'selectors' => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-twitter-thumb-wrapper img' => 'width: {{SIZE}}{{UNIT}};',
		        ],
		    ]
		);

		$this->add_control(
		    'avatar_opacity',
		    [
		        'label'     => __('Opacity (%)', 'bdthemes-element-pack'),
		        'type'      => Controls_Manager::SLIDER,
		        'default'   => [
		            'size' => 1,
		        ],
		        'range'     => [
		            'px' => [
		                'max'  => 1,
		                'min'  => 0.10,
		                'step' => 0.01,
		            ],
		        ],
		        'selectors' => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-twitter-thumb-wrapper img' => 'opacity: {{SIZE}};',
		        ],
		    ]
		);

		$this->add_group_control(
		    Group_Control_Box_Shadow::get_type(),
		    [
		        'name'     => 'avatar_shadow',
		        'selector' => '{{WRAPPER}} .bdt-twitter-grid .bdt-twitter-thumb-wrapper',
		    ]
		);

		$this->add_group_control(
		    Group_Control_Css_Filter::get_type(),
		    [
		        'name'     => 'avatar_css_filters',
		        'selector' => '{{WRAPPER}} .bdt-twitter-grid .bdt-twitter-thumb-wrapper',
		    ]
		);

		$this->add_responsive_control(
		    'avatar_align',
		    [
		        'label'     => __('Alignment', 'bdthemes-element-pack'),
		        'type'      => Controls_Manager::CHOOSE,
		        'options'   => [
		            'left'   => [
		                'title' => __('Left', 'bdthemes-element-pack'),
		                'icon'  => 'eicon-text-align-left',
		            ],
		            'center' => [
		                'title' => __('Center', 'bdthemes-element-pack'),
		                'icon'  => 'eicon-text-align-center',
		            ],
		            'right'  => [
		                'title' => __('Right', 'bdthemes-element-pack'),
		                'icon'  => 'eicon-text-align-right',
		            ],
		        ],
		        'selectors' => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-twitter-thumb' => 'text-align: {{VALUE}};',
		        ],
		    ]
		);

		$this->end_controls_section();


		$this->start_controls_section(
		    'section_style_meta',
		    [
		        'label'     => __('Execute Buttons', 'bdthemes-element-pack'),
		        'tab'       => Controls_Manager::TAB_STYLE,
		        'condition' => [
		            'show_meta_button' => 'yes',
		        ],
		    ]
		);

		$this->add_control(
		    'meta_color',
		    [
		        'label'     => __('Color', 'bdthemes-element-pack'),
		        'type'      => Controls_Manager::COLOR,
		        'selectors' => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-twitter-meta-button > a' => 'color: {{VALUE}};',
		        ],
		    ]
		);

		$this->add_control(
		    'meta_hover_color',
		    [
		        'label'     => __('Hover Color', 'bdthemes-element-pack'),
		        'type'      => Controls_Manager::COLOR,
		        'selectors' => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-twitter-meta-button > a:hover' => 'color: {{VALUE}};',
		        ],
		    ]
		);

		$this->add_responsive_control(
		    'meta_icon_size',
		    [
		        'label'     => __('Size', 'bdthemes-element-pack') . BDTEP_NC,
		        'type'      => Controls_Manager::SLIDER,
		        'range'     => [
		            'px' => [
		                'min' => 0,
		                'max' => 50,
		            ],
		        ],
		        'selectors' => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-twitter-meta-button > a' => 'font-size: {{SIZE}}{{UNIT}};',
		        ],
		    ]
		);

		$this->add_responsive_control(
		    'meta_icon_spacing',
		    [
		        'label'     => __('Spacing', 'bdthemes-element-pack') . BDTEP_NC,
		        'type'      => Controls_Manager::SLIDER,
		        'range'     => [
		            'px' => [
		                'min' => 0,
		                'max' => 50,
		            ],
		        ],
		        'selectors' => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-grid-item .bdt-twitter-meta-wrapper .bdt-twitter-meta-button a+a' => 'margin-left: {{SIZE}}{{UNIT}};',
		        ],
		    ]
		);

		$this->end_controls_section();

		$this->start_controls_section(
		    'section_style_time',
		    [
		        'label'     => __('Time', 'bdthemes-element-pack'),
		        'tab'       => Controls_Manager::TAB_STYLE,
		        'condition' => [
		            'show_time' => 'yes',
		        ],
		    ]
		);

		$this->add_control(
		    'time_color',
		    [
		        'label'     => __('Color', 'bdthemes-element-pack'),
		        'type'      => Controls_Manager::COLOR,
		        'selectors' => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-twitter-meta-wrapper a.bdt-twitter-time-link' => 'color: {{VALUE}};',
		        ],
		    ]
		);

		$this->add_control(
		    'time_hover_color',
		    [
		        'label'     => __('Hover Color', 'bdthemes-element-pack'),
		        'type'      => Controls_Manager::COLOR,
		        'selectors' => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-twitter-meta-wrapper a.bdt-twitter-time-link:hover' => 'color: {{VALUE}};',
		        ],
		    ]
		);

		$this->add_group_control(
		    Group_Control_Typography::get_type(),
		    [
		        'name'     => 'time_typography',
		        'label'    => esc_html__('Typography', 'bdthemes-element-pack') . BDTEP_NC,
		        'selector' => '{{WRAPPER}} .bdt-twitter-grid .bdt-twitter-meta-wrapper a.bdt-twitter-time-link',
		    ]
		);

		$this->end_controls_section();
		} elseif ( 'carousel' === $type ) {
		$this->start_controls_section(
			'section_style_layout',
			[ 
				'label' => __( 'Items', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_item_style' );

		$this->start_controls_tab(
			'tab_item_normal',
			[ 
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'item_background',
			[ 
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_color',
			[ 
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item .bdt-twitter-text,
					{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item .bdt-twitter-text *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_align',
			[ 
				'label'     => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [ 
					'left'   => [ 
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [ 
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [ 
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item .bdt-card-body' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'item_border',
				'label'       => __( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item',
			]
		);

		$this->add_control(
			'item_border_radius',
			[ 
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item, {{WRAPPER}} .bdt-twitter-carousel .swiper-carousel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[ 
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [ 
					'top'    => '40',
					'bottom' => '40',
					'left'   => '40',
					'right'  => '40',
					'unit'   => 'px',
				],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-card-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'shadow_mode',
			[ 
				'label'        => esc_html__( 'Shadow Mode', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-ep-shadow-mode-',
			]
		);

		$this->add_control(
			'shadow_color',
			[ 
				'label'     => esc_html__( 'Shadow Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'shadow_mode' => 'yes',
				],
				'selectors' => [ 
					'{{WRAPPER}}.bdt-ep-shadow-mode-yes:before' => is_rtl() ? 'background: linear-gradient(to left, {{VALUE}} 5%,rgba(255,255,255,0) 100%);' : 'background: linear-gradient(to right, {{VALUE}} 5%,rgba(255,255,255,0) 100%);',
					'{{WRAPPER}}.bdt-ep-shadow-mode-yes:after'  => is_rtl() ? 'background: linear-gradient(to left, rgba(255,255,255,0) 0%, {{VALUE}} 95%);' : 'background: linear-gradient(to right, rgba(255,255,255,0) 0%, {{VALUE}} 95%);',
				],
			]
		);

		$this->add_control(
			'item_opacity',
			[ 
				'label'     => esc_html__( 'Opacity', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min'  => 0,
						'step' => 0.1,
						'max'  => 1,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[ 
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'item_hover_background',
			[ 
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_hover_border_color',
			[ 
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'border_border!' => '',
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'item_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item:hover',
			]
		);

		$this->add_responsive_control(
			'item_shadow_padding',
			[ 
				'label'       => __( 'Match Padding', 'bdthemes-element-pack' ),
				'description' => __( 'You have to add padding for matching overlaping hover shadow', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [ 
					'px' => [ 
						'min'  => 0,
						'step' => 1,
						'max'  => 50,
					]
				],
				'default'     => [ 
					'size' => 10
				],
				'selectors'   => [ 
					'{{WRAPPER}} .swiper-carousel' => 'padding: {{SIZE}}{{UNIT}}; margin: 0 -{{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'item_hover_opacity',
			[ 
				'label'     => esc_html__( 'Opacity', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min'  => 0,
						'step' => 0.1,
						'max'  => 1,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item:hover' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_active',
			[ 
				'label' => __( 'Active', 'bdthemes-element-pack' ) . BDTEP_NC,
			]
		);

		$this->add_control(
			'item_active_background',
			[ 
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item.swiper-slide-active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_active_border_color',
			[ 
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'item_border_border!' => '',
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item.swiper-slide-active' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'item_active_shadow',
				'selector' => '{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item.swiper-slide-active',
			]
		);

		$this->add_control(
			'item_active_opacity',
			[ 
				'label'     => esc_html__( 'Opacity', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min'  => 0,
						'step' => 0.1,
						'max'  => 1,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-carousel-item.swiper-slide-active' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_avatar',
			[ 
				'label'     => __( 'Avatar', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_avatar' => 'yes',
				],
			]
		);

		$this->add_control(
			'avatar_width',
			[ 
				'label'     => __( 'Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'max' => 48,
						'min' => 15,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb-wrapper img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'avatar_align',
			[ 
				'label'     => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [ 
					'left'   => [ 
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [ 
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [ 
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'avatar_background',
			[ 
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'avatar_border',
				'label'       => __( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb-wrapper',
			]
		);

		$this->add_responsive_control(
			'avatar_border_radius',
			[ 
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb-wrapper, {{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'avatar_padding',
			[ 
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'avatar_margin',
			[ 
				'label'      => __( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'avatar_opacity',
			[ 
				'label'     => __( 'Opacity (%)', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ 
					'size' => 1,
				],
				'range'     => [ 
					'px' => [ 
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb-wrapper img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'avatar_shadow',
				'selector' => '{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb-wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[ 
				'name'     => 'avatar_css_filters',
				'selector' => '{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-thumb-wrapper',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_meta',
			[ 
				'label'     => __( 'Execute Buttons', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_meta_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'meta_color',
			[ 
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-meta-button > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'meta_hover_color',
			[ 
				'label'     => __( 'Hover Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-meta-button > a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_time',
			[ 
				'label'     => __( 'Time', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_time' => 'yes',
				],
			]
		);

		$this->add_control(
			'time_color',
			[ 
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-meta-wrapper a.bdt-twitter-time-link' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'time_hover_color',
			[ 
				'label'     => __( 'Hover Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-twitter-carousel .bdt-twitter-meta-wrapper a.bdt-twitter-time-link:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		//Navigation Style
		$this->start_controls_section(
			'section_style_navigation',
			[ 
				'label'      => __( 'Navigation', 'bdthemes-element-pack' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [ 
					'relation' => 'or',
					'terms'    => [ 
						[ 
							'name'     => 'navigation',
							'operator' => '!=',
							'value'    => 'none',
						],
						[ 
							'name'  => 'show_scrollbar',
							'value' => 'yes',
						],
					],
				],
			]
		);

		//Global Navigation Style Controls
		$this->register_navigation_style_controls( 'swiper-carousel' );

		$this->end_controls_section();
		} else {
		$this->start_controls_section(
			'section_style_layout',
			[
				'label' => __('Items', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'item_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-slider .bdt-twitter-slider-item .bdt-twitter-text,
					{{WRAPPER}} .bdt-twitter-slider .bdt-twitter-slider-item .bdt-twitter-text *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_background_color',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-slider .bdt-twitter-slider-item .bdt-card-body' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-twitter-slider .bdt-card-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'alignment',
			[
				'label'     => __('Alignment', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => [
					'left'   => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-slider .bdt-twitter-slider-item .bdt-card-body' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_avatar',
			[
				'label'     => __('Avatar', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_avatar' => 'yes',
				],
			]
		);

		$this->add_control(
			'avatar_width',
			[
				'label'     => __('Size', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 48,
						'min' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-slider .bdt-twitter-thumb-wrapper img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'avatar_align',
			[
				'label'     => __('Alignment', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-slider .bdt-twitter-thumb' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'avatar_background',
			[
				'label'     => __('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-slider .bdt-twitter-thumb-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'avatar_border',
				'label'       => __('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-twitter-slider .bdt-twitter-thumb-wrapper',
			]
		);

		$this->add_responsive_control(
			'avatar_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-twitter-slider .bdt-twitter-thumb-wrapper, {{WRAPPER}} .bdt-twitter-slider .bdt-twitter-thumb-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'avatar_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-twitter-slider .bdt-twitter-thumb-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'avatar_margin',
			[
				'label'      => __('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-twitter-slider .bdt-twitter-thumb-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'avatar_opacity',
			[
				'label'     => __('Opacity (%)', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 1,
				],
				'range'     => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-slider .bdt-twitter-thumb-wrapper img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'avatar_shadow',
				'selector' => '{{WRAPPER}} .bdt-twitter-slider .bdt-twitter-thumb-wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'avatar_css_filters',
				'selector' => '{{WRAPPER}} .bdt-twitter-slider .bdt-twitter-thumb-wrapper',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_meta',
			[
				'label'     => __('Execute Buttons', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_meta_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-slider .bdt-twitter-meta-button > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'meta_hover_color',
			[
				'label'     => __('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-slider .bdt-twitter-meta-button > a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_time',
			[
				'label'     => __('Time', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_time' => 'yes',
				],
			]
		);

		$this->add_control(
			'time_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-slider .bdt-twitter-meta-wrapper a.bdt-twitter-time-link' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'time_hover_color',
			[
				'label'     => __('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-twitter-slider .bdt-twitter-meta-wrapper a.bdt-twitter-time-link:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		//Navigation Style
		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'      => __('Navigation', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'navigation',
							'operator' => '!=',
							'value'    => 'none',
						],
						[
							'name'  => 'show_scrollbar',
							'value' => 'yes',
						],
					],
				],
			]
		);

		//Global Navigation Style Controls
		$this->register_navigation_style_controls('swiper-carousel');

		$this->end_controls_section();
		}
	}

	protected function register_twitter_layout_controls( $type ) {
		if ( 'grid' === $type ) {
		$this->start_controls_section(
		    'section_carousel_layout',
		    [
		        'label' => __('Layout', 'bdthemes-element-pack'),
		    ]
		);

		$this->add_responsive_control(
		    'columns',
		    [
		        'label'          => esc_html__('Columns', 'bdthemes-element-pack'),
		        'type'           => Controls_Manager::SELECT,
		        'default'        => '3',
		        'tablet_default' => '2',
		        'mobile_default' => '1',
		        'options'        => [
		            '1' => '1',
		            '2' => '2',
		            '3' => '3',
		            '4' => '4',
		            '5' => '5',
		            '6' => '6',
		        ],
		    ]
		);

		$this->add_control(
		    'column_gap',
		    [
		        'label'   => esc_html__('Column Gap', 'bdthemes-element-pack'),
		        'type'    => Controls_Manager::SELECT,
		        'default' => 'medium',
		        'options' => [
		            'small'    => esc_html__('Small', 'bdthemes-element-pack'),
		            'medium'   => esc_html__('Medium', 'bdthemes-element-pack'),
		            'large'    => esc_html__('Large', 'bdthemes-element-pack'),
		            'collapse' => esc_html__('Collapse', 'bdthemes-element-pack'),
		        ],
		    ]
		);

		$this->add_responsive_control(
		    'row_gap',
		    [
		        'label'     => esc_html__('Row Gap', 'bdthemes-element-pack'),
		        'type'      => Controls_Manager::SLIDER,
		        'default'   => [
		            'size' => 30,
		        ],
		        'range'     => [
		            'px' => [
		                'min'  => 0,
		                'max'  => 100,
		                'step' => 5,
		            ],
		        ],
		        'selectors' => [
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-grid'     => 'margin-top: -{{SIZE}}px',
		            '{{WRAPPER}} .bdt-twitter-grid .bdt-grid > *' => 'margin-top: {{SIZE}}px',
		        ],
		    ]
		);

		$this->add_control(
		    'num_tweets',
		    [
		        'label'   => __('Limit', 'bdthemes-element-pack'),
		        'type'    => Controls_Manager::NUMBER,
		        'default' => 6,
		    ]
		);

		$this->add_control(
		    'cache_time',
		    [
		        'label'   => __('Cache Time(m)', 'bdthemes-element-pack'),
		        'type'    => Controls_Manager::NUMBER,
		        'default' => 60,
		    ]
		);

		$this->add_control(
		    'show_avatar',
		    [
		        'label' => __('Show Avatar', 'bdthemes-element-pack'),
		        'type'  => Controls_Manager::SWITCHER,
		    ]
		);

		$this->add_control(
		    'enable_twitter_auth2_api',
		    [
		        'label' => __('Enable Twitter Auth2 API', 'bdthemes-element-pack') . BDTEP_NC,
		        'type'  => Controls_Manager::SWITCHER,
		    ]
		);

		$this->add_control(
		    'avatar_link',
		    [
		        'label'     => __('Avatar Link', 'bdthemes-element-pack'),
		        'type'      => Controls_Manager::SWITCHER,
		        'condition' => [
		            'show_avatar' => 'yes'
		        ]
		    ]
		);

		$this->add_control(
		    'show_time',
		    [
		        'label'   => __('Show Time', 'bdthemes-element-pack'),
		        'type'    => Controls_Manager::SWITCHER,
		        'default' => 'yes',
		    ]
		);

		$this->add_control(
		    'long_time_format',
		    [
		        'label'     => __('Long Time Format', 'bdthemes-element-pack'),
		        'type'      => Controls_Manager::SWITCHER,
		        'default'   => 'yes',
		        'condition' => [
		            'show_time' => 'yes',
		        ]
		    ]
		);


		$this->add_control(
		    'show_meta_button',
		    [
		        'label'   => __('Execute Buttons', 'bdthemes-element-pack'),
		        'type'    => Controls_Manager::SWITCHER,
		        'default' => 'yes',
		    ]
		);

		$this->add_control(
		    'exclude_replies',
		    [
		        'label' => __('Exclude Replies', 'bdthemes-element-pack'),
		        'type'  => Controls_Manager::SWITCHER,
		    ]
		);

		$this->add_control(
		    'strip_emoji',
		    [
		        'label' => __('Strip Emoji', 'bdthemes-element-pack'),
		        'type'  => Controls_Manager::SWITCHER,
		    ]
		);

		$this->add_control(
		    'match_height',
		    [
		        'label'   => __('Item Match Height', 'bdthemes-element-pack'),
		        'type'    => Controls_Manager::SWITCHER,
		        'default' => 'yes',

		    ]
		);

		$this->end_controls_section();


		//Style
		} elseif ( 'carousel' === $type ) {
		$this->start_controls_section(
			'section_carousel_layout',
			[ 
				'label' => __( 'Layout', 'bdthemes-element-pack' ),
			]
		);

		//swiper carousel columns & item gap controls
		$this->register_carousel_column_gap_controls();

		$this->add_control(
			'num_tweets',
			[ 
				'label'   => __( 'Limit', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
			]
		);

		$this->add_control(
			'cache_time',
			[ 
				'label'   => __( 'Cache Time(m)', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 60,
			]
		);

		$this->add_control(
			'show_avatar',
			[ 
				'label' => __( 'Show Avatar', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'enable_twitter_auth2_api',
			[ 
				'label' => __( 'Enable Twitter Auth2 API', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'avatar_link',
			[ 
				'label'     => __( 'Avatar Link', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [ 
					'show_avatar' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_time',
			[ 
				'label'   => __( 'Show Time', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'long_time_format',
			[ 
				'label'     => __( 'Long Time Format', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [ 
					'show_time' => 'yes',
				]
			]
		);


		$this->add_control(
			'show_meta_button',
			[ 
				'label'   => __( 'Execute Buttons', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'exclude_replies',
			[ 
				'label' => __( 'Exclude Replies', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'strip_emoji',
			[ 
				'label' => __( 'Strip Emoji', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'match_height',
			[ 
				'label'   => __( 'Item Match Height', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',

			]
		);

		$this->end_controls_section();

		//Navigation Controls
		} else {
		$this->start_controls_section(
			'section_carousel_layout',
			[
				'label' => __('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'num_tweets',
			[
				'label'   => __('Limit', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
			]
		);

		$this->add_control(
			'cache_time',
			[
				'label'   => __('Cache Time(m)', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::NUMBER,
				'default' => 60,
			]
		);

		$this->add_control(
			'show_avatar',
			[
				'label'   => __('Show Avatar', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'enable_twitter_auth2_api',
			[
				'label' => __('Enable Twitter Auth2 API', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'avatar_link',
			[
				'label'     => __('Avatar Link', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'show_avatar' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_time',
			[
				'label'   => __('Show Time', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'long_time_format',
			[
				'label'     => __('Long Time Format', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'show_time' => 'yes',
				]
			]
		);


		$this->add_control(
			'show_meta_button',
			[
				'label' => __('Execute Buttons', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'exclude_replies',
			[
				'label' => __('Exclude Replies', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'strip_emoji',
			[
				'label' => __('Strip Emoji', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->end_controls_section();

		//Navigation Controls
		}
	}

	public function get_twitter_auth2_data( $consumerKey, $consumerSecret, $username ) {
		$access_token = get_option('elementpack_twitter_access_token_' . $username);

		if ( !$access_token ) {
		    $credentials = base64_encode($consumerKey . ':' . $consumerSecret);
		    $response    = wp_remote_post('https://api.twitter.com/oauth2/token', [
		        'method'      => 'POST',
		        'httpversion' => '1.1',
		        'sslverify'   => false,
		        'blocking'    => true,
		        'headers'     => [
		            'Authorization' => 'Basic ' . $credentials,
		            'Content-Type'  => 'application/x-www-form-urlencoded;charset=UTF-8',
		        ],
		        'body'        => ['grant_type' => 'client_credentials'],
		    ]);

		    $body = json_decode(wp_remote_retrieve_body($response));

		    if ( $body && isset($body->access_token) ) {
		        update_option('elementpack_twitter_access_token_' . $username, $body->access_token);
		        $access_token = $body->access_token;
		    }
		}

		$response = wp_remote_get('https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=' . $username . '&count=999&tweet_mode=extended', [
		    'httpversion' => '1.1',
		    'blocking'    => true,
		    'sslverify'   => false,
		    'headers'     => [
		        'Authorization' => "Bearer $access_token",
		    ],
		]);

		if ( $response['response']['code'] == 200 && !empty($response['response']) ) {
		    return json_decode(wp_remote_retrieve_body($response), true);
		}

	}

	public function get_twitter_auth1_data( $consumerKey, $consumerSecret, $accessToken, $accessTokenSecret, $twitter_name ) {
		$connection = new \TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);

		$settings        = $this->get_settings_for_display();
		$exclude_replies = ('yes' === $settings['exclude_replies']) ? true : false;

		// If excluding replies, we need to fetch more than requested as the
		// total is fetched first, and then replies removed.
		$totalToFetch = ($exclude_replies) ? max(50, $settings['num_tweets'] * 3) : $settings['num_tweets'];

		$fetchedTweets = $connection->get(
		    'statuses/user_timeline',
		    array(
		        'screen_name' => $twitter_name,
		        'count'       => $totalToFetch,
		    )
		);

		if ( $connection->http_code == 200 ) {
		    return $fetchedTweets;
		}

	}

	public function getTwitterAuth2Data( $consumerKey, $consumerSecret, $username ) {
		return $this->get_twitter_auth2_data( $consumerKey, $consumerSecret, $username );
	}

	public function getTwitterAuth1Data( $consumerKey, $consumerSecret, $accessToken, $accessTokenSecret, $twitter_name ) {
		return $this->get_twitter_auth1_data( $consumerKey, $consumerSecret, $accessToken, $accessTokenSecret, $twitter_name );
	}

	protected function render_twitter_loop( $type, $consumerKey, $consumerSecret, $accessToken, $accessTokenSecret, $twitter_name ) {
		$settings      = $this->get_settings_for_display();
		$isEnableAuth2 = isset($settings['enable_twitter_auth2_api']) && $settings['enable_twitter_auth2_api'] == 'yes';
		$name          = $twitter_name;

		$tweets        = [];
		$fetchedTweets = [];
		$transName     = 'bdt-tweets-' . $name; // Name of value in database. [added $name for multiple account use]
		$backupName    = $transName . '-backup'; // Name of backup value in database.

		if ( $isEnableAuth2 ) {
		    $transName  = 'bdt-tweets-auth2-' . $name;
		    $backupName = $transName . '-backup';
		}

		if ( $isEnableAuth2 ) {
		    if ( !get_transient($name) ) {
		        $fetchedTweets = $this->get_twitter_auth2_data($consumerKey, $consumerSecret, $twitter_name);
		        if ( $fetchedTweets ) {
		            $fetchedTweets = json_decode(json_encode($fetchedTweets)); // convert array to json recursively.
		        }
		    }

		} else {
		    if ( !get_transient($name) ) {
		        $fetchedTweets = $this->get_twitter_auth1_data($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret, $twitter_name);
		    }
		}

		// Did the fetch fail?
		if ( !$fetchedTweets ) :
		    $tweets = get_option($backupName); // False if there has never been data saved.
		else :
		    // Fetch succeeded.
		    // Now update the array to store just what we need.
		    // (Done here instead of PHP doing this for every page load)
		    $limitToDisplay = min($settings['num_tweets'], count($fetchedTweets));

		    for ( $i = 0; $i < $limitToDisplay; $i++ ) :
		        $tweet = $fetchedTweets[$i];

		        // Core info.
		        $name = $tweet->user->name;
		        // COMMUNITY REQUEST !!!!!! (2)
		        $screen_name = $tweet->user->screen_name;
		        $permalink   = 'https://twitter.com/' . $screen_name . '/status/' . $tweet->id_str;
		        $tweet_id    = $tweet->id_str;

		        /* Alternative image sizes method: http://dev.twitter.com/doc/get/users/profile_image/:screen_name */
		        //  Check for SSL via protocol https then display relevant image - thanks SO - this should do
		        if ( isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
		            // $protocol = 'https://';
		            $image = $tweet->user->profile_image_url_https;
		        } else {
		            // $protocol = 'http://';
		            $image = $tweet->user->profile_image_url;
		        }

		        // Process Tweets - Use Twitter entities for correct URL, hash and mentions
		        $text = $this->process_twitter_links($tweet);
		        // lets strip 4-byte emojis
		        if ( $settings['strip_emoji'] == 'yes' ) {
		            $text = $this->twitter_api_strip_emoji($text);
		        }

		        // Need to get time in Unix format.
		        $time  = $tweet->created_at;
		        $time  = date_parse($time);
		        $uTime = mktime($time['hour'], $time['minute'], $time['second'], $time['month'], $time['day'], $time['year']);

		        // Now make the new array.
		        $tweets[] = array(
		            'text'      => $text,
		            'name'      => $name,
		            'permalink' => $permalink,
		            'image'     => $image,
		            'time'      => $uTime,
		            'tweet_id'  => $tweet_id
		        );
		    endfor;

		    set_transient($transName, $tweets, 60 * (int) $settings['cache_time']);
		    update_option($backupName, $tweets);
		endif;
		?>

		<?php

		// Now display the tweets, if we can.
		if ( $tweets ) : ?>
		    <?php foreach ( (array) $tweets as $t ) : ?>
				<?php if ( 'grid' === $type ) : ?>
				<div>
					<div class="bdt-grid-item">
				<?php elseif ( 'carousel' === $type ) : ?>
				<div class="bdt-carousel-item swiper-slide">
				<?php else : ?>
				<div class="bdt-twitter-slider-item swiper-slide">
				<?php endif; ?>
		                <div class="bdt-card">
		                    <div class="bdt-card-body">
		                        <?php if ( 'yes' === $settings['show_avatar'] ) : ?>

		                            <?php if ( 'yes' === $settings['avatar_link'] ) : ?>
		                                <a href="https://twitter.com/<?php echo esc_attr($name); ?>">
		                            <?php endif; ?>
		                            <div class="bdt-twitter-thumb">
		                                <div class="bdt-twitter-thumb-wrapper">
		                                    <img src="<?php echo esc_url($t['image']); ?>"
		                                         alt="<?php echo esc_html($t['name']); ?>"/>
		                                </div>
		                            </div>
		                            <?php if ( 'yes' === $settings['avatar_link'] ) : ?>
		                                </a>
		                            <?php endif; ?>

		                        <?php endif; ?>

		                        <div class="bdt-twitter-text bdt-clearfix">
		                            <?php echo wp_kses_post($t['text']); ?>
		                        </div>

		                        <div class="bdt-twitter-meta-wrapper">

		                            <?php if ( 'yes' === $settings['show_time'] ) : ?>
		                                <a href="<?php echo esc_url($t['permalink']); ?>" target="_blank"
		                                   class="bdt-twitter-time-link">
		                                    <?php
		                                    // Original - long time ref: hours...
		                                    if ( 'yes' === $settings['long_time_format'] ) {
		                                        // New - short Twitter style time ref: h...
		                                        $timeDisplay = human_time_diff($t['time'], current_time('timestamp'));
		                                    } else {
		                                        $timeDisplay = ( 'slider' === $type ) ? element_pack_time_diff( $t['time'], current_time( 'timestamp' ) ) : $this->twitter_time_diff( $t['time'], current_time( 'timestamp' ) );
		                                    }
		                                    $displayAgo = _x('ago', 'leading space is required', 'bdthemes-element-pack');
		                                    // Use to make il8n compliant
		                                    printf(esc_html__('%1$s %2$s', 'bdthemes-element-pack'), wp_kses_post($timeDisplay), wp_kses_post($displayAgo));
		                                    ?>
		                                </a>
		                            <?php endif; ?>


		                            <?php if ( 'yes' === $settings['show_meta_button'] ) : ?>
		                                <div class="bdt-twitter-meta-button">
		                                    <a href="https://twitter.com/intent/tweet?in_reply_to=<?php echo esc_url($t['tweet_id']); ?>"
		                                       data-lang="en" class="bdt-tmb-reply"
		                                       title="<?php esc_html_e('Reply', 'bdthemes-element-pack'); ?>" target="_blank">
		                                        <i class="ep-icon-reply" aria-hidden="true"></i>
		                                    </a>
		                                    <a href="https://twitter.com/intent/retweet?tweet_id=<?php echo esc_url($t['tweet_id']); ?>"
		                                       data-lang="en" class="bdt-tmb-retweet"
		                                       title="<?php esc_html_e('Retweet', 'bdthemes-element-pack'); ?>" target="_blank">
		                                        <i class="ep-icon-refresh" aria-hidden="true"></i>
		                                    </a>
		                                    <a href="https://twitter.com/intent/favorite?tweet_id=<?php echo esc_url($t['tweet_id']); ?>"
		                                       data-lang="en" class="bdt-tmb-favorite"
		                                       title="<?php esc_html_e('Favourite', 'bdthemes-element-pack'); ?>"
		                                       target="_blank">
		                                        <i class="ep-icon-star" aria-hidden="true"></i>
		                                    </a>
		                                </div>
		                            <?php endif; ?>
		                        </div>
		                    </div>
		                </div>
		            </div>
		        </div>
				<?php if ( 'grid' === $type ) : ?>
				</div>
				<?php endif; ?>
				</div>
			<?php endforeach; ?>
		<?php endif;

	}

	protected function render_twitter( $type ) {
		if ( !class_exists('TwitterOAuth') ) {
		    include BDTEP_PATH . 'includes/twitteroauth/twitteroauth.php';
		}

		$settings = $this->get_settings_for_display();
		$options  = get_option('element_pack_api_settings');

		$consumerKey       = (!empty($options['twitter_consumer_key'])) ? $options['twitter_consumer_key'] : '';
		$consumerSecret    = (!empty($options['twitter_consumer_secret'])) ? $options['twitter_consumer_secret'] : '';
		$accessToken       = (!empty($options['twitter_access_token'])) ? $options['twitter_access_token'] : '';
		$accessTokenSecret = (!empty($options['twitter_access_token_secret'])) ? $options['twitter_access_token_secret'] : '';
		$twitter_name      = (!empty($options['twitter_name'])) ? $options['twitter_name'] : '';

		$this->render_twitter_loop_header( $type );

		if ( $consumerKey and $consumerSecret and $accessToken and $accessTokenSecret ) {
		    $this->render_twitter_loop( $type, $consumerKey, $consumerSecret, $accessToken, $accessTokenSecret, $twitter_name);
		} else {
		    ?>
		    <div class="bdt-alert-warning" bdt-alert>
		        <a class="bdt-alert-close" bdt-close></a>
		        <?php 
				$ep_setting_url = admin_url( 'admin.php?page=element_pack_options#element_pack_api_settings' );
		        echo '<p>';
		        echo sprintf(esc_html__('Please add your twitter API key in Element Pack settings. %1$sClick here%2$s to add your twitter API key.', 'bdthemes-element-pack'), '<a href="' . esc_url($ep_setting_url) . '">', '</a>');
		        echo '</p>';
		        ?>
		    </div>
		    <?php
		}

		$this->render_twitter_footer( $type );


	}

	protected function twitter_api_strip_emoji( $text ) {
		// four byte utf8: 11110www 10xxxxxx 10yyyyyy 10zzzzzz
		return preg_replace('/[\xF0-\xF7][\x80-\xBF]{3}/', '', $text);

	}

	protected function process_twitter_links( $tweet ) {
		// Is the Tweet a ReTweet - then grab the full text of the original Tweet
		$fullText = isset($tweet->text) ? $tweet->text : (isset($tweet->full_text) ? $tweet->full_text : '');
		if ( isset($tweet->retweeted_status) ) {
		    // Split it so indices count correctly for @mentions etc.
		    $rt_section = current(explode(":", $fullText));
		    $text       = $rt_section . ": ";
		    // Get Text
		    $text .= $tweet->retweeted_status->text;
		} else {
		    // Not a retweet - get Tweet
		    $text = $fullText;
		}

		// NEW Link Creation from clickable items in the text
		$text = preg_replace('/((http)+(s)?:\/\/[^<>\s]+)/i', '<a href="$0" target="_blank" rel="nofollow">$0</a>', $text);
		// Clickable Twitter names
		$text = preg_replace('/[@]+([A-Za-z0-9-_]+)/', '<a href="http://twitter.com/$1" target="_blank" rel="nofollow">@$1</a>', $text);
		// Clickable Twitter hash tags
		$text = preg_replace('/[#]+([A-Za-z0-9-_]+)/', '<a href="http://twitter.com/search?q=%23$1" target="_blank" rel="nofollow">$0</a>', $text);
		// END TWEET CONTENT REGEX
		return $text;

	}

	protected function twitter_time_diff( $from, $to = '' ) {
		$diff    = human_time_diff($from, $to);
		$replace = array(
		    ' hour'    => 'h',
		    ' hours'   => 'h',
		    ' day'     => 'd',
		    ' days'    => 'd',
		    ' minute'  => 'm',
		    ' minutes' => 'm',
		    ' second'  => 's',
		    ' seconds' => 's',
		);
		return strtr($diff, $replace);

	}

	protected function render_twitter_loop_header( $type ) {
		if ( 'grid' === $type ) {
		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

		$desktop_cols = isset($settings['columns']) ? $settings['columns'] : 3;
		$tablet_cols  = isset($settings['columns_tablet']) ? $settings['columns_tablet'] : 2;
		$mobile_cols  = isset($settings['columns_mobile']) ? $settings['columns_mobile'] : 1;

		if ( $settings['match_height'] ) {
		    $this->add_render_attribute('twitter_grid', 'data-bdt-height-match', 'target: > div > div > .bdt-grid-item > div > div > .bdt-twitter-text');
		}

		$this->add_render_attribute('twitter_grid', 'class', 'bdt-twitter-grid');

		?>
		<div id="bdt-twitter-grid-<?php echo esc_attr($id); ?>" <?php $this->print_render_attribute_string('twitter_grid'); ?>>
		<div class="bdt-grid bdt-grid-<?php echo esc_attr($settings['column_gap']); ?> bdt-child-width-1-<?php echo esc_attr($mobile_cols); ?> bdt-child-width-1-<?php echo esc_attr($tablet_cols); ?>@s bdt-child-width-1-<?php echo esc_attr($desktop_cols); ?>@l" data-bdt-grid>

		<?php

		} elseif ( 'carousel' === $type ) {
		$settings = $this->get_settings_for_display();

		//Global Function
		$this->render_swiper_header_attribute( 'twitter-carousel' );

		$this->add_render_attribute( 'carousel', 'class', 'bdt-twitter-carousel bdt-carousel' );

		if ( $settings['match_height'] ) {
			$this->add_render_attribute( 'carousel', 'data-bdt-height-match', 'target: > div > div > .bdt-carousel-item > div > div > .bdt-twitter-text' );
		}

		?>
		<div <?php $this->print_render_attribute_string( 'carousel' ); ?>>
			<div <?php $this->print_render_attribute_string( 'swiper' ); ?>>
				<div class="swiper-wrapper">
					<?php

		} else {
		$id       = 'bdt-twitter-slider-' . $this->get_id();
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('slider', 'id', $id);
		$this->add_render_attribute('slider', 'class', 'bdt-twitter-slider bdt-carousel');

		if ('arrows' == $settings['navigation']) {
			$this->add_render_attribute('slider', 'class', 'bdt-arrows-align-' . $settings['arrows_position']);
		} elseif ('dots' == $settings['navigation']) {
			$this->add_render_attribute('slider', 'class', 'bdt-dots-align-' . $settings['dots_position']);
		} elseif ('both' == $settings['navigation']) {
			$this->add_render_attribute('slider', 'class', 'bdt-arrows-dots-align-' . $settings['both_position']);
		} elseif ('arrows-fraction' == $settings['navigation']) {
			$this->add_render_attribute('slider', 'class', 'bdt-arrows-dots-align-' . $settings['arrows_fraction_position']);
		}

		if ('arrows-fraction' == $settings['navigation']) {
			$pagination_type = 'fraction';
		} elseif ('both' == $settings['navigation'] or 'dots' == $settings['navigation']) {
			$pagination_type = 'bullets';
		} elseif ('progressbar' == $settings['navigation']) {
			$pagination_type = 'progressbar';
		} else {
			$pagination_type = '';
		}

		$this->add_render_attribute(
			[
				'slider' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							'autoplay'     => ('yes' == $settings['autoplay']) ? ['delay' => $settings['autoplay_speed']] : false,
							'loop'         => ($settings['loop'] == 'yes') ? true : false,
							'speed'        => $settings['speed']['size'],
							'pauseOnHover' => ('yes' == $settings['pauseonhover']) ? true : false,
							'effect'       => $settings['transition'],
							'navigation'   => [
								'nextEl' => '#' . $id . ' .bdt-navigation-next',
								'prevEl' => '#' . $id . ' .bdt-navigation-prev',
							],
							"pagination"   => [
								"el"             => "#" . $id . " .swiper-pagination",
								"type"           => $pagination_type,
								"clickable"      => "true",
								'autoHeight'     => true,
								'dynamicBullets' => ("yes" == $settings["dynamic_bullets"]) ? true : false,
							],
							"scrollbar"    => [
								"el"   => "#" . $id . " .swiper-scrollbar",
								"hide" => "true",
							],
						]))
					]
				]
			]
		);

		$this->add_render_attribute('swiper', 'class', 'swiper-carousel swiper');

		?>
		<div <?php $this->print_render_attribute_string('slider'); ?>>
			<div <?php $this->print_render_attribute_string('swiper'); ?>>
				<div class="swiper-wrapper">
			<?php

		}
	}

	protected function render_twitter_footer( $type ) {
		if ( 'grid' === $type ) {
		$settings = $this->get_settings_for_display();

		?>

		</div>
		</div>
		<?php

		} else {
			$this->render_footer();
		}
	}

	// ========== Testimonial Widget Family ==========

	protected function register_testimonial_controls( $type ) {
		switch ( $type ) {
			case 'grid':
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
            'show_designation',
            [
                'label'   => esc_html__( 'Designation', 'bdthemes-element-pack' ) . BDTEP_NC,
                'type'    => Controls_Manager::SWITCHER,
                'default' => '',
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
            $taxonomies = $this->get_testimonial_taxonomies( $key );
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
            'section_style_designation',
            [
                'label'     => esc_html__( 'Designation', 'bdthemes-element-pack' ) . BDTEP_NC,
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [ 'show_designation' => 'yes' ],
            ]
        );

        $this->add_control(
            'designation_color',
            [
                'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-designation' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'designation_margin',
            [
                'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-designation' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'designation_typography',
                'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
                'selector' => '{{WRAPPER}} .bdt-testimonial-grid .bdt-testimonial-grid-designation',
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
				break;
			case 'carousel':
				$slides_per_view = range( 1, 10 );
		$slides_per_view = array_combine( $slides_per_view, $slides_per_view );

		$this->start_controls_section(
			'section_content_layout',
			[ 
				'label' => esc_html__( 'Layout', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'layout_style',
			[ 
				'label'     => esc_html__( 'Layout Style', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'style-1',
				'options'   => [ 
					'style-1' => '01',
					'style-2' => '02',
					'style-3' => '03',
				],
				'condition' => [ 
					'_skin' => 'bdt-twyla',
				],
			]
		);

		//swiper carousel columns & item gap controls
		$this->register_carousel_column_gap_controls();

		$this->add_control(
			'show_image',
			[ 
				'label'     => esc_html__( 'Testimonial Image', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
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
			'show_designation',
			[
				'label'   => esc_html__( 'Designation', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
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
			]
		);

		$this->add_control(
			'show_text',
			[ 
				'label'     => esc_html__( 'Text', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'text_limit',
			[ 
				'label'       => esc_html__( 'Text Limit', 'bdthemes-element-pack' ),
				'description' => esc_html__( 'It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 40,
				'condition'   => [ 
					'show_text' => 'yes',
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
				'label'     => esc_html__( 'Rating', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'rating_bullet',
			[ 
				'label'        => esc_html__( 'Rating Bullet', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-rating-bullet--',
				'render_type'  => 'template',
				'condition'    => [ 
					'show_rating' => 'yes',
					// '_skin' => 'bdt-vyxo',
				],
			]
		);

		$this->add_control(
			'rating_position',
			[ 
				'label'     => esc_html__( 'Rating Position', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom',
				'options'   => [ 
					'top'    => __( 'Top', 'bdthemes-element-pack' ),
					'bottom' => __( 'Bottom', 'bdthemes-element-pack' ),
				],
				'condition' => [ 
					'show_rating' => 'yes',
					'_skin'       => 'bdt-vyxo',
				],
			]
		);

		$this->add_control(
			'show_review_platform',
			[ 
				'label'     => esc_html__( 'Review Platform', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'content_alignment',
			[ 
				'label'     => esc_html__( 'Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [ 
					'left'   => [ 
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [ 
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [ 
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item-wrapper, {{WRAPPER}} .bdt-testimonial-carousel.skin-vyxo .bdt-testimonial-carousel-item' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_match_height',
			[ 
				'label'       => esc_html__( 'Item Match Height', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'render_type' => 'template',
				'prefix_class' => 'bdt-testimonial-carousel--match-height-',
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
					'Organization' => esc_html__( 'Organization', 'bdthemes-element-pack' ),
					'Product'      => esc_html__( 'Product', 'bdthemes-element-pack' ),
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
				'type'    => Controls_Manager::HIDDEN,
				'options' => $this->getGroupControlQueryPostTypes(),
				'default' => 'bdthemes-testimonial',

			]
		);
		$this->update_control(
			'posts_per_page',
			[ 
				'default' => 10,
			]
		);
		$this->end_controls_section();

		//Navigation Controls
		$this->start_controls_section(
			'section_content_navigation',
			[ 
				'label' => __( 'Navigation', 'bdthemes-element-pack' ),
			]
		);

		//Global Navigation Controls
		$this->register_navigation_controls();

		$this->end_controls_section();

		//Global Carousel Settings Controls
		$this->register_carousel_settings_controls();

		//Style
		$this->start_controls_section(
			'section_style_item',
			[ 
				'label' => esc_html__( 'Items', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// content padding
		$this->add_responsive_control(
			'content_padding',
			[ 
				'label'      => esc_html__( 'Content Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .skin-twyla .bdt-twyla-content-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [ 
					'_skin' => 'bdt-twyla',
				],
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
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item-wrapper' => 'background-color: {{VALUE}};',
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
				'selector'    => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item, {{WRAPPER}} .bdt-testimonial-carousel .swiper-carousel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item',
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'shadow_mode',
			[ 
				'label'        => esc_html__( 'Shadow Mode', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-ep-shadow-mode-',
			]
		);

		$this->add_control(
			'shadow_color',
			[ 
				'label'     => esc_html__( 'Shadow Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'shadow_mode' => 'yes',
				],
				'selectors' => [ 
					'{{WRAPPER}}.bdt-ep-shadow-mode-yes:before' => is_rtl() ? 'background: linear-gradient(to left, {{VALUE}} 5%,rgba(255,255,255,0) 100%);' : 'background: linear-gradient(to right, {{VALUE}} 5%,rgba(255,255,255,0) 100%);',
					'{{WRAPPER}}.bdt-ep-shadow-mode-yes:after'  => is_rtl() ? 'background: linear-gradient(to left, rgba(255,255,255,0) 0%, {{VALUE}} 95%);' : 'background: linear-gradient(to right, rgba(255,255,255,0) 0%, {{VALUE}} 95%);',
				],
			]
		);

		$this->add_control(
			'item_opacity',
			[ 
				'label'     => esc_html__( 'Opacity', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min'  => 0,
						'step' => 0.1,
						'max'  => 1,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item' => 'opacity: {{SIZE}};',
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
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item-wrapper:hover' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'item_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item:hover',
			]
		);

		$this->add_responsive_control(
			'item_shadow_padding',
			[ 
				'label'       => __( 'Match Padding', 'bdthemes-element-pack' ),
				'description' => __( 'You have to add padding for matching overlaping hover shadow', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [ 
					'px' => [ 
						'min'  => 0,
						'step' => 1,
						'max'  => 50,
					],
				],
				'default'     => [ 
					'size' => 10,
				],
				'selectors'   => [ 
						'{{WRAPPER}} .swiper-carousel' => 'padding: {{SIZE}}{{UNIT}}; margin: 0 -{{SIZE}}{{UNIT}};',
					],
			]
		);

		$this->add_control(
			'item_hover_opacity',
			[ 
				'label'     => esc_html__( 'Opacity', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min'  => 0,
						'step' => 0.1,
						'max'  => 1,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item:hover' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_active',
			[ 
				'label' => __( 'Active', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'item_active_background',
			[ 
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item.swiper-slide-active .bdt-testimonial-carousel-item-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_active_border_color',
			[ 
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'item_border_border!' => '',
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item.swiper-slide-active' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'item_active_shadow',
				'selector' => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item.swiper-slide-active',
			]
		);

		$this->add_control(
			'item_active_opacity',
			[ 
				'label'     => esc_html__( 'Opacity', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min'  => 0,
						'step' => 0.1,
						'max'  => 1,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-item.swiper-slide-active' => 'opacity: {{SIZE}};',
				],
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
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'image_background_color',
				'selector' => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-img-wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'image_border',
				'label'       => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-img-wrapper',
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
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-img-wrapper:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-img-wrapper, {{WRAPPER}} .bdt-testimonial-carousel-img-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'image_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-img-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'image_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-img-wrapper',
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
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-img-wrapper' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'image_offset',
			[ 
				'label'     => esc_html__( 'Vertical Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-img-wrapper' => 'transform: translateY({{SIZE}}px);',
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
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_active_color',
			[ 
				'label'     => esc_html__( 'Active Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .swiper-slide-active .bdt-testimonial-carousel-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_designation',
			[
				'label'     => esc_html__( 'Designation', 'bdthemes-element-pack' ) . BDTEP_NC,
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_designation' => 'yes' ],
			]
		);

		$this->add_control(
			'designation_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-designation' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'designation_active_color',
			[
				'label'     => esc_html__( 'Active Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-testimonial-carousel .swiper-slide-active .bdt-testimonial-carousel-designation' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'designation_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-designation',
			]
		);

		$this->add_responsive_control(
			'designation_margin',
			[
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-designation' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-address' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'address_active_color',
			[ 
				'label'     => esc_html__( 'Company Name/Address Active Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .swiper-slide-active .bdt-testimonial-carousel-address' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'address_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel-address' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'address_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-address',
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
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_active_color',
			[ 
				'label'     => esc_html__( 'Active Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .swiper-slide-active .bdt-testimonial-carousel-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_top_border_color',
			[ 
				'label'     => esc_html__( 'Top Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-text' => 'border-top-color: {{VALUE}};',
				],
				'condition' => [ 
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'active_text_top_border_color',
			[ 
				'label'     => esc_html__( 'Top Border Active Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .swiper-slide-active .bdt-testimonial-carousel-text' => 'border-top-color: {{VALUE}};',
				],
				'condition' => [ 
					'_skin' => '',
				],
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-text',
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
					'show_review_platform' => 'yes',
				],
			]
		);

		$this->add_control(
			'rating_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e7e7e7',
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-rating .bdt-rating-item' => 'color: {{VALUE}};',
				],
				'condition' => [ 
					'original_color' => '',
				],
			]
		);

		$this->add_control(
			'active_rating_color',
			[ 
				'label'     => esc_html__( 'Active Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFCC00',
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-rating.bdt-rating-1 .bdt-rating-item:nth-of-type(1)'    => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-rating.bdt-rating-2 .bdt-rating-item:nth-of-type(-n+2)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-rating.bdt-rating-3 .bdt-rating-item:nth-of-type(-n+3)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-rating.bdt-rating-4 .bdt-rating-item:nth-of-type(-n+4)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-rating.bdt-rating-5 .bdt-rating-item:nth-of-type(-n+5)' => 'color: {{VALUE}};',
				],
				'condition' => [ 
					'original_color' => '',
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
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-rating .bdt-rating-item' => 'font-size: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-rating .bdt-rating-item' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rating_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-carousel .bdt-testimonial-carousel-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_review_platform',
			[ 
				'label'     => __( 'Review Platform', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_review_platform' => 'yes',
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
			Group_Control_Border::get_type(), [ 
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
			'section_style_quatation',
			[ 
				'label'     => esc_html__( 'Quatation', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'_skin'         => 'bdt-twyla',
					'layout_style!' => 'style-1',
				],
			]
		);

		$this->add_control(
			'quatation_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .skin-twyla .testimonial-item-header::after' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'quatation_background_color',
				'selector' => '{{WRAPPER}} .skin-twyla .testimonial-item-header::after',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(), [ 
				'name'        => 'quatation_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .skin-twyla .testimonial-item-header::after',
			]
		);

		$this->add_responsive_control(
			'quatation_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .skin-twyla .testimonial-item-header::after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'testimonial_quatation_size',
			[ 
				'label'     => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 10,
						'max' => 500,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .skin-twyla .testimonial-item-header::after' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; line-height: calc(20px + {{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_responsive_control(
			'quatation_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .skin-twyla .testimonial-item-header::after' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'quatation_typography',
				'selector' => '{{WRAPPER}} .skin-twyla .testimonial-item-header::after',
			]
		);

		$this->add_control(
			'quatation_offset_toggle',
			[ 
				'label'        => __( 'Offset', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => __( 'None', 'bdthemes-element-pack' ),
				'label_on'     => __( 'Custom', 'bdthemes-element-pack' ),
				'return_value' => 'yes',
				'separator'    => 'before',

			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'quatation_horizontal_offset',
			[ 
				'label'          => __( 'Horizontal Offset', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [ 
					'size' => 0,
				],
				'tablet_default' => [ 
					'size' => 0,
				],
				'mobile_default' => [ 
					'size' => 0,
				],
				'range'          => [ 
					'px' => [ 
						'min'  => -300,
						'step' => 2,
						'max'  => 300,
					],
				],
				'condition'      => [ 
					'quatation_offset_toggle' => 'yes'
				],
				'render_type'    => 'ui',
				'selectors'      => [ 
					'{{WRAPPER}}' => '--ep-testimonial-carousel-quatation-h-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'quatation_vertical_offset',
			[ 
				'label'          => __( 'Vertical Offset', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [ 
					'size' => 0,
				],
				'tablet_default' => [ 
					'size' => 0,
				],
				'mobile_default' => [ 
					'size' => 0,
				],
				'range'          => [ 
					'px' => [ 
						'min'  => -300,
						'step' => 2,
						'max'  => 300,
					],
				],
				'condition'      => [ 
					'quatation_offset_toggle' => 'yes'
				],
				'render_type'    => 'ui',
				'selectors'      => [ 
					'{{WRAPPER}}' => '--ep-testimonial-carousel-quatation-v-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'quatation_rotate',
			[ 
				'label'          => esc_html__( 'Rotate', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [ 
					'size' => 0,
				],
				'tablet_default' => [ 
					'size' => 0,
				],
				'mobile_default' => [ 
					'size' => 0,
				],
				'range'          => [ 
					'px' => [ 
						'min'  => -360,
						'max'  => 360,
						'step' => 5,
					],
				],
				'condition'      => [ 
					'quatation_offset_toggle' => 'yes'
				],
				'render_type'    => 'ui',
				'selectors'      => [ 
					'{{WRAPPER}}' => '--ep-testimonial-carousel-quatation-rotate: {{SIZE}}deg;'
				],
			]
		);

		$this->end_popover();


		$this->end_controls_section();


		//Navigation Style
		$this->start_controls_section(
			'section_style_navigation',
			[ 
				'label'      => __( 'Navigation', 'bdthemes-element-pack' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [ 
					'relation' => 'or',
					'terms'    => [ 
						[ 
							'name'     => 'navigation',
							'operator' => '!=',
							'value'    => 'none',
						],
						[ 
							'name'  => 'show_scrollbar',
							'value' => 'yes',
						],
					],
				],
			]
		);

		//Global Navigation Style Controls
		$this->register_navigation_style_controls( 'swiper-carousel' );

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
				break;
			case 'slider':
				$this->start_controls_section(
			'section_content_layout',
			[ 
				'label' => esc_html__( 'Layout', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'thumb',
			[ 
				'label'     => esc_html__( 'Testimonial Image', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [ 
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'title',
			[ 
				'label'   => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_designation',
			[
				'label'   => esc_html__( 'Designation', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			]
		);

		$this->add_control(
			'company_name',
			[ 
				'label'   => esc_html__( 'Company Name/Address', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'meta_multi_line',
			[ 
				'label' => esc_html__( 'Meta Multiline', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'show_comma',
			[ 
				'label'   => esc_html__( 'Show Comma After Title', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_text',
			[ 
				'label'     => esc_html__( 'Text', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'text_limit',
			[ 
				'label'       => esc_html__( 'Text Limit', 'bdthemes-element-pack' ),
				'description' => esc_html__( 'It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 80,
				'condition'   => [ 
					'show_text' => 'yes',
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
			'rating',
			[ 
				'label'   => esc_html__( 'Rating', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_review_platform',
			[ 
				'label' => esc_html__( 'Review Platform', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'meta_position',
			[ 
				'label'   => __( 'Meta Position', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [ 
					'before' => [ 
						'title' => __( 'Before', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-v-align-top',
					],
					'after'  => [ 
						'title' => __( 'After', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default' => 'after',
				'toggle'  => false,
			]
		);

		$this->add_control(
			'meta_alignment',
			[ 
				'label'        => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [ 
					'left'   => [ 
						'title' => __( 'Start', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [ 
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [ 
						'title' => __( 'End', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'prefix_class' => 'bdt-testi-meta-align-',
				'render_type'  => 'template',
				'toggle'       => false,
				'default'      => 'center',
				'condition'    => [ 
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'alignment',
			[ 
				'label'     => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [ 
					'left'   => [ 
						'title' => __( 'Start', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [ 
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [ 
						'title' => __( 'End', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'left',
				'toggle'    => false,
				'condition' => [ 
					'_skin!' => '',
				],
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
				'type'    => Controls_Manager::HIDDEN,
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
			'section_content_slider_settins',
			[ 
				'label' => esc_html__( 'Slider Settings', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'autoplay',
			[ 
				'label'   => esc_html__( 'Auto Play', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'autoplay_interval',
			[ 
				'label'     => esc_html__( 'Autoplay Speed', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 7000,
				'condition' => [ 
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pause_on_hover',
			[ 
				'label'     => esc_html__( 'Pause on Hover', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [ 
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'velocity',
			[ 
				'label'   => __( 'Animation Speed (ms)', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 500,
			]
		);

		$this->add_control(
			'loop',
			[ 
				'label'   => esc_html__( 'Loop', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'auto-height',
			[ 
				'label' => esc_html__( 'Auto Height', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->end_controls_section();

		//Navigation Controls
		$this->start_controls_section(
			'section_content_navigation',
			[ 
				'label'     => __( 'Navigation', 'bdthemes-element-pack' ),
				'condition' => [ 
					'_skin!' => 'bdt-thumb',
				],
			]
		);

		//Global Navigation Controls
		$this->register_navigation_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_thumb',
			[ 
				'label' => __( 'Item', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'testimonial_background',
			[ 
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-item-inner, {{WRAPPER}} .bdt-testimonial-slider li.bdt-slider-thumbnav .bdt-slider-thumbnav-inner:before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'testimonial_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-item-inner',
			]
		);

		$this->add_responsive_control(
			'testimonial_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-item-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'testimonial_padding',
			[ 
				'label'     => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-item-inner' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_iamge',
			[ 
				'label'     => esc_html__( 'Image', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'_skin!' => 'bdt-thumb',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'testimonial_iamge_background',
				'selector' => '{{WRAPPER}} .bdt-testimonial-slider .bdt-testimonial-thumb',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'testimonial_iamge_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-testimonial-slider .bdt-testimonial-thumb, {{WRAPPER}} .bdt-testimonial-slider .bdt-testimonial-thumb img',
				'separator'   => 'before'
			]
		);

		$this->add_responsive_control(
			'testimonial_image_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-testimonial-thumb, {{WRAPPER}} .bdt-testimonial-slider .bdt-testimonial-thumb img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'testimonial_image_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-testimonial-thumb-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'testimonial_image_size',
			[ 
				'label'     => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 10,
						'max' => 500,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-testimonial-thumb' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [ 
					'_skin' => '',
				],
			]
		);

		$this->add_responsive_control(
			'image_size',
			[ 
				'label'     => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ 
					'size' => 300
				],
				'range'     => [ 
					'px' => [ 
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-testimonial-thumb' => 'min-width: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [ 
					'_skin' => 'bdt-single',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'testimonial_iamge_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-testimonial-slider .bdt-testimonial-thumb',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_quatation',
			[ 
				'label' => esc_html__( 'Quotation', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'quatation_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-text:after, {{WRAPPER}} .bdt-testimonial-slider.skin-single .bdt-testimonial-thumb::after' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'      => 'quatation_background_color',
				'selector'  => '{{WRAPPER}} .bdt-testimonial-slider.skin-single .bdt-testimonial-thumb::after',
				'condition' => [ 
					'_skin' => 'bdt-single',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(), [ 
				'name'        => 'quatation_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-testimonial-slider.skin-single .bdt-testimonial-thumb::after',
				'condition'   => [ 
					'_skin' => 'bdt-single',
				],
			]
		);

		$this->add_responsive_control(
			'quatation_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-slider.skin-single .bdt-testimonial-thumb::after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [ 
					'_skin' => 'bdt-single',
				],
			]
		);

		$this->add_responsive_control(
			'testimonial_quatation_size',
			[ 
				'label'     => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 10,
						'max' => 500,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-slider.skin-single .bdt-testimonial-thumb::after' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; line-height: calc(20px + {{SIZE}}{{UNIT}});',
				],
				'condition' => [ 
					'_skin' => 'bdt-single',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'quatation_typography',
				'selector' => '{{WRAPPER}} .bdt-testimonial-text:after, {{WRAPPER}} .bdt-testimonial-slider.skin-single .bdt-testimonial-thumb::after',
			]
		);


		$this->add_control(
			'quatation_offset_toggle',
			[ 
				'label'        => __( 'Offset', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => __( 'None', 'bdthemes-element-pack' ),
				'label_on'     => __( 'Custom', 'bdthemes-element-pack' ),
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'quatation_horizontal_offset',
			[ 
				'label'          => __( 'Horizontal Offset', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [ 
					'size' => 0,
				],
				'tablet_default' => [ 
					'size' => 0,
				],
				'mobile_default' => [ 
					'size' => 0,
				],
				'range'          => [ 
					'px' => [ 
						'min'  => -300,
						'step' => 1,
						'max'  => 300,
					],
				],
				'condition'      => [ 
					'quatation_offset_toggle' => 'yes'
				],
				'render_type'    => 'ui',
				'selectors'      => [ 
					'{{WRAPPER}}' => '--ep-quatation-h-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'quatation_vertical_offset',
			[ 
				'label'          => __( 'Vertical Offset', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [ 
					'size' => 0,
				],
				'tablet_default' => [ 
					'size' => 0,
				],
				'mobile_default' => [ 
					'size' => 0,
				],
				'range'          => [ 
					'px' => [ 
						'min'  => -300,
						'step' => 1,
						'max'  => 300,
					],
				],
				'condition'      => [ 
					'quatation_offset_toggle' => 'yes'
				],
				'render_type'    => 'ui',
				'selectors'      => [ 
					'{{WRAPPER}}' => '--ep-quatation-v-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'quatation_rotate_x',
			[ 
				'label'          => esc_html__( 'Rotate X', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [ 
					'size' => 0,
				],
				'tablet_default' => [ 
					'size' => 0,
				],
				'mobile_default' => [ 
					'size' => 0,
				],
				'range'          => [ 
					'px' => [ 
						'min'  => -360,
						'max'  => 360,
						'step' => 1,
					],
				],
				'condition'      => [ 
					'quatation_offset_toggle' => 'yes'
				],
				'render_type'    => 'ui',
				'selectors'      => [ 
					'{{WRAPPER}}' => '--ep-quatation-rotate-x: {{SIZE}}deg;'
				],
			]
		);

		$this->add_responsive_control(
			'quatation_rotate_y',
			[ 
				'label'          => esc_html__( 'Rotate Y', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [ 
					'size' => 35,
				],
				'tablet_default' => [ 
					'size' => 35,
				],
				'mobile_default' => [ 
					'size' => 35,
				],
				'range'          => [ 
					'px' => [ 
						'min'  => -360,
						'max'  => 360,
						'step' => 1,
					],
				],
				'condition'      => [ 
					'quatation_offset_toggle' => 'yes'
				],
				'render_type'    => 'ui',
				'selectors'      => [ 
					'{{WRAPPER}}' => '--ep-quatation-rotate-y: {{SIZE}}deg;'
				],
			]
		);

		$this->end_popover();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[ 
				'label'     => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'title' => 'yes' ],
			]
		);

		$this->add_control(
			'title_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-meta .bdt-testimonial-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .bdt-testimonial-meta .bdt-testimonial-title',
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-meta .bdt-testimonial-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_designation',
			[
				'label'     => esc_html__( 'Designation', 'bdthemes-element-pack' ) . BDTEP_NC,
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_designation' => 'yes' ],
			]
		);

		$this->add_control(
			'designation_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-testimonial-meta .bdt-testimonial-designation' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'designation_typography',
				'selector' => '{{WRAPPER}} .bdt-testimonial-meta .bdt-testimonial-designation',
			]
		);

		$this->add_responsive_control(
			'designation_margin',
			[
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-testimonial-meta .bdt-testimonial-designation' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_text',
			[ 
				'label' => esc_html__( 'Text', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .bdt-testimonial-text',
			]
		);

		$this->add_responsive_control(
			'text_cite_space',
			[ 
				'label'     => __( 'Meta Space', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-slider-item-inner > div:first-child' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_address',
			[ 
				'label'     => esc_html__( 'Address', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'company_name' => 'yes',
				],
			]
		);

		$this->add_control(
			'address_color',
			[ 
				'label'     => esc_html__( 'Company Name/Address Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-meta .bdt-testimonial-address' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'address_typography',
				'selector' => '{{WRAPPER}} .bdt-testimonial-meta .bdt-testimonial-address',
			]
		);

		//address margin
		$this->add_responsive_control(
			'address_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-meta .bdt-testimonial-address' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_rating',
			[ 
				'label'     => esc_html__( 'Rating', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'rating' => 'yes',
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
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-rating .bdt-rating-item' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-rating.bdt-rating-1 .bdt-rating-item:nth-of-type(1)'    => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-rating.bdt-rating-2 .bdt-rating-item:nth-of-type(-n+2)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-rating.bdt-rating-3 .bdt-rating-item:nth-of-type(-n+3)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-rating.bdt-rating-4 .bdt-rating-item:nth-of-type(-n+4)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-rating.bdt-rating-5 .bdt-rating-item:nth-of-type(-n+5)' => 'color: {{VALUE}};',
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
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();

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
			Group_Control_Border::get_type(), [ 
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
			'section_style_thumbs',
			[ 
				'label'     => esc_html__( 'Thumbs', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'_skin' => 'bdt-thumb',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_thumbs_style' );

		$this->start_controls_tab(
			'tab_thumbs_normal',
			[ 
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'hide_arrow_style',
			[ 
				'label'        => esc_html__( 'Hide Arrow Style', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-arrow-style-hide-',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'thumb_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-thumbnav-inner img',
			]
		);

		$this->add_responsive_control(
			'thumb_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-thumbnav-inner img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumb_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-thumbnav-inner img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumb_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-thumbnav-inner img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'thumb_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-thumbnav-inner img'
			]
		);

		$this->add_control(
			'thumb_opacity',
			[ 
				'label'     => __( 'Opacity', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min'  => 0.05,
						'max'  => 1,
						'step' => 0.05,
					],
				],
				'default'   => [ 
					'size' => 0.8,
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-thumbnav-inner img' => 'opacity: {{SIZE}};',
				],

			]
		);

		$this->add_responsive_control(
			'horizontal_spacing',
			[ 
				'label'     => esc_html__( 'Horizontal Space', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ 
					'size' => 20,
				],
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-thumbnav:not(:first-child)' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'vertical_spacing',
			[ 
				'label'     => esc_html__( 'Vertical Space', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ 
					'size' => 0,
				],
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-slider-thumbnav-inner' => 'padding-top: calc({{SIZE}}{{UNIT}} + 20px);',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_thumbs_active',
			[ 
				'label' => __( 'Active', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'active_thumb_border_color',
			[ 
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-active .bdt-slider-thumbnav-inner img' => 'border-color: {{VALUE}};',
				],
				'condition' => [ 
					'thumb_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'thumb_hover_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-testimonial-slider .bdt-active .bdt-slider-thumbnav-inner img'
			]
		);

		$this->add_control(
			'active_thumb_opacity',
			[ 
				'label'     => __( 'Opacity', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min'  => 0.05,
						'max'  => 1,
						'step' => 0.05,
					],
				],
				'default'   => [ 
					'size' => 1,
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-testimonial-slider .bdt-active .bdt-slider-thumbnav-inner img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		//Navigation Style
		$this->start_controls_section(
			'section_style_navigation',
			[ 
				'label'      => __( 'Navigation', 'bdthemes-element-pack' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [ 
					'relation' => 'and',
					'terms'    => [ 
						[ 
							'name'     => '_skin',
							'operator' => '!==',
							'value'    => 'bdt-thumb'
						],
						[ 
							'relation' => 'or',
							'terms'    => [ 
								[ 
									'name'     => 'navigation',
									'operator' => '!=',
									'value'    => 'none',
								],
								[ 
									'name'  => 'show_scrollbar',
									'value' => 'yes',
								]
							]
						]
					]
				]
			]
		);

		//Global Navigation Style Controls
		$this->register_navigation_style_controls( 'swiper-carousel' );

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
				break;
		}
	}

	protected function get_testimonial_taxonomies( $post_type = '' ) {
		$result = [];
        if ( $post_type ) {
            $taxonomies = get_taxonomies( [ 'public' => true, 'object_type' => [ $post_type ] ], 'object' );
            $tax       = array_diff_key( wp_list_pluck( $taxonomies, 'label', 'name' ), [] );
            $result[ $post_type ] = $tax !== [] ? $tax : '';
        }
        return $result;
	}

	protected function get_testimonial_filter_menu_terms( $settings ) {
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

	protected function get_testimonial_filter_menu_categories( $settings ) {
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

	protected function render_testimonial_filter_menu( $settings ) {
		$testi_categories = $this->get_testimonial_filter_menu_categories( $settings );
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

	protected function get_testimonial_sanitized_rating( $post_id ) {
		$raw = get_post_meta( $post_id, 'bdthemes_tm_rating', true );
        $num = intval( $raw );
        if ( $num < 1 && is_string( $raw ) && preg_match( '/\d+/', $raw, $m ) ) {
            $num = intval( $m[0] );
        }
        return max( 1, min( 5, $num ) );
	}

	protected function render_testimonial_schema_item_reviewed( $settings ) {
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

	protected function render_testimonial_rating_schema_only( $post_id, $settings ) {
		if ( empty( $settings['schema_rich_results'] ) || $settings['schema_rich_results'] !== 'yes' ) {
			return;
		}
		if ( ( isset( $settings['show_rating'] ) && $settings['show_rating'] === 'yes' ) || ( isset( $settings['rating'] ) && $settings['rating'] === 'yes' ) ) {
			return;
		}
		$rating   = $this->get_testimonial_sanitized_rating( $post_id );
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

	protected function render_testimonial_review_platform( $post_id, $settings ) {
		if ( empty( $settings['show_review_platform'] ) || $settings['show_review_platform'] !== 'yes' ) {
			return;
		}

		$platform    = get_post_meta( $post_id, 'bdthemes_tm_platform', true );
		$review_link = get_post_meta( $post_id, 'bdthemes_tm_review_link', true );

		if ( ! $platform ) {
			$platform = 'self';
		}

		if ( ! $review_link ) {
			$review_link = '#';
		}

		?>
		<a href="<?php echo esc_url( $review_link ); ?>" class="bdt-review-platform bdt-flex-inline"
			bdt-tooltip="<?php echo wp_kses_post( $platform ); ?>">
			<i class="ep-icon-<?php echo esc_attr( strtolower( $platform ) ); ?> bdt-platform-icon bdt-flex bdt-flex-middle bdt-flex-center"
				aria-hidden="true"></i>
		</a>
		<?php
	}

	protected function query_testimonial_posts( $type, $posts_per_page ) {
		if ( 'grid' === $type ) {
			$raw              = $this->get_settings();
        $posts_per_page   = isset( $posts_per_page ) ? (int) $posts_per_page : 0;
        $args             = $this->getGroupControlQueryArgs();
        $is_current_query = ( ! empty( $raw['posts_source'] ) && $raw['posts_source'] === 'current_query' );

        if ( $is_current_query ) {
            unset( $args['offset'] );
            unset( $args['no_found_rows'] );
            $posts_per_page = 0;
        }

        if ( $posts_per_page > 0 ) {
            $args['posts_per_page'] = $posts_per_page;
        } else {
            $args['posts_per_page'] = (int) get_option( 'posts_per_page', 10 );
        }

        if ( ! empty( $raw['show_pagination'] ) ) {
            $args['paged'] = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
        }

        $this->_query = new \WP_Query( $args );
        return $this->_query;
			return $this->_query;
		}
		if ( 'slider' === $type ) {
			$args = [
			'posts_per_page' => $posts_per_page,
			'paged'          => max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) ),
		];
		$args         = array_merge( $this->getGroupControlQueryArgs(), $args );
		$this->_query = new \WP_Query( $args );
		return $this->_query;
			return $this->_query;
		}
		$args                   = [];
		$args['posts_per_page'] = $posts_per_page;
		$args['paged']          = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );

		$default = $this->getGroupControlQueryArgs();
		$args    = array_merge( $default, $args );

		return $this->_query = new \WP_Query( $args );
		return $this->_query;
	}

	protected function render_testimonial_title( $type, $post_id, $settings ) {
		if ( 'carousel' === $type ) {
			if ( empty( $settings['show_title'] ) || $settings['show_title'] !== 'yes' ) {
			return;
		}

		$company_name = get_post_meta( $post_id, 'bdthemes_tm_company_name', true );
		$author_name  = get_the_title( $post_id );
		$use_schema   = ! empty( $settings['schema_rich_results'] ) && $settings['schema_rich_results'] === 'yes';
		$show_comma   = ! empty( $settings['show_comma'] ) && $settings['show_comma'] === 'yes';
		$show_address = ! empty( $settings['show_address'] ) && $settings['show_address'] === 'yes';
		$show_comma_condition = $show_comma && $show_address && ! empty( $company_name );

		if ( $use_schema ) {
			?>
			<h4 class="bdt-testimonial-carousel-title bdt-margin-remove-bottom">
				<span itemprop="author" itemscope itemtype="https://schema.org/Person">
					<span itemprop="name"><?php echo esc_html( $author_name ); ?></span>
				</span><?php if ( $show_comma_condition ) {
					echo ', ';
				} ?>
			</h4>
			<?php
		} else {
			?>
			<h4 class="bdt-testimonial-carousel-title bdt-margin-remove-bottom" itemprop="name">
				<?php echo esc_html( $author_name ); ?><?php if ( $show_comma_condition ) {
					echo ', ';
				} ?>
			</h4>
			<?php
		}
			return;
		}
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

	protected function render_testimonial_address( $type, $post_id, $settings ) {
		if ( 'carousel' === $type ) {
			if ( empty( $settings['show_address'] ) || $settings['show_address'] !== 'yes' ) {
			return;
		}

		$company_name = get_post_meta( $post_id, 'bdthemes_tm_company_name', true );
		if ( empty( $company_name ) ) {
			return;
		}
		?>
		<p class="bdt-testimonial-carousel-address bdt-text-meta">
			<?php echo wp_kses_post( $company_name ); ?>
		</p>
		<?php
			return;
		}
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

	protected function render_testimonial_designation( $type, $post_id, $settings ) {
		if ( 'carousel' === $type ) {
			if ( empty( $settings['show_designation'] ) || $settings['show_designation'] !== 'yes' ) {
						return;
					}
					$designation = get_post_meta( $post_id, 'bdthemes_tm_designation', true );
					if ( empty( $designation ) ) {
						return;
					}
					?>
					<p class="bdt-testimonial-carousel-designation">
						<?php echo esc_html( $designation ); ?>
					</p>
					<?php
			return;
		}
		if ( 'slider' === $type ) {
			if ( empty( $settings['show_designation'] ) || $settings['show_designation'] !== 'yes' ) {
						return;
					}
					$designation = get_post_meta( $post_id, 'bdthemes_tm_designation', true );
					if ( empty( $designation ) ) {
						return;
					}
					?>
					<div class="bdt-testimonial-designation">
						<?php echo esc_html( $designation ); ?>
					</div>
					<?php
			return;
		}
		if ( $settings['show_designation'] !== 'yes' ) {
		            return;
		        }
		        $designation = get_post_meta( $post_id, 'bdthemes_tm_designation', true );
		        if ( empty( $designation ) ) {
		            return;
		        }
		        ?>
		        <p class="bdt-testimonial-grid-designation bdt-text-meta bdt-margin-remove">
		            <?php echo esc_html( $designation ); ?>
		        </p>
		        <?php
	}

	protected function render_testimonial_rating( $type, $post_id, $settings ) {
		if ( 'carousel' === $type ) {
			if ( empty( $settings['show_rating'] ) || $settings['show_rating'] !== 'yes' ) {
			return;
		}

		$rating         = $this->get_testimonial_sanitized_rating( $post_id );
		$date_published = get_the_date( 'c', $post_id );
		?>
	<meta itemprop="datePublished" content="<?php echo esc_attr( $date_published ); ?>">
	<div itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
		<meta itemprop="worstRating" content="1">
		<meta itemprop="ratingValue" content="<?php echo absint( $rating ); ?>">
		<meta itemprop="bestRating" content="5">
		<ul class="bdt-rating bdt-rating-<?php echo absint( $rating ); ?> bdt-grid bdt-grid-collapse" data-bdt-grid>
			<li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
			<li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
			<li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
			<li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
			<li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
		</ul>
	</div>
		<?php
			return;
		}
		if ( 'slider' === $type ) {
			// Slider uses render_testimonial_slider_meta for rating display.
			return;
		}
		if ( $settings['show_rating'] !== 'yes' ) {
            return;
        }
        $rating = $this->get_testimonial_sanitized_rating( $post_id );
        $date   = get_the_date( 'c', $post_id );
        ?>
        <div class="bdt-testimonial-grid-rating">
            <meta itemprop="datePublished" content="<?php echo esc_attr( $date ); ?>">
            <div itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
                <meta itemprop="worstRating" content="1">
                <meta itemprop="ratingValue" content="<?php echo absint( $rating ); ?>">
                <meta itemprop="bestRating" content="5">
                <ul class="bdt-rating bdt-rating-<?php echo absint( $rating ); ?> bdt-grid bdt-grid-collapse" data-bdt-grid>
                    <li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
                    <li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
                    <li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
                    <li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
                    <li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
                </ul>
            </div>
        </div>
        <?php
	}

	protected function render_testimonial_image( $type, $image_id, $settings ) {
		if ( 'carousel' === $type ) {
			if ( empty( $settings['show_image'] ) || $settings['show_image'] !== 'yes' ) {
			return;
		}

		$testimonial_thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $image_id ), 'medium' );
		$thumb_url        = $testimonial_thumb ? $testimonial_thumb[0] : BDTEP_ASSETS_URL . 'images/member.svg';
		$title_alt        = get_the_title( $image_id );
		?>
		<div class="bdt-width-auto bdt-flex bdt-position-relative">
			<div class="bdt-testimonial-carousel-img-wrapper bdt-overflow-hidden bdt-border-circle bdt-background-cover">
				<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $title_alt ); ?>" />
			</div>
			<?php $this->render_testimonial_review_platform( $image_id, $settings ); ?>
		</div>
		<?php
			return;
		}
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
            <?php $this->render_testimonial_review_platform( $image_id, $settings ); ?>
        </div>
        <?php
	}

	protected function render_testimonial_excerpt( $type, $settings ) {
		if ( 'carousel' === $type ) {
			if ( empty( $settings['show_text'] ) || $settings['show_text'] !== 'yes' ) {
			return;
		}

		$this->add_render_attribute(
			[
				'text-wrap' => [
					'class'     => [ 'bdt-testimonial-carousel-text' ],
					'itemprop'  => [ 'description' ],
				],
			],
			'',
			'',
			true
		);

		$strip_shortcode = isset( $settings['strip_shortcode'] ) ? $settings['strip_shortcode'] : '';
		$read_more_toggle = ! empty( $settings['text_read_more_toggle'] ) && $settings['text_read_more_toggle'] === 'yes';

		if ( $read_more_toggle ) {
			$text_limit = isset( $settings['text_limit'] ) && ! empty( $settings['text_limit'] ) ? (int) $settings['text_limit'] : 0;
			if ( $text_limit > 0 ) {
				$this->add_render_attribute( 'text-wrap', 'class', 'bdt-ep-read-more-text', true );
				$this->add_render_attribute(
					'text-wrap',
					'data-read-more',
					wp_json_encode( [ 'words_length' => $text_limit ] ),
					true
				);
			}
			$text_limit = 0;
		} else {
			$text_limit = isset( $settings['text_limit'] ) ? (int) $settings['text_limit'] : 0;
		}

		?>
		<div <?php $this->print_render_attribute_string( 'text-wrap' ); ?>>
			<?php
			if ( has_excerpt() ) {
				the_excerpt();
			} else {
				echo wp_kses_post( element_pack_custom_excerpt( $text_limit, $strip_shortcode ) );
			}
			?>
		</div>
		<?php
			return;
		}
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

	protected function render_testimonial_grid_header( $settings ) {
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
                <?php $this->render_testimonial_filter_menu( $settings ); ?>
            <?php endif; ?>
        <?php
	}

	protected function render_testimonial_grid_footer() {
		?>
        </div>
        <?php
	}

	protected function render_testimonial_grid_loop_item( $settings ) {
		$widget_id = $this->get_id();
        $per_page  = isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 4;
        $wp_query  = $this->query_testimonial_posts( 'grid',  $per_page );

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
                    $this->add_render_attribute( $item_key, 'data-filter', $this->get_testimonial_filter_menu_terms( $settings ) );
                }
                ?>
                <div <?php $this->print_render_attribute_string( $item_key ); ?>>
                    <?php $this->render_testimonial_schema_item_reviewed( $settings ); ?>
                    <?php if ( $schema_on && ! $show_rating ) : ?>
                        <?php $this->render_testimonial_rating_schema_only( $post_id, $settings ); ?>
                    <?php endif; ?>

                    <?php if ( $layout === '1' ) : ?>
                        <div class="bdt-testimonial-grid-item-inner">
                            <div class="bdt-grid bdt-position-relative bdt-grid-small bdt-flex-middle" data-bdt-grid>
                                <?php $this->render_testimonial_image( 'grid',  $post_id, $settings ); ?>
                                <?php if ( $show_title || $show_address ) : ?>
                                    <div class="bdt-testimonial-grid-title-address <?php echo $meta_multi ? 'bdt-meta-multi-line' : ''; ?>">
                                        <?php
                                        $this->render_testimonial_title( 'grid',  $post_id, $settings );
                                        $this->render_testimonial_designation( 'grid',  $post_id, $settings );
                                        $this->render_testimonial_address( 'grid',  $post_id, $settings );
                                        if ( ! $rating_above && $show_rating ) :
                                            if ( (int) $columns >= 3 ) :
                                                $this->render_testimonial_rating( 'grid',  $post_id, $settings );
                                            elseif ( (int) $columns <= 2 ) : ?>
                                                <div class="bdt-position-center-right bdt-text-right">
                                                    <?php $this->render_testimonial_rating( 'grid',  $post_id, $settings ); ?>
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
                                        <?php $this->render_testimonial_rating( 'grid',  $post_id, $settings ); ?>
                                    <?php endif; ?>
                                    <?php $this->render_testimonial_excerpt( 'grid',  $settings ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( $layout === '2' ) : ?>
                        <div class="bdt-testimonial-grid-item-inner bdt-position-relative bdt-text-center">
                            <div class="bdt-position-relative bdt-flex-inline">
                                <?php $this->render_testimonial_image( 'grid',  $post_id, $settings ); ?>
                            </div>
                            <?php if ( $show_title || $show_address ) : ?>
                                <div class="bdt-testimonial-grid-title-address <?php echo $meta_multi ? 'bdt-meta-multi-line' : ''; ?>">
                                    <?php
                                    $this->render_testimonial_title( 'grid',  $post_id, $settings );
                                    $this->render_testimonial_designation( 'grid',  $post_id, $settings );
                                    $this->render_testimonial_address( 'grid',  $post_id, $settings );
                                    ?>
                                </div>
                            <?php endif; ?>
                            <?php $this->render_testimonial_excerpt( 'grid',  $settings ); ?>
                            <?php $this->render_testimonial_rating( 'grid',  $post_id, $settings ); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( $layout === '3' ) : ?>
                        <div class="bdt-testimonial-grid-item-inner">
                            <?php $this->render_testimonial_excerpt( 'grid',  $settings ); ?>
                            <div class="bdt-grid bdt-position-relative bdt-grid-small bdt-flex-middle" data-bdt-grid>
                                <?php $this->render_testimonial_image( 'grid',  $post_id, $settings ); ?>
                                <?php if ( $show_title || $show_address ) : ?>
                                    <div class="bdt-testimonial-grid-title-address <?php echo $meta_multi ? 'bdt-meta-multi-line' : ''; ?>">
                                        <?php
                                        $this->render_testimonial_title( 'grid',  $post_id, $settings );
                                        if ( $show_address ) {
                                            $this->render_testimonial_designation( 'grid',  $post_id, $settings );
                                        }
                                        $this->render_testimonial_address( 'grid',  $post_id, $settings );
                                        if ( $show_rating ) :
                                            if ( (int) $columns >= 3 ) :
                                                $this->render_testimonial_rating( 'grid',  $post_id, $settings );
                                            elseif ( (int) $columns <= 2 ) : ?>
                                                <div class="bdt-position-center-right bdt-text-right">
                                                    <?php $this->render_testimonial_rating( 'grid',  $post_id, $settings ); ?>
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

	protected function render_testimonial_grid() {
		$settings = $this->get_settings_for_display();
		$this->render_testimonial_grid_header( $settings );
		$this->render_testimonial_grid_loop_item( $settings );
		$this->render_testimonial_grid_footer();
	}

	protected function render_testimonial_carousel_header( $skin, $settings ) {
		$this->render_swiper_header_attribute( 'testimonial-carousel' );

		$layout_style = isset( $settings['layout_style'] ) ? $settings['layout_style'] : 'style-1';
		$this->add_render_attribute( 'carousel', 'class', 'bdt-testimonial-carousel bdt-testimonials-twyla-' . esc_attr( $layout_style ) . ' skin-' . esc_attr( $skin ) );

		if ( ! empty( $settings['item_match_height'] ) && $settings['item_match_height'] === 'yes' ) {
			$this->add_render_attribute( 'carousel', 'data-bdt-height-match', 'target: > div > div > div > div > .bdt-testimonial-carousel-text' );
		}

		?>
		<div <?php $this->print_render_attribute_string( 'carousel' ); ?>>
			<div <?php $this->print_render_attribute_string( 'swiper' ); ?>>
				<div class="swiper-wrapper">
					<?php
	}

	protected function render_testimonial_carousel_loop_item( $settings ) {
		$posts_per_page = isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 6;
		$wp_query       = $this->query_testimonial_posts( 'carousel',  $posts_per_page );

		if ( ! $wp_query->have_posts() ) {
			echo '<div class="bdt-alert-warning" bdt-alert>' . esc_html_x( 'Oppps!! There is no post, please select actual post or categories.', 'Frontend', 'bdthemes-element-pack' ) . '<div>';
			return;
		}

		while ( $wp_query->have_posts() ) :
			$wp_query->the_post();
			$post_id  = get_the_ID();
			$platform = get_post_meta( $post_id, 'bdthemes_tm_platform', true );
			$platform = ! empty( $platform ) ? strtolower( $platform ) : 'self';

			$show_rating  = ! empty( $settings['show_rating'] ) && $settings['show_rating'] === 'yes';
			$show_text    = ! empty( $settings['show_text'] ) && $settings['show_text'] === 'yes';
			$show_address = ! empty( $settings['show_address'] ) && $settings['show_address'] === 'yes';
			$schema_on    = ! empty( $settings['schema_rich_results'] ) && $settings['schema_rich_results'] === 'yes';
			$meta_multi_line = ! empty( $settings['meta_multi_line'] ) && $settings['meta_multi_line'] === 'yes';
			?>
			<div class="swiper-slide bdt-testimonial-carousel-item bdt-review-<?php echo esc_attr( $platform ); ?>"
				itemprop="review" itemscope itemtype="https://schema.org/Review">
				<?php $this->render_testimonial_schema_item_reviewed( $settings ); ?>
				<?php if ( $schema_on && ! $show_rating ) : ?>
					<?php $this->render_testimonial_rating_schema_only( $post_id, $settings ); ?>
				<?php endif; ?>
				<div class="bdt-testimonial-carousel-item-wrapper">
					<div class="testimonial-item-header">
						<div class="bdt-grid bdt-grid-small bdt-flex-middle" data-bdt-grid>
							<?php $this->render_testimonial_image( 'carousel',  $post_id, $settings ); ?>
							<?php if ( $show_rating || $show_text || $show_address ) : ?>
								<div class="bdt-width-expand">
									<div class="bdt-testimonial-meta <?php echo $meta_multi_line ? '' : 'bdt-meta-multi-line'; ?>">
										<?php
										$this->render_testimonial_title( 'carousel',  $post_id, $settings );
										$this->render_testimonial_designation( 'carousel',  $post_id, $settings );
										$this->render_testimonial_address( 'carousel',  $post_id, $settings );
										if ( $show_rating && ! $show_text ) :
											?>
											<div class="bdt-testimonial-carousel-rating bdt-margin-small-top bdt-padding-remove">
												<?php $this->render_testimonial_rating( 'carousel',  $post_id, $settings ); ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<?php $this->render_testimonial_excerpt( 'carousel',  $settings ); ?>
					<?php if ( $show_rating && $show_text ) : ?>
						<div class="bdt-testimonial-carousel-rating">
							<?php $this->render_testimonial_rating( 'carousel',  $post_id, $settings ); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<?php
		endwhile;
		wp_reset_postdata();
	}

	protected function render_testimonial_carousel() {
		$settings = $this->get_settings_for_display();
		$skin = isset( $settings['_skin'] ) ? $settings['_skin'] : 'default';
		$this->render_testimonial_carousel_header( $skin, $settings );
		$this->render_testimonial_carousel_loop_item( $settings );
		$this->render_footer();
	}

	protected function render_testimonial_slider_header( $skin, $id, $settings ) {
		$this->add_render_attribute( 'testimonial-slider', 'id', 'bdt-testimonial-slider-' . esc_attr( $id ) );
		$this->add_render_attribute( 'testimonial-slider', 'class', [ 'bdt-testimonial-slider', 'skin-' . esc_attr( $skin ) ] );
		$slider_id = 'bdt-testimonial-slider-' . $this->get_id();
		$nav       = $settings['navigation'] ?? '';

		if ( $nav === 'arrows' ) {
			$this->add_render_attribute( 'testimonial-slider', 'class', 'bdt-arrows-align-' . ( $settings['arrows_position'] ?? '' ) );
		} elseif ( $nav === 'dots' ) {
			$this->add_render_attribute( 'testimonial-slider', 'class', 'bdt-dots-align-' . ( $settings['dots_position'] ?? '' ) );
		} elseif ( $nav === 'both' ) {
			$this->add_render_attribute( 'testimonial-slider', 'class', 'bdt-arrows-dots-align-' . ( $settings['both_position'] ?? '' ) );
		} elseif ( $nav === 'arrows-fraction' ) {
			$this->add_render_attribute( 'testimonial-slider', 'class', 'bdt-arrows-dots-align-' . ( $settings['arrows_fraction_position'] ?? '' ) );
		}

		if ( $nav === 'arrows-fraction' ) {
			$pagination_type = 'fraction';
		} elseif ( $nav === 'both' || $nav === 'dots' ) {
			$pagination_type = 'bullets';
		} elseif ( $nav === 'progressbar' ) {
			$pagination_type = 'progressbar';
		} else {
			$pagination_type = '';
		}

		$this->add_render_attribute( 'testimonial-slider', 'data-settings', wp_json_encode( array_filter( [
			'autoplay'     => $settings['autoplay'] === 'yes' ? [ 'delay' => intval( $settings['autoplay_interval'] ?? 7000 ) ] : false,
			'loop'         => $settings['loop'] === 'yes',
			'autoHeight'   => $settings['auto-height'] === 'yes',
			'speed'        => intval( $settings['velocity'] ?? 500 ),
			'pauseOnHover' => ! empty( $settings['pause_on_hover'] ),
			'navigation'   => [
				'nextEl' => '#' . $slider_id . ' .bdt-navigation-next',
				'prevEl' => '#' . $slider_id . ' .bdt-navigation-prev',
			],
			'pagination'   => [
				'el'             => '#' . $slider_id . ' .swiper-pagination',
				'type'           => $pagination_type,
				'clickable'      => 'true',
				'autoHeight'     => true,
				'dynamicBullets' => $settings['dynamic_bullets'] === 'yes',
			],
			'scrollbar'    => [
				'el'   => '#' . $slider_id . ' .swiper-scrollbar',
				'hide' => 'true',
			],
		] ) ) );

		$this->add_render_attribute( 'swiper', 'class', 'swiper-carousel swiper' );
		?>
		<div <?php $this->print_render_attribute_string( 'testimonial-slider' ); ?>>
			<div <?php $this->print_render_attribute_string( 'swiper' ); ?>>
				<div class="swiper-wrapper">
		<?php
	}

	protected function render_testimonial_slider_navigation( $settings ) {
		if ( ( $settings['navigation'] ?? '' ) !== 'arrows' ) {
			return;
		}
		$hide_mobile = ! empty( $settings['hide_arrow_on_mobile'] ) ? ' bdt-visible@m' : '';
		$position   = $settings['arrows_position'] ?? '';
		$icon       = $settings['nav_arrows_icon'] ?? '';
		?>
		<div class="bdt-position-z-index bdt-position-<?php echo esc_attr( $position . $hide_mobile ); ?>">
			<div class="bdt-arrows-container bdt-slidenav-container">
				<a href="" class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav">
					<i class="ep-icon-arrow-left-<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
				</a>
				<a href="" class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav">
					<i class="ep-icon-arrow-right-<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
				</a>
			</div>
		</div>
		<?php
	}

	protected function render_testimonial_slider_pagination( $settings ) {
		$nav = $settings['navigation'] ?? '';
		if ( $nav === 'dots' || $nav === 'arrows-fraction' ) : ?>
			<div class="bdt-position-z-index bdt-position-<?php echo esc_attr( $settings['dots_position'] ?? '' ); ?>">
				<div class="bdt-dots-container">
					<div class="swiper-pagination"></div>
				</div>
			</div>
		<?php elseif ( $nav === 'progressbar' ) : ?>
			<div class="swiper-pagination bdt-position-z-index bdt-position-<?php echo esc_attr( $settings['progress_position'] ?? '' ); ?>"></div>
		<?php endif;
	}

	protected function render_testimonial_slider_both_navigation( $settings ) {
		$hide_mobile = ! empty( $settings['hide_arrow_on_mobile'] ) ? 'bdt-visible@m' : '';
		$position   = $settings['both_position'] ?? '';
		$icon       = $settings['nav_arrows_icon'] ?? '';
		?>
		<div class="bdt-position-z-index bdt-position-<?php echo esc_attr( $position ); ?>">
			<div class="bdt-arrows-dots-container bdt-slidenav-container">
				<div class="bdt-flex bdt-flex-middle">
					<div class="<?php echo esc_attr( $hide_mobile ); ?>">
						<a href="" class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav">
							<i class="ep-icon-arrow-left-<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
						</a>
					</div>
					<?php if ( ( $settings['both_position'] ?? '' ) !== 'center' ) : ?>
						<div class="swiper-pagination"></div>
					<?php endif; ?>
					<div class="<?php echo esc_attr( $hide_mobile ); ?>">
						<a href="" class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav">
							<i class="ep-icon-arrow-right-<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
						</a>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	protected function render_testimonial_slider_arrows_fraction( $settings ) {
		$hide_mobile = ! empty( $settings['hide_arrow_on_mobile'] ) ? 'bdt-visible@m' : '';
		$position   = $settings['arrows_fraction_position'] ?? '';
		$icon       = $settings['nav_arrows_icon'] ?? '';
		?>
		<div class="bdt-position-z-index bdt-position-<?php echo esc_attr( $position ); ?>">
			<div class="bdt-arrows-fraction-container bdt-slidenav-container">
				<div class="bdt-flex bdt-flex-middle">
					<div class="<?php echo esc_attr( $hide_mobile ); ?>">
						<a href="" class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav">
							<i class="ep-icon-arrow-left-<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
						</a>
					</div>
					<?php if ( ( $settings['arrows_fraction_position'] ?? '' ) !== 'center' ) : ?>
						<div class="swiper-pagination"></div>
					<?php endif; ?>
					<div class="<?php echo esc_attr( $hide_mobile ); ?>">
						<a href="" class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav">
							<i class="ep-icon-arrow-right-<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
						</a>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	protected function render_testimonial_slider_footer( $settings ) {
		?>
				</div>
				<?php if ( $settings['show_scrollbar'] === 'yes' ) : ?>
					<div class="swiper-scrollbar"></div>
				<?php endif; ?>
			</div>
			<?php
			$nav = $settings['navigation'] ?? '';
			if ( $nav === 'both' ) :
				$this->render_testimonial_slider_both_navigation( $settings );
				if ( $settings['both_position'] === 'center' ) : ?>
					<div class="bdt-position-z-index bdt-position-bottom">
						<div class="bdt-dots-container">
							<div class="swiper-pagination"></div>
						</div>
					</div>
				<?php endif;
			elseif ( $nav === 'arrows-fraction' ) :
				$this->render_testimonial_slider_arrows_fraction( $settings );
				if ( $settings['arrows_fraction_position'] === 'center' ) : ?>
					<div class="bdt-dots-container">
						<div class="swiper-pagination"></div>
					</div>
				<?php endif;
			else :
				$this->render_testimonial_slider_pagination( $settings );
				$this->render_testimonial_slider_navigation( $settings );
			endif;
			?>
		</div>
		<?php
	}

	protected function render_testimonial_slider_image( $settings ) {
		if ( $settings['thumb'] !== 'yes' ) {
			return;
		}
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' );
		$src   = ( $thumb && isset( $thumb[0] ) ) ? $thumb[0] : BDTEP_ASSETS_URL . 'images/member.svg';
		$title = get_the_title();
		?>
		<div class="bdt-testimonial-thumb-wrap bdt-flex bdt-position-relative">
			<div class="bdt-testimonial-thumb bdt-position-relative">
				<img src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $title ); ?>" />
			</div>
			<?php $this->render_testimonial_review_platform( get_the_ID(), $settings ); ?>
		</div>
		<?php
	}

	protected function render_testimonial_slider_excerpt( $settings ) {
		if ( $settings['show_text'] !== 'yes' ) {
			return;
		}
		$strip_shortcode = $settings['strip_shortcode'] === 'yes';
		if ( ( $settings['text_read_more_toggle'] ?? '' ) === 'yes' && isset( $settings['text_limit'] ) && (int) $settings['text_limit'] > 0 ) {
			$this->add_render_attribute( 'read-text', 'class', 'bdt-ep-read-more-text', true );
			$this->add_render_attribute( 'read-text', 'data-read-more', wp_json_encode( [ 'words_length' => (int) $settings['text_limit'] ] ), true );
			$text_limit = 0;
		} else {
			$text_limit = isset( $settings['text_limit'] ) ? (int) $settings['text_limit'] : 80;
		}
		if ( $settings['schema_rich_results'] === 'yes' ) {
			$this->add_render_attribute( 'read-text', 'itemprop', 'description', true );
		}
		?>
		<div <?php $this->print_render_attribute_string( 'read-text' ); ?>>
			<?php
			if ( has_excerpt() ) {
				the_excerpt();
			} else {
				echo wp_kses_post( element_pack_custom_excerpt( $text_limit, $strip_shortcode ) );
			}
			?>
		</div>
		<?php
	}

	protected function render_testimonial_slider_meta( $element_key, $settings ) {
		$post_id    = get_the_ID();
		$rating_num = $this->get_testimonial_sanitized_rating( $post_id );
		$classes = [ 'bdt-rating', 'bdt-grid', 'bdt-grid-collapse', 'bdt-rating-' . $rating_num ];
		if ( $settings['thumb'] !== 'yes' ) {
			$classes[] = 'bdt-flex-' . ( $settings['alignment'] ?? 'left' );
		}
	$this->add_render_attribute( $element_key, 'class', $classes );
		$show_title   = $settings['title'] === 'yes';
		$show_company = $settings['company_name'] === 'yes';
		$show_rating  = $settings['rating'] === 'yes';
		if ( ! $show_title && ! $show_company && ! $show_rating ) {
			return;
		}
		$company_name = get_post_meta( $post_id, 'bdthemes_tm_company_name', true );
		$author_name  = get_the_title();
		$use_schema   = $settings['schema_rich_results'] === 'yes';
		$show_comma   = $settings['show_comma'] === 'yes' && $show_company && $company_name !== '';
		$meta_class   = ! empty( $settings['meta_multi_line'] ) ? ' bdt-meta-multi-line' : '';
		?>
		<div class="bdt-testimonial-meta<?php echo esc_attr( $meta_class ); ?>">
			<?php if ( $show_title ) : ?>
				<div class="bdt-testimonial-title">
					<?php if ( $use_schema ) : ?>
						<span itemprop="author" itemscope itemtype="https://schema.org/Person">
							<span itemprop="name"><?php echo esc_html( $author_name ); ?></span>
						</span><?php if ( $show_comma ) { echo ', '; } ?>
					<?php else : ?>
						<?php echo esc_html( $author_name ); ?><?php if ( $show_comma ) { echo ', '; } ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php $this->render_testimonial_designation( 'slider',  $post_id, $settings ); ?>
			<?php if ( $show_company ) : ?>
				<div class="bdt-testimonial-address">
					<?php echo wp_kses_post( $company_name ); ?>
				</div>
			<?php endif; ?>
		<?php if ( $show_rating ) : ?>
			<meta itemprop="datePublished" content="<?php echo esc_attr( get_the_date( 'c', $post_id ) ); ?>">
			<div itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
				<meta itemprop="worstRating" content="1">
				<meta itemprop="ratingValue" content="<?php echo absint( $rating_num ); ?>">
				<meta itemprop="bestRating" content="5">
				<ul <?php $this->print_render_attribute_string( $element_key ); ?>>
					<li class="bdt-rating-item"><span><i class="ep-icon-star-full" aria-hidden="true"></i></span></li>
					<li class="bdt-rating-item"><span><i class="ep-icon-star-full" aria-hidden="true"></i></span></li>
					<li class="bdt-rating-item"><span><i class="ep-icon-star-full" aria-hidden="true"></i></span></li>
					<li class="bdt-rating-item"><span><i class="ep-icon-star-full" aria-hidden="true"></i></span></li>
					<li class="bdt-rating-item"><span><i class="ep-icon-star-full" aria-hidden="true"></i></span></li>
				</ul>
			</div>
		<?php endif; ?>
		</div>
		<?php
	}

	protected function render_testimonial_slider() {
		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();
		$index    = 1;
		$per_page = isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 4;
		$wp_query = $this->query_testimonial_posts( 'slider',  $per_page );

		if ( ! $wp_query->found_posts ) {
			return;
		}

		$this->render_testimonial_slider_header( 'default', $id, $settings );

		$schema_on = $settings['schema_rich_results'] === 'yes';
		$show_rating = $settings['rating'] === 'yes';
		$meta_pos    = $settings['meta_position'] ?? 'after';

		while ( $wp_query->have_posts() ) :
			$wp_query->the_post();
			$post_id  = get_the_ID();
			$platform = get_post_meta( $post_id, 'bdthemes_tm_platform', true );
			$platform = $platform !== '' ? strtolower( $platform ) : '';
			$slide_key = 'slide-' . $post_id;
			$this->add_render_attribute( $slide_key, 'class', 'swiper-slide bdt-review-' . $platform );
			if ( $schema_on ) {
				$this->add_render_attribute( $slide_key, 'itemprop', 'review' );
				$this->add_render_attribute( $slide_key, 'itemscope', '' );
				$this->add_render_attribute( $slide_key, 'itemtype', 'https://schema.org/Review' );
			}
			?>
			<div <?php $this->print_render_attribute_string( $slide_key ); ?>>
				<?php $this->render_testimonial_schema_item_reviewed( $settings ); ?>
				<?php if ( $schema_on && ! $show_rating ) : ?>
					<?php $this->render_testimonial_rating_schema_only( $post_id, $settings ); ?>
				<?php endif; ?>
				<div class="bdt-slider-item-inner">
					<?php if ( $meta_pos === 'after' ) : ?>
						<div class="bdt-testimonial-text">
							<?php $this->render_testimonial_slider_excerpt( $settings ); ?>
						</div>
					<?php endif; ?>
					<div class="bdt-info-details bdt-flex bdt-flex-center bdt-flex-middle">
						<?php $this->render_testimonial_slider_image( $settings ); ?>
						<?php $this->render_testimonial_slider_meta( 'testmonial-meta-' . $index, $settings ); ?>
					</div>
					<?php if ( $meta_pos === 'before' ) : ?>
						<div class="bdt-testimonial-text">
							<?php $this->render_testimonial_slider_excerpt( $settings ); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<?php
			$index++;
		endwhile;

		wp_reset_postdata();

		$this->render_testimonial_slider_footer( $settings );
	}


	public $feed_data = [];
	public $api_url = 'https://graph.facebook.com/v4.0/%1$s/posts?%2$s&access_token=%3$s';
	public $api_queries = 'fields=status_type,created_time,from,message,story,full_picture,permalink_url,attachments{type,media_type,title,description,unshimmed_url,subattachments},comments.summary(total_count),reactions.summary(total_count)';
	private $cache_version = '1.1';

	protected function get_facebook_feed_wrapper_class( $type ) {
		return 'carousel' === $type ? 'bdt-facebook-feed-carousel' : 'bdt-facebook-feed-wrap';
	}

	protected function register_facebook_feed_controls( $type ) {
		$fb = '{{WRAPPER}} .' . $this->get_facebook_feed_wrapper_class( $type );

		$this->start_controls_section(
			'section_content_air_pollution',
			[ 
				'label' => esc_html__( 'Facebook Feed', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'select_api_type',
			[ 
				'label'       => esc_html__( 'Select API', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'global',
				'options'     => [ 
					'custom' => esc_html__( 'Custom', 'bdthemes-element-pack' ),
					'global' => esc_html__( 'Global', 'bdthemes-element-pack' ),
				],
				'render_type' => 'template',
			]
		);


		$this->add_control(
			'api_page_id',
			[ 
				'label'       => esc_html__( 'Page ID', 'bdthemes-element-pack' ),
				'description' => esc_html__( 'A Facebook page ID is a unique identifier assigned to each Facebook page. It can be used to access information about the page, such as its name, profile picture, and cover photo, as well as to interact with the page\'s content and followers.', 'bdthemes-element-pack' ) . sprintf( '<br> Link - <a href="https://developers.facebook.com/apps/" target="blank">Get Page ID</a>' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
				'condition'   => [ 
					'select_api_type' => 'custom'
				]
			]
		);

		$this->add_control(
			'api_access_token',
			[ 
				'label'       => esc_html__( 'Access Token', 'bdthemes-element-pack' ),
				'description' => esc_html__( 'A Facebook page access token is a string of characters that is generated by Facebook and is used to grant an app or server access to a specific Facebook page.', 'bdthemes-element-pack' ) . sprintf( '<br> Link - <a href="https://developers.facebook.com/apps/" target="blank">Get Access Token</a>' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
				'condition'   => [ 
					'select_api_type' => 'custom'
				]
			]
		);

		$this->add_control(
			'data_cache',
			[ 
				'label'       => esc_html__( 'Cache Feeds', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'description' => esc_html__( 'Note:- Please use this cache option to reduce your request of API Calls.', 'bdthemes-element-pack' ),
				'separator'   => 'before'
			]
		);

		$this->add_control(
			'cache_refresh',
			[ 
				'label'     => esc_html__( 'Reload Cache after ', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '12',
				'options'   => array(
					'30'  => esc_html__( '30 Minutes', 'bdthemes-element-pack' ),
					'1'   => esc_html__( '1 Hour', 'bdthemes-element-pack' ),
					'3'   => esc_html__( '3 Hour', 'bdthemes-element-pack' ),
					'6'   => esc_html__( '6 Hour', 'bdthemes-element-pack' ),
					'12'  => esc_html__( '12 Hour', 'bdthemes-element-pack' ),
					'24'  => esc_html__( '24 Hour', 'bdthemes-element-pack' ),
					'7d'  => esc_html__( '7 Days', 'bdthemes-element-pack' ),
					'15d' => esc_html__( '15 Days', 'bdthemes-element-pack' ),
					'30d' => esc_html__( '30 Days', 'bdthemes-element-pack' ),
				),
				'condition' => [ 
					'data_cache' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional',
			[ 
				'label' => esc_html__( 'Additional Options', 'bdthemes-element-pack' ),
			]
		);

		if ( 'carousel' === $type ) {
		//swiper carousel columns & item gap controls
		$this->register_carousel_column_gap_controls();
		}

		if ( 'grid' === $type ) {
		$this->add_control(
			'masonry',
			[ 
				'label'        => __( 'Masonry', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'prefix_class' => 'bdt-ep-masonry--',
				'render_type'  => 'template'
			]
		);

		$this->add_responsive_control(
			'columns',
			[ 
				'label'          => __( 'Columns', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => [ 
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'selectors'      => [ 
					'{{WRAPPER}} .bdt-grid-wrap' => 'columns: {{SIZE}}; display: block;'
				],
				'condition'      => [ 
					'masonry' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'grid_columns',
			[ 
				'label'          => __( 'Columns', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => [ 
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'selectors'      => [ 
					'{{WRAPPER}} .bdt-grid-wrap' => 'grid-template-columns: repeat({{SIZE}}, 1fr); display: grid;',
				],
				'condition'      => [ 
					'masonry' => ''
				]
			]
		);


		$this->add_responsive_control(
			'row_gap',
			[ 
				'label'     => esc_html__( 'Row Gap', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ 
					'size' => 20,
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-grid-wrap'                                        => 'grid-row-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.bdt-ep-masonry--yes .bdt-facebook-feed-wrap .bdt-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[ 
				'label'     => esc_html__( 'Column Gap', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ 
					'size' => 20,
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-grid-wrap' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);
		}

		$this->add_control(
			'post_limit',
			[ 
				'label'   => esc_html__( 'Post Limit', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
			]
		);


		$this->add_control(
			'layout_style',
			[ 
				'label'   => __( 'Layout Style', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [ 
					'style-1' => '1',
					'style-2' => '2',
				],
				'default' => 'style-1',
			]
		);

		if ( 'carousel' === $type ) {
		$this->add_control(
			'item_match_height',
			[ 
				'label'        => __( 'Item Match Height', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'prefix_class' => 'bdt-item-match-height--',
				'render_type'  => 'template'
			]
		);
		}

		$this->add_control(
			'show_desc',
			[ 
				'label'     => ( 'grid' === $type ? esc_html__( 'Show Text', 'bdthemes-element-pack' ) : esc_html__( 'Show Description', 'bdthemes-element-pack' ) ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'desc_word_count',
			[ 
				'label'     => ( 'grid' === $type ? esc_html__( 'Text Word Count', 'bdthemes-element-pack' ) : esc_html__( 'Description Word Count', 'bdthemes-element-pack' ) ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 500,
				'default'   => 15,
				'condition' => [ 
					'show_desc' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_image_album',
			[ 
				'label'   => ( 'carousel' === $type ? esc_html__( 'Show Image Album', 'bdthemes-element-pack' ) . BDTEP_NC : esc_html__( 'Show Image Album', 'bdthemes-element-pack' ) ),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'show_feature_image',
			[ 
				'label'   => esc_html__( 'Show Feature Image', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [ 
					'show_image_album!' => 'yes'
				]	
			]
		);

		$this->add_control(
			'show_author_image',
			[ 
				'label'   => esc_html__( 'Show Author Image', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_author_name',
			[ 
				'label'   => esc_html__( 'Show Author Name', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_date',
			[ 
				'label'   => esc_html__( 'Show Date', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => ( 'grid' === $type ? 'before' : '' ),
			]
		);

		$this->add_control(
			'show_like',
			[ 
				'label'   => esc_html__( 'Show Like', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_comments',
			[ 
				'label'   => esc_html__( 'Show Comments', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_share',
			[ 
				'label'   => esc_html__( 'Show Share', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_read_more',
			[ 
				'label'   => esc_html__( 'Show Read More', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => ( 'grid' === $type ? 'before' : '' ),
			]
		);

		$this->add_control(
			'read_more_text',
			[ 
				'label'   => esc_html__( 'Read More Text', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true ],
				'default' => esc_html__( 'See More', 'bdthemes-element-pack' ),
				'condition' => [ 
					'show_read_more' => 'yes'
				]
			]
		);

		$this->add_control(
			'link_target',
			[ 
				'label'     => esc_html__( 'Link Target', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [ 
					'_self'  => esc_html__( 'Open in same window', 'bdthemes-element-pack' ),
					'_blank' => esc_html__( 'Open in new window', 'bdthemes-element-pack' ),
				],
				'default'   => '_blank',
				'condition' => [ 
					'show_read_more' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		if ( 'carousel' === $type ) {
		//Global Carousel Settings Controls
		$this->register_carousel_settings_controls();

		//Navigation Controls
		$this->start_controls_section(
			'section_content_navigation',
			[ 
				'label' => __( 'Navigation', 'bdthemes-element-pack' ),
			]
		);

		//Global Navigation Controls
		$this->register_navigation_controls();

		$this->end_controls_section();
		}

		$this->start_controls_section(
			( 'grid' === $type ? 'section_item_style' : 'section_style_items' ),
			[ 
				'label' => ( 'grid' === $type ? esc_html__( 'Item', 'bdthemes-element-pack' ) : esc_html__( 'Items', 'bdthemes-element-pack' ) ),
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

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'item_background',
				'selector' => '{{WRAPPER}} .bdt-item',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'      => 'item_border',
				'selector'  => '{{WRAPPER}} .bdt-item',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [ 
					'layout_style' => 'style-2'
				]
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-fb-style-1 .bdt-fb-content'         => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bdt-fb-style-1 .bdt-share-and-readmore' => 'padding: 0 {{RIGHT}}{{UNIT}} 0 {{LEFT}}{{UNIT}};',
				],
				'condition'  => [ 
					'layout_style' => 'style-1'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-item',
			]
		);

		if ( 'carousel' === $type ) {
		$this->add_responsive_control(
			'item_shadow_padding',
			[ 
				'label'       => __( 'Match Padding', 'bdthemes-element-pack' ),
				'description' => __( 'You have to add padding for matching overlaping normal/hover box shadow when you used Box Shadow option.', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [ 
					'px' => [ 
						'min'  => 0,
						'step' => 1,
						'max'  => 50,
					]
				],
				'selectors'   => [ 
					'{{WRAPPER}} .swiper-carousel' => 'padding: {{SIZE}}{{UNIT}}; margin: 0 -{{SIZE}}{{UNIT}};'
				],
			]
		);
		}

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[ 
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'item_hover_background',
				'selector' => '{{WRAPPER}} .bdt-item:hover',
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
					'{{WRAPPER}} .bdt-item:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'item_hover_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-item:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_author_style',
			[ 
				'label' => esc_html__( 'Author', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'conditions' => [ 
					'relation' => 'or',
					'terms'    => 
					[
						[
							'name'     => 'show_author_name',
							'operator' => '===',
							'value'    => 'yes'
						],
						[
							'name'     => 'show_author_image',
							'operator' => '===',
							'value'    => 'yes'
						]
					]
				]
			]
		);

		$this->add_control(
			'author_name',
			[ 
				'label' => esc_html__( 'NAME', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::HEADING,
				'condition' => [ 
					'show_author_name' => 'yes'
				]
			]
		);

		$this->start_controls_tabs(
			'style_name_tabs',
			[ 
				'condition' => [ 
					'show_author_name' => 'yes'
				]
			]
		);

		$this->start_controls_tab(
			'name_tab_normal',
			[ 
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'author_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-author-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'author_typography',
				'selector' => '{{WRAPPER}} .bdt-author-name',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[ 
				'name'     => 'author_text_shadow',
				'label'    => esc_html__( 'Text Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-author-name',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'name_tab_hover',
			[ 
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
				'condition' => ( 'carousel' === $type ? [ 'show_author_name' => 'yes' ] : [] ),
			]
		);

		$this->add_control(
			'author_color_hover',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-author-name:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[ 
				'name'     => 'author_text_shadow_hover',
				'label'    => esc_html__( 'Text Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-author-name:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'fb_feed_author_divider',
			[
				'type' 		=> Controls_Manager::DIVIDER,
				'condition' => [ 
					'show_author_name'  => 'yes',
					'show_author_image' => 'yes'
				]
			]
		);

		$this->add_control(
			'author_img',
			[ 
				'label'     => esc_html__( 'IMAGE', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [ 
					'show_author_image' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'author_img_size',
			[ 
				'label'     => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 30,
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-icon-img' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [ 
					'show_author_image' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'author_img_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-icon-img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [ 
					'show_author_image' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'author_img_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-icon-img',
				'condition' => [ 
					'show_author_image' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'author_img_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-icon-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
				'condition' => [ 
					'show_author_image' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'author_img_background',
				'selector' => '{{WRAPPER}} .bdt-icon-img',
				'condition' => [ 
					'show_author_image' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'author_img_shadow',
				'selector' => '{{WRAPPER}} .bdt-icon-img',
				'condition' => [ 
					'show_author_image' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_date',
			[ 
				'label'     => esc_html__( 'Date', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_date' => 'yes'
				]
			]
		);

		$this->add_control(
			'date_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-date-muted' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'date_typography',
				'selector' => '{{WRAPPER}} .bdt-date-muted',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[ 
				'name'     => 'date_text_shadow',
				'label'    => esc_html__( 'Text Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-date-muted',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_description',
			[ 
				'label' => ( 'grid' === $type ? esc_html__( 'Text', 'bdthemes-element-pack' ) : esc_html__( 'Description', 'bdthemes-element-pack' ) . BDTEP_UC ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_desc' => 'yes'
				]
			]
		);

		$this->add_control(
			'desc_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .bdt-text',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[ 
				'name'     => 'desc_text_shadow',
				'label'    => esc_html__( 'Text Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-text',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_feature_image',
			[ 
				'label'     => esc_html__( 'Feature Image', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_feature_image' => 'yes'
				]
			]
		);


		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'feature_img_background',
				'selector' => '{{WRAPPER}} .bdt-img-wrap .bdt-img-item',
			]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'feature_img_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-img-wrap .bdt-img-item',
			]
		);

		$this->add_responsive_control(
			'feature_img_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-img-wrap .bdt-img-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'feature_img_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-img-wrap .bdt-img-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'feature_img_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-img-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'feature_img_shadow',
				'selector' => '{{WRAPPER}} .bdt-img-wrap .bdt-img-item',
			]
		);

		$this->add_responsive_control(
			'feature_img_gap',
			[ 
				'label'     => esc_html__( 'Gap', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					$fb . ' .bdt-img-wrap' => 'gap: {{SIZE}}{{UNIT}};',
				],
				'condition' => [ 
					'show_image_album' => 'yes'
				],
				'separator' => 'before'
			]
		);
		$this->add_control(
			'feature_img_text_color',
			[ 
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					$fb . ' .bdt-album-img-count' => 'color: {{VALUE}};',
				],
				'condition' => [ 
					'show_image_album' => 'yes'
				],
			]
		);
		$this->add_control(
			'feature_img_overlay_color',
			[ 
				'label'     => esc_html__( 'Overlay Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					$fb . ' .bdt-album-img-count' => 'background-color: {{VALUE}};',
				],
				'condition' => [ 
					'show_image_album' => 'yes'
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'feature_img_typography',
				'selector' => $fb . ' .bdt-album-img-count',
				'condition' => [ 
					'show_image_album' => 'yes'
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_read_more',
			[ 
				'label'     => esc_html__( 'Read More', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_read_more' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'readmore_spacing',
			[ 
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-share-and-readmore' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
			'style_read_more_tabs'
		);

		$this->start_controls_tab(
			'read_more_normal',
			[ 
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'read_more_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-read-more a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'read_more_typography',
				'selector' => '{{WRAPPER}} .bdt-read-more a',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[ 
				'name'     => 'read_more_text_shadow',
				'label'    => esc_html__( 'Text Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-read-more a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'read_more_hover',
			[ 
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'read_more_color_hover',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-read-more a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[ 
				'name'     => 'read_more_text_shadow_hover',
				'label'    => esc_html__( 'Text Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-read-more:hover a',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		$this->start_controls_section(
			'section_social_button',
			[ 
				'label' => esc_html__( 'Like/Comments', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'show_like',
							'operator' => '===',
							'value'    => 'yes',
						],
						[
							'name'     => 'show_comments',
							'operator' => '===',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'like_comments_button_margin',
			[ 
				'label'      => __( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					$fb . ' .bdt-social-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => ( 'carousel' === $type ? [
					'show_like_comments' => 'yes',
					'show_like' => 'yes',
				] : [
					'show_like' => 'yes',
					'show_comments' => 'yes'
				] )
			]
		);

		$this->add_responsive_control(
			'like_comments_button_spacing_gap',
			[ 
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					$fb . ' .bdt-social-button' => 'gap: {{SIZE}}{{UNIT}};',
				],
				'condition' => ( 'carousel' === $type ? [
					'show_like_comments' => 'yes',
					'show_like' => 'yes',
				] : [
					'show_like' => 'yes',
					'show_comments' => 'yes'
				] )
			]
		);

		$this->start_controls_tabs(
			'like_comments_button_style_tabs'
		);

		$this->start_controls_tab(
			'like_button_style_tab',
			[ 
				'label'     => __( 'Like', 'bdthemes-element-pack' ),
				'condition' => [ 
					'show_like' => 'yes'
				]
			]
		);


		$this->add_control(
			'like_button_text_color',
			[ 
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					$fb . ' .bdt-social-button .bdt-like-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'like_button_background',
				'selector' => $fb . ' .bdt-social-button .bdt-like-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'     => 'like_button_border',
				'selector' => $fb . ' .bdt-social-button .bdt-like-icon'
			]
		);

		$this->add_responsive_control(
			'like_button_radius',
			[ 
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					$fb . ' .bdt-social-button .bdt-like-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'like_button_shadow',
				'selector' => $fb . ' .bdt-social-button .bdt-like-icon',
			]
		);

		$this->add_responsive_control(
			'like_button_padding',
			[ 
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					$fb . ' .bdt-social-button .bdt-like-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'like_button_typography',
				'selector' => $fb . ' .bdt-social-button .bdt-like-icon .bdt-count',
			]
		);

		$this->add_responsive_control(
			'like_icon_size_gap',
			[ 
				'label'     => esc_html__( 'Icon Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					$fb . ' .bdt-social-button .bdt-like-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'like_spacing_gap',
			[ 
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					$fb . ' .bdt-social-button .bdt-like-icon' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'like_button_hover_options',
			[ 
				'label'     => esc_html__( 'HOVER', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'like_button_hover_text_color',
			[ 
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					$fb . ' .bdt-social-button .bdt-like-icon:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'like_button_hover_background',
				'selector' => $fb . ' .bdt-social-button .bdt-like-icon:hover',
			]
		);

		$this->add_control(
			'like_button_hover_border_color',
			[ 
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					$fb . ' .bdt-social-button .bdt-like-icon:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [ 
					'like_button_border_border!' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'like_button_hover_shadow',
				'selector' => $fb . ' .bdt-social-button .bdt-like-icon:hover',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'comments_button_style_hover_tab',
			[ 
				'label'     => __( 'Comments', 'bdthemes-element-pack' ),
				'condition' => [ 
					'show_comments' => 'yes'
				]
			]
		);

		$this->add_control(
			'commenting_button_text_color',
			[ 
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					$fb . ' .bdt-social-button .bdt-commenting-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'commenting_button_background',
				'selector' => $fb . ' .bdt-social-button .bdt-commenting-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'     => 'commenting_button_border',
				'selector' => $fb . ' .bdt-social-button .bdt-commenting-icon'
			]
		);

		$this->add_responsive_control(
			'commenting_button_radius',
			[ 
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					$fb . ' .bdt-social-button .bdt-commenting-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'commenting_button_shadow',
				'selector' => $fb . ' .bdt-social-button .bdt-commenting-icon',
			]
		);

		$this->add_responsive_control(
			'commenting_button_padding',
			[ 
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					$fb . ' .bdt-social-button .bdt-commenting-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'commenting_button_typography',
				'selector' => $fb . ' .bdt-social-button .bdt-commenting-icon .bdt-count',
			]
		);

		$this->add_responsive_control(
			'commenting_icon_size_gap',
			[ 
				'label'     => esc_html__( 'Icon Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					$fb . ' .bdt-social-button .bdt-commenting-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'commenting_spacing_gap',
			[ 
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					$fb . ' .bdt-social-button .bdt-commenting-icon' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'commenting_button_hover_options',
			[ 
				'label'     => esc_html__( 'HOVER', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'commenting_button_hover_text_color',
			[ 
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					$fb . ' .bdt-social-button .bdt-commenting-icon:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'commenting_button_hover_background',
				'selector' => $fb . ' .bdt-social-button .bdt-commenting-icon:hover',
			]
		);

		$this->add_control(
			'commenting_button_hover_border_color',
			[ 
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					$fb . ' .bdt-social-button .bdt-commenting-icon:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [ 
					'commenting_button_border_border!' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'commenting_button_hover_shadow',
				'selector' => $fb . ' .bdt-social-button .bdt-commenting-icon:hover',
			]
		);


		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->end_controls_section();

		$this->start_controls_section(
			'section_share_button',
			[ 
				'label'     => esc_html__( 'Share', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_share' => 'yes'
				]
			]
		);

		$this->start_controls_tabs(
			'share_button_style_tabs'
		);

		$this->start_controls_tab(
			'share_button_normal_tab',
			[ 
				'label' => __( 'Share', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'share_button_text_color',
			[ 
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					$fb . ' .bdt-share-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'share_button_background',
				'selector' => $fb . ' .bdt-share-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'     => 'share_button_border',
				'selector' => $fb . ' .bdt-share-icon'
			]
		);

		$this->add_responsive_control(
			'share_button_radius',
			[ 
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					$fb . ' .bdt-share-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);



		$this->add_responsive_control(
			'share_button_padding',
			[ 
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					$fb . ' .bdt-share-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'share_button_margin',
			[ 
				'label'      => __( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-share-btn-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'share_button_typography',
				'selector' => $fb . ' .bdt-share-icon .bdt-count',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'share_button_shadow',
				'selector' => $fb . ' .bdt-share-icon',
			]
		);

		$this->add_responsive_control(
			'share_button_icon_font_size',
			[ 
				'label'     => esc_html__( 'Icon Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					$fb . ' .bdt-share-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'share_button_hover_name',
			[ 
				'label'     => esc_html__( 'HOVER', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'share_button_hover_text_color',
			[ 
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					$fb . ' .bdt-share-icon:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'share_button_hover_background',
				'selector' => $fb . ' .bdt-share-icon:hover',
			]
		);

		$this->add_control(
			'share_button_hover_border_color',
			[ 
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					$fb . ' .bdt-share-icon:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [ 
					'share_button_border_border!' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'share_button_hover_shadow',
				'selector' => $fb . ' .bdt-share-icon:hover',
			]
		);


		$this->end_controls_tab();

		$this->start_controls_tab(
			'share_button_dropdown_style_hover_tab',
			[ 
				'label' => __( 'Dropdown', 'bdthemes-element-pack' ),
			]
		);

		$this->add_responsive_control(
			'share_button_dropdown_width',
			[
				'label' => __( 'Width', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 500,
					],
				],
				'selectors' => [
					$fb . ' .bdt-dropdown' => 'min-width: {{SIZE}}{{UNIT}} !important; max-width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'share_button_drop_background',
				'selector' => $fb . ' .bdt-dropdown',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'     => 'share_button_drop_border',
				'selector' => $fb . ' .bdt-dropdown'
			]
		);

		$this->add_responsive_control(
			'share_button_drop_radius',
			[ 
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					$fb . ' .bdt-dropdown' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_responsive_control(
			'share_button_drop_padding',
			[ 
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					$fb . ' .bdt-dropdown' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'share_button_drop_shadow',
				'selector' => $fb . ' .bdt-dropdown',
			]
		);

		$this->add_control(
			'share_button_drop_title_name',
			[ 
				'label'     => esc_html__( 'TITLE', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'share_button_drop_title_color',
			[ 
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					$fb . ' .bdt-dropdown-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'share_button_drop_title_typography',
				'selector' => $fb . ' .bdt-dropdown-title',
			]
		);

		$this->add_responsive_control(
			'share_button_drop_title_margin',
			[ 
				'label'      => __( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					$fb . ' .bdt-dropdown-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'share_button_drop_menu_name',
			[ 
				'label'     => esc_html__( 'L I S T', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'share_button_drop_text_color',
			[ 
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					$fb . ' .bdt-dropdown a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'share_button_drop_text_hover_color',
			[ 
				'label'     => __( 'Text Hover Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					$fb . ' .bdt-dropdown a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'share_button_drop_text_typography',
				'selector' => $fb . ' .bdt-dropdown a',
			]
		);


		$this->add_responsive_control(
			'share_button_drop_text_spacing',
			[ 
				'label'     => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					$fb . ' .bdt-dropdown-nav' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->end_controls_section();

		if ( 'carousel' === $type ) {
		//Navigation Style
		$this->start_controls_section(
			'section_style_navigation',
			[ 
				'label'      => __( 'Navigation', 'bdthemes-element-pack' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [ 
					'relation' => 'or',
					'terms'    => [ 
						[ 
							'name'     => 'navigation',
							'operator' => '!=',
							'value'    => 'none',
						],
						[ 
							'name'  => 'show_scrollbar',
							'value' => 'yes',
						],
					],
				],
			]
		);

		//Global Navigation Style Controls
		$this->register_navigation_style_controls( 'swiper-carousel' );

		$this->end_controls_section();
		}

	}
	protected function render_facebook_feed_read_more( $data, $settings ) {
		if ( 'yes' !== $settings['show_read_more'] && ! empty( $settings['read_more_text'] ) ) {
			return;
		}
		printf(
			'<div class="bdt-read-more">
				<a href="%1$s" target="%2$s">
					%3$s
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="one bi bi-arrow-right" viewBox="0 0 16 16">
						<path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
					</svg>
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="two bi bi-arrow-right" viewBox="0 0 16 16">
						<path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
					</svg>
				</a>
			</div>',
			esc_url( $data['permalink_url'] ),
			esc_attr( $settings['link_target'] ),
			esc_html( $settings['read_more_text'] )
		);
	}

	protected function render_facebook_feed_main_share( $data, $settings ) {
		if ( 'yes' !== $settings['show_share'] ) {
			return;
		}

		?>
		<div class="bdt-share-btn-wrap">
			<div class="bdt-share-icon" type="button">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-share-fill"
					viewBox="0 0 16 16">
					<path
						d="M11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.499 2.499 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5z" />
				</svg>
			</div>
			<div bdt-dropdown="pos: top-right">
				<ul class="bdt-nav bdt-dropdown-nav">
					<li class="bdt-dropdown-title">
						<?php esc_html_e( 'share on', 'bdthemes-element-pack' ) ?>
					</li>
					<?php
					printf(
						'<li>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=%1$s" target="%2$s">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
                                    <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z" />
                                </svg>
                                <span>%3$s</span>
                            </a>
                        </li>',
						esc_url( $data['permalink_url'] ),
						esc_attr( $settings['link_target'] ),
						esc_html__( 'Facebook', 'bdthemes-element-pack' )
					);
					printf(
						'<li>
                            <a href="https://twitter.com/intent/tweet?url=%1$s" target="%2$s">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-twitter" viewBox="0 0 16 16">
                                    <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z" />
                                </svg>
                                <span>%3$s</span></a>
                            </a>
                        </li>',
						esc_url( $data['permalink_url'] ),
						esc_attr( $settings['link_target'] ),
						esc_html__( 'X', 'bdthemes-element-pack' )
					);
					printf(
						'<li>
                            <a href="https://pinterest.com/pin/create/button/?url=%1$s" target="%2$s">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pinterest" viewBox="0 0 16 16">
                                    <path d="M8 0a8 8 0 0 0-2.915 15.452c-.07-.633-.134-1.606.027-2.297.146-.625.938-3.977.938-3.977s-.239-.479-.239-1.187c0-1.113.645-1.943 1.448-1.943.682 0 1.012.512 1.012 1.127 0 .686-.437 1.712-.663 2.663-.188.796.4 1.446 1.185 1.446 1.422 0 2.515-1.5 2.515-3.664 0-1.915-1.377-3.254-3.342-3.254-2.276 0-3.612 1.707-3.612 3.471 0 .688.265 1.425.595 1.826a.24.24 0 0 1 .056.23c-.061.252-.196.796-.222.907-.035.146-.116.177-.268.107-1-.465-1.624-1.926-1.624-3.1 0-2.523 1.834-4.84 5.286-4.84 2.775 0 4.932 1.977 4.932 4.62 0 2.757-1.739 4.976-4.151 4.976-.811 0-1.573-.421-1.834-.919l-.498 1.902c-.181.695-.669 1.566-.995 2.097A8 8 0 1 0 8 0z" />
                                </svg>
                                <span>%3$s</span></a>
                            </a>
                        </li>',
						esc_url( $data['permalink_url'] ),
						esc_attr( $settings['link_target'] ),
						esc_html__( 'Pinterest', 'bdthemes-element-pack' )
					);
					?>

				</ul>
			</div>
		</div>
		<?php
	}

	protected function render_facebook_feed_date( $data, $settings ) {
		if ( 'yes' !== $settings['show_date'] ) {
			return;
		}
		printf(
			'<div class="bdt-date-muted">%1$s</div>',
			esc_html( date( "M d Y", strtotime( $data['created_time'] ) ) )
		);
	}

	protected function render_facebook_feed_author_image( $data, $settings ) {
		if ( 'yes' !== $settings['show_author_image'] ) {
			return;
		}
		$page_url   = "https://facebook.com/{$data['from']['id']}";
		// Use cached avatar if available, otherwise use Facebook URL
		$avatar_url = ! empty( $data['from']['avatar_cached'] ) ? $data['from']['avatar_cached'] : "https://graph.facebook.com/v4.0/{$data['from']['id']}/picture";
		printf(
			'<a href="%1$s" target="%2$s" class="bdt-icon-img-wrap"><img class="bdt-icon-img" src="%3$s" alt="%4$s"></a>',
			esc_url( $page_url ),
			esc_attr( $settings['link_target'] ),
			esc_url( $avatar_url ),
			esc_html( $data['from']['name'] )
		);
	}

	protected function render_facebook_feed_author_name( $data, $settings ) {
		if ( 'yes' !== $settings['show_author_name'] ) {
			return;
		}

		$page_url = "https://facebook.com/{$data['from']['id']}";

		printf(
			'<div class="bdt-user-content"><a class="bdt-author-name" href="%1$s" target="%2$s">%3$s</a></div>',
			esc_url( $page_url ),
			esc_attr( $settings['link_target'] ),
			esc_html( $data['from']['name'] )
		);
	}

	protected function render_facebook_feed_feature_image( $data, $settings ) {
		$show_feature_image = in_array( 'yes', [$settings['show_feature_image'], $settings['show_image_album']] );
		$attachments = $data['attachments'] ?? [];
	
		// Return early if feature image is not enabled or there are no attachments
		if ( !$show_feature_image || empty( $attachments ) ) {
			return;
		}
	
		// Handle image album case
		if ( isset( $attachments['data'] ) && is_array( $attachments['data'] ) && 'yes' === $settings['show_image_album'] ) {
			foreach ( $attachments['data'] as $attachment ) {
				if ( 'album' === $attachment['media_type'] && isset( $attachment['subattachments']['data'] ) ) {
					$this->render_facebook_feed_image_album( $attachment['subattachments']['data'], $settings, $data );
				} else {
					$this->render_facebook_feed_single_image( $data, $settings );
				}
			}
		} else {
			$this->render_facebook_feed_single_image( $data, $settings );
		}
	}
	
	protected function render_facebook_feed_single_image( $data, $settings ) {
		if ( isset( $data['full_picture'] ) ) {
			// Use cached image if available, otherwise use Facebook URL
			$image_url = ! empty( $data['full_picture_cached'] ) ? $data['full_picture_cached'] : $data['full_picture'];
			printf(
				'<div class="bdt-img-wrap"><div class="bdt-img-album-single bdt-img-item"><a href="%1$s" target="%2$s"><img src="%3$s" alt="%4$s"></a></div></div>',
				esc_url( $data['permalink_url'] ),
				esc_attr( $settings['link_target'] ),
				esc_url( $image_url ),
				esc_html( $data['from']['name'] )
			);
		}
	}
	
	protected function render_facebook_feed_image_album( $subattachments, $settings, $data ) {
		$image_count = 4; // TODO: Make this configurable
		$skip_image_count = max( 0, count($subattachments) - $image_count );

		$album_img_count = count($subattachments) - $skip_image_count;

		echo '<div class="bdt-img-wrap bdt-img-album-'. esc_attr($album_img_count) .'">';
		foreach ( $subattachments as $index => $subattachment ) {
			if ( isset( $subattachment['media']['image']['src'] ) ) {
				// Use cached image if available, otherwise use Facebook URL
				$image_url = ! empty( $subattachment['media']['image']['src_cached'] ) ? $subattachment['media']['image']['src_cached'] : $subattachment['media']['image']['src'];
				$span = ( $index === $image_count - 1 && $skip_image_count > 0 ) 
					? '<span class="bdt-album-img-count">+' . $skip_image_count . '</span>'
					: '';
	
				printf(
					'<div class="bdt-img-album-item bdt-img-item"><a href="%1$s" target="%2$s">%3$s<img src="%4$s" alt="%5$s"></a></div>',
					esc_url( $subattachment['target']['url'] ),
					esc_attr( $settings['link_target'] ),
					wp_kses_post($span),
					esc_url( $image_url ),
					esc_html( $data['from']['name'] )
				);
	
				// Break the loop after the 4th image
				if ( $index === $image_count - 1 ) {
					break;
				}
			}
		}
		echo '</div>';
	}
	
	

	protected function render_facebook_feed_desc( $data, $settings ) {

		if ( 'yes' !== $settings['show_desc'] ) {
			return;
		}

		$description = ! empty( $data['message'] ) ? explode( ' ', $data['message'] ) : [];

		if ( ! empty( $settings['desc_word_count'] ) && count( $description ) > $settings['desc_word_count'] ) {
			$srt = array_slice( $description, 0, $settings['desc_word_count'] );
			printf( '<div class="bdt-text">%1$s</div>', wp_kses_post( implode( ' ', $srt ) ) . '...' );
			return;
		}

		printf( '<div class="bdt-text">%1$s</div>', ! empty( $data['message'] ) ? wp_kses_post( $data['message'] ) : '' );
	}

	protected function render_facebook_feed_like( $data, $settings ) {
		if ( 'yes' !== $settings['show_like'] ) {
			return;
		}

		printf(
			'<a class="bdt-like-icon" href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-thumbs-up-fill" viewBox="0 0 16 16">
                <path d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a9.84 9.84 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733.058.119.103.242.138.363.077.27.113.567.113.856 0 .289-.036.586-.113.856-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.163 3.163 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.82 4.82 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z"/>
                </svg>
                <span class="bdt-count">%1$s</span>
        </a>',
			esc_html( $data['reactions']['summary']['total_count'] )
		);
	}

	protected function render_facebook_feed_comments( $data, $settings ) {
		if ( 'yes' !== $settings['show_comments'] ) {
			return;
		}

		printf(
			'<a class="bdt-commenting-icon" href="#">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-left-fill" viewBox="0 0 16 16">
                <path d="M2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                </svg>
                <span class="bdt-count">%1$s</span>
            </a>',
			esc_html( $data['comments']['summary']['total_count'] )
		);
	}

	public function facebook_feed_get_transient_expire( $settings ) {
		$expire_value = $settings['cache_refresh'];
		$expire_time  = 1 * HOUR_IN_SECONDS;

		if ( '1' === $expire_value ) {
			$expire_time = 1 * HOUR_IN_SECONDS;
		} elseif ( '3' === $expire_value ) {
			$expire_time = 3 * HOUR_IN_SECONDS;
		} elseif ( '6' === $expire_value ) {
			$expire_time = 6 * HOUR_IN_SECONDS;
		} elseif ( '12' === $expire_value ) {
			$expire_time = 12 * HOUR_IN_SECONDS;
		} elseif ( '24' === $expire_value ) {
			$expire_time = 24 * HOUR_IN_SECONDS;
		} elseif ( '15' === $expire_value ) {
			$expire_time = 15 * MINUTE_IN_SECONDS;
		} elseif ( '30' === $expire_value ) {
			$expire_time = 30 * MINUTE_IN_SECONDS;
		} elseif ( '7d' === $expire_value ) {
			$expire_time = 7 * DAY_IN_SECONDS;
		} elseif ( '15d' === $expire_value ) {
			$expire_time = 15 * DAY_IN_SECONDS;
		} elseif ( '30d' === $expire_value ) {
			$expire_time = 30 * DAY_IN_SECONDS;
		}

		return $expire_time;
	}

	public function facebook_feed_data() {
		$settings        = $this->get_settings_for_display();
		$ep_api_settings = get_option( 'element_pack_api_settings' );
		$fb_page_id      = ! empty( $ep_api_settings['fb_page_id'] ) ? $ep_api_settings['fb_page_id'] : '';
		$fb_access_token = ! empty( $ep_api_settings['fb_access_token'] ) ? $ep_api_settings['fb_access_token'] : '';

		/**
		 * Override If Custom Yes
		 */
		if ( 'custom' == $settings['select_api_type'] ) {
			$fb_page_id      = $settings['api_page_id'];
			$fb_access_token = $settings['api_access_token'];
		}

		if ( ! $fb_page_id || ! $fb_access_token ) {
			$message = esc_html__( 'Ops! I think you forget to set API key, please set your API key.', 'bdthemes-element-pack' );
			$this->facebook_feed_error_notice( $message );
			return false;
		}

		$id            = $fb_page_id;
		$transient_key = sprintf( 'bdt-facebook-feed-data-%s-v%s', md5( $id ), $this->cache_version );

		if ( $settings['data_cache'] == 'yes' ) {
			$data = get_transient( $transient_key );
		} else {
			$data = '';
		}

		/**
		 * Transient Data Not Found
		 * Let's send a API request for data
		 */

		if ( ! $data ) {
			// Clean up old images before fetching fresh data
			if ( $settings['data_cache'] == 'yes' ) {
				$this->facebook_feed_cleanup_page_images( $fb_page_id );
			}
			
			$request_url   = sprintf( $this->api_url, $fb_page_id, $this->api_queries, $fb_access_token );
			$raw_feed_data = $this->facebook_feed_data_remote_request( $request_url );

			if ( ! $raw_feed_data ) {
				return false;
			}

			/**
			 * Check If any Error
			 */
			if ( ! empty( $raw_feed_data ) && array_key_exists( 'error', $raw_feed_data ) ) {
				if ( isset( $raw_feed_data['error']['message'] ) ) {
					$message = $raw_feed_data['error']['message'];
				} else {
					$message = esc_html__( 'Your API credentials are not correct.', 'bdthemes-element-pack' );
				}

				$this->facebook_feed_error_notice( $message );

				return false;
			}

			$data = $this->facebook_feed_transient_feed_data( $raw_feed_data );

			if ( empty( $data ) ) {
				return false;
			}

			// Process and cache images BEFORE saving transient
			if ( $settings['data_cache'] == 'yes' && ! empty( $data ) ) {
				$downloaded_images = array();
				foreach ( $data as $key => $item ) {
					$result = $this->facebook_feed_cache_feed_images( $item );
					$data[$key] = $result['data'];
					$downloaded_images = array_merge( $downloaded_images, $result['images'] );
				}
				
				// Store list of downloaded images for cleanup
				$image_list_key = $transient_key . '_images';
				$expireTime = $this->facebook_feed_get_transient_expire( $settings );
				set_transient( $image_list_key, $downloaded_images, apply_filters( 'element-pack/facebook-feed/cached-time', $expireTime ) );
			}

			$expireTime = $this->facebook_feed_get_transient_expire( $settings );

			if ( $settings['data_cache'] == 'yes' ) {
				set_transient( $transient_key, $data, apply_filters( 'element-pack/facebook-feed/cached-time', $expireTime ) );
			}
		}

		return $data;
	}

	public function facebook_feed_data_remote_request( $url ) {

		$response = wp_remote_get( $url, array( 'timeout' => 30 ) );

		if ( ! $response || is_wp_error( $response ) ) {
			return false;
		}

		$remote_data = wp_remote_retrieve_body( $response );

		if ( ! $remote_data || is_wp_error( $remote_data ) ) {
			return false;
		}

		$remote_data = json_decode( $remote_data, true );

		if ( empty( $remote_data ) ) {
			return false;
		}
		return $remote_data;
	}

	public function facebook_feed_transient_feed_data( $raw_data = [] ) {
		return $raw_data['data'];
	}

	public function facebook_feed_error_notice( $message ) {
		?>
		<div class="bdt-alert-warning" data-bdt-alert>
			<a class="bdt-alert-close" data-bdt-close></a>
			<p>
				<?php echo esc_html( $message ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Get the upload directory for cached Facebook images
	 */
	private function facebook_feed_get_upload_dir() {
		$upload_dir = wp_upload_dir();
		$fb_cache_dir = trailingslashit( $upload_dir['basedir'] ) . 'element-pack/facebook-feed/';
		$fb_cache_url = trailingslashit( $upload_dir['baseurl'] ) . 'element-pack/facebook-feed/';
		
		if ( ! file_exists( $fb_cache_dir ) ) {
			wp_mkdir_p( $fb_cache_dir );
			// Add .htaccess to protect directory
			file_put_contents( $fb_cache_dir . '.htaccess', 'Options -Indexes' );
		}
		
		return array(
			'dir' => $fb_cache_dir,
			'url' => $fb_cache_url
		);
	}

	/**
	 * Download and cache a single image locally
	 */
	private function facebook_feed_download_and_cache_image( $image_url, $identifier = '' ) {
		if ( empty( $image_url ) ) {
			return array( 'url' => $image_url, 'filename' => null );
		}
		
		// Generate unique filename based on URL
		$filename = md5( $image_url . $identifier ) . '.jpg';
		$upload_info = $this->facebook_feed_get_upload_dir();
		$file_path = $upload_info['dir'] . $filename;
		$file_url = $upload_info['url'] . $filename;
		
		// If file already exists and is recent, return local URL
		if ( file_exists( $file_path ) ) {
			return array( 'url' => $file_url, 'filename' => $filename );
		}
		
		// Download image
		$response = wp_remote_get( $image_url, array(
			'timeout' => 30,
			'sslverify' => false
		) );
		
		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return array( 'url' => $image_url, 'filename' => null ); // Return original URL if download fails
		}
		
		$image_data = wp_remote_retrieve_body( $response );
		
		if ( empty( $image_data ) ) {
			return array( 'url' => $image_url, 'filename' => null );
		}
		
		// Save image locally
		$saved = file_put_contents( $file_path, $image_data );
		
		if ( $saved === false ) {
			return array( 'url' => $image_url, 'filename' => null );
		}
		
		return array( 'url' => $file_url, 'filename' => $filename );
	}

	/**
	 * Cache all images in a feed item
	 */
	private function facebook_feed_cache_feed_images( $item ) {
		if ( empty( $item ) ) {
			return array( 'data' => $item, 'images' => array() );
		}
		
		$downloaded_files = array();
		
		// Cache main feature image
		if ( ! empty( $item['full_picture'] ) ) {
			$result = $this->facebook_feed_download_and_cache_image( $item['full_picture'], 'full' );
			$item['full_picture_cached'] = $result['url'];
			if ( $result['filename'] ) {
				$downloaded_files[] = $result['filename'];
			}
		}
		
		// Cache author profile image
		if ( ! empty( $item['from']['id'] ) ) {
			$avatar_url = "https://graph.facebook.com/v4.0/{$item['from']['id']}/picture";
			$result = $this->facebook_feed_download_and_cache_image( $avatar_url, 'avatar_' . $item['from']['id'] );
			$item['from']['avatar_cached'] = $result['url'];
			if ( $result['filename'] ) {
				$downloaded_files[] = $result['filename'];
			}
		}
		
		// Cache album images
		if ( ! empty( $item['attachments']['data'] ) && is_array( $item['attachments']['data'] ) ) {
			foreach ( $item['attachments']['data'] as $att_key => $attachment ) {
				if ( isset( $attachment['subattachments']['data'] ) && is_array( $attachment['subattachments']['data'] ) ) {
					foreach ( $attachment['subattachments']['data'] as $sub_key => $subattachment ) {
						if ( isset( $subattachment['media']['image']['src'] ) ) {
							$result = $this->facebook_feed_download_and_cache_image( 
								$subattachment['media']['image']['src'], 
								'album_' . $sub_key 
							);
							$item['attachments']['data'][$att_key]['subattachments']['data'][$sub_key]['media']['image']['src_cached'] = $result['url'];
							if ( $result['filename'] ) {
								$downloaded_files[] = $result['filename'];
							}
						}
					}
				}
			}
		}
		
		return array(
			'data' => $item,
			'images' => $downloaded_files
		);
	}

	/**
	 * Clean up old cached images
	 */
	public function facebook_feed_cleanup_old_images( $max_age_days = 30 ) {
		$upload_info = $this->facebook_feed_get_upload_dir();
		$cache_dir = $upload_info['dir'];
		
		if ( ! file_exists( $cache_dir ) ) {
			return;
		}
		
		$files = glob( $cache_dir . '*.jpg' );
		$now = time();
		$max_age = $max_age_days * DAY_IN_SECONDS;
		
		foreach ( $files as $file ) {
			if ( is_file( $file ) ) {
				$file_age = $now - filemtime( $file );
				if ( $file_age > $max_age ) {
					@unlink( $file );
				}
			}
		}
	}

	/**
	 * Clean up images for a specific page when transient expires
	 */
	private function facebook_feed_cleanup_page_images( $page_id ) {
		$upload_info = $this->facebook_feed_get_upload_dir();
		$cache_dir = $upload_info['dir'];
		
		if ( ! file_exists( $cache_dir ) ) {
			return;
		}
		
		// Get transient key to track when it was last set
		$transient_key = sprintf( 'bdt-facebook-feed-data-%s', md5( $page_id ) );
		$image_list_key = $transient_key . '_images';
		
		// Get list of images from previous cache
		$old_images = get_transient( $image_list_key );
		
		if ( ! empty( $old_images ) && is_array( $old_images ) ) {
			foreach ( $old_images as $image_file ) {
				$file_path = $cache_dir . $image_file;
				if ( file_exists( $file_path ) ) {
					@unlink( $file_path );
				}
			}
		}
		
		// Clear the image list
		delete_transient( $image_list_key );
	}

	protected function render_facebook_feed_item_content( $item, $settings ) {
		?>
		<div class="bdt-content">
			<div class="bdt-fb-content">
				<div class="bdt-inner-content">
					<div class="bdt-author-wrap">
						<?php $this->render_facebook_feed_author_image( $item, $settings ); ?>
						<div>
							<?php $this->render_facebook_feed_author_name( $item, $settings ); ?>
							<?php $this->render_facebook_feed_date( $item, $settings ); ?>
						</div>
					</div>
					<?php
					if ( 'yes' == $settings['show_like'] || 'yes' == $settings['show_comments'] ) {
						printf( '<div class="bdt-social-button">' );
					}
					$this->render_facebook_feed_like( $item, $settings );
					$this->render_facebook_feed_comments( $item, $settings );
					if ( 'yes' == $settings['show_like'] || 'yes' == $settings['show_comments'] ) {
						printf( '</div>' );
					}
					?>
				</div>
				<?php $this->render_facebook_feed_desc( $item, $settings ); ?>
			</div>
			<div class="bdt-img-content">
				<?php
				if ( 'yes' == $settings['show_read_more'] || 'yes' == $settings['show_share'] ) {
					printf( '<div class="bdt-share-and-readmore">' );
				}
				if ( 'yes' === $settings['show_read_more'] ) {
					$this->render_facebook_feed_read_more( $item, $settings );
				}
				if ( 'yes' === $settings['show_share'] ) {
					$this->render_facebook_feed_main_share( $item, $settings );
				}
				if ( 'yes' == $settings['show_read_more'] || 'yes' == $settings['show_share'] ) {
					printf( '</div>' );
				}
				?>
				<?php $this->render_facebook_feed_feature_image( $item, $settings ); ?>
			</div>
		</div>
		<?php
	}

	protected function render_facebook_feed_carosuel_item( $items, $settings ) {
		foreach ( $items as $item ) :
			?>
			<div class="swiper-slide bdt-item">
				<?php $this->render_facebook_feed_item_content( $item, $settings ); ?>
			</div>
			<?php
		endforeach;
	}

	protected function render_facebook_feed_carousel_header( $settings ) {
		$this->render_swiper_header_attribute( 'facebook-feed-carousel' );
		$this->add_render_attribute( 'carousel', 'class', 'bdt-facebook-feed-carousel  bdt-fb-' . $settings['layout_style'] . '' );
		?>
		<div <?php $this->print_render_attribute_string( 'carousel' ); ?>>
			<div <?php $this->print_render_attribute_string( 'swiper' ); ?>>
				<div class="swiper-wrapper">
					<?php
	}

	protected function render_facebook_feed( $type ) {
		$settings        = $this->get_settings_for_display();
		$this->feed_data = $this->facebook_feed_data();
		$data            = $this->feed_data;

		if ( false === $data ) {
			return;
		}

		$post_limit = ! empty( $settings['post_limit'] ) ? $settings['post_limit'] : 6;
		$items      = array_splice( $data, 0, $post_limit );

		if ( 'carousel' === $type ) {
			$this->render_facebook_feed_carousel_header( $settings );
			$this->render_facebook_feed_carosuel_item( $items, $settings );
			$this->render_footer();
			return;
		}

		$this->add_render_attribute( 'facebook-feed', 'class', 'bdt-facebook-feed-wrap bdt-fb-' . $settings['layout_style'] . '' );
		?>
		<div <?php $this->print_render_attribute_string( 'facebook-feed' ); ?>>
			<div class="bdt-grid-wrap">
				<?php foreach ( $items as $item ) : ?>
					<div class="bdt-item">
						<?php $this->render_facebook_feed_item_content( $item, $settings ); ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}


	protected function register_lottie_source_controls($type) {
		if ( 'icon_box' === $type ) {
		$this->add_control(
			'lottie_json_source',
			[ 
				'label'   => esc_html__('Select JSON Source', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'url',
				'options' => [ 
					'url'    => esc_html__('Load From URL', 'bdthemes-element-pack'),
					'local'  => esc_html__('Self Hosted', 'bdthemes-element-pack'),
					'custom' => esc_html__('Custom JSON Code', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'lottie_json_path',
			[ 
				'label'         => esc_html__('Lottie JSON URL', 'bdthemes-element-pack'),
				/* translators: %1$s and %2$s are HTML tags for a link */
				'description'   => sprintf( esc_html__('Enter your lottie josn file, if you don\'t understand lottie json file so please %1$s look here %2$s', 'bdthemes-element-pack'), '<a href="https://lottiefiles.com/featured" target="_blank">', '</a>'),
				'type'          => Controls_Manager::TEXT,
				'autocomplete'  => false,
				'show_external' => false,
				'label_block'   => true,
				'show_label'    => false,
				'default'       => BDTEP_ASSETS_URL . 'others/rocket-space.json',
				'placeholder'   => esc_html__('Enter your json URL', 'bdthemes-element-pack'),
				'condition'     => [ 
					'lottie_json_source' => 'url',
				],
				'dynamic'       => [ 
					'active' => true,
				],

			]
		);

		$this->add_control(
			'upload_json_file',
			[ 
				'label'       => esc_html__('Select JSON File', 'bdthemes-element-pack'),
				'type'        => 'json-upload',
				'label_block' => true,
				'show_label'  => true,
				//'callback_selector'=>'lottie_json_path',
				'condition'   => [ 
					'lottie_json_source' => 'local',
				],
				'dynamic'     => [ 
					'active' => true,
				],
			]
		);

		$this->add_control(
			'lottie_json_code',
			[ 
				'label'       => esc_html__('Paste JSON Code', 'bdthemes-element-pack'),
				/* translators: %1$s and %2$s are HTML tags for a link */
				'description' => sprintf( esc_html__('Enter your lottie josn text, if you don\'t understand lottie json file so please %1$s look here %2$s', 'bdthemes-element-pack'), '<a href="https://lottiefiles.com/featured" target="_blank">', '</a>'),
				'type'        => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'show_label'  => true,
				'dynamic'     => [ 
					'active' => true,
				],
				'placeholder' => esc_html__('Enter your json TEXT', 'bdthemes-element-pack'),
				'condition'   => [ 
					'lottie_json_source' => 'custom',
				],

			]
		);
		} else {
		$this->add_control(
			'lottie_json_source',
			[ 
				'label'   => __( 'Select JSON Source', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'url',
				'options' => [ 
					'url'    => __( 'Load From URL', 'bdthemes-element-pack' ),
					'local'  => __( 'Self Hosted', 'bdthemes-element-pack' ),
					'custom' => __( 'Custom JSON Code', 'bdthemes-element-pack' ),
				],
			]
		);

		$this->add_control(
			'lottie_json_path',
			[ 
				'label'         => __( 'Lottie JSON URL', 'bdthemes-element-pack' ),
				'description'   => sprintf( __( 'Enter your lottie josn file, if you don\'t understand lottie json file so please %1s look here %2s', 'bdthemes-element-pack' ), '<a href="https://lottiefiles.com/featured" target="_blank">', '</a>' ),
				'type'          => Controls_Manager::TEXT,
				'autocomplete'  => false,
				'show_external' => false,
				'label_block'   => true,
				'show_label'    => false,
				'default'       => BDTEP_ASSETS_URL . 'others/teamwork.json',
				'placeholder'   => __( 'Enter your json URL', 'bdthemes-element-pack' ),
				'condition'     => [ 
					'lottie_json_source' => 'url',
				],
				'dynamic'       => [ 
					'active' => true,
				],

			]
		);

		$this->add_control(
			'upload_json_file',
			[ 
				'label'       => __( 'Select JSON File', 'bdthemes-element-pack' ),
				'type'        => 'json-upload',
				'label_block' => true,
				'show_label'  => true,
				'condition'   => [ 
					'lottie_json_source' => 'local',
				],
				'dynamic'     => [ 
					'active' => true,
				],
			]
		);

		$this->add_control(
			'lottie_json_code',
			[ 
				'label'       => __( 'Paste JSON Code', 'bdthemes-element-pack' ),
				'description' => sprintf( __( 'Enter your lottie josn text, if you don\'t understand lottie json file so please %1s look here %2s', 'bdthemes-element-pack' ), '<a href="https://lottiefiles.com/featured" target="_blank">', '</a>' ),
				'type'        => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'show_label'  => true,
				'dynamic'     => [ 
					'active' => true,
				],
				'placeholder' => __( 'Enter your json TEXT', 'bdthemes-element-pack' ),
				'condition'   => [ 
					'lottie_json_source' => 'custom',
				],

			]
		);
		}
	}

	protected function register_lottie_animation_controls($type) {
		if ( 'icon_box' === $type ) {
		$this->add_control(
			'play_action',
			[ 
				'label'   => esc_html__('Play Action', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'autoplay',
				'options' => [ 
					''         => esc_html__('None', 'bdthemes-element-pack'),
					'autoplay' => esc_html__('Auto Play', 'bdthemes-element-pack'),
					'click'    => esc_html__('Play on Click', 'bdthemes-element-pack'),
					'column'   => esc_html__('Play on Hover', 'bdthemes-element-pack'),
					'section'  => esc_html__('Play on Hover Section', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'view_type',
			[ 
				'label'     => esc_html__('Start When', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'options'   => [ 
					'pageload' => esc_html__('Page Loaded', 'bdthemes-element-pack'),
					'scroll'   => esc_html__('When Scroll', 'bdthemes-element-pack'),
				],
				'default'   => 'pageload',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'loop',
			[ 
				'label'   => esc_html__('Loop', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control( //*
			'lottie_number_of_times',
			[ 
				'label'              => esc_html__('Times', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::NUMBER,
				'render_type'        => 'content',
				// 'conditions' => [
				//  'relation' => 'and',
				//  'terms' => [
				//      [
				//          'name' => 'lottie_trigger',
				//          'operator' => '!==',
				//          'value' => 'bind_to_scroll',
				//      ],
				//      [
				//          'name' => 'loop',
				//          'operator' => '===',
				//          'value' => 'yes',
				//      ],
				//  ],
				// ],
				'min'                => 0,
				'step'               => 1,
				'frontend_available' => true,
				'condition'          => [ 
					'loop' => [ 'yes' ],
				]
			]
		);

		$this->add_control(
			'speed',
			[ 
				'label' => esc_html__('Play Speed', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [ 
					'px' => [ 
						'min'  => 0.1,
						'max'  => 1,
						'step' => 0.1,
					],
				],
			]
		);

		$this->add_control(
			'lottie_start_point',
			[ 
				'label'              => esc_html__('Start Point', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'frontend_available' => true,
				'render_type'        => 'content',
				'default'            => [ 
					'size' => '0',
					'unit' => '%',
				],
				'size_units'         => [ '%' ],
			]
		);

		$this->add_control(
			'lottie_end_point',
			[ 
				'label'              => esc_html__('End Point', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'frontend_available' => true,
				'render_type'        => 'content',
				'default'            => [ 
					'size' => '100',
					'unit' => '%',
				],
				'size_units'         => [ '%' ],
			]
		);

		$this->add_control(
			'lottie_renderer',
			[ 
				'label'     => esc_html__('Renderer', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'svg',
				'options'   => [ 
					'svg'    => esc_html__('SVG', 'bdthemes-element-pack'),
					'canvas' => esc_html__('Canvas', 'bdthemes-element-pack'),
				],
				'separator' => 'before',
			]
		);
		} else {
		$this->add_control(
			'play_action',
			[ 
				'label'   => __( 'Play Action', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'autoplay',
				'options' => [ 
					''         => __( 'None', 'bdthemes-element-pack' ),
					'autoplay' => __( 'Auto Play', 'bdthemes-element-pack' ),
					'hover'    => __( 'Play on Hover', 'bdthemes-element-pack' ),
					'click'    => __( 'Play on Click', 'bdthemes-element-pack' ),
					'column'   => __( 'Play on Hover Column', 'bdthemes-element-pack' ),
					'section'  => __( 'Play on Hover Section', 'bdthemes-element-pack' ),
				],
			]
		);

		$this->add_control(
			'view_type',
			[ 
				'label'     => esc_html__( 'Start When', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [ 
					'pageload' => esc_html__( 'Page Loaded', 'bdthemes-element-pack' ),
					'scroll'   => esc_html__( 'When Scroll', 'bdthemes-element-pack' ),
				],
				'default'   => 'pageload',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'loop',
			[ 
				'label'   => esc_html__( 'Loop', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'lottie_number_of_times',
			[ 
				'label'              => __( 'Times', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::NUMBER,
				'render_type'        => 'content',
				'min'                => 0,
				'step'               => 1,
				'frontend_available' => true,
				'condition'          => [ 
					'loop' => [ 'yes' ],
				]
			]
		);

		$this->add_control(
			'speed',
			[ 
				'label'   => esc_html__( 'Play Speed', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => [ 
					'px' => [ 
						'min'  => 0.1,
						'max'  => 5,
						'step' => 0.1,
					],
				],
				'default' => [ 
					'size' => '1',
				],
			]
		);

		$this->add_control(
			'lottie_start_point',
			[ 
				'label'              => __( 'Start Point', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::SLIDER,
				'frontend_available' => true,
				'render_type'        => 'content',
				'default'            => [ 
					'size' => '0',
					'unit' => '%',
				],
				'size_units'         => [ '%' ],
			]
		);

		$this->add_control(
			'lottie_end_point',
			[ 
				'label'              => __( 'End Point', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::SLIDER,
				'frontend_available' => true,
				'render_type'        => 'content',
				'default'            => [ 
					'size' => '100',
					'unit' => '%',
				],
				'size_units'         => [ '%' ],
			]
		);

		$this->add_control(
			'lottie_renderer',
			[ 
				'label'     => __( 'Renderer', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'svg',
				'options'   => [ 
					'svg'    => __( 'SVG', 'bdthemes-element-pack' ),
					'canvas' => __( 'Canvas', 'bdthemes-element-pack' ),
				],
				'separator' => 'before',
			]
		);
		}
	}

	protected function register_lottie_controls($type) {
		if ( 'icon_box' === $type ) {
			$this->register_lottie_icon_box_content_layout_controls();
			$this->register_lottie_icon_box_content_additional_controls();
			$this->register_lottie_icon_box_content_readmore_controls();
			$this->register_lottie_icon_box_content_indicator_controls();
			$this->register_lottie_icon_box_content_badge_controls();
			$this->register_lottie_icon_box_style_image_controls();
			$this->register_lottie_icon_box_style_title_controls();
			$this->register_lottie_icon_box_style_sub_title_controls();
			$this->register_lottie_icon_box_style_description_controls();
			$this->register_lottie_icon_box_style_title_separator_controls();
			$this->register_lottie_icon_box_style_readmore_controls();
			$this->register_lottie_icon_box_style_indicator_controls();
			$this->register_lottie_icon_box_style_badge_controls();
			$this->register_lottie_icon_box_style_additional_controls();
		} else {
			$this->register_lottie_image_content_layout_controls();
			$this->register_lottie_image_content_additional_controls();
			$this->register_lottie_image_style_image_controls();
			$this->register_lottie_image_style_caption_controls();
		}
	}

	protected function register_lottie_icon_box_content_layout_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[ 
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->register_lottie_source_controls( 'icon_box' );

		$this->register_lottie_animation_controls( 'icon_box' );

		$this->add_control(
			'title_text',
			[ 
				'label'       => esc_html__('Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 
					'active' => true,
				],
				'default'     => esc_html__('Icon Box Heading', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Enter your title', 'bdthemes-element-pack'),
				'label_block' => true,
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'title_link',
			[ 
				'label'        => esc_html__('Title Link', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-title-link-'
			]
		);

		$this->add_control(
			'title_link_url',
			[ 
				'label'       => esc_html__('Title Link URL', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => 'http://your-link.com',
				'condition'   => [ 
					'title_link' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_sub_title',
			[ 
				'label'     => esc_html__('Show Sub Title', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sub_title_text',
			[ 
				'label'       => esc_html__('Sub Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 
					'active' => true,
				],
				'default'     => esc_html__('Icon Box Sub Heading', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Enter your sub title', 'bdthemes-element-pack'),
				'label_block' => true,
				'condition'   => [ 
					'show_sub_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_separator',
			[ 
				'label'     => esc_html__('Title Separator', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'description_text',
			[ 
				'label'       => esc_html__('Description', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => [ 
					'active' => true,
				],
				'default'     => esc_html__('Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Enter your description', 'bdthemes-element-pack'),
				'rows'        => 10,
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'position',
			[ 
				'label'        => esc_html__('Icon Position', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::CHOOSE,
				'separator'    => 'before',
				'default'      => 'top',
				'options'      => [ 
					'left'  => [ 
						'title' => esc_html__('Start', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-left',
					],
					'top'   => [ 
						'title' => esc_html__('Top', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-top',
					],
					'right' => [ 
						'title' => esc_html__('End', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'prefix_class' => 'elementor-position-',
				'toggle'       => false,
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'icon_inline',
			[ 
				'label'     => esc_html__('Icon Inline', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [ 
					'position' => [ 'left', 'right' ]
				],
			]
		);

		$this->add_control(
			'icon_vertical_alignment',
			[ 
				'label'        => esc_html__('Icon Vertical Alignment', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [ 
					'top'    => [ 
						'title' => esc_html__('Top', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [ 
						'title' => esc_html__('Middle', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [ 
						'title' => esc_html__('Bottom', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'      => 'top',
				'toggle'       => false,
				'prefix_class' => 'elementor-vertical-align-',
				'condition'    => [ 
					'position'    => [ 'left', 'right' ],
					'icon_inline' => '',
				],
			]
		);

		$this->add_responsive_control(
			'text_align',
			[ 
				'label'     => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [ 
					'left'    => [ 
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [ 
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [ 
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [ 
						'title' => esc_html__('Justified', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
	}

	protected function register_lottie_icon_box_content_additional_controls() {
		$this->start_controls_section(
			'section_content_additional',
			[ 
				'label' => esc_html__('Additional Options', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'top_icon_vertical_offset',
			[ 
				'label'          => esc_html__('Icon Vertical Offset', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [ 
					'size' => 0,
				],
				'tablet_default' => [ 
					'size' => 0,
				],
				'mobile_default' => [ 
					'size' => 0,
				],
				'range'          => [ 
					'px' => [ 
						'min' => 0,
						'max' => 200,
					],
				],
				'condition'      => [ 
					'position' => 'top',
				],
				'selectors'      => [ 
					'{{WRAPPER}}' => '--ep-lottie-icon-box-icon-top-v-offset: -{{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'top_icon_horizontal_offset',
			[ 
				'label'          => esc_html__('Icon Horizontal Offset', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'range'          => [ 
					'px' => [ 
						'min' => -200,
						'max' => 200,
					],
				],
				'default'        => [ 
					'size' => 0,
				],
				'tablet_default' => [ 
					'size' => 0,
				],
				'mobile_default' => [ 
					'size' => 0,
				],
				'condition'      => [ 
					'position' => 'top',
				],
				'selectors'      => [ 
					'{{WRAPPER}}' => '--ep-lottie-icon-box-icon-top-h-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'left_right_icon_horizontal_offset',
			[ 
				'label'          => esc_html__('Icon Horizontal Offset', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [ 
					'size' => 0,
				],
				'tablet_default' => [ 
					'size' => 0,
				],
				'mobile_default' => [ 
					'size' => 0,
				],
				'range'          => [ 
					'px' => [ 
						'min' => -200,
						'max' => 200,
					],
				],
				'condition'      => [ 
					'position' => [ 'left', 'right' ],
				],
				'selectors'      => [ 
					'{{WRAPPER}}' => '--ep-lottie-icon-box-icon-left-h-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'left_right_icon_vertical_offset',
			[ 
				'label'          => esc_html__('Icon Vertical Offset', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'range'          => [ 
					'px' => [ 
						'min' => -200,
						'max' => 200,
					],
				],
				'default'        => [ 
					'size' => 0,
				],
				'tablet_default' => [ 
					'size' => 0,
				],
				'mobile_default' => [ 
					'size' => 0,
				],
				'condition'      => [ 
					'position' => [ 'left', 'right' ],
				],
				'selectors'      => [ 
					'{{WRAPPER}}' => '--ep-lottie-icon-box-icon-left-v-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_control(
			'title_size',
			[ 
				'label'   => esc_html__('Title HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => element_pack_title_tags(),
			]
		);

		$this->add_control(
			'readmore',
			[ 
				'label'     => esc_html__('Read More Button', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'default'   => 'yes',
			]
		);

		$this->add_control(
			'indicator',
			[ 
				'label' => esc_html__('Indicator', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'badge',
			[ 
				'label' => esc_html__('Badge', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'global_link',
			[ 
				'label'        => esc_html__('Global Link', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-global-link-',
				'description'  => esc_html__('Be aware! When Global Link activated then title link and read more link will not work', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'global_link_url',
			[ 
				'label'       => esc_html__('Global Link URL', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => 'http://your-link.com',
				'condition'   => [ 
					'global_link' => 'yes'
				]
			]
		);

		$this->end_controls_section();
	}

	protected function register_lottie_icon_box_content_readmore_controls() {
		$this->start_controls_section(
			'section_content_readmore',
			[ 
				'label'     => esc_html__('Read More', 'bdthemes-element-pack'),
				'condition' => [ 
					'readmore' => 'yes',
				],
			]
		);

		$this->add_control(
			'readmore_text',
			[ 
				'label'       => esc_html__('Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'default'     => esc_html__('Read More', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Read More', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'readmore_link',
			[ 
				'label'       => esc_html__('Link to', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 
					'active' => true,
				],
				'placeholder' => esc_html__('https://your-link.com', 'bdthemes-element-pack'),
				'default'     => [ 
					'url' => '#',
				],
				'condition'   => [ 
					'readmore' => 'yes',
					//'readmore_text!' => '',
				]
			]
		);

		$this->add_control(
			'advanced_readmore_icon',
			[ 
				'label'       => esc_html__('Icon', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::ICONS,
				'condition'   => [ 
					'readmore' => 'yes'
				],
				'label_block' => false,
				'skin'        => 'inline'
			]
		);

		$this->add_control(
			'readmore_icon_align',
			[ 
				'label'     => esc_html__('Icon Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'right',
				'options'   => [ 
					'left'  => esc_html__('Left', 'bdthemes-element-pack'),
					'right' => esc_html__('Right', 'bdthemes-element-pack'),
				],
				'condition' => [ 
					'advanced_readmore_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'readmore_icon_indent',
			[ 
				'label'     => esc_html__('Icon Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'max' => 100,
					],
				],
				'default'   => [ 
					'size' => 8,
				],
				'condition' => [ 
					'advanced_readmore_icon[value]!' => '',
					'readmore_text!'                 => '',
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box-readmore' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'readmore_on_hover',
			[ 
				'label'        => esc_html__('Show on Hover', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-readmore-on-hover-',
			]
		);

		$this->add_responsive_control(
			'readmore_horizontal_offset',
			[ 
				'label'          => esc_html__('Horizontal Offset', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [ 
					'size' => -50,
				],
				'tablet_default' => [ 
					'size' => 0,
				],
				'mobile_default' => [ 
					'size' => 0,
				],
				'range'          => [ 
					'px' => [ 
						'min' => -200,
						'max' => 200,
					],
				],
				'condition'      => [ 
					'readmore_on_hover' => 'yes',
				],
				'selectors'      => [ 
					'{{WRAPPER}}' => '--ep-lottie-icon-box-readmore-h-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'readmore_vertical_offset',
			[ 
				'label'          => esc_html__('Vertical Offset', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'devices'        => [ 'desktop', 'tablet', 'mobile' ],
				'default'        => [ 
					'size' => 0,
				],
				'tablet_default' => [ 
					'size' => 0,
				],
				'mobile_default' => [ 
					'size' => 0,
				],
				'range'          => [ 
					'px' => [ 
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors'      => [ 
					'{{WRAPPER}}' => '--ep-lottie-icon-box-readmore-v-offset: {{SIZE}}px;'
				],
				'condition'      => [ 
					'readmore_on_hover' => 'yes',
				],
			]
		);

		$this->add_control(
			'button_css_id',
			[ 
				'label'       => esc_html__('Button ID', 'bdthemes-element-pack') . BDTEP_NC,
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 
					'active' => true,
				],
				'default'     => '',
				'title'       => esc_html__('Add your custom id WITHOUT the Pound key. e.g: my-id', 'bdthemes-element-pack'),
				'description' => esc_html__('Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'bdthemes-element-pack'),
				'separator'   => 'before',
			]
		);

		$this->end_controls_section();
	}

	protected function register_lottie_icon_box_content_indicator_controls() {
		$this->start_controls_section(
			'section_content_indicator',
			[ 
				'label'     => esc_html__('Indicator', 'bdthemes-element-pack'),
				'condition' => [ 
					'indicator' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'indicator_width',
			[ 
				'label'     => esc_html__('Width', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min'  => 10,
						'step' => 2,
						'max'  => 300,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-indicator-svg' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'indicator_horizontal_offset',
			[ 
				'label'          => esc_html__('Horizontal Offset', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [ 
					'size' => 0,
				],
				'tablet_default' => [ 
					'size' => 0,
				],
				'mobile_default' => [ 
					'size' => 0,
				],
				'range'          => [ 
					'px' => [ 
						'min'  => -300,
						'step' => 2,
						'max'  => 300,
					],
				],
				'selectors'      => [ 
					'{{WRAPPER}}' => '--ep-lottie-icon-box-indicator-h-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'indicator_vertical_offset',
			[ 
				'label'          => esc_html__('Vertical Offset', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [ 
					'size' => 0,
				],
				'tablet_default' => [ 
					'size' => 0,
				],
				'mobile_default' => [ 
					'size' => 0,
				],
				'range'          => [ 
					'px' => [ 
						'min'  => -300,
						'step' => 2,
						'max'  => 300,
					],
				],
				'selectors'      => [ 
					'{{WRAPPER}}' => '--ep-lottie-icon-box-indicator-v-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'indicator_rotate',
			[ 
				'label'          => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'devices'        => [ 'desktop', 'tablet', 'mobile' ],
				'default'        => [ 
					'size' => 0,
				],
				'tablet_default' => [ 
					'size' => 0,
				],
				'mobile_default' => [ 
					'size' => 0,
				],
				'range'          => [ 
					'px' => [ 
						'min'  => -360,
						'max'  => 360,
						'step' => 5,
					],
				],
				'selectors'      => [ 
					'{{WRAPPER}}' => '--ep-lottie-icon-box-indicator-rotate: {{SIZE}}deg;'
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_lottie_icon_box_content_badge_controls() {
		$this->start_controls_section(
			'section_content_badge',
			[ 
				'label'     => esc_html__('Badge', 'bdthemes-element-pack'),
				'condition' => [ 
					'badge' => 'yes',
				],
			]
		);

		$this->add_control(
			'badge_text',
			[ 
				'label'       => esc_html__('Badge Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'POPULAR',
				'placeholder' => 'Type Badge Title',
				'dynamic'     => [ 
					'active' => true,
				],
			]
		);

		$this->add_control(
			'badge_position',
			[ 
				'label'   => esc_html__('Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top-right',
				'options' => element_pack_position(),
			]
		);

		$this->add_responsive_control(
			'badge_horizontal_offset',
			[ 
				'label'          => esc_html__('Horizontal Offset', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [ 
					'size' => 0,
				],
				'tablet_default' => [ 
					'size' => 0,
				],
				'mobile_default' => [ 
					'size' => 0,
				],
				'range'          => [ 
					'px' => [ 
						'min'  => -300,
						'step' => 2,
						'max'  => 300,
					],
				],
				'selectors'      => [ 
					'{{WRAPPER}}' => '--ep-lottie-icon-box-badge-h-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'badge_vertical_offset',
			[ 
				'label'          => esc_html__('Vertical Offset', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [ 
					'size' => 0,
				],
				'tablet_default' => [ 
					'size' => 0,
				],
				'mobile_default' => [ 
					'size' => 0,
				],
				'range'          => [ 
					'px' => [ 
						'min'  => -300,
						'step' => 2,
						'max'  => 300,
					],
				],
				'selectors'      => [ 
					'{{WRAPPER}}' => '--ep-lottie-icon-box-badge-v-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'badge_rotate',
			[ 
				'label'          => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SLIDER,
				'devices'        => [ 'desktop', 'tablet', 'mobile' ],
				'default'        => [ 
					'size' => 0,
				],
				'tablet_default' => [ 
					'size' => 0,
				],
				'mobile_default' => [ 
					'size' => 0,
				],
				'range'          => [ 
					'px' => [ 
						'min'  => -360,
						'max'  => 360,
						'step' => 5,
					],
				],
				'selectors'      => [ 
					'{{WRAPPER}}' => '--ep-lottie-icon-box-badge-rotate: {{SIZE}}deg;'
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_lottie_icon_box_style_image_controls() {
		$this->start_controls_section(
			'section_style_image',
			[ 
				'label' => esc_html__('Lottie Icon', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('image_effects');

		$this->start_controls_tab(
			'normal',
			[ 
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'icon_fill_color',
			[ 
				'label'     => esc_html__('Icon Fill Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_stroke_color',
			[ 
				'label'     => esc_html__('Icon Stroke Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap svg *' => 'stroke: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'icon_background',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap',
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[ 
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'separator'  => 'before',
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'icon_border',
				'placeholder' => '1px',
				'separator'   => 'before',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap'
			]
		);

		$this->add_control(
			'icon_radius',
			[ 
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'separator'  => 'after',
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
				'condition'  => [ 
					'icon_radius_advanced_show!' => 'yes',
				],
			]
		);

		$this->add_control(
			'icon_radius_advanced_show',
			[ 
				'label' => esc_html__('Advanced Radius', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'icon_radius_advanced',
			[ 
				'label'       => esc_html__('Radius', 'bdthemes-element-pack'),
				'description' => sprintf( esc_html__('For example: <b>%1s</b> or Go <a href="%2s" target="_blank">this link</a> and copy and paste the radius value.', 'bdthemes-element-pack'), '75% 25% 43% 57% / 46% 29% 71% 54%', 'https://9elements.github.io/fancy-border-radius/'),
				'type'        => Controls_Manager::TEXT,
				'size_units'  => [ 'px', '%' ],
				'separator'   => 'after',
				'default'     => '75% 25% 43% 57% / 46% 29% 71% 54%',
				'selectors'   => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap'     => 'border-radius: {{VALUE}}; overflow: hidden;',
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap img' => 'border-radius: {{VALUE}}; overflow: hidden;'
				],
				'condition'   => [ 
					'icon_radius_advanced_show' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'icon_shadow',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap'
			]
		);

		$this->add_responsive_control(
			'icon_space',
			[ 
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'separator' => 'before',
				'default'   => [ 
					'size' => 15,
				],
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}}.elementor-position-right .bdt-lottie-icon-box-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.elementor-position-left .bdt-lottie-icon-box-icon'  => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.elementor-position-top .bdt-lottie-icon-box-icon'   => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'(mobile){{WRAPPER}} .bdt-lottie-icon-box-icon'                  => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[ 
				'label'      => esc_html__('Size', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'vh', 'vw' ],
				'range'      => [ 
					'px' => [ 
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'rotate',
			[ 
				'label'     => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ 
					'size' => 0,
					'unit' => 'deg',
				],
				'range'     => [ 
					'deg' => [ 
						'max' => 360,
						'min' => -360,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap .bdt-lottie-container' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'icon_background_rotate',
			[ 
				'label'     => esc_html__('Background Rotate', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ 
					'size' => 0,
					'unit' => 'deg',
				],
				'range'     => [ 
					'deg' => [ 
						'max' => 360,
						'min' => -360,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'background_hover_transition_image',
			[ 
				'label'     => esc_html__('Transition Duration', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ 
					'size' => 0.3,
				],
				'range'     => [ 
					'px' => [ 
						'max'  => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->add_control(
			'opacity',
			[ 
				'label'     => esc_html__('Opacity', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-image svg' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[ 
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .bdt-lottie-image svg',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover',
			[ 
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'icon_fill_hover_color',
			[ 
				'label'     => esc_html__('Icon Fill Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-icon-wrap svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_stroke_hover_color',
			[ 
				'label'     => esc_html__('Icon Stroke Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-icon-wrap svg *' => 'stroke: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'icon_hover_background',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-icon-wrap:after',
			]
		);

		$this->add_control(
			'icon_effect',
			[ 
				'label'        => esc_html__('Effect', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'bdt-icon-effect-',
				'default'      => 'none',
				'options'      => [ 
					'none' => esc_html__('None', 'bdthemes-element-pack'),
					'a'    => esc_html__('Effect A', 'bdthemes-element-pack'),
					'b'    => esc_html__('Effect B', 'bdthemes-element-pack'),
					'c'    => esc_html__('Effect C', 'bdthemes-element-pack'),
					'd'    => esc_html__('Effect D', 'bdthemes-element-pack'),
					'e'    => esc_html__('Effect E', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'icon_hover_border_color',
			[ 
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-icon-wrap' => 'border-color: {{VALUE}};',
				],
				'condition' => [ 
					'icon_border_border!' => '',
				],
			]
		);

		$this->add_control(
			'icon_hover_radius',
			[ 
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'separator'  => 'after',
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-icon-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'icon_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-icon-wrap'
			]
		);

		$this->add_control(
			'icon_hover_rotate',
			[ 
				'label'     => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ 
					'unit' => 'deg',
				],
				'range'     => [ 
					'deg' => [ 
						'max' => 360,
						'min' => -360,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-icon-wrap .bdt-lottie-container' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'icon_hover_background_rotate',
			[ 
				'label'     => esc_html__('Background Rotate', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ 
					'unit' => 'deg',
				],
				'range'     => [ 
					'deg' => [ 
						'max' => 360,
						'min' => -360,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-icon-wrap' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'opacity_hover',
			[ 
				'label'     => esc_html__('Opacity', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-image svg' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[ 
				'name'     => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-image svg',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_lottie_icon_box_style_title_controls() {
		$this->start_controls_section(
			'section_style_title',
			[ 
				'label' => esc_html__('Title', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_title_style');

		$this->start_controls_tab(
			'tab_title_style_normal',
			[ 
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'title_bottom_space',
			[ 
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[ 
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box-content .bdt-lottie-icon-box-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box-content .bdt-lottie-icon-box-title',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_style_hover',
			[ 
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'title_color_hover',
			[ 
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-content .bdt-lottie-icon-box-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'title_typography_hover',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-content .bdt-lottie-icon-box-title',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_lottie_icon_box_style_sub_title_controls() {
		$this->start_controls_section(
			'section_style_sub_title',
			[ 
				'label'     => esc_html__('Sub Title', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_sub_title' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_sub_title_style');

		$this->start_controls_tab(
			'tab_sub_title_style_normal',
			[ 
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'sub_title_bottom_space',
			[ 
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box-sub-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'sub_title_color',
			[ 
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box-content .bdt-lottie-icon-box-sub-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'sub_title_typography',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box-content .bdt-lottie-icon-box-sub-title',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_sub_title_style_hover',
			[ 
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'sub_title_color_hover',
			[ 
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-content .bdt-lottie-icon-box-sub-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'sub_title_typography_hover',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-content .bdt-lottie-icon-box-sub-title',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_lottie_icon_box_style_description_controls() {
		$this->start_controls_section(
			'section_style_description',
			[ 
				'label' => esc_html__('Description', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_description_style');

		$this->start_controls_tab(
			'tab_description_style_normal',
			[ 
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'description_bottom_space',
			[ 
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box-content .bdt-lottie-icon-box-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'description_color',
			[ 
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box-content .bdt-lottie-icon-box-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box-content .bdt-lottie-icon-box-description',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_description_style_hover',
			[ 
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'description_color_hover',
			[ 
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-content .bdt-lottie-icon-box-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'description_typography_hover',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-content .bdt-lottie-icon-box-description',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_lottie_icon_box_style_title_separator_controls() {
		$this->start_controls_section(
			'section_content_title_separator',
			[ 
				'label'     => esc_html__('Title Separator', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_separator' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_separator_type',
			[ 
				'label'   => esc_html__('Separator Type', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'line',
				'options' => [ 
					'line'        => esc_html__('Line', 'bdthemes-element-pack'),
					'bloomstar'   => esc_html__('Bloomstar', 'bdthemes-element-pack'),
					'bobbleaf'    => esc_html__('Bobbleaf', 'bdthemes-element-pack'),
					'demaxa'      => esc_html__('Demaxa', 'bdthemes-element-pack'),
					'fill-circle' => esc_html__('Fill Circle', 'bdthemes-element-pack'),
					'finalio'     => esc_html__('Finalio', 'bdthemes-element-pack'),
					//'fitical' 	  => esc_html__('Fitical', 'bdthemes-element-pack'),
					'jemik'       => esc_html__('Jemik', 'bdthemes-element-pack'),
					//'genizen' 	  => esc_html__('Genizen', 'bdthemes-element-pack'),
					'leaf-line'   => esc_html__('Leaf Line', 'bdthemes-element-pack'),
					//'lendine' 	  => esc_html__('Lendine', 'bdthemes-element-pack'),
					'multinus'    => esc_html__('Multinus', 'bdthemes-element-pack'),
					//'oradox' 	  => esc_html__('Oradox', 'bdthemes-element-pack'),
					'rotate-box'  => esc_html__('Rotate Box', 'bdthemes-element-pack'),
					'sarator'     => esc_html__('Sarator', 'bdthemes-element-pack'),
					'separk'      => esc_html__('Separk', 'bdthemes-element-pack'),
					'slash-line'  => esc_html__('Slash Line', 'bdthemes-element-pack'),
					//'subtrexo' 	  => esc_html__('Subtrexo', 'bdthemes-element-pack'),
					'tripline'    => esc_html__('Tripline', 'bdthemes-element-pack'),
					'vague'       => esc_html__('Vague', 'bdthemes-element-pack'),
					'zigzag-dot'  => esc_html__('Zigzag Dot', 'bdthemes-element-pack'),
					'zozobe'      => esc_html__('Zozobe', 'bdthemes-element-pack'),
				],
				//'render_type' => 'none',		
			]
		);

		$this->add_control(
			'title_separator_border_style',
			[ 
				'label'     => esc_html__('Separator Style', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => [ 
					'solid'  => esc_html__('Solid', 'bdthemes-element-pack'),
					'dotted' => esc_html__('Dotted', 'bdthemes-element-pack'),
					'dashed' => esc_html__('Dashed', 'bdthemes-element-pack'),
					'groove' => esc_html__('Groove', 'bdthemes-element-pack'),
				],
				'condition' => [ 
					'title_separator_type' => 'line'
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-separator' => 'border-top-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_separator_line_color',
			[ 
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'title_separator_type' => 'line'
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-separator' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_separator_height',
			[ 
				'label'     => esc_html__('Height', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 1,
						'max' => 15,
					]
				],
				'condition' => [ 
					'title_separator_type' => 'line'
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-separator' => 'border-top-width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_responsive_control(
			'title_separator_width',
			[ 
				'label'      => esc_html__('Width', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 
					'%'  => [ 
						'min' => 1,
						'max' => 100,
					],
					'px' => [ 
						'min' => 1,
						'max' => 300,
					]
				],
				'condition'  => [ 
					'title_separator_type' => 'line'
				],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-separator' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'title_separator_svg_fill_color',
			[ 
				'label'     => esc_html__('Fill Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'title_separator_type!' => 'line'
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-separator-wrap svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_separator_svg_stroke_color',
			[ 
				'label'     => esc_html__('Stroke Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'title_separator_type!' => 'line'
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-separator-wrap svg *' => 'stroke: {{VALUE}};',
				],
			]
		);


		$this->add_responsive_control(
			'title_separator_svg_width',
			[ 
				'label'      => esc_html__('Width', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 
					'%'  => [ 
						'min' => 1,
						'max' => 100,
					],
					'px' => [ 
						'min' => 1,
						'max' => 300,
					]
				],
				'condition'  => [ 
					'title_separator_type!' => 'line'
				],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-separator-wrap > *' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'title_separator_spacing',
			[ 
				'label'     => esc_html__('Separator Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-separator-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->end_controls_section();
	}

	protected function register_lottie_icon_box_style_readmore_controls() {
		$this->start_controls_section(
			'section_style_readmore',
			[ 
				'label'     => esc_html__('Read More', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'readmore' => 'yes',
				],
			]
		);

		$this->add_control(
			'readmore_attention',
			[ 
				'label' => esc_html__('Attention', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->start_controls_tabs('tabs_readmore_style');

		$this->start_controls_tab(
			'tab_readmore_normal',
			[ 
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'readmore_text_color',
			[ 
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box-readmore'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-lottie-icon-box-readmore svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'      => 'readmore_background',
				'selector'  => '{{WRAPPER}} .bdt-lottie-icon-box-readmore',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'readmore_border',
				'placeholder' => '1px',
				'separator'   => 'before',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-lottie-icon-box-readmore'
			]
		);

		$this->add_responsive_control(
			'readmore_radius',
			[ 
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'separator'  => 'after',
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'readmore_shadow',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box-readmore',
			]
		);

		$this->add_responsive_control(
			'readmore_padding',
			[ 
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box-readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'readmore_typography',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box-readmore',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_readmore_hover',
			[ 
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'readmore_hover_text_color',
			[ 
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box-readmore:hover'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-lottie-icon-box-readmore:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'      => 'readmore_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-lottie-icon-box-readmore:hover',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'readmore_hover_border_color',
			[ 
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box-readmore:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [ 
					'readmore_border_border!' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'readmore_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box-readmore:hover',
			]
		);

		$this->add_control(
			'readmore_hover_animation',
			[ 
				'label' => esc_html__('Hover Animation', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_lottie_icon_box_style_indicator_controls() {
		$this->start_controls_section(
			'section_style_indicator',
			[ 
				'label'     => esc_html__('Indicator', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'indicator' => 'yes',
				],
			]
		);

		$this->add_control(
			'indicator_style',
			[ 
				'label'   => esc_html__('Indicator Style', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [ 
					'1' => esc_html__('Style 1', 'bdthemes-element-pack'),
					'2' => esc_html__('Style 2', 'bdthemes-element-pack'),
					'3' => esc_html__('Style 3', 'bdthemes-element-pack'),
					'4' => esc_html__('Style 4', 'bdthemes-element-pack'),
					'5' => esc_html__('Style 5', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'indicator_fill_color',
			[ 
				'label'     => esc_html__('Fill Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-indicator-svg svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'indicator_stroke_color',
			[ 
				'label'     => esc_html__('Stroke Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-indicator-svg svg' => 'stroke: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_lottie_icon_box_style_badge_controls() {
		$this->start_controls_section(
			'section_style_badge',
			[ 
				'label'     => esc_html__('Badge', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'badge' => 'yes',
				],
			]
		);

		$this->add_control(
			'badge_text_color',
			[ 
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box-badge span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'      => 'badge_background',
				'selector'  => '{{WRAPPER}} .bdt-lottie-icon-box-badge span',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'        => 'badge_border',
				'placeholder' => '1px',
				'separator'   => 'before',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-lottie-icon-box-badge span'
			]
		);

		$this->add_responsive_control(
			'badge_radius',
			[ 
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'separator'  => 'after',
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box-badge span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'badge_shadow',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box-badge span',
			]
		);

		$this->add_responsive_control(
			'badge_padding',
			[ 
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box-badge span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'badge_typography',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box-badge span',
			]
		);

		$this->end_controls_section();
	}

	protected function register_lottie_icon_box_style_additional_controls() {
		$this->start_controls_section(
			'section_style_additional',
			[ 
				'label' => esc_html__('Additional', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[ 
				'label'      => esc_html__('Content Inner Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'icon_inline_spacing',
			[ 
				'label'     => esc_html__('Icon Inline Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'max' => 100,
					],
				],
				'condition' => [ 
					'position'    => [ 'left', 'right' ],
					'icon_inline' => 'yes',
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-icon-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_lottie_image_content_layout_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[ 
				'label' => esc_html__( 'Lottie Image', 'bdthemes-element-pack' ),
			]
		);

		$this->register_lottie_source_controls( 'image' );

		$this->add_responsive_control(
			'align',
			[ 
				'label'     => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [ 
					'left'   => [ 
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [ 
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [ 
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [ 
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'caption_source',
			[ 
				'label'              => __( 'Caption', 'bdthemes-element-pack' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'none',
				'options'            => [ 
					'none'           => __( 'None', 'bdthemes-element-pack' ),
					// 'title_caption'  => __( 'Title', 'bdthemes-element-pack' ),
					'custom_caption' => __( 'Custom', 'bdthemes-element-pack' ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'caption',
			[ 
				'label'       => __( 'Custom Caption', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Enter your image caption', 'bdthemes-element-pack' ),
				'condition'   => [ 
					'caption_source' => 'custom_caption'
				],
				'dynamic'     => [ 
					'active' => true,
				],
			]
		);

		$this->add_control(
			'link_to',
			[ 
				'label'   => __( 'Link', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [ 
					'none'   => __( 'None', 'bdthemes-element-pack' ),
					'custom' => __( 'Custom URL', 'bdthemes-element-pack' ),
				],
			]
		);

		$this->add_control(
			'link',
			[ 
				'label'       => __( 'Link', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'bdthemes-element-pack' ),
				'condition'   => [ 
					'link_to' => 'custom',
				],
				'show_label'  => false,
			]
		);
		$this->end_controls_section();
	}

	protected function register_lottie_image_content_additional_controls() {
		$this->start_controls_section(
			'section_content_additional',
			[ 
				'label' => esc_html__( 'Additional Settings', 'bdthemes-element-pack' ),
			]
		);

		$this->register_lottie_animation_controls( 'image' );

		$this->end_controls_section();
	}

	protected function register_lottie_image_style_image_controls() {
		$this->start_controls_section(
			'section_style_image',
			[ 
				'label' => __( 'Lottie', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'width',
			[ 
				'label'          => __( 'Width', 'bdthemes-element-pack' ),
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
					'{{WRAPPER}} .bdt-lottie-image svg' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'space',
			[ 
				'label'          => __( 'Max Width', 'bdthemes-element-pack' ) . ' (%)',
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
					'{{WRAPPER}} .bdt-lottie-image svg' => 'max-width: {{SIZE}}{{UNIT}};',
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
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'opacity',
			[ 
				'label'     => __( 'Opacity', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-image svg' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[ 
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .bdt-lottie-image svg',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover',
			[ 
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'opacity_hover',
			[ 
				'label'     => __( 'Opacity', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-image:hover svg' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[ 
				'name'     => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .bdt-lottie-image:hover svg',
			]
		);

		$this->add_control(
			'background_hover_transition',
			[ 
				'label'     => __( 'Transition Duration', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'max'  => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-lottie-image svg' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'      => 'image_border',
				'selector'  => '{{WRAPPER}} .bdt-lottie-image svg',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[ 
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-lottie-image svg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'image_box_shadow',
				'exclude'  => [ 
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .bdt-lottie-image svg',
			]
		);

		$this->end_controls_section();
	}

	protected function register_lottie_image_style_caption_controls() {
		$this->start_controls_section(
			'section_style_caption',
			[ 
				'label'     => __( 'Caption', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'caption_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'caption_align',
			[ 
				'label'     => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [ 
					'left'    => [ 
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [ 
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [ 
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [ 
						'title' => __( 'Justified', 'bdthemes-element-pack' ),
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
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [ 
					'{{WRAPPER}} .widget-image-caption' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'caption_background_color',
			[ 
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .widget-image-caption' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'caption_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
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
				'label'     => __( 'Spacing', 'bdthemes-element-pack' ),
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


	protected function get_lottie_json_data( $settings ) {
		$json_code   = '';
		$json_path   = '';
		$is_json_url = true;

		if ( 'url' === $settings['lottie_json_source'] ) {
			$json_path = $settings['lottie_json_path'];
		} elseif ( 'local' === $settings['lottie_json_source'] ) {
			$json_path = $settings['upload_json_file'];
		} elseif ( 'custom' === $settings['lottie_json_source'] ) {
			$json_code   = $settings['lottie_json_code'];
			$is_json_url = false;
		}

		return compact( 'json_path', 'json_code', 'is_json_url' );
	}

	protected function get_lottie_loop_setting( $settings ) {
		$loopSet = '';
		if ( isset( $settings['loop'] ) ) {
			$loopSet = ( $settings['loop'] ) ? true : false;
		}

		if ( ! empty( $settings['lottie_number_of_times'] ) && strlen( $settings['lottie_number_of_times'] ) > 0 ) {
			$loopSet = ( $settings['lottie_number_of_times'] ) - 1;
		}

		return $loopSet;
	}

	protected function get_lottie_start_end_points( $settings ) {
		$lottie_start_point = ( ! empty( $settings['lottie_start_point']['size'] ) ? $settings['lottie_start_point']['size'] : 0 );
		$lottie_end_point   = ( isset( $settings['lottie_end_point']['size'] ) ) ? $settings['lottie_end_point']['size'] : 0;
		$lottie_end_point   = ( strlen( $lottie_end_point ) > 0 ) ? $lottie_end_point : 100;

		return compact( 'lottie_start_point', 'lottie_end_point' );
	}

	protected function add_lottie_render_attributes( $settings, $wrapper_key = 'wrapper', $wrapper_classes = [] ) {
		extract( $this->get_lottie_json_data( $settings ) );
		extract( $this->get_lottie_start_end_points( $settings ) );
		$loopSet = $this->get_lottie_loop_setting( $settings );

		if ( ! empty( $wrapper_classes ) ) {
			$this->add_render_attribute( $wrapper_key, 'class', $wrapper_classes );
		}

		if ( ! empty( $settings['shape'] ) ) {
			$this->add_render_attribute( $wrapper_key, 'class', 'elementor-image-shape-' . $settings['shape'] );
		}

		$this->add_render_attribute(
			[
				'lottie' => [
					'id'            => 'bdt-lottie-' . $this->get_id(),
					'class'         => 'bdt-lottie-container',
					'data-settings' => [
						wp_json_encode( [
							'loop'            => $loopSet,
							'is_json_url'     => $is_json_url,
							'json_path'       => $json_path,
							'json_code'       => $json_code,
							'view_type'       => $settings['view_type'],
							'speed'           => ( $settings['speed']['size'] ) ? $settings['speed']['size'] : 1,
							'play_action'     => $settings['play_action'],
							'start_point'     => $lottie_start_point,
							'end_point'       => $lottie_end_point,
							'lottie_renderer' => $settings['lottie_renderer'],
						] ),
					],
				],
			]
		);
	}

	protected function get_lottie_image_link_url( $settings ) {
		if ( 'custom' === $settings['link_to'] ) {
			if ( empty ( $settings['link']['url'] ) ) {
				return false;
			}
			return $settings['link'];
		} else {
			return false;
		}
	}

	protected function get_lottie_image_caption( $settings ) {
		if ( 'custom_caption' === $settings['caption_source'] ) {
			return $settings['caption'];
		} else if ( 'title_caption' === $settings['caption_source'] ) {
			return get_the_title( $settings['upload_json_file'] );
		}

		return '';
	}

	protected function render_lottie_icon_box_icon($settings) {
		$this->add_lottie_render_attributes( $settings, 'wrapper', [ 'bdt-lottie-image', 'bdt-lottie-icon-box-icon-wrap' ] );
		?>
		<div class="bdt-lottie-icon-box-icon">
			<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
				<div <?php $this->print_render_attribute_string( 'lottie' ); ?>></div>
			</div>
		</div>
		<?php
	}

	protected function render_lottie_icon_box() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('advanced-icon-box-title', 'class', 'bdt-lottie-icon-box-title');

		$this->add_render_attribute('advanced-icon-box-sub-title', 'class', 'bdt-lottie-icon-box-sub-title');

		if ('yes' == $settings['title_link'] and $settings['title_link_url']['url'] ) {

			$target = $settings['title_link_url']['is_external'] ? '_blank' : '_self';

			$this->add_render_attribute('advanced-icon-box-title', 'onclick', "window.open('" . esc_url($settings['title_link_url']['url']) . "', '$target')" );
		}

		$this->add_render_attribute('description_text', 'class', 'bdt-lottie-icon-box-description');

		$this->add_inline_editing_attributes('title_text', 'none');
		$this->add_inline_editing_attributes('description_text');

		$this->add_render_attribute('readmore', 'class', [ 'bdt-lottie-icon-box-readmore' ] );

		if ( ! empty( $settings['readmore_link']['url'] ) ) {
			$this->add_link_attributes('readmore', $settings['readmore_link'] );
		}

		if ( $settings['readmore_attention'] ) {
			$this->add_render_attribute('readmore', 'class', 'bdt-ep-attention-button');
		}

		if ( $settings['readmore_hover_animation'] ) {
			$this->add_render_attribute('readmore', 'class', 'elementor-animation-' . $settings['readmore_hover_animation'] );
		}

		if ( ! empty( $settings['button_css_id'] ) ) {
			$this->add_render_attribute('readmore', 'id', $settings['button_css_id'] );
		}

		$this->add_render_attribute('advanced-icon-box', 'class', 'bdt-lottie-icon-box');

		if ('yes' == $settings['global_link'] and $settings['global_link_url']['url'] ) {

			$target = $settings['global_link_url']['is_external'] ? '_blank' : '_self';

			$this->add_render_attribute('advanced-icon-box', 'onclick', "window.open('" . esc_url($settings['global_link_url']['url']) . "', '$target')" );
		}

		if ('yes' == $settings['icon_inline'] && 'top' != $settings['position'] ) {
			$this->add_render_attribute('advanced-icon-box-icon-heading', 'class', 'bdt-icon-heading bdt-flex bdt-flex-middle');

			if ('right' == $settings['position'] ) {
				$this->add_render_attribute('advanced-icon-box-icon-heading', 'class', 'bdt-flex-row-reverse');
			}
		}

		?>
		<div <?php $this->print_render_attribute_string('advanced-icon-box'); ?>>

			<?php if ('' == $settings['icon_inline'] ) : ?>
				<?php $this->render_lottie_icon_box_icon( $settings); ?>
			<?php endif; ?>

			<div class="bdt-lottie-icon-box-content">

				<div <?php $this->print_render_attribute_string('advanced-icon-box-icon-heading'); ?>>
					<?php if ('yes' == $settings['icon_inline'] ) : ?>
						<?php $this->render_lottie_icon_box_icon( $settings ); ?>
					<?php endif; ?>

					<div class="bdt-icon-box-title-wrapper">
						<?php if ( $settings['title_text'] ) : ?>
							<<?php echo esc_attr( Utils::get_valid_html_tag( $settings['title_size'] ) ); ?>
								<?php $this->print_render_attribute_string('advanced-icon-box-title'); ?>>
								<span <?php $this->print_render_attribute_string('title_text'); ?>>
									<?php echo wp_kses( $settings['title_text'], element_pack_allow_tags('title') ); ?>
								</span>
							</<?php echo esc_attr( Utils::get_valid_html_tag( $settings['title_size'] ) ); ?>>
						<?php endif; ?>


						<?php if ('yes' == $settings['show_sub_title'] ) : ?>
							<div <?php $this->print_render_attribute_string('advanced-icon-box-sub-title'); ?>>
								<?php echo wp_kses( $settings['sub_title_text'], element_pack_allow_tags('title') ); ?>
							</div>
						<?php endif; ?>
					</div>

				</div>

				<?php if ( $settings['show_separator'] ) : ?>

					<?php if ('line' == $settings['title_separator_type'] ) : ?>
						<div class="bdt-lottie-icon-box-separator-wrap">
							<div class="bdt-lottie-icon-box-separator"></div>
						</div>
					<?php elseif ('line' != $settings['title_separator_type'] ) : ?>
						<div class="bdt-lottie-icon-box-separator-wrap">
							<?php
							$svg_image = BDTEP_ASSETS_PATH . 'images/divider/' . $settings['title_separator_type'] . '.svg';

							if ( file_exists( $svg_image ) ) {

								ob_start();

								include( $svg_image );

								$svg_image = ob_get_clean();

								echo wp_kses( $svg_image, element_pack_allow_tags('svg') );
							}
							?>
						</div>
					<?php endif; ?>

				<?php endif; ?>

				<?php if ( $settings['description_text'] ) : ?>
					<div <?php $this->print_render_attribute_string('description_text'); ?>>
						<?php echo wp_kses( $settings['description_text'], element_pack_allow_tags('text') ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $settings['readmore'] ) : ?>
					<a <?php $this->print_render_attribute_string('readmore'); ?>>
						<?php if ( ! empty( $settings['advanced_readmore_icon']['value'] ) && isset( $settings['readmore_icon_align'] ) && 'left' === $settings['readmore_icon_align'] ) : ?>
							<span class="bdt-button-icon-align-left">
								<?php \Elementor\Icons_Manager::render_icon( $settings['advanced_readmore_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] ); ?>
							</span>
						<?php endif; ?>

						<?php echo esc_html( $settings['readmore_text'] ); ?>

						<?php if ( ! empty( $settings['advanced_readmore_icon']['value'] ) && ( ! isset( $settings['readmore_icon_align'] ) || 'right' === $settings['readmore_icon_align'] ) ) : ?>
							<span class="bdt-button-icon-align-right">
								<?php \Elementor\Icons_Manager::render_icon( $settings['advanced_readmore_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] ); ?>
							</span>
						<?php endif; ?>
					</a>
				<?php endif ?>
			</div>
		</div>

		<?php if ( $settings['indicator'] ) : ?>
			<div class="bdt-indicator-svg bdt-svg-style-<?php echo esc_attr( $settings['indicator_style'] ); ?>">

				<?php echo wp_kses( element_pack_svg_icon('arrow-' . $settings['indicator_style'] ), element_pack_allow_tags('svg') ); ?>

			</div>
		<?php endif; ?>

		<?php if ( $settings['badge'] and '' != $settings['badge_text'] ) : ?>
			<div class="bdt-lottie-icon-box-badge bdt-position-<?php echo esc_attr( $settings['badge_position'] ); ?>">
				<span class="bdt-badge bdt-padding-small">
					<?php echo esc_html( $settings['badge_text'] ); ?>
				</span>
			</div>
		<?php endif; ?>

		<?php
	}

	protected function render_lottie_image() {
		$settings = $this->get_settings_for_display();

		$this->add_lottie_render_attributes( $settings, 'wrapper', [ 'bdt-lottie-image' ] );

		$link = $this->get_lottie_image_link_url( $settings );

		if ( $link ) {

			if ( \ElementPack\Element_Pack_Loader::elementor()->editor->is_edit_mode() ) {
				$this->add_render_attribute( 'link', [
					'class' => 'elementor-clickable',
				] );
			}

			$this->add_link_attributes( 'link', $link );
		}

		$caption = $this->get_lottie_image_caption( $settings );

		?>

		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>

			<?php if ( 'custom_caption' === $settings['caption_source'] ) : ?>
				<figure class="wp-caption">
				<?php endif; ?>

				<?php if ( $link ) : ?>
					<a <?php $this->print_render_attribute_string( 'link' ); ?>>
					<?php endif; ?>
					<div <?php $this->print_render_attribute_string( 'lottie' ); ?>></div>
					<?php if ( $link ) : ?>
					</a>
				<?php endif; ?>

				<?php if ( 'custom_caption' === $settings['caption_source'] ) : ?>
					<figcaption class="widget-image-caption wp-caption-text">
						<?php echo wp_kses_post( $caption ); ?>
					</figcaption>

				</figure>
			<?php endif; ?>

		</div>


		<?php
	}

	protected function content_template_lottie_icon_box() {
		?>
		?>
		<#
		var dividerBase = '<?php echo esc_url( BDTEP_ASSETS_URL ); ?>images/divider/';
		var readmoreIconHTML = elementor.helpers.renderIcon( view, settings.advanced_readmore_icon, { 'aria-hidden': true, 'class': 'fa-fw' }, 'i', 'object' );

		var jsonPath = '';
		var jsonCode = '';
		var isJsonUrl = true;
		if ( settings.lottie_json_source === 'url' ) {
			jsonPath = settings.lottie_json_path || '';
		} else if ( settings.lottie_json_source === 'local' ) {
			jsonPath = ( settings.upload_json_file && settings.upload_json_file.url ) ? settings.upload_json_file.url : ( settings.upload_json_file || '' );
		} else if ( settings.lottie_json_source === 'custom' ) {
			jsonCode = settings.lottie_json_code || '';
			isJsonUrl = false;
		}

		var lottieStart = ( settings.lottie_start_point && settings.lottie_start_point.size !== undefined ) ? settings.lottie_start_point.size : 0;
		var lottieEnd = ( settings.lottie_end_point && settings.lottie_end_point.size !== undefined && settings.lottie_end_point.size !== '' ) ? settings.lottie_end_point.size : 100;

		var loopSet = false;
		if ( settings.loop ) {
			loopSet = true;
		}
		if ( settings.lottie_number_of_times && String( settings.lottie_number_of_times ).length > 0 ) {
			loopSet = parseInt( settings.lottie_number_of_times, 10 ) - 1;
		}

		var lottieSettingsObj = {
			loop: loopSet,
			is_json_url: isJsonUrl,
			json_path: jsonPath,
			json_code: jsonCode,
			view_type: settings.view_type,
			speed: ( settings.speed && settings.speed.size ) ? settings.speed.size : 1,
			play_action: settings.play_action,
			start_point: lottieStart,
			end_point: lottieEnd,
			lottie_renderer: settings.lottie_renderer
		};
		var lottieDataAttr = JSON.stringify( lottieSettingsObj ).replace( /"/g, '&quot;' );
		var lottieId = 'bdt-lottie-' + view.getID();

		var wrapperShapeClass = settings.shape ? ' elementor-image-shape-' + settings.shape : '';

		var rmHref = ( settings.readmore_link && settings.readmore_link.url ) ? settings.readmore_link.url : '#';
		var rmTarget = ( settings.readmore_link && settings.readmore_link.is_external ) ? ' target="_blank"' : '';
		var rmRelParts = [];
		if ( settings.readmore_link && settings.readmore_link.is_external ) {
			rmRelParts.push( 'noopener', 'noreferrer' );
		}
		if ( settings.readmore_link && settings.readmore_link.nofollow ) {
			rmRelParts.push( 'nofollow' );
		}
		var rmRel = rmRelParts.length ? ' rel="' + rmRelParts.join( ' ' ) + '"' : '';

		var rmClasses = 'bdt-lottie-icon-box-readmore';
		if ( 'yes' === settings.readmore_attention ) {
			rmClasses += ' bdt-ep-attention-button';
		}
		if ( settings.readmore_hover_animation ) {
			rmClasses += ' elementor-animation-' + settings.readmore_hover_animation;
		}

		var globalOnclick = '';
		if ( 'yes' === settings.global_link && settings.global_link_url && settings.global_link_url.url ) {
			var gTarget = settings.global_link_url.is_external ? '_blank' : '_self';
			globalOnclick = "window.open('" + settings.global_link_url.url + "', '" + gTarget + "')";
		}

		var titleOnclick = '';
		if ( 'yes' !== settings.global_link && 'yes' === settings.title_link && settings.title_link_url && settings.title_link_url.url ) {
			var tTarget = settings.title_link_url.is_external ? '_blank' : '_self';
			titleOnclick = "window.open('" + settings.title_link_url.url + "', '" + tTarget + "')";
		}
		#>
		<div class="bdt-lottie-icon-box"<# if ( globalOnclick ) { #> onclick="<# print( globalOnclick ); #>"<# } #>>

			<# if ( '' === settings.icon_inline ) { #>
				<div class="bdt-lottie-icon-box-icon">
					<div class="bdt-lottie-image bdt-lottie-icon-box-icon-wrap<# print( wrapperShapeClass ); #>">
						<div id="<# print( lottieId ); #>-stack" class="bdt-lottie-container" data-settings="<# print( lottieDataAttr ); #>"></div>
					</div>
				</div>
			<# } #>

			<div class="bdt-lottie-icon-box-content">

				<# if ( 'yes' === settings.icon_inline ) { #>
					<#
					var iconHeadingClass = 'bdt-icon-heading bdt-flex bdt-flex-middle';
					if ( 'right' === settings.position ) {
						iconHeadingClass += ' bdt-flex-row-reverse';
					}
					#>
					<div class="<# print( iconHeadingClass ); #>">
						<div class="bdt-lottie-icon-box-icon">
							<div class="bdt-lottie-image bdt-lottie-icon-box-icon-wrap<# print( wrapperShapeClass ); #>">
								<div id="<# print( lottieId ); #>-inline" class="bdt-lottie-container" data-settings="<# print( lottieDataAttr ); #>"></div>
							</div>
						</div>
						<div class="bdt-icon-box-title-wrapper">
							<# if ( settings.title_text ) { #>
								<{{{ settings.title_size }}} class="bdt-lottie-icon-box-title"<# if ( titleOnclick ) { #> onclick="<# print( titleOnclick ); #>"<# } #>>
									<span class="elementor-inline-editing" data-elementor-setting-key="title_text" data-elementor-inline-editing-toolbar="none">{{{ settings.title_text }}}</span>
								</{{{ settings.title_size }}}>
							<# } #>
							<# if ( 'yes' === settings.show_sub_title && settings.sub_title_text ) { #>
								<div class="bdt-lottie-icon-box-sub-title">{{{ settings.sub_title_text }}}</div>
							<# } #>
						</div>
					</div>
				<# } else { #>
					<div>
						<div class="bdt-icon-box-title-wrapper">
							<# if ( settings.title_text ) { #>
								<{{{ settings.title_size }}} class="bdt-lottie-icon-box-title"<# if ( titleOnclick ) { #> onclick="<# print( titleOnclick ); #>"<# } #>>
									<span class="elementor-inline-editing" data-elementor-setting-key="title_text" data-elementor-inline-editing-toolbar="none">{{{ settings.title_text }}}</span>
								</{{{ settings.title_size }}}>
							<# } #>
							<# if ( 'yes' === settings.show_sub_title && settings.sub_title_text ) { #>
								<div class="bdt-lottie-icon-box-sub-title">{{{ settings.sub_title_text }}}</div>
							<# } #>
						</div>
					</div>
				<# } #>

				<# if ( settings.show_separator ) { #>
					<# if ( 'line' === settings.title_separator_type ) { #>
						<div class="bdt-lottie-icon-box-separator-wrap">
							<div class="bdt-lottie-icon-box-separator"></div>
						</div>
					<# } else { #>
						<div class="bdt-lottie-icon-box-separator-wrap">
							<img class="bdt-animation-stroke" src="<# print( dividerBase + settings.title_separator_type + '.svg' ); #>" alt="">
						</div>
					<# } #>
				<# } #>

				<# if ( settings.description_text ) { #>
					<div class="bdt-lottie-icon-box-description elementor-inline-editing" data-elementor-setting-key="description_text" data-elementor-inline-editing-toolbar="advanced">{{{ settings.description_text }}}</div>
				<# } #>

				<# if ( settings.readmore ) { #>
					<a class="<# print( rmClasses ); #>" href="<# print( rmHref ); #>"<# print( rmTarget ); #><# print( rmRel ); #><# if ( settings.button_css_id ) { #> id="<# print( settings.button_css_id ); #>"<# } #>>
						<# if ( readmoreIconHTML && readmoreIconHTML.rendered && 'left' === settings.readmore_icon_align ) { #>
							<span class="bdt-button-icon-align-left">{{{ readmoreIconHTML.value }}}</span>
						<# } #>
						{{ settings.readmore_text }}
						<# if ( readmoreIconHTML && readmoreIconHTML.rendered && ( ! settings.readmore_icon_align || 'right' === settings.readmore_icon_align ) ) { #>
							<span class="bdt-button-icon-align-right">{{{ readmoreIconHTML.value }}}</span>
						<# } #>
					</a>
				<# } #>
			</div>
		</div>

		<# if ( settings.indicator ) { #>
			<div class="bdt-indicator-svg bdt-svg-style-{{ settings.indicator_style }}"></div>
		<# } #>

		<# if ( settings.badge && settings.badge_text ) { #>
			<div class="bdt-lottie-icon-box-badge bdt-position-{{ settings.badge_position }}">
				<span class="bdt-badge bdt-padding-small">{{ settings.badge_text }}</span>
			</div>
		<# } #>
		<?php
	}
}
