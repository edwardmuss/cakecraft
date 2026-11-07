(function (global) {
    function qs(root, sel) {
        return root.querySelector(sel);
    }
    function qsa(root, sel) {
        return Array.from(root.querySelectorAll(sel));
    }

    function escapeHtml(s = "") {
        return String(s)
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    }

    function collectVarNumsFromString(str, set) {
        const re = /\{\{\s*(\d+)\s*\}\}/g;
        let m = null;
        const s = String(str ?? "");
        while ((m = re.exec(s)) !== null) set.add(m[1]);
    }

    function safeParseComponents(optionEl) {
        const raw = optionEl?.getAttribute("data-components");
        if (!raw) return [];
        try {
            return JSON.parse(raw);
        } catch (e) {
            return [];
        }
    }

    function WATemplateComposer(opts) {
        this.root =
            typeof opts.root === "string"
                ? document.querySelector(opts.root)
                : opts.root;
        if (!this.root) throw new Error("WATemplateComposer: root not found");

        this.assets = opts.assets || {};
        this.routes = opts.routes || {};
        this.csrf = opts.csrf || "";
        this.onChange = opts.onChange || null;

        // recipients integration (optional)
        this.getRecipients = opts.getRecipients || (() => []);
        this.setRecipients = opts.setRecipients || (() => {});

        // state
        this.activeTemplate = null;
        this.headerMediaPreviewUrl = null;
        this.campaignVars = { header: {}, body: {}, buttons: {} };

        this.bind();
    }

    WATemplateComposer.prototype.bind = function () {
        const root = this.root;

        // IMPORTANT: use existing campaign page IDs (inside root)
        const q = (sel) => root.querySelector(sel);

        // template select
        this.elTemplate = q("#waTemplate");
        if (!this.elTemplate)
            throw new Error(
                "WATemplateComposer: #waTemplate not found inside root",
            );

        // panels
        this.headerPanel = q("#headerPanel");
        this.headerType = q("#headerTypeBadge");
        this.headerTextWrap = q("#headerTextWrap");
        this.headerText = q("#headerTextInput");

        this.headerMediaWrap = q("#headerMediaWrap");
        this.headerMediaInput = q("#headerMedia");
        this.btnUploadMedia = q("#btnUploadHeaderMedia");
        this.btnClearMedia = q("#btnClearMedia");
        this.headerMediaHint = q("#headerMediaHint");
        this.headerMediaPayload = q("#headerMediaPayload");

        this.varsPanel = q("#varsPanel");
        this.varsArea = q("#varsArea");

        this.footerPanel = q("#footerPanel");
        this.footerText = q("#footerTextInput");

        this.buttonsPanel = q("#buttonsPanel");
        this.buttonsInfo = q("#buttonsInfo");

        this.noTemplate = q("#noTemplateHint");

        // preview is OUTSIDE root, so use document
        const d = (sel) => document.querySelector(sel);
        this.pHeaderMedia = d("#previewHeaderMedia");
        this.pHeaderImg = d("#previewHeaderImg");
        this.pHeaderVideo = d("#previewHeaderVideo");
        this.pHeaderDoc = d("#previewHeaderDoc");
        this.pHeaderDocName = d("#previewHeaderDocName");
        this.pHeaderText = d("#previewHeaderText");
        this.pBody = d("#previewBody");
        this.pFooter = d("#previewFooter");
        this.pButtons = d("#previewButtons");
        this.hrHeader = d("#hrHeader");
        this.hrFooter = d("#hrFooter");

        // --- bind events safely ---
        this.elTemplate.addEventListener("change", () =>
            this.onTemplateChange(),
        );

        // If select2 is active, listen to its event too
        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.select2) {
            window
                .jQuery(this.elTemplate)
                .on("select2:select select2:clear", () => {
                    this.onTemplateChange();
                });
        }

        // local header media preview
        if (this.headerMediaInput) {
            this.headerMediaInput.addEventListener("change", () => {
                if (this.headerMediaPreviewUrl)
                    URL.revokeObjectURL(this.headerMediaPreviewUrl);
                const f = this.headerMediaInput.files?.[0];
                if (!f) return;
                this.headerMediaPreviewUrl = URL.createObjectURL(f);
                this.renderPreview();
            });
        }

        // upload/clear media
        if (this.btnUploadMedia) {
            this.btnUploadMedia.addEventListener("click", () =>
                this.uploadHeaderMedia(),
            );
        }
        if (this.btnClearMedia) {
            this.btnClearMedia.addEventListener("click", () =>
                this.clearHeaderMediaPreview(),
            );
        }

        // variables input (delegated)
        root.addEventListener("input", (e) => {
            const el = e.target;
            if (!el || !el.classList.contains("var-input")) return;

            const scope = el.getAttribute("data-scope");
            const num = el.getAttribute("data-var");
            const val = el.value || "";

            if (scope === "header") this.campaignVars.header[num] = val;
            if (scope === "body") this.campaignVars.body[num] = val;
            if (scope === "button") {
                const btn = String(el.getAttribute("data-btn"));
                this.campaignVars.buttons[btn] ||= {};
                this.campaignVars.buttons[btn][num] = val;
            }

            this.renderPreview();
            this.onChange?.(this.getState());
        });

        // insert variable button (delegated)
        root.addEventListener("click", (e) => {
            const btn = e.target.closest(".btn-insert-var");
            if (!btn) return;

            const scope = btn.getAttribute("data-scope");
            const num = btn.getAttribute("data-var");
            const btnIndex = btn.getAttribute("data-btn");

            const input = btn.closest(".input-group")?.querySelector("input");
            if (!input) return;

            this.openInsertVariableModal((val) => {
                this.insertAtCursor(input, val);

                if (scope === "header")
                    this.campaignVars.header[num] = input.value;
                if (scope === "body") this.campaignVars.body[num] = input.value;
                if (scope === "button") {
                    const k = String(btnIndex);
                    this.campaignVars.buttons[k] ||= {};
                    this.campaignVars.buttons[k][num] = input.value;
                }

                this.renderPreview();
                this.onChange?.(this.getState());
            });
        });
    };

    WATemplateComposer.prototype.findComp = function (type) {
        const comps = this.activeTemplate?.components || [];
        return comps.find((c) => String(c.type || "").toUpperCase() === type);
    };

    WATemplateComposer.prototype.extractTemplateVarsScoped = function (tpl) {
        const out = { header: new Set(), body: new Set(), buttons: {} };
        const comps = tpl.components || [];

        const header = comps.find((c) => c.type === "HEADER");
        if (header?.format === "TEXT" && header.text)
            collectVarNumsFromString(header.text, out.header);

        const body = comps.find((c) => c.type === "BODY");
        if (body?.text) collectVarNumsFromString(body.text, out.body);

        const buttons = comps.find((c) => c.type === "BUTTONS");
        if (buttons?.buttons?.length) {
            buttons.buttons.forEach((b, idx) => {
                if (!out.buttons[idx]) out.buttons[idx] = new Set();
                if (b.type === "URL" && b.url)
                    collectVarNumsFromString(b.url, out.buttons[idx]);
            });
        }

        return {
            header: Array.from(out.header).sort((a, b) => +a - +b),
            body: Array.from(out.body).sort((a, b) => +a - +b),
            buttons: Object.fromEntries(
                Object.entries(out.buttons).map(([i, set]) => [
                    i,
                    Array.from(set).sort((a, b) => +a - +b),
                ]),
            ),
        };
    };

    WATemplateComposer.prototype.ensureScopedVars = function (tpl) {
        const scoped = this.extractTemplateVarsScoped(tpl);

        this.campaignVars = { header: {}, body: {}, buttons: {} };
        scoped.header.forEach((v) => (this.campaignVars.header[v] = ""));
        scoped.body.forEach((v) => (this.campaignVars.body[v] = ""));
        Object.entries(scoped.buttons).forEach(([btnIndex, vars]) => {
            this.campaignVars.buttons[btnIndex] = {};
            (vars || []).forEach(
                (v) => (this.campaignVars.buttons[btnIndex][v] = ""),
            );
        });

        return scoped;
    };

    WATemplateComposer.prototype.applyScoped = function (text, map) {
        return String(text || "").replace(/\{\{\s*(\d+)\s*\}\}/g, (_, num) => {
            const v = map?.[String(num)];
            return v !== undefined && v !== null && String(v).length
                ? String(v)
                : `{{${num}}}`;
        });
    };

    WATemplateComposer.prototype.setHeaderMediaHint = function (fmt) {
        const f = String(fmt || "").toUpperCase();
        if (!this.headerMediaHint) return;

        if (f === "IMAGE")
            this.headerMediaHint.textContent =
                "Upload an image for the template header.";
        else if (f === "VIDEO")
            this.headerMediaHint.textContent =
                "Upload a video for the template header.";
        else if (f === "DOCUMENT")
            this.headerMediaHint.textContent =
                "Upload a document (PDF) for the template header.";
        else this.headerMediaHint.textContent = "";
    };

    WATemplateComposer.prototype.clearHeaderMediaPreview = function () {
        if (this.headerMediaPreviewUrl)
            URL.revokeObjectURL(this.headerMediaPreviewUrl);
        this.headerMediaPreviewUrl = null;

        this.pHeaderMedia?.classList.add("d-none");
        this.pHeaderImg?.classList.add("d-none");
        this.pHeaderVideo?.classList.add("d-none");
        this.pHeaderDoc?.classList.add("d-none");

        if (this.pHeaderImg) this.pHeaderImg.src = "";
        if (this.pHeaderVideo) {
            this.pHeaderVideo.removeAttribute("src");
            this.pHeaderVideo.load();
        }

        if (this.headerMediaInput) this.headerMediaInput.value = "";
        if (this.headerMediaPayload) this.headerMediaPayload.value = "";

        this.renderPreview();
    };

    WATemplateComposer.prototype.renderVarsUI = function (scoped) {
        const headerVars = scoped.header || [];
        const bodyVars = scoped.body || [];
        const buttonVars = scoped.buttons || {};

        if (
            !headerVars.length &&
            !bodyVars.length &&
            !Object.keys(buttonVars).length
        ) {
            this.varsPanel?.classList.add("d-none");
            if (this.varsArea) this.varsArea.innerHTML = "";
            return;
        }

        this.varsPanel?.classList.remove("d-none");

        const renderInput = (label, scope, btnIndex, varNum, value) => {
            const btnAttr =
                btnIndex !== null && btnIndex !== undefined
                    ? `data-btn="${btnIndex}"`
                    : "";
            return `
        <div class="col-md-6">
          <label class="form-label mb-1">${label} {{${varNum}}}</label>
          <div class="input-group">
            <input type="text" class="form-control var-input"
              data-scope="${scope}" ${btnAttr} data-var="${varNum}"
              value="${escapeHtml(value || "")}">
            <button class="btn btn-outline-secondary btn-insert-var" type="button"
              data-scope="${scope}" ${btnAttr} data-var="${varNum}">
              Insert Variable
            </button>
          </div>
        </div>
      `;
        };

        const blocks = [];

        if (headerVars.length) {
            blocks.push(`
        <div class="mb-3">
          <div class="fw-semibold mb-2">Header Variables</div>
          <div class="row g-2">
            ${headerVars.map((n) => renderInput("HEADER", "header", null, n, this.campaignVars.header[n])).join("")}
          </div>
        </div>
      `);
        }

        if (bodyVars.length) {
            blocks.push(`
        <div class="mb-3">
          <div class="fw-semibold mb-2">Body Variables</div>
          <div class="row g-2">
            ${bodyVars.map((n) => renderInput("BODY", "body", null, n, this.campaignVars.body[n])).join("")}
          </div>
        </div>
      `);
        }

        const btnEntries = Object.entries(buttonVars);
        if (btnEntries.length) {
            blocks.push(`
        <div class="mb-2">
          <div class="fw-semibold mb-2">Button URL Variables</div>
          ${btnEntries
              .map(
                  ([btnIndex, vars]) => `
            <div class="border rounded p-2 mb-2">
              <div class="fw-semibold mb-2">Button ${Number(btnIndex) + 1}</div>
              <div class="row g-2">
                ${(vars || [])
                    .map((n) =>
                        renderInput(
                            "BTN",
                            "button",
                            btnIndex,
                            n,
                            this.campaignVars.buttons?.[btnIndex]?.[n],
                        ),
                    )
                    .join("")}
              </div>
            </div>
          `,
              )
              .join("")}
        </div>
      `);
        }

        if (this.varsArea) this.varsArea.innerHTML = blocks.join("");
    };

    WATemplateComposer.prototype.renderComponentPanels = function () {
        const header = this.findComp("HEADER");
        const footer = this.findComp("FOOTER");
        const buttons = this.findComp("BUTTONS");

        this.noTemplate?.classList.add("d-none");

        // HEADER
        if (header) {
            this.headerPanel?.classList.remove("d-none");
            const fmt = String(header.format || "TEXT").toUpperCase();
            if (this.headerType) this.headerType.textContent = fmt;

            this.headerTextWrap?.classList.add("d-none");
            this.headerMediaWrap?.classList.add("d-none");

            if (fmt === "TEXT") {
                this.headerTextWrap?.classList.remove("d-none");
                if (this.headerText) this.headerText.value = header.text || "";
            } else if (["IMAGE", "VIDEO", "DOCUMENT"].includes(fmt)) {
                this.headerMediaWrap?.classList.remove("d-none");
                this.setHeaderMediaHint(fmt);
            }
        } else {
            this.headerPanel?.classList.add("d-none");
        }

        // FOOTER
        if (footer?.text !== undefined) {
            this.footerPanel?.classList.remove("d-none");
            if (this.footerText) this.footerText.value = footer.text || "";
        } else {
            this.footerPanel?.classList.add("d-none");
        }

        // BUTTONS
        if (buttons?.buttons?.length) {
            this.buttonsPanel?.classList.remove("d-none");
            const btns = buttons.buttons
                .map((b, i) => `#${i + 1} ${b.type}: ${b.text}`)
                .join(" • ");
            if (this.buttonsInfo) this.buttonsInfo.textContent = btns;
        } else {
            this.buttonsPanel?.classList.add("d-none");
        }
    };

    WATemplateComposer.prototype.renderPreview = function () {
        // reset
        this.pHeaderText?.classList.add("d-none");
        this.pFooter?.classList.add("d-none");
        this.pHeaderMedia?.classList.add("d-none");
        if (this.pButtons) this.pButtons.innerHTML = "";
        this.hrHeader?.classList.add("d-none");
        this.hrFooter?.classList.add("d-none");

        if (!this.activeTemplate) {
            if (this.pBody) this.pBody.textContent = "";
            return;
        }

        const header = this.findComp("HEADER");
        const body = this.findComp("BODY");
        const footer = this.findComp("FOOTER");
        const buttons = this.findComp("BUTTONS");

        // header
        let hasHeader = false;
        if (header) {
            const fmt = String(header.format || "TEXT").toUpperCase();
            if (fmt === "TEXT") {
                const hText = this.applyScoped(
                    header.text || "",
                    this.campaignVars.header,
                );
                if (this.pHeaderText) {
                    this.pHeaderText.textContent = hText;
                    this.pHeaderText.classList.toggle("d-none", !hText);
                }
                hasHeader = !!hText;
            } else if (["IMAGE", "VIDEO", "DOCUMENT"].includes(fmt)) {
                this.pHeaderMedia?.classList.remove("d-none");
                this.pHeaderImg?.classList.add("d-none");
                this.pHeaderVideo?.classList.add("d-none");
                this.pHeaderDoc?.classList.add("d-none");

                const src =
                    this.headerMediaPreviewUrl ||
                    header?.example?.header_handle?.[0];

                if (fmt === "IMAGE" && src && this.pHeaderImg) {
                    this.pHeaderImg.src = src;
                    this.pHeaderImg.classList.remove("d-none");
                    hasHeader = true;
                } else if (fmt === "VIDEO" && src && this.pHeaderVideo) {
                    this.pHeaderVideo.src = src;
                    this.pHeaderVideo.classList.remove("d-none");
                    hasHeader = true;
                } else if (fmt === "DOCUMENT" && this.pHeaderDoc) {
                    this.pHeaderDoc.classList.remove("d-none");
                    if (this.pHeaderDocName) {
                        this.pHeaderDocName.textContent =
                            this.headerMediaInput?.files?.[0]?.name ||
                            "Document";
                    }
                    hasHeader = true;
                }
            }
        }
        if (hasHeader) this.hrHeader?.classList.remove("d-none");

        // body
        if (this.pBody)
            this.pBody.textContent = this.applyScoped(
                body?.text || "",
                this.campaignVars.body,
            );

        // footer
        const footerText = this.footerText?.value || footer?.text;
        if (footerText && this.pFooter) {
            this.pFooter.textContent = footerText;
            this.pFooter.classList.remove("d-none");
            this.hrFooter?.classList.remove("d-none");
        }

        // buttons
        if (buttons?.buttons?.length && this.pButtons) {
            this.pButtons.innerHTML = buttons.buttons
                .map((btn) => {
                    const isUrl = btn.type === "URL";
                    const icon = isUrl
                        ? this.assets.btnOpen || ""
                        : this.assets.btnReply || "";
                    return `
          <div class="wa-btn-item border-top py-2 text-center" style="cursor: default;">
            ${icon ? `<img class="wa-button-icon me-1" src="${icon}" style="width:14px; height:14px;" />` : ""}
            <span style="color:#00a5f4; font-size: 0.9rem;">${escapeHtml(btn.text || "")}</span>
          </div>
        `;
                })
                .join("");
        }
    };

    WATemplateComposer.prototype.onTemplateChange = function () {
        const opt = this.elTemplate.options[this.elTemplate.selectedIndex];

        // reset bits
        this.clearHeaderMediaPreview();
        if (this.varsArea) this.varsArea.innerHTML = "";

        if (!opt || !opt.value) {
            this.activeTemplate = null;
            this.campaignVars = { header: {}, body: {}, buttons: {} };

            this.headerPanel?.classList.add("d-none");
            this.varsPanel?.classList.add("d-none");
            this.footerPanel?.classList.add("d-none");
            this.buttonsPanel?.classList.add("d-none");
            this.noTemplate?.classList.remove("d-none");

            this.renderPreview();
            this.onChange?.(this.getState());
            return;
        }

        this.activeTemplate = {
            id: opt.value,
            name: opt.getAttribute("data-name") || "",
            language: opt.getAttribute("data-language") || "",
            category: opt.getAttribute("data-category") || "",
            components: safeParseComponents(opt) || [],
        };

        const scoped = this.ensureScopedVars(this.activeTemplate);
        this.renderComponentPanels();
        this.renderVarsUI(scoped);
        this.renderPreview();
        this.onChange?.(this.getState());
    };

    WATemplateComposer.prototype.openInsertVariableModal = function (onPick) {
        if (!global.Swal) {
            // fallback if SweetAlert isn't available
            const val = prompt("Enter variable e.g. {{contact.first_name}}");
            if (val) onPick?.(val);
            return;
        }

        const vars = {
            "{{contact.full_name}}": "{{contact.full_name}}",
            "{{contact.first_name}}": "{{contact.first_name}}",
            "{{contact.last_name}}": "{{contact.last_name}}",
            "{{contact.phone}}": "{{contact.phone}}",
            "{{contact.email}}": "{{contact.email}}",
            "{{contact.operator}}": "{{contact.operator}}",
            "{{contact.field_1}}": "{{contact.field_1}}",
            "{{contact.field_2}}": "{{contact.field_2}}",
            "{{contact.field_3}}": "{{contact.field_3}}",
        };

        global.Swal.fire({
            title: "Insert Variable",
            input: "select",
            inputOptions: vars,
            inputPlaceholder: "Choose...",
            showCancelButton: true,
            confirmButtonText: "Insert",
        }).then((res) => {
            if (!res.isConfirmed) return;
            onPick?.(res.value);
        });
    };

    WATemplateComposer.prototype.insertAtCursor = function (input, text) {
        const start = input.selectionStart ?? input.value.length;
        const end = input.selectionEnd ?? input.value.length;
        input.value =
            input.value.substring(0, start) + text + input.value.substring(end);
        input.selectionStart = input.selectionEnd = start + text.length;
        input.dispatchEvent(new Event("input"));
        input.focus();
    };

    WATemplateComposer.prototype.uploadHeaderMedia = async function () {
        if (!this.activeTemplate) return;

        const header = this.findComp("HEADER");
        const fmt = String(header?.format || "").toUpperCase();
        if (!["IMAGE", "VIDEO", "DOCUMENT"].includes(fmt)) {
            global.Swal?.fire(
                "Not required",
                "This template header is not media.",
                "info",
            );
            return;
        }

        const file = this.headerMediaInput?.files?.[0];
        if (!file) {
            global.Swal?.fire("No file", "Select a file first.", "warning");
            return;
        }

        const waType =
            fmt === "IMAGE" ? "image" : fmt === "VIDEO" ? "video" : "document";

        const form = new FormData();
        form.append("media", file);
        form.append("type", waType);

        if (this.btnUploadMedia) this.btnUploadMedia.disabled = true;

        try {
            const res = await fetch(this.routes.mediaUpload, {
                method: "POST",
                headers: { "X-CSRF-TOKEN": this.csrf },
                body: form,
            }).then((r) => r.json());

            if (this.btnUploadMedia) this.btnUploadMedia.disabled = false;

            if (!res?.ok) {
                global.Swal?.fire(
                    "Upload failed",
                    res?.message || "Could not upload file",
                    "error",
                );
                return;
            }

            if (this.headerMediaPayload) {
                this.headerMediaPayload.value = JSON.stringify({
                    media_disk: res.disk,
                    media_path: res.path,
                    mime_type: res.mime_type,
                    original_name: res.original_name,
                    file_size: res.file_size,
                    wa_type: res.type,
                });
            }

            global.Swal?.fire({
                icon: "success",
                title: "Uploaded",
                text: res.original_name || "Header media uploaded.",
            });
        } catch (e) {
            if (this.btnUploadMedia) this.btnUploadMedia.disabled = false;
            global.Swal?.fire("Upload failed", "Network error", "error");
        }
    };

    WATemplateComposer.prototype.getPayload = function () {
        return {
            template_id: this.activeTemplate?.id || null,
            variables: this.campaignVars,
            header_media: this.headerMediaPayload?.value
                ? JSON.parse(this.headerMediaPayload.value)
                : null,
            footer_text: this.footerText?.value || null,
            contacts: (this.getRecipients() || []).join(","),
        };
    };

    WATemplateComposer.prototype.getState = function () {
        return {
            activeTemplate: this.activeTemplate,
            vars: this.campaignVars,
            payload: this.getPayload(),
        };
    };

    WATemplateComposer.prototype.reset = function () {
        if (this.elTemplate) this.elTemplate.value = "";
        this.activeTemplate = null;
        this.campaignVars = { header: {}, body: {}, buttons: {} };
        this.clearHeaderMediaPreview();
        this.varsPanel?.classList.add("d-none");
        this.footerPanel?.classList.add("d-none");
        this.buttonsPanel?.classList.add("d-none");
        this.headerPanel?.classList.add("d-none");
        this.noTemplate?.classList.remove("d-none");
        if (this.varsArea) this.varsArea.innerHTML = "";
        this.renderPreview();
    };

    global.WATemplateComposer = WATemplateComposer;
})(window);
