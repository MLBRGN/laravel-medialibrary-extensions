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

        const url = new URL('/mlbrgn-mle/media-manager-tinymce', window.location.origin);
        url.search = new URLSearchParams(params).toString();

        // open the dialog
        const win = tinymce.activeEditor.windowManager.openUrl({
            title: 'Select images',
            url: url.toString(),
            buttons: [{ type: 'cancel', text: 'Close' }]
        });

        // fallback: handle postMessage directly
        // WORKING CODE!!! 1 insert at a time
        function handleMessage(event) {
            if (!event.data || !event.data.mce) return; // only accept our messages

            const message = event.data;
            console.log('Fallback message received:', message);

            // TODO message content is always an array, pick the first?
            if (Array.isArray(message.content)) {
                message.content.forEach(file => {
                    callback(file.url, { alt: file.alt || '' });
                });
            } else if (message.content?.url) {
                callback(message.content.url, { alt: message.content.alt || '' });
            }

            window.removeEventListener('message', handleMessage);
            win.close();
        }

        // function handleMessage(event) {
        //     if (!event.data || !event.data.mce) return; // only accept our messages
        //
        //     const message = event.data;
        //     console.log('Fallback message received:', message);
        //
        //     const editor = tinymce.activeEditor;
        //
        //     if (Array.isArray(message.content)) {
        //         // Multiple images → insert directly into editor
        //         const html = message.content
        //             .map(file => `<img src="${file.url}" alt="${file.alt || ''}">`)
        //             .join('');
        //         editor.insertContent(html);
        //     } else if (message.content?.url) {
        //         // Single image → let TinyMCE’s callback handle it
        //         callback(message.content.url, { alt: message.content.alt || '' });
        //     }
        //
        //     window.removeEventListener('message', handleMessage);
        //     win.close();
        // }

        window.addEventListener('message', handleMessage);
    }
};

// window.mleFilePicker = (callback, value, meta) => {
//     console.log('value', value);
//     console.log('meta', meta);
//
//     if (meta.filetype === 'image') {
//         const editor = tinymce.activeEditor;
//         const textarea = editor.getElement();
//
//         const params = {
//             initiator_id: textarea.getAttribute('data-initiator-id'),
//             model_type: textarea.getAttribute('data-model-type'),
//             model_id: textarea.getAttribute('data-model-id'),
//             media_manager_id: 'myMediaManager',
//             image_collection: 'image-collection',
//             video_collection: 'video-collection',
//             audio_collection: 'audio-collection',
//         };
//
//         const url = new URL('/mlbrgn-mle/media-manager-tinymce', window.location.origin);
//         url.search = new URLSearchParams(params).toString();
//
//         tinymce.activeEditor.windowManager.openUrl({
//             title: 'Select images',
//             url: url.toString(),
//             buttons: [
//                 {
//                     type: 'cancel',
//                     text: 'Close'
//                 }
//             ],
//             onMessage: (api, message) => {
//                 console.log('message from iframe', message);
//
//                 if (Array.isArray(message.content)) {
//                     message.content.forEach(file => {
//                         callback(file.url, { alt: file.alt || '' });
//                     });
//                 } else if (message.content?.url) {
//                     callback(message.content.url, { alt: message.content.alt || '' });
//                 }
//                 api.close();
//             }
//             // onMessage: (api, message) => {
//             //     console.log('message from iframe', message);
//             //
//             //     if (Array.isArray(message.content)) {
//             //         // multiple selected
//             //         message.content.forEach(file => {
//             //             callback(file.url, { alt: file.alt || '' });
//             //         });
//             //     } else if (message.content?.url) {
//             //         // single file
//             //         callback(message.content.url, { alt: message.content.alt || '' });
//             //     }
//             //
//             //     api.close();
//             // }
//         });
//     }
// };


window.addEventListener('message', (event) => {
    console.log('RAW postMessage seen by parent:', event);
});

// Inside your iframe (media manager) call this when done:
function sendSelectedMediaToTinyMCE(api) {
    const selected = Array.from(
        document.querySelectorAll('.mle-media-select-checkbox:checked')
    ).map(cb => ({
        url: cb.dataset.url,
        alt: cb.dataset.alt || ''
    }));

    if (selected.length === 0) {
        alert('Please select at least one image.');
        return;
    }

    // This sends the message back to TinyMCE’s onMessage handler
    window.parent.postMessage({
        mce: true,
        content: selected
    }, '*');
}


