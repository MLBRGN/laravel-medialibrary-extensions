<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Illuminate\View\View;

trait ViewHelpers
{
    public function getView($viewName, $theme): View
    {
        if (! $theme) {
            $theme = config('medialibrary-extensions.frontend_theme', 'bootstrap-5');
        }
        $viewPath = "medialibrary-extensions::components.$theme.$viewName";

        return view($viewPath);
    }

    public function getPartialView($viewName, $theme): View
    {
        if (! $theme) {
            $theme = config('medialibrary-extensions.frontend_theme', 'bootstrap-5');
        }
        $viewPath = "medialibrary-extensions::components.$theme.partial.$viewName";

        return view($viewPath);
    }
}
