import {
    handleAjaxError,
    xhrRequestStart,
    xhrRequestEnd,
    showStatusMessage
} from "@/js/shared/xhrStatus";

document.addEventListener('onImageSave', (e) => {
    // console.log('onImageSave:', e.detail, e);
    updateMedia(e.detail);
});

document.addEventListener('onCanvasStatusMessage', (e) => {
    // console.log('onCanvasStatusMessage:', e.detail);
});

document.addEventListener('onCloseImageEditor', (e) => {
    const imageEditor = e.detail.imageEditorInstance;
    const modal = imageEditor.closest('[data-mle-image-editor-modal]');
    // Always anchor events to the nearest media manager container
    const mediaManager = modal?.closest('[data-mle-media-manager]');
    if (!mediaManager) {
        console.warn('Media Manager element not found on close');
        return;
    }

    mediaManager.dispatchEvent(new CustomEvent('imageEditorModalCloseRequest', {
        bubbles: true,
        composed: true,
        detail: {'modal': modal}
    }));
});

const updateMedia = (detail) => {

    const modal = detail.imageEditorInstance.closest('[data-mle-image-editor-modal]');
    const configInput = modal.querySelector('[data-mle-image-editor-modal-config]');
    if (!configInput) return;

    let config = {};

    try {
        config = JSON.parse(configInput.value);
    } catch (e) {
        console.error('Invalid JSON config');
    }

    const useXhr = config.useXhr;

    if (!useXhr) {
        const file = detail.file;
        const form = modal.querySelector('[data-mle-image-editor-update-form]');

        // get or create the file input
        let fileInput = form.querySelector('[ data-mle-image-editor-update-form-file]');
        if (!fileInput) return

        // assign the File object using DataTransfer
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
        form.submit();
        return
    }

    // Resolve the media manager context directly from the modal
    const mediaManager = modal.closest('[data-mle-media-manager]');
    const initiator = mediaManager; // for backward event dispatch naming

    let mediaManagerStatusContainer = resolveStatusAreaContainer(mediaManager);

    // if (!initiator) {
    //     // Fallback to media manager ID if the specific item is gone
    //     const mediaManagerDomId = config.mediaManagerDomId;
    //     if (mediaManagerDomId) {
    //         initiator = document.querySelector('#' + mediaManagerDomId);
    //     }
    // }
    //
    // if (!initiator) {
    //     console.warn('Initiator element not found:', initiatorIdFromConfig, 'Media Manager:', config.mediaManagerDomId);
    //     return;
    // }

    const localStatusAreaContainer = resolveStatusAreaContainer(modal);
    let parentStatusAreaContainer = resolveStatusAreaContainer(initiator);// initiator = media manager
    const mediaLab = initiator.closest('[data-mle-media-manager-lab]');

    if (mediaLab) {
        parentStatusAreaContainer  = resolveStatusAreaContainer(mediaLab);
    }

    if (!localStatusAreaContainer) {
        console.warn('statusAreaContainer not found', localStatusAreaContainer);
        return;
    }
    xhrRequestStart(localStatusAreaContainer);

    // console.log('collections', config.collections);
    const file = detail.file;
    const formData = new FormData();
    const mediumId = config.mediumId ?? null;
    const modelType = config.modelType;
    const modelId = config.modelId ?? '';
    // instanceId is derived server-side from base_id; do not send from client
    const dataSource = config.dataSource;
    const baseId = modal.getAttribute('data-base-id') || config.id;

    formData.append('base_id', baseId);
    formData.append('model_type', modelType);
    formData.append('model_id', modelId );
    formData.append('single_media_id', config.singleMedia?.id ?? null);// TODO keep both?
    formData.append('medium_id', mediumId);// TODO keep both?
    // formData.append('collections', JSON.stringify(config.collections));
    formData.append('options', JSON.stringify(config.options));
    formData.append('collection', config.collection);
    formData.append('temporary_upload_mode', config.temporaryUploadMode);
    formData.append('file', file); // 'media' must match Laravel's expected field
    formData.append('data_source', dataSource); // 'media' must match Laravel's expected field

    // Inject current persistent client token
    if (config.clientToken) {
        formData.append('client_token', config.clientToken);
    }

    Object.entries(config.collections).forEach(([key, value]) => {
        formData.append(`collections[${key}]`, value);
    });

    fetch(config.storeUpdatedMediaRoute, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': config.csrfToken,
            'Accept': 'application/json'
        },
        body: formData,
        cache: 'no-store', // prevents using or storing cache
    })
    .then(async response => {
        let data = {};

        try {
            data = await response.json();
        } catch (e) {
            console.warn('Response is not JSON');

            try {
                data = {
                    message: await response.clone().text()
                };
            } catch {
                data = {
                    message: 'Unable to read response body'
                };
            }
        }

        if (!response.ok) {
            handleAjaxError(response, data, localStatusAreaContainer);// note localStatusArea when errors occur!
            // initiator.dispatchEvent(new CustomEvent('imageEditorModalCloseRequest', {
            //     bubbles: true,
            //     composed: true,
            //     detail: {'modal': modal}
            // }));
            throw new Error(`HTTP ${response.status}`);
        }

        return data;
    })
    .then(json => {

        // console.log('fire events imageEditorModalCloseRequest and refreshRequest and onImageUpdated');
        initiator.dispatchEvent(new CustomEvent('imageEditorModalCloseRequest', {
            bubbles: true,
            composed: true,
            detail: {'modal': modal}
        }));

        showStatusMessage(mediaManagerStatusContainer, {
           type: 'success',
           message: trans('medium_replaced'),
        });

        initiator.dispatchEvent(new CustomEvent('refreshRequest', {
            bubbles: true,
            composed: true,
            detail: {
                'singleMediaId': json.singleMediaId ?? null,
            }
        }));

        const newMediumId = json.newMediumId;
        // console.log('newMediumId', newMediumId);
        // Notify listeners that the previews were updated
        document.dispatchEvent(new CustomEvent('imageUpdated', {
            bubbles: false,
            detail: {
                mediumId: newMediumId,
                modelType: modelType,
                modelId: modelId,
                baseId: baseId,
            }
        }));
    }).
    catch(error => {
        showStatusMessage(localStatusAreaContainer, {
            type: 'error',
            message: trans('update_failed'),
        });
    }).
    finally(() => {
        xhrRequestEnd(localStatusAreaContainer);
    });
}

function resolveStatusAreaContainer(startNode) {
    if (!startNode) return null;

    return startNode.querySelector('[data-mle-status-area-container]');
}

function trans (key) {
    return window.mediaLibraryTranslations?.[key] || key;
}
