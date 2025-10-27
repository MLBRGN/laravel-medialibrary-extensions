<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\View\Component;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaFirstAvailable extends Component
{
    use ResolveModelOrClassName;
    use InteractsWithOptionsAndConfig;

    public ?Media $medium = null;

    public function __construct(
        public string $id,
        public mixed $modelOrClassName,
        public ?array $mediaCollections = [],
        public array $options = [],
    ) {

        $this->resolveModelOrClassName($modelOrClassName);

        if (! $this->temporaryUploadMode) {
            // Find the first medium from the ordered collections
            $this->medium = collect($this->mediaCollections ?? [])
                ->map(fn (string $collection) => $this->model->getFirstMedia($collection))
                ->filter()// remove falsy values
                ->first();
        } else {
            throw new Exception('Temporary uploads Not implemented yet');
        }
        $this->initializeConfig();

    }

    public function render(): View
    {
        return view('media-library-extensions::components.media-first-available');
    }
}
