// noinspection JSUnresolvedReference
document.addEventListener('DOMContentLoaded', () => {

    const modals = document.querySelectorAll('.image-editor-modal');

    modals.forEach((modal) => {
        modal.addEventListener('onImageUpdated', (e) => {
            console.log('onImageUpdated', e);
            const modalInstance = bootstrap.Modal.getInstance(modal);
            modalInstance.hide();
        })
    });

});
