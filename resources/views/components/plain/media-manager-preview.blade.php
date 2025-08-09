@if ($temporaryUploads)
    @include('media-library-extensions::components.plain.media-manager-preview-temporary')
@else
    @include('media-library-extensions::components.plain.media-manager-preview-permanent')
@endif