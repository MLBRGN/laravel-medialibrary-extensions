<?php
//
///**
// * EXAMPLE TEST FILE
// *
// * This file contains example tests for the ImageResponsive component.
// * These tests demonstrate how to use Pest to test components in a more integrated way.
// * You may need to adapt these tests to your specific environment.
// *
// * Note: The beforeEach hook attempts to create test views, which may not work in all environments.
// * You might need to manually create these views or adapt the tests to your specific setup.
// */
//
//use Illuminate\Support\Facades\Blade;
//use Illuminate\Support\Facades\View;
//use Spatie\MediaLibrary\MediaCollections\Models\Media;
//
//beforeEach(function () {
//    // Register a test view that uses the component
//    View::addNamespace('test', __DIR__ . '/../../Feature/views');
//
//    // Create a test view file if it doesn't exist
//    $viewPath = __DIR__ . '/../../Feature/views/test-image-responsive.blade.php';
//    if (!file_exists($viewPath)) {
//        if (!is_dir(dirname($viewPath))) {
//            mkdir(dirname($viewPath), 0755, true);
//        }
//        file_put_contents($viewPath, '
//            <x-media-library-extensions::image-responsive
//                :medium="$medium"
//                :conversion="$conversion"
//                :sizes="$sizes"
//                :lazy="$lazy"
//                :alt="$alt"
//                class="test-class"
//            />
//        ');
//    }
//})->skip();
//
//it('renders the component with a media object', function () {
//    // Arrange
//    $medium = Mockery::mock(Media::class);
//    $medium->shouldReceive('hasGeneratedConversion')->with('thumb')->andReturn(true);
//    $medium->shouldReceive('getUrl')->with('thumb')->andReturn('https://example.com/thumb.jpg');
//    $medium->shouldReceive('getSrcset')->with('thumb')->andReturn('https://example.com/thumb.jpg 300w, https://example.com/thumb.jpg 600w');
//
//    // Act
//    $html = Blade::render(
//        '@include("test::test-image-responsive")',
//        [
//            'medium' => $medium,
//            'conversion' => 'thumb',
//            'sizes' => '100vw',
//            'lazy' => true,
//            'alt' => 'Test image'
//        ]
//    );
//
//    // Assert
//    expect($html)->toContain('src="https://example.com/thumb.jpg"');
//    expect($html)->toContain('srcset="https://example.com/thumb.jpg 300w, https://example.com/thumb.jpg 600w"');
//    expect($html)->toContain('sizes="100vw"');
//    expect($html)->toContain('loading="lazy"');
//    expect($html)->toContain('alt="Test image"');
//    expect($html)->toContain('class="test-class"');
//})->skip();
//
//it('renders the fallback image when no media is provided', function () {
//    // Act
//    $html = Blade::render(
//        '@include("test::test-image-responsive")',
//        [
//            'medium' => null,
//            'conversion' => '',
//            'sizes' => '100vw',
//            'lazy' => true,
//            'alt' => 'Test image'
//        ]
//    );
//
//    // Assert
//    expect($html)->toContain('src="' . asset('vendor/media-library-extensions/images/fallback.png') . '"');
//    expect($html)->toContain('alt="Missing image"');
//    expect($html)->toContain('class="test-class opacity-50"');
//})->skip();
//
//it('renders the component in a route', function () {
//    // Arrange
//    $medium = Mockery::mock(Media::class);
//    $medium->shouldReceive('hasGeneratedConversion')->with('thumb')->andReturn(true);
//    $medium->shouldReceive('getUrl')->with('thumb')->andReturn('https://example.com/thumb.jpg');
//    $medium->shouldReceive('getSrcset')->with('thumb')->andReturn('https://example.com/thumb.jpg 300w, https://example.com/thumb.jpg 600w');
//
//    // Create a test route view
//    $viewPath = __DIR__ . '/../../Feature/views/test-route-image.blade.php';
//    if (!file_exists($viewPath)) {
//        file_put_contents($viewPath, '
//            <html>
//                <body>
//                    <x-media-library-extensions::image-responsive
//                        :medium="$medium"
//                        :conversion="$conversion"
//                        :sizes="$sizes"
//                        :lazy="$lazy"
//                        :alt="$alt"
//                        class="test-class"
//                    />
//                </body>
//            </html>
//        ');
//    }
//
//    // Register a test route
//    $this->registerTestRoute('test-route-image', function () {
//        return 'Route response';
//    });
//
//    // Act & Assert
//    $this->get('/test-route-image', [
//        'medium' => $medium,
//        'conversion' => 'thumb',
//        'sizes' => '100vw',
//        'lazy' => true,
//        'alt' => 'Test image'
//    ])
//    ->assertStatus(200)
//    ->assertViewIs('test-route-image');
//})->skip();
