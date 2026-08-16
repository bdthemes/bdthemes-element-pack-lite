/**
 * Start slider widget script
 */

( function( $, elementor ) {

	'use strict';

	const widgetSlider = function( $scope, $ ) {

		const $slider = $scope.find( '.bdt-slider' );

        if ( ! $slider.length ) {
            return;
        }

        const $sliderContainer = $slider.find('.swiper-carousel'),
			$settings = $slider.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();

        async function initSwiper() {

			await new Swiper($sliderContainer, $settings);

			if ($settings.pauseOnHover) {
				 $($sliderContainer).hover(function() {
					(this).swiper.autoplay.stop();
				}, function() {
					(this).swiper.autoplay.start();
				});
			}
		};

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-slider.default', widgetSlider );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-acf-slider.default', widgetSlider );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End slider widget script
 */
