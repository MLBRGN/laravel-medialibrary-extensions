document.addEventListener('DOMContentLoaded', function () {
    // TODO better name for formDataContainer

    const mediaManagers = document.querySelectorAll('[data-media-manager]');

    console.log(mediaManagers);
    mediaManagers.forEach(mediaManager => {

        const mediaManagerId = mediaManager.id;
        // routes
        const mediaUploadRoute = mediaManager.getAttribute('data-media-upload-route');
        const previewRefreshRoute = mediaManager.getAttribute('data-preview-refresh-route');
        // destroy and set-as-first routes are stored as a data attribute on "submit" button of the respective form

        // formContainer
        const formContainer = mediaManager.querySelector('.media-manager-upload-form');
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
            const formElement = target.closest('[data-xhr-form]');

            if (!target) {// do not handle clicks om elements without data-action attribute
                return;
            } else {
                e.stopPropagation();
            }

            e.preventDefault();
            const action = target.getAttribute('data-action');


            showSpinner(formElement);

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
            const mediaManagerPreviewMediaContainer = target.closest('.media-manager-preview-media-container');
            console.log(mediaManagerPreviewMediaContainer);
            const routes = {
                'upload-media': mediaUploadRoute,
                'destroy-medium': mediaManagerPreviewMediaContainer?.getAttribute('data-destroy-route') || '',
                'set-as-first': mediaManagerPreviewMediaContainer?.getAttribute('data-set-as-first-route') || '',
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
                            console.log('response not ok')
                            const errorMessage = data?.message || 'Upload failed';

                            showStatusMessage(formContainer, {message: errorMessage, type: 'error'}, theme);
                        } else {

                            showStatusMessage(formContainer, data, theme);

                            const flash = document.getElementById(formElement.dataset.target + '-flash');
                            if (flash && data.message) {
                                flash.innerHTML = `<div class="alert alert-${data.type}">${data.message}</div>`;
                            }

                            refreshMediaManager();
                        }
                    })
                    .catch(error => {
                        console.error('Error during upload:', error);
                        // Optionally show fallback error
                    })
                    .finally(() => {
                        hideSpinner(formElement);
                    });
            } else {
                showStatusMessage(formContainer, {
                    type: 'error',
                    message: 'invalid action'
                }, theme);
            }
        });

        // TODO
        function refreshMediaManager() {
            console.log('refresh');
            const previewGrid = mediaManager.querySelector('.media-manager-preview-grid');
            if (!previewGrid) return;

            console.log('youtube_collection', youtubeCollection)
            console.log('document_collection', documentCollection)
            const params = new URLSearchParams({
                model_type: modelType,
                model_id: modelId,
                collection: collection,
                youtube_collection: youtubeCollection,
                document_collection: documentCollection,
                target_id: mediaManagerId
            });
            console.log('previewRefreshRoute', previewRefreshRoute)
            fetch(`${previewRefreshRoute}?${params}`, {
                headers: {
                    'Accept': 'text/html'
                }
            }).then(response => response.text())
                .then(html => {
                    // console.log('html', html)
                    previewGrid.innerHTML = html;
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

        console.log('showStatusMessage', status.message);

        // first remove the previous status message (if any)
        const oldStatusMessage = container.querySelector('div[data-status-message]');
        oldStatusMessage?.remove();

        const messageDiv = document.createElement('div');
        messageDiv.setAttribute('data-status-message', '');
        messageDiv.classList.add('mle-status-message');
        messageDiv.classList.add(`mle-status-message-${status.type}`);

        let extraClasses = '';
        if (theme === 'bootstrap-5') {
            extraClasses += 'alert w-100 ';
            extraClasses += status.type === 'success' ? 'alert-success' : 'alert-danger';
        }
        if (extraClasses) {
            extraClasses.split(' ').forEach(cls => messageDiv.classList.add(cls));
        }
        messageDiv.textContent = status.message;

        container.prepend(messageDiv);
    };

});
