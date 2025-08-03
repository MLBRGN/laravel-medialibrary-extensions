<?php

namespace Mlbrgn\MediaLibraryExtensions\Helpers;

use Illuminate\Support\Facades\Request;

class DemoHelper
{
    /**
     * Determine if the current request is from a demo page.
     */
    public static function isRequestFromDemoPage(): bool
    {
        if (! config('media-library-extensions.demo_pages_enabled')) {
            return false;
        }

        $routePrefix = config('media-library-extensions.route_prefix');
        $currentUrl = Request::path();
        $referer = Request::header('referer');

        // Check if the current URL is a demo page
        $isDemoUrl = str_contains($currentUrl, $routePrefix.'/mle-demo-plain') ||
                     str_contains($currentUrl, $routePrefix.'/mle-demo-bootstrap-5');

        // Check if the referer is a demo page
        $isFromDemoPage = $referer && (
            str_contains($referer, $routePrefix.'/mle-demo-plain') ||
            str_contains($referer, $routePrefix.'/mle-demo-bootstrap-5')
        );

        // Return true if either the current URL is a demo page or the referer is a demo page
        return $isDemoUrl || $isFromDemoPage;
    }
}
