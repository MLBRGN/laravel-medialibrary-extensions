<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class ImageResponsive extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    protected array $generatedConversions = [];

    public mixed $modelReference = null;
    public ?array $collections = [];
    public ?string $dataSource = 'default';

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
        $collections = null,
        $dataSource = null,
        $modelReference = null,
    ) {
        parent::__construct($id);
        $this->options = $options;
        $this->collections = $collections ?? [];
        $this->dataSource = $dataSource ?? 'default';
        $this->modelReference = $modelReference;

        $this->configKeys = array_merge($this->configKeys, [
            'previewMode',
            'expandableInModal',
            'instanceId',
            'clientToken',
            'collections',
            'dataSource',
        ]);

        if ($this->medium) {
            $this->generatedConversions = $this->medium->generated_conversions ?? [];
        }

        if ($this->expandableInModal && $this->medium && $this->modelReference === null) {
            if ($this->medium instanceof Media) {
                $this->modelReference = $this->medium->model;
            } elseif ($this->medium instanceof TemporaryUpload) {
                $this->modelReference = TemporaryUpload::class;
            }
        }

        $this->resolveConfig();
    }

    public function hasGeneratedConversion(): bool
    {
        if (! $this->medium || $this->originalOnly) {
            return false;
        }

        $conversion = $this->getUseConversion();

        return $conversion !== '' && isset($this->generatedConversions[$conversion]);
    }

    public function getUseConversion(): string
    {
        if (! $this->medium || $this->originalOnly) {
            return '';
        }

        if (! empty($this->conversion) && ($this->generatedConversions[$this->conversion] ?? false)) {
            return $this->conversion;
        }

        foreach ($this->conversions as $conversionName) {
            if ($this->generatedConversions[$conversionName] ?? false) {
                return $conversionName;
            }
        }

        return '';
    }

    protected function buildCacheBustedUrl(string $url): string
    {
        try {
            // Use the current time in milliseconds as cache-buster
            $timestamp = (int) (microtime(true) * 1000);
            $separator = str_contains($url, '?') ? '&' : '?';

            return "{$url}{$separator}v={$timestamp}";
        } catch (Throwable) {
            return $url;
        }
    }

    protected function domIdSuffix(): string
    {
        return 'image-responsive';
    }

    public function render(): View
    {
        $hasConversion = $this->hasGeneratedConversion();
        $useConversion = $this->getUseConversion();

        $url = '';
        $srcset = '';

        $this->placeholder ??= asset(
            config('medialibrary-extensions.asset_path').'/images/fallback.png'
        );

        $modelReference = $this->modelReference;
        $collections = $this->collections;
        $dataSource = $this->dataSource;

        if ($this->attributes) {
            $modelReference = $this->attributes->get('model-reference') ?? $this->attributes->get('modelReference') ?? $modelReference;
            $collections = $this->attributes->get('collections') ?? $this->attributes->get('collections') ?? $collections;
            $dataSource = $this->attributes->get('data-source') ?? $this->attributes->get('dataSource') ?? $dataSource;
        }

        if ($this->expandableInModal && !$modelReference && $this->medium) {
            if ($this->medium instanceof Media) {
                try {
                    $modelReference = app(\Mlbrgn\MediaLibraryExtensions\Services\MediaModelResolver::class)->resolveModelById(
                        $this->medium->model_type,
                        $this->medium->model_id,
                        $dataSource
                    );
                } catch (\Throwable) {
                    $modelReference = $this->medium->model;
                }
            } elseif ($this->medium instanceof TemporaryUpload) {
                $modelReference = TemporaryUpload::class;
            }
        }

        try {
            if ($this->medium) {
                $rawUrl = $hasConversion
                    ? $this->medium->getUrl($useConversion)
                    : $this->medium->getUrl();

                $url = $this->buildCacheBustedUrl($rawUrl);

                $srcset = $hasConversion
                    ? $this->medium->getSrcset($useConversion)
                    : '';
            }
        } catch (Throwable) {
            $url = ($this->medium && method_exists($this->medium, 'getUrl'))
                ? $this->medium->getUrl()
                : '';
        }

        $this->config['collections'] = is_array($collections) ? $collections : [$collections];
        $this->config['dataSource'] = $dataSource;

        return $this->renderView('', null, false, 'medialibrary-extensions::components.image-responsive', [
            'hasGeneratedConversion' => $hasConversion,
            'useConversion' => $useConversion,
            'url' => $url,
            'srcset' => $srcset,
            'modelReference' => $modelReference,
            'collections' => $this->config['collections'],
            'dataSource' => $dataSource,
        ]);
    }
}
