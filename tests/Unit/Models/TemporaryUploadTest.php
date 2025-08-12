<?php

//
// use Illuminate\Support\Facades\Config;
// use Mlbrgn\MediaLibraryExtensions\Helpers\DemoHelper;
// use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
//
// // Create a test double for the TemporaryUpload class
// class TemporaryUploadTest extends TemporaryUpload
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
//    $temporaryUpload = new TestTemporaryUpload();
//
//    // Act & Assert
//    expect($temporaryUpload->getConnectionName())->toBe('default_connection');
// })->skip();
//
// it('uses default connection when not on a demo page', function () {
//    // Arrange
//    $temporaryUpload = new TestTemporaryUpload();
//
//    // Mock DemoHelper to return false
//    $demoHelper = \Mockery::mock('alias:' . DemoHelper::class);
//    $demoHelper->shouldReceive('isRequestFromDemoPage')
//        ->once()
//        ->andReturn(false);
//
//    // Act & Assert
//    expect($temporaryUpload->getConnectionName())->toBe('default_connection');
// })->skip();
//
// it('uses demo connection when on a demo page', function () {
//    // Arrange
//    $temporaryUpload = new TestTemporaryUpload();
//
//    // Mock DemoHelper to return true
//    $demoHelper = \Mockery::mock('alias:' . DemoHelper::class);
//    $demoHelper->shouldReceive('isRequestFromDemoPage')
//        ->once()
//        ->andReturn(true);
//
//    // Act & Assert
//    expect($temporaryUpload->getConnectionName())->toBe('media_demo');
// })->skip();
//
// it('uses the configured demo database name', function () {
//    // Arrange
//    Config::set('media-library-extensions.temp_database_name', 'custom_demo_db');
//    $temporaryUpload = new TestTemporaryUpload();
//
//    // Mock DemoHelper to return true
//    $demoHelper = \Mockery::mock('alias:' . DemoHelper::class);
//    $demoHelper->shouldReceive('isRequestFromDemoPage')
//        ->once()
//        ->andReturn(true);
//
//    // Act & Assert
//    expect($temporaryUpload->getConnectionName())->toBe('custom_demo_db');
// })->skip();
//
// it('checks if the table exists in the database', function () {
//    // This test would require a more complex setup with a real database connection
//    // For now, we'll just test that the method exists
//    $temporaryUpload = new TemporaryUpload();
//    expect($temporaryUpload)->toHaveMethod('isAvailable');
// })->skip();
//
// it('retrieves uploads for the current session', function () {
//    // This test would require a more complex setup with a real database connection
//    // For now, we'll just test that the method exists
//    expect(TemporaryUpload::class)->toHaveMethod('forCurrentSession');
// })->skip();
//
// it('determines if the upload is an image', function () {
//    // Arrange
//    $temporaryUpload = new TemporaryUpload();
//    $temporaryUpload->mime_type = 'image/jpeg';
//
//    // Act & Assert
//    expect($temporaryUpload->isImage())->toBeTrue();
// })->skip();
//
// it('determines if the upload is a document', function () {
//    // Arrange
//    $temporaryUpload = new TemporaryUpload();
//    $temporaryUpload->mime_type = 'application/pdf';
//
//    // Act & Assert
//    expect($temporaryUpload->isDocument())->toBeTrue();
// })->skip();
//
// it('determines if the upload is a YouTube video', function () {
//    // Arrange
//    $temporaryUpload = new TemporaryUpload();
//    $temporaryUpload->custom_properties = ['youtube-id' => 'abc123'];
//
//    // Act & Assert
//    expect($temporaryUpload->isYouTubeVideo())->toBeTrue();
// })->skip();
