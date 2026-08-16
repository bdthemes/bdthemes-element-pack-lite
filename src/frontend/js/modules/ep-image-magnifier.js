/**
 * Start image magnifier widget script
 */

(function () {
    'use strict';

    const widgetImageMagnifier = (scope) => {
        const scopeEl = scope instanceof jQuery ? scope[0] : scope;
        const imageMagnifier = scopeEl.querySelector('.bdt-image-magnifier');
        if (!imageMagnifier) return;

        const magnifier = imageMagnifier.querySelector(':scope > .bdt-image-magnifier-image');
        if (!magnifier) return;

        let settings = {};
        try {
            const raw = imageMagnifier.dataset.settings;
            settings = raw ? JSON.parse(raw) : {};
        } catch (_) {}

        // ImageZoom is a jQuery plugin - requires jQuery
        jQuery(magnifier).ImageZoom(settings);
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-image-magnifier.default', widgetImageMagnifier);
    });
})();

/**
 * End image magnifier widget script
 */
