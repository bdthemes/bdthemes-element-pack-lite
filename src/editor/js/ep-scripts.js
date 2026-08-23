/**
 * Start accordion widget script
 */

(() => {
  "use strict";

  const isMobileDevice = () => window.matchMedia("(max-width: 767px)").matches;

  const smoothScrollTo = (element, offset = 0, duration = 1000) =>
    new Promise((resolve) => {
      const startPosition = window.scrollY;
      const distance = element.getBoundingClientRect().top - offset;
      let startTime = null;

      const animation = (currentTime) => {
        if (startTime === null) startTime = currentTime;
        const timeElapsed = currentTime - startTime;
        const progress = Math.min(timeElapsed / duration, 1);
        const ease = progress < 0.5
          ? 2 * progress * progress
          : 1 - Math.pow(-2 * progress + 2, 2) / 2;

        window.scrollTo(0, startPosition + distance * ease);

        if (timeElapsed < duration) {
          requestAnimationFrame(animation);
        } else {
          resolve();
        }
      };

      requestAnimationFrame(animation);
    });

  const handleHash = async (accordion, settings, scrollTime, animate = true) => {
    const hash = window.location.hash;
    if (!hash) return;

    const targetElement = accordion.querySelector(`[data-title="${hash.substring(1)}"]`);
    if (!targetElement) return;

    const accordionIndex = targetElement.dataset.accordionIndex;
    const accordionContainer = targetElement.closest(".bdt-ep-accordion");
    if (!accordionIndex || !accordionContainer) return;

    const accordionId = accordionContainer.id;
    const bdtAccordion = window.bdtUIkit?.accordion(accordion);
    if (!bdtAccordion) return;

    const index = parseInt(accordionIndex, 10);

    if (settings.activeScrollspy === "yes" && accordionId) {
      const targetContainer = document.getElementById(accordionId);
      if (targetContainer) {
        await smoothScrollTo(targetContainer, settings.hashTopOffset, scrollTime);
      }
      bdtAccordion.toggle(index, false);
    } else {
      bdtAccordion.toggle(index, animate);
    }
  };

  const widgetAccordion = (scope) => {
    const scopeElement = scope?.jquery ? scope[0] : scope;

    const accrContainer = scopeElement.querySelector(".bdt-ep-accordion-container");
    if (!accrContainer) return;

    const accordion = accrContainer.querySelector(".bdt-ep-accordion");
    if (!accordion) return;

    const settingsData = accordion.dataset.settings;
    if (!settingsData) return;

    let settings;
    try {
      settings = typeof settingsData === "string" ? JSON.parse(settingsData) : settingsData;
    } catch (e) {
      console.error("Failed to parse accordion settings:", e);
      return;
    }

    const {
      activeHash = "no",
      hashScrollspyTime = 1000,
      closeAllItemsOnMobile = false
    } = settings;

    if (closeAllItemsOnMobile && isMobileDevice()) {
      accrContainer.querySelectorAll(".bdt-ep-accordion-item.bdt-open").forEach((item) => {
        item.classList.remove("bdt-open");
        const content = item.querySelector(".bdt-ep-accordion-content");
        if (content) content.hidden = true;
      });
    }

    if (activeHash === "yes") {
      const abortController = new AbortController();
      const signal = abortController.signal;

      const handleLoad = () => handleHash(accordion, settings, hashScrollspyTime, false);

      const handleTitleClick = (event) => {
        const title = event.currentTarget.dataset.title;
        if (title) {
          window.location.hash = title.trim();
          handleHash(accordion, settings, 1000);
        }
      };

      const handleHashChange = () => handleHash(accordion, settings, 1000);

      if (document.readyState === "complete") {
        handleLoad();
      } else {
        window.addEventListener("load", handleLoad, { signal, once: true });
      }

      window.addEventListener("hashchange", handleHashChange, { signal });

      accordion.querySelectorAll(".bdt-ep-accordion-title").forEach((title) => {
        title.addEventListener("click", handleTitleClick, { signal });
      });

      accordion._cleanupAccordion = () => abortController.abort();
    }
  };

  window.addEventListener("elementor/frontend/init", () => {
    if (window.elementorFrontend?.hooks) {
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/bdt-accordion.default",
        widgetAccordion
      );
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/bdt-acf-accordion.default",
        widgetAccordion
      );
    }
  });
})();

/**
 * End accordion widget script
 */

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

/**
 * Start business hours widget script
 * Optimized version - Minimal jQuery (required for jclock)
 */

(() => {
  "use strict";

  const widgetBusinessHours = (scope) => {
    const scopeElement = scope instanceof jQuery ? scope[0] : scope;

    const businessHoursContainer = scopeElement.querySelector(".bdt-ep-business-hours");
    if (!businessHoursContainer) return;

    const currentTimeElement = businessHoursContainer.querySelector(
      ".bdt-ep-business-hours-current-time"
    );
    if (!currentTimeElement) return;

    const settingsData = businessHoursContainer.dataset.settings;
    if (!settingsData) return;

    let settings;
    try {
      settings = typeof settingsData === "string" ? JSON.parse(settingsData) : settingsData;
    } catch (e) {
      console.error("Failed to parse business hours settings:", e);
      return;
    }

    const { business_hour_style, timeNotation, dynamic_timezone, dynamic_timezone_default } = settings;

    if (business_hour_style !== "dynamic") return;

    if (typeof jQuery === "undefined" || !jQuery.fn.jclock) {
      console.error("jclock library is not loaded");
      return;
    }

    const offsetVal =
      business_hour_style === "static" ? dynamic_timezone_default : dynamic_timezone;

    if (!offsetVal) {
      console.warn("Timezone offset is not set");
      return;
    }

    const timeFormat = timeNotation === "12h" ? "%I:%M:%S %p" : "%H:%M:%S";

    const options = {
      format: timeFormat,
      timeNotation: timeNotation,
      am_pm: true,
      utc: true,
      utc_offset: offsetVal,
    };

    jQuery(currentTimeElement).jclock(options);
  };

  window.addEventListener("elementor/frontend/init", () => {
    if (window.elementorFrontend?.hooks) {
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/bdt-business-hours.default",
        widgetBusinessHours
      );
    }
  });
})();

/**
 * End business hours widget script
 */

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

/**
 * Start countdown widget script
 */

(() => {
    'use strict';

    const setCookie = (name, value, hours) => {
        let expires = '';
        if (hours) {
            const date = new Date();
            date.setTime(date.getTime() + hours * 60 * 60 * 1000);
            expires = `; expires=${date.toUTCString()}`;
        }
        document.cookie = `${name}=${value ?? ''}${expires}; path=/`;
    };

    const getCookie = (name) => {
        const match = document.cookie
            .split(';')
            .find(c => c.trimStart().startsWith(name + '='));
        return match ? match.trimStart().slice(name.length + 1) : null;
    };

    const randomInRange = (min, max) => Math.floor(Math.random() * (max - min + 1) + min);

    const getTimeSpan = (date) => {
        const total = date - Date.now();
        return {
            total,
            seconds : Math.floor(total / 1000 % 60),
        };
    };

    const handleCountdownEnd = async (settings, endTime) => {
        try {
            const body = new URLSearchParams({
                action         : 'element_pack_countdown_end',
                endTime,
                couponTrickyId : settings.couponTrickyId
            });

            const response = await fetch(settings.adminAjaxUrl, { method: 'POST', body });
            const data     = await response.text();

            if (data !== 'ended') return;

            if (settings.endActionType === 'message') {
                document.querySelector(settings.msgId)?.style.setProperty('display', 'block');
                document.querySelector(`${settings.id}-timer`)?.style.setProperty('display', 'none');
            }

            if (settings.endActionType === 'url' && settings.redirectUrl?.includes('http')) {
                setTimeout(() => { window.location.href = settings.redirectUrl; }, settings.redirectDelay);
            }

            if (settings.triggerId) {
                setTimeout(() => {
                    document.getElementById(settings.triggerId)?.click();
                }, 1500);
            }

        } catch (e) {
            console.error('Countdown end action failed:', e);
        }
    };

    const widgetCountdown = (scope) => {
        const scopeElement = scope?.jquery ? scope[0] : scope;

        const countdownWrapper = scopeElement.querySelector('.bdt-countdown-wrapper');
        if (!countdownWrapper) return;

        const settingsData = countdownWrapper.dataset.settings;
        if (!settingsData) return;

        let settings;
        try {
            settings = typeof settingsData === 'string' ? JSON.parse(settingsData) : settingsData;
        } catch (e) {
            console.error('Failed to parse countdown settings:', e);
            return;
        }

        const { endTime, loopHours, isLogged } = settings;
        const isEditMode = document.body.classList.contains('elementor-editor-active');

        if (!loopHours) {
            const timerEl  = document.querySelector(`${settings.id}-timer`);
            const countdown = bdtUIkit.countdown(timerEl, { date: settings.finalTime });

            const interval = setInterval(() => {
                const { seconds } = getTimeSpan(countdown.date);

                if (seconds < 0) {
                    clearInterval(interval);

                    if (!isEditMode) {
                        document.querySelector(`${settings.id}-msg`)?.style.setProperty('display', 'none');

                        if (settings.endActionType !== 'none' || settings.triggerId) {
                            handleCountdownEnd(settings, endTime);
                        }
                    }
                }
            }, 1000);
        }

        if (loopHours) {
            const randMinute        = randomInRange(6, 14);
            const hours             = loopHours * 60 * 60 * 1000 - randMinute * 60 * 1000;
            const loopTime          = new Date(Date.now() + hours).toISOString();
            const cookieLoopTime    = getCookie('bdtCountdownLoopTime');
            const cookieIsEmpty     = cookieLoopTime === null || cookieLoopTime === 'undefined';

            if (cookieIsEmpty && isLogged === false) {
                setCookie('bdtCountdownLoopTime', loopTime, loopHours);
            }

            const setLoopTimer = isLogged !== false ? loopTime : getCookie('bdtCountdownLoopTime');

            const timerEl = document.querySelector(`${settings.id}-timer`);
            timerEl?.setAttribute('data-bdt-countdown', `date: ${setLoopTimer}`);

            const countdown     = bdtUIkit.countdown(timerEl, { date: setLoopTimer });
            const countdownDate = countdown.date;

            setInterval(() => {
                const { seconds } = getTimeSpan(countdownDate);

                if (seconds > 0 && cookieIsEmpty && isLogged === false) {
                    setCookie('bdtCountdownLoopTime', loopTime, loopHours);
                    bdtUIkit.countdown(timerEl, { date: setLoopTimer });
                }
            }, 1000);
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (window.elementorFrontend?.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-countdown.default',          widgetCountdown);
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-countdown.bdt-tiny-countdown', widgetCountdown);
        }
    });

})();

/**
 * End countdown widget script
 */

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

(() => {
    'use strict';

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        const ModuleHandler = elementorModules.frontend.handlers.Base;

        const FloatingEffect = ModuleHandler.extend({

            bindEvents() {
                this.run();
            },

            getDefaultSettings() {
                return {
                    direction: 'alternate',
                    easing: 'easeInOutSine',
                    loop: true,
                };
            },

            settings(key) {
                return this.getElementSettings('ep_floating_effects_' + key);
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_floating') !== -1) {
                    this.anime && this.anime.restart();
                    this.run();
                }
            }, 400),

            run() {
                const options = this.getDefaultSettings();
                const element = this.$element[0];

                if (this.settings('translate_toggle')) {
                    if (this.settings('translate_x.sizes.from').length !== 0 || this.settings('translate_x.sizes.to').length !== 0) {
                        options.translateX = {
                            value: [this.settings('translate_x.sizes.from') || 0, this.settings('translate_x.sizes.to') || 0],
                            duration: this.settings('translate_duration.size'),
                            delay: this.settings('translate_delay.size') || 0,
                        };
                    }

                    if (this.settings('translate_y.sizes.from').length !== 0 || this.settings('translate_y.sizes.to').length !== 0) {
                        options.translateY = {
                            value: [this.settings('translate_y.sizes.from') || 0, this.settings('translate_y.sizes.to') || 0],
                            duration: this.settings('translate_duration.size'),
                            delay: this.settings('translate_delay.size') || 0,
                        };
                    }
                }

                if (this.settings('rotate_toggle')) {
                    if (this.settings('rotate_infinite') !== 'yes') {
                        if (this.settings('rotate_x.sizes.from').length !== 0 || this.settings('rotate_x.sizes.to').length !== 0) {
                            options.rotateX = {
                                value: [this.settings('rotate_x.sizes.from') || 0, this.settings('rotate_x.sizes.to') || 0],
                                duration: this.settings('rotate_duration.size'),
                                delay: this.settings('rotate_delay.size') || 0,
                            };
                        }
                        if (this.settings('rotate_y.sizes.from').length !== 0 || this.settings('rotate_y.sizes.to').length !== 0) {
                            options.rotateY = {
                                value: [this.settings('rotate_y.sizes.from') || 0, this.settings('rotate_y.sizes.to') || 0],
                                duration: this.settings('rotate_duration.size'),
                                delay: this.settings('rotate_delay.size') || 0,
                            };
                        }
                        if (this.settings('rotate_z.sizes.from').length !== 0 || this.settings('rotate_z.sizes.to').length !== 0) {
                            options.rotateZ = {
                                value: [this.settings('rotate_z.sizes.from') || 0, this.settings('rotate_z.sizes.to') || 0],
                                duration: this.settings('rotate_duration.size'),
                                delay: this.settings('rotate_delay.size') || 0,
                            };
                        }
                    }
                }

                if (this.settings('scale_toggle')) {
                    if (this.settings('scale_x.sizes.from').length !== 0 || this.settings('scale_x.sizes.to').length !== 0) {
                        options.scaleX = {
                            value: [this.settings('scale_x.sizes.from') || 0, this.settings('scale_x.sizes.to') || 0],
                            duration: this.settings('scale_duration.size'),
                            delay: this.settings('scale_delay.size') || 0,
                        };
                    }
                    if (this.settings('scale_y.sizes.from').length !== 0 || this.settings('scale_y.sizes.to').length !== 0) {
                        options.scaleY = {
                            value: [this.settings('scale_y.sizes.from') || 0, this.settings('scale_y.sizes.to') || 0],
                            duration: this.settings('scale_duration.size'),
                            delay: this.settings('scale_delay.size') || 0,
                        };
                    }
                }

                if (this.settings('skew_toggle')) {
                    if (this.settings('skew_x.sizes.from').length !== 0 || this.settings('skew_x.sizes.to').length !== 0) {
                        options.skewX = {
                            value: [this.settings('skew_x.sizes.from') || 0, this.settings('skew_x.sizes.to') || 0],
                            duration: this.settings('skew_duration.size'),
                            delay: this.settings('skew_delay.size') || 0,
                        };
                    }
                    if (this.settings('skew_y.sizes.from').length !== 0 || this.settings('skew_y.sizes.to').length !== 0) {
                        options.skewY = {
                            value: [this.settings('skew_y.sizes.from') || 0, this.settings('skew_y.sizes.to') || 0],
                            duration: this.settings('skew_duration.size'),
                            delay: this.settings('skew_delay.size') || 0,
                        };
                    }
                }

                if (this.settings('border_radius_toggle')) {
                    element.style.overflow = 'hidden';
                    if (this.settings('border_radius.sizes.from').length !== 0 || this.settings('border_radius.sizes.to').length !== 0) {
                        options.borderRadius = {
                            value: [this.settings('border_radius.sizes.from') || 0, this.settings('border_radius.sizes.to') || 0],
                            duration: this.settings('border_radius_duration.size'),
                            delay: this.settings('border_radius_delay.size') || 0,
                        };
                    }
                }

                if (this.settings('opacity_toggle')) {
                    if (this.settings('opacity_start.size').length !== 0 || this.settings('opacity_end.size').length !== 0) {
                        options.opacity = {
                            value: [this.settings('opacity_start.size') || 1, this.settings('opacity_end.size') || 0],
                            duration: this.settings('opacity_duration.size'),
                            easing: 'linear',
                        };
                    }
                }

                if (this.settings('easing')) {
                    options.easing = this.settings('easing');
                }

                if (this.settings('show')) {
                    options.targets = element;
                    if (
                        this.settings('translate_toggle') ||
                        this.settings('rotate_toggle') ||
                        this.settings('scale_toggle') ||
                        this.settings('skew_toggle') ||
                        this.settings('border_radius_toggle') ||
                        this.settings('opacity_toggle')
                    ) {
                        this.anime = window.anime && window.anime(options);
                    }
                }
            },
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', ($scope) => {
            elementorFrontend.elementsHandler.addHandler(FloatingEffect, { $element: $scope });
        });
    });

})();

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

/**
 * Start image accordion widget script
 */

(function () {
    'use strict';

    const setActive = (item, siblings) => {
        siblings.forEach((sib) => sib.classList.remove('active'));
        item.classList.add('active');
    };

    const getSiblings = (el) => {
        const parent = el.parentElement;
        return parent ? [...parent.children].filter((c) => c !== el) : [];
    };

    const widgetImageAccordion = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;
        const imageAccordion = scopeEl.querySelector('.bdt-ep-image-accordion');
        if (!imageAccordion) return;

        let settings = {};
        try {
            const raw = imageAccordion.dataset.settings;
            settings = raw ? JSON.parse(raw) : {};
        } catch (_) {}

        const accordionItems = imageAccordion.querySelectorAll('.bdt-ep-image-accordion-item');
        const totalItems = accordionItems.length;

        accordionItems.forEach((item) => item.setAttribute('tabindex', '0'));

        if (settings.activeItem === true && settings.activeItemNumber <= totalItems) {
            accordionItems.forEach((item) => item.classList.remove('active'));
            const activeIndex = settings.activeItemNumber - 1;
            if (accordionItems[activeIndex]) accordionItems[activeIndex].classList.add('active');
        }

        const mouseEvent = settings.mouse_event || 'click';

        accordionItems.forEach((item) => {
            item.addEventListener(mouseEvent, function () {
                setActive(this, getSiblings(this));
            });

            item.addEventListener('focus', function () {
                setActive(this, getSiblings(this));
            });

            item.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    setActive(this, siblings(this));
                }
            });
        });

        if (settings.activeItem !== true) {
            document.body.addEventListener(mouseEvent, function (e) {
                if (imageAccordion.contains(e.target)) return;
                accordionItems.forEach((item) => item.classList.remove('active'));
            });
        }

        if (settings.swiping) {
            let touchstartX = 0;
            let touchendX = 0;

            accordionItems.forEach((item) => {
                item.addEventListener('touchstart', function (e) {
                    touchstartX = e.changedTouches[0]?.screenX ?? 0;
                });

                item.addEventListener('touchend', function (e) {
                    touchendX = e.changedTouches[0]?.screenX ?? 0;
                    const deltaX = touchendX - touchstartX;
                    const prev = item.previousElementSibling;
                    const next = item.nextElementSibling;

                    if (deltaX > 50 && prev) {
                        accordionItems.forEach((i) => i.classList.remove('active'));
                        prev.classList.add('active');
                    } else if (deltaX < -50 && next) {
                        accordionItems.forEach((i) => i.classList.remove('active'));
                        next.classList.add('active');
                    }
                });
            });
        }

        if (settings.inactiveItemOverlay) {
            accordionItems.forEach((item) => {
                item.addEventListener(mouseEvent, function (e) {
                    e.stopPropagation();
                    if (this.classList.contains('active')) {
                        this.classList.remove('bdt-inactive');
                        getSiblings(this).forEach((s) => s.classList.add('bdt-inactive'));
                    } else {
                        getSiblings(this).forEach((s) => s.classList.remove('bdt-inactive'));
                    }
                });
            });

            document.addEventListener(mouseEvent, function () {
                accordionItems.forEach((item) => item.classList.remove('bdt-inactive'));
            });
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-image-accordion.default', widgetImageAccordion);
    });
})();

/**
 * End image accordion widget script
 */

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

/**
 * Start logo grid widget script
 */

(function () {
    'use strict';

    const widgetLogoGrid = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;
        const logoGrid = scopeEl.querySelector('.bdt-logo-grid-wrapper');
        if (!logoGrid) return;

        const tooltips = logoGrid.querySelectorAll(':scope > .bdt-tippy-tooltip');
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
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-logo-grid.default', widgetLogoGrid);
    });
})();

/**
 * End logo grid widget script
 */

/**
 * Start open street map widget script
 */

( function( $, elementor ) {

	'use strict';

	// Build popup markup while stripping scripts and inline event handlers to prevent XSS.
	function createSafePopup( html ) {
		var popupContent = document.createElement('div');
		popupContent.innerHTML = html;

		popupContent.querySelectorAll('script').forEach(function (script) {
			script.remove();
		});

		popupContent.querySelectorAll('[onclick], [onload], [onerror], [onmouseover], [onmouseout]').forEach(function (el) {
			el.removeAttribute('onclick');
			el.removeAttribute('onload');
			el.removeAttribute('onerror');
			el.removeAttribute('onmouseover');
			el.removeAttribute('onmouseout');
		});

		return popupContent;
	}

	var widgetOpenStreetMap = function( $scope, $ ) {

		const $openStreetMap = $scope.find( '.bdt-open-street-map' ),
            settings       = $openStreetMap.data('settings'),
            markers        = $openStreetMap.data('map_markers');

        if ( ! $openStreetMap.length ) {
            return;
        }

        const avdOSMap = L.map($openStreetMap[0], {
                zoomControl: settings.zoomControl,
                scrollWheelZoom: false
            }).setView([
                    settings.lat,
                    settings.lng
                ], 
                settings.zoom
            );

        if (settings.mapboxToken !== '' && settings.mapboxToken !== false) {
          const tileSource = 'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + settings.mapboxToken;
            L.tileLayer( tileSource, {
                maxZoom: 18,
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery &copy; <a href="https://www.mapbox.com/">Mapbox</a>',
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1
            }).addTo(avdOSMap);
        } else {
            L.tileLayer( 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(avdOSMap);
        }


        for (var i in markers) { 
            if( (markers[i]['iconUrl']) != '' && typeof (markers[i]['iconUrl']) !== 'undefined'){ 
                const LeafIcon = L.Icon.extend({
                    options: {
                        iconSize   : [25, 41],
                        iconAnchor : [12, 41],
                        popupAnchor: [2, -41]
                    }
                });
                const greenIcon = new LeafIcon({iconUrl: markers[i]['iconUrl'] });
                L.marker( [markers[i]['lat'], markers[i]['lng']], {icon: greenIcon} ).bindPopup(createSafePopup(markers[i]['infoWindow'])).addTo(avdOSMap);
            } else {
                if( (markers[i]['lat']) != '' && typeof (markers[i]['lat']) !== 'undefined'){ 
                    L.marker( [markers[i]['lat'], markers[i]['lng']] ).bindPopup(createSafePopup(markers[i]['infoWindow'])).addTo(avdOSMap);
                }
            }
        }

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-open-street-map.default', widgetOpenStreetMap );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End open street map widget script
 */


/**
 * Start panel slider widget script
 */

(function ($, elementor) {

	'use strict';

	var widgetPanelSlider = function ($scope, $) {

		const $slider = $scope.find('.bdt-panel-slider');

		if (!$slider.length) {
			return;
		}

		const $sliderContainer = $slider.find('.swiper-carousel'),
			$settings = $slider.data('settings'),
			$widgetSettings = $slider.data('widget-settings');

		const Swiper = elementorFrontend.utils.swiper;
		initSwiper();
		async function initSwiper() {
			const swiper = await new Swiper($sliderContainer, $settings);

			if ($settings.pauseOnHover) {
				$($sliderContainer).hover(function () {
					(this).swiper.autoplay.stop();
				}, function () {
					(this).swiper.autoplay.start();
				});
			}
		};

		if ($widgetSettings.mouseInteractivity == true) {
			setTimeout(() => {
				const data = $($widgetSettings.id).find('.bdt-panel-slide-item');
				$(data).each((index, element) => {
					const scene = $(element).get(0);
					new Parallax(scene, {
						selector: '.bdt-panel-slide-thumb',
						hoverOnly: true,
						pointerEvents: true
					});
				});
			}, 2000);
		}

	};


	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-panel-slider.default', widgetPanelSlider);
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-panel-slider.bdt-middle', widgetPanelSlider);
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-panel-slider.always-visible', widgetPanelSlider);
	});

}(jQuery, window.elementorFrontend));

/**
 * End panel slider widget script
 */
/**
 * Start progress pie widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetProgressPie = function ($scope, $) {

        const $progressPie = $scope.find('.bdt-progress-pie');

        if (!$progressPie.length) {
            return;
        }

        epObserveTarget($scope[0], function () {
            $progressPie.asPieProgress({
                namespace: 'pieProgress',
                classes: {
                    svg: 'bdt-progress-pie-svg',
                    number: 'bdt-progress-pie-number',
                    content: 'bdt-progress-pie-content'
                }
            });

            $progressPie.asPieProgress('start');

        }, {
            root: null,
            rootMargin: '0px',
            threshold: 1
        });

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-progress-pie.default', widgetProgressPie);
    });

}(jQuery, window.elementorFrontend));

/**
 * End progress pie widget script
 */


/**
 * Start reading progress widget script
 */

 (function($, elementor) {

    'use strict';

    var readingProgressWidget = function($scope, $) {

        var $readingProgress = $scope.find('.bdt-reading-progress');

        if (!$readingProgress.length) {
            return;
        }
        var $settings = $readingProgress.data('settings');

        jQuery(document).ready(function(){

            var settings = {
                borderSize: 10,
                mainBgColor: '#E6F4F7',
                lightBorderColor: '#A2ECFB',
                darkBorderColor: '#39B4CC'
            };

            var colorBg = $settings.progress_bg;
            var progressColor = $settings.scroll_bg;
            var innerHeight, offsetHeight, netHeight,
            container = $($readingProgress),
            borderContainer = 'bdt-reading-progress-border',
            circleContainer = 'bdt-reading-progress-circle',
            textContainer = 'bdt-reading-progress-text';

            var getHeight = function () {
                innerHeight = window.innerHeight;
                offsetHeight = document.body.offsetHeight;
                netHeight = offsetHeight - innerHeight;
            };

            var addEvent = function () {
                var e = document.createEvent('Event');
                e.initEvent('scroll', false, false);
                window.dispatchEvent(e);
            };
            var updateProgress = function (percnt) {
                var per = Math.round(100 * percnt);
                if (typeof percnt !== 'number' || !isFinite(percnt) || per < 0 || per > 100) {
                    per = 0;
                }
                var deg = per * 360 / 100;
                if (deg <= 180) {
                    $('.' + borderContainer, container).css('background-image', 'linear-gradient(' + (90 + deg) + 'deg, transparent 50%, ' + colorBg + ' 50%),linear-gradient(90deg, ' + colorBg + ' 50%, transparent 50%)');
                } else {
                    $('.' + borderContainer, container).css('background-image', 'linear-gradient(' + (deg - 90) + 'deg, transparent 50%, ' + progressColor + ' 50%),linear-gradient(90deg, ' + colorBg + ' 50%, transparent 50%)');
                }
                $('.' + textContainer, container).text(per + '%');
            };
            var prepare = function () {
                    $(container).html("<div class='" + borderContainer + "'><div class='" + circleContainer + "'><span class='" + textContainer + "'></span></div></div>");

                    $('.' + borderContainer, container).css({
                        'background-color': progressColor,
                        'background-image': 'linear-gradient(91deg, transparent 50%,' + settings.lightBorderColor + '50%), linear-gradient(90deg,' + settings.lightBorderColor + '50%, transparent 50%'
                    });
                    $('.' + circleContainer, container).css({
                        'width': settings.width - settings.borderSize,
                        'height': settings.height - settings.borderSize
                    });

                };
            var init = function () {
                    getHeight();
                    prepare();
                    $(window).on('scroll', function () {
                        var getOffset = window.scrollY || document.documentElement.scrollTop;
                        var percnt = (typeof netHeight === 'number' && isFinite(netHeight) && netHeight > 0)
                            ? Math.max(0, Math.min(1, getOffset / netHeight))
                            : 0;
                        updateProgress(percnt);
                    });
                    $(window).on('resize', function () {
                        getHeight();
                        addEvent();
                    });
                    $(window).on('load', function () {
                        getHeight();
                        addEvent();
                    });
                    addEvent();
                };
                 init();
            });

    };

    var readingProgressCursorSkin = function($scope, $) {

        var $readingProgress = $scope.find('.bdt-progress-with-cursor');

        if (!$readingProgress.length) {
            return;
        }

        document.getElementsByTagName('body')[0].addEventListener('mousemove', function(n) {
            t.style.left = n.clientX + 'px';
            t.style.top = n.clientY + 'px';
            e.style.left = n.clientX + 'px';
            e.style.top = n.clientY + 'px';
            i.style.left = n.clientX + 'px';
            i.style.top = n.clientY + 'px';
        });
        var t = document.querySelector('.bdt-cursor'),
        e = document.querySelector('.bdt-cursor2'),
        i = document.querySelector('.bdt-cursor3');

        function n(t) {
            e.classList.add('hover'), i.classList.add('hover');
        }

        function s(t) {
            e.classList.remove('hover'), i.classList.remove('hover');
        }
        s();
        for (var r = document.querySelectorAll('.hover-target'), a = r.length - 1; a >= 0; a--) {
            o(r[a]);
        }

        function o(t) {
            t.addEventListener('mouseover', n);
            t.addEventListener('mouseout', s);
        }

        $(document).ready(function() {
            var progressPath = document.querySelector('.bdt-progress-wrap path');
            var pathLength = progressPath.getTotalLength();
            progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
            progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
            progressPath.style.strokeDashoffset = pathLength;
            progressPath.getBoundingClientRect();
            progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';
            var updateProgress = function() {
                var scroll = $(window).scrollTop();
                var height = $(document).height() - $(window).height();
                var progress = pathLength - (scroll * pathLength / height);
                progressPath.style.strokeDashoffset = progress;
            };
            updateProgress();
            jQuery(window).on('scroll', updateProgress);


        });

    };

    var readingProgressHorizontalSkin = function($scope, $) {

        var $readingProgress = $scope.find('.bdt-horizontal-progress');

        if (!$readingProgress.length) {
            return;
        }

        $('#bdt-progress').progress({ size: '3px', wapperBg: '#eee', innerBg: '#DA4453' });

    };

    var readingProgressBackToTopSkin = function($scope, $) {

        var $readingProgress = $scope.find('.bdt-progress-with-top');

        if (!$readingProgress.length) {
            return;
        }

        var progressPath = document.querySelector('.bdt-progress-wrap path');
        var pathLength = progressPath.getTotalLength();
        progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
        progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
        progressPath.style.strokeDashoffset = pathLength;
        progressPath.getBoundingClientRect();
        progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';
        var updateProgress = function() {
            var scroll = jQuery(window).scrollTop();
            var height = jQuery(document).height() - jQuery(window).height();
            var progress = pathLength - (scroll * pathLength / height);
            progressPath.style.strokeDashoffset = progress;
        };
        updateProgress();
        jQuery(window).on('scroll', updateProgress);
        var offset = 50;
        var duration = 550;
        jQuery(window).on('scroll', function() {
            if (jQuery(this).scrollTop() > offset) {
                jQuery('.bdt-progress-wrap').addClass('active-progress');
            } else {
                jQuery('.bdt-progress-wrap').removeClass('active-progress');
            }
        });
        jQuery('.bdt-progress-wrap').on('click', function(event) {
            event.preventDefault();
            jQuery('html, body').animate({ scrollTop: 0 }, duration);
            return false;
        });

    };

    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-reading-progress.default', readingProgressWidget);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-reading-progress.bdt-progress-with-cursor', readingProgressCursorSkin);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-reading-progress.bdt-horizontal-progress', readingProgressHorizontalSkin);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-reading-progress.bdt-back-to-top-with-progress', readingProgressBackToTopSkin);
    });

}(jQuery, window.elementorFrontend));

/**
 * End reading progress widget script
 */


(function ($, elementor) {
  $(window).on("elementor/frontend/init", function () {
    let ModuleHandler = elementorModules.frontend.handlers.Base,
      ReadingTimer;

    ReadingTimer = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },
      getDefaultSettings: function () {
        return {
          allowHTML: true,
        };
      },

      settings: function (key) {
        return this.getElementSettings("reading_timer_" + key);
      },

      calculateReadingTime: function (ReadingContent) {
        let wordCount = ReadingContent.split(/\s+/).filter(function (word) {
            return word !== "";
          }).length,
          averageReadingSpeed = this.settings("avg_words_per_minute")
            ? this.settings("avg_words_per_minute").size
            : 200,
          readingTime = Math.floor(wordCount / averageReadingSpeed),
          reading_seconds = Math.floor(
            (wordCount % averageReadingSpeed) / (averageReadingSpeed / 60)
          ),
          minText = this.settings("minute_text")
            ? this.settings("minute_text")
            : "min read",
          secText = this.settings("seconds_text")
            ? this.settings("seconds_text")
            : "sec read";

        if (wordCount >= averageReadingSpeed) {
          return `${readingTime} ${minText}`;
        } else {
          return `${reading_seconds} ${secText}`;
        }
      },

      run: function () {
        const widgetID = this.$element.data("id"),
          widgetContainer = `.elementor-element-${widgetID} .bdt-reading-timer`,
          contentSelector = this.settings("content_id");
        let minText = this.settings("minute_text")
          ? this.settings("minute_text")
          : "min read";

        const editMode = Boolean(elementorFrontend.isEditMode());
        if (editMode) {
          $(widgetContainer).append("2 " + minText + "");
          return;
        }
        if (contentSelector) {
          ReadingContent = $(document).find(`#${contentSelector}`).text();
          const readTime = this.calculateReadingTime(ReadingContent);
          $(widgetContainer).append(readTime);
        } else return;
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-reading-timer.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(ReadingTimer, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);

/**
 * Start twitter carousel widget script
 */

(function ($, elementor) {

	'use strict';

	var widgetReviewCardCarousel = function ($scope, $) {

		var $reviewCardCarousel = $scope.find('.bdt-review-card-carousel');

		if (!$reviewCardCarousel.length) {
			return;
		}

		var $reviewCardCarouselContainer = $reviewCardCarousel.find('.swiper-carousel'),
			$settings = $reviewCardCarousel.data('settings');

		const Swiper = elementorFrontend.utils.swiper;
		initSwiper();
		async function initSwiper() {
			var swiper = await new Swiper($reviewCardCarouselContainer, $settings);

			if ($settings.pauseOnHover) {
				$($reviewCardCarouselContainer).hover(function () {
					(this).swiper.autoplay.stop();
				}, function () {
					(this).swiper.autoplay.start();
				});
			}

		};


	};


	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-review-card-carousel.default', widgetReviewCardCarousel);
	});

}(jQuery, window.elementorFrontend));

/**
 * End twitter carousel widget script
 */


/**
 * Start scroll button widget script
 */

( function( $, elementor ) {

	'use strict';

	const widgetScrollButton = function( $scope, $ ) {

			const $scrollButton = $scope.find('.bdt-scroll-button');

	    if ( ! $scrollButton.length ) {
	    	return;
	    }

			const $selector = $scrollButton.data('selector'),
			$settings = $scrollButton.data('settings');

	    if ($settings.HideOnBeforeScrolling == true) {

			$(window).scroll(function() {
			  if ($(window).scrollTop() > 300) {
			    $scrollButton.css("opacity", "1");
			  } else {
			    $scrollButton.css("opacity", "0");
			  }
			});
	    }

	    $($scrollButton).on('click', function(event){
	    	event.preventDefault();
	    	bdtUIkit.scroll($scrollButton, $settings ).scrollTo($($selector));

	    });

	};

	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-scroll-button.default', widgetScrollButton );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End scroll button widget script
 */

/**
 * Start search widget script
 */

(function ($, elementor) {
  'use strict';
  var serachTimer;
  var widgetAjaxSearch = function ($scope, $) {
    var $searchContainer = $scope.find('.bdt-search-container'),
      $searchWidget = $scope.find('.bdt-ajax-search');

    $($scope).find('.bdt-navbar-dropdown-close').on('click', function () {
      bdtUIkit.drop($scope.find('.bdt-navbar-dropdown')).hide();
    });

    let $search;

    if (!$searchWidget.length) {
      return;
    }

    var $resultHolder = $($searchWidget).find('.bdt-search-result'),
      $settings = $($searchWidget).data('settings'),
      $connectSettings = $($searchContainer).data('settings'),
      $target = $($searchWidget).attr('anchor-target');

    if ('yes' === $target) {
      $target = '_blank';
    } else {
      $target = '_self';
    }

    clearTimeout(serachTimer);

    if ($connectSettings && $connectSettings.element_connect) {
      $($connectSettings.element_selector).hide();
    }

    $($searchWidget).on('keyup keypress', function (e) {
      var keyCode = e.keyCode || e.which;
      if (keyCode === 13) {
        e.preventDefault();
        return false;
      }
    });

    $searchWidget.find('.bdt-search-input').keyup(function () {
      $search = $(this).val();
      serachTimer = setTimeout(function () {
        $($searchWidget).addClass('bdt-search-loading');
        jQuery.ajax({
          url: window.ElementPackConfig.ajaxurl,
          type: 'post',
          data: {
            action: 'element_pack_search',
            nonce: window.ElementPackConfig.nonce,
            s: $search,
            settings: $settings,
          },
          success: function (response) {
            var response = $.parseJSON(response);

            if (response.results.length > 0) {
              if ($search.length >= 3) {
                var output = `<div class="bdt-search-result-inner">
                          <h3 class="bdt-search-result-header">${window.ElementPackConfig.search.search_result}<i class="ep-icon-close bdt-search-result-close-btn"></i></h3>
                          <ul class="bdt-list bdt-list-divider">`;
                for (let i = 0; i < response.results.length; i++) {
                  const element = response.results[i];
                  output += `<li class="bdt-search-item" data-url="${element.url}">
                            <a href="${element.url}" target="${$target}">
                            <div class="bdt-search-title">${element.title}</div>
                            <div class="bdt-search-text">${element.text}</div>
                            </a>
                          </li>`;
                }
                output += `</ul><a class="bdt-search-more">${window.ElementPackConfig.search.more_result}</a></div>`;

                $resultHolder.html(output);
                $resultHolder.show();
                $(".bdt-search-result-close-btn").on("click", function (e) {
                  $(".bdt-search-result").hide();
                  $(".bdt-search-input").val("");
                });

                $($searchWidget).removeClass("bdt-search-loading");
                $(".bdt-search-more").on("click", function (event) {
                  event.preventDefault();
                  $($searchWidget).submit();
                });
              } else {
                $resultHolder.hide();
              }
            } else {
              if ($search.length > 3) {
                var not_found = `<div class="bdt-search-result-inner">
                                  <h3 class="bdt-search-result-header">${window.ElementPackConfig.search.search_result}<i class="ep-icon-close bdt-search-result-close-btn"></i></h3>
                                  <div class="bdt-search-text">${$search} ${window.ElementPackConfig.search.not_found}</div>
                                </div>`;
                $resultHolder.html(not_found);
                $resultHolder.show();
                $(".bdt-search-result-close-btn").on("click", function (e) {
                  $(".bdt-search-result").hide();
                  $(".bdt-search-input").val("");
                });
                $($searchWidget).removeClass("bdt-search-loading");

                if ($connectSettings && $connectSettings.element_connect) {
                  $resultHolder.hide();
                  setTimeout(function () {
                    $($connectSettings.element_selector).show();
                  }, 1500);
                }

              } else {
                $resultHolder.hide();
                $($searchWidget).removeClass("bdt-search-loading");
              }

            }
          }
        });
      }, 450);
    });

  };


  jQuery(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction('frontend/element_ready/bdt-search.default', widgetAjaxSearch);
  });

})(jQuery, window.elementorFrontend);

/**
 * End search widget script
 */
/**
 * Start slider widget script
 */

( function( $, elementor ) {

	'use strict';

	const widgetSlider = function( $scope, $ ) {

		const $slider = $scope.find( '.bdt-slider' );

        if ( ! $slider.length ) {
            return;
        }

        const $sliderContainer = $slider.find('.swiper-carousel'),
			$settings = $slider.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();

        async function initSwiper() {

			await new Swiper($sliderContainer, $settings);

			if ($settings.pauseOnHover) {
				 $($sliderContainer).hover(function() {
					(this).swiper.autoplay.stop();
				}, function() {
					(this).swiper.autoplay.start();
				});
			}
		};

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-slider.default', widgetSlider );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-acf-slider.default', widgetSlider );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End slider widget script
 */

/**
 * Start twitter carousel widget script
 */

( function( $, elementor ) {

	'use strict';

	const widgetStaticCarousel = function( $scope, $ ) {

		const $StaticCarousel = $scope.find( '.bdt-static-carousel' );

        if ( ! $StaticCarousel.length ) {
            return;
        }

		const $StaticCarouselContainer = $StaticCarousel.find('.swiper-carousel'),
			$settings = $StaticCarousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();

        async function initSwiper() {

			await new Swiper($StaticCarouselContainer, $settings);

			if ($settings.pauseOnHover) {
				 $($StaticCarouselContainer).hover(function() {
					(this).swiper.autoplay.stop();
				}, function() {
					(this).swiper.autoplay.start();
				});
			}
		};

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-static-carousel.default', widgetStaticCarousel );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End twitter carousel widget script
 */

/**
 * Start post grid tab widget script
 */

;
(function ($, elementor) {

	'use strict';

	const widgetStaticPostTab = function ($scope, $) {

		const $postGridTab = $scope.find('.bdt-static-grid-tab');

		if (!$postGridTab.length) {
			return;
		}

		const gridTab = $postGridTab.find('.gridtab');
		const $settings = $postGridTab.data('settings');

		$(gridTab).gridtab($settings);

	};


	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-static-grid-tab.default', widgetStaticPostTab);
	});

}(jQuery, window.elementorFrontend));

/**
 * End post grid tab widget script
 */

/**
 * Start step flow widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetStepFlow = function ($scope, $) {

        var $avdDivider = $scope.find('.bdt-step-flow'),
            divider = $avdDivider.find('.bdt-title-separator-wrapper > img');

        if (!$avdDivider.length) {
            return;
        }

        epObserveTarget($scope[0], function () {
            bdtUIkit.svg(divider, {
                strokeAnimation: true
            });
        }, {
            root: null,
            rootMargin: '0px',
            threshold: 0.8
        });

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-step-flow.default', widgetStepFlow);
    });

}(jQuery, window.elementorFrontend));

/**
 * End step flow widget script
 */


/**
 * Start toggle widget script
 */

(function ($, elementor) {
    'use strict';
    var widgetToggle = function ($scope, $) {
        var $toggleContainer = $scope.find('.bdt-show-hide-container');
        var $toggle          = $toggleContainer.find('.bdt-show-hide');

        if ( !$toggleContainer.length ) {
            return;
        } 
        var $settings            = $toggle.data('settings');
        var toggleId             = $settings.id;
        var animTime             = $settings.scrollspy_time;
        var scrollspy_top_offset = $settings.scrollspy_top_offset;

        var by_widget_selector_status = $settings.by_widget_selector_status;
        var toggle_initially_open     = $settings.toggle_initially_open;
        var source_selector           = $settings.source_selector;
        var widget_visibility         = $settings.widget_visibility;
        var widget_visibility_tablet  = $settings.widget_visibility_tablet;
        var widget_visibility_mobile  = $settings.widget_visibility_mobile;
        var viewport_lg               = $settings.viewport_lg;
        var viewport_md               = $settings.viewport_md;

        var widget_visibility_filtered = widget_visibility;

        if ( $settings.widget_visibility == 'undefined' || $settings.widget_visibility == null ) {
            widget_visibility_filtered = widget_visibility = 0;
        }

        if ( $settings.widget_visibility_tablet == 'undefined' || $settings.widget_visibility_tablet == null ) {
            widget_visibility_tablet = widget_visibility;
        }

        if ( $settings.widget_visibility_mobile == 'undefined' || $settings.widget_visibility_mobile == null ) {
            widget_visibility_mobile = widget_visibility;
        }

        function widgetVsibleFiltered() {
            if ( (window.outerWidth) > (viewport_lg) ) {
                widget_visibility_filtered = widget_visibility;
            } else if ( (window.outerWidth) > (viewport_md) ) {
                widget_visibility_filtered = widget_visibility_tablet;
            } else {
                widget_visibility_filtered = widget_visibility_mobile;
            }
        }

        $(window).resize(function () {
            widgetVsibleFiltered();
        });


        function scrollspyHandler($toggle, toggleId, toggleBtn, animTime, scrollspy_top_offset) {
            if ( $settings.status_scrollspy === 'yes' && by_widget_selector_status !== 'yes' ) {
                if ( $($toggle).find('.bdt-show-hide-item') ) {
                    if ( $settings.hash_location === 'yes' ) {
                        window.location.hash = ($.trim(toggleId));
                    }
                    var scrollspyWrapper = $('#bdt-show-hide-' + toggleId).find('.bdt-show-hide-item');
                    $('html, body').animate({
                        easing   : 'slow',
                        scrollTop: $(scrollspyWrapper).offset().top - scrollspy_top_offset
                    }, animTime, function () {
                        //#code
                    }).promise().then(function () {
                        $(toggleBtn).siblings('.bdt-show-hide-content').slideToggle('slow', function () {
                            $(toggleBtn).parent().toggleClass('bdt-open');
                        });
                    });
                }
            } else {
                if ( by_widget_selector_status === 'yes' ) {
                    $(toggleBtn).parent().toggleClass('bdt-open');
                    $(toggleBtn).siblings('.bdt-show-hide-content').slideToggle('slow', function () {
                    });
                }else{
                    $(toggleBtn).siblings('.bdt-show-hide-content').slideToggle('slow', function () {
                        $(toggleBtn).parent().toggleClass('bdt-open');
                    });
                }
                
            }
        }

        $($toggle).find('.bdt-show-hide-title').off('click').on('click', function (event) {
            var toggleBtn = $(this);
            scrollspyHandler($toggle, toggleId, toggleBtn, animTime, scrollspy_top_offset);
        });

        function hashHandler() {
            toggleId             = window.location.hash.substring(1);
            var toggleBtn        = $('#bdt-show-hide-' + toggleId).find('.bdt-show-hide-title');
            var scrollspyWrapper = $('#bdt-show-hide-' + toggleId).find('.bdt-show-hide-item');
            $('html, body').animate({
                easing   : 'slow',
                scrollTop: $(scrollspyWrapper).offset().top - scrollspy_top_offset
            }, animTime, function () {
                //#code
            }).promise().then(function () {
                $(toggleBtn).siblings('.bdt-show-hide-content').slideToggle('slow', function () {
                    $(toggleBtn).parent().toggleClass('bdt-open');
                });
            });
        }

        $(window).on('load', function () {
            if ( $($toggleContainer).find('#bdt-show-hide-' + window.location.hash.substring(1)).length != 0 ) {
                if ( $settings.hash_location === 'yes' ) {
                    hashHandler();
                }
            }
        });

        function autoHeightAnimate(element, time){
    var curHeight = element.height(), // Get Default Height
        autoHeight = element.css('height', 'auto').height(); // Get Auto Height
          element.height(curHeight); // Reset to Default Height
          element.stop().animate({ height: autoHeight }, time); // Animate to Auto Height
      }
      function byWidgetHandler() {
        if ( $settings.status_scrollspy === 'yes' ) {
            $('html, body').animate({
                easing   : 'slow',
                scrollTop: $(source_selector).offset().top - scrollspy_top_offset
            }, animTime, function () {
                    //#code
                }).promise().then(function () {
                    if ( $(source_selector).hasClass('bdt-fold-close') ) {
                        $(source_selector).removeClass('bdt-fold-close toggle_initially_open').addClass('bdt-fold-open');
                        autoHeightAnimate($(source_selector), 500);
                    } else {
                        $(source_selector).css({
                            'height': widget_visibility_filtered + 'px'
                        }).addClass('bdt-fold-close').removeClass('bdt-fold-open');
                    }
                });
            } else {
                if ( $(source_selector).hasClass('bdt-fold-close') ) {
                    $(source_selector).removeClass('bdt-fold-close toggle_initially_open').addClass('bdt-fold-open');
                    autoHeightAnimate($(source_selector), 500);

                } else {
                    $(source_selector).css({
                        'height': widget_visibility_filtered + 'px',
                        'transition' : 'all 1s ease-in-out 0s'
                    }).addClass('bdt-fold-close').removeClass('bdt-fold-open');    
                } 
            }

        }


        if ( by_widget_selector_status === 'yes' ) {
            $($toggle).find('.bdt-show-hide-title').on('click', function () {
                byWidgetHandler();
            });

            if ( toggle_initially_open === 'yes' ) {
                $(source_selector).addClass('bdt-fold-toggle bdt-fold-open toggle_initially_open');
            } else {
                $(source_selector).addClass('bdt-fold-toggle bdt-fold-close toggle_initially_open');
            }

            $(window).resize(function () {
                visibilityCalled();
            });
            visibilityCalled();
        }

        function visibilityCalled() {
            if ( $(source_selector).hasClass('bdt-fold-close') ) {
                $(source_selector).css({
                    'height': widget_visibility_filtered + 'px'
                });
            } else {
                autoHeightAnimate($(source_selector), 500);
            }
        }


    };
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-toggle.default', widgetToggle);
    });
}(jQuery, window.elementorFrontend));

/**
 * End toggle widget script
 */


/**
 * Start tutor lms grid widget script
 */

(function ($, elementor) {

	'use strict';

	var widgetTutorLMSGrid = function ($scope, $) {

		var $tutorLMS = $scope.find('.bdt-tutor-lms-course-grid');

		if (!$tutorLMS.length) {
			return;
		}

		var $settings = $tutorLMS.data('settings');

		if ($settings.tiltShow == true) {
			var elements = document.querySelectorAll($settings.id + " .bdt-tutor-course-item");
			VanillaTilt.init(elements);
		}

	};

	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-tutor-lms-course-grid.default', widgetTutorLMSGrid);
	});

}(jQuery, window.elementorFrontend));

/**
 * End tutor lms grid widget script
 */

/**
 * Start tutor lms widget script
 */

(function ($, elementor) {

	'use strict';

	var widgetTutorCarousel = function ($scope, $) {

		var $tutorCarousel = $scope.find('.bdt-tutor-lms-course-carousel');

		if (!$tutorCarousel.length) {
			return;
		}

		var $tutorCarouselContainer = $tutorCarousel.find('.swiper-carousel'),
			$settings = $tutorCarousel.data('settings');

		const Swiper = elementorFrontend.utils.swiper;
		initSwiper();

		async function initSwiper() {

			await new Swiper($tutorCarouselContainer, $settings);

			if ($settings.pauseOnHover) {
				$($tutorCarouselContainer).hover(function () {
					(this).swiper.autoplay.stop();
				}, function () {
					(this).swiper.autoplay.start();
				});
			}
		};
	};


	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-tutor-lms-course-carousel.default', widgetTutorCarousel);
	});

}(jQuery, window.elementorFrontend));

/**
 * End tutor lms widget script
 */
/**
 * Start user register widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetUserRegistrationForm = {

        registraitonFormSubmit: function (_this, $scope) {

            bdtUIkit.notification({
                message: '<div bdt-spinner></div>' + $(_this).find('.bdt_spinner_message').val(),
                timeout: false
            });
            $(_this).find('button.bdt-button').attr("disabled", true);
            var redirect_url = $(_this).find('.redirect_after_register').val();
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: element_pack_ajax_login_config.ajaxurl,
                data: {
                    'action': 'element_pack_ajax_register', //calls wp_ajax_nopriv_element_pack_ajax_register
                    'first_name': $(_this).find('.first_name').val(),
                    'terms': $(_this).find('.user_terms').is(':checked'),
                    'last_name': $(_this).find('.last_name').val(),
                    'email': $(_this).find('.user_email').val(),
                    'password': $(_this).find('.user_password').val(),
                    'is_password_required': $(_this).find('.is_password_required').val(),
                    'g-recaptcha-response': $(_this).find('#g-recaptcha-response').val(),
                    'widget_id': $scope.data('id'),
                    'page_id': $(_this).find('.page_id').val(),
                    'security': $(_this).find('#bdt-user-register-sc').val(),
                    'lang': element_pack_ajax_login_config.language
                },
                success: function (data) {
                    var recaptcha_field = _this.find('.element-pack-google-recaptcha');
                    if (recaptcha_field.length > 0) {
                        var recaptcha_id = recaptcha_field.attr('data-widgetid');
                        grecaptcha.reset(recaptcha_id);
                        grecaptcha.execute(recaptcha_id);
                    }

                    if (data.registered === true) {
                        bdtUIkit.notification.closeAll();
                        bdtUIkit.notification({
                            message: '<div class="bdt-flex"><span bdt-icon=\'icon: info\'></span><span>' + data.message + '</span></div>',
                            status: 'primary'
                        });
                        if (redirect_url) {
                            document.location.href = redirect_url;
                        }
                    } else {
                        bdtUIkit.notification.closeAll();
                        bdtUIkit.notification({
                            message: '<div class="bdt-flex"><span bdt-icon=\'icon: warning\'></span><span>' + data.message + '</span></div>',
                            status: 'warning'
                        });
                    }
                    $(_this).find('button.bdt-button').attr("disabled", false);

                },
            });
        },
        load_recaptcha: function () {
            var reCaptchaFields = $('.element-pack-google-recaptcha'),
                widgetID;

            if (reCaptchaFields.length > 0) {
                reCaptchaFields.each(function () {
                    var self = $(this),
                        attrWidget = self.attr('data-widgetid');
                    // Avoid re-rendering as it's throwing API error
                    if ((typeof attrWidget !== typeof undefined && attrWidget !== false)) {
                        return;
                    } else {
                        widgetID = grecaptcha.render($(this).attr('id'), {
                            sitekey: self.data('sitekey'),
                            callback: function (response) {
                                if (response !== '') {
                                    self.append(jQuery('<input>', {
                                        type: 'hidden',
                                        value: response,
                                        class: 'g-recaptcha-response'
                                    }));
                                }
                            }
                        });
                        self.attr('data-widgetid', widgetID);
                    }
                });
            }
        }

    }


    window.onLoadElementPackRegisterCaptcha = widgetUserRegistrationForm.load_recaptcha;

    var widgetUserRegisterForm = function ($scope, $) {
        var register_form = $scope.find('.bdt-user-register-widget'),
            recaptcha_field = $scope.find('.element-pack-google-recaptcha'),
            $userRegister = $scope.find('.bdt-user-register');

        // Perform AJAX register on form submit
        register_form.on('submit', function (e) {
            e.preventDefault();
            widgetUserRegistrationForm.registraitonFormSubmit(register_form, $scope)
        });

        if (elementorFrontend.isEditMode() && undefined === recaptcha_field.attr('data-widgetid')) {
            onLoadElementPackRegisterCaptcha();
        }

        if (recaptcha_field.length > 0) {
            grecaptcha.ready(function () {
                var recaptcha_id = recaptcha_field.attr('data-widgetid');
                grecaptcha.execute(recaptcha_id);
            });
        }

        var $settings = $userRegister.data('settings');

        if (!$settings || typeof $settings.passStrength === "undefined") {
            return;
        }

        var percentage = 0,
            $selector = $('#' + $settings.id),
            $progressBar = $('#' + $settings.id).find('.bdt-progress-bar');

        var passStrength = {
            progress: function ($value = 0) {
                if ($value <= 100) {
                    $($progressBar).css({
                        'width': $value + '%'
                    });
                }
            },
            formula: function (input, length) {

                if (length < 6) {
                    percentage = 0;
                    $($progressBar).css('background', '#ff4d4d'); //red
                } else if (length < 8) {
                    percentage = 10;
                    $($progressBar).css('background', '#ffff1a'); //yellow
                } else if (input.match(/0|1|2|3|4|5|6|7|8|9/) == null && input.match(/[A-Z]/) == null) {
                    percentage = 40;
                    $($progressBar).css('background', '#ffc14d'); //orange
                }else{
                    if (length < 12){
                        percentage = 50;
                        $($progressBar).css('background', '#1aff1a'); //green
                    }else{
                        percentage = 60;
                        $($progressBar).css('background', '#1aff1a'); //green
                    }
                }


                //Lowercase Words only
                if ((input.match(/[a-z]/) != null)) {
                    percentage += 10;
                }

                //Uppercase Words only
                if ((input.match(/[A-Z]/) != null)) {
                    percentage += 10;
                }

                //Digits only
                if ((input.match(/0|1|2|3|4|5|6|7|8|9/) != null)) {
                    percentage += 10;
                }

                //Special characters
                if ((input.match(/\W/) != null) && (input.match(/\D/) != null)) {
                    percentage += 10;
                }
                return percentage;
            },
            forceStrongPass: function (result) {
                if (result >= 70) {
                    $($selector).find('.elementor-field-type-submit .bdt-button').prop('disabled', false);
                } else {
                    $($selector).find('.elementor-field-type-submit .bdt-button').prop('disabled', true);
                }
            },
            init: function () {
                $scope.find('.user_password').keyup(function () {
                    var input = $(this).val(),
                        length = input.length;
                    let result = passStrength.formula(input, length);
                    passStrength.progress(result);

                    if (typeof $settings.forceStrongPass !== 'undefined') {
                        passStrength.forceStrongPass(result);
                    }
                });
                if (typeof $settings.forceStrongPass !== 'undefined') {
                    $($selector).find('.elementor-field-type-submit .bdt-button').prop('disabled', true);
                }

                $scope.find('.confirm_password').keyup(function () {
                    let input = $(this).val(),
                        length = input.length;
                    let result = passStrength.formula(input, length);
                    passStrength.progress(result);

                    let pass = $scope.find('.user_password').val();
                    
                    if(input !== pass){
                        $scope.find('.bdt-user-register-pass-res').removeClass('bdt-hidden');
                        $($selector).find('.elementor-field-type-submit .bdt-button').prop('disabled', true);
                    }else{
                        $scope.find('.bdt-user-register-pass-res').addClass('bdt-hidden');
                        if (typeof $settings.forceStrongPass !== 'undefined') {
                            passStrength.forceStrongPass(result);
                        }
                    }

                });
            }
        }

        passStrength.init();

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-user-register.default', widgetUserRegisterForm);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-user-register.bdt-dropdown', widgetUserRegisterForm);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-user-register.bdt-modal', widgetUserRegisterForm);
    });

}(jQuery, window.elementorFrontend));

/**
 * End user register widget script
 */
jQuery(document).ready(function () {
    jQuery('body').on('click', '.bdt-element-link', function () {
        var $el = jQuery(this)
          , settings = $el.data("ep-wrapper-link");
        if (settings && settings.url && (/^(https?:\/\/|tel:|mailto:|sms:)/.test(settings.url) || settings.url.startsWith("#"))) {
            var id = "bdt-element-link-" + $el.data("id");
            0 === jQuery("#" + id).length && jQuery("body").append(jQuery("<a/>").prop({
                target: settings.is_external ? "_blank" : "_self",
                href: settings.url,
                class: "bdt-hidden",
                id: id,
                rel: settings.is_external ? "noopener noreferrer" : ""
            })),
            jQuery("#" + id)[0].click()
        }
    });
});

; (function ($, elementor) {
	'use strict';

	$(window).on('elementor/frontend/init', function () {
		var ModuleHandler = elementorModules.frontend.handlers.Base,
			ThreedText;

		function parseJSON(value) {
			if (!value || 'string' !== typeof value) {
				return null;
			}

			try {
				return JSON.parse(value);
			} catch (error) {
				return null;
			}
		}

		function unwrapAtomicValue(value) {
			if (value && 'object' === typeof value && Object.prototype.hasOwnProperty.call(value, 'value')) {
				return unwrapAtomicValue(value.value);
			}

			return value;
		}

		function normalizeAtomicSettings(settings) {
			if (!settings) {
				return null;
			}

			if (settings.active || settings.depth || settings.layers) {
				return settings;
			}

			if (unwrapAtomicValue(settings.ep_threed_text_active) !== true && unwrapAtomicValue(settings.ep_threed_text_active) !== 'yes') {
				return null;
			}

			return {
				active: 'yes',
				depth: { size: parseFloat(unwrapAtomicValue(settings.ep_threed_text_depth) || 30), unit: 'px' },
				layers: parseInt(unwrapAtomicValue(settings.ep_threed_text_layers) || 8, 10),
				depth_color: unwrapAtomicValue(settings.ep_threed_text_depth_color) || '',
				perspective: { size: parseFloat(unwrapAtomicValue(settings.ep_threed_text_perspective) || 500), unit: 'px' },
				fade: unwrapAtomicValue(settings.ep_threed_text_fade) ? 'yes' : '',
				event: unwrapAtomicValue(settings.ep_threed_text_event) || 'none',
				event_rotation: { size: parseFloat(unwrapAtomicValue(settings.ep_threed_text_event_rotation) || 35), unit: 'deg' },
				event_direction: unwrapAtomicValue(settings.ep_threed_text_event_direction) || 'default'
			};
		}

		function applyThreeDText($heading, settings, forcedId) {
			if (!$heading.length || !settings || settings.active !== 'yes') {
				return;
			}

			var node = $heading.get(0);
			var headingId = forcedId || $heading.attr('id') || ('ep-atomic-' + Date.now() + '-' + Math.floor(Math.random() * 10000));
			var selector = '#' + headingId;
			var options = {
				depth: '30px',
				layers: 8
			};

			$heading.attr('id', headingId);

			if (settings.depth && settings.depth.size) {
				options.depth = settings.depth.size + (settings.depth.unit || 'px');
			}
			if (settings.layers) {
				options.layers = settings.layers;
			}
			if (settings.perspective && settings.perspective.size) {
				options.perspective = settings.perspective.size + 'px';
			}
			if (settings.fade) {
				options.fade = settings.fade === 'yes' || settings.fade === true;
			}
			if (settings.event) {
				options.event = settings.event;
			}
			if (settings.event_rotation && settings.event !== 'none') {
				options.eventRotation = settings.event_rotation.size + 'deg';
			}
			if (settings.event_direction && settings.event !== 'none') {
				options.eventDirection = settings.event_direction;
			}

			var text = $heading.html();
			$heading.parent().find('.ep-z-text-duplicate').remove();
			$heading.parent().append('<div class="ep-z-text-duplicate" style="display:none;">' + text + '</div>');
			text = $heading.parent().find('.ep-z-text-duplicate:first').html();

			$heading.find('.z-text').remove();
			new Ztextify(selector, options, text);

			if (settings.depth_color) {
				$(selector).find('.z-layers .z-layer:not(:first-child)').css('color', settings.depth_color);
			} else if (node) {
				var computedColor = window.getComputedStyle(node).color;
				$(selector).find('.z-layers .z-layer:not(:first-child)').css('color', computedColor);
			}
		}

		function runAtomicThreedText() {
			jQuery('.elementor-widget-e-heading .e-heading-base, [data-widget_type^="e-heading"] .e-heading-base, .e-heading-base[data-ep-threed-text]').each(function () {
				var heading = this;
				var wrapper = heading.closest('[data-id]');
				var settings = parseJSON(heading.getAttribute('data-ep-threed-text'));

				if (!settings && wrapper) {
					settings = parseJSON(wrapper.getAttribute('data-ep-threed-text'));
				}

				if (!settings && wrapper) {
					settings = jQuery(wrapper).data('settings') || parseJSON(wrapper.getAttribute('data-settings'));
				}

				settings = normalizeAtomicSettings(settings);

				if (!settings) {
					return;
				}

				applyThreeDText(jQuery(heading), settings, 'ep-atomic-' + (wrapper ? wrapper.getAttribute('data-id') : Date.now()));
			});
		}

		ThreedText = ModuleHandler.extend({

			bindEvents: function () {
				this.run();
			},

			getDefaultSettings: function () {
				return {
					depth: '30px',
					layers: 8,
				};
			},

			onElementChange: debounce(function (prop) {
				if (prop.indexOf('ep_threed_text_') !== -1) {
					this.run();
				}
			}, 400),

			settings: function (key) {
				return this.getElementSettings('ep_threed_text_' + key);
			},

			run: function () {
				var options = this.getDefaultSettings(),
					$element = this.findElement('.elementor-heading-title, .bdt-main-heading-inner'),
					$widgetId = 'ep-' + this.getID(),
					$widgetIdSelect = '#' + $widgetId;

				jQuery($element).attr('id', $widgetId);

				if (this.settings('depth.size')) {
					options.depth = this.settings('depth.size') + this.settings('depth.unit') || '30px';
				}
				if (this.settings('layers')) {
					options.layers = this.settings('layers') || 8;
				}
				if (this.settings('perspective.size')) {
					options.perspective = this.settings('perspective.size') + 'px' || '500px';
				}
				if (this.settings('fade')) {
					options.fade = !!this.settings('fade');
				}
				if (this.settings('event')) {
					options.event = this.settings('event') || 'pointer';
				}
				if (this.settings('event_rotation') && this.settings('event') != 'none') {
					options.eventRotation = this.settings('event_rotation.size') + 'deg' || '35deg';
				}
				if (this.settings('event_direction') && this.settings('event') != 'none') {
					options.eventDirection = this.settings('event_direction') || 'default';
				}

				if (this.settings('active') == 'yes') {

					var $text = $($widgetIdSelect).html();
					$($widgetIdSelect).parent().append('<div class="ep-z-text-duplicate" style="display:none;">' + $text + '</div>');

					$text = $($widgetIdSelect).parent().find('.ep-z-text-duplicate:first').html();

					$($widgetIdSelect).find('.z-text').remove();

					new Ztextify($widgetIdSelect, options, $text);
				}

				if (this.settings('depth_color')) {
					var depthColor = this.settings('depth_color') || '#fafafa';
					$($widgetIdSelect).find('.z-layers .z-layer:not(:first-child)').css('color', depthColor);
				}
			}
		});

		elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
			elementorFrontend.elementsHandler.addHandler(ThreedText, {
				$element: $scope
			});
		});

		elementorFrontend.hooks.addAction('frontend/element_ready/e-heading.default', function () {
			runAtomicThreedText();
		});

		runAtomicThreedText();
		jQuery(window).on('load', runAtomicThreedText);
	});
})(jQuery, window.elementorFrontend);

/**
 * Start twitter carousel widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetProductCarousel = function( $scope, $ ) {

		const $ProductCarousel = $scope.find( '.bdt-ep-product-carousel' );
				
        if ( ! $ProductCarousel.length ) {
            return;
        }

		const $ProductCarouselContainer = $ProductCarousel.find('.swiper-carousel'),
			$settings 		 = $ProductCarousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        
        async function initSwiper() {

			await new Swiper($ProductCarouselContainer, $settings);

			if ($settings.pauseOnHover) {
				 $($ProductCarouselContainer).hover(function() {
					this.swiper.autoplay.stop();
				}, function() {
					this.swiper.autoplay.start();
				});
			}
		};

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-product-carousel.default', widgetProductCarousel );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End twitter carousel widget script
 */


/**
 * Start age-gate script
 */

(() => {
  "use strict";

  class AgeGateModal {
    constructor(element, settings, isEditMode) {
      this.element = element;
      this.settings = settings;
      this.isEditMode = isEditMode;
      this.widgetId = settings.widgetId;
      this.abortController = new AbortController();
      this.signal = this.abortController.signal;
    }

    setLocalize() {
      if (this.isEditMode) {
        this.clearLocalize();
        return;
      }

      this.clearLocalize();

      const hours = this.settings.displayTimesExpire;
      const expires = hours * 60 * 60; // Convert to seconds
      const now = Date.now();
      const schedule = now + expires * 1000;

      if (localStorage.getItem(this.widgetId) === null) {
        localStorage.setItem(this.widgetId, "0");
        localStorage.setItem(`${this.widgetId}_expiresIn`, schedule.toString());
      }

      if (localStorage.getItem(this.widgetId) !== null) {
        let count = parseInt(localStorage.getItem(this.widgetId), 10) || 0;
        count++;
        localStorage.setItem(this.widgetId, count.toString());
      }
    }

    clearLocalize() {
      const localizeExpiry = parseInt(
        localStorage.getItem(`${this.widgetId}_expiresIn`), 10
      );
      const now = Date.now();

      if (now >= localizeExpiry) {
        localStorage.removeItem(`${this.widgetId}_expiresIn`);
        localStorage.removeItem(this.widgetId);
      }
    }

    modalFire() {
      const displayTimes = this.settings.displayTimes || 1;
      const firedNotify = parseInt(localStorage.getItem(this.widgetId), 10) || 0;

      if (displayTimes !== false && firedNotify >= displayTimes) {
        return;
      }

      if (window.bdtUIkit?.modal) {
        window.bdtUIkit.modal(this.element, {
          bgclose: false,
          keyboard: false,
        }).show();
      }
    }

    setupAgeVerify() {
      let firedNotify = parseInt(localStorage.getItem(this.widgetId), 10) || 0;
      const modal = this.element;
      const buttons = modal.querySelectorAll(".bdt-button");
      const redirectLink = this.isEditMode ? false : this.settings.redirect_link;
      let requiredAge = this.settings.requiredAge;

      buttons.forEach((button) => {
        button.addEventListener(
          "click",
          () => {
            const ageInput = modal.querySelector(".bdt-age-input");
            let inputAge = parseInt(ageInput?.value, 10) || 0;

            if (button.classList.contains("data-val-yes")) {
              inputAge = 18;
            }
            if (button.classList.contains("data-val-no")) {
              requiredAge = 18;
              inputAge = 1;
            }

            if (inputAge >= requiredAge) {
              this.setLocalize();
              firedNotify += 1;
              if (window.bdtUIkit?.modal) {
                window.bdtUIkit.modal(this.element).hide();
              }
            } else {
              const msgText = document.querySelector(".modal-msg-text");
              if (msgText) {
                msgText.classList.remove("bdt-hidden");
              }

              if (redirectLink !== false) {
                window.location.replace(redirectLink);
              }
            }
          },
          { signal: this.signal }
        );
      });

      if (window.bdtUIkit?.util) {
        window.bdtUIkit.util.on(this.element, "hidden", () => {
          if (this.isEditMode) {
            return;
          }

          if (redirectLink === false && firedNotify <= 0) {
            setTimeout(() => {
              this.modalFire();
            }, 1500);
            return;
          }

          if (redirectLink !== false && firedNotify <= 0) {
            window.location.replace(redirectLink);
          }
        });
      }
    }

    setupCloseBtnDelay() {
      const { widgetId, delayTime } = this.settings;
      const modalElement = document.getElementById(widgetId);
      if (!modalElement) {
        return;
      }

      const closeButton = modalElement.querySelector("#bdt-modal-close-button");
      if (!closeButton) return;

      closeButton.style.display = "none";

      if (window.bdtUIkit?.util) {
        window.bdtUIkit.util.on(modalElement, "shown", () => {
          closeButton.style.display = "none";

          setTimeout(() => {
            closeButton.style.display = "";
            closeButton.style.opacity = "0";
            closeButton.style.transition = "opacity 0.3s";

            setTimeout(() => {
              closeButton.style.opacity = "1";
            }, 10);
          }, delayTime);
        });

        window.bdtUIkit.util.on(modalElement, "hide", () => {
          closeButton.style.display = "none";
        });
      }
    }

    init() {
      this.modalFire();
      this.setupAgeVerify();

      if (this.settings.closeBtnDelayShow) {
        this.setupCloseBtnDelay();
      }
    }

    destroy() {
      this.abortController.abort();
    }
  }

  const widgetAgeGate = (scope) => {
    const scopeElement = scope?.jquery ? scope[0] : scope;

    const modals = scopeElement.querySelectorAll(".bdt-age-gate");
    if (modals.length === 0) return;

    const isEditMode = Boolean(window.elementorFrontend?.isEditMode());

    modals.forEach((modal) => {
      const settingsData = modal.dataset.settings;
      if (!settingsData) return;

      let settings;
      try {
        settings = typeof settingsData === "string" ? JSON.parse(settingsData) : settingsData;
      } catch (e) {
        console.error("Failed to parse age gate settings:", e);
        return;
      }

      const ageGateModal = new AgeGateModal(modal, settings, isEditMode);
      ageGateModal.init();

      modal._ageGateInstance = ageGateModal;
    });
  };

  window.addEventListener("elementor/frontend/init", () => {
    if (window.elementorFrontend?.hooks) {
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/bdt-age-gate.default",
        widgetAgeGate
      );
    }
  });
})();

/**
 * End age-gate script
 */

(() => {
    'use strict';

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        const ModuleHandler = elementorModules.frontend.handlers.Base;

        const widgetDarkMode = ModuleHandler.extend({

            bindEvents() {
                this.run();
            },

            getDefaultSettings() {
                return {
                    left             : 'unset',
                    time             : '.5s',
                    mixColor         : '#fff',
                    backgroundColor  : '#fff',
                    saveInCookies    : false,
                    label            : '🌓',
                    autoMatchOsTheme : false,
                };
            },

            onElementChange: debounce(function () {
                this.run();
            }, 400),

            settings(key) {
                return this.getElementSettings(key);
            },

            setCookie(name, value, days) {
                let expires = '';
                if (days) {
                    const date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = '; expires=' + date.toUTCString();
                }
                document.cookie = name + '=' + (value || '') + expires + '; path=/';
            },

            getCookie(name) {
                const nameEQ = name + '=';
                for (let c of document.cookie.split(';')) {
                    c = c.trim();
                    if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length);
                }
                return null;
            },

            eraseCookie(name) {
                document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            },

            run() {
                const options = this.getDefaultSettings();

                options.time             = this.settings('time.size') / 1000 + 's';
                options.mixColor         = this.settings('mix_color');
                options.backgroundColor  = this.settings('default_background');
                options.saveInCookies    = this.settings('saveInCookies') === 'yes';
                options.autoMatchOsTheme = this.settings('autoMatchOsTheme') === 'yes';

                const dayToggleBg = this.settings('day_mode_icon_background');
                const darkToggleBg = this.settings('dark_mode_icon_background');
                if (dayToggleBg) {
                    options.buttonColorDark = dayToggleBg;
                }
                if (darkToggleBg) {
                    options.buttonColorLight = darkToggleBg;
                }

                const toRemove = [...document.body.classList].filter(c => /^bdt-dark-mode-\S+/.test(c));
                document.body.classList.remove(...toRemove);
                document.body.classList.add('bdt-dark-mode-position-' + this.settings('toggle_position'));

                const ignoreSelector = this.settings('ignore_element');
                if (ignoreSelector) {
                    document.querySelectorAll(ignoreSelector).forEach(el => el.classList.add('darkmode-ignore'));
                }

                if (!options.mixColor) return;

                document.querySelectorAll('.darkmode-toggle, .darkmode-layer, .darkmode-background')
                    .forEach(el => el.remove());

                const darkmode = new Darkmode(options);
                darkmode.showWidget();

                if (this.settings('default_mode') === 'dark') {
                    darkmode.toggle();
                    document.body.classList.add('darkmode--activated');
                    document.querySelectorAll('.darkmode-layer').forEach(el => {
                        el.classList.add('darkmode-layer--simple', 'darkmode-layer--expanded');
                    });
                } else {
                    document.body.classList.remove('darkmode--activated');
                    document.querySelectorAll('.darkmode-layer').forEach(el => {
                        el.classList.remove('darkmode-layer--simple', 'darkmode-layer--expanded');
                    });
                }

                const editMode = document.body.classList.contains('elementor-editor-active');

                if (!editMode && options.saveInCookies) {
                    document.querySelectorAll('.darkmode-toggle').forEach(el => {
                        el.addEventListener('click', () => {
                            this.eraseCookie('bdtDarkModeUserAction');
                            this.setCookie('bdtDarkModeUserAction', darkmode.isActivated() ? 'dark' : 'light', 10);
                        });
                    });

                    const userCookie = this.getCookie('bdtDarkModeUserAction');
                    if (userCookie !== null && userCookie !== 'undefined') {
                        if (userCookie === 'dark') {
                            darkmode.toggle();
                            document.body.classList.add('darkmode--activated');
                            document.querySelectorAll('.darkmode-layer').forEach(el => {
                                el.classList.add('darkmode-layer--simple', 'darkmode-layer--expanded');
                            });
                        } else {
                            document.body.classList.remove('darkmode--activated');
                            document.querySelectorAll('.darkmode-layer').forEach(el => {
                                el.classList.remove('darkmode-layer--simple', 'darkmode-layer--expanded');
                            });
                        }
                    }
                }
            },
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-dark-mode.default', ($scope) => {
            elementorFrontend.elementsHandler.addHandler(widgetDarkMode, { $element: $scope });
        });
    });

})();

/**
 * End Dark Mode widget script
 */

/**
 * Start animated gradient background widget script
 */

(() => {
  "use strict";

  window.addEventListener("elementor/frontend/init", () => {
    const ModuleHandler = elementorModules.frontend.handlers.Base;

    const AnimatedGradientBackground = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },

      getDefaultSettings: function () {
        return {
          allowHTML: true,
        };
      },

      onElementChange: debounce(function (prop) {
        if (prop.indexOf("element_pack_agbg_") !== -1) {
          this.run();
        }
      }, 400),

      settings: function (key) {
        return this.getElementSettings(`element_pack_agbg_${key}`);
      },

      parseColor: function (color) {
        // Convert RGBA to 6-digit HEX if alpha is 1
        if (/^rgba?\((\d+),\s*(\d+),\s*(\d+),?\s*([\d.]*)\)$/.test(color)) {
          const [, r, g, b, a = "1"] = color.match(
            /^rgba?\((\d+),\s*(\d+),\s*(\d+),?\s*([\d.]*)\)$/
          );
          const alpha = parseFloat(a);

          if (alpha === 1) {
            // Convert to 6-digit HEX format
            return `#${((1 << 24) + (parseInt(r, 10) << 16) + (parseInt(g, 10) << 8) + parseInt(b, 10))
              .toString(16)
              .slice(1)}`;
          }

          // Format as .decimal if alpha < 1
          const decimalPart = a.toString().split(".")[1] || "0";
          return `rgba(${r}, ${g}, ${b}, .${decimalPart})`;
        }

        // Convert 8-digit HEXA (#RRGGBBAA) to 6-digit HEX if alpha is 1
        if (/^#([A-Fa-f0-9]{8})$/.test(color)) {
          const rgba = color.match(/[A-Fa-f0-9]{2}/g).map((hex) => parseInt(hex, 16));
          const alpha = parseFloat((rgba[3] / 255).toFixed(2));

          if (alpha === 1) {
            return `#${color.slice(1, 7)}`; // Remove alpha part if 100% opaque
          }

          const decimalPart = alpha.toString().split(".")[1] || "0";
          return `rgba(${rgba[0]}, ${rgba[1]}, ${rgba[2]}, .${decimalPart})`;
        }

        // Convert 6-digit HEX to standard 6-digit HEX (lowercase)
        if (/^#([A-Fa-f0-9]{6})$/.test(color)) {
          return color.toLowerCase();
        }

        // Handle HSLA, standardizing alpha to .decimal format
        if (/^hsla?\((\d+),\s*([\d.]+)%,\s*([\d.]+)%,?\s*([\d.]*)\)$/.test(color)) {
          const [, h, s, l, a = "1"] = color.match(
            /^hsla?\((\d+),\s*([\d.]+)%,\s*([\d.]+)%,?\s*([\d.]*)\)$/
          );
          const alpha = parseFloat(a);

          if (alpha === 1) {
            return `hsl(${h}, ${s}%, ${l}%)`; // No alpha if fully opaque
          }

          const decimalPart = a.toString().split(".")[1] || "0";
          return `hsla(${h}, ${s}%, ${l}%, .${decimalPart})`;
        }

        // Return color as-is for named colors or other formats
        return color;
      },

      run: function () {
        if (this.settings("show") !== "yes") {
          return;
        }

        const sectionID = this.$element.data("id");
        const widgetContainer = document.querySelector(`.elementor-element-${sectionID}`);

        if (!widgetContainer) return;

        let canvasElement = widgetContainer.querySelector(".bdt-animated-gradient-background");

        if (!canvasElement) {
          canvasElement = document.createElement("canvas");
          canvasElement.id = `canvas-basic-${sectionID}`;
          canvasElement.className = "bdt-animated-gradient-background";
          widgetContainer.prepend(canvasElement);
        }

        const gradientID = canvasElement.id;

        const colorList = this.settings("color_list");
        const colors = colorList.map((color) => [
          this.parseColor(color.start_color),
          this.parseColor(color.end_color),
        ]);

        const direction = this.settings("direction") || "diagonal";
        const transitionSpeed = this.settings("transitionSpeed.size") || 5500;

        if (typeof Granim === "undefined") {
          console.error("Granim library is not loaded");
          return;
        }

        const granimInstance = new Granim({
          element: `#${gradientID}`,
          direction: direction,
          isPausedWhenNotInView: true,
          states: {
            "default-state": {
              gradients: colors,
              transitionSpeed: transitionSpeed,
            },
          },
        });
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/section",
      (scope) => {
        elementorFrontend.elementsHandler.addHandler(AnimatedGradientBackground, {
          $element: scope,
        });
      }
    );

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/container",
      (scope) => {
        elementorFrontend.elementsHandler.addHandler(AnimatedGradientBackground, {
          $element: scope,
        });
      }
    );
  });
})();

/**
 * End animated gradient background widget script
 */

;
(function ($, elementorFrontend) {
    'use strict';

    var STYLE_PRESETS = [
        'default',
        'custom-layout',
        'stripe',
        'ribbon',
        'corner',
        'flag',
        'pill',
        'bubble',
        'normal',
        'bookmark',
        'bookmark-vertical',
    ];

    $(window).on('elementor/frontend/init', function () {
        if (!elementorFrontend?.hooks || window.__epGlobalBadgeInitialized) {
            return;
        }

        window.__epGlobalBadgeInitialized = true;

        var GlobalBadge = elementorModules.frontend.handlers.Base.extend({

            bindEvents: function () {
                this.run();
            },

            settings: function (key) {
                return this.getElementSettings('ep_global_badge_' + key);
            },

            isEditMode: function () {
                return $('body').hasClass('elementor-editor-active')
                    || (elementorFrontend.isEditMode && elementorFrontend.isEditMode());
            },

            shouldRenderBadgeViaJs: function () {
                return this.isEditMode();
            },

            getSvgCode: function () {
                var code = this.getElementSettings('ep_global_badge_svg_code');

                if (typeof code === 'string' && code.trim()) {
                    return code;
                }

                return this.settings('svg_code') || '';
            },

            hasSvgLayer: function () {
                return this.settings('svg_layer') === 'yes' && !!this.getSvgCode();
            },

            getSvgInnerStyle: function () {
                var width, size, unit;

                if (!this.hasSvgLayer()) {
                    return '';
                }

                width = this.getElementSettings('ep_global_badge_svg_width');
                size = width && width.size !== undefined && width.size !== '' ? width.size : 100;
                unit = width && width.unit ? width.unit : 'px';

                return 'overflow:visible;min-inline-size:' + size + unit + ';min-block-size:' + size + unit + ';';
            },

            getEditor: function () {
                return window.elementor || (window.parent && window.parent.elementor);
            },

            getElementorHelpers: function () {
                var editor = this.getEditor();

                return editor && editor.helpers ? editor.helpers : null;
            },

            refreshBadgeAfterSvgIconLoad: function () {
                if (!this.shouldRenderBadgeViaJs() || this.settings('enable') !== 'yes') {
                    return;
                }

                this.updateBadgeMarkup();
                this.syncBadgePositionClass();
                this.syncBadgeStyleClass();
                this.syncBadgeLayoutStyles();
                this.ensureBadgePlacement();
                this.initFloatingIfNeeded();
                this.initBadgeLottie(this.getBadgeElement());
            },

            getSvgIconHtml: function (icon) {
                var helpers = this.getElementorHelpers(),
                    rendered,
                    svgValue = icon.value,
                    self = this;

                if (!svgValue || !svgValue.url) {
                    return '';
                }

                if (helpers && helpers.renderIcon) {
                    rendered = helpers.renderIcon(null, icon, { 'aria-hidden': true });

                    if (rendered && rendered.value) {
                        return rendered.value;
                    }
                }

                if (svgValue.id && helpers && helpers.fetchInlineSvg && this._pendingSvgIconId !== svgValue.id) {
                    this._pendingSvgIconId = svgValue.id;

                    helpers.fetchInlineSvg(svgValue.url, function (data) {
                        self._pendingSvgIconId = null;

                        if (!data) {
                            return;
                        }

                        if (helpers._inlineSvg) {
                            helpers._inlineSvg[svgValue.id] = data;
                        }

                        self.refreshBadgeAfterSvgIconLoad();
                    });
                }

                return '';
            },

            needsMarkupRefresh: function (prop) {
                var keys = [
                    'ep_global_badge_enable',
                    'ep_global_badge_text',
                    'ep_global_badge_icon',
                    'ep_global_badge_icon_type',
                    'ep_global_badge_icon_selected',
                    'ep_global_badge_lottie_json_source',
                    'ep_global_badge_lottie_json_path',
                    'ep_global_badge_lottie_upload_json_file',
                    'ep_global_badge_lottie_json_code',
                    'ep_global_badge_lottie_loop',
                    'ep_global_badge_lottie_speed',
                    'ep_global_badge_lottie_start_point',
                    'ep_global_badge_lottie_end_point',
                    'ep_global_badge_lottie_renderer',
                    'ep_global_badge_clip_path',
                    'ep_global_badge_clip_path_value',
                    'ep_global_badge_svg_layer',
                    'ep_global_badge_svg_code',
                ];

                return keys.some(function (key) {
                    return prop === key || prop.indexOf(key) === 0;
                });
            },

            getBadgeIconType: function () {
                return this.settings('icon_type') === 'lottie' ? 'lottie' : 'icon';
            },

            hasLottieContent: function () {
                var source = this.settings('lottie_json_source') || 'url',
                    uploadFile;

                if (source === 'url') {
                    return !!this.settings('lottie_json_path');
                }

                if (source === 'local') {
                    uploadFile = this.getElementSettings('ep_global_badge_lottie_upload_json_file');

                    if (typeof uploadFile === 'string' && uploadFile) {
                        return true;
                    }

                    if (uploadFile && (uploadFile.url || uploadFile.id)) {
                        return true;
                    }

                    return !!this.settings('lottie_upload_json_file');
                }

                if (source === 'custom') {
                    return !!(this.settings('lottie_json_code') || '').trim();
                }

                return false;
            },

            hasBadgeIconContent: function () {
                var icon;

                if (this.settings('icon') !== 'yes') {
                    return false;
                }

                if (this.getBadgeIconType() === 'lottie') {
                    return this.hasLottieContent();
                }

                icon = this.settings('icon_selected');

                return !!(icon && icon.value);
            },

            getLottieDataSettings: function () {
                var source = this.settings('lottie_json_source') || 'url',
                    jsonPath = '',
                    jsonCode = '',
                    isJsonUrl = true,
                    uploadFile,
                    startPoint,
                    endPoint,
                    speed;

                if (source === 'url') {
                    jsonPath = this.settings('lottie_json_path') || '';
                } else if (source === 'local') {
                    uploadFile = this.getElementSettings('ep_global_badge_lottie_upload_json_file');

                    if (typeof uploadFile === 'string') {
                        jsonPath = uploadFile;
                    } else if (uploadFile && uploadFile.url) {
                        jsonPath = uploadFile.url;
                    } else {
                        jsonPath = this.settings('lottie_upload_json_file') || '';
                    }
                } else if (source === 'custom') {
                    jsonCode = this.settings('lottie_json_code') || '';
                    isJsonUrl = false;
                }

                startPoint = this.getElementSettings('ep_global_badge_lottie_start_point');
                endPoint = this.getElementSettings('ep_global_badge_lottie_end_point');
                speed = this.getElementSettings('ep_global_badge_lottie_speed');

                return {
                    loop: this.settings('lottie_loop') === 'yes',
                    is_json_url: isJsonUrl,
                    json_path: jsonPath,
                    json_code: jsonCode,
                    speed: speed && speed.size !== undefined ? speed.size : 1,
                    play_action: 'autoplay',
                    start_point: startPoint && startPoint.size !== undefined ? startPoint.size : 0,
                    end_point: endPoint && endPoint.size !== undefined ? endPoint.size : 100,
                    lottie_renderer: this.settings('lottie_renderer') || 'svg',
                };
            },

            getLottieHtml: function () {
                var lottieId = 'bdt-ep-global-badge-lottie-' + this.getID(),
                    dataSettings;

                if (!this.hasLottieContent()) {
                    return '';
                }

                dataSettings = JSON.stringify(this.getLottieDataSettings())
                    .replace(/"/g, '&quot;');

                return '<div id="' + lottieId + '" class="bdt-lottie-container" data-settings="' + dataSettings + '" aria-hidden="true"></div>';
            },

            initBadgeLottie: function ($badge) {
                var self = this;

                if (!$badge || !$badge.length || typeof window.lottie === 'undefined') {
                    return;
                }

                $badge.find('.bdt-lottie-container').each(function () {
                    var lottieEl = this,
                        settings = {},
                        jsonPathUrl,
                        animation;

                    if (lottieEl._epGlobalBadgeLottie) {
                        lottieEl._epGlobalBadgeLottie.destroy();
                        lottieEl._epGlobalBadgeLottie = null;
                    }

                    try {
                        settings = lottieEl.dataset.settings ? JSON.parse(lottieEl.dataset.settings) : {};
                    } catch (e) {
                        settings = {};
                    }

                    if (settings.is_json_url == 1 || settings.is_json_url === true) {
                        if (settings.json_path) {
                            jsonPathUrl = settings.json_path;
                        }
                    } else if (settings.json_code) {
                        jsonPathUrl = URL.createObjectURL(new Blob([settings.json_code], { type: 'application/javascript' }));
                    }

                    if (!jsonPathUrl) {
                        return;
                    }

                    animation = window.lottie.loadAnimation({
                        container: lottieEl,
                        path: jsonPathUrl,
                        renderer: settings.lottie_renderer || 'svg',
                        autoplay: settings.play_action === 'autoplay',
                        loop: settings.loop,
                    });

                    if (jsonPathUrl.indexOf('blob:') === 0) {
                        URL.revokeObjectURL(jsonPathUrl);
                    }

                    animation.addEventListener('DOMLoaded', function () {
                        var firstFrame = animation.firstFrame,
                            totalFrame = animation.totalFrames,
                            getFrameNumberByPercent = function (percent) {
                                percent = Math.min(100, Math.max(0, percent));
                                return firstFrame + (totalFrame - firstFrame) * percent / 100;
                            },
                            startPoint = getFrameNumberByPercent(settings.start_point ?? 0),
                            endPoint = getFrameNumberByPercent(settings.end_point ?? 100);

                        animation.playSegments([startPoint, endPoint], true);
                    });

                    animation.setSpeed(settings.speed ?? 1);
                    lottieEl._epGlobalBadgeLottie = animation;
                });
            },

            needsLayoutRefresh: function (prop) {
                return /ep_global_badge_(offset_x|offset_y|rotate|horizontal|vertical|style)/.test(prop);
            },

            normalizeStyleSlug: function (style) {
                if ('custom-layout' === style) {
                    return 'custom-layout';
                }

                return 'default';
            },

            onElementChange: function (prop) {
                if (prop.indexOf('ep_global_badge_') === -1) {
                    return;
                }

                if (this.animeInstance) {
                    this.animeInstance.pause();
                    this.animeInstance = null;
                }

                if (prop === 'ep_global_badge_enable') {
                    this.syncWrapperClasses();

                    if (this.settings('enable') === 'yes') {
                        this.updateBadgeMarkup();
                        this.initFloatingIfNeeded();
                    } else {
                        this.removeAllBadges();
                    }

                    return;
                }

                if (this.needsMarkupRefresh(prop)) {
                    this.debouncedMarkupRefresh();
                    return;
                }

                if (this.needsLayoutRefresh(prop)) {
                    this.scheduleLayoutRefresh();
                    return;
                }

                if (this.isEditMode()) {
                    this.ensureBadgeExists();
                    this.ensureBadgePlacement();
                }
            },

            debouncedMarkupRefresh: debounce(function () {
                if (!this.shouldRenderBadgeViaJs()) {
                    return;
                }

                this.updateBadgeMarkup();
                this.ensureBadgePlacement();
                this.initFloatingIfNeeded();
                this.initBadgeLottie(this.getBadgeElement());
            }, 200),

            scheduleLayoutRefresh: function () {
                var self = this;

                if (this._layoutRaf) {
                    return;
                }

                this._layoutRaf = requestAnimationFrame(function () {
                    self._layoutRaf = null;
                    self.syncBadgePositionClass();
                    self.syncBadgeStyleClass();
                    self.syncBadgeLayoutStyles();
                    self.ensureBadgePlacement();
                });
            },

            onDestroy: function () {
                if (this._layoutRaf) {
                    cancelAnimationFrame(this._layoutRaf);
                    this._layoutRaf = null;
                }

                if (this.animeInstance) {
                    this.animeInstance.pause();
                    this.animeInstance = null;
                }
            },

            escapeHtml: function (text) {
                return $('<div/>').text(text || '').html();
            },

            getBadgeMountElement: function () {
                var id = this.getID(),
                    $mount;

                if (id) {
                    $mount = $('.elementor-element-' + id);

                    if ($mount.length) {
                        return $mount.first();
                    }
                }

                $mount = this.$element;

                if ($mount.hasClass('e-con-inner')) {
                    $mount = $mount.closest('.e-con.elementor-element');
                }

                if ($mount.hasClass('elementor-widget') || $mount.hasClass('e-con')) {
                    return $mount;
                }

                if ($mount.hasClass('elementor-element')) {
                    return $mount;
                }

                $mount = $mount.closest('.elementor-element.e-con, .elementor-element.elementor-widget');

                return $mount.length ? $mount.first() : this.$element;
            },

            getElementType: function () {
                return this.getBadgeMountElement().attr('data-element_type') || '';
            },

            isContainerElement: function () {
                return this.getElementType() === 'container';
            },

            isWidgetElement: function () {
                return this.getElementType() === 'widget';
            },

            getBadgeForAttr: function () {
                return String(this.getID() || '');
            },

            unwrapWidgetMount: function () {
                var $mount = this.getBadgeMountElement(),
                    $wrap = $mount.parent('.bdt-ep-global-badge-mount');

                if (!$wrap.length) {
                    return;
                }

                $wrap.children('.bdt-ep-global-badge').appendTo($mount);
                $mount.unwrap();
            },

            getBadgeInsertParent: function () {
                var $mount = this.getBadgeMountElement(),
                    $inner;

                if (this.isWidgetElement()) {
                    return $mount;
                }

                if (this.isContainerElement()) {
                    $inner = $mount.children('.e-con-inner').first();

                    if ($inner.length) {
                        return $inner;
                    }
                }

                $inner = $mount.children('.elementor-container, .elementor-widget-wrap').first();

                return $inner.length ? $inner : $mount;
            },

            syncWrapperClasses: function () {
                var $mount = this.getBadgeMountElement();

                if (this.settings('enable') === 'yes') {
                    $mount.addClass('bdt-ep-global-badge-active');
                } else {
                    $mount.removeClass('bdt-ep-global-badge-active');
                }
            },

            getBadgeElement: function () {
                var forId = this.getBadgeForAttr();

                if (!forId) {
                    return $();
                }

                return $('.bdt-ep-global-badge[data-ep-global-badge-for="' + forId + '"]').first();
            },

            dedupeBadges: function () {
                var forId = this.getBadgeForAttr(),
                    $badges;

                if (!forId) {
                    return;
                }

                $badges = $('.bdt-ep-global-badge[data-ep-global-badge-for="' + forId + '"]');

                if ($badges.length <= 1) {
                    return;
                }

                $badges.slice(1).remove();
            },

            removeAllBadges: function () {
                var forId = this.getBadgeForAttr();

                if (!forId) {
                    return;
                }

                $('.bdt-ep-global-badge[data-ep-global-badge-for="' + forId + '"]').remove();
            },

            /**
             * Container: badge inside .e-con-inner (or section/column inner wrap).
             * Widget: badge inside data-element_type="widget" element.
             */
            ensureBadgePlacement: function () {
                var $mount = this.getBadgeMountElement(),
                    $badge = this.getBadgeElement(),
                    $parent;

                if (!$badge.length) {
                    return;
                }

                if (this.isWidgetElement()) {
                    this.unwrapWidgetMount();

                    if ($badge.parent()[0] !== $mount[0]) {
                        $badge.detach().appendTo($mount);
                    }

                    return;
                }

                $parent = this.getBadgeInsertParent();

                if (!$parent.length) {
                    return;
                }

                if ($badge.parent()[0] !== $parent[0]) {
                    $badge.detach().appendTo($parent);
                }
            },

            rehomeBadgeIntoMount: function () {
                this.dedupeBadges();
                this.ensureBadgePlacement();
            },

            getPositionClass: function () {
                var horizontal = this.settings('horizontal') || 'right',
                    vertical = this.settings('vertical') || 'top',
                    map = {
                        left: { top: 'top-left', bottom: 'bottom-left' },
                        right: { top: 'top-right', bottom: 'bottom-right' },
                    };

                return (map[horizontal] && map[horizontal][vertical]) ? map[horizontal][vertical] : 'top-right';
            },

            getSliderCSSValue: function (key) {
                var data = this.getElementSettings('ep_global_badge_' + key);

                if (!data || data.size === '' || data.size === undefined || data.size === null) {
                    return null;
                }

                return String(data.size) + (data.unit || 'px');
            },

            getLayoutTransformValue: function () {
                var offsetX = this.getSliderCSSValue('offset_x') || '0px',
                    offsetY = this.getSliderCSSValue('offset_y') || '0px',
                    rotate = this.getSliderCSSValue('rotate') || '0deg';

                return 'translate(' + offsetX + ', ' + offsetY + ') rotate(' + rotate + ')';
            },

            clearWrapperLayoutVars: function () {
                var mount = this.getBadgeMountElement()[0];

                if (!mount) {
                    return;
                }

                mount.style.removeProperty('--ep-global-badge-offset-x');
                mount.style.removeProperty('--ep-global-badge-offset-y');
                mount.style.removeProperty('--ep-global-badge-rotate');
            },

            syncBadgeLayoutStyles: function () {
                var $badge = this.getBadgeElement();

                this.clearWrapperLayoutVars();

                if (!$badge.length) {
                    return;
                }

                $badge.css('transform', this.getLayoutTransformValue());
            },

            syncBadgePositionClass: function () {
                var $badge = this.getBadgeElement(),
                    positionClass,
                    positionClasses,
                    i;

                if (!$badge.length) {
                    return;
                }

                positionClass = this.getPositionClass();
                positionClasses = ['top-left', 'top-right', 'bottom-left', 'bottom-right', 'top', 'bottom', 'left', 'right'];

                for (i = 0; i < positionClasses.length; i++) {
                    $badge.removeClass('bdt-position-' + positionClasses[i]);
                }

                $badge.addClass('bdt-position-' + positionClass);
            },

            syncBadgeStyleClass: function () {
                var $badge = this.getBadgeElement(),
                    style,
                    i;

                if (!$badge.length) {
                    return;
                }

                style = this.normalizeStyleSlug(this.settings('style') || 'default');

                for (i = 0; i < STYLE_PRESETS.length; i++) {
                    $badge.removeClass('bdt-ep-global-badge--' + STYLE_PRESETS[i]);
                }

                $badge.addClass('bdt-ep-global-badge--' + style);
            },

            getIconHtml: function () {
                var icon = this.settings('icon_selected'),
                    helpers,
                    iconType = this.getBadgeIconType();

                if (!this.hasBadgeIconContent()) {
                    return '';
                }

                if (iconType === 'lottie') {
                    return this.getLottieHtml();
                }

                if (!icon || !icon.value) {
                    return '';
                }

                if (icon.library === 'svg') {
                    return this.getSvgIconHtml(icon);
                }

                helpers = this.getElementorHelpers();

                if (helpers && helpers.enqueueIconFonts && icon.library) {
                    helpers.enqueueIconFonts(icon.library);
                }

                return '<i class="' + icon.value + '" aria-hidden="true"></i>';
            },

            getBadgeIconWrapHtml: function (iconHtml) {
                if (!iconHtml) {
                    return '';
                }

                return '<span class="bdt-ep-global-badge-icon bdt-ep-global-badge-icon--' + this.getBadgeIconType() + '">' + iconHtml + '</span>';
            },

            updateBadgeMarkup: function () {
                var $mount = this.getBadgeMountElement(),
                    style, positionClass, text, innerStyle, svgHtml, iconHtml, textHtml, badgeHtml,
                    transform = this.getLayoutTransformValue(),
                    forId = this.getBadgeForAttr(),
                    $parent;

                this.removeAllBadges();

                if (this.settings('enable') !== 'yes') {
                    return;
                }

                style = this.normalizeStyleSlug(this.settings('style') || 'default');
                positionClass = this.getPositionClass();
                text = this.settings('text') || '';
                var hasSvg = this.hasSvgLayer();

                innerStyle = this.getSvgInnerStyle();

                if (this.settings('clip_path') === 'yes' && this.settings('clip_path_value')) {
                    innerStyle += 'clip-path:' + this.settings('clip_path_value') + ';-webkit-clip-path:' + this.settings('clip_path_value') + ';';
                }

                svgHtml = '';

                if (hasSvg) {
                    svgHtml = '<div class="bdt-ep-global-badge-svg" aria-hidden="true">' + this.getSvgCode() + '</div>';
                }

                iconHtml = this.getIconHtml();
                textHtml = '';

                if (text) {
                    textHtml = '<span class="bdt-ep-global-badge-text">' + this.escapeHtml(text) + '</span>';
                }

                badgeHtml =
                    '<div class="bdt-ep-global-badge bdt-position-small bdt-position-' + positionClass + ' bdt-ep-global-badge--' + style + (hasSvg ? ' bdt-ep-global-badge-has-svg' : '') + '" data-ep-global-badge-for="' + forId + '" style="transform:' + transform + '">' +
                        '<div class="bdt-ep-global-badge-inner"' + (innerStyle ? ' style="' + innerStyle + '"' : '') + '>' +
                            svgHtml +
                            '<div class="bdt-ep-global-badge-content">' +
                                this.getBadgeIconWrapHtml(iconHtml) +
                                textHtml +
                            '</div>' +
                        '</div>' +
                    '</div>';

                $parent = this.getBadgeInsertParent();

                if ($parent.length) {
                    $parent.append(badgeHtml);
                } else {
                    $mount.append(badgeHtml);
                }

                this.ensureBadgePlacement();
                this.initBadgeLottie(this.getBadgeElement());
            },

            ensureBadgeExists: function () {
                if (this.settings('enable') === 'yes' && !this.getBadgeElement().length && this.shouldRenderBadgeViaJs()) {
                    this.updateBadgeMarkup();
                }
            },

            getSpeedDuration: function (speedKey) {
                var speed = parseFloat(this.settings(speedKey + '.size')) || 1;
                return Math.max(300, Math.round(3000 / speed));
            },

            hasRangeValue: function (key) {
                var from = this.settings(key + '.sizes.from'),
                    to = this.settings(key + '.sizes.to');

                if (from === undefined && to === undefined) {
                    return false;
                }

                return Number(from) !== Number(to);
            },

            initFloatingIfNeeded: function () {
                var $badgeInner = this.getBadgeElement().find('.bdt-ep-global-badge-inner').first();

                if (!$badgeInner.length || this.settings('floating') !== 'yes') {
                    return;
                }

                this.initFloating($badgeInner[0]);
            },

            run: function () {
                if (this.settings('enable') !== 'yes') {
                    this.removeAllBadges();
                    this.syncWrapperClasses();
                    return;
                }

                if (this.isWidgetElement()) {
                    this.unwrapWidgetMount();
                }

                this.syncWrapperClasses();
                this.rehomeBadgeIntoMount();

                if (this.getBadgeElement().find('.bdt-ep-global-badge-svg').length) {
                    this.getBadgeElement().addClass('bdt-ep-global-badge-has-svg');
                }

                if (!this.getBadgeElement().length && this.shouldRenderBadgeViaJs()) {
                    this.updateBadgeMarkup();
                }

                this.syncBadgePositionClass();
                this.syncBadgeStyleClass();
                this.syncBadgeLayoutStyles();
                this.ensureBadgePlacement();
                this.initFloatingIfNeeded();
                this.initBadgeLottie(this.getBadgeElement());
            },

            initFloating: function (target) {
                var options, hasAnimation, self = this;

                if (typeof window.anime !== 'function') {
                    return;
                }

                options = {
                    targets: target,
                    direction: 'alternate',
                    loop: true,
                    easing: 'easeInOutSine',
                };

                hasAnimation = false;

                if (this.settings('floating_translate_toggle') === 'yes') {
                    var duration = this.getSpeedDuration('floating_translate_speed');

                    if (this.hasRangeValue('floating_translate_x')) {
                        options.translateX = {
                            value: [
                                this.settings('floating_translate_x.sizes.from') || 0,
                                this.settings('floating_translate_x.sizes.to') || 0,
                            ],
                            duration: duration,
                        };
                        hasAnimation = true;
                    }

                    if (this.hasRangeValue('floating_translate_y')) {
                        options.translateY = {
                            value: [
                                this.settings('floating_translate_y.sizes.from') || 0,
                                this.settings('floating_translate_y.sizes.to') || 0,
                            ],
                            duration: duration,
                        };
                        hasAnimation = true;
                    }
                }

                if (this.settings('floating_rotate_toggle') === 'yes') {
                    var rotateDuration = this.getSpeedDuration('floating_rotate_speed');

                    ['x', 'y', 'z'].forEach(function (axis) {
                        var key = 'floating_rotate_' + axis;

                        if (!self.hasRangeValue(key)) {
                            return;
                        }

                        var prop = 'rotate' + axis.toUpperCase();

                        options[prop] = {
                            value: [
                                self.settings(key + '.sizes.from') || 0,
                                self.settings(key + '.sizes.to') || 0,
                            ],
                            duration: rotateDuration,
                        };
                        hasAnimation = true;
                    });
                }

                if (this.settings('floating_opacity_toggle') === 'yes') {
                    var start = this.settings('floating_opacity_start.size'),
                        end = this.settings('floating_opacity_end.size');

                    if (start !== undefined || end !== undefined) {
                        options.opacity = {
                            value: [start !== undefined ? start : 1, end !== undefined ? end : 0.5],
                            duration: this.getSpeedDuration('floating_opacity_speed'),
                            easing: 'linear',
                        };
                        hasAnimation = true;
                    }
                }

                if (this.settings('floating_blur_toggle') === 'yes') {
                    var blurStart = this.settings('floating_blur_start.size') || 0,
                        blurEnd = this.settings('floating_blur_end.size') || 4;

                    options.filter = {
                        value: ['blur(' + blurStart + 'px)', 'blur(' + blurEnd + 'px)'],
                        duration: this.getSpeedDuration('floating_blur_speed'),
                    };
                    hasAnimation = true;
                }

                if (!hasAnimation) {
                    return;
                }

                this.animeInstance = window.anime(options);
            },
        });

        var addBadgeHandler = function ($scope) {
            var $target = $scope;

            if ($scope.hasClass('e-con-inner')) {
                $target = $scope.closest('.e-con.elementor-element');
            }

            if (!$target.length) {
                $target = $scope;
            }

            elementorFrontend.elementsHandler.addHandler(GlobalBadge, {
                $element: $target,
            });
        };

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', addBadgeHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/container', addBadgeHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/section', addBadgeHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/column', addBadgeHandler);

        // Containers enabled before script init (e.g. toggle in panel).
        elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
            var $mount = $scope.hasClass('e-con') ? $scope : $scope.find('.e-con').first();

            if (!$mount.length || !$mount.hasClass('bdt-ep-global-badge-yes')) {
                return;
            }

            if ($mount.find('.bdt-ep-global-badge[data-ep-global-badge-for]').length) {
                return;
            }

            addBadgeHandler($mount);
        });
    });

}(jQuery, window.elementorFrontend));

; (function ($, elementor) {
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            Tooltip;

        Tooltip = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    allowHTML: true,
                };
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('element_pack_widget_') !== -1) {
                    this.instance.destroy();
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('element_pack_widget_' + key);
            },

            run: function () {
                var options = this.getDefaultSettings();
                var widgetID = this.$element.data('id');
                var widgetContainer = document.querySelector('.elementor-element-' + widgetID);

                if (this.settings('tooltip_text')) {
                    options.content = EP_SAFE_HTML(this.settings('tooltip_text'));
                }

                options.arrow = !!this.settings('tooltip_arrow');
                options.followCursor = !!this.settings('tooltip_follow_cursor');

                if (this.settings('tooltip_placement')) {
                    options.placement = this.settings('tooltip_placement');
                }

                if (this.settings('tooltip_trigger')) {
                    if (this.settings('tooltip_custom_trigger')) {
                        options.triggerTarget = document.querySelector(this.settings('tooltip_custom_trigger'));
                    } else {
                        options.trigger = this.settings('tooltip_trigger');
                    }
                }
                if (this.settings('tooltip_animation')) {
                    if (this.settings('tooltip_animation') === 'fill') {
                        options.animateFill = true;
                    } else {
                        options.animation = this.settings('tooltip_animation');
                    }
                }
                if (this.settings('tooltip_x_offset.size') || this.settings('tooltip_y_offset.size')) {
                    options.offset = [this.settings('tooltip_x_offset.size') || 0, this.settings('tooltip_y_offset.size') || 0];
                }
                if (this.settings('tooltip')) {
                    options.theme = 'bdt-tippy-' + widgetID;
                    this.instance = tippy(widgetContainer, options);
                }
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(Tooltip, {
                $element: $scope
            });
        });
    });
})(jQuery, window.elementorFrontend);

; (function ($, elementor) {
    $(window).on('elementor/frontend/init', function () {
        let ModuleHandler = elementorModules.frontend.handlers.Base,
            CursorEffect;

        CursorEffect = ModuleHandler.extend({
            bindEvents: function () {
                this.run();
            },
            getDefaultSettings: function () {
                return {

                };
            },
            onElementChange: debounce(function (prop) {
                if (prop.indexOf('element_pack_cursor_effects_') !== -1) {
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('element_pack_cursor_effects_' + key);
            },

            copyCursorVarsToWrapper: function (wrapper, source) {
                var elementEl = this.$element[0];
                var computed = window.getComputedStyle(elementEl);
                var cursorVars = [
                    "cursor-ball-color",
                    "cursor-ball-size",
                    "cursor-circle-color",
                    "cursor-circle-size",
                    "cursor-text-label",
                    "cursor-image-size"
                ];

                cursorVars.forEach(function (name) {
                    var val = computed.getPropertyValue("--" + name).trim();

                    if (val) {
                        wrapper.style.setProperty("--" + name, val);
                    }
                });

                if (source === "image") {
                    var sizeAttr = this.$element.attr("data-bdt-cursor-image-size");
                    var sizeSetting = this.settings("image_size");
                    var imageSize = sizeAttr;

                    if (!imageSize && sizeSetting && sizeSetting.size) {
                        imageSize = sizeSetting.size + (sizeSetting.unit || "px");
                    }

                    if (!imageSize) {
                        imageSize = computed.getPropertyValue("--cursor-image-size").trim();
                    }

                    if (imageSize) {
                        wrapper.style.setProperty("--cursor-image-size", imageSize);
                    }
                }
            },

            applyImageSize: function (wrapper) {
                var imageSize = wrapper.style.getPropertyValue("--cursor-image-size").trim();

                if (!imageSize) {
                    imageSize = this.$element.attr("data-bdt-cursor-image-size");
                }

                if (!imageSize) {
                    var sizeSetting = this.settings("image_size");

                    if (sizeSetting && sizeSetting.size) {
                        imageSize = sizeSetting.size + (sizeSetting.unit || "px");
                    }
                }

                if (!imageSize) {
                    return;
                }

                wrapper.style.setProperty("--cursor-image-size", imageSize);
                wrapper.querySelectorAll(".bdt-cursor-image").forEach(function (img) {
                    img.style.width = imageSize;
                    img.style.height = imageSize;
                    img.style.maxWidth = "none";
                });
            },

            run: function () {
                var elementID = this.$element.data("id");
                var cursorWrapId = "bdt-cursor-effects-wrap-" + elementID;
                if (this.settings("show") !== "yes") {
                    var stale = document.getElementById(cursorWrapId);
                    if (stale) stale.remove();
                    return;
                }

                const disableOnMobile = this.settings("disable_on_mobile") === "yes";
                const isMobile = window.innerWidth <= 767;
                if (disableOnMobile && isMobile) {
                    var mobileStale = document.getElementById(cursorWrapId);
                    if (mobileStale) mobileStale.remove();
                    return;
                }

                var options = this.getDefaultSettings(),
                    elementContainer = ".elementor-element-" + elementID,
                    $element = this.$element,
                    cursorStyle = this.settings("style");
                var source = this.settings("source");
                var gsapId = "bdt-ep-cursor-gsap-" + elementID;
                var elementEl = $element[0];
                var isGsap = source === "image"
                    && this.settings("image_gsap_animation") === "yes"
                    && $element.hasClass("cursor-effects-smooth-animation-yes");

                if (!isGsap) {
                    var staleGsap = document.getElementById(gsapId);
                    if (staleGsap) staleGsap.remove();
                    var staleWrap = document.getElementById(cursorWrapId);
                    if (staleWrap) staleWrap.remove();
                    if (elementEl._bdtGsapTicker) {
                        gsap.ticker.remove(elementEl._bdtGsapTicker);
                        elementEl._bdtGsapTicker = null;
                    }
                    if (elementEl._bdtGsapHandlers) {
                        elementEl.removeEventListener("mousemove", elementEl._bdtGsapHandlers.move);
                        elementEl.removeEventListener("mouseleave", elementEl._bdtGsapHandlers.leave);
                        elementEl._bdtGsapHandlers = null;
                    }
                }

                if (isGsap) {
                    var staleWrap = document.getElementById(cursorWrapId);
                    if (staleWrap) staleWrap.remove();
                    var gsapImage = this.settings("image_src.url");
                    var gsapWidth = this.settings("gsap_width.size") || 385;
                    var gsapHeight = this.settings("gsap_height.size") || 280;

                    // Rebuild gallery on each run() so size/image changes apply
                    var existing = document.getElementById(gsapId);
                    if (existing) existing.remove();

                    // position:fixed at 0,0 — movement via transform x/y for GPU compositing
                    $("body").append(
                        '<div id="' + gsapId + '" class="bdt-cursor-gsap-gallery"' +
                        ' style="position:fixed;top:0;left:0;width:' + gsapWidth + 'px;height:' + gsapHeight + 'px;' +
                        'z-index:9999;overflow:hidden;pointer-events:none;will-change:transform;">' +
                        '<img class="bdt-cursor-image" src="' + gsapImage + '"' +
                        ' style="width:100%;height:100%;object-fit:cover;display:block;">' +
                        "</div>"
                    );

                    var galleryEl = document.getElementById(gsapId);

                    gsap.set(galleryEl, { autoAlpha: 0, xPercent: -50, yPercent: -50 });

                    if (elementEl._bdtGsapHandlers) {
                        elementEl.removeEventListener("mousemove", elementEl._bdtGsapHandlers.move);
                        elementEl.removeEventListener("mouseleave", elementEl._bdtGsapHandlers.leave);
                    }

                    var xTo = gsap.quickTo(galleryEl, "x", { duration: 0.5, ease: "power3.out" });
                    var yTo = gsap.quickTo(galleryEl, "y", { duration: 0.5, ease: "power3.out" });

                    // isVisible flag: ensures image only appears when mouse ACTUALLY MOVES
                    // inside the element — not when the element scrolls under a stationary cursor.
                    var isVisible = false;

                    var onMove = function (e) {
                        xTo(e.clientX);
                        yTo(e.clientY);
                        if (!isVisible) {
                            isVisible = true;
                            gsap.to(galleryEl, { autoAlpha: 1, duration: 0.4, ease: "power2.out" });
                        }
                    };
                    var onLeave = function () {
                        isVisible = false;
                        gsap.to(galleryEl, { autoAlpha: 0, duration: 0.3, ease: "power2.in" });
                    };

                    elementEl._bdtGsapHandlers = { move: onMove, leave: onLeave };
                    elementEl.addEventListener("mousemove", onMove);
                    elementEl.addEventListener("mouseleave", onLeave);

                    return; // Skip Cotton.js initialisation
                }

                var cursorInnerHtml = "";
                    if (source === "image") {
                        var image = this.settings("image_src.url");
                        cursorInnerHtml =
                            '<div class="bdt-cursor-effects"><div id="bdt-ep-cursor-ball-effects-' +
                            elementID +
                            '" class="ep-cursor-ball"><img class="bdt-cursor-image" src="' +
                            image +
                            '"></div></div>';
                    } else if (source === "icons") {
                        var svg = this.settings("icons.value.url");
                        var icons = this.settings("icons.value");
                        if (svg !== undefined) {
                            cursorInnerHtml =
                                '<div class="bdt-cursor-effects"><div id="bdt-ep-cursor-ball-effects-' +
                                elementID +
                                '" class="ep-cursor-ball"><img class="bdt-cursor-image" src="' +
                                svg +
                                '"></img></div></div>';
                        } else {
                            cursorInnerHtml =
                                '<div class="bdt-cursor-effects"><div id="bdt-ep-cursor-ball-effects-' +
                                elementID +
                                '" class="ep-cursor-ball"><i class="' +
                                icons +
                                ' bdt-cursor-icons"></i></div></div>';
                        }
                    } else if (source === "text") {
                        var text = this.settings("text_label");
                        cursorInnerHtml =
                            '<div class="bdt-cursor-effects"><div id="bdt-ep-cursor-ball-effects-' +
                            elementID +
                            '" class="ep-cursor-ball"><span class="bdt-cursor-text">' +
                            text +
                            "</span></div></div>";
                    } else {
                        cursorInnerHtml =
                            '<div class="bdt-cursor-effects ' +
                            cursorStyle +
                            '"><div id="bdt-ep-cursor-ball-effects-' +
                            elementID +
                            '" class="ep-cursor-ball"></div><div id="bdt-ep-cursor-circle-effects-' +
                            elementID +
                            '"  class="ep-cursor-circle"></div></div>';
                    }

                    if (cursorInnerHtml) {
                        document.getElementById(cursorWrapId) && document.getElementById(cursorWrapId).remove();
                        var wrapper = document.createElement("div");
                        wrapper.id = cursorWrapId;
                        wrapper.className = "bdt-cursor-effects-yes bdt-cursor-effects-body-wrap" + (source === "icons" ? " bdt-cursor-effects--icons" : "") + (source === "image" ? " bdt-cursor-effects--image" : "");
                        wrapper.innerHTML = cursorInnerHtml;
                        this.copyCursorVarsToWrapper(wrapper, source);
                        document.body.appendChild(wrapper);

                        if (source === "image") {
                            this.applyImageSize(wrapper);
                        }
                    }
                var cursorBallID =
                    "#bdt-ep-cursor-ball-effects-" + this.$element.data("id");
                const cursorBall = document.querySelector(cursorBallID);
                if (cursorBall) {
                    options.models = elementContainer;
                    options.speed = 1;
                    options.centerMouse = true;
                    new Cotton(cursorBall, options);

                    if (source === "default") {
                        const cursorCircleID =
                            "#bdt-ep-cursor-circle-effects-" + this.$element.data("id");
                        const cursorCircle = document.querySelector(cursorCircleID);
                        if (cursorCircle) {
                            options.models = elementContainer;
                            options.speed = this.settings("speed")
                                ? this.settings("speed.size")
                                : 0.725;
                            options.centerMouse = true;
                            new Cotton(cursorCircle, options);
                        }
                    }
                }
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(CursorEffect, {
                $element: $scope
            });
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/section', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(CursorEffect, {
                $element: $scope
            });
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/container', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(CursorEffect, {
                $element: $scope
            });
        });
    });
})(jQuery, window.elementorFrontend);

/**
 * Start Content Switcher widget script
 */

(() => {
    'use strict';

    const widgetContentSwitcher = (scope) => {
        const scopeElement = scope?.jquery ? scope[0] : scope;

        const contentSwitcher = scopeElement.querySelector('.bdt-content-switcher');
        if (!contentSwitcher) return;

        const parseData = (key) => {
            const raw = contentSwitcher.dataset[key];
            if (!raw) return undefined;
            try {
                return typeof raw === 'string' ? JSON.parse(raw) : raw;
            } catch (e) {
                console.error(`Failed to parse content switcher data-${key}:`, e);
                return undefined;
            }
        };

        const settings       = parseData('settings');
        const linkedSections = parseData('linkedSections');
        const linkedWidgets  = parseData('linkedWidgets');
        const editMode       = Boolean(elementorFrontend.isEditMode());

        const sectionContainerId = linkedSections ? `bdt-content-switcher-section-${linkedSections.id}` : null;

        const updateLinkedSectionActive = (index) => {
            if (!linkedSections?.positionUnchanged || !sectionContainerId) return;
            const items = document.querySelectorAll(`#${sectionContainerId} .bdt-switcher-section-content-inner`);
            items.forEach(item => item.classList.remove('bdt-active'));
            items[index]?.classList.add('bdt-active');
        };

        const updateLinkedWidgets = (activeIndex) => {
            if (!linkedWidgets) return;
            Object.entries(linkedWidgets.widgets).forEach(([idx, widgetId]) => {
                const widget = document.getElementById(widgetId);
                if (!widget) return;
                const isActive = +idx === activeIndex;
                widget.style.opacity = isActive ? '1' : '0';
                widget.style.display = isActive ? 'block' : 'none';
            });
        };

        if (linkedSections !== undefined && !editMode) {
            Object.entries(linkedSections.sections).forEach(([index, sectionId]) => {
                const idx              = +index;
                const switcherContainer = contentSwitcher.querySelectorAll('.bdt-switcher-content')[idx];
                const sectionContent   = document.getElementById(sectionId);

                if (!sectionContent) return;

                if (linkedSections.positionUnchanged !== true) {
                    const target = switcherContainer?.querySelector('.bdt-switcher-item-content-section');
                    if (switcherContainer && target) {
                        target.appendChild(sectionContent);
                    }
                } else {
                    const switchers       = contentSwitcher.querySelectorAll('.bdt-switcher-content');
                    const isPrimaryActive = contentSwitcher.querySelector('.bdt-primary')?.classList.contains('bdt-active');
                    const isIndexActive   = switchers[idx]?.classList.contains('bdt-active');
                    const activeClass     = (idx === 0 && isPrimaryActive) || (idx > 0 && isIndexActive) ? 'bdt-active' : '';

                    if (!document.getElementById(sectionContainerId)) {
                        sectionContent.parentElement.insertAdjacentHTML(
                            'beforeend',
                            `<div id="${sectionContainerId}" class="bdt-switcher bdt-switcher-section-content"></div>`
                        );
                    }

                    const container = document.getElementById(sectionContainerId);
                    container.appendChild(sectionContent);

                    const wrapper = document.createElement('div');
                    wrapper.className = `bdt-switcher-section-content-inner ${activeClass}`.trim();
                    sectionContent.parentNode.insertBefore(wrapper, sectionContent);
                    wrapper.appendChild(sectionContent);
                }
            });
        }

        if (linkedWidgets !== undefined && !editMode) {
            Object.entries(linkedWidgets.widgets).forEach(([index, widgetId]) => {
                const widget = document.getElementById(widgetId);
                if (!widget) return;

                const idx        = +index;
                const switchers  = contentSwitcher.querySelectorAll('.bdt-switcher-content');
                let isActive     = false;

                if (settings?.switcherStyle !== 'button') {
                    if (idx === 0) {
                        isActive = contentSwitcher.querySelector('.bdt-primary')?.classList.contains('bdt-active') ?? false;
                    } else if (idx === 1) {
                        isActive = contentSwitcher.querySelector('.bdt-secondary')?.classList.contains('bdt-active') ?? false;
                    }
                } else {
                    isActive = switchers[idx]?.classList.contains('bdt-active') ?? false;
                }

                Object.assign(widget.style, {
                    opacity         : isActive ? '1' : '0',
                    display         : isActive ? 'block' : 'none',
                    gridRowStart    : '1',
                    gridColumnStart : '1'
                });

                widget.parentElement.style.display = 'grid';
            });
        }

        if (settings?.switcherStyle !== 'button') {
            const checkbox        = contentSwitcher.querySelector('input[type="checkbox"]');
            const primarySwitcher = contentSwitcher.querySelector('.bdt-primary-switcher');
            const secondarySwitcher = contentSwitcher.querySelector('.bdt-secondary-switcher');
            const primaryIcon     = contentSwitcher.querySelector('.bdt-primary-icon');
            const secondaryIcon   = contentSwitcher.querySelector('.bdt-secondary-icon');
            const primaryText     = contentSwitcher.querySelector('.bdt-primary-text');
            const secondaryText   = contentSwitcher.querySelector('.bdt-secondary-text');
            const primaryContent  = contentSwitcher.querySelector('.bdt-switcher-content.bdt-primary');
            const secondaryContent = contentSwitcher.querySelector('.bdt-switcher-content.bdt-secondary');

            const toggleCheckboxState = (isChecked) => {
                [primarySwitcher, primaryIcon, primaryText, primaryContent].forEach(el => {
                    el?.classList.toggle('bdt-active', !isChecked);
                });
                [secondarySwitcher, secondaryIcon, secondaryText, secondaryContent].forEach(el => {
                    el?.classList.toggle('bdt-active', isChecked);
                });
                updateLinkedSectionActive(isChecked ? 1 : 0);
                updateLinkedWidgets(isChecked ? 1 : 0);
            };

            checkbox?.addEventListener('change', (e) => toggleCheckboxState(e.target.checked));

        } else {

            const tabs = contentSwitcher.querySelectorAll('.bdt-content-switcher-tab');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const id      = tab.id;
                    const content = contentSwitcher.querySelector(`.bdt-switcher-content[data-content-id="${id}"]`);
                    const index   = [...tab.parentElement.children].indexOf(tab);

                    [...tab.parentElement.children].forEach(t => t.classList.remove('bdt-active'));
                    tab.classList.add('bdt-active');

                    [...(tab.parentElement.nextElementSibling?.children ?? [])].forEach(c => c.classList.remove('bdt-active'));
                    content?.classList.add('bdt-active');

                    updateLinkedSectionActive(index);
                    updateLinkedWidgets(index);
                });
            });
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (window.elementorFrontend?.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-content-switcher.default', widgetContentSwitcher);
        }
    });

})();

/**
 * End Content Switcher widget script
 */

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

/**
 * Start scrollnav widget script
 */

( function( $, elementor ) {

	'use strict';

	const widgetScrollNav = function( $scope, $ ) {

		const $scrollnav = $scope.find( '.bdt-dotnav > li' );

        if ( ! $scrollnav.length ) {
            return;
        }

		const $tooltip = $scrollnav.find('> .bdt-tippy-tooltip'),
			widgetID = $scope.data('id');

		$tooltip.each( function() {
			tippy( this, {
				allowHTML: true,
				theme: 'bdt-tippy-' + widgetID
			});
		});

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-scrollnav.default', widgetScrollNav );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End scrollnav widget script
 */

// Common js for review card, review card carousel, review card grid, testimonial carousel, testimonial grid
(function ($, elementor) {
    "use strict";
    $(window).on("elementor/frontend/init", function () {
        /** Read more */
        const readMoreWidgetHandler = function readMoreWidgetHandler($scope) {
            if (jQuery($scope).find(".bdt-ep-read-more-text").length) {
                jQuery($scope)
                    .find(".bdt-ep-read-more-text")
                    .each(function () {
                        var words_limit_settings = $(this).data("read-more");
                        var max_words = words_limit_settings.words_length || 20; // Set the maximum number of words to show
                        var content = $(this).html(); // Get the full content
                        var cleanContent = content.replace(/<\/?[^>]+(>|$)/g, ""); // Removes all HTML tags
                        var words = cleanContent.split(/\s+/);

                        if (words.length > max_words) {
                            var short_content = words.slice(0, max_words).join(" "); // Get the first part of the content
                            var long_content = words.slice(max_words).join(" "); // Get the remaining part of the content

                            $(this).html(`
                          ${short_content}
                          <a href="#" class="bdt_read_more">...${ElementPackConfig.words_limit.read_more}</a>
                          <span class="bdt_more_text" style="display:none;">${long_content}</span>
                          <a href="#" class="bdt_read_less" style="display:none;">${ElementPackConfig.words_limit.read_less}</a>
                      `);

                            $(this)
                                .find("a.bdt_read_more")
                                .on('click', function (event) {
                                    event.preventDefault();
                                    $(this).hide(); // Hide the read more link
                                    $(this).siblings(".bdt_more_text").show(); // Show the more text
                                    $(this).siblings("a.bdt_read_less").show(); // Show the read less link
                                });

                            $(this)
                                .find("a.bdt_read_less")
                                .click(function (event) {
                                    event.preventDefault();
                                    $(this).hide(); // Hide the read less link
                                    $(this).siblings(".bdt_more_text").hide(); // Hide the more text
                                    $(this).siblings("a.bdt_read_more").show(); // Show the read more link
                                });
                        }
                    });
            }
        };

        const readMoreWidgetsHanlders = {
            "bdt-review-card.default": readMoreWidgetHandler,
            "bdt-review-card-carousel.default": readMoreWidgetHandler,
            "bdt-review-card-grid.default": readMoreWidgetHandler,
            "bdt-testimonial-carousel.default": readMoreWidgetHandler,
            "bdt-testimonial-carousel.bdt-twyla": readMoreWidgetHandler,
            "bdt-testimonial-carousel.bdt-vyxo": readMoreWidgetHandler,
            "bdt-testimonial-grid.default": readMoreWidgetHandler,
            "bdt-testimonial-slider.default": readMoreWidgetHandler,
            "bdt-testimonial-slider.bdt-single": readMoreWidgetHandler,
            "bdt-testimonial-slider.bdt-thumb": readMoreWidgetHandler,
        };

        $.each(readMoreWidgetsHanlders, function (widgetName, handlerFn) {
            elementorFrontend.hooks.addAction(
                "frontend/element_ready/" + widgetName,
                handlerFn
            );
        });
        /** /Read more */
    });
})(jQuery, window.elementorFrontend);

// end

/**
 * Start custom calculator widget script
 * Optimized version - No jQuery dependency
 */

(() => {
  "use strict";

  const parseNumericValue = (value) => {
    if (value === undefined || value === null || value === "") return null;
    const numValue = Number(value);
    return Number.isInteger(numValue) ? numValue : parseFloat(value);
  };

  const shouldIncludeInput = (input, processedRadios) => {
    const type = input.type;

    if (type === "radio") {
      if (!input.checked || processedRadios.has(input.name)) {
        return false;
      }
      processedRadios.add(input.name);
      return true;
    }

    if (type === "checkbox") {
      return input.checked;
    }

    return true; // text, hidden, number, select
  };

  const getVariableDataArray = (container) => {
    const selector = `.bdt-ep-advanced-calculator-field-wrap input[type="text"], 
                      .bdt-ep-advanced-calculator-field-wrap input[type="hidden"], 
                      .bdt-ep-advanced-calculator-field-wrap input[type="checkbox"], 
                      .bdt-ep-advanced-calculator-field-wrap input[type="radio"], 
                      .bdt-ep-advanced-calculator-field-wrap input[type="number"], 
                      .bdt-ep-advanced-calculator-field-wrap select`;

    const inputs = container.querySelectorAll(selector);
    const data = [];
    const variableArray = [];
    const processedRadios = new Set();
    let variableIndex = 1;

    inputs.forEach((input, index) => {
      if (!shouldIncludeInput(input, processedRadios)) {
        return;
      }

      const value = input.value;
      const realValue = parseNumericValue(value);
      const variable = `f${variableIndex}`;

      if (realValue !== null && !isNaN(realValue)) {
        variableArray.push({ variable, value: realValue });
      }

      data.push({
        type: input.type,
        index,
        value,
        variable,
        real_value: realValue,
      });

      variableIndex++;
    });

    return [data, variableArray];
  };

  const extractFormulaString = (settings) => {
    if (!settings?.formula) return null;
    const match = settings.formula.match(/'(.*)'/);
    return match ? match[1] : null;
  };

  const replaceVariablesInFormula = (formulaString, variableArray) => {
    const regexp = /f([1-9]\d{0,2}|1000)\b/g;
    const variableMap = new Map(
      variableArray.map(({ variable, value }) => [variable, value])
    );

    return formulaString.replace(regexp, (match) => {
      return variableMap.has(match) ? variableMap.get(match) : "null";
    });
  };

  const executeFormula = (formulaString) => {
    if (!window.formulajs) {
      console.error("FormulaJS library is not loaded");
      return null;
    }

    try {
      // Safer alternative to eval - use Function constructor
      const func = new Function("formulajs", `return formulajs.${formulaString}`);
      return func(window.formulajs);
    } catch (error) {
      console.error("Formula execution error:", error);
      return null;
    }
  };

  const showError = (container, duration = 5000) => {
    const errorElement = container.querySelector(".bdt-ep-advanced-calculator-error");
    if (!errorElement) return;

    errorElement.classList.remove("bdt-hidden");

    setTimeout(() => {
      errorElement.classList.add("bdt-hidden");
    }, duration);
  };

  const updateResult = (container, value) => {
    const resultElement = container.querySelector(".bdt-ep-advanced-calculator-result span");
    if (resultElement) {
      resultElement.textContent = value.toFixed(2);
    }
  };

  const processCalculation = (container, settings) => {
    const [, variableArray] = getVariableDataArray(container);

    if (variableArray.length === 0) return;

    const formulaString = extractFormulaString(settings);
    if (!formulaString) return;

    const processedFormula = replaceVariablesInFormula(formulaString, variableArray);
    const result = executeFormula(processedFormula);

    if (result !== null && !isNaN(result)) {
      updateResult(container, result);
    } else {
      showError(container);
    }
  };

  const widgetCalculator = (scope) => {
    const scopeElement = scope?.jquery ? scope[0] : scope;

    const customCalculator = scopeElement.querySelector(".bdt-ep-advanced-calculator");
    if (!customCalculator) return;

    const settingsData = customCalculator.dataset.settings;
    if (!settingsData) return;

    let settings;
    try {
      settings = typeof settingsData === "string" ? JSON.parse(settingsData) : settingsData;
    } catch (e) {
      console.error("Failed to parse calculator settings:", e);
      return;
    }

    if (!settings?.id) return;

    const container = document.querySelector(settings.id);
    if (!container) return;

    const form = container.querySelector(".bdt-ep-advanced-calculator-form");
    if (!form) return;

    const abortController = new AbortController();
    const signal = abortController.signal;

    if (settings.resultShow === "submit") {
      form.addEventListener(
        "submit",
        (e) => {
          e.preventDefault();
          processCalculation(container, settings);
        },
        { signal }
      );
    } else if (settings.resultShow === "change") {
      form.addEventListener(
        "change",
        (e) => {
          if (e.target.matches("input, select")) {
            processCalculation(container, settings);
          }
        },
        { signal }
      );

      form.addEventListener(
        "input",
        (e) => {
          if (e.target.matches('input[type="number"], input[type="text"]')) {
            processCalculation(container, settings);
          }
        },
        { signal }
      );
    }

    customCalculator._cleanupCalculator = () => {
      abortController.abort();
    };
  };

  window.addEventListener("elementor/frontend/init", () => {
    if (window.elementorFrontend?.hooks) {
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/bdt-advanced-calculator.default",
        widgetCalculator
      );
    }
  });
})();

/**
 * End custom calculator widget script
 */
/**
 * Start advanced counter widget script
 */

(() => {
  "use strict";

  const widgetAdvancedCounter = (scope) => {
    const scopeElement = scope?.jquery ? scope[0] : scope;

    const advancedCounter = scopeElement.querySelector(".bdt-ep-advanced-counter");
    if (!advancedCounter) return;

    const settingsData = advancedCounter.dataset.settings;
    if (!settingsData) return;

    let settings;
    try {
      settings = typeof settingsData === "string" ? JSON.parse(settingsData) : settingsData;
    } catch (e) {
      console.error("Failed to parse counter settings:", e);
      return;
    }

    const {
      countNumber = 0,
      countStart = 0,
      language,
      decimalPlaces = 0,
      duration = 0,
      useEasing = null,
      useGrouping = null,
      counterSeparator = "",
      decimalSymbol = "",
      counterPrefix = "",
      counterSuffix = "",
      id,
    } = settings;

    if (typeof CountUp === "undefined") {
      console.error("CountUp.js library is not loaded");
      return;
    }

    const options = {
      startVal: countStart,
      numerals: language,
      decimalPlaces,
      duration,
      useEasing: useEasing !== null,
      useGrouping: useGrouping !== null,
      separator: counterSeparator,
      decimal: decimalSymbol,
      prefix: counterPrefix,
      suffix: counterSuffix,
    };

    const startCounter = () => {
      const target =
        scopeElement.querySelector(".bdt-count-this") ||
        advancedCounter.querySelector(".bdt-count-this") ||
        (id ? document.getElementById(id) : null);

      if (!target) {
        return;
      }

      const counter = new CountUp(target, countNumber, options);

      if (!counter.error) {
        counter.start();
      } else {
        console.error("CountUp initialization error:", counter.error);
      }
    };

    if (typeof epObserveTarget === "function") {
      epObserveTarget(scopeElement, startCounter);
    } else {
      console.warn("epObserveTarget is not available, initializing counter immediately");
      startCounter();
    }
  };

  window.addEventListener("elementor/frontend/init", () => {
    if (window.elementorFrontend?.hooks) {
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/bdt-advanced-counter.default",
        widgetAdvancedCounter
      );
    }
  });
})();

/**
 * End advanced counter widget script
 */

/**
 * Start advanced divider widget script
 * Optimized version - No jQuery dependency
 */

(() => {
  "use strict";

  const widgetAdvancedDivider = (scope) => {
    const scopeElement = scope?.jquery ? scope[0] : scope;

    const advancedDivider = scopeElement.querySelector(".bdt-ep-advanced-divider");
    if (!advancedDivider) return;

    const settingsData = advancedDivider.dataset.settings;
    if (!settingsData) return;

    let settings;
    try {
      settings = typeof settingsData === "string" ? JSON.parse(settingsData) : settingsData;
    } catch (e) {
      console.error("Failed to parse divider settings:", e);
      return;
    }

    if (!window.bdtUIkit?.svg) {
      console.error("bdtUIkit.svg is not available");
      return;
    }

    const imgElement = advancedDivider.querySelector("img");
    if (!imgElement) return;

    const { animation = false, loop = false } = settings;

    if (animation === true) {
      if (typeof epObserveTarget === "function") {
        epObserveTarget(
          scopeElement,
          () => {
            window.bdtUIkit.svg(imgElement, {
              strokeAnimation: true,
            });
          },
          { loop }
        );
      } else {
        console.warn("epObserveTarget is not available, initializing divider immediately");
        window.bdtUIkit.svg(imgElement, {
          strokeAnimation: true,
        });
      }
    } else {
      window.bdtUIkit.svg(imgElement);
    }
  };

  window.addEventListener("elementor/frontend/init", () => {
    if (window.elementorFrontend?.hooks) {
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/bdt-advanced-divider.default",
        widgetAdvancedDivider
      );
    }
  });
})();

/**
 * End advanced divider widget script
 */


/**
 * Start advanced Google Maps widget script
 */

(() => {
  "use strict";

  const debounce = (func, wait = 300) => {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  };

  const sanitizeContent = (content) => {
    const div = document.createElement("div");
    div.innerHTML = content;

    div.querySelectorAll("script").forEach((script) => script.remove());

    const dangerousAttrs = [
      "onclick", "onload", "onerror", "onmouseover", "onmouseout",
      "onmouseenter", "onmouseleave", "onfocus", "onblur", "onchange",
      "onsubmit", "onkeydown", "onkeyup", "onkeypress"
    ];

    div.querySelectorAll("*").forEach((element) => {
      dangerousAttrs.forEach((attr) => element.removeAttribute(attr));
    });

    return div.innerHTML;
  };

  const createMarkerContent = (marker, markerImage = "") => {
    const listMarker = markerImage
      ? `<div class="bdt-map-tooltip-top-image"><img class="bdt-map-image" src="${markerImage}" alt="" /></div>`
      : "";

    const markupWebsite = marker.website
      ? `<a href="${marker.website}">${marker.website}</a>`
      : "";

    const markupPhone = marker.phone
      ? `<a href="tel:${marker.phone}">${marker.phone}</a>`
      : "";

    const markupContent = marker.content
      ? `<span class="bdt-tooltip-content">${sanitizeContent(marker.content)}</span><br>`
      : "";

    const markupPlace = marker.place
      ? `<h5 class="bdt-tooltip-place">${marker.place}</h5>`
      : "";

    const markupTitle = marker.title
      ? `<h4 class="bdt-tooltip-title">${marker.title}</h4>`
      : "";

    return `<div class="bdt-map-tooltip-view">
              <div class="bdt-map-tooltip-view-inner">
                ${listMarker}
                <div class="bdt-map-tooltip-bottom-footer">
                  ${markupTitle}
                  ${markupPlace}
                  ${markupContent}
                  ${markupWebsite}
                  ${markupPhone}
                </div>
              </div>
            </div>`;
  };

  const applyMapStyles = (map, element, cachedStyles = null) => {
    const styleData = element.dataset.map_style;
    if (!styleData) return null;

    if (cachedStyles) {
      map.setOptions({ styles: cachedStyles });
      return cachedStyles;
    }

    try {
      const styles = typeof styleData === "string" ? JSON.parse(styleData) : styleData;
      map.setOptions({ styles });
      return styles;
    } catch (e) {
      console.error("Error parsing map styles:", e);
      return null;
    }
  };

  const filterMapLists = (listItems, searchValue) => {
    const lowerSearch = searchValue.toLowerCase();
    listItems.forEach((item) => {
      const text = item.textContent.toLowerCase();
      item.style.display = text.includes(lowerSearch) ? "" : "none";
    });
  };

  const createMapMarker = async (map, options) => {
    const { position, title, icon } = options;

    if (window.google?.maps?.marker?.AdvancedMarkerElement) {
      try {
        const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");

        const markerOptions = {
          map,
          position,
          title,
          gmpClickable: true,
        };

        if (icon) {
          const img = document.createElement("img");
          img.src = icon;
          img.style.width = "32px";
          img.style.height = "32px";
          markerOptions.content = img;
        } else {
          markerOptions.content = new PinElement();
        }

        return new AdvancedMarkerElement(markerOptions);
      } catch (e) {
        console.warn("AdvancedMarkerElement failed, falling back to Marker:", e);
      }
    }

    const markerOptions = {
      position,
      map,
      title,
    };

    if (icon) {
      markerOptions.icon = icon;
    }

    return new google.maps.Marker(markerOptions);
  };

  const initMap = async (
    gmapWrapper,
    mapSettings,
    markers,
    mapLists,
    mapSearchForm,
    mapSearchTextBox,
    mapForm,
    advancedGoogleMap
  ) => {
    gmapWrapper.removeAttribute("style");

    if (!window.google?.maps) {
      console.error("Google Maps API is not loaded");
      return;
    }

    const mapOptions = {
      center: {
        lat: parseFloat(mapSettings.lat),
        lng: parseFloat(mapSettings.lng),
      },
      zoom: mapSettings.zoom || 15,
      mapTypeId:
        google.maps.MapTypeId[mapSettings.mapTypeId?.toUpperCase()] ||
        google.maps.MapTypeId.ROADMAP,
      zoomControl: mapSettings.zoomControl !== undefined ? mapSettings.zoomControl : true,
      zoomControlOptions: {
        position: google.maps.ControlPosition.TOP_LEFT,
      },
      mapTypeControl: mapSettings.mapTypeControl !== undefined ? mapSettings.mapTypeControl : true,
      streetViewControl: mapSettings.streetViewControl !== undefined ? mapSettings.streetViewControl : true,
      scrollwheel: mapSettings.scrollwheel !== undefined ? mapSettings.scrollwheel : true,
      fullscreenControl: true,
      mapId: "DEMO_MAP_ID", // Required for AdvancedMarkerElement
    };

    const googleMap = new google.maps.Map(advancedGoogleMap, mapOptions);
    const infoWindow = new google.maps.InfoWindow();
    const allMarkers = [];
    let temporaryMarker = null;
    let cachedStyles = null;

    cachedStyles = applyMapStyles(googleMap, advancedGoogleMap, cachedStyles);

    for (const markerData of markers) {
      const markerImage = markerData.image || "";
      const markerPosition = {
        lat: parseFloat(markerData.lat),
        lng: parseFloat(markerData.lng),
      };

      const marker = await createMapMarker(googleMap, {
        position: markerPosition,
        title: markerData.title,
        icon: markerData.icon,
      });

      if (marker instanceof google.maps.Marker) {
        marker.addListener("click", () => {
          const content = createMarkerContent(markerData, markerImage);
          infoWindow.setContent(content);
          infoWindow.open(googleMap, marker);
        });
      } else {
        marker.addEventListener("gmp-click", () => {
          const content = createMarkerContent(markerData, markerImage);
          infoWindow.setContent(content);
          infoWindow.open(googleMap, marker);
        });
      }

      allMarkers.push(marker);
    }

    const abortController = new AbortController();
    const { signal } = abortController;

    if (advancedGoogleMap.dataset.map_geocode) {
      mapForm?.addEventListener(
        "submit",
        async (e) => {
          e.preventDefault();
          const geocoder = new google.maps.Geocoder();
          const address = mapSearchTextBox.value.trim();

          if (!address) return;

          geocoder.geocode({ address }, async (results, status) => {
            if (status === "OK" && results[0]) {
              const location = results[0].geometry.location;
              googleMap.setCenter(location);

              if (temporaryMarker) {
                temporaryMarker.map = null;
              }

              temporaryMarker = await createMapMarker(googleMap, {
                position: location,
                title: address,
              });
            } else {
              console.warn("Geocode was not successful:", status);
            }
          });
        },
        { signal }
      );
    }

    mapLists.forEach((listItem) => {
      listItem.addEventListener(
        "click",
        async () => {
          const settingsData = listItem.dataset.settings;
          if (!settingsData) return;

          let dataSettings;
          try {
            dataSettings = typeof settingsData === "string" ? JSON.parse(settingsData) : settingsData;
          } catch (e) {
            console.error("Failed to parse list item settings:", e);
            return;
          }

          const position = {
            lat: parseFloat(dataSettings.lat),
            lng: parseFloat(dataSettings.lng),
          };

          googleMap.setCenter(position);
          googleMap.setZoom(mapSettings.zoom);

          if (temporaryMarker) {
            temporaryMarker.map = null;
          }

          const markerImage = dataSettings.image?.[0] || "";

          temporaryMarker = await createMapMarker(googleMap, {
            position,
            title: dataSettings.title,
            icon: dataSettings.icon,
          });

          const content = createMarkerContent(dataSettings, markerImage);
          infoWindow.setContent(content);
          infoWindow.open(googleMap, temporaryMarker);

          if (cachedStyles) {
            googleMap.setOptions({ styles: cachedStyles });
          }
        },
        { signal }
      );
    });

    const debouncedFilter = debounce((searchValue) => {
      filterMapLists(mapLists, searchValue);
    }, 300);

    if (mapSearchForm) {
      mapSearchForm.addEventListener(
        "submit",
        (e) => {
          e.preventDefault();
          const searchValue = mapSearchTextBox.value;
          filterMapLists(mapLists, searchValue);
        },
        { signal }
      );
    }

    if (mapSearchTextBox) {
      mapSearchTextBox.addEventListener(
        "input",
        (e) => {
          debouncedFilter(e.target.value);
        },
        { signal }
      );
    }

    advancedGoogleMap._cleanupMap = () => {
      abortController.abort();
      if (temporaryMarker) {
        if (temporaryMarker.map !== undefined) {
          temporaryMarker.map = null;
        } else if (temporaryMarker.setMap) {
          temporaryMarker.setMap(null);
        }
      }
      allMarkers.forEach((marker) => {
        if (marker.map !== undefined) {
          marker.map = null;
        } else if (marker.setMap) {
          marker.setMap(null);
        }
      });
    };
  };

  const widgetAdvancedGoogleMap = (scope) => {
    const scopeElement = scope?.jquery ? scope[0] : scope;

    const advancedGoogleMap = scopeElement.querySelector(".bdt-advanced-gmap");
    if (!advancedGoogleMap) return;

    const gmapWrapper = scopeElement.querySelector(".bdt-advanced-map");
    const mapLists = scopeElement.querySelectorAll("ul.bdt-gmap-lists div.bdt-gmap-list-item");
    const mapSearchForm = scopeElement.querySelector(".bdt-search");
    const mapSearchTextBox = scopeElement.querySelector(".bdt-search-input");
    const mapForm = scopeElement.querySelector(".bdt-gmap-search-wrapper > form");

    const mapSettingsData = advancedGoogleMap.dataset.map_settings;
    const markersData = advancedGoogleMap.dataset.map_markers;

    if (!mapSettingsData || !markersData) {
      console.error("Map settings or markers data is missing");
      return;
    }

    let mapSettings, markers;
    try {
      mapSettings = typeof mapSettingsData === "string" ? JSON.parse(mapSettingsData) : mapSettingsData;
      markers = typeof markersData === "string" ? JSON.parse(markersData) : markersData;
    } catch (e) {
      console.error("Failed to parse map data:", e);
      return;
    }

    const initializeMap = () => {
      initMap(
        gmapWrapper,
        mapSettings,
        markers,
        mapLists,
        mapSearchForm,
        mapSearchTextBox,
        mapForm,
        advancedGoogleMap
      );
    };

    if (window.elementorFrontend?.isEditMode()) {
      initializeMap();
    } else {
      if (document.readyState === "complete") {
        initializeMap();
      } else {
        window.addEventListener("load", initializeMap, { once: true });
      }
    }
  };

  window.addEventListener("elementor/frontend/init", () => {
    if (window.elementorFrontend?.hooks) {
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/bdt-advanced-gmap.default",
        widgetAdvancedGoogleMap
      );
    }
  });
})();

/**
 * End advanced Google Maps widget script
 */

/**
 * Start advanced heading widget script
 * Optimized version - No jQuery dependency
 */

(() => {
  "use strict";

  const generateRandomColor = () => {
    const color = Math.floor(Math.random() * 16777215).toString(16);
    return `#${color.padStart(6, "0")}`;
  };

  const applyMultiColorEffect = (headingInner) => {
    const text = headingInner.textContent.trim();
    if (!text) return;

    const words = text.split(/\s+/);

    const fragment = document.createDocumentFragment();

    words.forEach((word) => {
      const span = document.createElement("span");
      span.textContent = word + " ";
      span.style.color = generateRandomColor();
      fragment.appendChild(span);
    });

    headingInner.textContent = "";
    headingInner.appendChild(fragment);
  };

  const widgetAdvancedHeading = (scope) => {
    const scopeElement = scope?.jquery ? scope[0] : scope;

    const advHeading = scopeElement.querySelector(".bdt-ep-advanced-heading");
    if (!advHeading) return;

    const headingInner = advHeading.querySelector(".bdt-ep-advanced-heading-main-title-inner");
    if (!headingInner) return;

    const settingsData = advHeading.dataset.settings;
    if (!settingsData) return;

    let settings;
    try {
      settings = typeof settingsData === "string" ? JSON.parse(settingsData) : settingsData;
    } catch (e) {
      console.error("Failed to parse heading settings:", e);
      return;
    }

    if (settings.titleMultiColor === "yes") {
      applyMultiColorEffect(headingInner);
    }
  };

  window.addEventListener("elementor/frontend/init", () => {
    if (window.elementorFrontend?.hooks) {
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/bdt-advanced-heading.default",
        widgetAdvancedHeading
      );
    }
  });
})();

/**
 * End advanced heading widget script
 */

/**
 * Start advanced icon box widget script
 * Optimized version - No jQuery dependency
 */

(() => {
  "use strict";

  const widgetAdvancedIconBox = (scope) => {
    const scopeElement = scope?.jquery ? scope[0] : scope;

    const iconBox = scopeElement.querySelector(".bdt-ep-advanced-icon-box");
    if (!iconBox) return;

    const separatorImg = iconBox.querySelector(".bdt-ep-advanced-icon-box-separator-wrap > img");
    if (!separatorImg) return;

    if (!window.bdtUIkit?.svg) {
      console.error("bdtUIkit.svg is not available");
      return;
    }

    if (typeof epObserveTarget === "function") {
      epObserveTarget(scopeElement, () => {
        window.bdtUIkit.svg(separatorImg, {
          strokeAnimation: true,
        });
      });
    } else {
      console.warn("epObserveTarget is not available, initializing icon box immediately");
      window.bdtUIkit.svg(separatorImg, {
        strokeAnimation: true,
      });
    }
  };

  window.addEventListener("elementor/frontend/init", () => {
    if (window.elementorFrontend?.hooks) {
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/bdt-advanced-icon-box.default",
        widgetAdvancedIconBox
      );
    }
  });
})();

/**
 * End advanced icon box widget script
 */

/**
 * Start bdt advanced image gallery widget script
 * Optimized version - No jQuery dependency
 */

(() => {
  "use strict";

  const widgetAdvancedImageGallery = (scope) => {
    const scopeElement = scope?.jquery ? scope[0] : scope;

    const advancedImageGallery = scopeElement.querySelector(".bdt-ep-advanced-image-gallery");
    if (!advancedImageGallery) return;

    const settingsData = advancedImageGallery.dataset.settings;
    if (!settingsData) return;

    let settings;
    try {
      settings = typeof settingsData === "string" ? JSON.parse(settingsData) : settingsData;
    } catch (e) {
      console.error("Failed to parse image gallery settings:", e);
      return;
    }

    if (settings.tiltShow === true) {
      if (typeof VanillaTilt === "undefined") {
        console.error("VanillaTilt library is not loaded");
        return;
      }

      const tiltElements = document.querySelectorAll(`${settings.id} [data-tilt]`);

      if (tiltElements.length > 0) {
        VanillaTilt.init(tiltElements);
      }
    }
  };

  window.addEventListener("elementor/frontend/init", () => {
    if (window.elementorFrontend?.hooks) {
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/bdt-advanced-image-gallery.default",
        widgetAdvancedImageGallery
      );
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/bdt-advanced-image-gallery.bdt-carousel",
        widgetAdvancedImageGallery
      );
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/bdt-acf-gallery.default",
        widgetAdvancedImageGallery
      );
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/bdt-acf-gallery.bdt-carousel",
        widgetAdvancedImageGallery
      );
    }
  });
})();

/**
 * End bdt advanced image gallery widget script
 */
/**
 * Start advanced progress bar widget script
 * Optimized version - No jQuery dependency
 */

(() => {
  "use strict";

  const widgetAdvancedProgressBar = (scope) => {
    const scopeElement = scope?.jquery ? scope[0] : scope;

    const progressBarItems = scopeElement.querySelectorAll(
      ".bdt-ep-advanced-progress-bar-item"
    );

    if (progressBarItems.length === 0) return;

    if (typeof epObserveTarget !== "function") {
      console.warn("epObserveTarget is not available");
      return;
    }

    progressBarItems.forEach((item, index) => {
      epObserveTarget(item, () => {
        const progressBars = item.querySelectorAll(".bdt-ep-advanced-progress-bar-fill");

        progressBars.forEach((barFill) => {
          const maxValue = parseFloat(barFill.dataset.maxValue) || 100;
          const fillValue = parseFloat(barFill.dataset.width) || 0;
          const animationDelay = parseFloat(barFill.dataset.animationDelay) || 0;

          const percentage = (fillValue * 100) / maxValue;

          barFill.style.width = `${percentage}%`;
          barFill.style.transitionDelay = `${index * animationDelay}s`;

          const percentageDisplay = barFill.querySelector(
            ".bdt-ep-advanced-progress-bar-parcentage"
          );

          if (percentageDisplay) {
            percentageDisplay.style.transform = "scale(1)";
          }
        });
      });
    });
  };

  window.addEventListener("elementor/frontend/init", () => {
    if (window.elementorFrontend?.hooks) {
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/bdt-advanced-progress-bar.default",
        widgetAdvancedProgressBar
      );
    }
  });
})();

/**
 * End advanced progress bar widget script
 */

/**
 * Start animated heading widget script
 */

(() => {
    'use strict';

    const widgetAnimatedHeading = (scope) => {
        const scopeEl = scope instanceof jQuery ? scope[0] : scope;

        const headingContainerEl = scopeEl.querySelector('.bdt-heading');
        const headingChildEl     = scopeEl.querySelector('.bdt-heading > *');

        if (!headingChildEl) return;

        const animatedHeadingEl = headingChildEl.querySelector('.bdt-animated-heading');

        let settings;
        try {
            const raw = animatedHeadingEl?.dataset.settings;
            settings = raw ? JSON.parse(raw) : null;
        } catch (e) {
            settings = null;
        }

        if (!settings) {
            console.warn('BDT Animated Heading: No settings found');
            return;
        }

        // ── Layout: animated (Morphext — jQuery plugin required) ─────────────
        if (settings.layout === 'animated') {
            headingContainerEl.style.display = 'block'; // Fix full list on first loading
            jQuery(animatedHeadingEl).Morphext(settings);

        // ── Layout: typed ────────────────────────────────────────────────────
        } else if (settings.layout === 'typed') {
            new Typed('#' + animatedHeadingEl.id, settings);

        // ── Layout: split_text ───────────────────────────────────────────────
        } else if (settings.layout === 'split_text') {
            const splitTextTimeline = gsap.timeline();
            const mySplitText = new SplitText(headingChildEl, { type: 'chars, words, lines' });

            gsap.set(headingChildEl, { perspective: settings.anim_perspective });

            // Reset before re-splitting
            splitTextTimeline.clear().time(0);
            mySplitText.revert();

            mySplitText.split({ type: 'chars, words, lines' });

            const stringType = settings.animation_on === 'lines' ? mySplitText.lines
                : settings.animation_on === 'chars'              ? mySplitText.chars
                : mySplitText.words;

            const animationConfig = {
                opacity        : 0,
                scale          : settings.anim_scale,
                y              : settings.anim_rotation_y,
                rotationX      : settings.anim_rotation_x,
                transformOrigin: settings.anim_transform_origin,
            };

            if (settings.anim_repeat) {
                animationConfig.repeat = -1;
                if (settings.anim_yoyo) {
                    animationConfig.yoyo = true;
                }
            }

            splitTextTimeline.staggerFrom(stringType, 0.5, animationConfig, settings.anim_duration);

        // ── Layout: gsap_slide ───────────────────────────────────────────────
        } else if (settings.layout === 'gsap_slide') {
            const maskEl = headingContainerEl.querySelector('.bdt-gsap-mask');

            let bgColor = settings.gsap_slide_mask_color || '';

            if (!bgColor) {
                const sectionEl = headingContainerEl.closest('.elementor-section');
                if (sectionEl) {
                    bgColor = window.getComputedStyle(sectionEl).backgroundColor;
                }

                if (!bgColor || bgColor === 'rgba(0, 0, 0, 0)' || bgColor === 'transparent') {
                    bgColor = window.getComputedStyle(headingContainerEl).backgroundColor;
                }

                if (!bgColor || bgColor === 'rgba(0, 0, 0, 0)' || bgColor === 'transparent') {
                    bgColor = window.getComputedStyle(document.body).backgroundColor || '#262626';
                }
            }

            if (maskEl) {
                Object.assign(maskEl.style, {
                    position       : 'absolute',
                    top            : '0',
                    left           : '100%',
                    width          : '200%',
                    height         : '100%',
                    maxWidth       : 'none',
                    backgroundImage: `linear-gradient(to right, transparent, ${bgColor} 50%, ${bgColor})`,
                    pointerEvents  : 'none',
                });

                const slideConfig = {
                    xPercent    : settings.gsap_slide_xpercent  ?? -100,
                    duration    : settings.gsap_slide_duration   ?? 3,
                    repeat      : -1,
                    repeatDelay : settings.gsap_slide_repeat_delay ?? 0.5,
                    ease        : 'none',
                };

                if (settings.gsap_slide_yoyo) {
                    slideConfig.yoyo = true;
                }

                gsap.to(maskEl, slideConfig);

            } else {
                console.warn('BDT Animated Heading: Mask element not found for GSAP Slide');
            }
        }

        headingChildEl.style.transition = 'opacity 0.5s ease';
        headingChildEl.style.opacity    = '1';
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (window.elementorFrontend?.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-animated-heading.default', widgetAnimatedHeading);
        }
    });

})();

/**
 * End animated heading widget script
 */

/**
 * Start audio player widget script
 * Optimized version - Minimal jQuery (required for jPlayer)
 */

(() => {
  "use strict";

  const widgetAudioPlayer = (scope) => {
    const scopeElement = scope instanceof jQuery ? scope[0] : scope;

    const audioPlayerElement = scopeElement.querySelector(".bdt-audio-player .jp-jplayer");
    if (!audioPlayerElement) return;

    const settingsData = audioPlayerElement.dataset.settings;
    if (!settingsData) return;

    let settings;
    try {
      settings = typeof settingsData === "string" ? JSON.parse(settingsData) : settingsData;
    } catch (e) {
      console.error("Failed to parse audio player settings:", e);
      return;
    }

    if (typeof jQuery === "undefined" || !jQuery.fn.jPlayer) {
      console.error("jPlayer library is not loaded");
      return;
    }

    const jpAudioElement = audioPlayerElement.nextElementSibling;
    const containerId = jpAudioElement?.classList.contains("jp-audio") ? jpAudioElement.id : null;

    if (!containerId) {
      console.error("Audio player container not found");
      return;
    }

    const $audioPlayer = jQuery(audioPlayerElement);
    const $jpAudio = jQuery(jpAudioElement);

    $audioPlayer.jPlayer({
      ready: function (event) {
        jQuery(this).jPlayer("setMedia", {
          title: settings.audio_title,
          mp3: settings.audio_source,
        });

        if (settings.autoplay === true) {
          jQuery(this).jPlayer("play", 1);
        }
      },

      play: function () {
        $jpAudio.removeClass("bdt-player-played");
        jQuery(this).jPlayer("pauseOthers");
      },

      ended: function () {
        $jpAudio.addClass("bdt-player-played");
      },

      timeupdate: function (event) {
        if (settings.time_restrict === true) {
          if (event.jPlayer.status.currentTime > settings.restrict_duration) {
            jQuery(this).jPlayer("stop");
          }
        }
      },

      cssSelectorAncestor: `#${containerId}`,
      useStateClassSkin: true,
      autoBlur: settings.smooth_show,
      smoothPlayBar: true,
      keyEnabled: settings.keyboard_enable,
      remainingDuration: true,
      toggleDuration: true,
      volume: settings.volume_level,
      loop: settings.loop,
    });
  };

  /**
   * Showcase skin — full playlist player (Swiper + list + sticky bar).
   *
   * Flow:
   * 1. Read `data-settings` JSON rendered by skin-showcase.php
   * 2. Wire DOM controls to a single hidden <audio> element
   * 3. Sync progress/volume fills via CSS custom properties
   *
   * @param {HTMLElement|jQuery} scope Widget root from Elementor
   */
  const widgetAudioPlayerShowcase = (scope) => {
    const scopeElement = scope instanceof jQuery ? scope[0] : scope;
    const showcaseRoot = scopeElement.querySelector(".bdt-audio-player.skin-showcase");
    if (!showcaseRoot) return;

    const settingsData = showcaseRoot.dataset.settings;
    if (!settingsData) return;

    let settings;
    try {
      settings = typeof settingsData === "string" ? JSON.parse(settingsData) : settingsData;
    } catch (e) {
      console.error("Failed to parse showcase settings:", e);
      return;
    }

    const isEditMode = Boolean(window.elementorFrontend?.isEditMode?.());
    const allSongs = Array.isArray(settings.songs) ? settings.songs : [];
    const hasPlayableSong = allSongs.some((song) => song?.audio);
    if (!hasPlayableSong) return;

    const widgetId =
      scopeElement.getAttribute("data-id") ||
      scopeElement.closest("[data-id]")?.getAttribute("data-id");

    const snapshotSongs = (list) => list.map((song) => ({ audio: song?.audio || "" }));

    const getShowcasePreviewState = () => {
      if (!widgetId) return null;

      window.__bdtShowcasePreview = window.__bdtShowcasePreview || {};
      window.__bdtShowcasePreview[widgetId] =
        window.__bdtShowcasePreview[widgetId] || { songs: [] };

      return window.__bdtShowcasePreview[widgetId];
    };

    const detectEditorPreviewIndex = (previousSongs, nextSongs) => {
      if (!previousSongs.length) {
        return null;
      }

      if (nextSongs.length > previousSongs.length) {
        for (let i = previousSongs.length; i < nextSongs.length; i += 1) {
          if (nextSongs[i]?.audio) {
            return i;
          }
        }

        return nextSongs.length - 1;
      }

      let changedIndex = null;

      for (let i = 0; i < nextSongs.length; i += 1) {
        const prevAudio = previousSongs[i]?.audio || "";
        const nextAudio = nextSongs[i]?.audio || "";

        if (nextAudio && prevAudio !== nextAudio) {
          changedIndex = i;
        }
      }

      return changedIndex;
    };

    const resolveStartIndex = () => {
      let fallbackIndex = Number.isInteger(settings.initial_index)
        ? settings.initial_index
        : Number.parseInt(settings.initial_index, 10);

      if (Number.isNaN(fallbackIndex)) {
        fallbackIndex = 0;
      }

      fallbackIndex = Math.max(0, Math.min(fallbackIndex, allSongs.length - 1));

      if (!isEditMode) {
        return { index: fallbackIndex, previewChanged: false };
      }

      const previewState = getShowcasePreviewState();
      const nextSnapshot = snapshotSongs(allSongs);

      if (!previewState) {
        return { index: fallbackIndex, previewChanged: false };
      }

      const previewIndex = detectEditorPreviewIndex(previewState.songs, nextSnapshot);
      previewState.songs = nextSnapshot;

      if (previewIndex !== null && previewIndex >= 0 && previewIndex < allSongs.length) {
        return { index: previewIndex, previewChanged: true };
      }

      return { index: fallbackIndex, previewChanged: false };
    };

    const audioPlayer = showcaseRoot.querySelector(".bdt-ap-showcase-audio");
    const playlistContainer = showcaseRoot.querySelector(".bdt-ap-showcase-playlist");
    const playlistItems = showcaseRoot.querySelectorAll(".bdt-ap-showcase-playlist-item");
    const likeButtons = showcaseRoot.querySelectorAll(".bdt-ap-showcase-like-btn");
    const volumeRange = showcaseRoot.querySelector(".bdt-ap-showcase-volume-range");
    const volumeContainer = showcaseRoot.querySelector(".bdt-ap-showcase-volume");
    const volumeIcon = showcaseRoot.querySelector(".bdt-ap-showcase-volume-icon");
    const progressBar = showcaseRoot.querySelector(".bdt-ap-showcase-progress-bar");
    const progressCurrent = showcaseRoot.querySelector(".bdt-ap-showcase-progress-current");
    const progressTotal = showcaseRoot.querySelector(".bdt-ap-showcase-progress-total");
    const playPauseBtn = showcaseRoot.querySelector(".bdt-ap-showcase-play-pause");
    const playPauseIcon = showcaseRoot.querySelector(".bdt-ap-showcase-play-icon");
    const prevBtn = showcaseRoot.querySelector(".bdt-ap-showcase-prev");
    const nextBtn = showcaseRoot.querySelector(".bdt-ap-showcase-next");
    const shuffleBtn = showcaseRoot.querySelector(".bdt-ap-showcase-shuffle");
    const swiperContainer = showcaseRoot.querySelector(".bdt-ap-showcase-swiper");
    const showShuffle = settings.show_shuffle !== false;
    const showLike = settings.show_like !== false;
    const showDuration = settings.show_duration !== false;
    const showProgressTime = settings.show_progress_time !== false;
    const autoNext = settings.auto_next !== false;

    const PLAYLIST_SCROLL_ITEM_LIMIT_DESKTOP = 5;
    const PLAYLIST_SCROLL_VISIBLE_MOBILE = 3;
    const MOBILE_SCROLL_MIN_ITEMS = 3;
    const MOBILE_PLAYLIST_BREAKPOINT = 580;
    const MOBILE_SWIPER_BREAKPOINT = 580;
    const swiperSpeed = Number(settings.swiper_speed) || 700;

    if (
      !audioPlayer ||
      !playlistContainer ||
      !volumeRange ||
      !progressBar ||
      !playPauseBtn ||
      !playPauseIcon ||
      !prevBtn ||
      !nextBtn ||
      !swiperContainer
    ) {
      return;
    }

    const bindAction = (element, callback) => {
      if (!element) return;

      element.addEventListener("click", callback);
      element.addEventListener("keydown", (event) => {
        if (event.key === "Enter" || event.key === " ") {
          event.preventDefault();
          callback(event);
        }
      });
    };

    const updateVolumeIcon = (volumePercent) => {
      if (!volumeIcon) return;

      const level =
        volumePercent <= 0 ? "mute" : volumePercent <= 50 ? "medium" : "high";
      volumeIcon.dataset.volumeLevel = level;
    };

    const syncVolumeRange = (value = Number(volumeRange.value)) => {
      const max = Number(volumeRange.max) || 100;
      const current = Math.min(Math.max(Number(value) || 0, 0), max);

      volumeRange.style.setProperty(
        "--volume-percent",
        max > 0 ? `${(current / max) * 100}%` : "0%"
      );
    };

    const initVolumeHover = () => {
      if (!volumeContainer) return;

      let hideTimer = null;

      const showVolume = () => {
        clearTimeout(hideTimer);
        volumeContainer.classList.add("is-hovering");
      };

      const scheduleHideVolume = () => {
        clearTimeout(hideTimer);
        hideTimer = window.setTimeout(() => {
          volumeContainer.classList.remove("is-hovering");
        }, 280);
      };

      volumeContainer.addEventListener("mouseover", showVolume);
      volumeContainer.addEventListener("mouseout", (event) => {
        if (volumeContainer.contains(event.relatedTarget)) {
          return;
        }

        scheduleHideVolume();
      });

      volumeContainer.addEventListener("click", (event) => {
        if (event.target.closest(".bdt-ap-showcase-volume-range")) {
          return;
        }

        event.stopPropagation();
        volumeContainer.classList.toggle("is-open");
      });

      document.addEventListener("click", () => {
        volumeContainer.classList.remove("is-open");
      });

      volumeRange.addEventListener("focus", () => {
        volumeContainer.classList.add("is-open");
        showVolume();
      });
    };

    const initPlaylistScroll = () => {
      if (!playlistContainer) {
        return;
      }

      if (showcaseRoot._playlistScrollAbort) {
        showcaseRoot._playlistScrollAbort.abort();
      }

      const scrollAbortController = new AbortController();
      const scrollSignal = scrollAbortController.signal;
      showcaseRoot._playlistScrollAbort = scrollAbortController;

      const getPlaylistItems = () =>
        showcaseRoot.querySelectorAll(".bdt-ap-showcase-playlist-item");

      const isMobilePlaylist = () => window.innerWidth <= MOBILE_PLAYLIST_BREAKPOINT;

      const getVisibleItemLimit = () =>
        isMobilePlaylist() ? PLAYLIST_SCROLL_VISIBLE_MOBILE : PLAYLIST_SCROLL_ITEM_LIMIT_DESKTOP;

      const shouldEnableScroll = (itemCount = getPlaylistItems().length) => {
        if (isMobilePlaylist()) {
          return itemCount >= MOBILE_SCROLL_MIN_ITEMS;
        }

        return itemCount > PLAYLIST_SCROLL_ITEM_LIMIT_DESKTOP;
      };

      const getMaxScroll = () =>
        Math.max(0, playlistContainer.scrollHeight - playlistContainer.clientHeight);

      const measureVisibleHeight = (limit, items = getPlaylistItems()) =>
        Array.from(items)
          .slice(0, limit)
          .reduce((total, item) => {
            const itemStyle = window.getComputedStyle(item);
            const itemMarginBottom = Number.parseFloat(itemStyle.marginBottom) || 0;
            return total + item.offsetHeight + itemMarginBottom;
          }, 0);

      const applyScrollContainer = () => {
        const items = getPlaylistItems();

        if (!items.length || !shouldEnableScroll(items.length)) {
          playlistContainer.classList.remove("is-scrollable");
          playlistContainer.classList.remove("is-scrollable-ssr-mobile");
          playlistContainer.style.removeProperty("--playlist-visible-height");
          playlistContainer.style.removeProperty("max-height");
          return;
        }

        playlistContainer.classList.add("is-scrollable");

        const limit = getVisibleItemLimit();
        const visibleHeight = Math.ceil(measureVisibleHeight(limit, items));

        playlistContainer.style.setProperty(
          "--playlist-visible-height",
          `${visibleHeight}px`
        );
        playlistContainer.style.maxHeight = `${visibleHeight}px`;
        playlistContainer.classList.remove("is-scrollable-ssr-mobile");
      };

      const getWheelDelta = (event) => {
        let delta = event.deltaY;

        if (event.deltaMode === 1) {
          delta *= 16;
        } else if (event.deltaMode === 2) {
          delta *= playlistContainer.clientHeight;
        }

        return delta;
      };

      let targetScroll = playlistContainer.scrollTop;
      let rafId = null;
      const scrollEase = 0.16;

      const clampScroll = (value) => Math.max(0, Math.min(getMaxScroll(), value));

      const refreshScrollContainer = () => {
        applyScrollContainer();

        if (playlistContainer.classList.contains("is-scrollable")) {
          targetScroll = clampScroll(targetScroll);
          playlistContainer.scrollTop = targetScroll;
        }
      };

      refreshScrollContainer();

      let touchStartY = 0;
      playlistContainer.addEventListener(
        "touchstart",
        (event) => {
          if (!playlistContainer.classList.contains("is-scrollable")) {
            return;
          }

          touchStartY = event.touches[0].clientY;
        },
        { passive: true, signal: scrollSignal }
      );

      playlistContainer.addEventListener(
        "touchmove",
        (event) => {
          if (!playlistContainer.classList.contains("is-scrollable") || getMaxScroll() <= 0) {
            return;
          }

          const deltaY = event.touches[0].clientY - touchStartY;
          const atTop = playlistContainer.scrollTop <= 0;
          const atBottom =
            playlistContainer.scrollTop + playlistContainer.clientHeight >=
            playlistContainer.scrollHeight - 1;

          if ((atTop && deltaY > 0) || (atBottom && deltaY < 0)) {
            return;
          }

          event.stopPropagation();
        },
        { passive: true, signal: scrollSignal }
      );

      const animateScroll = () => {
        const currentScroll = playlistContainer.scrollTop;
        const distance = targetScroll - currentScroll;

        if (Math.abs(distance) < 0.5) {
          if (currentScroll !== targetScroll) {
            playlistContainer.scrollTop = targetScroll;
          }
          rafId = null;
          return;
        }

        playlistContainer.scrollTop = currentScroll + distance * scrollEase;
        rafId = requestAnimationFrame(animateScroll);
      };

      const scheduleScroll = () => {
        if (rafId === null) {
          rafId = requestAnimationFrame(animateScroll);
        }
      };

      const syncTargetFromContainer = () => {
        targetScroll = playlistContainer.scrollTop;
      };

      playlistContainer.addEventListener(
        "wheel",
        (event) => {
          if (!playlistContainer.classList.contains("is-scrollable")) {
            return;
          }

          if (getMaxScroll() <= 0) {
            return;
          }

          event.preventDefault();
          event.stopPropagation();

          if (rafId === null) {
            syncTargetFromContainer();
          }

          targetScroll = clampScroll(targetScroll + getWheelDelta(event));
          scheduleScroll();
        },
        { passive: false, signal: scrollSignal }
      );

      playlistContainer.addEventListener(
        "scroll",
        () => {
          if (rafId === null) {
            syncTargetFromContainer();
          }
        },
        { passive: true, signal: scrollSignal }
      );

      let resizeTimer;
      window.addEventListener(
        "resize",
        () => {
          clearTimeout(resizeTimer);
          resizeTimer = window.setTimeout(refreshScrollContainer, 150);
        },
        { signal: scrollSignal }
      );

      if (typeof ResizeObserver !== "undefined") {
        const playlistResizeObserver = new ResizeObserver(() => {
          requestAnimationFrame(refreshScrollContainer);
        });

        playlistResizeObserver.observe(playlistContainer);
        scrollAbortController.signal.addEventListener("abort", () => {
          playlistResizeObserver.disconnect();
        });
      }
    };

    initPlaylistScroll();

    const { index: startIndex, previewChanged } = resolveStartIndex();
    let currentSongIndex = startIndex;

    if (!allSongs[currentSongIndex]?.audio) {
      const playableIndex = allSongs.findIndex((song) => song?.audio);

      if (playableIndex >= 0) {
        currentSongIndex = playableIndex;
      }
    }

    let isSongLoaded = false;
    let swiperInstance = null;

    const setControlTooltip = (element, text, position = "top") => {
      if (!element || !text) {
        return;
      }

      element.setAttribute("data-bdt-tooltip", `title: ${text}; pos: ${position};`);

      if (window.bdtUIkit?.tooltip) {
        window.bdtUIkit.tooltip(element);
      }
    };

    const initShowcaseTooltips = () => {
      showcaseRoot
        .querySelectorAll(
          ".bdt-ap-showcase-controls [data-bdt-tooltip], .bdt-ap-showcase-progress [data-bdt-tooltip]"
        )
        .forEach((element) => {
          if (window.bdtUIkit?.tooltip) {
            window.bdtUIkit.tooltip(element);
          }
        });
    };

    const updatePlayPauseIcon = (isPlaying) => {
      const playSvg = playPauseIcon.querySelector(".icon-play");
      const pauseSvg = playPauseIcon.querySelector(".icon-pause");
      if (!playSvg || !pauseSvg) return;

      playSvg.style.display = isPlaying ? "none" : "inline-block";
      pauseSvg.style.display = isPlaying ? "inline-block" : "none";

      if (playPauseBtn) {
        const label = isPlaying
          ? playPauseBtn.dataset.tooltipPause || "Pause"
          : playPauseBtn.dataset.tooltipPlay || "Play";
        setControlTooltip(playPauseBtn, label);
      }
    };

    const updatePlaylistHighlight = (index) => {
      playlistItems.forEach((item, itemIndex) => {
        item.classList.toggle("active-playlist-item", itemIndex === index);
      });
    };

    const formatDuration = (seconds) => {
      if (!Number.isFinite(seconds) || seconds < 0) {
        return "0:00";
      }

      const totalSeconds = Math.floor(seconds);
      const hours = Math.floor(totalSeconds / 3600);
      const minutes = Math.floor((totalSeconds % 3600) / 60);
      const remainingSeconds = totalSeconds % 60;

      if (hours > 0) {
        return `${hours}:${String(minutes).padStart(2, "0")}:${String(remainingSeconds).padStart(2, "0")}`;
      }

      return `${minutes}:${String(remainingSeconds).padStart(2, "0")}`;
    };

    const updateProgressTime = () => {
      if (!showProgressTime) {
        return;
      }

      if (progressCurrent) {
        progressCurrent.textContent = formatDuration(audioPlayer.currentTime);
      }

      if (progressTotal) {
        progressTotal.textContent = formatDuration(audioPlayer.duration);
      }
    };

    const syncProgressBar = (currentTime = audioPlayer.currentTime) => {
      const duration = Number.isFinite(audioPlayer.duration) ? audioPlayer.duration : Number(progressBar.max);
      const max = duration > 0 ? duration : 0;
      const current = max > 0 ? Math.min(Math.max(Number(currentTime) || 0, 0), max) : 0;

      if (max > 0) {
        progressBar.max = String(max);
      }

      progressBar.value = String(current);
      progressBar.style.setProperty(
        "--progress-percent",
        max > 0 ? `${(current / max) * 100}%` : "0%"
      );
    };

    const updateDurationDisplay = (index, seconds) => {
      const playlistItem = playlistItems[index];
      if (!playlistItem) {
        return;
      }

      const durationElement = playlistItem.querySelector(".bdt-ap-showcase-duration");
      if (durationElement) {
        durationElement.textContent = formatDuration(seconds);
      }
    };

    const initPlaylistDurations = () => {
      allSongs.forEach((song, index) => {
        if (!song?.audio) {
          return;
        }

        const probe = new Audio();
        probe.preload = "metadata";
        probe.src = song.audio;

        const applyDuration = () => {
          if (Number.isFinite(probe.duration) && probe.duration > 0) {
            updateDurationDisplay(index, probe.duration);
          }

          probe.removeAttribute("src");
          probe.load();
        };

        probe.addEventListener("loadedmetadata", applyDuration, { once: true });
        probe.addEventListener("error", applyDuration, { once: true });
      });
    };

    const updateSwiperToMatchSong = (index) => {
      if (!swiperInstance) return;
      const activeIndex = typeof swiperInstance.realIndex === "number" ? swiperInstance.realIndex : swiperInstance.activeIndex;

      if (activeIndex !== index) {
        swiperInstance.slideTo(index);
      }
    };

    const playSong = () => {
      const playPromise = audioPlayer.play();
      if (playPromise && typeof playPromise.then === "function") {
        playPromise.then(() => updatePlayPauseIcon(true)).catch(() => updatePlayPauseIcon(false));
      } else {
        updatePlayPauseIcon(true);
      }
    };

    const pauseSong = () => {
      audioPlayer.pause();
      updatePlayPauseIcon(false);
    };

    const loadAndPlaySong = (index) => {
      const song = allSongs[index];
      if (!song?.audio) return;

      audioPlayer.src = song.audio;
      playSong();
      updatePlaylistHighlight(index);
      updateSwiperToMatchSong(index);
      isSongLoaded = true;
    };

    const nextSong = () => {
      currentSongIndex = (currentSongIndex + 1) % allSongs.length;
      loadAndPlaySong(currentSongIndex);
    };

    /**
     * On track end: auto-advance when enabled, otherwise pause at 100%.
     * Respects `auto_next` from Elementor and stops on the last playlist item.
     */
    const handleSongEnded = () => {
      if (autoNext && currentSongIndex < allSongs.length - 1) {
        currentSongIndex += 1;
        loadAndPlaySong(currentSongIndex);
        return;
      }

      pauseSong();
      syncProgressBar(audioPlayer.duration || Number(progressBar.max) || 0);
      updateProgressTime();
    };

    const prevSong = () => {
      currentSongIndex = (currentSongIndex - 1 + allSongs.length) % allSongs.length;
      loadAndPlaySong(currentSongIndex);
    };

    const togglePlayPause = () => {
      if (!isSongLoaded) {
        loadAndPlaySong(currentSongIndex);
      } else if (audioPlayer.paused) {
        playSong();
      } else {
        pauseSong();
      }
    };

    const initSwiper = () => {
      if (typeof Swiper === "undefined") return;

      const isMobileSwiper = window.innerWidth <= MOBILE_SWIPER_BREAKPOINT;
      const swiperOptions = isMobileSwiper
        ? {
            effect: "slide",
            slidesPerView: 1,
            spaceBetween: 0,
            grabCursor: true,
            speed: swiperSpeed,
            initialSlide: currentSongIndex,
          }
        : {
            effect: "cards",
            cardsEffect: {
              perSlideOffset: 9,
              perSlideRotate: 3,
            },
            grabCursor: true,
            speed: swiperSpeed,
            initialSlide: currentSongIndex,
          };

      try {
        swiperInstance = new Swiper(swiperContainer, swiperOptions);
      } catch (e) {
        swiperInstance = new Swiper(swiperContainer, {
          grabCursor: true,
          speed: swiperSpeed,
          initialSlide: currentSongIndex,
        });
      }

      if (swiperInstance) {
        swiperInstance.on("slideChange", () => {
          const newIndex =
            typeof swiperInstance.realIndex === "number"
              ? swiperInstance.realIndex
              : swiperInstance.activeIndex;
          if (newIndex !== currentSongIndex) {
            currentSongIndex = newIndex;
            loadAndPlaySong(currentSongIndex);
          }
        });
      }
    };

    audioPlayer.loop = settings.loop === true;
    const volumeSetting = Number(settings.volume);
    audioPlayer.volume = Math.max(
      0,
      Math.min(1, Number.isFinite(volumeSetting) ? volumeSetting : 1)
    );
    volumeRange.value = String(Math.round(audioPlayer.volume * 100));
    updateVolumeIcon(Number(volumeRange.value));
    syncVolumeRange(Number(volumeRange.value));
    initVolumeHover();

    updatePlaylistHighlight(currentSongIndex);
    initSwiper();

    if (showDuration) {
      initPlaylistDurations();
    }

    if (settings.autoplay === true || (isEditMode && previewChanged)) {
      loadAndPlaySong(currentSongIndex);
    } else {
      const startSong = allSongs[currentSongIndex];

      if (startSong?.audio) {
        audioPlayer.src = startSong.audio;
      }

      isSongLoaded = true;
      updatePlayPauseIcon(false);
    }

    playlistItems.forEach((item, index) => {
      item.addEventListener("click", () => {
        currentSongIndex = index;
        loadAndPlaySong(index);
      });
    });

    if (showLike) {
      likeButtons.forEach((likeButton) => {
        bindAction(likeButton, (event) => {
          event.stopPropagation();
          likeButton.classList.toggle("is-liked");
        });
      });
    }

    initShowcaseTooltips();

    bindAction(playPauseBtn, togglePlayPause);
    bindAction(nextBtn, nextSong);
    bindAction(prevBtn, prevSong);

    if (showShuffle && shuffleBtn) {
      bindAction(shuffleBtn, () => {
        const randomIndex = Math.floor(Math.random() * allSongs.length);
        currentSongIndex =
          randomIndex !== currentSongIndex ? randomIndex : (randomIndex + 1) % allSongs.length;
        loadAndPlaySong(currentSongIndex);
      });
    }

    audioPlayer.addEventListener("loadedmetadata", () => {
      syncProgressBar(audioPlayer.currentTime || 0);

      if (showDuration) {
        updateDurationDisplay(currentSongIndex, audioPlayer.duration);
      }

      updateProgressTime();
    });

    audioPlayer.addEventListener("timeupdate", () => {
      if (!audioPlayer.paused) {
        syncProgressBar(audioPlayer.currentTime);
      }

      updateProgressTime();
    });

    progressBar.addEventListener("input", () => {
      audioPlayer.currentTime = Number(progressBar.value);
      syncProgressBar(Number(progressBar.value));
      updateProgressTime();
    });

    progressBar.addEventListener("change", () => {
      if (!audioPlayer.paused) {
        playSong();
      }
    });

    volumeRange.addEventListener("input", () => {
      const volumePercent = Number(volumeRange.value);
      audioPlayer.volume = volumePercent / 100;
      updateVolumeIcon(volumePercent);
      syncVolumeRange(volumePercent);
    });

    audioPlayer.addEventListener("play", () => updatePlayPauseIcon(true));
    audioPlayer.addEventListener("pause", () => updatePlayPauseIcon(false));
    audioPlayer.addEventListener("ended", handleSongEnded);
  };

  // Initialize on Elementor frontend ready
  window.addEventListener("elementor/frontend/init", () => {
    if (window.elementorFrontend?.hooks) {
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/bdt-audio-player.default",
        widgetAudioPlayer
      );
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/bdt-audio-player.bdt-poster",
        widgetAudioPlayer
      );
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/bdt-audio-player.bdt-showcase",
        widgetAudioPlayerShowcase
      );
    }
  });
})();

/**
 * End audio player widget script
 */

/**
 * Start background parallax widget script
 */

(() => {
  "use strict";

  window.addEventListener("elementor/frontend/init", () => {
    const ModuleHandler = elementorModules.frontend.handlers.Base;

    const BackgroundParallax = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },

      getDefaultSettings: function () {
        return {
          media: false,
          easing: 1,
          viewport: 1,
        };
      },

      onElementChange: debounce(function (prop) {
        if (
          prop.indexOf("section_parallax_") !== -1 ||
          prop.indexOf("ep_parallax_") !== -1
        ) {
          this.run();
        }
      }, 400),

      settings: function (key) {
        return this.getElementSettings(key);
      },

      run: function () {
        if (elementorFrontend.isEditMode()) {
          return;
        }

        if (this.bgParallax) {
          this.bgParallax.$destroy();
          this.bgParallax = null;
        }

        const settings = this.getElementSettings();
        const options = this.getDefaultSettings();
        const widgetID = this.$element.data("id");

        if (settings.section_parallax_on !== "yes") {
          return;
        }

        const element = document.querySelector(`.elementor-element-${widgetID}`);

        if (!element) {
          console.warn(`Element with ID ${widgetID} not found`);
          return;
        }

        if (settings.section_parallax_x_value?.size) {
          options.bgx = settings.section_parallax_x_value.size || 0;
        }

        if (settings.section_parallax_value?.size) {
          options.bgy = settings.section_parallax_value.size || 0;
        }

        if (settings.ep_parallax_bg_colors) {
          if (
            settings.ep_parallax_bg_border_color_start ||
            settings.ep_parallax_bg_border_color_end
          ) {
            options.borderColor = [
              settings.ep_parallax_bg_border_color_start || 0,
              settings.ep_parallax_bg_border_color_end || 0,
            ];
          }

          if (
            settings.ep_parallax_bg_color_start ||
            settings.ep_parallax_bg_color_end
          ) {
            options.backgroundColor = [
              settings.ep_parallax_bg_color_start || 0,
              settings.ep_parallax_bg_color_end || 0,
            ];
          }
        }

        if (
          settings.section_parallax_x_value ||
          settings.section_parallax_value ||
          settings.ep_parallax_bg_colors
        ) {
          if (!window.bdtUIkit?.parallax) {
            console.error("bdtUIkit.parallax is not available");
            return;
          }

          this.bgParallax = window.bdtUIkit.parallax(element, options);
        }
      },

      onDestroy: function () {
        if (this.bgParallax) {
          this.bgParallax.$destroy();
          this.bgParallax = null;
        }
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/section",
      (scope) => {
        elementorFrontend.elementsHandler.addHandler(BackgroundParallax, {
          $element: scope,
        });
      }
    );

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/container",
      (scope) => {
        elementorFrontend.elementsHandler.addHandler(BackgroundParallax, {
          $element: scope,
        });
      }
    );
  });
})();

/**
 * End background parallax widget script
 */

/**
 * Start barcode widget script
 * Optimized version - No jQuery dependency
 */

(() => {
  "use strict";

  window.addEventListener("elementor/frontend/init", () => {
    const ModuleHandler = elementorModules.frontend.handlers.Base;
    let BarCode;

    BarCode = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },

      getDefaultSettings: function () {
        return {
          format: "code128",
        };
      },

      onElementChange: debounce(function (prop) {
        if (prop.indexOf("ep_barcode") !== -1) {
          this.run();
        }
      }, 400),

      settings: function (key) {
        return this.getElementSettings(`ep_barcode_${key}`);
      },

      run: function () {
        const options = this.getDefaultSettings();

        const scopeElement = this.$element instanceof jQuery ? this.$element[0] : this.$element;
        const container = scopeElement.querySelector(".bdt-ep-barcode");

        if (!container) {
          return;
        }

        const content = this.settings("content");
        if (!content) {
          console.warn("Barcode content is empty");
          return;
        }

        options.displayValue = this.settings("show_label") === "yes";
        options.format = this.settings("format") || "code128";
        options.text = this.settings("label_text") || "";
        options.width = this.settings("width.size") || 2;
        options.height = this.settings("height.size") || 40;
        options.fontOptions = this.settings("font_width") || "normal";
        options.textAlign = this.settings("label_alignment") || "center";
        options.textPosition = this.settings("label_position") || "bottom";
        options.textMargin = this.settings("label_spacing.size") || 2;
        options.margin = 0;

        if (typeof JsBarcode === "undefined") {
          console.error("JsBarcode library is not loaded");
          return;
        }

        const widgetID = this.$element.data("id");
        const barcodeSelector = `#bdt-ep-barcode-${widgetID}`;

        try {
          JsBarcode(barcodeSelector, content, options);
        } catch (error) {
          console.error("Barcode generation error:", error);
        }
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-barcode.default",
      (scope) => {
        elementorFrontend.elementsHandler.addHandler(BarCode, {
          $element: scope,
        });
      }
    );
  });
})();

/**
 * End barcode widget script
 */


(function ($, elementor) {
  "use strict";

  $(window).on("elementor/frontend/init", function () {
    var ModuleHandler = elementorModules.frontend.handlers.Base,
      SvgBlob;

    SvgBlob = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },

      getDefaultSettings: function () {
        return {};
      },

      settings: function (key) {
        return this.getElementSettings("svg_blob_" + key);
      },

      run: function () {
        var options = this.getDefaultSettings();
        var $container = this.$element.find(".bdt-svg-blob");
        if (!$container.length) {
          return;
        }
        const path = $container.data("settings");
        const firstSVG = $container.find("path")[0];
        options = {
          targets: firstSVG,
          d: [{ value: path || [] }],
          easing: 'easeOutQuad',
          direction: 'alternate',
          loop: this.settings('loop') === 'yes',
          duration:
            this.settings('duration.size') !== ''
              ? this.settings('duration.size')
              : 2000,
          delay:
            this.settings('delay.size') !== ''
              ? this.settings('delay.size')
              : 10,
          endDelay:
            this.settings('end_delay.size') !== ''
              ? this.settings('end_delay.size')
              : 10,
        };
        anime(options);
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-svg-blob.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(SvgBlob, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);

(function ($, elementor) {
  "use strict";
  $(window).on("elementor/frontend/init", function () {
    var ModuleHandler = elementorModules.frontend.handlers.Base,
      PostGrid;

    PostGrid = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },

      getDefaultSettings: function () {
        return {};
      },

      onElementChange: debounce(function (prop) {
        if (prop.indexOf("post_grid") !== -1) {
          this.run();
        }
      }, 400),

      settings: function (key) {
        return this.getElementSettings("post_grid_" + key);
      },

      run: function () {
        var content = this.settings("ajax_loadmore");
        var $container = this.$element.find(".bdt-post-grid");
        if (!$container.length) {
          return;
        }
        if (content === undefined) {
          return;
        }
        var settingsLoadmore = this.settings("show_loadmore");
        var settingsInfiniteScroll = this.settings("show_infinite_scroll");

        var loadButtonContainer = this.$element.find(".bdt-loadmore-container");
        var grid = $container.find(".bdt-grid");
        var loadButton = loadButtonContainer.find(".bdt-loadmore");
        var loading = false;
        var settings = $container.data("settings");
        var readMore = $container.data("settings-button");
        var currentItemCount = settings.posts_per_page;

        var loadMorePosts = function () {
          var dataSettings = {
            action: "ep_loadmore_posts",
            settings: settings,
            readMore: readMore,
            per_page: settings.ajax_item_load,
            offset: currentItemCount,
            paged: settings.paged,
            nonce: settings.nonce,
          };
          jQuery.ajax({
            url: window.ElementPackConfig.ajaxurl,
            type: "post",
            data: dataSettings,
            success: function (response) {
              $(grid).append(response.markup);
              currentItemCount += settings.ajax_item_load;

              settings.paged += 1;
              loading = false;
              if (settingsLoadmore === "yes") {
                loadButton.html("Load More");
              }

              if ($(response.markup).length < settings.ajax_item_load) {
                loadButton.hide();
                loadButtonContainer.hide();
              }
            },
          });
        };

        if (settingsLoadmore === "yes") {
          $(loadButton).on("click", function () {
            if (!loading) {
              loading = true;
              loadButton.html("loading...");
              loadMorePosts();
            }
          });
        }

        if (settingsInfiniteScroll === "yes") {
          $(window).scroll(function () {
            if (
              $(window).scrollTop() ==
              $(document).height() - $(window).height()
            ) {
              $(loadButton).css("display", "block");
              loadMorePosts();
            } else {
              return;
            }
          });
        }
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-post-grid.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(PostGrid, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);

(function ($) {
    const categoryCache = {};

    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/bdt-post-list.default",
            function (scope) {
                scope.find(".bdt-post-list-wrap").each(function () {
                    const element = $(this)[0];

                    if (element) {
                        const $settings_showHide = $(this).data("show-hide");
                        const tabs = $(this).find(".bdt-option");
                        const tabs_header = $(this).find(".bdt-post-list-header");
                        const item_wrapper = $(this).find(".bdt-post-list");
                        const loader = $(this).find("#bdt-loading-image");
                        const settings = item_wrapper.data("settings");

                        function loadCategoryData(slug) {
                            $(loader).show();

                            if (categoryCache[slug]) {
                                item_wrapper.html(categoryCache[slug]);
                                console.log("Using cached data for category:", slug);
                                $(loader).hide();
                            } else {
                                $.ajax({
                                    url: ElementPackConfig.ajaxurl,
                                    data: {
                                        action: "bdt_post_list",
                                        nonce: ElementPackConfig.nonce,
                                        settings: settings,
                                        post_type: settings["post-type"],
                                        showHide: $settings_showHide,
                                        category: slug,
                                        human_diff_time: $settings_showHide["human_diff_time"],
                                        human_diff_time_short: $settings_showHide["human_diff_time_short"],
                                        bdt_link_new_tab: $settings_showHide["bdt_link_new_tab"],
                                    },
                                    type: "POST",
                                    dataType: "HTML",
                                    beforeSend: function() {
                                        $(loader).show();
                                    },
                                    success: function (response) {
                                        categoryCache[slug] = response;
                                        item_wrapper.html(response);
                                    },
                                    error: function (response) {
                                        console.log(response);
                                    },
                                    complete: function() {
                                        $(loader).hide();
                                    },
                                });
                            }
                        }

                        tabs.on("click", function (e) {
                            const slug = $(this).data("slug");
                            tabs_header.find(".bdt-filter-list").removeClass("bdt-active");
                            $(this).parent().addClass("bdt-active");
                            e.preventDefault();
                            loadCategoryData(slug);
                        });
                        
                    }
                });
            }
        );
    });
})(jQuery);

/**
 * Mega Menu widget script
 */

(function () {
  "use strict";

  const MegaMenuAjax = {
    initAjaxLoading(container, mode) {
      const scope = container instanceof jQuery ? container[0] : container;
      const panels = scope?.querySelectorAll('.ep-ajax-megamenu[data-ajax-load="true"]') || [];
      panels.forEach((panel) => {
        const postId = panel.dataset?.id;
        const menuItem = panel.closest("li.ep-has-megamenu");
        if (!menuItem || !postId) return;
        const eventType = mode === "click" ? "click" : "mouseenter";
        const handler = () => {
          if (panel.dataset.ajaxLoaded) return;
          panel.dataset.ajaxLoaded = "true";
          MegaMenuAjax.loadAjaxContent(panel, postId);
        };
        menuItem.addEventListener(eventType, handler);
      });
    },

    initAjaxMobileLoading(container) {
      const scope = container instanceof jQuery ? container[0] : container;
      if (!scope) return;
      const postId = scope.dataset?.id;
      scope.removeAttribute("hidden");
      MegaMenuAjax.loadAjaxContent(scope, postId);
    },

    async loadAjaxContent(panel, postId) {
      const scope = panel instanceof jQuery ? panel[0] : panel;
      const menuItem = scope?.closest(".ep-has-megamenu");
      const menuLink = menuItem?.querySelector(".ep-menu-nav-link");
      if (!menuLink) return;
      if (menuLink.classList.contains("ajax-loading")) return;

      menuLink.classList.add("ajax-loading");
      const spinner = document.createElement("span");
      spinner.className = "bdt-megamenu-indicator bdt-spinner";
      spinner.setAttribute("bdt-spinner", "ratio: 0.5");
      menuLink.appendChild(spinner);

      try {
        const url = new URL("/wp-json/element-pack/v1/megamenu/ajax_content/", window.location.origin);
        url.searchParams.set("id", postId);
        const res = await fetch(url, { method: "GET", headers: { Accept: "application/json" } });
        const response = await res.json();
        MegaMenuAjax.cleanupLoading(menuLink);
        if (response?.content) {
          scope.innerHTML = response.content;
          MegaMenuAjax.reinitializeWidgets(scope);
        } else {
          MegaMenuAjax.showError(scope);
        }
      } catch (err) {
        console.error("MegaMenu Ajax Error:", err);
        if (scope?.dataset) delete scope.dataset.ajaxLoaded;
        MegaMenuAjax.cleanupLoading(menuLink);
        MegaMenuAjax.showError(scope);
      }
    },

    cleanupLoading(menuLink) {
      const el = menuLink instanceof jQuery ? menuLink[0] : menuLink;
      if (el) {
        el.classList.remove("ajax-loading");
        el.querySelector(".bdt-spinner")?.remove();
      }
    },

    reinitializeWidgets(panel) {
      const scope = panel instanceof jQuery ? panel[0] : panel;
      if (typeof elementorFrontend === "undefined" || !scope) return;
      const elements = scope.querySelectorAll(".elementor-element");
      const $ = window.jQuery;
      elements.forEach((el) => {
        if ($) {
          elementorFrontend.elementsHandler.runReadyTrigger($(el));
        }
      });
    },

    showError(panel) {
      const scope = panel instanceof jQuery ? panel[0] : panel;
      if (scope) scope.innerHTML = '<li class="ep-ajax-error">Failed to load content</li>';
    },
  };

  window.addEventListener("elementor/frontend/init", function () {
    if (!window.elementorModules?.frontend?.handlers?.Base || !window.elementorFrontend?.hooks) return;

    const ModuleHandler = elementorModules.frontend.handlers.Base;
    const MegaMenu = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },

      getDefaultSettings: function () {
        return {};
      },

      onElementChange: debounce(function (prop) {
        if (prop.indexOf("ep_megamenu_") !== -1) {
          this.run();
        }
      }, 400),

      settings: function (key) {
        return this.getElementSettings("ep_megamenu_" + key);
      },

      run: function () {
        const self = this;
        const options = this.getDefaultSettings();
        const element = this.$element[0];
        const container = element?.querySelector(".ep-megamenu");
        const widgetID = this.$element.data("id");

        if (!container) return;

        const mobileMenuType = this.getElementSettings("mobile_menu_type") || "hamburger";

        MegaMenuAjax.initAjaxLoading(container, this.settings("mode"));

        container.querySelectorAll(".megamenu-header-mobile").forEach((el) => el.removeAttribute("style"));
        container.classList.remove("initialized");

        container.querySelectorAll(".ep-has-megamenu").forEach((megamenuItem) => {
          if (megamenuItem.querySelector(".ep-item.active")) {
            const link = megamenuItem.querySelector(":scope > .ep-menu-nav-link");
            if (link) link.classList.add("active");
          }
        });

        const dropMenus = container.querySelectorAll(".ep-megamenu-vertical-dropdown");
        if (dropMenus.length && window.bdtUIkit) {
          const dropOpts = {
            offset: this.settings("vertical_dropdown_offset") ?? "10",
            animation: this.settings("vertical_dropdown_animation_type") ?? "fade",
            duration: this.settings("vertical_dropdown_animation_duration") ?? "200",
            delayHide: this.settings("vertical_dropdown_delay_hide") ?? "800",
            mode: this.settings("vertical_dropdown_mode") ?? "click",
            animateOut: this.settings("vertical_dropdown_animate_out") ?? false,
          };
          dropMenus.forEach((el) => bdtUIkit.drop(el, dropOpts));
        }

        const megamenuItems = container.querySelectorAll(".ep-has-megamenu");
        const subDropdowns = container.querySelectorAll(".ep-default-submenu-panel");

        options.flip = false;
        options.offset = this.settings("offset.size") !== "" ? this.settings("offset.size") : "10";
        options.animation = this.settings("animation_type") ?? "fade";
        options.duration = this.settings("animation_duration") ?? "200";
        options.delayHide = this.settings("delay_hide") ?? "800";
        options.mode = this.settings("mode") ?? "hover";
        options.animateOut = this.settings("animate_out") ?? false;

        megamenuItems.forEach((item) => {
          const drop = item.querySelector(".ep-megamenu-panel");
          if (!drop || !window.bdtUIkit) return;
          const widthType = item.dataset?.widthType;
          let defaltWidthSelector = item.closest(".e-con-inner") || item.closest(".elementor-container");

          if (self.settings("direction") === "horizontal") {
            switch (widthType) {
              case "custom":
                options.stretch = null;
                options.target = null;
                options.boundary = null;
                options.pos = item.dataset?.contentPos;
                drop.style.minWidth = item.dataset?.contentWidth || "";
                drop.style.maxWidth = item.dataset?.contentWidth || "";
                break;
              case "full":
                options.stretch = "x";
                options.target = "#ep-megamenu-" + widgetID;
                options.boundary = false;
                break;
              default:
                options.stretch = "x";
                options.target = "#ep-megamenu-" + widgetID;
                options.boundary = defaltWidthSelector;
                break;
            }
          } else if (self.settings("direction") === "vertical") {
            switch (widthType) {
              case "custom":
                options.stretch = false;
                options.target = false;
                options.boundary = false;
                drop.style.minWidth = item.dataset?.contentWidth || "";
                drop.style.maxWidth = item.dataset?.contentWidth || "";
                break;
              default:
                options.stretch = "x";
                break;
            }
            options.pos = container.dataset?.isRtl === "1" ? "left-top" : "right-top";
          }
          bdtUIkit.drop(drop, { ...options });
        });

        subDropdowns.forEach((item) => {
          const dropOptions = { ...options };
          if (self.settings("direction") === "horizontal") {
            dropOptions.pos = item.classList.contains("ep-parent-element") ? "bottom-left" : "right-top";
          } else if (self.settings("direction") === "vertical") {
            dropOptions.stretch = false;
            const panel = item.querySelector(".ep-megamenu-panel");
            if (panel) panel.style.paddingLeft = "20px";
            dropOptions.pos = container.dataset?.isRtl === "1" ? "left-top" : "right-top";
          }
          dropOptions.stretch = false;
          dropOptions.target = false;
          dropOptions.flip = true;
          if (window.bdtUIkit) bdtUIkit.drop(item, dropOptions);
        });

        let dropWrapper = element?.closest(".elementor-top-section")
          || element?.closest(".elementor-element.e-con.e-parent")
          || element?.closest(".elementor-widget-bdt-mega-menu");

        if (!dropWrapper) return;

        const virtualArea = dropWrapper.querySelector(".ep-virtual-area");
        if (!virtualArea) {
          const menuEl = document.getElementById("ep-megamenu-" + widgetID);
          if (menuEl) {
            const clone = menuEl.cloneNode(true);
            const wrapper = document.createElement("div");
            wrapper.className = "ep-virtual-area";
            wrapper.appendChild(clone);
            dropWrapper.appendChild(wrapper);
          }
          const va = dropWrapper.querySelector(".ep-virtual-area");
          if (va) {
            va.querySelectorAll("[id]").forEach((obj) => {
              const oldId = obj.getAttribute("id");
              if (oldId) obj.setAttribute("id", oldId + "-virtual");
            });
            va.querySelectorAll("[fill]").forEach((obj) => {
              const fillId = obj.getAttribute("fill") || "";
              if (fillId.startsWith("url(#")) {
                obj.setAttribute("fill", fillId.slice(0, -1) + "-virtual)");
              }
            });
          }
        }

        const va = dropWrapper.querySelector(".ep-virtual-area");
        if (va) {
          va.querySelectorAll(".bdt-navbar-nav").forEach((el) => el.removeAttribute("class"));
          va.querySelectorAll(".menu-item").forEach((el) => {
            el.removeAttribute("data-width-type");
            el.removeAttribute("data-content-width");
            el.removeAttribute("data-content-pos");
          });
          va.querySelectorAll(".menu-item-has-children").forEach((el) => el.classList.add("ep-has-megamenu"));
          va.querySelectorAll(".ep-megamenu-panel").forEach((el) => {
            el.className = "bdt-accordion-content";
          });
          va.querySelectorAll(".bdt-accordion-content").forEach((el) => el.removeAttribute("style"));
        }

        element?.querySelectorAll(".details").forEach((el) => el.classList.remove("hidden"));
        container.querySelectorAll(".sub-menu-toggle").forEach((el) => el.remove());

        if (va && !va.querySelector(".bdt-accrodion-title-megamenu")) {
          va.querySelectorAll(".ep-menu-nav-link").forEach((link) => {
            const wrap = document.createElement("span");
            wrap.className = "bdt-accordion-title bdt-accrodion-title-megamenu";
            link.parentNode.insertBefore(wrap, link);
            wrap.appendChild(link);
            link.onclick = (e) => e.stopPropagation();
          });
          va.querySelectorAll(".bdt-megamenu-indicator").forEach((el) => el.remove());
          const accordionTitles = va.querySelectorAll(".ep-has-megamenu .bdt-accordion-title");
          accordionTitles.forEach((title) => {
            const indicator = document.createElement("i");
            indicator.className = "bdt-megamenu-indicator ep-icon-arrow-down-3";
            title.appendChild(indicator);
          });
        }

        const toggler = container.querySelector(".bdt-navbar-toggle");
        const toggleContent = dropWrapper?.querySelector(".ep-virtual-area");
        if (toggleContent && window.bdtUIkit) {
          bdtUIkit.drop(toggleContent, {
            offset: this.settings("offset_mobile.size") !== "" ? this.settings("offset_mobile.size") : "5",
            toggle: toggler,
            animation: this.settings("animation_type") ?? "fade",
            duration: this.settings("animation_duration") ?? "200",
            mode: "click",
          });
        }

        const accordionSelector = "#ep-megamenu-" + widgetID + "-virtual";
        const accordionEl = document.querySelector(accordionSelector);
        if (accordionEl && window.bdtUIkit) {
          bdtUIkit.accordion(accordionSelector, { offset: 10 });
          accordionEl.addEventListener("show", function (event) {
            const target = event.target?.querySelector("[data-ajax-load='true']");
            if (target && !target.dataset.ajaxLoaded) {
              target.dataset.ajaxLoaded = "true";
              MegaMenuAjax.initAjaxMobileLoading(target);
            }
          });
        }

        if (toggler) {
          const icons = toggler.querySelectorAll("svg");
          if (icons.length) icons[icons.length - 1].style.display = "none";
          toggler.addEventListener("click", (e) => {
            e.stopPropagation();
            toggler.querySelectorAll("svg").forEach((s) => {
              s.style.display = s.style.display === "none" ? "" : "none";
            });
          });
        }

        document.addEventListener("click", (e) => {
          if (toggler && toggleContent && !toggler.contains(e.target) && !toggleContent.contains(e.target)) {
            const svgs = toggler.querySelectorAll("svg");
            if (svgs.length) {
              svgs[0].style.display = "";
              if (svgs.length > 1) svgs[svgs.length - 1].style.display = "none";
            }
          }
        });

        if (mobileMenuType === "offcanvas") {
          const offcanvas = element?.querySelector(".ep-megamenu-offcanvas");
          const offcanvasNav = offcanvas?.querySelector(".ep-offcanvas-nav");

          if (offcanvas && offcanvasNav && window.bdtUIkit) {
            offcanvasNav.querySelectorAll(".ep-megamenu-panel, .ep-default-submenu-panel").forEach((el) => {
              el.classList.remove("bdt-drop", "bdt-open");
            });

            bdtUIkit.offcanvas(offcanvas);

            offcanvasNav.querySelectorAll("li.ep-has-megamenu, li.menu-item-has-children").forEach((menuItem) => {
              const link = menuItem.querySelector(":scope > a");
              const submenu = menuItem.querySelector(":scope > .ep-megamenu-panel, :scope > .ep-default-submenu-panel, :scope > .sub-menu");

              if (link && submenu) {
                submenu.classList.remove("bdt-drop", "bdt-open");
                submenu.style.display = "none";

                link.addEventListener("click", (e) => {
                  e.preventDefault();
                  if (menuItem.classList.contains("menu-open")) {
                    menuItem.classList.remove("menu-open");
                    submenu.style.display = "none";
                  } else {
                    [...menuItem.parentElement?.children || []].filter((c) => c !== menuItem && c.classList.contains("menu-open")).forEach((sib) => {
                      sib.classList.remove("menu-open");
                      const sm = sib.querySelector(":scope > .ep-megamenu-panel, :scope > .ep-default-submenu-panel, :scope > .sub-menu");
                      if (sm) sm.style.display = "none";
                    });
                    menuItem.classList.add("menu-open");
                    submenu.style.display = "";
                  }
                });
              }
            });
          }
        }
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-mega-menu.default",
      ($scope) => {
        elementorFrontend.elementsHandler.addHandler(MegaMenu, {
          $element: $scope,
        });
      }
    );
  });
})();
/**
 * Start brand carousel widget script
 * Optimized version - No jQuery dependency
 */

(() => {
  "use strict";

  const widgetBrandCarousel = async (scope) => {
    const scopeElement = scope?.jquery ? scope[0] : scope;

    const brandCarousel = scopeElement.querySelector(".bdt-ep-brand-carousel");
    if (!brandCarousel) return;

    const carouselContainer = brandCarousel.querySelector(".swiper-carousel");
    if (!carouselContainer) return;

    const settingsData = brandCarousel.dataset.settings;
    if (!settingsData) return;

    let settings;
    try {
      settings = typeof settingsData === "string" ? JSON.parse(settingsData) : settingsData;
    } catch (e) {
      console.error("Failed to parse brand carousel settings:", e);
      return;
    }

    const Swiper = elementorFrontend?.utils?.swiper;
    if (!Swiper) {
      console.error("Swiper is not available");
      return;
    }

    try {
      const swiper = await new Swiper(carouselContainer, settings);

      if (settings.pauseOnHover) {
        carouselContainer.addEventListener("mouseenter", () => {
          swiper.autoplay?.stop();
        });

        carouselContainer.addEventListener("mouseleave", () => {
          swiper.autoplay?.start();
        });
      }
    } catch (error) {
      console.error("Swiper initialization error:", error);
    }
  };

  window.addEventListener("elementor/frontend/init", () => {
    if (window.elementorFrontend?.hooks) {
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/bdt-brand-carousel.default",
        widgetBrandCarousel
      );
    }
  });
})();

/**
 * End brand carousel widget script
 */

/**
 * Start carousel widget script
 */

(() => {
    'use strict';

    const widgetCarousel = (scope) => {
        const scopeElement = scope?.jquery ? scope[0] : scope;

        const carousel = scopeElement.querySelector('.bdt-ep-carousel');
        if (!carousel) return;

        const carouselContainer = carousel.querySelector('.swiper-carousel');

        const settingsData = carousel.dataset.settings;
        if (!settingsData) return;

        let settings;
        try {
            settings = typeof settingsData === 'string' ? JSON.parse(settingsData) : settingsData;
        } catch (e) {
            console.error('Failed to parse carousel settings:', e);
            return;
        }

        const Swiper = window.elementorFrontend.utils.swiper;

        async function initSwiper() {
            const swiper = await new Swiper(carouselContainer, settings);

            if (settings.pauseOnHover) {
                carouselContainer.addEventListener('mouseenter', () => swiper.autoplay.stop());
                carouselContainer.addEventListener('mouseleave', () => swiper.autoplay.start());
            }
        }

        initSwiper();
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (window.elementorFrontend?.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-carousel.default',   widgetCarousel);
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-carousel.bdt-alice',    widgetCarousel);
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-carousel.bdt-vertical', widgetCarousel);
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-carousel.bdt-ramble',   widgetCarousel);
        }
    });

})();

/**
 * End carousel widget script
 */

/**
 * Start chart widget script
 */

(() => {
    'use strict';

    const addCommas = (nStr, separatorSymbol, kFormatter) => {
        nStr = String(nStr);

        if (kFormatter) {
            const num = parseFloat(nStr);
            if (num >= 1_000_000_000) return (num / 1_000_000_000).toFixed(1).replace(/\.0$/, '') + 'G';
            if (num >= 1_000_000)     return (num / 1_000_000).toFixed(1).replace(/\.0$/, '') + 'M';
            if (num >= 1_000)         return (num / 1_000).toFixed(1).replace(/\.0$/, '') + 'K';
            return nStr;
        }

        const [integer, decimal = ''] = nStr.split('.');
        const formatted = integer.replace(/(\d+)(\d{3})/, '$1' + separatorSymbol + '$2');
        return decimal ? `${formatted}.${decimal}` : formatted;
    };

    const buildTickCallback = (prefix, suffix, thouSeparator, separatorSymbol, kFormatter, axesSeparator) => {
        return (value) => {
            if (thouSeparator === 'yes' && axesSeparator === 'yes') {
                return prefix + addCommas(value, separatorSymbol, kFormatter) + suffix;
            }
            return prefix + value + suffix;
        };
    };

    const updateChartSetting = (chart, suffixprefix, thouSeparator, separatorSymbol, kFormatter, s_p_status, xAxesSeparator, yAxesSeparator) => {
        const prefix = s_p_status === 'yes' ? (suffixprefix.y_custom_prefix ?? '') : '';
        const suffix = s_p_status === 'yes' ? (suffixprefix.y_custom_suffix ?? '') : '';

        const tickCallback = buildTickCallback(prefix, suffix, thouSeparator, separatorSymbol, kFormatter, yAxesSeparator);

        if (suffixprefix.type === 'horizontalBar') {
            chart.options.scales.x.ticks = { callback: tickCallback };
        } else if (['bar', 'line', 'bubble'].includes(suffixprefix.type)) {
            chart.options.scales.y.ticks = { callback: tickCallback };
        }

        chart.update();
    };

    const widgetChart = (scope) => {
        const scopeElement = scope?.jquery ? scope[0] : scope;

        const chartEl = scopeElement.querySelector('.bdt-chart');
        if (!chartEl) return;

        const settingsData    = chartEl.dataset.settings;
        const suffixprefixData = chartEl.dataset.suffixprefix;
        if (!settingsData) return;

        let settings, suffixprefix;
        try {
            settings     = typeof settingsData    === 'string' ? JSON.parse(settingsData)    : settingsData;
            suffixprefix = typeof suffixprefixData === 'string' ? JSON.parse(suffixprefixData) : (suffixprefixData ?? {});
        } catch (e) {
            console.error('Failed to parse chart settings:', e);
            return;
        }

        epObserveTarget(scopeElement, () => {
            const canvas = chartEl.querySelector(':scope > canvas');
            if (!canvas) return;

            const ctx     = canvas.getContext('2d');
            const myChart = new Chart(ctx, settings);

            if (settings.type === 'pie' || settings.type === 'doughnut') return;

            const thouSeparator   = settings.valueSeparator   ?? 'no';
            const separatorSymbol = settings.separatorSymbol  ?? ',';
            const xAxesSeparator  = settings.xAxesSeparator   ?? 'no';
            const yAxesSeparator  = settings.yAxesSeparator   ?? 'no';
            const kFormatter      = settings.kFormatter === 'yes';

            const s_p_status = suffixprefix.suffix_prefix_status ?? 'no';

            const needsUpdate = s_p_status === 'yes' || thouSeparator === 'yes';
            if (needsUpdate) {
                updateChartSetting(myChart, suffixprefix, thouSeparator, separatorSymbol, kFormatter, s_p_status, xAxesSeparator, yAxesSeparator);
            }

        }, {
            root: null,
            rootMargin: '0px',
            threshold: 0.8
        });
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (window.elementorFrontend?.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-chart.default', widgetChart);
        }
    });

})();

/**
 * End chart widget script
 */

/**
 * Start circle info widget script
 */

(() => {
    'use strict';

    const circleJs = (id, circleMoving, movingTime, mouseEvent) => {
        const circles        = document.querySelectorAll(`#${id} .bdt-ep-circle-info-sub-circle`);
        const circleContents = document.querySelectorAll(`#${id} .bdt-ep-circle-info-item`);
        const inner          = document.querySelector(`#${id} .bdt-ep-circle-info-inner`);

        if (!inner) return;

        let i = 2;

        if (movingTime <= 0 || circleMoving === false) {
            movingTime = 100_000_000_000;
        }

        const removeClasses = (nodes, cls) => {
            nodes.forEach(node => node.classList.remove(cls));
        };

        const setActive = (nodeList, index) => {
            if (nodeList[index]) nodeList[index].classList.add('active');
        };

        const myTimer = () => {
            const activeCircle = inner.querySelector('.bdt-ep-circle-info-sub-circle.active');
            const dataTab      = activeCircle ? parseInt(activeCircle.dataset.circleIndex, 10) : 0;
            const total        = circles.length;

            if (dataTab > total || i > total) {
                i = 1;
            }

            removeClasses(circles, 'active');
            const target = inner.querySelector(`[data-circle-index='${i}']`);
            if (target) target.classList.add('active');

            removeClasses(circleContents, 'active');
            setActive(circleContents, i - 1);

            i++;

            const iconRotation  = 360 - (i - 2) * 36;
            const innerRotation = (i - 2) * 36;

            inner.querySelectorAll('.bdt-ep-circle-info-sub-circle i, .bdt-ep-circle-info-sub-circle svg').forEach(icon => {
                icon.style.transform = `rotate(${iconRotation}deg)`;
                icon.style.transition = '2s';
            });

            inner.style.transform = `rotate(${innerRotation}deg)`;
            inner.style.transition = '1s';
        };

        if (circleMoving === true) {
            setInterval(myTimer, movingTime);
        }

        const spreadCircles = () => {
            const { width, height } = inner.getBoundingClientRect();
            Array.from(circles).reverse().forEach((circle, index) => {
                const angle = index * (360 / circles.length);
                const x = (width  / 2) * Math.cos((angle * Math.PI) / 180);
                const y = (height / 2) * Math.sin((angle * Math.PI) / 180);
                circle.style.transform = `translate3d(${x.toFixed(5)}px,${y.toFixed(5)}px,0)`;
            });
        };

        spreadCircles();

        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(spreadCircles, 50);
        });

        const eventType = mouseEvent === 'click' ? 'click' : 'mouseover';

        circles.forEach((circle, index) => {
            circle.addEventListener(eventType, () => {
                if (!circle.classList.contains('active')) {
                    removeClasses(circles, 'active');
                    removeClasses(circleContents, 'active');
                    setActive(circles, index);
                    setActive(circleContents, index);
                }
            }, true);
        });
    };

    const widgetCircleInfo = (scope) => {
        const scopeElement = scope?.jquery ? scope[0] : scope;

        const circleInfo = scopeElement.querySelector('.bdt-ep-circle-info');
        if (!circleInfo) return;

        const settingsData = circleInfo.dataset.settings;
        if (!settingsData) return;

        let settings;
        try {
            settings = typeof settingsData === 'string' ? JSON.parse(settingsData) : settingsData;
        } catch (e) {
            console.error('Failed to parse circle info settings:', e);
            return;
        }

        epObserveTarget(circleInfo, () => {
            circleJs(settings.id, settings.circleMoving, settings.movingTime, settings.mouseEvent);
        }, {
            root: null,
            rootMargin: '0px',
            threshold: 0.8
        });
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (window.elementorFrontend?.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-circle-info.default', widgetCircleInfo);
        }
    });

})();

/**
 * End circle info widget script
 */

/**
 * Start circle menu widget script
 */

(() => {
    'use strict';

    const widgetCircleMenu = (scope) => {
        const scopeElement = scope instanceof jQuery ? scope[0] : scope;

        const circleMenu = scopeElement.querySelector('.bdt-circle-menu');
        if (!circleMenu) return;

        const settingsData = circleMenu.dataset.settings;
        if (!settingsData) return;

        let settings;
        try {
            settings = typeof settingsData === 'string' ? JSON.parse(settingsData) : settingsData;
        } catch (e) {
            console.error('Failed to parse circle menu settings:', e);
            return;
        }

        const {
            direction,
            item_diameter,
            circle_radius,
            speed,
            delay,
            step_out,
            step_in,
            trigger,
            transition_function
        } = settings;

        // circleMenu is a jQuery plugin — jQuery required here
        jQuery(circleMenu).circleMenu({
            direction,
            item_diameter,
            circle_radius,
            speed,
            delay,
            step_out,
            step_in,
            trigger,
            transition_function
        });

        const widgetID = scopeElement.dataset.id;

        scopeElement.querySelectorAll('.bdt-tippy-tooltip').forEach(el => {
            tippy(el, {
                allowHTML: true,
                theme: `bdt-tippy-${widgetID}`
            });
        });
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (window.elementorFrontend?.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-circle-menu.default', widgetCircleMenu);
        }
    });

})();

/**
 * End circle menu widget script
 */

/**
 * Start comment widget script
 */

(() => {
    'use strict';

    const loadScript = (src, id) => {
        if (document.getElementById(id)) return;
        const script = document.createElement('script');
        script.id  = id;
        script.src = src;
        script.setAttribute('data-timestamp', +new Date());
        (document.head || document.body).appendChild(script);
    };

    const widgetComment = (scope) => {
        const scopeElement = scope?.jquery ? scope[0] : scope;

        const commentEl = scopeElement.querySelector('.bdt-comment-container');
        if (!commentEl) return;

        const settingsData = commentEl.dataset.settings;
        if (!settingsData) return;

        let settings;
        try {
            settings = typeof settingsData === 'string' ? JSON.parse(settingsData) : settingsData;
        } catch (e) {
            console.error('Failed to parse comment settings:', e);
            return;
        }

        if (settings.layout === 'disqus') {

            window.disqus_config = function () {
                this.page.url        = settings.permalink;
                this.page.identifier = settings.identifier ?? commentEl.id;
            };

            loadScript(`//${settings.username}.disqus.com/embed.js`, 'dsq-embed-scr');

        } else if (settings.layout === 'facebook') {

            window.fbAsyncInit = function () {
                FB.init({
                    appId            : settings.app_id,
                    autoLogAppEvents : true,
                    xfbml            : true,
                    version          : 'v3.2'
                });
            };

            loadScript('https://connect.facebook.net/en_US/sdk.js', 'facebook-jssdk');
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (window.elementorFrontend?.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-comment.default', widgetComment);
        }
    });

})();

/**
 * End comment widget script
 */

/**
 * Start confetti effects widget script
 */

(() => {
    'use strict';

    const parseJSON = (value) => {
        if (!value || typeof value !== 'string') {
            return null;
        }

        try {
            return JSON.parse(value);
        } catch (e) {
            return null;
        }
    };

    const runAtomicConfettiFallback = () => {
        if (typeof confetti === 'undefined') {
            return;
        }

        document.querySelectorAll('[data-ep-confetti]').forEach((element) => {
            if (element.dataset.epConfettiFallbackInit === 'yes') {
                return;
            }

            const settings = parseJSON(element.getAttribute('data-ep-confetti'));
            if (!settings || settings.ep_widget_cf_confetti !== 'yes') {
                return;
            }

            element.dataset.epConfettiFallbackInit = 'yes';

            const getSize = (key, fallback) => {
                const value = settings[key];
                if (value && typeof value === 'object' && Object.prototype.hasOwnProperty.call(value, 'size')) {
                    const parsed = parseFloat(value.size);
                    return Number.isNaN(parsed) ? fallback : parsed;
                }

                const parsed = parseFloat(value);
                return Number.isNaN(parsed) ? fallback : parsed;
            };

            const options = {
                resize: true,
                useWorker: true,
                particleCount: getSize('ep_widget_cf_particle_count', 100),
                startVelocity: getSize('ep_widget_cf_start_velocity', 45),
                spread: getSize('ep_widget_cf_spread', 70),
                angle: getSize('ep_widget_cf_angle', 90),
                scalar: getSize('ep_widget_cf_scalar', 1),
            };

            if (settings.ep_widget_cf_z_index !== '' && settings.ep_widget_cf_z_index !== null && settings.ep_widget_cf_z_index !== undefined) {
                options.zIndex = settings.ep_widget_cf_z_index;
            }

            if (settings.ep_widget_cf_origin === 'yes') {
                options.origin = {
                    x: getSize('ep_widget_cf_origin_x', 0.5),
                    y: getSize('ep_widget_cf_origin_y', 0.6),
                };
            }

            const colorsRaw = settings.ep_widget_cf_colors || '';
            const shapesRaw = settings.ep_widget_cf_shapes || '';
            const colors = colorsRaw ? colorsRaw.split(',') : ['#26ccff', '#a25afd', '#ff5e7e', '#88ff5a', '#fcff42', '#ffa62d', '#ff36ff'];
            const shapes = shapesRaw ? shapesRaw.split(/,|\|/) : ['circle', 'square'];
            const randomInRange = (min, max) => Math.random() * (max - min) + min;

            if (colorsRaw) {
                options.colors = colors;
            }

            if (shapesRaw) {
                options.shapes = shapes;
            }

            const shapeType = settings.ep_widget_cf_shape_type || 'basic';
            if (shapeType === 'emoji' && settings.ep_widget_cf_shapes_emoji && typeof confetti.shapeFromText === 'function') {
                options.shapes = settings.ep_widget_cf_shapes_emoji
                    .split(/,|\|/)
                    .map((shape) => confetti.shapeFromText({ text: shape }));
            }

            if (shapeType === 'svg' && settings.ep_widget_cf_shapes_svg && typeof confetti.shapeFromPath === 'function') {
                options.shapes = settings.ep_widget_cf_shapes_svg
                    .split('|')
                    .map((shape) => confetti.shapeFromPath({
                        path: shape,
                        matrix: [0.03597122302158273, 0, 0, 0.03597122302158273, -4.856115107913669, -5.071942446043165],
                    }));
            }

            const triggerType = settings.ep_widget_cf_trigger_type || 'load';
            const type = settings.ep_widget_cf_type || 'basic';
            const run = () => {
                if (type === 'random') {
                    options.angle = randomInRange(55, getSize('ep_widget_cf_angle', 90));
                    options.spread = randomInRange(50, getSize('ep_widget_cf_spread', 70));
                    options.particleCount = randomInRange(55, getSize('ep_widget_cf_particle_count', 100));
                }

                if (type === 'fireworks') {
                    const duration = getSize('ep_widget_cf_fireworks_duration', 1500);
                    const animationEnd = Date.now() + duration;
                    const defaults = {
                        startVelocity: getSize('ep_widget_cf_start_velocity', 30),
                        spread: getSize('ep_widget_cf_spread', 360),
                        shapes: shapesRaw ? shapes : ['circle', 'circle', 'square'],
                        ticks: 60,
                        zIndex: settings.ep_widget_cf_z_index || 0,
                    };

                    const interval = setInterval(() => {
                        const timeLeft = animationEnd - Date.now();
                        if (timeLeft <= 0) {
                            clearInterval(interval);
                            return;
                        }
                        const particleCount = 50 * (timeLeft / duration);
                        confetti({ ...defaults, particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } });
                        confetti({ ...defaults, particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } });
                    }, 250);
                    return;
                }

                if (type === 'school-pride') {
                    const duration = getSize('ep_widget_cf_fireworks_duration', 1500);
                    const end = Date.now() + duration;
                    const angle = getSize('ep_widget_cf_angle', 60);
                    const spread = getSize('ep_widget_cf_spread', 55);
                    const particleCount = getSize('ep_widget_cf_particle_count', 2);
                    const prideShapes = shapesRaw ? shapes : ['circle', 'circle', 'square'];

                    const frame = () => {
                        confetti({ particleCount, angle, spread, shapes: prideShapes, colors, origin: { x: 0 } });
                        confetti({ particleCount, angle: angle * 2, spread, shapes: prideShapes, colors, origin: { x: 1 } });
                        if (Date.now() < end) {
                            requestAnimationFrame(frame);
                        }
                    };
                    frame();
                    return;
                }

                if (type === 'snow') {
                    let duration = getSize('ep_widget_cf_fireworks_duration', 1500);
                    if (settings.ep_widget_cf_anim_infinite === 'yes' && !elementorFrontend.isEditMode()) {
                        duration = 24 * 60 * 60 * 1000;
                    }
                    const animationEnd = Date.now() + duration;
                    const particleCount = getSize('ep_widget_cf_particle_count', 1);
                    const startVelocity = getSize('ep_widget_cf_start_velocity', 0);
                    const snowShapes = shapesRaw ? shapes : ['circle'];
                    let skew = 1;

                    const frame = () => {
                        const timeLeft = animationEnd - Date.now();
                        const ticks = Math.max(200, 500 * (timeLeft / duration));
                        skew = Math.max(0.8, skew - 0.001);
                        confetti({
                            particleCount,
                            startVelocity,
                            ticks,
                            origin: { x: Math.random(), y: (Math.random() * skew) - 0.2 },
                            colors,
                            shapes: snowShapes,
                            gravity: randomInRange(0.4, 0.6),
                            scalar: randomInRange(0.4, 1),
                            drift: randomInRange(-0.4, 0.4),
                        });

                        if (timeLeft > 0) {
                            requestAnimationFrame(frame);
                        }
                    };
                    frame();
                    return;
                }

                confetti(options);
            };

            if (triggerType === 'delay') {
                setTimeout(run, getSize('ep_widget_cf_trigger_delay', 1000));
                return;
            }

            if (triggerType === 'ajax-success') {
                jQuery(document).on('ajaxComplete', run);
                return;
            }

            if (triggerType === 'onview' && typeof epObserveTarget === 'function') {
                epObserveTarget(element, run);
                return;
            }

            if (triggerType === 'click' || triggerType === 'mouseenter') {
                const selector = settings.ep_widget_cf_trigger_selector;
                if (selector) {
                    try {
                        document.querySelectorAll(selector).forEach((target) => target.addEventListener(triggerType, run));
                    } catch (e) {
                        // noop
                    }
                    return;
                }
            }

            run();
        });
    };

    runAtomicConfettiFallback();

    window.addEventListener('elementor/frontend/init', () => {

        const ModuleHandler = elementorModules.frontend.handlers.Base;

        const Confetti = ModuleHandler.extend({

            bindEvents() {
                this.run();
            },

            getDefaultSettings() {
                return {
                    resize    : true,
                    useWorker : true,
                };
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_widget_cf_') !== -1) {
                    this.run();
                }
            }, 400),

            settings(key) {
                return this.getElementSettings('ep_widget_cf_' + key);
            },

            parseJSON(value) {
                if (!value || typeof value !== 'string') {
                    return null;
                }

                try {
                    return JSON.parse(value);
                } catch (e) {
                    return null;
                }
            },

            unwrap(value) {
                if (value && typeof value === 'object' && Object.prototype.hasOwnProperty.call(value, 'value')) {
                    return this.unwrap(value.value);
                }

                return value;
            },

            getByPath(obj, path) {
                if (!obj || !path) {
                    return undefined;
                }

                return path.split('.').reduce((acc, key) => {
                    if (acc && typeof acc === 'object' && key in acc) {
                        return acc[key];
                    }

                    return undefined;
                }, obj);
            },

            normalizeAtomicSettings(raw) {
                if (!raw || typeof raw !== 'object') {
                    return null;
                }

                const active = this.unwrap(raw.ep_widget_cf_confetti);
                if (!(active === true || active === 'yes' || active === 1 || active === '1')) {
                    return null;
                }

                const num = (value, fallback) => {
                    const unwrapped = this.unwrap(value);
                    if (unwrapped && typeof unwrapped === 'object' && Object.prototype.hasOwnProperty.call(unwrapped, 'size')) {
                        return isNaN(parseFloat(unwrapped.size)) ? fallback : parseFloat(unwrapped.size);
                    }

                    return isNaN(parseFloat(unwrapped)) ? fallback : parseFloat(unwrapped);
                };

                const str = (value, fallback) => {
                    const unwrapped = this.unwrap(value);
                    return (typeof unwrapped === 'string' && unwrapped !== '') ? unwrapped : fallback;
                };

                const yesNo = (value) => {
                    const unwrapped = this.unwrap(value);
                    return (unwrapped === true || unwrapped === 'yes' || unwrapped === 1 || unwrapped === '1') ? 'yes' : '';
                };

                return {
                    ep_widget_cf_confetti: 'yes',
                    ep_widget_cf_type: str(raw.ep_widget_cf_type, 'basic'),
                    ep_widget_cf_fireworks_duration: { size: num(raw.ep_widget_cf_fireworks_duration, 1500) },
                    ep_widget_cf_anim_infinite: yesNo(raw.ep_widget_cf_anim_infinite),
                    ep_widget_cf_particle_count: { size: num(raw.ep_widget_cf_particle_count, 100) },
                    ep_widget_cf_start_velocity: { size: num(raw.ep_widget_cf_start_velocity, 45) },
                    ep_widget_cf_spread: { size: num(raw.ep_widget_cf_spread, 70) },
                    ep_widget_cf_angle: { size: num(raw.ep_widget_cf_angle, 90) },
                    ep_widget_cf_colors: str(raw.ep_widget_cf_colors, ''),
                    ep_widget_cf_shape_type: str(raw.ep_widget_cf_shape_type, 'basic'),
                    ep_widget_cf_shapes: str(raw.ep_widget_cf_shapes, 'square|circle'),
                    ep_widget_cf_shapes_emoji: str(raw.ep_widget_cf_shapes_emoji, '🎃|🎄|💜'),
                    ep_widget_cf_shapes_svg: str(raw.ep_widget_cf_shapes_svg, ''),
                    ep_widget_cf_scalar: { size: num(raw.ep_widget_cf_scalar, 1) },
                    ep_widget_cf_origin: yesNo(raw.ep_widget_cf_origin),
                    ep_widget_cf_origin_x: { size: num(raw.ep_widget_cf_origin_x, 0.5) },
                    ep_widget_cf_origin_y: { size: num(raw.ep_widget_cf_origin_y, 0.6) },
                    ep_widget_cf_trigger_type: str(raw.ep_widget_cf_trigger_type, 'load'),
                    ep_widget_cf_trigger_selector: str(raw.ep_widget_cf_trigger_selector, ''),
                    ep_widget_cf_trigger_delay: { size: num(raw.ep_widget_cf_trigger_delay, 3000) },
                    ep_widget_cf_z_index: this.unwrap(raw.ep_widget_cf_z_index),
                };
            },

            getAtomicSettingsFromElement(element) {
                if (!element) {
                    return null;
                }

                const target =
                    element.querySelector('[data-ep-confetti]') ||
                    element.closest('[data-ep-confetti]');

                if (!target) {
                    return null;
                }

                const parsed = this.parseJSON(target.getAttribute('data-ep-confetti'));
                if (!parsed) {
                    return null;
                }

                if (Object.prototype.hasOwnProperty.call(parsed, 'ep_widget_cf_confetti')) {
                    return this.normalizeAtomicSettings(parsed) || parsed;
                }

                return null;
            },

            randomInRange(min, max) {
                return Math.random() * (max - min) + min;
            },

            run() {
                const $element = this.$element;
                const atomicSettings = this.getAtomicSettingsFromElement($element && $element[0] ? $element[0] : null);
                const setting = (path) => {
                    if (atomicSettings) {
                        return this.getByPath(atomicSettings, 'ep_widget_cf_' + path);
                    }

                    return this.settings(path);
                };

                if (setting('confetti') !== 'yes') return;

                const options   = this.getDefaultSettings();

                if (setting('z_index'))            options.zIndex        = setting('z_index');
                if (setting('particle_count.size')) options.particleCount = setting('particle_count.size') || 100;
                if (setting('start_velocity.size')) options.startVelocity = setting('start_velocity.size') || 45;
                if (setting('spread.size'))         options.spread        = setting('spread.size') || 70;
                if (setting('scalar.size'))         options.scalar        = setting('scalar.size') || 1;
                if (setting('angle.size'))          options.angle         = setting('angle.size') || 90;

                if (setting('origin') && (setting('origin_x.size') || setting('origin_y.size'))) {
                    options.origin = {
                        x: setting('origin_x.size') || 0.5,
                        y: setting('origin_y.size') || 0.6
                    };
                }

                const colorsRaw  = setting('colors') || '';
                const shapesRaw  = setting('shapes') || '';
                const colors     = colorsRaw ? colorsRaw.split(',')     : ['#26ccff', '#a25afd', '#ff5e7e', '#88ff5a', '#fcff42', '#ffa62d', '#ff36ff'];
                const shapes     = shapesRaw ? shapesRaw.split(/,|\|/)  : ['circle', 'square'];

                if (colorsRaw) options.colors = colors;
                if (shapesRaw) options.shapes = shapes;

                const shapeType = setting('shape_type');

                if (shapeType === 'emoji' && setting('shapes_emoji')) {
                    options.shapes = setting('shapes_emoji')
                        .split(/,|\|/)
                        .map(shape => confetti.shapeFromText({ text: shape }));
                }

                if (shapeType === 'svg' && setting('shapes_svg')) {
                    options.shapes = setting('shapes_svg')
                        .split('|')
                        .map(shape => confetti.shapeFromPath({
                            path   : shape,
                            matrix : [0.03597122302158273, 0, 0, 0.03597122302158273, -4.856115107913669, -5.071942446043165]
                        }));
                }

                const type        = setting('type');
                const triggerType = setting('trigger_type');

                const executeConfetti = () => {

                    if (type === 'random') {
                        options.angle         = this.randomInRange(55, setting('angle.size') || 90);
                        options.spread        = this.randomInRange(50, setting('spread.size') || 70);
                        options.particleCount = this.randomInRange(55, setting('particle_count.size') || 100);
                    }

                    if (type === 'fireworks') {
                        const duration     = setting('fireworks_duration.size') || 1500;
                        const animationEnd = Date.now() + duration;
                        const defaults     = {
                            startVelocity : setting('start_velocity.size') || 30,
                            spread        : setting('spread.size') || 360,
                            shapes        : shapesRaw ? shapes : ['circle', 'circle', 'square'],
                            ticks         : 60,
                            zIndex        : setting('z_index') || 0
                        };

                        const interval = setInterval(() => {
                            const timeLeft = animationEnd - Date.now();
                            if (timeLeft <= 0) {
                                clearInterval(interval);
                                return;
                            }
                            const particleCount = 50 * (timeLeft / duration);
                            confetti({ ...defaults, particleCount, origin: { x: this.randomInRange(0.1, 0.3), y: Math.random() - 0.2 } });
                            confetti({ ...defaults, particleCount, origin: { x: this.randomInRange(0.7, 0.9), y: Math.random() - 0.2 } });
                        }, 250);
                    }

                    if (type === 'school-pride') {
                        const duration      = setting('fireworks_duration.size') || 1500;
                        const end           = Date.now() + duration;
                        const angle         = setting('angle.size') || 60;
                        const spread        = setting('spread.size') || 55;
                        const particleCount = setting('particle_count.size') || 2;
                        const prideShapes   = shapesRaw ? shapes : ['circle', 'circle', 'square'];

                        const frame = () => {
                            confetti({ particleCount, angle,         spread, shapes: prideShapes, colors, origin: { x: 0 } });
                            confetti({ particleCount, angle: angle * 2, spread, shapes: prideShapes, colors, origin: { x: 1 } });
                            if (Date.now() < end) requestAnimationFrame(frame);
                        };
                        frame();
                    }

                    if (type === 'snow') {
                        let duration = setting('fireworks_duration.size') || 1500;
                        if (setting('anim_infinite') === 'yes' && !elementorFrontend.isEditMode()) {
                            duration = 24 * 60 * 60 * 1000;
                        }
                        const animationEnd  = Date.now() + duration;
                        const particleCount = setting('particle_count.size') || 1;
                        const startVelocity = setting('start_velocity.size') || 0;
                        const snowShapes    = shapesRaw ? shapes : ['circle'];
                        let skew = 1;

                        const frame = () => {
                            const timeLeft = animationEnd - Date.now();
                            const ticks    = Math.max(200, 500 * (timeLeft / duration));
                            skew = Math.max(0.8, skew - 0.001);
                            confetti({
                                particleCount,
                                startVelocity,
                                ticks,
                                origin  : { x: Math.random(), y: (Math.random() * skew) - 0.2 },
                                colors,
                                shapes  : snowShapes,
                                gravity : this.randomInRange(0.4, 0.6),
                                scalar  : this.randomInRange(0.4, 1),
                                drift   : this.randomInRange(-0.4, 0.4)
                            });
                            if (timeLeft > 0) requestAnimationFrame(frame);
                        };
                        frame();
                    }

                    if (type === 'basic' || type === 'random') {
                        this.instance = confetti(options);
                    }
                };

                if (triggerType === 'click' || triggerType === 'mouseenter') {
                    const selector = setting('trigger_selector');
                    if (selector) {
                        try {
                            document.querySelectorAll(selector).forEach(el => {
                                el.addEventListener(triggerType, executeConfetti);
                            });
                        } catch (e) {
                            console.error('Invalid confetti trigger selector:', selector, e);
                        }
                    }
                } else if (triggerType === 'ajax-success') {
                    // ajaxComplete has no native equivalent for jQuery AJAX in WordPress
                    jQuery(document).on('ajaxComplete', executeConfetti);
                } else if (triggerType === 'delay') {
                    setTimeout(executeConfetti, setting('trigger_delay.size') || 1000);
                } else if (triggerType === 'onview') {
                    epObserveTarget($element[0], executeConfetti);
                } else {
                    executeConfetti();
                }
            }
        });

        const addHandler = ($scope) => {
            elementorFrontend.elementsHandler.addHandler(Confetti, { $element: $scope });
        };

        elementorFrontend.hooks.addAction('frontend/element_ready/widget',    addHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/section',   addHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/container', addHandler);

        // Atomic fallback: run from server-injected payload even if handler hook misses.
        runAtomicConfettiFallback();
        window.setTimeout(runAtomicConfettiFallback, 300);
        window.setTimeout(runAtomicConfettiFallback, 1000);

    });

})();

/**
 * End confetti effects widget script
 */

/**
 * Start coupon reveal widget script
 */

(() => {
    'use strict';

    const widgetCoupon = (scope) => {
        const scopeElement = scope instanceof jQuery ? scope[0] : scope;

        const widgetContainer = scopeElement.querySelector('.bdt-coupon-code');
        if (!widgetContainer) return;

        const settingsData = widgetContainer.dataset.settings;
        if (!settingsData) return;

        let settings;
        try {
            settings = typeof settingsData === 'string' ? JSON.parse(settingsData) : settingsData;
        } catch (e) {
            console.error('Failed to parse coupon code settings:', e);
            return;
        }

        const editMode   = Boolean(elementorFrontend.isEditMode());
        const triggerURL = settings.triggerURL;
        let couponExecuted = false;

        const decodeCoupon = async (couponCode) => {
            try {
                const body = new URLSearchParams({
                    action      : 'element_pack_coupon_code',
                    coupon_code : couponCode
                });
                const response = await fetch(settings.adminAjaxURL, { method: 'POST', body });
                const result   = await response.text();

                const couponEl = document.querySelector(settings.couponId);
                if (!couponEl) return;

                const textEl = couponEl.querySelector('.bdt-coupon-code-text');
                if (textEl) {
                    textEl.innerHTML = result;
                } else {
                    couponEl.innerHTML = result;
                }
            } catch (e) {
                const couponEl = document.querySelector(settings.couponId);
                if (couponEl) couponEl.innerHTML = 'Something wrong, please contact support team.';
                console.error('Coupon decode failed:', e);
            }
        };

        const openTriggerURL = (url) => {
            const target = settings.is_external === true ? '_blank' : '_self';
            window.open(url, target);

            if (target === '_self' && url.includes('#')) {
                const hash  = url.split('#')[1];
                const hashEl = hash ? document.getElementById(hash) : null;
                if (hashEl) {
                    const top = hashEl.getBoundingClientRect().top + window.scrollY - 100;
                    window.scrollTo({ top, behavior: 'smooth' });
                }
            }
        };

        const displayCoupon = () => widgetContainer.classList.add('active');

        const formSubmitted = () => {
            displayCoupon();
            if (triggerURL) openTriggerURL(triggerURL);
            decodeCoupon(settings.couponCode);
            couponExecuted = true;
        };

        if (settings.triggerByAction !== true) {
            const clipboard = new ClipboardJS(settings.couponMsgId, {
                target: (trigger) => trigger.nextElementSibling
            });

            clipboard.on('success', (event) => {
                event.trigger.classList.add('active');
                event.clearSelection();
                setTimeout(() => event.trigger.classList.remove('active'), 3000);
            });
        }

        if (settings.couponLayout === 'style-2' && settings.triggerByAction === true) {
            const clipboard = new ClipboardJS(settings.couponId, {
                target: (trigger) => trigger
            });

            clipboard.on('success', (event) => {
                widgetContainer.querySelector(settings.couponId)?.classList.add('active');
                event.clearSelection();
                setTimeout(() => {
                    widgetContainer.querySelector(settings.couponId)?.classList.remove('active');
                }, 2000);
            });

            widgetContainer.addEventListener('click', () => {
                if (!widgetContainer.classList.contains('active') && settings.triggerAttention !== false) {
                    const triggerSelector = settings.triggerInputId;
                    const form = document.querySelector(`[name="${triggerSelector.slice(1)}"]`)?.closest('form');
                    form?.classList.add('ep-shake-animation-cc');
                    setTimeout(() => form?.classList.remove('ep-shake-animation-cc'), 5000);
                }
            });
        }

        widgetContainer.addEventListener('click', () => {
            if (!widgetContainer.classList.contains('active') && settings.triggerByAction !== true) {
                displayCoupon();
                if (triggerURL) {
                    setTimeout(() => openTriggerURL(triggerURL), 2000);
                }
            }
        });

        // jQuery ajaxComplete has no native equivalent — kept intentionally
        if (!editMode) {
            jQuery(document).on('ajaxComplete', (event, jqxhr, ajaxSettings) => {
                if (couponExecuted) return;
                const triggerInput = settings.triggerInputId;

                if (triggerInput && settings.triggerByAction === true) {
                    if (ajaxSettings.data?.toLowerCase().includes(triggerInput.slice(1))) {
                        formSubmitted();
                    }
                } else if (settings.triggerByAction === true) {
                    formSubmitted();
                }
            });
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (window.elementorFrontend?.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-coupon-code.default', widgetCoupon);
        }
    });

})();

/**
 * End coupon reveal widget script
 */

/**
 * Start custom carousel widget script
 */

(() => {
    'use strict';

    const widgetCustomCarousel = async (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const carouselEl = scopeEl.querySelector('.bdt-ep-custom-carousel');
        if (!carouselEl) return;

        const containerEl = carouselEl.querySelector('.swiper-carousel');
        const settings    = JSON.parse(carouselEl.dataset.settings || '{}');

        const Swiper = elementorFrontend.utils.swiper;
        await new Swiper(containerEl, settings);

        if (settings.pauseOnHover) {
            containerEl.addEventListener('mouseenter', () => containerEl.swiper.autoplay.stop());
            containerEl.addEventListener('mouseleave', () => containerEl.swiper.autoplay.start());
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-custom-carousel.default',          widgetCustomCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-custom-carousel.bdt-custom-content', widgetCustomCarousel);
    });

})();

/**
 * End custom carousel widget script
 */

/**
 * Start dynamic carousel widget script
 */

(() => {
    'use strict';

    const widgetCarousel = async (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const carouselEl = scopeEl.querySelector('.bdt-dynamic-carousel');
        if (!carouselEl) return;

        const containerEl = carouselEl.querySelector('.swiper-carousel');
        const settings    = JSON.parse(carouselEl.dataset.settings || '{}');

        const Swiper = elementorFrontend.utils.swiper;
        await new Swiper(containerEl, settings);

        if (settings.pauseOnHover) {
            containerEl.addEventListener('mouseenter', () => containerEl.swiper.autoplay.stop());
            containerEl.addEventListener('mouseleave', () => containerEl.swiper.autoplay.start());
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-dynamic-carousel.default', widgetCarousel);
    });

})();

/**
 * End dynamic carousel widget script
 */

/**
 * Start background expand widget script
 */

(() => {
  "use strict";

  window.addEventListener("elementor/frontend/init", () => {
    const ModuleHandler = elementorModules.frontend.handlers.Base;

    const BackgroundExpand = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },

      getDefaultSettings: function () {
        return {
          direction: "alternate",
        };
      },

      settings: function (key) {
        return this.getElementSettings(`ep_bg_expand_${key}`);
      },

      onElementChange: debounce(function (prop) {
        if (prop.indexOf("ep_bg_expand_") !== -1) {
          this.run();
        }
      }, 400),

      run: function () {
        const options = this.getDefaultSettings();
        let element = this.$element.get(0);

        if (this.settings("enable") !== "yes") {
          return;
        }

        if (this.settings("selector")) {
          const customSelector = this.settings("selector");
          element = typeof customSelector === "string" 
            ? document.querySelector(customSelector) 
            : customSelector;
        }

        if (!element) {
          console.error("Background expand element not found");
          return;
        }

        if (typeof gsap === "undefined" || !gsap.timeline) {
          console.error("GSAP library is not loaded");
          return;
        }

        const tl = gsap.timeline({
          scrollTrigger: {
            trigger: element,
            start: "top center",
            end: "100% bottom",
            toggleActions: "restart none none reverse",
            onEnter: () => element.classList.add("bdt-bx-active"),
            onEnterBack: () => element.classList.remove("bdt-bx-active"),
          },
        });
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/section",
      (scope) => {
        elementorFrontend.elementsHandler.addHandler(BackgroundExpand, {
          $element: scope,
        });
      }
    );

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/container",
      (scope) => {
        elementorFrontend.elementsHandler.addHandler(BackgroundExpand, {
          $element: scope,
        });
      }
    );
  });
})();

/**
 * End background expand widget script
 */

/**
 * Start webhook form widget script
 */

(function ($, elementor) {
    "use strict";
    var widgetWebhookForm = function ($scope, $) {
        var $formWrapper = $scope.find(".bdt-ep-webhook-form.without-recaptcha"),
            $form = $formWrapper.find(".bdt-ep-webhook-form-form"),
            $settings = $formWrapper.data("settings");

        if (!$formWrapper.length) {
            return;
        }

        $($settings.id).find(".bdt-ep-webhook-form-form").on('submit', function (e) {
            e.preventDefault();
            send_form_data($form);
        });
    };

    function send_form_data(form) {
        var langStr = window.ElementPackConfig.contact_form;

        var formData = $(form).serialize();
        formData = formData + "&action=submit_webhook_form";
        formData = formData + "&nonce=" + ElementPackConfig.nonce;

        $.ajax({
            url: ElementPackConfig.ajaxurl,
            type: "post",
            data: formData,
            beforeSend: function () {
                bdtUIkit.notification({
                    message: "<div bdt-spinner></div> " + langStr.sending_msg,
                    timeout: false,
                    status: "primary",
                });
            },
            success: function (res) {
                let response = JSON.parse(res);
                bdtUIkit.notification.closeAll();

                if (true == response.success) {
                    bdtUIkit.notification({
                        message: '<div bdt-icon="icon: check"></div> ' + response.message,
                    });
                } else {
                    bdtUIkit.notification({
                        message: '<div bdt-icon="icon: close"></div> ' + response.message,
                    });
                }
            },
        });
    }

    function elementPackWebFormGIC() {

        var langStr = window.ElementPackConfig.contact_form;

        return new Promise(function (resolve, reject) {

            if (grecaptcha === undefined) {
                bdtUIkit.notification({
                    message: '<div bdt-spinner></div> ' + langStr.captcha_nd,
                    timeout: false,
                    status: 'warning'
                });
                reject();
            }

            var response = grecaptcha.getResponse();

            if (!response) {
                bdtUIkit.notification({
                    message: '<div bdt-spinner></div> ' + langStr.captcha_nr,
                    timeout: false,
                    status: 'warning'
                });
                reject();
            }

            var $webhookForm = $('textarea.g-recaptcha-response').filter(function () {
                return $(this).val() === response;
            }).closest('form.bdt-ep-webhook-form-form');

            var contactFormAction = $webhookForm.attr('action');

            if (contactFormAction && contactFormAction !== '') {
                send_form_data($webhookForm);
            }

        grecaptcha.reset();

    });

    }

    window.elementPackWfGICCB = elementPackWebFormGIC;

    jQuery(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/bdt-webhook-form.default",
            widgetWebhookForm
        );
    });
})(jQuery, window.elementorFrontend);

/**
 * End webhook form widget script
 */
(function ($, elementor) {

    'use strict';

    $(window).on('elementor/frontend/init', function ($) {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            ScrollFillEffect;

        function killElementScrollTriggers(element) {
            if (typeof ScrollTrigger === 'undefined' || !ScrollTrigger.getAll) {
                return;
            }

            ScrollTrigger.getAll().forEach(function (trigger) {
                if (trigger.trigger === element) {
                    trigger.kill();
                }
            });
        }

        function parseJSON(value) {
            if (!value || 'string' !== typeof value) {
                return null;
            }

            try {
                return JSON.parse(value);
            } catch (error) {
                return null;
            }
        }

        function unwrapAtomicValue(value) {
            if (value && 'object' === typeof value && Object.prototype.hasOwnProperty.call(value, 'value')) {
                return unwrapAtomicValue(value.value);
            }

            return value;
        }

        function getAtomicSetting(settings, key, fallback) {
            var value = settings ? settings[key] : null;

            value = unwrapAtomicValue(value);

            return 'undefined' === typeof value || null === value || '' === value ? fallback : value;
        }

        function isAtomicEnabled(value) {
            value = unwrapAtomicValue(value);

            return true === value || 'yes' === value || 1 === value || '1' === value;
        }

        function formatAtomicNumber(value) {
            value = parseFloat(value);

            if (isNaN(value)) {
                value = 90;
            }

            return parseFloat(value.toFixed(4)).toString();
        }

        function atomicNumberValue(value, fallback) {
            value = unwrapAtomicValue(value);

            if (value && 'object' === typeof value && 'undefined' !== typeof value.size) {
                value = value.size;
            }

            value = parseFloat(value);

            return isNaN(value) ? fallback : value;
        }

        function atomicStringValue(value, fallback) {
            value = unwrapAtomicValue(value);

            return 'string' === typeof value && '' !== value ? value : fallback;
        }

        function normalizeAtomicSettings(settings) {
            var fillBackground;
            var startColor;
            var endColor;
            var angle;

            if (!settings) {
                return null;
            }

            if (settings.enable || settings.animation_type || settings.fill_bg) {
                return settings;
            }

            if (!isAtomicEnabled(settings.ep_widget_sf_fx_enable)) {
                return null;
            }

            fillBackground = getAtomicSetting(settings, 'ep_widget_sf_fx_fill_bg', '');

            if (!fillBackground) {
                startColor = getAtomicSetting(settings, 'ep_widget_sf_fx_fill_bg_start_color', '#08aeec');
                endColor = getAtomicSetting(settings, 'ep_widget_sf_fx_fill_bg_end_color', '#2af598');
                angle = getAtomicSetting(settings, 'ep_widget_sf_fx_fill_bg_angle', 90);
                fillBackground = 'linear-gradient(' + formatAtomicNumber(angle) + 'deg, ' + startColor + ' 0%, ' + endColor + ' 100%)';
            }

            return {
                enable: 'yes',
                animation_type: getAtomicSetting(settings, 'ep_widget_sf_fx_animation_type', 'scroll_fill'),
                base_color: getAtomicSetting(settings, 'ep_widget_sf_fx_base_color', 'rgba(156, 156, 156, 0.5)'),
                fill_bg: fillBackground,
                sg_start_scale: { size: atomicNumberValue(settings.ep_widget_sf_fx_sg_start_scale, 2) },
                sg_end_scale: { size: atomicNumberValue(settings.ep_widget_sf_fx_sg_end_scale, 1) },
                sg_start_opacity: { size: atomicNumberValue(settings.ep_widget_sf_fx_sg_start_opacity, 0) },
                sg_end_opacity: { size: atomicNumberValue(settings.ep_widget_sf_fx_sg_end_opacity, 1) },
                sg_start_color: atomicStringValue(settings.ep_widget_sf_fx_sg_start_color, '#00e239'),
                sg_end_color: atomicStringValue(settings.ep_widget_sf_fx_sg_end_color, '#ffffff'),
                sg_stagger: { size: atomicNumberValue(settings.ep_widget_sf_fx_sg_stagger, 0.1) },
                sg_duration: { size: atomicNumberValue(settings.ep_widget_sf_fx_sg_duration, 1.5) },
                sg_start_trigger: atomicStringValue(settings.ep_widget_sf_fx_sg_start_trigger, 'top 80%'),
                sg_end_trigger: atomicStringValue(settings.ep_widget_sf_fx_sg_end_trigger, 'top 30%'),
                rgm_mask_color: atomicStringValue(settings.ep_widget_sf_fx_rgm_mask_color, '#000000'),
                rgm_x_percent: { size: atomicNumberValue(settings.ep_widget_sf_fx_rgm_x_percent, -100) },
                rgm_start_trigger: atomicStringValue(settings.ep_widget_sf_fx_rgm_start_trigger, 'top 80%'),
                rgm_end_trigger: atomicStringValue(settings.ep_widget_sf_fx_rgm_end_trigger, 'top 20%')
            };
        }

        function getAtomicHeadingSettings(heading) {
            var wrapper = heading.closest('[data-id]');
            var settings = parseJSON(heading.getAttribute('data-ep-scroll-fill-effect'));

            if (!settings && wrapper) {
                settings = parseJSON(wrapper.getAttribute('data-ep-scroll-fill-effect'));
            }

            if (!settings && wrapper) {
                settings = jQuery(wrapper).data('settings') || parseJSON(wrapper.getAttribute('data-settings'));
            }

            return normalizeAtomicSettings(settings);
        }

        function applyAtomicScrollFillEffect($heading, settings) {
            if (!$heading.length || !settings) {
                return;
            }

            var animationType = settings.animation_type || 'scroll_fill';

            if ('scaling_gradient_text' === animationType) {
                applyAtomicScalingGradientEffect($heading, settings);
            } else if ('reveal_gradient_mask' === animationType) {
                applyAtomicRevealGradientMask($heading, settings);
            } else {
                applyAtomicScrollFill($heading, settings);
            }
        }

        function applyAtomicScrollFill($heading, settings) {
            $heading.each(function () {
                var heading = this;
                var fillBackground = settings.fill_bg || 'linear-gradient(90deg, #08aeec 0%, #2af598 100%)';
                var baseColor = settings.base_color || 'rgba(156, 156, 156, 0.5)';

                killElementScrollTriggers(heading);

                heading.setAttribute('data-ep-scroll-fill-effect', JSON.stringify(settings));
                heading.classList.add('bdt-scroll-effect-yes', 'bdt-scroll-effect-scroll_fill');

                heading.style.backgroundImage = fillBackground + ', linear-gradient(' + baseColor + ', ' + baseColor + ')';
                heading.style.backgroundRepeat = 'no-repeat';
                heading.style.backgroundSize = '0% 100%, 100% 100%';
                heading.style.backgroundPosition = 'left center, left center';
                heading.style.transition = 'background-size 0.25s linear';
                heading.style.setProperty('background-clip', 'text');
                heading.style.setProperty('-webkit-background-clip', 'text');
                heading.style.setProperty('color', 'transparent');
                heading.style.setProperty('-webkit-text-fill-color', 'transparent');

                if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
                    heading.style.backgroundSize = '100% 100%, 100% 100%';
                    return;
                }

                gsap.to(heading, {
                    scrollTrigger: {
                        trigger: heading,
                        start: 'top bottom',
                        end: 'bottom center',
                        scrub: 1,
                        markers: false,
                        invalidateOnRefresh: true
                    },
                    backgroundSize: '100% 100%, 100% 100%'
                });
            });
        }

        function applyAtomicScalingGradientEffect($heading, settings) {
            $heading.each(function () {
                var heading = this;
                var $animatedText = jQuery(heading);

                if ($animatedText.hasClass('bdt-sg-processed')) {
                    return;
                }

                $animatedText.addClass('bdt-sg-processed');
                heading.classList.add('bdt-scroll-effect-yes', 'bdt-scroll-effect-scaling_gradient_text');

                var startScale = atomicNumberValue(settings.sg_start_scale, 2);
                var endScale = atomicNumberValue(settings.sg_end_scale, 1);
                var startOpacity = atomicNumberValue(settings.sg_start_opacity, 0);
                var endOpacity = atomicNumberValue(settings.sg_end_opacity, 1);
                var startColor = atomicStringValue(settings.sg_start_color, '#08AEEC');
                var endColor = atomicStringValue(settings.sg_end_color, '#ffffff');
                var stagger = atomicNumberValue(settings.sg_stagger, 0.1);
                var duration = atomicNumberValue(settings.sg_duration, 1.5);
                var startTrigger = atomicStringValue(settings.sg_start_trigger, 'top bottom');
                var endTrigger = atomicStringValue(settings.sg_end_trigger, 'bottom top');

                var textContent = $animatedText.text();
                var splitHTML = textContent.split('').map(function (char) {
                    if (char === ' ') {
                        return '<span class="bdt-sg-letter" style="display: inline-flex;">&nbsp;</span>';
                    }

                    return '<span class="bdt-sg-letter" style="display: inline-flex;">' + char + '</span>';
                }).join('');

                $animatedText.html(splitHTML);

                var letters = $animatedText.find('.bdt-sg-letter');

                if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
                    letters.css({
                        transform: 'scale(' + endScale + ')',
                        opacity: endOpacity,
                        color: endColor,
                        '-webkit-text-fill-color': endColor
                    });
                    return;
                }

                gsap.set(letters.get(), {
                    scale: startScale,
                    opacity: startOpacity,
                    color: startColor,
                    webkitTextFillColor: startColor
                });

                gsap.fromTo(
                    letters.get(),
                    {
                        scale: startScale,
                        opacity: startOpacity,
                        color: startColor,
                        webkitTextFillColor: startColor
                    },
                    {
                        scrollTrigger: {
                            trigger: heading,
                            start: startTrigger,
                            end: endTrigger,
                            scrub: 1,
                            markers: false,
                            invalidateOnRefresh: true
                        },
                        scale: endScale,
                        opacity: endOpacity,
                        color: endColor,
                        webkitTextFillColor: endColor,
                        stagger: stagger,
                        ease: 'power2.out',
                        duration: duration
                    }
                );
            });
        }

        function applyAtomicRevealGradientMask($heading, settings) {
            $heading.each(function () {
                var heading = this;
                var $textElement = jQuery(heading);

                if ($textElement.hasClass('bdt-rgm-processed')) {
                    return;
                }

                $textElement.addClass('bdt-rgm-processed');
                heading.classList.add('bdt-scroll-effect-yes', 'bdt-scroll-effect-reveal_gradient_mask');

                var xPercent = atomicNumberValue(settings.rgm_x_percent, -100);
                var startTrigger = atomicStringValue(settings.rgm_start_trigger, 'top 80%');
                var endTrigger = atomicStringValue(settings.rgm_end_trigger, 'top 20%');
                var maskColor = atomicStringValue(settings.rgm_mask_color, '#262626');

                var $wrapper = jQuery('<div class="bdt-rgm-wrapper"></div>');
                $wrapper.css({
                    'position': 'relative',
                    'display': 'inline-block',
                    'width': 'auto',
                    'height': 'auto'
                });

                var $mask = jQuery('<div class="bdt-rgm-mask"></div>');
                $mask.css({
                    'position': 'absolute',
                    'top': '0',
                    'bottom': '0',
                    'left': '100%',
                    'width': '200%',
                    'max-width': 'none',
                    'background-image': 'linear-gradient(to right, transparent, ' + maskColor + ' 50%, ' + maskColor + ')',
                    'pointer-events': 'none',
                    'z-index': '1'
                });

                $wrapper.html($textElement.html());
                $wrapper.append($mask);
                $textElement.html($wrapper);
                $textElement.css({ 'display': 'inline-block', 'position': 'relative' });

                if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
                    return;
                }

                gsap.set($mask.get(0), { xPercent: 0 });

                gsap.to($mask.get(0), {
                    xPercent: xPercent,
                    ease: 'none',
                    scrollTrigger: {
                        trigger: heading,
                        start: startTrigger,
                        end: endTrigger,
                        scrub: 1,
                        invalidateOnRefresh: true
                    }
                });
            });
        }

        function runAtomicScrollFillEffects() {
            var $headings = jQuery('.elementor-widget-e-heading .e-heading-base, [data-widget_type^="e-heading"] .e-heading-base, .e-heading-base[data-ep-scroll-fill-effect]');

            if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined' && gsap.registerPlugin) {
                gsap.registerPlugin(ScrollTrigger);
            }

            $headings.each(function () {
                applyAtomicScrollFillEffect(jQuery(this), getAtomicHeadingSettings(this));
            });

            if (typeof ScrollTrigger !== 'undefined' && ScrollTrigger.refresh) {
                ScrollTrigger.refresh();
            }
        }

        ScrollFillEffect = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },
            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_widget_sf_fx_') !== -1) {
                    this.cleanup();
                    this.run();
                }
            }, 400),
            settings: function (key) {
                return this.getElementSettings('ep_widget_sf_fx_' + key);
            },
            cleanup: function () {
                var $element = this.$element;

                if (typeof ScrollTrigger !== 'undefined' && ScrollTrigger.getAll) {
                    ScrollTrigger.getAll().forEach(function (trigger) {
                        if (trigger.trigger && $element.find(trigger.trigger).length > 0) {
                            trigger.kill();
                        }
                    });
                }

                $element.find('.bdt-sg-processed, .bdt-rgm-processed').removeClass('bdt-sg-processed bdt-rgm-processed');
            },
            run: function () {
                var $element = this.$element;
                var self = this;

                if (this.settings('enable') !== 'yes') {
                    this.cleanup();
                    return;
                }

                // Initialize immediately without intersection observer to prevent scroll pause
                var $selector;

                if ($element.hasClass('elementor-widget-text-editor')) {
                    $selector = $element.children('h1, h2, h3, h4, h5, h6, p, div, span');

                    if ($selector.length === 0) {
                        $selector = $element.find('h1, h2, h3, h4, h5, h6, p').first();
                    }
                } else {
                    $selector = jQuery($element).find('.elementor-heading-title, .bdt-heading-tag span, .bdt-ep-advanced-heading-main-title-inner');
                }

                if ($selector.length === 0) {
                    return;
                }

                var animationType = self.settings('animation_type');
                var editScaling = animationType === 'scaling_gradient_text' && Boolean(elementorFrontend.isEditMode());
                var editReveal = animationType === 'reveal_gradient_mask' && Boolean(elementorFrontend.isEditMode());
                if (!(editScaling || editReveal)) {
                    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
                        return;
                    }
                    gsap.registerPlugin(ScrollTrigger);
                }


                if (animationType === 'scaling_gradient_text') {

                    const editMode = Boolean(elementorFrontend.isEditMode());
                    if (editMode) {
                        return;
                    }
                    self.applyScalingGradientEffect($selector);
                } else if (animationType === 'reveal_gradient_mask') {
                    const editMode = Boolean(elementorFrontend.isEditMode());
                    if (editMode) {
                        return;
                    }
                    self.applyRevealGradientMask($selector);
                } else {

                    gsap.set($selector, {
                        backgroundSize: '0% 100%'
                    });

                    gsap.to($selector, {
                        scrollTrigger: {
                            trigger: $selector,
                            start: "top bottom",
                            end: "bottom center",
                            scrub: 1,
                            markers: false,
                            invalidateOnRefresh: true
                        },
                        backgroundSize: '100% 200%',
                    });
                }
            },

            applyScalingGradientEffect: function($selector) {
                var self = this;
                
                $selector.each(function() {
                    var $animatedText = jQuery(this);

                    if ($animatedText.hasClass('bdt-sg-processed')) {
                        return;
                    }

                    $animatedText.addClass('bdt-sg-processed');

                    var startScale = self.settings('sg_start_scale') ? self.settings('sg_start_scale').size : 2;
                    var endScale = self.settings('sg_end_scale') ? self.settings('sg_end_scale').size : 1;
                    var startOpacity = typeof self.settings('sg_start_opacity') !== 'undefined' && self.settings('sg_start_opacity') !== null 
                        ? self.settings('sg_start_opacity').size : 0;
                    var endOpacity = typeof self.settings('sg_end_opacity') !== 'undefined' && self.settings('sg_end_opacity') !== null 
                        ? self.settings('sg_end_opacity').size : 1;

                    var startColorSetting = self.settings('sg_start_color');
                    var endColorSetting = self.settings('sg_end_color');

                    var startColor = (startColorSetting && startColorSetting !== '') ? startColorSetting : '#08AEEC';
                    var endColor = (endColorSetting && endColorSetting !== '') ? endColorSetting : '#ffffff';

                    var stagger = self.settings('sg_stagger') ? self.settings('sg_stagger').size : 0.1;
                    var duration = self.settings('sg_duration') ? self.settings('sg_duration').size : 1.5;
                    var startTrigger = self.settings('sg_start_trigger') || 'top bottom';
                    var endTrigger = self.settings('sg_end_trigger') || 'bottom top';

                    var textContent = $animatedText.text();
                    var splitHTML = textContent.split('').map(function(char) {
                        if (char === ' ') {
                            return '<span class="bdt-sg-letter" style="display: inline-flex;">&nbsp;</span>';
                        } else {
                            return '<span class="bdt-sg-letter" style="display: inline-flex;">' + char + '</span>';
                        }
                    }).join('');
                    
                    $animatedText.html(splitHTML);

                    var letters = $animatedText.find('.bdt-sg-letter');

                    // Set initial state immediately to prevent flash of unstyled content
                    gsap.set(letters.get(), {
                        scale: startScale,
                        opacity: startOpacity,
                        color: startColor,
                        webkitTextFillColor: startColor
                    });

                    gsap.fromTo(
                        letters.get(),
                        {
                            scale: startScale,
                            opacity: startOpacity,
                            color: startColor,
                            webkitTextFillColor: startColor
                        },
                        {
                            scrollTrigger: {
                                trigger: $animatedText.get(0),
                                start: startTrigger,
                                end: endTrigger,
                                scrub: 1,
                                markers: false,
                                invalidateOnRefresh: true
                            },
                            scale: endScale,
                            opacity: endOpacity,
                            color: endColor,
                            webkitTextFillColor: endColor,
                            stagger: stagger,
                            ease: "power2.out",
                            duration: duration
                        }
                    );
                });
            },

            applyRevealGradientMask: function($selector) {
                var self = this;
                
                $selector.each(function() {
                    var $textElement = jQuery(this);

                    if ($textElement.hasClass('bdt-rgm-processed')) {
                        return;
                    }
                    $textElement.addClass('bdt-rgm-processed');

                    var xPercent     = self.settings('rgm_x_percent')     ? self.settings('rgm_x_percent').size : -100;
                    var startTrigger = self.settings('rgm_start_trigger') || 'top 80%';
                    var endTrigger   = self.settings('rgm_end_trigger')   || 'top 20%';

                    var maskColorSetting = self.settings('rgm_mask_color');
                    var maskColor = (maskColorSetting && maskColorSetting !== '') ? maskColorSetting : '#262626';
                    
                    // ── Build DOM ──────────────────────────────────────────────
                    // Wrapper shrinks to text width so the mask percentage is relative
                    // to the text block, not the full container.
                    var $wrapper = jQuery('<div class="bdt-rgm-wrapper"></div>');
                    $wrapper.css({
                        'position': 'relative',
                        'display':  'inline-block',
                        'width':    'auto',
                        'height':   'auto'
                    });

                    // Mask starts at left:100% (just off the right edge of the wrapper)
                    // and slides left. Gradient: transparent → solid so the leading edge
                    // is a soft fade rather than a hard cut.
                    var $mask = jQuery('<div class="bdt-rgm-mask"></div>');
                    $mask.css({
                        'position':         'absolute',
                        'top':              '0',
                        'bottom':           '0',
                        'left':             '100%',
                        'width':            '200%',
                        'max-width':        'none',
                        'background-image': 'linear-gradient(to right, transparent, ' + maskColor + ' 50%, ' + maskColor + ')',
                        'pointer-events':   'none',
                        'z-index':          '1'
                    });

                    $wrapper.html($textElement.html());
                    $wrapper.append($mask);
                    $textElement.html($wrapper);
                    $textElement.css({ 'display': 'inline-block', 'position': 'relative' });

                    // Ensure mask starts at its resting position
                    gsap.set($mask.get(0), { xPercent: 0 });

                    // ── Scroll-driven reveal ───────────────────────────────────
                    // scrub:1 keeps animation 1 second behind the scroll position
                    // for a smooth, natural feel. Scrolling back reverses the reveal.
                    gsap.to($mask.get(0), {
                        xPercent: xPercent,
                        ease: 'none',
                        scrollTrigger: {
                            trigger:             $textElement.get(0),
                            start:               startTrigger,
                            end:                 endTrigger,
                            scrub:               1,
                            invalidateOnRefresh: true
                        }
                    });
                });
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/heading.default', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(ScrollFillEffect, {
                $element: $scope
            });
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/text-editor.default', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(ScrollFillEffect, {
                $element: $scope
            });
        });
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-advanced-heading.default', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(ScrollFillEffect, {
                $element: $scope
            });
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/e-heading.default', function () {
            runAtomicScrollFillEffects();
        });

        runAtomicScrollFillEffects();
        jQuery(window).on('load', runAtomicScrollFillEffects);
    });

}(jQuery, window.elementorFrontend));

(function (window, document) {
	'use strict';

	var HEADING_SELECTOR = '.elementor-widget-e-heading .e-heading-base, [data-widget_type^="e-heading"] .e-heading-base, .e-heading-base';
	var HEADING_WRAPPER_SELECTOR = '.elementor-widget-e-heading[data-id], [data-widget_type^="e-heading"][data-id], [data-id][data-atomic]';
	var SCROLL_FILL_ENABLE_KEY = 'ep_widget_sf_fx_enable';
	var SCROLL_FILL_ANIMATION_TYPE_KEY = 'ep_widget_sf_fx_animation_type';
	var SCROLL_FILL_BASE_COLOR_KEY = 'ep_widget_sf_fx_base_color';
	var SCROLL_FILL_BG_KEY = 'ep_widget_sf_fx_fill_bg';
	var SCROLL_FILL_BG_START_COLOR_KEY = 'ep_widget_sf_fx_fill_bg_start_color';
	var SCROLL_FILL_BG_END_COLOR_KEY = 'ep_widget_sf_fx_fill_bg_end_color';
	var SCROLL_FILL_BG_ANGLE_KEY = 'ep_widget_sf_fx_fill_bg_angle';
	var retryTimer = null;
	var initialized = false;

	function getPreviewDocument() {
		var iframe = document.querySelector('#elementor-preview-iframe');

		return iframe && iframe.contentDocument ? iframe.contentDocument : null;
	}

	function unwrapAtomicValue(value) {
		if (value && 'object' === typeof value && Object.prototype.hasOwnProperty.call(value, 'value')) {
			return unwrapAtomicValue(value.value);
		}

		return value;
	}

	function isEnabled(value) {
		value = unwrapAtomicValue(value);

		return true === value || 'yes' === value || 1 === value || '1' === value;
	}

	function getString(settings, key, fallback) {
		var value = unwrapAtomicValue(getSettingValue(settings, key));

		return 'string' === typeof value && '' !== value ? value : fallback;
	}

	function getFloat(settings, key, fallback) {
		var value = unwrapAtomicValue(getSettingValue(settings, key));

		value = parseFloat(value);

		return isNaN(value) ? fallback : value;
	}

	function formatNumber(value) {
		return parseFloat(value.toFixed(4)).toString();
	}

	function normalizeScrollFillSettings(settings) {
		var fillBg;
		var startColor;
		var endColor;
		var angle;

		if (!settings || !isEnabled(getSettingValue(settings, SCROLL_FILL_ENABLE_KEY))) {
			return null;
		}

		fillBg = getString(settings, SCROLL_FILL_BG_KEY, '');

		if ('' === fillBg) {
			startColor = getString(settings, SCROLL_FILL_BG_START_COLOR_KEY, '#08aeec');
			endColor = getString(settings, SCROLL_FILL_BG_END_COLOR_KEY, '#2af598');
			angle = getFloat(settings, SCROLL_FILL_BG_ANGLE_KEY, 90);
			fillBg = startColor && endColor ? 'linear-gradient(' + formatNumber(angle) + 'deg, ' + startColor + ' 0%, ' + endColor + ' 100%)' : 'linear-gradient(90deg, #08aeec 0%, #2af598 100%)';
		}

		return {
			enable: 'yes',
			animation_type: getString(settings, SCROLL_FILL_ANIMATION_TYPE_KEY, 'scroll_fill'),
			base_color: getString(settings, SCROLL_FILL_BASE_COLOR_KEY, 'rgba(156, 156, 156, 0.5)'),
			fill_bg: fillBg
		};
	}

	function getElementId(element) {
		var closestElement = element.closest('[data-id]');

		return closestElement ? closestElement.getAttribute('data-id') : null;
	}

	function normalizeSettings(settings) {
		if (!settings) {
			return {};
		}

		if ('function' === typeof settings.toJSON) {
			return settings.toJSON();
		}

		if (settings.attributes && 'object' === typeof settings.attributes) {
			return settings.attributes;
		}

		return settings;
	}

	function getSettingValue(settings, key) {
		if (!settings) {
			return null;
		}

		if ('function' === typeof settings.get) {
			return settings.get(key);
		}

		settings = normalizeSettings(settings);

		return settings ? settings[key] : null;
	}

	function getElementSettings(elementId) {
		var container;
		var settings;

		if (!window.elementor || !elementId || !window.elementor.getContainer) {
			return null;
		}

		container = window.elementor.getContainer(elementId);

		if (!container || !container.model) {
			return null;
		}

		settings = container.model.get('settings') || {};

		return normalizeSettings(settings);
	}

	function getElementModel(elementId) {
		var container;

		if (!window.elementor || !elementId || !window.elementor.getContainer) {
			return null;
		}

		container = window.elementor.getContainer(elementId);

		return container && container.model ? container.model : null;
	}

	function getElementTypeFromModel(model) {
		return model ? model.get('widgetType') || model.get('elType') : '';
	}

	function isHeadingElementId(elementId) {
		var model = getElementModel(elementId);
		var elementType = getElementTypeFromModel(model);

		return 'e-heading' === elementType || 'e-heading.default' === elementType;
	}

	function isHeadingModel(model) {
		var elementType = getElementTypeFromModel(model);

		return 'e-heading' === elementType || 'e-heading.default' === elementType;
	}

	function escapeAttributeValue(value) {
		if (window.CSS && window.CSS.escape) {
			return window.CSS.escape(value);
		}

		return String(value).replace(/"/g, '\\"');
	}

	function getHeadingElement(elementId) {
		var previewDocument = getPreviewDocument();
		var safeId;

		if (!previewDocument || !elementId) {
			return null;
		}

		safeId = escapeAttributeValue(elementId);

		return previewDocument.querySelector(
			'[data-id="' + safeId + '"] .e-heading-base, ' +
			'[data-id="' + safeId + '"].elementor-widget-e-heading .e-heading-base, ' +
			'[data-id="' + safeId + '"][data-widget_type^="e-heading"] .e-heading-base'
		);
	}

	function getElementWrapper(elementId) {
		var previewDocument = getPreviewDocument();
		var safeId;

		if (!previewDocument || !elementId) {
			return null;
		}

		safeId = escapeAttributeValue(elementId);

		return previewDocument.querySelector('[data-id="' + safeId + '"]');
	}

	function getPreviewWindow() {
		var previewDocument = getPreviewDocument();

		return previewDocument && previewDocument.defaultView ? previewDocument.defaultView : null;
	}

	function getGsap() {
		var previewWindow = getPreviewWindow();

		return window.gsap || (previewWindow && previewWindow.gsap) || null;
	}

	function getScrollTrigger() {
		var previewWindow = getPreviewWindow();

		return window.ScrollTrigger || (previewWindow && previewWindow.ScrollTrigger) || null;
	}

	function killHeadingTriggers(heading) {
		var gsapInstance = getGsap();
		var scrollTrigger = getScrollTrigger();

		if (!heading || !scrollTrigger || !scrollTrigger.getAll) {
			return;
		}

		scrollTrigger.getAll().forEach(function (trigger) {
			if (trigger.trigger === heading) {
				trigger.kill();
			}
		});

		if (gsapInstance) {
			gsapInstance.killTweensOf(heading);
		}
	}

	function cleanupHeading(heading) {
		if (!heading) {
			return;
		}

		killHeadingTriggers(heading);
		heading.removeAttribute('data-ep-scroll-fill-effect');
		heading.removeAttribute('data-ep-scroll-fill-enabled');
		heading.classList.remove('bdt-scroll-effect-yes');
		heading.classList.remove('bdt-scroll-effect-scroll_fill');
		heading.classList.remove('bdt-scroll-effect-scaling_gradient_text');
		heading.classList.remove('bdt-scroll-effect-reveal_gradient_mask');
		heading.style.backgroundImage = '';
		heading.style.backgroundSize = '';
		heading.style.backgroundRepeat = '';
		heading.style.backgroundClip = '';
		heading.style.webkitBackgroundClip = '';
		heading.style.color = '';
		heading.style.webkitTextFillColor = '';
	}

	function cleanupElementById(elementId) {
		var heading = getHeadingElement(elementId);
		var wrapper = getElementWrapper(elementId);

		cleanupHeading(heading);

		if (wrapper && wrapper !== heading) {
			cleanupHeading(wrapper);
		}
	}

	function applyScrollFillEffect(heading, normalizedSettings) {
		var gsapInstance = getGsap();
		var scrollTrigger = getScrollTrigger();

		cleanupHeading(heading);

		if (!heading || !normalizedSettings) {
			return;
		}

		heading.setAttribute('data-ep-scroll-fill-enabled', 'yes');
		heading.setAttribute('data-ep-scroll-fill-effect', JSON.stringify(normalizedSettings));
		heading.classList.add('bdt-scroll-effect-yes');
		heading.classList.add('bdt-scroll-effect-' + normalizedSettings.animation_type);

		if ('scroll_fill' !== normalizedSettings.animation_type) {
			return;
		}

		heading.style.backgroundImage = normalizedSettings.fill_bg + ', linear-gradient(' + normalizedSettings.base_color + ', ' + normalizedSettings.base_color + ')';
		heading.style.backgroundRepeat = 'no-repeat';
		heading.style.backgroundSize = '0% 100%, 100% 100%';
		heading.style.backgroundPosition = 'left center, left center';
		heading.style.transition = 'background-size 0.25s linear';
		heading.style.setProperty('background-clip', 'text');
		heading.style.setProperty('-webkit-background-clip', 'text');
		heading.style.setProperty('color', 'transparent');
		heading.style.setProperty('-webkit-text-fill-color', 'transparent');

		if (!gsapInstance || !scrollTrigger) {
			heading.style.backgroundSize = '100% 100%, 100% 100%';
			return;
		}

		if (gsapInstance.registerPlugin) {
			gsapInstance.registerPlugin(scrollTrigger);
		}

		gsapInstance.to(heading, {
			backgroundSize: '100% 100%, 100% 100%',
			scrollTrigger: {
				trigger: heading,
				start: 'top bottom',
				end: 'bottom center',
				scrub: 1,
				markers: false,
				invalidateOnRefresh: true
			}
		});
		
	}

	function updateHeading(elementId, settings, source) {
		var heading = getHeadingElement(elementId);
		var normalizedRawSettings = normalizeSettings(settings);
		var normalizedSettings = normalizeScrollFillSettings(normalizedRawSettings);

		cleanupElementById(elementId);

		if (!heading) {
			console.warn('[EP Scroll Fill Editor] heading element not found for atomic heading.', {
				source: source,
				id: elementId
			});
			return;
		}

		if (!normalizedSettings) {
			cleanupHeading(heading);
		} else {
			applyScrollFillEffect(heading, normalizedSettings);
		}
	}

	function syncExistingHeadings() {
		var previewDocument = getPreviewDocument();
		var elements;

		if (!previewDocument) {
			console.log('[EP Scroll Fill Editor] preview document not found');
			return;
		}

		elements = previewDocument.querySelectorAll(HEADING_WRAPPER_SELECTOR + ', ' + HEADING_SELECTOR);
	
		elements.forEach(function (element) {
			var elementId = getElementId(element);

			if (!isHeadingElementId(elementId)) {
				return;
			}

			updateHeading(elementId, getElementSettings(elementId), 'initial-sync');
		});
	}

	function scheduleElementUpdate(elementId, settings, source) {
		updateHeading(elementId, settings, source);

		window.setTimeout(function () {
			updateHeading(elementId, settings, source + '-delayed-100');
		}, 100);

		window.setTimeout(function () {
			updateHeading(elementId, settings, source + '-delayed-500');
		}, 500);
	}

	function scheduleInitialSync() {
		var previewDocument = getPreviewDocument();

		if (!previewDocument || !previewDocument.body) {
			retryTimer = window.setTimeout(function () {
				retryTimer = null;
				scheduleInitialSync();
			}, 500);
			return;
		}

		window.requestAnimationFrame(syncExistingHeadings);
	}

	function getChangedModel(view) {
		if (view && view.container && view.container.model) {
			return view.container.model;
		}

		if (view && view.model) {
			return view.model;
		}

		return null;
	}

	function bindEditorChange() {
		if (!window.elementor || !window.elementor.channels || !window.elementor.channels.editor) {
			return;
		}

		window.elementor.channels.editor.on('change', function (view) {
			var model = getChangedModel(view);
			var elementId;

			if (!isHeadingModel(model)) {
				return;
			}

			elementId = model.get('id');
			scheduleElementUpdate(elementId, normalizeSettings(model.get('settings') || {}), 'control-change');
		});
	}

	function bindPreviewFrontendHooks() {
		var previewDocument = getPreviewDocument();
		var previewWindow = previewDocument && previewDocument.defaultView;
		var frontend = previewWindow && previewWindow.elementorFrontend;

		if (!frontend || !frontend.hooks) {
			return;
		}

		frontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
			var element = $scope && $scope[0];
			var elementId = element ? getElementId(element) : null;

			if (!elementId || !isHeadingElementId(elementId)) {
				return;
			}

			updateHeading(elementId, getElementSettings(elementId), 'frontend-ready');
		});
	}

	function init() {
		var iframe = document.querySelector('#elementor-preview-iframe');
		
		if (!initialized) {
			bindEditorChange();
			initialized = true;
		}

		if (iframe) {
			iframe.addEventListener('load', function () {
				bindPreviewFrontendHooks();
				scheduleInitialSync();
			});
		}

		if (window.elementor) {
			window.elementor.on('preview:loaded', function () {
				bindPreviewFrontendHooks();
				scheduleInitialSync();
			});
		}

		bindPreviewFrontendHooks();
		scheduleInitialSync();
	}

	if ('loading' === document.readyState) {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
}(window, document));

(function (window, document) {
	'use strict';

	var LEGACY_HEADING_SELECTOR = '.elementor-heading-title';
	var ATOMIC_HEADING_SELECTOR = '.e-heading-base';
	var TGB_CLASS = 'element-pack-tgb-background';
	var TGB_ENABLED_CLASS = 'bdt-tgb-yes';
	var ENABLE_KEY = 'element_pack_tgb_enable';
	var SELECTOR_KEY = 'element_pack_tgb_selector';
	var BACKGROUND_KEY = 'element_pack_tgb_background';
	var BACKGROUND_START_COLOR_KEY = 'element_pack_tgb_background_start_color';
	var BACKGROUND_END_COLOR_KEY = 'element_pack_tgb_background_end_color';
	var BACKGROUND_START_COLOR_LOCATION_KEY = 'element_pack_tgb_background_start_color_location';
	var BACKGROUND_END_COLOR_LOCATION_KEY = 'element_pack_tgb_background_end_color_location';
	var BACKGROUND_ANGLE_KEY = 'element_pack_tgb_background_angle';
	var initialized = false;
	var retryTimer = null;
	var previewHooksBound = false;

	function getPreviewDocument() {
		var iframe = document.querySelector('#elementor-preview-iframe');

		return iframe && iframe.contentDocument ? iframe.contentDocument : null;
	}

	function unwrapAtomicValue(value) {
		if (value && 'object' === typeof value && Object.prototype.hasOwnProperty.call(value, 'value')) {
			return unwrapAtomicValue(value.value);
		}

		return value;
	}

	function normalizeSettings(settings) {
		if (!settings) {
			return {};
		}

		if ('function' === typeof settings.toJSON) {
			return settings.toJSON();
		}

		if (settings.attributes && 'object' === typeof settings.attributes) {
			return settings.attributes;
		}

		return settings;
	}

	function getSettingValue(settings, key) {
		if (!settings) {
			return null;
		}

		if ('function' === typeof settings.get) {
			return settings.get(key);
		}

		settings = normalizeSettings(settings);

		return settings ? settings[key] : null;
	}

	function getElementSettings(elementId) {
		var container;
		var settings;

		if (!window.elementor || !elementId || !window.elementor.getContainer) {
			return null;
		}

		container = window.elementor.getContainer(elementId);

		if (!container || !container.model) {
			return null;
		}

		settings = container.model.get('settings') || {};

		return normalizeSettings(settings);
	}

	function getElementModel(elementId) {
		var container;

		if (!window.elementor || !elementId || !window.elementor.getContainer) {
			return null;
		}

		container = window.elementor.getContainer(elementId);

		return container && container.model ? container.model : null;
	}

	function getElementTypeFromModel(model) {
		return model ? model.get('widgetType') || model.get('elType') : '';
	}

	function isAtomicHeadingType(elementType) {
		return 'e-heading' === elementType || 'e-heading.default' === elementType;
	}

	function isLegacyHeadingType(elementType) {
		return 'heading' === elementType || 'heading.default' === elementType;
	}

	function isHeadingType(elementType) {
		return isAtomicHeadingType(elementType) || isLegacyHeadingType(elementType);
	}

	function getDefaultSelector(elementType) {
		if (isAtomicHeadingType(elementType)) {
			return ATOMIC_HEADING_SELECTOR;
		}

		if (isLegacyHeadingType(elementType)) {
			return LEGACY_HEADING_SELECTOR;
		}

		return '';
	}

	function escapeAttributeValue(value) {
		if (window.CSS && window.CSS.escape) {
			return window.CSS.escape(value);
		}

		return String(value).replace(/"/g, '\\"');
	}

	function getElementWrapper(elementId) {
		var previewDocument = getPreviewDocument();
		var safeId;

		if (!previewDocument || !elementId) {
			return null;
		}

		safeId = escapeAttributeValue(elementId);

		return previewDocument.querySelector('[data-id="' + safeId + '"]');
	}

	function getElementId(element) {
		var closestElement = element.closest('[data-id]');

		return closestElement ? closestElement.getAttribute('data-id') : null;
	}

	function parseJSON(value) {
		if (!value || 'string' !== typeof value) {
			return null;
		}

		try {
			return JSON.parse(value);
		} catch (error) {
			return null;
		}
	}

	function normalizeToggle(value) {
		value = unwrapAtomicValue(value);

		if (true === value || 'yes' === value || 1 === value || '1' === value) {
			return 'yes';
		}

		return '';
	}

	function toString(value, fallback) {
		value = unwrapAtomicValue(value);

		return 'string' === typeof value && '' !== value ? value : fallback;
	}

	function toNumber(value, fallback) {
		value = unwrapAtomicValue(value);

		if (value && 'object' === typeof value) {
			if ('undefined' !== typeof value.size) {
				value = value.size;
			} else if ('undefined' !== typeof value.offset) {
				value = value.offset;
			}
		}

		value = parseFloat(value);

		return isNaN(value) ? fallback : value;
	}

	function hasLegacyGroupSettings(settings) {
		return '' !== toString(getSettingValue(settings, BACKGROUND_KEY + '_background'), '') ||
			'' !== toString(getSettingValue(settings, BACKGROUND_KEY + '_color'), '') ||
			'' !== toString(getSettingValue(settings, BACKGROUND_KEY + '_color_b'), '');
	}

	function formatNumber(value) {
		return parseFloat(value.toFixed(4)).toString();
	}

	function isNormalizedSettings(settings) {
		return settings && 'yes' === settings.enable && 'string' === typeof settings.background && '' !== settings.background;
	}

	function buildAtomicGradient(settings) {
		var startColor = toString(getSettingValue(settings, BACKGROUND_START_COLOR_KEY), '');
		var endColor = toString(getSettingValue(settings, BACKGROUND_END_COLOR_KEY), '');
		var startLocation = toNumber(getSettingValue(settings, BACKGROUND_START_COLOR_LOCATION_KEY), 0);
		var endLocation = toNumber(getSettingValue(settings, BACKGROUND_END_COLOR_LOCATION_KEY), 100);
		var angle = toNumber(getSettingValue(settings, BACKGROUND_ANGLE_KEY), 90);

		if ('' === startColor) {
			startColor = '#08aeec';
		}

		if ('' === endColor) {
			endColor = '#2af598';
		}

		return 'linear-gradient(' + formatNumber(angle) + 'deg, ' + startColor + ' ' + formatNumber(startLocation) + '%, ' + endColor + ' ' + formatNumber(endLocation) + '%)';
	}

	function normalizeLegacyGroupBackground(settings) {
		var type = toString(getSettingValue(settings, BACKGROUND_KEY + '_background'), '');
		var startColor = toString(getSettingValue(settings, BACKGROUND_KEY + '_color'), '');
		var endColor = toString(getSettingValue(settings, BACKGROUND_KEY + '_color_b'), '');
		var startLocation = toNumber(getSettingValue(settings, BACKGROUND_KEY + '_color_stop'), 0);
		var endLocation = toNumber(getSettingValue(settings, BACKGROUND_KEY + '_color_b_stop'), 100);
		var angle = toNumber(getSettingValue(settings, BACKGROUND_KEY + '_gradient_angle'), 90);

		// This group control only supports gradients, so an unset type must be
		// treated as a gradient (never "classic"/solid) — otherwise a heading
		// that only sets the first color would collapse to a solid fill.
		if ('' === type) {
			type = 'gradient';
		}

		if ('classic' === type) {
			return startColor ? 'linear-gradient(0deg, ' + startColor + ' ' + formatNumber(startLocation) + '%, ' + startColor + ' ' + formatNumber(endLocation) + '%)' : '';
		}

		if ('gradient' !== type) {
			return '';
		}

		if ('' === startColor) {
			startColor = '#08aeec';
		}

		if ('' === endColor) {
			endColor = '#2af598';
		}

		return 'linear-gradient(' + formatNumber(angle) + 'deg, ' + startColor + ' ' + formatNumber(startLocation) + '%, ' + endColor + ' ' + formatNumber(endLocation) + '%)';
	}

	function normalizeTextGradientSettings(settings, elementType) {
		var background;
		var selector;

		if (!settings) {
			return null;
		}

		if (isNormalizedSettings(settings)) {
			return settings;
		}

		if ('yes' !== normalizeToggle(getSettingValue(settings, ENABLE_KEY))) {
			return null;
		}

		background = toString(getSettingValue(settings, BACKGROUND_KEY), '');

		if ('' === background && hasLegacyGroupSettings(settings)) {
			background = normalizeLegacyGroupBackground(settings);
		}

		if ('' === background) {
			background = buildAtomicGradient(settings);
		}

		selector = toString(getSettingValue(settings, SELECTOR_KEY), '');

		if ('' === selector) {
			selector = getDefaultSelector(elementType);
		}

		return {
			enable: 'yes',
			selector: selector,
			background: background,
			// Never paint the gradient inline for classic/common widgets — our
			// reconstruction cannot reliably read the Group_Control_Background
			// sub-fields, and Elementor's own generated CSS already updates live
			// as the user edits. Only atomic (e-heading), whose discrete color
			// props ARE reliable and which has no Elementor CSS, applies inline.
			// We still add the class + clip below so Elementor's gradient shows.
			apply_background: isAtomicHeadingType(elementType)
		};
	}

	function cleanupTargets($targets) {
		if (!$targets || !$targets.length) {
			return;
		}

		$targets.each(function () {
			var element = this;

			element.removeAttribute('data-ep-text-gradient-background');
			element.classList.remove(TGB_ENABLED_CLASS);
			element.classList.remove(TGB_CLASS);
			element.style.backgroundImage = '';
			element.style.backgroundColor = '';
			element.style.backgroundClip = '';
			element.style.webkitBackgroundClip = '';
			element.style.color = '';
			element.style.webkitTextFillColor = '';
		});
	}

	function applyTextGradientBackground(target, normalizedSettings) {
		if (!target || !isNormalizedSettings(normalizedSettings)) {
			return;
		}

		var applyBackground = true === normalizedSettings.apply_background;

		target.setAttribute('data-ep-text-gradient-background', JSON.stringify(normalizedSettings));
		target.classList.add(TGB_ENABLED_CLASS, TGB_CLASS);

		if (applyBackground) {
			target.style.setProperty('background-image', normalizedSettings.background);
			target.style.setProperty('background-color', 'transparent', 'important');
		} else {
			target.style.removeProperty('background-image');
			target.style.removeProperty('background-color');
		}

		target.style.setProperty('background-clip', 'text', 'important');
		target.style.setProperty('-webkit-background-clip', 'text', 'important');
		target.style.setProperty('color', 'transparent', 'important');
		target.style.setProperty('-webkit-text-fill-color', 'transparent', 'important');
		target.querySelectorAll('a').forEach(function (link) {
			link.style.setProperty('color', 'transparent', 'important');
			link.style.setProperty('-webkit-text-fill-color', 'transparent', 'important');
		});
	}

	function getTargetsForElement(elementId, settings, elementType) {
		var previewDocument = getPreviewDocument();
		var wrapper = getElementWrapper(elementId);
		var normalizedSettings = normalizeTextGradientSettings(settings, elementType);
		var selector;
		var targets = [];
		var $;

		if (!previewDocument || !wrapper) {
			return {
				normalizedSettings: normalizedSettings,
				targets: targets
			};
		}

		$ = (previewDocument.defaultView && previewDocument.defaultView.jQuery) || window.jQuery;

		if (!normalizedSettings) {
			return {
				normalizedSettings: null,
				targets: wrapper.querySelectorAll('.' + TGB_CLASS)
			};
		}

		selector = normalizedSettings.selector || getDefaultSelector(elementType);

		if (!selector) {
			return {
				normalizedSettings: normalizedSettings,
				targets: targets
			};
		}

		$(wrapper).find(selector).each(function () {
			targets.push(this);
		});

		return {
			normalizedSettings: normalizedSettings,
			targets: targets
		};
	}

	function updateElement(elementId, settings) {
		var model = getElementModel(elementId);
		var elementType = getElementTypeFromModel(model);
		var wrapper = getElementWrapper(elementId);
		var result = getTargetsForElement(elementId, settings, elementType);
		var index;
		var $ = window.jQuery;

		if (wrapper) {
			cleanupTargets($(wrapper.querySelectorAll('.' + TGB_CLASS)));
		}

		if (!result.normalizedSettings) {
			return;
		}

		for (index = 0; index < result.targets.length; index++) {
			applyTextGradientBackground(result.targets[index], result.normalizedSettings);
		}
	}

	function applyFromDataAttribute(element) {
		var elementId = getElementId(element);
		var elementType = getElementTypeFromModel(getElementModel(elementId));
		var settings = getElementSettings(elementId);
		var normalizedSettings = normalizeTextGradientSettings(settings, elementType);

		if (!normalizedSettings) {
			normalizedSettings = normalizeTextGradientSettings(
				parseJSON(element.getAttribute('data-ep-text-gradient-background')),
				elementType
			);
		}

		if (!normalizedSettings) {
			return;
		}

		applyTextGradientBackground(element, normalizedSettings);
	}

	function syncExistingElements() {
		var previewDocument = getPreviewDocument();
		var wrappers;
		var index;
		var elementId;

		if (!previewDocument) {
			return;
		}

		wrappers = previewDocument.querySelectorAll('[data-id]');

		wrappers.forEach(function (wrapper) {
			elementId = wrapper.getAttribute('data-id');

			if (!elementId) {
				return;
			}

			wrapper.querySelectorAll('[data-ep-text-gradient-background]').forEach(function (element) {
				applyFromDataAttribute(element);
			});

			updateElement(elementId, getElementSettings(elementId));
		});
	}

	function scheduleInitialSync() {
		var previewDocument = getPreviewDocument();

		if (!previewDocument || !previewDocument.body) {
			retryTimer = window.setTimeout(function () {
				retryTimer = null;
				scheduleInitialSync();
			}, 100);
			return;
		}

		window.requestAnimationFrame(syncExistingElements);
	}

	function getChangedModel(view) {
		if (view && view.container && view.container.model) {
			return view.container.model;
		}

		if (view && view.model) {
			return view.model;
		}

		return null;
	}

	function isTextGradientSettingChanged(changed) {
		return (
			Object.prototype.hasOwnProperty.call(changed, ENABLE_KEY) ||
			Object.prototype.hasOwnProperty.call(changed, SELECTOR_KEY) ||
			Object.prototype.hasOwnProperty.call(changed, BACKGROUND_KEY) ||
			Object.prototype.hasOwnProperty.call(changed, BACKGROUND_KEY + '_background') ||
			Object.prototype.hasOwnProperty.call(changed, BACKGROUND_KEY + '_color') ||
			Object.prototype.hasOwnProperty.call(changed, BACKGROUND_KEY + '_color_b') ||
			Object.prototype.hasOwnProperty.call(changed, BACKGROUND_KEY + '_gradient_angle') ||
			Object.prototype.hasOwnProperty.call(changed, BACKGROUND_START_COLOR_KEY) ||
			Object.prototype.hasOwnProperty.call(changed, BACKGROUND_END_COLOR_KEY) ||
			Object.prototype.hasOwnProperty.call(changed, BACKGROUND_START_COLOR_LOCATION_KEY) ||
			Object.prototype.hasOwnProperty.call(changed, BACKGROUND_END_COLOR_LOCATION_KEY) ||
			Object.prototype.hasOwnProperty.call(changed, BACKGROUND_ANGLE_KEY) ||
			Object.prototype.hasOwnProperty.call(changed, BACKGROUND_KEY + '_color_stop') ||
			Object.prototype.hasOwnProperty.call(changed, BACKGROUND_KEY + '_color_b_stop')
		);
	}

	function bindEditorChange() {
		if (!window.elementor || !window.elementor.channels || !window.elementor.channels.editor) {
			return;
		}

		window.elementor.channels.editor.on('change', function (view) {
			var model = getChangedModel(view);
			var elementId;

			if (!model) {
				return;
			}

			elementId = model.get('id');

			if (!elementId) {
				return;
			}

			// Always re-apply from the element's authoritative container settings.
			// (The previous change-diff check inspected a Backbone settings *model*
			// with hasOwnProperty, which never matched, so live edits never
			// updated. updateElement() cleans up on its own when TGB is disabled.)
			updateElement(elementId, getElementSettings(elementId));
		});
	}

	function handlePreviewScope($scope) {
		var element = $scope && $scope[0];
		var elementId = element ? getElementId(element) : null;

		if (element) {
			element.querySelectorAll('[data-ep-text-gradient-background]').forEach(function (target) {
				applyFromDataAttribute(target);
			});
		}

		if (elementId) {
			updateElement(elementId, getElementSettings(elementId));
		}
	}

	function bindPreviewFrontendHooks() {
		var previewDocument = getPreviewDocument();
		var previewWindow = previewDocument && previewDocument.defaultView;
		var frontend = previewWindow && previewWindow.elementorFrontend;

		if (!frontend || !frontend.hooks || previewHooksBound) {
			return;
		}

		previewHooksBound = true;

		frontend.hooks.addAction('frontend/element_ready/heading.default', handlePreviewScope);
		frontend.hooks.addAction('frontend/element_ready/e-heading.default', handlePreviewScope);

		frontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
			var element = $scope && $scope[0];
			var elementId = element ? getElementId(element) : null;

			if (!elementId) {
				return;
			}

			updateElement(elementId, getElementSettings(elementId));
		});
	}

	function init() {
		var iframe = document.querySelector('#elementor-preview-iframe');

		if (!initialized) {
			bindEditorChange();
			initialized = true;
		}

		if (iframe) {
			iframe.addEventListener('load', function () {
				previewHooksBound = false;
				bindPreviewFrontendHooks();
				scheduleInitialSync();
			});
		}

		if (window.elementor) {
			window.elementor.on('preview:loaded', function () {
				previewHooksBound = false;
				bindPreviewFrontendHooks();
				scheduleInitialSync();
			});
		}

		bindPreviewFrontendHooks();
		scheduleInitialSync();
	}

	if ('loading' === document.readyState) {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
}(window, document));

(function (window, document) {
	'use strict';

	var ACTIVE_KEY = 'image_mask_popover';
	var SHAPE_KEY = 'image_mask_shape';
	var SHAPE_DEFAULT_KEY = 'image_mask_shape_default';
	var SHAPE_CUSTOM_KEY = 'image_mask_shape_custom';
	var POSITION_KEY = 'image_mask_shape_position';
	var SIZE_KEY = 'image_mask_shape_size';
	var CUSTOM_SIZE_KEY = 'image_mask_shape_custom_size';
	var REPEAT_KEY = 'image_mask_shape_repeat';
	var MASK_STYLE_PROPS = [
		'-webkit-mask-image',
		'mask-image',
		'-webkit-mask-position',
		'mask-position',
		'-webkit-mask-size',
		'mask-size',
		'-webkit-mask-repeat',
		'mask-repeat'
	];
	var POSITION_MAP = {
		'center-center': 'center center',
		'center-left': 'center left',
		'center-right': 'center right',
		'top-center': 'top center',
		'top-left': 'top left',
		'top-right': 'top right',
		'bottom-center': 'bottom center',
		'bottom-left': 'bottom left',
		'bottom-right': 'bottom right'
	};
	var REPEAT_MAP = {
		'repeat': 'repeat',
		'repeat-x': 'repeat-x',
		'repeat-y': 'repeat-y',
		'space': 'space',
		'round': 'round',
		'no-repeat': 'no-repeat',
		'repeat-space': 'repeat space',
		'round-space': 'round space',
		'no-repeat-round': 'no-repeat round'
	};

	var initialized = false;
	var editorChangeBound = false;
	var previewHooksBound = false;
	var retryTimer = null;
	var pendingAttachmentFetches = {};

	function getMaskAssetsUrl() {
		return (window.epImageMaskingEditorConfig && window.epImageMaskingEditorConfig.maskAssetsUrl) || '';
	}

	function getColorShapes() {
		return (window.epImageMaskingEditorConfig && window.epImageMaskingEditorConfig.colorShapes) || [];
	}

	function getDefaultImageSize() {
		return (window.epImageMaskingEditorConfig && window.epImageMaskingEditorConfig.defaultImageSize) || 'full';
	}

	function hasColorOverlayShape(shapeKey) {
		return getColorShapes().indexOf(shapeKey) !== -1;
	}

	function getPreviewDocument() {
		var iframe = document.querySelector('#elementor-preview-iframe');

		return iframe && iframe.contentDocument ? iframe.contentDocument : null;
	}

	function unwrapAtomicValue(value) {
		if (value && 'object' === typeof value && Object.prototype.hasOwnProperty.call(value, 'value')) {
			return unwrapAtomicValue(value.value);
		}

		return value;
	}

	function isTruthy(value) {
		value = unwrapAtomicValue(value);

		return true === value || 'yes' === value || 1 === value || '1' === value;
	}

	function normalizeSettings(settings) {
		if (!settings) {
			return {};
		}

		if ('function' === typeof settings.toJSON) {
			return settings.toJSON();
		}

		if (settings.attributes && 'object' === typeof settings.attributes) {
			return settings.attributes;
		}

		return settings;
	}

	function getSettingValue(settings, key) {
		if (!settings) {
			return null;
		}

		if ('function' === typeof settings.get) {
			return settings.get(key);
		}

		settings = normalizeSettings(settings);

		return settings ? settings[key] : null;
	}

	function getElementSettings(elementId) {
		var container;
		var settings;

		if (!window.elementor || !elementId || !window.elementor.getContainer) {
			return null;
		}

		container = window.elementor.getContainer(elementId);

		if (!container || !container.model) {
			return null;
		}

		settings = container.model.get('settings') || {};

		return normalizeSettings(settings);
	}

	function getElementModel(elementId) {
		var container;

		if (!window.elementor || !elementId || !window.elementor.getContainer) {
			return null;
		}

		container = window.elementor.getContainer(elementId);

		return container && container.model ? container.model : null;
	}

	function getElementTypeFromModel(model) {
		return model ? model.get('widgetType') || model.get('elType') : '';
	}

	function isImageElementId(elementId) {
		var model = getElementModel(elementId);
		var elementType = getElementTypeFromModel(model);

		return 'e-image' === elementType || 'e-image.default' === elementType;
	}

	function isImageModel(model) {
		var elementType = getElementTypeFromModel(model);

		return 'e-image' === elementType || 'e-image.default' === elementType;
	}

	function hasImageMaskingControl(settings) {
		settings = normalizeSettings(settings);

		if (!settings) {
			return false;
		}

		return Object.prototype.hasOwnProperty.call(settings, ACTIVE_KEY) ||
			Object.prototype.hasOwnProperty.call(settings, SHAPE_KEY) ||
			Object.prototype.hasOwnProperty.call(settings, SHAPE_DEFAULT_KEY) ||
			Object.prototype.hasOwnProperty.call(settings, SHAPE_CUSTOM_KEY);
	}

	function escapeAttributeValue(value) {
		if (window.CSS && window.CSS.escape) {
			return window.CSS.escape(value);
		}

		return String(value).replace(/"/g, '\\"');
	}

	function getElementWrapper(elementId) {
		var previewDocument = getPreviewDocument();
		var safeId;

		if (!previewDocument || !elementId) {
			return null;
		}

		safeId = escapeAttributeValue(elementId);

		return previewDocument.querySelector('[data-id="' + safeId + '"]');
	}

	function getElementId(element) {
		var closestElement = element && element.closest ? element.closest('[data-id]') : null;

		return closestElement ? closestElement.getAttribute('data-id') : null;
	}

	function getString(settings, key, fallback) {
		var value = unwrapAtomicValue(getSettingValue(settings, key));

		return 'string' === typeof value && '' !== value ? value : fallback;
	}

	function getAtomicImageSize(value) {
		var unwrapped = unwrapAtomicValue(value);
		var size;

		if (!unwrapped || 'object' !== typeof unwrapped) {
			return getDefaultImageSize();
		}

		size = unwrapAtomicValue(unwrapped.size);

		return 'string' === typeof size && '' !== size ? size : getDefaultImageSize();
	}

	function resolveAttachmentUrl(id, imageSize) {
		var attachment;
		var url;
		var sizes;

		if (!id || !window.wp || !wp.media) {
			return '';
		}

		imageSize = imageSize || getDefaultImageSize();
		attachment = wp.media.attachment(id);
		url = attachment.get('url');

		if (url && 'full' !== imageSize) {
			sizes = attachment.get('sizes');

			if (sizes && sizes[imageSize] && sizes[imageSize].url) {
				return sizes[imageSize].url;
			}
		}

		if (url) {
			return url;
		}

		if (!pendingAttachmentFetches[id]) {
			pendingAttachmentFetches[id] = attachment.fetch().then(function () {
				delete pendingAttachmentFetches[id];
				scheduleInitialSync();
			}).catch(function () {
				delete pendingAttachmentFetches[id];
			});
		}

		return '';
	}

	function extractImageUrl(value, imageSize) {
		var unwrapped = unwrapAtomicValue(value);
		var urlValue;
		var id;

		if (!unwrapped) {
			return '';
		}

		if ('string' === typeof unwrapped) {
			return unwrapped;
		}

		if ('object' !== typeof unwrapped) {
			return '';
		}

		if (unwrapped.src) {
			return extractImageUrl(unwrapped.src, getAtomicImageSize(unwrapped));
		}

		urlValue = unwrapAtomicValue(unwrapped.url);

		if ('string' === typeof urlValue && '' !== urlValue) {
			return urlValue;
		}

		if (unwrapped.url) {
			return extractImageUrl(unwrapped.url, imageSize);
		}

		id = parseInt(unwrapAtomicValue(unwrapped.id), 10);

		if (id) {
			return resolveAttachmentUrl(id, imageSize || getDefaultImageSize());
		}

		return '';
	}

	function formatCustomSize(value) {
		var unwrapped = unwrapAtomicValue(value);

		if (unwrapped && 'object' === typeof unwrapped && Object.prototype.hasOwnProperty.call(unwrapped, 'size')) {
			return String(unwrapped.size) + (unwrapped.unit || '%');
		}

		return '100%';
	}

	function normalizeImageMaskingSettings(settings) {
		var shapeType;
		var defaultShape;
		var maskUrl;
		var colorOverlayUrl;

		settings = normalizeSettings(settings);

		if (!settings || !isTruthy(getSettingValue(settings, ACTIVE_KEY))) {
			return null;
		}

		shapeType = getString(settings, SHAPE_KEY, 'default');
		defaultShape = getString(settings, SHAPE_DEFAULT_KEY, 'shape-1');
		maskUrl = '';
		colorOverlayUrl = '';

		if ('custom' === shapeType) {
			maskUrl = extractImageUrl(getSettingValue(settings, SHAPE_CUSTOM_KEY));
		} else {
			maskUrl = getMaskAssetsUrl() + defaultShape + '.svg';

			if (hasColorOverlayShape(defaultShape)) {
				colorOverlayUrl = getMaskAssetsUrl() + 'color-' + defaultShape + '.svg';
			}
		}

		if (!maskUrl) {
			return null;
		}

		return {
			mask_url: maskUrl,
			color_overlay_url: colorOverlayUrl,
			mask_position: POSITION_MAP[getString(settings, POSITION_KEY, 'center-center')] || POSITION_MAP['center-center'],
			mask_size: 'initial' === getString(settings, SIZE_KEY, 'contain')
				? formatCustomSize(getSettingValue(settings, CUSTOM_SIZE_KEY))
				: getString(settings, SIZE_KEY, 'contain'),
			mask_repeat: REPEAT_MAP[getString(settings, REPEAT_KEY, 'no-repeat')] || REPEAT_MAP['no-repeat']
		};
	}

	function buildImgStyle(settings) {
		return [
			'-webkit-mask-image: url(' + settings.mask_url + ')',
			'mask-image: url(' + settings.mask_url + ')',
			'-webkit-mask-position: ' + settings.mask_position,
			'mask-position: ' + settings.mask_position,
			'-webkit-mask-size: ' + settings.mask_size,
			'mask-size: ' + settings.mask_size,
			'-webkit-mask-repeat: ' + settings.mask_repeat,
			'mask-repeat: ' + settings.mask_repeat
		].join('; ');
	}

	function buildWrapperStyle(settings) {
		if (!settings.color_overlay_url) {
			return '';
		}

		return '--bdt-mask-overlay: url(' + settings.color_overlay_url + ')';
	}

	function parseInlineStyle(styleValue) {
		var styleObject = {};
		var parts;
		var index;
		var declaration;
		var splitIndex;
		var property;
		var value;

		if (!styleValue || 'string' !== typeof styleValue) {
			return styleObject;
		}

		parts = styleValue.split(';');

		for (index = 0; index < parts.length; index++) {
			declaration = parts[index].trim();

			if (!declaration) {
				continue;
			}

			splitIndex = declaration.indexOf(':');

			if (-1 === splitIndex) {
				continue;
			}

			property = declaration.slice(0, splitIndex).trim();
			value = declaration.slice(splitIndex + 1).trim();

			if (property) {
				styleObject[property] = value;
			}
		}

		return styleObject;
	}

	function stringifyInlineStyle(styleObject) {
		return Object.keys(styleObject).map(function (property) {
			return property + ': ' + styleObject[property];
		}).join('; ');
	}

	function mergeInlineStyles(existingStyle, newStyle) {
		var styleObject = parseInlineStyle(existingStyle);
		var newStyleObject = parseInlineStyle(newStyle);
		var key;

		for (key in newStyleObject) {
			if (Object.prototype.hasOwnProperty.call(newStyleObject, key)) {
				styleObject[key] = newStyleObject[key];
			}
		}

		return stringifyInlineStyle(styleObject);
	}

	function removeMaskStylesFromInlineStyle(styleValue) {
		var styleObject = parseInlineStyle(styleValue);
		var index;

		for (index = 0; index < MASK_STYLE_PROPS.length; index++) {
			delete styleObject[MASK_STYLE_PROPS[index]];
		}

		delete styleObject['--bdt-mask-overlay'];

		return stringifyInlineStyle(styleObject);
	}

	function removeMaskClasses(element) {
		if (!element || !element.classList) {
			return;
		}

		element.classList.remove('bdt-image-masking-yes');
		element.classList.remove('bdt-image-mask');
	}

	function unwrapGeneratedContainer(wrapper) {
		var generatedWrap;

		if (!wrapper) {
			return;
		}

		generatedWrap = wrapper.querySelector('[data-ep-image-masking-wrap="yes"]');

		if (!generatedWrap || !generatedWrap.parentNode) {
			return;
		}

		while (generatedWrap.firstChild) {
			generatedWrap.parentNode.insertBefore(generatedWrap.firstChild, generatedWrap);
		}

		generatedWrap.parentNode.removeChild(generatedWrap);
	}

	function cleanupElementById(elementId) {
		var wrapper = getElementWrapper(elementId);
		var image;
		var link;
		var index;

		if (!wrapper) {
			return;
		}

		unwrapGeneratedContainer(wrapper);

		image = wrapper.querySelector('img');
		link = wrapper.querySelector('a');

		if (image) {
			image.style.cssText = removeMaskStylesFromInlineStyle(image.getAttribute('style') || '');
		}

		if (link) {
			link.style.cssText = removeMaskStylesFromInlineStyle(link.getAttribute('style') || '');
			removeMaskClasses(link);
		}

		for (index = 0; index < wrapper.children.length; index++) {
			removeMaskClasses(wrapper.children[index]);
		}
	}

	function applyImageMasking(elementId, normalizedSettings) {
		var wrapper = getElementWrapper(elementId);
		var image;
		var link;
		var imgStyle;
		var wrapperStyle;
		var generatedWrap;

		if (!wrapper || !normalizedSettings) {
			return;
		}

		image = wrapper.querySelector('img');

		if (!image) {
			return;
		}

		imgStyle = buildImgStyle(normalizedSettings);
		wrapperStyle = buildWrapperStyle(normalizedSettings);
		link = image.closest('a');

		image.style.cssText = mergeInlineStyles(image.getAttribute('style') || '', imgStyle);

		if (link && wrapper.contains(link)) {
			link.classList.add('bdt-image-masking-yes', 'bdt-image-mask');

			if (wrapperStyle) {
				link.style.cssText = mergeInlineStyles(link.getAttribute('style') || '', wrapperStyle);
			}

			return;
		}

		generatedWrap = document.createElement('span');
		generatedWrap.className = 'bdt-image-masking-yes bdt-image-mask';
		generatedWrap.setAttribute('data-ep-image-masking-wrap', 'yes');

		if (wrapperStyle) {
			generatedWrap.style.cssText = wrapperStyle;
		}

		image.parentNode.insertBefore(generatedWrap, image);
		generatedWrap.appendChild(image);
	}

	function updateImageMasking(elementId, settings, source) {
		var normalizedSettings = normalizeImageMaskingSettings(settings);
		var isActive = isTruthy(getSettingValue(settings, ACTIVE_KEY));

		if (normalizedSettings) {
			cleanupElementById(elementId);
			applyImageMasking(elementId, normalizedSettings);
			return;
		}

		if (!isActive) {
			cleanupElementById(elementId);
		}
	}

	function scheduleElementUpdate(elementId, settings, source) {
		updateImageMasking(elementId, settings, source);

		window.setTimeout(function () {
			updateImageMasking(elementId, settings, source + '-delayed-100');
		}, 100);

		window.setTimeout(function () {
			updateImageMasking(elementId, settings, source + '-delayed-500');
		}, 500);
	}

	function syncExistingImages() {
		var previewDocument = getPreviewDocument();
		var elements;
		var index;
		var elementId;
		var settings;

		if (!previewDocument) {
			return;
		}

		elements = previewDocument.querySelectorAll('[data-id]');

		for (index = 0; index < elements.length; index++) {
			elementId = elements[index].getAttribute('data-id');
			settings = getElementSettings(elementId);

			if (!isImageElementId(elementId) || !hasImageMaskingControl(settings)) {
				continue;
			}

			scheduleElementUpdate(elementId, settings, 'initial-sync');
		}
	}

	function scheduleInitialSync() {
		var previewDocument = getPreviewDocument();

		if (!previewDocument || !previewDocument.body) {
			retryTimer = window.setTimeout(function () {
				retryTimer = null;
				scheduleInitialSync();
			}, 400);
			return;
		}

		window.requestAnimationFrame(syncExistingImages);
	}

	function getChangedModel(view) {
		if (view && view.container && view.container.model) {
			return view.container.model;
		}

		if (view && view.model) {
			return view.model;
		}

		return null;
	}

	function bindEditorChange() {
		if (editorChangeBound || !window.elementor || !window.elementor.channels || !window.elementor.channels.editor) {
			return;
		}

		editorChangeBound = true;

		window.elementor.channels.editor.on('change', function (view) {
			var model = getChangedModel(view);
			var settings;
			var elementId;

			if (!model || 'function' !== typeof model.get || !isImageModel(model)) {
				return;
			}

			settings = normalizeSettings(model.get('settings') || {});
			elementId = model.get('id');

			if (!elementId || !hasImageMaskingControl(settings)) {
				return;
			}

			scheduleElementUpdate(elementId, settings, 'control-change');
		});
	}

	function bindPreviewFrontendHooks() {
		var previewDocument = getPreviewDocument();
		var previewWindow = previewDocument && previewDocument.defaultView;
		var frontend = previewWindow && previewWindow.elementorFrontend;

		if (!frontend || !frontend.hooks || previewHooksBound) {
			return;
		}

		previewHooksBound = true;

		frontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
			var element = $scope && $scope[0];
			var elementId = element ? getElementId(element) : null;
			var settings;

			if (!elementId || !isImageElementId(elementId)) {
				return;
			}

			settings = getElementSettings(elementId);

			if (hasImageMaskingControl(settings)) {
				scheduleElementUpdate(elementId, settings, 'frontend-ready');
			}
		});

		frontend.hooks.addAction('frontend/element_ready/e-image.default', function ($scope) {
			var element = $scope && $scope[0];
			var elementId = element ? getElementId(element) : null;

			if (!elementId) {
				return;
			}

			scheduleElementUpdate(elementId, getElementSettings(elementId), 'frontend-ready-e-image');
		});
	}

	function onPreviewReady() {
		previewHooksBound = false;
		bindPreviewFrontendHooks();
		scheduleInitialSync();
	}

	function init() {
		var iframe = document.querySelector('#elementor-preview-iframe');

		if (!initialized) {
			bindEditorChange();
			initialized = true;
		}

		if (iframe) {
			iframe.addEventListener('load', onPreviewReady);
		}

		if (window.elementor) {
			window.elementor.on('preview:loaded', onPreviewReady);
		}

		onPreviewReady();
	}

	if ('loading' === document.readyState) {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
}(window, document));

/**
 * Start EDD product review carousel widget script
 */

(() => {
    'use strict';

    const widgetProductReviewCarousel = async (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const carouselEl = scopeEl.querySelector('.ep-edd-product-review-carousel');
        if (!carouselEl) return;

        const containerEl = carouselEl.querySelector('.swiper-carousel');
        const settings    = JSON.parse(carouselEl.dataset.settings || '{}');

        const Swiper = elementorFrontend.utils.swiper;
        await new Swiper(containerEl, settings);

        if (settings.pauseOnHover) {
            containerEl.addEventListener('mouseenter', () => containerEl.swiper.autoplay.stop());
            containerEl.addEventListener('mouseleave', () => containerEl.swiper.autoplay.start());
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction(
            'frontend/element_ready/bdt-edd-product-review-carousel.default',
            widgetProductReviewCarousel
        );
    });

})();

/**
 * End EDD product review carousel widget script
 */

/**
 * Start EDD Category carousel widget script
 */

(() => {
    'use strict';

    const widgetEddCategoryCarousel = async (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const carouselEl = scopeEl.querySelector('.bdt-edd-category-carousel');
        if (!carouselEl) return;

        const containerEl = carouselEl.querySelector('.swiper-carousel');
        const settings    = JSON.parse(carouselEl.dataset.settings || '{}');

        const Swiper = elementorFrontend.utils.swiper;
        await new Swiper(containerEl, settings);

        if (settings.pauseOnHover) {
            containerEl.addEventListener('mouseenter', () => containerEl.swiper.autoplay.stop());
            containerEl.addEventListener('mouseleave', () => containerEl.swiper.autoplay.start());
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction(
            'frontend/element_ready/bdt-edd-category-carousel.default',
            widgetEddCategoryCarousel
        );
    });

})();

/**
 * End EDD category carousel widget script
 */

/**
 * Start EDD product carousel widget script
 */

(() => {
    'use strict';

    const widgetEddProductCarousel = async (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const carouselEl = scopeEl.querySelector('.bdt-edd-product-carousel');
        if (!carouselEl) return;

        const containerEl = carouselEl.querySelector('.swiper-carousel');
        const settings    = JSON.parse(carouselEl.dataset.settings || '{}');

        const Swiper = elementorFrontend.utils.swiper;
        await new Swiper(containerEl, settings);

        if (settings.pauseOnHover) {
            containerEl.addEventListener('mouseenter', () => containerEl.swiper.autoplay.stop());
            containerEl.addEventListener('mouseleave', () => containerEl.swiper.autoplay.start());
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction(
            'frontend/element_ready/bdt-edd-product-carousel.default',
            widgetEddProductCarousel
        );
    });

})();

/**
 * End EDD product carousel widget script
 */

/**
 * Start EDD tabs widget script
 */

(() => {
    'use strict';

    const wrapInner = (parentEl, html) => {
        const tmp = document.createElement('div');
        tmp.innerHTML = html;
        const wrapper = tmp.firstElementChild;
        if (!wrapper) return;
        while (parentEl.firstChild) wrapper.appendChild(parentEl.firstChild);
        parentEl.appendChild(wrapper);
    };

    const scrollToEl = (targetEl, offset, duration, onComplete) => {
        const top = targetEl.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top, behavior: 'smooth' });
        setTimeout(onComplete, duration);
    };

    const widgetTabs = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const tabsAreaEl = scopeEl.querySelector('.bdt-tabs-area');
        if (!tabsAreaEl) return;

        const tabsEl = tabsAreaEl.querySelector('.bdt-tabs');
        const tabEl  = tabsEl?.querySelector('.bdt-tab');
        const rawSettings = tabsEl?.dataset.settings;
        const settings = rawSettings ? JSON.parse(rawSettings) : {};

        const animTime      = settings.hashScrollspyTime ?? 500;
        const customOffset  = settings.hashTopOffset ?? 0;
        const navStickyOffset = settings.navStickyOffset ?? 10;

        // Edit mode: modal iframe links
        scopeEl.querySelectorAll('.bdt-template-modal-iframe-edit-link').forEach(linkEl => {
            const modalSelector = linkEl.dataset.modalElement;
            const modalEl       = modalSelector ? document.querySelector(modalSelector) : null;
            if (!modalEl) return;

            linkEl.addEventListener('click', (e) => {
                e.preventDefault();
                bdtUIkit.modal(modalEl).show();
            });
            modalEl.addEventListener('beforehide', () => {
                window.parent.location.reload();
            });
        });

        const hash = () => window.location.hash.substring(1);

        const hashHandler = () => {
            if (!window.location.hash || !tabEl) return;

            const targetEl = tabsEl?.querySelector(`[data-title="${hash()}"]`);
            if (!targetEl) return;

            const tabsContainer = targetEl.closest('.bdt-tabs');
            const hashTargetId  = tabsContainer?.id;
            if (!hashTargetId) return;

            const targetContainer = document.getElementById(hashTargetId);
            if (!targetContainer) return;

            const tabIndex = parseInt(targetEl.dataset.tabIndex, 10) || 0;
            scrollToEl(targetContainer, customOffset, animTime, () => {
                bdtUIkit.tab(tabEl).show(tabIndex);
            });
        };

        if (settings.activeHash === 'yes' && settings.status !== 'bdt-sticky-custom') {
            window.addEventListener('load', hashHandler);
            tabsEl?.querySelectorAll('.bdt-tabs-item-title').forEach(titleEl => {
                titleEl.addEventListener('click', (e) => {
                    const dataTitle = (titleEl.dataset.title || '').trim();
                    if (dataTitle) window.location.hash = dataTitle;
                });
            });
            window.addEventListener('hashchange', hashHandler);
        }

        const stickyHashChange = () => {
            if (!window.location.hash || !tabEl) return;

            const targetEl = tabsEl?.querySelector(`[data-title="${hash()}"]`);
            if (!targetEl) return;

            const tabsContainer = targetEl.closest('.bdt-tabs');
            const hashTargetId  = tabsContainer?.id;
            if (!hashTargetId) return;

            const targetContainer = document.getElementById(hashTargetId);
            if (!targetContainer) return;

            const tabIndex = parseInt(targetEl.dataset.tabIndex, 10) || 0;
            scrollToEl(targetContainer, navStickyOffset, 1000, () => {
                bdtUIkit.tab(tabEl).show(tabIndex);
            });
        };

        if (settings.status === 'bdt-sticky-custom') {
            tabsEl?.querySelectorAll('.bdt-tabs-item-title').forEach(titleEl => {
                titleEl.addEventListener('click', (e) => {
                    if (settings.activeHash === 'yes') {
                        const dataTitle = (titleEl.dataset.title || '').trim();
                        if (dataTitle) window.location.hash = dataTitle;
                    } else {
                        const top = tabsEl.getBoundingClientRect().top + window.scrollY - navStickyOffset;
                        window.scrollTo({ top, behavior: 'smooth' });
                    }
                });
            });

            if (settings.activeHash === 'yes') {
                window.addEventListener('load', () => {
                    if (window.location.hash) stickyHashChange();
                });
                window.addEventListener('hashchange', stickyHashChange);
            }
        }

        // linkWidget: wire up external widgets to tabs
        const editMode   = Boolean(elementorFrontend.isEditMode());
        const linkWidget = settings.linkWidgetSettings;
        const activeItem = (settings.activeItem || 1) - 1;

        if (linkWidget && !editMode) {
            linkWidget.forEach((entrySelector, index) => {
                const entryEl = document.querySelector(entrySelector);
                if (!entryEl) return;

                const parent = entryEl.parentElement;
                if (!parent) return;

                if (index === 0) {
                    const contentEl = document.getElementById('bdt-tab-content-' + settings.linkWidgetId);
                    contentEl?.parentElement?.remove();

                    wrapInner(parent, '<div class="bdt-switcher-wrapper"></div>');
                    wrapInner(parent, `<div id="bdt-tab-content-${settings.linkWidgetId}" class="bdt-switcher bdt-switcher-item-content"></div>`);

                    if (settings.activeItem === undefined) {
                        entryEl.classList.add('bdt-active');
                    }
                }

                if (settings.activeItem !== undefined && index === activeItem) {
                    entryEl.classList.add('bdt-active');
                }

                entryEl.setAttribute('data-content-id', 'tab-' + (index + 1));
            });
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-edd-tabs.default', widgetTabs);
    });

})();

/**
 * End EDD tabs widget script
 */

/**
 * Start event calendar widget script
 */

(() => {
    'use strict';

    const widgetEventCarousel = async (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const carouselEl = scopeEl.querySelector('.bdt-event-calendar');
        if (!carouselEl) return;

        const containerEl = carouselEl.querySelector('.swiper-carousel');
        const settings    = JSON.parse(carouselEl.dataset.settings || '{}');

        const Swiper = elementorFrontend.utils.swiper;
        await new Swiper(containerEl, settings);

        if (settings.pauseOnHover) {
            containerEl.addEventListener('mouseenter', () => containerEl.swiper.autoplay.stop());
            containerEl.addEventListener('mouseleave', () => containerEl.swiper.autoplay.start());
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-event-carousel.default', widgetEventCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-event-carousel.fable', widgetEventCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-event-carousel.altra', widgetEventCarousel);
    });

})();

/**
 * End event calendar widget script
 */

/**
 * FooEvents Calendar Carousel widget script
 */

(() => {
    'use strict';

    const initFooeventsCarousel = async (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const carouselEl = scopeEl.querySelector('.bdt-fooevents-calendar-carousel');
        if (!carouselEl) return;

        const containerEl = carouselEl.querySelector('.swiper-carousel');
        if (!containerEl) return;

        const settings = JSON.parse(carouselEl.dataset.settings || '{}');

        const Swiper = elementorFrontend.utils.swiper;
        await new Swiper(containerEl, settings);

        if (settings.pauseOnHover) {
            containerEl.addEventListener('mouseenter', () => containerEl.swiper.autoplay.stop());
            containerEl.addEventListener('mouseleave', () => containerEl.swiper.autoplay.start());
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-fooevents-calendar-carousel.default', initFooeventsCarousel);
    });

})();

/**
 * Start fancy slider widget script
 */

(() => {
    'use strict';

    const widgetFancySlider = async (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const sliderEl = scopeEl.querySelector('.bdt-ep-fancy-slider');
        if (!sliderEl) return;

        const containerEl = sliderEl.querySelector('.swiper-carousel');
        const settings    = JSON.parse(sliderEl.dataset.settings || '{}');

        const Swiper = elementorFrontend.utils.swiper;
        await new Swiper(containerEl, settings);

        if (settings.pauseOnHover) {
            containerEl.addEventListener('mouseenter', () => containerEl.swiper.autoplay.stop());
            containerEl.addEventListener('mouseleave', () => containerEl.swiper.autoplay.start());
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-fancy-slider.default', widgetFancySlider);
    });

})();

/**
 * End fancy slider widget script
 */

/**
 * Start fancy tabs widget script
 */

(() => {
    'use strict';

    const widgetFancyTabs = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const fancyTabsEl = scopeEl.querySelector('.bdt-ep-fancy-tabs');
        if (!fancyTabsEl) return;

        const rawSettings = fancyTabsEl.dataset.settings;
        const settings    = rawSettings ? JSON.parse(rawSettings) : {};
        const tabsId      = settings.tabs_id;
        if (!tabsId) return;

        const iconBx   = document.querySelectorAll(`#${tabsId} .bdt-ep-fancy-tabs-item`);
        const contentBx = document.querySelectorAll(`#${tabsId} .bdt-ep-fancy-tabs-content`);
        const mouseEvent = settings.mouse_event || 'click';

        iconBx.forEach(itemEl => {
            itemEl.addEventListener(mouseEvent, function () {
                const targetId = this.dataset.id;
                contentBx.forEach(el => {
                    el.className = 'bdt-ep-fancy-tabs-content';
                });

                const targetContent = targetId ? document.getElementById(targetId) : null;
                if (targetContent) {
                    targetContent.className = 'bdt-ep-fancy-tabs-content active';
                }

                iconBx.forEach(el => {
                    el.className = 'bdt-ep-fancy-tabs-item';
                });
                this.className = 'bdt-ep-fancy-tabs-item active';
            });
        });
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-fancy-tabs.default', widgetFancyTabs);
    });

})();

/**
 * End fancy tabs widget script
 */

/**
 * Start faq widget script
 */

(() => {
    'use strict';

    const scrollToEl = (targetEl, offset, duration, onComplete) => {
        const top = targetEl.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top, behavior: 'smooth' });
        setTimeout(onComplete, duration);
    };

    const widgetFaq = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const faqWrapperEl = scopeEl.querySelector('.bdt-faq-wrapper');
        const faqFilterEl  = faqWrapperEl?.querySelector('.bdt-ep-grid-filters-wrapper');
        if (!faqFilterEl) return;

        const rawSettings = faqFilterEl.dataset.hashSettings;
        const settings    = rawSettings ? JSON.parse(rawSettings) : {};
        if (!settings || settings.activeHash !== 'yes') return;

        const hashTopOffset     = settings.hashTopOffset ?? 0;
        const hashScrollspyTime = settings.hashScrollspyTime ?? 500;

        const hash = () => window.location.hash.substring(1);

        const hashHandler = (scrollTime, offset) => {
            if (!window.location.hash) return;

            const filterControlSelector = `[bdt-filter-control="[data-filter*='bdtf-${hash()}']"]`;
            const filterEl = faqFilterEl.querySelector(filterControlSelector);
            if (!filterEl) return;

            const filterContainer = filterEl.closest('.bdt-ep-grid-filters-wrapper');
            const hashTargetId   = filterContainer?.id;
            if (!hashTargetId) return;

            const targetEl = document.getElementById(hashTargetId);
            if (!targetEl) return;

            scrollToEl(targetEl, offset, scrollTime, () => {
                filterEl.click();
            });
        };

        window.addEventListener('load', () => {
            hashHandler(1500, hashTopOffset);
        });

        faqFilterEl.querySelectorAll('.bdt-ep-grid-filter').forEach(filterEl => {
            filterEl.addEventListener('click', function () {
                const text = (this.innerText || '').trim().toLowerCase().replace(/\s+/g, '-');
                if (text) window.location.hash = text;
            });
        });

        window.addEventListener('hashchange', () => {
            hashHandler(hashScrollspyTime, hashTopOffset);
        });
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-faq.default', widgetFaq);
    });

})();

/**
 * End faq widget script
 */

/**
 * Start helpdesk widget script
 */

(() => {
    'use strict';

    const widgetHelpDesk = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const helpdeskEl = scopeEl.querySelector('.bdt-helpdesk');
        if (!helpdeskEl) return;

        const helpdeskTooltipEl = helpdeskEl.querySelector('.bdt-helpdesk-icons');
        const tooltips = helpdeskTooltipEl?.querySelectorAll(':scope > .bdt-tippy-tooltip') ?? [];
        const widgetID = scopeEl.dataset.id ?? '';

        tooltips.forEach((tipEl) => {
            tippy(tipEl, {
                allowHTML: true,
                theme: 'bdt-tippy-' + widgetID,
            });
        });
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-helpdesk.default', widgetHelpDesk);
    });

})();

/**
 * End helpdesk widget script
 */

/**
 * Start honeycombs widget script
 */

(() => {
    'use strict';

    const widgetHoneycombs = (scope) => {
        const scopeEl = scope instanceof jQuery ? scope[0] : scope;

        const honeycombsAreaEl = scopeEl.querySelector('.bdt-honeycombs-area');
        if (!honeycombsAreaEl) return;

        const honeycombsEl = honeycombsAreaEl.querySelector('.bdt-honeycombs');
        if (!honeycombsEl) return;

        const rawSettings = honeycombsEl.dataset.settings;
        const settings = rawSettings ? JSON.parse(rawSettings) : {};
        if (!settings) return;

        jQuery(honeycombsEl).honeycombs({
            combWidth   : settings.width,
            margin     : settings.margin,
            threshold  : 3,
            widthTablet: settings.width_tablet,
            widthMobile: settings.width_mobile,
            viewportLg : settings.viewport_lg,
            viewportMd : settings.viewport_md,
        });

        honeycombsEl.classList.add('honeycombs-loaded');
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-honeycombs.default', widgetHoneycombs);
    });

})();

/**
 * End honeycombs widget script
 */

/**
 * Start horizontal scroller widget script
 */

(() => {
    'use strict';

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        const ModuleHandler = elementorModules.frontend.handlers.Base;

        class HorizontalScroller extends ModuleHandler {
            bindEvents() {
                this.run();
            }

            getDefaultSettings() {
                return {
                    allowHTML: true,
                };
            }

            settings(key) {
                return this.getElementSettings(`horizontal_scroller_${key}`);
            }

            sectionJoiner() {
                const widgetID = this.$element[0]?.dataset?.id;
                const sectionList = this.settings('section_list');
                const widgetWrapper = `.elementor-element-${widgetID} .bdt-ep-hc-wrapper`;

                const sectionIds = (sectionList ?? [])
                    .map((section) => `#${section.horizontal_scroller_section_id}`)
                    .filter((id) => document.querySelector(id));

                if (!sectionIds.length) return;

                const selectedElements = document.querySelectorAll(sectionIds.join(', '));
                const wrapperEl = document.querySelector(widgetWrapper);
                if (wrapperEl) {
                    selectedElements.forEach((el) => wrapperEl.appendChild(el));
                }
            }

            horizontalScroller() {
                gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);

                const widgetID = this.$element[0]?.dataset?.id;
                const widgetWrapper = `.elementor-element-${widgetID} .bdt-ep-hc-wrapper`;
                const scroller = document.querySelector(widgetWrapper);
                const navLis = document.querySelectorAll(`${widgetWrapper} nav li`);

                const sections = gsap.utils.toArray(`${widgetWrapper} > .elementor-element`);
                if (!sections.length) return;

                const numSections = sections.length - 1;
                const snapVal = 1 / numSections;
                const optionSnap = this.settings('auto_fill') ? snapVal : false;
                let lastIndex = 0;

                const tween = gsap.to(sections, {
                    xPercent: -100 * numSections,
                    ease: 'none',
                    scrollTrigger: {
                        trigger: widgetWrapper,
                        pin: true,
                        scrub: true,
                        snap: optionSnap,
                        end: () => `+=${scroller.scrollWidth - innerWidth}`,
                        onUpdate: (self) => {
                            const newIndex = Math.round(self.progress / snapVal);
                            if (this.settings('show_dots') && newIndex !== lastIndex) {
                                navLis[lastIndex]?.classList.remove('is-active');
                                navLis[newIndex]?.classList.add('is-active');
                                lastIndex = newIndex;
                            }
                        },
                    },
                });

                navLis.forEach((anchor, i) => {
                    anchor.addEventListener('click', () => {
                        gsap.to(window, {
                            scrollTo: {
                                y: tween.scrollTrigger.start + i * innerWidth,
                                autoKill: false,
                            },
                            duration: 1,
                        });
                    });
                });
            }

            run() {
                if (elementorFrontend.isEditMode()) return;

                const widgetID = this.$element[0]?.dataset?.id;
                const widgetContainer = `.elementor-element-${widgetID}`;
                const containerEl = document.querySelector(widgetContainer);

                ScrollTrigger.matchMedia({
                    '(min-width: 1024px)': () => {
                        containerEl?.classList.add('bdt-ep-hc-active');
                        this.sectionJoiner();
                        this.horizontalScroller();
                    },
                    '(max-width: 1023px)': () => {
                        containerEl?.classList.remove('bdt-ep-hc-active');
                    },
                });
            }
        }

        elementorFrontend.hooks.addAction(
            'frontend/element_ready/bdt-horizontal-scroller.default',
            ($scope) => {
                elementorFrontend.elementsHandler.addHandler(HorizontalScroller, {
                    $element: $scope,
                });
            }
        );
    });

})();

/**
 * End horizontal scroller widget script
 */

(function ($, elementor) {
  $(window).on("elementor/frontend/init", function () {
    let ModuleHandler = elementorModules.frontend.handlers.Base,
      Stacker;

    Stacker = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },
      getDefaultSettings: function () {
        return {
          allowHTML: true,
        };
      },

      settings: function (key) {
        return this.getElementSettings("stacker_" + key);
      },

      sectionJoiner: function () {
        const widgetID = this.$element.data("id"),
          sectionId = [],
          sectionList = this.settings("section_list"),
          widgetContainer = ".elementor-element-" + widgetID,
          widgetWrapper = widgetContainer + " .bdt-ep-stacker";

        sectionList.forEach((section) => {
          sectionId.push("#" + section.stacker_section_id);
        });

        let haveIds = [];
        let elements;

        const topSection = document.querySelector(".elementor-top-section");
        const eConElements = document.getElementsByClassName("e-con");

        if (topSection) {
          elements = document.querySelectorAll(".elementor-top-section");
        }

        if (eConElements.length > 0) {
          elements = document.querySelectorAll(".elementor-element.e-con");
        }

        elements.forEach((element) => {
          const elementsWrapper = element.getAttribute("id");
          haveIds.push("#" + elementsWrapper);
        });

        function intersection(arr1, arr2) {
          const temp = [];
          for (const i in arr1) {
            const element = arr1[i];

            if (arr2.indexOf(element) > -1) {
              temp.push(element);
            }
          }
          return temp;
        }
        function multi_intersect() {
          const arrays = Array.prototype.slice.apply(arguments).slice(1);
          let temp = arguments[0];
          for (const i in arrays) {
            temp = intersection(arrays[i], temp);
            if (temp == []) {
              break;
            }
          }
          return temp;
        }

        const ids = multi_intersect(haveIds, sectionId).toString();
        if (ids) {
          const selectedIDs = document.querySelectorAll(ids);
          $(widgetWrapper).append(selectedIDs);
        }
      },

      StackerOpacity: function () {},
      StackerScript: function () {
        gsap.registerPlugin(ScrollTrigger);
        let cards;
        const widgetID = this.$element.data("id"),
          widgetContainer = ".elementor-element-" + widgetID;
        let stickDistance = 0;
        let opacityEnabled = this.settings("stacking_opacity") == 'yes' ? true : false;
        let stackingSpace = this.settings("stacking_space")
          ? this.settings("stacking_space").size
          : 40;
        let scrollerStart = this.settings("scroller_start")
          ? this.settings("scroller_start").size + "%"
          : "10%";

        let use3DEffect = this.settings("3d_effect") == 'yes' ? true : false;
        let scaleRatio = this.settings("scale_ratio")
          ? this.settings("scale_ratio").size / 100
          : 0.85;

        cards = gsap.utils.toArray(
          widgetContainer + " .bdt-ep-stacker > .elementor-element"
        );

        cards.forEach((card, i) => {
          let lastCardST = ScrollTrigger.create({
            trigger: cards[cards.length - 1],
            start: `top-=${0 * i} ${scrollerStart}`,
          });

          if(opacityEnabled) {
            gsap.set(card, { opacity: 0 });
            gsap.from(card, {
              opacity: 1,
              scrollTrigger: {
                trigger: card,
                scrub: true,
                start: `top-=${stackingSpace * i} ${scrollerStart}`,
                end: () => lastCardST.start + stickDistance,
              },
              ease: "none",
            });
          }

          if(use3DEffect) {
            gsap.set(card, {
              scale: 1,
              y: 0,
              transformOrigin: "center top",
              zIndex: i + 1
            });

            gsap.to(card, {
              scale: Math.pow(scaleRatio, cards.length - 1 - i),
              y: 0,
              scrollTrigger: {
                trigger: card,
                scrub: true,
                start: `top-=${stackingSpace * i} ${scrollerStart}`,
                end: () => lastCardST.start + stickDistance,
              },
              ease: "none",
            });
          }

          ScrollTrigger.create({
            trigger: card,
            start: `top-=${stackingSpace * i} ${scrollerStart}`,
            end: () => lastCardST.start + stickDistance,
            endTrigger: cards[cards.length - 1],
            pin: true,
            pinSpacing: false,
            ease: "none",
            toggleActions: "restart none none reverse",
          });
        });
      },

      run: function () {
        const widgetID = this.$element.data("id"),
          widgetContainer = ".elementor-element-" + widgetID,
          widgetWrapper = widgetContainer + " .bdt-ep-stacker";

        const editMode = Boolean(elementorFrontend.isEditMode());
        if (editMode) {
          $(widgetWrapper).append(
            '<div class="bdt-alert-warning" bdt-alert><a class="bdt-alert-close" bdt-close></a><p>Stacker Widget Placed Here (Only Visible for Editor).</p></div>'
          );
          return;
        }
        this.sectionJoiner();
        this.StackerScript();
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-stacker.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(Stacker, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);

/**
 * Start hover box widget script
 */

(() => {
    'use strict';

    const widgetHoverBox = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const hoverBoxEl = scopeEl.querySelector('.bdt-ep-hover-box');
        if (!hoverBoxEl) return;

        const rawSettings = hoverBoxEl.dataset.settings;
        const settings = rawSettings ? JSON.parse(rawSettings) : {};
        const boxId = settings?.box_id;
        if (!boxId) return;

        const iconBx = document.querySelectorAll(`#${boxId} .bdt-ep-hover-box-item`);
        const contentBx = document.querySelectorAll(`#${boxId} .bdt-ep-hover-box-content`);
        const mouseEvent = settings.mouse_event || 'click';

        iconBx.forEach((itemEl) => {
            itemEl.addEventListener(mouseEvent, function () {
                const targetId = this.dataset.id;
                contentBx.forEach((el) => {
                    el.className = 'bdt-ep-hover-box-content';
                });

                const targetContent = targetId ? document.getElementById(targetId) : null;
                if (targetContent) {
                    targetContent.className = 'bdt-ep-hover-box-content active';
                }

                iconBx.forEach((el) => {
                    el.className = 'bdt-ep-hover-box-item';
                });
                this.className = 'bdt-ep-hover-box-item active';
            });
        });
    };

    const widgetHoverBoxFlexure = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const hoverBoxEl = scopeEl.querySelector('.bdt-ep-hover-box');
        if (!hoverBoxEl) return;

        const rawSettings = hoverBoxEl.dataset.settings;
        const settings = rawSettings ? JSON.parse(rawSettings) : {};
        const mouseEvent = settings.mouse_event || 'click';

        const iconBoxes = hoverBoxEl.querySelectorAll('.bdt-ep-hover-box-item');

        const getByDataId = (id) => hoverBoxEl.querySelector(`[data-id="${id}"]`);
        const getContentById = (id) => document.getElementById(id);

        const removeActiveFromSiblings = (targetId) => {
            const contentEl = getContentById(targetId);
            contentEl?.parentElement?.querySelectorAll(':scope > .active').forEach((el) => {
                if (el !== contentEl) el.classList.remove('active');
            });
        };
        const removeActiveFromIconSiblings = (targetId) => {
            const iconEl = getByDataId(targetId);
            iconEl?.parentElement?.querySelectorAll(':scope > .active').forEach((el) => {
                if (el !== iconEl) el.classList.remove('active');
            });
        };
        const addInvisiableToIconSiblings = (targetId) => {
            const iconEl = getByDataId(targetId);
            iconEl?.parentElement?.querySelectorAll(':scope > .bdt-ep-hover-box-item').forEach((el) => {
                if (el !== iconEl) el.classList.add('invisiable');
            });
        };

        iconBoxes.forEach((iconEl) => {
            iconEl.addEventListener(mouseEvent, function () {
                const target = this.dataset.id;
                if (!target) return;

                removeActiveFromSiblings(target);
                removeActiveFromIconSiblings(target);

                const contentEl = getContentById(target);
                const targetIcon = getByDataId(target);

                if (mouseEvent === 'click') {
                    contentEl?.classList.toggle('active');
                    targetIcon?.classList.toggle('active');
                    addInvisiableToIconSiblings(target);
                } else {
                    contentEl?.classList.add('active');
                    targetIcon?.classList.add('active');
                    addInvisiableToIconSiblings(target);
                }
            });
        });

        if (mouseEvent === 'mouseover') {
            iconBoxes.forEach((iconEl) => {
                iconEl.addEventListener('mouseleave', function () {
                    const target = this.dataset.id;
                    if (!target) return;

                    const contentEl = getContentById(target);
                    const targetIcon = getByDataId(target);

                    contentEl?.parentElement?.querySelectorAll(':scope > *').forEach((el) => {
                        el.classList.remove('active');
                    });
                    contentEl?.classList.remove('active');

                    targetIcon?.parentElement?.querySelectorAll(':scope > *').forEach((el) => {
                        el.classList.remove('active', 'invisiable');
                    });
                    targetIcon?.classList.remove('active', 'invisiable');
                });
            });
        } else {
            // Click mode: delegated handler for .invisiable and .active toggles
            hoverBoxEl.addEventListener('click', (e) => {
                const item = e.target.closest('.bdt-ep-hover-box-item');
                if (!item) return;

                if (item.classList.contains('invisiable')) {
                    iconBoxes.forEach((el) => el.classList.add('invisiable'));
                } else if (item.classList.contains('active')) {
                    iconBoxes.forEach((el) => el.classList.remove('invisiable'));
                }
            });
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-hover-box.default', widgetHoverBox);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-hover-box.bdt-envelope', widgetHoverBox);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-hover-box.bdt-flexure', widgetHoverBoxFlexure);
    });

})();

/**
 * End hover box widget script
 */

/**
 * Start hover video widget script
 */

(() => {
    'use strict';

    const videoBufferChecker = (videoId) => {
        const checkInterval = 50;
        let lastPlayPos = 0;
        let currentPlayPos = 0;
        let bufferingDetected = false;
        const player = document.getElementById(videoId);
        if (!player) return;

        const checkBuffering = () => {
            currentPlayPos = player.currentTime;
            const offset = (checkInterval - 20) / 2000;

            if (
                !bufferingDetected &&
                currentPlayPos < lastPlayPos + offset &&
                !player.paused
            ) {
                const loader = player.closest('.bdt-hover-video')?.querySelector('.hover-video-loader');
                loader?.classList.add('active');
                bufferingDetected = true;
            }

            if (
                bufferingDetected &&
                currentPlayPos > lastPlayPos + offset &&
                !player.paused
            ) {
                const loader = player.closest('.bdt-hover-video')?.querySelector('.hover-video-loader');
                loader?.classList.remove('active');
                bufferingDetected = false;
            }
            lastPlayPos = currentPlayPos;
        };

        setInterval(checkBuffering, checkInterval);
    };

    const widgetDefaultSkin = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const instaVideoEl = scopeEl.querySelector('.bdt-hover-video');
        if (!instaVideoEl) return;

        const rawSettings = instaVideoEl.dataset.settings;
        const settings = rawSettings ? JSON.parse(rawSettings) : {};
        if (!settings?.id) return;

        const containerEl = document.getElementById(settings.id);
        if (!containerEl) return;

        const videos = instaVideoEl.querySelectorAll('.bdt-hover-wrapper-list video');
        const videoListParent = videos[0]?.parentElement;

        const showVideoAndPlay = (videoId, replay = false) => {
            const videoEl = document.getElementById(videoId);
            if (!videoEl) return;

            videoEl.parentElement?.querySelectorAll(':scope > *').forEach((sib) => {
                sib.style.display = sib === videoEl ? 'block' : 'none';
                sib.classList.toggle('active', sib === videoEl);
            });

            if (replay) videoEl.currentTime = 0;
            videoEl.play();
            videoBufferChecker(videoId);
        };

        const updateProgressUI = (videoId, videoEl) => {
            const barEl = instaVideoEl.querySelector(`.bdt-hover-bar-list [data-id="${videoId}"]`);
            const progressEl = instaVideoEl.querySelector(`.bdt-hover-btn-wrapper .bdt-hover-progress[data-id="${videoId}"]`);
            const pct = videoEl.duration ? (videoEl.currentTime / videoEl.duration) * 100 : 0;
            if (barEl) barEl.style.width = pct + '%';
            if (progressEl) progressEl.style.width = pct + '%';
        };

        const setupAutoplayEnded = () => {
            const videoElements = Array.from(videos);
            videoElements.forEach((v) => {
                v.addEventListener('ended', function () {
                    const next = this.nextElementSibling;
                    const first = videoElements[0];
                    const targetEl = (next && next.tagName === 'VIDEO' ? next : first);

                    targetEl.parentElement?.querySelectorAll(':scope > *').forEach((sib) => {
                        sib.style.display = sib === targetEl ? 'block' : 'none';
                        sib.classList.toggle('active', sib === targetEl);
                    });

                    instaVideoEl.querySelectorAll('.bdt-hover-bar-list .bdt-hover-progress').forEach((p) => {
                        p.style.width = '0%';
                    });
                    instaVideoEl.querySelectorAll('.bdt-hover-btn-wrapper .bdt-hover-progress').forEach((p) => {
                        p.style.width = '0%';
                    });

                    instaVideoEl.querySelectorAll('.bdt-hover-btn-wrapper [data-id]').forEach((el) => {
                        el.classList.toggle('active', el.dataset.id === targetEl.id);
                    });

                    targetEl.play();
                });
            });
        };

        const playVideoWithReplay = (el) => {
            if (settings.videoReplay === 'yes') el.currentTime = 0;
            el.play();
            videoBufferChecker(el.id);

            const endedHandler = () => {
                el.removeEventListener('ended', endedHandler);
                setTimeout(() => {
                    el.currentTime = 0;
                    el.play();
                    videoBufferChecker(el.id);
                    el.addEventListener('ended', endedHandler);
                }, 1500);
            };
            el.addEventListener('ended', endedHandler, { once: true });
        };

        videos.forEach((videoEl) => {
            const onPlay = () => playVideoWithReplay(videoEl);

            videoEl.addEventListener('mouseenter', onPlay);
            videoEl.addEventListener('click', onPlay);

            videoEl.addEventListener('mouseout', function () {
                if (settings.posterAgain === 'yes') {
                    this.currentTime = 0;
                    this.load();
                } else {
                    this.pause();
                }
            });

            videoEl.addEventListener('timeupdate', function () {
                const activeVideo = videoListParent?.querySelector('video.active');
                if (!activeVideo) return;
                const videoBarList = activeVideo.id;
                updateProgressUI(videoBarList, activeVideo);
            });
        });

        if (instaVideoEl.querySelector('.autoplay')) {
            const firstVideo = instaVideoEl.querySelector('.bdt-hover-wrapper-list video');
            firstVideo?.play();
            setupAutoplayEnded();
        }

        containerEl.querySelectorAll('.bdt-hover-btn').forEach((btn) => {
            const onBtnActivate = function () {
                const videoId = this.dataset.id;
                if (!videoId) return;
                showVideoAndPlay(videoId, settings.videoReplay === 'yes');

                instaVideoEl.querySelectorAll('.bdt-hover-bar-list .bdt-hover-progress').forEach((p) => p.classList.remove('active'));
                instaVideoEl.querySelector(`.bdt-hover-bar-list [data-id="${videoId}"]`)?.classList.add('active');
                instaVideoEl.querySelectorAll('.bdt-hover-btn-wrapper [data-id]').forEach((el) => {
                    el.classList.toggle('active', el.dataset.id === videoId);
                });
            };

            btn.addEventListener('mouseenter', onBtnActivate);
            btn.addEventListener('click', onBtnActivate);
        });
    };

    const widgetVideoAccordion = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const videoAccordionEl = scopeEl.querySelector('.bdt-hover-video');
        if (!videoAccordionEl) return;

        const rawSettings = videoAccordionEl.dataset.settings;
        const settings = rawSettings ? JSON.parse(rawSettings) : {};
        if (!settings?.id) return;

        const containerEl = document.getElementById(settings.id);
        if (!containerEl) return;

        const videos = videoAccordionEl.querySelectorAll('.bdt-hover-wrapper-list video');
        const videoListParent = videos[0]?.parentElement;

        const setupAutoplayEnded = () => {
            const videoElements = Array.from(videos);
            videoElements.forEach((v) => {
                v.addEventListener('ended', function () {
                    const next = this.nextElementSibling;
                    const first = videoElements[0];
                    const targetEl = (next && next.tagName === 'VIDEO' ? next : first);

                    targetEl.parentElement?.querySelectorAll(':scope > *').forEach((sib) => {
                        sib.style.display = sib === targetEl ? 'block' : 'none';
                        sib.classList.toggle('active', sib === targetEl);
                    });

                    videoAccordionEl.querySelectorAll('.bdt-hover-bar-list .bdt-hover-progress').forEach((p) => {
                        p.style.width = '0%';
                    });

                    targetEl.play();
                });
            });
        };

        videos.forEach((videoEl) => {
            videoEl.addEventListener('timeupdate', function () {
                const activeVideo = videoListParent?.querySelector('video.active');
                if (!activeVideo) return;
                const videoBarList = activeVideo.id;
                const barEl = videoAccordionEl.querySelector(`.bdt-hover-bar-list [data-id="${videoBarList}"]`);
                const pct = activeVideo.duration ? (activeVideo.currentTime / activeVideo.duration) * 100 : 0;
                if (barEl) barEl.style.width = pct + '%';
            });
        });

        if (videoAccordionEl.querySelector('.autoplay')) {
            const firstVideo = videoAccordionEl.querySelector('.hover-video-list video');
            firstVideo?.play();
            setupAutoplayEnded();
        }

        containerEl.querySelectorAll('.bdt-hover-mask-list .bdt-hover-mask').forEach((maskEl) => {
            maskEl.addEventListener('mouseenter', function () {
                const videoId = this.dataset.id;
                if (!videoId) return;

                const videoEl = document.getElementById(videoId);
                videoEl?.parentElement?.querySelectorAll(':scope > *').forEach((sib) => {
                    if (sib.tagName === 'VIDEO') {
                        sib.pause();
                        sib.style.display = sib.id === videoId ? 'block' : 'none';
                        sib.classList.toggle('active', sib.id === videoId);
                    }
                });

                if (settings.videoReplay === 'yes') videoEl.currentTime = 0;
                videoEl?.play();
                videoBufferChecker(videoId);

                videoAccordionEl.querySelectorAll('.bdt-hover-bar-list .bdt-hover-progress').forEach((p) => p.classList.remove('active'));
                videoAccordionEl.querySelector(`.bdt-hover-bar-list [data-id="${videoId}"]`)?.classList.add('active');
                videoAccordionEl.querySelectorAll('.bdt-hover-mask-list [data-id]').forEach((el) => {
                    el.classList.toggle('active', el.dataset.id === videoId);
                });

                const endedHandler = () => {
                    videoEl?.removeEventListener('ended', endedHandler);
                    setTimeout(() => {
                        videoEl?.play();
                        videoBufferChecker(videoId);
                        videoEl?.addEventListener('ended', endedHandler);
                    }, 1500);
                };
                videoEl?.addEventListener('ended', endedHandler, { once: true });
            });

            maskEl.addEventListener('click', function () {
                const videoId = this.dataset.id;
                if (!videoId) return;

                const videoEl = document.getElementById(videoId);
                videoEl?.parentElement?.querySelectorAll(':scope > video').forEach((v) => {
                    v.pause();
                    v.style.display = v.id === videoId ? 'block' : 'none';
                    v.classList.toggle('active', v.id === videoId);
                });

                if (settings.videoReplay === 'yes') videoEl.currentTime = 0;
                videoEl?.play();
                videoBufferChecker(videoId);

                videoAccordionEl.querySelectorAll('.bdt-hover-bar-list .bdt-hover-progress').forEach((p) => p.classList.remove('active'));
                videoAccordionEl.querySelector(`.bdt-hover-bar-list [data-id="${videoId}"]`)?.classList.add('active');
                videoAccordionEl.querySelectorAll('.bdt-hover-mask-list [data-id]').forEach((el) => {
                    el.classList.toggle('active', el.dataset.id === videoId);
                });

                const endedHandler = () => {
                    videoEl?.removeEventListener('ended', endedHandler);
                    setTimeout(() => {
                        videoEl?.play();
                        videoBufferChecker(videoId);
                        videoEl?.addEventListener('ended', endedHandler);
                    }, 1500);
                };
                videoEl?.addEventListener('ended', endedHandler, { once: true });
            });
        });

        const maskList = containerEl.querySelector('.bdt-hover-mask-list');
        if (maskList) {
            maskList.addEventListener('mouseout', (e) => {
                if (maskList.contains(e.relatedTarget)) return;
                const targetVideos = videoAccordionEl.querySelectorAll('.bdt-hover-wrapper-list video');
                targetVideos?.forEach((v) => {
                    if (settings.posterAgain === 'yes') {
                        v.currentTime = 0;
                        v.load();
                    } else {
                        v.pause();
                    }
                });
            });
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-hover-video.default', widgetDefaultSkin);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-hover-video.accordion', widgetVideoAccordion);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-hover-video.vertical', widgetVideoAccordion);
    });

})();

/**
 * End hover video widget script
 */

/**
 * Start iconnav widget script
 */

(function () {
    'use strict';

    const widgetIconNav = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;
        const iconnav = scopeEl.querySelector('div.bdt-icon-nav');
        if (!iconnav) return;

        const tooltips = iconnav.querySelectorAll('.bdt-icon-nav > .bdt-tippy-tooltip');
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
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-iconnav.default', widgetIconNav);
    });
})();

/**
 * End iconnav widget script
 */

/**
 * Start iframe widget script
 */

(function () {
    'use strict';

    const widgetIframe = (scope) => {
        const scopeEl = scope instanceof jQuery ? scope[0] : scope;
        const iframes = scopeEl.querySelectorAll('.bdt-iframe > iframe');
        if (!iframes.length) return;

        const firstIframe = iframes[0];
        const autoHeight = firstIframe.dataset.auto_height === 'true' || firstIframe.dataset.auto_height === 'yes';
        const throttle = parseInt(firstIframe.dataset.throttle, 10) || 300;
        const threshold = parseInt(firstIframe.dataset.threshold, 10) || 100;
        const live = firstIframe.dataset.live !== 'false';

        // Recliner (jQuery plugin) - required for lazy loading
        jQuery(iframes).recliner({ throttle, threshold, live });

        if (autoHeight) {
            iframes.forEach((iframe) => {
                iframe.addEventListener('lazyshow', function () {
                    try {
                        const doc = this.contentDocument;
                        const height = doc?.documentElement?.scrollHeight ?? doc?.documentElement?.offsetHeight ?? this.offsetHeight;
                        this.style.height = height + 'px';
                    } catch (_) {
                        // Cross-origin: cannot access contentDocument
                    }
                });
            });
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-iframe.default', widgetIframe);
    });
})();

/**
 * End iframe widget script
 */

/**
 * Start image expand widget script
 */

(function () {
    'use strict';

    const getSiblings = (el, selector) => {
        const parent = el.parentElement;
        if (!parent) return [];
        return [...parent.children].filter((c) => c !== el && (!selector || c.matches(selector)));
    };

    const widgetImageExpand = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;
        const imageExpand = scopeEl.querySelector('.bdt-ep-image-expand');
        if (!imageExpand) return;

        let settings = {};
        try {
            const raw = imageExpand.dataset.settings;
            settings = raw ? JSON.parse(raw) : {};
        } catch (_) {}

        const items = imageExpand.querySelectorAll('.bdt-ep-image-expand-item');
        const animClass = 'bdt-animation-' + (settings.default_animation_type || 'fade');

        items.forEach((item) => {
            item.addEventListener('click', function () {
                this.classList.toggle('active');
                document.body.classList.add('bdt-ep-image-expanded');
            });
        });

        document.addEventListener('click', function closeHandler(e) {
            if (!document.body.classList.contains('bdt-ep-image-expanded')) return;
            if (imageExpand.contains(e.target)) return;

            items.forEach((item) => item.classList.remove('active'));
            imageExpand.querySelectorAll('.bdt-ep-image-expand-item .bdt-ep-image-expand-content *').forEach((el) => el.classList.remove(animClass));
            imageExpand.querySelectorAll('.bdt-ep-image-expand-button').forEach((el) => el.classList.remove('bdt-animation-slide-bottom'));
        });

        if (settings.animation_status === 'yes') {
            const animationOf = settings.animation_of || '.bdt-ep-image-expand-sub-title';

            items.forEach((item) => {
                const quotes = [...item.querySelectorAll(animationOf)];
                if (!quotes.length || typeof SplitText === 'undefined' || typeof gsap === 'undefined') return;

                const splitTexts = quotes.map((quote) => {
                    gsap.set(quote, { perspective: 400 });
                    return new SplitText(quote, { type: 'chars, words, lines' });
                });
                const splitTextTimeline = gsap.timeline();

                const kill = () => {
                    splitTextTimeline.clear().time(0);
                    splitTexts.forEach((st) => st.revert());
                };

                item.addEventListener('click', function () {
                    imageExpand.querySelectorAll('.bdt-ep-image-expand-button').forEach((el) => {
                        el.classList.remove('bdt-animation-slide-bottom');
                        el.classList.add('bdt-invisible');
                    });

                    setTimeout(() => {
                        kill();
                        splitTexts.forEach((st) => st.split({ type: 'chars, words, lines' }));

                        let stringType = [];
                        splitTexts.forEach((st) => {
                            if (settings.animation_on === 'lines') stringType = stringType.concat(st.lines);
                            else if (settings.animation_on === 'chars') stringType = stringType.concat(st.chars);
                            else stringType = stringType.concat(st.words);
                        });

                        splitTextTimeline
                            .staggerFrom(stringType, 0.5, {
                                opacity: 0,
                                scale: settings.anim_scale ?? 0,
                                y: settings.anim_rotation_y ?? 80,
                                rotationX: settings.anim_rotation_x ?? 180,
                                transformOrigin: settings.anim_transform_origin || '0% 50% -50'
                            }, 0.1)
                            .then(() => {
                                imageExpand.querySelectorAll('.bdt-ep-image-expand-button').forEach((el) => el.classList.remove('bdt-invisible'));
                                const activeBtn = imageExpand.querySelector('.bdt-ep-image-expand-item.active .bdt-ep-image-expand-button');
                                activeBtn?.classList.add('bdt-animation-slide-bottom');
                            });
                        splitTextTimeline.play();
                    }, 1000);
                });
            });
        } else {
            imageExpand.addEventListener('click', function (e) {
                const item = e.target.closest('.bdt-ep-image-expand-item');
                if (!item) return;

                const siblingItems = getSiblings(item, '.bdt-ep-image-expand-item');
                siblingItems.forEach((sib) => {
                    sib.querySelectorAll('.bdt-ep-image-expand-content *').forEach((el) => el.classList.remove(animClass));
                });
                item.querySelectorAll('.bdt-ep-image-expand-content *').forEach((el) => el.classList.remove(animClass));

                setTimeout(() => {
                    const activeItem = imageExpand.querySelector('.bdt-ep-image-expand-item.active');
                    activeItem?.querySelectorAll('.bdt-ep-image-expand-content *').forEach((el) => el.classList.add(animClass));
                }, 1000);
            });
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-image-expand.default', widgetImageExpand);
    });
})();

/**
 * End image expand widget script
 */

/**
 * Start image parallax widget script
 */

(function () {
    'use strict';

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorModules?.frontend?.handlers?.Base || !window.elementorFrontend?.hooks) return;

        const ModuleHandler = elementorModules.frontend.handlers.Base;
        const ImagePrallaxEffects = ModuleHandler.extend({
            bindEvents() {
                this.run();
            },
            onElementChange: debounce(function (prop) {
                if (prop.indexOf('element_pack_image_parallax_effects_') !== -1) {
                    this.run();
                }
            }, 400),
            settings(key) {
                return this.getElementSettings('element_pack_image_parallax_effects_' + key);
            },
            run() {
                const element = this.$element[0];
                const widgetContainer = element?.querySelector('.elementor-image');
                if (!widgetContainer) return;

                const img = widgetContainer.querySelector('img');
                const image = img?.src;
                if (!image) return;

                if (this.settings('enable') !== 'yes') return;

                const existing = widgetContainer.querySelector('.bdt-image-parallax-wrapper');
                if (existing) existing.remove();

                const wrapper = document.createElement('div');
                wrapper.className = 'bdt-image-parallax-wrapper';
                wrapper.setAttribute('bdt-parallax', 'bgy: -200');
                wrapper.style.backgroundImage = 'url(' + image + ')';
                widgetContainer.appendChild(wrapper);
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', ($scope) => {
            elementorFrontend.elementsHandler.addHandler(ImagePrallaxEffects, { $element: $scope });
        });
    });
})();

/**
 * End image parallax widget script
 */

/**
 * Start instagram widget script
 */

(function () {
    'use strict';

    const widgetInstagram = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;
        const instagram = scopeEl.querySelector('.bdt-instagram');
        if (!instagram) return;

        let settings = {};
        try {
            const raw = instagram.dataset.settings;
            settings = raw ? JSON.parse(raw) : {};
        } catch (_) {}

        const loadMoreBtn = instagram.querySelector('.bdt-load-more');
        const itemHolder = instagram.querySelector('.bdt-grid');
        if (!itemHolder) return;

        let currentPage = settings.current_page ?? 1;

        const callInstagram = async () => {
            settings.current_page = currentPage;
            const body = new URLSearchParams();
            for (const [k, v] of Object.entries(settings)) {
                if (v !== null && v !== undefined) {
                    body.append(k, typeof v === 'object' ? JSON.stringify(v) : String(v));
                }
            }

            try {
                const res = await fetch(window.ElementPackConfig?.ajaxurl || '', {
                    method: 'POST',
                    body,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await res.text();
                itemHolder.insertAdjacentHTML('beforeend', html);
            } finally {
                if (loadMoreBtn) loadMoreBtn.classList.remove('bdt-load-more-loading');
            }
        };

        callInstagram();

        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', () => {
                loadMoreBtn.classList.add('bdt-load-more-loading');
                currentPage++;
                callInstagram();
            });
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-instagram.default', widgetInstagram);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-instagram.bdt-instagram-carousel', widgetInstagram);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-instagram.bdt-classic-grid', widgetInstagram);
    });
})();

/**
 * End instagram widget script
 */

(function ($, elementor) {
  "use strict";

  $(window).on("elementor/frontend/init", function () {
    var ModuleHandler = elementorModules.frontend.handlers.Base,
      SvgMaps;
    SvgMaps = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },

      getDefaultSettings: function () {
        return {};
      },

      onElementChange: debounce(function (prop) {
        if (prop.indexOf("svg_maps") !== -1) {
          this.run();
        }
      }, 400),

      settings: function (key) {
        return this.getElementSettings("svg_maps_" + key);
      },
      createColorAxisArray: function () {
        const axisColorList = this.settings("region_axis_color");

        const colors = [];
        axisColorList.forEach((color) => {
          if (color.axis_color !== "") {
            colors.push(color.axis_color);
          }
        });

        return colors;
      },

      createCustomRegion: function (data, options, isLinkable) {
        const regionList = this.settings("array_regions");
        const currentRegionColors = [];
        data.addColumn("string", "Country");
        data.addColumn("number", "Population");
        data.addColumn({ type: "string", role: "tooltip", p: { html: true } });
        regionList.forEach((region, index) => {
          currentRegionColors.push(
            region.active_region_color ? region.active_region_color : "#146C94"
          );
          options.colors = currentRegionColors;
          data.addRows([
            [
              {
                v: region.active_region_code,
                f:
                  region.active_region_name !== ""
                    ? region.active_region_name
                    : region.active_region_code,
              },
              index,
              region.active_tooltip_content,
            ],
          ]);

          isLinkable[region.active_region_code] = {
            url: region.region_link ? region.region_link.url : "",
            target:
              region.region_link && !region.region_link.is_external
                ? "_self"
                : "",
          };
        });
      },
      createDataVisRegions: function (isLinkable) {
        const dataVisualArray = [];
        const dataVisualTitle = this.settings("region_value_title");
        const dataRegionList = this.settings("data_visual_array_regions");
        dataVisualArray[0] = ["Country", dataVisualTitle];

        dataRegionList.forEach((region) => {
          dataVisualArray.push([
            region.visual_data_region_name,
            region.visual_data_value,
          ]);

          isLinkable[region.visual_data_region_name] = {
            url: region.visual_data_region_link
              ? region.visual_data_region_link.url
              : "",
            target:
              region.visual_data_region_link &&
              !region.visual_data_region_link.is_external
                ? "_self"
                : "",
          };
        });

        var data = google.visualization.arrayToDataTable(dataVisualArray);
        return {
          data,
        };
      },
      run: function () {
        const self = this;
        var options = this.getDefaultSettings();
        var widgetID = this.$element.data("id");
        
        var $container = this.$element.find(".bdt-svg-maps");
        if (!$container.length) {
          return;
        }
        const $mapWrapper = document.getElementById(`bdt-svg-maps-${widgetID}`);

        google.charts.load("current", {
          packages: ["geochart"],
        });
        google.charts.setOnLoadCallback(drawTable);

        function drawTable() {
          var data = new google.visualization.DataTable();
          let isLinkable = [];
          let markerIsLinkable = [];

          switch (self.settings("region_type")) {
            case "continent":
              options.region = self.settings("display_region_continent")
                ? self.settings("display_region_continent")
                : "002";
              break;

            case "subcontinent":
              options.region = self.settings("display_region_sub_continent")
                ? self.settings("display_region_sub_continent")
                : "015";
              break;

            case "countries":
              options.region = self.settings("display_region_countries")
                ? self.settings("display_region_countries")
                : "AU";
              break;

            default:
              options.region = "world";
              break;
          }

          options.width = self.settings("width")
            ? self.settings("width").size
            : 600;
          options.height = self.settings("height")
            ? self.settings("height").size
            : 400;
          options.backgroundColor = self.settings("background_color")
            ? self.settings("background_color")
            : "#81d4fa";
          options.datalessRegionColor = self.settings("dataless_region_color")
            ? self.settings("dataless_region_color")
            : "#f8bbd0";

          options.tooltip = {
            isHtml: true,
            trigger: self.settings("tooltip_trigger")
              ? self.settings("tooltip_trigger")
              : "focus",
            textStyle: {
              // fontSize: self.settings("tooltip_font_size")   ? self.settings("tooltip_font_size") : 14,
              bold:
                self.settings("tooltip_font_weight") === "yes" ? true : false,
              italic:
                self.settings("tooltip_font_style") === "yes" ? true : false,
            },
          };
          if (self.settings("show_legend") !== "yes") {
            options.legend = "none";
          } else {
            options.legend = {
              textStyle: {
                color: self.settings("legend_font_color")
                  ? self.settings("legend_font_color")
                  : "#000000",
                fontSize: self.settings("legend_font_size")
                  ? self.settings("legend_font_size")
                  : 16,
                bold:
                  self.settings("legend_font_weight") === "yes" ? true : false,
                italic:
                  self.settings("legend_font_style") === "yes" ? true : false,
              },
            };
          }

          if (self.settings("display_mode") === "regions") {
            if (self.settings("display_type") === "custom") {
              self.createCustomRegion(data, options, isLinkable);
            } else {
              const dataVisRegions = self.createDataVisRegions(isLinkable);
              data = dataVisRegions.data;
              options.colorAxis = {
                colors: self.createColorAxisArray(),
              };
            }
          }

          var chart = new google.visualization.GeoChart($mapWrapper);
          google.visualization.events.addListener(chart, "select", () => {
            const selection = chart.getSelection();
            if (selection.length === 1) {
              const selectedRow = selection[0].row;
              const selectedRegion = data.getValue(selectedRow, 0);
              switch (self.settings("display_mode")) {
                case "regions":
                  isLinkable[selectedRegion].url !== ""
                    ? window.open(
                        isLinkable[selectedRegion].url,
                        isLinkable[selectedRegion].target
                      )
                    : "";
                  break;
                case "markers":
                  markerIsLinkable[selectedRegion].url !== ""
                    ? window.open(
                        markerIsLinkable[selectedRegion].url,
                        markerIsLinkable[selectedRegion].target
                      )
                    : "";
                  break;
              }
            }
          });
          chart.draw(data, options);
        }
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-svg-maps.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(SvgMaps, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);

/**
 * Start interactive tabs widget script
 */

(function () {
    'use strict';

    const widgetInteractiveTabs = async (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;
        const slider = scopeEl.querySelector('.bdt-interactive-tabs-content');
        const tabs = scopeEl.querySelector('.bdt-interactive-tabs');
        if (!slider || !tabs) return;

        const sliderContainer = slider.querySelector('.swiper-carousel');
        if (!sliderContainer) return;

        let settings = {};
        try {
            const raw = slider.dataset.settings;
            settings = raw ? JSON.parse(raw) : {};
        } catch (_) {}

        const Swiper = elementorFrontend?.utils?.swiper;
        if (!Swiper) return;

        const swiper = await new Swiper(sliderContainer, settings);

        if (settings.pauseOnHover) {
            sliderContainer.addEventListener('mouseenter', () => sliderContainer.swiper?.autoplay?.stop());
            sliderContainer.addEventListener('mouseleave', () => sliderContainer.swiper?.autoplay?.start());
        }

        const stopVideos = () => {
            scopeEl.querySelectorAll('.bdt-interactive-tabs-iframe').forEach((video) => {
                const src = video.src;
                video.src = '';
                if (src) video.src = src.replace(/autoplay=1|autoplay=true/gi, 'autoplay=0');
            });
        };

        const tabItems = tabs.querySelectorAll('.bdt-interactive-tabs-item');
        const firstTab = tabItems[0];
        if (firstTab) firstTab.classList.add('bdt-active');

        swiper.on('slideChange', () => {
            tabItems.forEach((item) => item.classList.remove('bdt-active'));
            const activeTab = tabItems[swiper.realIndex];
            if (activeTab) activeTab.classList.add('bdt-active');
            stopVideos();
        });

        tabs.querySelectorAll('.bdt-interactive-tabs-wrap .bdt-interactive-tabs-item[data-slide]').forEach((item) => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const slideno = parseInt(item.dataset.slide, 10) || 0;
                stopVideos();
                swiper.slideTo(slideno + 1);
            });
        });
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-interactive-tabs.default', widgetInteractiveTabs);
    });
})();

/**
 * End interactive tabs widget script
 */

/**
 * Start logo carousel widget script
 */

(function () {
    'use strict';

    const widgetLogoCarousel = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;
        const logoCarousel = scopeEl.querySelector('.bdt-logo-carousel-wrapper');
        if (!logoCarousel) return;

        const tooltips = logoCarousel.querySelectorAll(':scope > .bdt-tippy-tooltip');
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
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-logo-carousel.default', widgetLogoCarousel);
    });
})();

/**
 * End logo carousel widget script
 */

/**
 * Start lottie icon box widget script
 */

(function () {
    'use strict';

    const widgetLottieImage = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;
        const lottieEl = scopeEl.querySelector('.bdt-lottie-container');
        if (!lottieEl) return;

        let settings = {};
        try {
            const raw = lottieEl.dataset.settings;
            settings = raw ? JSON.parse(raw) : {};
        } catch (_) {}

        const lottieRun = () => {
            let jsonPathUrl = '';

            if (settings.is_json_url == 1) {
                if (settings.json_path) jsonPathUrl = settings.json_path;
            } else if (settings.json_code) {
                const blob = new Blob([settings.json_code], { type: 'application/javascript' });
                jsonPathUrl = URL.createObjectURL(blob);
            }

            if (!jsonPathUrl) return;

            const animation = lottie.loadAnimation({
                container: lottieEl,
                path: jsonPathUrl,
                renderer: settings.lottie_renderer,
                autoplay: settings.play_action === 'autoplay',
                loop: settings.loop
            });

            if (jsonPathUrl.startsWith('blob:')) URL.revokeObjectURL(jsonPathUrl);

            animation.addEventListener('DOMLoaded', () => {
                const firstFrame = animation.firstFrame;
                const totalFrame = animation.totalFrames;
                const getFrameNumberByPercent = (percent) => {
                    percent = Math.min(100, Math.max(0, percent));
                    return firstFrame + (totalFrame - firstFrame) * percent / 100;
                };
                const startPoint = getFrameNumberByPercent(settings.start_point ?? 0);
                const endPoint = getFrameNumberByPercent(settings.end_point ?? 100);
                animation.playSegments([startPoint, endPoint], true);
            });

            animation.setSpeed(settings.speed ?? 1);

            if (settings.play_action) {
                let eventTarget = lottieEl;
                if (settings.play_action === 'column') eventTarget = scopeEl.closest('.elementor-element') || lottieEl;
                else if (settings.play_action === 'section') eventTarget = scopeEl.closest('.elementor-section') || lottieEl;

                if (settings.play_action === 'click') {
                    const clickTarget = scopeEl.closest('.elementor-element') || lottieEl;
                    clickTarget.addEventListener('click', () => animation.goToAndPlay(0));
                } else if (settings.play_action !== 'autoplay') {
                    eventTarget.addEventListener('mouseenter', () => animation.goToAndPlay(0));
                }
            }
        };

        if (settings.view_type === 'scroll') {
            epObserveTarget(scopeEl, lottieRun);
        } else {
            lottieRun();
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-lottie-icon-box.default', widgetLottieImage);
    });
})();

/**
 * End lottie icon box widget script
 */

/**
 * Start lottie image widget script
 */

(function () {
    'use strict';

    const widgetLottieImage = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;
        const lottieEl = scopeEl.querySelector('.bdt-lottie-container');
        if (!lottieEl) return;

        let settings = {};
        try {
            const raw = lottieEl.dataset.settings;
            settings = raw ? JSON.parse(raw) : {};
        } catch (_) {}

        const lottieRun = () => {
            let jsonPathUrl = '';

            if (settings.is_json_url == 1) {
                if (settings.json_path) jsonPathUrl = settings.json_path;
            } else if (settings.json_code) {
                const blob = new Blob([settings.json_code], { type: 'application/javascript' });
                jsonPathUrl = URL.createObjectURL(blob);
            }

            if (!jsonPathUrl) return;

            const animation = lottie.loadAnimation({
                container: lottieEl,
                path: jsonPathUrl,
                renderer: settings.lottie_renderer,
                autoplay: settings.play_action === 'autoplay',
                loop: settings.loop
            });

            if (jsonPathUrl.startsWith('blob:')) URL.revokeObjectURL(jsonPathUrl);

            animation.addEventListener('DOMLoaded', () => {
                const firstFrame = animation.firstFrame;
                const totalFrame = animation.totalFrames;
                const getFrameNumberByPercent = (percent) => {
                    percent = Math.min(100, Math.max(0, percent));
                    return firstFrame + (totalFrame - firstFrame) * percent / 100;
                };
                const startPoint = getFrameNumberByPercent(settings.start_point ?? 0);
                const endPoint = getFrameNumberByPercent(settings.end_point ?? 100);
                animation.playSegments([startPoint, endPoint], true);
            });

            animation.setSpeed(settings.speed ?? 1);

            if (settings.play_action) {
                let eventTarget = lottieEl;
                if (settings.play_action === 'column') eventTarget = scopeEl.closest('.elementor-element') || lottieEl;
                else if (settings.play_action === 'section') eventTarget = scopeEl.closest('.elementor-section') || lottieEl;

                if (settings.play_action === 'click') {
                    eventTarget.addEventListener('click', () => animation.goToAndPlay(0));
                } else if (settings.play_action !== 'autoplay') {
                    eventTarget.addEventListener('mouseenter', () => animation.goToAndPlay(0));
                }
            }
        };

        if (settings.view_type === 'scroll') {
            epObserveTarget(scopeEl, lottieRun);
        } else {
            lottieRun();
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-lottie-image.default', widgetLottieImage);
    });
})();

/**
 * End lottie image widget script
 */

/**
 * Start mailchimp widget script
 */

(function () {
    'use strict';

    const widgetMailChimp = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;
        const mailChimpForm = scopeEl.querySelector('.bdt-mailchimp');
        if (!mailChimpForm) return;

        const langStr = window.ElementPackConfig?.mailchimp ?? { subscribing: 'Subscribing...' };

        mailChimpForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            window.bdtUIkit?.notification?.({ message: '<span bdt-spinner></span> ' + langStr.subscribing, timeout: false, status: 'primary' });

            try {
                const formData = new FormData(mailChimpForm);
                const actionUrl = mailChimpForm.getAttribute('action') || '';
                const res = await fetch(actionUrl || window.location.href, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.text();

                window.bdtUIkit?.notification?.closeAll?.();
                window.bdtUIkit?.notification?.({ message: data, status: 'success' });
            } catch (err) {
                window.bdtUIkit?.notification?.closeAll?.();
                window.bdtUIkit?.notification?.({ message: err?.message || 'Request failed', status: 'danger' });
            }
        });
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-mailchimp.default', widgetMailChimp);
    });
})();

/**
 * End mailchimp widget script
 */

/**
 * Start marker widget script
 */

(function () {
    'use strict';

    const isElementInViewport = (el) => {
        if (typeof el.getBoundingClientRect !== 'function') return true;
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    };

    const isElementVisible = (el) => {
        if (!el) return false;
        const rect = el.getBoundingClientRect();
        if (rect.width === 0 || rect.height === 0) return false;
        const style = window.getComputedStyle(el);
        return style.display !== 'none' && style.visibility !== 'hidden';
    };

    const widgetMarker = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;
        const marker = scopeEl.querySelector('.bdt-marker-wrapper');
        if (!marker) return;

        const tooltips = marker.querySelectorAll('.bdt-tippy-tooltip');
        const widgetID = scopeEl.dataset.id || '';

        tooltips.forEach((el) => {
            const alwaysShow = el.dataset.alwaysShow === 'true';
            const triggerType = el.dataset.tippyTrigger || 'mouseenter focus';

            const tippyOptions = {
                allowHTML: true,
                theme: 'bdt-tippy-' + widgetID,
                interactive: true,
                trigger: alwaysShow ? 'manual' : triggerType,
                showOnCreate: false,
                hideOnClick: false
            };

            const tippyInstance = tippy(el, tippyOptions);

            if (alwaysShow) {
                setTimeout(() => {
                    if (isElementInViewport(el) && isElementVisible(el)) {
                        tippyInstance.show();
                    }
                }, 500);
            }

            if (triggerType === 'click' || triggerType === 'mouseenter focus') {
                let tooltipStatus = alwaysShow;

                el.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    tooltipStatus = !tooltipStatus;
                    if (tooltipStatus) tippyInstance.show();
                    else tippyInstance.hide();
                });
            }
        });

        const alwaysShowTooltips = marker.querySelectorAll('.bdt-tippy-tooltip[data-always-show="true"]');
        if (alwaysShowTooltips.length > 0) {
            let scrollTimeout;

            const scrollHandler = () => {
                alwaysShowTooltips.forEach((el) => {
                    const instance = el._tippy;
                    if (!instance) return;

                    if (isElementInViewport(el)) {
                        if (!instance.state.isVisible) instance.show();
                    } else if (instance.state.isVisible) {
                        instance.hide();
                    }
                });
            };

            window.addEventListener('scroll', () => {
                if (scrollTimeout) clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(scrollHandler, 100);
            });

            setTimeout(scrollHandler, 500);
            setTimeout(scrollHandler, 1000);
            setTimeout(scrollHandler, 1500);
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-marker.default', widgetMarker);
    });
})();

/**
 * End marker widget script
 */

/**
 * Modal widget script
 */

(function () {
  "use strict";

  function getSettings(el) {
    const scope = el?.jquery ? el[0] : el;
    const raw = scope?.getAttribute?.("data-settings");
    if (!raw) return null;
    try {
      return JSON.parse(raw);
    } catch {
      return null;
    }
  }

  /**
   * Ensures links inside the modal body navigate: raises stacking (CSS) and, if another
   * script calls preventDefault() on the click, performs navigation in a microtask.
   */
  function bindModalBodyLinks(modalEl) {
    const body = modalEl.querySelector(".bdt-modal-body");
    if (!body || body.dataset.epModalLinksBound === "1") return;
    body.dataset.epModalLinksBound = "1";

    body.addEventListener("click", function onModalBodyLinkClick(e) {
      const anchor = e.target && e.target.closest && e.target.closest("a[href]");
      if (!anchor || !body.contains(anchor)) return;

      const hrefAttr = anchor.getAttribute("href") || "";
      if (!hrefAttr || hrefAttr.charAt(0) === "#" || /^javascript:/i.test(hrefAttr)) return;
      if (e.button !== 0) return;
      if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
      if (anchor.hasAttribute("download")) return;

      window.setTimeout(function () {
        if (!e.defaultPrevented) return;
        let proto = "";
        try {
          proto = new URL(anchor.href, window.location.href).protocol;
        } catch (err) {
          return;
        }
        if (proto !== "http:" && proto !== "https:") return;

        if (anchor.target === "_blank") {
          window.open(anchor.href, "_blank", "noopener,noreferrer");
        } else {
          window.location.assign(anchor.href);
        }
      }, 0);
    });
  }

  function widgetModal(scope) {
    const root = scope?.jquery ? scope[0] : scope;
    const modals = root?.querySelectorAll?.(".bdt-modal") || [];
    if (!modals.length) return;

    modals.forEach((modalEl) => {
      const settings = getSettings(modalEl);
      if (!settings) return;

      const modalID = settings.id;
      const displayTimes = settings.displayTimes;
      const scrollDirection = settings.scrollDirection;
      const scrollSelector = settings.scrollSelector;
      const scrollOffset = settings.scrollOffset;
      const splashInactivity = settings.splashInactivity;
      const closeBtnDelayShow = settings.closeBtnDelayShow;
      const delayTime = settings.delayTime;
      const splashDelay = settings.splashDelay;
      const widgetId = settings.widgetId;
      const layout = settings.layout;
      const editMode = Boolean(elementorFrontend?.isEditMode?.());
      let inactiveTime;

      const modal = {
        setLocalize() {
          if (document.body.classList.contains("logged-in") || editMode) {
            if (settings.cacheOnAdmin !== true) {
              localStorage.removeItem(widgetId + "_expiresIn");
              localStorage.removeItem(widgetId);
              return;
            }
          }
          this.clearLocalize();
          const hours = settings.displayTimesExpire || 4;
          const expires = hours * 60 * 60;
          const now = Date.now();
          const schedule = now + expires * 1000;

          if (localStorage.getItem(widgetId) === null) {
            localStorage.setItem(widgetId, "0");
            localStorage.setItem(widgetId + "_expiresIn", String(schedule));
          }
          const count = parseInt(localStorage.getItem(widgetId) || "0", 10) + 1;
          localStorage.setItem(widgetId, String(count));
        },

        clearLocalize() {
          const expiry = parseInt(localStorage.getItem(widgetId + "_expiresIn") || "0", 10);
          if (Date.now() >= expiry) {
            localStorage.removeItem(widgetId + "_expiresIn");
            localStorage.removeItem(widgetId);
          }
        },

        modalFire() {
          if (layout === "splash" || layout === "exit" || layout === "on_scroll") {
            modal.setLocalize();
            const firedNotify = parseInt(localStorage.getItem(widgetId) || "0", 10);
            if (displayTimes !== false && firedNotify > displayTimes) return;
          }
          if (window.bdtUIkit) bdtUIkit.modal(modalEl).show();
        },

        closeBtnDelayShow() {
          const closeBtn = modalEl.querySelector("#bdt-modal-close-button");
          if (!closeBtn) return;

          closeBtn.style.display = "none";
          modalEl.addEventListener("shown", function onShown() {
            closeBtn.style.display = "";
            closeBtn.style.opacity = "0";
            closeBtn.style.transition = `opacity ${delayTime || 300}ms`;
            requestAnimationFrame(() => { closeBtn.style.opacity = "1"; });
            modalEl.removeEventListener("shown", onShown);
          });
          modalEl.addEventListener("hide", function onHide() {
            closeBtn.style.display = "none";
            modalEl.removeEventListener("hide", onHide);
          });
        },

        customTrigger() {
          const sel = String(modalID || "").startsWith("#") ? modalID : "#" + modalID;
          const trigger = document.querySelector(sel);
          if (trigger) {
            trigger.addEventListener("click", (e) => {
              e.preventDefault();
              modal.modalFire();
            });
          }
        },

        scrollDetect(fn) {
          let lastScroll = 0;
          let ticking = false;
          window.addEventListener("scroll", () => {
            const prev = lastScroll;
            lastScroll = window.scrollY;
            if (!ticking) {
              requestAnimationFrame(() => {
                fn(lastScroll, prev);
                ticking = false;
              });
              ticking = true;
            }
          });
        },

        modalFireOnSelector() {
          if (!scrollSelector) return;
          const target = document.querySelector(scrollSelector);
          if (!target) return;
          if (target.dataset.modalSelectorInit) return;
          target.dataset.modalSelectorInit = "1";

          const firedId = widgetId + "-fired";
          const check = () => {
            const rect = target.getBoundingClientRect();
            const hT = rect.top + window.scrollY;
            const hH = target.offsetHeight;
            const wH = window.innerHeight;
            const wS = window.scrollY;

            if (wS > hT + hH - wH) {
              if (!target.classList.contains(firedId)) modal.modalFire();
              target.classList.add(firedId);
            }
          };

          window.addEventListener("scroll", check);
        },

        onScroll() {
          this.scrollDetect((scrollPos, previousScrollPos) => {
            const docHeight = document.documentElement.scrollHeight;
            const winHeight = window.innerHeight;
            const scrollTrigger = scrollOffset / 100;
            const firedId = widgetId + "-fired";

            if (previousScrollPos > scrollPos && scrollDirection === "up") {
              if (!document.body.classList.contains(firedId)) {
                modal.modalFire();
                document.body.classList.add(firedId);
              }
            } else if (previousScrollPos < scrollPos && scrollDirection === "down" && previousScrollPos !== 0) {
              if (scrollPos / (docHeight - winHeight) > scrollTrigger) {
                if (!document.body.classList.contains(firedId)) {
                  modal.modalFire();
                  document.body.classList.add(firedId);
                }
              }
            } else if (previousScrollPos < scrollPos && scrollDirection === "selector" && previousScrollPos !== 0) {
              modal.modalFireOnSelector();
            }
          });
        },

        exitPopup() {
          document.addEventListener("mouseleave", (e) => {
            if (editMode) return;
            if (
              e.clientY <= 0 ||
              e.clientX <= 0 ||
              e.clientX >= window.innerWidth ||
              e.clientY >= window.innerHeight
            ) {
              modal.modalFire();
            }
          });
        },

        resetTimer() {
          clearTimeout(inactiveTime);
          inactiveTime = setTimeout(() => modal.modalFire(), splashInactivity);
        },

        splashInactivity() {
          const handlers = () => modal.resetTimer();
          window.addEventListener("load", handlers);
          window.addEventListener("mousemove", handlers);
          window.addEventListener("mousedown", handlers);
          window.addEventListener("touchstart", handlers);
          window.addEventListener("click", handlers);
          window.addEventListener("keydown", handlers);
          window.addEventListener("scroll", handlers, true);
        },

        splashTiming() {
          setTimeout(() => modal.modalFire(), splashDelay);
        },

        splashInit() {
          if (!splashInactivity) {
            this.splashTiming();
            return;
          }
          this.splashInactivity();
        },

        defaultLayout() {
          const sel = String(modalID || "").startsWith("#") ? modalID : "#" + modalID;
          const trigger = document.querySelector(sel);
          if (trigger) {
            trigger.addEventListener("click", (e) => {
              e.preventDefault();
              modal.modalFire();
            });
          }
        },

        init() {
          if (layout === "default") modal.defaultLayout();
          if (layout === "splash") modal.splashInit();
          if (layout === "exit") modal.exitPopup();
          if (layout === "on_scroll") modal.onScroll();
          if (layout === "custom") modal.customTrigger();
          if (closeBtnDelayShow) modal.closeBtnDelayShow();
        },
      };

      modal.init();

      bindModalBodyLinks(modalEl);

      if (settings && !editMode && settings.custom_section !== false) {
        const modalContent = document.querySelector(settings.custom_section);
        const modalBody = modalEl.querySelector(".bdt-modal-body");
        if (modalContent && modalBody) {
          modalEl.classList.add("elementor", "elementor-" + (settings.pageID || ""));
          modalBody.appendChild(modalContent);
        }
      }
    });
  }

  window.addEventListener("elementor/frontend/init", () => {
    if (!window.elementorFrontend?.hooks) return;
    elementorFrontend.hooks.addAction("frontend/element_ready/bdt-modal.default", widgetModal);
  });
})();

/**
 * Start news ticker widget script
 */

(function ($) {
    "use strict";
    $.epNewsTickerOld = function (element, options) {

        var defaults = {
            effect: 'fade',
            direction: 'ltr',
            autoPlay: false,
            interval: 4000,
            scrollSpeed: 2,
            pauseOnHover: false,
            position: 'auto',
            zIndex: 99999
        }

        var ticker = this;
        ticker.settings = {};
        ticker._element = $(element);

        ticker._label = ticker._element.children(".bdt-news-ticker-label"),
            ticker._news = ticker._element.children(".bdt-news-ticker-content"),
            ticker._ul = ticker._news.children("ul"),
            ticker._li = ticker._ul.children("li"),
            ticker._controls = ticker._element.children(".bdt-news-ticker-controls"),
            ticker._prev = ticker._controls.find(".bdt-news-ticker-prev").parent(),
            ticker._action = ticker._controls.find(".bdt-news-ticker-action").parent(),
            ticker._next = ticker._controls.find(".bdt-news-ticker-next").parent();

        ticker._pause = false;
        ticker._controlsIsActive = true;
        ticker._totalNews = ticker._ul.children("li").length;
        ticker._activeNews = 0;
        ticker._interval = false;
        ticker._frameId = null;

        var setContainerWidth = function () {
            if (ticker._label.length > 0) {
                if (ticker.settings.direction == 'rtl')
                    ticker._news.css({
                        "right": ticker._label.outerWidth()
                    });
                else
                    ticker._news.css({
                        "left": ticker._label.outerWidth()
                    });
            }

            if (ticker._controls.length > 0) {
                var controlsWidth = ticker._controls.outerWidth();
                if (ticker.settings.direction == 'rtl')
                    ticker._news.css({
                        "left": controlsWidth
                    });
                else
                    ticker._news.css({
                        "right": controlsWidth
                    });
            }

            if (ticker.settings.effect === 'scroll') {
                var totalW = 0;
                ticker._li.each(function () {
                    totalW += $(this).outerWidth();
                });
                totalW += 50;
                ticker._ul.css({
                    'width': totalW
                });
            }
        }


        var startScrollAnimationLTR = function () {
            var _ulPosition = parseFloat(ticker._ul.css('marginLeft'));
            _ulPosition -= ticker.settings.scrollSpeed / 2;
            ticker._ul.css({
                'marginLeft': _ulPosition
            });

            if (_ulPosition <= -ticker._ul.find('li:first-child').outerWidth()) {
                ticker._ul.find('li:first-child').insertAfter(ticker._ul.find('li:last-child'));
                ticker._ul.css({
                    'marginLeft': 0
                });
            }
            if (ticker._pause === false) {
                ticker._frameId = requestAnimationFrame(startScrollAnimationLTR);
                (window.requestAnimationFrame && ticker._frameId) || setTimeout(startScrollAnimationLTR, 16);
            }
        }

        var startScrollAnimationRTL = function () {
            var _ulPosition = parseFloat(ticker._ul.css('marginRight'));
            _ulPosition -= ticker.settings.scrollSpeed / 2;
            ticker._ul.css({
                'marginRight': _ulPosition
            });

            if (_ulPosition <= -ticker._ul.find('li:first-child').outerWidth()) {
                ticker._ul.find('li:first-child').insertAfter(ticker._ul.find('li:last-child'));
                ticker._ul.css({
                    'marginRight': 0
                });
            }
            if (ticker._pause === false)
                ticker._frameId = requestAnimationFrame(startScrollAnimationRTL);
            (window.requestAnimationFrame && ticker._frameId) || setTimeout(startScrollAnimationRTL, 16);
        }

        var scrollPlaying = function () {
            if (ticker.settings.direction === 'rtl') {
                if (ticker._ul.width() > ticker._news.width())
                    startScrollAnimationRTL();
                else
                    ticker._ul.css({
                        'marginRight': 0
                    });
            } else
            if (ticker._ul.width() > ticker._news.width())
                startScrollAnimationLTR();
            else
                ticker._ul.css({
                    'marginLeft': 0
                });
        }

        var scrollGoNextLTR = function () {
            ticker._ul.stop().animate({
                marginLeft: -ticker._ul.find('li:first-child').outerWidth()
            }, 300, function () {
                ticker._ul.find('li:first-child').insertAfter(ticker._ul.find('li:last-child'));
                ticker._ul.css({
                    'marginLeft': 0
                });
                ticker._controlsIsActive = true;
            });
        }

        var scrollGoNextRTL = function () {
            ticker._ul.stop().animate({
                marginRight: -ticker._ul.find('li:first-child').outerWidth()
            }, 300, function () {
                ticker._ul.find('li:first-child').insertAfter(ticker._ul.find('li:last-child'));
                ticker._ul.css({
                    'marginRight': 0
                });
                ticker._controlsIsActive = true;
            });
        }

        var scrollGoPrevLTR = function () {
            var _ulPosition = parseInt(ticker._ul.css('marginLeft'), 10);
            if (_ulPosition >= 0) {
                ticker._ul.css({
                    'margin-left': -ticker._ul.find('li:last-child').outerWidth()
                });
                ticker._ul.find('li:last-child').insertBefore(ticker._ul.find('li:first-child'));
            }

            ticker._ul.stop().animate({
                marginLeft: 0
            }, 300, function () {
                ticker._controlsIsActive = true;
            });
        }

        var scrollGoPrevRTL = function () {
            var _ulPosition = parseInt(ticker._ul.css('marginRight'), 10);
            if (_ulPosition >= 0) {
                ticker._ul.css({
                    'margin-right': -ticker._ul.find('li:last-child').outerWidth()
                });
                ticker._ul.find('li:last-child').insertBefore(ticker._ul.find('li:first-child'));
            }

            ticker._ul.stop().animate({
                marginRight: 0
            }, 300, function () {
                ticker._controlsIsActive = true;
            });
        }

        var scrollNext = function () {
            if (ticker.settings.direction === 'rtl')
                scrollGoNextRTL();
            else
                scrollGoNextLTR();
        }

        var scrollPrev = function () {
            if (ticker.settings.direction === 'rtl')
                scrollGoPrevRTL();
            else
                scrollGoPrevLTR();
        }

        var effectTypography = function () {
            ticker._ul.find('li').hide();
            ticker._ul.find('li').eq(ticker._activeNews).width(30).show();
            ticker._ul.find('li').eq(ticker._activeNews).animate({
                width: '100%',
                opacity: 1
            }, 1500);
        }

        var effectFade = function () {
            ticker._ul.find('li').hide();
            ticker._ul.find('li').eq(ticker._activeNews).fadeIn();
        }

        var effectSlideDown = function () {
            if (ticker._totalNews <= 1) {
                ticker._ul.find('li').animate({
                    'top': 30,
                    'opacity': 0
                }, 300, function () {
                    $(this).css({
                        'top': -30,
                        'opacity': 0,
                        'display': 'block'
                    })
                    $(this).animate({
                        'top': 0,
                        'opacity': 1
                    }, 300);
                });
            } else {
                ticker._ul.find('li:visible').animate({
                    'top': 30,
                    'opacity': 0
                }, 300, function () {
                    $(this).hide();
                });

                ticker._ul.find('li').eq(ticker._activeNews).css({
                    'top': -30,
                    'opacity': 0
                }).show();

                ticker._ul.find('li').eq(ticker._activeNews).animate({
                    'top': 0,
                    'opacity': 1
                }, 300);
            }
        }

        var effectSlideUp = function () {
            if (ticker._totalNews <= 1) {
                ticker._ul.find('li').animate({
                    'top': -30,
                    'opacity': 0
                }, 300, function () {
                    $(this).css({
                        'top': 30,
                        'opacity': 0,
                        'display': 'block'
                    })
                    $(this).animate({
                        'top': 0,
                        'opacity': 1
                    }, 300);
                });
            } else {
                ticker._ul.find('li:visible').animate({
                    'top': -30,
                    'opacity': 0
                }, 300, function () {
                    $(this).hide();
                });

                ticker._ul.find('li').eq(ticker._activeNews).css({
                    'top': 30,
                    'opacity': 0
                }).show();

                ticker._ul.find('li').eq(ticker._activeNews).animate({
                    'top': 0,
                    'opacity': 1
                }, 300);
            }
        }

        var effectSlideRight = function () {
            if (ticker._totalNews <= 1) {
                ticker._ul.find('li').animate({
                    'left': '50%',
                    'opacity': 0
                }, 300, function () {
                    $(this).css({
                        'left': -50,
                        'opacity': 0,
                        'display': 'block'
                    })
                    $(this).animate({
                        'left': 0,
                        'opacity': 1
                    }, 300);
                });
            } else {
                ticker._ul.find('li:visible').animate({
                    'left': '50%',
                    'opacity': 0
                }, 300, function () {
                    $(this).hide();
                });

                ticker._ul.find('li').eq(ticker._activeNews).css({
                    'left': -50,
                    'opacity': 0
                }).show();

                ticker._ul.find('li').eq(ticker._activeNews).animate({
                    'left': 0,
                    'opacity': 1
                }, 300);
            }
        }

        var effectSlideLeft = function () {
            if (ticker._totalNews <= 1) {
                ticker._ul.find('li').animate({
                    'left': '-50%',
                    'opacity': 0
                }, 300, function () {
                    $(this).css({
                        'left': '50%',
                        'opacity': 0,
                        'display': 'block'
                    })
                    $(this).animate({
                        'left': 0,
                        'opacity': 1
                    }, 300);
                });
            } else {
                ticker._ul.find('li:visible').animate({
                    'left': '-50%',
                    'opacity': 0
                }, 300, function () {
                    $(this).hide();
                });

                ticker._ul.find('li').eq(ticker._activeNews).css({
                    'left': '50%',
                    'opacity': 0
                }).show();

                ticker._ul.find('li').eq(ticker._activeNews).animate({
                    'left': 0,
                    'opacity': 1
                }, 300);
            }
        }


        var showThis = function () {
            ticker._controlsIsActive = true;

            switch (ticker.settings.effect) {
                case 'typography':
                    effectTypography();
                    break;
                case 'fade':
                    effectFade();
                    break;
                case 'slide-down':
                    effectSlideDown();
                    break;
                case 'slide-up':
                    effectSlideUp();
                    break;
                case 'slide-right':
                    effectSlideRight();
                    break;
                case 'slide-left':
                    effectSlideLeft();
                    break;
                default:
                    ticker._ul.find('li').hide();
                    ticker._ul.find('li').eq(ticker._activeNews).show();
            }

        }

        var nextHandler = function () {
            switch (ticker.settings.effect) {
                case 'scroll':
                    scrollNext();
                    break;
                default:
                    ticker._activeNews++;
                    if (ticker._activeNews >= ticker._totalNews)
                        ticker._activeNews = 0;

                    showThis();

            }
        }

        var prevHandler = function () {
            switch (ticker.settings.effect) {
                case 'scroll':
                    scrollPrev();
                    break;
                default:
                    ticker._activeNews--;
                    if (ticker._activeNews < 0)
                        ticker._activeNews = ticker._totalNews - 1;

                    showThis();
            }
        }

        var playHandler = function () {
            ticker._pause = false;
            if (ticker.settings.autoPlay) {
                switch (ticker.settings.effect) {
                    case 'scroll':
                        scrollPlaying();
                        break;
                    default:
                        ticker.pause();
                        ticker._interval = setInterval(function () {
                            ticker.next();
                        }, ticker.settings.interval);
                }
            }
        }

        var resizeEvent = function () {
            if (ticker._element.width() < 480) {
                ticker._label.hide();
                if (ticker.settings.direction == 'rtl')
                    ticker._news.css({
                        "right": 0
                    });
                else
                    ticker._news.css({
                        "left": 0
                    });
            } else {
                ticker._label.show();
                if (ticker.settings.direction == 'rtl')
                    ticker._news.css({
                        "right": ticker._label.outerWidth()
                    });
                else
                    ticker._news.css({
                        "left": ticker._label.outerWidth()
                    });
            }
        }

        ticker.init = function () {
            ticker.settings = $.extend({}, defaults, options);

            ticker._element.addClass('bdt-effect-' + ticker.settings.effect + ' bdt-direction-' + ticker.settings.direction);

            setContainerWidth();

            if (ticker.settings.effect != 'scroll')
                showThis();

            playHandler();

            if (!ticker.settings.autoPlay)
                ticker._action.find('span').removeClass('bdt-news-ticker-pause').addClass('bdt-news-ticker-play');
            else
                ticker._action.find('span').removeClass('bdt-news-ticker-play').addClass('bdt-news-ticker-pause');


            ticker._element.on('mouseleave', function (e) {
                var activePosition = $(document.elementFromPoint(e.clientX, e.clientY)).parents('.bdt-breaking-news')[0];
                if ($(this)[0] === activePosition) {
                    return;
                }


                if (ticker.settings.pauseOnHover === true) {
                    if (ticker.settings.autoPlay === true)
                        ticker.play();
                } else {
                    if (ticker.settings.autoPlay === true && ticker._pause === true)
                        ticker.play();
                }

            });

            ticker._element.on('mouseenter', function () {
                if (ticker.settings.pauseOnHover === true)
                    ticker.pause();
            });

            ticker._next.on('click', function () {
                if (ticker._controlsIsActive) {
                    ticker._controlsIsActive = false;
                    ticker.pause();
                    ticker.next();
                }
            });

            ticker._prev.on('click', function () {
                if (ticker._controlsIsActive) {
                    ticker._controlsIsActive = false;
                    ticker.pause();
                    ticker.prev();
                }
            });

            ticker._action.on('click', function () {
                if (ticker._controlsIsActive) {
                    if (ticker._action.find('span').hasClass('bdt-news-ticker-pause')) {
                        ticker._action.find('span').removeClass('bdt-news-ticker-pause').addClass('bdt-news-ticker-play');
                        ticker.stop();
                    } else {
                        ticker.settings.autoPlay = true;
                        ticker._action.find('span').removeClass('bdt-news-ticker-play').addClass('bdt-news-ticker-pause');
                    }
                }
            });

            resizeEvent();

            $(window).on('resize', function () {
                resizeEvent();
                ticker.pause();
                ticker.play();
            });

        }

        ticker.pause = function () {
            ticker._pause = true;
            clearInterval(ticker._interval);
            cancelAnimationFrame(ticker._frameId);
        }

        ticker.stop = function () {
            ticker._pause = true;
            ticker.settings.autoPlay = false;
        }

        ticker.play = function () {
            playHandler();
        }

        ticker.next = function () {
            nextHandler();
        }

        ticker.prev = function () {
            prevHandler();
        }

        ticker.init();

    }

    $.fn.epNewsTickerOld = function (options) {

        return this.each(function () {
            if (undefined == $(this).data('epNewsTickerOld')) {
                var ticker = new $.epNewsTickerOld(this, options);
                $(this).data('epNewsTickerOld', ticker);
            }
        });

    }

})(jQuery);



(function ($, elementor) {

    'use strict';

    var widgetNewsTicker = function ($scope, $) {

        var $newsTicker = $scope.find('.bdt-news-ticker'),
            $settings = $newsTicker.data('settings');

        if (!$newsTicker.length) {
            return;
        }

        $($newsTicker).epNewsTickerOld($settings);

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-news-ticker.default', widgetNewsTicker);
    });

}(jQuery, window.elementorFrontend));

/**
 * End news ticker widget script
 */

;
(function ($, elementor) {
    'use strict';

    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            Notation;

        Notation = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    type: 'underline',
                    multiline: true
                };
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_notation_') !== -1) {
                    this.run();
                }
            }, 400),

            isEditMode: function () {
                if (window.elementorFrontend && elementorFrontend.isEditMode && elementorFrontend.isEditMode()) {
                    return true;
                }

                return $('body').hasClass('elementor-editor-active');
            },

            isAtomicWidget: function () {
                var atomicSettings = this.getAtomicSettings();

                return !!(atomicSettings && atomicSettings.active === 'yes' && Array.isArray(atomicSettings.list));
            },

            settings: function (key) {
                return this.getElementSettings('ep_notation_' + key);
            },

            getSliderSize: function (value, fallback) {
                if (value && typeof value === 'object' && typeof value.size !== 'undefined') {
                    return value.size;
                }

                if (typeof value === 'number') {
                    return value;
                }

                return fallback;
            },

            cleanupNotations: function () {
                this.$element.find('[data-notation]').each(function () {
                    $(this).removeAttr('data-notation').siblings('.rough-annotation').remove();
                });
                this.$element.find('.rough-annotation').remove();
            },

            getWidgetTargetElement: function () {
                var $scope = this.$element;

                if (!this.isAtomicWidget()) {
                    var $direct = this.findElement(' > :not(style)');

                    if (this.isEditMode() && $direct.length > 1) {
                        return $direct.get(1);
                    }

                    return $direct.get(0) || $scope.get(0);
                }

                var $target = $scope.find('.e-heading-base, .e-paragraph-base, .elementor-heading-title, .bdt-heading-tag, .bdt-ep-advanced-heading-main-title-inner').first();

                if ($target.length) {
                    return $target.get(0);
                }

                $target = $scope.find('h1, h2, h3, h4, h5, h6, p').filter(function () {
                    return $.trim($(this).text()).length > 0;
                }).first();

                if ($target.length) {
                    return $target.get(0);
                }

                var $atomicDirect = this.findElement(' > :not(style)');

                if ($atomicDirect.length > 1) {
                    var $container = $atomicDirect.filter('.elementor-widget-container').first();

                    if ($container.length) {
                        var $child = $container.children(':not(style)').first();

                        if ($child.length) {
                            return $child.get(0);
                        }
                    }

                    return $atomicDirect.last().get(0);
                }

                return $atomicDirect.get(0) || $scope.get(0);
            },

            getAtomicSettings: function () {
                var element = this.$element.get(0);

                if (!element) {
                    return null;
                }

                var dataNode = element.matches('[data-ep-notation]')
                    ? element
                    : element.querySelector('[data-ep-notation]');

                if (!dataNode) {
                    return null;
                }

                var payload = dataNode.getAttribute('data-ep-notation');

                if (!payload) {
                    return null;
                }

                try {
                    var parsed = JSON.parse(payload);

                    if (!parsed || parsed.active !== 'yes' || !Array.isArray(parsed.list) || !parsed.list.length) {
                        return null;
                    }

                    return parsed;
                } catch (error) {
                    return null;
                }
            },

            getSettingValue: function (key) {
                if (!this.isAtomicWidget()) {
                    return this.settings(key);
                }

                var atomicSettings = this.getAtomicSettings();

                if (atomicSettings && typeof atomicSettings[key] !== 'undefined') {
                    return atomicSettings[key];
                }

                return this.settings(key);
            },

            run: function () {
                if (this.getSettingValue('active') != 'yes') {
                    this.cleanupNotations();
                    return;
                }

                this.cleanupNotations();

                var
                    $element = this.$element,
                    $widgetId = 'ep-' + this.getID(),
                    $elementID = this.getID(),
                    $globalthis = this;

                var $list = this.getSettingValue('list');

                if (!$list || !Array.isArray($list)) {
                    return;
                }

                var rtl = ($("body").hasClass("rtl")) ? true : false;
                var inEditor = this.isEditMode();


                $list.forEach(element => {
                    var
                        $selectElement = '',
                        bracketOn = '',
                        options = this.getDefaultSettings();

                    if (element.ep_notation_select_type == 'widget') {
                        var targetEl = $globalthis.getWidgetTargetElement();

                        if (targetEl) {
                            $(targetEl).attr('data-notation', $widgetId);
                        }
                        $selectElement = '[data-notation="' + $widgetId + '"]';
                    }
                    if (element.ep_notation_select_type == 'custom') {
                        var customSelector = element.ep_notation_custom_selector;


                        if (element.ep_notation_custom_selector && customSelector.length > 1) {
                            $selectElement = '[data-id="' + $elementID + '"] ' + ' ' + customSelector;
                        } else {
                            $selectElement = '.-bdt-empty';
                        }

                    }

                    if (element.ep_notation_type == 'bracket') {
                        bracketOn = element.ep_notation_bracket_on || 'left,right';
                        bracketOn = typeof bracketOn === 'string' ? bracketOn.split(',') : ['left', 'right'];
                        options.brackets = bracketOn;
                    }

                    if ($selectElement.length > 0) {

                        var n1 = null;

                        if ($selectElement.indexOf('[data-notation=') === 0) {
                            n1 = $element[0].querySelector($selectElement);
                        } else if (element.ep_notation_select_type === 'custom' && element.ep_notation_custom_selector) {
                            n1 = $element[0].querySelector(element.ep_notation_custom_selector) ||
                                document.querySelector($selectElement);
                        } else {
                            n1 = document.querySelector($selectElement);
                        }

                        if (!n1) {
                            return;
                        }

                        options.type = element.ep_notation_type;
                        options.color = element.ep_notation_color || '#f23427';
                        options.animationDuration = $globalthis.getSliderSize(element.ep_notation_anim_duration, 800);
                        options.strokeWidth = $globalthis.getSliderSize(element.ep_notation_stroke_width, 1);
                        options.rtl = rtl;
                        options.infinityLoop = element.ep_notation_infinity_loop === 'yes' && !inEditor;
                        options.loopDelay = $globalthis.getSliderSize(element.ep_notation_loop_delay, 2000);



                        if (n1) {
                            // Remove any existing rough-annotation SVGs to prevent duplication
                            $(n1).siblings('.rough-annotation').remove();

                            var renderNotation = function () {

                                const t = "http://www.w3.org/2000/svg";
                                class e {
                                    constructor(t) {
                                        this.seed = t;
                                    }
                                    next() {
                                        return this.seed ? (2 ** 31 - 1 & (this.seed = Math.imul(48271, this.seed))) / 2 ** 31 : Math.random();
                                    }
                                }

                                function s(t, e, s, i, n) {
                                    return {
                                        type: "path",
                                        ops: c(t, e, s, i, n)
                                    };
                                }

                                function i(t, e, i) {
                                    const n = (t || []).length;
                                    if (n > 2) {
                                        const s = [];
                                        for (let e = 0; e < n - 1; e++) s.push(...c(t[e][0], t[e][1], t[e + 1][0], t[e + 1][1], i));
                                        return e && s.push(...c(t[n - 1][0], t[n - 1][1], t[0][0], t[0][1], i)), {
                                            type: "path",
                                            ops: s
                                        };
                                    }
                                    return 2 === n ? s(t[0][0], t[0][1], t[1][0], t[1][1], i) : {
                                        type: "path",
                                        ops: []
                                    };
                                }

                                function n(t, e, s, n, o) {
                                    return function (t, e) {
                                        return i(t, !0, e);
                                    }([
                                        [t, e],
                                        [t + s, e],
                                        [t + s, e + n],
                                        [t, e + n]
                                    ], o);
                                }

                                function o(t, e, s, i, n) {
                                    return function (t, e, s, i) {
                                        const [n, o] = l(i.increment, t, e, i.rx, i.ry, 1, i.increment * h(.1, h(.4, 1, s), s), s);
                                        let r = f(n, null, s);
                                        if (!s.disableMultiStroke) {
                                            const [n] = l(i.increment, t, e, i.rx, i.ry, 1.5, 0, s), o = f(n, null, s);
                                            r = r.concat(o);
                                        }
                                        return {
                                            estimatedPoints: o,
                                            opset: {
                                                type: "path",
                                                ops: r
                                            }
                                        };
                                    }(t, e, n, function (t, e, s) {
                                        const i = Math.sqrt(2 * Math.PI * Math.sqrt((Math.pow(t / 2, 2) + Math.pow(e / 2, 2)) / 2)),
                                            n = Math.max(s.curveStepCount, s.curveStepCount / Math.sqrt(200) * i),
                                            o = 2 * Math.PI / n;
                                        let r = Math.abs(t / 2),
                                            h = Math.abs(e / 2);
                                        const c = 1 - s.curveFitting;
                                        return r += a(r * c, s), h += a(h * c, s), {
                                            increment: o,
                                            rx: r,
                                            ry: h
                                        };
                                    }(s, i, n)).opset;
                                }

                                function r(t) {
                                    return t.randomizer || (t.randomizer = new e(t.seed || 0)), t.randomizer.next();
                                }

                                function h(t, e, s, i = 1) {
                                    return s.roughness * i * (r(s) * (e - t) + t);
                                }

                                function a(t, e, s = 1) {
                                    return h(-t, t, e, s);
                                }

                                function c(t, e, s, i, n, o = !1) {
                                    const r = o ? n.disableMultiStrokeFill : n.disableMultiStroke,
                                        h = u(t, e, s, i, n, !0, !1);
                                    if (r) return h;
                                    const a = u(t, e, s, i, n, !0, !0);
                                    return h.concat(a);
                                }

                                function u(t, e, s, i, n, o, h) {
                                    const c = Math.pow(t - s, 2) + Math.pow(e - i, 2),
                                        u = Math.sqrt(c);
                                    let f = 1;
                                    f = u < 200 ? 1 : u > 500 ? .4 : -.0016668 * u + 1.233334;
                                    let l = n.maxRandomnessOffset || 0;
                                    l * l * 100 > c && (l = u / 10);
                                    const g = l / 2,
                                        d = .2 + .2 * r(n);
                                    let p = n.bowing * n.maxRandomnessOffset * (i - e) / 200,
                                        _ = n.bowing * n.maxRandomnessOffset * (t - s) / 200;
                                    p = a(p, n, f), _ = a(_, n, f);
                                    const m = [],
                                        w = () => a(g, n, f),
                                        v = () => a(l, n, f);
                                    return o && (h ? m.push({
                                        op: "move",
                                        data: [t + w(), e + w()]
                                    }) : m.push({
                                        op: "move",
                                        data: [t + a(l, n, f), e + a(l, n, f)]
                                    })), h ? m.push({
                                        op: "bcurveTo",
                                        data: [p + t + (s - t) * d + w(), _ + e + (i - e) * d + w(), p + t + 2 * (s - t) * d + w(), _ + e + 2 * (i - e) * d + w(), s + w(), i + w()]
                                    }) : m.push({
                                        op: "bcurveTo",
                                        data: [p + t + (s - t) * d + v(), _ + e + (i - e) * d + v(), p + t + 2 * (s - t) * d + v(), _ + e + 2 * (i - e) * d + v(), s + v(), i + v()]
                                    }), m;
                                }

                                function f(t, e, s) {
                                    const i = t.length,
                                        n = [];
                                    if (i > 3) {
                                        const o = [],
                                            r = 1 - s.curveTightness;
                                        n.push({
                                            op: "move",
                                            data: [t[1][0], t[1][1]]
                                        });
                                        for (let e = 1; e + 2 < i; e++) {
                                            const s = t[e];
                                            o[0] = [s[0], s[1]], o[1] = [s[0] + (r * t[e + 1][0] - r * t[e - 1][0]) / 6, s[1] + (r * t[e + 1][1] - r * t[e - 1][1]) / 6], o[2] = [t[e + 1][0] + (r * t[e][0] - r * t[e + 2][0]) / 6, t[e + 1][1] + (r * t[e][1] - r * t[e + 2][1]) / 6], o[3] = [t[e + 1][0], t[e + 1][1]], n.push({
                                                op: "bcurveTo",
                                                data: [o[1][0], o[1][1], o[2][0], o[2][1], o[3][0], o[3][1]]
                                            });
                                        }
                                        if (e && 2 === e.length) {
                                            const t = s.maxRandomnessOffset;
                                            n.push({
                                                op: "lineTo",
                                                data: [e[0] + a(t, s), e[1] + a(t, s)]
                                            });
                                        }
                                    } else 3 === i ? (n.push({
                                        op: "move",
                                        data: [t[1][0], t[1][1]]
                                    }), n.push({
                                        op: "bcurveTo",
                                        data: [t[1][0], t[1][1], t[2][0], t[2][1], t[2][0], t[2][1]]
                                    })) : 2 === i && n.push(...c(t[0][0], t[0][1], t[1][0], t[1][1], s));
                                    return n;
                                }

                                function l(t, e, s, i, n, o, r, h) {
                                    const c = [],
                                        u = [],
                                        f = a(.5, h) - Math.PI / 2;
                                    u.push([a(o, h) + e + .9 * i * Math.cos(f - t), a(o, h) + s + .9 * n * Math.sin(f - t)]);
                                    for (let r = f; r < 2 * Math.PI + f - .01; r += t) {
                                        const t = [a(o, h) + e + i * Math.cos(r), a(o, h) + s + n * Math.sin(r)];
                                        c.push(t), u.push(t);
                                    }
                                    return u.push([a(o, h) + e + i * Math.cos(f + 2 * Math.PI + .5 * r), a(o, h) + s + n * Math.sin(f + 2 * Math.PI + .5 * r)]), u.push([a(o, h) + e + .98 * i * Math.cos(f + r), a(o, h) + s + .98 * n * Math.sin(f + r)]), u.push([a(o, h) + e + .9 * i * Math.cos(f + .5 * r), a(o, h) + s + .9 * n * Math.sin(f + .5 * r)]), [u, c];
                                }

                                function g(t, e) {
                                    return {
                                        maxRandomnessOffset: 2,
                                        roughness: "highlight" === t ? 3 : 1.5,
                                        bowing: 1,
                                        stroke: "#000",
                                        strokeWidth: 1.5,
                                        curveTightness: 0,
                                        curveFitting: .95,
                                        curveStepCount: 9,
                                        fillStyle: "hachure",
                                        fillWeight: -1,
                                        hachureAngle: -41,
                                        hachureGap: -1,
                                        dashOffset: -1,
                                        dashGap: -1,
                                        zigzagOffset: -1,
                                        combineNestedSvgPaths: !1,
                                        disableMultiStroke: "double" !== t,
                                        disableMultiStrokeFill: !1,
                                        seed: e
                                    };
                                }

                                function d(e, r, h, a, c, u) {
                                    const f = [];
                                    let l = h.strokeWidth || 2;
                                    const d = function (t) {
                                        const e = t.padding;
                                        if (e || 0 === e) {
                                            if ("number" == typeof e) return [e, e, e, e];
                                            if (Array.isArray(e)) {
                                                const t = e;
                                                if (t.length) switch (t.length) {
                                                    case 4:
                                                        return [...t];
                                                    case 1:
                                                        return [t[0], t[0], t[0], t[0]];
                                                    case 2:
                                                        return [...t, ...t];
                                                    case 3:
                                                        return [...t, t[1]];
                                                    default:
                                                        return [t[0], t[1], t[2], t[3]];
                                                }
                                            }
                                        }
                                        return [5, 5, 5, 5];
                                    }(h),
                                        p = void 0 === h.animate || !!h.animate,
                                        _ = h.iterations || 1,
                                        m = h.rtl ? 1 : 0,
                                        w = g("single", u);
                                    switch (h.type) {
                                        case "underline": {
                                            const t = r.y + r.h + d[2];
                                            for (let e = m; e < _ + m; e++) e % 2 ? f.push(s(r.x + r.w, t, r.x, t, w)) : f.push(s(r.x, t, r.x + r.w, t, w));
                                            break;
                                        }
                                        case "strike-through": {
                                            const t = r.y + r.h / 2;
                                            for (let e = m; e < _ + m; e++) e % 2 ? f.push(s(r.x + r.w, t, r.x, t, w)) : f.push(s(r.x, t, r.x + r.w, t, w));
                                            break;
                                        }
                                        case "box": {
                                            const t = r.x - d[3],
                                                e = r.y - d[0],
                                                s = r.w + (d[1] + d[3]),
                                                i = r.h + (d[0] + d[2]);
                                            for (let o = 0; o < _; o++) f.push(n(t, e, s, i, w));
                                            break;
                                        }
                                        case "bracket": {
                                            const t = Array.isArray(h.brackets) ? h.brackets : h.brackets ? [h.brackets] : ["right"],
                                                e = r.x - 2 * d[3],
                                                s = r.x + r.w + 2 * d[1],
                                                n = r.y - 2 * d[0],
                                                o = r.y + r.h + 2 * d[2];
                                            for (const h of t) {
                                                let t;
                                                switch (h) {
                                                    case "bottom":
                                                        t = [
                                                            [e, r.y + r.h],
                                                            [e, o],
                                                            [s, o],
                                                            [s, r.y + r.h]
                                                        ];
                                                        break;
                                                    case "top":
                                                        t = [
                                                            [e, r.y],
                                                            [e, n],
                                                            [s, n],
                                                            [s, r.y]
                                                        ];
                                                        break;
                                                    case "left":
                                                        t = [
                                                            [r.x, n],
                                                            [e, n],
                                                            [e, o],
                                                            [r.x, o]
                                                        ];
                                                        break;
                                                    case "right":
                                                        t = [
                                                            [r.x + r.w, n],
                                                            [s, n],
                                                            [s, o],
                                                            [r.x + r.w, o]
                                                        ];
                                                }
                                                t && f.push(i(t, !1, w));
                                            }
                                            break;
                                        }
                                        case "crossed-off": {
                                            const t = r.x,
                                                e = r.y,
                                                i = t + r.w,
                                                n = e + r.h;
                                            for (let o = m; o < _ + m; o++) o % 2 ? f.push(s(i, n, t, e, w)) : f.push(s(t, e, i, n, w));
                                            for (let o = m; o < _ + m; o++) o % 2 ? f.push(s(t, n, i, e, w)) : f.push(s(i, e, t, n, w));
                                            break;
                                        }
                                        case "circle": {
                                            const t = g("double", u),
                                                e = r.w + (d[1] + d[3]),
                                                s = r.h + (d[0] + d[2]),
                                                i = r.x - d[3] + e / 2,
                                                n = r.y - d[0] + s / 2,
                                                h = Math.floor(_ / 2),
                                                a = _ - 2 * h;
                                            for (let r = 0; r < h; r++) f.push(o(i, n, e, s, t));
                                            for (let t = 0; t < a; t++) f.push(o(i, n, e, s, w));
                                            break;
                                        }
                                        case "highlight": {
                                            const t = g("highlight", u);
                                            l = .95 * r.h;
                                            const e = r.y + r.h / 2;
                                            for (let i = m; i < _ + m; i++) i % 2 ? f.push(s(r.x + r.w, e, r.x, e, t)) : f.push(s(r.x, e, r.x + r.w, e, t));
                                            break;
                                        }
                                        case "soft-wave": {
                                            const t = r.y + r.h + d[2];
                                            for (let e = m; e < _ + m; e++) {
                                                const points = [];
                                                for (let i = 0; i <= r.w; i += 4) {
                                                    const x = r.x + i;
                                                    // Create soft, gentle wave with multiple sine waves for natural flow
                                                    const y = t + Math.sin(i * 0.06) * 8 + Math.cos(i * 0.04) * 4 + Math.sin(i * 0.02) * 2;
                                                    points.push([x, y]);
                                                }
                                                f.push(i(points, false, w));
                                            }
                                            break;
                                        }
                                    }
                                    if (f.length) {
                                        const s = function (t) {
                                            const e = [];
                                            for (const s of t) {
                                                let t = "";
                                                for (const i of s.ops) {
                                                    const s = i.data;
                                                    switch (i.op) {
                                                        case "move":
                                                            t.trim() && e.push(t.trim()), t = `M${s[0]} ${s[1]} `;
                                                            break;
                                                        case "bcurveTo":
                                                            t += `C${s[0]} ${s[1]}, ${s[2]} ${s[3]}, ${s[4]} ${s[5]} `;
                                                            break;
                                                        case "lineTo":
                                                            t += `L${s[0]} ${s[1]} `;
                                                    }
                                                }
                                                t.trim() && e.push(t.trim());
                                            }
                                            return e;
                                        }(f),
                                            i = [],
                                            n = [];
                                        let o = 0;
                                        const r = (t, e, s) => t.setAttribute(e, s);
                                        for (const a of s) {
                                            const s = document.createElementNS(t, "path");
                                            if (r(s, "d", a), r(s, "fill", "none"), r(s, "stroke", h.color || "currentColor"), r(s, "stroke-width", "" + l), p) {
                                                const t = s.getTotalLength();
                                                i.push(t), o += t;
                                            }
                                            e.appendChild(s), n.push(s);
                                        }
                                        if (p) {
                                            let t = 0;
                                            for (let e = 0; e < n.length; e++) {
                                                const s = n[e],
                                                    r = i[e],
                                                    h = o ? c * (r / o) : 0,
                                                    u = a + t,
                                                    f = s.style;
                                                f.strokeDashoffset = "" + r, f.strokeDasharray = "" + r, f.animation = `rough-notation-dash ${h}ms ease-out ${u}ms forwards`, t += h;
                                            }
                                        }
                                    }
                                }
                                class p {
                                    constructor(t, e) {
                                        this._state = "unattached", this._resizing = !1, this._seed = Math.floor(Math.random() * 2 ** 31), this._lastSizes = [], this._animationDelay = 0, this._loopTimeout = null, this._resizeListener = () => {
                                            this._resizing || (this._resizing = !0, setTimeout(() => {
                                                this._resizing = !1, "showing" === this._state && this.haveRectsChanged() && this.show();
                                            }, 400));
                                        }, this._e = t, this._config = JSON.parse(JSON.stringify(e)), this.attach();
                                    }
                                    get animate() {
                                        return this._config.animate;
                                    }
                                    set animate(t) {
                                        this._config.animate = t;
                                    }
                                    get animationDuration() {
                                        return this._config.animationDuration;
                                    }
                                    set animationDuration(t) {
                                        this._config.animationDuration = t;
                                    }
                                    get iterations() {
                                        return this._config.iterations;
                                    }
                                    set iterations(t) {
                                        this._config.iterations = t;
                                    }
                                    get color() {
                                        return this._config.color;
                                    }
                                    set color(t) {
                                        this._config.color !== t && (this._config.color = t, this.refresh());
                                    }
                                    get strokeWidth() {
                                        return this._config.strokeWidth;
                                    }
                                    set strokeWidth(t) {
                                        this._config.strokeWidth !== t && (this._config.strokeWidth = t, this.refresh());
                                    }
                                    get padding() {
                                        return this._config.padding;
                                    }
                                    set padding(t) {
                                        this._config.padding !== t && (this._config.padding = t, this.refresh());
                                    }
                                    attach() {
                                        if ("unattached" === this._state && this._e.parentElement) {
                                            // Remove any existing rough-annotation SVGs to prevent duplication
                                            const existingSvg = this._e.parentElement.querySelector('.rough-annotation');
                                            if (existingSvg) {
                                                existingSvg.remove();
                                            }
                                            
                                            ! function () {
                                                if (!window.__rno_kf_s) {
                                                    const t = window.__rno_kf_s = document.createElement("style");
                                                    t.textContent = "@keyframes rough-notation-dash { to { stroke-dashoffset: 0; } }", document.head.appendChild(t);
                                                }
                                            }();
                                            const e = this._svg = document.createElementNS(t, "svg");
                                            e.setAttribute("class", "rough-annotation");
                                            const s = e.style;
                                            s.position = "absolute", s.top = "0", s.left = "0", s.overflow = "visible", s.pointerEvents = "none", s.width = "100px", s.height = "100px";
                                            const i = "highlight" === this._config.type;
                                            if (this._e.insertAdjacentElement(i ? "beforebegin" : "afterend", e), this._state = "not-showing", i) {
                                                const t = window.getComputedStyle(this._e).position;
                                                (!t || "static" === t) && (this._e.style.position = "relative");
                                            }
                                            this.attachListeners();
                                        }
                                    }
                                    detachListeners() {
                                        window.removeEventListener("resize", this._resizeListener), this._ro && this._ro.unobserve(this._e);
                                    }
                                    attachListeners() {
                                        this.detachListeners(), window.addEventListener("resize", this._resizeListener, {
                                            passive: !0
                                        }), !this._ro && "ResizeObserver" in window && (this._ro = new window.ResizeObserver(t => {
                                            for (const e of t) e.contentRect && this._resizeListener();
                                        })), this._ro && this._ro.observe(this._e);
                                    }
                                    haveRectsChanged() {
                                        if (this._lastSizes.length) {
                                            const t = this.rects();
                                            if (t.length !== this._lastSizes.length) return !0;
                                            for (let e = 0; e < t.length; e++)
                                                if (!this.isSameRect(t[e], this._lastSizes[e])) return !0;
                                        }
                                        return !1;
                                    }
                                    isSameRect(t, e) {
                                        const s = (t, e) => Math.round(t) === Math.round(e);
                                        return s(t.x, e.x) && s(t.y, e.y) && s(t.w, e.w) && s(t.h, e.h);
                                    }
                                    isShowing() {
                                        return "not-showing" !== this._state;
                                    }
                                    refresh() {
                                        this.isShowing() && !this.pendingRefresh && (this.pendingRefresh = Promise.resolve().then(() => {
                                            this.isShowing() && this.show(), delete this.pendingRefresh;
                                        }));
                                    }
                                    show() {
                                        // Clear any existing loop timeout
                                        if (this._loopTimeout) {
                                            clearTimeout(this._loopTimeout);
                                            this._loopTimeout = null;
                                        }
                                        
                                        switch (this._state) {
                                            case "unattached":
                                                break;
                                            case "showing":
                                                this.hide(), this._svg && this.render(this._svg, !0);
                                                break;
                                            case "not-showing":
                                                this.attach(), this._svg && this.render(this._svg, !1);
                                        }
                                        
                                        // Handle infinity loop
                                        if (this._config.infinityLoop && this._config.loopDelay) {
                                            this._loopTimeout = setTimeout(() => {
                                                this.hide();
                                                setTimeout(() => {
                                                    this.show();
                                                }, 100);
                                            }, this._config.animationDuration + this._config.loopDelay);
                                        }
                                    }
                                    hide() {
                                        // Clear loop timeout when hiding
                                        if (this._loopTimeout) {
                                            clearTimeout(this._loopTimeout);
                                            this._loopTimeout = null;
                                        }
                                        
                                        if (this._svg)
                                            for (; this._svg.lastChild;) this._svg.removeChild(this._svg.lastChild);
                                        this._state = "not-showing";
                                    }
                                    remove() {
                                        this._svg && this._svg.parentElement && this._svg.parentElement.removeChild(this._svg), this._svg = void 0, this._state = "unattached", this.detachListeners();
                                    }
                                    render(t, e) {
                                        let s = this._config;
                                        e && (s = JSON.parse(JSON.stringify(this._config)), s.animate = !1);
                                        const i = this.rects();
                                        let n = 0;
                                        i.forEach(t => n += t.w);
                                        const o = s.animationDuration || 800;
                                        let r = 0;
                                        for (let e = 0; e < i.length; e++) {
                                            const h = o * (i[e].w / n);
                                            d(t, i[e], s, r + this._animationDelay, h, this._seed), r += h;
                                        }
                                        this._lastSizes = i, this._state = "showing";
                                    }
                                    rects() {
                                        const t = [];
                                        if (this._svg)
                                            if (this._config.multiline) {
                                                const e = this._e.getClientRects();
                                                for (let s = 0; s < e.length; s++) t.push(this.svgRect(this._svg, e[s]));
                                            } else t.push(this.svgRect(this._svg, this._e.getBoundingClientRect()));
                                        return t;
                                    }
                                    svgRect(t, e) {
                                        const s = t.getBoundingClientRect(),
                                            i = e;
                                        return {
                                            x: (i.x || i.left) - (s.x || s.left),
                                            y: (i.y || i.top) - (s.y || s.top),
                                            w: i.width,
                                            h: i.height
                                        };
                                    }
                                }

                                function _(t, e) {
                                    return new p(t, e);
                                }

                                function m(t) {
                                    let e = 0;
                                    for (const s of t) {
                                        const t = s;
                                        t._animationDelay = e;
                                        e += 0 === t.animationDuration ? 0 : t.animationDuration || 800;
                                    }
                                    const s = [...t];
                                    return {
                                        show() {
                                            for (const t of s) t.show();
                                        },
                                        hide() {
                                            for (const t of s) t.hide();
                                        }
                                    };
                                }

                                var a1 = _(n1, options);
                                a1.show();
                            };

                            if (inEditor) {
                                renderNotation();
                            } else if (typeof epObserveTarget === 'function') {
                                epObserveTarget($element[0], renderNotation);
                            } else {
                                renderNotation();
                            }
                        }

                    }

                });


            }

        });

        function epAddNotationHandler($scope) {
            elementorFrontend.elementsHandler.addHandler(Notation, {
                $element: $scope
            });
        }

        var notationFallbackTimer = null;

        function runAtomicNotationFallback() {
            if (notationFallbackTimer) {
                window.clearTimeout(notationFallbackTimer);
            }

            notationFallbackTimer = window.setTimeout(function () {
                notationFallbackTimer = null;

                $('.elementor-element[data-id]').each(function () {
                    var $scope = $(this);

                    if ($scope.is('[data-ep-notation]') || $scope.find('[data-ep-notation]').length) {
                        epAddNotationHandler($scope);
                    }
                });
            }, 150);
        }

        window.epRunAtomicNotationFallback = runAtomicNotationFallback;

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            epAddNotationHandler($scope);
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/e-heading.default', function ($scope) {
            epAddNotationHandler($scope);
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/e-paragraph.default', function ($scope) {
            epAddNotationHandler($scope);
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
            var $root = $($scope);

            if (!$root.length) {
                return;
            }

            if (!$root.is('[data-ep-notation]') && !$root.find('[data-ep-notation]').length) {
                return;
            }

            epAddNotationHandler($root);
        });

    });

}(jQuery, window.elementorFrontend));

/**
 * Start notification widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetNotification = function ($scope, $) {

        var $avdNotification = $scope.find('.bdt-notification-wrapper'),
            $settings = $avdNotification.data('settings');

        if (!$avdNotification.length) {
            return;
        }

        if (Boolean(elementorFrontend.isEditMode()) === false) {
            if ($($scope).is('.elementor-hidden-desktop, .elementor-hidden-tablet, .elementor-hidden-mobile')) {
                return;
            }

            if ($settings.externalSystem == 'yes' && $settings.externalSystemValid == false) {
                return;
            }

            if ($settings.linkWithConfetti === true){
                jQuery.ajax({
                    type: "post",
                    dataType: "json",
                    url: ElementPackConfig.ajaxurl,
                    data: {
                        action: "ep_connect_confetti",
                        data  : 'empty'
                    },
                    success: function () {
                    }
                })
            }
           
        }


        var id = '#' + $settings.id,
            timeOut = $settings.notifyTimeout,
            notifyType = $settings.notifyType,
            notifyFixPos = $settings.notifyFixPosition,
            editMode = Boolean(elementorFrontend.isEditMode());


        if (typeof $settings.notifyTimeout === "undefined") {
            timeOut = null;
        }

        bdtUIkit.util.on(document, 'beforehide', '[bdt-alert]', function (event) {
            if (notifyFixPos === 'top') {
                $('html').attr('style', 'margin-top: unset !important');
            }
        });

        var notification = {
            htmlMarginRemove: function () {
                $('html').css({
                    'margin-top': 'unset  !important'
                });
            },
            appendBody: function () {
                $('body > ' + id).slice(1).remove();
                $(id).prependTo($("body"));
            },
            showNotify: function () {
                $(id).removeClass('bdt-hidden');
            },
            notifyFixed: function () {
                this.htmlMarginRemove();
                setTimeout(function () {
                    if (notifyFixPos == 'top') {
                        var notifyHeight = $('.bdt-notify-wrapper').outerHeight();
                        if ($('.admin-bar').length) {
                            notifyHeight = notifyHeight + 32;
                            $(id).attr('style', 'margin-top: 32px !important');
                        }
                        $('html').attr('style', 'margin-top: ' + notifyHeight + 'px !important');
                        $('html').css({
                            'transition': 'margin-top .8s ease'
                        });
                        $(window).on('resize', function () {
                            notifyHeight = $('.bdt-notify-wrapper').outerHeight();
                            if ($('.admin-bar').length) {
                                notifyHeight = notifyHeight + 32;
                            }
                            $('html').attr('style', 'margin-top: ' + notifyHeight + 'px !important');
                        });
                    }
                }, 1000);
            },
            notifyRelative: function () {
                $('body > ' + id).remove();
            },
            notifyPopup: function () {
                bdtUIkit.notification({
                    message: $settings.msg,
                    status: $settings.notifyStatus,
                    pos: $settings.notifyPosition,
                    timeout: timeOut
                });
            },
            notifyFire: function () {
                if (notifyType === 'fixed') {
                    if (notifyFixPos !== 'relative') {
                        this.appendBody();
                        this.notifyFixed();
                    } else {
                        this.htmlMarginRemove();
                        this.notifyRelative();
                    }
                } else {
                    this.notifyPopup();
                }
            },
            setLocalize: function () {
                if (editMode) {
                    this.clearLocalize();
                    return;
                }
                var widgetID = $settings.id,
                    localVal = 0,
                    hours = $settings.displayTimesExpire;

                var expires = (hours * 60 * 60);
                var now = Date.now();
                var schedule = now + expires * 1000;

                if (localStorage.getItem(widgetID) === null) {
                    localStorage.setItem(widgetID, localVal);
                    localStorage.setItem(widgetID + '_expiresIn', schedule);
                }
                if (localStorage.getItem(widgetID) !== null) {
                    var count = parseInt(localStorage.getItem(widgetID), 10);
                    count++;
                    localStorage.setItem(widgetID, count);
                }
            },
            clearLocalize: function () {
                var localizeExpiry = parseInt(localStorage.getItem($settings.id + '_expiresIn'), 10);
                var now = Date.now();
                var schedule = now;
                if (schedule >= localizeExpiry) {
                    localStorage.removeItem($settings.id + '_expiresIn');
                    localStorage.removeItem($settings.id);
                }
            },
            notificationInit: function () {
                var init = this;

                this.setLocalize();
                var displayTimes = $settings.displayTimes,
                    firedNotify = parseInt(localStorage.getItem($settings.id), 10);

                if ((displayTimes !== false) && (firedNotify > displayTimes)) {
                    return;
                }

                this.showNotify();

                if ($settings.notifyEvent == 'onload' || $settings.notifyEvent == 'inDelay') {
                    $(document).ready(function () {
                        setTimeout(function () {
                            init.notifyFire();
                        }, $settings.notifyInDelay);
                    });
                }
                if ($settings.notifyEvent == 'click' || $settings.notifyEvent == 'mouseover') {
                    $($settings.notifySelector).on($settings.notifyEvent, function () {
                        init.notifyFire();
                    });
                }
            },
        };

        notification.notificationInit();

        $('.bdt-notify-wrapper.bdt-position-fixed .bdt-alert-close').on('click', function (e) {
            $('html').attr('style', 'margin-top: unset !important');
            if ($('.admin-bar').length) {
                $('html').attr('style', 'margin-top: 32px !important');
            }
        });
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-notification.default', widgetNotification);
    });

}(jQuery, window.elementorFrontend));

/**
 * End notification widget script
 */
/**
 * Start offcanvas widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetOffcanvas = function( $scope, $ ) {

		const $offcanvas = $scope.find( '.bdt-offcanvas' );
			
        if ( ! $offcanvas.length ) {
            return;
        }


        $.each($offcanvas, function(index, val) {
            
            const $this   	= $(this),
                $settings   = $this.data('settings'),
                offcanvasID = $settings.id;
            
            if ( $(offcanvasID).length ) {
                $(offcanvasID).on('click', function(event){
                    event.preventDefault();       
                    bdtUIkit.offcanvas( $this ).show();
                });
            }

        });

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-offcanvas.default', widgetOffcanvas );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End offcanvas widget script
 */


; (function ($, elementor) {
    'use strict';

    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            ScrollingEffect;

        ScrollingEffect = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    media: false,
                    easing: 1,
                    viewport: 1,
                };
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_parallax_effects') !== -1) {
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('ep_parallax_effects_' + key);
            },

            run: function () {
                const options = this.getDefaultSettings(),
                    element = this.$element[0];

                if (this.settings('y')) {
                    if (this.settings('y_custom_show')) {
                        options.y = this.settings('y_custom_value');
                    } else {
                        if (this.settings('y_start.size') || this.settings('y_end.size')) {
                            options.y = [this.settings('y_start.size') || 0, this.settings('y_end.size') || 0];

                        }
                    }
                }

                if (this.settings('x')) {
                    if (this.settings('x_custom_show')) {
                        options.x = this.settings('x_custom_value');
                    } else {
                        if (this.settings('x_start.size') || this.settings('x_end.size')) {
                            options.x = [this.settings('x_start.size'), this.settings('x_end.size')];
                        }
                    }
                }

                if (this.settings('opacity_toggole')) {
                    if (this.settings('opacity_custom_show')) {
                        options.opacity = this.settings('opacity_custom_value');
                    } else {
                        if ('htov' === this.settings('opacity')) {
                            options.opacity = [0, 1];
                        } else if ('vtoh' === this.settings('opacity')) {
                            options.opacity = [1, 0];
                        }
                    }
                }

                if (this.settings('blur')) {
                    if (this.settings('blur_start.size') || this.settings('blur_end.size')) {
                        options.blur = [this.settings('blur_start.size') || 0, this.settings('blur_end.size') || 0];
                    }
                }

                if (this.settings('rotate')) {
                    if (this.settings('rotate_start.size') || this.settings('rotate_end.size')) {
                        options.rotate = [this.settings('rotate_start.size') || 0, this.settings('rotate_end.size') || 0];
                    }
                }

                if (this.settings('scale')) {
                    if (this.settings('scale_start.size') || this.settings('scale_end.size')) {
                        options.scale = [this.settings('scale_start.size') || 1, this.settings('scale_end.size') || 1];
                    }
                }

                if (this.settings('hue')) {
                    if (this.settings('hue_value.size')) {
                        options.hue = this.settings('hue_value.size');
                    }
                }

                if (this.settings('sepia')) {
                    if (this.settings('sepia_value.size')) {
                        options.sepia = this.settings('sepia_value.size');
                    }
                }

                if (this.settings('viewport')) {
                    if (this.settings('viewport_start')) {
                        options.start = this.settings('viewport_start');
                    }
                    if (this.settings('viewport_end')) {
                        options.end = this.settings('viewport_end');
                    }
                }

                if (this.settings('media_query')) {
                    options.media = this.settings('media_query');
                }

                if (this.settings('easing')) {
                    if (this.settings('easing_value.size')) {
                        options.easing = this.settings('easing_value.size');
                    }
                }

                if (this.settings('target')) {
                    if (this.settings('target') === 'section') {
                        options.target = '.elementor-section.elementor-element-' + jQuery(element).closest('section').data('id');
                    }
                }


                if (this.settings('show')) {
                    if (
                        this.settings('y') ||
                        this.settings('x') ||
                        this.settings('opacity') ||
                        this.settings('blur') ||
                        this.settings('rotate') ||
                        this.settings('scale') ||
                        this.settings('hue') ||
                        this.settings('sepia') ||
                        this.settings('viewport') ||
                        this.settings('media_query') ||
                        this.settings('easing') ||
                        this.settings('target')
                    ) {
                        bdtUIkit.parallax(element, options);
                    }
                }

            }
        });


        elementorFrontend.hooks.addAction('frontend/element_ready/section', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(ScrollingEffect, { $element: $scope });
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(ScrollingEffect, { $element: $scope });
        });
    });
}(jQuery, window.elementorFrontend));

;
(function ($, elementor) {
    'use strict';
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            Particles;

        Particles = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    allowHTML: true,
                };
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('section_particles') !== -1) {
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('section_particles_' + key);
            },

            run: function () {
                const particleID = 'bdt-particle-container-' + this.$element.data('id'),
                    widgetID = this.$element.data('id'),
                    widgetContainer = $('.elementor-element-' + widgetID);
                this.particleID = particleID;

                let jsonData = {
                    'particles': {
                        'number': {
                            'value': 80,
                            'density': {
                                'enable': true,
                                'value_area': 800
                            }
                        },
                        'color': {
                            'value': '#ffffff'
                        },
                        'shape': {
                            'type': 'circle',
                            'stroke': {
                                'width': 0,
                                'color': '#000000'
                            },
                            'polygon': {
                                'nb_sides': 5
                            },
                            'image': {
                                'src': '',
                                'width': 100,
                                'height': 100
                            }
                        },
                        'opacity': {
                            'value': 0.5,
                            'random': false,
                            'anim': {
                                'enable': false,
                                'speed': 1,
                                'opacity_min': 0.1,
                                'sync': false
                            }
                        },
                        'size': {
                            'value': 3,
                            'random': true,
                            'anim': {
                                'enable': false,
                                'speed': 40,
                                'size_min': 0.1,
                                'sync': false
                            }
                        },
                        'line_linked': {
                            'enable': true,
                            'distance': 150,
                            'color': '#ffffff',
                            'opacity': 0.4,
                            'width': 1
                        },
                        'move': {
                            'enable': true,
                            'speed': 6,
                            'direction': 'none',
                            'random': false,
                            'straight': false,
                            'out_mode': 'out',
                            'bounce': false,
                            'attract': {
                                'enable': false,
                                'rotateX': 600,
                                'rotateY': 1200
                            }
                        }
                    },
                    'interactivity': {
                        'detect_on': 'canvas',
                        'events': {
                            'onhover': {
                                'enable': false,
                                'mode': 'repulse'
                            },
                            'onclick': {
                                'enable': true,
                                'mode': 'push'
                            },
                            'resize': true
                        },
                        'modes': {
                            'grab': {
                                'distance': 400,
                                'line_linked': {
                                    'opacity': 1
                                }
                            },
                            'bubble': {
                                'distance': 400,
                                'size': 40,
                                'duration': 2,
                                'opacity': 8,
                                'speed': 3
                            },
                            'repulse': {
                                'distance': 200,
                                'duration': 0.4
                            },
                            'push': {
                                'particles_nb': 4
                            },
                            'remove': {
                                'particles_nb': 2
                            }
                        }
                    },
                    'retina_detect': true
                };

                if (this.settings('js') && this.settings('js').length !== 0) {
                    jsonData = JSON.parse(this.settings('js'));
                }

                if (this.settings('on')) {
                    if ($('#' + particleID).length === 0) {
                        $(widgetContainer).prepend('<div id="' + particleID + '" class="bdt-particle-container"></div>');
                    }

                    particlesJS(particleID, jsonData);

                }
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/section', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(Particles, {
                $element: $scope
            });
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/container', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(Particles, {
                $element: $scope
            });
        });

    });

}(jQuery, window.elementorFrontend));
/**
 * Start portfolio carousel widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetPortfolioCarousel = function ($scope, $) {

        const $carousel = $scope.find('.bdt-portfolio-carousel');

        if (!$carousel.length) {
            return;
        }

        const $carouselContainer = $carousel.find('.swiper-carousel'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            const swiper = await new Swiper($carouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }

        };
    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-carousel.default', widgetPortfolioCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-carousel.bdt-abetis', widgetPortfolioCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-carousel.bdt-fedara', widgetPortfolioCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-carousel.bdt-trosia', widgetPortfolioCarousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-carousel.bdt-janes', widgetPortfolioCarousel);
    });

}(jQuery, window.elementorFrontend));

/**
 * End portfolio carousel widget script
 */
/**
 * Start portfolio gallery widget script
 */

(function ($, elementor) {
    'use strict';

    var widgetPortfolioGallery = function ($scope, $) {
        var $portfolioGalleryWrapper = $scope.find('.bdt-portfolio-gallery-wrapper'),
            $settings = $portfolioGalleryWrapper.data('settings'),
            $portfolioFilter = $portfolioGalleryWrapper.find('.bdt-ep-grid-filters-wrapper');

        if (!$portfolioGalleryWrapper.length) {
            return;
        }

        if ($settings.tiltShow == true) {
            var elements = document.querySelectorAll($settings.id + " [data-tilt]");
            VanillaTilt.init(elements);
        }

        if (!$portfolioFilter.length) {
            return;
        }
        var $settings = $portfolioFilter.data('hash-settings');
        var activeHash = $settings.activeHash;
        var hashTopOffset = $settings.hashTopOffset;
        var hashScrollspyTime = $settings.hashScrollspyTime;

        function hashHandler($portfolioFilter, hashScrollspyTime, hashTopOffset) {
            if (window.location.hash) {
                if ($($portfolioFilter).find('[bdt-filter-control="[data-filter*=\'' + window.location.hash.substring(1) + '\']"]').length) {
                    var hashTarget = $('[bdt-filter-control="[data-filter*=\'' + window.location.hash.substring(1) + '\']"]').closest($portfolioFilter).attr('id');
                    $('html, body').animate({
                        easing: 'slow',
                        scrollTop: $('#' + hashTarget).offset().top - hashTopOffset
                    }, hashScrollspyTime, function () {
                    }).promise().then(function () {
                        $('[bdt-filter-control="[data-filter*=\'' + window.location.hash.substring(1) + '\']"]').trigger("click");
                    });
                }
            }
        }
        if ($settings.activeHash == 'yes') {
            $(window).on('load', function () {
                hashHandler($portfolioFilter, hashScrollspyTime = 1500, hashTopOffset);
            });
            $($portfolioFilter).find('.bdt-ep-grid-filter').off('click').on('click', function (event) {
                window.location.hash = ($.trim($(this).context.innerText.toLowerCase())).replace(/\s+/g, '-');
            });
            $(window).on('hashchange', function (e) {
                hashHandler($portfolioFilter, hashScrollspyTime, hashTopOffset);
            });
        }


        var categories = {},
            category;

        var arr = [];
        var totalItem = 0;
        $($portfolioGalleryWrapper).find(".bdt-portfolio-gallery div[data-filter]").each(function (i, el) {
            category = $(el).data("filter");
            let list = category.split(/\s+/);
            $(list).each(function (i, el) {
                arr.push(el);
            });
            totalItem = totalItem + 1;
        });

        var counts = {};
        arr.forEach(function (x) {
            counts[x] = (counts[x] || 0) + 1;
        });
        $($portfolioGalleryWrapper).find('.bdt-all-count').text(totalItem);
        for (var key in counts) {
            $($portfolioGalleryWrapper).find('[data-bdt-target=' + key + '] .bdt-count').text(counts[key]);
        }


    };
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-gallery.default', widgetPortfolioGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-gallery.bdt-abetis', widgetPortfolioGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-gallery.bdt-fedara', widgetPortfolioGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-gallery.bdt-trosia', widgetPortfolioGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-gallery.bdt-janes', widgetPortfolioGallery);
    });
}(jQuery, window.elementorFrontend));

/**
 * End portfolio gallery widget script
 */
(function ($, elementor) {
    'use strict';

    var widgetPostGallery = function ($scope, $) {
        var $postGalleryWrapper = $scope.find('.bdt-post-gallery-wrapper'),
            $bdtPostGallery = $scope.find('.bdt-post-gallery'),
            $settings = $bdtPostGallery.data('settings'),
            $postFilter = $postGalleryWrapper.find('.bdt-ep-grid-filters-wrapper'),
            _skin = (typeof $settings !== 'undefined' && typeof $settings._skin !== 'undefined') ? $settings._skin.split('-').pop() : 'default',
            isEditorMode = $('.elementor-editor-active').length > 0 ? true : false;

        const tiltSelector = $settings.id + " [data-tilt]";

        if (!$postGalleryWrapper.length) {
            return;
        }

        if ($settings.tilt_show == true) {
            initializeTilt(tiltSelector);
        }

        if (!$postFilter.length) {
            return;
        }

        var $hashSettings = $postFilter.data('hash-settings');
        var activeHash = $hashSettings.activeHash;
        var hashTopOffset = $hashSettings.hashTopOffset || 70;
        var hashScrollspyTime = $hashSettings.hashScrollspyTime || 1000;

        var categoryCache = {},
            tabs_header = $postGalleryWrapper.find(".bdt-ep-grid-filter"),
            tabs = tabs_header.find(".bdt-option"),
            loader = $postGalleryWrapper.find("#bdt-loading-image");

        function loadCategoryData(slug) {
            $(loader).show();

            if (!isEditorMode && categoryCache[slug]) {
                $bdtPostGallery.fadeOut(200, function () {
                    $(this)
                        .html(categoryCache[slug])
                        .fadeIn(300)
                        .css("transform", "translateY(-10px)")
                        .animate({ transform: "translateY(0)" }, 300);
                });
                $(loader).hide();
            } else {
                $.ajax({
                    url: ElementPackConfig.ajaxurl,
                    data: {
                        action: "bdt_post_gallery",
                        settings: $settings,
                        category: slug,
                        _skin: _skin,
                        nonce: ElementPackConfig.nonce,
                    },
                    type: "POST",
                    dataType: "HTML",
                    beforeSend: function () {
                        $(loader).show();
                    },
                    success: function (response) {
                        categoryCache[slug] = response;
                        $bdtPostGallery.fadeOut(200, function () {
                            $(this).html(response).fadeIn(300, function () {
                                if ($settings.tilt_show == true) {
                                    destroyTiltInstances(tiltSelector);
                                    initializeTilt(tiltSelector);
                                    observeTiltElements(tiltSelector);
                                }
                            });
                        });
                    },
                    error: function (response) {
                        console.log(response);
                    },
                    complete: function () {
                        $(loader).hide();
                    },
                });
            }
        }

        function hashHandler() {
            if (window.location.hash) {
                var currentHash = window.location.hash.substring(1);

                var decodedHash = decodeURIComponent(currentHash);

                var targetTab = tabs_header.find('[data-slug]').filter(function() {
                    var dataSlug = $.trim($(this).attr('data-slug')).toLowerCase();
                    var hashValue = $.trim(decodedHash).toLowerCase();
                    return dataSlug === hashValue;
                });

                if (targetTab.length) {
                    tabs_header.removeClass("bdt-active");
                    targetTab.parent().addClass("bdt-active");

                    loadCategoryData(targetTab.data('slug'));

                    $('html, body').animate({
                        easing: 'slow',
                        scrollTop: $postGalleryWrapper.offset().top - hashTopOffset
                    }, hashScrollspyTime);
                }
            }
        }

        if (activeHash) {
            $(document).ready(function () {
                setTimeout(function() {
                    hashHandler();
                }, 100);
            });
            
            $(window).on('load', function () {
                setTimeout(function() {
                    hashHandler();
                }, 100);
            });

            $(window).on('hashchange', function () {
                hashHandler();
            });
        }

        tabs.on("click", function (e) {
            e.preventDefault();
            var $this = $(this),
                slug = $this.data("slug");

            tabs_header.removeClass("bdt-active");
            $this.parent().addClass("bdt-active");

            loadCategoryData(slug);
            
            if (activeHash) {
                const title = $.trim(slug);
                const encoded = encodeURIComponent(title);
                window.location.hash = encoded;
                history.replaceState(null, null, '#' + title);
            }
        });

        function destroyTiltInstances(selector) {
            var elements = document.querySelectorAll(selector);
            elements.forEach(function (element) {
                if (element.vanillaTilt) {
                    element.vanillaTilt.destroy();
                }
            });
        }

        function initializeTilt(selector) {
            var elements = document.querySelectorAll(selector);
            if (elements.length > 0) {
                VanillaTilt.init(elements);
            }
        }

        function observeTiltElements(selector) {
            var observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.type === 'childList') {
                        initializeTilt(selector);
                    }
                });
            });

            var container = document.querySelector($settings.id);
            if (container) {
                observer.observe(container, { childList: true, subtree: true });
            }
        }
    };

    jQuery(window).on("elementor/frontend/init", function () {
        [
            "bdt-post-gallery.default",
            "bdt-post-gallery.bdt-abetis",
            "bdt-post-gallery.bdt-fedara",
            "bdt-post-gallery.bdt-trosia",
        ].forEach((hook) => elementorFrontend.hooks.addAction(`frontend/element_ready/${hook}`, widgetPostGallery));
    });
})(jQuery, window.elementorFrontend);
/**
 * Start post grid tab widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetPostGridTab = function( $scope, $ ) {

		const $postGridTab = $scope.find( '.bdt-post-grid-tab' ),
			gridTab      = $postGridTab.find('.gridtab');

		if ( ! $postGridTab.length ) {
			return;
		}

		gridTab.gridtab($postGridTab.data('settings'));

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-post-grid-tab.default', widgetPostGridTab );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End post grid tab widget script
 */

 
/**
 * Start price table widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetPriceTable = function( $scope, $ ) {

		const $priceTable = $scope.find( '.bdt-price-table' ),
            $featuresList = $priceTable.find( '.bdt-price-table-feature-inner' ),
			$settings = $priceTable.data('settings');

        if ( ! $priceTable.length ) {
            return;
        }
		
		if ( $settings.read_more_toggle ) {

			const $read_more = $priceTable.find(".bdt-read-more-features");
			const default_load = $priceTable.find(".bdt-read-more-features").data("bdt-default-load");
			const $ul_listing = $priceTable.find(".bdt-price-table-features-list");

			$ul_listing.each(function() {				   
				const $list = $(this);
				$list.find("li:gt("+default_load+")").hide();
			});

			$read_more.off("click").on("click", function(e) {
				e.preventDefault();
				const a = $(this),
					$priceTable = a.closest(".bdt-price-table"),
					$ul_listing = $priceTable.find(".bdt-price-table-features-list"),
					$items_to_toggle = $ul_listing.find("li:gt("+default_load+")"),
					$less_text = a.data("bdt-less"),
					$more_text = a.data("bdt-more");

				if (a.hasClass("bdt-more")) {
					$items_to_toggle.each(function(index) {
						const $item = $(this);
						setTimeout(function() {
							$item.slideDown(200);
						}, index * 50);
					});
					a.text($less_text).addClass("bdt-less").removeClass("bdt-more");
				} else if (a.hasClass("bdt-less")) {
					$items_to_toggle.slideUp(300);
					a.text($more_text).addClass("bdt-more").removeClass("bdt-less");
				}
			});

		}
					
        const $tooltip = $featuresList.find('> .bdt-tippy-tooltip'),
        	widgetID = $scope.data('id');
		
		$tooltip.each( function( index ) {
			tippy( this, {
				allowHTML: true,
				theme: 'bdt-tippy-' + widgetID
			});				
		});
    };    

	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-price-table.default', widgetPriceTable );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-price-table.bdt-partait', widgetPriceTable );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-price-table.bdt-erect', widgetPriceTable );
	});

	/**
	 * WooCommerce integration — EDD-style single button flow.
	 * After a successful AJAX add-to-cart, WooCommerce appends a separate "View cart"
	 * link and marks the button as "added". We drop that extra link and turn the price
	 * table footer button itself into the "View Cart" action, matching the EDD behaviour.
	 */
	$( document.body ).on( 'added_to_cart', function( event, fragments, cart_hash, $button ) {
		if ( ! $button || ! $button.hasClass || ! $button.hasClass( 'bdt-price-table-button' ) ) {
			return;
		}

		// Defer so this runs after WooCommerce has appended its own "View cart" link,
		// regardless of script load order.
		setTimeout( function() {
			var wcParams     = ( typeof wc_add_to_cart_params !== 'undefined' ) ? wc_add_to_cart_params : {};
			var cartUrl      = wcParams.cart_url || $button.attr( 'href' );
			var viewCartText = wcParams.i18n_view_cart || 'View cart';

			// Remove the extra "View cart" link WooCommerce injected next to the button.
			$button.parent().find( '.added_to_cart' ).remove();

			// Transform the footer button into the view-cart button.
			$button
				.removeClass( 'add_to_cart_button ajax_add_to_cart loading' )
				.addClass( 'bdt-price-table-view-cart added' )
				.attr( 'href', cartUrl )
				.text( viewCartText );
		}, 0 );
	} );

}( jQuery, window.elementorFrontend ) );

/**
 * End price table widget script
 */


/**
 * Start qrcode widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetQRCode = function( $scope, $ ) {

		const $qrcode = $scope.find( '.bdt-qrcode' ),
            image   = $scope.find( '.bdt-qrcode-image' );

        if ( ! $qrcode.length ) {
            return;
        }
        const settings = $qrcode.data('settings');
            settings.image = image[0];

        $qrcode.qrcode(settings);

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-qrcode.default', widgetQRCode );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End qrcode widget script
 */


(function ($, elementor) {

    'use strict';
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            RevealFX;

        RevealFX = ModuleHandler.extend({
            bindEvents: function () {
                this.run();
            },
            settings: function (key) {
                return this.getElementSettings('element_pack_reveal_effects_' + key);
            },
            run: function () {

                if ('yes' !== this.settings('enable')) {
                    return;
                }

                var widgetID = this.$element.data('id'),
                    widgetContainer = $('.elementor-element-' + widgetID);

                $(widgetContainer).attr('data-ep-reveal', 'ep-reveal-' + widgetID + '');

                const revealID = '*[data-ep-reveal="ep-reveal-' + widgetID + '"]';
                const revealWrapper = document.querySelector(revealID);
                const revealFX = new RevealFx(revealWrapper, {
                    revealSettings: {
                        bgColors: this.settings('color') ? [this.settings('color')] : ['#333'],
                        direction: this.settings('direction') ? String(this.settings('direction')) : String('c'),
                        duration: this.settings('speed') ? Number(this.settings('speed.size') * 100) : Number(500),
                        easing: this.settings('easing') ? String(this.settings('easing')) : String('easeOutQuint'),
                        onHalfway: function (contentEl, ngsrevealerEl) {
                            contentEl.style.opacity = 1;
                        }
                    }
                });

                epObserveTarget(revealWrapper, function () {
                    revealFX.reveal();
                }, {
                    root: null,
                    rootMargin: '0px',
                    threshold: 0.8
                });
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(RevealFX, {
                $element: $scope
            });
        });
    });

}(jQuery, window.elementorFrontend));
(function ($, elementor) {
    'use strict';
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            RippleEffects;

        RippleEffects = ModuleHandler.extend({
            bindEvents: function () {
                this.run();
            },
            getDefaultSettings: function () {
                return {
                    multi: true,
                };
            },
            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_ripple_') !== -1) {
                    this.run();
                }
            }, 400),
            settings: function (key) {
                return this.getElementSettings('ep_ripple_' + key);
            },
            run: function () {
                if (this.settings('enable') !== 'yes') {
                    return;
                }

                var $element = this.$element,
                    options = this.getDefaultSettings(),
                    $widgetId = 'ep-' + this.getID(),
                    $widgetClassSelect = '.elementor-element-' + this.getID(),
                    $selector = '';

                if (this.settings('selector') === 'widgets') {
                    $selector = $widgetClassSelect + ' > :first-child';
                }
                if (this.settings('selector') === 'images') {
                    $selector = $widgetClassSelect + ' img';
                }
                if (this.settings('selector') === 'buttons') {
                    $selector = $widgetClassSelect + ' a';
                }
                if (this.settings('selector') === 'both') {
                    $selector = $widgetClassSelect + ' a,' + $widgetClassSelect + ' img';
                }
                if (this.settings('selector') === 'custom' && this.settings('custom_selector')) {
                    $selector = this.settings('custom_selector');
                }

                if ('' === $selector ) {
                    return;
                }

                $(document).on('click', '[href="#"]', function (e) { e.preventDefault(); });
                if (this.settings('on')) {
                    options.on = this.settings('on');
                }
                if (this.settings('easing')) {
                    options.easing = this.settings('easing');
                }
                if (this.settings('duration.size')) {
                    options.duration = this.settings('duration.size');
                }
                if (this.settings('opacity.size')) {
                    options.opacity = this.settings('opacity.size');
                }
                if (this.settings('color')) {
                    options.color = this.settings('color');
                }

                document.querySelectorAll($selector).forEach(function (el) {
                    if ('IMG' == el.tagName) {
                        var $image = $(el);
                        $image.wrap('<div id="bdt-ripple-effect-img-wrapper-' + $widgetId + '"></div>');
                        window.rippler = $.ripple('#bdt-ripple-effect-img-wrapper-' + $widgetId, options);
                    } else {
                        window.rippler = $.ripple($selector, options);
                    }
                });
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(RippleEffects, {
                $element: $scope
            });
        });
    });

}(jQuery, window.elementorFrontend));

/**
 * Start section sticky widget script
 */

(function ($, elementor) {

    'use strict';

    function debounce(func, wait) {
        let timeout;
        return function executedFunction() {
            const later = function() {
                clearTimeout(timeout);
                func();
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function handleDynamicPositioning($stickyElement) {
        const stickyData = $stickyElement.attr('data-bdt-sticky');

        if (!stickyData) {
            return;
        }

        const originalOffset = $stickyElement.offset();
        const originalLeft = originalOffset ? originalOffset.left : 0;
        let isPositioned = false;

        function calculateInsetValue() {
            const elementWidth = $stickyElement.outerWidth();
            const documentWidth = $(document).width();
            const isRTL = $('html').attr('dir') === 'rtl' || $('body').hasClass('rtl');

            return isRTL ?
                Math.max(documentWidth - elementWidth - originalLeft, 0) :
                originalLeft;
        }

        function updatePositioning(immediate) {
            if ($stickyElement.hasClass('bdt-active')) {
                if (!isPositioned) {
                    const insetValue = calculateInsetValue();
                    $stickyElement.css({
                        'inset-inline-start': insetValue + 'px',
                        'transition': immediate ? 'none' : 'inset-inline-start 0.15s ease-out'
                    });
                    isPositioned = true;
                }
            } else {
                if (isPositioned) {
                    $stickyElement.css({
                        'inset-inline-start': '',
                        'transition': 'inset-inline-start 0.15s ease-out'
                    });
                    isPositioned = false;
                }
            }
        }

        const debouncedResize = debounce(function() {
            if (isPositioned) {
                updatePositioning(true);
            }
        }, 100);

        $(window).on('resize', debouncedResize);

        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    requestAnimationFrame(function() {
                        updatePositioning(false);
                    });
                }
            });
        });

        observer.observe($stickyElement[0], {
            attributes: true,
            attributeFilter: ['class']
        });

        // Listen for UIkit sticky events
        $stickyElement.on('active', function () {
            requestAnimationFrame(function() {
                updatePositioning(true);
            });
        });

        $stickyElement.on('inactive', function () {
            requestAnimationFrame(function() {
                updatePositioning(false);
            });
        });

        // Initial check with delay to ensure proper initialization
        setTimeout(function() {
            updatePositioning(true);
        }, 150);
    }

    const widgetSectionSticky = function ($scope, $) {
        const $section = $scope;

        // Sticky fixes for inner section
        $section.each(function () {
            const $stickyFound = $(this).find('.elementor-inner-section.bdt-sticky');
            if ($stickyFound.length) {
                $stickyFound.wrap('<div class="bdt-sticky-wrapper"></div>');
            }
        });

        let $stickyElements = $section.find('[data-bdt-sticky]');
        if ($stickyElements.length === 0) {
            $stickyElements = $('[data-bdt-sticky]');
        }

        $stickyElements.each(function () {
            handleDynamicPositioning($(this));
        });
    };

    $(document).ready(function () {
        $('[data-bdt-sticky]').each(function () {
            handleDynamicPositioning($(this));
        });
    });

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/section', widgetSectionSticky);
    });

}(jQuery, window.elementorFrontend));

/**
 * End section sticky widget script
 */

/**
 * Start slideshow widget script
 */

(function($, elementor) {

    'use strict';

    const widgetSlideshow = function($scope, $) {

        const $slideshow = $scope.find( '.bdt-slideshow' );

        if ( ! $slideshow.length ) {
            return;
        }

        const $thumbNav = $($slideshow).find('.bdt-thumbnav-wrapper > .bdt-thumbnav-scroller');

        $($thumbNav).mThumbnailScroller({
            axis: 'yx',
            type: 'hover-precise'
        });

    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-slideshow.default', widgetSlideshow);
    });

}(jQuery, window.elementorFrontend));

/**
 * End slideshow widget script
 */

/**
 * Start vertical menu widget script
 */

(function ($, elementor) {
    'use strict';
    // Vertical Menu
    var widgetSlinkyVerticalMenu = function ($scope, $) {
        var $vrMenu = $scope.find('.bdt-slinky-vertical-menu');
        var $settings = $vrMenu.attr('id');
        if (!$vrMenu.length) {
            return;
        }

        const slinky = $('#'+$settings).slinky();

        // Override the _move method to handle RTL (moved from vendor)
        const isRTL = document.documentElement.dir === 'rtl';
        if (isRTL) {
            slinky._move = function(depth, callback = () => {}) {
                // get current position from the right
                const right = Math.round(parseInt($('#'+$settings).children().first().get(0).style.right, 10)) || 0;

                // set the new position from the right
                $('#'+$settings).children().first().css("right", `${right - depth * 100}%`);

                // fire callback after animation completes so .active state
                // is managed correctly (hides child ul, re-activates parent)
                if (typeof callback === "function") {
                    setTimeout(callback, slinky.settings.speed);
                }
            };
        };
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-slinky-vertical-menu.default', widgetSlinkyVerticalMenu);
    });

}(jQuery, window.elementorFrontend));

/**
 * End vertical menu widget script
 */


;(function ($, elementor) {
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            SoundEffects;

        SoundEffects = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    event: 'hover',
                };
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_sound_effects_') !== -1) {
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('ep_sound_effects_' + key);
            },

            isAtomicWidget: function () {
                return !!this.getAtomicSettings();
            },

            getAtomicSettings: function () {
                var element = this.$element.get(0);

                if (!element) {
                    return null;
                }

                var dataNode = element.matches('[data-ep-sound-effects]')
                    ? element
                    : element.querySelector('[data-ep-sound-effects]');

                if (!dataNode) {
                    var firstChild = this.findElement('> :first-child').get(0);

                    if (firstChild) {
                        dataNode = firstChild.matches('[data-ep-sound-effects]')
                            ? firstChild
                            : firstChild.querySelector('[data-ep-sound-effects]') || firstChild;
                    }
                }

                if (!dataNode) {
                    return null;
                }

                var payload = dataNode.getAttribute('data-ep-sound-effects');

                if (!payload) {
                    return null;
                }

                try {
                    var parsed = JSON.parse(payload);

                    if (!parsed || parsed.active !== 'yes') {
                        return null;
                    }

                    return parsed;
                } catch (error) {
                    return null;
                }
            },

            getSettingValue: function (key) {
                var atomicSettings = this.getAtomicSettings();

                if (atomicSettings && typeof atomicSettings[key] !== 'undefined') {
                    return atomicSettings[key];
                }

                return this.settings(key);
            },

            getSoundEffectsRoot: function () {
                var $scope = this.$element;
                var $container = this.findElement('.elementor-widget-container').first();

                if ($container.length) {
                    return $container.get(0);
                }

                var $direct = this.findElement('> :not(style)').first();

                if ($direct.length) {
                    return $direct.get(0);
                }

                return $scope.get(0);
            },

            resolveCustomSelector: function (selector) {
                var $scope = this.$element;
                var trimmed = (selector || '').trim();

                if (!trimmed) {
                    return $();
                }

                var $matches = $scope.find(trimmed);

                if (!$matches.length) {
                    $matches = $scope.filter(trimmed);
                }

                if (!$matches.length && trimmed.indexOf('.') === -1 && trimmed.indexOf('#') === -1 && trimmed.indexOf(' ') === -1) {
                    $matches = $scope.find('#' + trimmed);

                    if (!$matches.length) {
                        $matches = $scope.find('.' + trimmed);
                    }
                }

                return $matches;
            },

            resolveSoundEffectsTargets: function () {
                var selectType = this.getSettingValue('select_type');
                var customSelector = this.getSettingValue('element_selector');
                var widgetId = 'ep-sound-effects' + this.getID();
                var $scope = this.$element;
                var root = this.getSoundEffectsRoot();

                if (selectType === 'custom') {
                    return this.resolveCustomSelector(customSelector);
                }

                if (root) {
                    $(root).attr('id', widgetId);
                }

                var idSelector = '#' + widgetId;

                if (selectType === 'anchor_tag') {
                    if (this.isAtomicWidget()) {
                        return $scope.find('a');
                    }

                    return $(idSelector + ' a');
                }

                if (selectType === 'widget') {
                    if (this.isAtomicWidget()) {
                        return root ? $(idSelector) : $scope;
                    }

                    return $(idSelector);
                }

                return $();
            },

            run: function () {
                if (this.getSettingValue('active') != 'yes') {
                    return;
                }

                var $element = this.resolveSoundEffectsTargets(),
                    $soundAudioSource,
                    $soundAudioSourceMp3;

                if (!$element || !$element.length) {
                    return;
                }

                if (this.getSettingValue('source') !== 'hosted_url') {
                    $soundAudioSource = this.getSettingValue('source_local_link') + this.getSettingValue('source');
                } else {
                    var hostedUrl = this.getSettingValue('hosted_url'),
                        hostedUrlMp3 = this.getSettingValue('hosted_url_mp3');

                    if (hostedUrl && hostedUrl.url) {
                        $soundAudioSource = hostedUrl.url.replace(/\.[^/.]+$/, "");
                    }

                    if (hostedUrlMp3 && hostedUrlMp3.url) {
                        $soundAudioSourceMp3 = hostedUrlMp3.url.replace(/\.[^/.]+$/, "");
                    }
                }

                if (!$soundAudioSource) {
                    return;
                }

                if (!document.createElement('audio').canPlayType) {
                    console.error('Oh man 😩! \nYour browser doesn\'t support audio awesomeness.');
                    return function () { };
                }

                var audioPlayer = document.createElement('audio'),
                    mp3Source = document.createElement('source'),
                    oggSource = document.createElement('source'),
                    eventsSet = false;

                audioPlayer.setAttribute('preload', true);

                mp3Source.setAttribute('type', 'audio/mpeg');
                oggSource.setAttribute('type', 'audio/ogg');

                audioPlayer.appendChild(mp3Source);
                audioPlayer.appendChild(oggSource);

                document.body.appendChild(audioPlayer);

                function playAudio() {
                    var audioSrc = $soundAudioSource,
                        soundMp3Link,
                        soundOggLink;

                    if (!audioSrc) {
                        return;
                    }

                    if ($soundAudioSourceMp3) {
                        soundMp3Link = $soundAudioSourceMp3 + '.mp3';
                    }

                    soundOggLink = audioSrc + '.ogg';

                    if (!eventsSet) {
                        eventsSet = true;
                        oggSource.addEventListener('error', function () {
                            console.error('😶 D\'oh! The ogg file URL is wrong!');
                        });
                    }

                    if (soundMp3Link || soundOggLink) {
                        if ($soundAudioSourceMp3) {
                            mp3Source.setAttribute('src', soundMp3Link);
                        }
                        oggSource.setAttribute('src', soundOggLink);

                        audioPlayer.load();
                    }

                    audioPlayer.play();
                }

                function stopAudio() {
                    audioPlayer.pause();
                    audioPlayer.currentTime = 0;
                }

                var initScope = this;

                jQuery(document).ready(function () {
                    if (initScope.getSettingValue('event') == 'hover') {
                        jQuery($element).on('mouseenter', function () {
                            playAudio();
                        });
                        jQuery($element).on('mouseleave', function () {
                            stopAudio();
                        });
                        jQuery($element).on('click', function () {
                            stopAudio();
                        });
                        jQuery($element).on('touchstart', function () {
                            playAudio();
                        });
                    }

                    if (initScope.getSettingValue('event') == 'click') {
                        $($element).on('click', function () {
                            playAudio();
                        });
                    }
                });
            }

        });

        function addHandler($scope) {
            elementorFrontend.elementsHandler.addHandler(SoundEffects, {
                $element: $scope
            });
        }

        function runAtomicSoundEffectsFallback() {
            $('.elementor-element[data-id]').each(function () {
                var $scope = $(this);

                if ($scope.is('[data-ep-sound-effects]') || $scope.find('[data-ep-sound-effects]').length) {
                    addHandler($scope);
                }
            });
        }

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', addHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/e-heading.default', addHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/e-paragraph.default', addHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/e-button.default', addHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
            var $root = $($scope);

            if (!$root.length) {
                return;
            }

            if (
                $root.is('[data-ep-sound-effects]') ||
                $root.find('[data-ep-sound-effects]').length
            ) {
                addHandler($root);
            }
        });

        runAtomicSoundEffectsFallback();
        jQuery(window).on('load', runAtomicSoundEffectsFallback);

    });

})(jQuery, window.elementorFrontend);

/**
 * Start source code widget script
 */

( function( $, elementor ) {

    'use strict';

    const sourceCodeWidget = function( $scope, $ ) {
        const $sourceCode = $scope.find('.bdt-source-code');

        if ( ! $sourceCode.length ) {
            return;
        }

        const $preCode = $sourceCode.find('pre > code');

        const clipboard = new ClipboardJS('.bdt-copy-button', {
            target: function target(trigger) {
                return trigger.nextElementSibling;
            }
        });

        clipboard.on('success', function (event) {
            event.trigger.textContent = 'copied!';
            setTimeout(function () {
                event.clearSelection();
                event.trigger.textContent = 'copy';
            }, 2000);
        });

        Prism.highlightElement($preCode.get(0));

    };

    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-source-code.default', sourceCodeWidget );
    });

}( jQuery, window.elementorFrontend ) );

/**
 * End source code widget script
 */

/**
 * Start switcher widget script
 */

(function ($, elementor) {

	'use strict';

	var sectionSwitcher = function ($scope, $) {
		var $switcher = $scope.find('.bdt-switchers'),
			$settings = $switcher.data('settings'),
			$activatorSettings = $switcher.data('activator'),
			$settingsLinkWidget = $switcher.data('bdt-link-widget'),
			editMode = Boolean(elementorFrontend.isEditMode());


			if ($activatorSettings) {
				const switcherActivator = `#bdt-switcher-activator-${$activatorSettings.id}`;
				const switcherMain = `#bdt-switcher-${$activatorSettings.id}`;
			
				function toggleSwitcher(index) {
					bdtUIkit.switcher(switcherActivator).show(index);
					bdtUIkit.switcher(switcherMain).show(index);
			
					if ($settingsLinkWidget) {
						const showA = index === 0;
						$($settingsLinkWidget.linkWidgetTargetA).css({ 'opacity': showA ? 1 : 0, 'display': showA ? 'block' : 'none' });
						$($settingsLinkWidget.linkWidgetTargetB).css({ 'opacity': showA ? 0 : 1, 'display': showA ? 'none' : 'block' });
					}
				}
			
				bdtUIkit.util.on($activatorSettings.switchA, "click", () => toggleSwitcher(0));
				bdtUIkit.util.on($activatorSettings.switchB, "click", () => toggleSwitcher(1));
			}			

		if ($settings !== undefined && editMode === false) {
			var $switchAContainer = $switcher.find('.bdt-switcher > div > div > .bdt-switcher-item-a'),
				$switchBContainer = $switcher.find('.bdt-switcher > div > div > .bdt-switcher-item-b'),
				$switcherContentA = $('.elementor').find('.elementor-element' + '#' + $settings['switch-a-content']),
				$switcherContentB = $('.elementor').find('.elementor-element' + '#' + $settings['switch-b-content']);

			if ($settings.positionUnchanged !== true) {
				if ($switchAContainer.length && $switcherContentA.length) {
					$switcherContentA.appendTo($switchAContainer);
				}

				if ($switchBContainer.length && $switcherContentB.length) {
					$switcherContentB.appendTo($switchBContainer);
				}
			}

			if ($settings.positionUnchanged == true) {
				$('#bdt-tabs-' + $settings.id).find('.bdt-switcher').remove();

				var $switcherContentAAA = $('#' + $settings['switch-a-content']);
				var $switcherContentBBB = $('#' + $settings['switch-b-content']);

				$('#' + $settings['switch-a-content']).parent().append(`<div id="bdt-switcher-${$settings.id}" class="bdt-switcher bdt-switcher-item-content" style="width:100%;"></div>`);

				$switcherContentAAA.appendTo($('#bdt-switcher-' + $settings.id));
				$switcherContentBBB.appendTo($('#bdt-switcher-' + $settings.id));

				var $activeA, $activeB = '';
				if ($settings.defaultActive == 'a') {
					$activeA = 'bdt-active';
				} else {
					$activeB = 'bdt-active';
				}

				$('#' + $settings['switch-a-content']).wrapAll('<div class="bdt-switcher-item-content-inner ' + $activeA + '"></div>');
				$('#' + $settings['switch-b-content']).wrapAll('<div class="bdt-switcher-item-content-inner ' + $activeB + '"></div>');
			}
		}


		if ($settingsLinkWidget !== undefined && editMode === false) {
			var $targetA = $($settingsLinkWidget.linkWidgetTargetA),
				$targetB = $($settingsLinkWidget.linkWidgetTargetB),
				$switcher = '#bdt-switcher-' + $settingsLinkWidget.id;

			if ($settingsLinkWidget.defaultActive == 'a') {
				$targetA.css({
					'opacity': 1,
					'display': 'block'
				});
				$targetB.css({
					'opacity': 0,
					'display': 'none'
				});
			} else {
				$targetA.css({
					'opacity': 0,
					'display': 'none'
				});
				$targetB.css({
					'opacity': 1,
					'display': 'block'
				});
			}

			$targetA.css({
				'grid-row-start': 1,
				'grid-column-start': 1
			});
			$targetB.css({
				'grid-row-start': 1,
				'grid-column-start': 1
			});

			$targetA.parent().css({
				'display': 'grid'
			});

			bdtUIkit.util.on($switcher, 'shown', function (e) {
				var index = bdtUIkit.util.index(e.target)
				if (index == 0) {
					$targetA.css({
						'opacity': 1,
						'display': 'block',
					});
					$targetB.css({
						'opacity': 0,
						'display': 'none',
					});
				} else {
					$targetB.css({
						'opacity': 1,
						'display': 'block',
					});
					$targetA.css({
						'opacity': 0,
						'display': 'none',
					});
				}

			})
		}


	};

	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-switcher.default', sectionSwitcher);
	});

}(jQuery, window.elementorFrontend));

/**
 * End switcher widget script
 */
; (function ($, elementor) {
$(window).on('elementor/frontend/init', function () {
    let ModuleHandler = elementorModules.frontend.handlers.Base,
        textReadMore;

    textReadMore = ModuleHandler.extend({
        bindEvents: function () {
            this.run();
        },
        getDefaultSettings: function () {
            return {
                allowHTML: true,
            };
        },

        onElementChange: debounce(function (prop) {
            if (prop.indexOf('ep_text_read_more_') !== -1) {
                this.run();
            }
        }, 400),

        settings: function (key) {
            return this.getElementSettings('ep_text_read_more_' + key);
        },

        run: function () {
            if (this.settings('enable') === 'yes') {
                const dReadMore = new DReadMore();

                window.addEventListener('resize', function () {
                    dReadMore.forEach(function (item) {
                        item.update();
                    });
                });
            }
        }
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
        elementorFrontend.elementsHandler.addHandler(textReadMore, {
            $element: $scope
        });
    });
});
})(jQuery, window.elementorFrontend);

/**
 * Start table of content widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetTableOfContent = function( $scope, $ ) {

		var $tableOfContent = $scope.find( '.bdt-table-of-content' );
				
        if ( ! $tableOfContent.length ) {
            return;
        }

        // Move fixed layout TOC to body so position:fixed works (not broken by parent transform)
        var $fixedContainer = $scope.find( '.table-of-content-layout-fixed' ).first();
        if ( $fixedContainer.length && ! $fixedContainer.data( 'ep-toc-moved' ) ) {
            $fixedContainer.data( 'ep-toc-moved', true );
            $fixedContainer.after( '<div class="bdt-toc-fixed-placeholder" style="width:320px;min-height:1px;" aria-hidden="true"></div>' );
            $fixedContainer.appendTo( 'body' );
        }

        var settings = $tableOfContent.data('settings') || {};
        $tableOfContent.tocify(settings);

        // When Auto Collapse is on: start with all sub-items closed (scroll will open the active section's submenu)
        if ( settings.showAndHide ) {
            $tableOfContent.find( '.tocify-subheader' ).hide();
            // After a click, disable scroll-driven open until scroll settles so submenu doesn't close-then-open
            $tableOfContent.on( 'click.toc-ep', 'li', function() {
                $tableOfContent.tocify( 'option', 'showAndHideOnScroll', false );
                clearTimeout( $tableOfContent.data( 'ep-toc-scroll-lock' ) );
                $tableOfContent.data( 'ep-toc-scroll-lock', setTimeout( function() {
                    $tableOfContent.tocify( 'option', 'showAndHideOnScroll', true );
                }, 2500 ) );
            } );
        }

        // Handle incoming hash URLs only if hash navigation is enabled
        if (settings && settings.hashNavigation) {
            handleHashOnLoad($tableOfContent);
        }

	};

    function handleHashOnLoad($tableOfContent) {
        var hash = window.location.hash;
        if (hash && hash.length > 1) {
            setTimeout(function() {
                var target = $('[name="' + hash.substring(1) + '"]');
                if (target.length) {
                    // Get scroll offset from TOC settings
                    var settings = $tableOfContent.data('settings');
                    var scrollOffset = settings ? (settings.scrollTo || 0) : 0;
                    
                    $('html, body').animate({
                        scrollTop: target.offset().top - scrollOffset
                    }, 800);
                }
            }, 1500);
        }
    }

	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-table-of-content.default', widgetTableOfContent );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End table of content widget script
 */



/**
 * Start table widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetTable = function ($scope, $) {

        var $tableContainer = $scope.find('.bdt-data-table'),
            $settings = $tableContainer.data('settings'),
            $table = $tableContainer.find('> table'),
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$tableContainer.length) {
            return;
        }

        $settings.language = window.ElementPackConfig.data_table.language;

        if (editMode) {
            DataTable.ext.errMode = function (s, tn, msg) {
                console.log(msg, tn);
            };
        }

        $table.DataTable($settings);

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-table.default', widgetTable);
    });

}(jQuery, window.elementorFrontend));

/**
 * End table widget script
 */
/**
 * Start tabs widget script
 */

(function ($, elementor) {
    'use strict';
    var widgetTabs = function ($scope, $) {
        const $tabsArea = $scope.find('.bdt-tabs-area'),
            $tabs = $tabsArea.find('.bdt-tabs'),
            $tab = $tabs.find('.bdt-tab'),
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$tabsArea.length) {
            return;
        }
        const $settings = $tabs.data('settings'),
            animTime = $settings.hashScrollspyTime,
            customOffset = $settings.hashTopOffset,
            navStickyOffset = $settings.navStickyOffset;

        if (navStickyOffset == 'undefined') {
            navStickyOffset = 10;
        }

        const reinitElementorTabContent = function ($panel) {
            if (!$panel || !$panel.length || typeof elementorFrontend === 'undefined') {
                return;
            }

            $panel.find('.elementor-element').each(function () {
                elementorFrontend.elementsHandler.runReadyTrigger($(this));
            });

            $panel.find('.swiper').each(function () {
                if (this.swiper && typeof this.swiper.update === 'function') {
                    this.swiper.update();
                }
            });

            window.dispatchEvent(new Event('resize'));
        };

        const $switcher = $tabsArea.find('.bdt-switcher.bdt-switcher-item-content');

        if ($switcher.length) {
            bdtUIkit.util.on($switcher, 'shown', function (event) {
                reinitElementorTabContent($(event.target));
            });

            if ($tab.length) {
                bdtUIkit.util.on($tab, 'shown', function () {
                    reinitElementorTabContent($switcher.children('.bdt-active').first());
                });
            }

            reinitElementorTabContent($switcher.children('.bdt-active').first());
        }

        $scope.find('.bdt-template-modal-iframe-edit-link').each(function () {
            var modal = $($(this).data('modal-element'));
            $(this).on('click', function (event) {
                bdtUIkit.modal(modal).show();
            });
            modal.on('beforehide', function () {
                window.parent.location.reload();
            });
        });


        function hashHandler($tabs, $tab, animTime, customOffset) {
            if (window.location.hash) {
                var currentHash = decodeURIComponent(window.location.hash.substring(1));

                var $targetTab = $($tabs).find('[data-title]').filter(function() {
                    var dataTitle = $.trim($(this).attr('data-title')).toLowerCase();
                    var hashValue = $.trim(currentHash).toLowerCase();
                    var matches = dataTitle === hashValue;
                    return matches;
                });
                
                
                if ($targetTab.length) {
                    var hashTarget = $targetTab.closest($tabs).attr('id');
                    $('html, body').animate({
                        easing: 'slow',
                        scrollTop: $('#' + hashTarget).offset().top - customOffset
                    }, animTime, function () {
                    }).promise().then(function () {
                        
                        try {
                            var tabInstance = bdtUIkit.tab($tab);
                            tabInstance.show($targetTab.data('tab-index'));
                        } catch{}
                    });
                }
            }
        }
        if ($settings.activeHash == 'yes' && $settings.status != 'bdt-sticky-custom') {
            $(document).ready(function () {
                setTimeout(function() {
                    hashHandler($tabs, $tab, animTime, customOffset);
                }, 100);
            });
            $(window).on('load', function () {
                setTimeout(function() {
                    hashHandler($tabs, $tab, animTime, customOffset);
                }, 100);
            });
            $($tabs).find('.bdt-tabs-item-title').off('click').on('click', function (event) {
                event.preventDefault();
                const title = $.trim($(this).attr('data-title'));
                const encoded = encodeURIComponent(title);
                window.location.hash = encoded;
                history.replaceState(null, null, '#' + title);
            });
            $(window).on('hashchange', function (e) {
                hashHandler($tabs, $tab, animTime, customOffset);
            });
        }
        function stickyHachChange($tabs, $tab, navStickyOffset) {
            var currentHash = window.location.hash.substring(1);
            var decodedHash = decodeURIComponent(currentHash);

            var $targetTab = $($tabs).find('[data-title]').filter(function() {
                return $(this).attr('data-title').toLowerCase() === decodedHash.toLowerCase();
            });
            
            if ($targetTab.length) {
                var hashTarget = $targetTab.closest($tabs).attr('id');
                $('html, body').animate({
                    easing: 'slow',
                    scrollTop: $('#' + hashTarget).offset().top - navStickyOffset
                }, 1000, function () {
                }).promise().then(function () {
                    bdtUIkit.tab($tab).show($targetTab.data('tab-index'));
                });
            }
        }
        if ($settings.status == 'bdt-sticky-custom') {
            $($tabs).find('.bdt-tabs-item-title').bind().click('click', function (event) {
                if ($settings.activeHash == 'yes') {
                    const title = $.trim($(this).attr('data-title'));
                    const encoded = encodeURIComponent(title);
                    window.location.hash = encoded;
                    history.replaceState(null, null, '#' + title);
                } else {
                    $('html, body').animate({
                        easing: 'slow',
                        scrollTop: $($tabs).offset().top - navStickyOffset
                    }, 500, function () {
                    });
                }
            });
            if ($settings.activeHash == 'yes' && $settings.status == 'bdt-sticky-custom') {
                $(document).ready(function () {
                    if (window.location.hash) {
                        setTimeout(function() {
                            stickyHachChange($tabs, $tab, navStickyOffset);
                        }, 100);
                    }
                });
                $(window).on('load', function () {
                    if (window.location.hash) {
                        setTimeout(function() {
                            stickyHachChange($tabs, $tab, navStickyOffset);
                        }, 100);
                    }
                });
                $(window).on('hashchange', function (e) {
                    stickyHachChange($tabs, $tab, navStickyOffset);
                });
            }
        }

        // start linkWidget


        var $linkWidget = $settings['linkWidgetSettings'],
            $activeItem = ($settings['activeItem']) - 1;
        if ($linkWidget !== undefined && editMode === false) {

            $linkWidget.forEach(function (entry, index) {

                if (index == 0) {
                    $('#bdt-tab-content-' + $settings['linkWidgetId']).parent().remove();
                    $(entry).parent().wrapInner('<div class="bdt-switcher-wrapper" />');
                    $(entry).parent().wrapInner('<div id="bdt-tab-content-' + $settings['linkWidgetId'] + '" class="bdt-switcher bdt-switcher-item-content" />');

                    if ($settings['activeItem'] == undefined) {
                        $(entry).addClass('bdt-active');
                    }
                }

                if ($settings['activeItem'] !== undefined && index == $activeItem) {
                    $(entry).addClass('bdt-active');
                }

                $(entry).attr('data-content-id', "tab-" + (index + 1));

            });

            /**
             * Sometimes not works UIKIT connect that's why below code
             */
            $tab.find('a').on('click', function () {
                let index = $(this).data('tab-index');
                const $panels = $('#bdt-tab-content-' + $settings['linkWidgetId'] + '>');
                $panels.removeClass('bdt-active');
                const $activePanel = $panels.eq(index).addClass('bdt-active');
                reinitElementorTabContent($activePanel);
            });

        }
        // end linkWidget

        if (typeof $settings.sectionBg != "undefined") {
            if (typeof $settings.sectionBgSelector == "undefined") {
                return;
            }
            var $id = (($settings.sectionBgSelector) + '-ep-dynamic').substring(1);

            if ($(`#${$id}-wrapper`).length) {
                $(`#${$id}-wrapper`).remove();
            }

            var dynamicBG = `<div id="${$id}-wrapper"  style = "position: absolute; z-index: 0; top: 0; right: 0; bottom: 0; left: 0;" >`;

            $($settings.sectionBg).each(function (e) {

                let newLine = '<div class="bdt-hidden ' + $id + ' bdt-animation-' + $settings.sectionBgAnim + '" style=" width: 100%; height: 100%; transition: all .5s;">';
                newLine += '<img src = "' + $settings.sectionBg[e] + '" style = " height: 100%; width: 100%; object-fit: cover;" >';
                newLine += '</div>';
                dynamicBG += newLine;
            });

            dynamicBG += `</div>`;

            $($settings.sectionBgSelector).prepend(dynamicBG);
            var activeIndex = $tab.find('>.bdt-tabs-item.bdt-active').index();
            $(`.${$id}:eq('${activeIndex}')`).removeClass('bdt-hidden');

            $tabsArea.find('.bdt-tabs-item-title').on('click', function () {
                let $tabImg = $(this).data('tab-index');
                $('.' + $id + ':eq(' + $tabImg + ')').siblings().addClass('bdt-hidden');
                $('.' + $id + ':eq(' + $tabImg + ')').removeClass('bdt-hidden');
            });

        }

        // start section link
        var $linkSection = $settings['linkSectionSettings'];
        if ($linkSection !== undefined && editMode === false) {
            $linkSection.forEach(function (entry, index) {
                let $tabContent = $('#bdt-tab-content-' + $settings.linkWidgetId),
                $section = $(entry);
                $tabContent.find('.bdt-tab-content-item' + ':eq(' + index + ')').html($section);
            });
        }

    };
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-tabs.default', widgetTabs);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-acf-tabs.default', widgetTabs);
    });
}(jQuery, window.elementorFrontend));

/**
 * End tabs widget script
 */
/**
 * Start tags cloud widget script
 */

(function($, elementor) {

    'use strict';

    var widgetTagsCloud = function($scope, $) {
        var $tags_cloud = $scope.find('.bdt-tags-cloud');
            
            if (!$tags_cloud.length) {
                return;
            }
            var $settings = $tags_cloud.data('settings');
            var $tags_color = $settings.basic_tags_bg_type;
            var tags_color_solid = $settings.basic_tags_solid_bg;
 

            jQuery.fn.prettyTag = function (options) {

                var setting = jQuery.extend({
                        randomColor: true,
                        tagicon: true,
                        tags_color: options.tags_color
                    }, options);


                return this.each(function () {
                    var target = this;
                        if (setting.tagicon == true) {
                            var eachTag = $(target).find("a");
                            var ti = document.createElement("i");
                            $($tags_cloud).find(ti).addClass("fas fa-tags").prependTo(eachTag);
                        }

                        if( setting.tags_color == 'random' ){
                            coloredTags();
                        }else{
                            if (typeof(tags_color_solid) != "undefined"){
                                $($tags_cloud).find('.bdt-tags-list li a').css('background-color', tags_color_solid); 
                            }else{
                               $($tags_cloud).find('.bdt-tags-list li a').css('background-color', '#3FB8FD'); 
                           }
                       }

                        function coloredTags() {

                        var totalTags = $($tags_cloud).find("li").length;
                        var mct = $($tags_cloud).find("a");
                        var tagColor = ["#ff0084", "#ff66ff", "#43cea2", "#D38312", "#73C8A9", "#9D50BB",
                        "#780206", "#FF4E50", "#ADD100",
                        "#0F2027", "#00c6ff", "#81D8D0", "#5CB3FF", "#95B9C7", "#C11B17", "#3B9C9C", "#FF7F50", "#FFD801", "#79BAEC", "#F660AB", "#3D3C3A", "#3EA055"
                        ];

                        var tag = 0;
                        var color = 0;
                        do {
                            if (color > 21) {
                                color = 0;
                        }

                        if (setting.randomColor == true) {
                            var $rc = Math.floor(Math.random() * 22);
                            $(mct).eq(tag).css({
                            'background': tagColor[$rc]
                        });
                        } else {
                            $(mct).eq(tag).css({
                        'background': tagColor[color]
                    });
                        }
                        tag++;
                        color++;
                    } while (tag <= totalTags)

                }
            });
            };

            $($tags_cloud).find(".bdt-tags-list").prettyTag({'tags_color': $tags_color});

        };


        var widgetSkinAnimated = function($scope, $) {
            var $tags_globe = $scope.find('.bdt-tags-cloud');
            if (!$tags_globe.length) {
                return;
            }
            var $settings = $tags_globe.data('settings');
            
            // Determine animation settings based on animation type
            var animationType = $settings.animationType || 'hover';
            var initial = null;
            var dragControl = $settings.dragControl || false;
            var freezeActive = $settings.freezeActive || false;
            
            // If "always" animation, set initial spin and disable freeze/drag
            if (animationType === 'always') {
                initial = [0.2, 0.1]; // Default spin values
                dragControl = false;
                freezeActive = false;
            }
            
            // Ensure maxSpeed has a minimum value for animation
            var maxSpeed = $settings.maxSpeed || 0.05;
            if (maxSpeed === 0) {
                maxSpeed = 0.05;
            }
 
                try {
                    TagCanvas.Start($settings.idmyCanvas, $settings.idTags, { 
                        textColour         :  $settings.textColour,
                        outlineColour      :  $settings.outlineColour,
                        reverse            :  true,
                        depth              :  $settings.depth, 
                        maxSpeed           :  maxSpeed,
                        initial            :  initial,
                        activeCursor       :  $settings.activeCursor,
                        bgColour           :  $settings.bgColour, 
                        bgOutlineThickness :  $settings.bgOutlineThickness, 
                        bgRadius           :  $settings.bgRadius, 
                        dragControl        :  dragControl, 
                        fadeIn             :  $settings.fadeIn, 
                        freezeActive       :  freezeActive,
                        outlineDash        :  $settings.outlineDash,
                        outlineDashSpace   :  $settings.globe_outline_dash_space,
                        outlineDashSpeed   :  $settings.globe_outline_dash_speed,
                        outlineIncrease    :  $settings.outlineIncrease,
                        outlineMethod      :  $settings.outlineMethod, 
                        outlineRadius      :  $settings.outlineRadius,
                        outlineThickness   :  $settings.outlineThickness,
                        shadow             :  $settings.shadow,
                        shadowBlur         :  $settings.shadowBlur,
                        wheelZoom          :  $settings.wheelZoom

                    });
                } catch (e) {
                    document.getElementById($settings.idCanvas).style.display = 'none';
                }
           
        };


        var widgetSkinCloud = function($scope, $) {
            var $tags_cloud = $scope.find('.bdt-tags-cloud');

            if (!$tags_cloud.length) {
                return;
            }
            var $settings = $tags_cloud.data('settings');
            var $container = $scope.find('#' + $settings.idCloud);

            function runAwesomeCloud() {
                if (!$container.length) {
                    return;
                }
                var w = $container.width();
                var h = $container.height();
                if (w <= 0 || h <= 0) {
                    setTimeout(runAwesomeCloud, 50);
                    return;
                }
                $container.awesomeCloud({
                    "size": {
                        "grid": 9,
                        "factor": 1
                    },
                    "color": {
                        "background": "rgba(156,145,255,0)",
                        "start": "#20f",
                        "end": "rgb(200,0,0)"
                    },
                    "options": {
                        "background": "rgba(165,184,255,0)",
                        "color": $settings.cloudColor,
                        "sort": "highest"
                    },
                    "font": "'Times New Roman', Times, serif",
                    "shape": $settings.cloudStyle
                });
            }

            function resizeAwesomeCloud() {
                $container.find('canvas').remove();
                $container.children().show();
                runAwesomeCloud();
            }

            jQuery(document).ready(function() {
                setTimeout(resizeAwesomeCloud, 0);
            });

            jQuery(window).on("resize", function() {
                var $canvas = $container.find('#awesomeCloud' + $settings.idCloud);
                if ($canvas.length) {
                    $canvas.remove();
                    $container.children().show();
                    setTimeout(runAwesomeCloud, 0);
                }
            });
        };


        jQuery(window).on('elementor/frontend/init', function() {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-tags-cloud.default', widgetTagsCloud);
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-tags-cloud.bdt-animated', widgetSkinAnimated); 
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-tags-cloud.bdt-cloud', widgetSkinCloud); 
        });

    }(jQuery, window.elementorFrontend));

/**
 * End tags cloud widget script
 */

 
/**
 * Start testimonial carousel widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetTCarousel = function( $scope, $ ) {

		var $tCarousel = $scope.find( '.bdt-testimonial-carousel' );
            
        if ( ! $tCarousel.length ) {
            return;
        }

		var $tCarouselContainer = $tCarousel.find('.swiper-carousel'),
			$settings 		 = $tCarousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        
        async function initSwiper() {

			await new Swiper($tCarouselContainer, $settings);

			if ($settings.pauseOnHover) {
				 $tCarouselContainer.hover(function() {
					(this).swiper.autoplay.stop();
				}, function() {
					(this).swiper.autoplay.start();
				});
			}
		};

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-testimonial-carousel.default', widgetTCarousel );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-testimonial-carousel.bdt-twyla', widgetTCarousel );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-testimonial-carousel.bdt-vyxo', widgetTCarousel );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End testimonial carousel widget script
 */


/**
 * Start testimonial slider widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetCustomCarousel = function( $scope, $ ) {

		var $carousel = $scope.find( '.bdt-testimonial-slider' );
				
        if ( ! $carousel.length ) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-carousel'),
			$settings 		 = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        
        async function initSwiper() {

			await new Swiper($carouselContainer, $settings);

			if ($settings.pauseOnHover) {
				 $carouselContainer.hover(function() {
					(this).swiper.autoplay.stop();
				}, function() {
					(this).swiper.autoplay.start();
				});
			}
		};

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-testimonial-slider.default', widgetCustomCarousel );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-testimonial-slider.bdt-single', widgetCustomCarousel );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End testimonial slider widget script
 */


/**
 * Start threesixty product viewer widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetTSProductViewer = function ($scope, $) {

        var $TSPV = $scope.find('.bdt-threesixty-product-viewer'),
            $settings = $TSPV.data('settings'),
            $container = $TSPV.find('> .bdt-tspv-container'),
            $fullScreenBtn = $TSPV.find('> .bdt-tspv-fb');

        if (!$TSPV.length) {
            return;
        }

        if ($settings.source_type === 'remote') {
            $settings.source = SpriteSpin.sourceArray($settings.source, { frame: $settings.frame_limit, digits: $settings.image_digits });
        }

        epObserveTarget($scope[0], function () {
            $container.spritespin($settings);
        });

        $fullScreenBtn.on('click', function (e) {
            e.preventDefault();
            $container.spritespin('api').requestFullscreen();
        });

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-threesixty-product-viewer.default', widgetTSProductViewer);
    });

}(jQuery, window.elementorFrontend));

/**
 * End threesixty product viewer widget script
 */


; (function ($, elementor) {
$(window).on('elementor/frontend/init', function () {
    let ModuleHandler = elementorModules.frontend.handlers.Base,
        TileScroll;

    TileScroll = ModuleHandler.extend({
        bindEvents: function () {
            this.run();
        },
        getDefaultSettings: function () {
            return {
                allowHTML: true,
            };
        },

        settings: function (key) {
            return this.getElementSettings('element_pack_tile_scroll_' + key);
        },

        run: function () {
            if (this.settings('show') == 'yes') {
                const widgetID = this.$element.data('id');
                const tileScroll_ID = 'bdt-tile-scroll-container-' + widgetID;
                if ($('#' + tileScroll_ID).length === 0) {
                    let display = this.settings('display');
                    let $content = `
                        <div id="${tileScroll_ID}" class="bdt-tile-scroll bdt-tile-scroll--${display}">
                            <div class="bdt-tile-scroll__wrap">`;
                    this.settings('elements').forEach(element => {
                        let images = element.element_pack_tile_scroll_images;

                        let x_start = element.element_pack_tile_scroll_x_start.size;
                        let x_end = element.element_pack_tile_scroll_x_end.size;
                        let parallax;
                        if (display === 'horizontal') {
                            parallax = 'data-bdt-parallax="target: .elementor-element-' + widgetID + '; viewport: 1.1; x:' + x_start + ',' + x_end + '"';
                        } else {
                            parallax = 'data-bdt-parallax="y:' + x_start + ',' + x_end + '"';
                        }
                        $content += `<div class="bdt-tile-scroll__line" ${parallax}>`;
                        images.forEach(image => {
                            $content += `<div class=" bdt-tile-scroll__line-img" style="background-image:url(${image.url})" loading="lazy"></div>`;
                        });
                        $content += `</div>`;
                    });
                    $content += `</div></div>`;

                    $('.elementor-element-' + widgetID).prepend($content);
                }
            }
        }
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/section', function ($scope) {
        if (!$scope.hasClass("bdt-tile-scroll-yes")) {
            return;
        }
        elementorFrontend.elementsHandler.addHandler(TileScroll, {
            $element: $scope
        });
    });
    elementorFrontend.hooks.addAction('frontend/element_ready/container', function ($scope) {
        if (!$scope.hasClass("bdt-tile-scroll-yes")) {
            return;
        }
        elementorFrontend.elementsHandler.addHandler(TileScroll, {
            $element: $scope
        });
    });
});
}) (jQuery, window.elementorFrontend);

/**
 * Start time zone widget script
 */

(function ($, elementor) {
    'use strict';
    var widgetTimeZone = function ($scope, $) {
        var $TimeZone = $scope.find('.bdt-time-zone'),
            $settings = $TimeZone.data('settings'),
            timeFormat,
            offset = $settings.gmt,
            dateFormat = $settings.dateFormat,
            enableDST = $settings.enableDST === 'yes';

        if (!$TimeZone.length) {
            return;
        }

        var timeZoneApp = {
            digitalClock: function () {
                if ($settings.timeHour == '12h') {
                    timeFormat = '%I:%M:%S %p';
                } else {
                    timeFormat = '%H:%M:%S';
                }
                var dateFormat = $settings.dateFormat;
                if (dateFormat != 'emptyDate') {
                    dateFormat = '<div class=\"bdt-time-zone-date\"> ' + $settings.dateFormat + ' </div>'
                } else {
                    dateFormat = '';
                }
                var country;
                if ($settings.country != 'emptyCountry') {
                    country = '<div  class=\"bdt-time-zone-country\">' + $settings.country + '</div>';
                } else {
                    country = ' ';
                }
                
                // Check if we should apply DST
                const currentDate = new Date();
                let finalOffset = offset;
                let dstIndicator = '';
                
                if (enableDST && this.isDSTActive(currentDate, offset)) {
                    // Add 1 hour for DST if not using local time
                    if (offset !== 'local') {
                        finalOffset = parseFloat(offset) + 1;
                    }
                    // Add DST indicator with consistent styling
                    dstIndicator = '<small class="bdt-dst-indicator" style="margin-left: 5px;">DST</small>';
                }
                
                var timeZoneFormat = '<div class=\"bdt-time-zone-dt\"> ' + country + ' ' + dateFormat + 
                                    ' <div class=\"bdt-time-zone-time\">' + timeFormat + dstIndicator + '</div> </div>';

                if (offset == '') return;
                
                var options = {
                    format: timeZoneFormat,
                    timeNotation: $settings.timeHour,
                    am_pm: true,
                    utc: (offset == 'local') ? false : true,
                    utcOffset: (offset == 'local') ? null : finalOffset,
                }

                $('#' + $settings.id).jclock(options);
            },
            isDSTActive: function(date, offset) {
                // If DST is disabled in settings, return false
                if (!enableDST) return false;
                
                // If using local time, check browser's DST detection
                if (offset === 'local') {
                    // Compare January and July to see if DST is observed
                    const jan = new Date(date.getFullYear(), 0, 1).getTimezoneOffset();
                    const jul = new Date(date.getFullYear(), 6, 1).getTimezoneOffset();
                    const isDstObserved = jan !== jul;
                    
                    if (!isDstObserved) return false;
                    
                    // If DST is observed, check if it's currently active
                    const currentOffset = date.getTimezoneOffset();
                    return currentOffset === Math.min(jan, jul);
                }
                
                // For specific timezones, use a more accurate approach
                // Numeric offset is assumed to be GMT+X or GMT-X
                
                const month = date.getMonth(); // 0-11
                const day = date.getDate();    // 1-31
                const numericOffset = parseFloat(offset);
                
                // General DST rules for major regions:
                
                // Northern Hemisphere (Europe, North America, Asia)
                // DST typically starts on last Sunday in March and ends on last Sunday in October
                if (numericOffset >= -12 && numericOffset <= 14) {
                    // Northern hemisphere (rough approximation)
                    if (numericOffset > 0) {
                        // March (2) after ~last Sunday to October (9) before ~last Sunday
                        if (month > 2 && month < 9) return true;
                        
                        // Edge cases: last week of March and last week of October
                        if (month === 2 && day >= 25) return true; // Approx last week of March
                        if (month === 9 && day <= 25) return true; // Approx last week of October
                    }
                    // Southern hemisphere (Australia, South America, South Africa, etc.)
                    else if (numericOffset < 0 && numericOffset >= -12) {
                        // September (8) after ~first Sunday to April (3) before ~first Sunday
                        if (month < 3 || month > 8) return true;
                        
                        // Edge cases: first week of April and last week of September
                        if (month === 3 && day <= 7) return true; // Approx first week of April
                        if (month === 8 && day >= 25) return true; // Approx last week of September
                    }
                }
                
                return false;
            },
            convertToTimeZoneAndFormat: function (date, offset) {
                const utcTime = date.getTime() + (date.getTimezoneOffset() * 60000);

                // Apply DST correction if enabled and active
                let dstOffset = 0;
                if (enableDST && this.isDSTActive(date, offset)) {
                    dstOffset = 1; // Add one hour for DST
                }

                // Calculate the target time using the offset and DST if applicable
                const targetTime = new Date(utcTime + ((parseFloat(offset) + dstOffset) * 3600000));

                let hours = targetTime.getHours(),
                    minutes = targetTime.getMinutes(),
                    seconds = targetTime.getSeconds();
                const ampm = hours >= 12 ? 'PM' : 'AM',
                    getDate = targetTime.toDateString();
                hours = hours % 12 || 12; // Convert to 12-hour format and handle midnight (0 AM)

                minutes = minutes < 10 ? '0' + minutes : minutes;
                seconds = seconds < 10 ? '0' + seconds : seconds;

                return {
                    hours,
                    minutes,
                    seconds,
                    ampm,
                    getDate,
                };
            },
            formatDate: function (inputDate, formatOption) {
                var date = new Date(inputDate),
                    selectedFormat = formatOption;

                if (!selectedFormat) {
                    console.error('Invalid format option');
                    return '';
                }

                var formattedDate = selectedFormat.replace(/%([a-zA-Z])/g, function (_, formatCode) {
                    switch (formatCode) {
                        case 'd':
                            return String(date.getDate()).padStart(2, '0');
                        case 'm':
                            return String(date.getMonth() + 1).padStart(2, '0');
                        case 'y':
                            return String(date.getFullYear()).slice(-2);
                        case 'Y':
                            return String(date.getFullYear());
                        case 'b':
                            return date.toLocaleString('default', {
                                month: 'short'
                            });
                        case 'a':
                            return date.toLocaleString('default', {
                                weekday: 'short'
                            });
                        default:
                            return formatCode;
                    }
                });

                return formattedDate;
            },
            date: function () {
                let localDate = new Date(),
                    targetOffset = offset,
                    result = timeZoneApp.convertToTimeZoneAndFormat(localDate, targetOffset),
                    date = result.getDate;

                const formattedDate = this.formatDate(date, dateFormat);
                $($TimeZone).find('.bdt-time-zone-date').text(formattedDate);
            },
            updateTime: function () {
                const self = this;
                
                setInterval(function () {
                    let localDate = new Date(),
                        targetOffset = ('local' === offset) ? localDate.getTimezoneOffset() / -60 : offset,
                        result = timeZoneApp.convertToTimeZoneAndFormat(localDate, targetOffset);

                    let second = result.seconds * 6,
                        minute = result.minutes * 6 + second / 60,
                        hour = ((result.hours % 12) / 12) * 360 + 90 + minute / 12;

                    $($TimeZone).find('.bdt-clock-hour').css("transform", "rotate(" + hour + "deg)");
                    $($TimeZone).find('.bdt-clock-minute').css("transform", "rotate(" + minute + "deg)");
                    $($TimeZone).find('.bdt-clock-second').css("transform", "rotate(" + second + "deg)");
                    $($TimeZone).find('.bdt-clock-am-pm').text(result.ampm);
                    
                    // Add or remove DST indicator for analog clock
                    const isDstActive = self.isDSTActive(localDate, targetOffset);
                    const $dstIndicator = $($TimeZone).find('.bdt-dst-indicator');
                    
                    if (isDstActive && enableDST) {
                        if ($dstIndicator.length === 0) {
                            const $indicator = $('<small class="bdt-dst-indicator" style="margin-left: 5px;">DST</small>');
                            $($TimeZone).find('.bdt-clock-am-pm').append($indicator);
                        }
                    } else {
                        $dstIndicator.remove();
                    }

                }, 1000);

                this.date();
            },
            init: function () {
                if ('digital' == $settings.clock_style) {
                    this.digitalClock();
                } else {
                    this.updateTime();
                }
            }
        }

        epObserveTarget($scope[0], function () {
            timeZoneApp.init();
        });
    };
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-time-zone.default', widgetTimeZone);
    });
}(jQuery, window.elementorFrontend));

/**
 * End time zone widget script
 */

/**
 * Start timeline widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetTimeline = function( $scope, $ ) {

		var $timeline = $scope.find( '.bdt-timeline-skin-olivier' );
				
        if ( ! $timeline.length ) {
            return;
        }

        $($timeline).timeline({
            visibleItems : $timeline.data('visible_items'),
        });

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-timeline.bdt-olivier', widgetTimeline );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End timeline widget script
 */


/**
 * Start advanced counter widget script
 */

;(function($, elementor) {
    'use strict';
    var widgetAdvancedCounter = function($scope, $) {
        var $AdvancedCounter = $scope.find('.bdt-advanced-counter');
        if (!$AdvancedCounter.length) {
            return;
        }

        epObserveTarget($scope[0], function () {

            var $settings = $($AdvancedCounter).data('settings');

            var options = {
                startVal: $settings.countStart ?? 0,
                numerals: $settings.language,
                decimalPlaces: $settings.decimalPlaces ?? 0,
                duration: $settings.duration ?? 0,
                useEasing: $settings.useEasing !== null,
                useGrouping: $settings.useGrouping !== null,
                separator: $settings.counterSeparator ?? '',
                decimal: $settings.decimalSymbol ?? '',
                prefix: $settings.counterPrefix ?? '',
                suffix: $settings.counterSuffix ?? '',
            };

            var demo = new CountUp($settings.id, $settings.countNumber ?? 0, options);
            if (!demo.error) {
                demo.start();
            } else {
                console.error(demo.error);
            }

        }, {
            root: null,
            rootMargin: '0px',
            threshold: 0.8
        });

    };
    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-total-count.default', widgetAdvancedCounter);
    });
}(jQuery, window.elementorFrontend));

/**
 * End advanced counter widget script
 */


/**
 * Start LearnPress carousel widget script
 */

(() => {
    'use strict';

    const widgetLearnpressCarousel = async (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const carouselEl = scopeEl.querySelector('.ep-learnpress-carousel');
        if (!carouselEl) return;

        const containerEl = carouselEl.querySelector('.swiper-carousel');
        if (!containerEl) return;

        const settings = JSON.parse(carouselEl.dataset.settings || '{}');
        const Swiper = elementorFrontend?.utils?.swiper;
        if (!Swiper) return;
        await new Swiper(containerEl, settings);

        if (settings.pauseOnHover) {
            containerEl.addEventListener('mouseenter', () => containerEl.swiper?.autoplay?.stop());
            containerEl.addEventListener('mouseleave', () => containerEl.swiper?.autoplay?.start());
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-learnpress-carousel.default', widgetLearnpressCarousel);
    });
})();

/**
 * End LearnPress carousel widget script
 */

/**
 * Start twitter carousel widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetTwitterCarousel = function( $scope, $ ) {

		var $twitterCarousel = $scope.find( '.bdt-twitter-carousel' );
				
        if ( ! $twitterCarousel.length ) {
            return;
        }

		var $twitterCarouselContainer = $twitterCarousel.find('.swiper-carousel'),
			$settings 		 = $twitterCarousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();

        async function initSwiper() {

			await new Swiper($twitterCarouselContainer, $settings);

			if ($settings.pauseOnHover) {
				 $($twitterCarouselContainer).hover(function() {
					(this).swiper.autoplay.stop();
				}, function() {
					(this).swiper.autoplay.start();
				});
			}
		};
	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-twitter-carousel.default', widgetTwitterCarousel );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End twitter carousel widget script
 */


/**
 * Start twitter slider widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetTwitterSlider = function( $scope, $ ) {

		var $twitterSlider = $scope.find( '.bdt-twitter-slider' );
				
        if ( ! $twitterSlider.length ) {
            return;
        }

		var $twitterSliderContainer = $twitterSlider.find('.swiper-carousel'),
			$settings 		 = $twitterSlider.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();

        async function initSwiper() {

			await new Swiper($twitterSliderContainer, $settings);

			if ($settings.pauseOnHover) {
				 $($twitterSliderContainer).hover(function() {
					(this).swiper.autoplay.stop();
				}, function() {
					(this).swiper.autoplay.start();
				});
			}
		};
	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-twitter-slider.default', widgetTwitterSlider );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End twitter slider widget script
 */


/**
 * Start user login widget script
 */

(function ($, elementor) {
  "use strict";

  window.is_fb_loggedin = false;
  window.is_google_loggedin = false;

  var widgetUserLoginForm = {
    loginFormSubmission: function (login_form) {
      var redirect_url = login_form.find(".redirect_after_login").val();

      $.ajax({
        type: "POST",
        dataType: "json",
        url: element_pack_ajax_login_config.ajaxurl,
        data: login_form.serialize(),
        beforeSend: function (xhr) {
          bdtUIkit.notification({
            message:
              "<div bdt-spinner></div> " +
              element_pack_ajax_login_config.loadingmessage,
            timeout: false,
          });
        },
        success: function (data) {
          var recaptcha_field = login_form.find(
            ".element-pack-google-recaptcha"
          );
          if (recaptcha_field.length > 0) {
            var recaptcha_id = recaptcha_field.attr("data-widgetid");
            grecaptcha.reset(recaptcha_id);
            grecaptcha.execute(recaptcha_id);
          }

          if (data.loggedin == true) {
            bdtUIkit.notification.closeAll();
            bdtUIkit.notification({
              message: "<span bdt-icon='icon: check'></span> " + data.message,
              status: "primary",
            });
            document.location.href = redirect_url;
          } else {
            bdtUIkit.notification.closeAll();
            bdtUIkit.notification({
              message:
                "<div class=\"bdt-flex\"><span bdt-icon='icon: warning'></span><span>" +
                data.message +
                "</span></div>",
              status: "warning",
            });
          }
        },
        error: function (data) {
          bdtUIkit.notification.closeAll();
          bdtUIkit.notification({
            message:
              "<span bdt-icon='icon: warning'></span>" +
              element_pack_ajax_login_config.unknownerror,
            status: "warning",
          });
        },
      });
    },
    get_facebook_user_data: function (widget_wrapper) {
      var redirect_url = widget_wrapper.find(".redirect_after_login").val();

      FB.api(
        "/me",
        {
          fields:
            "id, name, first_name, last_name, email, link, gender, locale, picture",
        },
        function (response) {
          var userID = FB.getAuthResponse()["userID"];
          var access_token = FB.getAuthResponse()["accessToken"];

          window.is_fb_loggedin = true;

          var fb_data = {
            id: response.id,
            name: response.name,
            first_name: response.first_name,
            last_name: response.last_name,
            email: response.email,
            link: response.link,
          };

          $.ajax({
            url: window.ElementPackConfig.ajaxurl,
            method: "post",
            data: {
              action: "element_pack_social_facebook_login",
              data: fb_data,
              method: "post",
              dataType: "json",
              userID: userID,
              security_string: access_token,
              lang: element_pack_ajax_login_config.language,
            },
            dataType: "json",
            beforeSend: function (xhr) {
              bdtUIkit.notification({
                message:
                  "<div bdt-spinner></div> " +
                  element_pack_ajax_login_config.loadingmessage,
                timeout: false,
              });
            },
            success: function (data) {
              if (data.success === true) {
                if (undefined === redirect_url) {
                  location.reload();
                } else {
                  window.location = redirect_url;
                }
              } else {
                location.reload();
              }
            },
            complete: function (xhr, status) {
              bdtUIkit.notification.closeAll();
            },
          });
        }
      );
    },

    load_recaptcha: function () {
      var reCaptchaFields = $(".element-pack-google-recaptcha"),
        widgetID;

      if (reCaptchaFields.length > 0) {
        reCaptchaFields.each(function () {
          var self = $(this),
            attrWidget = self.attr("data-widgetid");
          // Avoid re-rendering as it's throwing API error
          if (typeof attrWidget !== typeof undefined && attrWidget !== false) {
            return;
          } else {
            widgetID = grecaptcha.render($(this).attr("id"), {
              sitekey: self.data("sitekey"),
              callback: function (response) {
                if (response !== "") {
                  self.append(
                    jQuery("<input>", {
                      type: "hidden",
                      value: response,
                      class: "g-recaptcha-response",
                    })
                  );
                }
              },
            });
            self.attr("data-widgetid", widgetID);
          }
        });
      }
    },
  };

  window.onLoadElementPackLoginCaptcha = widgetUserLoginForm.load_recaptcha;

  var widgetUserLoginFormHandler = function ($scope, $) {
    var widget_wrapper = $scope.find(".bdt-user-login");
    var login_form = $scope.find("form.bdt-user-login-form");
    var recaptcha_field = $scope.find(".element-pack-google-recaptcha");
    var fb_button = widget_wrapper.find(".fb_btn_link");
    var google_button = widget_wrapper.find("#google_btn_link");
    var redirect_url = widget_wrapper.find(".redirect_after_login").val();

    if (login_form.length > 0) {
      login_form.on("submit", function (e) {
        e.preventDefault();
        widgetUserLoginForm.loginFormSubmission(login_form);
      });
    }

    if (
      elementorFrontend.isEditMode() &&
      undefined === recaptcha_field.attr("data-widgetid")
    ) {
      onLoadElementPackLoginCaptcha();
    }

    if (recaptcha_field.length > 0) {
      grecaptcha.ready(function () {
        var recaptcha_id = recaptcha_field.attr("data-widgetid");
        grecaptcha.execute(recaptcha_id);
      });
    }

    if (fb_button.length > 0) {
      fb_button.on("click", function () {
        if (!is_fb_loggedin) {
          FB.login(
            function (response) {
              if (response.authResponse) {
                widgetUserLoginForm.get_facebook_user_data(widget_wrapper);
              }
            },
            { scope: "email" }
          );
        }
      });
    }

    if (google_button.length > 0) {
      var client_id = google_button.data("clientid");

      gapi.load("auth2", function () {
        var auth2 = gapi.auth2.init({
          client_id: client_id,
          cookiepolicy: "single_host_origin",
        });

        auth2.attachClickHandler(
          "google_btn_link",
          {},
          function (googleUser) {
            var profile = googleUser.getBasicProfile();
            var name = profile.getName();
            var email = profile.getEmail();

            if (window.is_google_loggedin) {
              var id_token = googleUser.getAuthResponse().id_token;

              $.ajax({
                url: window.ElementPackConfig.ajaxurl,
                method: "post",
                data: {
                  action: "element_pack_social_google_login",
                  id_token: id_token,
                },
                dataType: "json",
                beforeSend: function (xhr) {
                  bdtUIkit.notification({
                    message:
                      "<div bdt-spinner></div> " +
                      element_pack_ajax_login_config.loadingmessage,
                    timeout: false,
                  });
                },
                success: function (data) {
                  if (data.success === true) {
                    if (undefined === redirect_url) {
                      location.reload();
                    } else {
                      window.location = redirect_url;
                    }
                  }
                },
                complete: function (xhr, status) {
                  bdtUIkit.notification.closeAll();
                },
              });
            }
          },
          function (error) {
          }
        );
      });

      google_button.on("click", function () {
        window.is_google_loggedin = true;
      });
    }
  };

  // Password visibility toggle icon
  $(document).on(
    "click",
    ".bdt-user-login .bdt-toggle-pass-wrapper",
    function () {
      var input = $(this).prev("input");
      var icon = $(this).find("i");

      if ("password" === input.attr("type")) {
        icon.removeClass("ep-icon-eye-blocked").addClass("ep-icon-eye");
      } else {
        icon.removeClass("ep-icon-eye").addClass("ep-icon-eye-blocked");
      }
    }
  );

  // AJAX logout when custom redirect is set (allows external URLs; wp_validate_redirect blocks them).
  $(document).on("click", ".bdt-ep-logout-ajax", function (e) {
    e.preventDefault();
    var $el = $(this);
    var url = $el.data("redirect");
    var nonce = $el.data("nonce");
    if (!url) return;
    var ajaxurl =
      typeof element_pack_ajax_login_config !== "undefined" &&
      element_pack_ajax_login_config.ajaxurl
        ? element_pack_ajax_login_config.ajaxurl
        : typeof ElementPackConfig !== "undefined" && ElementPackConfig.ajaxurl
        ? ElementPackConfig.ajaxurl
        : "";
    if (!ajaxurl) {
      location.reload();
      return;
    }
    $.post(ajaxurl, { action: "element_pack_ajax_logout", nonce: nonce })
      .done(function (res) {
        if (res && res.success) {
          window.location.href = url;
        } else {
          location.reload();
        }
      })
      .fail(function () {
        location.reload();
      });
  });

  jQuery(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-user-login.default",
      widgetUserLoginFormHandler
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-user-login.bdt-dropdown",
      widgetUserLoginFormHandler
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-user-login.bdt-modal",
      widgetUserLoginFormHandler
    );
  });
})(jQuery, window.elementorFrontend);

/**
 * End user login widget script
 */

/**
 * Start vertical menu widget script
 */

(function ($, elementor) {
    'use strict';
    // Vertical Menu
    var widgetVerticalMenu = function ($scope, $) {
        var $vrMenu = $scope.find('.bdt-vertical-menu');
        if (!$vrMenu.length) {
            return;
        }

        var $settings = $vrMenu.data('settings');
        var $menu = $('#' + $settings.id);

        // Handle separate arrow clicks for inner submenu type
        if ($settings.submenuType === 'inner') {
            // Don't initialize metisMenu for inner type, handle manually
            $($vrMenu).find('.bdt-menu-arrow').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $arrow = $(this);
                var $parent = $arrow.closest('li');
                var $submenu = $parent.find('> ul');
                
                if (!$submenu.length) return;

                // Close other open submenus at the same level
                var $siblings = $parent.siblings();
                $siblings.each(function() {
                    var $siblingSubmenu = $(this).find('> ul.mm-show');
                    if ($siblingSubmenu.length) {
                        var siblingHeight = $siblingSubmenu[0].scrollHeight;
                        $siblingSubmenu.removeClass('mm-show').addClass('mm-collapsing').css('height', siblingHeight + 'px');
                        
                        // Force reflow
                        $siblingSubmenu[0].offsetHeight;
                        
                        setTimeout(function() {
                            $siblingSubmenu.css('height', '0px');
                        }, 10);
                        
                        setTimeout(function() {
                            $siblingSubmenu.removeClass('mm-collapsing').addClass('mm-collapse').css('height', '');
                        }, 360);
                        
                        $(this).removeClass('mm-active');
                    }
                });
                
                // Toggle the current submenu with animation
                if ($submenu.hasClass('mm-show')) {
                    // Closing - remove mm-active immediately for arrow rotation
                    $parent.removeClass('mm-active');
                    var currentHeight = $submenu[0].scrollHeight;
                    $submenu.removeClass('mm-show').addClass('mm-collapsing').css('height', currentHeight + 'px');
                    
                    // Force reflow to ensure the height is set before animating
                    $submenu[0].offsetHeight;
                    
                    setTimeout(function() {
                        $submenu.css('height', '0px');
                    }, 10);
                    
                    setTimeout(function() {
                        $submenu.removeClass('mm-collapsing').addClass('mm-collapse').css('height', '');
                    }, 360);
                } else {
                    // Opening - add mm-active immediately for arrow rotation
                    $parent.addClass('mm-active');
                    $submenu.removeClass('mm-collapse').addClass('mm-collapsing');
                    $submenu.css('height', '0px');
                    
                    var targetHeight = $submenu[0].scrollHeight;
                    
                    setTimeout(function() {
                        $submenu.css('height', targetHeight + 'px');
                    }, 10);
                    
                    setTimeout(function() {
                        $submenu.removeClass('mm-collapsing').addClass('mm-show');
                        $submenu.css('height', '');
                    }, 360);
                }
            });
        } else {
            // Initialize metisMenu for outer type
            $menu.metisMenu();
        }

        if ('yes' == $settings.removeParentLink) {
            $($vrMenu).find('.has-arrow').attr('href', 'javascript:void(0);')
        }
    }
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-vertical-menu.default', widgetVerticalMenu);
    });


}(jQuery, window.elementorFrontend));

/**
 * End vertical menu widget script
 */
/**
 * Start video gallery widget script
 */

(function ($, elementor) {

	'use strict';

	var widgetVideoGallery = function ($scope, $) {

		var $video_gallery = $scope.find('.rvs-container');

		if (!$video_gallery.length) {
			return;
		}

		$($video_gallery).rvslider();

		// Fix: Astra and themes with deferred layout – trigger resize after layout settles
		// so rvslider recalculates dimensions (fixes playlist not scrolling until window resize)
		function triggerResize() {
			var instance = $video_gallery.data('__RVSlider__');
			if (instance && typeof instance.resize === 'function') {
				instance.resize();
			}
		}
		setTimeout(triggerResize, 150);
		setTimeout(triggerResize, 500);

	};


	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-video-gallery.default', widgetVideoGallery);
	});

}(jQuery, window.elementorFrontend));

/**
 * End video gallery widget script
 */
(function ($, elementor) {
  "use strict";
  $(window).on("elementor/frontend/init", function () {
    var ModuleHandler = elementorModules.frontend.handlers.Base,
      WCProducts;

    WCProducts = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },

      getDefaultSettings: function () {
        return {};
      },

      onElementChange: debounce(function (prop) {
        if (prop.indexOf("wc") !== -1) {
          this.run();
        }
      }, 400),

      settings: function (key) {
        return this.getElementSettings("wc_products_" + key);
      },

      run: function () {
        const options = this.getDefaultSettings(),
         content = this.settings("enable_ajax_loadmore"),
         container = this.$element.find(".bdt-wc-products");

        if (!container.length || content === undefined) {
          return;
        }

        const settingsLoadmore = this.settings("show_loadmore"),
         settingsInfiniteScroll = this.settings("show_infinite_scroll"),
         loadButtonContainer = this.$element.find(".bdt-loadmore-container"),
         products = container.find(".bdt-wc-products-wrapper"),
         loadButton = loadButtonContainer.find(".bdt-loadmore");
        let loading = false;
        const settings = container.data("settings");
        let currentItemCount = Number(settings.posts_per_page);

        const loadMorePosts = () => {
          const dataSettings = {
            action: "bdt_ep_wc_products_load_more",
            settings: settings,
            per_page: settings.ajax_item_load,
            offset: currentItemCount,
            nonce: settings.nonce,
            paged: settings.paged,
          };

          $.ajax({
            url: window.ElementPackConfig.ajaxurl,
            type: "post",
            data: dataSettings,
            success: (response) => {
              $(products).append(response.markup);
              currentItemCount += settings.ajax_item_load;
              settings.paged += 1;
              loading = false;

              if (settingsLoadmore === "yes") {
                loadButton.html("Load More");
              }

              if ($(response.markup).length < settings.ajax_item_load) {
                loadButton.hide();
                loadButtonContainer.hide();
              }
            },
          });
        };

        const handleButtonClick = () => {
          if (!loading) {
            loading = true;
            loadButton.html("Loading...");
            loadMorePosts();
          }
        };

        if (settingsLoadmore === "yes") {
          $(loadButton).on("click", handleButtonClick);
        }

        if (settingsInfiniteScroll === "yes") {
          $(window).scroll(() => {
            if (
              $(window).scrollTop() ===
                $(document).height() - $(window).height() &&
              !loading
            ) {
              $(loadButton).css("display", "block");
              loading = true;
              loadMorePosts();
            }
          });
        }
      },
    });
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-wc-products.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(WCProducts, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);

/**
 * Start weather widget script
 */

(function ($, elementor) {
    'use strict';
    var widgetWeather = function ($scope, $) {
        var $weatherContainer = $scope.find('.bdt-weather');
        if (!$weatherContainer.length) {
            return;
        }
        var $settings = $weatherContainer.data('settings');

        if ($settings.dynamicBG !== false) {
            $($weatherContainer).css('background-image', 'url(' + $settings.url + ')');
            $($weatherContainer).css({
                'background-size': 'cover',
                'background-position': 'center center',
                'background-repeat': 'no-repeat'
            });
        }

    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-weather.default', widgetWeather);
    });

}(jQuery, window.elementorFrontend));

/**
 * End weather widget script
 */

/**
 * Start remote arrows widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetRemoteArrows = function ($scope, $) {
        var $remoteArrows = $scope.find('.bdt-remote-arrows'),
            $settings = $remoteArrows.data('settings'),
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$remoteArrows.length) {
            return;
        }

        if (!$settings.remoteId) {
            var $parentSection = $scope.closest('.elementor-section, .e-con .e-con-inner');
            $settings['remoteId'] = $parentSection;
        }

        if ($($settings.remoteId).find('.swiper').length <= 0) {
            if (editMode == true) {
                $($settings.id + '-notice').removeClass('bdt-hidden');
            }
            return;
        }

        $($settings.id + '-notice').addClass('bdt-hidden');

        $(document).ready(function () {
            setTimeout(() => {
                const swiperInstance = $($settings.remoteId).find('.swiper')[0].swiper;

                $($settings.id).find('.bdt-prev').on("click", function () {
                    swiperInstance.slidePrev();
                });

                $($settings.id).find('.bdt-next').on("click", function () {
                    swiperInstance.slideNext();
                });

            }, 3000);

        });

    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-remote-arrows.default', widgetRemoteArrows);
    });

}(jQuery, window.elementorFrontend));

/**
 * End remote arrows widget script
 */
/**
 * Start remote thumbs widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetRemoteThumbs = function ($scope, $) {
        var $remoteThumbs = $scope.find('.bdt-remote-thumbs'),
            $settings = $remoteThumbs.data('settings'),
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$remoteThumbs.length) {
            return;
        }

        if (!$settings.remoteId) {
            var $parentSection = $scope.closest('.elementor-section, .e-con .e-con-inner');
            $settings['remoteId'] = $parentSection;
        }

        if ($($settings.remoteId).find('.swiper').length <= 0) {
            if (editMode == true) {
                $($settings.id + '-notice').removeClass('bdt-hidden');
            }
            return;
        }

        $($settings.id + '-notice').addClass('bdt-hidden');

        function getSwiperSlideCount(swiperInstance) {
            if (!swiperInstance.slides || !swiperInstance.slides.length) return 0;
            if (swiperInstance.params.loop) {
                var maxIndex = -1;
                for (var i = 0; i < swiperInstance.slides.length; i++) {
                    var idx = swiperInstance.slides[i].getAttribute('data-swiper-slide-index');
                    if (idx !== null && idx !== undefined) {
                        maxIndex = Math.max(maxIndex, parseInt(idx, 10));
                    }
                }
                return maxIndex >= 0 ? maxIndex + 1 : swiperInstance.slides.length;
            }
            return swiperInstance.slides.length;
        }

        function getSlideImageSrc(swiperInstance, realIndex) {
            var slideEl = null;
            if (swiperInstance.params.loop) {
                for (var s = 0; s < swiperInstance.slides.length; s++) {
                    var idx = swiperInstance.slides[s].getAttribute('data-swiper-slide-index');
                    if (idx !== null && parseInt(idx, 10) === realIndex) {
                        slideEl = swiperInstance.slides[s];
                        break;
                    }
                }
            }
            if (!slideEl && swiperInstance.slides[realIndex]) {
                slideEl = swiperInstance.slides[realIndex];
            }
            if (!slideEl) return '';
            var $img = $(slideEl).find('img').first();
            return $img.length ? ($img.attr('src') || '') : '';
        }

        function initRemoteThumbs(swiperInstance) {
            var $wrapper = $($settings.id).find('.bdt-thumbs-wrapper');
            var isAutomatic = $settings.thumbsType === 'automatic';

            if (isAutomatic) {
                var slideCount = getSwiperSlideCount(swiperInstance);
                var placeholderSrc = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
                $wrapper.empty();
                for (var i = 0; i < slideCount; i++) {
                    var imgSrc = getSlideImageSrc(swiperInstance, i) || placeholderSrc;
                    $wrapper.append(
                        '<a href="javascript:void(0);" class="bdt-item" data-index="' + i + '">' +
                        '<img src="' + imgSrc + '" alt="" />' +
                        '</a>'
                    );
                }
            }

            var $slideActive = $($settings.remoteId).find('.swiper-slide-active');
            var realIndex = $slideActive.data('swiper-slide-index');
            if (typeof realIndex === 'undefined') {
                realIndex = $slideActive.index();
            }

            $($settings.id).find('.bdt-item:eq(' + realIndex + ')').addClass('bdt-active');

            $($settings.id).find('.bdt-item').on("click", function () {
                var index = $(this).data('index');

                if ($settings.loopStatus) {
                    swiperInstance.slideToLoop(index);
                } else {
                    swiperInstance.slideTo(index);
                }

                $($settings.id).find('.bdt-item').removeClass('bdt-active');
                $($settings.id).find('.bdt-item:eq(' + index + ')').addClass('bdt-active');
                $($settings.id).addClass('wait--');

            });

            swiperInstance.on('slideChangeTransitionEnd', function () {
                if ($($settings.id).hasClass('wait--')) {
                    $($settings.id).removeClass('wait--');
                    return;
                } else {
                    $($settings.id).find('.bdt-item').removeClass('bdt-active');
                    $($settings.id).find('.bdt-item:eq(' + swiperInstance.realIndex + ')').addClass('bdt-active');
                }

            });
        }

        var maxAttempts = 100;
        var pollInterval = 100;
        var attempt = 0;

        function tryInit() {
            var swiperEl = $($settings.remoteId).find('.swiper')[0];
            var swiperInstance = swiperEl && swiperEl.swiper;
            if (swiperInstance) {
                if (swiperInstance.initialized) {
                    initRemoteThumbs(swiperInstance);
                } else {
                    swiperInstance.once('init', function () {
                        initRemoteThumbs(swiperInstance);
                    });
                }
                return;
            }
            attempt++;
            if (attempt < maxAttempts) {
                setTimeout(tryInit, pollInterval);
            }
        }

        tryInit();
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-remote-thumbs.default', widgetRemoteThumbs);
    });

}(jQuery, window.elementorFrontend));

/**
 * End remote thumbs widget script
 */
/**
 * Start remote pagination widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetRemotePagination = function ($scope, $) {
        var $remotePagination = $scope.find('.bdt-remote-pagination'),
            $settings = $remotePagination.data('settings'),
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$remotePagination.length) {
            return;
        }

        if (!$settings.remoteId) {
            var $parentSection = $scope.closest('.elementor-section, .e-con .e-con-inner');

            $settings['remoteId'] = $parentSection;
        }

        if ($($settings.remoteId).find('.swiper').length <= 0) {
            if (editMode == true) {
                $($settings.id + '-notice').removeClass('bdt-hidden');
            }
            return;
        }

        $($settings.id + '-notice').addClass('bdt-hidden');

        function initRemotePagination(swiperInstance) {
            var $wrapper = $($settings.id).find('.bdt-pagination-wrapper');
            var isAutomatic = $settings.paginationType === 'automatic';

            if (isAutomatic) {
                var slideCount = getSwiperSlideCount(swiperInstance);
                $wrapper.empty();
                for (var i = 0; i < slideCount; i++) {
                    $wrapper.append(
                        '<a href="javascript:void(0);" class="bdt-item" data-index="' + i + '">' +
                        '<div class="bdt-pagination">' + (i + 1) + '</div></a>'
                    );
                }
            }

            var $slideActive = $($settings.remoteId).find('.swiper-slide-active');
            var realIndex = $slideActive.data('swiper-slide-index');
            if (typeof realIndex === 'undefined') {
                realIndex = $slideActive.index();
            }

            $($settings.id).find('.bdt-item:eq(' + realIndex + ')').addClass('bdt-active');

            $($settings.id).find('.bdt-item').on("click", function () {
                var index = $(this).data('index');

                if ($settings.loopStatus) {
                    swiperInstance.slideToLoop(index);
                } else {
                    swiperInstance.slideTo(index);
                }

                $($settings.id).find('.bdt-item').removeClass('bdt-active');
                $($settings.id).find('.bdt-item:eq(' + index + ')').addClass('bdt-active');
                $($settings.id).addClass('wait--');

            });

            swiperInstance.on('slideChangeTransitionEnd', function (e) {
                if ($($settings.id).hasClass('wait--')) {
                    $($settings.id).removeClass('wait--');
                    return;
                } else {
                    $($settings.id).find('.bdt-item').removeClass('bdt-active');
                    $($settings.id).find('.bdt-item:eq(' + swiperInstance.realIndex + ')').addClass('bdt-active');
                }

            });
        }

        var maxAttempts = 100;
        var pollInterval = 100;
        var attempt = 0;

        function tryInit() {
            var swiperEl = $($settings.remoteId).find('.swiper')[0];
            var swiperInstance = swiperEl && swiperEl.swiper;
            if (swiperInstance) {
                if (swiperInstance.initialized) {
                    initRemotePagination(swiperInstance);
                } else {
                    swiperInstance.once('init', function () {
                        initRemotePagination(swiperInstance);
                    });
                }
                return;
            }
            attempt++;
            if (attempt < maxAttempts) {
                setTimeout(tryInit, pollInterval);
            }
        }

        tryInit();

        function getSwiperSlideCount(swiperInstance) {
            if (swiperInstance.params.loop && swiperInstance.slides && swiperInstance.slides.length) {
                var maxIndex = -1;
                for (var i = 0; i < swiperInstance.slides.length; i++) {
                    var idx = swiperInstance.slides[i].getAttribute('data-swiper-slide-index');
                    if (idx !== null && idx !== undefined) {
                        maxIndex = Math.max(maxIndex, parseInt(idx, 10));
                    }
                }
                return maxIndex >= 0 ? maxIndex + 1 : swiperInstance.slides.length;
            }
            return swiperInstance.slides ? swiperInstance.slides.length : 0;
        }
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-remote-pagination.default', widgetRemotePagination);
    });

}(jQuery, window.elementorFrontend));

/**
 * End remote pagination widget script
 */
/**
 * Start remote fraction widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetRemoteFraction = function ($scope, $) {
        var $remoteFraction = $scope.find('.bdt-remote-fraction'),
            $settings = $remoteFraction.data('settings'),
            $pad = $settings.pad,
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$remoteFraction.length) {
            return;
        }

        if (!$settings.remoteId) {
            var $parentSection = $scope.closest('.elementor-section, .e-con .e-con-inner');

            $settings['remoteId'] = $parentSection;
        }

        if ($($settings.remoteId).find('.swiper').length <= 0) {
            if (editMode == true) {
                $($settings.id + '-notice').removeClass('bdt-hidden');
            }
            return;
        }

        $($settings.id + '-notice').addClass('bdt-hidden');

        function initRemoteFraction(swiperInstance) {
            var $slideActive = $($settings.remoteId).find('.swiper-slide-active');
            var realIndex = $slideActive.data('swiper-slide-index');
            if (typeof realIndex === 'undefined') {
                realIndex = $slideActive.index();
            }

            var $totalSlides = $($settings.remoteId).find('.swiper-slide:not(.swiper-slide-duplicate)').length;
            $totalSlides = $totalSlides + '';
            realIndex = ((realIndex + 1) + '');

            $($settings.id).find('.bdt-current').text(realIndex.padStart($pad, "0"));
            $($settings.id).find('.bdt-total').text($totalSlides.padStart($pad, "0"));

            swiperInstance.on('slideChangeTransitionEnd', function () {
                var item = swiperInstance.realIndex + 1 + '';
                $($settings.id).find('.bdt-current').text(item.padStart($pad, "0"));
            });
        }

        var maxAttempts = 100;
        var pollInterval = 100;
        var attempt = 0;

        function tryInit() {
            var swiperEl = $($settings.remoteId).find('.swiper')[0];
            var swiperInstance = swiperEl && swiperEl.swiper;
            if (swiperInstance) {
                if (swiperInstance.initialized) {
                    initRemoteFraction(swiperInstance);
                } else {
                    swiperInstance.once('init', function () {
                        initRemoteFraction(swiperInstance);
                    });
                }
                return;
            }
            attempt++;
            if (attempt < maxAttempts) {
                setTimeout(tryInit, pollInterval);
            }
        }

        tryInit();
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-remote-fraction.default', widgetRemoteFraction);
    });

}(jQuery, window.elementorFrontend));

/**
 * End remote fraction widget script
 */
/**
 * Start Hash Link widget script
 */

(() => {
    'use strict';

    const ep_linker_builder = (rawText) => {
        const specialChars = '!@#$^&%*()+=-[]/{}|:<>?,.';
        const text = rawText.replace(/\s+/g, '-').toLowerCase();
        const escaped = specialChars.replace(/[\]\\^-]/g, '\\$&');
        return text.replace(new RegExp('[' + escaped + ']', 'g'), '');
    };

    const init = () => {
        const el = document.getElementById('ep-hash-link');
        if (!el) return;

        const rawSettings = el.dataset.settings;
        const settings = rawSettings ? JSON.parse(rawSettings) : {};
        if (!settings?.container || !settings?.selector) return;

        const container = document.querySelector(settings.container);
        if (!container) return;

        const selectorEls = container.querySelectorAll(settings.selector);
        if (!selectorEls.length) return;

        selectorEls.forEach((itemEl, index) => {
            itemEl.classList.add('ep-hash-link-inner-el');

            const rawText = itemEl.textContent ?? '';
            const url = ep_linker_builder(rawText);
            const anchor = document.createElement('a');
            anchor.id = 'ep-hash-link-' + index;
            anchor.dataset.id = String(index);
            anchor.className = 'ep-hash-link';
            anchor.href = '#' + index + '_' + url;

            itemEl.parentNode.insertBefore(anchor, itemEl);
            anchor.appendChild(itemEl);
        });

        if (window.location.hash) {
            const hash = window.location.hash;
            const idPart = hash.split('_')[0].slice(1);
            const targetId = idPart ? 'ep-hash-link-' + idPart : null;
            if (targetId) {
                const targetEl = document.getElementById(targetId);
                if (targetEl) {
                    const top = targetEl.getBoundingClientRect().top + window.scrollY - 150;
                    window.scrollTo({ top, behavior: 'smooth' });
                }
            }
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();

/**
 * End Hash Link widget script
 */

/**
 * Start crypto currency card widget script
 */

(() => {
    'use strict';

    const widgetCrypto = (scope) => {
        const scopeElement = scope?.jquery ? scope[0] : scope;

        const cryptoWidget = scopeElement.querySelector('.bdt-ep-crypto-currency-card');
        if (!cryptoWidget) return;

        const settingsData = cryptoWidget.dataset.settings;
        if (!settingsData) return;

        let settings;
        try {
            settings = typeof settingsData === 'string' ? JSON.parse(settingsData) : settingsData;
        } catch (e) {
            console.error('Failed to parse crypto currency card settings:', e);
            return;
        }

        const editMode = Boolean(elementorFrontend.isEditMode());

        const options = {
            currency : settings.currency || 'usd',
            limit    : 100,
            order    : settings.order || 'market_cap_desc',
        };

        const currencySelected    = options.currency;
        const currencyCodeUpper   = currencySelected.toUpperCase();
        const ajaxUrl             = ElementPackConfig.ajaxurl;

        const getData = async () => {
            try {
                const params = new URLSearchParams({
                    currency : currencySelected,
                    per_page : 1,
                    order    : options.order,
                    ids      : settings.ids
                });

                const response = await fetch(`${ajaxUrl}?action=ep_crypto_data&${params}`);
                const liveData = await response.json();

                const liveMap = new Map(liveData.map(item => [item.id, item]));

                const items = cryptoWidget.querySelectorAll('.bdt-crypto-currency-card-item');
                const idPriceArray = [...items].map(item => ({
                    el            : item,
                    id            : item.dataset.id,
                    current_price : parseFloat(item.querySelector('.bdt-price-text')?.textContent ?? '0')
                }));

                idPriceArray.forEach(({ el, id, current_price }) => {
                    const live = liveMap.get(id);
                    if (!live || live.current_price === current_price) return;

                    el.classList.add('data-changed');

                    const priceTextEl = el.querySelector('.bdt-price-text');
                    if (priceTextEl) priceTextEl.textContent = live.current_price;

                    const priceIntEl = el.querySelector('.price-int');
                    if (priceIntEl) priceIntEl.textContent = returnCurrencySymbol(currencyCodeUpper) + live.current_price;
                });

                setTimeout(() => {
                    cryptoWidget.querySelectorAll('.bdt-crypto-currency-card-item')
                        .forEach(item => item.classList.remove('data-changed'));
                    getData();
                }, 10000);

            } catch (e) {
                console.error('Crypto live data fetch failed:', e);
            }
        };

        const loadInitialData = async () => {
            try {
                const params = new URLSearchParams({
                    currency : currencySelected,
                    per_page : options.limit,
                    order    : options.order,
                    ids      : settings.ids
                });

                const response = await fetch(`${ajaxUrl}?action=ep_crypto&${params}`);
                const result   = await response.json();

                cryptoWidget.innerHTML = '';

                if (result.apiErrors === true && result.data?.length > 0) {
                    cryptoWidget.insertAdjacentHTML('beforeend',
                        `<div class="bdt-alert-danger" bdt-alert>
                            <a class="bdt-alert-close" bdt-close></a>
                            <p>${result.data}</p>
                        </div>`
                    );
                    return;
                }

                const element = result.data?.[0];
                if (!element) return;

                const amount       = returnCurrencySymbol(currencyCodeUpper) + element.current_price;
                const priceChange1h = element.price_change_percentage_1h;
                const oneHourData  = (Number(priceChange1h) === priceChange1h && priceChange1h % 1 !== 0)
                    ? priceChange1h.toFixed(2) + '%'
                    : priceChange1h + '%';

                const imgHtml           = settings.showCurrencyImage === true
                    ? `<div class="bdt-ep-currency-image"><img src="${element.image}" alt="${element.id}"></div>` : '';
                const symbolHtml        = settings.showCurrencyShortName === true
                    ? `<div class="bdt-ep-currency-short-name"><span>${element.symbol}</span></div>` : '';
                const nameHtml          = settings.showCurrencyName === true
                    ? `<div class="bdt-crypto-name-wrap"><div class="bdt-ep-currency-name"><span>${element.id}</span></div>${symbolHtml}</div>` : '';
                const hourlyPriceHtml   = settings.showCurrencyChangePrice === true
                    ? `<div class="bdt-percentage" title="1 Hour Data Change">${oneHourData}</div>` : '';
                const priceHtml         = settings.showCurrencyCurrentPrice === true
                    ? `<div class="bdt-width-1-1 bdt-width-1-2@s"><div class="bdt-ep-current-price"><div class="bdt-price">${amount}</div>${hourlyPriceHtml}</div></div>` : '';
                const marketCapRankHtml = settings.showMarketCapRank === true
                    ? `<div class="bdt-ep-ccc-atribute"><span class="bdt-ep-item-text">Market Cap Rank: </span><span>#${element.market_cap_rank}</span></div>` : '';
                const marketCapHtml     = settings.showMarketCap === true
                    ? `<div class="bdt-ep-ccc-atribute"><span class="bdt-ep-item-text">Market Cap: </span><span>${element.market_cap}</span></div>` : '';
                const totalVolumeHtml   = settings.showTotalVolume === true
                    ? `<div class="bdt-ep-ccc-atribute"><span class="bdt-ep-item-text">Total Volume: </span><span>${element.total_volume}</span></div>` : '';
                const priceChangeHtml   = settings.showPriceChange === true
                    ? `<div class="bdt-ep-ccc-atribute"><span class="bdt-ep-item-text">24H Change(%): </span><span>${element.price_change_percentage_24h}</span></div>` : '';

                cryptoWidget.insertAdjacentHTML('beforeend',
                    `<div class="bdt-grid" bdt-grid data-id="${element.id}">
                        <div class="bdt-width-1-1 bdt-width-1-2@s">
                            <div class="bdt-ep-currency">
                                ${imgHtml}
                                ${nameHtml}
                            </div>
                        </div>
                        ${priceHtml}
                        <div class="bdt-width-1-1 bdt-margin-small-top bdt-ep-ccc-atributes bdt-grid-margin bdt-first-column">
                            ${marketCapRankHtml}
                            ${marketCapHtml}
                            ${totalVolumeHtml}
                            ${priceChangeHtml}
                        </div>
                        <span class="bdt-price-text bdt-hidden">${element.current_price}</span>
                    </div>`
                );

            } catch (e) {
                console.error('Crypto initial data fetch failed:', e);
            }
        };

        loadInitialData();

        if (!editMode) {
            setTimeout(getData, 5000);
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (window.elementorFrontend?.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-card.default', widgetCrypto);
        }
    });

})();

/**
 * End crypto currency card widget script
 */

/**
 * Start crypto currency table widget script
 */

(() => {
    'use strict';

    const numFormatter = (num) => {
        if (num > 1_000_000_000) return (num / 1_000_000_000).toFixed(2) + 'B';
        if (num > 1_000_000)     return (num / 1_000_000).toFixed(2) + 'M';
        if (num > 999)           return (num / 1_000).toFixed(2) + 'K';
        return num;
    };

    const widgetCrypto = (scope) => {
        const scopeElement = scope instanceof jQuery ? scope[0] : scope;

        const cryptoWidget = scopeElement.querySelector('.bdt-crypto-currency-table');
        if (!cryptoWidget) return;

        const settingsData = cryptoWidget.dataset.settings;
        if (!settingsData) return;

        let settings;
        try {
            settings = typeof settingsData === 'string' ? JSON.parse(settingsData) : settingsData;
        } catch (e) {
            console.error('Failed to parse crypto table settings:', e);
            return;
        }

        const editMode = Boolean(elementorFrontend.isEditMode());

        const options = {
            currency : settings.currency || 'usd',
            limit    : settings.limit    || 100,
            order    : settings.order    || 'market_cap_desc',
        };

        const currencySelected  = options.currency;
        const currencyCodeUpper = currencySelected.toUpperCase();
        const ajaxUrl           = ElementPackConfig.ajaxurl;

        // ── Live price polling (every 10s) ────────────────────────────────────

        const getData = async () => {
            try {
                const params = new URLSearchParams({
                    currency : currencySelected,
                    per_page : options.limit,
                    order    : options.order,
                    ids      : settings.ids
                });

                const response = await fetch(`${ajaxUrl}?action=ep_crypto_data&${params}`);
                const liveData = await response.json();

                // Build O(1) lookup map from live data
                const liveMap  = new Map(liveData.map(item => [item.id, item]));
                const tableData = table.rows().data();

                // Find changed rows
                const changesIdArray = [];
                for (let i = 0; i < tableData.length; i++) {
                    const row  = tableData[i];
                    const live = liveMap.get(row.id);
                    if (live && live.current_price !== row.current_price) {
                        changesIdArray.push({
                            id            : row.id,
                            current_price : live.current_price,
                            old_price     : row.current_price,
                        });
                    }
                }

                // Update changed DataTable cells
                changesIdArray.forEach(changed => {
                    table.column(1).data().each((value, index) => {
                        if (value !== changed.id) return;

                        table.column(2).nodes().each((node, colIndex) => {
                            if (colIndex === index) {
                                table.cell(node).data(changed.current_price);
                                table.column(2).nodes()[index].classList.add('focus-item');
                            }
                        });
                    });
                });

                setTimeout(() => {
                    table.column(2).nodes().each(function () {
                        this.classList.remove('focus-item');
                    });
                    getData();
                }, 10000);

            } catch (e) {
                console.error('Crypto table live data fetch failed:', e);
            }
        };

        // ── DataTable init (jQuery DataTables plugin — jQuery required) ───────

        const table = jQuery(settings.tableId).DataTable({
            language    : window.ElementPackConfig.data_table.language,
            destroy     : true,
            processing  : true,
            serverSide  : false,
            searching   : settings.searching,
            ordering    : settings.ordering,
            paging      : settings.paging,
            info        : settings.info,
            pageLength  : settings.pageLength,
            ajax: {
                type     : 'GET',
                dataType : 'json',
                url      : `${ajaxUrl}?action=ep_crypto`,
                data: {
                    currency : currencySelected,
                    per_page : options.limit,
                    order    : options.order,
                    ids      : settings.ids
                },
            },
            columns: [
                { data: 'market_cap_rank' },
                {
                    data   : 'id',
                    render : (data, type, row) =>
                        `<div class="bdt-coin">
                            <div class="bdt-coin-image"><img src="${row.image}" alt="${row.id}"></div>
                            <div class="bdt-coin-title">
                                <div class="bdt-coin-name">${row.id}</div>
                                <div class="bdt-coin-symbol">${row.symbol}</div>
                            </div>
                        </div>`,
                },
                {
                    data   : 'current_price',
                    render : (data) => {
                        const val = Number(data) === data && data % 1 !== 0 ? data.toFixed(2) : data;
                        return returnCurrencySymbol(currencyCodeUpper) + val;
                    },
                },
                {
                    data   : 'price_change_percentage_24h',
                    render : (data) =>
                        Number(data) === data && data % 1 !== 0 ? data.toFixed(2) + '%' : data + '%',
                },
                {
                    data   : 'market_cap',
                    render : (data) => returnCurrencySymbol(currencyCodeUpper) + numFormatter(data),
                },
                {
                    data   : 'total_volume',
                    render : (data) => returnCurrencySymbol(currencyCodeUpper) + numFormatter(data),
                },
                {
                    data   : 'circulating_supply',
                    render : (data) => numFormatter(data),
                },
                {
                    data   : 'last_seven_days_changes',
                    render : (data, type, row) =>
                        `<input type="hidden" class="hdnInputCanvas-${row.id}" value="${data}"/>` +
                        `<div class="chart-container" style="position: relative; height: 100%; width: 250px">` +
                            `<canvas id="canvas-${row.id}"></canvas>` +
                        `</div>`,
                },
            ],
            columnDefs : [{ searchable: false, orderable: false, targets: [7] }],
            order      : [[0, 'asc']],
            createdRow : (row, data) => {
                const tdEl       = row.querySelectorAll('td')[7];
                const hiddenInput = tdEl?.querySelector('input');
                const canvasEl   = tdEl?.querySelector('canvas');
                if (!hiddenInput || !canvasEl) return;

                let splitData = hiddenInput.value.split(',');
                if (splitData.length > 15) {
                    splitData = splitData.slice(0, 14);
                }

                const chart = new Chart(canvasEl, {
                    type : 'line',
                    data : {
                        labels   : splitData.map((_, i) => i),
                        datasets : [{
                            label                : '',
                            backgroundColor      : 'rgba(30,135,240,0.2)',
                            borderColor          : '#1e87f0',
                            fill                 : true,
                            lineTension          : 0.4,
                            pointStyle           : 'circle',
                            pointBackgroundColor : 'transparent',
                            pointBorderWidth     : 0,
                            borderWidth          : 2,
                            data                 : splitData.map(Number),
                        }],
                    },
                    options: {
                        responsive : true,
                        plugins    : {
                            legend  : { display: false },
                            tooltip : { enabled: true, callbacks: { title: () => '' } }
                        },
                        scales: {
                            x: { ticks: { display: false }, grid: { display: false, drawBorder: false, drawOnChartArea: false, drawTicks: false } },
                            y: { ticks: { display: false }, grid: { display: false, drawBorder: false, drawOnChartArea: false, drawTicks: false } },
                        },
                    },
                });

                chart.canvas.parentNode.style.width  = '100%';
                chart.canvas.parentNode.style.height = '60px';
                chart.canvas.style.width  = '100%';
                chart.canvas.style.height = '60px';
            },
        });

        if (!editMode) {
            setTimeout(getData, 5000);
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (window.elementorFrontend?.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-table.default', widgetCrypto);
        }
    });

})();

/**
 * End crypto currency table widget script
 */

/**
 * Start crypto currency grid widget script
 */

(() => {
    'use strict';

    const widgetCrypto = (scope) => {
        const scopeElement = scope?.jquery ? scope[0] : scope;

        const cryptoWidget = scopeElement.querySelector('.bdt-crypto-currency-grid');
        if (!cryptoWidget) return;

        const settingsData = cryptoWidget.dataset.settings;
        if (!settingsData) return;

        let settings;
        try {
            settings = typeof settingsData === 'string' ? JSON.parse(settingsData) : settingsData;
        } catch (e) {
            console.error('Failed to parse crypto grid settings:', e);
            return;
        }

        const editMode = Boolean(elementorFrontend.isEditMode());

        const options = {
            currency : settings.currency || 'usd',
            limit    : settings.limit    || 100,
            order    : settings.order    || 'market_cap_desc',
        };

        const currencySelected  = options.currency;
        const currencyCodeUpper = currencySelected.toUpperCase();
        const ajaxUrl           = ElementPackConfig.ajaxurl;

        // ── Live price polling (every 10s) ────────────────────────────────────

        const getData = async () => {
            try {
                const params = new URLSearchParams({
                    currency : currencySelected,
                    per_page : options.limit,
                    order    : options.order,
                    ids      : settings.ids
                });

                const response = await fetch(`${ajaxUrl}?action=ep_crypto_data&${params}`);
                const liveData = await response.json();

                // Build O(1) lookup map from live data
                const liveMap = new Map(liveData.map(item => [item.id, item]));

                const items = cryptoWidget.querySelectorAll('.bdt-crypto-currency-grid-item');
                const idPriceArray = [...items].map(item => ({
                    el            : item,
                    id            : item.dataset.id,
                    current_price : parseFloat(item.querySelector('.bdt-price-text')?.textContent ?? '0')
                }));

                // Apply updates only where price changed — O(n)
                idPriceArray.forEach(({ el, id, current_price }) => {
                    const live = liveMap.get(id);
                    if (!live || live.current_price === current_price) return;

                    el.classList.add('data-changed');

                    const priceTextEl = el.querySelector('.bdt-price-text');
                    if (priceTextEl) priceTextEl.textContent = live.current_price;

                    const priceIntEl = el.querySelector('.price-int');
                    if (priceIntEl) priceIntEl.textContent = returnCurrencySymbol(currencyCodeUpper) + live.current_price;
                });

                setTimeout(() => {
                    cryptoWidget.querySelectorAll('.bdt-crypto-currency-grid-item')
                        .forEach(item => item.classList.remove('data-changed'));
                    getData();
                }, 10000);

            } catch (e) {
                console.error('Crypto grid live data fetch failed:', e);
            }
        };

        // ── Initial data load ─────────────────────────────────────────────────

        const loadInitialData = async () => {
            try {
                const params = new URLSearchParams({
                    currency : currencySelected,
                    per_page : options.limit,
                    order    : options.order,
                    ids      : settings.ids
                });

                const response = await fetch(`${ajaxUrl}?action=ep_crypto&${params}`);
                const result   = await response.json();

                cryptoWidget.innerHTML = '';

                if (result.apiErrors === true && result.data?.length > 0) {
                    cryptoWidget.insertAdjacentHTML('beforeend',
                        `<div class="bdt-alert-danger" bdt-alert>
                            <a class="bdt-alert-close" bdt-close></a>
                            <p>${result.data}</p>
                        </div>`
                    );
                    return;
                }

                if (!result.data?.length) return;

                result.data.slice(0, options.limit).forEach(element => {
                    const amount = returnCurrencySymbol(currencyCodeUpper) + element.current_price;

                    const imgHtml        = settings.showCurrencyImage === true
                        ? `<div class="bdt-crypto-currency-grid-img"><img src="${element.image}" alt="${element.id}"></div>` : '';
                    const symbolHtml     = settings.showCurrencyShortName === true
                        ? `<span>(${element.symbol})</span>` : '';
                    const nameHtml       = settings.showCurrencyName === true
                        ? `<div class="bdt-crypto-currency-grid-title"><h4>${element.id} ${symbolHtml}</h4></div>` : '';
                    const priceLabelHtml = settings.showCurrencyPriceLabel === true
                        ? `<div class="bdt-crypto-currency-grid-price-text"><span>price</span></div>` : '';
                    const priceHtml      = settings.showCurrencyCurrentPrice === true
                        ? `${priceLabelHtml}<div class="bdt-crypto-currency-grid-price-nu"><span class="price-int">${amount}</span></div>` : '';

                    cryptoWidget.insertAdjacentHTML('beforeend',
                        `<div class="bdt-crypto-currency-grid-item" data-id="${element.id}">
                            <div class="bdt-crypto-currency-grid-content">
                                <div class="bdt-crypto-currency-grid-bg">
                                    <img src="${element.image}" alt="${element.id}">
                                </div>
                                <div class="bdt-crypto-currency-grid-head-content">
                                    ${imgHtml}
                                    ${nameHtml}
                                </div>
                                <div class="bdt-crypto-currency-grid-bottom-content">
                                    ${priceHtml}
                                </div>
                            </div>
                            <span class="bdt-price-text bdt-hidden">${element.current_price}</span>
                        </div>`
                    );
                });

            } catch (e) {
                console.error('Crypto grid initial data fetch failed:', e);
            }
        };

        loadInitialData();

        if (!editMode) {
            setTimeout(getData, 5000);
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (window.elementorFrontend?.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-grid.default', widgetCrypto);
        }
    });

})();

/**
 * End crypto currency grid widget script
 */

/**
 * Start crypto currency list widget script
 */

(() => {
    'use strict';

    const widgetCrypto = (scope) => {
        const scopeElement = scope?.jquery ? scope[0] : scope;

        const cryptoWidget = scopeElement.querySelector('.bdt-crypto-currency-list');
        if (!cryptoWidget) return;

        const settingsData = cryptoWidget.dataset.settings;
        if (!settingsData) return;

        let settings;
        try {
            settings = typeof settingsData === 'string' ? JSON.parse(settingsData) : settingsData;
        } catch (e) {
            console.error('Failed to parse crypto list settings:', e);
            return;
        }

        const editMode = Boolean(elementorFrontend.isEditMode());

        const options = {
            currency : settings.currency || 'usd',
            limit    : settings.limit    || 100,
            order    : settings.order    || 'market_cap_desc',
        };

        const currencySelected  = options.currency;
        const currencyCodeUpper = currencySelected.toUpperCase();
        const ajaxUrl           = ElementPackConfig.ajaxurl;

        // ── Live price polling (every 10s) ────────────────────────────────────

        const getData = async () => {
            try {
                const params = new URLSearchParams({
                    currency : currencySelected,
                    per_page : options.limit,
                    order    : options.order,
                    ids      : settings.ids
                });

                const response = await fetch(`${ajaxUrl}?action=ep_crypto_data&${params}`);
                const liveData = await response.json();

                // Build O(1) lookup map from live data
                const liveMap = new Map(liveData.map(item => [item.id, item]));

                const items = cryptoWidget.querySelectorAll('.bdt-crypto-currency-list-item');
                const idPriceArray = [...items].map(item => ({
                    el            : item,
                    id            : item.dataset.id,
                    current_price : parseFloat(item.querySelector('.bdt-price-text')?.textContent ?? '0')
                }));

                // Apply updates only where price changed — O(n)
                idPriceArray.forEach(({ el, id, current_price }) => {
                    const live = liveMap.get(id);
                    if (!live || live.current_price === current_price) return;

                    el.classList.add('data-changed');

                    const priceTextEl = el.querySelector('.bdt-price-text');
                    if (priceTextEl) priceTextEl.textContent = live.current_price;

                    const priceIntEl = el.querySelector('.price-int');
                    if (priceIntEl) priceIntEl.textContent = returnCurrencySymbol(currencyCodeUpper) + live.current_price;
                });

                setTimeout(() => {
                    cryptoWidget.querySelectorAll('.bdt-crypto-currency-list-item')
                        .forEach(item => item.classList.remove('data-changed'));
                    getData();
                }, 10000);

            } catch (e) {
                console.error('Crypto list live data fetch failed:', e);
            }
        };

        // ── Initial data load ─────────────────────────────────────────────────

        const loadInitialData = async () => {
            try {
                const params = new URLSearchParams({
                    currency : currencySelected,
                    per_page : options.limit,
                    order    : options.order,
                    ids      : settings.ids
                });

                const response = await fetch(`${ajaxUrl}?action=ep_crypto&${params}`);
                const result   = await response.json();

                cryptoWidget.innerHTML = '';

                if (!result.data?.length) return;

                result.data.slice(0, options.limit).forEach(element => {
                    const amount = returnCurrencySymbol(currencyCodeUpper) + element.current_price;

                    const imgHtml    = settings.showCurrencyImage === true
                        ? `<div class="bdt-crypto-currency-list-img"><img src="${element.image}" alt="${element.id}"></div>` : '';
                    const symbolHtml = settings.showCurrencyShortName === true
                        ? `<span>(${element.symbol})</span>` : '';
                    const nameHtml   = settings.showCurrencyName === true
                        ? `<div class="bdt-crypto-currency-list-title">${element.id} ${symbolHtml}</div>` : '';
                    const priceHtml  = settings.showCurrencyCurrentPrice === true
                        ? `<div class="bdt-crypto-currency-list-price"><span>${amount}</span></div>` : '';

                    cryptoWidget.insertAdjacentHTML('beforeend',
                        `<div class="bdt-crypto-currency-list-item" data-id="${element.id}">
                            <div class="bdt-crypto-currency-list-content">
                                <div class="bdt-crypto-currency-list-inner">
                                    ${imgHtml}
                                    ${nameHtml}
                                </div>
                                ${priceHtml}
                            </div>
                            <span class="bdt-price-text bdt-hidden">${element.current_price}</span>
                        </div>`
                    );
                });

            } catch (e) {
                console.error('Crypto list initial data fetch failed:', e);
            }
        };

        loadInitialData();

        if (!editMode) {
            setTimeout(getData, 5000);
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (window.elementorFrontend?.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-list.default', widgetCrypto);
        }
    });

})();

/**
 * End crypto currency list widget script
 */

/**
 * Start crypto currency carousel widget script
 */

(() => {
    'use strict';

    const widgetCrypto = (scope) => {
        const scopeElement = scope?.jquery ? scope[0] : scope;

        const cryptoWidget = scopeElement.querySelector('.bdt-crypto-currency-carousel');
        if (!cryptoWidget) return;

        const cryptoSettingsData = cryptoWidget.dataset.cryptoSettings;
        if (!cryptoSettingsData) return;

        let settings;
        try {
            settings = typeof cryptoSettingsData === 'string' ? JSON.parse(cryptoSettingsData) : cryptoSettingsData;
        } catch (e) {
            console.error('Failed to parse crypto carousel settings:', e);
            return;
        }

        const editMode = Boolean(elementorFrontend.isEditMode());

        const options = {
            currency : settings.currency || 'usd',
            limit    : settings.limit    || 100,
            order    : settings.order    || 'market_cap_desc',
        };

        const currencySelected  = options.currency;
        const currencyCodeUpper = currencySelected.toUpperCase();
        const ajaxUrl           = ElementPackConfig.ajaxurl;

        const getData = async () => {
            try {
                const params = new URLSearchParams({
                    currency : currencySelected,
                    per_page : options.limit,
                    order    : options.order,
                    ids      : settings.ids
                });

                const response = await fetch(`${ajaxUrl}?action=ep_crypto_data&${params}`);
                const liveData = await response.json();

                const liveMap = new Map(liveData.map(item => [item.id, item]));

                const items = cryptoWidget.querySelectorAll('.bdt-crypto-currency-carousel-item');
                const idPriceArray = [...items].map(item => ({
                    el            : item,
                    id            : item.dataset.id,
                    current_price : parseFloat(item.querySelector('.bdt-price-text')?.textContent ?? '0')
                }));

                idPriceArray.forEach(({ el, id, current_price }) => {
                    const live = liveMap.get(id);
                    if (!live || live.current_price === current_price) return;

                    el.classList.add('data-changed');

                    const priceTextEl = el.querySelector('.bdt-price-text');
                    if (priceTextEl) priceTextEl.textContent = live.current_price;

                    const priceIntEl = el.querySelector('.price-int');
                    if (priceIntEl) priceIntEl.textContent = returnCurrencySymbol(currencyCodeUpper) + live.current_price;
                });

                setTimeout(() => {
                    cryptoWidget.querySelectorAll('.bdt-crypto-currency-carousel-item')
                        .forEach(item => item.classList.remove('data-changed'));
                    getData();
                }, 10000);

            } catch (e) {
                console.error('Crypto carousel live data fetch failed:', e);
            }
        };

        const loadInitialData = async () => {
            try {
                const params = new URLSearchParams({
                    currency : currencySelected,
                    per_page : options.limit,
                    order    : options.order,
                    ids      : settings.ids
                });

                const response = await fetch(`${ajaxUrl}?action=ep_crypto&${params}`);
                const result   = await response.json();

                const swiperWrapper = cryptoWidget.querySelector('.swiper-wrapper');
                if (swiperWrapper) swiperWrapper.innerHTML = '';

                if (!result.data?.length) return;

                result.data.slice(0, options.limit).forEach(element => {
                    const amount = returnCurrencySymbol(currencyCodeUpper) + element.current_price;

                    const imgHtml        = settings.showCurrencyImage === true
                        ? `<div class="bdt-crypto-currency-carousel-img"><img src="${element.image}" alt="${element.id}"></div>` : '';
                    const symbolHtml     = settings.showCurrencyShortName === true
                        ? `<span>(${element.symbol})</span>` : '';
                    const nameHtml       = settings.showCurrencyName === true
                        ? `<div class="bdt-crypto-currency-carousel-title"><h4>${element.id} ${symbolHtml}</h4></div>` : '';
                    const priceLabelHtml = settings.showCurrencyPriceLabel === true
                        ? `<div class="bdt-crypto-currency-carousel-price-text"><span>price</span></div>` : '';
                    const priceHtml      = settings.showCurrencyCurrentPrice === true
                        ? `${priceLabelHtml}<div class="bdt-crypto-currency-carousel-price-nu"><span class="price-int">${amount}</span></div>` : '';

                    swiperWrapper?.insertAdjacentHTML('beforeend',
                        `<div class="swiper-slide">
                            <div class="bdt-crypto-currency-carousel-item" data-id="${element.id}">
                                <div class="bdt-crypto-currency-carousel-content">
                                    <div class="bdt-crypto-currency-carousel-bg">
                                        <img src="${element.image}" alt="${element.id}">
                                    </div>
                                    <div class="bdt-crypto-currency-carousel-head-content">
                                        ${imgHtml}
                                        ${nameHtml}
                                    </div>
                                    <div class="bdt-crypto-currency-carousel-bottom-content">
                                        ${priceHtml}
                                    </div>
                                </div>
                                <span class="bdt-price-text bdt-hidden">${element.current_price}</span>
                            </div>
                        </div>`
                    );
                });

                const carouselContainer = cryptoWidget.querySelector('.swiper-carousel');
                const carouselSettingsData = cryptoWidget.dataset.settings;
                const carouselSettings = carouselSettingsData
                    ? (typeof carouselSettingsData === 'string' ? JSON.parse(carouselSettingsData) : carouselSettingsData)
                    : {};

                const Swiper = elementorFrontend.utils.swiper;
                const swiper = await new Swiper(carouselContainer, carouselSettings);

                if (carouselSettings.pauseOnHover) {
                    carouselContainer.addEventListener('mouseenter', () => swiper.autoplay.stop());
                    carouselContainer.addEventListener('mouseleave', () => swiper.autoplay.start());
                }

            } catch (e) {
                console.error('Crypto carousel initial data fetch failed:', e);
            }
        };

        loadInitialData();

        if (!editMode) {
            setTimeout(getData, 5000);
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (window.elementorFrontend?.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-carousel.default', widgetCrypto);
        }
    });

})();

/**
 * End crypto currency carousel widget script
 */

/**
 * Start crypto currency chart widget script
 */

(() => {
    'use strict';

    const initItemChart = (itemEl) => {
        const hiddenInput = itemEl.querySelector('input[type="hidden"]');
        const canvasEl    = itemEl.querySelector('canvas');
        if (!hiddenInput || !canvasEl) return;

        let splitData = hiddenInput.value.split(',');
        if (splitData.length > 15) {
            splitData = splitData.slice(0, 14);
        }

        new Chart(canvasEl, {
            type : 'line',
            data : {
                labels   : splitData.map((_, i) => i),
                datasets : [{
                    label                : '',
                    backgroundColor      : 'rgba(30,135,240,0.2)',
                    borderColor          : '#1e87f0',
                    fill                 : true,
                    lineTension          : 0.4,
                    pointStyle           : 'circle',
                    pointBackgroundColor : '#1e87f0',
                    pointBorderWidth     : 1,
                    borderWidth          : 2,
                    data                 : splitData.map(Number),
                }],
            },
            options: {
                responsive : true,
                plugins    : {
                    legend  : { display: false },
                    tooltip : { enabled: true }
                },
                scales: {
                    x: { ticks: { display: false }, grid: { display: false, drawBorder: false, drawOnChartArea: false, drawTicks: false } },
                    y: { ticks: { display: false }, grid: { display: false, drawBorder: false, drawOnChartArea: false, drawTicks: false } },
                },
            },
        });
    };

    const widgetCrypto = (scope) => {
        const scopeElement = scope?.jquery ? scope[0] : scope;

        const cryptoWidget = scopeElement.querySelector('.bdt-crypto-currency-chart');
        if (!cryptoWidget) return;

        const settingsData = cryptoWidget.dataset.settings;
        if (!settingsData) return;

        let settings;
        try {
            settings = typeof settingsData === 'string' ? JSON.parse(settingsData) : settingsData;
        } catch (e) {
            console.error('Failed to parse crypto chart settings:', e);
            return;
        }

        const editMode = Boolean(elementorFrontend.isEditMode());

        const options = {
            currency : settings.currency || 'usd',
            limit    : settings.limit    || 100,
            order    : settings.order    || 'market_cap_desc',
        };

        const currencySelected  = options.currency;
        const currencyCodeUpper = currencySelected.toUpperCase();
        const ajaxUrl           = ElementPackConfig.ajaxurl;

        const getData = async () => {
            try {
                const params = new URLSearchParams({
                    currency : currencySelected,
                    per_page : options.limit,
                    order    : options.order,
                    ids      : settings.ids
                });

                const response = await fetch(`${ajaxUrl}?action=ep_crypto_data&${params}`);
                const liveData = await response.json();

                const liveMap = new Map(liveData.map(item => [item.id, item]));

                const items = cryptoWidget.querySelectorAll('.bdt-crypto-currency-chart-item');
                const idPriceArray = [...items].map(item => ({
                    el            : item,
                    id            : item.dataset.id,
                    current_price : parseFloat(item.querySelector('.bdt-price-text')?.textContent ?? '0')
                }));

                idPriceArray.forEach(({ el, id, current_price }) => {
                    const live = liveMap.get(id);
                    if (!live || live.current_price === current_price) return;

                    el.classList.add('data-changed');

                    const priceTextEl = el.querySelector('.bdt-price-text');
                    if (priceTextEl) priceTextEl.textContent = live.current_price;

                    const priceIntEl = el.querySelector('.price-int');
                    if (priceIntEl) priceIntEl.textContent = returnCurrencySymbol(currencyCodeUpper) + live.current_price;
                });

                setTimeout(() => {
                    cryptoWidget.querySelectorAll('.bdt-crypto-currency-chart-item')
                        .forEach(item => item.classList.remove('data-changed'));
                    getData();
                }, 10000);

            } catch (e) {
                console.error('Crypto chart live data fetch failed:', e);
            }
        };

        const loadInitialData = async () => {
            try {
                const params = new URLSearchParams({
                    currency : currencySelected,
                    per_page : options.limit,
                    order    : options.order,
                    ids      : settings.ids
                });

                const response = await fetch(`${ajaxUrl}?action=ep_crypto&${params}`);
                const result   = await response.json();

                cryptoWidget.innerHTML = '';

                if (!result.data?.length) return;

                result.data.slice(0, options.limit).forEach(element => {
                    const amount    = returnCurrencySymbol(currencyCodeUpper) + element.current_price;
                    const priceChange1h = element.price_change_percentage_1h;
                    const oneHourData   = (Number(priceChange1h) === priceChange1h && priceChange1h % 1 !== 0)
                        ? priceChange1h.toFixed(2) + '%'
                        : priceChange1h + '%';

                    const symbolHtml      = settings.showCurrencyShortName === true
                        ? `<span>(${element.symbol})</span>` : '';
                    const nameHtml        = settings.showCurrencyName === true
                        ? `<div class="bdt-crypto-currency-chart-title"><h4>${element.id} ${symbolHtml}</h4></div>` : '';
                    const priceHtml       = settings.showCurrencyCurrentPrice === true
                        ? `<div class="bdt-crypto-currency-chart-price-l"><span class="price-int">${amount}</span></div>` : '';
                    const priceChangeHtml = settings.showPriceChangePercentage === true
                        ? `<div class="bdt-crypto-currency-chart-change">
                               <span class="bdt-crypto-currency-chart-list-change up" title="1 Hour Data Change">${oneHourData}</span>
                           </div>` : '';

                    cryptoWidget.insertAdjacentHTML('beforeend',
                        `<div class="bdt-crypto-currency-chart-item" data-id="${element.id}">
                            <div class="bdt-crypto-currency-chart-head-content">
                                <div class="bdt-crypto-currency-chart-head-inner-content">
                                    ${nameHtml}
                                    ${priceChangeHtml}
                                </div>
                                <div class="bdt-crypto-currency-chart-bottom-inner-content">
                                    ${priceHtml}
                                </div>
                            </div>
                            <div class="bdt-crypto-currency-chart-chart">
                                <input type="hidden" class="hdnInputCanvas-${element.id}" value="${element.last_seven_days_changes}"/>
                                <div class="chart-container" style="position: relative;">
                                    <canvas id="canvas-${element.id}"></canvas>
                                </div>
                            </div>
                            <span class="bdt-price-text bdt-hidden">${element.current_price}</span>
                        </div>`
                    );

                    const itemEl = cryptoWidget.querySelector(`.bdt-crypto-currency-chart-item[data-id="${element.id}"]`);
                    if (itemEl) initItemChart(itemEl);
                });

            } catch (e) {
                console.error('Crypto chart initial data fetch failed:', e);
            }
        };

        loadInitialData();

        if (!editMode) {
            setTimeout(getData, 5000);
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (window.elementorFrontend?.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-chart.default', widgetCrypto);
        }
    });

})();

/**
 * End crypto currency chart widget script
 */

/**
 * Start crypto currency chart carousel widget script
 */

(() => {
    'use strict';

    const initItemChart = (itemEl) => {
        const hiddenInput = itemEl.querySelector('input[type="hidden"]');
        const canvasEl    = itemEl.querySelector('canvas');
        if (!hiddenInput || !canvasEl) return;

        let splitData = hiddenInput.value.split(',');
        if (splitData.length > 15) {
            splitData = splitData.slice(0, 14);
        }

        const labels         = splitData.map((_, i) => i);
        const dataPointvalue = splitData.map(Number);

        new Chart(canvasEl, {
            type : 'line',
            data : {
                labels,
                datasets: [{
                    label                : '',
                    backgroundColor      : 'rgba(30,135,240,0.2)',
                    borderColor          : '#1e87f0',
                    fill                 : true,
                    lineTension          : 0.4,
                    pointStyle           : 'circle',
                    pointBackgroundColor : '#1e87f0',
                    pointBorderWidth     : 1,
                    borderWidth          : 2,
                    data                 : dataPointvalue,
                }],
            },
            options: {
                responsive : true,
                plugins    : {
                    legend  : { display: false },
                    tooltip : { enabled: true }
                },
                scales: {
                    x: { ticks: { display: false }, grid: { display: false, drawBorder: false, drawOnChartArea: false, drawTicks: false } },
                    y: { ticks: { display: false }, grid: { display: false, drawBorder: false, drawOnChartArea: false, drawTicks: false } },
                },
            },
        });
    };

    const widgetCrypto = (scope) => {
        const scopeElement = scope?.jquery ? scope[0] : scope;

        const cryptoWidget = scopeElement.querySelector('.bdt-crypto-currency-chart-carousel');
        if (!cryptoWidget) return;

        const cryptoSettingsData = cryptoWidget.dataset.cryptoSettings;
        if (!cryptoSettingsData) return;

        let settings;
        try {
            settings = typeof cryptoSettingsData === 'string' ? JSON.parse(cryptoSettingsData) : cryptoSettingsData;
        } catch (e) {
            console.error('Failed to parse crypto chart carousel settings:', e);
            return;
        }

        const editMode = Boolean(elementorFrontend.isEditMode());

        const options = {
            currency : settings.currency || 'usd',
            limit    : settings.limit    || 100,
            order    : settings.order    || 'market_cap_desc',
        };

        const currencySelected  = options.currency;
        const currencyCodeUpper = currencySelected.toUpperCase();
        const ajaxUrl           = ElementPackConfig.ajaxurl;

        const getData = async () => {
            try {
                const params = new URLSearchParams({
                    currency : currencySelected,
                    per_page : options.limit,
                    order    : options.order,
                    ids      : settings.ids
                });

                const response = await fetch(`${ajaxUrl}?action=ep_crypto_data&${params}`);
                const liveData = await response.json();

                const liveMap = new Map(liveData.map(item => [item.id, item]));

                const items = cryptoWidget.querySelectorAll('.bdt-crypto-currency-chart-carousel-item');
                const idPriceArray = [...items].map(item => ({
                    el            : item,
                    id            : item.dataset.id,
                    current_price : parseFloat(item.querySelector('.bdt-price-text')?.textContent ?? '0')
                }));

                idPriceArray.forEach(({ el, id, current_price }) => {
                    const live = liveMap.get(id);
                    if (!live || live.current_price === current_price) return;

                    el.classList.add('data-changed');

                    const priceTextEl = el.querySelector('.bdt-price-text');
                    if (priceTextEl) priceTextEl.textContent = live.current_price;

                    const priceIntEl = el.querySelector('.price-int');
                    if (priceIntEl) priceIntEl.textContent = returnCurrencySymbol(currencyCodeUpper) + live.current_price;
                });

                setTimeout(() => {
                    cryptoWidget.querySelectorAll('.bdt-crypto-currency-chart-carousel-item')
                        .forEach(item => item.classList.remove('data-changed'));
                    getData();
                }, 10000);

            } catch (e) {
                console.error('Crypto chart carousel live data fetch failed:', e);
            }
        };

        const loadInitialData = async () => {
            try {
                const params = new URLSearchParams({
                    currency : currencySelected,
                    per_page : options.limit,
                    order    : options.order,
                    ids      : settings.ids
                });

                const response = await fetch(`${ajaxUrl}?action=ep_crypto&${params}`);
                const result   = await response.json();

                const swiperWrapper = cryptoWidget.querySelector('.swiper-wrapper');
                if (swiperWrapper) swiperWrapper.innerHTML = '';

                if (!result.data?.length) return;

                result.data.slice(0, options.limit).forEach(element => {
                    const amount    = returnCurrencySymbol(currencyCodeUpper) + element.current_price;
                    const priceChange1h = element.price_change_percentage_1h;
                    const oneHourData   = (Number(priceChange1h) === priceChange1h && priceChange1h % 1 !== 0)
                        ? priceChange1h.toFixed(2) + '%'
                        : priceChange1h + '%';

                    const symbolHtml      = settings.showCurrencyShortName === true
                        ? `<span>(${element.symbol})</span>` : '';
                    const nameHtml        = settings.showCurrencyName === true
                        ? `<div class="bdt-crypto-currency-chart-carousel-title"><h4>${element.id} ${symbolHtml}</h4></div>` : '';
                    const priceHtml       = settings.showCurrencyCurrentPrice === true
                        ? `<div class="bdt-crypto-currency-chart-carousel-price-l"><span>${amount}</span></div>` : '';
                    const priceChangeHtml = settings.showPriceChangePercentage === true
                        ? `<div class="bdt-crypto-currency-chart-carousel-change">
                               <span class="bdt-crypto-currency-chart-carousel-list-change up" title="1 Hour Data Change">${oneHourData}</span>
                           </div>` : '';

                    swiperWrapper?.insertAdjacentHTML('beforeend',
                        `<div class="swiper-slide">
                            <div class="bdt-crypto-currency-chart-carousel-item" data-id="${element.id}">
                                <div class="bdt-crypto-currency-chart-carousel-head-content">
                                    <div class="bdt-crypto-currency-chart-carousel-head-inner-content">
                                        ${nameHtml}
                                        ${priceChangeHtml}
                                    </div>
                                    <div class="bdt-crypto-currency-chart-carousel-bottom-inner-content">
                                        ${priceHtml}
                                    </div>
                                </div>
                                <div class="bdt-crypto-currency-chart-carousel-chart">
                                    <input type="hidden" class="hdnInputCanvas-${element.id}" value="${element.last_seven_days_changes}"/>
                                    <div class="chart-container" style="position: relative;">
                                        <canvas id="canvas-${element.id}"></canvas>
                                    </div>
                                </div>
                                <span class="bdt-price-text bdt-hidden">${element.current_price}</span>
                            </div>
                        </div>`
                    );

                    const itemEl = cryptoWidget.querySelector(`.bdt-crypto-currency-chart-carousel-item[data-id="${element.id}"]`);
                    if (itemEl) initItemChart(itemEl);
                });

                const carouselContainer    = cryptoWidget.querySelector('.swiper-carousel');
                const carouselSettingsData = cryptoWidget.dataset.settings;
                const carouselSettings     = carouselSettingsData
                    ? (typeof carouselSettingsData === 'string' ? JSON.parse(carouselSettingsData) : carouselSettingsData)
                    : {};

                const Swiper = elementorFrontend.utils.swiper;
                const swiper = await new Swiper(carouselContainer, carouselSettings);

                if (carouselSettings.pauseOnHover) {
                    carouselContainer.addEventListener('mouseenter', () => swiper.autoplay.stop());
                    carouselContainer.addEventListener('mouseleave', () => swiper.autoplay.start());
                }

            } catch (e) {
                console.error('Crypto chart carousel initial data fetch failed:', e);
            }
        };

        loadInitialData();

        if (!editMode) {
            setTimeout(getData, 5000);
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (window.elementorFrontend?.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-chart-carousel.default', widgetCrypto);
        }
    });

})();

/**
 * End crypto currency chart carousel widget script
 */

/**
 * Start crypto currency ticker widget script
 */

(() => {
    'use strict';

    const widgetCrypto = (scope) => {
        const scopeEl = scope instanceof jQuery ? scope[0] : scope;

        const cryptoWidget = scopeEl.querySelector('.bdt-crypto-currency-ticker');
        if (!cryptoWidget) return;

        const settings       = cryptoWidget.dataset.settings       ? JSON.parse(cryptoWidget.dataset.settings)       : {};
        const tickerSettings = cryptoWidget.dataset.tickerSettings  ? JSON.parse(cryptoWidget.dataset.tickerSettings)  : {};
        const editMode       = Boolean(elementorFrontend.isEditMode());

        const options = {
            currency : settings.currency || 'usd',
            limit    : settings.limit    || 100,
            order    : settings.order    || 'market_cap_desc',
        };

        const currencySelected  = options.currency;
        const currencyCodeUpper = currencySelected.toUpperCase();
        const ajaxUrl           = ElementPackConfig.ajaxurl;
        const listEl            = cryptoWidget.querySelector('ul');

        // ── Live price polling ────────────────────────────────────────────────

        const getData = async () => {
            try {
                const params = new URLSearchParams({
                    currency : currencySelected,
                    per_page : options.limit,
                    order    : options.order,
                    ids      : settings.ids,
                });

                const response = await fetch(`${ajaxUrl}?action=ep_crypto_data&${params}`);
                const liveData = await response.json();

                // Build O(1) lookup map from live data
                const liveMap = new Map(liveData.map(item => [item.id, item]));

                // Read current prices from DOM
                const items = cryptoWidget.querySelectorAll('.bdt-crypto-currency-ticker-item');
                const changesIdArray = [];

                items.forEach(item => {
                    const id           = item.dataset.id;
                    const currentPrice = parseFloat(item.querySelector('.bdt-price-text')?.textContent ?? 0);
                    const live         = liveMap.get(id);

                    if (live && live.current_price !== currentPrice) {
                        changesIdArray.push({
                            id            : id,
                            current_price : live.current_price,
                            old_price     : currentPrice,
                        });
                    }
                });

                if (changesIdArray.length) {
                    changesIdArray.forEach(({ id, current_price }) => {
                        const itemEl = cryptoWidget.querySelector(`[data-id="${id}"]`);
                        if (!itemEl) return;

                        itemEl.classList.add('data-changed');
                        const priceTextEl = itemEl.querySelector('.bdt-price-text');
                        if (priceTextEl) priceTextEl.textContent = current_price;

                        const priceIntEl = itemEl.querySelector('.price-int');
                        if (priceIntEl) priceIntEl.textContent = returnCurrencySymbol(currencyCodeUpper) + current_price;
                    });
                }

                setTimeout(() => {
                    cryptoWidget.querySelectorAll('.bdt-crypto-currency-ticker-item').forEach(el => {
                        el.classList.remove('data-changed');
                    });
                    getData();
                }, 10000);

            } catch (e) {
                console.error('Crypto ticker live data fetch failed:', e);
            }
        };

        // ── Initial data load ─────────────────────────────────────────────────

        const loadInitialData = async () => {
            try {
                const params = new URLSearchParams({
                    currency : currencySelected,
                    per_page : options.limit,
                    order    : options.order,
                    ids      : settings.ids,
                });

                const response = await fetch(`${ajaxUrl}?action=ep_crypto&${params}`);
                const result   = await response.json();

                if (!listEl || !result?.data) return;

                listEl.innerHTML = '';

                result.data.slice(0, options.limit).forEach(element => {
                    const amount     = returnCurrencySymbol(currencyCodeUpper) + element.current_price;
                    const rawChange  = element.price_change_percentage_1h;
                    const oneHourPct = (Number(rawChange) === rawChange && rawChange % 1 !== 0)
                        ? rawChange.toFixed(2) + '%'
                        : rawChange + '%';

                    const imgHtml = settings.showCurrencyImage
                        ? `<div class="bdt-crypto-currency-ticker-img"><img src="${element.image}" alt="${element.id}"></div>`
                        : '';

                    const symbolHtml = settings.showCurrencyShortName
                        ? `<span>(${element.symbol})</span>`
                        : '';

                    const nameHtml = settings.showCurrencyName
                        ? `<h3 class="bdt-crypto-currency-ticker-title">${element.id} ${symbolHtml}</h3>`
                        : '';

                    const priceHtml = settings.showCurrencyCurrentPrice
                        ? `<span class="bdt-crypto-currency-ticker-price">${amount}</span>`
                        : '';

                    const pctHtml = settings.showPriceChangePercentage
                        ? `<div class="bdt-crypto-currency-ticker-percentage">
                                <svg width="25" height="22" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M6.646 11.646a.5.5 0 01.708 0L10 14.293l2.646-2.647a.5.5 0 01.708.708l-3 3a.5.5 0 01-.708 0l-3-3a.5.5 0 010-.708z" clip-rule="evenodd"/>
                                    <path fill-rule="evenodd" d="M10 4.5a.5.5 0 01.5.5v9a.5.5 0 01-1 0V5a.5.5 0 01.5-.5z" clip-rule="evenodd"/>
                                </svg>
                                <span>${oneHourPct}</span>
                            </div>`
                        : '';

                    listEl.insertAdjacentHTML('beforeend',
                        `<li class="bdt-crypto-currency-ticker-item" data-id="${element.id}">
                            <div class="bdt-crypto-currency-ticker-inner-item">
                                ${imgHtml}
                                <div class="bdt-crypto-currency-ticker-content">
                                    ${nameHtml}
                                    ${priceHtml}
                                    ${pctHtml}
                                </div>
                            </div>
                            <span class="bdt-price-text bdt-hidden">${element.current_price}</span>
                        </li>`
                    );
                });

            } catch (e) {
                console.error('Crypto ticker initial data fetch failed:', e);
            }
        };

        loadInitialData();

        if (!editMode) {
            // Start live polling and ticker plugin after initial data has had time to load
            setTimeout(() => {
                getData();
                jQuery(cryptoWidget).epNewsTicker(tickerSettings); // jQuery required for epNewsTicker plugin
            }, 7000);
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (window.elementorFrontend?.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-ticker.default', widgetCrypto);
        }
    });

})();

/**
 * End crypto currency ticker widget script
 */

;
(function ($, elementor) {
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            RealisticShadow;

        RealisticShadow = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    allowHTML: true,
                };
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('element_pack_ris_') !== -1) {
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('element_pack_ris_' + key);
            },

            run: function () {
                const widgetID = this.$element.data('id');
                let widgetContainer = $('.elementor-element-' + widgetID);
                const obj = this;

                if ('yes' !== this.settings('enable')) {
                    return;
                }

                if (this.settings('selector')) {
                    widgetContainer = $('.elementor-element-' + widgetID).find(this.settings('selector'));
                }

                const $image = widgetContainer.find('img');

                $image.each(function () {
                    const $this = $(this);
                    if (!$this.hasClass('element-pack-ris-image')) {
                        const $duplicateImage = $this.clone();
                        $duplicateImage.addClass('element-pack-ris-image');

                        const $existingImages = $($this).parent().find('.element-pack-ris-image');
                        if ($existingImages.length > 1) {
                            $existingImages.not(':first').remove();
                        }

                        if ($existingImages.length < 1) {
                            $($this).parent().append($duplicateImage);
                        }

                        widgetContainer.addClass('bdt-realistic-image-shadow');

                        if (obj.settings('on_hover') === 'yes') {
                            widgetContainer.addClass('bdt-hover');
                        }
                    }
                });

            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(RealisticShadow, {
                $element: $scope
            });
        });
    });
})(jQuery, window.elementorFrontend);

;
(function ($, elementor) {
    'use strict';

    var LEGACY_HEADING_SELECTOR = '.elementor-heading-title';
    var ATOMIC_HEADING_SELECTOR = '.e-heading-base';
    var TGB_CLASS = 'element-pack-tgb-background';
    var TGB_ENABLED_CLASS = 'bdt-tgb-yes';

    // In the Elementor editor preview the dedicated editor script owns styling —
    // it reads the authoritative container model. This frontend script cannot
    // read the group-control sub-fields here, so if it also ran it would rebuild
    // the default gradient and fight the editor script (reverting live edits).
    function isEditMode() {
        return !!(window.elementorFrontend && elementorFrontend.isEditMode && elementorFrontend.isEditMode());
    }

    function parseJSON(value) {
        if (!value || 'string' !== typeof value) {
            return null;
        }

        try {
            return JSON.parse(value);
        } catch (error) {
            return null;
        }
    }

    function unwrapAtomicValue(value) {
        if (value && 'object' === typeof value && Object.prototype.hasOwnProperty.call(value, 'value')) {
            return unwrapAtomicValue(value.value);
        }

        return value;
    }

    function isEnabled(value) {
        value = unwrapAtomicValue(value);

        return true === value || 'yes' === value || 1 === value || '1' === value;
    }

    function stringValue(value, fallback) {
        value = unwrapAtomicValue(value);

        return 'string' === typeof value && '' !== value ? value : fallback;
    }

    function numberValue(value, fallback) {
        value = unwrapAtomicValue(value);

        if (value && 'object' === typeof value) {
            if ('undefined' !== typeof value.size) {
                value = value.size;
            } else if ('undefined' !== typeof value.offset) {
                value = value.offset;
            }
        }

        value = parseFloat(value);

        return isNaN(value) ? fallback : value;
    }

    function hasLegacyGroupSettings(settings) {
        var prefix = 'element_pack_tgb_background';

        return '' !== stringValue(settings[prefix + '_background'], '') ||
            '' !== stringValue(settings[prefix + '_color'], '') ||
            '' !== stringValue(settings[prefix + '_color_b'], '');
    }

    function formatNumber(value) {
        return parseFloat(value.toFixed(4)).toString();
    }

    function isNormalizedSettings(settings) {
        return settings && 'yes' === settings.enable && 'string' === typeof settings.background && '' !== settings.background;
    }

    // Whether the raw element settings actually carry gradient inputs. On the
    // frontend the Group_Control_Background sub-fields are NOT exposed as
    // element settings, so this returns false there and we must not rebuild the
    // gradient from empty settings.
    function hasBackgroundInput(settings) {
        if (!settings) {
            return false;
        }

        if ('' !== stringValue(settings.element_pack_tgb_background, '')) {
            return true;
        }

        if (hasLegacyGroupSettings(settings)) {
            return true;
        }

        return '' !== stringValue(settings.element_pack_tgb_background_start_color, '') ||
            '' !== stringValue(settings.element_pack_tgb_background_end_color, '') ||
            null != unwrapAtomicValue(settings.element_pack_tgb_background_start_color_location) ||
            null != unwrapAtomicValue(settings.element_pack_tgb_background_end_color_location) ||
            null != unwrapAtomicValue(settings.element_pack_tgb_background_angle);
    }

    // Read the already-normalized settings the server injected onto a target's
    // data attribute. This is the source of truth on the frontend.
    function readSettingsFromTargets($targets) {
        var result = null;

        if (!$targets || !$targets.length) {
            return null;
        }

        $targets.each(function () {
            if (result) {
                return;
            }

            var parsed = normalizeSettings(parseJSON(this.getAttribute('data-ep-text-gradient-background')));

            if (parsed) {
                result = parsed;
            }
        });

        return result;
    }

    function buildAtomicGradient(settings) {
        var startColor = stringValue(settings.element_pack_tgb_background_start_color, '');
        var endColor = stringValue(settings.element_pack_tgb_background_end_color, '');
        var startLocation = numberValue(settings.element_pack_tgb_background_start_color_location, 0);
        var endLocation = numberValue(settings.element_pack_tgb_background_end_color_location, 100);
        var angle = numberValue(settings.element_pack_tgb_background_angle, 90);

        if ('' === startColor) {
            startColor = '#08aeec';
        }

        if ('' === endColor) {
            endColor = '#2af598';
        }

        return 'linear-gradient(' + formatNumber(angle) + 'deg, ' + startColor + ' ' + formatNumber(startLocation) + '%, ' + endColor + ' ' + formatNumber(endLocation) + '%)';
    }

    function normalizeLegacyGroupBackground(settings) {
        var prefix = 'element_pack_tgb_background';
        var type = stringValue(settings[prefix + '_background'], '');
        var startColor = stringValue(settings[prefix + '_color'], '');
        var endColor = stringValue(settings[prefix + '_color_b'], '');
        var startLocation = numberValue(settings[prefix + '_color_stop'], 0);
        var endLocation = numberValue(settings[prefix + '_color_b_stop'], 100);
        var angle = numberValue(settings[prefix + '_gradient_angle'], 90);

        // This group control only supports gradients, so an unset type must be
        // treated as a gradient (never "classic"/solid) — otherwise a heading
        // that only sets the first color would collapse to a solid fill.
        if ('' === type) {
            type = 'gradient';
        }

        if ('classic' === type) {
            return startColor ? 'linear-gradient(0deg, ' + startColor + ' ' + formatNumber(startLocation) + '%, ' + startColor + ' ' + formatNumber(endLocation) + '%)' : '';
        }

        if ('gradient' !== type) {
            return '';
        }

        if ('' === startColor) {
            startColor = '#08aeec';
        }

        if ('' === endColor) {
            endColor = '#2af598';
        }

        return 'linear-gradient(' + formatNumber(angle) + 'deg, ' + startColor + ' ' + formatNumber(startLocation) + '%, ' + endColor + ' ' + formatNumber(endLocation) + '%)';
    }

    function normalizeSettings(settings) {
        var background;

        if (!settings) {
            return null;
        }

        if (isNormalizedSettings(settings)) {
            return settings;
        }

        if (!isEnabled(settings.element_pack_tgb_enable)) {
            return null;
        }

        background = stringValue(settings.element_pack_tgb_background, '');

        if (!background && hasLegacyGroupSettings(settings)) {
            background = normalizeLegacyGroupBackground(settings);
        }

        if (!background) {
            background = buildAtomicGradient(settings);
        }

        return {
            enable: 'yes',
            selector: stringValue(settings.element_pack_tgb_selector, ''),
            background: background
        };
    }

    function getDefaultSelector(widgetType) {
        if (!widgetType) {
            return '';
        }

        if (0 === widgetType.indexOf('e-heading')) {
            return ATOMIC_HEADING_SELECTOR;
        }

        if (0 === widgetType.indexOf('heading')) {
            return LEGACY_HEADING_SELECTOR;
        }

        return '';
    }

    function resolveSelector(settings, widgetType) {
        return settings.selector || getDefaultSelector(widgetType);
    }

    function cleanupTextGradientStyles($elements) {
        if (!$elements || !$elements.length) {
            return;
        }

        $elements.each(function () {
            var $this = $(this);

            $this.removeClass(TGB_CLASS + ' ' + TGB_ENABLED_CLASS);
            $this.removeAttr('data-ep-text-gradient-background');
            $this.css({
                'background-color': '',
                'background-image': '',
                'background-clip': '',
                '-webkit-background-clip': '',
                'color': '',
                '-webkit-text-fill-color': '',
                'text-fill-color': ''
            });
        });
    }

    function applyTextGradientStyles($elements, settings) {
        if (!$elements.length || !isNormalizedSettings(settings)) {
            return;
        }

        // Only atomic (e-heading) widgets need the gradient painted inline. For
        // classic/common widgets Elementor's Group_Control_Background already
        // paints it on .element-pack-tgb-background, so we must not override it.
        var applyBackground = true === settings.apply_background;

        $elements.each(function () {
            var element = this;

            element.classList.add(TGB_CLASS, TGB_ENABLED_CLASS);
            element.setAttribute('data-ep-text-gradient-background', JSON.stringify(settings));

            if (applyBackground) {
                element.style.setProperty('background-color', 'transparent', 'important');
                element.style.setProperty('background-image', settings.background);
            } else {
                element.style.removeProperty('background-image');
                element.style.removeProperty('background-color');
            }

            element.style.setProperty('background-clip', 'text', 'important');
            element.style.setProperty('-webkit-background-clip', 'text', 'important');
            element.style.setProperty('color', 'transparent', 'important');
            element.style.setProperty('-webkit-text-fill-color', 'transparent', 'important');
            element.querySelectorAll('a').forEach(function (link) {
                link.style.setProperty('color', 'transparent', 'important');
                link.style.setProperty('-webkit-text-fill-color', 'transparent', 'important');
            });
        });
    }

    function applyTextGradientToElement(element) {
        var settings;

        if (!element || !element.getAttribute || isEditMode()) {
            return;
        }

        settings = normalizeSettings(parseJSON(element.getAttribute('data-ep-text-gradient-background')));

        if (!settings) {
            cleanupTextGradientStyles(jQuery(element));
            return;
        }

        applyTextGradientStyles(jQuery(element), settings);
    }

    function runTextGradientBackground() {
        jQuery(
            '.elementor-heading-title[data-ep-text-gradient-background], ' +
            '.e-heading-base[data-ep-text-gradient-background], ' +
            '[data-ep-text-gradient-background]'
        ).each(function () {
            applyTextGradientToElement(this);
        });
    }

    function runEarlyTextGradientBackground() {
        if (!document.querySelectorAll) {
            return;
        }

        document.querySelectorAll('[data-ep-text-gradient-background]').forEach(function (element) {
            applyTextGradientToElement(element);
        });
    }

    if ('loading' === document.readyState) {
        document.addEventListener('DOMContentLoaded', runEarlyTextGradientBackground);
    } else {
        runEarlyTextGradientBackground();
    }

    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            TextGradientBackground;

        TextGradientBackground = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    allowHTML: true,
                };
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('element_pack_tgb_') !== -1) {
                    this.run();
                }
            }, 400),

            getRawElementSettings: function () {
                return {
                    element_pack_tgb_enable: this.getElementSettings('element_pack_tgb_enable'),
                    element_pack_tgb_selector: this.getElementSettings('element_pack_tgb_selector'),
                    element_pack_tgb_background: this.getElementSettings('element_pack_tgb_background'),
                    element_pack_tgb_background_background: this.getElementSettings('element_pack_tgb_background_background'),
                    element_pack_tgb_background_color: this.getElementSettings('element_pack_tgb_background_color'),
                    element_pack_tgb_background_color_b: this.getElementSettings('element_pack_tgb_background_color_b'),
                    element_pack_tgb_background_gradient_angle: this.getElementSettings('element_pack_tgb_background_gradient_angle'),
                    element_pack_tgb_background_start_color: this.getElementSettings('element_pack_tgb_background_start_color'),
                    element_pack_tgb_background_end_color: this.getElementSettings('element_pack_tgb_background_end_color'),
                    element_pack_tgb_background_start_color_location: this.getElementSettings('element_pack_tgb_background_start_color_location'),
                    element_pack_tgb_background_end_color_location: this.getElementSettings('element_pack_tgb_background_end_color_location'),
                    element_pack_tgb_background_angle: this.getElementSettings('element_pack_tgb_background_angle'),
                    element_pack_tgb_background_color_stop: this.getElementSettings('element_pack_tgb_background_color_stop'),
                    element_pack_tgb_background_color_b_stop: this.getElementSettings('element_pack_tgb_background_color_b_stop')
                };
            },

            getNormalizedSettings: function () {
                return normalizeSettings(this.getRawElementSettings());
            },

            getTargetElements: function () {
                var widgetType = this.$element.data('widget_type') || '';
                var normalizedSettings = this.getNormalizedSettings();
                var selector;
                var $targets;

                if (!normalizedSettings) {
                    return this.$element.find('.' + TGB_CLASS);
                }

                selector = resolveSelector(normalizedSettings, widgetType);

                if (!selector) {
                    return $();
                }

                $targets = this.$element.find(selector);

                return $targets.length ? $targets : $();
            },

            run: function () {
                // The editor script is authoritative in the editor preview;
                // staying out avoids the two scripts racing (this handler can't
                // read the group-control sub-fields here and would revert to the
                // default gradient shortly after each live edit).
                if (isEditMode()) {
                    return;
                }

                var rawSettings = this.getRawElementSettings();
                var normalizedSettings = normalizeSettings(rawSettings);
                var $targets = this.getTargetElements();
                var widgetType = this.$element.data('widget_type') || '';
                var isAtomic = 0 === widgetType.indexOf('e-heading');

                // On the frontend the gradient sub-fields are not exposed as
                // element settings, so normalizeSettings() would rebuild the
                // DEFAULT gradient and clobber the correct value the server
                // already normalized onto the target. When the live settings
                // carry no gradient input, trust the data attribute instead.
                if (normalizedSettings && !hasBackgroundInput(rawSettings)) {
                    var domSettings = readSettingsFromTargets($targets);

                    if (domSettings) {
                        normalizedSettings = domSettings;
                    }
                }

                if (!normalizedSettings) {
                    cleanupTextGradientStyles($targets);
                    return;
                }

                // This runs only on the real frontend (edit mode returned above).
                // The data attribute carries the flag; fall back to widget type
                // only when building fresh. Classic/common widgets keep using
                // Elementor's generated CSS (apply_background stays false).
                if ('undefined' === typeof normalizedSettings.apply_background) {
                    normalizedSettings.apply_background = isAtomic;
                }

                applyTextGradientStyles($targets, normalizedSettings);
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(TextGradientBackground, {
                $element: $scope
            });
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/heading.default', function ($scope) {
            if ($scope && $scope[0]) {
                $scope[0].querySelectorAll(LEGACY_HEADING_SELECTOR + ', [data-ep-text-gradient-background]').forEach(function (element) {
                    applyTextGradientToElement(element);
                });
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/e-heading.default', function ($scope) {
            if ($scope && $scope[0]) {
                $scope[0].querySelectorAll(ATOMIC_HEADING_SELECTOR + ', [data-ep-text-gradient-background]').forEach(function (element) {
                    applyTextGradientToElement(element);
                });
            }
        });

        runTextGradientBackground();
    });
})(jQuery, window.elementorFrontend);

/**
 * Start Floating Knowledgebase widget script
 */

(() => {
    'use strict';

    const searchSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M15.2 16.34a7.5 7.5 0 1 1 1.38-1.45l4.2 4.2a1 1 0 1 1-1.42 1.41l-4.16-4.16zm-4.7.16a6 6 0 1 0 0-12 6 6 0 0 0 0 12z"></path></svg>';
    const crossSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M13.06 12.15l5.02-5.03a.75.75 0 1 0-1.06-1.06L12 11.1 6.62 5.7a.75.75 0 1 0-1.06 1.06l5.38 5.38-5.23 5.23a.75.75 0 1 0 1.06 1.06L12 13.2l4.88 4.87a.75.75 0 1 0 1.06-1.06l-4.88-4.87z"></path></svg>';
    const resizerSVG = '<svg class="bdt-expand" xmlns="http://www.w3.org/2000/svg" height="24" width="24" fill="currentColor"><path d="M2.675 21.325v-8.65h2.65v4.15l11.5-11.5h-4.15v-2.65h8.65v8.65h-2.65v-4.15l-11.5 11.5h4.15v2.65Z"></path></svg>' +
        '<svg class="bdt-close" xmlns="http://www.w3.org/2000/svg" height="24" width="24" fill="currentColor"><path d="m3.075 22.775-1.85-1.85L7.5 14.65H3.35V12H12v8.65H9.35V16.5ZM12 12V3.35h2.65V7.5l6.275-6.275 1.85 1.85L16.5 9.35h4.15V12Z"></path></svg>';
    const backSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M15.45 17.97L9.5 12.01a.25.25 0 0 1 0-.36l5.87-5.87a.75.75 0 0 0-1.06-1.06l-5.87 5.87c-.69.68-.69 1.8 0 2.48l5.96 5.96a.75.75 0 0 0 1.06-1.06z"></path></svg>';
    const listArrowSVG = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path fill="currentColor" d="M6.47 4.29l3.54 3.53c.1.1.1.26 0 .36L6.47 11.7a.75.75 0 1 0 1.06 1.06l3.54-3.53c.68-.69.68-1.8 0-2.48L7.53 3.23a.75.75 0 0 0-1.06 1.06z"></path></svg>';
    const externalArrowSVG = '<svg fill="#3a3f3f" width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M11.268 5.824L5.232 11.86a.75.75 0 1 1-1.06-1.06L10.22 4.75H5.75a.75.75 0 0 1 0-1.5h6.268a.75.75 0 0 1 .75.75v6.243a.75.75 0 0 1-1.5 0v-4.42z" fill="currentColor"></path></svg>';

    const el = (tag, attrs = {}) => {
        const e = document.createElement(tag);
        Object.entries(attrs).forEach(([k, v]) => {
            if (k === 'class') e.className = v;
            else if (k === 'html' || k === 'innerHTML') e.innerHTML = v;
            else if (k === 'textContent') e.textContent = v;
            else if (k === 'id' || k === 'type' || k === 'placeholder' || k === 'target' || k === 'href') e.setAttribute(k, v);
            else if (v != null && k in e) e[k] = v;
        });
        return e;
    };

    const widgetFloatingKnowledgebase = (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const floatingEl = scopeEl.querySelector('.bdt-floating-knowledgebase');
        if (!floatingEl) return;

        const containerEl = scopeEl.querySelector('#bdt-floating-help-center');
        const btnIconEl  = floatingEl.querySelector('.bdt-icon-wrapper');
        const rawSettings = floatingEl.dataset.settings;
        const settings   = rawSettings ? JSON.parse(rawSettings) : {};
        if (!settings) return;

        let nodesStack = [];
        let titleStack = [];
        let jsonData;

        const helpCenterConfig = {
            linkColor      : '#007bff',
            showHelperText  : true,
            helperTextLabel : settings.helperTextLabel || "Have any queries?<br /><strong>Check Help Center</strong>",
            showContactUsLink: true,
            contactUsLabel  : settings.supportLinkText || "Still no luck? We can help!",
            contactUsLink   : settings.supportLink || '/contact-us',
            noResultsLabel  : settings.noSearchResultText || "Sorry, we don't have any results. Updates are being added all the time.",
            resetOnPopupClose: false,
            btnZindex       : 999,
            popupZindex     : 998,
            onPopupOpen     : () => {},
            onPopupClose    : () => {},
        };

        const togglePopup = () => {
            const popup   = containerEl.querySelector('.floating-help-center__popup');
            const popBtn  = containerEl.querySelector('.floating-help-center__btn');
            const activeClass = 'floating-help-center__popup--active';
            if (popup?.classList.contains(activeClass)) {
                popup.classList.remove(activeClass);
                popBtn?.classList.remove(activeClass);
                helpCenterConfig.onPopupClose.call(this);
            } else {
                if (helpCenterConfig.resetOnPopupClose) resetPopupContent();
                popup?.classList.add(activeClass);
                popBtn?.classList.add(activeClass);
                helpCenterConfig.onPopupOpen.call(this);
            }
        };

        const resetPreviousState = () => {
            containerEl.querySelectorAll('#htmlContent, #noResultTxt').forEach(n => n.remove());
            const listContainer = containerEl.querySelector('#listItemsContainer');
            if (listContainer) listContainer.innerHTML = '';
            containerEl.querySelector('#floatingHelpCenterPopup')?.classList.remove('bdt-content-open');
        };

        const setInputTitle = (title) => {
            const input = containerEl.querySelector('.searchbox__input');
            if (!input) return;
            if (title && title !== '') {
                input.value = ' ';
                titleStack.push(title);
            } else {
                input.value = '';
            }
        };

        const findObject = (obj, key, value, performSearch) => {
            const searchVal = performSearch ? value.toUpperCase() : value;
            const results = [];

            const recursiveSearch = (o) => {
                if (!o || typeof o !== 'object') return;
                if (performSearch) {
                    if (o[key] && String(o[key]).toUpperCase().indexOf(searchVal) > -1) results.push(o);
                } else {
                    if (o[key] === value) results.push(o);
                }
                Object.keys(o).forEach(k => recursiveSearch(o[k]));
            };
            recursiveSearch(obj);
            return results;
        };

        const searchInputReadonlyToggle = () => {
            const input = containerEl.querySelector('.searchbox__input');
            if (input) input.readOnly = nodesStack.length > 1;
        };

        let backBtnHandlerRef = null;

        const toggleBackButton = () => {
            const searchIcon = containerEl.querySelector('.searchbox__search-icon');
            if (!searchIcon) return;

            if (backBtnHandlerRef) {
                searchIcon.removeEventListener('click', backBtnHandlerRef);
                backBtnHandlerRef = null;
            }

            const popup = containerEl.querySelector('#floatingHelpCenterPopup');
            if (nodesStack.length > 1) {
                searchIcon.innerHTML = backSVG;
                backBtnHandlerRef = backBtnHandler;
                searchIcon.addEventListener('click', backBtnHandlerRef);
                popup?.classList.add('bdt-content-expand');
            } else {
                searchIcon.innerHTML = searchSVG;
                popup?.classList.remove('bdt-content-expand');
            }
        };

        const backBtnHandler = () => {
            nodesStack.pop();
            titleStack.pop();
            const lastNode  = nodesStack.pop();
            const lastTitle = titleStack.pop();
            setInputTitle(lastTitle);
            setPopupContent(lastNode);
            searchInputReadonlyToggle();
        };

        const beforeSetPopupContent = (data, event) => {
            if (event === undefined || event !== 'search') nodesStack.push(data);
            toggleBackButton();
            resetPreviousState();
        };

        const renderNoResults = () => {
            resetPreviousState();
            if (containerEl.querySelector('#noResultTxt')) return;
            const noResult = el('p', { id: 'noResultTxt', class: 'no-result', html: helpCenterConfig.noResultsLabel });
            const searchbox = containerEl.querySelector('.searchbox');
            searchbox?.insertAdjacentElement('afterend', noResult);
        };

        const renderPopupContentList = (data) => {
            const listContainer = containerEl.querySelector('#listItemsContainer');
            if (!listContainer) return;

            data.forEach(listObj => {
                const li = el('li', { class: 'help-list__item' });
                const arrow = el('span', { class: 'help-list__item-arrow', html: listArrowSVG });
                const txt  = el('span', { class: 'help-list__item-txt', html: listObj.title });
                li.appendChild(txt);
                li.appendChild(arrow);
                li.addEventListener('click', () => {
                    const listTitle = li.querySelector('.help-list__item-txt')?.textContent ?? '';
                    const matchedObj = findObject(jsonData, 'title', listTitle);
                    setInputTitle(listTitle);
                    setPopupContent(matchedObj);
                    searchInputReadonlyToggle();
                });
                listContainer.appendChild(li);
            });
        };

        const anchorDataTitleHandler = () => {
            containerEl.querySelectorAll('#htmlContent a').forEach(a => {
                a.addEventListener('click', function (e) {
                    const dataTitle = this.getAttribute('data-title');
                    if (dataTitle && dataTitle !== '') {
                        e.preventDefault();
                        const resultsArr = findObject(jsonData, 'title', dataTitle);
                        setInputTitle(dataTitle);
                        setPopupContent(resultsArr);
                    }
                });
            });
        };

        const renderHTML = (title, htmlContent) => {
            containerEl.querySelector('.searchbox__cross-icon')?.style.setProperty('display', 'none');
            const htmlContentEl = el('div', { id: 'htmlContent', class: 'html-content', html: htmlContent });
            const input = containerEl.querySelector('.searchbox__input');
            const articleTitle = title || (input?.value ?? '').trim();
            const h5 = el('h5', { id: 'contentTitle', class: 'html-content__title', html: articleTitle });
            htmlContentEl.insertBefore(h5, htmlContentEl.firstChild);

            htmlContentEl.querySelectorAll('a:not(.callout-block a)').forEach(a => {
                a.style.color = helpCenterConfig.linkColor;
            });

            const listContainer = containerEl.querySelector('#listItemsContainer');
            listContainer?.parentNode?.insertBefore(htmlContentEl, listContainer);

            containerEl.querySelector('#floatingHelpCenterPopup')?.classList.add('bdt-content-open');
            anchorDataTitleHandler();
        };

        const setPopupContent = (data, event) => {
            const arr = Array.isArray(data) ? data : (data ? [data] : []);
            if (arr.length === 0) {
                renderNoResults();
                return;
            }
            if (arr.length > 1) {
                beforeSetPopupContent(arr, event);
                renderPopupContentList(arr);
                return;
            }
            if (event === 'search') {
                beforeSetPopupContent(arr, event);
                renderPopupContentList(arr);
                return;
            }
            const destructuredObj = arr.pop();
            if (typeof destructuredObj === 'string') {
                beforeSetPopupContent([destructuredObj], event);
                renderHTML('', destructuredObj);
                return;
            }
            if (Object.prototype.hasOwnProperty.call(destructuredObj, 'nodes')) {
                beforeSetPopupContent(destructuredObj.nodes, event);
                renderPopupContentList(destructuredObj.nodes);
                return;
            }
            beforeSetPopupContent([destructuredObj.html], event);
            renderHTML(destructuredObj.title, destructuredObj.html);
        };

        const resetPopupContent = () => {
            nodesStack = [];
            titleStack = [];
            setInputTitle('');
            containerEl.querySelector('.searchbox__cross-icon')?.style.setProperty('display', 'none');
            setPopupContent(jsonData);
            containerEl.querySelectorAll('#htmlContent, #noResultTxt').forEach(n => n.remove());
        };

        const searchInputHandler = function () {
            const crossIcon = containerEl.querySelector('.searchbox__cross-icon');
            const query = this.value ?? '';
            if (query.trim() !== '') {
                crossIcon?.style.setProperty('display', 'block');
                const resultsArr = findObject(jsonData, 'title', query, true);
                setPopupContent(resultsArr, 'search');
            } else {
                crossIcon?.style.setProperty('display', 'none');
                resetPopupContent();
            }
        };

        const renderHelpCenterBtn = () => {
            const btnWrap = el('div', { class: 'floating-help-center__btn' });
            btnWrap.style.zIndex = helpCenterConfig.btnZindex;
            btnWrap.addEventListener('click', togglePopup);

            const btn = el('button', { class: 'btn' });
            if (btnIconEl) {
                btnIconEl.classList.remove('bdt-hidden');
                btn.appendChild(btnIconEl);
            }

            if (helpCenterConfig.showHelperText) {
                const helperTxt = el('p', { class: 'helper-txt', html: helpCenterConfig.helperTextLabel });
                btnWrap.appendChild(helperTxt);
            }
            btnWrap.appendChild(btn);
            containerEl.appendChild(btnWrap);
        };

        const renderPopup = (data) => {
            const outerWrap = el('div', { id: 'floatingHelpCenterPopup', class: 'floating-help-center__popup' });
            outerWrap.style.zIndex = helpCenterConfig.popupZindex;

            const searchOuter = el('div', { class: 'searchbox' });
            const searchIcon = el('div', { class: 'searchbox__search-icon', html: searchSVG });
            const input = el('input', { class: 'searchbox__input', type: 'text', placeholder: 'Search...' });
            const crossIcon = el('div', { class: 'searchbox__cross-icon', html: crossSVG });
            crossIcon.style.display = 'none';
            const resizerIcon = el('div', { class: 'bdt-resizer-icon', html: resizerSVG });

            crossIcon.addEventListener('click', resetPopupContent);
            resizerIcon.addEventListener('click', () => outerWrap.classList.toggle('bdt-content-expand'));
            input.addEventListener('input', searchInputHandler);

            searchOuter.appendChild(searchIcon);
            searchOuter.appendChild(input);
            searchOuter.appendChild(crossIcon);
            searchOuter.appendChild(resizerIcon);

            const helpList = el('ul', { id: 'listItemsContainer', class: 'help-list' });

            const externalLinkWrap = el('div', { id: 'externalLinkWrap', class: 'external' });
            const externalLink = el('a', { class: 'external__link', target: '_blank', href: helpCenterConfig.contactUsLink });
            externalLink.textContent = helpCenterConfig.contactUsLabel;
            const externalArrow = el('span', { class: 'external__arrow', html: externalArrowSVG });
            externalLink.appendChild(externalArrow);
            externalLinkWrap.appendChild(externalLink);

            const headerWrap = el('div', { id: 'headerWrap', class: 'bdt-header' });

            if (settings.logo?.url) {
                const logoAlt = settings.title || settings.logo?.alt;
                headerWrap.insertAdjacentHTML('beforeend', `<div class="bdt-header-logo"><img src="${settings.logo.url}" alt="${logoAlt}"></div>`);
            }
            if (settings.title) {
                headerWrap.insertAdjacentHTML('beforeend', `<div class="bdt-header-title">${settings.title}</div>`);
            }
            if (settings.description) {
                headerWrap.insertAdjacentHTML('beforeend', `<div class="bdt-header-description">${settings.description}</div>`);
            }

            outerWrap.appendChild(headerWrap);
            outerWrap.appendChild(searchOuter);
            outerWrap.appendChild(helpList);
            if (helpCenterConfig.showContactUsLink) outerWrap.appendChild(externalLinkWrap);

            containerEl.appendChild(outerWrap);
            setPopupContent(data);
        };

        jsonData = settings.data_source || [];
        renderHelpCenterBtn();
        renderPopup(jsonData);

        window.floatingHelpCenter = {
            init() {},
            toggle: togglePopup,
            isOpen() {
                return containerEl?.querySelector('.floating-help-center__popup')?.classList.contains('floating-help-center__popup--active') ?? false;
            },
        };
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-floating-knowledgebase.default', widgetFloatingKnowledgebase);
    });

})();

/**
 * End Floating Knowledgebase widget script
 */

/**
 * Start facebook feed carousel widget script
 */

(() => {
    'use strict';

    const widgetFbFeedCarousel = async (scope) => {
        const scopeEl = scope?.jquery ? scope[0] : scope;

        const carouselEl = scopeEl.querySelector('.bdt-facebook-feed-carousel');
        if (!carouselEl) return;

        const containerEl = carouselEl.querySelector('.swiper-carousel');
        const settings    = JSON.parse(carouselEl.dataset.settings || '{}');

        const Swiper = elementorFrontend.utils.swiper;
        await new Swiper(containerEl, settings);

        if (settings.pauseOnHover) {
            containerEl.addEventListener('mouseenter', () => containerEl.swiper.autoplay.stop());
            containerEl.addEventListener('mouseleave', () => containerEl.swiper.autoplay.start());
        }
    };

    window.addEventListener('elementor/frontend/init', () => {
        if (!window.elementorFrontend?.hooks) return;

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-facebook-feed-carousel.default', widgetFbFeedCarousel);
    });

})();

/**
 * End facebook feed carousel widget script
 */

/**
 * Start background image parallax widget script
 */

(() => {
  "use strict";

  window.addEventListener("elementor/frontend/init", () => {
    const ModuleHandler = elementorModules.frontend.handlers.Base;

    const BackgroundImageParallaxHandler = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },

      getDefaultSettings: function () {
        return {
          orientation: "left",
        };
      },

      onElementChange: debounce(function (prop) {
        if (prop.indexOf("ep_background_image_parallax_") !== -1) {
          this.run();
        }
      }, 400),

      settings: function (key) {
        return this.getElementSettings(`ep_background_image_parallax_${key}`);
      },

      run: function () {
        const options = this.getDefaultSettings();
        const widgetID = this.$element.data("id");

        const images = document.querySelectorAll(
          `.elementor-element-${widgetID}.bdt-background-image-parallax-yes img`
        );

        if (images.length === 0) return;

        if (this.settings("orientation")) {
          options.orientation = this.settings("orientation");
        }

        if (this.settings("scale.size")) {
          options.scale = this.settings("scale.size");
        }

        if (this.settings("delay.size")) {
          options.delay = this.settings("delay.size");
        }

        options.overflow = this.settings("overflow") === "yes";

        if (typeof SimpleParallax === "undefined") {
          console.error("SimpleParallax library is not loaded");
          return;
        }

        new SimpleParallax(images, options);
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/widget",
      (scope) => {
        elementorFrontend.elementsHandler.addHandler(BackgroundImageParallaxHandler, {
          $element: scope,
        });
      }
    );
  });
})();

/**
 * End background image parallax widget script
 */

/**
 * Start shape builder widget script
 */

jQuery(window).on('elementor/frontend/init', () => {

  const ANIMATION_PRESETS = {
    'fade-in': { opacity: 0 },
    'fade-in-up': { opacity: 0, y: 50 },
    'fade-in-down': { opacity: 0, y: -50 },
    'fade-in-left': { opacity: 0, x: -50 },
    'fade-in-right': { opacity: 0, x: 50 },
    'zoom-in': { scale: 0 },
    'zoom-out': { scale: 2 },
    'rotate-in': { rotation: -360 },
    'flip-x': { rotationX: 180 },
    'flip-y': { rotationY: 180 },
    'bounce': { y: -30 },
    'pulse': { scale: 0.9 },
    'swing': { rotation: 15 },
    'shake': { x: -10 },
    'slide-in-left': { x: -100 },
    'slide-in-right': { x: 100 },
    'slide-in-up': { y: 100 },
    'slide-in-down': { y: -100 }
  };

  const MOTION_ANIMATIONS = ['bounce', 'pulse', 'swing', 'shake'];

  const getAnimationProps = (name) => ANIMATION_PRESETS[name] || { opacity: 0 };

  const buildTargetProps = (fromProps, options = {}) => {
    const toProps = {
      ...options,
      transformOrigin: 'center center'
    };

    if ('opacity' in fromProps) toProps.opacity = 1;
    if ('x' in fromProps) toProps.x = 0;
    if ('y' in fromProps) toProps.y = 0;
    if ('scale' in fromProps) toProps.scale = 1;
    if ('rotation' in fromProps) toProps.rotation = 0;
    if ('rotationX' in fromProps) toProps.rotationX = 0;
    if ('rotationY' in fromProps) toProps.rotationY = 0;

    return toProps;
  };

  const applyShapeToWrapper = () => {
    const shapes = document.querySelectorAll('.bdt-shape-builder');

    shapes.forEach(el => {
      const wrapperClass = el.dataset.wrapperId;
      if (!wrapperClass) return;

      const wrapper = document.querySelector(`.${wrapperClass}`);
      if (wrapper) {
        wrapper.appendChild(el);
      }
    });
  };

  const initOnLoadAnimations = () => {
    const shapes = document.querySelectorAll('.bdt-shape-builder[data-animation-enabled="true"][data-animation-trigger="on-load"]');

    shapes.forEach(el => {
      const animationName = el.dataset.animationName;
      const duration = parseFloat(el.dataset.animationDuration) || 1;
      const delay = parseFloat(el.dataset.animationDelay) || 0;
      const easing = el.dataset.animationEasing || 'none';
      const repeat = parseInt(el.dataset.animationRepeat, 10) || 0;
      const yoyo = el.dataset.animationYoyo;
      const viewport = parseFloat(el.dataset.animationViewport) || 0.1;

      const fromProps = getAnimationProps(animationName);
      const toProps = buildTargetProps(fromProps, { duration, delay, ease: easing, repeat, yoyo });

      // Set initial state immediately to prevent flash/jump
      gsap.set(el, {
        ...fromProps,
        transformOrigin: 'center center'
      });

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            gsap.to(el, toProps);
            observer.unobserve(el);
          }
        });
      }, {
        threshold: viewport,
        rootMargin: '0px'
      });

      observer.observe(el);
    });
  };

  const initOnHoverAnimations = () => {
    const elements = document.querySelectorAll('.elementor-element[data-settings]');

    elements.forEach(parentEl => {
      const settingsAttr = parentEl.getAttribute('data-settings');
      if (!settingsAttr) return;

      let settings;
      try {
        const decodedSettings = jQuery('<textarea/>').html(settingsAttr).text();
        settings = JSON.parse(decodedSettings);
      } catch (e) {
        return;
      }

      if (!settings.bdt_shape_builder_list || !Array.isArray(settings.bdt_shape_builder_list)) {
        return;
      }

      const hoverShapes = settings.bdt_shape_builder_list.filter(shape =>
        shape.shape_builder_animation_popover === 'yes' && shape.animation_trigger_type === 'on-hover'
      );

      if (hoverShapes.length === 0) return;

      const timelines = [];

      hoverShapes.forEach(shapeSettings => {
        const shapeId = shapeSettings._id;
        const shapeEl = parentEl.querySelector(`.bdt-shape-builder.elementor-repeater-item-${shapeId}`);

        if (!shapeEl) return;

        const animationName = shapeSettings.animation_name || 'fade-in';
        const duration = shapeSettings.animation_duration?.size
          ? parseFloat(shapeSettings.animation_duration.size)
          : 1;
        const easing = shapeSettings.animation_easing || 'none';

        const fromProps = getAnimationProps(animationName);

        gsap.set(shapeEl, { opacity: 1 });

        const tl = gsap.timeline({ paused: true });

        if (MOTION_ANIMATIONS.includes(animationName)) {
          tl.to(shapeEl, {
            ...fromProps,
            duration: duration / 2,
            ease: easing,
            yoyo: true,
            repeat: 1
          });
        } else {
          tl.to(shapeEl, {
            ...fromProps,
            duration: duration,
            ease: easing
          });
        }

        timelines.push(tl);
      });

      if (timelines.length > 0) {
        parentEl.addEventListener('mouseenter', () => {
          timelines.forEach(tl => tl.restart());
        });

        parentEl.addEventListener('mouseleave', () => {
          timelines.forEach(tl => tl.reverse());
        });
      }
    });
  };

  const initShapeAnimations = () => {
    initOnLoadAnimations();
    initOnHoverAnimations();
  };

  const initShapeBuilder = () => {
    applyShapeToWrapper();
    initShapeAnimations();
  };

  const elementTypes = ['container', 'section', 'column', 'inner-section'];
  elementTypes.forEach(type => {
    elementorFrontend.hooks.addAction(`frontend/element_ready/${type}`, initShapeBuilder);
  });

  jQuery(window).on('load', initShapeBuilder);
});

/**
 * End shape builder widget script
 */
