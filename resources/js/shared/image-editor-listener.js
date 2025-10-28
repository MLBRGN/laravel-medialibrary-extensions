import {handleAjaxError, hideSpinner, showSpinner, showStatusMessage} from "@/js/shared/xhrStatus";

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
    const configInput = modal.querySelector('[data-image-editor-modal-config]');
    if (!configInput) return;

    let config = {};

    try {
        config = JSON.parse(configInput.value);
        console.log(config);
    } catch (e) {
        console.error('Invalid JSON config');
    }

    const useXhr = config.useXhr;

    // console.log('config', config);
    if (!useXhr) {
        const file = detail.file;
        const form = modal.querySelector('[data-image-editor-update-form]');

        // get or create the file input
        let fileInput = form.querySelector('[ data-image-editor-update-form-file]');
        if (!fileInput) return

        // assign the File object using DataTransfer
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
        form.submit();
        return
    }

    const initiator = document.querySelector('#' + config.initiatorId);// TODO initiator not found after preview refresh!

    const statusAreaContainer = initiator.querySelector('[data-status-area-container]')
    const { statusContainer, spinnerContainer } = resolveStatusContext(initiator);

    showSpinner(spinnerContainer);

    // console.log('collections', config.collections);
    const file = detail.file;
    const formData = new FormData();
    const mediumId = config.mediumId ?? null;
    const modelType = config.modelType;
    const modelId = config.modelId ?? '';
    const initiatorId = config.initiatorId;

    formData.append('initiator_id', initiatorId);
    formData.append('media_manager_id', config.mediaManagerId ?? '');
    formData.append('model_type', modelType);
    formData.append('model_id', modelId );
    formData.append('single_medium_id', config.singleMedium?.id ?? null);// TODO keep both?
    formData.append('medium_id', mediumId);// TODO keep both?
    // formData.append('collections', JSON.stringify(config.collections));
    formData.append('options', JSON.stringify(config.options));
    formData.append('collection', config.collection);
    formData.append('temporary_upload_mode', config.temporaryUploadMode);
    formData.append('file', file); // 'media' must match Laravel's expected field
    Object.entries(config.collections).forEach(([key, value]) => {
        formData.append(`collections[${key}]`, value);
    });

    fetch(config.saveUpdatedMediumRoute, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': config.csrfToken,
            'Accept': 'application/json'
        },
        body: formData,
        cache: 'no-store', // prevents using or storing cache
    })
    .then(async response => {
        const json = await response.json();
        if (!response.ok) {
            handleAjaxError(response, json, statusAreaContainer);
            throw new Error('Update of medium failed');// stops the chain, goes to .catch
        }

        return json;
    })
    .then(json => {

        showStatusMessage(statusContainer, {
           type: 'success',
           message: trans('medium_replaced'),
        });



        console.log('fire events imageEditorModalCloseRequest and refreshRequest and onImageUpdated');
        initiator.dispatchEvent(new CustomEvent('imageEditorModalCloseRequest', {
            bubbles: true,
            composed: true,
            detail: {'modal': modal}
        }));

        initiator.dispatchEvent(new CustomEvent('refreshRequest', {
            bubbles: true,
            composed: true,
            detail: {
                'singleMediumId': json.singleMediumId ?? null,
            }
        }));

        const newMediumId = json.newMediumId;
        console.log('newMediumId', newMediumId);
        // Notify listeners that the previews were updated
        document.dispatchEvent(new CustomEvent('imageUpdated', {
            bubbles: false,
            detail: {
                mediumId: newMediumId,
                modelType: modelType,
                modelId: modelId,
                initiatorId: initiatorId,
            }
        }));
    }).finally(() => {
        hideSpinner(spinnerContainer);
    });
}

// TODO would like neater solution, not hacky
function resolveStatusContext(initiator) {
    // The default containers live relative to the initiator
    let statusContainer = initiator.querySelector('[data-status-area-container]');
    let spinnerContainer = statusContainer;

    // Walk up the DOM to find a higher-level media manager context if needed
    const parentLab = initiator.closest('[data-media-manager-lab]');
    if (parentLab && parentLab !== initiator.closest('[data-media-manager-lab]:scope')) {
        // If we're nested inside another lab, prefer the outermost one
        const parentStatusContainer = parentLab.querySelector('[data-status-area-container]');
        if (parentStatusContainer) {
            statusContainer = parentStatusContainer;
            spinnerContainer = parentStatusContainer;
        }
    }

    return { statusContainer, spinnerContainer };
}

function trans (key) {
    return window.mediaLibraryTranslations?.[key] || key;
}
