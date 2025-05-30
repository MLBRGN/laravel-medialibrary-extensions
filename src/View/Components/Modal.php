<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Contracts\View\View;

class Modal extends BaseComponent
{
    //    public string $modalId;

    public function __construct(
        public string $id,
        public string $title,
        public bool $noPadding = false,
        public bool $showHeader = true,
        public bool $showBody = true,
        public string $sizeClass = 'modal-lg',
        public ?string $frontendTheme = null

    ) {
        parent::__construct($id, $frontendTheme);
        $this->frontend = $frontendTheme ?? config('media-library-extensions.frontend_theme', 'plain');
        //        $this->modalId = $id.'-modal';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return $this->getView('modal');
    }
}
