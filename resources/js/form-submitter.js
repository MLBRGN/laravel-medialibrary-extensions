document.addEventListener('DOMContentLoaded', function () {
    let statusMessageTimeout = null;

    const mediaManagers = document.querySelectorAll('[data-media-manager]');

      mediaManagers.forEach(mediaManager => {
        const mediaManagerId = mediaManager.id;
        const formContainer = mediaManager.querySelector('.media-manager-form');
        const configInput = mediaManager.querySelector('.media-manager-config');
        if (!configInput) return;

        let config = {};
        try {
          config = JSON.parse(configInput.value);
        } catch (e) {
          console.error(`Invalid JSON config for ${mediaManagerId}:`, e);
          return;
        }

        const {
          id: initiatorId,
          model_type: modelType,
          model_id: modelId,
          image_collection: imageCollection,
          document_collection: documentCollection,
          youtube_collection: youtubeCollection,
          frontend_theme: frontendTheme,
          destroy_enabled: destroyEnabled,
          set_as_first_enabled: setAsFirstEnabled,
          show_media_url: showMediaUrl,
          show_order: showOrder,
          media_upload_route: mediaUploadRoute,
          preview_refresh_route: previewRefreshRoute,
          youtube_upload_route: youtubeUploadRoute,
          csrf_token: csrfToken,
        } = config;

        mediaManager.addEventListener('click', function (e) {
            const target = e.target.closest('[data-action]');
            if (!target) {// do not handle clicks on elements without the "data-action" attribute
                return;
            }
            e.preventDefault();
            const action = target.getAttribute('data-action');
            const formElement = target.closest('[data-xhr-form]');

            showSpinner(formContainer);
            const formData = getFormData(formElement);

          // Setup route mapping
          const actionRoutes = {
            'upload-media': mediaUploadRoute,
            'upload-youtube-medium': youtubeUploadRoute,
          };

          const mediaContainer = target.closest('.media-manager-preview-media-container');
          if (mediaContainer) {
            actionRoutes['destroy-medium'] = mediaContainer.dataset.destroyRoute || '';
            actionRoutes['set-as-first'] = mediaContainer.dataset.setAsFirstRoute || '';
          }

            const route = actionRoutes[action];
            if (!route) {
                showStatusMessage(formContainer, {
                    type: 'error',
                    message: 'Invalid action',
                });
                hideSpinner(formContainer);
                return;
            }

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
                        handleAjaxError(response, data, formContainer);
                        return;
                    }

                    showStatusMessage(formContainer, data);

                    const flash = document.getElementById(formElement.dataset.target + '-flash');
                    if (flash && data.message) {
                        flash.innerHTML = `<div class="alert alert-${data.type}">${data.message}</div>`;
                    }

                    refreshMediaManager();
                    resetFields(formElement);
                })
                .catch(error => {
                    console.error('Error during upload:', error);
                })
                .finally(() => {
                    hideSpinner(formContainer);
                });
            });

            function refreshMediaManager() {
                const previewGrid = mediaManager.querySelector('.media-manager-preview-grid');
                if (!previewGrid) return;

                const params = {
                    model_type: modelType,
                    model_id: modelId,
                    image_collection: imageCollection,
                    youtube_collection: youtubeCollection,
                    document_collection: documentCollection,
                    initiator_id: initiatorId,
                    destroy_enabled: destroyEnabled === 'true',
                    set_as_first_enabled: setAsFirstEnabled === 'true',
                    show_media_url: showMediaUrl === 'true',
                    show_order: showOrder === 'true',
                    frontend_theme: frontendTheme,
                }

                const searchParams = new URLSearchParams(params);

                fetch(`${previewRefreshRoute}?${searchParams}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                }).then(response => response.json())
                    .then(json => {
                        previewGrid.innerHTML = json.html;
                    })
                    .catch(error => {
                        console.error('Error refreshing media manager:', error);
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

    const showStatusMessage = (container, status) => {
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

    function handleAjaxError(response, data, formContainer) {
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
                            showStatusMessage(formContainer, { message: msg, type: 'error' });
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

        showStatusMessage(formContainer, { message: errorMessage, type: 'error' });
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
