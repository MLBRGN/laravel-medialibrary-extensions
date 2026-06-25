<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMimeTypes;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseMediaComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// TODO $dataSource?
class UploadForm extends BaseMediaComponent
{
    use InteractsWithMimeTypes;
    use InteractsWithOptionsAndConfig;

    /** Identity of the parent MediaManager (logical ID, not suffixed) */
    public string $mediaManagerId;

    /** Identity of the parent MediaManager (DOM ID, potentially suffixed) */
    public string $mediaManagerDomId;

    public function __construct(
        ?string $id,
        ?string $mediaManagerDomId,
        public mixed $modelOrClassName,// either a model implementing HasMedia or its class name
        public Media|TemporaryUpload|null $singleMedia = null,
        public array $collections = [],
        array $options = [],
        public bool $multiple = false,
        public ?bool $readonly = false,
        public ?bool $disabled = false,
        public string $instanceId = '',
        public ?string $dataSource = 'default',
        ?string $mediaManagerId = null,
    ) {
        parent::__construct($id, $this->modelOrClassName, $dataSource);

        $this->mediaManagerId = $mediaManagerId ?? $this->id;
        $this->mediaManagerDomId = $mediaManagerDomId ?? $this->getDomId();

        $this->instanceId = InstanceManager::getInstanceId($this->mediaManagerId);

        $this->options = $options;

        $mimeData = $this->resolveAllowedMimeTypes();

        $this->resolveConfig([
            ...$mimeData,
        ]);

        $this->totalMediaCount = $this->mediaService->countMediaInCollections(
            $this->resolvedModel,
            $this->collections,
            $this->instanceId,
            $this->clientToken,
            $this->dataSource
        );
    }

    protected function domIdSuffix(): string
    {
        return 'upload-form';
    }

    public function render(): View
    {
        return $this->renderView('upload-form', $this->getConfig('frontendTheme'), true);
    }
}
