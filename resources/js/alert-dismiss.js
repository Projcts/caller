document.addEventListener("DOMContentLoaded", function () {
  // Auto dismiss alerts after 5 seconds
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach(function (alert) {
    setTimeout(function () {
      const dismissButton = alert.querySelector(".btn-close");
      if (dismissButton) {
        dismissButton.click();
      }
    }, 5000);
  });
});
