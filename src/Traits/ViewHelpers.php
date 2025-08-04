<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Illuminate\View\View;

trait ViewHelpers
{
    public function getView($viewName, $theme): View
    {
        $viewPath = "media-library-extensions::components.$theme.$viewName";
        return view($viewPath);
    }

    public function getPartialView($viewName, $theme): View
    {
        $viewPath = "media-library-extensions::components.$theme.partial.$viewName";
        return view($viewPath);
    }
}
