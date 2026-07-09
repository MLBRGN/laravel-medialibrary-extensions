<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

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
        $this->info('Media Library Extensions - preparing demo databases...');

        // Resolve connection names and database file paths
        $demoConnection = config('medialibrary-extensions.demo_connection', 'media_demo');
        $hostSandboxConnection = config('medialibrary-extensions.demo_host_app_connection', 'media_demo_host_app_sandbox');

        $demoDbPath = (string) Config::get("database.connections.{$demoConnection}.database");
        $hostDbPath = (string) Config::get("database.connections.{$hostSandboxConnection}.database");

        // Ensure files exist for SQLite connections to prevent connection exceptions
        $this->ensureSqliteFileExists($demoDbPath, $demoConnection);
        $this->ensureSqliteFileExists($hostDbPath, $hostSandboxConnection);

        $this->info('Media Library Extensions - migrating demo database tables with demo specific migrations.');
        $this->call('migrate:fresh', [
            '--database' => $demoConnection,
            '--path' => realpath(self::DEMO_MIGRATIONS_PATH),
            '--realpath' => true,
            '--force' => true,
        ]);

        $this->info('Media Library Extensions - migrating demo-host-sandbox database with app migrations.');
        $this->call('migrate:fresh', [
            '--database' => $hostSandboxConnection,
            '--force' => true,
        ]);

        $this->info('Media Library Extensions - migrating demo-host-sandbox database tables with demo specific migrations.');
        $this->call('migrate', [
            '--database' => $hostSandboxConnection,
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
}
