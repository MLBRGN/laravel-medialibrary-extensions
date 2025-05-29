<?php

namespace Mlbrgn\MediaLibraryExtensions\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

// TODO deprecated remove
/**
 * Handles asset delivery for the package by dynamically resolving file paths
 * and determining MIME types. It serves the requested asset if it exists
 * in the predefined map.
 */
class PackageAssetController extends Controller
{
    protected array $map = [
        'media-library-extensions.js' => '/../../..//dist/js/media-library-extensions.js',
        'media-library-extensions.css' => '/../../../dist/css/media-library-extensions.css',
    ];

    public function __invoke(string $name)
    {
        //        if (! auth()->check()) {
        //            abort(403, __('media-library-extensions::messages.not-authorized'));
        //        }

        if (! isset($this->map[$name])) {
            abort(404);
        }

        $path = realpath(__DIR__.$this->map[$name]);

        if (! $path || ! File::exists($path)) {
            abort(404);
        }

        return Response::file($path, [
            'Content-Type' => $this->mimeType($path),
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    protected function mimeType(string $path): string
    {
        return match (pathinfo($path, PATHINFO_EXTENSION)) {
            'css' => 'text/css',
            'js' => 'application/javascript',
            default => File::mimeType($path),
        };
    }
}
