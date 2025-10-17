import config from "bootstrap/js/src/util/config";
import {
    showStatusMessage,
    handleAjaxError,
    trans,
    showSpinner,
    hideSpinner
} from './xhrStatus';

document.addEventListener('DOMContentLoaded', function () {
    const mediaManagers = document.querySelectorAll('[data-media-manager]');

    mediaManagers.forEach(mediaManager => {

        const container = mediaManager.querySelector('.media-manager-row');

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
            // console.log('action', action);
            const formElement = target.closest('[data-xhr-form]');
            const method = formElement?.getAttribute('data-xhr-method') ?? 'post';
            const route = getRouteFromAction(action, target, config);

            if (!route) {
                showStatusMessage(container, {
                    type: 'error',
                    message: trans('invalid_action'),
                });
                return;
            }

            showSpinner(container);

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

                console.log('response', response);

                const data = await response.json();

                if (!response.ok) {
                    handleAjaxError(response, data, container);
                    return;
                }

                showStatusMessage(container, data);
                updatePreview(mediaManager, config);
                resetFields(formElement);
            } catch (error) {
                console.error('Error during upload:', error);
                showStatusMessage(container, {
                    type: 'error',
                    message: trans('upload_failed'),
                });
            } finally {
                hideSpinner(container);
            }
        });

        mediaManager.addEventListener('refreshRequest', function (e) {
            const config = getMediaManagerConfig(mediaManager);
            // console.log('Refresh requested:', e);
            updatePreview(mediaManager, config);
        })
    });

    function getMediaManagerConfig(mediaManager) {
        const configInput = mediaManager.querySelector('.media-manager-config');
        if (!configInput) return null;

        try {
            return JSON.parse(configInput.value);
        } catch (e) {
            console.error(`Invalid JSON config for ${mediaManager.id}:`, e);
            return null;
        }
    }

    function getRouteFromAction(action, target, config) {
        const mediaContainer = target.closest('.media-manager-preview-media-container');
        const routes = {
            'upload-media': config.mediaUploadRoute,
            'upload-youtube-medium': config.youtubeUploadRoute,
            'temporary-upload-destroy': mediaContainer?.dataset?.temporaryUploadDestroyRoute,
            'destroy-medium': mediaContainer?.dataset?.destroyRoute,
            'set-as-first': mediaContainer?.dataset?.setAsFirstRoute,
            'temporary-upload-set-as-first': mediaContainer?.dataset?.temporaryUploadSetAsFirstRoute,
        };

        return routes[action] || null;
    }

    function updatePreview(mediaManager, config) {
        // console.log('update preview:', mediaManager);
        const previewGrid = mediaManager.querySelector('.media-manager-preview-grid');
        const forms = mediaManager.querySelectorAll('form, [data-xhr-form]');
        if (!previewGrid) return;

        const params = new URLSearchParams({
            model_type: config.modelType,
            model_id: config.modelId,
            // medium_id: config.medium.id,
            temporary_upload_mode: config.temporaryUploadMode,
            initiator_id: config.id,
            collections: JSON.stringify(config.collections),
            options: JSON.stringify(config.options),
        });
        // console.log('params2', Object.fromEntries(params));

        // showSpinner(container);
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
        console.log('formData', formData);
        formData.forEach((value, key) => {
            console.log(key, value);
        });
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
});
