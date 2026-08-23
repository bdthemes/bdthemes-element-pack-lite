/**
 * Start image stack widget script
 */

(function () {
    'use strict';

    const widgetImageStack = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;
        const imageStack = scopeEl.querySelector('.bdt-image-stack');
        if (!imageStack) return;

        const tooltips = imageStack.querySelectorAll('.bdt-tippy-tooltip');
        const widgetID = scopeEl.dataset.id || '';

        tooltips.forEach((el) => {
            tippy(el, {
                allowHTML: false,
                theme: 'bdt-tippy-' + widgetID
            });
        });
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-image-stack.default', widgetImageStack);
    });
})();

/**
 * End image stack widget script
 */
