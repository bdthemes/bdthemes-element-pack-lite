/**
 * Start dual button widget script
 */

(() => {
    'use strict';

    const widgetDualButton = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const buttons = scopeEl.querySelectorAll('.bdt-dual-button .bdt-ep-button[data-onclick]');
        if (!buttons.length) return;

        buttons.forEach(btn => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();

                const functionName = btn.dataset.onclick?.trim().replace(/[\(\);\s]/g, '');
                if (!functionName) return;

                if (typeof window[functionName] === 'function') {
                    window[functionName]();
                } else {
                    console.warn(`Function "${functionName}" is not defined.`);
                }
            });
        });
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-dual-button.default', widgetDualButton);
    });

})();

/**
 * End dual button widget script
 */
