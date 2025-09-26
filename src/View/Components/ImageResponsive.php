<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class ImageResponsive extends Component
{
    protected array $generatedConversions = [];

    public function __construct(
        public ?Media $medium,
        public string $conversion = '',
        public ?array $conversions = [],
        public string $sizes = '100vw',
        public bool $lazy = true,
        public string $alt = ''// set alt to empty for when none provided
    ) {

        if ($this->medium) {
            // read directly from custom_properties instead of lazy-loading
            $this->generatedConversions = $this->medium->getCustomProperty('generated_conversions', []);
        }
    }

    public function hasGeneratedConversion(): bool
    {
        return $this->medium && $this->getUseConversion() !== '';
    }

    public function getUseConversion(): string
    {
        if (! $this->medium) {
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

    public function render(): View
    {
        $hasConversion = $this->hasGeneratedConversion();
        $useConversion = $this->getUseConversion();

        $url = '';
        $srcset = '';

        try {
            if ($this->medium) {
                $url = $hasConversion
                    ? $this->medium->getUrl($useConversion)   // safe now, no reload
                    : $this->medium->getUrl();

                $srcset = $hasConversion
                    ? $this->medium->getSrcset($useConversion)
                    : '';
            }
        } catch (Throwable) {
            $url = $this->medium?->getUrl() ?? '';
        }

        return view('media-library-extensions::components.image-responsive', [
            'hasGeneratedConversion' => $hasConversion,
            'useConversion' => $useConversion,
            'url' => $url,
            'srcset' => $srcset,
        ]);
    }
}
