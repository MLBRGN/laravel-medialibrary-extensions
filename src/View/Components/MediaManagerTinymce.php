<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;

class MediaManagerTinymce extends BaseMediaComponent
{
    use InteractsWithOptionsAndConfig;

    public bool $disableForm = false;

    protected string $mediaUploadRoute;

    protected string $mediaManagerPreviewUpdateRoute;

    protected string $youtubeUploadRoute;

    // TODO not used?
    /**
     * @var Repository|Application|mixed|object|null
     */
    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public array $collections = [],
        array $options = [],
        public bool $multiple = true,
        public bool $disabled = false,
        public bool $readonly = false,
        public bool $selectable = false,
        public ?string $dataSource = 'default',
    ) {

        parent::__construct($id, $this->modelOrClassName, $dataSource);
        $this->options = $options;

        // override: enforce disabled / readonly
        if ($this->readonly || $this->disabled) {
            $this->setOption('showUploadForm', false);
            $this->setOption('showDestroyButton', false);
            $this->setOption('showSetAsFirstButton', false);
        }

        // throw exception when no media collection provided at all
        if (! $this->hasCollections()) {
            throw new Exception(__('medialibrary-extensions::messages.no_media_collections'));
        }

        // override
        if (! $this->hasCollection('image') && ! $this->hasCollection('document') && ! $this->hasCollection('video') && ! $this->hasCollection('audio')) {
            $this->setOption('showUploadForm', false);
        }

        // override
        if (! $this->hasCollection('youtube')) {
            $this->setOption('showYouTubeUploadForm', false);
        }

        // Override: Always disable "set-as-first" when multiple files disabled
        if (! $this->multiple) {
            $this->setOption('showSetAsFirstButton', false);
        }

        // the routes, "set-as-first" and "destroy" are "medium specific" routes, so not defined here
        $this->mediaManagerPreviewUpdateRoute = route(mle_prefix_route('media-manager-preview-update'));
        $this->youtubeUploadRoute = route(mle_prefix_route('media-upload-youtube'));

        if ($this->multiple) {
            $this->mediaUploadRoute = route(mle_prefix_route('media-upload-multiple'));
        } else {
            $this->mediaUploadRoute = route(mle_prefix_route('media-upload-single'));
        }

        $this->resolveConfig([
            'uploadFieldName' => 'media',
            'selectable' => $selectable,
            'instanceId' => $this->instanceId,
            'dataSource' => $this->dataSource,
        ]);
    }

    protected function domIdSuffix(): string {
        if ($this->multiple) {
            return 'mmm';
        } else {
            return 'mms';
        }
    }

    public function render(): View
    {
        return $this->renderView('media-manager-tinymce', $this->getConfig('frontendTheme'));
    }
}
