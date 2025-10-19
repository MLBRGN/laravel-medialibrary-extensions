<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Contracts\View\View;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;

class MediaModal extends BaseComponent
{
    use ResolveModelOrClassName;
    use InteractsWithOptionsAndConfig;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,
        public ?string $mediaCollection,
        public ?array $mediaCollections,
        public ?string $title,// TODO do i want this?
        public array $options = [],
        public bool $videoAutoPlay = true,

    ) {
        parent::__construct($id, $this->getOption('frontendTheme'));

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
