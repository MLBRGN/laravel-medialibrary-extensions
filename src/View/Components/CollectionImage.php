<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithResponsiveImages;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class CollectionImage extends BaseMediaComponent
{
    use InteractsWithOptionsAndConfig;
    use InteractsWithResponsiveImages;

    public function __construct(
        string $id,
        mixed $modelReference,
        public string $collection = 'default',
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
        parent::__construct($id, $modelReference, $dataSource);
        $this->options = $options;

        $this->configKeys = array_merge($this->configKeys, [
            'previewMode',
            'expandableInModal',
            'modelReference',
            'collection',
            'dataSource',
            'instanceId',
            'clientToken',
        ]);

        if ($this->expandableInModal && $this->model === null) {
            $this->expandableInModal = false;
        }

        $this->resolveConfig();
    }

    protected function resolveModel(mixed $modelReference, ?string $dataSource = 'default'): void
    {
        try {
            parent::resolveModel($modelReference, $dataSource);
        } catch (\Throwable) {
            $this->resolvedModel = new \Mlbrgn\MediaLibraryExtensions\Services\ResolvedModel(
                model: null,
                modelType: null,
                modelId: null,
                temporaryUploadMode: false
            );
            $this->setResolvedModelProperties($this->resolvedModel);
        }
    }

    protected function getMedium(): Media|TemporaryUpload|null
    {
        if ($this->model) {
            return $this->model->getFirstMedia($this->collection);
        }

        return null;
    }

    protected function domIdSuffix(): string
    {
        return 'collection-image';
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
            $this->placeholder = $this->resolvePlaceholder($this->collection);
        }

        return $this->renderView('', null, false, 'medialibrary-extensions::components.collection-image', [
            'hasGeneratedConversion' => $hasConversion,
            'useConversion' => $useConversion,
            'url' => $url,
            'srcset' => $srcset,
            'placeholder' => $this->placeholder,
            'medium' => $this->getMedium(),
            'collections' => [$this->collection],
        ]);
    }
}
