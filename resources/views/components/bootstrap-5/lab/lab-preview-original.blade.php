<x-mle-lab-preview
    class="mle-media-lab-preview-original"
    title="{{ __('medialibrary-extensions::messages.original') }}"
    :model-or-class-name="$media->model"
    data-mle-media-lab-preview-original
    :options="$getOptions()"
    id="{{ $getDomId() }}"
>
    @if(method_exists($media->model, 'getArchivedOriginalUrlFor'))
        <img src="{{ $media->model->getArchivedOriginalUrlFor($media) }}"
             alt=""
             class="mle-image-responsive"
        >
    @else
        Geen origineel opgeslagen
    @endif

    <x-slot name="menuStart">

    </x-slot>

    <x-slot name="menuEnd">
        <x-mle-partial-media-restore-form
            :model-or-class-name="$media->model"
            :media="$media"
            :options="$getOptions()"
        />
    </x-slot>
    <x-slot name="imageInfo">
        <div class="mle-info-panel">
            <div class="mle-info-row mle-info-header">
                <div>&nbsp;</div>
                <div>{{ __('medialibrary-extensions::messages.dimensions') }}</div>
                <div>{{ __('medialibrary-extensions::messages.ratio') }}</div>
            </div>

            <div class="mle-info-row">
                <div>&nbsp;</div>
                @php($info = $imageInfo ?? [])
                @if(($info['filled'] ?? false) === true)
                    <div>{{ $info['dimensions'] ?? '?' }}</div>
                    <div>{{ $info['approx_label'] ?? ($info['fraction'] ?? '?') }}</div>
                @else
                    <div>{{ __('medialibrary-extensions::messages.no_original_saved') }}</div>
                    <div>&nbsp;</div>
                @endif
            </div>
            <div class="mle-info-row">
                <div>&nbsp;</div>
                <div>&nbsp;<br>&nbsp;</div>
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