// window.mleFilePicker = (callback, value, meta) => {
//     console.log('value', value);
//     console.log('meta', meta);
//     const editor = tinymce.activeEditor;
//     const textarea = editor.getElement();
//     console.log('Textarea element:', textarea);
//
//     if (meta.filetype === 'image') {
//         const params = {
//             initiator_id: textarea.getAttribute('data-initiator-id'),
//             model_type: textarea.getAttribute('data-model-type'),
//             model_id: textarea.getAttribute('data-model-id'),
//             media_manager_id: 'myMediaManager',
//             image_collection: 'image-collection',
//             video_collection: 'video-collection',
//             audio_collection: 'audio-collection',
//         };
//
//         const url = new URL('/mlbrgn-mle/media-manager-tinymce', window.location.origin);
//         url.search = new URLSearchParams(params).toString();
//
//         const win = tinymce.activeEditor.windowManager.openUrl({
//             title: 'Select images',
//             url: url.toString(),
//             buttons: [
//                 // {
//                 //     type: 'custom',
//                 //     text: 'Insert Selected',
//                 //     primary: true,
//                 //     // onclick: (api) => {
//                 //     onAction: (api) => {
//                 //         console.log('on action', api);
//                 //         // sendSelectedMediaToTinyMCE(api);
//                 //         // Send a "get-selected-media" request to the iframe
//                 //         const iframe = document.querySelector('iframe.tox-dialog__iframe');
//                 //         iframe.contentWindow.postMessage({ action: 'get-selected-media' }, '*');
//                 //     }
//                 // },
//
//                 {
//                     type: 'cancel',
//                     text: 'Close'
//                 }
//             ],
//             onMessage: (api, message) => {
//                 console.log('message', message);
//                 if (Array.isArray(message.content)) {
//                     message.content.forEach(file => callback(file.url, { alt: file.alt }));
//                 } else if (message.content?.url) {
//                     callback(message.content.url, { alt: message.content.alt || '' });
//                 }
//                 api.close();
//             }
//             // onMessage: (api, message) => {
//             //     if (Array.isArray(message.content)) {
//             //         // Multiple images selected → insert all
//             //         message.content.forEach(file => {
//             //             callback(file.url, { alt: file.alt });
//             //         });
//             //     } else {
//             //         // Single image fallback
//             //         callback(message.content.url, { alt: message.content.alt || '' });
//             //     }
//             //     api.close();
//             // }
//         });
//
//         function handleMessage(event) {
//             if (event.origin !== window.location.origin) return;
//
//             const data = event.data;
//             if (data.mce && data.content && data.content.length > 0) {
//                 const image = data.content[0]; // take the first image
//                 callback(image.url, { alt: image.alt || '' });
//
//                 // cleanup: stop listening and hide modal
//                 window.removeEventListener('message', handleMessage);
//                 // modal.style.display = 'none';
//             }
//         }
//
//         window.addEventListener('message', handleMessage);
//
//         // iframe.contentWindow.postMessage({ action: 'prepare-selection' }, window.location.origin);
//     }
// };
//
//
// // your function stays as is:
// function sendSelectedMediaToTinyMCE(api) {
//     console.log('sendSelectedMediaToTinyMCE', api);
//     const selected = Array.from(
//         document.querySelectorAll('.mle-media-select-checkbox:checked')
//     ).map(cb => ({
//         url: cb.dataset.url,
//         alt: cb.dataset.alt || ''
//     }));
//
//     if (selected.length === 0) {
//         alert('Please select at least one image.');
//         return;
//     }
//
//     window.parent.postMessage({
//         mce: true,
//         content: selected
//     }, '*');
//
//     api.close();
// }


// window.mleFilePicker = (callback, value, meta) => {
//
//     console.log('value', value)
//     console.log('meta', meta)
//     const editor = tinymce.activeEditor;
//     const textarea = editor.getElement();
//     console.log('Textarea element:', textarea);
//
//     if (meta.filetype === 'image') {
//
//         const params = {
//             initiator_id: textarea.getAttribute('data-initiator-id'),
//             model_type: textarea.getAttribute('data-model-type'),
//             model_id: textarea.getAttribute('data-model-id'),
//             media_manager_id: 'myMediaManager',
//             image_collection: 'image-collection',
//             video_collection: 'video-collection',
//             audio_collection: 'audio-collection',
//         };
//
//         const url = new URL('/mlbrgn-mle/media-manager-tinymce', window.location.origin);
//         url.search = new URLSearchParams(params).toString();                // Example: open your own modal / browse dialog
//         console.log('url', url.toString());
//         const win = tinymce.activeEditor.windowManager.openUrl({
//             title: 'Select an image',
//             url: url.toString(), // <-- your custom media manager route
//             buttons: [
//                 {
//                     type: 'cancel',
//                     text: 'Close'
//                 }
//             ],
//             onMessage: (api, message) => {
//                 // message should contain the selected file URL
//                 callback(message.content, {alt: ''});
//                 api.close();
//             }
//         });
//     }
// }

function getImageList(success) {
    // Example: fetch from Laravel route
    fetch('/media/images/list')
        .then(r => r.json())
        .then(data => success(data));
}
