<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Support\Middleware;

use Closure;
use Illuminate\Http\Request;

class MleTestConfigMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Cleanup files bag recursively to prevent TypeError in Laravel's file handling
        if ($request->files->count() > 0) {
            $request->files->replace($this->cleanFiles($request->files->all()));
        }

        $overrides = session('mle_config_overrides', []);
        
        if ($queryOverrides = $request->query('mle_config')) {
            $overrides = array_merge($overrides, $queryOverrides);
        }

        foreach ($overrides as $key => $value) {
            \Illuminate\Support\Facades\Log::debug("MleTestConfigMiddleware - Applying override: $key => $value");
            config(["medialibrary-extensions.$key" => $value]);
        }

        return $next($request);
    }

    protected function cleanFiles(array $files): array
    {
        return array_filter(array_map(function ($file) {
            if (is_array($file)) {
                return $this->cleanFiles($file);
            }
            
            return $file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile ? $file : null;
        }, $files));
    }
}
