<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallMediaLibraryExtensions extends Command
{
    protected $signature = 'mle:install';

    protected $description = 'Install the media library extensions.';

    public function handle(): void
    {
        $this->info('Installing media library extensions...');

        $this->info('Publishing configuration...');

        if (! $this->configExists('media-library-extensions.php')) {
            $this->publishConfiguration();
            $this->info('Published configuration');
        } else {
            if ($this->shouldOverwriteConfig()) {
                $this->info('Overwriting configuration file...');
                $this->publishConfiguration($force = true);
            } else {
                $this->info('Existing configuration was not overwritten');
            }
        }

        $this->info('Installed media library extensions...');
    }

    private function configExists($fileName)
    {
        return File::exists(config_path($fileName));
    }

    private function shouldOverwriteConfig()
    {
        return $this->confirm(
            'Config file already exists. Do you want to overwrite it?',
            false
        );
    }

    private function publishConfiguration($forcePublish = false)
    {
        $params = [
            '--provider' => "Mlbrgn\SpatieMediaLibraryExtensions\SpatieMediaLibraryExtensions\Providers\MediaLibraryServiceProvider",
            '--tag' => 'config',
        ];

        if ($forcePublish === true) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);
    }
}
