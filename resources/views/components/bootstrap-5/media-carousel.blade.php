@if ($temporaryUpload)
    @include('media-library-extensions::components.bootstrap-5.media-carousel-temporary')
@else
    @include('media-library-extensions::components.bootstrap-5.media-carousel-permanent')
@endif