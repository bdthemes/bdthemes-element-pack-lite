<?php
	
	namespace ElementPack\Modules\ProductCarousel\Widgets;
	
	use ElementPack\Base\Module_Base;
	use Elementor\Controls_Manager;
	use ElementPack\Traits\Global_Swiper_Controls;
	use ElementPack\Traits\Global_Mask_Controls;
	use ElementPack\Traits\Global_Controls_Functions;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	
class Product_Carousel extends Module_Base {

	use Global_Swiper_Controls;
	use Global_Mask_Controls;
	use Global_Controls_Functions;
	
	public function get_name() {
		return 'bdt-product-carousel';
	}
	
	public function get_title() {
		return BDTEP . esc_html__( 'Product Carousel', 'bdthemes-element-pack' );
	}
	
	public function get_icon() {
		return 'bdt-wi-product-carousel bdt-new';
	}
	
	public function get_categories() {
		return [ 'element-pack' ];
	}
	
	public function get_keywords() {
		return [ 'product', 'carousel', 'client', 'logo', 'showcase' ];
	}
	
	public function get_style_depends() {
		return $this->ep_is_edit_mode() ? [ 'swiper', 'ep-styles' ] : [ 'swiper', 'ep-font', 'ep-product-carousel' ];
	}

	public function get_script_depends() {
		return $this->ep_is_edit_mode() ? [ 'swiper', 'ep-scripts' ] : [ 'swiper', 'ep-product-carousel' ];
	}
	
	public function get_custom_help_url() {
		return 'https://youtu.be/ZFpkJIctXic';
	}

public function has_widget_inner_wrapper(): bool {
	return ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
}
protected function is_dynamic_content(): bool {
	return false;
}

