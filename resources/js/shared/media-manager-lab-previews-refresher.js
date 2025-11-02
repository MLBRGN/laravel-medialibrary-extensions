import { handleAjaxError } from './xhrStatus';
import {getMediaManagerConfig} from "@/js/shared/media-manager-config";

/**
 * Refreshes the media preview container after upload, deletion, or restore.
 */

// updatePreviews(mediaManager, config, mediumId, { part: 'base' | 'original' })
export async function updatePreviews(mediaManager, config, mediumId,  detail = {}) {
    const previewsContainer = mediaManager.querySelector('[data-media-manager-lab-previews]');
    if (!previewsContainer) return;

    const params = new URLSearchParams({
        initiator_id: config.id,
        medium_id: mediumId,
    });

    // Cache-busting param
    params.append('_', Date.now());

    if (detail.part) {
        params.append('part', detail.part);
    }

    try {
        const response = await fetch(`${config.mediaManagerLabPreviewUpdateRoute}?${params}`, {
            headers: { 'Accept': 'application/json' },
            cache: 'no-store', // prevents using or storing cache
        });

        const data = await response.json();

        if (!response.ok) {
            handleAjaxError(response, data);
            return;
        }

        if (!data.html) {
            return;
        }

        // If partial update, replace only that part
        if (detail.part === 'original') {
            const replaced = previewsContainer.querySelector('[data-media-lab-preview-original]');
            if (replaced) replaced.outerHTML = data.html;
        } else if (detail.part === 'base') {
            const replaced = previewsContainer.querySelector('[data-media-lab-preview-base]');
            if (replaced) replaced.outerHTML = data.html;
        } else {
            previewsContainer.innerHTML = data.html;
        }

        // Notify listeners that the previews were updated
        document.dispatchEvent(new CustomEvent('mediaManagerLabPreviewsUpdated', {
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

document.addEventListener('imageUpdated', (e) => {
    // console.log('image updated', e);
    const initiator =  document.getElementById(e.detail.initiatorId);
    const mediaManagerLab = initiator.closest('[data-media-manager-lab]')
    const mediumId = e.detail.mediumId;

    const config = getMediaManagerConfig(mediaManagerLab);
    if (!config) {
        console.warn('Could not get config')
        return;
    }

    updatePreviews(mediaManagerLab, config, mediumId, { part : 'all'})
});
