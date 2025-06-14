{{-- 
mlbrgn-mle-component media-manager media-manager-multiple
    media-manager-row
        media-manager-form
        media-manager-previews
--}}


<div
    id="{{ $id }}"
    {{ $attributes->class([
        'mlbrgn-mle-component media-manager media-manager-multiple',
        'container-fluid px-0',
    ]) }}>
    <x-mle-partial-debug/>
    <div class="media-manager-row row">
        <div class="media-manager-form col-12 col-md-4">
            @if($uploadEnabled)
                <x-mle-partial-upload-form
                    :allowed-mime-types="$allowedMimeTypes" 
                    :media-collection="$mediaCollection" 
                    :document-collection="$documentCollection"
                    :model="$model" 
                    :id="$id"
                    :multiple="true"/>
            @endif
            @if($youtubeCollection)
                <x-mle-partial-youtube-upload-form
                    class="mt-3"
                    :youtube-collection="$youtubeCollection"
                    :model="$model"
                    :id="$id"
                />
            @endif
        </div>

        <div class="media-manager-previews col-12 col-sm-8">
            @if($media->count() > 0)
                {{-- Preview of all images in grid --}}
                    <div class="media-manager-preview-grid">
                        @foreach($media as $medium)
                            <x-mle-media-manager-preview 
                                :medium="$medium" 
                                :id="$id" 
                                loop-index="{{ $loop->index }}"
                                :show-order="$showOrder"
                                :destroy-enabled="$destroyEnabled"
                                :set-as-first-enabled="$setAsFirstEnabled"
                                :is-first-in-collection="$medium->order_column === $media->min('order_column')"
                                :model="$model"
                                :media-collection="$mediaCollection"
                            />
                        @endforeach
                    </div>

                    {{-- TODO title--}}
                    <x-mle-media-modal
                        :id="$id"
                        :model="$model"
                        :media-collections="[$mediaCollection, $youtubeCollection, $documentCollection]"
                        title="Media carousel"/>
            @else
                {{-- TODO status class? --}}
                <span>{{ __('media-library-extensions::messages.no_media') }}</span>
            @endif
        </div>
    </div>
</div>
<x-mle-partial-assets include-css="true" include-js="true"/>
