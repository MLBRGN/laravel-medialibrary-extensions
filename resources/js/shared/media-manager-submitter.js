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

const mediaManagers = document.querySelectorAll('[data-mle-media-manager]');

mediaManagers.forEach(mediaManager => {

    // console.log('mediaManager', mediaManager);
    const statusAreaContainer = mediaManager.querySelector('[data-mle-status-area-container]')

    mediaManager.addEventListener('click', async function (e) {
        const target = e.target.closest('[data-mle-action]');
        if (!target) return;

        const action = target.getAttribute('data-mle-action');
        if (action === 'debugger-toggle') return;

        // console.log('action', action);

        const config = getMediaManagerConfig(mediaManager);
        if (!config) return;

        // if not using XHR skip let form handle normal submission
        const useXhr = config.useXhr;
        if (!useXhr) return

        e.preventDefault();

        const formElement = target.closest('[data-mle-xhr-form]');
        const method = formElement?.getAttribute('data-xhr-method') ?? 'post';
        const route = getRouteFromAction(action, target, config);

        if (!route) {
            showStatusMessage(statusAreaContainer, {
                type: 'error',
                message: trans('invalid_action'),
            });
            return;
        }

        console.log('media-manager-submitter.js - statusAreaContainer: ' + statusAreaContainer)
        xhrRequestStart(statusAreaContainer);

        try {
            const formData = getFormData(formElement);

            // Inject current persistent client token
            formData.set('client_token', config.clientToken);

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
                handleAjaxError(response, data, statusAreaContainer);
                return;
            }

            // Ensure we have a Base ID in config for targeting
            if (!config.id) {
                console.warn('media-manager-submitter.js - missing Base ID in config', config);
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

    // console.log('getRouteFromAction', action, target, config);
    const routes = {
        'upload-media': config.routes.mediaUpload,
        'upload-youtube-medium': config.routes.youtubeUpload,
        'destroy-medium': target?.dataset?.mleRoute,
        'set-as-first': target?.dataset?.mleRoute,
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
