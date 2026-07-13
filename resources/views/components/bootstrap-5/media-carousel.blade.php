@if ($getConfig('temporaryUploadMode'))
    @include('medialibrary-extensions::components.bootstrap-5.media-carousel-temporary')
@else
    @include('medialibrary-extensions::components.bootstrap-5.media-carousel-permanent')
@endif
