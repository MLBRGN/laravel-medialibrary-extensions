<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class PackageAssetController extends Controller
{
    protected array $map = [
        'preview.css' => '/../dist/css/mlbrgn-preview.css',
        'preview.js' => '/../dist/js/mlbrgn-preview.js',
        'main.css' => '/../dist/css/mlbrgn-form-components.css',
        'main.js' => '/../dist/js/mlbrgn-form-components.js',
        'html-editor.js' => '/../dist/js/mlbrgn-html-editor.js',
        'form-validation.js' => '/../dist/js/mlbrgn-form-validation.js',
        'form-validation.css' => '/../dist/css/mlbrgn-form-validation.css',
        'button-image.png' => '/../public/images/button-image.png',
        'icon-envelope.png' => '/../public/images/icon-envelope.png',
        'sprite.svg' => '/../public/images/sprite.svg',
    ];

    public function __invoke(string $name)
    {
        dd($name);
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
