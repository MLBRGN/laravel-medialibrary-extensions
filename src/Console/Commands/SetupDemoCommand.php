<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;
use Mlbrgn\MediaLibraryExtensions\Providers\MediaLibraryExtensionsServiceProvider;
use Mlbrgn\MediaLibraryExtensions\Support\PackageInfrastructure;

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
        'mle:setup-demo'
    ];

    public function handle(): int
    {
        $this->info('Media Library Extensions - preparing demo databases...');

        PackageInfrastructure::register('demo');

        $demoConnection = PackageInfrastructure::connection('demo');
        $hostConnection = PackageInfrastructure::connection('demo', 'alt');

        $this->info(
            'Media Library Extensions - migrating demo database tables with demo specific migrations.'
        );

        $this->call('migrate:fresh', [
            '--database' => $demoConnection,
            '--path' => realpath(self::DEMO_MIGRATIONS_PATH),
            '--realpath' => true,
            '--force' => true,
        ]);

        $this->info(
            'Media Library Extensions - migrating demo-host-sandbox database with app migrations.'
        );

        $this->call('migrate:fresh', [
            '--database' => $hostConnection,
            '--force' => true,
        ]);

        $this->info(
            'Media Library Extensions - migrating demo-host-sandbox database tables with demo specific migrations.'
        );

        $this->call('migrate', [
            '--database' => $hostConnection,
            '--path' => realpath(self::DEMO_HOST_APP_MIGRATIONS_PATH),
            '--realpath' => true,
            '--force' => true,
        ]);

        $this->seedSingleAlienIfMissing($demoConnection);
        $this->seedSingleAlienIfMissing($hostConnection);

        $this->outroSuccess();

        return self::SUCCESS;
    }
//    public function handle(): int
//    {
//        $this->info('Media Library Extensions - preparing demo databases...');
//
//        PackageInfrastructure::register('demo');
//        // Resolve connection names and database file paths
//
//        $demoConnection = PackageInfrastructure::connection();
//        $hostConnection = PackageInfrastructure::hostConnection();
//
////        $demoConnection = MediaLibraryExtensionsServiceProvider::DEMO_CONNECTION;
////        $hostSandboxConnection = MediaLibraryExtensionsServiceProvider::DEMO_HOST_APP_CONNECTION;
//        //        $demoConnection = config('medialibrary-extensions.demo_connection', 'media_demo');
////        $hostSandboxConnection = config('medialibrary-extensions.demo_host_app_connection', 'media_demo_host_app');
//        $demoDbPath = (string) Config::get("database.connections.{$demoConnection}.database");
//        $hostDbPath = (string) Config::get("database.connections.{$hostConnection}.database");
//
//        // Ensure files exist for SQLite connections to prevent connection exceptions
//        $this->ensureSqliteFileExists($demoDbPath, $demoConnection);
//        $this->ensureSqliteFileExists($hostDbPath, $hostConnection);
//
//        $this->info('Media Library Extensions - migrating demo database tables with demo specific migrations.');
//        $this->call('migrate:fresh', [
//            '--database' => $demoConnection,
//            '--path' => realpath(self::DEMO_MIGRATIONS_PATH),
//            '--realpath' => true,
//            '--force' => true,
//        ]);
//
//        $this->info('Media Library Extensions - migrating demo-host-sandbox database with app migrations.');
//        $this->call('migrate:fresh', [
//            '--database' => $hostConnection,
//            '--force' => true,
//        ]);
//
//        $this->info('Media Library Extensions - migrating demo-host-sandbox database tables with demo specific migrations.');
//        $this->call('migrate', [
//            '--database' => $hostConnection,
//            '--path' => realpath(self::DEMO_HOST_APP_MIGRATIONS_PATH),
//            '--realpath' => true,
//            '--force' => true,
//        ]);
//
//        // The following migration step is not required for the demo setup anymore.
//        // Keeping it here commented for reference in case you need to bring in host-app
//        // specific migrations in the future.
//        // $this->info('Media Library Extensions - adding Alien table to host app database (skipped).');
//        // $this->call('migrate', [
//        //     '--path' => realpath(self::HOST_APP_MIGRATIONS_PATH),
//        //     '--realpath' => true,
//        //     '--force' => true,
//        // ]);
//
//        // Seed a single Alien model in both demo databases so the demo page
//        // can consistently use the first record without creating a new one on refresh.
//        $this->seedSingleAlienIfMissing($demoConnection);
//        $this->seedSingleAlienIfMissing($hostConnection);
//
//        $this->outroSuccess();
//
//        return self::SUCCESS;
//    }

    /**
     * Ensure an SQLite database file exists for the given connection path.
     */
    protected function ensureSqliteFileExists(string $absolutePath, string $connectionName): void
    {
        if ($absolutePath === '' || $absolutePath === ':memory:') {
            // Nothing to create for in-memory or empty path
            return;
        }

        // Create directory if needed
        $dir = dirname($absolutePath);
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        if (! File::exists($absolutePath)) {
            File::put($absolutePath, '');
            $this->line(" - Created SQLite file for connection '{$connectionName}': {$absolutePath}");
        } else {
            $this->line(" - SQLite file already exists for connection '{$connectionName}': {$absolutePath}");
        }
    }

    protected function outroSuccess(): void
    {
        $this->info('Media Library Extensions demo setup completed.');
    }

    /**
     * Ensure there is at least one Alien record present on a given connection.
     */
    protected function seedSingleAlienIfMissing(string $connection): void
    {
        try {
            $alien = new Alien();
            $alien->setConnection($connection);

            $count = $alien->newQuery()->count();
            if ($count === 0) {
                $created = $alien->newQuery()->create();
                $this->line(" - Seeded Alien id={$created->getKey()} on connection '{$connection}'");
            } else {
                $this->line(" - Alien records already present on connection '{$connection}' (count={$count})");
            }
        } catch (\Throwable $e) {
            $this->warn(" - Failed to seed Alien on connection '{$connection}': ".$e->getMessage());
        }
    }
}
