/**
 * Start scrollnav widget script
 */

( function( $, elementor ) {

	'use strict';

	const widgetScrollNav = function( $scope, $ ) {

		const $scrollnav = $scope.find( '.bdt-dotnav > li' );

        if ( ! $scrollnav.length ) {
            return;
        }

		const $tooltip = $scrollnav.find('> .bdt-tippy-tooltip'),
			widgetID = $scope.data('id');

		$tooltip.each( function() {
			tippy( this, {
				allowHTML: true,
				theme: 'bdt-tippy-' + widgetID
			});
		});

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-scrollnav.default', widgetScrollNav );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End scrollnav widget script
 */
