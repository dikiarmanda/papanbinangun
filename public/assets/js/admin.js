document.addEventListener("DOMContentLoaded", () => {
  initAdminNav();
  initAlerts();
  initSelect2();
  initDropify();
  LexicalAdminEditor?.initLexicalEditors?.();
  initSlugGenerator();
  initFasilitasEditor();
  initGaleriModals();
});

const SELECT2_MIN_OPTIONS = 8;

function getSelect2Options($el) {
  const isMultiple = $el.prop("multiple") || $el.hasClass("select2-multiple");
  const placeholder = $el.data("placeholder") || $el.find("option[value='']").first().text() || "— Pilih —";

  return {
    width: "100%",
    placeholder,
    allowClear: true,
    multiple: isMultiple,
    dropdownParent: jQuery("body"),
    language: {
      noResults: () => "Tidak ditemukan",
      searching: () => "Mencari...",
      inputTooShort: () => "Ketik untuk mencari",
    },
  };
}

function shouldUseSelect2(select) {
  if (select.classList.contains("no-select2")) return false;
  if (select.classList.contains("select2-field")) return true;
  if (select.classList.contains("select2-multiple")) return true;
  return select.options.length >= SELECT2_MIN_OPTIONS;
}

function initSelect2(scope) {
  const $ = window.jQuery;
  if (!$ || !$.fn.select2) return;

  const $root = scope ? $(scope) : $(document);
  const selects = scope ? ($root.is("select") ? $root : $root.find("select")) : $("select");

  selects.each(function () {
    const $el = $(this);
    if ($el.hasClass("select2-hidden-accessible")) return;
    if (!shouldUseSelect2(this)) return;
    $el.select2(getSelect2Options($el));
  });
}

function destroySelect2(scope) {
  const $ = window.jQuery;
  if (!$ || !$.fn.select2) return;

  const $root = scope ? $(scope) : $(document);
  const selects = scope ? ($root.is("select") ? $root : $root.find("select")) : $("select.select2-hidden-accessible");

  selects.each(function () {
    const $el = $(this);
    if ($el.hasClass("select2-hidden-accessible")) {
      $el.select2("destroy");
    }
  });
}

function initDropify() {
  const $ = window.jQuery;
  if (!$ || !$.fn.dropify) return;

  $(".dropify").each(function () {
    const $input = $(this);
    if ($input.data("dropify")) return;

    $input.dropify({
      messages: {
        default: "Seret & lepas gambar atau klik",
        replace: "Ganti gambar",
        remove: "Hapus",
        error: "File tidak valid",
      },
      error: {
        fileSize: "Ukuran file terlalu besar (maks. {{ value }}).",
        imageFormat: "Format tidak didukung ({{ value }}).",
      },
    });
  });
}

function initAdminNav() {
  const toggle = document.getElementById("adminNavToggle");
  const sidebar = document.getElementById("adminSidebar");
  const overlay = document.getElementById("adminOverlay");
  const nav = document.getElementById("adminNav");

  if (!toggle || !sidebar || !overlay) return;

  const icon = toggle.querySelector("i");

  const setOpen = (open) => {
    sidebar.classList.toggle("is-open", open);
    overlay.classList.toggle("is-visible", open);
    overlay.hidden = !open;
    toggle.setAttribute("aria-expanded", open ? "true" : "false");
    toggle.setAttribute("aria-label", open ? "Tutup menu" : "Buka menu");
    document.body.style.overflow = open ? "hidden" : "";
    if (icon) {
      icon.className = open ? "fa-solid fa-xmark" : "fa-solid fa-bars";
    }
  };

  toggle.addEventListener("click", () => {
    setOpen(!sidebar.classList.contains("is-open"));
  });

  overlay.addEventListener("click", () => setOpen(false));

  nav?.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      if (window.innerWidth <= 992) setOpen(false);
    });
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") setOpen(false);
  });

  window.addEventListener("resize", () => {
    if (window.innerWidth > 992) setOpen(false);
  });
}

function initAlerts() {
  document.querySelectorAll(".alert").forEach((el) => {
    setTimeout(() => {
      el.style.opacity = "0";
      el.style.transition = "opacity .4s ease";
      setTimeout(() => el.remove(), 400);
    }, 5000);
  });
}

function slugify(text) {
  return (
    String(text || "")
      .toLowerCase()
      .trim()
      .replace(/[^a-z0-9\s-]/g, "")
      .replace(/[\s-]+/g, "-")
      .replace(/^-+|-+$/g, "") || "item"
  );
}

function initSlugGenerator() {
  document.querySelectorAll("[data-slug-from][data-slug-to]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const source = document.getElementById(btn.dataset.slugFrom);
      const target = document.getElementById(btn.dataset.slugTo);

      if (!source || !target) return;

      const name = source.value.trim();
      if (!name) {
        source.focus();
        return;
      }

      target.value = slugify(name);
      target.focus();
    });
  });
}

