<?php
//
//use Illuminate\Support\Facades\Config;
//use Mlbrgn\MediaLibraryExtensions\Helpers\DemoHelper;
//use Mlbrgn\MediaLibraryExtensions\Models\demo\Aliens;
//
//// Create a test double for the Aliens class
//class AliensTest extends Aliens
//{
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
//    public function getConnectionName(): string
//    {
//        if (config('media-library-extensions.demo_pages_enabled') && DemoHelper::isRequestFromDemoPage()) {
//            return config('media-library-extensions.temp_database_name');
//        }
//
//        return $this->getParentConnectionName();
//    }
//}
//
//beforeEach(function () {
//    // Set default configuration for tests
//    Config::set('media-library-extensions.demo_pages_enabled', true);
//    Config::set('media-library-extensions.temp_database_name', 'media_demo');
//})->skip();
//
//it('uses default connection when demo pages are disabled', function () {
//    // Arrange
//    Config::set('media-library-extensions.demo_pages_enabled', false);
//    $aliens = new TestAliens();
//
//    // Act & Assert
//    expect($aliens->getConnectionName())->toBe('default_connection');
//})->skip();
//
//it('uses default connection when not on a demo page', function () {
//    // Arrange
//    $aliens = new TestAliens();
//
//    // Mock DemoHelper to return false
//    $demoHelper = \Mockery::mock('alias:' . DemoHelper::class);
//    $demoHelper->shouldReceive('isRequestFromDemoPage')
//        ->once()
//        ->andReturn(false);
//
//    // Act & Assert
//    expect($aliens->getConnectionName())->toBe('default_connection');
//})->skip();
//
//it('uses demo connection when on a demo page', function () {
//    // Arrange
//    $aliens = new TestAliens();
//
//    // Mock DemoHelper to return true
//    $demoHelper = \Mockery::mock('alias:' . DemoHelper::class);
//    $demoHelper->shouldReceive('isRequestFromDemoPage')
//        ->once()
//        ->andReturn(true);
//
//    // Act & Assert
//    expect($aliens->getConnectionName())->toBe('media_demo');
//})->skip();
//
//it('uses the configured demo database name', function () {
//    // Arrange
//    Config::set('media-library-extensions.temp_database_name', 'custom_demo_db');
//    $aliens = new TestAliens();
//
//    // Mock DemoHelper to return true
//    $demoHelper = \Mockery::mock('alias:' . DemoHelper::class);
//    $demoHelper->shouldReceive('isRequestFromDemoPage')
//        ->once()
//        ->andReturn(true);
//
//    // Act & Assert
//    expect($aliens->getConnectionName())->toBe('custom_demo_db');
//})->skip();
//
//it('has the expected media collections', function () {
//    // This test would require a more complex setup with a real database connection
//    // For now, we'll just test that the method exists
//    $aliens = new Aliens();
//    expect($aliens)->toHaveMethod('registerMediaCollections');
//
//    // We could also reflect on the class to check if it uses the expected traits
//    $uses = class_uses_recursive(Aliens::class);
//    expect($uses)->toContain('Spatie\MediaLibrary\InteractsWithMedia');
//    expect($uses)->toContain('Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMediaExtended');
//    expect($uses)->toContain('Mlbrgn\MediaLibraryExtensions\Traits\YouTubeCollection');
//})->skip();
