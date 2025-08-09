<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\Contracts\View\View;
use Spatie\MediaLibrary\HasMedia;

class MediaModal extends BaseComponent
{

    public HasMedia|null $model = null;

    public ?string $modelType = null;

    public mixed $modelId = null;

    public bool $temporaryUpload = false;

    public function __construct(
        public HasMedia|string $modelOrClassName,
        public ?string $mediaCollection,
        public ?array $mediaCollections,
        public string $title,// TODO do i want this?
        public string $id = '',
        public ?string $frontendTheme = null,
        public bool $videoAutoPlay = true,
    ) {
        parent::__construct($id, $frontendTheme);
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

        $this->id = $this->id . '-modal';

    }

    public function render(): View
    {
        return $this->getView('media-modal', $this->frontendTheme);
    }
}
