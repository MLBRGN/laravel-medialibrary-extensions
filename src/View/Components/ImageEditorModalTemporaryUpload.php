<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\Contracts\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageEditorModalTemporaryUpload extends BaseComponent
{

    public array $config = [];
    public string $saveUpdatedMediumRoute;


    public HasMedia|null $model = null;
    public ?string $modelType = null;
    public mixed $modelId = null;
    public bool $temporaryUpload = false;

    public function __construct(
        public HasMedia|string|null $modelOrClassName = null,// either a modal that implements HasMedia or it's class name
        public TemporaryUpload $medium,
        public string $title = 'no title',// TODO do i want this?
        public string $id = '',
        public ?string $frontendTheme = null,
    ) {
        parent::__construct($id, $frontendTheme);

        $this->id = $this->id.'-image-editor-modal-'.$medium->id;

        if (is_null($modelOrClassName)) {
            throw new Exception('model-or-class-name attribute must be set');
        }

        if ($modelOrClassName instanceof HasMedia) {
            $this->model = $modelOrClassName;
            $this->modelType = $modelOrClassName->getMorphClass();
            $this->modelId = $modelOrClassName->getKey();
        } elseif (is_string($modelOrClassName)) {
            $this->model = null;
            $this->modelType = $modelOrClassName;
            $this->modelId = null;
            $this->temporaryUpload = true;
        } else {
            throw new Exception('model-or-class-name must be either a HasMedia model or a string representing the model class');
        }

        $this->saveUpdatedMediumRoute = route(mle_prefix_route('save-updated-temporary-upload'), $medium);

        // Config array passed to view
        $this->config = [
            'id' => $this->id,
            'model_type' => $this->modelType,
            'model_id' => $this->modelId,
            'medium_id' => $medium->id,
            'collection' => $medium->collection_name,
            'csrf_token' => csrf_token(),
            'save_updated_medium_route' => $this->saveUpdatedMediumRoute,
        ];
    }

    public function render(): View
    {
        return $this->getView('image-editor-modal-temporary-upload',  $this->theme);
    }
}
