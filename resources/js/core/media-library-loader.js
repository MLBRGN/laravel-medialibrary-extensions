import {
    createAssetLoader,
    collectConfigs,
    mergeConfigs,
} from './asset-loader-core';

/**
 * Shared bootstrapper for all MLBRGN asset bundles
 */
function bootAssets({ selector, namespace, runner }) {
   // console.log('mlb bootAssets')
    const configs = collectConfigs(selector);
    const manifest = mergeConfigs(configs);

    if (!manifest) return;

    const loader = createAssetLoader(namespace, {
        basePath: manifest.assetBasePath,
    });

    runner(loader, manifest);
}

/* =========================================================
 * MEDIA LIBRARY EXTENSIONS
 * ========================================================= */

function loadMediaAssets(loader, manifest) {
    const { assets = {}, theme, translations = {}, debug, imageEditorTranslationsPath } = manifest;
    const { loadScript, loadStyle } = loader;

    // console.log('assets to load: ', assets)
    window.mediaLibraryTranslations = {
        ...(window.mediaLibraryTranslations ?? {}),
        ...translations,
    };

    if (imageEditorTranslationsPath) {
        window.imageEditorTranslationsPath = imageEditorTranslationsPath;
    }

    const tasks = [];

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
        tasks.push(loadScript(`js/${theme}/modal-image-editor.js`));
    }

    if (assets.mediaModal) {
        tasks.push(loadScript(`js/${theme}/modal-media.js`));
    }

    if (assets.imageEditor) {
        tasks.push(loadScript(`js/shared/image-editor-listener.js`));
    }

    if (assets.mediaManagerSubmitter) {
        tasks.push(loadScript(`js/shared/media-manager-submitter.js`));
    }

    if (assets.mediaManagerLabSubmitter) {
        tasks.push(loadScript(`js/shared/media-manager-lab-submitter.js`));
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

    // console.log('assets to load: ', tasks)
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
