<video {{ $attributes->class('media-manager-video-preview') }} id="{{ $id }}" controls preload="metadata" data-mle-video>
    <source src="{{ $medium->getUrl() }}" type="{{ $medium->mime_type }}">
    Your browser does not support the video tag.
</video>