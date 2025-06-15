<div id="{{ $id }}" {{ $attributes->class([
        'mlbrgn-mle-component media-manager media-manager-single',
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
                    :multiple="false"/>
            @endif
        </div>

        <div class="media-manager-previews col-12 col-md-8">
            @if($medium)
                <x-mle-media-manager-preview
                    :medium="$medium"
                    :id="$id"
                    :destroy-enabled="$destroyEnabled"
                    :set-as-first-enabled="false"
                    :is-first-in-collection="true"
                    :model="$model"
                    :media-collection="$mediaCollection"
                />
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
<x-mle-partial-assets include-css="true" include-js="true" />
