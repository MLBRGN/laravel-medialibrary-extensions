// noinspection JSUnresolvedReference
const closeBootstrapModal = (modal) => {
    if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
        console.warn('Bootstrap Modal is not available.');
        return;
    }
    const modalInstance = bootstrap.Modal.getInstance(modal);
    modalInstance.hide();
}

function initializeImageEditor(config) {
    console.log('initializeImageEditor config', config)
    const imageEditor = config.imageEditorInstance;

    if (!imageEditor) {
        console.warn('No imageEditorInstance provided.');
        return;
    }

    const {
        name,
        path,
        initiatorId,
        requiredAspectRatio,
        minDimensions,
        maxDimensions,
    } = config;

    imageEditor.setImage(name, path, initiatorId);
    const imageEditorConfig = {
        debug: false,
        rotateDegreesStep: 90,
        freeSelectDisabled: true,
        freeRotateDisabled: true,
        freeResizeDisabled: true,
        filtersDisabled: true,
        selectionAspectRatios: [requiredAspectRatio],
        selectionAspectRatio: requiredAspectRatio,
        minWidth: minDimensions.width,
        minHeight: minDimensions.height,
        maxWidth: maxDimensions.width,
        maxHeight: maxDimensions.height,
        imagePropertiesEnabled: false,
        fileFormatEnabled: false,
        rotationEnabled: true,
        mirroringEnabled: true,
        selectingEnabled: true,
        croppingEnabled: true,
        gridEnabled: false,
        downloadingEnabled: false,
        freeSelectEnabled: false,
        freeRotationEnabled: false,
        resizingEnabled: false,
        filtersEnabled: false,
        selectionInfoEnabled: false,
        selectionAspectRatioEnabled: false,
        helpEnabled: false,
    }
    imageEditor.setConfiguration(imageEditorConfig);
}

function initializeImageEditorModal(modal) {
    if (modal.dataset.mleImageEditorInitialized) return;

    const placeholder = modal.querySelector('[data-mle-image-editor-placeholder]');

    modal.addEventListener('show.bs.modal', function () {
        const imageEditorModalConfig = JSON.parse(modal.querySelector('[data-mle-image-editor-modal-config]').value);
        const mediumPath = modal.getAttribute('data-mle-medium-path');
        const displayName = modal.getAttribute('data-mle-medium-display-name');
        const forcedAspectRatio = modal.getAttribute('data-mle-medium-forced-aspect-ratio') ?? '4:3';
        const minDimensions = parseDimensions(modal.getAttribute('data-mle-medium-minimal-dimensions'), { width: 800, height: 600 });
        const maxDimensions = parseDimensions(modal.getAttribute('data-mle-medium-maximal-dimensions'), { width: 7040, height: 3960 });
        const initiatorId = imageEditorModalConfig.initiatorId;

        if (!customElements.get('image-editor')) {
            console.warn('<image-editor> custom element is not registered.');
            return;
        }

        placeholder.innerHTML = '';

        const editor = document.createElement('image-editor');
        editor.id = 'my-image-editor';

        editor.addEventListener('imageEditorReady', (e) => {
            initializeImageEditor({
                imageEditorInstance: e.detail.imageEditorInstance,
                name: displayName,
                path: mediumPath,
                initiatorId,
                requiredAspectRatio: forcedAspectRatio,
                minDimensions,
                maxDimensions,
            });
        }, { once: true });

        placeholder.appendChild(editor);
    });

    modal.addEventListener('hidden.bs.modal', function () {
        placeholder.innerHTML = '';
    });

    modal.dataset.mleImageEditorInitialized = 'true';
}

function parseDimensions(dimensionString, fallback) {
    if (!dimensionString) return fallback;
    const [w, h] = dimensionString.split(/[x:]/).map(Number);
    return { width: w || fallback.width, height: h || fallback.height };
}

// listen to preview updated to reinitialize functionality
document.addEventListener('mediaManagerPreviewsUpdated', (e) => {
    const mediaManager = e.detail.mediaManager;
    mediaManager.querySelectorAll('[data-mle-image-editor-modal]')
        .forEach(initializeImageEditorModal);
    // console.log('reinitialize image editor modals for media manager', mediaManager);
});

// Handle external close requests
document.addEventListener('imageEditorModalCloseRequest', e => {
    const modal = e.detail.modal;
    closeBootstrapModal(modal);
});

// observe dynamic models, e.g. added later on by javascript, for example in media lab when refreshing previews
const observeDynamicModals = () => {
    const observer = new MutationObserver(mutations => {
        for (const mutation of mutations) {
            for (const node of mutation.addedNodes) {
                if (!(node instanceof HTMLElement)) continue;

                // Direct modal element
                if (node.matches('[data-mle-image-editor-modal]')) {
                    initializeImageEditorModal(node);
                }

                // Nested modals inside appended fragments
                node.querySelectorAll?.('[data-mle-image-editor-modal]').forEach(initializeImageEditorModal);
            }
        }
    });

    observer.observe(document.body, { childList: true, subtree: true });
};

// Start watching
observeDynamicModals();

document.querySelectorAll('[data-mle-image-editor-modal]').forEach(initializeImageEditorModal);
