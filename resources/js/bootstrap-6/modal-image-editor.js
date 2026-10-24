// noinspection JSUnresolvedReference
import {getMleBootstrapInstance} from "@/js/bootstrap-6/bootstrap-resolver";

if (window.mleImageEditorDialogInitialized) return;
window.mleImageEditorDialogInitialized = true;

const closeBootstrapDialog = (dialog) => {
    // try to find a close button that uses data-bs-dismiss="dialog" first
    // as it's more reliable in some environments than the JS API
    const closeButton = dialog.querySelector('[data-bs-dismiss="dialog"]');
    if (closeButton) {
        closeButton.click();
    }

    const bs = getMleBootstrapInstance();
    const dialogInstance = bs.Dialog.getOrCreateInstance(dialog);
    dialogInstance.hide();

    // Safety fallback for tests/race conditions: if it's still visible after a tiny tick, force it.
    // We use a small timeout to allow Bootstrap's native event cycle a chance to run first.
    setTimeout(() => {
        if (dialog.classList.contains('show') || dialog.hasAttribute('open')) {
            // console.warn('Forcing dialog closed after hide() failed');
            dialog.classList.remove('show');
            dialog.style.display = 'none';
            if (typeof dialog.close === 'function') {
                try {
                    dialog.close();
                } catch (e) {
                    // console.warn('dialog.close() failed', e);
                }
            }
            document.body.classList.remove('dialog-open');
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
            const backdrop = document.querySelector('.dialog-backdrop, .modal-backdrop');
            if (backdrop) backdrop.remove();
        }
    }, 10);
}

function initializeImageEditor(config) {
    // console.log('initializeImageEditor config', config)
    const imageEditor = config.imageEditorInstance;

    if (!imageEditor) {
        console.warn('No imageEditorInstance provided.');
        return;
    }

    const {
        name,
        path,
        baseId,
        requiredAspectRatio,
        minDimensions,
        maxDimensions,
    } = config;

    console.log('just before setImage')
    console.log('name: ', name, ' path: ', path, ' baseId: ', baseId)

    // Base ID is the single source of truth for scoping/identity
    imageEditor.setImage(name, path, baseId);

    console.log('just after setImage')

    const imageEditorConfig = {
        debug: false,
        rotateDegreesStep: 90,
        freeSelectDisabled: true,
        freeRotateDisabled: true,
        freeResizeDisabled: true,
        filtersDisabled: true,
        selectionAspectRatios: [requiredAspectRatio],
        selectionAspectRatio: requiredAspectRatio,
        minWidth: minDimensions.width,
        minHeight: minDimensions.height,
        maxWidth: maxDimensions.width,
        maxHeight: maxDimensions.height,
        imagePropertiesEnabled: false,
        fileFormatEnabled: false,
        rotationEnabled: true,
        mirroringEnabled: true,
        selectingEnabled: true,
        croppingEnabled: true,
        gridEnabled: false,
        downloadingEnabled: false,
        freeSelectEnabled: false,
        freeRotationEnabled: false,
        resizingEnabled: false,
        filtersEnabled: false,
        selectionInfoEnabled: false,
        selectionAspectRatioEnabled: false,
        helpEnabled: false,
    }
    imageEditor.setConfiguration(imageEditorConfig);
}

function initializeImageEditorDialog(dialog) {
    // console.log('initializeImageEditorDialog', dialog);
    // console.log('dialog is initialized', dialog.dataset.mleImageEditorInitialized)
    // console.log('dialog is initialized', dialog.getAttribute('data-mle-image-editor-initialized'))
    if (dialog.dataset.mleImageEditorInitialized === 'true') {
        // console.log('dialog already initialized, skipping')
        return;
    }

    dialog.dataset.mleImageEditorInitialized = 'true';

    const placeholder = dialog.querySelector('[data-mle-image-editor-placeholder]');

    dialog.addEventListener('show.bs.dialog', function (event) {
        if (event.target !== dialog) return;
        // console.log('show.bs.dialog', dialog.id);
        const imageEditorDialogConfig = JSON.parse(dialog.querySelector('[data-mle-media-manager-config]').value);
        const mediumPath = dialog.getAttribute('data-mle-medium-path');
        const displayName = dialog.getAttribute('data-mle-medium-display-name');
        const forcedAspectRatio = dialog.getAttribute('data-mle-medium-forced-aspect-ratio') ?? '4:3';
        const minDimensions = parseDimensions(dialog.getAttribute('data-mle-medium-minimal-dimensions'), { width: 800, height: 600 });
        const maxDimensions = parseDimensions(dialog.getAttribute('data-mle-medium-maximal-dimensions'), { width: 7040, height: 3960 });
        // Prefer baseId from config; fall back to data-base-id on the dialog
        const baseId = imageEditorDialogConfig.baseId
            ?? dialog.getAttribute('data-base-id');

        const mountEditor = () => {
            placeholder.innerHTML = '';

            const editor = document.createElement('image-editor');
            editor.id = 'mle-image-editor';

            editor.addEventListener('imageEditorReady', (e) => {
                console.log('imageEditorReady', e);
                initializeImageEditor({
                    imageEditorInstance: e.detail.imageEditorInstance,
                    name: displayName,
                    path: mediumPath,
                    baseId,
                    requiredAspectRatio: forcedAspectRatio,
                    minDimensions,
                    maxDimensions,
                });
            }, { once: true });

            placeholder.appendChild(editor);
        };

        // If the custom element is not registered yet, wait for it instead of bailing out
        if (!customElements.get('image-editor')) {
            console.warn('<image-editor> custom element is not registered yet. Waiting…');
            customElements.whenDefined('image-editor').then(mountEditor);
            return;
        }

        mountEditor();
    });

    dialog.addEventListener('hidden.bs.dialog', function (event) {
        if (event.target !== dialog) return;
        // console.log('hidden.bs.dialog', dialog.id);
        placeholder.innerHTML = '';
    });

    dialog.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeBootstrapDialog(dialog);
        }
    });
}

function parseDimensions(dimensionString, fallback) {
    if (!dimensionString) return fallback;
    const [w, h] = dimensionString.split(/[x:]/).map(Number);
    return { width: w || fallback.width, height: h || fallback.height };
}

// Handle external close requests
document.addEventListener('imageEditorModalCloseRequest', e => {
    console.log('bootstrap-6/modal-image-editor.js - imageEditorModalCloseRequest received', e.detail);
    const dialog = e.detail.modal || e.detail.dialog;
    if (!dialog) {
        console.warn('bootstrap-6/modal-image-editor.js - No dialog provided in event detail');
        return;
    }
    closeBootstrapDialog(dialog);
});

// observe dynamic models, e.g. added later on by javascript, for example in media lab when refreshing previews
const observeDynamicDialogs = () => {
    // console.log('observeDynamicDialogs')
    const observer = new MutationObserver(mutations => {
        for (const mutation of mutations) {
            for (const node of mutation.addedNodes) {
                if (!(node instanceof HTMLElement)) continue;

                // Direct dialog element
                if (node.matches('[data-mle-image-editor-dialog]')) {
                    // console.log('found image editor dialog to initialize')
                    initializeImageEditorDialog(node);
                }

                // Nested dialogs inside appended fragments
                node.querySelectorAll?.('[data-mle-image-editor-dialog]').forEach(initializeImageEditorDialog);
            }
        }
    });

    observer.observe(document.body, { childList: true, subtree: true });
};

// Start watching
observeDynamicDialogs();

document.querySelectorAll('[data-mle-image-editor-dialog]').forEach(initializeImageEditorDialog);
