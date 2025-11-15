<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

/*
 * Edit media and restore original if needed
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaLab extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public string $mediaManagerLabPreviewUpdateRoute = '';

    public ?Model $model = null;

    public ?string $modelType = null;
    public ?int $modelId = null;

    public function __construct(
        ?string $id,
        public Media|TemporaryUpload|null $medium,
        public array $options = [],
    ) {
        $id = filled($id) ? $id : 'mle-media-lab-'.uniqid();

        parent::__construct($id);

        $this->model = $medium->model;
        $this->modelType = $this->model->getMorphClass();
        $this->modelId = $this->model->getKey();

        // overrides
        $this->options['showDestroyButton'] = false;
        $this->options['showSetAsFirstButton'] = false;
        $this->options['showMediaEditButton'] = true;
        $this->options['showMenu'] = true;
        $this->options['showUploadForms'] = false;
        //        $this->options['frontendTheme'] = 'plain';

        $this->mediaManagerLabPreviewUpdateRoute = route(mle_prefix_route('media-manager-lab-preview-update'));

        $this->initializeConfig();

    }

    public function render(): View
    {
        return $this->getView('media-lab', $this->getConfig('frontendTheme'));

    }
}
