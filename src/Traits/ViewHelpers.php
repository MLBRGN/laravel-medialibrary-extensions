<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Illuminate\View\View;

trait ViewHelpers
{
    public function getView($viewName, $frontendTheme): View
    {
        $viewPath = "media-library-extensions::components.$frontendTheme.$viewName";
        return view($viewPath);
    }

    public function getPartialView($viewName, $frontendTheme): View
    {
        $viewPath = "media-library-extensions::components.$frontendTheme.partial.$viewName";
        return view($viewPath);
    }
}
