<div
    id="{{ $id }}"
    {{ $attributes->class([
        'mlbrgn-mle-component media-manager media-manager-multiple',
    ]) }}
    data-media-manager=""
    data-media-upload-route="{{ $mediaUploadRoute }}"
    data-preview-refresh-route="{{ $previewRefreshRoute }}"
    data-media-youtube-upload-route="{{ $youtubeUploadRoute }}"
    data-model-type="{{ $model->getMorphClass() }}"
    data-model-id="{{ $model->getKey() }}"
    data-collection="{{ $mediaCollection }}"
    data-youtube-collection="{{ $youtubeCollection }}"
    data-document-collection="{{ $documentCollection }}"
    data-destroy-enabled="{{ $destroyEnabled ? 'true' : 'false' }}"
    data-set-as-first-enabled="{{ $setAsFirstEnabled ? 'true' : 'false' }}"
    data-csrf-token="{{ csrf_token() }}"
    data-theme="{{ $theme }}"
>

    <x-mle-partial-debug/>

    <div class="media-manager-row">

        <div class="media-manager-form">
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
                <hr>
                <x-mle-partial-youtube-upload-form
                    class="mt-3"
                    :youtube-collection="$youtubeCollection"
                    :model="$model"
                    :id="$id"
                />
            @endif
            <x-mle-partial-status-area
                id="{{ $id }}-status"
                :target-id="$id"/>
        </div>

        <div class="media-manager-previews">
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
<x-mle-partial-assets include-css="true" include-js="true"/>

