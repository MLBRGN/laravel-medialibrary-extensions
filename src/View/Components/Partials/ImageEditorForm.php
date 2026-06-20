<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseMediaComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageEditorForm extends BaseMediaComponent
{
    use InteractsWithOptionsAndConfig;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload $medium,
        public Media|TemporaryUpload|null $singleMedia,
        public array $collections,
        array $options,
        public string $initiatorId,
        public ?string $mediaManagerId = '',
        public ?bool $disabled = false,
    ) {
        parent::__construct($id, $this->modelOrClassName, 'default');// TODO use default?
        $this->options = $options;

        $this->id = $this->id.'-ie-update-form';

        $storeUpdatedMediaRoute = $this->getOption('temporaryUploadMode') ?
            route(mle_prefix_route('save-updated-temporary-upload'), $medium) :
            route(mle_prefix_route('save-updated-media'), $medium);

        $this->resolveConfig([
            //            'frontendTheme' => $this->getOption('frontendTheme', config('medialibrary-extensions.frontend_theme')),
            //            'useXhr' => config('medialibrary-extensions.use_xhr'),
            'storeUpdatedMediaRoute' => $storeUpdatedMediaRoute,
        ]);
    }

    public function render(): View
    {
        return $this->renderView('image-editor-form', $this->getConfig('frontendTheme'), true);
    }
}
