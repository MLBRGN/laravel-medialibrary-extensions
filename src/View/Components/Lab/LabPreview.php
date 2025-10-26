<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Lab;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class LabPreview extends BaseComponent
{
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media $medium,
        public string $title
    )
    {
        $id = filled($id) ? $id : null;
        parent::__construct($id);

        $this->resolveModelOrClassName($modelOrClassName);
        $this->initializeConfig();

    }

    public function render(): View
    {
        return $this->getView('lab.lab-preview', $this->getConfig('frontendTheme'));
    }
}
