// modal-core.js
import { fireEvent, trapFocus, releaseFocus } from '@/js/plain/helpers';

export function closeModal(modal) {
    modal.classList.remove('active');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    releaseFocus(modal);
    fireEvent('modalClosed', modal);
}

export function openModal(modalId, slideTo = 0, controller = null) {
    const modal = document.querySelector(modalId);
    if (!modal) return;

    modal.classList.add('active');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    trapFocus(modal);

    if (controller) {
        controller.goToSlide(parseInt(slideTo), true);
    }

    fireEvent('modalOpened', modal);
}

export function setupModalBase(modal, onClose = () => {}, onOpen = () => {}) {
    modal.addEventListener('modalOpened', onOpen);
    modal.addEventListener('modalClosed', onClose);

    // Optional image update listener
    document.addEventListener('onImageUpdated', (e) => {
        console.log('✅ Global listener got event:', e.detail);
    });

    modal.addEventListener('onImageUpdated', (e) => {
        console.log('onImageUpdated', e);
        closeModal(modal);
    });
}
