<?php

namespace Mlbrgn\MediaLibraryExtensions\Support;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

// Registers connections/databases/disks.
class PackageInfrastructure
{

    protected const PROFILES = [

        'demo' => [

            'connections' => [
                'default' => 'mle_demo_default',
                'alt'     => 'mle_demo_alt',
            ],

            'databases' => [
                'default' => 'mle-demo-default.sqlite',
                'alt'     => 'mle-demo-alt.sqlite',
            ],

            'disk' => [
                'name' => 'mle_demo_disk',
                'root' => 'storage/app/public/mle_demo_disk',
                'url'  => '/storage/mle_demo_disk',
            ],

            'migrations' => [
                'default' => [
                    'database/migrations',
                ],

                'alt' => [
                    'database/demo-migrations',
                ],
            ],

        ],

        'test' => [

            'connections' => [
                'default' => 'mle_test_default',
                'alt'     => 'mle_test_alt',
            ],

            'databases' => [
                'default' => 'mle-test-default.sqlite',
                'alt'     => 'mle-test-alt.sqlite',
            ],

            'disk' => [
                'name' => 'mle_test_disk',
                'root' => 'tests/Support/storage/mle_test_disk',
                'url'  => '/storage/mle_test_disk',
            ],

            'migrations' => [
                'default' => [
                    'database/migrations',
                ],

                'alt' => [
                    'database/demo-migrations',
                ],
            ],
        ],

    ];

    public static function register(string $profile): void
    {
        if (! isset(self::PROFILES[$profile])) {
            throw new \InvalidArgumentException(
                "Unknown infrastructure profile: {$profile}"
            );
        }

        self::registerConnections($profile);
        self::registerDisk($profile);

        // only force default for test profile (or when unit tests are running)
        if ($profile === 'test' || app()->runningUnitTests()) {
            self::setDefaultConnection($profile);
        }

        config()->set('session.driver', 'file');
        config()->set('session.files', storage_path('framework/sessions'));
    }

    protected static function registerConnections(string $profile): void
    {
        $config = self::PROFILES[$profile];

        foreach ($config['connections'] as $key => $connection) {

            $database = self::databasePath(
                $profile,
                $config['databases'][$key]
            );

            self::ensureDatabaseFile($database);

            Config::set(
                "database.connections.{$connection}",
                [
                    'driver' => 'sqlite',
                    'database' => $database,
                    'prefix' => '',
                    'foreign_key_constraints' => true,
                ]
            );
        }
    }

    protected static function registerDisk(string $profile): void
    {
        $disk = self::PROFILES[$profile]['disk'];

        Config::set(
            "filesystems.disks.{$disk['name']}",
            [
                'driver' => 'local',
                'root' => base_path($disk['root']),
                'url' => $disk['url'],
                'visibility' => 'public',
            ]
        );
    }

    protected static function setDefaultConnection(string $profile): void
    {
        // TODO i don't ever want to set the default connection! this has effects on the whole app!
//        if (!app()->runningInConsole()) {
            config()->set(
                'database.default',
                self::connection($profile)
            );
//        }
    }

    protected static function ensureDatabaseFile(string $path): void
    {
        File::ensureDirectoryExists(dirname($path));

        if (! File::exists($path)) {
            File::put($path, '');
        }
    }

    protected static function databasePath(
        string $profile,
        string $filename
    ): string {

        return match ($profile) {

            'demo' =>
            storage_path("app/medialibrary-extensions/demo/{$filename}"),

            'test' =>
            base_path("tests/database/{$filename}"),

        };
    }

    public static function connection(
        string $profile,
        string $name = 'default'
    ): string {
        return self::PROFILES[$profile]['connections'][$name];
    }


    public static function disk(string $profile): string
    {
        return self::PROFILES[$profile]['disk']['name'];
    }


    public static function enabled(): bool
    {
        return (bool) config(
            'medialibrary-extensions.demo_pages_enabled'
        );
    }

    public static function migrations(
        string $profile,
        string $connection = 'default'
    ): array {

        return self::PROFILES[$profile]['migrations'][$connection] ?? [];
    }

    public static function connections(string $profile): array
    {
        return self::PROFILES[$profile]['connections'];
    }

    public static function migrationPaths(
        string $profile,
        string $connection = 'default'
    ): array {
        return self::PROFILES[$profile]['migrations'][$connection] ?? [];
    }

    public static function diskUrlSegment(string $profile): string
    {
        return basename(
            self::PROFILES[$profile]['disk']['url']
        );
    }

//
//    public static function registerEverything(): void {
//
//    }
//
//    public static function ensureDatabaseFiles(): void {
//
//    }
//
//    public static function registerDemoInfrastructure(): void {
//
//    }
//
//    public static function registerTestInfrastructure(): void {
//
//    }
//
//    public static function connection(): void {
//
//    }
//
//    public static function disk(): void {
//
//    }
//
//    public static function databasePath(): void {
//
//    }
//
//    public static function enabled(): bool
//    {
//        return (bool) config('medialibrary-extensions.demo_pages_enabled');
//    }

//    public static function disk(): string
//    {
//        return app()->environment('testing')
//            ? self::TEST_DISK
//            : self::DEMO_DISK;
//    }
//
//    public static function databasePath(
//        string $packageShortName,
//        bool   $host = false
//    ): string
//    {
//        $suffix = $host
//            ? 'demo-host-app.sqlite'
//            : 'demo.sqlite';
//
//        if (app()->environment('testing')) {
//            $suffix = $host
//                ? 'test-host-app.sqlite'
//                : 'test-demo.sqlite';
//        }
//
//        return storage_path(
//            "app/{$packageShortName}/demo/{$packageShortName}-{$suffix}"
//        );
//    }
//
//    public static function connectionConfig(string $database): array
//    {
//        return [
//            'driver' => 'sqlite',
//            'database' => $database,
//            'prefix' => '',
//            'foreign_key_constraints' => true,
//        ];
//    }
//
//    public static function diskConfig(string $packageShortName): array
//    {
//        return [
//            'driver' => 'local',
//            'root' => app()->environment('testing')
//                ? base_path("tests/Support/storage/{$packageShortName}")
//                : storage_path("app/public/{$packageShortName}/demo-media"),
//
//            'url' => app()->environment('testing')
//                ? "/storage/{$packageShortName}"
//                : asset("storage/{$packageShortName}/demo-media"),
//
//            'visibility' => 'public',
//        ];
//    }
}
