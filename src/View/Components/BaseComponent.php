<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\Component;
use Mlbrgn\MediaLibraryExtensions\Traits\ViewHelpers;

abstract class BaseComponent extends Component
{
    use ViewHelpers;

    public ?array $status = [];

    public function __construct(
        public string $id,
        public ?string $frontendTheme = null

    ) {
        $this->status = session(status_session_prefix());

        if (empty($this->id)) {
            $this->id = 'component-'.uniqid();
        }

        $this->frontendTheme= $frontendTheme ?? config('media-library-extensions.frontend_theme');
    }
}
