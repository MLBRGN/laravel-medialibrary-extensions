import EventBus from '@evertjanmlbrgn/imageshared/EventBus.js'

document.addEventListener('imageEditorReady', (e) => {
    console.log('Image editor is ready:', e.detail.instance);
    // You now have full access to the image editor instance
    const imageEditor = e.detail.instance;

    const initiatorId = imageEditor.getAttribute('data-initiator-id');
    const name = imageEditor.getAttribute('data-medium-name');
    const path = imageEditor.getAttribute('data-medium-path');

    // console.log('Calling setImage:', { name, path, initiatorId });
    imageEditor.setImage(name, path, initiatorId);

    imageEditor.setConfiguration({
        debug: false,// Image disapears when debug is true when selecting
        rotateDegreesStep: 90
    });
    // console.log(imageEditor.configuration);

});

EventBus.register('onImageSave', (e) => {

    const modal = document.getElementById(e.detail.id);
    // updateMedia(e.detail)
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
    // console.log('imageEditor', imageEditor);
    // const initiatorId = imageEditor.getAttribute('data-initiator-id');
    const configInput = modal.querySelector('.image-editor-modal-config');
    // console.log('initiatorId', initiatorId);
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
        // id: initiatorId,
        // frontend_theme: frontendTheme,
    } = config;

    console.log('modelType', modelType);
    console.log('saveUpdatedMediumRoute', saveUpdatedMediumRoute);

    const file = detail.file;
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
                    // handleAjaxError(response, data, formContainer);
                    handleAjaxError(response, data);
                    return;
                }

                // close modal

                console.log('data', data)
                console.log('modal', modal);
                // const modal = document.getElementById(initiatorId);
                console.log('close modal',  modal);
                const closeButton = modal.querySelector('.image-editor-modal-close-button');
                closeButton.click()
                // showStatusMessage(formContainer, data);

                // const flash = document.getElementById(formElement.dataset.target + '-flash');
                // if (flash && data.message) {
                //     flash.innerHTML = `<div class="alert alert-${data.type}">${data.message}</div>`;
                // }

                // refreshMediaManager();
                // resetFields(formElement);
            })
            .then(data => {
                console.log('Upload successful:', data);
            })
            .catch(error => {
                console.error('Upload failed:', error);
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
