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
    <x-mle-partial-debug :theme="$theme" :model="$model"/>

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
                id="{{ $id }}"
                :initiator-id="$id"/>
        </div>

        <div class="media-manager-previews">
            <div class="media-manager-preview-grid">
{{--                @if($media->count() > 0)--}}
                    <x-mle-media-manager-preview
                        {{--                        :media="$media"--}}
                        :id="$id"
                        :show-order="$showOrder"
                        :destroy-enabled="$destroyEnabled"
                        :set-as-first-enabled="$setAsFirstEnabled"
                        :model="$model"
                        :media-collection="$mediaCollection"
                        :youtube-collection="$youtubeCollection"
                        :document-collection="$documentCollection"
                    />
{{--                @else--}}
{{--                    <div class="mlbrgn-mle-component media-manager-preview-media-container media-manager-no-media">--}}
{{--                        <span class="mle-no-media">{{ __('media-library-extensions::messages.no_media') }}</span>--}}
{{--                    </div>--}}
{{--                @endif--}}
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
        </div>
    </div>
</div>
<x-mle-partial-assets include-css="true" include-js="true"/>

