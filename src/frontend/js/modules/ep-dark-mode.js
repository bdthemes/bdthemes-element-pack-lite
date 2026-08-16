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
