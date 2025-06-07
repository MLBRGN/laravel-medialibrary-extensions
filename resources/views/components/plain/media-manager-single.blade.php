<div class="mlbrgn-mle-component">
    <div id="{{ $id }}" {{ $attributes->class([
            'media-manager media-manager-single',
        ]) }}>
        <x-mle-partial-debug/>

        <div class="media-manager-row media-manager-single-row">
            <div class="media-manager-form">
                @if($uploadEnabled)
                    <x-mle-partial-upload-form
                        :allowedMimeTypes="$allowedMimeTypes"
                        :mediaCollection="$mediaCollection"
                        :model="$model"
                        :id="$id"
                        :multiple="false"/>
                @endif
            </div>

            <div class="media-manager-preview-wrapper media-manager-single-preview-wrapper">
                @if($medium)
                    <a
                        class="media-manager-preview-medium-link media-manager-single-preview-medium-link mle-cursor-zoom-in"
                        data-modal-id="{{ $id }}-modal"
                        data-slide-to="0"
                        data-modal-trigger="{{ $id }}-modal">
                        <x-mle-image-responsive 
                            :medium="$medium" 
                            class="media-manager-preview-medium media-manager-single-preview-medium" 
                            alt="{{ $medium->name }}"/>
                    </a>
                    <div class="media-manager-preview-menu media-manager-single-preview-menu">
                        @if($destroyEnabled)
                            <x-mle-partial-destroy-form :medium="$medium" :id="$id"/>
                        @endif
                    </div>

                    {{-- JS-Driven Modal --}}
                    <x-mle-media-modal
                        :id="$id"
                        :model="$model"
                        :media-collection="$mediaCollection"
                        :media="collect([$medium])"
                        :inModal="true"
                        :plainJs="true"
                        title="Media carousel"/>
                @else
                    <span>{{ __('media-library-extensions::messages.no_medium') }}</span>
                @endif
            </div>
        </div>
    </div>
</div>
<x-mle-partial-assets include-css="true" include-js="true" />
