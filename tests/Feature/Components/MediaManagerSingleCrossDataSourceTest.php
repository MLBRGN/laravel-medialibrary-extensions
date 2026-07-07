<?php

use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerSingle;

it('keeps single temporary manager enabled on demo when default has an upload in another collection', function () {
    // Arrange scope values
    $baseId = 'single-'.Str::ulid();
    $instanceId = InstanceManager::getInstanceId($baseId);
    $clientToken = (string) Str::ulid();

    // Create a temporary upload on the DEFAULT data source in a different collection
    TemporaryUpload::create([
        'client_token' => $clientToken,
        'instance_id' => $instanceId,
        'collection_name' => 'blog-single-document',
        'disk' => 'public',
        'path' => 'temp/1.png',
        'file_name' => '1.png',
        'name' => '1',
        'mime_type' => 'image/png',
        'size' => 100,
        'custom_properties' => ['priority' => 0],
        'order_column' => 0,
    ]);

    // Build a Single manager in TEMPORARY mode by passing a class string; the target collection is empty on DEMO
    $collections = [
        'image' => 'blog-single-image', // target we care about
        'document' => 'blog-single-document', // has an item but on DEFAULT connection
    ];

    // Instantiate the component; pass the same instance id and client token via constructor context
    // Note: BaseComponent derives instanceId from logical id; we must use the same $baseId
    $component = new MediaManagerSingle(
        id: $baseId,
        modelOrClassName: \Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog::class,
        singleMedia: null,
        collections: $collections,
        options: [],
        disabled: false,
        readonly: false,
        selectable: false,
        dataSource: 'demo', // ensure we count on DEMO while the upload exists on DEFAULT
    );

    // Force component to reuse our test client token
    // (BaseComponent pulls from ClientContext; in tests that falls back to config value)
    // Overwrite the public property so the count uses our token
    $component->clientToken = $clientToken;

    // Assert: because DEMO has 0 items in the target collection, Single must be enabled
    expect($component->getOption('disableForm'))->toBeFalse();
});

it('does not block uploads across different instanceIds for the same clientToken', function () {
    $baseIdA = 'single-a-'.Str::ulid();
    $baseIdB = 'single-b-'.Str::ulid();
    $instanceA = InstanceManager::getInstanceId($baseIdA);
    $instanceB = InstanceManager::getInstanceId($baseIdB);
    $clientToken = (string) Str::ulid();

    // Existing upload on "default" for instance A
    TemporaryUpload::create([
        'client_token' => $clientToken,
        'instance_id' => $instanceA,
        'collection_name' => 'blog-single-image',
        'disk' => 'public',
        'path' => 'temp/2.png',
        'file_name' => '2.png',
        'name' => '2',
        'mime_type' => 'image/png',
        'size' => 100,
        'custom_properties' => ['priority' => 0],
        'order_column' => 0,
    ]);

    $collections = ['image' => 'blog-single-image'];

    $componentB = new MediaManagerSingle(
        id: $baseIdB,
        modelOrClassName: \Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog::class,
        collections: $collections,
        options: [],
        disabled: false,
        readonly: false,
        selectable: false,
        dataSource: 'default',
    );
    $componentB->clientToken = $clientToken;

    // Instance B should not be disabled by uploads belonging to instance A
    expect($componentB->getOption('disableForm'))->toBeFalse();
});

it('does not block uploads across different clientTokens for the same instanceId', function () {
    $baseId = 'single-'.Str::ulid();
    $instanceId = InstanceManager::getInstanceId($baseId);
    $clientTokenA = (string) Str::ulid();
    $clientTokenB = (string) Str::ulid();

    // Existing upload for client A
    TemporaryUpload::create([
        'client_token' => $clientTokenA,
        'instance_id' => $instanceId,
        'collection_name' => 'blog-single-image',
        'disk' => 'public',
        'path' => 'temp/3.png',
        'file_name' => '3.png',
        'name' => '3',
        'mime_type' => 'image/png',
        'size' => 100,
        'custom_properties' => ['priority' => 0],
        'order_column' => 0,
    ]);

    $collections = ['image' => 'blog-single-image'];

    $component = new MediaManagerSingle(
        id: $baseId,
        modelOrClassName: \Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog::class,
        collections: $collections,
        options: [],
        disabled: false,
        readonly: false,
        selectable: false,
        dataSource: 'default',
    );
    $component->clientToken = $clientTokenB; // different client

    // Client B should not be disabled by uploads belonging to client A
    expect($component->getOption('disableForm'))->toBeFalse();
});
