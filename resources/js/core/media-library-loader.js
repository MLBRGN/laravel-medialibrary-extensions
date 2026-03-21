import {
    createAssetLoader,
    collectConfigs,
    mergeConfigs,
} from './asset-loader-core';

const loader = createAssetLoader('media', {
    basePath: '/vendor/mlbrgn/media-library-extensions'
});

function loadMediaAssets(loader, manifest) {
    const {theme, assets, translations, debug} = manifest;
    const {loadScript, loadStyle} = loader;

    // expose translations safely
    window.mediaLibraryTranslations = {
        ...(window.mediaLibraryTranslations ?? {}),
        ...translations,
    };

    try {
        if (assets.css) {
            loadStyle(`css/${theme}.css`);
        }

        if (assets.js) {
            loadScript(`js/${theme}.js`);
        }

        if (assets.carousel && theme === 'plain') {
            loadScript(`js/plain/media-carousel.js`);
        }

        if (assets.tinymceIframe) {
            loadScript(
                `js/shared/tinymce-custom-file-picker-iframe.js`
            );
        }

        if (assets.imageEditorModal) {
            loadScript(`js/${theme}/modal-image-editor.js`);
        }

        if (assets.mediaModal) {
            loadScript(`js/${theme}/modal-media.js`);
        }

        if (assets.imageEditor) {
            loadScript(`js/shared/image-editor-listener.js`);
        }

        if (assets.mediaManagerSubmitter) {
            loadScript(`js/shared/media-manager-submitter.js`);
        }

        if (assets.mediaManagerLabSubmitter) {
            loadScript(`js/shared/media-manager-lab-submitter.js`);
        }

        if (assets.liteYoutube) {
            loadScript(`js/shared/lite-youtube.js`);

            if (!window.YT) {
                loadScript(
                    'https://www.youtube.com/iframe_api',
                    { type: 'text/javascript' }
                );
            }
        }

        if (debug) {
            console.log('[media] assets loaded', manifest);
        }
    } catch (e) {
        console.error('[media] asset loading failed', e);
    }
}

// Boot
const configs = collectConfigs('.mlbrgn-medialibrary-config');
const manifest = mergeConfigs(configs);
console.log('mle configs', configs)
console.log('mle manifest', manifest)

if (manifest) {
    loadMediaAssets(loader, manifest);
}
