
let statusMessageTimeoutMap = new WeakMap();
const spinnerDelayTimeoutMap = new WeakMap();

// Called when an XHR or fetch request starts.
// Will only show spinner if request takes longer than delay
export function xhrRequestStart(statusAreaContainer, customMessage = null) {
    if (!statusAreaContainer) {
        console.error("xhrRequestStart: No statusAreaContainer provided");
        return;
    }
    const delay = 300; // 1 second delay before showing spinner

    clearTimeout(spinnerDelayTimeoutMap.get(statusAreaContainer));

    const timeout = setTimeout(() => {
        showSpinner(statusAreaContainer, customMessage);
    }, delay);

    spinnerDelayTimeoutMap.set(statusAreaContainer, timeout);
}

 // Called when an XHR or fetch request finishes (success or error).
// Hides spinner and clears any delayed show timeout.
export function xhrRequestEnd(statusAreaContainer) {
    if (!statusAreaContainer) {
        console.error("xhrRequestEnd: No statusAreaContainer provided");
        return;
    }

    clearTimeout(spinnerDelayTimeoutMap.get(statusAreaContainer));
    hideSpinner(statusAreaContainer);
}

export function showStatusMessage(statusAreaContainer, data) {

    if (!statusAreaContainer) {
        console.error('no statusAreaContainer provided');
        return;
    }
    const { type, message, message_extra: messageExtra = null } = data;
    const statusContainer = statusAreaContainer.querySelector('[data-mle-status-container]');
    const messageDiv = statusAreaContainer?.querySelector('[data-mle-status-message]');
    if (!statusContainer || !messageDiv) {
        console.error('could not find status container')
        return;
    }

    const base = messageDiv.getAttribute('data-mle-base-classes') || '';
    const typeClasses = type === 'success'
        ? messageDiv.getAttribute('data-mle-success-classes') || ''
        : messageDiv.getAttribute('data-mle-error-classes') || '';

    messageDiv.className = [base, typeClasses].filter(Boolean).join(' ');
    messageDiv.textContent = [message, messageExtra].filter(Boolean).join('\n\n')

    statusContainer.classList.remove('visible');
    void statusContainer.offsetWidth; // force reflow
    statusContainer.classList.add('visible');

    const timeoutDuration = parseInt(statusContainer.dataset.mleStatusTimeout, 10) || 5000;
    // Track timeout per container
    clearTimeout(statusMessageTimeoutMap.get(statusAreaContainer));
    const timeout = setTimeout(() => hideStatusMessage(statusAreaContainer), timeoutDuration);
    statusMessageTimeoutMap.set(statusAreaContainer, timeout);
}

export function hideStatusMessage(statusAreaContainer) {
    if (!statusAreaContainer) {
        console.error('no statusAreaContainer provided');
        return;
    }
    const statusContainer = statusAreaContainer.querySelector('[data-mle-status-container]');
    if (!statusAreaContainer) {
        console.error('could not find status container')
        return;
    }
    statusContainer?.classList.remove('visible');
}

export function showSpinner(statusAreaContainer, customMessage = null) {
    if (!statusAreaContainer) {
        console.error('no statusAreaContainer provided');
        return;
    }
    hideStatusMessage(statusAreaContainer); // Hides the message before showing spinner
    const spinnerContainer = statusAreaContainer.querySelector('[data-mle-spinner-container]');
    if (!spinnerContainer) {
        console.error('could not find spinner container')
        return;
    }

    if (customMessage) {
        // Find the spinner text span or create it if missing
        let textEl = spinnerContainer.querySelector('[data-mle-spinner-text]');
        textEl.textContent = message;
    }
    spinnerContainer.classList.add('active');
}

export function hideSpinner(statusAreaContainer) {
    if (!statusAreaContainer) {
        console.error('no statusAreaContainer provided');
        return;
    }
    const spinnerContainer = statusAreaContainer.querySelector('[data-mle-spinner-container]');
    if (!spinnerContainer) {
        console.error('could not find spinner container')
        return;
    }
    spinnerContainer?.classList.remove('active');
}

export function handleAjaxError(response, data, statusAreaContainer) {
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
                showStatusMessage(statusAreaContainer, {
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

    showStatusMessage(statusAreaContainer, { type: 'error', message });
}

export function trans(key) {
    return window.mediaLibraryTranslations?.[key] || key;
}
