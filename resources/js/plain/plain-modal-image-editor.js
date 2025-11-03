import { setupModalBase, closeModal, initModalEvents, registerModalEventHandler } from './_modal-core';
import '@/js/plain/_modal-core';

const editors = new WeakMap(); // modal => editor instance

function parseDimensions(dimensionString, fallback) {
    if (!dimensionString) return fallback;
    const [w, h] = dimensionString.split(/[x:]/).map(Number);
    return { width: w || fallback.width, height: h || fallback.height };
}

function initializeImageEditor(config) {
    console.log('initializeImageEditor config', config);
    const imageEditor = config.imageEditorInstance;

    if (!imageEditor) {
        console.warn('No imageEditorInstance provided.');
        return;
    }

    const {
        name,
        path,
        initiatorId,
        requiredAspectRatio,
        minDimensions,
        maxDimensions,
    } = config;

    imageEditor.setImage(name, path, initiatorId);
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

function setupImageEditorModal(modal) {
    if (modal.dataset.mleImageEditorInitialized) return;

    const placeholder = modal.querySelector('[data-mle-image-editor-placeholder]');

    const onOpen = () => {
        if (editors.has(modal)) return;

        let config = {};
        try {
            const configInput = modal.querySelector('[data-mle-image-editor-modal-config]');
            if (configInput) {
                config = JSON.parse(configInput.value);
            }
        } catch (e) {
            console.error('Invalid or missing data-mle-image-editor-modal-config:', e);
        }

        const mediumPath = modal.getAttribute('data-mle-medium-path');
        const displayName = modal.getAttribute('data-mle-medium-display-name');
        const forcedAspectRatio = modal.getAttribute('data-mle-medium-forced-aspect-ratio') ?? '16:9';
        const minDimensions = parseDimensions(modal.getAttribute('data-mle-medium-minimal-dimensions'), { width: 800, height: 600 });
        const maxDimensions = parseDimensions(modal.getAttribute('data-mle-medium-maximal-dimensions'), { width: 7040, height: 3960 });
        const initiatorId = config.initiatorId;

        const editor = document.createElement('image-editor');
        editor.id = 'my-image-editor';

        editor.addEventListener('imageEditorReady', e => {
            initializeImageEditor({
                imageEditorInstance: e.detail.imageEditorInstance,
                name: displayName,
                path: mediumPath,
                initiatorId,
                requiredAspectRatio: forcedAspectRatio,
                minDimensions,
                maxDimensions,
            });
        }, { once: true });

        placeholder.appendChild(editor);
        editors.set(modal, editor);
    };

    const onClose = () => {
        const editor = editors.get(modal);
        if (editor) {
            editor.remove();
            editors.delete(modal);
        }
        placeholder.innerHTML = '';
    };

    setupModalBase(modal, onClose, onOpen);
    modal.dataset.mleImageEditorInitialized = 'true';
}

/**
 * Global handler for image updates inside any editor modal.
 */
function globalImageUpdatedHandler(e) {
    const modal = e.target.closest('[data-mle-image-editor-modal]');
    if (!modal) return;
    closeModal(modal);
}

// Register modal event handlers
registerModalEventHandler('onImageUpdated', globalImageUpdatedHandler);

// Initialize any pre-rendered modals
initModalEvents();
document.querySelectorAll('[data-mle-image-editor-modal]').forEach(setupImageEditorModal);

// Reinitialize after media previews are refreshed
document.addEventListener('mediaManagerPreviewsUpdated', e => {
    const mediaManager = e.detail.mediaManager;
    mediaManager.querySelectorAll('[data-mle-image-editor-modal]').forEach(setupImageEditorModal);
});

// Handle external close requests
document.addEventListener('imageEditorModalCloseRequest', e => {
    const modal = e.detail.modal;
    closeModal(modal);
});

// Observe dynamically added modals
const observeDynamicModals = () => {
    const observer = new MutationObserver(mutations => {
        for (const mutation of mutations) {
            for (const node of mutation.addedNodes) {
                if (!(node instanceof HTMLElement)) continue;

                if (node.matches('[data-mle-image-editor-modal]')) {
                    setupImageEditorModal(node);
                }

                node.querySelectorAll?.('[data-mle-image-editor-modal]').forEach(setupImageEditorModal);
            }
        }
    });

    observer.observe(document.body, { childList: true, subtree: true });
};

observeDynamicModals();
