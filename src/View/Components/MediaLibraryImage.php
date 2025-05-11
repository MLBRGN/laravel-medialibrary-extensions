<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaLibraryImage extends Component
{
    public Media $media;

    public string $conversion;

    public string $sizes;

    public $attributes;

    public bool $lazy;

    public function __construct(Media $media, string $conversion = '', string $sizes = '100vw', array $attributes = [], bool $lazy = true)
    {
        $this->media = $media;
        $this->conversion = $conversion;
        $this->attributes = $attributes;
        $this->sizes = $sizes;
        $this->lazy = $lazy;
    }

    public function render(): View
    {
        return view('media-library-extensions::components.library-image');
    }
}
