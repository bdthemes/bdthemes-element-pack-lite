<?php

namespace ElementPack\Modules\ReviewCard\Widgets;

use ElementPack\Base\Module_Base;
use ElementPack\Traits\Global_Mask_Controls;
use ElementPack\Traits\Global_Widget_Controls;
use ElementPack\Traits\Global_Controls_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Review_Card extends Module_Base {

	use Global_Mask_Controls;
	use Global_Widget_Controls;
	use Global_Controls_Functions;

	public function get_name() {
		return 'bdt-review-card';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Review Card', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-review-card';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'interactive', 'review', 'image', 'services', 'card', 'box', 'features', 'testimonial', 'client' ];
	}

	public function get_style_depends() {
		return $this->ep_is_edit_mode() ? [ 'ep-styles' ] : [ 'ep-font', 'ep-review-card' ];
	}

	public function get_script_depends() {
		return $this->ep_is_edit_mode() ? [ 'ep-scripts' ] : [ 'ep-text-read-more-toggle' ];
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/xFtjeR1qgSE';
	}

	public function has_widget_inner_wrapper(): bool {
        return ! \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
    }
	protected function is_dynamic_content(): bool {
		return false;
	}


	protected function register_controls() {
		$widget_prefix = 'review-card';

		$this->register_review_card_single_content_controls();
		$this->register_review_card_additional_settings_controls(
			$widget_prefix,
			[
				'is_single'             => true,
				'image_position_labels' => 'left_right',
			]
		);

		$this->register_review_card_style_item_controls( $widget_prefix, [ 'is_single' => true ] );
		$this->register_review_card_style_image_controls( $widget_prefix, [ 'is_single' => true ] );
		$this->register_review_card_style_name_controls( $widget_prefix, [ 'is_single' => true ] );
		$this->register_review_card_style_job_title_controls( $widget_prefix, [ 'is_single' => true ] );
		$this->register_review_card_style_text_controls(
			$widget_prefix,
			[
				'is_single'      => true,
				'text_margin_nc' => true,
			]
		);
		$this->register_review_card_style_rating_controls( $widget_prefix );
		$this->register_review_card_read_more_style_controls();
		$this->register_review_card_style_additional_controls( $widget_prefix );
	}

	protected function render() {
		$settings      = $this->get_settings_for_display();
		$widget_prefix = 'review-card';

		$this->add_render_attribute( 'review-card', 'class', 'bdt-review-card bdt-review-card-style-1' );
		?>
		<div <?php $this->print_render_attribute_string( 'review-card' ); ?>>
			<?php
			$this->render_review_card_item(
				$settings,
				$settings,
				$widget_prefix,
				[
					'rating_min'         => 0.5,
					'wrap_rating'        => true,
					'flex_inline_rating' => false,
				]
			);
			?>
		</div>
		<?php
	}

	protected function content_template() {
		?>
		<#
		var ratingNumber = settings.rating_number && settings.rating_number.size !== undefined ? parseFloat( settings.rating_number.size ) : 0;
		ratingNumber = Math.min( 5.0, Math.max( 0.5, ratingNumber ) );
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

		var getReviewCardStarIcons = function( ratingValue, ratingMin ) {
			ratingMin = ratingMin || 0;
			ratingValue = Math.min( 5.0, Math.max( ratingMin, ratingValue ) );
			var fullStars = Math.floor( ratingValue );
			var hasHalf   = ( ratingValue - fullStars ) >= 0.495;
			var icons     = [];

			for ( var i = 1; i <= 5; i++ ) {
				if ( i <= fullStars ) {
					icons.push( 'ep-icon-star-full' );
				} else if ( hasHalf && i === ( fullStars + 1 ) ) {
					icons.push( 'ep-icon-star-half' );
				} else {
					icons.push( 'ep-icon-star-empty' );
				}
			}

			return icons;
		};

		var renderRating = function() {
			var html = '<div><div class="bdt-ep-review-card-rating bdt-' + ratingType + ' bdt-' + ratingPosition + '">';
			if ( ratingType === 'number' ) {
				html += '<span>' + ratingNumber + '</span><i class="ep-icon-star-full" aria-hidden="true"></i>';
			} else {
				var starIcons = getReviewCardStarIcons( ratingNumber, 0.5 );
				html += '<span class="epsc-rating">';
				for ( var s = 0; s < starIcons.length; s++ ) {
					html += '<span class="epsc-rating-item"><i class="' + starIcons[ s ] + '" aria-hidden="true"></i></span>';
				}
				html += '</span>';
			}
			html += '</div></div>';
			return html;
		};
		#>
		<div class="bdt-review-card bdt-review-card-style-1">
			<div class="bdt-ep-review-card-item">

				<# if ( imageInline !== 'yes' && settings.show_reviewer_image === 'yes' && settings.image && settings.image.url ) { #>
				<div class="bdt-ep-review-card-image<# print( imageMaskClass ); #>">
					<img src="{{ settings.image.url }}" alt="{{ settings.reviewer_name }}">
				</div>
				<# } #>

				<div class="bdt-ep-review-card-content">
					<# if ( imageInline === 'yes' && settings.show_reviewer_image === 'yes' ) { #>
					<div class="<# print( imageInlineRowClass ); #>">
						<# if ( settings.image && settings.image.url ) { #>
						<div class="bdt-ep-review-card-image<# print( imageMaskClass ); #>">
							<img src="{{ settings.image.url }}" alt="{{ settings.reviewer_name }}">
						</div>
						<# } #>
						<div class="bdt-flex bdt-flex-column bdt-flex-center">
							<# if ( settings.show_reviewer_name === 'yes' && settings.reviewer_name ) { #>
							<{{ settings.review_name_tag }} class="bdt-ep-review-card-name">{{{ settings.reviewer_name }}}</{{ settings.review_name_tag }}>
							<# } #>
							<# if ( settings.show_reviewer_job_title === 'yes' && settings.reviewer_job_title ) { #>
							<div class="bdt-ep-review-card-job-title">{{{ settings.reviewer_job_title }}}</div>
							<# } #>
							<# if ( settings.show_rating === 'yes' && ratingPosition === 'before' ) { #>
							{{{ renderRating() }}}
							<# } #>
						</div>
					</div>
					<# } #>

					<# if ( imageInline !== 'yes' ) { #>
					<# if ( settings.show_reviewer_name === 'yes' && settings.reviewer_name ) { #>
					<{{ settings.review_name_tag }} class="bdt-ep-review-card-name">{{{ settings.reviewer_name }}}</{{ settings.review_name_tag }}>
					<# } #>
					<# if ( settings.show_reviewer_job_title === 'yes' && settings.reviewer_job_title ) { #>
					<div class="bdt-ep-review-card-job-title">{{{ settings.reviewer_job_title }}}</div>
					<# } #>
					<# if ( settings.show_rating === 'yes' && ratingPosition === 'before' ) { #>
					{{{ renderRating() }}}
					<# } #>
					<# } #>

					<# if ( settings.show_review_text === 'yes' && settings.review_text ) { #>
					<div class="bdt-ep-review-card-text<# if ( settings.review_words_length ) { #> bdt-ep-read-more-text<# } #>"<# if ( settings.review_words_length ) { #> data-read-more="<# print( _.escape( readMoreData ) ); #>"<# } #>>{{{ settings.review_text }}}</div>
					<# } #>

					<# if ( settings.show_rating === 'yes' && ratingPosition === 'after' ) { #>
					{{{ renderRating() }}}
					<# } #>
				</div>
			</div>
		</div>
		<?php
	}
}
