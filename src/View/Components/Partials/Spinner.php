<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class Spinner extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public string $initiatorId;

    public function __construct(
        ?string $id,
        string $initiatorId,
        public string $mediaManagerId,
        array $options = [],
    ) {
        parent::__construct($id);
        $this->initiatorId = $initiatorId;
        $this->resolveConfig();
    }

    public function render(): View
    {
        return $this->getPartialView('spinner', $this->getConfig('frontendTheme'));
    }
}
