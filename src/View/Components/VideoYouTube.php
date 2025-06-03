<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class VideoYouTube extends Component
{
    public function __construct(
        public Media $medium,
        public bool $preview = true,
        public string $youtubeId = '',
    ) {}

    public function render(): View
    {
        return view('media-library-extensions::components.video-youtube');
    }
}
