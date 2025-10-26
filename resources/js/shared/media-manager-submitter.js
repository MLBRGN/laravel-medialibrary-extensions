import config from "bootstrap/js/src/util/config";
import {
    showStatusMessage,
    handleAjaxError,
    trans,
    showSpinner,
    hideSpinner
} from './xhrStatus';

const mediaManagers = document.querySelectorAll('[data-media-manager]');

mediaManagers.forEach(mediaManager => {

    // const statusContainer = mediaManager.querySelector('[data-media-manager-layout]')
    const statusAreaContainer = mediaManager.querySelector('[data-status-area-container]')
    console.log('statusAreaContainer', statusAreaContainer);

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
            console.log('componentId', componentId);
            const component = document.querySelector('#'+componentId);
            const debugSection = component.querySelector('.mle-debug');

            console.log('debugSection', debugSection);
            debugSection.classList.toggle('hidden');

            console.log('toggle debugger TODO')
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

        showSpinner(statusAreaContainer);

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
            });

            // console.log('response', response);

            const data = await response.json();

            if (!response.ok) {
                handleAjaxError(response, data, statusAreaContainer);
                return;
            }

            showStatusMessage(statusAreaContainer, data);

            // TODO use data- attribute for refresh?
            if (action !== 'medium-restore') {
                updatePreview(mediaManager, config, {});
            }

            resetFields(formElement);
        } catch (error) {
            console.error('Error during upload:', error);
            showStatusMessage(statusAreaContainer, {
                type: 'error',
                message: trans('upload_failed'),
            });
        } finally {
            hideSpinner(statusAreaContainer);
        }
    });

        mediaManager.addEventListener('refreshRequest', function (e) {
            const detail = e.detail;
            const config = getMediaManagerConfig(mediaManager);
            // console.log('Refresh requested:', e);
            updatePreview(mediaManager, config, detail);
        })
});

function getMediaManagerConfig(mediaManager) {
    const configInput = mediaManager.querySelector('[data-media-manager-config]');
    if (!configInput) return null;

    try {
        return JSON.parse(configInput.value);
    } catch (e) {
        console.error(`Invalid JSON config for ${mediaManager.id}:`, e);
        return null;
    }
}

function getRouteFromAction(action, target, config) {

    console.log('target', target);
    console.log('config', config);
    const routes = {
        'upload-media': config.mediaUploadRoute,
        'upload-youtube-medium': config.youtubeUploadRoute,
        'destroy-medium': target?.dataset?.route,
        'set-as-first': target?.dataset?.route,
        'medium-restore': target?.dataset?.route,
    };

    console.log('routes', routes)

    return routes[action] || null;
}

function updatePreview(mediaManager, config, detail = {}) {
    // console.log('update preview:', mediaManager);
    const previewGrid = mediaManager.querySelector('[data-media-preview-grid]');
    const forms = mediaManager.querySelectorAll('[data-form], [data-xhr-form]');
    if (!previewGrid) return;

    const params = new URLSearchParams({
        model_type: config.modelType,
        model_id: config.modelId,
        single_medium_id: detail.singleMediumId ?? null,
        temporary_upload_mode: config.temporaryUploadMode,
        initiator_id: config.id,
        collections: JSON.stringify(config.collections),
        options: JSON.stringify(config.options),
    });

    fetch(`${config.previewUpdateRoute}?${params}`, {
        headers: { 'Accept': 'application/json' }
    })
        // .then(response => response.json())
        .then(async response => {
            const data = await response.json();

            if (!response.ok) {
                handleAjaxError(response, data);
                return;
            }

            return data;
        })
        .then(json => {
            if (!json.html) {
                return;
            }
            previewGrid.innerHTML = json.html;

            // when only as single medium is allowed, disable / enable form elements
            if (!config.multiple) {
                if (json.mediaCount !== undefined && json.mediaCount !== null) {
                    if (json.mediaCount < 1) {
                        // console.log('enable form elements');
                        enableFormElements(forms);
                    } else {
                        // console.log('disable form elements');
                        disableFormElements(forms);
                    }
                }
            }

            // can be listened to by other parts of the code to, for example, reinitialize functionality
            document.dispatchEvent(new CustomEvent('mediaManagerPreviewsUpdated', {
                bubbles: false,
                detail: {
                    'mediaManager': mediaManager,
                    'previewGrid': previewGrid,
                }
            }));
        })
        .catch(error => {
            console.error('Error refreshing media manager:', error);

        })
        .finally(() => {
        });
}

function getFormData(formElement) {
    const formData = new FormData();
    formElement.querySelectorAll('input').forEach(input => {
        if (input.type === 'file') {
            Array.from(input.files).forEach(file => {
                formData.append(input.name, file);
            });
        } else {
            formData.append(input.name, input.value);
        }
    });

    // console.log('formData', formData);

    // formData.forEach((value, key) => {
    //     console.log(key, value);
    // });
    return formData;
}

function resetFields(formElement) {
    formElement.querySelectorAll('input').forEach(input => {
        if (input.type !== 'hidden') {
            input.value = '';
        }
    });
}

function setFormElementsDisabled(forms, disabled) {
    forms.forEach(form => {
        // console.log('forms', forms, 'form', form)
        form.querySelectorAll('input:not([type="hidden"]), button')
            .forEach(el => el.disabled = disabled);
    });
}

function disableFormElements(forms) {
    setFormElementsDisabled(forms, true);
}

function enableFormElements(forms) {
    setFormElementsDisabled(forms, false);
}
