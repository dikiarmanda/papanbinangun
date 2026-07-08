import { createEditor, $getRoot, $getSelection, $isRangeSelection, $isElementNode, $findMatchingParent, $createParagraphNode, FORMAT_TEXT_COMMAND, FORMAT_ELEMENT_COMMAND, UNDO_COMMAND, REDO_COMMAND, CAN_UNDO_COMMAND, CAN_REDO_COMMAND, SELECTION_CHANGE_COMMAND, COMMAND_PRIORITY_LOW, $insertNodes, ParagraphNode, TextNode } from "lexical";
import { HeadingNode, QuoteNode, $createHeadingNode, $createQuoteNode, $isHeadingNode, $isQuoteNode, registerRichText } from "@lexical/rich-text";
import { ListNode, ListItemNode, registerList, INSERT_ORDERED_LIST_COMMAND, INSERT_UNORDERED_LIST_COMMAND } from "@lexical/list";
import { LinkNode, registerLink, TOGGLE_LINK_COMMAND } from "@lexical/link";
import { registerHistory, createEmptyHistoryState } from "@lexical/history";
import { $generateHtmlFromNodes, $generateNodesFromDOM } from "@lexical/html";
import { $setBlocksType, $patchStyleText, $getSelectionStyleValueForProperty } from "@lexical/selection";
import { mergeRegister } from "@lexical/utils";
import { namedSignals } from "@lexical/extension";

const linkStores = namedSignals({
  validateUrl: (url) => {
    try {
      const parsed = new URL(url);
      return ["http:", "https:", "mailto:", "tel:"].includes(parsed.protocol);
    } catch {
      return url.startsWith("/") || url.startsWith("#");
    }
  },
  attributes: undefined,
});

const FONT_FAMILIES = [
  { label: "Default", value: "" },
  { label: "Arial", value: "Arial, sans-serif" },
  { label: "Georgia", value: "Georgia, serif" },
  { label: "Times New Roman", value: "'Times New Roman', serif" },
  { label: "Verdana", value: "Verdana, sans-serif" },
  { label: "Courier New", value: "'Courier New', monospace" },
];

const FONT_SIZES = [12, 14, 16, 18, 20, 24, 28, 32];

const BLOCK_TYPES = [
  { label: "Normal", value: "paragraph" },
  { label: "Heading 1", value: "h1" },
  { label: "Heading 2", value: "h2" },
  { label: "Heading 3", value: "h3" },
  { label: "Kutipan", value: "quote" },
];

const ALIGNMENTS = [
  { value: "left", icon: "fa-align-left", title: "Rata kiri" },
  { value: "center", icon: "fa-align-center", title: "Rata tengah" },
  { value: "right", icon: "fa-align-right", title: "Rata kanan" },
  { value: "justify", icon: "fa-align-justify", title: "Rata kanan-kiri" },
];

const SIMPLE_TOOLBAR_ITEMS = [{ cmd: UNDO_COMMAND, icon: "fa-rotate-left", title: "Urungkan" }, { cmd: REDO_COMMAND, icon: "fa-rotate-right", title: "Ulangi" }, { type: "sep" }, { cmd: FORMAT_TEXT_COMMAND, arg: "bold", icon: "fa-bold", title: "Tebal" }, { cmd: FORMAT_TEXT_COMMAND, arg: "italic", icon: "fa-italic", title: "Miring" }, { type: "sep" }, { cmd: INSERT_UNORDERED_LIST_COMMAND, icon: "fa-list-ul", title: "Daftar bullet" }, { cmd: INSERT_ORDERED_LIST_COMMAND, icon: "fa-list-ol", title: "Daftar nomor" }, { type: "sep" }, { action: "link", icon: "fa-link", title: "Tautan" }];

const EDITOR_THEME = {
  paragraph: "lexical-paragraph",
  text: {
    bold: "lexical-text-bold",
    italic: "lexical-text-italic",
    underline: "lexical-text-underline",
    strikethrough: "lexical-text-strikethrough",
    code: "lexical-text-code",
  },
  list: {
    ul: "lexical-list-ul",
    ol: "lexical-list-ol",
    listitem: "lexical-list-item",
  },
  link: "lexical-link",
  heading: {
    h1: "lexical-heading-h1",
    h2: "lexical-heading-h2",
    h3: "lexical-heading-h3",
  },
  quote: "lexical-quote",
};

