<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class ImageResponsive extends Component
{
    protected array $generatedConversions = [];

    public function __construct(
        public Media|TemporaryUpload|null $medium = null,
        public bool $previewMode = true,
        public string $conversion = '',
        public array $conversions = [],
        public string $sizes = '100vw',
        public bool $lazy = true,
        public string $alt = '',
        public bool $originalOnly = false,
        public array $options = [],
    ) {
        if ($this->medium) {
            $this->generatedConversions = $this->medium->generated_conversions ?? [];
        }
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

    /**
     * Build a cache-busted URL for the medium.
     */
    protected function buildCacheBustedUrl(string $url): string
    {
        try {
            // For Spatie MediaLibrary (local disk)
            if ($this->medium instanceof Media && $this->medium->disk === 'public') {
                $path = $this->medium->getPath($this->getUseConversion());
                if (file_exists($path)) {
                    // Cache-busting based on actual file modification time
                    $timestamp = filemtime($path);
                    $separator = str_contains($url, '?') ? '&' : '?';

                    return "{$url}{$separator}v={$timestamp}";
                }
            }

            // Fallback: use updated_at if available
            if ($this->medium?->updated_at) {
                $timestamp = $this->medium->updated_at->timestamp;
                $separator = str_contains($url, '?') ? '&' : '?';

                return "{$url}{$separator}v={$timestamp}";
            }
        } catch (Throwable) {
            // Swallow errors (e.g. missing file)
        }

        return $url;
    }

    public function render(): View
    {
        $hasConversion = $this->hasGeneratedConversion();
        $useConversion = $this->getUseConversion();

        $url = '';
        $srcset = '';

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

        return view('media-library-extensions::components.image-responsive', [
            'hasGeneratedConversion' => $hasConversion,
            'useConversion' => $useConversion,
            'url' => $url,
            'srcset' => $srcset,
        ]);
    }
}
