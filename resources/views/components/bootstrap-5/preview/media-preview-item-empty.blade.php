<div 
    class="mle-component mle-media-preview-container mle-media-manager-no-media"
     data-mle-media-preview-container
    id="{{ $getDomId() }}"
>
    <span class="mle-no-media">
         @include('medialibrary-extensions::components.shared.no-media-icon')
        <p>{{ __('medialibrary-extensions::messages.no_media') }}</p>
    </span>
</div>
