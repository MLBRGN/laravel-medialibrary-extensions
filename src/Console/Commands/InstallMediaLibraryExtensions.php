<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Console\Commands;

use Illuminate\Console\Command;

class InstallMediaLibraryExtensions extends Command
{
    protected $signature = 'media-library-extensions:install {--force : Overwrite any existing files}';

    protected $description = 'Install the media library extensions.';

    public function handle(): int
    {
        $force = $this->option('force');

        if (! $force) {
            $force = $this->confirm('Some files may already exist. Do you want to overwrite them?', false);
        }

        $this->publishWithMessage('media-library-extensions-config', config_path('media-library-extensions.php'), $force);
        $this->publishWithMessage('media-library-extensions-views', resource_path('views/vendor/media-library-extensions'), $force);
        $this->publishWithMessage('media-library-extensions-assets', public_path('vendor/media-library-extensions'), $force);
        $this->publishWithMessage('media-library-extensions-translations', resource_path('lang/vendor/media-library-extensions'), $force);
        $this->publishWithMessage('media-library-extensions-policy', app_path('Policies/MediaPolicy.php'), $force);

        $this->info('Media Library Extensions installed successfully.');
        return self::SUCCESS;
    }

    protected function publishWithMessage(string $tag, string $targetPath, bool $force): void
    {
        if (file_exists($targetPath) && ! $force) {
            $this->warn("Skipped publishing [$tag]: file/folder already exists at [$targetPath]");
            return;
        }

        $this->call('vendor:publish', [
            '--tag' => $tag,
            '--force' => $force,
        ]);
    }

}
