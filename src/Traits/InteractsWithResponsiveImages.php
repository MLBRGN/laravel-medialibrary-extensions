<?php

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

trait InteractsWithResponsiveImages
{
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

    public function getFallbackConversion(): string
    {
        if ($this->getMedium()) {
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

    protected function resolvePlaceholder(?string $collection = null): ?string
    {
        if (! empty($this->placeholder)) {
            return $this->placeholder;
        }

        if (is_object($this->modelReference) && $this->modelReference instanceof HasMedia) {
            if (method_exists($this->modelReference, 'registerMediaCollections')) {
                $this->modelReference->registerMediaCollections();
            }

            $fallback = $this->modelReference->getFallbackMediaUrl($collection ?? 'default', $this->getFallbackConversion());

            if (! empty($fallback)) {
                return $fallback;
            }
        }

        return config('medialibrary-extensions.placeholder_url');
    }

    abstract protected function getMedium(): Media|TemporaryUpload|null;
}
