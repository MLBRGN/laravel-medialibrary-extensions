<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Lab;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseMediaComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// TODO dataSource?
class LabPreview extends BaseMediaComponent
{
    use InteractsWithOptionsAndConfig;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media $media,
        public string $title,
        array $options = []
    ) {
        $id = filled($id) ? $id : null;
        parent::__construct($id, $this->modelOrClassName, 'default');// TODO use default?
        $this->options = $options;

        $this->resolveConfig();
    }

    protected function domIdSuffix(): string {
        return 'lab-preview';
    }

    public function render(): View
    {
        return $this->renderView('lab.lab-preview', $this->getConfig('frontendTheme'));
    }
}
