<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class StatusArea extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public function __construct(
        ?string $id,
        array $options = [],
    ) {
        parent::__construct($id);

        $this->options = $options;
        $this->resolveConfig();

    }

    protected function domIdSuffix(): string
    {
        return 'status-area';
    }

    public function render(): View
    {
        return $this->getPartialView('status-area', $this->getConfig('theme'));
    }
}
