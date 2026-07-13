import { setupModalBase, closeModal, initModalEvents, registerModalEventHandler } from './modal-core';
import '@/js/plain/modal-core';

const editors = new WeakMap(); // modal => editor instance

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
    // console.log('initializeImageEditorModal', modal);
    // console.log('modal is initialized', modal.dataset.mleImageEditorInitialized)
    // console.log('modal is initialized', modal.getAttribute('data-mle-image-editor-initialized'))
    if (modal.dataset.mleImageEditorInitialized === 'true') {
        // console.log('modal already initialized, skipping')
        return;
    } else {
        // console.log('modal not initialized, initializing')
    }

    const placeholder = modal.querySelector('[data-mle-image-editor-placeholder]');

    // console.log('placeholder', placeholder)
    const onOpen = () => {
        // console.log('onOpen', modal, editors.has(modal));
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
            // console.log('editors.set called', modal, editor)
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

    // console.log('just before setupModalBase')
    setupModalBase(modal, onClose, onOpen);
    modal.dataset.mleImageEditorInitialized = 'true';
    // console.log('set modal initialized to true')
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

// Initialize any pre-rendered modals
initModalEvents();
document.querySelectorAll('[data-mle-image-editor-modal]').forEach(initializeImageEditorModal);

// Reinitialize after media previews are refreshed
document.addEventListener('mediaManagerPreviewsUpdated', e => {
    // console.log('reinitialize image editor modals for media manager', e);
    const mediaManager = e.detail.mediaManager;
    mediaManager.querySelectorAll('[data-mle-image-editor-modal]').forEach(initializeImageEditorModal);
});

// Handle external close requests
document.addEventListener('imageEditorModalCloseRequest', e => {
    const modal = e.detail.modal;
    closeModal(modal);
});

// observe dynamic models, e.g. added later on by javascript, for example in media lab when refreshing previews
const observeDynamicModals = () => {
    console.log('observeDynamicModals: observing dynamic modals')
    // console.log('observeDynamicModals')
    const observer = new MutationObserver(mutations => {

        console.log('observeDynamicModals: mutations', mutations)
        for (const mutation of mutations) {
            for (const node of mutation.addedNodes) {
                const isElement = node instanceof Element; // HTMLElement, SVGElement, etc.
                const isFragment = node instanceof DocumentFragment;
                if (!isElement && !isFragment) {
                    continue;
                }

                console.log('observeDynamicModals: mutation added node', node)

                // If the added node itself is the modal element
                if (isElement && node.matches?.('[data-mle-image-editor-modal]')) {
                    console.log('observeDynamicModals: found image editor modal to initialize (direct)')
                    initializeImageEditorModal(node);
                }

                // Look inside the added node (Element or DocumentFragment) for any nested modals
                node.querySelectorAll?.('[data-mle-image-editor-modal]')
                    .forEach((modal) => {
                        console.log('observeDynamicModals: found nested image editor modal to initialize', modal)
                        initializeImageEditorModal(modal);
                    });
            }
        }
    });

    observer.observe(document.body, { childList: true, subtree: true });
};

// Start watching
observeDynamicModals();

// document.querySelectorAll('[data-mle-image-editor-modal]').forEach(initializeImageEditorModal);
