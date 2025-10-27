import { handleAjaxError } from './xhrStatus';
import { disableFormElements, enableFormElements } from './form';
import {getMediaManagerConfig} from "@/js/shared/media-manager-config";

/**
 * Refreshes the media preview container after upload, deletion, or restore.
 */

export async function updatePreviews(mediaManager, config, mediumId,  detail = {}) {
    const previewsContainer = mediaManager.querySelector('[data-media-manager-lab-previews]');
    if (!previewsContainer) return;

    const params = new URLSearchParams({
        initiator_id: config.id,
        medium_id: mediumId,
    });

    try {
        const response = await fetch(`${config.mediaManagerLabPreviewUpdateRoute}?${params}`, {
            headers: { 'Accept': 'application/json' }
        });

        const data = await response.json();

        if (!response.ok) {
            handleAjaxError(response, data);
            return;
        }

        if (!data.html) {
            return;
        }

        previewsContainer.innerHTML = data.html;

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

// TODO refresh media manager lab previews after nested media manager refreshed
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

    // console.log('config', config, mediumId, {});

   updatePreviews(mediaManagerLab, config, mediumId, {})
// debugger;
    // console.log('reinitialize image editor modals for media manager', mediaManager);
});
