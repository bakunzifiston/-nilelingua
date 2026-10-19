/* Nile Lingua Lab — admin dashboard behaviour (no dependencies). */
(() => {
    'use strict';

    const $ = (selector, root = document) => root.querySelector(selector);
    const $$ = (selector, root = document) => Array.from(root.querySelectorAll(selector));
    const csrf = () => $('meta[name="csrf-token"]')?.content || '';

    // Toast ------------------------------------------------------------------
    const toastEl = $('[data-toast]');
    let toastTimer;
    const toast = (message) => {
        if (!toastEl) return;
        toastEl.textContent = message;
        toastEl.classList.add('is-visible');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toastEl.classList.remove('is-visible'), 2600);
    };

    // Sidebar on small screens ----------------------------------------------
    const shell = $('[data-shell]');
    const menuToggle = $('[data-menu-toggle]');
    const setMenu = (open) => {
        shell?.classList.toggle('is-menu-open', open);
        menuToggle?.setAttribute('aria-expanded', String(open));
    };
    menuToggle?.addEventListener('click', () => setMenu(!shell.classList.contains('is-menu-open')));
    shell?.addEventListener('click', (event) => {
        if (shell.classList.contains('is-menu-open') && !event.target.closest('.a-sidebar') && !event.target.closest('[data-menu-toggle]')) setMenu(false);
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (shell?.classList.contains('is-menu-open')) { setMenu(false); menuToggle?.focus(); }
            $$('details[data-dismissable][open]').forEach((details) => details.removeAttribute('open'));
        }
    });
    document.addEventListener('click', (event) => {
        $$('details[data-dismissable][open]').forEach((details) => {
            if (!details.contains(event.target)) details.removeAttribute('open');
        });
    });

    // Flash messages ---------------------------------------------------------
    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-dismiss]');
        if (button) button.closest('.a-flash')?.remove();
    });

    // Confirmation dialog ----------------------------------------------------
    const dialog = $('[data-confirm-dialog]');
    const confirmAction = ({ title, text, button }) => new Promise((resolve) => {
        if (!dialog || typeof dialog.showModal !== 'function') {
            resolve(window.confirm(text || title));
            return;
        }
        $('[data-confirm-title]', dialog).textContent = title || 'Are you sure?';
        $('[data-confirm-text]', dialog).textContent = text || '';
        const ok = $('[data-confirm-ok]', dialog);
        const cancel = $('button[value="cancel"]', dialog);
        ok.textContent = button || 'Confirm';

        // Answer from the buttons (and Escape) directly; handlers are replaced on every use.
        const finish = (answer) => {
            ok.onclick = cancel.onclick = dialog.oncancel = null;
            if (dialog.open) dialog.close();
            resolve(answer);
        };
        ok.onclick = (event) => { event.preventDefault(); finish(true); };
        cancel.onclick = (event) => { event.preventDefault(); finish(false); };
        dialog.oncancel = (event) => { event.preventDefault(); finish(false); };
        if (!dialog.open) dialog.showModal();
        cancel.focus();
    });

    document.addEventListener('submit', async (event) => {
        const form = event.target;
        if (!form.matches('form[data-confirm]') || form.dataset.confirmed === '1') return;
        event.preventDefault();
        const ok = await confirmAction({ title: form.dataset.confirmTitle, text: form.dataset.confirm, button: form.dataset.confirmButton });
        if (ok) {
            form.dataset.confirmed = '1';
            form.requestSubmit ? form.requestSubmit() : form.submit();
        }
    });

    // Field helpers ----------------------------------------------------------
    const autosize = (textarea) => {
        textarea.style.height = 'auto';
        textarea.style.height = `${Math.min(textarea.scrollHeight + 2, 640)}px`;
    };
    const initFields = (root = document) => {
        $$('textarea[data-autosize]', root).forEach(autosize);
        $$('[data-counter-for]', root).forEach((counter) => {
            const field = document.getElementById(counter.dataset.counterFor);
            if (!field) return;
            const update = () => {
                const length = field.value.length;
                const limit = Number(counter.dataset.limit);
                counter.textContent = `${length} / ${limit}`;
                counter.classList.toggle('is-over', length > limit);
            };
            field.addEventListener('input', update);
            update();
        });
    };
    initFields();

    document.addEventListener('input', (event) => {
        if (event.target.matches('textarea[data-autosize]')) autosize(event.target);
    });

    document.addEventListener('change', (event) => {
        const select = event.target.closest('[data-media-select]');
        if (select) {
            const thumb = select.closest('.a-media-field')?.querySelector('[data-media-thumb]');
            if (thumb) thumb.style.backgroundImage = select.value ? `url("${select.dataset.base}${select.value}")` : '';
        }
        const visibility = event.target.closest('[data-visibility]');
        if (visibility) {
            const item = visibility.closest('[data-item]');
            item?.classList.toggle('is-hidden-section', !visibility.checked);
            const summary = item?.querySelector(':scope > details > summary');
            let badge = summary?.querySelector('[data-hidden-badge]');
            if (!visibility.checked && summary && !badge) {
                badge = document.createElement('span');
                badge.className = 'a-badge a-badge-off';
                badge.dataset.hiddenBadge = '';
                badge.textContent = 'Hidden';
                summary.querySelector('.a-item-tools')?.before(badge);
            } else if (visibility.checked) {
                badge?.remove();
            }
        }
    });

    // Item titles follow their first text field.
    document.addEventListener('input', (event) => {
        const field = event.target;
        if (!field.matches('.a-input, .a-textarea')) return;
        const item = field.closest('[data-item]');
        const body = item?.querySelector(':scope > details > .a-item-body');
        if (!body) return;
        // Same priority as the server: a heading or title names the item, otherwise its first text field.
        const own = [...body.querySelectorAll('.a-input:not([type="number"]), .a-textarea')].filter((input) => input.closest('[data-item]') === item);
        const named = ['title', 'heading', 'headline', 'label', 'question', 'name']
            .map((key) => own.find((input) => input.name.endsWith(`[${key}]`)))
            .find(Boolean);
        if ((named || own[0]) !== field) return;
        const title = item.querySelector(':scope > details > summary [data-summary]');
        if (title) title.textContent = field.value.replace(/\*+/g, '').trim().slice(0, 90) || 'Untitled';
    });

    // Unsaved changes --------------------------------------------------------
    let dirty = false;
    const dirtyForms = $$('form[data-content-form], form[data-dirty-form]');
    const markDirty = () => {
        if (dirty) return;
        dirty = true;
        $$('[data-dirty-status]').forEach((status) => {
            status.textContent = 'Unsaved changes';
            status.classList.add('is-dirty');
        });
    };
    dirtyForms.forEach((form) => {
        form.addEventListener('input', markDirty);
        form.addEventListener('change', markDirty);
    });
    window.addEventListener('beforeunload', (event) => {
        if (dirty) {
            event.preventDefault();
            event.returnValue = '';
        }
    });

    // Content forms are sent as one JSON field, so long pages are never cut off by
    // PHP's limit on the number of form fields.
    $$('form[data-content-form]').forEach((form) => {
        form.addEventListener('submit', () => {
            dirty = false;
            const target = $('[data-json-field]', form);
            if (!target) return;
            const pairs = [];
            Array.from(form.elements).forEach((element) => {
                if (!element.name || !element.name.startsWith('c[') || element.disabled) return;
                if ((element.type === 'checkbox' || element.type === 'radio') && !element.checked) return;
                pairs.push([element.name, element.value]);
            });
            target.value = JSON.stringify(pairs);
            // Send only the JSON copy (re-enabled on return, see pageshow).
            Array.from(form.elements).forEach((element) => {
                if (element.name && element.name.startsWith('c[') && !element.disabled) {
                    element.dataset.wasEnabled = '1';
                    element.disabled = true;
                }
            });
            setTimeout(restoreFields, 1500); // when the form opens a preview tab, this page stays open
        });
    });
    const restoreFields = () => {
        $$('[data-was-enabled]').forEach((element) => {
            element.disabled = false;
            delete element.dataset.wasEnabled;
        });
    };
    window.addEventListener('pageshow', restoreFields);
    $$('form[data-dirty-form]').forEach((form) => form.addEventListener('submit', () => { dirty = false; }));

    // Repeatable lists -------------------------------------------------------
    let counter = 0;
    const updateCount = (repeater) => {
        const count = $$(':scope > [data-items] > [data-item]', repeater).length;
        const label = $(':scope > .a-repeater-head [data-count-label]', repeater);
        if (label) label.textContent = `${count} ${count === 1 ? 'item' : 'items'}${count > 1 ? ' · drag or use the arrows to reorder' : ''}`;
        $(':scope > [data-empty-note]', repeater)?.toggleAttribute('hidden', count > 0);
    };

    document.addEventListener('click', async (event) => {
        const add = event.target.closest('[data-add]');
        if (add) {
            const repeater = add.closest('[data-repeater]');
            const template = $(`:scope > template[data-template="${add.dataset.add}"]`, repeater);
            if (!template) return;
            counter += 1;
            const html = template.innerHTML.split(repeater.dataset.placeholder).join(`n${Date.now().toString(36)}${counter}`);
            const holder = document.createElement('div');
            holder.innerHTML = html.trim();
            const item = holder.firstElementChild;
            $(':scope > [data-items]', repeater).append(item);
            initFields(item);
            updateCount(repeater);
            markDirty();
            item.querySelector('.a-input, .a-textarea, select')?.focus();
            return;
        }

        const tool = event.target.closest('[data-move], [data-remove]');
        if (tool && tool.closest('[data-item]')) {
            event.preventDefault(); // buttons sit inside <summary>: do not toggle it
            const item = tool.closest('[data-item]');
            const repeater = item.closest('[data-repeater]');
            if (tool.matches('[data-remove]')) {
                const title = item.querySelector('[data-summary]')?.textContent.trim();
                const ok = await confirmAction({
                    title: 'Remove this item?',
                    text: `${title ? `“${title}” will be removed. ` : ''}The change is applied when you save.`,
                    button: 'Remove',
                });
                if (!ok) return;
                const next = item.nextElementSibling || item.previousElementSibling;
                item.remove();
                updateCount(repeater);
                markDirty();
                (next?.querySelector('[data-remove], [data-move]') || $('[data-add]', repeater))?.focus();
                return;
            }
            const sibling = tool.dataset.move === 'up' ? item.previousElementSibling : item.nextElementSibling;
            if (!sibling) return;
            tool.dataset.move === 'up' ? sibling.before(item) : sibling.after(item);
            tool.focus();
            markDirty();
            toast('Moved. Save to keep the new order.');
        }
    });

    // Drag and drop (items move only when dragged by their handle).
    let dragged = null;
    document.addEventListener('pointerdown', (event) => {
        const handle = event.target.closest('[data-handle]');
        if (!handle) return;
        const item = handle.closest('[data-item], tr[data-id]');
        if (item) item.draggable = true;
    });
    document.addEventListener('dragstart', (event) => {
        const item = event.target.closest?.('[data-item], tr[data-id]');
        if (!item || !item.draggable) return;
        dragged = item;
        item.classList.add('is-dragging');
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', '');
    });
    document.addEventListener('dragover', (event) => {
        if (!dragged) return;
        const over = event.target.closest('[data-item], tr[data-id]');
        if (!over || over === dragged || over.parentElement !== dragged.parentElement) return;
        event.preventDefault();
        const rect = over.getBoundingClientRect();
        const after = event.clientY > rect.top + rect.height / 2;
        after ? over.after(dragged) : over.before(dragged);
    });
    document.addEventListener('dragend', () => {
        if (!dragged) return;
        dragged.classList.remove('is-dragging');
        dragged.draggable = false;
        if (dragged.matches('tr[data-id]')) {
            saveRowOrder(dragged.closest('[data-sortable-rows]'));
        } else {
            markDirty();
        }
        dragged = null;
    });

    // Collection order (saved immediately).
    const saveRowOrder = async (tbody) => {
        if (!tbody) return;
        const order = $$('tr[data-id]', tbody).map((row) => Number(row.dataset.id));
        try {
            const response = await fetch(tbody.dataset.reorderUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf() },
                body: JSON.stringify({ order }),
            });
            toast(response.ok ? 'Order saved.' : 'The order could not be saved. Please reload and try again.');
        } catch {
            toast('The order could not be saved. Check your connection.');
        }
    };
    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-row-move]');
        if (!button) return;
        const row = button.closest('tr[data-id]');
        const sibling = button.dataset.rowMove === 'up' ? row.previousElementSibling : row.nextElementSibling;
        if (!sibling) return;
        button.dataset.rowMove === 'up' ? sibling.before(row) : sibling.after(row);
        button.focus();
        saveRowOrder(row.closest('[data-sortable-rows]'));
    });

    // Copy links -------------------------------------------------------------
    document.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-copy]');
        if (!button) return;
        try {
            await navigator.clipboard.writeText(button.dataset.copy);
            toast('Link copied.');
        } catch {
            window.prompt('Copy this link:', button.dataset.copy);
        }
    });

    // Media upload: refuse files that are too large before sending, and show progress while uploading.
    $$('form[data-upload-form]').forEach((form) => {
        const input = $('input[type="file"]', form);
        const error = $('[data-upload-error]', form);
        const progress = $('[data-upload-progress]', form);
        const button = $('button[type="submit"]', form);
        const max = Number(form.dataset.maxBytes) || 0;
        const mb = (bytes) => `${(bytes / 1048576).toFixed(1)} MB`;
        const showError = (message) => {
            error.textContent = message;
            error.hidden = !message;
            input.toggleAttribute('aria-invalid', Boolean(message));
        };
        const check = () => {
            const file = input.files[0];
            if (!file) return false;
            if (max && file.size > max) {
                const hint = /\.svg$/i.test(file.name) ? ' This SVG probably contains a photo: save it as a JPG or WebP instead, which is much smaller.' : ' Make the image smaller (for example 1600 pixels wide, saved as JPG or WebP) and try again.';
                showError(`This file is ${mb(file.size)}, but the limit is ${form.dataset.maxLabel}.${hint}`);
                return false;
            }
            showError('');
            return true;
        };
        input.addEventListener('change', check);
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            if (!input.files[0]) { showError('Choose an image to upload.'); return; }
            if (!check()) return;
            const request = new XMLHttpRequest();
            request.open('POST', form.action);
            request.setRequestHeader('Accept', 'text/html');
            request.upload.addEventListener('progress', (e) => {
                if (!e.lengthComputable) return;
                const percent = Math.round((e.loaded / e.total) * 100);
                progress.textContent = percent < 100 ? `Uploading… ${percent}% (${mb(e.loaded)} of ${mb(e.total)})` : 'Saving the image…';
            });
            // Show the page the server answered with, so its success or error message appears.
            request.addEventListener('load', () => {
                if (request.responseURL) window.history.replaceState(null, '', request.responseURL);
                document.open();
                document.write(request.responseText);
                document.close();
            });
            request.addEventListener('error', () => {
                button.disabled = false;
                progress.hidden = true;
                showError('The upload was interrupted. Check your internet connection and try again.');
            });
            button.disabled = true;
            progress.hidden = false;
            progress.textContent = 'Uploading… 0%';
            request.send(new FormData(form));
        });
    });

    // New item: suggest an address from the title ---------------------------
    const slugSource = $('[data-slug-source]');
    const slugTarget = $('[data-slug-target]');
    const slugPreview = $('[data-slug-preview]');
    if (slugSource && slugTarget) {
        let touched = slugTarget.value !== '';
        const slugify = (text) => text.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().replace(/&/g, ' and ').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 80);
        const sync = () => { if (slugPreview) slugPreview.textContent = slugTarget.value || 'your-address'; };
        slugSource.addEventListener('input', () => { if (!touched) { slugTarget.value = slugify(slugSource.value); sync(); } });
        slugTarget.addEventListener('input', () => {
            touched = true;
            slugTarget.value = slugTarget.value.toLowerCase().replace(/[^a-z0-9-]+/g, '-').replace(/-{2,}/g, '-');
            sync();
        });
        slugTarget.addEventListener('change', () => { slugTarget.value = slugify(slugTarget.value); sync(); });
    }
})();
