document.addEventListener('DOMContentLoaded', function () {
    // TODO better name for formDataContainer
    const formDataContainers = document.querySelectorAll('[data-ajax-upload-form]');

    formDataContainers.forEach(formDataContainer => {

        const formActionRoute = formDataContainer.getAttribute('data-form-action-route');
        const previewRefreshRoute = formDataContainer.getAttribute('data-preview-refresh-route');
        const formButton = formDataContainer.querySelector('button');
        const formContainer = formDataContainer.parentNode;
        const mediaManagerId = formDataContainer.getAttribute('data-media-manager-id');
        const csrfToken = formDataContainer.getAttribute('data-csrf-token');
        const theme = formDataContainer.getAttribute('data-theme');

        // needed to refresh previews
        const modelType = formDataContainer.getAttribute('data-model-type');
        const modelId = formDataContainer.getAttribute('data-model-id');
        const collection = formDataContainer.getAttribute('data-collection');
        // const collections = formDataContainer.getAttribute('data-collections');
        const youtubeCollection = formDataContainer.getAttribute('data-youtube-collection');
        const documentCollection = formDataContainer.getAttribute('data-document-collection');
        console.log(modelType, modelId, collection, youtubeCollection, documentCollection);

        formButton.addEventListener('click', function (e) {
            e.preventDefault();

            showSpinner(formDataContainer);

            const formData = new FormData();

            formDataContainer.querySelectorAll('input').forEach(input => {
                if (input.type === 'file') {
                    [...input.files].forEach(file => {
                        formData.append(input.name, file);
                    });
                } else {
                    formData.append(input.name, input.value);
                }
            });

            fetch(formActionRoute, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    console.log('data', data)

                    extraClasses = '';

                    if (theme === 'bootstrap-5') {
                        extraClasses += 'alert w-100 ';
                        extraClasses += data.type === 'success' ? 'alert-success' : 'alert-danger';
                    }

                    showStatusMessage(formContainer, data, extraClasses);

                    console.log(data.message);
                    const flash = document.getElementById(formDataContainer.dataset.target + '-flash');
                    if (flash && data.message) {
                        flash.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    }

                    refreshMediaManager();

                })
                .catch(error => {
                    console.log('catch', error);
                    console.log(error.response);
                    // if (error.response.status === 422) {
                    //     console.log('Validation errors:', error.response.data.errors);
                    // } else {
                    //     console.error('Upload failed:', error.response);
                    // }
                    // spinner?.classList.add('d-none');
                    // alert('Upload failed. See console for details.');
                    // console.error(error);
                    refreshMediaManager('media_target_id', 'App\\Models\\Workplace', 1, 'images', 'youtube', 'documents');

                }).finally(() => {
                    console.log('finally');
                    hideSpinner(formDataContainer);
                });

            /*
            .then(response => {
    if (!response.ok) {
        return response.json().then(errorData => Promise.reject(errorData));
    }
    return response.json();
})
.then(data => {
    console.log('Success:', data);
})
.catch(error => {
    console.error('Validation errors:', error.errors);
});
             */




        });

        // TODO
        function refreshMediaManager() {
            console.log('refresh');
            const mediaManager = document.getElementById(mediaManagerId);
            const previewGrid = mediaManager.querySelector('.media-manager-preview-grid');
            if (!previewGrid) return;

            const params = new URLSearchParams({
                model_type: modelType,
                model_id: modelId,
                collection: collection,
                youtube_collection: youtubeCollection,
                document_collection: documentCollection,
                target_id: mediaManagerId
            });

            fetch(`${previewRefreshRoute}?${params}`, {
                headers: {
                    'Accept': 'text/html'
                }
            }).then(response => response.text())
                .then(html => {
                    console.log('html', html)
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

    const showStatusMessage = (container, status, extraClasses = '') => {

        console.log('showStatusMessage');

        // first remove previous status message (if any)
        const oldStatusMessage = container.querySelector('div[data-status-message]');
        oldStatusMessage?.remove();

        const messageDiv = document.createElement('div');
        messageDiv.setAttribute('data-status-message', '');
        messageDiv.classList.add('mle-status-message');
        messageDiv.classList.add(`mle-status-message-${status.type}`);
        if (extraClasses) {
            extraClasses.split(' ').forEach(cls => messageDiv.classList.add(cls));
        }
        messageDiv.textContent = status.message;

        container.prepend(messageDiv);
    };

});
