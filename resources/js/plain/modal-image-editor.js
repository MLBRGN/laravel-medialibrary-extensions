import { setupModalBase, closeModal, initModalEvents } from './modal-core';

function setupImageEditorModal(modal) {
    if (modal.dataset.imageEditorInitialized) return;

    const placeholder = modal.querySelector('[image-editor-placeholder]');
    let editor = null;

    const onClose = () => {
        if (editor) {
            editor.remove();
            editor = null;
        }
    };

    const onOpen = () => {
        if (editor) return; // already exists

        let config = {};
        try {
            const configInput = modal.querySelector('.image-editor-modal-config');
            if (configInput) {
                config = JSON.parse(configInput.value);
            }
        } catch (e) {
            console.error('Invalid or missing image-editor-modal-config:', e);
        }

        const mediumPath = modal.getAttribute('data-medium-path');
        const displayName = modal.getAttribute('data-medium-display-name');
        const initiatorId = config.initiator_id;

        editor = document.createElement('image-editor');
        editor.setAttribute('id', 'my-image-editor');
        editor.setAttribute('data-medium-display-name', displayName);
        editor.setAttribute('data-medium-path', mediumPath);
        editor.setAttribute('data-initiator-id', initiatorId);

        editor.addEventListener('imageEditorReady', (e) => {
            console.log('imageEditorReady', e);
            // Only initialize once per instance
            initializeImageEditor(e.detail);
        }, { once: true }); // make sure listener only fires once

        placeholder.appendChild(editor);

        modal.addEventListener('onImageUpdated', (e) => {
            console.log('onImageUpdated', e);
            closeModal(modal);
        });
    };

    setupModalBase(modal, onClose, onOpen);

    modal.dataset.imageEditorInitialized = 'true';
}

document.addEventListener('DOMContentLoaded', () => {
    initModalEvents();
    document.querySelectorAll('[data-image-editor-modal]').forEach(setupImageEditorModal);
});

function initializeImageEditor(detail) {
    const imageEditor = detail.imageEditorInstance;
    console.log('initializeImageEditorModal', detail, 'imageEditorInstance', imageEditor);

    const initiatorId = imageEditor.getAttribute('data-initiator-id');
    const name = imageEditor.getAttribute('data-medium-display-name');
    const path = imageEditor.getAttribute('data-medium-path');

    console.log('imageEditor.setImage(', name, ',', path, ',', initiatorId, ')');

    imageEditor.setImage(name, path, initiatorId);
    imageEditor.setConfiguration({
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
