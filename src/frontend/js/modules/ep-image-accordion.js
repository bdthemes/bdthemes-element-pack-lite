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
