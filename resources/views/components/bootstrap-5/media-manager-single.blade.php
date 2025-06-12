<div class="mlbrgn-mle-component">
    <div id="{{ $id }}" {{ $attributes->class([
            'media-manager media-manager-single',
        ]) }}>
        <x-mle-partial-debug/>
    
        <div class="media-manager-row media-manager-single-row row">
            <div class="media-manager-form col-12 col-md-4">
                @if($uploadEnabled)
                    <x-mle-partial-upload-form
                        :allowedMimeTypes="$allowedMimeTypes"
                        :mediaCollection="$mediaCollection"
{{--                        :documentCollection="$documentCollection"--}}
                        :model="$model"
                        :id="$id"
                        :multiple="false"/>
                @endif
            </div>

            <div class="media-manager-previews media-manager-single-previews col-12 col-md-8 text-center">
                @if($medium)
                    <a
                        class="media-manager-preview-medium-link media-manager-single-preview-medium-link mle-cursor-zoom-in"
                        data-bs-toggle="modal"
                        data-bs-target="#{{$id}}-modal">
                        <x-mle-image-responsive
                            :medium="$medium"
                            class="media-manager-preview-medium media-manager-single-preview-medium image-fluid"
                            alt="{{ $medium->name }}"/>
                    </a>
                    <div class="media-manager-preview-menu media-manager-single-preview-menu">
                        @if($destroyEnabled)
                            <x-mle-partial-destroy-form :medium="$medium" :id="$id"/>
                        @endif
                    </div>
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
