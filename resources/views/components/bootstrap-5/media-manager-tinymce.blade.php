<div class="mlbrgn-mle-component media-manager-tinymce">
    <x-mle-media-manager-multiple
        :model-or-class-name="$modelOrClassName"
        id="{{ $id }}"
        :collections="$collections"
        :options="$options"
        :multiple="$multiple"
        :selectable="true"
    />
    <a href="#" class="btn btn-primary">test</a>
</div>
<x-mle-shared-assets
    include-css="true"
    include-js="true"
    :frontend-theme="$getConfig('frontendTheme')"
/>
<script type="module" src="{{ asset('vendor/mlbrgn/media-library-extensions/tinymce-custom-file-picker-iframe.js') }}"></script>
{{--<script type="module" src="{{ asset('vendor/mlbrgn/media-library-extensions/app-bootstrap-5.js') }}"></script>--}}
{{--<link rel="stylesheet" href="{{ asset('vendor/mlbrgn/media-library-extensions/app-bootstrap-5.css') }}">--}}
<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    {{--            integrity="sha384-QWTKZyjpPEjISv5WaRU5M6QdFVb2l9gCk0GZg6CJWjvvoE5yOAy+n9C80+XW9HdT"--}}
    crossorigin="anonymous"
>