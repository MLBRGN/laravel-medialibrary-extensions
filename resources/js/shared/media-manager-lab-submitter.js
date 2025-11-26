import {
    showStatusMessage,
    handleAjaxError,
    trans,
    xhrRequestStart,
    xhrRequestEnd
} from './xhrStatus';

import { updatePreviews } from './media-manager-lab-previews-refresher'
import { getFormData } from './form';
import { getMediaManagerConfig } from './media-manager-config';

const mediaManagerLabs = document.querySelectorAll('[data-mle-media-manager-lab]');

mediaManagerLabs.forEach(mediaManagerLab => {

    const statusAreaContainer = mediaManagerLab.querySelector('[data-mle-status-area-container]')

    mediaManagerLab.addEventListener('click', async function (e) {
        const config = getMediaManagerConfig(mediaManagerLab);
        if (!config) return;

        // if not using XHR skip let form handle normal submission
        const useXhr = config.useXhr;
        if (!useXhr) return

        const target = e.target.closest('[data-mle-action]');

        const action = target?.getAttribute('data-mle-action');
        if (!action) return;

        console.log('action', action);

        e.preventDefault();

        const mediumId = target.dataset.mleMediumId;
        if (!target) return;

        const formElement = target.closest('[data-mle-xhr-form]');
        const method = formElement?.getAttribute('data-xhr-method') ?? 'post';
        const route = getRouteFromAction(action, target, config);

        if (action === 'debugger-toggle') {
            return
        }

        if (!route) {
            showStatusMessage(statusAreaContainer, {
                type: 'error',
                message: trans('invalid_action'),
            });
            return;
        }

        xhrRequestStart(statusAreaContainer);

        try {

            // const formData = getFormData(formElement);
            // // const formData = new FormData();
            //
            // // const file = detail.file;
            // const mediumId = config.mediumId ?? null;
            // const modelType = config.modelType;
            // const modelId = config.modelId ?? '';
            // const initiatorId = config.initiatorId;
            //
            // formData.append('initiator_id', initiatorId);
            // formData.append('model_type', modelType);
            // formData.append('model_id', modelId );
            // formData.append('single_medium_id', config.singleMedium?.id ?? null);// TODO keep both?
            //
            // formData.append('options', JSON.stringify(config.options));
            //
            // formData.append('temporary_upload_mode', config.temporaryUploadMode);
            // // formData.append('media_manager_id', config.mediaManagerId ?? '');
            // // formData.append('medium_id', mediumId);// TODO keep both?
            // // formData.append('collections', JSON.stringify(config.collections));
            // // formData.append('collection', config.collection);
            //
            // const normalizedMethod = method.toUpperCase();
            // if (['DELETE', 'PUT', 'PATCH'].includes(normalizedMethod)) {
            //     formData.append('_method', normalizedMethod);
            // }

            console.log(formElement);
            const formData = getFormData(formElement);
            console.log('just before formData')
            formData.forEach((value, key) => {
                console.log(key, value);
            });
            const normalizedMethod = method.toUpperCase();
            if (['DELETE', 'PUT', 'PATCH'].includes(normalizedMethod)) {
                formData.append('_method', normalizedMethod);
            }
            const response = await fetch(route, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': config.csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
                cache: 'no-store', // prevents using or storing cache
            });

            const data = await response.json();

            if (!response.ok) {
                handleAjaxError(response, data, statusAreaContainer);
                return;
            }

            updatePreviews(mediaManagerLab, config, mediumId, { part : 'base' });

            showStatusMessage(statusAreaContainer, data);

            // const div = document.createElement('div');
            // console.log(mediaManagerLab);
            // div.innerHTML = 'mediaManagerLab id:' + mediaManagerLab.id + ' mediumId: ' + mediumId + 'config.initiatorId:  ' + config.initatorId;
            // mediaManagerLab.appendChild(div);
        } catch (error) {
            console.error('Error during upload:', error);
            showStatusMessage(statusAreaContainer, {
                type: 'error',
                message: trans('upload_failed'),
            });
        } finally {
            xhrRequestEnd(statusAreaContainer);
        }
    });

    // mediaManagerLab.addEventListener('refreshRequest', function (e) {
    //     const detail = e.detail;
    //     const config = getMediaManagerConfig(mediaManagerLab);
    //     updatePreviews(mediaManagerLab, config, detail);
    // })
});

function getRouteFromAction(action, target, config) {
    const routes = {
        'medium-restore': target?.dataset?.mleRoute,
    };

    return routes[action] || null;
}
