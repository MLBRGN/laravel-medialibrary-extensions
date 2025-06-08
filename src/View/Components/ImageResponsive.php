<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageResponsive extends Component
{
    public function __construct(
        public Media $medium,
        public string $conversion = '',
        public ?array $conversions = [],
        public string $sizes = '100vw',
        public bool $lazy = true,
        public string $alt = ''// set alt to empty for when none provided
    ) {}

    public function hasGeneratedConversion(): bool
    {
        return $this->getUseConversion() !== '';
    }

    public function getUseConversion(): string
    {
        if (! empty($this->conversion) && $this->medium->hasGeneratedConversion($this->conversion)) {
            return $this->conversion;
        }

        foreach ($this->conversions as $conversionName) {
            if ($this->medium->hasGeneratedConversion($conversionName)) {
                return $conversionName;
            }
        }

        return '';
    }

    public function render(): View
    {
        $hasConversion = $this->hasGeneratedConversion();
        $useConversion = $this->getUseConversion();

        $url = '';
        $srcset = '';

        try {
            $url = $hasConversion
                ? $this->medium->getUrl($useConversion)
                : $this->medium->getUrl();

            if ($hasConversion) {
                $srcset = $this->medium->getSrcset($useConversion);
            }
        } catch (\Throwable $e) {
            // Fallback to original URL if conversion fails
            $url = $this->medium->getUrl();
            $srcset = '';
        }

        return view('media-library-extensions::components.image-responsive', [
            'hasGeneratedConversion' => $hasConversion,
            'useConversion' => $useConversion,
            'url' => $url,
            'srcset' => $srcset,
        ]);
    }

}
