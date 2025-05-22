<x-mle_internal-modal {{ $attributes->merge([
                'class' => mle_media_class('media-manager-preview-modal')
             ]) }}
             :modal-id="$modalId"
             title="{{ $title }}"
             :show-header="false"
             :no-padding="true"
             :size-class="$sizeClass"
             data-modal-autofocus>
    <button
        type="button"
        class="@mediaClass('button-close')"
        data-bs-dismiss="modal"
        aria-label="Sluit"></button>

    <x-mle_internal-media-preview-carousel
         id="{{ $modalId }}-carousel"
        :model="$model"
        :media-collection-name="$mediaCollectionName"/>

</x-mle_internal-modal>


{{--TODO--}}
{{--<script src="https://www.youtube.com/iframe_api"></script>--}}
{{--@once--}}
{{--    @vite('resources/js/modules/mediaPreviewModal.js')--}}
{{--@endonce--}}
{{--<script>--}}
{{--    @php--}}
{{--        $jsFilePath = public_path('js/vendor/media-library-extensions/mediaPreviewModal.js');--}}
{{--    @endphp--}}
{{--    @if (file_exists($jsFilePath))--}}
{{--        {!! file_get_contents($jsFilePath) !!}--}}
{{--        console.log('found published js')--}}
{{--    @else--}}
{{--        console.log("JavaScript file not found, using inline fallback.");--}}
{{--        // Your fallback JS code here--}}
{{--        // alert("Fallback JS loaded");--}}
{{--    @endif--}}
{{--    {!! file_get_contents(__DIR__ . '/../../js/mediaPreviewModal.js') !!}--}}
{{--</script>--}}
{{--<script src="{{ asset('mlbrgn/spatie-media-library-extensions/mediaPreviewModal.js') }}"></script>--}}
@once
    <link rel="stylesheet" href="{{ mle_package_asset('media-library-extensions.css') }}">
{{--{{ \Illuminate\Support\Facades\Vite::useHotFile('vendor/media-library-extensions/media-library-extensions.hot')--}}
{{--    ->useBuildDirectory("vendor/media-library-extensions")--}}
{{--    ->withEntryPoints(['resources/css/app.scss', 'resources/js/app.js']) }}--}}

{{--<link href="{{ asset('media-library-extensions/css/app.css') }}" rel="stylesheet" />--}}
@endonce
