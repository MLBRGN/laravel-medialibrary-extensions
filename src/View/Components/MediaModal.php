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
        public ?string $title,// TODO do i want this?
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
            if (! class_exists($modelOrClassName)) {
                throw new \InvalidArgumentException(__('media-library-extensions::messages.class_does_not_exist', ['class_name' => $modelOrClassName]));
            }

            if (! is_subclass_of($modelOrClassName, HasMedia::class)) {
                throw new \InvalidArgumentException(__('media-library-extensions::messages.class_must_implement', ['class_name' => HasMedia::class]));
            }

            $this->model = null;
            $this->modelType = $modelOrClassName;
            $this->modelId = null;
            $this->temporaryUpload = true;
        }

        $this->id = $this->id . '-mod';

    }

    public function render(): View
    {
        return $this->getView('media-modal', $this->frontendTheme);
    }
}
