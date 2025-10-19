<?php

dataset('authenticated_post_routes', [
    'single upload' => fn () => config('media-library-extensions.route_prefix').'-media-upload-single',
    'multiple upload' => fn () => config('media-library-extensions.route_prefix').'-media-upload-multiple',
    'youtube upload' => fn () => config('media-library-extensions.route_prefix').'-media-upload-youtube',
    'save update medium' => fn () => config('media-library-extensions.route_prefix').'-save-updated-medium',
    'save updated temporary upload' => fn () => config('media-library-extensions.route_prefix').'-save-updated-temporary-upload',
]);

dataset('authenticated_delete_routes', [
    'medium destroy' => fn () => config('media-library-extensions.route_prefix').'-medium-destroy',
    'temporary upload destroy' => fn () => config('media-library-extensions.route_prefix').'-temporary-upload-destroy',
]);

dataset('authenticated_put_routes', [
    'set as first' => fn () => config('media-library-extensions.route_prefix').'-set-as-first',
    'temporary upload set as first' => fn () => config('media-library-extensions.route_prefix').'-temporary-upload-set-as-first',
]);

dataset('authenticated_get_routes', [
    'preview update' => fn () => config('media-library-extensions.route_prefix').'-preview-update',
]);

it('cannot sen post requests to routes when not authenticated', function ($routeName) {
    // Arrange: model + media
    $model = $this->getTestBlogModel();
    $media = $model->addMedia($this->getFixtureUploadedFile('test.png'))
        ->preservingOriginal()
        ->withCustomProperties(['priority' => 3])
        ->toMediaCollection('images');

    // Payload as JS FormData equivalent
    $payload = [
        'initiator_id' => 'foo',
        'media_manager_id' => 'bar',
        'collections' => ['images'],
    ];

    // Act
    $response = $this->postJson(route($routeName, $media), $payload);

    // Assert
    $response->assertStatus(401);
})->with('authenticated_post_routes');

it('cannot send delete requests to routes when not authenticated', function ($routeName) {
    // Arrange: model + media
    $model = $this->getTestBlogModel();
    $media = $model->addMedia($this->getFixtureUploadedFile('test.png'))
        ->preservingOriginal()
        ->withCustomProperties(['priority' => 3])
        ->toMediaCollection('images');

    // Payload as JS FormData equivalent
    $payload = [
        'initiator_id' => 'foo',
        'media_manager_id' => 'bar',
        'collections' => ['images'],
    ];

    // Act
    $response = $this->deleteJson(route($routeName, $media), $payload);

    // Assert
    $response->assertStatus(401);
})->with('authenticated_delete_routes');

it('cannot send put requests to routes when not authenticated', function ($routeName) {
    // Arrange: model + media
    $model = $this->getTestBlogModel();
    $media = $model->addMedia($this->getFixtureUploadedFile('test.png'))
        ->preservingOriginal()
        ->withCustomProperties(['priority' => 3])
        ->toMediaCollection('images');

    // Payload as JS FormData equivalent
    $payload = [
        'initiator_id' => 'foo',
        'media_manager_id' => 'bar',
        'collections' => ['images'],
    ];

    // Act
    $response = $this->putJson(route($routeName, $media), $payload);

    // Assert
    $response->assertStatus(401);
})->with('authenticated_put_routes');

it('cannot send get requests to routes when not authenticated', function ($routeName) {
    // Arrange: model + media
    $model = $this->getTestBlogModel();
    $media = $model->addMedia($this->getFixtureUploadedFile('test.png'))
        ->preservingOriginal()
        ->withCustomProperties(['priority' => 3])
        ->toMediaCollection('images');

    // Payload as JS FormData equivalent
    $payload = [
        'initiator_id' => 'foo',
        'media_manager_id' => 'bar',
        'collections' => ['images'],
    ];

    // Act
    $response = $this->getJson(route($routeName, $media), $payload);

    // Assert
    $response->assertStatus(401);
})->with('authenticated_get_routes');
