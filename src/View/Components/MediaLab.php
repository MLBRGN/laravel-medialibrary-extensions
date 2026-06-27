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

    public ?string $modelOrClassName = null;

    public function __construct(
        ?string $id,
        public Media|TemporaryUpload|null $media,
        array $options = [],
    ) {
        $this->options = $options;

        parent::__construct($id);

        $this->model = $media?->model;
        if (! $this->model) {
            throw new \Exception('MediaLab component requires a media or temporary upload');
        }
        $this->modelType = $this->model->getMorphClass();
        $this->modelId = $this->model->getKey();
        $this->modelOrClassName = $this->modelType;

        // overrides
        $this->options['showDestroyButton'] = false; // don't show the destroy button
        $this->options['showSetAsFirstButton'] = false;
        $this->options['showMediaEditButton'] = true;
        $this->options['showMenu'] = true;
        // Never show any upload forms inside the Media Lab previews
        $this->options['showUploadForms'] = false; // container visibility
        $this->options['showUploadForm'] = false; // singular file upload form
        $this->options['showYouTubeUploadForm'] = false; // don't show YouTube upload form

        $this->mediaManagerLabPreviewUpdateRoute = route(mle_prefix_route('media-manager-lab-preview-update'));

        $this->resolveConfig();

    }

    protected function domIdSuffix(): string {
        return 'lab';
    }

    public function render(): View
    {
        return $this->renderView('media-lab', $this->getConfig('frontendTheme'));
    }
}
