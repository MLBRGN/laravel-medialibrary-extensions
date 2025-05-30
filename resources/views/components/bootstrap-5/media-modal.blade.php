{{--<pre>--}}
{{--        media-modal--}}
{{--        id - {{ $id }}--}}
{{--    </pre>--}}
<x-mle_internal-modal {{ $attributes->merge([
                'class' => 'media-manager-preview-modal'
             ]) }}
                      :id="$id"
                      title="{{ $title }}"
                      :show-header="false"
                      :no-padding="true"
                      :size-class="$sizeClass"
                      data-modal-autofocus>
    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="modal"
        aria-label="Sluit"></button>

    {{-- important set click to open in modal to false otherwise endless inclusion --}}
    <x-mle-media-carousel
        id="{{ $id }}"
        :model="$model"
        :click-to-open-in-modal="false"
        :media-collection="$mediaCollection"
        :media-collections="$mediaCollections"/>

</x-mle_internal-modal>

@once
    <link rel="stylesheet" href="{{ asset('vendor/media-library-extensions/app.css') }}">
@endonce

