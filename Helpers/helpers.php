<?php

if (! function_exists('mle_package_asset')) {
    function mle_package_asset($name): string
    {
        return route(config('media-library-extensions.route_prefix').'-package.assets', ['name' => $name]);
    }
}

// TODO still needed?
if (! function_exists('media_manager_theme')) {
    function media_manager_theme(): string
    {
        $supported = config('media-library-extensions.supported_frontend_themes', ['plain']);
        $configured = config('media-library-extensions.frontend_theme', 'plain');

        return in_array($configured, $supported) ? $configured : 'plain';
    }
}

if (! function_exists('mle_media_class')) {

    function mle_media_class(string $key, string $default = ''): string
    {
        $theme = config('media-library-extensions.frontend_theme', 'plain');
        $classes = config("media-library-extensions.classes.{$theme}", []);

        return $classes[$key] ?? $default;
    }
}

if (! function_exists('mle_prefix_route')) {

    function mle_prefix_route(string $suffix): string
    {
        return config('media-library-extensions.route_prefix').'-'.$suffix;
    }
}

if (! function_exists('component_exists')) {
    function component_exists(string $name): bool
    {
        return \Illuminate\Support\Facades\Blade::getClassComponentAliases()->has($name)
            || \Illuminate\Support\Facades\View::exists("components.$name");
    }
}

if (! function_exists('status_session_prefix')) {
    function status_session_prefix(string $name): string
    {
        return config('media-library-extensions.status_session_prefix').$name;
    }
}
