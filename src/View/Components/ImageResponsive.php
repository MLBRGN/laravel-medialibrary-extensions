<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class ImageResponsive extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    protected array $generatedConversions = [];

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
        public mixed $modelReference = null,
        public ?array $collections = [],
        public ?string $dataSource = 'default',
    ) {
        parent::__construct($id);
        $this->options = $options;

        $this->configKeys = array_merge($this->configKeys, [
            'previewMode',
            'expandableInModal',
            'modelReference',
            'collections',
            'dataSource',
            'instanceId',
            'clientToken',
        ]);

        if ($this->medium) {
            $this->generatedConversions = $this->medium->generated_conversions ?? [];
        }

        if ($this->expandableInModal && $this->medium === null && $this->modelReference === null) {
            $this->expandableInModal = false;
        }

        if ($this->expandableInModal && $this->modelReference === null && $this->medium) {
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
        $medium = $this->getMedium();
        $conversions = $medium ? ($medium->generated_conversions ?? []) : [];

        if (! $medium || $this->originalOnly) {
            return false;
        }

        $conversion = $this->getUseConversion();

        return $conversion !== '' && isset($conversions[$conversion]);
    }

    public function getUseConversion(): string
    {
        $medium = $this->getMedium();
        $conversions = $medium ? ($medium->generated_conversions ?? []) : [];

        if (! $medium || $this->originalOnly) {
            return '';
        }

        if (! empty($this->conversion) && ($conversions[$this->conversion] ?? false)) {
            return $this->conversion;
        }

        foreach ($this->conversions as $conversionName) {
            if ($conversions[$conversionName] ?? false) {
                return $conversionName;
            }
        }

        return '';
    }

    protected function getMedium(): Media|TemporaryUpload|null
    {
        if ($this->medium) {
            return $this->medium;
        }

        if (is_object($this->modelReference) && $this->modelReference instanceof HasMedia) {
            $collection = ! empty($this->collections) ? $this->collections[0] : 'default';

            return $this->medium = $this->modelReference->getFirstMedia($collection);
        }

        return null;
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

    public function getFallbackConversion(): string
    {
        if ($this->medium) {
            return $this->getUseConversion();
        }

        if (! empty($this->conversion)) {
            return $this->conversion;
        }

        if (! empty($this->conversions)) {
            return $this->conversions[0];
        }

        return '';
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
            if (empty($this->placeholder)) {
                if (is_object($this->modelReference) && $this->modelReference instanceof HasMedia) {
                    $collection = ! empty($this->collections) ? $this->collections[0] : 'default';
                    if (method_exists($this->modelReference, 'registerMediaCollections')) {
                        $this->modelReference->registerMediaCollections();
                    }
                    $this->placeholder = $this->modelReference->getFallbackMediaUrl($collection, $this->getFallbackConversion());
                }
            }

            if (empty($this->placeholder)) {
                $this->placeholder = config('medialibrary-extensions.placeholder_url');
            }
        }

        return $this->renderView('', null, false, 'medialibrary-extensions::components.image-responsive', [
            'hasGeneratedConversion' => $hasConversion,
            'useConversion' => $useConversion,
            'url' => $url,
            'srcset' => $srcset,
            'placeholder' => $this->placeholder,
        ]);
    }
}
