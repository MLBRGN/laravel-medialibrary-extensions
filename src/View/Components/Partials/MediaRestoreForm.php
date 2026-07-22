<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use InvalidArgumentException;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseMediaComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// TODO dataSource?
class MediaRestoreForm extends BaseMediaComponent
{
    use InteractsWithOptionsAndConfig;

    public string $mediumRestoreRoute;

    public function __construct(
        ?string $id,
        // New preferred prop; legacy supported for BC
        public mixed $modelReference = null,
        public mixed $modelOrClassName = null,// either a model that implements HasMedia or its class name
        public Media|TemporaryUpload $media,
        public Media|TemporaryUpload|null $singleMedia = null,
        public array $collections = [],
        array $options = [],
        public ?bool $disabled = false,
        public ?string $dataSource = 'default'
    ) {
        // Normalize both props for downstream blades
        if ($this->modelReference !== null) {
            $this->modelOrClassName = $this->modelReference;
        } elseif ($this->modelOrClassName !== null) {
            $this->modelReference = $this->modelOrClassName;
        }

        parent::__construct($id, $this->modelReference, $this->modelOrClassName, $this->dataSource);

        $this->options = $options;

        if ($this->temporaryUploadMode) {
            throw new InvalidArgumentException(__('medialibrary-extensions::messages.temporary_upload_original_cannot_be_restored'));
        } else {
            $mediaRestoreRoute = route(
                mle_prefix_route('restore-original-medium'),
                ['mediaId' => $media->id]
            );
        }

        $this->mediumRestoreRoute = $mediaRestoreRoute;

        $this->resolveConfig();

        $this->setConfig('routes.mediumRestore', $this->mediumRestoreRoute);
    }

    protected function domIdSuffix(): string
    {
        return 'media-restore-form-'.$this->media->id;
    }

    public function render(): View
    {
        return $this->renderView('media-restore-form', $this->getConfig('theme'), true);
    }
}
