// this script is loaded within the tinymce iframe
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('insert-selected').addEventListener('click', () => {
        // TinyMCE injects a global `tinymce` object in the iframe window
        // so you can do:
        const tinymce = window.parent?.tinymce;
        if (!tinymce) {
            console.error("TinyMCE not found on window.parent");
            return;
        }
        const selected = Array.from(document.querySelectorAll('.mle-media-select-checkbox:checked'))
            .map(checkbox => ({
                url: checkbox.dataset.url,
                alt: checkbox.dataset.alt || '',
                vspace: '1rem',
                hspace: '1rem',
                border: 0,
                borderstyle: 'none',
            }));

        // const selected = Array.from(document.querySelectorAll('.mle-media-select-checkbox:checked'))
        //     .map(checkbox => ({ url: checkbox.dataset.url, alt: checkbox.dataset.alt || '' }));

        if (!selected.length) {
            tinymce.activeEditor.windowManager.alert('Please select one image.');
            return;
        }
        if (selected.length > 1) {
            tinymce.activeEditor.windowManager.alert('Please select only one image.');
            return;
        }

        // Always pick only the first selected image
        const file = selected[0];

        // Close the popup if needed
        window.close();

        // TinyMCE will catch this in onMessage
        window.parent.postMessage({
            mce: true,
            content: file
        }, '*'); // use '*' for now

    });
});
