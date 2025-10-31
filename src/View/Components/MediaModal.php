<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Contracts\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaModal extends BaseComponent
{
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,
        public ?string $mediaCollection,
        public ?array $mediaCollections,
        public ?string $title,// TODO do i want this?
        public Media|TemporaryUpload|null $singleMedium = null, // when provided, skip collection lookups and use this medium
        public array $options = [],
        public bool $videoAutoPlay = true,

    ) {
        parent::__construct($id);

        $this->resolveModelOrClassName($modelOrClassName);

        $this->id = $this->id.'-mod';

        // merge into config
        $this->initializeConfig();
    }

    public function render(): View
    {
        return $this->getView('media-modal', $this->getConfig('frontendTheme'));
    }
}
