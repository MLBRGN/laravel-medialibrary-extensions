<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageEditorForm extends BaseComponent
{
    use ResolveModelOrClassName;

    public string $saveUpdatedMediumRoute;

    public function __construct(
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload $medium,
        public string $id,
        public ?string $frontendTheme,
        public string $initiatorId,
        public ?bool $useXhr = null,
        public ?string $imageCollection = '',
        public ?string $documentCollection = '',
        public ?string $youtubeCollection = '',
        public ?string $videoCollection = '',
        public ?string $audioCollection = '',
        public ?string $mediaManagerId = '',
    ) {
        parent::__construct($id, $frontendTheme);

        $this->id = $this->id . '-ie-update-form';

        $this->resolveModelOrClassName($modelOrClassName);
        $this->saveUpdatedMediumRoute = $this->temporaryUpload ? route(mle_prefix_route('save-updated-temporary-upload'), $medium) : route(mle_prefix_route('save-updated-medium'), $medium);
    }

    public function render(): View
    {
        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

        return $this->getPartialView('image-editor-form', $this->frontendTheme);
    }
}
