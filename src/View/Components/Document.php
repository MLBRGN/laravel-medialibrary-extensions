<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Document extends Component
{
    public function __construct(
        public Media|TemporaryUpload $medium,
        public string $alt = ''// set alt to empty for when none provided
    ) {}

    public function render(): View
    {
        return view('media-library-extensions::components.document');
    }
}
