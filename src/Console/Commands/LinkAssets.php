<?php

namespace Mlbrgn\MediaLibraryExtensions\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LinkAssets extends Command
{
    protected $signature = 'media-library-extensions:link-assets';

    protected $description = 'Symlink the package dist folder to public/vendor/laravel-medialibrary-extensions, so that css and js stay up to date during development of this package. ';

    public function handle(): int
    {
        $target = realpath(__DIR__.'/../../../dist');
        $link = public_path('vendor/media-library-extensions');

        if (! $target || ! is_dir($target)) {
            $this->error("Target dist folder not found at: $target");

            return 1;
        }

        if (file_exists($link) || is_link($link)) {
            $this->info("Removing existing link at: $link");
            File::delete($link);
        }

        $this->info("Creating symlink: $link → $target");
        symlink($target, $link);

        $this->info('Symlink created successfully.');

        return 0;
    }
}
