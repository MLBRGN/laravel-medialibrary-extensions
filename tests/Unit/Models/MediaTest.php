<?php

//
// use Illuminate\Support\Facades\Config;
// use Mlbrgn\MediaLibraryExtensions\Helpers\DemoHelper;
// use Mlbrgn\MediaLibraryExtensions\Models\Media;
// use Mockery\MockInterface;
//
// // Create a test double for the Media class
// class MediaTest extends Media
// {
//    protected $parentConnection = 'default_connection';
//
//    public function getParentConnectionName()
//    {
//        return $this->parentConnection;
//    }
//
//    public function setParentConnection($connection)
//    {
//        $this->parentConnection = $connection;
//        return $this;
//    }
//
//    // Override parent method to call our test method instead
//    public function getConnectionName()
//    {
//        if (config('media-library-extensions.demo_pages_enabled') && DemoHelper::isRequestFromDemoPage()) {
//            return config('media-library-extensions.temp_database_name');
//        }
//
//        return $this->getParentConnectionName();
//    }
// }
//
// beforeEach(function () {
//    // Set default configuration for tests
//    Config::set('media-library-extensions.demo_pages_enabled', true);
//    Config::set('media-library-extensions.temp_database_name', 'media_demo');
// })->skip();
//
// it('uses default connection when demo pages are disabled', function () {
//    // Arrange
//    Config::set('media-library-extensions.demo_pages_enabled', false);
//    $media = new TestMedia();
//
//    // Act & Assert
//    expect($media->getConnectionName())->toBe('default_connection');
// })->skip();
//
// it('uses default connection when not on a demo page', function () {
//    // Arrange
//    $media = new TestMedia();
//
//    // Mock DemoHelper to return false
//    $demoHelper = \Mockery::mock('alias:' . DemoHelper::class);
//    $demoHelper->shouldReceive('isRequestFromDemoPage')
//        ->once()
//        ->andReturn(false);
//
//    // Act & Assert
//    expect($media->getConnectionName())->toBe('default_connection');
// })->skip();
//
// it('uses demo connection when on a demo page', function () {
//    // Arrange
//    $media = new TestMedia();
//
//    // Mock DemoHelper to return true
//    $demoHelper = \Mockery::mock('alias:' . DemoHelper::class);
//    $demoHelper->shouldReceive('isRequestFromDemoPage')
//        ->once()
//        ->andReturn(true);
//
//    // Act & Assert
//    expect($media->getConnectionName())->toBe('media_demo');
// })->skip();
//
// it('uses the configured demo database name', function () {
//    // Arrange
//    Config::set('media-library-extensions.temp_database_name', 'custom_demo_db');
//    $media = new TestMedia();
//
//    // Mock DemoHelper to return true
//    $demoHelper = \Mockery::mock('alias:' . DemoHelper::class);
//    $demoHelper->shouldReceive('isRequestFromDemoPage')
//        ->once()
//        ->andReturn(true);
//
//    // Act & Assert
//    expect($media->getConnectionName())->toBe('custom_demo_db');
// })->skip();
