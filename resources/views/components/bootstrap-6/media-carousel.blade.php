@if ($getConfig('temporaryUploadMode'))
    @include('medialibrary-extensions::components.bootstrap-6.media-carousel-temporary')
@else
    @include('medialibrary-extensions::components.bootstrap-6.media-carousel-permanent')
@endif
