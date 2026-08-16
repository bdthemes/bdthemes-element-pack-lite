/**
 * Start scroll button widget script
 */

( function( $, elementor ) {

	'use strict';

	const widgetScrollButton = function( $scope, $ ) {

			const $scrollButton = $scope.find('.bdt-scroll-button');

	    if ( ! $scrollButton.length ) {
	    	return;
	    }

			const $selector = $scrollButton.data('selector'),
			$settings = $scrollButton.data('settings');

	    if ($settings.HideOnBeforeScrolling == true) {

			$(window).scroll(function() {
			  if ($(window).scrollTop() > 300) {
			    $scrollButton.css("opacity", "1");
			  } else {
			    $scrollButton.css("opacity", "0");
			  }
			});
	    }

	    $($scrollButton).on('click', function(event){
	    	event.preventDefault();
	    	bdtUIkit.scroll($scrollButton, $settings ).scrollTo($($selector));

	    });

	};

	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-scroll-button.default', widgetScrollButton );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End scroll button widget script
 */
