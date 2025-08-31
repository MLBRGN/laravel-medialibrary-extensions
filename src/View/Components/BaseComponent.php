<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\Component;
use Mlbrgn\MediaLibraryExtensions\Traits\ViewHelpers;

abstract class BaseComponent extends Component
{
    use ViewHelpers;

    public ?string $frontendTheme = null;

    public function __construct(
        public string $id,
        ?string $frontendTheme = null,
    ) {

        if (empty($this->id)) {
            $this->id = 'component-'.uniqid();
        }

        $this->frontendTheme = $frontendTheme ? $frontendTheme : config('media-library-extensions.frontend_theme');
    }
}
