<?php

namespace Mlbrgn\MediaLibraryExtensions\Helpers;

use Illuminate\Support\Facades\File;

/**
 * Asset Versioning Helper
 *
 * This class helps with loading versioned assets using the Vite manifest file.
 * It ensures that when assets are rebuilt with new content, browsers will load
 * the new versions instead of using cached versions.
 *
 * The Vite build process generates a manifest.json file that maps original filenames
 * to their versioned counterparts with content hashes. This class reads that manifest
 * and provides the correct versioned paths for assets.
 */
class AssetVersioning
{
    /**
     * The path to the manifest file.
     *
     * @var string
     */
    protected static $manifestPath;

    /**
     * The loaded manifest data.
     *
     * @var array|null
     */
    protected static $manifest = null;

    /**
     * Get the versioned path for a file.
     *
     * @param string $file
     * @return string
     */
    public static function versionedAsset(string $file): string
    {
        // If the file is not in the manifest, return the original file
        if (! static::fileExistsInManifest($file)) {
            return asset('vendor/mlbrgn/media-library-extensions/' . $file);
        }

        // Get the versioned file path from the manifest
        $versionedFile = static::getManifestFile($file);

        return asset('vendor/mlbrgn/media-library-extensions/' . $versionedFile);
    }

    /**
     * Check if a file exists in the manifest.
     *
     * @param string $file
     * @return bool
     */
    protected static function fileExistsInManifest(string $file): bool
    {
        return isset(static::getManifest()[$file]);
    }

    /**
     * Get the versioned file path from the manifest.
     *
     * @param string $file
     * @return string
     */
    protected static function getManifestFile(string $file): string
    {
        return static::getManifest()[$file] ?? $file;
    }

    /**
     * Get the manifest data.
     *
     * @return array
     */
    protected static function getManifest(): array
    {
        if (static::$manifest !== null) {
            return static::$manifest;
        }

        $manifestPath = static::getManifestPath();

        if (! File::exists($manifestPath)) {
            return static::$manifest = [];
        }

        return static::$manifest = json_decode(File::get($manifestPath), true) ?? [];
    }

    /**
     * Get the path to the manifest file.
     *
     * @return string
     */
    protected static function getManifestPath(): string
    {
        if (static::$manifestPath) {
            return static::$manifestPath;
        }

        return static::$manifestPath = public_path('vendor/mlbrgn/media-library-extensions/manifest.json');
    }

    /**
     * Set a custom manifest path.
     *
     * @param string $path
     * @return void
     */
    public static function setManifestPath(string $path): void
    {
        static::$manifestPath = $path;
        static::$manifest = null;
    }
}
