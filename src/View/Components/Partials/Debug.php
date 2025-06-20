<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\Support\Facades\Blade;
use Illuminate\View\Component;
use Illuminate\View\View;

class Debug extends Component
{
    public bool $iconExists = false;

    public array $errors = [];

    public function __construct(
        public ?string $theme = null,
    ) {
        $iconExists = $this->iconExists = collect(Blade::getClassComponentAliases())->keys()->contains(config('media-library-extensions.icons.delete'));
        if (! $iconExists) {
            $this->errors[] = __('media-library-extensions::messages.no_blade_ui_kit_icon_package_detected_download_at_:link', ['link' => '<a href="https://github.com/driesvints/blade-icons" target="_blank">Blade UI Kit icon package</a>']);
        }
    }

    public function render(): View
    {
        return view('media-library-extensions::components.partial.debug');
    }
}
