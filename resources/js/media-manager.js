import {
    handleAjaxError,
    refreshMediaManager,
    readMediaManagerConfig,
    showStatusMessage, hideSpinner, showSpinner
} from './general-helpers';

document.addEventListener('DOMContentLoaded', function () {
    const mediaManagers = document.querySelectorAll('[data-media-manager]');

      mediaManagers.forEach(mediaManager => {
        const formContainer = mediaManager.querySelector('.media-manager-form');
        const config = readMediaManagerConfig(mediaManager);

        if (!config) {
          console.log('could not get config')
          return
        }

        console.log(config);

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
            'upload-media': config.mediaUploadRoute,
            'upload-youtube-medium': config.youtubeUploadRoute,
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
                    'X-CSRF-TOKEN': config.csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            })
                .then(async response => {
                    const data = await response.json();

                    if (!response.ok) {
                        // TODO look at this code
                        console.log('callback image editor handleAjaxError', data);
                        handleAjaxError(response, data, function() {
                            console.log('callback media manager handleAjaxError', data);
                            showStatusMessage(formContainer, data);
                        });
                        return;
                    }

                    showStatusMessage(formContainer, data);

                    const flash = document.getElementById(formElement.dataset.target + '-flash');
                    if (flash && data.message) {
                        flash.innerHTML = `<div class="alert alert-${data.type}">${data.message}</div>`;
                    }

                    refreshMediaManager(mediaManager);
                    resetFields(formElement);
                })
                .catch(error => {
                    console.error('Error during upload:', error);
                })
                .finally(() => {
                    hideSpinner(formContainer);
                });
            });

        });

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