function isHtmlEmpty(html) {
  if (!html) return true;
  const stripped = html
    .replace(/<br\s*\/?>/gi, "")
    .replace(/<[^>]*>/g, "")
    .replace(/&nbsp;/gi, " ")
    .trim();
  return stripped === "";
}

function normalizeInitialHtml(html) {
  if (!html || /<[a-z][\s\S]*>/i.test(html)) return html;
  return html
    .split(/\n{2,}/)
    .filter((block) => block.trim() !== "")
    .map((block) => `<p>${block.trim().replace(/\n/g, "<br>")}</p>`)
    .join("");
}

function loadHtml(editor, html) {
  const normalized = normalizeInitialHtml(html);
  editor.update(() => {
    const root = $getRoot();
    root.clear();
    if (isHtmlEmpty(html)) return;

    const parser = new DOMParser();
    const dom = parser.parseFromString(normalized, "text/html");
    const nodes = $generateNodesFromDOM(editor, dom);
    root.select();
    $insertNodes(nodes);
  });
}

function exportHtml(editor) {
  let html = "";
  editor.getEditorState().read(() => {
    html = $generateHtmlFromNodes(editor, null);
  });
  return isHtmlEmpty(html) ? "" : html;
}

function syncTextarea(editor, textarea) {
  textarea.value = exportHtml(editor);
}

function createSep() {
  const sep = document.createElement("span");
  sep.className = "lexical-toolbar-sep";
  sep.setAttribute("aria-hidden", "true");
  return sep;
}

function createBtn(title, icon, formatKey) {
  const btn = document.createElement("button");
  btn.type = "button";
  btn.className = "lexical-toolbar-btn";
  btn.title = title;
  btn.innerHTML = `<i class="fa-solid ${icon}"></i>`;
  if (formatKey) btn.dataset.format = formatKey;
  return btn;
}

function createSelect(className, title) {
  const select = document.createElement("select");
  select.className = className;
  select.title = title;
  select.setAttribute("aria-label", title);
  return select;
}

function applyLink(editor) {
  editor.update(() => {
    const selection = $getSelection();
    if (!$isRangeSelection(selection)) return;

    const url = window.prompt("URL tautan:", "https://");
    if (url === null) return;
    if (url.trim() === "") {
      editor.dispatchCommand(TOGGLE_LINK_COMMAND, null);
      return;
    }
    editor.dispatchCommand(TOGGLE_LINK_COMMAND, url.trim());
  });
}

function setBlockType(editor, type) {
  editor.update(() => {
    const selection = $getSelection();
    if (!$isRangeSelection(selection)) return;

    if (type === "paragraph") {
      $setBlocksType(selection, () => $createParagraphNode());
      return;
    }
    if (type === "quote") {
      $setBlocksType(selection, () => $createQuoteNode());
      return;
    }
    if (type.startsWith("h")) {
      $setBlocksType(selection, () => $createHeadingNode(type));
    }
  });
}

function getSelectedBlockType() {
  const selection = $getSelection();
  if (!$isRangeSelection(selection)) return "paragraph";

  const anchor = selection.anchor.getNode();
  const block = $findMatchingParent(anchor, (node) => {
    if ($isHeadingNode(node)) return true;
    if ($isQuoteNode(node)) return true;
    return node.getType() === "paragraph";
  });

  if ($isHeadingNode(block)) return block.getTag();
  if ($isQuoteNode(block)) return "quote";
  return "paragraph";
}

function getSelectedAlignment() {
  const selection = $getSelection();
  if (!$isRangeSelection(selection)) return "left";

  const anchor = selection.anchor.getNode();
  const block = $findMatchingParent(anchor, $isElementNode);
  if (!block || typeof block.getFormatType !== "function") return "left";
  return block.getFormatType() || "left";
}

function clearFormatting(editor) {
  editor.update(() => {
    const selection = $getSelection();
    if (!$isRangeSelection(selection)) return;

    $patchStyleText(selection, {
      "font-family": null,
      "font-size": null,
      color: null,
    });

    ["bold", "italic", "underline", "strikethrough", "code"].forEach((format) => {
      if (selection.hasFormat(format)) {
        editor.dispatchCommand(FORMAT_TEXT_COMMAND, format);
      }
    });
  });
}

