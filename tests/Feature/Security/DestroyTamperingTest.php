<?php

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

it('blocks deleting media that belongs to another model (URL tampering)', function () {
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

    $route = route(mle_prefix_route('destroy-media'), $foreign);

    $response = $this
        ->actingAs($user)
        ->deleteJson($route, [
            'base_id' => 'base-x',
            'collections' => ['image' => 'blog-main'],
            'model_type' => get_class($a),
            'model_id' => (string) $a->getKey(),
            'data_source' => 'default',
        ]);

    $response->assertStatus(403);
    $response->assertJsonFragment(['type' => 'error']);
    $response->assertJsonPath('message', trans('medialibrary-extensions::messages.not_authorized'));

    // Media remains
    $this->assertDatabaseHas('media', ['id' => $foreign->id]);
});

it('allows deleting media that belongs to the authorized model (happy path)', function () {
    $user = $this->getUser();

    $a = Blog::query()->create(['title' => 'A']);

    Storage::fake('media');
    Queue::fake();
    config()->set('media-library.generate_responsive_images', false);
    config()->set('media-library.queue_connection_name', 'sync');

    $m = $a->addMedia($this->getFixtureUploadedFile('test.png'))
        ->preservingOriginal()
        ->toMediaCollection('blog-main');

    $route = route(mle_prefix_route('destroy-media'), $m);

    $response = $this
        ->actingAs($user)
        ->deleteJson($route, [
            'base_id' => 'base-y',
            'collections' => ['image' => 'blog-main'],
            'model_type' => get_class($a),
            'model_id' => (string) $a->getKey(),
            'data_source' => 'default',
        ]);

    $response->assertOk();
    $response->assertJsonFragment(['type' => 'success']);
    $this->assertDatabaseMissing('media', ['id' => $m->id]);
});
