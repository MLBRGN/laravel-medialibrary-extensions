{{--TODO try to unify multiple with single view--}}
<div id="{{ $id }}" 
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
                    :youtube-collection="$youtubeCollection"
                    :model="$model"
                    :id="$id"
                    :multiple="false"
                    :use-xhr="$useXhr"
                />
            @endif
            @if($youtubeCollection)
                <x-mle-partial-youtube-upload-form
                    class="mt-3"
                    :youtube-collection="$youtubeCollection"
                    :model="$model"
                    :id="$id"
                    :use-xhr="$useXhr"
                />
            @endif
        </div>

        <div class="media-manager-previews col-12 col-md-8">
            <div class="media-manager-preview-grid">
                @if($media->count() > 0)
                    <x-mle-media-manager-preview
                        :media="$media"
                        :id="$id"
                        :show-order="false"
                        :destroy-enabled="$destroyEnabled"
                        :set-as-first-enabled="false"
                        :model="$model"
                        :media-collection="$mediaCollection"
                    />
                    {{-- TODO title--}}
                    <x-mle-media-modal
                        :id="$id"
                        :model="$model"
                        :media-collection="$mediaCollection"
                        title="Media carousel"/>
                @else
                    {{-- TODO status class? --}}
                    <span>{{ __('media-library-extensions::messages.no_medium') }}</span>
                @endif
            </div>
        </div>
    </div>
</div>
<x-mle-partial-assets include-css="true" include-js="true" />
