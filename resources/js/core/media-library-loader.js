import {
    createAssetLoader,
    collectConfigs,
    mergeConfigs,
    ensureScript as coreEnsureScript,
    ensureStyle as coreEnsureStyle,
} from './asset-loader-core';

/**
 * Shared bootstrapper for all MLBRGN asset bundles
 */
function bootAssets({ selector, namespace, runner }) {
    console.log('mlb selector: ', selector)
    console.log('number of configs found: ', document.querySelectorAll(selector).length)

    const configs = collectConfigs(selector);
    const manifest = mergeConfigs(configs);

    if (!manifest) return;

    const loader = createAssetLoader(namespace, {
        basePath: manifest.assetBasePath,
    });

    runner(loader, manifest);

    // Ensure the custom element <image-editor> is registered early so tests/UI
    // can detect it immediately, even when the modal bundle loads later on-demand.
    try {
        if (typeof window !== 'undefined' && 'customElements' in window) {
            if (!window.customElements.get('image-editor')) {
                // This lightweight shared listener is responsible for defining the
                // <image-editor> custom element. It is safe and idempotent to load.
                loader.loadScript('js/shared/image-editor-listener.js');
            }
        }
    } catch (e) {
        // Ignore failures here; the lazy loader/observer below can still load it later.
    }

    // Expose globals for optional lazy loading and debugging
    window.mleTheme = manifest.theme;
    window.mleAssetBase = manifest.assetBasePath;
    window.mleDebug = !!manifest.debug;

    // Optional: observe DOM for newly injected configs or feature markers
    try {
        observeDomForMle({ selector, loader, manifest });
    } catch (e) {
        // swallow observer errors
    }
}

/* =========================================================
 * MEDIA LIBRARY EXTENSIONS
 * ========================================================= */

function loadMediaAssets(loader, manifest) {
    const { assets = {}, theme, translations = {}, debug, imageEditorTranslationsPath } = manifest;
    const { loadScript, loadStyle } = loader;

    if (debug) {
        console.debug('[mle] assets to load:', assets);
    }
    window.mediaLibraryTranslations = {
        ...(window.mediaLibraryTranslations ?? {}),
        ...translations,
    };

    if (imageEditorTranslationsPath) {
        window.imageEditorTranslationsPath = imageEditorTranslationsPath;
    }

    const tasks = [];

    // Always ensure the shared image editor listener is present so the
    // <image-editor> custom element is registered early. This is idempotent
    // and light-weight; loading it even when the editor UI isn't used is safe.
    tasks.push(loadScript('js/shared/image-editor-listener.js'));

    // Also preload the theme-specific modal controller so opening the image
    // editor modal can initialize immediately without race conditions.
    tasks.push(loadScript(`js/${theme}/modal-image-editor.js`));

    if (assets.css) {
        tasks.push(loadStyle(`css/${theme}.css`));
    }

    if (assets.js) {
        tasks.push(loadScript(`js/${theme}.js`));
    }

    if (assets.carousel && theme === 'plain') {
        tasks.push(loadScript(`js/plain/media-carousel.js`));
    }

    if (assets.tinymceIframe) {
        tasks.push(loadScript(`js/shared/tinymce-custom-file-picker-iframe.js`));
    }

    if (assets.imageEditorModal) {
        if (debug) console.debug('[mle] loading image editor modal');
        tasks.push(loadScript(`js/${theme}/modal-image-editor.js`));
    } else {
        if (debug) console.debug('[mle] not loading image editor modal');
    }

    if (assets.mediaModal) {
        if (debug) console.debug('[mle] loading media modal');
        tasks.push(loadScript(`js/${theme}/modal-media.js`));
    } else {
        if (debug) console.debug('[mle] not loading media modal');
    }

    if (assets.imageEditor) {
        tasks.push(loadScript(`js/shared/image-editor-listener.js`));
    }

    if (assets.mediaManagerSubmitter) {
        tasks.push(loadScript(`js/shared/media-manager-submitter.js`));
    }

    if (assets.mediaLabSubmitter) {
        tasks.push(loadScript(`js/shared/media-lab-submitter.js`));
    }

    if (assets.debugToggle || debug) {
        tasks.push(loadScript(`js/shared/debug-toggle-listener.js`));
    }

    if (assets.liteYoutube) {
        tasks.push(loadScript(`js/shared/lite-youtube.js`));

        if (!window.YT) {
            tasks.push(
                loadScript('https://www.youtube.com/iframe_api', {
                    type: 'text/javascript',
                })
            );
        }
    }

    Promise.allSettled(tasks).then(() => {
        if (debug) {
            console.debug('[media] assets loaded', manifest);
        }
    });
}

