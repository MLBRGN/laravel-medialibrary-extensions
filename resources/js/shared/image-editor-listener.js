import {handleAjaxError, showStatusMessage} from "@/js/shared/xhrStatus";

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

    const useXhr = config.useXhr;
    console.log('useXhr', useXhr);

    if (!useXhr) {
        const file = detail.file;
        const form = modal.querySelector('[data-image-editor-update-form]');

        // get or create the file input
        let fileInput = form.querySelector('input[type="file"][name="file"]');
        if (!fileInput) return

        // assign the File object using DataTransfer
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
        form.submit();
        return
    }

    const initiator = document.querySelector('#' + config.initiatorId);
    const container = initiator.querySelector('.media-manager-row')

    // const {
    //     model_type: modelType,
    //     model_id: modelId,
    //     medium_id: mediumId,
    //     collection: collection,
    //     csrf_token: csrfToken,
    //     save_updated_medium_route: saveUpdatedMediumRoute,
    //     temporary_upload: temporaryUpload,
    // } = config;

    const file = detail.file;
    const formData = new FormData();
    formData.append('initiator_id', config.initiatorId);
    formData.append('media_manager_id', config.mediaManagerId ?? '');
    formData.append('model_type', config.modelType);
    formData.append('model_id', config.modelId ?? '');
    formData.append('medium_id', config.mediumId);
    formData.append('collection', config.collection);
    formData.append('image_collection', config.imageCollection);
    formData.append('document_collection', config.documentCollection);
    formData.append('youtube_collection', config.youtubeCollection);
    formData.append('temporary_upload_mode', config.temporaryUploadMode);
    formData.append('file', file); // 'media' must match Laravel's expected field

    fetch(config.saveUpdatedMediumRoute, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': config.csrfToken,
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
