// phone-dialog.js

// Make CloseWindow function globally available
window.CloseWindow = function () {
  if (window.phoneManager) {
    window.phoneManager.closeDialog();
  }
};

// Constants

class PhoneDialogManager {
  constructor() {
    this.dialog = null;
    this.iframe = null;
    this.isDragging = false;
    this.dragOffset = { x: 0, y: 0 };
    this.modalOverlay = null;

    // Add styles to document
  }

  createPopupWindow(numberToDial) {
    const origin = window.location.origin;

    // Define the desired path for the popup
    const popupPath = "/caller/dialer";

    // Construct the full URL with the number to dial as a query parameter
    const url = numberToDial
      ? `${origin}${popupPath}?d=${numberToDial}`
      : `${origin}${popupPath}`;

    
    const width = 400;
    const height = 600;

    // Calculate the center of the screen
    const left = (window.screen.width - width) / 2;
    const top = (window.screen.height - height) / 2;

    const windowFeatures = `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes`;

    this.dialog = window.open(url, "DialerPopup", windowFeatures);

    if (this.dialog) {
      this.dialog.focus();
    } else {
      alert("Popup blocked. Please allow popups for this site.");
    }
  }

  openDialog(numberToDial) {
    if (this.dialog && !this.dialog.closed) {
      console.warn("Phone window already open");
      return;
    } else {
      this.dialog = null;
    }

    this.createPopupWindow(numberToDial);
  }

  confirmAndCall(number) {
    if (number && confirm(`Would you like to call the number: ${number}`)) {
      this.openDialog(number);
    }
  }

  openAsHidden() {
    if (this.dialog) {
      this.dialog.style.display = "block";
      return;
    }

    this.openDialog();
    this.dialog.style.display = "none";
  }

  showDialog() {
    if (this.dialog) {
      this.dialog.style.display = "block";
    }
  }

  closeDialog() {
    if (!this.dialog) return;

    if (this.iframe && this.iframe.contentWindow) {
      try {
        const phoneContext = this.iframe.contentWindow;
        const callCount = phoneContext.countSessions?.(0) || 0;

        if (callCount > 0) {
          console.warn("You are on a call:", callCount);
          alert(
            "You are on a call, please end the call before closing this window."
          );
          return;
        }

        if (phoneContext.Unregister) {
          phoneContext.Unregister(true);
        }
      } catch (e) {
        console.warn("Could not check call status:", e);
      }
    }

    if (this.modalOverlay) {
      this.modalOverlay.remove();
      this.modalOverlay = null;
    }

    window.removeEventListener("resize", this.handleResize);
    this.dialog.remove();
    this.dialog = null;
    this.iframe = null;
  }

  initializeCallerLinks() {
    document.addEventListener("click", (event) => {
      const callerLink = event.target.closest(".caller");
      if (callerLink) {
        event.preventDefault();
        const phoneNumber =
          callerLink.dataset.number || callerLink.getAttribute("href");
        if (phoneNumber) {
          const cleanNumber = phoneNumber.replace(/[^0-9+*#]/g, "");
          this.confirmAndCall(cleanNumber);
        }
      }
    });
  }
}

// Initialize when the DOM is ready
document.addEventListener("DOMContentLoaded", () => {
  window.phoneManager = new PhoneDialogManager();
  window.phoneManager.initializeCallerLinks();
});

/**
 * Fetches application settings from a specified URL with enhanced error handling and validation
 * @param {string} settingsUrl - The URL to fetch settings from
 * @param {string} csrfToken - CSRF token for request authentication
 * @param {object} options - Optional configuration parameters
 * @param {number} options.timeout - Request timeout in milliseconds (default: 5000)
 * @param {boolean} options.retry - Whether to retry failed requests (default: true)
 * @param {number} options.maxRetries - Maximum number of retry attempts (default: 3)
 * @returns {Promise<object>} The fetched settings data
 * @throws {Error} With detailed error information
 */
async function fetchSettings(settingsUrl, csrfToken, options = {}) {
  // Input validation
  if (!settingsUrl || typeof settingsUrl !== "string") {
    throw new TypeError("settingsUrl must be a valid string");
  }
  if (!csrfToken || typeof csrfToken !== "string") {
    throw new TypeError("csrfToken must be a valid string");
  }

  // Default options
  const { timeout = 5000, retry = true, maxRetries = 3 } = options;

  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), timeout);

