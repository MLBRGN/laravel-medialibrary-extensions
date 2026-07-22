<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseMediaComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageEditorForm extends BaseMediaComponent
{
    use InteractsWithOptionsAndConfig;

    public function __construct(
        ?string $id,
        // New preferred prop; legacy supported for BC
        public mixed $modelReference = null,
        public mixed $modelOrClassName = null,// either a model that implements HasMedia or its class name
        public Media|TemporaryUpload $medium,
        public Media|TemporaryUpload|null $singleMedia,
        public array $collections,
        array $options,
        public ?bool $disabled = false,
        public ?string $dataSource = 'default'
    ) {
        // Normalize both props
        if ($this->modelReference !== null) {
            $this->modelOrClassName = $this->modelReference;
        } elseif ($this->modelOrClassName !== null) {
            $this->modelReference = $this->modelOrClassName;
        }

        parent::__construct($id, $this->modelReference, $this->modelOrClassName, $this->dataSource);

        $this->options = $options;

        $storeUpdatedMediaRoute = $this->getOption('temporaryUploadMode') ?
            route(mle_prefix_route('save-updated-temporary-upload'), $medium) :
            route(mle_prefix_route('save-updated-media'), $medium);

        $this->resolveConfig([
            //            'theme' => $this->getOption('theme', config('medialibrary-extensions.frontend_theme')),
            //            'useXhr' => config('medialibrary-extensions.use_xhr'),
            'storeUpdatedMediaRoute' => $storeUpdatedMediaRoute,
        ]);
    }

    public function render(): View
    {
        return $this->renderView('image-editor-form', $this->getConfig('theme'), true);
    }
}
