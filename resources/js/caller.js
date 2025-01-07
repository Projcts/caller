// phone-dialog.js

// Make CloseWindow function globally available
window.CloseWindow = function () {
  if (window.phoneManager) {
    window.phoneManager.closeDialog();
  }
};

// Constants
const DIALOG_CONFIG = {
  width: 400,
  height: 700,
  title: "Browser Phone",
  modal: true,
  resizable: true,
};

// CSS Styles
const STYLES = `
.phone-dialog {
    position: fixed;
    background: white;
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
    border-radius: 4px;
    overflow: hidden;
    z-index: 1000;
    display: flex;
    flex-direction: column;
}

.dialog-title-bar {
    padding: 10px;
    background: #f0f0f0;
    cursor: move;
    user-select: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dialog-title-bar button {
    border: none;
    background: none;
    font-size: 20px;
    cursor: pointer;
    padding: 0 5px;
}

.dialog-content {
    flex: 1;
    overflow: hidden;
}

.resize-handle {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 15px;
    height: 15px;
    cursor: se-resize;
    background: linear-gradient(135deg, transparent 50%, #ccc 50%);
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 999;
}
`;

class PhoneDialogManager {
  constructor() {
    this.dialog = null;
    this.iframe = null;
    this.isDragging = false;
    this.dragOffset = { x: 0, y: 0 };
    this.modalOverlay = null;

    // Add styles to document
    this.addStyles();

    // Bind methods
    this.handleDragStart = this.handleDragStart.bind(this);
    this.handleDrag = this.handleDrag.bind(this);
    this.handleDragEnd = this.handleDragEnd.bind(this);
    this.handleResize = this.handleResize.bind(this);
    this.closeDialog = this.closeDialog.bind(this);

    // Make closeDialog method available globally
    window.CloseWindow = this.closeDialog.bind(this);
  }

  addStyles() {
    if (!document.getElementById("phone-dialog-styles")) {
      const styleSheet = document.createElement("style");
      styleSheet.id = "phone-dialog-styles";
      styleSheet.textContent = STYLES;
      document.head.appendChild(styleSheet);
    }
  }

  createDialog() {
    this.dialog = document.createElement("div");
    this.dialog.className = "phone-dialog";
    this.dialog.style.width = `${DIALOG_CONFIG.width}px`;
    this.dialog.style.height = `${DIALOG_CONFIG.height}px`;

    // Create title bar
    const titleBar = document.createElement("div");
    titleBar.className = "dialog-title-bar";

    const title = document.createElement("span");
    title.textContent = DIALOG_CONFIG.title;

    const closeButton = document.createElement("button");
    closeButton.textContent = "×";
    closeButton.onclick = () => this.closeDialog();

    titleBar.appendChild(title);
    titleBar.appendChild(closeButton);
    this.dialog.appendChild(titleBar);

    document.body.appendChild(this.dialog);
  }

  createIframe(numberToDial) {
    const container = document.createElement("div");
    container.className = "dialog-content";

    this.iframe = document.createElement("iframe");
    this.iframe.id = "ThePhone";
    this.iframe.style.width = "100%";
    this.iframe.style.height = "100%";
    this.iframe.style.border = "none";
    this.iframe.src = numberToDial ? `dialer?d=${numberToDial}` : "index.html";

    container.appendChild(this.iframe);
    this.dialog.appendChild(container);
  }

  openDialog(numberToDial) {
    if (this.dialog) {
      console.warn("Phone window already open");
      return;
    }

    this.createDialog();
    this.createIframe(numberToDial);
    this.setupEventListeners();
    this.centerDialog();

    if (DIALOG_CONFIG.modal) {
      this.createModalOverlay();
    }
  }

  setupEventListeners() {
    const titleBar = this.dialog.querySelector(".dialog-title-bar");
    titleBar.addEventListener("mousedown", this.handleDragStart);
    window.addEventListener("resize", this.handleResize);

    if (DIALOG_CONFIG.resizable) {
      this.setupResizeHandle();
    }
  }

