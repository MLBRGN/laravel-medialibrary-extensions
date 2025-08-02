import config from "bootstrap/js/src/util/config";

document.addEventListener('DOMContentLoaded', function () {
    const mediaManagers = document.querySelectorAll('[data-media-manager]');
    let statusMessageTimeout = null;

    mediaManagers.forEach(mediaManager => {
        const container = mediaManager.querySelector('.media-manager-row');

        mediaManager.addEventListener('click', async function (e) {
            const config = getMediaManagerConfig(mediaManager);
            if (!config) return;

            const target = e.target.closest('[data-action]');
            if (!target) return;

            e.preventDefault();
            const action = target.getAttribute('data-action');
            console.log('action', action);
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
                        'X-CSRF-TOKEN': config.csrf_token,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (!response.ok) {
                    handleAjaxError(response, data, container);
                    return;
                }

                showStatusMessage(container, data);
                updatePreview(mediaManager, config);

                // const flash = document.getElementById(`${formElement.dataset.target}-flash`);
                // if (flash && data.message) {
                //     flash.innerHTML = `<div class="alert alert-${data.type}">${data.message}</div>`;
                // }

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
            console.log('Refresh requested:', e);
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
            'upload-media': config.media_upload_route,
            'upload-youtube-medium': config.youtube_upload_route,
            'temporary-upload-destroy': mediaContainer?.dataset?.temporaryUploadDestroyRoute,
            'destroy-medium': mediaContainer?.dataset?.destroyRoute,
            'set-as-first': mediaContainer?.dataset?.setAsFirstRoute,
            'temporary-upload-set-as-first': mediaContainer?.dataset?.temporaryUploadSetAsFirstRoute,
        };

        return routes[action] || null;
    }
    // function getRouteFromAction(action, target, config) {
    //     const mediaContainer = target.closest('.media-manager-preview-media-container');
    //     const routes = {
    //         'upload-media': config.media_upload_route,
    //         'upload-youtube-medium': config.youtube_upload_route,
    //     };
    //
    //     if (mediaContainer) {
    //         routes['temporary-upload-destroy'] = mediaContainer.dataset.temporaryUploadDestroyRoute || '';
    //         routes['destroy-medium'] = mediaContainer.dataset.destroyRoute || '';
    //         routes['set-as-first'] = mediaContainer.dataset.setAsFirstRoute || '';
    //         routes['temporary-upload-set-as-first'] = mediaContainer.dataset.temporaryUploadSetAsFirstRoute || '';
    //     }
    //
    //     return routes[action] || null;
    // }

    function updatePreview(mediaManager, config) {
        const previewGrid = mediaManager.querySelector('.media-manager-preview-grid');
        if (!previewGrid) return;

        const params = new URLSearchParams({
            model_type: config.model_type,
            model_id: config.model_id,
            image_collection: config.image_collection,
            youtube_collection: config.youtube_collection,
            document_collection: config.document_collection,
            initiator_id: config.id,
            frontend_theme: config.frontend_theme,
            destroy_enabled: config.destroy_enabled,
            set_as_first_enabled: config.set_as_first_enabled,
            show_media_url: config.show_media_url,
            show_order: config.show_order,
            temporary_uploads: config.temporary_upload,
        });

        fetch(`${config.preview_update_route}?${params}`, {
            headers: { 'Accept': 'application/json' }
        })
            .then(response => response.json())
            .then(json => {
                previewGrid.innerHTML = json.html;
            })
            .catch(error => {
                console.error('Error refreshing media manager:', error);
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
        return formData;
    }

    function resetFields(formElement) {
        formElement.querySelectorAll('input').forEach(input => {
            if (input.type !== 'hidden') {
                input.value = '';
            }
        });
    }

    function showSpinner(container) {
        hideStatusMessage(container);
        container.querySelector('[data-spinner-container]')?.classList.add('active');
    }

    function hideSpinner(container) {
        container.querySelector('[data-spinner-container]')?.classList.remove('active');
    }

    function showStatusMessage(container, data) {

        const { type, message, message_extra: messageExtra = null } = data;
        const statusContainer = container.querySelector('[data-status-container]');
        const messageDiv = statusContainer?.querySelector('[data-status-message]');
        if (!statusContainer || !messageDiv) return;

        const base = messageDiv.getAttribute('data-base-classes') || '';
        const typeClasses = type === 'success'
            ? messageDiv.getAttribute('data-success-classes') || ''
            : messageDiv.getAttribute('data-error-classes') || '';

        messageDiv.className = base;
        typeClasses.split(' ').forEach(cls => cls && messageDiv.classList.add(cls));
        messageDiv.textContent = message;
        if (messageExtra) {
            messageDiv.textContent += '\n\n' + messageExtra;
        }
        statusContainer.classList.add('visible');

        clearTimeout(statusMessageTimeout);
        statusMessageTimeout = setTimeout(() => hideStatusMessage(container), 5000);
    }

    function hideStatusMessage(container) {
        container.querySelector('[data-status-container]')?.classList.remove('visible');
    }

    function handleAjaxError(response, data, container) {
        let message = trans('upload_failed');

        switch (response.status) {
            case 419: message = trans('csrf_token_mismatch'); break;
            case 401: message = trans('unauthenticated'); break;
            case 403: message = trans('forbidden'); break;
            case 404: message = trans('not_found'); break;
            case 422:
                if (data.errors) {
                    Object.values(data.errors).flat().forEach(msg => {
                        showStatusMessage(container, { type: 'error', message: msg });
                    });
                    return;
                }
                message = data.message || trans('validation_failed');
                break;
            case 429: message = trans('too_many_requests'); break;
            case 500:
            case 503: message = trans('server_error'); break;
            default:
                message = data.message || message;
        }

        showStatusMessage(container, { type: 'error', message });
    }

    function trans(key) {
        return window.mediaLibraryTranslations?.[key] || key;
    }

});
