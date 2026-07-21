<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Database\Factories\TemporaryUploadFactory;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

it('blocks deleting a temporary upload outside current scope (client_token/instanceId tampering)', function () {
    $user = $this->getUser();

    $tokenA = (string) Str::ulid();
    $tokenB = (string) Str::ulid();
    $baseIdA = 'base-tmp-a';
    $instanceIdA = InstanceManager::getInstanceId($baseIdA);

    // Foreign temp upload belongs to tokenB and a different instance
    $foreign = TemporaryUploadFactory::new()
        ->forClient($tokenB)
        ->state([
            'instance_id' => 'another-instance',
            'collection_name' => 'images',
        ])->create();

    $route = route(mle_prefix_route('destroy-temporary-upload'), ['temporaryUploadId' => $foreign->id]);

    $response = $this->actingAs($user)
        ->withCookie('mle_client_token', $tokenA)
        ->deleteJson($route, [
            'base_id' => $baseIdA,
            'data_source' => 'default',
            'collections' => ['image' => 'images'],
            'client_token' => $tokenA,
        ]);

    $response->assertStatus(403);
    expect(TemporaryUpload::query()->find($foreign->id))->not()->toBeNull();
});

it('allows deleting a temporary upload within current scope (happy path)', function () {
    $user = $this->getUser();

    $tokenA = (string) Str::ulid();
    $baseIdA = 'base-tmp-a2';
    $instanceIdA = InstanceManager::getInstanceId($baseIdA);

    $mine = TemporaryUploadFactory::new()
        ->forClient($tokenA)
        ->state([
            'instance_id' => $instanceIdA,
            'collection_name' => 'images',
        ])->create();

    $route = route(mle_prefix_route('destroy-temporary-upload'), ['temporaryUploadId' => $mine->id]);

    $response = $this->actingAs($user)
        ->withCookie('mle_client_token', $tokenA)
        ->deleteJson($route, [
            'base_id' => $baseIdA,
            'data_source' => 'default',
            'collections' => ['image' => 'images'],
            'client_token' => $tokenA,
        ]);

    $response->assertOk();
    expect(TemporaryUpload::query()->find($mine->id))->toBeNull();
});

it('blocks replacing a temporary upload outside current scope', function () {
    $user = $this->getUser();
    Storage::fake('public');

    $tokenA = (string) Str::ulid();
    $tokenB = (string) Str::ulid();
    $baseIdA = 'base-tmp-a3';
    $instanceIdA = InstanceManager::getInstanceId($baseIdA);

    $foreign = TemporaryUploadFactory::new()
        ->forClient($tokenB)
        ->state([
            'instance_id' => 'another-instance',
            'collection_name' => 'images',
        ])->create();

    $route = route(mle_prefix_route('save-updated-temporary-upload'), ['temporaryUploadId' => $foreign->id]);

    $file = UploadedFile::fake()->image('new.jpg', 10, 10);

    $response = $this->actingAs($user)
        ->withCookie('mle_client_token', $tokenA)
        ->postJson($route, [
            'base_id' => $baseIdA,
            'temporary_upload_mode' => 'true',
            'data_source' => 'default',
            'medium_id' => (string) $foreign->id,
            'model_type' => Blog::class,
            'collection' => 'images',
            'collections' => ['image' => 'images'],
            'client_token' => $tokenA,
            'file' => $file,
        ]);

    $response->assertStatus(403);
});

it('allows replacing a temporary upload within current scope (happy path)', function () {
    $user = $this->getUser();
    Storage::fake('public');

    $tokenA = (string) Str::ulid();
    $baseIdA = 'base-tmp-a4';
    $instanceIdA = InstanceManager::getInstanceId($baseIdA);

    $mine = TemporaryUploadFactory::new()
        ->forClient($tokenA)
        ->state([
            'instance_id' => $instanceIdA,
            'collection_name' => 'images',
        ])->create();

    $route = route(mle_prefix_route('save-updated-temporary-upload'), ['temporaryUploadId' => $mine->id]);

    $file = UploadedFile::fake()->image('new.jpg', 10, 10);

    $response = $this->actingAs($user)
        ->withCookie('mle_client_token', $tokenA)
        ->postJson($route, [
            'base_id' => $baseIdA,
            'temporary_upload_mode' => 'true',
            'data_source' => 'default',
            'medium_id' => (string) $mine->id,
            'model_type' => Blog::class,
            'collection' => 'images',
            'collections' => ['image' => 'images'],
            'client_token' => $tokenA,
            'file' => $file,
        ]);

    $response->assertOk();
    $response->assertJsonFragment(['type' => 'success']);
});

it('blocks setting a temporary upload as first outside current scope', function () {
    $user = $this->getUser();

    $tokenA = (string) Str::ulid();
    $tokenB = (string) Str::ulid();
    $baseIdA = 'base-tmp-a5';
    $instanceIdA = InstanceManager::getInstanceId($baseIdA);

    // In-scope list (empty) but target belongs to foreign scope
    $foreign = TemporaryUploadFactory::new()
        ->forClient($tokenB)
        ->state([
            'instance_id' => 'another-instance',
            'collection_name' => 'images',
        ])->create();

    $route = route(mle_prefix_route('temporary-upload-set-as-first'));

    $response = $this->actingAs($user)
        ->withCookie('mle_client_token', $tokenA)
        ->putJson($route, [
            'model_type' => Blog::class,
            'base_id' => $baseIdA,
            'data_source' => 'default',
            'target_media_collection' => 'images',
            'medium_id' => (string) $foreign->id,
            'collections' => ['image' => 'images'],
            'client_token' => $tokenA,
        ]);

    $response->assertStatus(403);
});

it('allows setting a temporary upload as first within current scope (happy path)', function () {
    $user = $this->getUser();

    $tokenA = (string) Str::ulid();
    $baseIdA = 'base-tmp-a6';
    $instanceIdA = InstanceManager::getInstanceId($baseIdA);

    // Two uploads in same scope and collection
    $u1 = TemporaryUploadFactory::new()->forClient($tokenA)->state([
        'instance_id' => $instanceIdA,
        'collection_name' => 'images',
        'order_column' => 10,
        'custom_properties' => ['priority' => 10],
    ])->create();
    $u2 = TemporaryUploadFactory::new()->forClient($tokenA)->state([
        'instance_id' => $instanceIdA,
        'collection_name' => 'images',
        'order_column' => 20,
        'custom_properties' => ['priority' => 20],
    ])->create();

    $route = route(mle_prefix_route('temporary-upload-set-as-first'));

    $response = $this->actingAs($user)
        ->withCookie('mle_client_token', $tokenA)
        ->putJson($route, [
            'model_type' => Blog::class,
            'base_id' => $baseIdA,
            'data_source' => 'default',
            'target_media_collection' => 'images',
            'medium_id' => (string) $u2->id,
            'collections' => ['image' => 'images'],
            'client_token' => $tokenA,
        ]);

    $response->assertOk();
    $response->assertJsonFragment(['type' => 'success']);

    $fresh1 = TemporaryUpload::query()->find($u1->id);
    $fresh2 = TemporaryUpload::query()->find($u2->id);
    expect($fresh2->getCustomProperty('priority'))->toBeLessThan($fresh1->getCustomProperty('priority'));
})->todo('Pending request-level authorization alignment for temporary set-as-first');
