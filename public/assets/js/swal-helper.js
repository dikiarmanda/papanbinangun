window.SwalApp = {
  defaults: {
    confirmButtonColor: "#c1502e",
    cancelButtonColor: "#7a6a58",
    buttonsStyling: true,
    reverseButtons: true,
  },

  toast(message, icon = "success") {
    if (!window.Swal) return;

    Swal.fire({
      toast: true,
      position: "top-end",
      icon,
      title: message,
      showConfirmButton: false,
      timer: 4500,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener("mouseenter", Swal.stopTimer);
        toast.addEventListener("mouseleave", Swal.resumeTimer);
      },
    });
  },

  showFlash() {
    document.querySelectorAll(".swal-flash").forEach((el) => {
      const type = el.dataset.type || "info";
      const message = el.dataset.message || "";
      if (message) {
        this.toast(message, type);
      }
      el.remove();
    });
  },

  confirm(options = {}) {
    if (!window.Swal) {
      return Promise.resolve({
        isConfirmed: window.confirm(options.title || "Lanjutkan?"),
      });
    }

    return Swal.fire({
      ...this.defaults,
      title: "Konfirmasi",
      text: "",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Ya",
      cancelButtonText: "Batal",
      ...options,
    });
  },
};

function initSwalFlash() {
  window.SwalApp?.showFlash?.();
}

function initSwalConfirm() {
  document.querySelectorAll("form.js-swal-confirm").forEach((form) => {
    form.addEventListener("submit", async (event) => {
      if (form.dataset.swalConfirmed === "1") {
        form.dataset.swalConfirmed = "0";
        return;
      }

      event.preventDefault();

      const result = await window.SwalApp.confirm({
        title: form.dataset.swalTitle || "Konfirmasi",
        text: form.dataset.swalText || "",
        html: form.dataset.swalHtml || undefined,
        icon: form.dataset.swalIcon || "warning",
        confirmButtonText: form.dataset.swalConfirm || "Ya",
        cancelButtonText: form.dataset.swalCancel || "Batal",
      });

      if (result.isConfirmed) {
        form.dataset.swalConfirmed = "1";
        form.requestSubmit();
      }
    });
  });
}
