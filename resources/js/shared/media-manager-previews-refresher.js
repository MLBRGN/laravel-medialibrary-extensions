import { handleAjaxError } from './xhrStatus';
import { disableFormElements, enableFormElements } from './form';

/**
 * Refreshes the media preview grid after upload, deletion, or restore.
 *
 * @param {HTMLElement} mediaManager
 * @param {Object} config
 * @param {Object} detail
 */
export async function updatePreviews(mediaManager, config, detail = {}) {
    console.log('media-manager-previews-refresher.js - updatePreviews called')

    const previewGrid = mediaManager.querySelector('[data-mle-media-preview-grid]');
    const forms = mediaManager.querySelectorAll('[data-mle-form], [data-mle-xhr-form]');
    if (!previewGrid) return;

    // console.log('update previews media manager', config, detail);
    const params = new URLSearchParams({
        model_type: config.modelType,
        model_id: config.modelId,
        single_media_id: detail.singleMediaId ?? null,
        temporary_upload_mode: config.temporaryUploadMode,
        base_id: config.id,
        collections: JSON.stringify(config.collections),
        // options: JSON.stringify(config.options),
        disabled: config.disabled,
        readonly: config.readonly,
        selectable: config.selectable,
        multiple: config.multiple,
        // Do not send instance_id; it is derived server-side from base_id
        client_token: config.clientToken,
        data_source: config.dataSource,
        theme: config.theme,
        include_debug: 'true',
    });

    // console.log('instance_id ', config.instanceId)

    // Cache-busting param
    params.append('_', Date.now());

    try {
        const response = await fetch(`${config.routes.mediaManagerPreviewUpdate}?${params}`, {
            headers: { 'Accept': 'application/json' },
            cache: 'no-store', // prevents using or storing cache
        });

        let data = {};

        try {
            data = await response.json();
        } catch (e) {
            console.warn('Response is not JSON');

            try {
                data = {
                    message: await response.clone().text()
                };
            } catch {
                data = {
                    message: 'Unable to read response body'
                };
            }
        }

        if (!response.ok) {
            handleAjaxError(response, data);
            return;
        }

        if (!data.html) return;

        previewGrid.innerHTML = data.html;

        // Update counts in the upload section header
        const formContainer = mediaManager.querySelector('.mle-media-manager-form');
        const countsEl = formContainer?.querySelector('.mle-media-manager-media-counts');
        const maxFromConfig = (typeof config.maxMediaCount !== 'undefined') ? Number(config.maxMediaCount) : undefined;
        const maxFromResponse = (typeof data.maxMediaCount !== 'undefined') ? Number(data.maxMediaCount) : undefined;
        const hasExplicitPerInstanceMax = (typeof maxFromConfig !== 'undefined' && !Number.isNaN(maxFromConfig));
        // Prefer per-instance max from component config when provided; fall back to server response, then default.
        const maxCount = hasExplicitPerInstanceMax
            ? maxFromConfig
            : (maxFromResponse ?? (config.multiple ? 10 : 1));
        if (countsEl && (typeof data.mediaCount !== 'undefined')) {
            const tpl = trans('media_counts') || ':current / :total';
            const localized = tpl.replace(':current', String(data.mediaCount))
                                 .replace(':total', String(maxCount));
            countsEl.textContent = localized;
        }

        // Update debug panel if present
        if (data.debugHtml) {
            const debugPanel = mediaManager.querySelector('[data-mle-debug]');
            if (debugPanel) {
                // We want to keep the current visibility state (hidden or not)
                const isHidden = debugPanel.classList.contains('hidden') || debugPanel.classList.contains('mle-hidden');

                // Replace the outer wrapper of the debug content
                const debugWrapper = debugPanel.closest('.mle-debug-wrapper');
                if (debugWrapper) {
                    debugWrapper.outerHTML = data.debugHtml;

                    // Re-apply the visibility state if it was hidden
                    const newDebugPanel = mediaManager.querySelector('[data-mle-debug]');
                    if (newDebugPanel && isHidden) {
                        newDebugPanel.classList.add('hidden', 'mle-hidden');
                    } else if (newDebugPanel) {
                        newDebugPanel.classList.remove('hidden', 'mle-hidden');
                    }
                }
            }
        }

        // Handle disabling/enabling of forms and alert visibility
        if (data.mediaCount !== undefined && data.mediaCount !== null) {
            // If the component provided an explicit per-instance max, compute locally against that value
            // and ignore potentially conflicting server-provided isAtMax (which uses global config).
            const isAtMax = hasExplicitPerInstanceMax
                ? (data.mediaCount >= maxCount)
                : ((typeof data.isAtMax !== 'undefined') ? data.isAtMax : (data.mediaCount >= maxCount));

            // Toggle form elements based on capacity for single; for multiple, keep enabled unless at max
            if (!config.multiple) {
                if (data.mediaCount < 1) {
                    enableFormElements(forms);
                } else {
                    disableFormElements(forms);
                }
            } else {
                if (isAtMax) {
                    disableFormElements(forms);
                } else {
                    enableFormElements(forms);
                }
            }

            // Hide alerts when no longer at max or disabled; add alert when reaching max
            if (!isAtMax && formContainer) {
                const maxAlert = formContainer.querySelector('[data-mle-max-reached-alert]');
                if (maxAlert) {
                    maxAlert.remove();
                }
                const disabledAlert = formContainer.querySelector('[data-mle-disabled-alert]');
                if (disabledAlert) {
                    disabledAlert.remove();
                }
            } else if (isAtMax && formContainer) {
                let maxAlert = formContainer.querySelector('[data-mle-max-reached-alert]');
                if (!maxAlert) {
                    maxAlert = document.createElement('div');
                    maxAlert.className = 'mle-alert alert alert-primary';
                    maxAlert.setAttribute('data-mle-max-reached-alert', '');
                    // Minimal text; server-side translations are not available here
                    maxAlert.textContent = config.multiple ? trans('upload_disabled_max_items_reached') : trans('upload_disabled_only_one_medium_allowed');
                    // Insert after counts if available, else append to formContainer
                    if (countsEl && countsEl.parentElement) {
                        countsEl.parentElement.insertBefore(maxAlert, countsEl.nextSibling);
                    } else {
                        formContainer.appendChild(maxAlert);
                    }
                }
            }
        }

        console.log('media-manager-previews-refresher.js - updatePreviews - dispatch event')
        // Notify listeners that the previews were updated
        mediaManager.dispatchEvent(new CustomEvent('mediaManagerPreviewsUpdated', {
            bubbles: true,
            detail: {
                mediaManager: mediaManager,
                previewGrid: previewGrid
            }
        }));

    } catch (error) {
        console.error('Error refreshing media manager:', error);
    }
}

function trans (key) {
    return window.mediaLibraryTranslations?.[key] || key;
}
