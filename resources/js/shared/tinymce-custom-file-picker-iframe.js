// this script is loaded within the tinymce iframe
// It notifies the parent TinyMCE window about the selected file
// and also provides the Media Manager instanceId so the parent
// form can submit it for correct temporary upload promotion.
const insertSelectedButton = document.getElementById('insert-selected');
insertSelectedButton.addEventListener('click', () => {
    // TinyMCE injects a global `tinymce` object in the iframe window
    // so you can do:
    const tinymce = window.parent?.tinymce;
    if (!tinymce) {
        console.error("TinyMCE not found on window.parent");
        return;
    }
    const selected = Array.from(document.querySelectorAll('[data-mle-media-select-checkbox]:checked'))
        .map(checkbox => ({
            url: checkbox.dataset.url,
            alt: checkbox.dataset.alt || '',
            vspace: '1rem',
            hspace: '1rem',
            border: 0,
            borderstyle: 'none',
        }));

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

    // Try to resolve the instanceId from the Media Manager config input
    // let instanceId = null;
    // try {
    //     const configEl = document.querySelector('.mle-media-manager-config');
    //     if (configEl && configEl.value) {
    //         const cfg = JSON.parse(configEl.value);
    //         instanceId = cfg.instanceId || null;
    //     }
    // } catch (e) {
    //     // ignore
    // }

    // TinyMCE will catch this in onMessage
    window.parent.postMessage({
        mce: true,
        // type: 'mle:picker:insert',
        // instanceId: instanceId,
        content: file
    }, '*'); // use '*' for now

});
