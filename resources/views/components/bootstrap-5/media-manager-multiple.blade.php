{{--TODO try to unify multiple with single view--}}
<div id="{{ $id }}"
    {{ $attributes->class([
        'mlbrgn-mle-component media-manager media-manager-multiple',
        'container-fluid px-0',
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
    data-theme="bootstrap-5"
    >
    <x-mle-partial-debug/>
    <div class="media-manager-row row">
        <div class="media-manager-form col-12 col-md-4">
            @if($uploadEnabled)
                <x-mle-partial-upload-form
                    :allowed-mime-types="$allowedMimeTypes" 
                    :media-collection="$mediaCollection" 
                    :document-collection="$documentCollection"
                    :youtube-collection="$youtubeCollection"
                    :destroy-enabled="$destroyEnabled"
                    :set-as-first-enabled="$setAsFirstEnabled"
                    :model="$model" 
                    :id="$id"
                    :multiple="true"
                    :use-xhr="$useXhr"
                />
            @endif
            @if($youtubeCollection)
                <x-mle-partial-youtube-upload-form
                    class="mt-3"
                    :model="$model"
                    :id="$id"
                    :media-collection="$mediaCollection"
                    :document-collection="$documentCollection"
                    :youtube-collection="$youtubeCollection"
                    :model="$model"
                    :id="$id"
                    :destroy-enabled="$destroyEnabled"
                    :set-as-first-enabled="$setAsFirstEnabled"
                    :model="$model"
                    :use-xhr="$useXhr"
                />
            @endif
        </div>

        <div class="media-manager-previews col-12 col-sm-8">
            <div class="media-manager-preview-grid">
                @if($media->count() > 0)
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
                @else
                    {{-- TODO status class? --}}
                    <span>{{ __('media-library-extensions::messages.no_media') }}</span>
                @endif
            </div>
            {{-- TODO title--}}
            <x-mle-media-modal
                :id="$id"
                :model="$model"
                :media-collections="[$mediaCollection, $youtubeCollection, $documentCollection]"
                title="Media carousel"/>
        </div>
    </div>
</div>
<x-mle-partial-assets include-css="true" include-js="true"/>
