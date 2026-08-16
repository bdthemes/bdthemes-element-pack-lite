/**
 * Start cookie consent widget script
 */

(() => {
    'use strict';

    const widgetCookieConsent = (scope) => {
        const scopeElement = scope?.jquery ? scope[0] : scope;

        const cookieConsentEl = scopeElement.querySelector('.bdt-cookie-consent');
        if (!cookieConsentEl) return;

        const editMode = Boolean(elementorFrontend.isEditMode());
        if (editMode) return;

        const parseData = (key) => {
            const raw = cookieConsentEl.dataset[key];
            if (!raw) return undefined;
            try {
                return typeof raw === 'string' ? JSON.parse(raw) : raw;
            } catch (e) {
                console.error(`Failed to parse cookie consent data-${key}:`, e);
                return undefined;
            }
        };

        const settings     = parseData('settings');
        const gtagSettings = parseData('gtag');

        window.cookieconsent.initialise(settings);

        const compliance = document.querySelector('.cc-compliance');
        const denyBtn    = document.createElement('button');
        denyBtn.className = 'btn-denyCookie bdt-cc-close-btn cc-btn cc-dismiss';
        denyBtn.innerHTML = `<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
        </svg>`;
        compliance?.appendChild(denyBtn);

        denyBtn.addEventListener('click', () => {
            cookieConsentEl.style.display = 'none';
            document.cookie = `element_pack_cookie_widget_gtag=denied; max-age=${60 * 60 * 24 * 7}; path=/`;
        });

        if (document.cookie.includes('element_pack_cookie_widget_gtag=denied')) {
            cookieConsentEl.style.display = 'none';
            return;
        }

        if (!gtagSettings?.gtag_enabled) return;

        const updateGtagConsent = (args) => gtag('consent', 'update', args);

        const gtagConsentObj = {
            ad_user_data       : gtagSettings.ad_user_data,
            ad_personalization : gtagSettings.ad_personalization,
            ad_storage         : gtagSettings.ad_storage,
            analytics_storage  : gtagSettings.analytics_storage,
        };

        document.querySelector('.cc-btn.cc-dismiss')?.addEventListener('click', () => {
            updateGtagConsent(gtagConsentObj);
        });

        denyBtn.addEventListener('click', () => {
            updateGtagConsent({
                ad_storage        : 'denied',
                analytics_storage : 'denied'
            });
        });
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (window.elementorFrontend?.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-cookie-consent.default', widgetCookieConsent);
        }
    });

})();

/**
 * End cookie consent widget script
 */
