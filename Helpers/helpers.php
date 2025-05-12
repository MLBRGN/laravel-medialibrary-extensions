<?php

if (! function_exists('mle_package_asset')) {
    function mle_package_asset($name): string
    {
        return route(config('media-library-extensions.route-prefix').'-package.assets', ['name' => $name]);
    }
}

// TODO still needed?
if (! function_exists('media_manager_theme')) {
    function media_manager_theme(): string
    {
        $supported = config('media-library-extensions.supported_frontend_themes', ['plain']);
        $configured = config('media-library-extensions.frontend-theme', 'plain');

        return in_array($configured, $supported) ? $configured : 'plain';
    }
}

if (! function_exists('mle_media_class')) {

    function mle_media_class(string $key, string $default = ''): string
    {
        $theme = config('media-library-extensions.frontend-theme', 'plain');
        $classes = config("media-library-extensions.classes.{$theme}", []);

        return $classes[$key] ?? $default;
    }
}