function registerHistoryButtons(editor, undoBtn, redoBtn) {
  undoBtn.disabled = true;
  redoBtn.disabled = true;

  editor.registerCommand(
    CAN_UNDO_COMMAND,
    (payload) => {
      undoBtn.disabled = !payload;
      return false;
    },
    COMMAND_PRIORITY_LOW,
  );

  editor.registerCommand(
    CAN_REDO_COMMAND,
    (payload) => {
      redoBtn.disabled = !payload;
      return false;
    },
    COMMAND_PRIORITY_LOW,
  );
}

function createSimpleToolbar(editor) {
  const toolbar = document.createElement("div");
  toolbar.className = "lexical-toolbar";

  SIMPLE_TOOLBAR_ITEMS.forEach((item) => {
    if (item.type === "sep") {
      toolbar.appendChild(createSep());
      return;
    }

    const btn = createBtn(item.title, item.icon, item.arg);
    if (item.action === "link") {
      btn.addEventListener("click", () => applyLink(editor));
    } else {
      btn.addEventListener("click", () => {
        editor.dispatchCommand(item.cmd, item.arg ?? undefined);
      });
    }

    toolbar.appendChild(btn);
  });

  const undoBtn = toolbar.querySelector('[title="Urungkan"]');
  const redoBtn = toolbar.querySelector('[title="Ulangi"]');
  if (undoBtn && redoBtn) {
    registerHistoryButtons(editor, undoBtn, redoBtn);
  }

  return { toolbar, controls: {} };
}

