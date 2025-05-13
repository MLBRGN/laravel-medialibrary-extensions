<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class PackageAssetController extends Controller
{
    protected array $map = [
        'mediaPreviewModal.js' => '/../../..//dist/js/mediaPreviewModal.js',
        'media-library-extensions.css' => '/../../../dist/css/media-library-extensions.css',
    ];

    public function __invoke(string $name)
    {
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
