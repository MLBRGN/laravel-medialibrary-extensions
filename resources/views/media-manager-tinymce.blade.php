<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Component tests: theme bootstrap-5</title>
        <link rel="icon" type="image/x-icon" href="{{ route('mle.favicon') }}">
        {{--    <link--}}
        {{--        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"--}}
        {{--        rel="stylesheet"--}}
        {{--                    integrity="sha384-QWTKZyjpPEjISv5WaRU5M6QdFVb2l9gCk0GZg6CJWjvvoE5yOAy+n9C80+XW9HdT"--}}
        {{--        crossorigin="anonymous"--}}
        {{--    >--}}
    </head>
    <body>
        {{--@dump($modelType)--}}
        {{--@dump($imageCollection)--}}
        <div class="mlbrgn-mle-component media-manager-tinymce">
            @if($temporaryUpload)
                <x-mle-media-manager-multiple
                    :model-or-class-name="$modelType"
                    id="{{ $id }}"
                    :image-collection="$imageCollection"
                    :document-collection="$documentCollection"
                    :youtube-collection="$youtubeCollection"
                    :video-collection="$videoCollection"
                    :audio-collection="$audioCollection"
                    :frontend-theme="$frontendTheme"
                    :destroy-enabled="true"
                    :set-as-first-enabled="true"
                    :show-order="false"
                    :show-menu="true"
                    :multiple="true"
                    :upload-enabled="true"
                    :selectable="true"
                />
            @else
                <x-mle-media-manager-multiple
                    :model-or-class-name="$modelType"
                    id="{{ $id }}"
                    :image-collection="$imageCollection"
                    :document-collection="$documentCollection"
                    :youtube-collection="$youtubeCollection"
                    :video-collection="$videoCollection"
                    :audio-collection="$audioCollection"
                    :frontend-theme="$frontendTheme"
                    :destroy-enabled="true"
                    :set-as-first-enabled="true"
                    :show-order="false"
                    :show-menu="true"
                    :upload-enabled="true"
                    :selectable="true"
                />
            @endif
        </div>
        <div class="mlbrgn-mle-component">
            <button type="button" id="insert-selected" class="mle-button mle-button-submit">
                Insert Selected
            </button>
        </div>
        <x-mle-shared-assets 
            include-css="true" 
            include-js="true" 
{{--            include-tinymce-custom-file-picker-js="true" --}}
            :frontend-theme="$frontendTheme"
        />
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('insert-selected').addEventListener('click', () => {
                    // TinyMCE injects a global `tinymce` object in the iframe window
                    // so you can do:
                    tinymce = window.parent.tinymce;
                    console.log('tinymce:', tinymce);

                    const selected = Array.from(document.querySelectorAll('.mle-media-select-checkbox:checked'))
                        .map(cb => ({ url: cb.dataset.url, alt: cb.dataset.alt || '' }));

                    if (!selected.length) {
                        tinymce.activeEditor.windowManager.alert('Please select at least one image.');
                        // alert('Please select at least one image.');
                        return;
                    }
                    if (selected.length > 1) {
                        tinymce.activeEditor.windowManager.alert('Please select only one image.');
                        // alert('Please select at least one image.');
                        return;
                    }
                    console.log('Selected', selected);

                    // Safer targetOrigin: parent page's origin via document.referrer
                    const targetOrigin = (() => {
                        try { return new URL(document.referrer).origin; }
                        catch { return window.location.origin; }
                    })();
                    // window.parent.postMessage(
                    //     { mce: true, content: selected },
                    //     targetOrigin
                    // );
                    // TinyMCE will catch this in onMessage
                    window.parent.postMessage({
                        mce: true,
                        content: selected
                    }, '*'); // use '*' for now

                    console.log(window.parent.tinymce?.activeEditor?.windowManager?.params)
                    // This will hit the parent’s onMessage
                    window.parent.tinymce?.activeEditor?.windowManager?.params?.onMessage({
                        content: selected
                    });
                    // then when button is clicked:
                    // tinymce.activeEditor.windowManager.postMessage({
                    //     content: selected
                    // });
                    // Send selection to TinyMCE
                    // window.parent.postMessage({ mce: true, content: selected }, '*');
                });
            });
            //     document.getElementById('insert-selected').addEventListener('click', () => {
            //         console.log('insert selected');
            //         const selected = Array.from(document.querySelectorAll('.mle-media-select-checkbox:checked'))
            //             .map(cb => ({
            //                 url: cb.dataset.url,
            //                 alt: cb.dataset.alt || ''
            //             }));
            //
            //         console.log('selected', selected);
            //
            //         if (selected.length === 0) {
            //             alert('Please select at least one image.');
            //             return;
            //         }
            //
            //         // Send selection back to TinyMCE
            //         // window.parent.postMessage({
            //         //     mce: true,
            //         //     content: selected
            //         // }, '*');
            //
            //         window.parent.postMessage({
            //             mce: true,
            //             content: selected
            //         }, window.location.origin);
            //
            //     });
        
    </script>
{{--        <script>--}}
{{--        --}}
{{--        window.addEventListener('message', (event) => {--}}
{{--            if (event.data.action === 'get-selected-media') {--}}
{{--                const selected = Array.from(--}}
{{--                    document.querySelectorAll('.mle-media-select-checkbox:checked')--}}
{{--                ).map(cb => ({--}}
{{--                    url: cb.dataset.url,--}}
{{--                    alt: cb.dataset.alt || ''--}}
{{--                }));--}}

{{--                // Post back to TinyMCE--}}
{{--                window.parent.postMessage({--}}
{{--                    mce: true,--}}
{{--                    content: selected--}}
{{--                }, '*');--}}
{{--            }--}}
{{--        });--}}
{{--    </script>--}}
    </body>
</html>