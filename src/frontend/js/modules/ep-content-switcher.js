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
