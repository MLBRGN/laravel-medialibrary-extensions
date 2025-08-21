<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Video extends Component
{
    public string $id;

    public function __construct(
        public Media|TemporaryUpload $medium,
    ) {
        $this->id = 'video-'.Str::uuid();
    }

    public function render(): View
    {
        return view('media-library-extensions::components.video');
    }
}
