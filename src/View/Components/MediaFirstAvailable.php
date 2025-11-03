<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\View\Component;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaFirstAvailable extends BaseComponent
{
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    public ?Media $medium = null;

    public ?string $componentToRender;

    public ?string $mediumType;

    public function __construct(
        public string $id,
        public mixed $modelOrClassName,
        public ?array $collections = [],
        public array $options = [],
    ) {
        parent::__construct($id ?: null);

        $this->resolveModelOrClassName($modelOrClassName);

        if (! $this->hasCollections()) {
            throw new Exception(__('media-library-extensions::messages.no_media_collections'));
        }

        if ($this->temporaryUploadMode) {
            throw new Exception('Temporary uploads not implemented');
        }

        // pick the first available medium
        $this->medium = collect($this->collections ?? [])
            ->map(fn(string $collection) => $this->model->getFirstMedia($collection))
            ->filter()
            ->first();

        $this->mediumType = getMediaType($this->medium);
        $this->componentToRender = $this->resolveComponentForMedium($this->medium);

        $this->initializeConfig();
//        $id = filled($id) ? $id : null;
//        parent::__construct($id);
//
//        $this->resolveModelOrClassName($modelOrClassName);
//
//        // throw exception when no media collection provided at all
//        if (! $this->hasCollections()) {
//            throw new Exception(__('media-library-extensions::messages.no_media_collections'));
//        }
//
//        if (! $this->temporaryUploadMode) {
//            // Find the first medium from the ordered collections
//            $this->medium = collect($this->collections ?? [])
//                ->map(fn (string $collection) => $this->model->getFirstMedia($collection))
//                ->filter()// remove falsy values
//                ->first();
//
////            dump($this->medium);
//            $componentMap = [
//                'youtube-video' => 'mle-video-youtube',
//                'document' => 'mle-document',
//                'video' => 'mle-video',
//                'audio' => 'mle-audio',
//                'image' => 'mle-image-responsive',
//            ];
//
//            $this->mediumType = getMediaType($this->medium);
//            $this->componentToRender = $componentMap[$this->mediumType] ?? null;
//        } else {
//            throw new Exception('Temporary uploads not implemented');
//        }
//        $this->initializeConfig();

    }

    public function render(): View
    {
        return view('media-library-extensions::components.media-first-available');
    }
}
