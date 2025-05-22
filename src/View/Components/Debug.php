<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\View\Components;

use Illuminate\Support\Facades\Blade;
use Illuminate\View\Component;
use Illuminate\View\View;

class Debug extends Component
{
    public bool $iconExists = false;

    public array $errors = [];

    public function __construct(
    ) {
        $iconExists = $this->iconExists = collect(Blade::getClassComponentAliases())->keys()->contains(config('media-library-extensions.icons.delete'));
        if (! $iconExists) {
            $this->errors[] = 'Please require the correct  <a href="https://github.com/driesvints/blade-icons" target="_blank">Blade UI Kit icon package</a> for icons to display and set the right icons in the configuration of this package.';
        }
    }

    public function render(): View
    {
        return view('media-library-extensions::components.debug');
    }
}
