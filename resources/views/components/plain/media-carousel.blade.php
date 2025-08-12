@if ($temporaryUpload)
    @include('media-library-extensions::components.plain.media-carousel-temporary')
{{--    @include('media-library-extensions::components.plain.media-carousel-temporary', [--}}
{{--        'media' => $media,--}}
{{--        'id' => $id,--}}
{{--        'frontendTheme' => $frontendTheme,--}}
{{--    ])--}}
@else
    @include('media-library-extensions::components.plain.media-carousel-permanent')
{{--    @include('media-library-extensions::components.plain.media-carousel-permanent', [--}}
{{--        'media' => $media,--}}
{{--        'model' => $model,--}}
{{--        'id' => $id,--}}
{{--        'frontendTheme' => $frontendTheme,--}}
{{--    ])--}}
@endif