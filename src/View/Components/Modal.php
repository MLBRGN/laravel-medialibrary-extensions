<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends BaseComponent
{
    public function __construct(
        public string $modalId,
        public string $title,
        public bool $noPadding = false,
        public bool $showHeader = true,
        public bool $showBody = true,
        public string $sizeClass = 'modal-lg',
        public string $id = 'no-id',
        public ?string $frontendTheme = null

    ) {
        parent::__construct($id, $frontendTheme);
        $this->frontend = $frontendTheme ?? config('media-library-extensions.frontend_theme', 'plain');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return $this->getView('modal');
    }
}