function createFullToolbar(editor, ctx) {
  const toolbar = document.createElement("div");
  toolbar.className = "lexical-toolbar lexical-toolbar-full";

  const rowMain = document.createElement("div");
  rowMain.className = "lexical-toolbar-row";

  const undoBtn = createBtn("Urungkan", "fa-rotate-left");
  const redoBtn = createBtn("Ulangi", "fa-rotate-right");
  undoBtn.addEventListener("click", () => editor.dispatchCommand(UNDO_COMMAND, undefined));
  redoBtn.addEventListener("click", () => editor.dispatchCommand(REDO_COMMAND, undefined));
  registerHistoryButtons(editor, undoBtn, redoBtn);

  const blockSelect = createSelect("lexical-toolbar-select lexical-toolbar-block", "Jenis blok");
  BLOCK_TYPES.forEach(({ label, value }) => {
    const opt = document.createElement("option");
    opt.value = value;
    opt.textContent = label;
    blockSelect.appendChild(opt);
  });
  blockSelect.addEventListener("change", () => setBlockType(editor, blockSelect.value));

  const fontSelect = createSelect("lexical-toolbar-select lexical-toolbar-font", "Font");
  FONT_FAMILIES.forEach(({ label, value }) => {
    const opt = document.createElement("option");
    opt.value = value;
    opt.textContent = label;
    fontSelect.appendChild(opt);
  });
  fontSelect.addEventListener("change", () => {
    editor.update(() => {
      const selection = $getSelection();
      if (!$isRangeSelection(selection)) return;
      $patchStyleText(selection, {
        "font-family": fontSelect.value || null,
      });
    });
  });

  const sizeWrap = document.createElement("div");
  sizeWrap.className = "lexical-toolbar-size";
  const sizeDown = createBtn("Perkecil font", "fa-minus");
  const sizeUp = createBtn("Perbesar font", "fa-plus");
  const sizeLabel = document.createElement("span");
  sizeLabel.className = "lexical-toolbar-size-value";
  sizeLabel.textContent = "16";

  const changeFontSize = (delta) => {
    editor.update(() => {
      const selection = $getSelection();
      if (!$isRangeSelection(selection)) return;
      const current = parseInt($getSelectionStyleValueForProperty(selection, "font-size", "16px"), 10) || 16;
      const next = Math.min(32, Math.max(12, current + delta));
      $patchStyleText(selection, { "font-size": `${next}px` });
    });
  };

  sizeDown.addEventListener("click", () => changeFontSize(-2));
  sizeUp.addEventListener("click", () => changeFontSize(2));
  sizeWrap.append(sizeDown, sizeLabel, sizeUp);

  const formatButtons = {};
  [
    ["bold", "Tebal", "fa-bold"],
    ["italic", "Miring", "fa-italic"],
    ["underline", "Garis bawah", "fa-underline"],
    ["strikethrough", "Coret", "fa-strikethrough"],
    ["code", "Kode", "fa-code"],
  ].forEach(([format, title, icon]) => {
    const btn = createBtn(title, icon, format);
    btn.addEventListener("click", () => {
      editor.dispatchCommand(FORMAT_TEXT_COMMAND, format);
    });
    formatButtons[format] = btn;
  });

  const linkBtn = createBtn("Tautan", "fa-link");
  linkBtn.addEventListener("click", () => applyLink(editor));

  const colorWrap = document.createElement("label");
  colorWrap.className = "lexical-toolbar-color";
  colorWrap.title = "Warna teks";
  colorWrap.innerHTML = '<i class="fa-solid fa-font"></i>';
  const colorInput = document.createElement("input");
  colorInput.type = "color";
  colorInput.value = "#2c2416";
  colorInput.addEventListener("input", () => {
    editor.update(() => {
      const selection = $getSelection();
      if (!$isRangeSelection(selection)) return;
      $patchStyleText(selection, { color: colorInput.value });
    });
  });
  colorWrap.appendChild(colorInput);

  const clearBtn = createBtn("Hapus format", "fa-eraser");
  clearBtn.addEventListener("click", () => clearFormatting(editor));

  const listUlBtn = createBtn("Daftar bullet", "fa-list-ul");
  listUlBtn.addEventListener("click", () => {
    editor.dispatchCommand(INSERT_UNORDERED_LIST_COMMAND, undefined);
  });
  const listOlBtn = createBtn("Daftar nomor", "fa-list-ol");
  listOlBtn.addEventListener("click", () => {
    editor.dispatchCommand(INSERT_ORDERED_LIST_COMMAND, undefined);
  });

  const alignButtons = {};
  const alignWrap = document.createElement("div");
  alignWrap.className = "lexical-toolbar-align";
  ALIGNMENTS.forEach(({ value, icon, title }) => {
    const btn = createBtn(title, icon);
    btn.dataset.align = value;
    btn.addEventListener("click", () => {
      editor.dispatchCommand(FORMAT_ELEMENT_COMMAND, value);
    });
    alignButtons[value] = btn;
    alignWrap.appendChild(btn);
  });

  rowMain.append(undoBtn, redoBtn, createSep(), blockSelect, fontSelect, sizeWrap, createSep(), ...Object.values(formatButtons), linkBtn, colorWrap, clearBtn, createSep(), listUlBtn, listOlBtn, createSep(), alignWrap);

  toolbar.appendChild(rowMain);

  const footer = document.createElement("div");
  footer.className = "lexical-toolbar-footer";

  const htmlToggle = createBtn("Mode HTML", "fa-code");
  htmlToggle.classList.add("lexical-toolbar-footer-btn");
  const clearAllBtn = createBtn("Kosongkan", "fa-trash");
  clearAllBtn.classList.add("lexical-toolbar-footer-btn");

  footer.append(htmlToggle, clearAllBtn);
  toolbar.appendChild(footer);

  const htmlPanel = document.createElement("div");
  htmlPanel.className = "lexical-html-panel";
  htmlPanel.hidden = true;
  const htmlArea = document.createElement("textarea");
  htmlArea.className = "lexical-html-source";
  htmlArea.spellcheck = false;
  htmlArea.placeholder = "Edit kode HTML…";
  const htmlActions = document.createElement("div");
  htmlActions.className = "lexical-html-actions";
  const htmlApply = document.createElement("button");
  htmlApply.type = "button";
  htmlApply.className = "btn btn-sm btn-primary";
  htmlApply.textContent = "Terapkan HTML";
  const htmlCancel = document.createElement("button");
  htmlCancel.type = "button";
  htmlCancel.className = "btn btn-sm";
  htmlCancel.textContent = "Tutup";
  htmlActions.append(htmlApply, htmlCancel);
  htmlPanel.append(htmlArea, htmlActions);

  htmlToggle.addEventListener("click", () => {
    const open = htmlPanel.hidden;
    if (open) {
      htmlArea.value = exportHtml(editor);
      ctx.editorEl.hidden = true;
      htmlPanel.hidden = false;
      htmlToggle.classList.add("is-active");
    } else {
      htmlPanel.hidden = true;
      ctx.editorEl.hidden = false;
      htmlToggle.classList.remove("is-active");
    }
  });

  htmlApply.addEventListener("click", () => {
    loadHtml(editor, htmlArea.value);
    syncTextarea(editor, ctx.textarea);
    htmlPanel.hidden = true;
    ctx.editorEl.hidden = false;
    htmlToggle.classList.remove("is-active");
  });

  htmlCancel.addEventListener("click", () => {
    htmlPanel.hidden = true;
    ctx.editorEl.hidden = false;
    htmlToggle.classList.remove("is-active");
  });

  clearAllBtn.addEventListener("click", async () => {
    const confirmed = window.SwalApp
      ? (
          await window.SwalApp.confirm({
            title: "Kosongkan editor?",
            text: "Seluruh isi editor akan dihapus.",
            icon: "warning",
            confirmButtonText: "Ya, kosongkan",
          })
        ).isConfirmed
      : window.confirm("Kosongkan seluruh isi editor?");

    if (!confirmed) return;
    editor.update(() => $getRoot().clear());
  });

  const updateToolbarState = () => {
    editor.getEditorState().read(() => {
      const selection = $getSelection();
      if (!$isRangeSelection(selection)) return;

      blockSelect.value = getSelectedBlockType();

      const fontFamily = $getSelectionStyleValueForProperty(selection, "font-family", "");
      fontSelect.value = FONT_FAMILIES.some((f) => f.value === fontFamily) ? fontFamily : "";

      const fontSize = parseInt($getSelectionStyleValueForProperty(selection, "font-size", "16px"), 10) || 16;
      sizeLabel.textContent = String(fontSize);

      Object.entries(formatButtons).forEach(([format, btn]) => {
        btn.classList.toggle("is-active", selection.hasFormat(format));
      });

      const align = getSelectedAlignment();
      Object.entries(alignButtons).forEach(([value, btn]) => {
        btn.classList.toggle("is-active", value === align);
      });
    });
  };

  editor.registerUpdateListener(updateToolbarState);
  editor.registerCommand(
    SELECTION_CHANGE_COMMAND,
    () => {
      updateToolbarState();
      return false;
    },
    COMMAND_PRIORITY_LOW,
  );

  return {
    toolbar,
    htmlPanel,
    controls: { updateToolbarState },
  };
}

