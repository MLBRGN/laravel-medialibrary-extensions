<?php

//
// use Illuminate\Support\Facades\Artisan;
// use Illuminate\Support\Facades\Config;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Schema;
// use Mlbrgn\MediaLibraryExtensions\Providers\MediaLibraryExtensionsServiceProvider;
//
// beforeEach(function () {
//    // Set default configuration for tests
//    Config::set('media-library-extensions.demo_pages_enabled', true);
//    Config::set('media-library-extensions.demo_database_name',config('media-library-extensions.media_disks.demo'));
// });
//
// it('registers demo database when demo pages are enabled', function () {
//    // Arrange
//    $provider = new MediaLibraryExtensionsServiceProvider(app());
//
//    // Mock Config facade
//    Config::shouldReceive('set')
//        ->once()
//        ->with('database.connections.media_demo', \Mockery::type('array'))
//        ->andReturnNull();
//
//    // Mock DB facade
//    DB::shouldReceive('purge')
//        ->once()
//        ->with(config('media-library-extensions.media_disks.demo'))
//        ->andReturnNull();
//
//    DB::shouldReceive('reconnect')
//        ->once()
//        ->with(config('media-library-extensions.media_disks.demo'))
//        ->andReturnNull();
//
//    // Mock Schema facade
//    $schemaMock = \Mockery::mock('alias:' . Schema::class);
//    $schemaMock->shouldReceive('connection->hasTable')
//        ->once()
//        ->with('aliens')
//        ->andReturn(true); // Assume table exists to avoid mocking Artisan
//
//    // Act
//    $provider->registerDemoDatabase();
//
//    // Assert - verification is done by Mockery expectations
// })->skip();
//
// it('runs migrations if aliens table does not exist', function () {
//    // Arrange
//    $provider = new MediaLibraryExtensionsServiceProvider(app());
//
//    // Mock Config facade
//    Config::shouldReceive('set')
//        ->once()
//        ->with('database.connections.media_demo', \Mockery::type('array'))
//        ->andReturnNull();
//
//    // Mock DB facade
//    DB::shouldReceive('purge')
//        ->once()
//        ->with(config('media-library-extensions.media_disks.demo'))
//        ->andReturnNull();
//
//    DB::shouldReceive('reconnect')
//        ->once()
//        ->with(config('media-library-extensions.media_disks.demo'))
//        ->andReturnNull();
//
//    // Mock Schema facade
//    $schemaMock = \Mockery::mock('alias:' . Schema::class);
//    $schemaMock->shouldReceive('connection->hasTable')
//        ->once()
//        ->with('aliens')
//        ->andReturn(false); // Table doesn't exist, so migrations should run
//
//    // Mock Artisan facade
//    Artisan::shouldReceive('call')
//        ->once()
//        ->with('migrate', \Mockery::type('array'))
//        ->andReturnNull();
//
//    // Act
//    $provider->registerDemoDatabase();
//
//    // Assert - verification is done by Mockery expectations
// })->skip();
//
// it('uses the configured database name', function () {
//    // Arrange
//    $provider = new MediaLibraryExtensionsServiceProvider(app());
//    Config::set('media-library-extensions.demo_database_name', 'custom_demo_db');
//
//    // Mock Config facade
//    Config::shouldReceive('set')
//        ->once()
//        ->with('database.connections.custom_demo_db', \Mockery::type('array'))
//        ->andReturnNull();
//
//    // Mock DB facade
//    DB::shouldReceive('purge')
//        ->once()
//        ->with('custom_demo_db')
//        ->andReturnNull();
//
//    DB::shouldReceive('reconnect')
//        ->once()
//        ->with('custom_demo_db')
//        ->andReturnNull();
//
//    // Mock Schema facade
//    $schemaMock = \Mockery::mock('alias:' . Schema::class);
//    $schemaMock->shouldReceive('connection->hasTable')
//        ->once()
//        ->with('aliens')
//        ->andReturn(true); // Assume table exists to avoid mocking Artisan
//
//    // Act
//    $provider->registerDemoDatabase();
//
//    // Assert - verification is done by Mockery expectations
// })->skip();
//
// it('does not register demo database when demo pages are disabled', function () {
//    // Arrange
//    Config::set('media-library-extensions.demo_pages_enabled', false);
//
//    // Mock Config facade to ensure it's not called
//    Config::shouldNotReceive('set');
//
//    // Mock DB facade to ensure it's not called
//    DB::shouldNotReceive('purge');
//    DB::shouldNotReceive('reconnect');
//
//    // Act
//    $provider = new MediaLibraryExtensionsServiceProvider(app());
//    $provider->boot();
//
//    // Assert - verification is done by Mockery expectations
// })->skip();
