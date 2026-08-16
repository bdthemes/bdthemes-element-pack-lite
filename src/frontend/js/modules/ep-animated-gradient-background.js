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
