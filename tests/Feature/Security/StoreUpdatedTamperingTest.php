<?php

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

it('blocks replacing a medium that belongs to another model (body tampering)', function () {
    $user = $this->getUser();

    $a = Blog::query()->create(['title' => 'A']);
    $b = Blog::query()->create(['title' => 'B']);

    Storage::fake('media');
    Queue::fake();
    config()->set('media-library.generate_responsive_images', false);
    config()->set('media-library.queue_connection_name', 'sync');

    $b->addMedia($this->getFixtureUploadedFile('test2.png'))
        ->preservingOriginal()
        ->toMediaCollection('blog-main');
    $foreign = $b->getFirstMedia('blog-main');

    $file = UploadedFile::fake()->image('replacement.jpg', 120, 120);

    $route = route(mle_prefix_route('save-updated-media'), ['mediaId' => $foreign->id]);
    $response = $this->actingAs($user)->postJson($route, [
        'base_id' => 'test-base',
        'model_type' => get_class($a),
        'model_id' => $a->getKey(),
        'medium_id' => (string) $foreign->id,
        'single_media_id' => (string) $foreign->id,
        'collection' => 'blog-main',
        'temporary_upload_mode' => 'false',
        'file' => $file,
        'data_source' => 'default',
        'collections' => ['image' => 'blog-main'],
        'options' => json_encode([]),
    ]);

    $response->assertStatus(403);
    $response->assertJsonFragment(['type' => 'error']);
    $response->assertJsonPath('message', trans('medialibrary-extensions::messages.not_authorized'));

    // Original medium was not deleted
    $this->assertDatabaseHas('media', ['id' => $foreign->id]);
});

it('allows replacing a medium that belongs to the authorized model (happy path)', function () {
    $user = $this->getUser();

    $a = Blog::query()->create(['title' => 'A']);

    Storage::fake('media');
    Queue::fake();
    config()->set('media-library.generate_responsive_images', false);
    config()->set('media-library.queue_connection_name', 'sync');

    $a->addMedia($this->getFixtureUploadedFile('test.png'))
        ->preservingOriginal()
        ->toMediaCollection('blog-main');
    $old = $a->getFirstMedia('blog-main');

    $file = UploadedFile::fake()->image('replacement.jpg', 120, 120);
    $route = route(mle_prefix_route('save-updated-media'), ['mediaId' => $old->id]);
    $response = $this->actingAs($user)->postJson($route, [
        'base_id' => 'test-base',
        'model_type' => get_class($a),
        'model_id' => $a->getKey(),
        'medium_id' => (string) $old->id,
        'single_media_id' => (string) $old->id,
        'collection' => 'blog-main',
        'temporary_upload_mode' => 'false',
        'file' => $file,
        'data_source' => 'default',
        'collections' => ['image' => 'blog-main'],
        'options' => json_encode([]),
    ]);

    $response->assertOk();
    $response->assertJsonFragment(['type' => 'success']);

    $json = $response->json();
    expect($json['oldMediumId'])->toBe((string) $old->id);
    expect($json['newMediumId'])->not()->toBe((string) $old->id);

    $this->assertDatabaseMissing('media', ['id' => $old->id]);
    $this->assertDatabaseHas('media', ['id' => $json['newMediumId']]);
});
