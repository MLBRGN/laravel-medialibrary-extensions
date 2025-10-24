<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Illuminate\View\View;

trait ViewHelpers
{
    public function getView($viewName, $frontendTheme): View
    {
        if (! $frontendTheme) {
            $frontendTheme = config('media-library-extensions.frontend_theme', 'bootstrap-5');
        }
        $viewPath = "media-library-extensions::components.$frontendTheme.$viewName";

        return view($viewPath);
    }

    public function getPartialView($viewName, $frontendTheme): View
    {
        if (! $frontendTheme) {
            $frontendTheme = config('media-library-extensions.frontend_theme', 'bootstrap-5');
        }
        $viewPath = "media-library-extensions::components.$frontendTheme.partial.$viewName";

        return view($viewPath);
    }
}
