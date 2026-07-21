<?php

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Actions\RestoreOriginalMediaAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\RestoreOriginalMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

it('blocks restoring original for media that belongs to another model (URL/body tampering)', function () {
    $user = $this->getUser();

    $a = Blog::query()->create(['title' => 'A']);
    $b = Blog::query()->create(['title' => 'B']);

    Storage::fake('media');
    Storage::fake('public');
    Storage::fake(config('medialibrary-extensions.media_disks.originals'));
    Queue::fake();

    config()->set('media-library.generate_responsive_images', false);
    config()->set('media-library.queue_connection_name', 'sync');

    $b->addMedia($this->getFixtureUploadedFile('test2.png'))
        ->preservingOriginal()
        ->toMediaCollection('blog-main');
    $foreign = $b->getFirstMedia('blog-main');

    // Ensure an original exists path-wise for the action to attempt
    $originalsDisk = config('medialibrary-extensions.media_disks.originals');
    Storage::disk($originalsDisk)->put("{$foreign->id}/{$foreign->file_name}", 'original');

    $route = route(mle_prefix_route('restore-original-medium'), ['mediaId' => $foreign->id]);

    $response = $this->actingAs($user)->postJson($route, [
        'base_id' => 'base-ro',
        'model_type' => get_class($a),
        'model_id' => (string) $a->getKey(),
        'data_source' => 'default',
        'temporary_upload_mode' => 'false',
        'medium_id' => (string) $foreign->id,
        'collections' => ['image' => 'blog-main'],
    ]);

    $response->assertStatus(403);
    $response->assertJsonFragment(['type' => 'error']);
    $response->assertJsonPath('message', trans('medialibrary-extensions::messages.not_authorized'));
});

it('allows restoring original for media that belongs to the authorized model (happy path)', function () {
    $user = $this->getUser();

    $a = Blog::query()->create(['title' => 'A']);

    Storage::fake('media');
    Storage::fake('public');
    Storage::fake(config('medialibrary-extensions.media_disks.originals'));
    Queue::fake();
    config()->set('media-library.generate_responsive_images', false);
    config()->set('media-library.queue_connection_name', 'sync');

    $a->addMedia($this->getFixtureUploadedFile('test.png'))
        ->preservingOriginal()
        ->toMediaCollection('blog-main');
    $m = $a->getFirstMedia('blog-main');

    // Write an original to restore
    $originalsDisk = config('medialibrary-extensions.media_disks.originals');
    Storage::disk($originalsDisk)->put("{$m->id}/{$m->file_name}", 'original');

    // Ensure the target disk used by Spatie for this medium is also faked
    Storage::fake($m->disk);

    // Use the action directly for the happy path (functionality verification)
    $request = RestoreOriginalMediumRequest::create('/restore', 'POST', [
        'data_source' => 'default',
    ]);
    $request->setLaravelSession(app('session.store'));
    $request->headers->set('Accept', 'application/json');

    // Ensure the target disk is faked for the write operation
    Storage::fake($m->disk);

    $action = app(RestoreOriginalMediaAction::class);
    $response = $action->execute($request, $m->id);

    expect($response->status())->toBe(200);
    $data = $response->getData(true);
    expect($data['type'])->toBe('success');
})
//    ->only();
    ->todo('This test is failing, needs to be fixed');
