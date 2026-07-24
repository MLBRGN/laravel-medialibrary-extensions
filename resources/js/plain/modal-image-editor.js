import {
    setupModalLifecycle,
    closeModal,
    registerModalEventHandler,
    registerModalInitializer
} from './modal-core';
import '@/js/plain/modal-core';

const editors = new WeakMap(); // modal => editor instance

function initializeImageEditor(config) {
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

    // Base ID is the single source of truth for scoping/identity
    imageEditor.setImage(name, path, baseId);
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

function initializeImageEditorModal(modal) {
    if (modal.dataset.mleImageEditorInitialized === 'true') {
        return;
    }
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
        // Prefer baseId from config; fall back to data-base-id on the modal
        const baseId = config.baseId
            ?? modal.getAttribute('data-base-id');

        const mountEditor = () => {
            placeholder.innerHTML = '';

            const editor = document.createElement('image-editor');
            editor.id = 'my-image-editor';

            editor.addEventListener('imageEditorReady', (e) => {
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
            editors.set(modal, editor);
        };

        if (!customElements.get('image-editor')) {
            console.warn('<image-editor> custom element is not registered yet. Waiting…');
            customElements.whenDefined('image-editor').then(mountEditor);
            return;
        }

        mountEditor();
    };

    const onClose = () => {
        const editor = editors.get(modal);
        if (editor) {
            editor.remove();
            editors.delete(modal);
        }
        placeholder.innerHTML = '';
    };

    setupModalLifecycle(modal, onClose, onOpen);
    modal.dataset.mleImageEditorInitialized = 'true';
}

function parseDimensions(dimensionString, fallback) {
    if (!dimensionString) return fallback;
    const [w, h] = dimensionString.split(/[x:]/).map(Number);
    return { width: w || fallback.width, height: h || fallback.height };
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

// Handle external close requests
document.addEventListener('imageEditorModalCloseRequest', e => {
    const modal = e.detail.modal;
    closeModal(modal);
});

registerModalInitializer(
    '[data-mle-image-editor-modal]',
    initializeImageEditorModal
);
