import { setupModalBase, closeModal, initModalEvents, registerModalEventHandler } from './modal-core';

document.addEventListener('DOMContentLoaded', () => {

    const editors = new WeakMap(); // modal => editor instance

    function setupImageEditorModal(modal) {
        if (modal.dataset.imageEditorInitialized) return;

        const placeholder = modal.querySelector('[data-image-editor-placeholder]');

        const onClose = () => {
            const editor = editors.get(modal);
            if (editor) {
                editor.remove();
                editors.delete(modal);
            }
        };

        const onOpen = () => {
            if (editors.has(modal)) return; // already exists

            let config = {};
            try {
                const configInput = modal.querySelector('.image-editor-modal-config');
                if (configInput) {
                    config = JSON.parse(configInput.value);
                }
            } catch (e) {
                console.error('Invalid or missing image-editor-modal-config:', e);
            }

            const mediumPath = modal.dataset.mediumPath;
            const displayName = modal.dataset.mediumDisplayName;
            const initiatorId = config.initiatorId;

            const editor = document.createElement('image-editor');
            editor.id = 'my-image-editor';
            editor.dataset.mediumDisplayName = displayName;
            editor.dataset.mediumPath = mediumPath;
            editor.dataset.initiatorId = initiatorId;

            // initialize editor once ready
            editor.addEventListener('imageEditorReady', e => {
                initializeImageEditor(e.detail);
            }, { once: true });

            placeholder.appendChild(editor);
            editors.set(modal, editor);
        };

        setupModalBase(modal, onClose, onOpen);
        modal.dataset.imageEditorInitialized = 'true';
    }

    function initializeImageEditor(detail) {
        const editor = detail.imageEditorInstance;
        if (!editor) return;

        const initiatorId = editor.dataset.initiatorId;
        const name = editor.dataset.mediumDisplayName;
        const path = editor.dataset.mediumPath;

        editor.setImage(name, path, initiatorId);
        editor.setConfiguration({
            debug: false,
            rotateDegreesStep: 90,
            freeSelectDisabled: true,
            freeRotateDisabled: true,
            freeResizeDisabled: true,
            filtersDisabled: true,
            selectionAspectRatios: ['16:9', '4:3'],
            selectionAspectRatio: '16:9',
        });
    }

    /**
     * Global handler for image updates inside any editor modal.
     * This replaces per-modal `addEventListener('onImageUpdated')`.
     */
    function globalImageUpdatedHandler(e) {
        const modal = e.target.closest('[data-image-editor-modal]');
        if (!modal) return;
        closeModal(modal);
    }

    // register global modal event handlers
    registerModalEventHandler('onImageUpdated', globalImageUpdatedHandler);

    // initial init
    initModalEvents();

    document.querySelectorAll('[data-image-editor-modal]').forEach(setupImageEditorModal);

    // reinitialize after previews update
    document.addEventListener('mediaManagerPreviewsUpdated', e => {
        const mediaManager = e.detail.mediaManager;
        mediaManager.querySelectorAll('[data-image-editor-modal]')
            .forEach(setupImageEditorModal);

        // console.log('reinitialized image editor modals for media manager', mediaManager);
    });

    document.addEventListener('imageEditorModalCloseRequest', (e) => {
        const modal = e.detail.modal;
        // console.log('imageEditorModalCloseRequest', modal);
        closeModal(modal);
    });
});
