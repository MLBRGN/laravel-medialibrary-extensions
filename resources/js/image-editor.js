import EventBus from '@evertjanmlbrgn/imageshared/EventBus.js'

import { handleAjaxError, refreshMediaManager } from './general-helpers';

document.addEventListener('imageEditorReady', (e) => {
    console.log('Image editor is ready:', e.detail.instance);
    const imageEditor = e.detail.instance;

    const initiatorId = imageEditor.getAttribute('data-initiator-id');
    const name = imageEditor.getAttribute('data-medium-name');
    const path = imageEditor.getAttribute('data-medium-path');

    debugger;
    imageEditor.setImage(name, path, initiatorId);

    imageEditor.setConfiguration({
        debug: false,// Image disappears when debug is true when selecting
        rotateDegreesStep: 90,
        freeSelectDisabled: true,
        freeRotateDisabled: true,
        freeResizeDisabled: true,
        filtersDisabled: true,
        selectionAspectRatios: ['16:9', '4:3'],
        selectionAspectRatio: '16:9',
    });
});

EventBus.register('onImageSave', (e) => {

    const modal = document.getElementById(e.detail.id);
    updateMedia(e.detail)
    modal.dispatchEvent(new CustomEvent('onImageUpdated', {
        bubbles: true,
        composed: true,
        detail: e.detail
    }));
    console.log('onImageSave using event bus', e.detail);
});

EventBus.register('onCanvasStatusMessage', (e) => {
    console.log('onCanvasStatusMessage using event bus', e.detail);
});

EventBus.register('onCloseImageEditor', (e) => {
    console.log('onCloseImageEditor using event bus', e.detail);
});

const updateMedia = (detail) => {

    const modal = document.getElementById(detail.id);
    const configInput = modal.querySelector('.image-editor-modal-config');
    if (!configInput) return;

    let config = {};

    try {
        config = JSON.parse(configInput.value);
    } catch (e) {
        console.error('Invalid JSON config');
    }

    const {
        model_type: modelType,
        model_id: modelId,
        medium_id: mediumId,
        collection: collection,
        csrf_token: csrfToken,
        save_updated_medium_route: saveUpdatedMediumRoute,
        media_manager_id: mediaManagerId,
        // id: initiatorId,
        // frontend_theme: frontendTheme,
    } = config;

    const file = detail.file;
    console.log('file', file);

    const formData = new FormData();
    formData.append('model_type', config.model_type);
    formData.append('model_id', config.model_id);
    formData.append('medium_id', config.medium_id);
    formData.append('collection', config.collection);
    formData.append('file', file); // 'media' must match Laravel's expected field

    fetch(saveUpdatedMediumRoute, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
        .then(async response => {
            const data = await response.json();

            if (!response.ok) {
                console.log('response', response, 'data', data);
                handleAjaxError(response, data, function() {console.log('callback image editor handleAjaxError', data);});
                return;
            }

            // close modal
            closeModal(modal)
            refreshMediaManager(mediaManagerId);
        })
        .then(data => {
            console.log('Upload successful:', data);
        })
        .catch(error => {
            // TODO show status message
            console.error('Upload failed:', error);
        });
}

const closeModal = (modal) => {
    const closeButton = modal.querySelector('.image-editor-modal-close-button');
    closeButton.click()
}
