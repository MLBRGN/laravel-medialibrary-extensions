<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithResponsiveImages;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class MediaResponsive extends BaseComponent
{
    use InteractsWithOptionsAndConfig;
    use InteractsWithResponsiveImages;

    public mixed $modelReference = null;

    public ?array $collections = [];

    public function __construct(
        string $id,
        public Media|TemporaryUpload|null $medium = null,
        public bool $previewMode = true,
        public string $conversion = '',
        public array $conversions = [],
        public string $sizes = '100vw',
        public bool $lazy = true,
        public string $alt = '',
        public bool $originalOnly = false,
        array $options = [],
        public ?string $placeholder = null,
        public bool $expandableInModal = false,
        public ?string $dataSource = 'default',
    ) {
        parent::__construct($id);
        $this->options = $options;
        
        $this->modelReference = null;
        $this->collections = [];

        if ($this->medium instanceof Media) {
            $this->modelReference = $this->medium->model;
            $this->collections = [$this->medium->collection_name];
        } elseif ($this->medium instanceof TemporaryUpload) {
            $this->modelReference = TemporaryUpload::class;
            $this->collections = [$this->medium->collection_name];
        }

        $this->configKeys = array_merge($this->configKeys, [
            'previewMode',
            'expandableInModal',
            'dataSource',
            'instanceId',
            'clientToken',
            'modelReference',
            'collections',
        ]);

        if ($this->expandableInModal && $this->medium === null) {
            $this->expandableInModal = false;
        }

        $this->resolveConfig();
    }

    protected function getMedium(): Media|TemporaryUpload|null
    {
        return $this->medium;
    }

    protected function domIdSuffix(): string
    {
        return 'media-responsive';
    }

    public function render(): View
    {
        $url = '';
        $srcset = '';
        $hasConversion = false;
        $useConversion = '';

        try {
            $medium = $this->getMedium();

            if ($medium) {
                $hasConversion = $this->hasGeneratedConversion();
                $useConversion = $this->getUseConversion();

                $rawUrl = $hasConversion
                    ? $medium->getUrl($useConversion)
                    : $medium->getUrl();

                $url = $this->buildCacheBustedUrl($rawUrl);

                $srcset = $hasConversion
                    ? $medium->getSrcset($useConversion)
                    : '';
            }
        } catch (Throwable) {
            $medium = $this->getMedium();
            $url = ($medium && method_exists($medium, 'getUrl'))
                ? $medium->getUrl()
                : '';
        }

        if (empty($url)) {
            $this->placeholder = $this->resolvePlaceholder();
        }

        return $this->renderView('', null, false, 'medialibrary-extensions::components.media-responsive', [
            'hasGeneratedConversion' => $hasConversion,
            'useConversion' => $useConversion,
            'url' => $url,
            'srcset' => $srcset,
            'placeholder' => $this->placeholder,
        ]);
    }
}
