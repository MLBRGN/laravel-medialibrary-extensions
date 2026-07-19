<?php

use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Support\PackageInfrastructure;
use Mlbrgn\MediaLibraryExtensions\Support\MediaUploadContext;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\ResponsiveImages\ResponsiveImageGenerator;
use Spatie\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\Blurred;
use Spatie\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\TinyPlaceholderGenerator;
use Spatie\MediaLibrary\ResponsiveImages\WidthCalculator\FileSizeOptimizedWidthCalculator;
use Spatie\MediaLibrary\ResponsiveImages\WidthCalculator\WidthCalculator;

beforeEach(function () {
    config()->set('media-library.responsive_images.width_calculator', FileSizeOptimizedWidthCalculator::class);
    config()->set('media-library.responsive_images.tiny_placeholder_generator', Blurred::class);
    // Avoid rendering placeholderSvg view (Spatie provider not loaded in these unit tests)
    config()->set('media-library.responsive_images.use_tiny_placeholders', false);

    $this->temporaryDisk = config('medialibrary-extensions.media_disks.temporary');
    $this->demoDisk = PackageInfrastructure::disk('demo');

    // Manually define the disks to avoid DiskDoesNotExist
    config()->set("filesystems.disks.{$this->demoDisk}", [
        'driver' => 'local',
        'root' => $this->getTempDirectory($this->demoDisk),
    ]);
    config()->set("filesystems.disks.{$this->temporaryDisk}", [
        'driver' => 'local',
        'root' => $this->getTempDirectory($this->temporaryDisk),
    ]);

    Storage::fake($this->demoDisk);
    Storage::fake($this->temporaryDisk);

    // Bind missing Spatie class for tests to avoid BindingResolutionException
    app()->bind(
        WidthCalculator::class,
        FileSizeOptimizedWidthCalculator::class
    );
    // Use an anonymous class for the dummy generator to avoid view dependencies
    app()->singleton(
        TinyPlaceholderGenerator::class,
        fn () => new class implements TinyPlaceholderGenerator
        {
            public function generateTinyPlaceholder(string $sourceImage, string $tinyImageDestination): void
            {
                file_put_contents($tinyImageDestination, base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAABAAEDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZnaGlqc3R1dnd4eXqGhcSlhIGgcICRhIGKjQ2Gj5OX8lJS4lYVWRnaGlpaW1ub3019r129vp7m6fP19v7n5+fo6erx8vP09fb3+Pn6/9oADAMBAAIRAxEAPwA/8D//2Q=='));
            }
        }
    );
    // Bind a no-op ResponsiveImageGenerator with correct constructor dependencies
    app()->singleton(
        ResponsiveImageGenerator::class,
        function () {
            $filesystem = app(\Spatie\MediaLibrary\MediaCollections\Filesystem::class);
            $widthCalculator = app(WidthCalculator::class);
            $tiny = app(TinyPlaceholderGenerator::class);

            return new class($filesystem, $widthCalculator, $tiny) extends ResponsiveImageGenerator
            {
                public function generateResponsiveImages(Media $media): void {}
            };
        }
    );

    // Add view hint to avoid "No hint path defined for [media-library]"
    try {
        view()->addNamespace('media-library', __DIR__.'/../../../resources/views');
    } catch (Throwable $e) {
        // Already added
    }
});

it('promotes single temporary upload on model creation using request context (default connection)', function () {
    config()->set('media-library.generate_responsive_images', false);

    $clientToken = 'test-token-single';
    $instanceId = 'INSTANCE-SINGLE';
    $fileName = 'single.jpg';

    // 1. Create temporary upload
    $demoImage = __DIR__.'/../../../resources/demo/demo_small.jpeg';
    Storage::disk($this->temporaryDisk)->put('temp/'.$fileName, file_get_contents($demoImage));
    TemporaryUpload::create([
        'disk' => $this->temporaryDisk,
        'path' => 'temp/'.$fileName,
        'name' => 'single',
        'file_name' => $fileName,
        'collection_name' => 'blog-main',
        'client_token' => $clientToken,
        'instance_id' => $instanceId,
        'size' => 123,
    ]);

    // 2. Simulate request context
    request()->merge([
        'client_token' => $clientToken,
        'instance_id' => $instanceId,
    ]);

    // 3. Create model - this should trigger promotion
    $blog = Blog::create(['title' => 'Promotion Test']);

    // 4. Assertions
    expect(TemporaryUpload::count())->toBe(0);
    expect(Media::count())->toBe(1);

    $media = $blog->getFirstMedia('blog-main');
    expect($media)->not->toBeNull();
    expect($media->file_name)->toBe($fileName);
    expect(Storage::disk('public')->exists($media->id.'/'.$fileName))->toBeTrue();
});

it('promotes multiple temporary uploads on model creation (default connection)', function () {
    config()->set('media-library.generate_responsive_images', false);

    $clientToken = 'test-token-multiple';
    $instanceId = 'INSTANCE-MULTIPLE';
    $demoImage = __DIR__.'/../../../resources/demo/demo_small.jpeg';

    // 1. Create multiple temporary uploads
    for ($i = 1; $i <= 3; $i++) {
        $fileName = "file-{$i}.jpg";
        Storage::disk($this->temporaryDisk)->put("temp/{$fileName}", file_get_contents($demoImage));
        TemporaryUpload::create([
            'disk' => $this->temporaryDisk,
            'path' => "temp/{$fileName}",
            'name' => "file-{$i}",
            'file_name' => $fileName,
            // use a multi-file collection for this test
            'collection_name' => 'blog-extra',
            'client_token' => $clientToken,
            'instance_id' => $instanceId,
            'size' => 100,
            'order_column' => $i,
        ]);
    }

    // 2. Simulate request context
    request()->merge([
        'client_token' => $clientToken,
        'instance_id' => $instanceId,
    ]);

    // 3. Create model
    $blog = Blog::create(['title' => 'Multiple Promotion Test']);

    // 4. Assertions
    expect(TemporaryUpload::count())->toBe(0);
    expect($blog->getMedia('blog-extra')->count())->toBe(3);

    $mediaItems = $blog->getMedia('blog-extra')->sortBy('order_column');
    expect($mediaItems->first()->file_name)->toBe('file-1.jpg');
    expect($mediaItems->last()->file_name)->toBe('file-3.jpg');
});

it('promotes temporary uploads on alternative connection (Alien model)', function () {
    config()->set('media-library.generate_responsive_images', false);

    // Resolve the alternative test connection via PackageInfrastructure
    $altConnection = PackageInfrastructure::connection('test', 'alt');

    $clientToken = 'test-token-alien';
    $instanceId = 'INSTANCE-ALIEN';
    $fileName = 'alien.jpg';
    $demoImage = __DIR__.'/../../../resources/demo/demo_small.jpeg';

    // 1. Create temporary upload on the alt connection
    Storage::disk($this->temporaryDisk)->put('temp/'.$fileName, file_get_contents($demoImage));
    TemporaryUpload::on($altConnection)->create([
        'disk' => $this->temporaryDisk,
        'path' => 'temp/'.$fileName,
        'name' => 'alien',
        'file_name' => $fileName,
        'collection_name' => 'alien-single-image',
        'client_token' => $clientToken,
        'instance_id' => $instanceId,
        'size' => 123,
    ]);

    // 2. Simulate request context and ensure promoter targets the alt data source
    // In the test profile, the alt connection maps to the 'test_alt' data source key
    request()->merge([
        'client_token' => $clientToken,
        'instance_id' => $instanceId,
        'data_source' => 'test_alt',
    ]);

    // 3. Create model on alt connection
    $alien = (new Alien)->setConnection($altConnection);
    $alien->save();

    // 4. Assertions
    expect(TemporaryUpload::on($altConnection)->count())->toBe(0);
    expect(Media::on($altConnection)->count())->toBe(1);

    $media = $alien->getFirstMedia('alien-single-image');
    expect($media)->not->toBeNull();
    expect($media->getConnectionName())->toBe($altConnection);
    expect($media->disk)->toBe($this->demoDisk);
    expect(Storage::disk($this->demoDisk)->exists($media->id.'/'.$fileName))->toBeTrue();
});

it('promotes using MediaUploadContext when request context is missing', function () {
    config()->set('media-library.generate_responsive_images', false);

    $clientToken = 'test-token-context';
    $instanceId = 'INSTANCE-CONTEXT';
    $fileName = 'context.jpg';
    $demoImage = __DIR__.'/../../../resources/demo/demo_small.jpeg';

    // 1. Create temporary upload
    Storage::disk($this->temporaryDisk)->put('temp/'.$fileName, file_get_contents($demoImage));
    TemporaryUpload::create([
        'disk' => $this->temporaryDisk,
        'path' => 'temp/'.$fileName,
        'name' => 'context',
        'file_name' => $fileName,
        'collection_name' => 'blog-main',
        'client_token' => $clientToken,
        'instance_id' => $instanceId,
        'size' => 123,
    ]);

    // 2. Set context manually
    app(MediaUploadContext::class)->set($instanceId, $clientToken);

    // 3. Ensure request is empty
    request()->replace([]);

    // 4. Create model
    $blog = Blog::create(['title' => 'Context Promotion Test']);

    // 5. Assertions
    expect(TemporaryUpload::count())->toBe(0);
    expect($blog->getMedia('blog-main')->count())->toBe(1);
});
