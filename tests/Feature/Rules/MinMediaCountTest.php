<?php

use Illuminate\Support\Facades\Validator;
use Mlbrgn\MediaLibraryExtensions\Database\Factories\TemporaryUploadFactory;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Rules\MinMediaCount;

beforeEach(function () {
    $this->model = mock(HasMediaExtended::class);
});

it('passes when minimum is 0', function () {
    $rule = new MinMediaCount(null, ['images'], 0);
    $validator = Validator::make(['gallery' => 0], ['gallery' => [$rule]]);

    expect($validator->passes())->toBeTrue();
});

it('passes when minimum requirement is met by permanent media', function () {
    $this->model
        ->shouldReceive('getMedia')
        ->with('images')
        ->andReturn(collect(['existing1', 'existing2']));

    $rule = new MinMediaCount($this->model, ['images'], 2);
    $validator = Validator::make(['gallery' => 2], ['gallery' => [$rule]]);

    expect($validator->passes())->toBeTrue();
});

it('passes when minimum requirement is met by temporary uploads', function () {
    $clientToken = 'test-client';

    TemporaryUploadFactory::new()
        ->forCollection('images')
        ->forClient($clientToken)
        ->count(2)
        ->create();

    $rule = new MinMediaCount(null, ['images'], 2, null, 'default', $clientToken);
    $validator = Validator::make(['gallery' => 2], ['gallery' => [$rule]]);

    expect($validator->passes())->toBeTrue();
});

it('passes when minimum requirement is met by sum of permanent and temporary', function () {
    $clientToken = 'test-client';

    $this->model
        ->shouldReceive('getMedia')
        ->with('images')
        ->andReturn(collect(['existing1']));

    TemporaryUploadFactory::new()
        ->forCollection('images')
        ->forClient($clientToken)
        ->count(1)
        ->create();

    $rule = new MinMediaCount($this->model, ['images'], 2, null, 'default', $clientToken);
    $validator = Validator::make(['gallery' => 2], ['gallery' => [$rule]]);

    expect($validator->passes())->toBeTrue();
});

it('fails when minimum requirement is not met', function () {
    $this->model
        ->shouldReceive('getMedia')
        ->with('images')
        ->andReturn(collect(['existing1']));

    $rule = new MinMediaCount($this->model, ['images'], 2);
    $validator = Validator::make(['gallery' => 1], ['gallery' => [$rule]]);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('gallery'))
        ->toBe($rule->message());
});

it('resolves client token from request if not provided', function () {
    $clientToken = 'request-token';
    request()->merge(['client_token' => $clientToken]);

    TemporaryUploadFactory::new()
        ->forCollection('images')
        ->forClient($clientToken)
        ->count(2)
        ->create();

    $rule = new MinMediaCount(null, ['images'], 2);
    $validator = Validator::make(['gallery' => 2], ['gallery' => [$rule]]);

    expect($validator->passes())->toBeTrue();
});

it('resolves instance id from request if not provided', function () {
    $clientToken = 'test-client';
    $instanceId = 'instance-123';
    $otherInstanceId = 'instance-456';
    
    request()->merge([
        'client_token' => $clientToken,
        'instance_id' => $instanceId,
    ]);

    // Create 2 uploads for the target instance
    TemporaryUploadFactory::new()
        ->forCollection('images')
        ->forClient($clientToken)
        ->state(['instance_id' => $instanceId])
        ->count(2)
        ->create();
        
    // Create 1 upload for a different instance (should be ignored)
    TemporaryUploadFactory::new()
        ->forCollection('images')
        ->forClient($clientToken)
        ->state(['instance_id' => $otherInstanceId])
        ->count(1)
        ->create();

    $rule = new MinMediaCount(null, ['images'], 2);
    $validator = Validator::make(['gallery' => 2], ['gallery' => [$rule]]);

    // Should pass because we have exactly 2 for instance-123
    expect($validator->passes())->toBeTrue();
    
    // If we required 3, it should fail (proving the other instance was ignored)
    $rule3 = new MinMediaCount(null, ['images'], 3);
    $validator3 = Validator::make(['gallery' => 2], ['gallery' => [$rule3]]);
    expect($validator3->fails())->toBeTrue();
});

it('returns the singular message when min is one', function () {
    $rule = new MinMediaCount(null, ['images'], 1);

    expect($rule->message())
        ->toBe(__('medialibrary-extensions::messages.at_least_one_medium_required'));
});

it('returns the plural message when min is greater than one', function () {
    $rule = new MinMediaCount(null, ['images'], 3);

    expect($rule->message())
        ->toBe(
            __('medialibrary-extensions::messages.this_collection_requires_at_least_:items_items', [
                'items' => 3,
            ])
        );
});
