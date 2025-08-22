
let statusMessageTimeoutMap = new WeakMap();

export function showStatusMessage(container, data) {

    const { type, message, message_extra: messageExtra = null } = data;
    const statusContainer = container.querySelector('[data-status-container]');
    const messageDiv = statusContainer?.querySelector('[data-status-message]');
    if (!statusContainer || !messageDiv) return;

    const base = messageDiv.getAttribute('data-base-classes') || '';
    const typeClasses = type === 'success'
        ? messageDiv.getAttribute('data-success-classes') || ''
        : messageDiv.getAttribute('data-error-classes') || '';

    messageDiv.className = [base, typeClasses].filter(Boolean).join(' ');
    messageDiv.textContent = [message, messageExtra].filter(Boolean).join('\n\n')

    statusContainer.classList.remove('visible');
    void statusContainer.offsetWidth; // force reflow
    statusContainer.classList.add('visible');

    const timeoutDuration = parseInt(statusContainer.dataset.statusTimeout, 10) || 5000;
    // Track timeout per container
    clearTimeout(statusMessageTimeoutMap.get(container));
    const timeout = setTimeout(() => hideStatusMessage(container), timeoutDuration);
    statusMessageTimeoutMap.set(container, timeout);
}

export function hideStatusMessage(container) {
    container.querySelector('[data-status-container]')?.classList.remove('visible');
}

export function showSpinner(container, customMessage = null) {
    hideStatusMessage(container); // Optional: hides message before showing spinner
    const spinnerContainer = container.querySelector('[data-spinner-container]');
    if (!spinnerContainer) return;

    if (customMessage) {
        // Find the spinner text span or create it if missing
        let textEl = spinnerContainer.querySelector('.mle-spinner-text');
        textEl.textContent = message;
    }
    spinnerContainer.classList.add('active');
}

export function hideSpinner(container) {
    container.querySelector('[data-spinner-container]')?.classList.remove('active');
}

export function handleAjaxError(response, data, container) {
    let message = trans('upload_failed');

    const status = response?.status || 500;
    switch (status) {
        case 419: message = trans('csrf_token_mismatch'); break;
        case 401: message = trans('unauthenticated'); break;
        case 403: message = trans('forbidden'); break;
        case 404: message = trans('not_found'); break;
        case 422:
            if (data.errors) {
                const allErrors = Object.values(data.errors).flat();
                showStatusMessage(container, {
                    type: 'error',
                    message: allErrors[0],
                    message_extra: allErrors.slice(1).join('\n')
                });
                return;
            }
            message = data.message || trans('validation_failed');
            break;
        case 429: message = trans('too_many_requests'); break;
        case 500:
        case 503: message = trans('server_error'); break;
        default:
            message = data.message || message;
    }

    showStatusMessage(container, { type: 'error', message });
}

export function trans(key) {
    return window.mediaLibraryTranslations?.[key] || key;
}
