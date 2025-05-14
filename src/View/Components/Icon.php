<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\View\Components;

use Illuminate\Support\Facades\Blade;
use Illuminate\View\Component;

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

    public function render()
    {
        return view('media-library-extensions::components.icon');
    }
}