  const fetchWithRetry = async (attempt = 1) => {
    try {
      const response = await fetch(settingsUrl, {
        method: "GET",
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-TOKEN": csrfToken,
        },
        signal: controller.signal,
      });

      // Clear timeout since request completed
      clearTimeout(timeoutId);

      // Handle different types of errors
      if (!response.ok) {
        const errorMessage = await response.text();
        throw new Error(
          `HTTP ${response.status}: ${errorMessage || response.statusText}`
        );
      }

      if (!response.headers.get("content-type")?.includes("application/json")) {
        throw new TypeError("Response is not JSON");
      }

      const data = await response.json();

      // Validate that we received an object
      if (!data || typeof data !== "object") {
        throw new TypeError("Invalid settings data received");
      }

      return data;
    } catch (error) {
      // Clear timeout if there's an error
      clearTimeout(timeoutId);

      // Handle abort errors
      if (error.name === "AbortError") {
        throw new Error(`Request timed out after ${timeout}ms`);
      }

      // Retry logic
      if (retry && attempt < maxRetries) {
        const delay = Math.min(1000 * Math.pow(2, attempt - 1), 5000); // Exponential backoff
        await new Promise((resolve) => setTimeout(resolve, delay));
        return fetchWithRetry(attempt + 1);
      }

      // Enhance error message with attempt information if retrying
      if (retry) {
        error.message = `Failed after ${attempt} attempts: ${error.message}`;
      }

      // Add additional context to the error
      error.url = settingsUrl;
      error.attempt = attempt;
      error.timestamp = new Date().toISOString();

      throw error;
    }
  };

  return fetchWithRetry();
}

document.addEventListener("DOMContentLoaded", function () {
  let e = document.getElementById("websocketForm");

  if (!e) {
    console.log("WebSocket form not found on this page");
    return;
  }
  e.addEventListener("submit", function (t) {
    t.preventDefault();
    let r = new FormData(e);
    fetch(callerUrl, {
      method: "POST",
      body: r,
      headers: {
        "X-CSRF-TOKEN": "{{ csrf_token() }}",
        Accept: "application/json",
      },
    })
      .then((e) => e.json())
      .then((response) => {
        console.log("Response:", response);

        if (response.success) {
          appendAlert("Successfully Created", "success");
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        } else if (response.errors) {
          // First clear previous errors
          document
            .querySelectorAll(".invalid-feedback")
            .forEach((el) => el.remove());
          document
            .querySelectorAll(".is-invalid")
            .forEach((el) => el.classList.remove("is-invalid"));

          // Now show new errors
          Object.keys(response.errors).forEach((fieldName) => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
              const errorFeedback = document.createElement("span");
              errorFeedback.textContent = response.errors[fieldName][0];
              errorFeedback.classList.add("invalid-feedback");
              field.classList.add("is-invalid");
              field.insertAdjacentElement("afterend", errorFeedback);
            }
          });
        } else {
          appendAlert("Failed to update settings. Please try again.", "danger");
        }
      })
      .catch((error) => {
        console.error("Catch Error:", error);
        appendAlert("An error occurred. Please try again.", "danger");
      });
  });
});

const alertPlaceholder = document.getElementById("liveAlertPlaceholder");
const appendAlert = (message, type) => {
  const wrapper = document.createElement("div");
  wrapper.innerHTML = [
    `<div class="alert alert-${type} alert-dismissible" role="alert">`,
    `   <div>${message}</div>`,
    "</div>",
  ].join("");

  alertPlaceholder.append(wrapper);
};
