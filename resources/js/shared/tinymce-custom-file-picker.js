// should be loaded when the custom file picker is enabled
window.mleFilePicker = (callback, value, meta) => {
    if (meta.filetype === 'image') {
        const editor = tinymce.activeEditor;
        const textarea = editor.getElement();

        const temporaryUploadMode = textarea.getAttribute('data-mle-model-id') === '';
        console.log('temporaryUploadMode', temporaryUploadMode);

        const initiatorId = textarea.getAttribute('data-mle-initiator-id');
        const modelType = textarea.getAttribute('data-mle-model-type') ?? '';
        const modelId = textarea.getAttribute('data-mle-model-id');
        const mediaManagerId = 'myMediaManager';
        let collections = {};

        try {
            const attr = textarea.getAttribute('data-mle-collections');
            if (attr) collections = JSON.parse(attr);
        } catch (e) {
            console.warn('Invalid data-mle-collections JSON', e);
            return;
        }

        const params = {
            initiator_id: initiatorId,
            model_type: modelType,
            model_id: modelId,
            media_manager_id: mediaManagerId,
            collections: JSON.stringify(collections),
            temporary_upload_mode: temporaryUploadMode,
            options: JSON.stringify({
                temporaryUploadMode: temporaryUploadMode,
                frontendTheme: 'plain',
            })
        };
        // console.log(params);

        const url = new URL('/mlbrgn-mle/media-manager-tinymce', window.location.origin);
        url.search = new URLSearchParams(params).toString();

        // open the dialog
        const win = tinymce.activeEditor.windowManager.openUrl({
            title: 'Select images',
            url: url.toString(),
            buttons: [{ type: 'cancel', text: 'Close' }]
        });

        function handleMessage(event) {
            if (!event.data || !event.data.mce) return;

            const file = event.data.content;
            if (!file) {
                console.warn('No file');
                return;
            }
            // console.log('just before callback')
            // console.log('file', file);

            if (meta.filetype === 'image') {
                // console.log('image', file);
                const img = new Image();
                img.src = file.url;
                img.onload = () => {
                    const width = img.naturalWidth;
                    const height = img.naturalHeight;
                    const data = {
                        alt: file.alt || '',
                        classes: '',
                        width: width + 'px',
                        height: height + 'px',
                        vspace: file.vspace || '0px',
                        hspace: file.hspace || '0px',
                        border: file.border || '0px',
                        borderstyle: file.borderstyle || 'none',
                    };
                    // console.log('data', data);
                    callback(file.url, data);
                };
                img.onerror = () => {
                    console.warn('Could not load image for dimensions');
                    callback(file.url, {
                        alt: file.alt || '',
                    });
                };
            } else if (meta.filetype === 'media') {
                // Logic for media files
                callback(file.url, {
                    source2: file.source2 || '',
                    poster: file.poster || ''
                });
            } else if (meta.filetype === 'file') {
                // Logic for general files
                callback(file.url, {
                    text: file.name || '',
                    title: file.title || ''
                });
            }

            window.removeEventListener('message', handleMessage);
            win.close();
        }

        window.addEventListener('message', handleMessage);
    }
};

window.addEventListener('message', (event) => {
    console.log('RAW postMessage seen by parent:', event);
});
