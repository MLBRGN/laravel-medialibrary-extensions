// dynamic-loader.js
// CSP-safe, ES-module-based, multi-component asset loader

// -----------------------------------------------------------------------------
// Internal registries (module-scoped, automatically single-instance)
// -----------------------------------------------------------------------------

const loadedScripts = new Set();
const loadedCss = new Set();

// -----------------------------------------------------------------------------
// Helpers
// -----------------------------------------------------------------------------

function loadScript(src, type = 'module') {
    if (loadedScripts.has(src)) return;
    loadedScripts.add(src);

    const script = document.createElement('script');
    script.src = src;
    script.type = type;
    document.head.appendChild(script);
}

function loadCss(src) {
    if (loadedCss.has(src)) return;
    loadedCss.add(src);

    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = src;
    document.head.appendChild(link);
}

// -----------------------------------------------------------------------------
// Config collection & merging
// -----------------------------------------------------------------------------

function collectConfigs() {
    const elements = document.querySelectorAll('.mlbrgn-asset-config');
    if (!elements.length) return [];

    return Array.from(elements)
        .map(el => {
            try {
                return JSON.parse(el.dataset.config);
            } catch {
                console.warn('[mlbrgn] Invalid asset config JSON', el);
                return null;
            }
        })
        .filter(Boolean);
}

function mergeConfigs(configs) {
    if (!configs.length) return null;

    const merged = {
        theme: configs[0].theme,
        translations: {},
    };

    const booleanFlags = [
        'includeCss',
        'includeJs',
        'includeCarouselJs',
        'includeTinymceIframeJs',
        'includeImageEditorModalJs',
        'includeMediaModalJs',
        'includeImageEditorJs',
        'includeMediaManagerSubmitter',
        'includeMediaManagerLabSubmitter',
        'includeLiteYoutube',
    ];

    // Merge booleans (true if ANY component requires it)
    for (const flag of booleanFlags) {
        merged[flag] = configs.some(c => c[flag] === true);
    }

    // Merge translations
    for (const c of configs) {
        if (c.translations) {
            Object.assign(merged.translations, c.translations);
        }
    }

    // Warn if themes differ
    const themes = new Set(configs.map(c => c.theme));
    if (themes.size > 1) {
        console.warn(
            '[mlbrgn] Multiple frontend themes detected:',
            Array.from(themes)
        );
    }

    return merged;
}

// -----------------------------------------------------------------------------
// Asset loading
// -----------------------------------------------------------------------------

function loadAssets(config) {
    const base = '/vendor/mlbrgn/media-library-extensions';
    const theme = config.theme;

    // Expose translations globally (merged safely)
    window.mediaLibraryTranslations = {
        ...(window.mediaLibraryTranslations ?? {}),
        ...config.translations,
    };

    // CSS
    if (config.includeCss) {
        loadCss(`${base}/css/app-${theme}.css`);
    }

    // Root JS
    if (config.includeJs) {
        loadScript(`${base}/js/root/app-${theme}.js`);
    }

    // Carousel (plain theme only)
    if (config.includeCarouselJs && theme === 'plain') {
        loadScript(`${base}/js/plain/media-carousel.js`);
    }

    // TinyMCE custom file picker iframe
    if (config.includeTinymceIframeJs) {
        loadScript(
            `${base}/js/shared/tinymce-custom-file-picker-iframe.js`
        );
    }

    // Image editor modal
    if (config.includeImageEditorModalJs) {
        loadScript(`${base}/js/${theme}/modal-image-editor.js`);
    }

    // Media modal
    if (config.includeMediaModalJs) {
        loadScript(`${base}/js/${theme}/modal-media.js`);
    }

    // Image editor listener
    if (config.includeImageEditorJs) {
        loadScript(`${base}/js/shared/image-editor-listener.js`);
    }

    // Media manager submitter
    if (config.includeMediaManagerSubmitter) {
        loadScript(`${base}/js/shared/media-manager-submitter.js`);
    }

    // Media manager lab submitter
    if (config.includeMediaManagerLabSubmitter) {
        loadScript(`${base}/js/shared/media-manager-lab-submitter.js`);
    }

    // Lite YouTube
    if (config.includeLiteYoutube) {
        if (!customElements.get('lite-youtube')) {
            loadScript(`${base}/js/shared/lite-youtube.js`);
        }

        if (!window.YT) {
            loadScript(
                'https://www.youtube.com/iframe_api',
                'text/javascript'
            );
        }
    }
}

// -----------------------------------------------------------------------------
// Boot (ES modules are deferred by default)
// so no need for DOMContentLoaded
// -----------------------------------------------------------------------------

const configs = collectConfigs();

if (configs.length) {
    const mergedConfig = mergeConfigs(configs);
    if (mergedConfig) {
        loadAssets(mergedConfig);
    }
}
