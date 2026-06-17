<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMimeTypes;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// TODO $dataSource?
class UploadForm extends BaseComponent
{
    use InteractsWithMimeTypes;
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    public ?string $mediaManagerId = '';

    //    public array $config = [];

    public function __construct(
        ?string $id,
        ?string $mediaManagerId,
        public mixed $modelOrClassName,// either a model implementing HasMedia or its class name
        public Media|TemporaryUpload|null $singleMedia = null,
        public array $collections = [],
        array $options = [],
        public bool $multiple = false,
        public ?bool $readonly = false,
        public ?bool $disabled = false,
        public string $instanceId = '',
    ) {
        parent::__construct($id);

        $this->mediaManagerId = $mediaManagerId ?? $this->originalId;

        // Ensure instanceId is derived from the mediaManagerId (the parent manager's identity)
        $this->instanceId = InstanceManager::getInstanceId($this->mediaManagerId);

        $this->options = $options;

        $this->resolveModelOrClassName($modelOrClassName, 'default');

        $mimeData = $this->resolveAllowedMimeTypes();

        $this->resolveConfig([
            ...$mimeData,
        ]);
    }

    public function render(): View
    {
        return $this->renderView('upload-form', $this->getConfig('frontendTheme'), true);
    }
}
