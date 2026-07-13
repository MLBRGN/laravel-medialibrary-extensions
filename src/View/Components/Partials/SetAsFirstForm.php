<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

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

    public string $mediumSetAsFirstRoute;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload $medium,
        public Media|TemporaryUpload|null $singleMedia,
        public array $collections,
        array $options = [],
        public ?bool $disabled = false,
        public ?string $dataSource = 'default',
        ?string $clientToken = null,
    ) {
        parent::__construct($id, $this->modelOrClassName, $dataSource);

        // Ensure instanceId is derived from the Base ID
        $this->instanceId = InstanceManager::getInstanceId($this->id);

        if ($clientToken) {
            $this->clientToken = $clientToken;
        }

        $this->options = $options;

        if ($this->medium instanceof Media && is_null($this->modelId)) {
            $this->modelId = $this->medium->model_id;
        }

        $this->targetMediaCollection = $medium->collection_name;

        // These routes do not accept path parameters; IDs are posted in the body.
        // Passing $medium to route() would append a bogus query string like `?10`.
        if ($this->temporaryUploadMode) {
            $mediumSetAsFirstRoute = route(mle_prefix_route('temporary-upload-set-as-first'));
        } else {
            $mediumSetAsFirstRoute = route(mle_prefix_route('set-as-first'));
        }

        $this->mediumSetAsFirstRoute = $mediumSetAsFirstRoute;

        $this->resolveConfig([
            // Expose under routes.* namespace for blade partials
            'routes' => array_merge($this->resolveConfigRoutes(), [
                'mediumSetAsFirst' => $this->mediumSetAsFirstRoute,
            ]),
            'mediumSetAsFirstRoute' => $this->mediumSetAsFirstRoute,
        ]);
    }

    protected function domIdSuffix(): string
    {
        return 'set-as-first-form-'.$this->medium->id;
    }

    public function render(): View
    {
        return $this->renderView('set-as-first-form', $this->getConfig('theme'), true);
    }
}
