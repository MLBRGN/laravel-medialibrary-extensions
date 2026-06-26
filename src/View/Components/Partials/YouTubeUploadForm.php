<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseMediaComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class YouTubeUploadForm extends BaseMediaComponent
{
    use InteractsWithOptionsAndConfig;

    public ?string $modelType = null;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or its class name
        public Media|TemporaryUpload|null $singleMedia = null,
        public array $collections = [],
        array $options = [],
        public bool $multiple = false,
        public ?bool $readonly = false,
        public ?bool $disabled = false,
        public string $instanceId = '',
        public ?string $dataSource = 'default',
        ?string $clientToken = null,
    ) {
        parent::__construct($id, $this->modelOrClassName, $dataSource);

        if (empty($instanceId)) {
            $this->instanceId = InstanceManager::getInstanceId($this->id);
        } else {
            $this->instanceId = $instanceId;
        }

        if ($clientToken) {
            $this->clientToken = $clientToken;
        }

        $this->options = $options;

        $youtubeCollection = $collections['youtube'] ?? null;
        $mediaUploadRoute = route(mle_prefix_route('media-upload-youtube'));
        $mediaManagerPreviewUpdateRoute = route(mle_prefix_route('media-manager-preview-update')); // : route(mle_prefix_route('media-upload-single-preview'));

        $this->resolveConfig([
            'instanceId' => $this->instanceId,
            'youtubeCollection' => $youtubeCollection,
            'mediaUploadRoute' => $mediaUploadRoute,
            'mediaManagerPreviewUpdateRoute' => $mediaManagerPreviewUpdateRoute,
        ]);

        $this->totalMediaCount = $this->mediaService->countMediaInCollections(
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
        return $this->renderView('youtube-upload-form', $this->getConfig('frontendTheme'), true);
    }
}
