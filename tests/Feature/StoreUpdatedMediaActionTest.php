<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// Do NOT use RefreshDatabase here; the package TestCase defines custom migrations
// for the test connections (mle_test_host_app, mle_test_demo). Pest is already
// configured to use our package TestCase in tests/Pest.php.

it('replaces a medium and returns newMediumId while deleting the old one', function () {


    $user = $this->getUser();

    /** @var Blog $model */
    $model = Blog::query()->create(['title' => 'Test blog']);

    // Use the disk configured by the package test scaffold
    Storage::fake('media');

    // Avoid running conversions / responsive image generation during this test
    Queue::fake();
    config()->set('media-library.generate_responsive_images', false);
    config()->set('media-library.queue_connection_name', 'sync');

    $initial = UploadedFile::fake()->image('initial.jpg', 100, 100);
    $model->addMedia($initial)->toMediaCollection('blog-main');

    /** @var Media $old */
    $old = $model->getFirstMedia('blog-main');

    // Act: replace via the action route used by the image editor
    $file = UploadedFile::fake()->image('replacement.jpg', 120, 120);

    $route = route(mle_prefix_route('save-updated-media'), ['mediaId' => $old->id]);
    $response = $this->actingAs($user)->postJson($route, [
        'base_id' => 'test-base',
        'model_type' => get_class($model),
        'model_id' => $model->getKey(),
        'medium_id' => (string) $old->id,
        'single_media_id' => (string) $old->id,
        'collection' => 'blog-main',
        'temporary_upload_mode' => 'false',
        'file' => $file,
        'data_source' => 'default',
        'collections' => ['image' => 'blog-main'],
        'options' => json_encode([]),
    ]);

    // Assert: response and DB state
    $response->assertSuccessful();
    $json = $response->json();

    expect($json['oldMediumId'])->toBe((string) $old->id);
    expect($json['newMediumId'])->not()->toBe((string) $old->id);

    // Old record is deleted, new exists
    $this->assertDatabaseMissing($old->getTable(), ['id' => $old->id]);
    $this->assertDatabaseHas($old->getTable(), ['id' => $json['newMediumId']]);
});
