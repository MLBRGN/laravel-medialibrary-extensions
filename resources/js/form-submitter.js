document.addEventListener('DOMContentLoaded', function () {
    let statusMessageTimeout = null;

    const mediaManagers = document.querySelectorAll('[data-media-manager]');

    mediaManagers.forEach(mediaManager => {

        const mediaManagerId = mediaManager.id;
        // routes
        const mediaUploadRoute = mediaManager.getAttribute('data-media-upload-route');
        const youtubeUploadRoute = mediaManager.getAttribute('data-media-youtube-upload-route');
        const previewRefreshRoute = mediaManager.getAttribute('data-preview-refresh-route');
        // destroy and set-as-first routes are stored as a data attribute on "submit" button of the respective form

        // formContainer
        const formContainer = mediaManager.querySelector('.media-manager-form');
        const csrfToken = mediaManager.getAttribute('data-csrf-token');
        const theme = mediaManager.getAttribute('data-theme');

        // needed to refresh previews
        const modelType = mediaManager.getAttribute('data-model-type');
        const modelId = mediaManager.getAttribute('data-model-id');
        const collection = mediaManager.getAttribute('data-collection');
        const youtubeCollection = mediaManager.getAttribute('data-youtube-collection');
        const documentCollection = mediaManager.getAttribute('data-document-collection');

        console.log('adding click listener for:', mediaManagerId);
        mediaManager.addEventListener('click', function (e) {

            const target = e.target.closest('[data-action]');

            console.log('click', target);
            if (!target) {// do not handle clicks on elements without the "data-action" attribute
                return;
            }

            console.log('target', target);
            const formElement = target.closest('[data-xhr-form]');
            console.log('formElement', formElement);

            e.preventDefault();
            const action = target.getAttribute('data-action');
            console.log(action);

            showSpinner(formContainer);
            const formData = getFormData(formElement);

            const mediaManagerPreviewMediaContainer = target.closest('.media-manager-preview-media-container');
            console.log('mediaManagerPreviewMediaContainer', mediaManagerPreviewMediaContainer);
            const routes = {
                'upload-media': mediaUploadRoute,
                'destroy-medium': mediaManagerPreviewMediaContainer?.getAttribute('data-destroy-route') || '',
                'set-as-first': mediaManagerPreviewMediaContainer?.getAttribute('data-set-as-first-route') || '',
                'upload-youtube-medium': youtubeUploadRoute,
            };

            const route = routes[action] || '';

            if (route) {
                fetch(route, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                    .then(async response => {
                        const data = await response.json();

                        if (!response.ok) {
                            handleAjaxError(response, data, formContainer, theme);
                            return;
                        }

                        showStatusMessage(formContainer, data, theme);

                        const flash = document.getElementById(formElement.dataset.target + '-flash');
                        if (flash && data.message) {
                            flash.innerHTML = `<div class="alert alert-${data.type}">${data.message}</div>`;
                        }

                        refreshMediaManager();
                        resetFields(formElement);
                    })
                    .catch(error => {
                        console.error('Error during upload:', error);
                        // Optionally show fallback error
                    })
                    .finally(() => {
                        hideSpinner(formContainer);
                    });
            } else {
                showStatusMessage(formContainer, {
                    type: 'error',
                    message: 'invalid action'
                }, theme);
            }
        });

        function refreshMediaManager() {
            const previewGrid = mediaManager.querySelector('.media-manager-preview-grid');
            if (!previewGrid) return;

            if (!modelType || !modelId || !collection || !youtubeCollection || !documentCollection || !mediaManagerId) {
                debugger;
                throw new Error('missing required params')
            }
            const params = new URLSearchParams({
                model_type: modelType,
                model_id: modelId,
                collection: collection,
                youtube_collection: youtubeCollection,
                document_collection: documentCollection,
                target_id: mediaManagerId,
                // show_order: showOrder,
                // destroyEnabled: true,
                // setAsFirstEnabled: true,
            });
            fetch(`${previewRefreshRoute}?${params}`, {
                headers: {
                    'Accept': 'text/html'
                }
            }).then(response => response.json())
                .then(json => {
                    // console.log('html', html)
                    previewGrid.innerHTML = json.html;
                })
                .catch(error => {
                    console.error('Error refreshing media manager:', error);
                })
                .finally(() => {
                    console.log('refresh finally preview refresh')
                });
        }
    });

    function showSpinner(container) {
        hideStatusMessage(container);
        const spinnerContainer = container.querySelector('div[data-spinner-container]');
        if (spinnerContainer) {
            spinnerContainer.classList.add('active');
        }
    }

    function hideSpinner(container) {
        const spinnerContainer = container.querySelector('div[data-spinner-container]');
        if (spinnerContainer) {
            spinnerContainer.classList.remove('active');
        }
    }

    const showStatusMessage = (container, status, theme) => {
        const statusContainer = container.querySelector('[data-status-container]');
        const messageDiv = statusContainer?.querySelector('[data-status-message]');

        if (!statusContainer || !messageDiv) return;

        // Get classes from data attributes
        const baseClasses = messageDiv.getAttribute('data-base-classes') || '';
        const successClasses = messageDiv.getAttribute('data-success-classes') || '';
        const errorClasses = messageDiv.getAttribute('data-error-classes') || '';

        // Reset base classes
        messageDiv.className = baseClasses;

        // Add status-specific classes
        const classesToAdd = status.type === 'success' ? successClasses : errorClasses;
        classesToAdd.split(' ').forEach(cls => {
            if (cls.trim()) messageDiv.classList.add(cls.trim());
        });

        // Set the message text
        messageDiv.textContent = status.message;

        statusContainer.classList.add('visible');

        // Cancel previous timeout if any
        if (statusMessageTimeout) {
            clearTimeout(statusMessageTimeout);
        }

        statusMessageTimeout = setTimeout(() => {
            hideStatusMessage(container);
        }, 5000);
    };

    const hideStatusMessage = (container) => {
        const statusWrapper = container.querySelector('[data-status-container]');
        if (statusWrapper) statusWrapper.classList.remove('visible');
    }

    function handleAjaxError(response, data, formContainer, theme) {
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
                            showStatusMessage(formContainer, { message: msg, type: 'error' }, theme);
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

        showStatusMessage(formContainer, { message: errorMessage, type: 'error' }, theme);
    }

    function trans(key) {
        return window.mediaLibraryTranslations?.[key] || key;
    }

    function getFormData (formElement) {
        const formData = new FormData();

        formElement.querySelectorAll('input').forEach(input => {
            if (input.type === 'file') {
                [...input.files].forEach(file => {
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

});
