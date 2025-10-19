// should be loaded when the custom file picker is enabled
window.mleFilePicker = (callback, value, meta) => {
    if (meta.filetype === 'image') {
        const editor = tinymce.activeEditor;
        const textarea = editor.getElement();

        const params = {
            initiator_id: textarea.getAttribute('data-initiator-id'),
            model_type: textarea.getAttribute('data-model-type'),
            model_id: textarea.getAttribute('data-model-id'),
            media_manager_id: 'myMediaManager',
            collections: {
                'image': textarea.getAttribute('data-image-collection'),
                'video': textarea.getAttribute('data-video-collection'),
                'audio': textarea.getAttribute('data-audio-collection'),
            },
            temporary_upload_mode: false, //textarea.getAttribute('temporaryUploadMode'),
            options: {

            }
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

        function handleMessage(event) {
            if (!event.data || !event.data.mce) return;

            const file = event.data.content;
            if (!file) {
                console.warn('No file');
                return;
            }
            console.log('just before callback')
            console.log('file', file);

            if (meta.filetype === 'image') {
                console.log('image', file);
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
                    console.log('data', data);
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
            // get dimensions
            // const img = new Image();
            // img.src = file.url;
            // img.onload = () => {
            //     const width = img.naturalWidth;
            //     const height = img.naturalHeight;
            //
            //     console.log('width', width, 'height', height);
            //     callback(file.url, {
            //         alt: file.alt || '',
            //         classes: 'mle-media-tinymce-image',
            //         // width: file.width || '',
            //         // height: file.height || '',
            //         // vspace: file.vspace || '',
            //         // hspace: file.hspace || '',
            //         // border: file.border || '',
            //         // borderstyle: file.borderstyle || ''
            //     });
            // };
            // img.onerror = () => {
            //     console.warn('Could not load image for dimensions');
            //     callback(file.url, {
            //         alt: file.alt || '',
            //     });
            // };

            window.removeEventListener('message', handleMessage);
            win.close();
        }

        window.addEventListener('message', handleMessage);
    }
};

window.addEventListener('message', (event) => {
    console.log('RAW postMessage seen by parent:', event);
});
