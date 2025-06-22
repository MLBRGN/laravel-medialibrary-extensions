<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Contracts\View\View;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageEditorModal extends BaseComponent
{

    public array $config = [];
    public string $saveUpdatedMediumRoute;
    public function __construct(
        public Media $medium,
        public HasMedia $model,
        public string $title = 'no title',// TODO do i want this?
        public string $id = '',
        public ?string $frontendTheme = null,
    ) {
        parent::__construct($id, $frontendTheme);

        $this->id = $this->id.'-image-editor-modal-'.$medium->id;

        $this->saveUpdatedMediumRoute = route(mle_prefix_route('save-updated-medium'), $model);

        // Config array passed to view
        $this->config = [
            'id' => $this->id,
            'model_type' => $model->getMorphClass(),
            'model_id' => $model->getKey(),
            'medium_id' => $medium?->id,
            'collection' => $medium?->collection_name,
            'csrf_token' => csrf_token(),
            'save_updated_medium_route' => $this->saveUpdatedMediumRoute,
        ];
    }

    public function render(): View
    {
        return $this->getView('image-editor-modal',  $this->theme);
    }
}
