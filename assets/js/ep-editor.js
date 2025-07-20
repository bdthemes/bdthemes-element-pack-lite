(function($) {
    'use strict';

    const ElementPackEditor = {
        init() {
            elementor.channels.editor.on('section:activated', this.onAnimatedBoxSectionActivated);
            window.elementor.on('preview:loaded', this.onPreviewLoaded);
        },

        onPreviewLoaded() {
            const elementorFrontend = $('#elementor-preview-iframe')[0].contentWindow.elementorFrontend;
            elementorFrontend.hooks.addAction('frontend/element_ready/widget', () => {
                // Template edit link functionality can be added here if needed
            });
        }
    };

    $(window).on('elementor:init', ElementPackEditor.init.bind(ElementPackEditor));
    window.ElementPackEditor = ElementPackEditor;





    elementor.hooks.addFilter("panel/elements/regionViews", function(panel) {
        // Add pro icon styles
        $('body').append('<style>.bdt-pro-unlock-icon:after{right: auto !important; left: 5px !important;}</style>');

        if (ElementPackConfig.pro_installed || ElementPackConfig.promotional_widgets <= 0) {
            return panel;
        }

        const { promotional_widgets: promotionalWidgets } = ElementPackConfig;
        const { collection: elementsCollection } = panel.elements.options;
        const { collection: categories } = panel.categories.options;
        const { view: categoriesView } = panel.categories;
        const { view: elementsView } = panel.elements;
        
        let freeCategoryIndex;
        const proWidgets = [];

        // Add promotional widgets to collection
        promotionalWidgets.forEach(widget => {
            elementsCollection.add({
                name: widget.name,
                title: widget.title,
                icon: widget.icon,
                categories: widget.categories,
                editable: false
            });
        });

        // Filter pro widgets
        elementsCollection.each(widget => {
            if (widget.get("categories")[0] === "element-pack-pro") {
                proWidgets.push(widget);
            }
        });

        // Add pro category
        freeCategoryIndex = categories.findIndex({ name: "element-pack" });
        if (freeCategoryIndex) {
            categories.add({
                name: "element-pack-pro",
                title: "Element Pack Pro",
                defaultActive: false,
                items: proWidgets
            }, { at: freeCategoryIndex + 1 });
        }

        const promotionalWidgetHandler = {
            getWedgetOption(name) {
                return promotionalWidgets.find(item => item.name === name);
            },

            className() {
                let className = 'elementor-element-wrapper';
                if (!this.isEditable()) {
                    className += ' elementor-element--promotion';
                }
                return className;
            },

            onMouseDown() {
                this.constructor.__super__.onMouseDown.call(this);
                const promotion = this.getWedgetOption(this.model.get("name"));
                elementor.promotion.showDialog({
                    title: sprintf(wp.i18n.__('%s', 'elementor'), this.model.get("title")),
                    content: sprintf(wp.i18n.__('Use %s widget and dozens more pro features to extend your toolbox and build sites faster and better.', 'elementor'), this.model.get("title")),
                    targetElement: this.el,
                    position: { blockStart: '-7' },
                    actionButton: {
                        url: promotion.action_button.url,
                        text: promotion.action_button.text,
                        classes: promotion.action_button.classes || ['elementor-button', 'elementor-button-success']
                    }
                });
            }
        };

        panel.elements.view = elementsView.extend({
            childView: elementsView.prototype.childView.extend(promotionalWidgetHandler)
        });

        panel.categories.view = categoriesView.extend({
            childView: categoriesView.prototype.childView.extend({
                childView: categoriesView.prototype.childView.prototype.childView.extend(promotionalWidgetHandler)
            })
        });

        return panel;
    });

    // Element Pack Dynamic Tags Override (when Pro is not active)
    if (ElementPackConfig.elementPackDynamicTags?.enabled && !ElementPackConfig.elementPackDynamicTags.isProActive) {
        
        const EPDynamicTagsOverride = {
            debounceTimer: null,

            init() {
                this.bindEvents();
            },

            bindEvents() {
                elementor.hooks.addAction('panel/open_editor/widget', () => {
                    setTimeout(() => this.overrideNativeDynamicTags(), 500);
                });
                this.observePanelChanges();
            },

            observePanelChanges() {
                if (!window.MutationObserver) return;
                
                const targetNode = document.getElementById('elementor-panel-content-wrapper');
                if (!targetNode) return;

                const observer = new MutationObserver(() => {
                    clearTimeout(this.debounceTimer);
                    this.debounceTimer = setTimeout(() => this.overrideNativeDynamicTags(), 300);
                });
                
                observer.observe(targetNode, { childList: true, subtree: true });
            },

            overrideNativeDynamicTags() {
                $('.elementor-control-dynamic-switcher').each((index, element) => {
                    const $button = $(element);
                    
                    if ($button.hasClass('ep-enhanced')) return;
                    
                    $button.addClass('ep-enhanced')
                           .on('click.ep-combined-dialog', () => {
                               this.showCombinedUpgradeDialog(element);
                           });
                });
            },

            showCombinedUpgradeDialog(targetElement) {
                const { proFeatures: config, upgradeUrl } = ElementPackConfig.elementPackDynamicTags;
                
                const combinedContent = `
                    <div class="ep-combined-promotion">
                        <div class="ep-promotion-section">
                            <h4 style="margin: 0 0 10px 0; color: #333; font-size: 16px;">🚀 ${config.title}</h4>
                            <p style="margin: 0 0 15px 0; color: #666; line-height: 1.5;">${config.content}</p>
                            <a href="${upgradeUrl}" target="_blank" style="display: inline-block; background: #007cba; color: white; padding: 8px 16px; text-decoration: none; border-radius: 3px; margin-right: 10px;">${config.upgradeText}</a>
                        </div>
                        <hr style="margin: 20px 0; border: 0; border-top: 1px solid #ddd;">
                        <div class="elementor-promotion-section">
                            <h4 style="margin: 0 0 10px 0; color: #333; font-size: 16px;">⚡ Dynamic Content (Elementor Pro)</h4>
                            <p style="margin: 0 0 15px 0; color: #666; line-height: 1.5;">Create more personalized and dynamic sites by populating data from various sources with dozens of dynamic tags to choose from.</p>
                            <a href="https://elementor.com/pro/" target="_blank" style="display: inline-block; background: #E2498A; color: white; padding: 8px 16px; text-decoration: none; border-radius: 3px;">Upgrade Elementor</a>
                        </div>
                    </div>
                `;

                elementor.promotion.showDialog({
                    title: 'Dynamic Content - Pro',
                    content: combinedContent,
                    targetElement,
                    position: { blockStart: '-10' }
                });
                
                // Remove empty button wrapper
                setTimeout(() => {
                    $('.dialog-buttons-wrapper.dialog-buttons-buttons-wrapper').remove();
                }, 10);
            }
        };

        // Initialize the Dynamic Tags Override system
        $(() => EPDynamicTagsOverride.init());
    }

})(jQuery);
