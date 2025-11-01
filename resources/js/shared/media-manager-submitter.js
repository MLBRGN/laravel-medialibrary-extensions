import config from "bootstrap/js/src/util/config";
import {
    showStatusMessage,
    handleAjaxError,
    trans,
    xhrRequestStart,
    xhrRequestEnd,
} from './xhrStatus';

import { updatePreviews } from './media-manager-previews-refresher'
import { getFormData } from './form';
import { getMediaManagerConfig } from './media-manager-config';

const mediaManagers = document.querySelectorAll('[data-media-manager]');

mediaManagers.forEach(mediaManager => {

    // const statusContainer = mediaManager.querySelector('[data-media-manager-layout]')
    const statusAreaContainer = mediaManager.querySelector('[data-status-area-container]')

    mediaManager.addEventListener('click', async function (e) {
        const config = getMediaManagerConfig(mediaManager);
        if (!config) return;

        // if not using XHR skip let form handle normal submission
        const useXhr = config.useXhr;
        if (!useXhr) return

        const target = e.target.closest('[data-action]');
        if (!target) return;

        e.preventDefault();
        const action = target.getAttribute('data-action');

        if (action === 'debugger-toggle') {

            const componentId = config.id;
            const component = document.querySelector('#'+componentId);
            const debugSection = component.querySelector('.mle-debug');

            debugSection.classList.toggle('hidden');

            return
        }

        const formElement = target.closest('[data-xhr-form]');
        const method = formElement?.getAttribute('data-xhr-method') ?? 'post';
        const route = getRouteFromAction(action, target, config);

        if (!route) {
            showStatusMessage(statusAreaContainer, {
                type: 'error',
                message: trans('invalid_action'),
            });
            return;
        }

        xhrRequestStart(statusAreaContainer);

        try {
            const formData = getFormData(formElement);
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

            showStatusMessage(statusAreaContainer, data);
            updatePreviews(mediaManager, config, {});
            resetFields(formElement);
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

        mediaManager.addEventListener('refreshRequest', function (e) {
            const detail = e.detail;
            const config = getMediaManagerConfig(mediaManager);
            updatePreviews(mediaManager, config, detail);
        })
});

function getRouteFromAction(action, target, config) {

    const routes = {
        'upload-media': config.mediaUploadRoute,
        'upload-youtube-medium': config.youtubeUploadRoute,
        'destroy-medium': target?.dataset?.route,
        'set-as-first': target?.dataset?.route,
    };

    return routes[action] || null;
}

function resetFields(formElement) {
    formElement.querySelectorAll('input').forEach(input => {
        if (input.type !== 'hidden') {
            input.value = '';
        }
    });
}
