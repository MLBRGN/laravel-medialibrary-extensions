document.addEventListener('onImageSave', (e) => {
    console.log('onImageSave:', e.detail, e);
    updateMedia(e.detail);
});

document.addEventListener('onCanvasStatusMessage', (e) => {
    console.log('onCanvasStatusMessage:', e.detail);
});

document.addEventListener('onCloseImageEditor', (e) => {
    console.log('onCloseImageEditor:', e.detail);
});

const updateMedia = (detail) => {

    console.log('updateMedia', detail);
    // const imageEditorInstance = document.getElementById(detail.imageEditorInstance);
    // console.log('imageEditorInstance', imageEditorInstance);
    const modal = detail.imageEditorInstance.closest('[data-image-editor-modal]');
    const configInput = modal.querySelector('[data-image-editor-modal-config]');
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
            const data = await response.json();

            if (!response.ok) {
                handleAjaxError(response, data);
                return;
            }

            return data;
            // showStatusMessage(formContainer, data);
        })
        .then(data => {
            console.log('Upload successful:', data);
        })
        .catch(error => {
            console.error('Upload failed:', error);
        }).finally(() => {

        const modalInstance = bootstrap.Modal.getInstance(modal);
        modalInstance.hide();

        const initiator = document.querySelector('#' + config.initiator_id);
        // console.log('initiator', initiator);
        initiator.dispatchEvent(new CustomEvent('refreshRequest', {
            bubbles: true,
            composed: true,
            detail: []
        }));
    });
}

function handleAjaxError (response, data) {
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
                return;
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

function trans (key) {
    return window.mediaLibraryTranslations?.[key] || key;
}
