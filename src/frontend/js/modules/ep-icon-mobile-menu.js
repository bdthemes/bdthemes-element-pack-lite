/**
 * Start icon mobile menu widget script
 */

(function () {
    'use strict';

    const widgetIconMobileMenu = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;
        const marker = scopeEl.querySelector('.bdt-icon-mobile-menu-wrap');
        if (!marker) return;

        const tooltips = marker.querySelectorAll('ul > li > .bdt-tippy-tooltip');
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
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-icon-mobile-menu.default', widgetIconMobileMenu);
    });
})();

/**
 * End icon mobile menu widget script
 */
