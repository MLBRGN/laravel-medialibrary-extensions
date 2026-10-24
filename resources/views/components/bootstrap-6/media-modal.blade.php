<dialog
    {{ $attributes->class([
        'mle-component',
        'mle-theme-'. $getConfig('theme'),
        'mle-media-modal',
        'mle-dialog',
        'dialog',
        ])->merge() }}
    id="{{ $getDomId() }}"
    @if($title)
        aria-labelledby="{{ $id }}-title"
    @endif
    @if($videoAutoPlay)
        data-mle-autoplay=""
    @endif
    data-mle-dialog
    data-mle-media-modal
>
    <div class="mle-media-modal-body mle-dialog-body dialog-body p-0">
        <button
            type="button"
            class="mle-dialog-close-button mle-media-modal-close-button"
            data-mle-dialog-close
            data-bs-dismiss="dialog"
            aria-label="{{ __('medialibrary-extensions::messages.close') }}"
            title="{{ __('medialibrary-extensions::messages.close') }}"
        >
            <x-mle-shared-icon
                name="{{ config('medialibrary-extensions.icons.close') }}"
                title="{{ __('medialibrary-extensions::messages.close') }}"
            />
        </button>
        {{-- important set expandableInModal to false otherwise endless inclusion --}}
        <x-mle-media-carousel
{{--                    class="mle-width-100 mle-height-100"--}}
            :id="$id"
            :model-reference="$modelReference"
            :single-media="$singleMedia"
            :expandable-in-modal="false"
            :collections="$collections"
            :options="$getOptions()"
            :in-modal="true"
            :preview-mode="false"
            :instance-id="$instanceId"
            :data-source="$dataSource"
            :client-token="$clientToken"
            :theme="$getConfig('theme')"
        />
    </div>
</dialog>
<x-mle-shared-assets 
    :include-css="true" 
    :include-js="true"
    :include-media-modal-js="true"
    :theme="$getConfig('theme')"
    for="bootstrap-6|media-modal"
/>

