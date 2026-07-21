<?php

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

it('blocks setting as first when media belongs to another model (URL/body tampering)', function () {
    $user = $this->getUser();

    $a = Blog::query()->create(['title' => 'A']);
    $b = Blog::query()->create(['title' => 'B']);

    Storage::fake('media');
    Queue::fake();
    config()->set('media-library.generate_responsive_images', false);
    config()->set('media-library.queue_connection_name', 'sync');

    // Create media on B
    $b->addMedia($this->getFixtureUploadedFile('test2.png'))
        ->preservingOriginal()
        ->toMediaCollection('blog-main');
    $foreign = $b->getFirstMedia('blog-main');

    $route = route(mle_prefix_route('set-as-first'));

    $response = $this->actingAs($user)->putJson($route, [
        'base_id' => 'base-saf',
        'model_type' => get_class($a),
        'model_id' => (string) $a->getKey(),
        'data_source' => 'default',
        'target_media_collection' => 'blog-main',
        'medium_id' => (string) $foreign->id,
        'collections' => ['image' => 'blog-main'],
    ]);

    if ($response->status() !== 403) {
        fwrite(STDERR, "\nSetAsFirst tampering response: ".print_r($response->json(), true)."\n");
    }
    $response->assertStatus(403);
    $response->assertJsonFragment(['type' => 'error']);
    $response->assertJsonPath('message', trans('medialibrary-extensions::messages.not_authorized'));
});

it('allows setting as first within the authorized model and collections (happy path)', function () {
    $user = $this->getUser();

    $a = Blog::query()->create(['title' => 'A']);

    Storage::fake('media');
    Queue::fake();
    config()->set('media-library.generate_responsive_images', false);
    config()->set('media-library.queue_connection_name', 'sync');

    // Two media on A to reorder (must use a non-single-file collection)
    $m1 = $a->addMedia($this->getFixtureUploadedFile('test.png'))
        ->preservingOriginal()
        ->toMediaCollection('blog-extra');
    $m2 = $a->addMedia($this->getFixtureUploadedFile('test2.png'))
        ->preservingOriginal()
        ->toMediaCollection('blog-extra');

    // Set the second as first
    $route = route(mle_prefix_route('set-as-first'));
    $response = $this->actingAs($user)->putJson($route, [
        'base_id' => 'base-saf2',
        'model_type' => get_class($a),
        'model_id' => (string) $a->getKey(),
        'data_source' => 'default',
        'target_media_collection' => 'blog-extra',
        'medium_id' => (string) $m2->id,
        'collections' => ['image' => 'blog-extra'],
    ]);

    $response->assertOk();
    $response->assertJsonFragment(['type' => 'success']);

    // Fetch priorities and assert m2 now has lower (earlier) priority than m1
    $m1->refresh();
    $m2->refresh();
    $fresh1 = $m1;
    $fresh2 = $m2;
    expect($fresh2->getCustomProperty('priority'))
        ->toBeLessThan($fresh1->getCustomProperty('priority'));
});
