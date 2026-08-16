/**
 * Start post grid tab widget script
 */

;
(function ($, elementor) {

	'use strict';

	const widgetStaticPostTab = function ($scope, $) {

		const $postGridTab = $scope.find('.bdt-static-grid-tab');

		if (!$postGridTab.length) {
			return;
		}

		const gridTab = $postGridTab.find('.gridtab');
		const $settings = $postGridTab.data('settings');

		$(gridTab).gridtab($settings);

	};


	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-static-grid-tab.default', widgetStaticPostTab);
	});

}(jQuery, window.elementorFrontend));

/**
 * End post grid tab widget script
 */
