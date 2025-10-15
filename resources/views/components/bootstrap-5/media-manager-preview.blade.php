@if ($temporaryUploadMode)
    @include('media-library-extensions::components.bootstrap-5.media-manager-preview-temporary')
@else
    @include('media-library-extensions::components.bootstrap-5.media-manager-preview-permanent')
@endif