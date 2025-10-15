<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Str;
use Illuminate\View\Component;
use Mlbrgn\MediaLibraryExtensions\Traits\ViewHelpers;

abstract class BaseComponent extends Component
{
    use ViewHelpers;

    public string $id;

    public ?string $frontendTheme = 'bootstrap-5';

    public function __construct(
        ?string $id = null,
        ?string $frontendTheme = null
    ) {
        //        dump('id in bc: ' . $id);
        $this->id = filled($id) ? $id : 'component-'.Str::uuid();
        //        dump('this->id in bc: ' . $this->id);
        $this->frontendTheme = $frontendTheme ?? config('medialibrary-extensions.frontend_theme');
    }
}
