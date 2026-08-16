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
