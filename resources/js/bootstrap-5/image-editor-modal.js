// noinspection JSUnresolvedReference
import EventBus from '@evertjanmlbrgn/imageshared/EventBus.js'

console.log('just before EventBus registering');
EventBus.register('onImageSave', (e) => {
    console.log(e);
    // updateMedia(e.detail);
});

EventBus.register('onCanvasStatusMessage', (e) => {
    console.log('onCanvasStatusMessage using event bus', e.detail);
});

EventBus.register('onCloseImageEditor', (e) => {
    console.log('onCloseImageEditor using event bus', e.detail);
});

document.addEventListener('DOMContentLoaded', () => {

    const modals = document.querySelectorAll('[data-image-editor-modal]');

    modals.forEach((modal) => {

        // NOT WORKING
        // modal.addEventListener('imageEditorReady', (e) => {
        //     console.log('Image editor editor ready!!!!!!');
        // });

        // fires immediately when the show method is called, don't wait for animations
        modal.addEventListener('show.bs.modal', function () {
            console.log('Image editor shown');

            // if (imageEditorInitialized) return;

            const placeholder = modal.querySelector('#image-editor-placeholder');
            const config = JSON.parse(modal.querySelector('.image-editor-modal-config').value);

            console.log('config', config);

            const mediumPath = modal.getAttribute('data-medium-path');//"{{ $medium->getFullUrl() }}";
            const displayName = modal.getAttribute('data-medium-display-name');// "{{ media_display_name($medium) }}";
            const initiatorId = config.initiator_id;
            const editor = document.createElement('image-editor');
            editor.setAttribute('id', 'my-image-editor');
            editor.setAttribute('data-medium-display-name', displayName);
            editor.setAttribute('data-medium-path', mediumPath);
            editor.setAttribute('data-initiator-id', initiatorId)

            editor.addEventListener('imageEditorReady', (e) => {
                console.log('Caught event editor.addE...:', e.detail);
            });
            editor.shadowRoot.addEventListener('imageEditorReady', (e) => {
                console.log('Caught event editor.shadowRoot.addE..:', e.detail);
            });
            // // Optional: You can also set any other needed attributes or properties here
            placeholder.appendChild(editor);

            const testImageEditor = document.querySelector('#my-image-editor');
            console.log('testImageEditor', testImageEditor);
            testImageEditor.addEventListener('imageEditorReady', (e) => {
                console.log('Caught event: testImageEditor.addE...', e.detail);
            });
            testImageEditor.shadowRoot.addEventListener('imageEditorReady', (e) => {
                console.log('Caught event: testImageEditor.shadowRoot.addE...', e.detail);
            });
            // document.addEventListener('imageEditorReady', (e) => {
            //     console.log('Image editor editor ready!!!!!!');
            // });
            //
            // editor.addEventListener('imageEditorReady', (e) => {
            //     console.log('Image editor editor ready!!!!!!');
            // });

            // const initiatorId = imageEditor.getAttribute('data-initiator-id');
            // const name = editor.getAttribute('data-medium-display-name');
            // const path = editor.getAttribute('data-medium-path');
            //
            // console.log('Image editor setImage:', name, path );
            // editor.setImage(name, path, initiatorId);
            //
            // editor.setConfiguration({
            //     debug: false,// Image disappears when debug is true when selecting
            //     rotateDegreesStep: 90,
            //     freeSelectDisabled: true,
            //     freeRotateDisabled: true,
            //     freeResizeDisabled: true,
            //     filtersDisabled: true,
            //     selectionAspectRatios: ['16:9', '4:3'],
            //     selectionAspectRatio: '16:9',
            // });
        });


        modal.addEventListener('imageEditorReady', (e) => {
            console.log('Image editor editor ready!!!!!!');
        });

        // fires after the modal has been hidden, cleanup
        modal.addEventListener('hidden.bs.modal', function () {
            console.log('image editor hidden');
            // const placeholder = modalElement.querySelector('#image-editor-placeholder');
            // placeholder.innerHTML = '';
            // imageEditorInitialized = false;
        });

        modal.addEventListener('onImageUpdated', (e) => {
            console.log('onImageUpdated', e);
            const modalInstance = bootstrap.Modal.getInstance(modal);
            modalInstance.hide();
        })
    });

});
