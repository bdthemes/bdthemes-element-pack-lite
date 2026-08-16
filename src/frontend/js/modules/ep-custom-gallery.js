/**
 * Start bdt custom gallery widget script
 */

(() => {
    'use strict';

    const widgetCustomGallery = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const customGalleryEl = scopeEl.querySelector('.bdt-custom-gallery');
        if (!customGalleryEl) return;

        const settings = JSON.parse(customGalleryEl.dataset.settings || '{}');

        if (settings.tiltShow === true) {
            const elements = document.querySelectorAll(settings.id + ' [data-tilt]');
            VanillaTilt.init(elements);
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-custom-gallery.default',    widgetCustomGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-custom-gallery.bdt-abetis', widgetCustomGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-custom-gallery.bdt-fedara', widgetCustomGallery);
    });

})();

/**
 * End bdt custom gallery widget script
 */
