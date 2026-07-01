<?php

namespace ElementPack\Modules\ReviewCardCarousel\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use ElementPack\Traits\Global_Swiper_Controls;
use ElementPack\Traits\Global_Mask_Controls;
use ElementPack\Traits\Global_Widget_Controls;
use ElementPack\Traits\Global_Controls_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Review_Card_Carousel extends Module_Base {

	use Global_Swiper_Controls;
	use Global_Mask_Controls;
	use Global_Widget_Controls;
	use Global_Controls_Functions;

	public function get_name() {
		return 'bdt-review-card-carousel';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Review Card Carousel', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-review-card-carousel';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'interactive', 'review', 'image', 'services', 'card', 'box', 'features', 'testimonial', 'client', 'carosul', 'slider' ];
	}

	public function get_style_depends() {
		return $this->ep_is_edit_mode() ? [ 'swiper', 'ep-styles' ] : [ 'swiper', 'ep-font', 'ep-review-card-carousel' ];
	}

	public function get_script_depends() {
		return $this->ep_is_edit_mode() ? [ 'swiper', 'ep-scripts' ] : [ 'swiper', 'ep-review-card-carousel', 'ep-text-read-more-toggle' ];
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/7kMyajVai6E';
	}

	public function has_widget_inner_wrapper(): bool {
        return ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
    }
	protected function is_dynamic_content(): bool {
		return false;
	}


	protected function register_controls() {
		$widget_prefix = 'review-card-carousel';

		$this->register_review_card_repeater_content_controls();

		$this->start_controls_section(
			'section_review_additional_settings',
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

		$this->register_review_card_additional_settings_controls(
			$widget_prefix,
			[
				'is_single'             => false,
				'image_position_labels' => 'start_end',
				'section_wrapper'       => false,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_navigation',
			[
				'label' => __( 'Navigation', 'bdthemes-element-pack' ),
			]
		);
		$this->register_navigation_controls();
		$this->end_controls_section();

		$this->register_carousel_settings_controls();

		$this->register_review_card_style_item_controls(
			$widget_prefix,
			[ 'show_shadow_padding' => true ]
		);
		$this->register_review_card_style_image_controls(
			$widget_prefix,
			[ 'advanced_size_nc' => true ]
		);
		$this->register_review_card_style_name_controls( $widget_prefix );
		$this->register_review_card_style_job_title_controls( $widget_prefix );
		$this->register_review_card_style_text_controls(
			$widget_prefix,
			[
				'text_margin_nc'                => true,
				'text_margin_before_typography' => true,
			]
		);
		$this->register_review_card_style_rating_controls( $widget_prefix );
		$this->register_review_card_style_additional_controls( $widget_prefix );

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

		$this->register_review_card_read_more_style_controls();
	}

	public function render_header() {
		$this->render_swiper_header_attribute( 'review-card-carousel' );
		$this->add_render_attribute( 'carousel', 'class', 'bdt-review-card-carousel' );
		?>
		<div <?php $this->print_render_attribute_string( 'carousel' ); ?>>
			<div <?php $this->print_render_attribute_string( 'swiper' ); ?>>
				<div class="swiper-wrapper">
		<?php
	}

	protected function render() {
		$settings      = $this->get_settings_for_display();
		$widget_prefix = 'review-card-carousel';

		$this->render_header();

		if ( ! empty( $settings['review_items'] ) && is_array( $settings['review_items'] ) ) {
			foreach ( $settings['review_items'] as $item ) {
				$this->render_review_card_item(
					is_array( $item ) ? $item : [],
					$settings,
					$widget_prefix,
					[
						'rating_min'         => 0,
						'flex_inline_rating' => true,
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
		var carouselId = 'bdt-review-card-carousel-' + view.getID();
		var nav = settings.navigation || 'none';
		var carouselClass = 'bdt-review-card-carousel';
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

		var ratingType     = settings.rating_type || 'star';
		var ratingPosition = settings.rating_position || 'before';
		var imageInline    = settings.image_inline || '';
		var imagePosition  = settings.iamge_position || 'top';
		var imageInlineRowClass = ( imagePosition === 'right' ) ? 'bdt-ep-img-inline bdt-flex bdt-flex-row-reverse' : 'bdt-ep-img-inline bdt-flex';
		var imageMaskClass = ( settings.image_mask_popover === 'yes' ) ? ' bdt-image-mask' : '';
		var readMoreData = '';
		if ( settings.review_words_length ) {
			readMoreData = JSON.stringify( { words_length: settings.review_words_length } );
		}

		var renderItemRating = function( item ) {
			var ratingNumber = item.rating_number && item.rating_number.size !== undefined ? parseFloat( item.rating_number.size ) : 0;
			ratingNumber = Math.min( 5.0, Math.max( 0, ratingNumber ) );
			var firstVal  = Math.floor( ratingNumber );
			var secondVal = ( ratingNumber - firstVal ) >= 0.5 ? 5 : 0;
			var score     = firstVal + '-' + secondVal;
			var ratingDisplay = item.rating_number && item.rating_number.size !== undefined ? item.rating_number.size : '';
			var html = '<div class="bdt-ep-review-card-carousel-rating bdt-flex-inline bdt-flex-middle bdt-' + ratingType + ' bdt-' + ratingPosition + '">';
			if ( ratingType === 'number' ) {
				html += '<span>' + ratingDisplay + '</span><i class="ep-icon-star-full" aria-hidden="true"></i>';
			} else {
				html += '<span class="epsc-rating epsc-rating-' + score + '">';
				for ( var s = 0; s < 5; s++ ) {
					html += '<span class="epsc-rating-item"><i class="ep-icon-star" aria-hidden="true"></i></span>';
				}
				html += '</span>';
			}
			html += '</div>';
			return html;
		};
		#>
		<div id="<# print( carouselId ); #>" class="<# print( carouselClass ); #>" data-settings="<# print( _.escape( dataSettings ) ); #>">
			<div class="swiper-carousel swiper" role="region" aria-roledescription="carousel" aria-label="<?php echo esc_attr( $this->get_title() ); ?>" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">
				<div class="swiper-wrapper">
					<# _.each( settings.review_items || [], function( item ) { #>
					<div class="bdt-ep-review-card-carousel-item swiper-slide">

						<# if ( imageInline !== 'yes' && settings.show_reviewer_image === 'yes' && item.image && item.image.url ) { #>
						<div class="bdt-ep-review-card-carousel-image<# print( imageMaskClass ); #>">
							<img src="{{ item.image.url }}" alt="{{ item.reviewer_name }}">
						</div>
						<# } #>

						<div class="bdt-ep-review-card-carousel-content">
							<# if ( imageInline === 'yes' && settings.show_reviewer_image === 'yes' ) { #>
							<div class="<# print( imageInlineRowClass ); #>">
								<# if ( item.image && item.image.url ) { #>
								<div class="bdt-ep-review-card-carousel-image<# print( imageMaskClass ); #>">
									<img src="{{ item.image.url }}" alt="{{ item.reviewer_name }}">
								</div>
								<# } #>
								<div class="bdt-flex bdt-flex-column bdt-flex-center">
									<# if ( settings.show_reviewer_name === 'yes' && item.reviewer_name ) { #>
									<{{ settings.review_name_tag }} class="bdt-ep-review-card-carousel-name">{{{ item.reviewer_name }}}</{{ settings.review_name_tag }}>
									<# } #>
									<# if ( settings.show_reviewer_job_title === 'yes' && item.reviewer_job_title ) { #>
									<div class="bdt-ep-review-card-carousel-job-title">{{{ item.reviewer_job_title }}}</div>
									<# } #>
									<# if ( settings.show_rating === 'yes' && ratingPosition === 'before' ) { #>
									{{{ renderItemRating( item ) }}}
									<# } #>
								</div>
							</div>
							<# } #>

							<# if ( imageInline !== 'yes' ) { #>
							<# if ( settings.show_reviewer_name === 'yes' && item.reviewer_name ) { #>
							<{{ settings.review_name_tag }} class="bdt-ep-review-card-carousel-name">{{{ item.reviewer_name }}}</{{ settings.review_name_tag }}>
							<# } #>
							<# if ( settings.show_reviewer_job_title === 'yes' && item.reviewer_job_title ) { #>
							<div class="bdt-ep-review-card-carousel-job-title">{{{ item.reviewer_job_title }}}</div>
							<# } #>
							<# if ( settings.show_rating === 'yes' && ratingPosition === 'before' ) { #>
							{{{ renderItemRating( item ) }}}
							<# } #>
							<# } #>

							<# if ( settings.show_review_text === 'yes' && item.review_text ) { #>
							<div class="bdt-ep-review-card-carousel-text<# if ( settings.review_words_length ) { #> bdt-ep-read-more-text<# } #>"<# if ( settings.review_words_length ) { #> data-read-more="<# print( _.escape( readMoreData ) ); #>"<# } #>>{{{ item.review_text }}}</div>
							<# } #>

							<# if ( settings.show_rating === 'yes' && ratingPosition === 'after' ) { #>
							{{{ renderItemRating( item ) }}}
							<# } #>
						</div>
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
