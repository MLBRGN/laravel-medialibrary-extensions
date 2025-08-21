export function fireEvent(eventName, element, detail) {
    const event = new CustomEvent(eventName, {
        bubbles: true,
        detail: detail
    });
    element.dispatchEvent(event);
}

export function trapFocus(modal) {
    const focusableSelectors = 'a[href], button, textarea, input, select, [tabindex]:not([tabindex="-1"])';

    const getFocusableElements = () =>
        Array.from(modal.querySelectorAll(focusableSelectors))
            .filter(el => !el.hasAttribute('disabled') && el.offsetParent !== null);

    const handleKeyDown = (e) => {
        const focusableEls = getFocusableElements();
        const first = focusableEls[0];
        const last = focusableEls[focusableEls.length - 1];

        if (e.key === 'Tab') {
            if (focusableEls.length === 0) return;

            if (e.shiftKey) {
                if (document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                }
            } else {
                if (document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        }
    };

    modal.addEventListener('keydown', handleKeyDown);
    modal._trapFocusHandler = handleKeyDown;

    // Optional: focus first element
    const focusableEls = getFocusableElements();
    if (focusableEls.length) {
        focusableEls[0].focus();
    }
}

export function releaseFocus(modal) {
    if (modal._trapFocusHandler) {
        modal.removeEventListener('keydown', modal._trapFocusHandler);
        delete modal._trapFocusHandler;
    }
}
