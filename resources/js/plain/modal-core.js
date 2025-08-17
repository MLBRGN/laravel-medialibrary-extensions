// modal-core.js
import { fireEvent, trapFocus, releaseFocus } from '@/js/plain/helpers';

export function closeModal(modal) {
    modal.classList.remove('active');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';

    releaseFocus(modal);
    fireEvent('modalClosed', modal);
}

export function openModal(modalId) {
    console.log('openModal: ', modalId);
    const modal = document.querySelector(modalId);
    if (!modal) {
        console.log('could not find modal' + modalId);
        return;
    }

    modal.classList.add('active');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    trapFocus(modal);
    fireEvent('modalOpened', modal);
}

export function setupModalBase(modal, onClose = () => {}, onOpen = () => {}) {
    // console.log('setupModalBase', modal);
    modal.addEventListener('modalOpened', onOpen);
    modal.addEventListener('modalClosed', onClose);
}

/**
 * Initializes default modal click + keyboard behavior.
 */
export function initModalEvents() {
    document.addEventListener('click', (e) => {
        e.stopPropagation();
        const target = e.target;
        console.log('target', target);
        const trigger = target.closest('[data-modal-trigger]');
        if (trigger) {
            e.preventDefault();
            const modalId = trigger.getAttribute('data-modal-trigger');
            openModal(modalId);
            return;
        }

        const closeBtn = target.closest('[data-modal-close]');
        // console.log('closeBtn', closeBtn);
        if (closeBtn) {
            const modal = closeBtn.closest('[data-modal]');
            if (modal) closeModal(modal);
            return;
        } else {
            console.log('no close button')
        }

        const modal = target.closest('[data-modal]');
        if (modal && target === modal) closeModal(modal);
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[data-modal].active').forEach(closeModal);
        }
    });
}
