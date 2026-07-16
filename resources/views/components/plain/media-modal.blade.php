<div
    {{ $attributes->class([
        'mle-component',
        'mle-theme-'. $getConfig('theme'),
        'mle-media-modal',
        'mle-modal',
        'mle-fade',
        ])->merge() }}
    id="{{ $getDomId() }}"
    tabindex="-1"
    role="dialog"
    @if($title)
        aria-labelledby="{{ $id }}-title"
    @endif
    aria-hidden="true"
    @if($videoAutoPlay)
        data-mle-autoplay=""
    @endif
    data-mle-modal
    data-mle-media-modal
>
    <div class="mle-modal-dialog mle-media-modal-dialog">
        <div class="mle-modal-content mle-media-modal-content">
            @if($title)
                <h1 class="mle-modal-title mle-media-modal-title mle-visually-hidden" id="{{ $getDomId() }}-title">{{ $title }}</h1>
            @endif
            <div class="mle-modal-body mle-media-modal-body">
                <button
                    type="button"
                    class="mle-modal-close-button mle-media-modal-close-button"
                    data-mle-modal-close
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
                    class="mle-width-100 mle-height-100"
                    id="{{ $getDomId() }}" {{-- append to media modal id (by using $getDomId()) here, otherwise id clash --}}
                    :model-or-class-name="$modelOrClassName"
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
        </div>
    </div>
</div>
<x-mle-shared-assets 
    :include-css="true" 
    :include-js="true"
    :include-media-modal-js="true"
    :theme="$getConfig('theme')"
    for="plain|media-modal"
/>

