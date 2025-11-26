<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Console\Commands;

use Illuminate\Console\Command;
use Mlbrgn\MediaLibraryExtensions\Providers\MediaLibraryExtensionsServiceProvider;

class InstallMediaLibraryExtensions extends Command
{
    protected $signature = 'media-library-extensions:install {--force : Overwrite any existing files}';
    protected $description = 'Install the media library extensions (publishes required assets and installs npm package).';

    // short form aliases
    protected $aliases = [
        'mle:install',
        'mle:i',
    ];

    public function handle(): int
    {
        $force = $this->option('force');

        if (! $force) {
            $force = $this->confirm('Some files may already exist. Do you want to overwrite them?', false);
        }

        // Publish required assets (CSS/JS)
        $this->publishWithMessage(
            'assets',
            public_path('vendor/medialibrary-extensions'),
            $force
        );

        // Publish config
        $this->publishWithMessage(
            'config',
            public_path('vendor/medialibrary-extensions'),
            $force
        );

        // show outro
        $this->outroSuccess();

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

    protected function outroSuccess(): void
    {
        $this->info('Media Library Extensions installed successfully.');
        $this->info('');
        $this->comment('----------------------------------------------');
        $this->comment('NOTE: For the "image editor" to work');
        $this->comment('run the following commands in your own project');
        $this->comment('----------------------------------------------');
        $this->info('');
        $this->comment('  npm install');
        $this->comment('  npm install @mlbrgn/media-library-extensions');
        $this->comment('  npm run build');
        $this->info('');

    }
}
