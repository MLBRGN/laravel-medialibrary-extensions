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
        const target = e.target.closest('[data-mle-action]');
        if (!target) return;

        const action = target.getAttribute('data-mle-action');
        if (!action || action === 'debugger-toggle') return;

        // console.log('action', action);

        const config = getMediaManagerConfig(mediaManagerLab);
        if (!config) return;

        // if not using XHR skip let form handle normal submission
        const useXhr = config.useXhr;
        if (!useXhr) return

        e.preventDefault();

        const mediumId = target.dataset.mleMediumId;

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

        xhrRequestStart(statusAreaContainer);

        try {

            // Legacy example block removed (initiator_id/media_manager_id)

            // console.log(formElement);
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

            updatePreviews(mediaManagerLab, config, mediumId, { part : 'base' });

            showStatusMessage(statusAreaContainer, data);

            // Debug snippet removed
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

    // Custom refreshRequest listener removed
});

function getRouteFromAction(action, target, config) {
    const routes = {
        'medium-restore': target?.dataset?.mleRoute,
    };

    return routes[action] || null;
}
