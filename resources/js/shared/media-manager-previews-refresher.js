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
    const previewGrid = mediaManager.querySelector('[data-mle-media-preview-grid]');
    const forms = mediaManager.querySelectorAll('[data-mle-form], [data-mle-xhr-form]');
    if (!previewGrid) return;

    console.log('update previews media manager', config, detail);
    const params = new URLSearchParams({
        model_type: config.modelType,
        model_id: config.modelId,
        single_media_id: detail.singleMediaId ?? null,
        temporary_upload_mode: config.temporaryUploadMode,
        initiator_id: config.id,
        collections: JSON.stringify(config.collections),
        // options: JSON.stringify(config.options),
        disabled: config.disabled,
        readonly: config.readonly,
        selectable: config.selectable,
        multiple: config.multiple,
        instance_id: config.instanceId,
        data_source: config.dataSource,
        theme: config.frontendTheme,
        include_debug: 'true',
    });

    // Cache-busting param
    params.append('_', Date.now());

    try {
        const response = await fetch(`${config.routes.mediaManagerPreviewUpdate}?${params}`, {
            headers: { 'Accept': 'application/json' },
            cache: 'no-store', // prevents using or storing cache
        });

        const data = await response.json();

        if (!response.ok) {
            handleAjaxError(response, data);
            return;
        }

        if (!data.html) return;

        previewGrid.innerHTML = data.html;

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

                    // Re-apply visibility state if it was hidden
                    const newDebugPanel = mediaManager.querySelector('[data-mle-debug]');
                    if (newDebugPanel && isHidden) {
                        newDebugPanel.classList.add('hidden', 'mle-hidden');
                    } else if (newDebugPanel) {
                        newDebugPanel.classList.remove('hidden', 'mle-hidden');
                    }
                }
            }
        }

        // Handle single-media disabling/enabling of forms
        if (!config.multiple && data.mediaCount !== undefined && data.mediaCount !== null) {
            if (data.mediaCount < 1) {
                enableFormElements(forms);
            } else {
                disableFormElements(forms);
            }
        }

        // Notify listeners that the previews were updated
        document.dispatchEvent(new CustomEvent('mediaManagerPreviewsUpdated', {
            bubbles: false,
            detail: {
                mediaManager: mediaManager,
                previewGrid: previewGrid
            }
        }));

    } catch (error) {
        console.error('Error refreshing media manager:', error);
    }
}
