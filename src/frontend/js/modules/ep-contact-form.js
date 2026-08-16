/**
 * Start contact form widget script
 */

(() => {
    'use strict';

    const getNotificationZIndex = (formEl) => {
        const holder = formEl && formEl.closest('[data-bdt-notification-z-index]');
        const raw    = holder && holder.dataset ? holder.dataset.bdtNotificationZIndex : '';
        return raw !== undefined && raw !== '' ? raw : null;
    };

    const applyNotificationZIndex = (notificationInstance, zIndexRaw) => {
        if (notificationInstance == null || zIndexRaw === null) {
            return;
        }
        const z = Number(zIndexRaw);
        if (!Number.isFinite(z)) {
            return;
        }
        const containerEl = notificationInstance.$el && notificationInstance.$el.parentElement;
        if (containerEl) {
            containerEl.style.zIndex = String(z);
        }
    };

    const sendContactForm = async (formEl, widgetID = false) => {
        const langStr = window.ElementPackConfig.contact_form;
        const zIndex  = getNotificationZIndex(formEl);

        const loadingNote = bdtUIkit.notification({
            message : `<div bdt-spinner></div> ${langStr.sending_msg}`,
            timeout : false,
            status  : 'primary'
        });
        applyNotificationZIndex(loadingNote, zIndex);

        try {
            const response = await fetch(formEl.getAttribute('action'), {
                method  : 'POST',
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
                body    : new URLSearchParams(new FormData(formEl)).toString()
            });

            const data = await response.text();

            const doc        = new DOMParser().parseFromString(data, 'text/html');
            const responseEl = doc.body.firstElementChild;

            const redirectURL = responseEl?.dataset.redirect;
            const isExternal  = responseEl?.dataset.external;
            const resetStatus = responseEl?.dataset.resetstatus;

            bdtUIkit.notification.closeAll();

            const notification = bdtUIkit.notification({
                message: `<div class="bdt-contact-form-success-message-${widgetID}">${data}</div>`
            });
            applyNotificationZIndex(notification, zIndex);

            if (redirectURL && redirectURL !== 'no') {
                bdtUIkit.util.on(document, 'close', (evt) => {
                    if (evt.detail[0] === notification) {
                        window.open(redirectURL, isExternal);
                    }
                });
            }

            localStorage.setItem('bdtCouponCode', formEl.id);

            if (resetStatus && resetStatus !== 'no') {
                formEl.reset();
            }

        } catch (e) {
            console.error('Contact form submission error:', e);
        }
    };

    const elementPackGIC = () => {
        const langStr = window.ElementPackConfig.contact_form;

        return new Promise((resolve, reject) => {

            if (typeof grecaptcha === 'undefined') {
                bdtUIkit.notification({
                    message : `<div bdt-spinner></div> ${langStr.captcha_nd}`,
                    timeout : false,
                    status  : 'warning'
                });
                return reject();
            }

            const response = grecaptcha.getResponse();

            if (!response) {
                bdtUIkit.notification({
                    message : `<div bdt-spinner></div> ${langStr.captcha_nr}`,
                    timeout : false,
                    status  : 'warning'
                });
                return reject();
            }

            const recaptchaTextarea = Array.from(
                document.querySelectorAll('textarea.g-recaptcha-response')
            ).find(el => el.value === response);

            const formEl = recaptchaTextarea?.closest('form.bdt-contact-form-form');
            const action = formEl?.getAttribute('action');

            if (action && action !== '') {
                sendContactForm(formEl);
            }

            grecaptcha.reset();
        });
    };

    window.elementPackGICCB = elementPackGIC;

    const widgetSimpleContactForm = (scope) => {
        const scopeElement = scope?.jquery ? scope[0] : scope;

        const widgetID = scopeElement.dataset.id;

        // Tel input validation — applies regardless of form variant
        scopeElement.querySelectorAll('.bdt-contact-form input[type="tel"]').forEach(input => {
            input.addEventListener('input', () => {
                input.value = input.value.replace(/[^0-9+]/g, '');
            });
        });

        const formEl = scopeElement.querySelector('.bdt-contact-form .without-recaptcha');
        if (!formEl) return;

        formEl.addEventListener('submit', (e) => {
            e.preventDefault();
            e.stopPropagation();
            sendContactForm(formEl, widgetID);
        });
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (window.elementorFrontend?.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-contact-form.default', widgetSimpleContactForm);
        }
    });

})();

/**
 * End contact form widget script
 */
