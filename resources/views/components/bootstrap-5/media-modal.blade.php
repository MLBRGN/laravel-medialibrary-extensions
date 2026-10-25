<div
    {{ $attributes->class([
        'mle-component',
        'mle-theme-'. $getConfig('theme'),
        'mle-media-modal',
        'mle-modal',
        'modal',
        'fade',
        ])->merge() }}
    id="{{ $getDomId() }}"
    tabindex="-1"
    @if($title)
        aria-labelledby="{{ $id }}-title"
    @endif
    @if($videoAutoPlay)
        data-mle-autoplay=""
    @endif
    data-mle-modal
    data-mle-media-modal
    
    {{-- no aria-hidden!, role gets added by bs --}}
>
    <div class="mle-media-modal-dialog mle-modal-dialog modal-dialog">
        <div class="mle-media-modal-content mle-modal-content modal-content justify-content-center">
            @if($title)
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="{{ $id }}-title">{{ $title }}</h1>
                    <button 
                        type="button" 
                        class="btn-close" 
                        data-bs-dismiss="modal" 
                        data-mle-modal-close 
                        aria-label="{{ __('medialibrary-extensions::messages.close') }}"
                    ></button>
                </div>
            @else
                <h1 class="mle-modal-title mle-visually-hidden" id="{{ $id }}-title">Media</h1>
            @endif
            <div class="mle-modal-body mle-media-modal-body modal-body p-0">
                @if(!$title)
                    <button
                        type="button"
                        class="btn-close"
                        data-mle-modal-close
                        data-bs-dismiss="modal"
                        aria-label="{{ __('medialibrary-extensions::messages.close') }}"
                        title="{{ __('medialibrary-extensions::messages.close') }}"
                    >
                    </button>
                @endif
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
        </div>
    </div>
</div>
<x-mle-shared-assets 
    :include-css="true" 
    :include-js="true"
    :include-media-modal-js="true"
    :theme="$getConfig('theme')"
    for="bootstrap-5|media-modal"
/>

