<?php

// if (! function_exists('mle_package_asset')) {
//    function mle_package_asset($name): string
//    {
//        return route(config('laravel-medialibrary-extensions.route_prefix').'-package.assets', ['name' => $name]);
//    }
// }

use Symfony\Component\VarDumper\VarDumper;

if (! function_exists('mle_package_asset')) {
    function mle_package_asset(string $path): string
    {
        return asset("vendor/media-library-extensions/{$path}");
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

if (! function_exists('mle_prefix_route')) {

    function mle_prefix_route(string $suffix): string
    {
        return config('media-library-extensions.route_prefix').'-'.$suffix;
    }
}

if (! function_exists('component_exists')) {
    function component_exists(string $name): bool
    {
        return array_key_exists($name, Blade::getClassComponentAliases())
            || View::exists("components.$name");
    }
}

if (! function_exists('status_session_prefix')) {
    function status_session_prefix(): string
    {
        return config('media-library-extensions.status_session_prefix');
    }
}

if (! function_exists('extractYouTubeId')) {

    function extractYouTubeId(string $url): ?string
    {
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/|shorts\/))([^\?&"\'<>#]+)/', $url,
            $matches)) {
            return $matches[1];
        }

        return null;
    }
}

if (! function_exists('getHumanMimeTypeLabel')) {
    function getHumanMimeTypeLabel(string $mimeType): string {

        $mimeTypeLabels = config('media-library-extensions.mimeTypeLabels');
        if (array_key_exists($mimeType, $mimeTypeLabels)) {
            return $mimeTypeLabels[$mimeType];
        }
        return $mimeType;
    }
}

if (! function_exists('isMediaType')) {
    function isMediaType($medium, string $type): bool
    {
        $mimeType = $medium->mime_type ?? null;
        $allowed = config("media-library-extensions.allowed_mimetypes.{$type}", []);
        return in_array($mimeType, $allowed, true);
    }
}

if (! function_exists('sanitize_filename')) {
    function sanitizeFilename(string $name): string
    {
        return Str::slug($name, '_'); // converts to lowercase, replaces spaces/special chars with underscore
    }
}