/* =========================================================
 * BOOTSTRAP ENTRIES
 * ========================================================= */

bootAssets({
    selector: '.mlbrgn-medialibrary-config',
    namespace: 'media',
    runner: loadMediaAssets,
});

/**
 * Expose an imperative API to require assets on-demand.
 */
export async function requireMediaAssets(keys = []) {
    const theme = window.mleTheme;
    const base = window.mleAssetBase;
    const debug = !!window.mleDebug;

    const path = (p) => new URL(p, base).toString();
    const tasks = [];

    for (const key of keys) {
        switch (key) {
            case 'css':
                tasks.push(coreEnsureStyle(path(`css/${theme}.css`))); break;
            case 'js':
                tasks.push(coreEnsureScript(path(`js/${theme}.js`))); break;
            case 'imageEditorModal':
                tasks.push(coreEnsureScript(path(`js/${theme}/modal-image-editor.js`))); break;
            case 'imageEditor':
                tasks.push(coreEnsureScript(path('js/shared/image-editor-listener.js'))); break;
            case 'mediaModal':
                tasks.push(coreEnsureScript(path(`js/${theme}/modal-media.js`))); break;
            case 'carousel':
                if (theme === 'plain') tasks.push(coreEnsureScript(path('js/plain/media-carousel.js')));
                break;
            case 'tinymceIframe':
                tasks.push(coreEnsureScript(path('js/shared/tinymce-custom-file-picker-iframe.js'))); break;
            case 'mediaManagerSubmitter':
                tasks.push(coreEnsureScript(path('js/shared/media-manager-submitter.js'))); break;
            case 'mediaLabSubmitter':
                tasks.push(coreEnsureScript(path('js/shared/media-lab-submitter.js'))); break;
            case 'debugToggle':
                tasks.push(coreEnsureScript(path('js/shared/debug-toggle-listener.js'))); break;
            case 'liteYoutube':
                tasks.push(coreEnsureScript(path('js/shared/lite-youtube.js')));
                if (!window.YT) tasks.push(coreEnsureScript('https://www.youtube.com/iframe_api'));
                break;
            default:
                break;
        }
    }

    await Promise.allSettled(tasks);
    if (debug) console.debug('[mle] required assets loaded (on-demand):', keys);
}

// Global event hook: components can dispatch mle:require-assets to lazy load
document.addEventListener('mle:require-assets', (e) => {
    const { keys = [] } = (e && e.detail) || {};
    requireMediaAssets(keys);
});

// MutationObserver: watch for newly injected configs or feature markers
function observeDomForMle({ selector, loader, manifest }) {
    const theme = manifest.theme;
    const base = manifest.assetBasePath;
    const debug = !!manifest.debug;
    let timer;

    const handle = async () => {
        // If an image editor modal container appears, ensure its assets
        if (document.querySelector('[data-mle-image-editor-modal]')) {
            await requireMediaAssets(['imageEditor', 'imageEditorModal']);
        }

        // If new config blocks were injected, merge and require any new assets
        const configs = collectConfigs(selector);
        const merged = mergeConfigs(configs) || { assets: {} };
        const toRequire = Object.entries(merged.assets)
            .filter(([, v]) => v === true)
            .map(([k]) => k);
        if (toRequire.length) {
            await requireMediaAssets(toRequire);
        }
        if (debug) console.debug('[mle] observer tick complete');
    };

    const obs = new MutationObserver(() => {
        clearTimeout(timer);
        timer = setTimeout(handle, 80);
    });

    obs.observe(document.body, { childList: true, subtree: true });
}
