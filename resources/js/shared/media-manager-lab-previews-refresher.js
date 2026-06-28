import { handleAjaxError } from './xhrStatus';
import {getMediaManagerConfig} from "@/js/shared/media-manager-config";

/**
 * Refreshes the media preview container after upload, deletion, or restore.
 */

// updatePreviews(mediaManager, config, mediumId, { part: 'base' | 'original' })
export async function updatePreviews(mediaManager, config, mediumId,  detail = {}) {
    console.log('media-manager-lab-previews-refresher.js - updatePreviews called')

    const previewsContainer = mediaManager.querySelector('[data-mle-media-manager-lab-previews]');
    if (!previewsContainer) {
        console.warn('No previews container found');
        return;
    } else {
        // console.log('previewsContainer found: ', previewsContainer)
    }
// console.log('medium id ', mediumId)
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

    if (detail.part) {
        params.append('part', detail.part);
    }

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

        // If partial update, replace only that part
        if (detail.part === 'original') {

            const replaced = previewsContainer.querySelector('[data-mle-media-lab-preview-original]');
            // console.log('replace "original" with updated html', replaced);
            if (!replaced) {
                console.warn('replaced not found')
                return
            }
            replaced.outerHTML = data.html;
        } else if (detail.part === 'base') {
            const replaced = previewsContainer.querySelector('[data-mle-media-lab-preview-base]');
            // console.log('replace "base" with updated html', replaced)
            if (!replaced) {
                console.warn('replaced not found')
                return
            }
            replaced.outerHTML = data.html;
        } else {
            // console.log('replace all with updated html')
            previewsContainer.innerHTML = data.html;
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

// TODO this code also triggers for non media lab managers,
//  for now i just return when no media manager lab found, but shouldn't listen at all?
//  How do i do this for regular media refresh?
document.addEventListener('imageUpdated', (e) => {
    // Resolve the media manager lab by Base ID
    const baseId = e.detail.baseId;
    let mediaManagerLab = document.querySelector(`[data-mle-media-manager-lab][data-base-id="${baseId}"]`);
    if (!mediaManagerLab) {
        // Fallback: find element by DOM id and climb to lab container
        const el = document.getElementById(baseId);
        mediaManagerLab = el?.closest('[data-mle-media-manager-lab]') ?? null;
    }

    const mediumId = e.detail?.mediumId;

    if (!mediaManagerLab) {
        // TODO should i differentiate between media lab and regular media manager?
        console.info('Media manager lab not found, skipping')
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

// export async function initializeMediaLab(mediaManagerLab) {
//     console.log('initializeMediaLab', mediaManagerLab)
    // TODO this code also triggers for non media lab managers,
    //  for now i just return when no media manager lab found, but shouldn't listen at all?
    //  How do i do this for regular media refresh?
    // document.addEventListener('imageUpdated', (e) => {
    //     console.log('imageUpdated triggered', e.detail)
    //     console.log('imageUpdated triggered within media lab', e)
    //     // Resolve the media manager lab by Base ID
    //     const baseId = e.detail.baseId;
    //     console.log('baseId', baseId)
    //     let mediaManagerLab = document.querySelector(`[data-mle-media-manager-lab][data-base-id="${baseId}"]`);
    //     console.log('imageUpdated', e.detail, mediaManagerLab)
    //     console.log('selector: [data-mle-media-manager-lab][data-base-id="' + baseId + '"]')
    //     if (!mediaManagerLab) {
    //         // Fallback: find element by DOM id and climb to lab container
    //         const el = document.getElementById(baseId);
    //         mediaManagerLab = el?.closest('[data-mle-media-manager-lab]') ?? null;
    //     }
    //
    //     const mediumId = e.detail?.mediumId;
    //
    //     if (!mediaManagerLab) {
    //         // TODO should i differentiate between media lab and regular media manager?
    //         console.info('Media manager lab not found, skipping')
    //         return;
    //     }
    //
    //     if (!mediumId) {
    //         console.warn('No mediumId found')
    //         return;
    //     }
    //
    //     const config = getMediaManagerConfig(mediaManagerLab);
    //     if (!config) {
    //         console.warn('Could not get config')
    //         return;
    //     }
    //
    //     updatePreviews(mediaManagerLab, config, mediumId, { part : 'all'})
    // });
// }

// document.querySelectorAll('[data-mle-media-manager-lab]').forEach(mediaManagerLab => {
//     initializeMediaLab(mediaManagerLab)
// })
