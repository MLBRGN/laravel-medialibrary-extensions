import { handleAjaxError } from './xhrStatus';
import {getMediaManagerConfig} from "@/js/shared/media-manager-config";

/**
 * Refreshes the media preview container after upload, deletion, or restore.
 */

export async function updateMediaLabBase(mediaManager, config, mediumId,  detail = {}) {

   console.log('media-lab-previews-refresher.js - updateMediaLabBase called, config: ', config)
    const previewsContainer = mediaManager.querySelector('[data-mle-media-lab-previews]');
    if (!previewsContainer) {
        console.warn('No previews container found');
        return;
    }

    console.log('updateMediaLabBase - config ', config)
    console.log('updateMediaLabBase - theme ', config.theme)
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

    // Cache-busting param
    params.append('_', Date.now());

    try {
        const response = await fetch(`${config.routes.mediaLabPreviewBaseUpdate}?${params}`, {
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
            console.warn('response not ok', response, data)
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

        const mediaLabPreviewBase = previewsContainer.querySelector('[data-mle-media-lab-preview-base]');
        if (!mediaLabPreviewBase) {
            console.warn('mediaLabPreviewBase not found')
            return
        }
        mediaLabPreviewBase.outerHTML = data.html;

        // Notify listeners that the previews were updated so components can re-initialize
        mediaManager.dispatchEvent(new CustomEvent('mediaManagerPreviewsUpdated', {
            bubbles: true,
            composed: true,
            detail: {
                mediaManager: mediaManager,
                previewsContainer: previewsContainer,
                section: 'base',
                mediumId: mediumId,
            }
        }));

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

        // Notify listeners (section-specific) that Base was updated
        mediaManager.dispatchEvent(new CustomEvent('mediaLabPreviewBaseUpdated', {
            bubbles: true,
            composed: true,
            detail: {
                mediaManager: mediaManager,
                previewsContainer: previewsContainer,
                mediumId: mediumId,
            }
        }));

    } catch (error) {
        console.error('Error refreshing media lab base:', error);
    }
}

export async function updateMediaLabOriginal(mediaManager, config, mediumId,  detail = {}) {
    // console.log('media-lab-previews-refresher.js - updateMediaLabOriginal called')

    const previewsContainer = mediaManager.querySelector('[data-mle-media-lab-previews]');
    if (!previewsContainer) {
        console.warn('No previews container found');
        return;
    }

    // console.log('updateMediaLabOriginal - medium id ', mediumId)
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

    // Cache-busting param
    params.append('_', Date.now());

    try {
        const response = await fetch(`${config.routes.mediaLabPreviewOriginalUpdate}?${params}`, {
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
            // console.log('response not ok', response, data)
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

        // console.log('updateMediaLabOriginal - data.html ', data.html)
        const mediaLabPreviewOriginal = previewsContainer.querySelector('[data-mle-media-lab-preview-original]');
        if (!mediaLabPreviewOriginal) {
            console.warn('mediaLabPreviewOriginal not found')
            return
        }
        mediaLabPreviewOriginal.outerHTML = data.html;

        // Notify listeners that the previews were updated so components can re-initialize
        mediaManager.dispatchEvent(new CustomEvent('mediaManagerPreviewsUpdated', {
            bubbles: true,
            composed: true,
            detail: {
                mediaManager: mediaManager,
                previewsContainer: previewsContainer,
                section: 'original',
                mediumId: mediumId,
            }
        }));
        // console.log('mediaLabPreviewOriginal html updated', mediaLabPreviewOriginal)
        // Update debug panel if present
        // if (data.debugHtml) {
        //     const debugPanel = mediaManager.querySelector('[data-mle-debug]');
        //     if (debugPanel) {
        //         // We want to keep the current visibility state (hidden or not)
        //         const isHidden = debugPanel.classList.contains('hidden') || debugPanel.classList.contains('mle-hidden');
        //
        //         // Replace the outer wrapper of the debug content
        //         const debugWrapper = debugPanel.closest('.mle-debug-wrapper');
        //         if (debugWrapper) {
        //             debugWrapper.outerHTML = data.debugHtml;
        //
        //             // Re-apply visibility state if it was hidden
        //             const newDebugPanel = mediaManager.querySelector('[data-mle-debug]');
        //             if (newDebugPanel && isHidden) {
        //                 newDebugPanel.classList.add('hidden', 'mle-hidden');
        //             } else if (newDebugPanel) {
        //                 newDebugPanel.classList.remove('hidden', 'mle-hidden');
        //             }
        //         }
        //     }
        // }

        // Notify listeners (section-specific) that Original was updated
        mediaManager.dispatchEvent(new CustomEvent('mediaLabPreviewOriginalUpdated', {
            bubbles: true,
            composed: true,
            detail: {
                mediaManager: mediaManager,
                previewsContainer: previewsContainer,
                mediumId: mediumId,
            }
        }));

    } catch (error) {
        console.error('Error refreshing media lab original:', error);
    }
}

// listen to imageUpdated event so that we can update the restore form's media_id
document.addEventListener('imageUpdated', (e) => {
    const newMediumId = e.detail?.newMediumId;
    if (!newMediumId) {
        console.warn('imageUpdated: missing newMediumId in event.detail');
        return;
    }

    const mediaLab = e.target.closest('[data-mle-media-lab]');
    if (!mediaLab) {
        console.warn('imageUpdated: could not resolve media lab from event target');
        return;
    }

    const config = getMediaManagerConfig(mediaLab);
    // Update both previews so hidden form inputs and buttons reflect the new medium id
    // This prevents stale actions like restore/delete referencing the old medium
    updateMediaLabBase(mediaLab, config, newMediumId, { sourceEvent: 'imageUpdated' });
    updateMediaLabOriginal(mediaLab, config, newMediumId, { sourceEvent: 'imageUpdated' });
});
