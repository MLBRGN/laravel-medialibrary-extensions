<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseMediaComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SetAsFirstForm extends BaseMediaComponent
{
    use InteractsWithOptionsAndConfig;

    public ?string $targetMediaCollection = null;

    public ?string $mediaManagerId = '';

    public string $mediumSetAsFirstRoute;

    public function __construct(
        ?string $id,
        ?string $mediaManagerId,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Collection $media,
        public Media|TemporaryUpload $medium,// TODO should never be temporary upload, but then I get error on demo pages?
        public Media|TemporaryUpload|null $singleMedia,
        public array $collections,
        array $options = [],
        public ?bool $disabled = false,
        public ?string $dataSource = 'default',
    ) {
        parent::__construct($id, $this->modelOrClassName, $dataSource);

        $this->mediaManagerId = $mediaManagerId ?? $this->originalId;

        // Ensure instanceId is derived from the mediaManagerId (the parent manager's identity)
        $this->instanceId = InstanceManager::getInstanceId($this->mediaManagerId);

        $this->options = $options;

        if ($this->medium instanceof Media && is_null($this->modelId)) {
            $this->modelId = $this->medium->model_id;
        }

        $this->setBaseId($this->getSuffixedId('set-as-first-form-'.$this->medium->id));

        $this->targetMediaCollection = $medium->collection_name;

        if ($this->temporaryUploadMode) {
            $mediumSetAsFirstRoute = route(mle_prefix_route('temporary-upload-set-as-first'), $medium);
        } else {
            $mediumSetAsFirstRoute = route(mle_prefix_route('set-as-first'), $medium);
        }

        $this->mediumSetAsFirstRoute = $mediumSetAsFirstRoute;

        $this->resolveConfig([
            'mediumSetAsFirstRoute' => $this->mediumSetAsFirstRoute,
        ]);
    }

    public function render(): View
    {
        return $this->renderView('set-as-first-form', $this->getConfig('frontendTheme'), true);
    }
}
