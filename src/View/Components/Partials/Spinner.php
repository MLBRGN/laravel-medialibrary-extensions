<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\Component;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use function Mlbrgn\MediaLibraryExtensions\View\Components\getPartialView;

class Spinner extends BaseComponent
{
    public string $targetId;

    public function __construct(
        public string $id,
        public ?string $frontendTheme,
        string $targetId
    ) {
        parent::__construct($id, $frontendTheme);
        $this->targetId = $targetId;
    }
    public function render(): View
    {
        return $this->getPartialView('spinner', $this->theme);
    }

}
