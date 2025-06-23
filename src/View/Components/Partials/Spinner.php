<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\Component;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class Spinner extends BaseComponent
{
    public string $initiatorId;

    public function __construct(
        public string $id,
        public ?string $frontendTheme,
        string $initiatorId
    ) {
        parent::__construct($id, $frontendTheme);
        $this->initiatorId = $initiatorId;
    }
    public function render(): View
    {
        return $this->getPartialView('spinner', $this->theme);
    }

}