function initFasilitasEditor() {
  const editor = document.getElementById("fasilitasEditor");
  const addBtn = document.getElementById("fasilitasAdd");
  if (!editor || !addBtn) return;

  let icons = {};
  try {
    icons = JSON.parse(editor.dataset.icons || "{}");
  } catch {
    icons = {};
  }

  const buildIconOptions = (selected = "") => {
    let html = '<option value="">— Pilih ikon —</option>';
    Object.entries(icons).forEach(([value, label]) => {
      const isSelected = value === selected ? " selected" : "";
      html += `<option value="${value}"${isSelected}>${label}</option>`;
    });
    return html;
  };

  const bindRow = (row) => {
    const select = row.querySelector("select");
    const preview = row.querySelector(".fasilitas-preview i");
    const removeBtn = row.querySelector(".fasilitas-remove");
    const $ = window.jQuery;

    const updatePreview = () => {
      if (!preview) return;
      const icon = select?.value || "fa-circle-question";
      preview.className = `fa-solid ${icon}`;
    };

    if (select && $) {
      $(select).on("change", updatePreview);
    } else {
      select?.addEventListener("change", updatePreview);
    }
    updatePreview();

    removeBtn?.addEventListener("click", () => {
      const rows = editor.querySelectorAll(".fasilitas-row");
      if (rows.length <= 1) {
        row.querySelector('input[name="fasilitas_nama[]"]').value = "";
        if (select && $) {
          $(select).val("").trigger("change");
        } else if (select) {
          select.value = "";
          updatePreview();
        }
        return;
      }
      if (select) destroySelect2(select);
      row.remove();
    });
  };

  editor.querySelectorAll(".fasilitas-row").forEach((row) => {
    bindRow(row);
    const select = row.querySelector("select");
    if (select) initSelect2(select);
  });

  addBtn.addEventListener("click", () => {
    const row = document.createElement("tr");
    row.className = "fasilitas-row";
    row.innerHTML = `
      <td><input type="text" name="fasilitas_nama[]" placeholder="Mis. Parkir"></td>
      <td><select name="fasilitas_icon[]" class="select2-field" data-placeholder="Pilih ikon">${buildIconOptions()}</select></td>
      <td class="col-preview"><span class="fasilitas-preview" aria-hidden="true"><i class="fa-solid fa-circle-question"></i></span></td>
      <td class="col-action"><button type="button" class="btn btn-sm btn-danger fasilitas-remove" title="Hapus baris"><i class="fa-solid fa-trash"></i></button></td>
    `;
    editor.appendChild(row);
    const select = row.querySelector("select");
    initSelect2(select);
    bindRow(row);
    row.querySelector('input[name="fasilitas_nama[]"]')?.focus();
  });
}

function initGaleriModals() {
  const editModal = document.getElementById("galeriEditModal");
  const deleteModal = document.getElementById("galeriDeleteModal");
  if (!editModal && !deleteModal) return;

  const $ = window.jQuery;

  const openModal = (modal) => {
    if (!modal) return;
    modal.hidden = false;
    modal.setAttribute("aria-hidden", "false");
    document.body.classList.add("modal-open");
    modal.querySelector("[data-modal-close]")?.focus();
  };

  const closeModal = (modal) => {
    if (!modal) return;
    modal.hidden = true;
    modal.setAttribute("aria-hidden", "true");
    if (!document.querySelector(".admin-modal:not([hidden])")) {
      document.body.classList.remove("modal-open");
    }
  };

  document.querySelectorAll("[data-modal-close]").forEach((el) => {
    el.addEventListener("click", () => {
      closeModal(el.closest(".admin-modal"));
    });
  });

  document.addEventListener("keydown", (e) => {
    if (e.key !== "Escape") return;
    document.querySelectorAll(".admin-modal:not([hidden])").forEach((modal) => closeModal(modal));
  });

  document.querySelectorAll(".galeri-edit-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const form = document.getElementById("galeriEditForm");
      const preview = document.getElementById("galeriEditPreview");
      const judulInput = document.getElementById("galeriEditJudul");
      const kategoriSelect = document.getElementById("galeriEditKategori");
      const wisataSelect = document.getElementById("galeriEditWisata");

      if (!form || !preview || !judulInput || !kategoriSelect || !wisataSelect) return;

      form.action = btn.dataset.updateUrl || "";
      preview.src = btn.dataset.gambarUrl || "";
      preview.alt = btn.dataset.judul || "Foto galeri";
      judulInput.value = btn.dataset.judul || "";

      let kategoriIds = [];
      try {
        kategoriIds = JSON.parse(btn.dataset.kategoriIds || "[]");
      } catch {
        kategoriIds = [];
      }

      if ($ && $.fn.select2) {
        $(kategoriSelect).val(kategoriIds.map(String)).trigger("change");
        $(wisataSelect).val(btn.dataset.wisataId || "").trigger("change");
      } else {
        Array.from(kategoriSelect.options).forEach((opt) => {
          opt.selected = kategoriIds.map(String).includes(opt.value);
        });
        wisataSelect.value = btn.dataset.wisataId || "";
      }

      openModal(editModal);
      setTimeout(() => judulInput.focus(), 120);
    });
  });

  document.querySelectorAll(".galeri-delete-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const form = document.getElementById("galeriDeleteForm");
      const preview = document.getElementById("galeriDeletePreview");
      const label = document.getElementById("galeriDeleteLabel");

      if (!form || !preview || !label) return;

      form.action = btn.dataset.deleteUrl || "";
      preview.src = btn.dataset.gambarUrl || "";
      preview.alt = btn.dataset.judul || "Foto galeri";
      label.textContent = btn.dataset.judul || "ini";

      openModal(deleteModal);
    });
  });
}
