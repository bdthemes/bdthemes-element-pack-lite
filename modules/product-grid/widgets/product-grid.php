<?php

namespace ElementPack\Modules\ProductGrid\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use ElementPack\Traits\Global_Mask_Controls;
use ElementPack\Traits\Global_Controls_Functions;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Product_Grid extends Module_Base {

	use Global_Mask_Controls;
	use Global_Controls_Functions;

	public function get_name() {
		return 'bdt-product-grid';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Product Grid', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-product-grid';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'product', 'grid', 'client', 'logo', 'showcase' ];
	}

	public function get_style_depends() {
		return $this->ep_is_edit_mode() ? [ 'ep-styles' ] : [ 'ep-font', 'ep-product-grid' ];
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/-UJhU-ak5_k';
	}

	public function has_widget_inner_wrapper(): bool {
		return ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	protected function is_dynamic_content(): bool {
		return false;
	}

	protected function get_upsale_data(): array {
		return [
			'condition'    => ! is_ep_pro(),
			'image'        => esc_url( BDTEP_ASSETS_URL . 'images/go-pro.svg' ),
			'image_alt'    => esc_attr__( 'Upgrade', 'bdthemes-element-pack' ),
			'title'        => esc_html__( 'Unlock Premium Features', 'bdthemes-element-pack' ),
			'description'  => sprintf( __( '<ul class="bdt-widget-promotion-list"><li>%1$s</li></ul> These features are available only in Element Pack Pro.', 'bdthemes-element-pack' ), 'Link to -> Wrapper Item' ),
			'upgrade_url'  => esc_url( 'https://www.elementpack.pro/pricing/?utm_source=widget_panel&utm_medium=ep_widget_panel' ),
			'upgrade_text' => sprintf( __( '<span class="bdt-widget-promotion-btn">%s</span>', 'bdthemes-element-pack' ), esc_html__( 'Upgrade to Pro', 'bdthemes-element-pack' ) ),
		];
	}


	protected function register_controls() {
	$widget_prefix = 'product-grid';

	$this->register_product_items_controls(
		$widget_prefix,
		[
			'repeater_style_tabs' => true,
		]
	);

	$this->start_controls_section(
		'section_additional_settings',
		[
			'label' => __( 'Additional Options', 'bdthemes-element-pack' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		]
	);

	$this->add_responsive_control(
		'columns',
		[
			'label'           => __( 'Columns', 'bdthemes-element-pack' ),
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
				'{{WRAPPER}} .bdt-ep-product-grid' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
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
				'{{WRAPPER}} .bdt-ep-product-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
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
				'{{WRAPPER}} .bdt-ep-product-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
			],
		]
	);

	$this->register_product_common_settings_controls(
		$widget_prefix,
		[
			'readmore_wrapper_option' => 'wrapper',
			'section_label'           => __( 'Additional Options', 'bdthemes-element-pack' ),
			'section_wrapper'         => false,
		]
	);

	$this->end_controls_section();

	$this->register_product_readmore_content_controls( $widget_prefix );
	$this->register_product_badge_content_controls();

	$this->register_product_style_items_controls( $widget_prefix );
	$this->register_product_style_image_controls(
		$widget_prefix,
		[
			'full_tabs' => true,
		]
	);
	$this->register_product_style_title_controls( $widget_prefix );
	$this->register_product_style_price_controls( $widget_prefix );
	$this->register_product_style_text_controls( $widget_prefix );
	$this->register_product_style_readmore_controls( $widget_prefix );
	$this->register_product_style_rating_controls( $widget_prefix );
	$this->register_product_style_time_controls( $widget_prefix );
	$this->register_product_style_badge_controls( $widget_prefix );
	}

	protected function render() {
	$settings      = $this->get_settings_for_display();
	$widget_prefix = 'product-grid';

	if ( empty( $settings['product_items'] ) ) {
		return;
	}

	$this->add_render_attribute( 'product-grid', 'class', 'bdt-ep-product-grid' );

	?>
	<div <?php $this->print_render_attribute_string( 'product-grid' ); ?>>
		<?php
		foreach ( $settings['product_items'] as $index => $item ) {
			$this->render_product_item(
				$item,
				$index,
				$settings,
				$widget_prefix,
				[
					'wrapper_link_key' => 'wrapper',
				]
			);
		}
		?>
	</div>
	<?php
	}

	protected function content_template() {
		?>
		<#
		var rmIconHTML = elementor.helpers.renderIcon( view, settings.readmore_icon, { 'aria-hidden': true, 'class': 'fa-fw' }, 'i', 'object' );
		var rmMigrated = elementor.helpers.isIconMigrated( settings, 'readmore_icon' );
		var iconAlign  = settings.icon_align || 'right';
		#>
		<div class="bdt-ep-product-grid">
			<# _.each( settings.product_items, function( item ) {

				var imageMaskClass = ( settings.image_mask_popover === 'yes' ) ? ' bdt-image-mask' : '';

				var ratingNum = parseFloat( ( item.rating_number && item.rating_number.size ) ? item.rating_number.size : 0 );
				var ratingStr = ratingNum.toString();
				var firstVal, secondVal;
				if ( ratingStr.indexOf('.') !== -1 ) {
					var parts = ratingStr.split('.');
					firstVal  = parseInt( parts[0] ) <= 5 ? parseInt( parts[0] ) : 5;
					secondVal = parseInt( parts[1] ) < 5 ? 0 : 5;
				} else {
					firstVal  = ratingNum <= 5 ? Math.floor( ratingNum ) : 5;
					secondVal = 0;
				}
				var score = firstVal + '-' + secondVal;

				var itemHref   = ( item.readmore_link && item.readmore_link.url ) ? item.readmore_link.url : '#';
				var itemTarget = ( item.readmore_link && item.readmore_link.is_external ) ? ' target="_blank"' : '';
				var itemRel    = ( item.readmore_link && item.readmore_link.nofollow ) ? ' rel="nofollow"' : '';

				var titlePriceClass = ( settings.show_price === 'yes' && settings.show_title === 'yes' )
					? 'bdt-ep-product-grid-title-price bdt-flex bdt-flex-middle bdt-flex-between'
					: 'bdt-ep-product-grid-title-price';
			#>
			<div class="bdt-ep-product-grid-item bdt-flex bdt-flex-column elementor-repeater-item-{{ item._id }}">

				<# if ( settings.show_image === 'yes' && item.image && item.image.url ) { #>
				<div class="bdt-ep-product-grid-image bdt-flex-inline{{ imageMaskClass }}">
					<img src="{{ item.image.url }}" alt="{{ item.title }}">
					<# if ( settings.readmore_link_to === 'image' ) { #>
						<a href="{{ itemHref }}"{{{ itemTarget }}}{{{ itemRel }}} class="bdt-ep-product-grid-link bdt-position-z-index"></a>
					<# } #>
				</div>
				<# } #>

				<div class="bdt-ep-product-grid-content bdt-flex bdt-flex-column bdt-flex-between">
					<div>
						<div class="{{ titlePriceClass }}">
							<# if ( settings.show_title === 'yes' && item.title ) { #>
							<{{ settings.title_tag }} class="bdt-ep-product-grid-title">
								{{{ item.title }}}
								<# if ( settings.readmore_link_to === 'title' ) { #>
									<a href="{{ itemHref }}"{{{ itemTarget }}}{{{ itemRel }}} class="bdt-ep-product-grid-link"></a>
								<# } #>
							</{{ settings.title_tag }}>
							<# } #>

							<# if ( settings.show_price === 'yes' && item.price ) { #>
							<div class="bdt-ep-product-grid-price">{{{ item.price }}}</div>
							<# } #>
						</div>

						<# if ( settings.show_text === 'yes' && item.text ) { #>
						<div class="bdt-ep-product-grid-text">{{{ item.text }}}</div>
						<# } #>

						<# if ( settings.readmore_link_to === 'button' && item.readmore_link && item.readmore_link.url ) { #>
						<div class="bdt-ep-product-grid-readmore-wrap">
							<a href="{{ itemHref }}"{{{ itemTarget }}}{{{ itemRel }}} class="bdt-ep-product-grid-readmore<# if ( settings.readmore_hover_animation ) { #> elementor-animation-{{ settings.readmore_hover_animation }}<# } #>">
								<# if ( settings.readmore_icon && settings.readmore_icon.value && iconAlign === 'left' ) { #>
								<span class="bdt-button-icon-align-left">
									<# if ( rmIconHTML && rmIconHTML.rendered && rmMigrated ) { #>
										{{{ rmIconHTML.value }}}
									<# } else { #>
										<i class="{{ settings.readmore_icon.value }}" aria-hidden="true"></i>
									<# } #>
								</span>
								<# } #>
								{{{ settings.readmore_text }}}
								<# if ( settings.readmore_icon && settings.readmore_icon.value && iconAlign === 'right' ) { #>
								<span class="bdt-button-icon-align-right">
									<# if ( rmIconHTML && rmIconHTML.rendered && rmMigrated ) { #>
										{{{ rmIconHTML.value }}}
									<# } else { #>
										<i class="{{ settings.readmore_icon.value }}" aria-hidden="true"></i>
									<# } #>
								</span>
								<# } #>
							</a>
						</div>
						<# } #>
					</div>

					<div class="bdt-ep-product-grid-rating-time bdt-flex bdt-flex-middle bdt-flex-between bdt-flex-wrap">
						<# if ( settings.show_rating === 'yes' ) { #>
						<div>
							<div class="bdt-ep-product-grid-rating bdt-flex-inline bdt-flex-middle bdt-{{ settings.rating_type }}">
								<# if ( settings.rating_type === 'number' ) { #>
									<span>{{ ratingNum }}</span>
									<i class="ep-icon-star-full" aria-hidden="true"></i>
								<# } else { #>
									<span class="epsc-rating epsc-rating-{{ score }}">
										<span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
										<span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
										<span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
										<span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
										<span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>
									</span>
								<# } #>
							</div>
							<span class="bdt-ep-product-grid-rating-count">{{ item.rating_count }}</span>
						</div>
						<# } #>

						<# if ( settings.show_time === 'yes' && item.time ) { #>
						<div class="bdt-ep-product-grid-time">
							<i class="ep-icon-clock-o" aria-hidden="true"></i>
							{{{ item.time }}}
						</div>
						<# } #>
					</div>
				</div>

				<# if ( settings.badge === 'yes' && item.badge_text ) { #>
				<div class="bdt-ep-product-grid-badge bdt-position-small bdt-position-{{ settings.badge_position }}">
					<span class="bdt-badge bdt-padding-small">{{{ item.badge_text }}}</span>
				</div>
				<# } #>

				<# if ( settings.readmore_link_to === 'wrapper' ) { #>
					<a href="{{ itemHref }}"{{{ itemTarget }}}{{{ itemRel }}} class="bdt-ep-product-grid-link bdt-position-z-index"></a>
				<# } #>
			</div>
			<# } ); #>
		</div>
		<?php
	}
}
