import EventBus from '@evertjanmlbrgn/imageshared/EventBus.js'

document.addEventListener('imageEditorReady', (e) => {
    console.log('Image editor is ready:', e.detail.instance);
    // You now have full access to the image editor instance
    const imageEditor = e.detail.instance;

    const initiatorId = imageEditor.getAttribute('data-initiator-id');
    const name = imageEditor.getAttribute('data-medium-name');
    const path = imageEditor.getAttribute('data-medium-path');

    imageEditor.setImage(name, path, initiatorId);

    imageEditor.setConfiguration({
        debug: false,// Image disapears when debug is true when selecting
        rotateDegreesStep: 90,
        freeSelectDisabled: true,
        freeRotateDisabled: true,
        freeResizeDisabled: true,
        filtersDisabled: true,
        selectionAspectRatios: ['16:9', '4:3'],
        selectionAspectRatio: '16:9',
    });
    // console.log(imageEditor.configuration);

});

EventBus.register('onImageSave', (e) => {
    updateMedia(e.detail);
});

EventBus.register('onCanvasStatusMessage', (e) => {
    console.log('onCanvasStatusMessage using event bus', e.detail);
});

EventBus.register('onCloseImageEditor', (e) => {
    console.log('onCloseImageEditor using event bus', e.detail);
});

const updateMedia = (detail) => {

    const modal = document.getElementById(detail.id);
    console.log('modal', modal);
    // updateMedia(e.detail)
    console.log('dispatching', detail)
    console.log('onImageSave using event bus', detail);
    console.log('updateMedia', detail);
    const configInput = modal.querySelector('.image-editor-modal-config');
    if (!configInput) return;

    let config = {};

    try {
        config = JSON.parse(configInput.value);
    } catch (e) {
        console.error('Invalid JSON config');
    }
    console.log('config', config);

    const {
        model_type: modelType,
        model_id: modelId,
        medium_id: mediumId,
        collection: collection,
        csrf_token: csrfToken,
        save_updated_medium_route: saveUpdatedMediumRoute,
        temporary_upload: temporaryUpload,
    } = config;

    console.log('temporaryUpload', temporaryUpload);
    console.log('modelType', modelType);
    console.log('saveUpdatedMediumRoute', saveUpdatedMediumRoute);

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

    console.log('saveUpdatedMediaRoute', saveUpdatedMediumRoute);
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
                    handleAjaxError(response, data);
                    return;
                }

                // showStatusMessage(formContainer, data);



            })
            .then(data => {
                console.log('Upload successful:', data);
            })
            .catch(error => {
                console.error('Upload failed:', error);
            }).finally(() => {
                modal.dispatchEvent(new CustomEvent('onImageUpdated', {
                    bubbles: true,
                    composed: true,
                    detail: detail
                }));
            const initiator = document.querySelector('#'+config.initiator_id);
            console.log('initiator', initiator);
            initiator.dispatchEvent(new CustomEvent('refreshRequest', {
                bubbles: true,
                composed: true,
                detail: []
            }));
        });

}

function handleAjaxError(response, data) {
// function handleAjaxError(response, data, formContainer) {
    let errorMessage = trans('upload_failed'); // default fallback message

    switch (response.status) {
        case 419:
            errorMessage = trans('csrf_token_mismatch');
            break;
        case 401:
            errorMessage = trans('unauthenticated');
            break;
        case 403:
            errorMessage = trans('forbidden');
            break;
        case 404:
            errorMessage = trans('not_found');
            break;
        case 422:
            if (data.errors) {
                // Show all validation errors
                for (const field in data.errors) {
                    data.errors[field].forEach(msg => {
                        // showStatusMessage(formContainer, { message: msg, type: 'error' });
                    });
                }
                return; // return
            }
            errorMessage = data.message || trans('validation_failed');// default
            break;
        case 429:
            errorMessage = trans('too_many_requests');
            break;
        case 500:
        case 503:
            errorMessage = trans('server_error');
            break;
        default:
            errorMessage = data.message || errorMessage;
            break;
    }

    console.log(errorMessage);
    // showStatusMessage(formContainer, { message: errorMessage, type: 'error' });
}

function trans(key) {
    return window.mediaLibraryTranslations?.[key] || key;
}
