<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\View\Components;

use Illuminate\Support\Facades\Blade;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * This class represents an Icon component within this package.
 * It dynamically checks if the specified icon name exists within the registered Blade component aliases.
 *
 * The Icon class is responsible for rendering an icon view based on the provided name and title.
 *
 * The `$iconExists` property determines whether the specified icon is recognized as a registered Blade component.
 *
 * @param  string  $name  The name of the icon to be displayed.
 * @param  string  $title  An optional title or label for the icon.
 *
 * @method render Renders the view for the Icon component.
 *
 * @return View The Blade view for the Icon component.
 *
 * @property bool $iconExists Indicates whether the specified icon name is registered in Blade component aliases.
 *
 * @constructor Initializes the Icon component, checking for the existence of the specified icon name.
 */
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
        return view('media-library-extensions::components.icon');
    }
}
