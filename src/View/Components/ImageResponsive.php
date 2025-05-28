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
        public string $alt = ''
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
        return view('media-library-extensions::components.image-responsive', [
            'hasGeneratedConversion' => $this->hasGeneratedConversion(),
            'useConversion' => $this->getUseConversion(),
        ]);
    }
}
