let statusMessageTimeout = null; // Private to this module

export function readMediaManagerConfig(mediaManagerEl) {
    const configInput = mediaManagerEl.querySelector('.media-manager-config');

    if (!configInput) {
        console.warn('No config input found in media manager:', mediaManagerEl);
        return null;
    }

    let rawConfig = {};
    try {
        rawConfig = JSON.parse(configInput.value);
    } catch (e) {
        console.error(`Invalid JSON config for ${configInput}:`, e);
        return null;
    }

    return {
        initiatorId: rawConfig.id,
        modelType: rawConfig.model_type,
        modelId: rawConfig.model_id,
        imageCollection: rawConfig.image_collection,
        documentCollection: rawConfig.document_collection,
        youtubeCollection: rawConfig.youtube_collection,
        frontendTheme: rawConfig.frontend_theme,
        destroyEnabled: rawConfig.destroy_enabled,
        setAsFirstEnabled: rawConfig.set_as_first_enabled,
        showMediaUrl: rawConfig.show_media_url,
        showOrder: rawConfig.show_order,
        mediaUploadRoute: rawConfig.media_upload_route,
        previewRefreshRoute: rawConfig.preview_refresh_route,
        youtubeUploadRoute: rawConfig.youtube_upload_route,
        csrfToken: rawConfig.csrf_token,
    };
}

export function trans(key) {
    console.log('trans', key, 'window.mlt', window.mediaLibraryTranslations);
    return window.mediaLibraryTranslations?.[key] || key;
}

export function handleAjaxError(response, data, showStatusCallback = null) {
    console.log('handleAjaxError', data);
    console.log(data);

    console.log('response.status', response.status);

    let errorMessage = trans('upload_failed');// TODO rename / transate to XHR error

    switch (response.status) {
        case 419: errorMessage = trans('csrf_token_mismatch'); break;
        case 401: errorMessage = trans('unauthenticated'); break;
        case 403: errorMessage = trans('forbidden'); break;
        case 404:
            console.log('404 detected', trans)

            errorMessage = trans('not_found');
            console.log(errorMessage);
            break;
        case 422:
            if (data.errors && showStatusCallback) {
                for (const field in data.errors) {
                    data.errors[field].forEach(msg => {
                        showStatusCallback({ message: msg, type: 'error' });
                    });
                }
                return;
            }
            errorMessage = data.message || trans('validation_failed');
            break;
        case 429: errorMessage = trans('too_many_requests'); break;
        case 500:
        case 503: errorMessage = trans('server_error'); break;
        default:
            console.log('falling back to default', data.message, errorMessage)
            errorMessage = data.message || errorMessage;
    }

    if (showStatusCallback) {
        console.log('showStatusCallback', errorMessage);
        showStatusCallback({ message: errorMessage, type: 'error' });
    } else {
        console.error(errorMessage);
    }
}

export function refreshMediaManager(mediaManagerOrId) {
    // Get the mediaManager element from id or use directly if already an element
    const mediaManager = typeof mediaManagerOrId === 'string'
        ? document.getElementById(mediaManagerOrId)
        : mediaManagerOrId;

    if (!mediaManager) {
        console.warn('refreshMediaManager: mediaManager element not found', mediaManagerOrId);
        return;
    }

    // Use your existing utility to read config (returns camelCase config)
    const config = readMediaManagerConfig(mediaManager);
    if (!config) {
        console.warn('refreshMediaManager: failed to read config');
        return;
    }

    // Extract the camelCase config object
    // If your readMediaManagerConfig returns the config directly (not wrapped in {config, elements}),
    // adapt accordingly. Assuming here it returns just the config object.
    const {
        initiatorId,
        modelType,
        modelId,
        imageCollection,
        youtubeCollection,
        documentCollection,
        destroyEnabled,
        setAsFirstEnabled,
        showMediaUrl,
        showOrder,
        frontendTheme,
        previewRefreshRoute,
    } = config;

    const previewGrid = mediaManager.querySelector('.media-manager-preview-grid');
    if (!previewGrid) {
        console.warn('refreshMediaManager: preview grid not found');
        return;
    }

    // Build URLSearchParams using snake_case keys expected by backend
    const params = new URLSearchParams({
        model_type: modelType,
        model_id: modelId,
        image_collection: imageCollection,
        youtube_collection: youtubeCollection,
        document_collection: documentCollection,
        initiator_id: initiatorId,
        destroy_enabled: destroyEnabled === true || destroyEnabled === 'true' ? 'true' : 'false',
        set_as_first_enabled: setAsFirstEnabled === true || setAsFirstEnabled === 'true' ? 'true' : 'false',
        show_media_url: showMediaUrl === true || showMediaUrl === 'true' ? 'true' : 'false',
        show_order: showOrder === true || showOrder === 'true' ? 'true' : 'false',
        frontend_theme: frontendTheme,
    });

    fetch(`${previewRefreshRoute}?${params}`, {
        headers: { 'Accept': 'application/json' },
    })
        .then(response => response.json())
        .then(json => {
            console.log('response', json);
            previewGrid.innerHTML = json.html;
        })
        .catch(error => {
            console.error('Error refreshing media manager:', error);
        });
}


export function showStatusMessage(container, status)  {
    const statusContainer = container.querySelector('[data-status-container]');
    const messageDiv = statusContainer?.querySelector('[data-status-message]');

    if (!statusContainer || !messageDiv) return;

    // Get classes from data attributes
    const baseClasses = messageDiv.getAttribute('data-base-classes') || '';
    const successClasses = messageDiv.getAttribute('data-success-classes') || '';
    const errorClasses = messageDiv.getAttribute('data-error-classes') || '';

    // Reset base classes
    messageDiv.className = baseClasses;

    // Add status-specific classes
    const classesToAdd = status.type === 'success' ? successClasses : errorClasses;
    classesToAdd.split(' ').forEach(cls => {
        if (cls.trim()) messageDiv.classList.add(cls.trim());
    });

    // Set the message text
    messageDiv.textContent = status.message;
    statusContainer.classList.add('visible');

    // Cancel previous timeout if any
    if (statusMessageTimeout) {
        clearTimeout(statusMessageTimeout);
    }

    statusMessageTimeout = setTimeout(() => {
        hideStatusMessage(container);
    }, 5000);
}

export function hideStatusMessage(container) {
    const statusWrapper = container.querySelector('[data-status-container]');
    if (statusWrapper) statusWrapper.classList.remove('visible');
}

export function showSpinner(container) {
    hideStatusMessage(container);
    const spinnerContainer = container.querySelector('div[data-spinner-container]');
    if (spinnerContainer) {
        spinnerContainer.classList.add('active');
    }
}

export function hideSpinner(container) {
    const spinnerContainer = container.querySelector('div[data-spinner-container]');
    if (spinnerContainer) {
        spinnerContainer.classList.remove('active');
    }
}
