<div class="mlbrgn-mle-component">
    <div
        id="{{ $id }}"
        {{ $attributes->class([
            'media-manager media-manager-multiple',
        ]) }}>

        <x-mle-partial-debug/>

        <div class="media-manager-row media-manager-multiple-row">

            <div class="media-manager-form">
                @if($uploadEnabled)
                    <x-mle-partial-upload-form
                        :allowedMimeTypes="$allowedMimeTypes"
                        :mediaCollection="$mediaCollection"
                        :model="$model"
                        :id="$id"
                        :multiple="true"/>
                @endif
                @if($youtubeCollection)
                    <x-mle-partial-youtube-upload-form
                        class="mt-3"
                        :youtubeCollection="$youtubeCollection"
                        :model="$model"
                        :id="$id"
                    />
                @endif
            </div>

            <div class="media-manager-previews media-manager-multiple-previews">
                @if($media->count() > 0)
                    <div class="media-manager-preview-grid">
                        @foreach($media as $index => $medium)
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
                    
                    {{-- TODO title? --}}
                    <x-mle-media-modal
                        :id="$id"
                        :model="$model"
{{--                        :media-collection="$mediaCollection"--}}
                        :media-collections="[$mediaCollection, $youtubeCollection, $documentCollection]"
                        title="Media carousel"
                        :media="$media"
                        :inModal="true"
                        :plainJs="true" />
                @else
                    <span>{{ __('media-library-extensions::messages.no_media') }}</span>
                @endif
            </div>
        </div>
    </div>
</div>
<x-mle-partial-assets include-css="true" include-js="true"/>

