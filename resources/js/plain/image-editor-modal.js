// noinspection JSUnresolvedReference
import {fireEvent, releaseFocus} from "@/js/plain/helpers";

document.addEventListener('DOMContentLoaded', () => {

    const modals = document.querySelectorAll('.image-editor-modal');

    modals.forEach((modal) => {
        modal.addEventListener('onImageUpdated', (e) => {
            console.log('onImageUpdated', e);
            closeModal(modal);
        })
    });

    function closeModal(modal) {
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';

        releaseFocus(modal);
        fireEvent('modalClosed', modal);
    }
});
