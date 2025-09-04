<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class ToggleRepository extends Command
{
    protected $signature = 'media-library-extensions:toggle-repository {--force : Skip confirmation prompts}';

    protected $description = 'Toggle between local and Packagist repositories for development packages. Manages symlinks, composer require versions, and runs composer update.';

    // ------------------ Constants ------------------
    protected const COMPOSER_JSON = 'composer.json';
    protected const DIST_FOLDER = 'dist';
    protected const DEV_VERSION = 'dev-main';
    protected const VENDOR_PATH = 'vendor';
    protected const VIEWS_PATH = 'views/vendor/laravel-media-library-extensions';
    protected const PUBLISH_PROVIDER = 'Mlbrgn\MediaLibraryExtensions\Providers\MediaLibraryExtensionsServiceProvider';
    protected const ASSETS_PUBLISH_TAG = 'media-library-extensions-assets';

    // ------------------ Packages ------------------
    protected array $packages = [
        'mlbrgn/laravel-medialibrary-extensions' => [
            'path' => './packages/mlbrgn/laravel-medialibrary-extensions',
            'symlink' => 'mlbrgn/media-library-extensions',
            'local_env_key' => 'MLE_USING_LOCAL_PACKAGE',
        ],
        // Add more packages here if needed
    ];

    public function handle(): int
    {
        $composerPath = base_path(self::COMPOSER_JSON);

        if (! file_exists($composerPath)) {
            $this->error('composer.json not found!');
            return self::FAILURE;
        }

        $composer = json_decode(file_get_contents($composerPath), true);
        $repositories = $composer['repositories'] ?? [];
        $originalRequires = $composer['extra']['original_require'] ?? [];

        $toggled = [];

        foreach ($this->packages as $name => $data) {
            $pathRepo = $data['path'];
            $symlinkName = $data['symlink'];
            $linkPath = public_path(self::VENDOR_PATH.'/'.$symlinkName);
            $targetPath = realpath(base_path(trim($pathRepo, './').'/'.self::DIST_FOLDER));

            $isLinked = collect($repositories)->contains(fn ($repo) => ($repo['type'] ?? '') === 'path' && ($repo['url'] ?? '') === $pathRepo);
            $usingLocal = ! $isLinked; // true if you switched to local
            $envKey = $data['local_env_key'] ?? '';
            $this->setLocalPackageFlag($usingLocal, $envKey);

            if ($isLinked) {
                if (! $this->option('force') && ! $this->confirm("Remove local path for [$name]?")) {
                    continue;
                }

                $this->cleanVendorPackage($name);
                $this->cleanPublishedViews();

                $repositories = array_values(array_filter($repositories, fn ($repo) => ! ($repo['type'] === 'path' && $repo['url'] === $pathRepo)));

                $this->removePath($linkPath);
                $this->info("🔗 Removed local path for $name");

                if (isset($originalRequires[$name])) {
                    $composer['require'][$name] = $originalRequires[$name];
                    unset($composer['extra']['original_require'][$name]);
                    $this->info("🔁 Restored version for $name to {$composer['require'][$name]}");
                } else {
                    $this->warn("⚠️ No stored original version for $name; leaving as-is.");
                }

                $this->publishAssets();
                $toggled[] = $name;
            } else {
                if (! $this->option('force') && ! $this->confirm("Use local path for [$name]?")) {
                    continue;
                }

                $this->cleanVendorPackage($name);
                $this->cleanPublishedViews();

                $repositories[] = [
                    'type' => 'path',
                    'url' => $pathRepo,
                    'options' => ['symlink' => true],
                ];

                $currentVersion = $composer['require'][$name] ?? null;
                if ($currentVersion && $currentVersion !== self::DEV_VERSION) {
                    if (! isset($composer['extra']['original_require'][$name])) {
                        $composer['extra']['original_require'][$name] = $currentVersion;
                        $this->info("💾 Saved original version for $name: $currentVersion");
                    } else {
                        $this->line("ℹ️ Skipping saving original version for $name; already saved as {$composer['extra']['original_require'][$name]}");
                    }
                }

                $composer['require'][$name] = self::DEV_VERSION;
                $this->info("🔖 Set version for $name to " . self::DEV_VERSION);

                if ($targetPath && is_dir($targetPath)) {
                    $this->removePath($linkPath);
                    File::ensureDirectoryExists(dirname($linkPath));
                    symlink($targetPath, $linkPath);
                    $this->info("🔗 Created symlink: $linkPath → $targetPath");
                } else {
                    $this->warn("⚠️ dist folder not found for $name, skipping symlink.");
                }

                $toggled[] = $name;
            }
        }

        $composer['repositories'] = $repositories;

        if (empty($composer['extra']['original_require'] ?? [])) {
            unset($composer['extra']['original_require']);
        }

        file_put_contents($composerPath, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        if (count($toggled)) {
            $this->info('📦 Running composer update for: ' . implode(', ', $toggled));
            $process = Process::fromShellCommandline('composer update ' . implode(' ', $toggled));
            $process->setTty(Process::isTtySupported());
            $process->run(function ($type, $buffer) {
                echo $buffer;
            });
        } else {
            $this->info('✅ Nothing to update.');
        }

        return self::SUCCESS;
    }

    protected function removePath(string $path): void
    {
        if (is_link($path)) {
            File::delete($path);
            $this->info("🗑 Removed symlink: $path");
        } elseif (is_dir($path)) {
            File::deleteDirectory($path);
            $this->info("🗑 Removed directory: $path");
        } elseif (file_exists($path)) {
            File::delete($path);
            $this->info("🗑 Removed file: $path");
        }
    }

    protected function cleanVendorPackage(string $name): void
    {
        $vendorPath = base_path(self::VENDOR_PATH . '/' . str_replace('/', DIRECTORY_SEPARATOR, $name));
        if (is_link($vendorPath) || is_dir($vendorPath) || file_exists($vendorPath)) {
            $this->removePath($vendorPath);
            $this->info("🧹 Cleaned vendor package directory: $vendorPath");
        }
    }

    protected function cleanPublishedViews(): void
    {
        $viewsPath = resource_path(self::VIEWS_PATH);
        if (is_dir($viewsPath)) {
            File::deleteDirectory($viewsPath);
            $this->info("🧹 Cleaned published views directory: $viewsPath");
        }
    }

    protected function publishAssets(): void
    {
        $this->info("📦 Publishing package assets for " . self::PUBLISH_PROVIDER);
        $this->call('vendor:publish', [
            '--provider' => self::PUBLISH_PROVIDER,
            '--tag' => self::ASSETS_PUBLISH_TAG,
            '--force' => true,
        ]);
    }

    protected function setLocalPackageFlag(bool $usingLocal, string $envKey): void
    {
        $envKey = trim($envKey);
        if ($envKey === '') return;

        $envLocalPath = base_path('.env.local');

        if (! file_exists($envLocalPath)) {
            file_put_contents($envLocalPath, "");
        }

        $content = file_get_contents($envLocalPath);

        if (str_contains($content, $envKey . '=')) {
            $content = preg_replace(
                '/' . preg_quote($envKey, '/') . '=.*/',
                $envKey . '=' . ($usingLocal ? 'true' : 'false'),
                $content
            );
        } else {
            // Append key/value with a single newline
            $content .= PHP_EOL . $envKey . '=' . ($usingLocal ? 'true' : 'false');
        }

        // Ensure no extra blank lines at the end
        $content = trim($content) . PHP_EOL;

        file_put_contents($envLocalPath, $content);

        $this->info('🔖 ' . $envKey . ' set to ' . ($usingLocal ? 'true' : 'false'));
    }

}
