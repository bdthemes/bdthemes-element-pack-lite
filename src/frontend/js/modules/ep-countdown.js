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
