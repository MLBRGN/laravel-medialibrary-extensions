<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Console\Commands;

use Illuminate\Console\Command;
use Mlbrgn\MediaLibraryExtensions\Providers\MediaLibraryExtensionsServiceProvider;

class InstallMediaLibraryExtensions extends Command
{
    protected $signature = 'media-library-extensions:install {--force : Overwrite any existing files}';
    protected $description = 'Install the media library extensions (publishes required assets and installs npm package).';

    public function handle(): int
    {
        $force = $this->option('force');

        if (! $force) {
            $force = $this->confirm('Some files may already exist. Do you want to overwrite them?', false);
        }

        // Only publish required assets (CSS/JS)
        $this->publishWithMessage(
            'assets',
            public_path('vendor/medialibrary-extensions'),
            $force
        );

        // Install npm package (required for JS functionality)
        $this->installNodePackage();

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
            '--provider' => MediaLibraryExtensionsServiceProvider::class,
        ]);
    }

    protected function installNodePackage(): void
    {
        $this->info('Installing npm package @mlbrgn/imageeditor...');

        $output = [];
        $returnVar = 0;

        exec('npm install @mlbrgn/imageeditor', $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error('npm install failed: ' . implode("\n", $output));
        } else {
            $this->info('npm package installed successfully.');
        }
    }
}
