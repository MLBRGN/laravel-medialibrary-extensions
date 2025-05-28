<x-mle_internal-modal {{ $attributes->merge([
                'class' => 'media-manager-preview-modal'
             ]) }}
                      :modal-id="$modalId"
                      title="{{ $title }}"
                      :show-header="false"
                      :no-padding="true"
                      :size-class="$sizeClass"
                      data-modal-autofocus>
    <button
        type="button"
        class="btn-close"
        aria-label="Sluit"></button>

    {{-- important set click to open in modal to false otherwise endless inclusion --}}
    <x-mle-media-previewer
        id="{{ $modalId }}-carousel"
        :model="$model"
        :click-to-open-in-modal="false"
        :media-collection="$mediaCollection"/>

</x-mle_internal-modal>

@once
    {{--    <link rel="stylesheet" href="{{ mle_package_asset('media-library-extensions.css') }}">--}}
    {{--{{ \Illuminate\Support\Facades\Vite::useHotFile('vendor/media-library-extensions/media-library-extensions.hot')--}}
    {{--    ->useBuildDirectory("vendor/media-library-extensions")--}}
    {{--    ->withEntryPoints(['resources/css/app.scss', 'resources/js/app.js']) }}--}}

    {{--<link href="{{ asset('media-library-extensions/css/app.css') }}" rel="stylesheet" />--}}
@endonce
