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
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload $medium,
        public ?string $frontendTheme,
        public array $collections, // in image, document, youtube, video, audio
        public string $initiatorId,
        public ?bool $useXhr = null,
        public ?string $mediaManagerId = '',
        public ?bool $disabled = false,
    ) {
        parent::__construct($id, $frontendTheme);

        $this->id = $this->id.'-ie-update-form';

        $this->resolveModelOrClassName($modelOrClassName);
        $this->saveUpdatedMediumRoute = $this->temporaryUploadMode ? route(mle_prefix_route('save-updated-temporary-upload'), $medium) : route(mle_prefix_route('save-updated-medium'), $medium);
    }

    public function render(): View
    {
        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

        return $this->getPartialView('image-editor-form', $this->frontendTheme);
    }
}
