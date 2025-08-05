// noinspection JSUnresolvedReference

document.addEventListener('DOMContentLoaded', () => {

    const modals = document.querySelectorAll('[data-image-editor-modal]');

    const initializeImageEditor  = function (detail) {
        console.log('initializeImageEditor:', detail);
        // You now have full access to the image editor instance
        const imageEditor = detail.imageEditorInstance;

        const initiatorId = imageEditor.getAttribute('data-initiator-id');
        const name = imageEditor.getAttribute('data-medium-display-name');
        const path = imageEditor.getAttribute('data-medium-path');

        // console.log('Image editor setImage:', name, path );
        imageEditor.setImage(name, path, initiatorId);

        imageEditor.setConfiguration({
            debug: false,// Image disappears when debug is true when selecting
            rotateDegreesStep: 90,
            freeSelectDisabled: true,
            freeRotateDisabled: true,
            freeResizeDisabled: true,
            filtersDisabled: true,
            selectionAspectRatios: ['16:9', '4:3'],
            selectionAspectRatio: '16:9',
        });
        // console.log(imageEditor.configuration);
    }

    modals.forEach((modal) => {
        const placeholder = modal.querySelector('#image-editor-placeholder');

        // fires immediately when the show method is called, don't wait for animations
        modal.addEventListener('show.bs.modal', function () {
            // console.log('modal show');
            // if (imageEditorInitialized) return;

            const config = JSON.parse(modal.querySelector('.image-editor-modal-config').value);
            // console.log('config', config);

            const mediumPath = modal.getAttribute('data-medium-path');//"{{ $medium->getFullUrl() }}";
            const displayName = modal.getAttribute('data-medium-display-name');// "{{ media_display_name($medium) }}";
            const initiatorId = config.initiator_id;
            const editor = document.createElement('image-editor');
            editor.setAttribute('id', 'my-image-editor');
            editor.setAttribute('data-medium-display-name', displayName);
            editor.setAttribute('data-medium-path', mediumPath);
            editor.setAttribute('data-initiator-id', initiatorId)

            editor.addEventListener('imageEditorReady', (e) => {
                console.log('Image editor is ready:', e);
                initializeImageEditor(e.detail);
            });

            placeholder.appendChild(editor);

        });

        // fires after the modal has been hidden, cleanup
        modal.addEventListener('hidden.bs.modal', function () {
            // console.log('model hidden');
            const editor = modal.querySelector('image-editor');
            placeholder.innerHTML = '';
            // TODO refactor ImageEditor and add proper cleanup one day
            // imageEditorInitialized = false;
        });
    });

});
