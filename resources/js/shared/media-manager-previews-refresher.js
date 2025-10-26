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
    const previewGrid = mediaManager.querySelector('[data-media-preview-grid]');
    const forms = mediaManager.querySelectorAll('[data-form], [data-xhr-form]');
    if (!previewGrid) return;

    const params = new URLSearchParams({
        model_type: config.modelType,
        model_id: config.modelId,
        single_medium_id: detail.singleMediumId ?? null,
        temporary_upload_mode: config.temporaryUploadMode,
        initiator_id: config.id,
        collections: JSON.stringify(config.collections),
        options: JSON.stringify(config.options),
    });

    try {
        const response = await fetch(`${config.mediaManagerPreviewUpdateRoute}?${params}`, {
            headers: { 'Accept': 'application/json' }
        });

        const data = await response.json();

        if (!response.ok) {
            handleAjaxError(response, data);
            return;
        }

        if (!data.html) return;

        previewGrid.innerHTML = data.html;

        // Handle single-medium disabling/enabling of forms
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
