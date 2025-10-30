<x-mle-lab-preview
    class="mle-media-lab-preview-original"
    title="{{ __('media-library-extensions::messages.original') }}"
    :model-or-class-name="$medium->model"
    data-media-lab-preview-original
>
    @if(method_exists($medium->model, 'getArchivedOriginalUrlFor'))
        <img src="{{ $medium->model->getArchivedOriginalUrlFor($medium) }}"
             alt=""
             class="media-preview-image"
        >
    @else
        Geen origineel opgeslagen
    @endif

    <x-slot name="menuStart">

    </x-slot>

    <x-slot name="menuEnd">
        <x-mle-partial-medium-restore-form
            :model-or-class-name="$medium->model"
            :medium="$medium"
        />
    </x-slot>
    <x-slot name="imageInfo">
        <div class="mle-info-panel">
            <div class="mle-info-row mle-info-header">
                <div>&nbsp;</div>
                <div>{{ __('media-library-extensions::messages.dimensions') }}</div>
                <div>{{ __('media-library-extensions::messages.ratio') }}</div>
            </div>

            <div class="mle-info-row">
                <div>&nbsp;</div>
                @if($imageInfo['filled'])
                    <div>{{ $imageInfo['dimensions'] ?? '?' }}</div>
                    <div>{{ $imageInfo['approx_label'] ?? '?' }}</div>
                @else
                    <div>{{ __('media-library-extensions::messages.no_original_saved') }}</div>
                    <div>&nbsp;</div>
                @endif
            </div>
            <div class="mle-info-row">
                <div>&nbsp;</div>
                <div>&nbsp;</div>
                <div>&nbsp;</div>
            </div>
            <div class="mle-info-row">
                <div>&nbsp;</div>
                <div>&nbsp;</div>
                <div>&nbsp;</div>
            </div>
            <div class="mle-info-row">
                <div>&nbsp;</div>
                <div>&nbsp;</div>
                <div>&nbsp;</div>
            </div>
        </div>
    </x-slot>
</x-mle-lab-preview>