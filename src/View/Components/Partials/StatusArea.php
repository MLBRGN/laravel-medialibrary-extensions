<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class StatusArea extends BaseComponent
{

    public function __construct(
        public string $id,
        public ?string $frontendTheme,
        public string $initiatorId,
        public string $mediaManagerId

    ) {
        parent::__construct($id, $frontendTheme);

    }

    public function render(): View
    {
        return $this->getPartialView('status-area', $this->frontendTheme);
    }
}
