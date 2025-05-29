{{--This file will handle shared logic and delegate some UI to a partial.--}}
<div id="{{ $id }}" {{ $attributes->class([
        'media-manager-single-wrapper',
        'mlbrgn-mle-component'
    ]) }}>
    
    <x-mle_internal-debug/>
    
    @if(!empty($title))
        <h2 class="media-manager-heading">{{ $title }}</h2>
    @endif

    <div class="media-manager-row media-manager-single-row row">
        @if($uploadEnabled)
            <x-mle_internal-media-manager-upload-form
                :allowedMimeTypes="$allowedMimeTypes"
                :mediaCollection="$mediaCollection"
                :model="$model"
                :id="$id"
                :multiple="false"
                :media-present="!is_null($medium)"/>
            @if(!$medium)
                <p class="media-manager-no-media">
                    {{ __('media-library-extensions::messages.no-medium') }}
                </p>
            @endif
        @endif

        @if($medium)
            <div class="media-manager-preview-wrapper media-manager-single-preview-wrapper col-12 col-md-8 text-center">
                <a
                    class="media-manager-preview-medium-link media-manager-single-preview-medium-link cursor-zoom-in"
                    data-bs-toggle="modal"
                    data-bs-target="#{{$modalId}}">
                    <img
                        src="{{ $medium->getUrl() }}"
                        class="media-manager-preview-medium media-manager-single-preview-medium image-fluid"
                        alt=" {{ __('media-library-extensions::messages.no-medium') }}">
                </a>
                <div class="media-manager-preview-menu media-manager-single-preview-menu">
                    @if($destroyEnabled)
                        <x-mle_internal-media-manager-destroy-form :medium="$medium" :id="$id"/>
                    @endif
                </div>
                <x-mle-media-previewer-modal
                    :id="$modalId"
                    :model="$model"
                    :media-collection="$mediaCollection"
                    title="Media carousel"/>
            </div>
        @endif
    </div>

    @if(!$uploadEnabled && !$medium)
        <span>{{ __('media-library-extensions::messages.no-medium') }}</span>
    @endif
</div>

@once
    <link rel="stylesheet" href="{{ asset('vendor/media-library-extensions/app.css') }}">
@endonce
