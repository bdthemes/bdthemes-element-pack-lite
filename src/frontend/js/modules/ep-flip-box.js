/**
 * Start Flip Box widget script
 */

(() => {
    'use strict';

    const widgetFlipBox = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const flipBoxes = scopeEl.querySelectorAll('.bdt-flip-box');
        if (!flipBoxes.length) return;

        const firstBox = flipBoxes[0];
        const rawSettings = firstBox.dataset.settings;
        const settings    = rawSettings ? JSON.parse(rawSettings) : {};
        if (!settings) return;

        const trigger = settings.flipTrigger;

        flipBoxes.forEach(boxEl => {
            if (trigger === 'click') {
                boxEl.addEventListener('click', () => boxEl.classList.toggle('bdt-active'));
            } else if (trigger === 'hover') {
                boxEl.addEventListener('mouseenter', () => boxEl.classList.add('bdt-active'));
                boxEl.addEventListener('mouseleave', () => boxEl.classList.remove('bdt-active'));
            }
        });
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-flip-box.default', widgetFlipBox);
    });

})();

/**
 * End Flip Box widget script
 */
