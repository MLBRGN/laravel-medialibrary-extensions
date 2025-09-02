window.mleFilePicker = (callback, value, meta) => {
    if (meta.filetype === 'image') {
        const editor = tinymce.activeEditor;
        const textarea = editor.getElement();

        const params = {
            initiator_id: textarea.getAttribute('data-initiator-id'),
            model_type: textarea.getAttribute('data-model-type'),
            model_id: textarea.getAttribute('data-model-id'),
            media_manager_id: 'myMediaManager',
            image_collection: 'image-collection',
            video_collection: 'video-collection',
            audio_collection: 'audio-collection',
        };
        console.log(params);

        const url = new URL('/mlbrgn-mle/media-manager-tinymce', window.location.origin);
        url.search = new URLSearchParams(params).toString();

        // open the dialog
        const win = tinymce.activeEditor.windowManager.openUrl({
            title: 'Select images',
            url: url.toString(),
            buttons: [{ type: 'cancel', text: 'Close' }]
        });

        // simplified handle message only 1 file.
        function handleMessage(event) {
            if (!event.data || !event.data.mce) return;

            const file = event.data.content;
            if (!file) {
                console.warn('No file');
                return;
            }
            callback(file.url, { alt: file.alt || '' });

            window.removeEventListener('message', handleMessage);
            win.close();
        }

        window.addEventListener('message', handleMessage);
    }
};

window.addEventListener('message', (event) => {
    console.log('RAW postMessage seen by parent:', event);
});