	protected function register_controls() {
	$widget_prefix = 'product-carousel';

	$this->register_product_items_controls( $widget_prefix );

	$this->start_controls_section(
		'section_additional_settings',
		[
			'label' => __( 'Additional Settings', 'bdthemes-element-pack' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		]
	);

	$this->register_carousel_column_gap_controls();

	$this->add_control(
		'item_match_height',
		[
			'label'        => __( 'Item Match Height', 'bdthemes-element-pack' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'prefix_class' => 'bdt-item-match-height--',
			'render_type'  => 'template',
		]
	);

	$this->register_product_common_settings_controls(
		$widget_prefix,
		[
			'readmore_wrapper_option' => 'item',
			'section_label'           => __( 'Additional Settings', 'bdthemes-element-pack' ),
			'section_wrapper'         => false,
		]
	);

	$this->end_controls_section();

	$this->register_product_readmore_content_controls( $widget_prefix );
	$this->register_product_badge_content_controls();

	$this->start_controls_section(
		'section_content_navigation',
		[
			'label' => __( 'Navigation', 'bdthemes-element-pack' ),
		]
	);

	$this->register_navigation_controls();

	$this->end_controls_section();

	$this->register_carousel_settings_controls();

	$this->register_product_style_items_controls(
		$widget_prefix,
		[
			'show_shadow_padding' => true,
		]
	);
	$this->register_product_style_image_controls( $widget_prefix );
	$this->register_product_style_title_controls( $widget_prefix );
	$this->register_product_style_price_controls( $widget_prefix );
	$this->register_product_style_text_controls( $widget_prefix );
	$this->register_product_style_readmore_controls( $widget_prefix );
	$this->register_product_style_rating_controls( $widget_prefix );
	$this->register_product_style_time_controls( $widget_prefix );
	$this->register_product_style_badge_controls( $widget_prefix );

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

	$this->register_navigation_style_controls( 'swiper-carousel' );

	$this->end_controls_section();
	}

	public function render_header() {
	$this->render_swiper_header_attribute( 'product-carousel' );
	$this->add_render_attribute( 'carousel', 'class', 'bdt-ep-product-carousel' );
	?>
	<div <?php $this->print_render_attribute_string( 'carousel' ); ?>>
		<div <?php $this->print_render_attribute_string( 'swiper' ); ?>>
			<div class="swiper-wrapper">
	<?php
	}

	public function render() {
		$settings      = $this->get_settings_for_display();
		$widget_prefix = 'product-carousel';

		$this->render_header();

		if ( ! empty( $settings['product_items'] ) ) {
			foreach ( $settings['product_items'] as $index => $item ) {
				$this->render_product_item(
					$item,
					$index,
					$settings,
					$widget_prefix,
					[
						'wrapper_link_key'   => 'item',
						'extra_item_classes' => [ 'swiper-slide' ],
					]
				);
			}
		}

		$this->render_footer();
	}

	protected function content_template() {
		$ep_viewport_lg = ! empty( get_option( 'elementor_viewport_lg' ) ) ? (int) get_option( 'elementor_viewport_lg' ) - 1 : 1023;
		$ep_viewport_md = ! empty( get_option( 'elementor_viewport_md' ) ) ? (int) get_option( 'elementor_viewport_md' ) - 1 : 767;
		?>
		<#
		var carouselId = 'bdt-product-carousel-' + view.getID();
		var nav = settings.navigation || 'none';
		var carouselClass = 'bdt-ep-product-carousel';
		if ( nav === 'arrows' ) {
			carouselClass += ' bdt-arrows-align-' + ( settings.arrows_position || 'center' );
		} else if ( nav === 'dots' ) {
			carouselClass += ' bdt-dots-align-' + ( settings.dots_position || 'bottom' );
		} else if ( nav === 'both' ) {
			carouselClass += ' bdt-arrows-dots-align-' + ( settings.both_position || 'center' );
		} else if ( nav === 'arrows-fraction' ) {
			carouselClass += ' bdt-arrows-dots-align-' + ( settings.arrows_fraction_position || 'center' );
		}

		var paginationType = '';
		if ( nav === 'arrows-fraction' ) {
			paginationType = 'fraction';
		} else if ( nav === 'both' || nav === 'dots' ) {
			paginationType = 'bullets';
		} else if ( nav === 'progressbar' ) {
			paginationType = 'progressbar';
		}

		var viewportMd = <?php echo (int) $ep_viewport_md; ?>;
		var viewportLg = <?php echo (int) $ep_viewport_lg; ?>;

		var swiperSettings = {
			loop: settings.loop === 'yes',
			speed: ( settings.speed && settings.speed.size ) ? settings.speed.size : 500,
			pauseOnHover: settings.pauseonhover === 'yes',
			slidesPerView: settings.columns_mobile ? parseInt( settings.columns_mobile, 10 ) : 1,
			slidesPerGroup: settings.slides_to_scroll_mobile ? parseInt( settings.slides_to_scroll_mobile, 10 ) : 1,
			spaceBetween: ( settings.item_gap_mobile && settings.item_gap_mobile.size ) ? parseInt( settings.item_gap_mobile.size, 10 ) : 0,
			centeredSlides: settings.centered_slides === 'yes',
			grabCursor: settings.grab_cursor === 'yes',
			freeMode: settings.free_mode === 'yes',
			effect: settings.skin || 'carousel',
			observer: !! settings.observer,
			observeParents: !! settings.observer,
			watchSlidesVisibility: settings.show_hidden_item === 'yes',
			watchSlidesProgress: settings.show_hidden_item === 'yes',
			mousewheel: !! settings.mousewheel,
			breakpoints: {},
			navigation: {
				nextEl: '#' + carouselId + ' .bdt-navigation-next',
				prevEl: '#' + carouselId + ' .bdt-navigation-prev'
			},
			pagination: {
				el: '#' + carouselId + ' .swiper-pagination',
				type: paginationType,
				clickable: 'true',
				dynamicBullets: settings.dynamic_bullets === 'yes'
			},
			scrollbar: {
				el: '#' + carouselId + ' .swiper-scrollbar',
				hide: 'true'
			},
			coverflowEffect: {
				rotate: ( settings.coverflow_toggle === 'yes' && settings.coverflow_rotate && settings.coverflow_rotate.size ) ? settings.coverflow_rotate.size : 50,
				stretch: ( settings.coverflow_toggle === 'yes' && settings.coverflow_stretch && settings.coverflow_stretch.size ) ? settings.coverflow_stretch.size : 0,
				depth: ( settings.coverflow_toggle === 'yes' && settings.coverflow_depth && settings.coverflow_depth.size ) ? settings.coverflow_depth.size : 100,
				modifier: ( settings.coverflow_toggle === 'yes' && settings.coverflow_modifier && settings.coverflow_modifier.size ) ? settings.coverflow_modifier.size : 1,
				slideShadows: true
			}
		};

		if ( settings.autoplay === 'yes' ) {
			swiperSettings.autoplay = {
				delay: settings.autoplay_speed ? parseInt( settings.autoplay_speed, 10 ) : 5000,
				disableOnInteraction: false
			};
		}

		swiperSettings.breakpoints[ viewportMd ] = {
			slidesPerView: settings.columns_tablet ? parseInt( settings.columns_tablet, 10 ) : 2,
			spaceBetween: ( settings.item_gap_tablet && settings.item_gap_tablet.size ) ? parseInt( settings.item_gap_tablet.size, 10 ) : 0,
			slidesPerGroup: settings.slides_to_scroll_tablet ? parseInt( settings.slides_to_scroll_tablet, 10 ) : 1
		};
		swiperSettings.breakpoints[ viewportLg ] = {
			slidesPerView: settings.columns ? parseInt( settings.columns, 10 ) : 3,
			spaceBetween: ( settings.item_gap && settings.item_gap.size ) ? parseInt( settings.item_gap.size, 10 ) : 0,
			slidesPerGroup: settings.slides_to_scroll ? parseInt( settings.slides_to_scroll, 10 ) : 1
		};

		var dataSettings = JSON.stringify( swiperSettings );
		var navIcon = settings.nav_arrows_icon || '5';
		var hideArrowMobile = settings.hide_arrow_on_mobile ? ' bdt-visible@m' : '';
		var rmIconHTML = elementor.helpers.renderIcon( view, settings.readmore_icon, { 'aria-hidden': true, 'class': 'fa-fw' }, 'i', 'object' );
		var iconAlign  = settings.icon_align || 'right';
		var readmoreLabel = settings.readmore_text || '<?php echo esc_js( __( 'Read More', 'bdthemes-element-pack' ) ); ?>';
		#>
		<div id="<# print( carouselId ); #>" class="<# print( carouselClass ); #>" data-settings="<# print( _.escape( dataSettings ) ); #>">
			<div class="swiper-carousel swiper" role="region" aria-roledescription="carousel" aria-label="<?php echo esc_attr( $this->get_title() ); ?>" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">
				<div class="swiper-wrapper">
					<# _.each( settings.product_items, function( item ) {

						var imageMaskClass = ( settings.image_mask_popover === 'yes' ) ? ' bdt-image-mask' : '';

						var ratingNum = parseFloat( ( item.rating_number && item.rating_number.size ) ? item.rating_number.size : 0 );
						var ratingStr = ratingNum.toString();
						var firstVal, secondVal;
						if ( ratingStr.indexOf('.') !== -1 ) {
							var parts = ratingStr.split('.');
							firstVal  = parseInt( parts[0], 10 ) <= 5 ? parseInt( parts[0], 10 ) : 5;
							secondVal = parseInt( parts[1], 10 ) < 5 ? 0 : 5;
						} else {
							firstVal  = ratingNum <= 5 ? Math.floor( ratingNum ) : 5;
							secondVal = 0;
						}
						var score = firstVal + '-' + secondVal;

						var itemHref   = ( item.readmore_link && item.readmore_link.url ) ? item.readmore_link.url : '#';
						var itemTarget = ( item.readmore_link && item.readmore_link.is_external ) ? ' target="_blank"' : '';
						var itemRel    = ( item.readmore_link && item.readmore_link.nofollow ) ? ' rel="nofollow"' : '';
					#>
					<div class="bdt-ep-product-carousel-item swiper-slide bdt-flex bdt-flex-column">

						<# if ( settings.show_image === 'yes' && item.image && item.image.url ) { #>
						<div class="bdt-ep-product-carousel-image bdt-flex-inline{{ imageMaskClass }}">
							<img src="{{ item.image.url }}" alt="{{ item.title }}">
							<# if ( settings.readmore_link_to === 'image' ) { #>
								<a href="{{ itemHref }}"{{{ itemTarget }}}{{{ itemRel }}} class="bdt-ep-product-carousel-link bdt-position-z-index"></a>
							<# } #>
						</div>
						<# } #>

						<div class="bdt-ep-product-carousel-content bdt-flex bdt-flex-column bdt-flex-between">
							<div>
								<div class="bdt-ep-product-carousel-title-price bdt-flex bdt-flex-middle bdt-flex-between">
									<# if ( settings.show_title === 'yes' && item.title ) { #>
									<{{ settings.title_tag }} class="bdt-ep-product-carousel-title">
										{{{ item.title }}}
										<# if ( settings.readmore_link_to === 'title' ) { #>
											<a href="{{ itemHref }}"{{{ itemTarget }}}{{{ itemRel }}} class="bdt-ep-product-carousel-link"></a>
										<# } #>
									</{{ settings.title_tag }}>
									<# } #>

									<# if ( settings.show_price === 'yes' && item.price ) { #>
									<div class="bdt-ep-product-carousel-price">{{{ item.price }}}</div>
									<# } #>
								</div>

								<# if ( settings.show_text === 'yes' && item.text ) { #>
								<div class="bdt-ep-product-carousel-text">{{{ item.text }}}</div>
								<# } #>

								<# if ( settings.readmore_link_to === 'button' && item.readmore_link && item.readmore_link.url ) { #>
								<div class="bdt-ep-product-carousel-readmore-wrap">
									<a href="{{ itemHref }}"{{{ itemTarget }}}{{{ itemRel }}} class="bdt-ep-product-carousel-readmore<# if ( settings.readmore_hover_animation ) { #> elementor-animation-{{ settings.readmore_hover_animation }}<# } #>">
										<# if ( settings.readmore_icon && settings.readmore_icon.value && iconAlign === 'left' ) { #>
										<span class="bdt-button-icon-align-left">
											{{{ rmIconHTML.value }}}
										</span>
										<# } #>
										{{{ readmoreLabel }}}
										<# if ( settings.readmore_icon && settings.readmore_icon.value && iconAlign === 'right' ) { #>
										<span class="bdt-button-icon-align-right">
											{{{ rmIconHTML.value }}}
										</span>
										<# } #>
									</a>
								</div>
								<# } #>
							</div>

							<div class="bdt-ep-product-carousel-rating-time bdt-flex bdt-flex-middle bdt-flex-between bdt-flex-wrap">
								<# if ( settings.show_rating === 'yes' ) { #>
								<div>
									<div class="bdt-ep-product-carousel-rating bdt-flex-inline bdt-flex-middle bdt-{{ settings.rating_type }}">
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
									<span class="bdt-ep-product-carousel-rating-count">{{{ item.rating_count }}}</span>
								</div>
								<# } #>

								<# if ( settings.show_time === 'yes' && item.time ) { #>
								<div class="bdt-ep-product-carousel-time">
									<i class="ep-icon-clock-o" aria-hidden="true"></i>
									{{{ item.time }}}
								</div>
								<# } #>
							</div>
						</div>

						<# if ( settings.badge === 'yes' && item.badge_text ) { #>
						<div class="bdt-ep-product-carousel-badge bdt-position-small bdt-position-{{ settings.badge_position }}">
							<span class="bdt-badge bdt-padding-small">{{{ item.badge_text }}}</span>
						</div>
						<# } #>

						<# if ( settings.readmore_link_to === 'item' ) { #>
							<a href="{{ itemHref }}"{{{ itemTarget }}}{{{ itemRel }}} class="bdt-ep-product-carousel-link bdt-position-z-index"></a>
						<# } #>
					</div>
					<# } ); #>
				</div>
				<# if ( settings.show_scrollbar === 'yes' ) { #>
				<div class="swiper-scrollbar"></div>
				<# } #>
			</div>

			<# if ( nav === 'both' ) { #>
			<div class="bdt-position-z-index bdt-position-{{ settings.both_position }}">
				<div class="bdt-arrows-dots-container bdt-slidenav-container">
					<div class="bdt-flex bdt-flex-middle">
						<div class="{{ hideArrowMobile }}">
							<div class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav">
								<i class="ep-icon-arrow-left-{{ navIcon }}" aria-hidden="true"></i>
							</div>
						</div>
						<# if ( settings.both_position !== 'center' ) { #>
						<div class="swiper-pagination"></div>
						<# } #>
						<div class="{{ hideArrowMobile }}">
							<div class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav">
								<i class="ep-icon-arrow-right-{{ navIcon }}" aria-hidden="true"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<# if ( settings.both_position === 'center' ) { #>
			<div class="bdt-position-z-index bdt-position-bottom">
				<div class="bdt-dots-container">
					<div class="swiper-pagination"></div>
				</div>
			</div>
			<# } #>
			<# } else if ( nav === 'arrows-fraction' ) { #>
			<div class="bdt-position-z-index bdt-position-{{ settings.arrows_fraction_position }}">
				<div class="bdt-arrows-fraction-container bdt-slidenav-container">
					<div class="bdt-flex bdt-flex-middle">
						<div class="{{ hideArrowMobile }}">
							<div class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav">
								<i class="ep-icon-arrow-left-{{ navIcon }}" aria-hidden="true"></i>
							</div>
						</div>
						<# if ( settings.arrows_fraction_position !== 'center' ) { #>
						<div class="swiper-pagination"></div>
						<# } #>
						<div class="{{ hideArrowMobile }}">
							<div class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav">
								<i class="ep-icon-arrow-right-{{ navIcon }}" aria-hidden="true"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<# if ( settings.arrows_fraction_position === 'center' ) { #>
			<div class="bdt-dots-container">
				<div class="swiper-pagination"></div>
			</div>
			<# } #>
			<# } else { #>
			<# if ( nav === 'dots' ) { #>
			<div class="bdt-position-z-index bdt-position-{{ settings.dots_position }}">
				<div class="bdt-dots-container">
					<div class="swiper-pagination"></div>
				</div>
			</div>
			<# } else if ( nav === 'progressbar' ) { #>
			<div class="swiper-pagination bdt-position-z-index bdt-position-{{ settings.progress_position }}"></div>
			<# } #>
			<# if ( nav === 'arrows' ) { #>
			<div class="bdt-position-z-index bdt-position-{{ settings.arrows_position }}{{ hideArrowMobile }}">
				<div class="bdt-arrows-container bdt-slidenav-container">
					<div class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav">
						<i class="ep-icon-arrow-left-{{ navIcon }}" aria-hidden="true"></i>
					</div>
					<div class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav">
						<i class="ep-icon-arrow-right-{{ navIcon }}" aria-hidden="true"></i>
					</div>
				</div>
			</div>
			<# } #>
			<# } #>
		</div>
		<?php
	}
}
