<?php

namespace ElementPack\Modules\BrandGrid\Widgets;

use ElementPack\Base\Module_Base;
use ElementPack\Traits\Global_Controls_Functions;
use Elementor\Controls_Manager;
use ElementPack\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Brand_Grid extends Module_Base {

	use Global_Controls_Functions;

	public function get_name() {
		return 'bdt-brand-grid';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Brand Grid', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-brand-grid';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'brand', 'grid', 'client', 'logo', 'showcase' ];
	}

	public function get_style_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'ep-styles' ];
		}

		return [ 'ep-font', 'ep-brand-grid' ];
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/a_wJL950Kz4';
	}

	public function has_widget_inner_wrapper(): bool {
		return ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	protected function is_dynamic_content(): bool {
		return false;
	}

	protected function content_template() {
		?>
		<#
		var nameTag = settings.brand_html_tag || 'h3';
		#>
		<div class="bdt-ep-brand-grid">
			<# _.each( settings.brand_items, function( item ) {
				var itemClass = 'bdt-ep-brand-grid-item';
				if ( settings.brand_event === 'hover-item' ) {
					itemClass += ' bdt-ep-brand-grid-item-hover';
				}
				var nameHtml = '<' + nameTag + ' class="bdt-ep-brand-grid-name">' + item.brand_name + '</' + nameTag + '>';
			#>
			<div class="{{ itemClass }}">
				<div class="bdt-ep-brand-grid-image">
					<# if ( item.image && item.image.url ) { #>
					<img src="{{ item.image.url }}" alt="{{ item.brand_name }}">
					<# } #>
				</div>
				<# if ( settings.brand_event === 'click' ) { #>
				<input class="bdt-ep-brand-grid-checkbox" type="checkbox">
				<# } #>
				<div class="bdt-ep-brand-grid-content">
					<div class="bdt-ep-brand-grid-icon">
						<i class="ep-icon-plus-2" aria-hidden="true"></i>
					</div>
					<div class="bdt-ep-brand-grid-inner">
						<# if ( item.brand_name && settings.show_brand_name === 'yes' ) { #>
						{{{ nameHtml }}}
						<# } #>
						<# if ( item.link && item.link.url && settings.show_website_link === 'yes' ) { #>
						<div class="bdt-ep-brand-grid-text">
							<a href="{{ item.link.url }}" class="bdt-ep-brand-grid-link">{{ item.website_link_text }}</a>
						</div>
						<# } #>
					</div>
				</div>
			</div>
			<# } ); #>
		</div>
		<?php
	}

	protected function register_controls() {
		$this->register_brand_items_controls();

		$this->start_controls_section(
			'section_additional_settings',
			[
				'label' => esc_html__( 'Additional Settings', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'           => esc_html__( 'Columns', 'bdthemes-element-pack' ),
				'type'            => Controls_Manager::SELECT,
				'desktop_default' => 3,
				'tablet_default'  => 2,
				'mobile_default'  => 1,
				'options'         => [
					1 => '1',
					2 => '2',
					3 => '3',
					4 => '4',
					5 => '5',
					6 => '6',
				],
				'selectors'       => [
					'{{WRAPPER}} .bdt-ep-brand-grid' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
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
					'{{WRAPPER}} .bdt-ep-brand-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
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
					'{{WRAPPER}} .bdt-ep-brand-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->register_brand_common_settings_controls();

		$this->end_controls_section();

		$this->register_brand_style_items_controls( 'brand-grid' );
		$this->register_brand_style_icon_controls( 'brand-grid', [ 'icon_radius_responsive' => true ] );
		$this->register_brand_style_name_controls( 'brand-grid' );
		$this->register_brand_style_website_link_controls( 'brand-grid' );
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['brand_items'] ) ) {
			return;
		}

		$this->add_render_attribute( 'brand-grid', 'class', 'bdt-ep-brand-grid' );

		$brand_items    = $settings['brand_items'];
		$brand_html_tag = Utils::get_valid_html_tag( $settings['brand_html_tag'] );

		?>
		<div <?php $this->print_render_attribute_string( 'brand-grid' ); ?>>
			<?php foreach ( $brand_items as $index => $item ) :
				$item_key = 'item-wrap' . $index;
				$this->add_render_attribute( $item_key, 'class', $this->get_brand_item_classes( $settings, 'brand-grid' ) );
				?>
				<div <?php $this->print_render_attribute_string( $item_key ); ?>>
					<?php $this->render_brand_grid_image( $item, $settings, 'brand-grid' ); ?>
					<?php
					$this->render_brand_item_content(
						$item,
						$index,
						$settings,
						'brand-grid',
						[
							'brand_html_tag' => $brand_html_tag,
						]
					);
					?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
