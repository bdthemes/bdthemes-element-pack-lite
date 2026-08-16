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
