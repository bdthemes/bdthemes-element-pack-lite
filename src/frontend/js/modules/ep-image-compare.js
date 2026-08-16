/**
 * Start image compare widget script
 */

(function () {
    'use strict';

    const sanitizeHTML = (str) => {
        if (typeof str !== 'string') return '';
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    const widgetImageCompare = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;
        const imageCompareEls = scopeEl.querySelectorAll('.image-compare');
        if (!imageCompareEls.length) return;

        const firstEl = imageCompareEls[0];
        let settings = {};
        try {
            const raw = firstEl.dataset.settings;
            settings = raw ? JSON.parse(raw) : {};
        } catch (_) {}

        const options = {
            controlColor: settings.bar_color,
            controlShadow: settings.add_circle_shadow,
            addCircle: settings.add_circle,
            addCircleBlur: settings.add_circle_blur,
            showLabels: settings.no_overlay,
            labelOptions: {
                before: sanitizeHTML(settings.before_label || ''),
                after: sanitizeHTML(settings.after_label || ''),
                onHover: settings.on_hover
            },
            smoothing: settings.smoothing,
            smoothingAmount: settings.smoothing_amount ?? 0,
            hoverStart: settings.move_slider_on_hover,
            verticalMode: settings.orientation,
            startingPoint: settings.default_offset_pct,
            fluidMode: false
        };

        imageCompareEls.forEach((element) => {
            new ImageCompare(element, options).mount();
        });
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-image-compare.default', widgetImageCompare);
    });
})();

/**
 * End image compare widget script
 */
