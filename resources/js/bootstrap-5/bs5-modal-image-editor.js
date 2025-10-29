// noinspection JSUnresolvedReference
const closeBootstrapModal = (modal) => {
    if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
        console.warn('Bootstrap Modal is not available.');
        return;
    }
    const modalInstance = bootstrap.Modal.getInstance(modal);
    modalInstance.hide();
}

function initializeImageEditor(detail) {
    const imageEditor = detail.imageEditorInstance;

    const initiatorId = imageEditor.getAttribute('data-initiator-id');
    const name = imageEditor.getAttribute('data-medium-display-name');
    const path = imageEditor.getAttribute('data-medium-path');
    const aspectRatio = imageEditor.getAttribute('data-medium-required-aspect-ratio') ?? '16:9';

    imageEditor.setImage(name, path, initiatorId);

    imageEditor.setConfiguration({
        debug: false,
        rotateDegreesStep: 90,
        freeSelectDisabled: true,
        freeRotateDisabled: true,
        freeResizeDisabled: true,
        filtersDisabled: true,
        selectionAspectRatios: [aspectRatio],
        // selectionAspectRatio: aspectRatio,
    });
}

function initializeImageEditorModal(modal) {
    if (modal.dataset.imageEditorInitialized) return;

    const placeholder = modal.querySelector('[data-image-editor-placeholder]');

    modal.addEventListener('show.bs.modal', function () {
        const imageEditorModalConfig = JSON.parse(modal.querySelector('[data-image-editor-modal-config]').value);
        console.log('image editor modal config', imageEditorModalConfig);
        const mediumPath = modal.getAttribute('data-medium-path');
        const displayName = modal.getAttribute('data-medium-display-name');
        const requiredAspectRatio = modal.getAttribute('data-medium-required-aspect-ratio');
        const initiatorId = imageEditorModalConfig.initiatorId;

        console.log('requiredAspectRatio', requiredAspectRatio);

        const editor = document.createElement('image-editor');
        editor.setAttribute('id', 'my-image-editor');
        editor.setAttribute('data-medium-display-name', displayName);
        editor.setAttribute('data-medium-path', mediumPath);
        editor.setAttribute('data-initiator-id', initiatorId)
        editor.setAttribute('data-aspect-ratio', requiredAspectRatio)

        editor.addEventListener('imageEditorReady', (e) => {
            initializeImageEditor(e.detail);
        });

        placeholder.appendChild(editor);
    });

    modal.addEventListener('hidden.bs.modal', function () {
        placeholder.innerHTML = '';
    });

    // Mark as initialized
    modal.dataset.imageEditorInitialized = 'true';
}

// listen to preview updated to reinitialize functionality
document.addEventListener('mediaManagerPreviewsUpdated', (e) => {
    const mediaManager = e.detail.mediaManager;
    mediaManager.querySelectorAll('[data-image-editor-modal]')
        .forEach(initializeImageEditorModal);
    // console.log('reinitialize image editor modals for media manager', mediaManager);
});

document.addEventListener('imageEditorModalCloseRequest', (e) => {
    const modal = e.detail.modal;
    // console.log('imageEditorModalCloseRequest', modal);
    closeBootstrapModal(modal);
});

// observe dynamic models, e.g. added later on by javascript, for example in media lab when refreshing previews
const observeDynamicModals = () => {
    const observer = new MutationObserver(mutations => {
        for (const mutation of mutations) {
            for (const node of mutation.addedNodes) {
                if (!(node instanceof HTMLElement)) continue;

                // Direct modal element
                if (node.matches('[data-image-editor-modal]')) {
                    initializeImageEditorModal(node);
                }

                // Nested modals inside appended fragments
                node.querySelectorAll?.('[data-image-editor-modal]').forEach(initializeImageEditorModal);
            }
        }
    });

    observer.observe(document.body, { childList: true, subtree: true });
};

// Start watching
observeDynamicModals();

document.querySelectorAll('[data-image-editor-modal]').forEach(initializeImageEditorModal);
