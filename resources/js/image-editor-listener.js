import {handleAjaxError, showStatusMessage} from "@/js/xhrStatus";

document.addEventListener('onImageSave', (e) => {
    // console.log('onImageSave:', e.detail, e);
    updateMedia(e.detail);
});

document.addEventListener('onCanvasStatusMessage', (e) => {
    console.log('onCanvasStatusMessage:', e.detail);
});

document.addEventListener('onCloseImageEditor', (e) => {
    const imageEditor = e.detail.imageEditorInstance;
    const modal = imageEditor.closest('[data-image-editor-modal]');
    const initiatorId = imageEditor.getAttribute('data-initiator-id');
    const initiator = document.querySelector('#' + initiatorId);

    initiator.dispatchEvent(new CustomEvent('imageEditorModalCloseRequest', {
        bubbles: true,
        composed: true,
        detail: {'modal': modal}
    }));
});

const updateMedia = (detail) => {

    const modal = detail.imageEditorInstance.closest('[data-image-editor-modal]');
    const configInput = modal.querySelector('.image-editor-modal-config');
    if (!configInput) return;

    let config = {};

    try {
        config = JSON.parse(configInput.value);
    } catch (e) {
        console.error('Invalid JSON config');
    }

    const initiator = document.querySelector('#' + config.initiator_id);
    const container = initiator.querySelector('.media-manager-row')

    const {
        model_type: modelType,
        model_id: modelId,
        medium_id: mediumId,
        collection: collection,
        csrf_token: csrfToken,
        save_updated_medium_route: saveUpdatedMediumRoute,
        temporary_upload: temporaryUpload,
    } = config;

    const file = detail.file;
    const formData = new FormData();
    formData.append('initiator_id', config.initiator_id);
    formData.append('model_type', config.model_type);
    formData.append('model_id', config.model_id ?? '');
    formData.append('medium_id', config.medium_id);
    formData.append('collection', config.collection);
    formData.append('image_collection', config.image_collection);
    formData.append('document_collection', config.document_collection);
    formData.append('youtube_collection', config.youtube_collection);
    formData.append('temporary_upload', config.temporary_upload);
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
        const json = await response.json();
        if (!response.ok) {
            handleAjaxError(response, json, container);
            throw new Error('Upload failed');// stops the chain, goes to .catch
        }

        return json;
    })
    .then(json => {
        showStatusMessage(container, {
            type: 'success',
            message: trans('medium_replaced'),
        });

        initiator.dispatchEvent(new CustomEvent('imageEditorModalCloseRequest', {
            bubbles: true,
            composed: true,
            detail: {'modal': modal}
        }));
        initiator.dispatchEvent(new CustomEvent('refreshRequest', {
            bubbles: true,
            composed: true,
            detail: []
        }));
    })
}

function trans (key) {
    return window.mediaLibraryTranslations?.[key] || key;
}
