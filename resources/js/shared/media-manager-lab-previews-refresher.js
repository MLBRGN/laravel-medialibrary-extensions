import { handleAjaxError } from './xhrStatus';
import {getMediaManagerConfig} from "@/js/shared/media-manager-config";

/**
 * Refreshes the media preview container after upload, deletion, or restore.
 */

export async function updateMediaLabBase(mediaManager, config, mediumId,  detail = {}) {
    console.log('media-manager-lab-previews-refresher.js - updatePreviews called')

    const previewsContainer = mediaManager.querySelector('[data-mle-media-lab-previews]');
    if (!previewsContainer) {
        console.warn('No previews container found');
        return;
    } else {
        // console.log('previewsContainer found: ', previewsContainer)
    }
    console.log('updateMediaLabBase - medium id ', mediumId)
    const params = new URLSearchParams({
        model_type: config.modelType,
        model_id: config.modelId,
        base_id: config.id,
        medium_id: mediumId,
        options: JSON.stringify(config.options),
        theme: config.theme,
        client_token: config.clientToken,
        include_debug: 'true',
        data_source: config.dataSource
    });


    console.log('config', config)
    // Cache-busting param
    params.append('_', Date.now());

    try {
        const response = await fetch(`${config.routes.mediaManagerLabPreviewUpdate}?${params}`, {
            headers: { 'Accept': 'application/json' },
            cache: 'no-store', // prevents using or storing cache
        });

        let data;

        const contentType = response.headers.get('content-type') ?? '';

        if (contentType.includes('application/json')) {
            data = await response.json();
        } else {
            data = {
                message: await response.text(),
            };
        }

        if (!response.ok) {
            console.log('response not ok', response, data)
            handleAjaxError(response, {
                message: data.message,
                status: response.status,
            });
            return;
        }

        // check for html in the response, as we expect
        if (!data.html) {
            return;
        }

        const labPreviewBase = previewsContainer.querySelector('[data-mle-media-lab-preview-base]');
        if (!labPreviewBase) {
            console.warn('labPreviewBase not found')
            return
        }
        labPreviewBase.outerHTML = data.html;

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

        // Notify listeners that the previews were updated
        mediaManager.dispatchEvent(new CustomEvent('mediaManagerLabPreviewsUpdated', {
            bubbles: false,
            detail: {
                mediaManager: mediaManager,
                previewsContainer: previewsContainer
            }
        }));

    } catch (error) {
        console.error('Error refreshing media manager:', error);
    }
}

// TODO does this event even get catched?
// listen to imageUpdated event so that we can update the restore form's media_id
document.addEventListener('imageUpdated', (e) => {
    console.log('imageUpdated event', e)
    const mediumId = e.detail?.mediumId;
    const newMediumId = e.detail?.newMediumId;
    console.log('imageUpdated old medium id ', mediumId, ' new medium id ', newMediumId)

    const baseId = e.detail.baseId;

    const mediaLab = e.target.closest('[data-mle-media-lab]');
    console.log('mediaLab', mediaLab)
    const restoreForm = mediaLab?.querySelector('[data-mle-restore-form]');
    console.log('restoreForm', restoreForm)
    if (!restoreForm) {
        console.error('Restore form not found for media lab with base ID:', baseId);
        return;
    }
    const mediaIdInput = restoreForm.querySelector('[name="medium_id"]');
    console.log('mediaIdInput', mediaIdInput)
    mediaIdInput.value = newMediumId;

    updateMediaLabBase(mediaLab, config, newMediumId)
});
