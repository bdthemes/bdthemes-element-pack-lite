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
