export function fireEvent(eventName, element) {
    const event = new CustomEvent(eventName, {
        bubbles: true,
        detail: {element}
    });
    element.dispatchEvent(event);
}

export function trapFocus(modal) {
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
}

export function releaseFocus(modal) {
    if (modal._trapFocusHandler) {
        modal.removeEventListener('keydown', modal._trapFocusHandler);
        delete modal._trapFocusHandler;
    }
}
