// should be loaded when the custom file picker is enabled
window.mleFilePicker = (callback, value, meta) => {
    let win = null;
    if (meta.filetype === 'image') {
        const editor = tinymce.activeEditor;
        const textarea = editor.getElement();

        const temporaryUploadMode = textarea.getAttribute('data-mle-model-id') === '';

        const mediaManagerId = textarea.getAttribute('data-mle-media-manager-id') || 'media-manager';
        const modelType = textarea.getAttribute('data-mle-model-type') ?? '';
        const modelId = textarea.getAttribute('data-mle-model-id');
        let multipleAttr = textarea.getAttribute('data-mle-multiple');
        const multiple = multipleAttr === null || multipleAttr === 'true' || multipleAttr === '';

        let collections = {};

        try {
            const attr = textarea.getAttribute('data-mle-collections');
            if (attr) collections = JSON.parse(attr);
        } catch (e) {
            console.warn('Invalid data-mle-collections JSON', e);
            editor.windowManager.alert({
                title: 'Configuration Error',
                text: 'The image picker could not start because the data-mle-collections attribute is invalid. Please check your editor setup.',
            });
            return;
        }

        const params = {
            model_type: modelType,
            model_id: modelId,
            multiple: multiple,
            media_manager_id: mediaManagerId,
            collections: JSON.stringify(collections),
            temporary_upload_mode: temporaryUploadMode,
            options: JSON.stringify({
                temporaryUploadMode: temporaryUploadMode,
                frontendTheme: 'plain',
            })
        };

        const url = new URL('/mlbrgn-mle/media-manager-tinymce', window.location.origin);
        url.search = new URLSearchParams(params).toString();

        try {
            // open the dialog
             win = editor.windowManager.openUrl({
                title: 'Select images',
                url: url.toString(),
                buttons: [{ type: 'cancel', text: 'Close' }]
            });
        } catch (err) {
            console.error('Error opening TinyMCE media manager', err);
            editor.windowManager.alert(
                'An unexpected error occurred while opening the image manager. Please try again. Error:' + err.message
            );
            return;
        }

        function handleMessage(event) {
            if (!event.data || !event.data.mce) return;

            const file = event.data.content;
            if (!file) {
                console.warn('No file');
                return;
            }

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
