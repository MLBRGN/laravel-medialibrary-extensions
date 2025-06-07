document.addEventListener('DOMContentLoaded', () => {
    const trapFocus = (modal) => {
        const focusableSelectors = 'a[href], button, textarea, input, select, [tabindex]:not([tabindex="-1"])';
        const focusableEls = modal.querySelectorAll(focusableSelectors);
        if (focusableEls.length === 0) return;

        const first = focusableEls[0];
        const last = focusableEls[focusableEls.length - 1];

        const handleKeyDown = (e) => {
            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    // Shift + Tab
                    if (document.activeElement === first) {
                        e.preventDefault();
                        last.focus();
                    }
                } else {
                    // Tab
                    if (document.activeElement === last) {
                        e.preventDefault();
                        first.focus();
                    }
                }
            }
        };

        modal.addEventListener('keydown', handleKeyDown);

        // Save handler reference on modal for removal later
        modal._trapFocusHandler = handleKeyDown;

        // Focus first element
        first.focus();
    };

    const releaseFocus = (modal) => {
        if (modal._trapFocusHandler) {
            modal.removeEventListener('keydown', modal._trapFocusHandler);
            delete modal._trapFocusHandler;
        }
    };

    const openModal = (modalId) => {
        console.log('openModal', modalId);
        const modal = document.getElementById(modalId);
        if (!modal) {
            console.log('modal not found', modalId);
            return;
        }
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        trapFocus(modal);
    };

    const closeModal = (modal) => {
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';

        releaseFocus(modal);
    };

    // Attach to all close buttons
    document.querySelectorAll('[data-modal-close]').forEach(element => {
        element.addEventListener('click', () => {
            const modal = element.closest('.media-modal');
            if (modal) closeModal(modal);
        });
    });

    // Close on backdrop click
    document.querySelectorAll('.media-modal').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal(modal);
        });
    });

    // Open modal trigger
    document.querySelectorAll('[data-modal-trigger]').forEach(element => {
        element.addEventListener('click', () => {
            const target = element.getAttribute('data-modal-trigger');
            openModal(target);
        });
    });

    // ESC key to close any active modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.media-modal.active').forEach(modal => {
                closeModal(modal);
            });
        }
    });
});
