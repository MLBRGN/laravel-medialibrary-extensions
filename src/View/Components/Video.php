<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Video extends Component
{
    public function __construct(
        public Media|TemporaryUpload $medium,
    ) {}

    public function render(): View
    {
        return view('media-library-extensions::components.video');
    }
}