  handleDragStart(e) {
    if (e.target.tagName.toLowerCase() === "button") return;

    this.isDragging = true;
    const rect = this.dialog.getBoundingClientRect();
    this.dragOffset = {
      x: e.clientX - rect.left,
      y: e.clientY - rect.top,
    };

    document.addEventListener("mousemove", this.handleDrag);
    document.addEventListener("mouseup", this.handleDragEnd);
  }

  handleDrag(e) {
    if (!this.isDragging) return;

    const newX = e.clientX - this.dragOffset.x;
    const newY = e.clientY - this.dragOffset.y;

    const maxX = window.innerWidth - this.dialog.offsetWidth;
    const maxY = window.innerHeight - this.dialog.offsetHeight;

    this.dialog.style.left = `${Math.min(Math.max(0, newX), maxX)}px`;
    this.dialog.style.top = `${Math.min(Math.max(0, newY), maxY)}px`;
  }

  handleDragEnd() {
    this.isDragging = false;
    document.removeEventListener("mousemove", this.handleDrag);
    document.removeEventListener("mouseup", this.handleDragEnd);
  }

  setupResizeHandle() {
    const resizeHandle = document.createElement("div");
    resizeHandle.className = "resize-handle";

    let isResizing = false;
    let originalWidth, originalHeight, originalX, originalY;

    resizeHandle.addEventListener("mousedown", (e) => {
      isResizing = true;
      originalWidth = this.dialog.offsetWidth;
      originalHeight = this.dialog.offsetHeight;
      originalX = e.clientX;
      originalY = e.clientY;

      const handleResize = (e) => {
        if (!isResizing) return;

        const newWidth = originalWidth + (e.clientX - originalX);
        const newHeight = originalHeight + (e.clientY - originalY);

        this.dialog.style.width = `${Math.max(300, newWidth)}px`;
        this.dialog.style.height = `${Math.max(200, newHeight)}px`;
      };

      const stopResize = () => {
        isResizing = false;
        document.removeEventListener("mousemove", handleResize);
        document.removeEventListener("mouseup", stopResize);
      };

      document.addEventListener("mousemove", handleResize);
      document.addEventListener("mouseup", stopResize);
    });

    this.dialog.appendChild(resizeHandle);
  }

  centerDialog() {
    const x = (window.innerWidth - DIALOG_CONFIG.width) / 2;
    const y = (window.innerHeight - DIALOG_CONFIG.height) / 2;

    this.dialog.style.left = `${Math.max(0, x)}px`;
    this.dialog.style.top = `${Math.max(0, y)}px`;
  }

  createModalOverlay() {
    this.modalOverlay = document.createElement("div");
    this.modalOverlay.className = "modal-overlay";
    document.body.appendChild(this.modalOverlay);
  }

  handleResize() {
    if (!this.dialog) return;

    const rect = this.dialog.getBoundingClientRect();
    const maxX = window.innerWidth - rect.width;
    const maxY = window.innerHeight - rect.height;

    if (rect.left > maxX) this.dialog.style.left = `${maxX}px`;
    if (rect.top > maxY) this.dialog.style.top = `${maxY}px`;
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
      .then((e) => {
        if (e.success) {
          appendAlert("Successfully Created", "success");
          setTimeout(function () {
            window.location.reload();
          }, 1000);
        } else
          e.errors
            ? (document.querySelectorAll(".invalid-feedback").forEach((e) => {
                e.remove();
              }),
              document.querySelectorAll(".is-invalid").forEach((e) => {
                e.classList.remove("is-invalid");
              }),
              Object.keys(e.errors).forEach((t) => {
                let r = document.querySelector(`[name="${t}"]`),
                  n = document.createElement("span");
                (n.textContent = e.errors[t][0]),
                  n.classList.add("invalid-feedback"),
                  r.classList.add("is-invalid"),
                  r.insertAdjacentElement("afterend", n);
              }))
            : alert("Failed to update settings. Please try again.");
      })
      .catch((e) => {
        console.error("Error:", e),
          alert("An error occurred. Please try again.");
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
