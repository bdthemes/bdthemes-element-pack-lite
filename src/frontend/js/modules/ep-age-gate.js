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
