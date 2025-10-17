<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\View\Component;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaFirstAvailable extends Component
{
    use ResolveModelOrClassName;

    public ?Media $medium = null;

    public function __construct(
        public string $id = '',
        public mixed $modelOrClassName,
        public ?array $mediaCollections = [],
        public ?string $frontendTheme = null,// TODO move to options
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

        $this->frontendTheme = $frontendTheme ? $this->frontendTheme : config('media-library-extensions.frontend_theme');
    }

    public function render(): View
    {
        return view('media-library-extensions::components.media-first-available');
    }
}