function createToolbar(editor, mode, ctx) {
  if (mode === "full") {
    return createFullToolbar(editor, ctx);
  }
  return createSimpleToolbar(editor);
}

function initLexicalField(textarea) {
  if (textarea.dataset.lexicalReady === "1") return;

  const minHeight = parseInt(textarea.dataset.minHeight || "220", 10);
  const placeholder = textarea.dataset.placeholder || "";
  const toolbarMode = textarea.dataset.toolbar || "simple";

  const wrapper = document.createElement("div");
  wrapper.className = "lexical-editor-wrap";
  if (toolbarMode === "full") {
    wrapper.classList.add("lexical-editor-wrap-full");
  }

  const editor = createEditor({
    namespace: "AdminLexical",
    theme: EDITOR_THEME,
    nodes: [HeadingNode, QuoteNode, ListNode, ListItemNode, LinkNode, ParagraphNode, TextNode],
    onError(error) {
      console.error("[Lexical]", error);
    },
  });

  const editorEl = document.createElement("div");
  editorEl.className = "lexical-editor-input";
  editorEl.contentEditable = "true";
  editorEl.setAttribute("role", "textbox");
  editorEl.setAttribute("aria-multiline", "true");
  editorEl.style.minHeight = `${minHeight}px`;
  if (placeholder) {
    editorEl.dataset.placeholder = placeholder;
  }

  const ctx = { textarea, editorEl };
  const { toolbar, htmlPanel } = createToolbar(editor, toolbarMode, ctx);

  wrapper.appendChild(toolbar);
  wrapper.appendChild(editorEl);
  if (htmlPanel) wrapper.appendChild(htmlPanel);

  textarea.classList.add("lexical-source");
  textarea.parentNode.insertBefore(wrapper, textarea.nextSibling);

  editor.setRootElement(editorEl);

  mergeRegister(registerRichText(editor), registerList(editor), registerLink(editor, linkStores), registerHistory(editor, createEmptyHistoryState(), 300));

  loadHtml(editor, textarea.value);

  const sync = () => syncTextarea(editor, textarea);
  editor.registerUpdateListener(() => sync());

  const form = textarea.closest("form");
  if (form) {
    form.addEventListener("submit", sync);
  }

  textarea.dataset.lexicalReady = "1";
  sync();
}

export function initLexicalEditors(scope) {
  const root = scope || document;
  root.querySelectorAll("textarea.lexical-field").forEach(initLexicalField);
}
