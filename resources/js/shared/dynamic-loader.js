// dynamic-loader.js — CSP-safe asset loader

document.addEventListener('DOMContentLoaded', () => {
    const configEl = document.getElementById('mlbrgn-asset-config');
    if (!configEl) return;

    const config = JSON.parse(configEl.dataset.config);

    const theme = config.theme;
    const base = `/vendor/mlbrgn/media-library-extensions`;

    // Global translations
    window.mediaLibraryTranslations = config.translations;

    /** Helper to append script */
    const loadScript = (src, type = 'module') => {
        console.log('loadScript', src, type);
        const s = document.createElement('script');
        s.src = src;
        s.type = type;
        document.head.appendChild(s);
    };

    /** Helper to append CSS */
    const loadCss = (src) => {
        console.log('loadCss', src);
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = src;
        document.head.appendChild(link);
    };

    // CSS
    if (config.includeCss) {
        loadCss(`${base}/css/app-${theme}.css`);
    }

    // Main JS
    if (config.includeJs) {
        loadScript(`${base}/js/root/app-${theme}.js`);
    }

    // Carousel
    if (config.includeCarouselJs && theme === 'plain') {
        loadScript(`${base}/js/plain/media-carousel.js`);
    }

    // TinyMCE file picker
    if (config.includeTinymceIframeJs) {
        loadScript(`${base}/js/shared/tinymce-custom-file-picker-iframe.js`);
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

    // Media Manager submitter
    if (config.includeMediaManagerSubmitter) {
        loadScript(`${base}/js/shared/media-manager-submitter.js`);
    }

    // Media Manager Lab submitter
    if (config.includeMediaManagerLabSubmitter) {
        loadScript(`${base}/js/shared/media-manager-lab-submitter.js`);
    }

    // Lite YouTube
    if (config.includeLiteYoutube) {
        if (!customElements.get('lite-youtube')) {
            loadScript(`${base}/js/shared/lite-youtube.js`);
        }

        if (!window.YT) {
            loadScript('https://www.youtube.com/iframe_api', 'text/javascript');
        }
    }
});
