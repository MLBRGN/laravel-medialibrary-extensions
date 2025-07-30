<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Illuminate\View\View;

trait ViewHelpers {

    public function getView($viewName, $theme): View
    {
        $viewPath = "media-library-extensions::components.$theme.$viewName";

//        dd($viewPath);

//        if (! view()->exists($viewPath)) {
//            $viewPath = "media-library-extensions::components.plain.$viewName";
//        }

        return view($viewPath);
    }

    public function getPartialView($viewName, $theme): View
    {
        $viewPath = "media-library-extensions::components.$theme.partial.$viewName";

//        if (! view()->exists($viewPath)) {
//            $viewPath = "media-library-extensions::components.plain.partial.$viewName";
//        }

        return view($viewPath);
    }
}
