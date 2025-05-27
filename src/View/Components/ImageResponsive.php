<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Represents a responsive image component.
 *
 * This component is used for rendering an image with responsive attributes.
 *
 * @param  Media  $medium  The media object representing the image.
 * @param  string  $conversion  The conversion format for the image. Defaults to an empty string.
 * @param  string  $conversion  The conversions formats for the image. When $conversion is $conversions is ignored
 * @param  string  $sizes  The sizes attribute for responsive images. Defaults to '100vw'.
 * @param  bool  $lazy  Indicates whether lazy loading is enabled for the image. Defaults to true.
 * @return View The view for rendering the responsive image component.
 */
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
