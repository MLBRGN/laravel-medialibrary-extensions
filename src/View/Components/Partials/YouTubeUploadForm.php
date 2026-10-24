<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaCounter;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseMediaComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class YouTubeUploadForm extends BaseMediaComponent
{
    use InteractsWithOptionsAndConfig;

    public ?string $modelType = null;

    public string $youtubeUploadRoute;

    public string $mediaManagerPreviewUpdateRoute;

    public function __construct(
        string $id,
        public mixed $modelReference,// either a modal that implements HasMediaExtended or its class name
        public Media|TemporaryUpload|null $singleMedia = null,
        public array $collections = [],
        array $options = [],
        public bool $multiple = false,
        public ?bool $readonly = false,
        public ?bool $disabled = false,
        ?string $instanceId = '___DEFAULT___',
        public ?string $dataSource = 'default',
        ?string $clientToken = '___DEFAULT___',
    ) {
        parent::__construct($id, $this->modelReference, $dataSource, null, $instanceId, $clientToken);

        $this->options = $options;

        $youtubeCollection = $collections['youtube'] ?? null;
        $this->youtubeUploadRoute = route(mle_prefix_route('media-upload-youtube'));
        $this->mediaManagerPreviewUpdateRoute = route(mle_prefix_route('media-manager-preview-update'));

        $this->resolveConfig([
            'instanceId' => $this->instanceId,
            'youtubeCollection' => $youtubeCollection,
        ]);

        $mediaCounter = app(MediaCounter::class);
        $this->totalMediaCount = $mediaCounter->countMediaInCollections(
            $this->resolvedModel,
            $this->collections,
            $this->instanceId,
            $this->clientToken,
            $this->dataSource
        );
    }

    protected function domIdSuffix(): string
    {
        return 'youtube-upload-form';
    }

    public function render(): View
    {
        return $this->renderView('youtube-upload-form', $this->getConfig('theme'), true);
    }
}
