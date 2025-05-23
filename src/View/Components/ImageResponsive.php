<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\View\Components;

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
 * @param  string  $sizes  The sizes attribute for responsive images. Defaults to '100vw'.
 * @param  array  $attributes  Additional HTML attributes for the image. Defaults to an empty array.
 * @param  bool  $lazy  Indicates whether lazy loading is enabled for the image. Defaults to true.
 * @return View The view for rendering the responsive image component.
 */
class ImageResponsive extends Component
{
    public bool $hasGeneratedConversion = false;

    public function __construct(
        public Media $medium,
        public string $conversion = '',
        public string $sizes = '100vw',
        public $attributes = [],
        public bool $lazy = true,
        public string $alt = ''
    ) {}

    public function render(): View
    {

        $this->hasGeneratedConversion = $this->medium->hasGeneratedConversion('16x9');

        return view('media-library-extensions::components.image-responsive');
    }
}
