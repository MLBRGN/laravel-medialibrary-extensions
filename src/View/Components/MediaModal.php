<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Contracts\View\View;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;

class MediaModal extends BaseComponent
{
    use ResolveModelOrClassName;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,
        public ?string $mediaCollection,
        public ?array $mediaCollections,
        public ?string $title,// TODO do i want this?
        public ?string $frontendTheme = null,
        public bool $videoAutoPlay = true,
    ) {
        parent::__construct($id, $frontendTheme);

        $this->resolveModelOrClassName($modelOrClassName);

        $this->id = $this->id.'-mod';

    }

    public function render(): View
    {
        return $this->getView('media-modal', $this->frontendTheme);
    }
}
