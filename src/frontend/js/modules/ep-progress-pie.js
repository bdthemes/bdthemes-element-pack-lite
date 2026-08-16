/**
 * Start progress pie widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetProgressPie = function ($scope, $) {

        const $progressPie = $scope.find('.bdt-progress-pie');

        if (!$progressPie.length) {
            return;
        }

        epObserveTarget($scope[0], function () {
            $progressPie.asPieProgress({
                namespace: 'pieProgress',
                classes: {
                    svg: 'bdt-progress-pie-svg',
                    number: 'bdt-progress-pie-number',
                    content: 'bdt-progress-pie-content'
                }
            });

            $progressPie.asPieProgress('start');

        }, {
            root: null,
            rootMargin: '0px',
            threshold: 1
        });

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-progress-pie.default', widgetProgressPie);
    });

}(jQuery, window.elementorFrontend));

/**
 * End progress pie widget script
 */

