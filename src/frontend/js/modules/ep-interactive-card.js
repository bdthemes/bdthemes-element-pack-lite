/**
 * Start interactive card widget script
 */

(function () {
    'use strict';

    const widgetInteractiveCard = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;
        const iCardMain = scopeEl.querySelector('.bdt-interactive-card');
        if (!iCardMain) return;

        let settings = {};
        try {
            const raw = iCardMain.dataset.settings;
            settings = raw ? JSON.parse(raw) : {};
        } catch (_) {}

        if (!settings.id) return;

        const waveEl = document.getElementById(settings.id);
        if (!waveEl || typeof wavify !== 'function') return;

        wavify(waveEl, {
            height: 60,
            bones: settings.wave_bones ?? 3,
            amplitude: settings.wave_amplitude ?? 40,
            speed: settings.wave_speed ?? 0.25
        });

        setTimeout(() => {
            iCardMain.classList.add('bdt-wavify-active');
        }, 1000);
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-interactive-card.default', widgetInteractiveCard);
    });
})();

/**
 * End interactive card widget script
 */
