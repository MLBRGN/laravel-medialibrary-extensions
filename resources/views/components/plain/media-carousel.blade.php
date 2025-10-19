@if ($getConfig('temporaryUploadMode'))
    @include('media-library-extensions::components.plain.media-carousel-temporary')
@else
    @include('media-library-extensions::components.plain.media-carousel-permanent')
@endif