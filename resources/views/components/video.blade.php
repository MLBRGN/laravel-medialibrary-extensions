<video {{ $attributes->class('media-manager-video-preview') }} controls  preload="metadata">
    <source src="{{ $medium->getUrl() }}" type="{{ $medium->mime_type }}">
    Your browser does not support the video tag.
</video>