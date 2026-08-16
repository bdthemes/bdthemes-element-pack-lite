/**
 * Start logo grid widget script
 */

(function () {
    'use strict';

    const widgetLogoGrid = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;
        const logoGrid = scopeEl.querySelector('.bdt-logo-grid-wrapper');
        if (!logoGrid) return;

        const tooltips = logoGrid.querySelectorAll(':scope > .bdt-tippy-tooltip');
        const widgetID = scopeEl.dataset.id || '';

        tooltips.forEach((el) => {
            tippy(el, {
                allowHTML: true,
                theme: 'bdt-tippy-' + widgetID
            });
        });
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-logo-grid.default', widgetLogoGrid);
    });
})();

/**
 * End logo grid widget script
 */
