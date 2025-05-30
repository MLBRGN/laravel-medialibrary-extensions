<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\Support\Facades\Blade;
use Illuminate\View\Component;
use Illuminate\View\View;

class Icon extends Component
{
    public bool $iconExists = false;

    public function __construct(
        public string $name,
        public string $title = '',
    ) {
        $this->iconExists = collect(Blade::getClassComponentAliases())->keys()->contains($name);
        //        dd($this->iconExists);
    }

    public function render(): View
    {
        return view('media-library-extensions::components.partial.icon');
    }
}
