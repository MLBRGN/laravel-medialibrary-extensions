// noinspection JSUnresolvedReference
import {fireEvent, releaseFocus} from "@/js/plain/helpers";
import {getCarouselController} from "@/js/plain/media-carousel";

document.addEventListener('DOMContentLoaded', () => {



    //*
    function setupModal(modal) {
        console.log('setupModal', modal);

        // Play video on modal open
        modal.addEventListener('modalOpened', () => {

        });

        document.addEventListener('onImageUpdated', (e) => {
            console.log('✅ Global listener got event:', e.detail);
        });

        modal.addEventListener('onImageUpdated', (e) => {
            console.log('onImageUpdated', e);
        })
        modal.addEventListener('onImageUpdated', (e) => {
            console.log('onImageUpdated', e);
            closeModal(modal);
        })

    }

    //*/

    function closeModal(modal) {
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';

        releaseFocus(modal);
        fireEvent('modalClosed', modal);
    }

    const modals = document.querySelectorAll('[data-image-editor-modal]');
    modals.forEach(setupModal);
    console.log('modals', modals);

});
