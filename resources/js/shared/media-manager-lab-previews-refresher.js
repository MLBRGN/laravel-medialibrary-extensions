import { handleAjaxError } from './xhrStatus';
import {getMediaManagerConfig} from "@/js/shared/media-manager-config";

/**
 * Refreshes the media preview container after upload, deletion, or restore.
 */

// updatePreviews(mediaManager, config, mediumId, { part: 'base' | 'original' })
export async function updatePreviews(mediaManager, config, mediumId,  detail = {}) {
    console.log('update previews media lab')

    const previewsContainer = mediaManager.querySelector('[data-mle-media-manager-lab-previews]');
    if (!previewsContainer) return;

    const params = new URLSearchParams({
        model_type: config.modelType,
        model_id: config.modelId,
        initiator_id: config.id,
        medium_id: mediumId,
        options: JSON.stringify(config.options),
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

            const replaced = previewsContainer.querySelector('[data-mle-media-lab-preview-original]');
            console.log('replace "original" with updated html', replaced);
            if (!replaced) {
                consolw.warn('replaced not found')
                return
            }
            replaced.outerHTML = data.html;
        } else if (detail.part === 'base') {
            const replaced = previewsContainer.querySelector('[data-mle-media-lab-preview-base]');
            console.log('replace "base" with updated html', replaced)
            if (!replaced) {
                consolw.warn('replaced not found')
                return
            }
            replaced.outerHTML = data.html;
        } else {
            console.log('replace all with updated html')
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

// TODO this code also triggers for non media lab managers,
//  for now i just return when no media manager lab found, but shouldn't listen at all?
//  How do i do this for regular media refresh?
document.addEventListener('imageUpdated', (e) => {
    const initiator =  document.getElementById(e.detail.initiatorId);
    const mediaManagerLab = initiator.closest('[data-mle-media-manager-lab]')
    const mediumId = e.detail?.mediumId;

    if (!mediaManagerLab) {
        console.warn('Media manager lab not found')
        return;
    }

    if (!mediumId) {
        console.warn('No mediumId found')
        return;
    }

    const config = getMediaManagerConfig(mediaManagerLab);
    if (!config) {
        console.warn('Could not get config')
        return;
    }

    updatePreviews(mediaManagerLab, config, mediumId, { part : 'all'})
});
