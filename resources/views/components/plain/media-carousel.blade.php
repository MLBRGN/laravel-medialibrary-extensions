@if ($getConfig('temporaryUploadMode'))
    @include('medialibrary-extensions::components.plain.media-carousel-temporary')
@else
    @include('medialibrary-extensions::components.plain.media-carousel-permanent')
@endif