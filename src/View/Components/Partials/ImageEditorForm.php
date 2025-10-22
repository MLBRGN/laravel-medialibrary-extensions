<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageEditorForm extends BaseComponent
{
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    //    public string $saveUpdatedMediumRoute;
    public array $config;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload $medium,
        public Media|TemporaryUpload|null $singleMedium = null,
        public array $collections,
        public array $options,
        public string $initiatorId,
        public ?string $mediaManagerId = '',
        public ?bool $disabled = false,
    ) {
        parent::__construct($id);

        $this->id = $this->id.'-ie-update-form';

        $this->resolveModelOrClassName($modelOrClassName);

        $saveUpdatedMediumRoute = $this->getOption('temporaryUploadMode') ?
            route(mle_prefix_route('save-updated-temporary-upload'), $medium) :
            route(mle_prefix_route('save-updated-medium'), $medium);

        $this->initializeConfig([
//            'frontendTheme' => $this->getOption('frontendTheme', config('media-library-extensions.frontend_theme')),
//            'useXhr' => config('media-library-extensions.use_xhr'),
            'saveUpdatedMediumRoute' => $saveUpdatedMediumRoute,
        ]);
    }

    public function render(): View
    {
        return $this->getPartialView('image-editor-form', $this->getConfig('frontendTheme'));
    }
}
