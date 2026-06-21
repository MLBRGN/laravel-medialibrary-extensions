<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Mlbrgn\MediaLibraryExtensions\Providers\MediaLibraryExtensionsServiceProvider;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;

class SetupDemoCommand extends Command
{
    protected $signature = 'medialibrary-extensions:demo-setup';

    protected $description = 'Setup the demo.';

    public const DEMO_MIGRATIONS_PATH =
        __DIR__.'/../../../database/demo-migrations';

    public const DEMO_HOST_APP_MIGRATIONS_PATH =
        __DIR__.'/../../../database/demo-host-app-migrations';

    public const HOST_APP_MIGRATIONS_PATH =
        __DIR__.'/../../../database/host-app-migrations';

    // short form aliases
    protected $aliases = [
        'mle:demo-setup',
    ];

    public function handle(): int
    {
        $this->info('Media Library Extensions - migrating demo database tables with demo specific migrations.');
        $this->call('migrate:fresh', [
            '--database' => config('medialibrary-extensions.demo_connection'),
            '--path' => realpath(self::DEMO_MIGRATIONS_PATH),
            '--realpath' => true,
            '--force' => true,
        ]);

        $this->info('Media Library Extensions - migrating demo-host-sandbox database with app migrations.');
        $this->call('migrate:fresh', [
            '--database' => config('medialibrary-extensions.demo_host_app_connection'),
            '--force' => true,
        ]);

        $this->info('Media Library Extensions - migrating demo-host-sandbox database tables with demo specific migrations.');
        $this->call('migrate', [
            '--database' => config('medialibrary-extensions.demo_host_app_connection'),
            '--path' => realpath(self::DEMO_HOST_APP_MIGRATIONS_PATH),
            '--realpath' => true,
            '--force' => true,
        ]);

        $this->info('Media Library Extensions - adding Alien table to host app database (TODO don\'t want this).');
        $this->call('migrate', [
//            '--database' => config('medialibrary-extensions.demo_host_app_connection'),
            '--path' => realpath(self::HOST_APP_MIGRATIONS_PATH),
            '--realpath' => true,
            '--force' => true,
        ]);


        $this->outroSuccess();

        return self::SUCCESS;
    }

    protected function outroSuccess(): void
    {
        $this->info('Media Library Extensions demo setup completed.');
    }
}
