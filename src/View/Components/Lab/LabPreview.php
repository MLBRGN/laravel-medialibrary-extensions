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
        // New preferred prop; legacy supported for BC
        public mixed $modelReference = null,
        public mixed $modelOrClassName = null,// either a model that implements HasMedia or its class name
        public Media $media,
        public string $title,
        array $options = [],
        public ?string $dataSource = 'default',
    ) {
        // Normalize both props for downstream blades
        if ($this->modelReference !== null) {
            $this->modelOrClassName = $this->modelReference;
        } elseif ($this->modelOrClassName !== null) {
            $this->modelReference = $this->modelOrClassName;
        }

        parent::__construct($id, $this->modelReference, $this->modelOrClassName, $dataSource);
        $this->options = $options;

        $this->resolveConfig();
    }

    protected function domIdSuffix(): string
    {
        return 'lab-preview';
    }

    public function render(): View
    {
        return $this->renderView('lab.lab-preview', $this->getConfig('theme'));
    }
}
