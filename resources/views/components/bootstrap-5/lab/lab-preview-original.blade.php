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
</x-mle-lab-preview>