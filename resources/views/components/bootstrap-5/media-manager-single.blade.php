{{--        <pre>--}}
{{--        Media manager single--}}
{{--        id - {{ $id }}--}}
{{--    </pre>--}}
<div class="mlbrgn-mle-component">
    <div id="{{ $id }}" {{ $attributes->class([
            'media-manager media-manager-single',
        ]) }}>
        <x-mle-partial-debug/>
        
        @if(!empty($title))
            <h2 class="mle-heading">{{ $title }}</h2>
        @endif
    
        <div class="media-manager-row media-manager-single-row row">
            @if($uploadEnabled)
                <x-mle-partial-upload-form
                    :allowedMimeTypes="$allowedMimeTypes"
                    :mediaCollection="$mediaCollection"
                    :model="$model"
                    :id="$id"
                    :multiple="false"
                    :media-present="!is_null($medium)"/>
            @endif

            <div class="media-manager-preview-wrapper media-manager-single-preview-wrapper col-12 col-md-8 text-center">
                @if($medium)
                    <a
                        class="media-manager-preview-medium-link media-manager-single-preview-medium-link mle-cursor-zoom-in"
                        data-bs-toggle="modal"
                        data-bs-target="#{{$id}}-modal">
                        <img
                            src="{{ $medium->getUrl() }}"
                            class="media-manager-preview-medium media-manager-single-preview-medium image-fluid"
                            alt=" {{ __('media-library-extensions::messages.no_medium') }}">
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

@once
    <link rel="stylesheet" href="{{ asset('vendor/media-library-extensions/app.css') }}">
@endonce
