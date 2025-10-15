document.addEventListener('DOMContentLoaded', () => {
    const temporaryUploadToggles = document.querySelectorAll('[data-temporary-upload-toggle]');
    temporaryUploadToggles.forEach(temporaryUploadToggle => {
        temporaryUploadToggle.addEventListener('click', (event) => {
            const enabled = temporaryUploadToggle.checked;
            const mediaManagerId = temporaryUploadToggle.getAttribute('data-for-media-manager');
            const mediaManager = document.querySelector('#'+mediaManagerId);
            const mediaManagerConfigInput = mediaManager.querySelector('.media-manager-config');
            const mediaManagerConfigJson = mediaManagerConfigInput.value;

            try {
                const config = JSON.parse(mediaManagerConfigJson);
                config.temporaryUpload = enabled;

                mediaManagerConfigInput.value = JSON.stringify(config);
                // console.info('changed temporary upload configuration to', enabled)
            } catch (e) {
                console.warn('Something went wrong while reading or updating JSON', e);
            }
        });
    });
});
