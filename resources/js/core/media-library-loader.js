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
    // console.log('mlb selector: ', selector)
    // console.log('number of configs found: ', document.querySelectorAll(selector).length)

    const configs = collectConfigs(selector);
    const manifest = mergeConfigs(configs);

    if (!manifest) return;

    const loader = createAssetLoader(namespace, {
        basePath: manifest.assetBasePath,
    });

    // Expose globals for optional lazy loading and debugging as early as possible
    // so that even if the runner throws, downstream code (requireMediaAssets, observer)
    // can still resolve absolute URLs correctly.
    window.mleTheme = manifest.theme;
    window.mleAssetBase = manifest.assetBasePath;
    window.mleDebug = !!manifest.debug;

    // Run the package-specific asset loader
    runner(loader, manifest);

    // Lazy strategy: do not eagerly register <image-editor> here.
    // The listener will be loaded on-demand via requireMediaAssets() or the observer.

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

    // Lazy strategy: do not preload the shared image editor listener here.

    // Also preload the theme-specific modal controller so opening the image
    // editor modal can initialize immediately without race conditions.
    tasks.push(loadScript(`js/${theme}/modal-image-editor.js`));

    if (assets.css) {
        tasks.push(loadStyle(`css/${theme}.css`));
    }

    if (assets.js) {
        tasks.push(loadScript(`js/${theme}.js`));
    }

    // if (assets.bootstrapCss) {
    //     tasks.push(loadStyle('css/bootstrap-5/bootstrap.css'));
    // }
    //
    // if (assets.bootstrapJs) {
    //     tasks.push(loadScript('js/bootstrap-5/bootstrap.bundle.min.js', {
    //         type: 'text/javascript',
    //     }));
    // }

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
        // Ensure the shared listener only when explicitly requested by config.
        // Use absolute URL + global ensure to avoid duplicates across loaders.
        const url = loader.resolveUrl('js/shared/image-editor-listener.js');
        tasks.push(coreEnsureScript(url));
        const imageEditorUrl = loader.resolveUrl('js/image-editor.js');
        tasks.push(coreEnsureScript(imageEditorUrl));
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
 * Global initialization for elements that need a client token (e.g. for promotion).
 */
function initClientTokens() {
    // Attempt to get token from localStorage or cookie
    let token = localStorage.getItem('mle_client_token');
    if (!token) {
        const match = document.cookie.match(/mle_client_token=([^;]+)/);
        if (match) token = match[1];
    }

    if (token) {
        document.querySelectorAll('[data-mle-client-token]').forEach((el) => {
            if (el.tagName === 'INPUT') {
                el.value = token;
            } else {
                el.setAttribute('data-mle-client-token', token);
            }
        });
    }
}

// Initial run
initClientTokens();

// Also watch for newly injected tokens
document.addEventListener('DOMContentLoaded', initClientTokens);

// Also listen for token generation events (e.g. from ClientToken.get())
document.addEventListener('mle:client-token-generated', (e) => {
    const token = e.detail?.token;
    if (token) {
        document.querySelectorAll('[data-mle-client-token]').forEach((el) => {
            if (el.tagName === 'INPUT') {
                el.value = token;
            } else {
                el.setAttribute('data-mle-client-token', token);
            }
        });
    }
});

/**
 * Expose an imperative API to require assets on-demand.
 */
export async function requireMediaAssets(keys = []) {
    const theme = window.mleTheme;
    const base = getMleAssetBase();
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

    // obs.observe(document.body, { childList: true, subtree: true });
}

// Robustly resolve the asset base path on-demand
function getMleAssetBase() {
    // 1) Prefer the global set during boot
    let base = typeof window !== 'undefined' ? window.mleAssetBase : null;

    // 2) If missing, try the global JSON config block
    if (!base) {
        try {
            const el = document.getElementById('mle-global');
            if (el && el.textContent) {
                const cfg = JSON.parse(el.textContent.trim());
                if (cfg && cfg.assetBasePath) {
                    base = cfg.assetBasePath;
                }
            }
        } catch (_) { /* noop */ }
    }

    // 3) If still missing, derive from the loader script tag URL
    if (!base) {
        try {
            const s = document.querySelector('script[src*="/js/core/media-library-loader.js"]');
            if (s && s.src) {
                const u = new URL(s.src, document.baseURI);
                const parts = u.pathname.split('/');
                const jsIdx = parts.lastIndexOf('js');
                if (jsIdx > 0) {
                    const baseParts = parts.slice(0, jsIdx); // path up to the package root
                    const root = new URL('/', u.origin);
                    root.pathname = baseParts.join('/').replace(/\/$/, '');
                    base = root.toString().replace(/\/$/, '');
                }
            }
        } catch (_) { /* noop */ }
    }

    // 4) Normalize short form '/vendor/mlbrgn' to include the package
    try {
        if (base) {
            const u = new URL(base, document.baseURI);
            const pathname = u.pathname.replace(/\/$/, '');
            if (pathname === '/vendor/mlbrgn') {
                u.pathname = '/vendor/mlbrgn/laravel-medialibrary-extensions';
                base = u.toString().replace(/\/$/, '');
            }
        }
    } catch (_) { /* noop */ }

    return base || '/vendor/mlbrgn/laravel-medialibrary-extensions';
}

/**
 * Global submit listener for non-XHR forms that want a "Please wait" indicator.
 */
document.addEventListener('submit', (e) => {
    const el = e.target;
    if (el.hasAttribute('data-mle-form') && el.hasAttribute('data-mle-onsubmit-message')) {
        const message = el.getAttribute('data-mle-onsubmit-message');
        if (!message) return;

        el.setAttribute('data-mle-busy', '1');
        const c = el.closest('[data-mle-media-manager]') || el.closest('[data-mle-media-lab]');
        if (!c) return;

        const a = c.querySelector('[data-mle-status-area-container]');
        if (!a) return;

        const m = document.createElement('div');
        m.setAttribute('data-mle-status-message', '');
        m.textContent = message;
        a.innerHTML = '';
        a.appendChild(m);
    }
});

/*
To debug use following snippet in console:
console.table([...document.querySelectorAll('.mlbrgn-medialibrary-config')].map((el,i)=>{
  const c = JSON.parse(el.textContent.trim());
  return {i, id: el.id, for: c.for, base: c.assetBasePath||null, theme: c.theme||null, assets: c.assets}
}));
*/
