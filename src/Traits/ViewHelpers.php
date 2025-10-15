<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

trait ViewHelpers
{
    public function getView($viewName, $frontendTheme): View
    {
        if (! $frontendTheme) {
            Log::error('No frontend theme, falling back');
            $frontendTheme = 'bootstrap-5';
        }
        $viewPath = "media-library-extensions::components.$frontendTheme.$viewName";

        return view($viewPath);
    }

    public function getPartialView($viewName, $frontendTheme): View
    {
        if (! $frontendTheme) {
            Log::error('No frontend theme, falling back');
            $frontendTheme = 'bootstrap-5';
        }
        $viewPath = "media-library-extensions::components.$frontendTheme.partial.$viewName";

        return view($viewPath);
    }
}
