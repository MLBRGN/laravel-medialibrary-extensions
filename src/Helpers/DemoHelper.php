<?php

namespace Mlbrgn\MediaLibraryExtensions\Helpers;

use Illuminate\Support\Facades\Request;

// TODO deprecate
class DemoHelper
{
    /**
     * Determine if the current request is from a demo page.
     * The demo database is setup by service provider when demo pages are enabled
     * The RegisterDemoDatabase middleware
     */
    public static function isRequestFromDemoPage(): bool
    {
        if (! config('medialibrary-extensions.demo_pages_enabled')) {
            return false;
        }

        $routePrefix = config('medialibrary-extensions.route_prefix');
        $currentUrl = Request::path();
        $referer = Request::header('referer');

        // Check if the current URL is a demo page
        $isDemoUrl = str_contains($currentUrl, $routePrefix.'/mle-demo');

        // Check if the referer is a demo page
        $isFromDemoPage = $referer && (
            str_contains($referer, $routePrefix.'/mle-demo')
        );

        // Return true if either the current URL is a demo page or the referer is a demo page
        return $isDemoUrl || $isFromDemoPage;
    }
}
