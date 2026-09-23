<?php

use Illuminate\Support\Facades\Validator;
use Mlbrgn\MediaLibraryExtensions\Database\Factories\TemporaryUploadFactory;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Rules\MediaCount;

beforeEach(function () {
    $this->model = mock(HasMediaExtended::class);
    $this->model->shouldReceive('setConnection')->andReturnSelf();
    $this->model->shouldReceive('getMorphClass')->andReturn('TestModel');
    $this->model->shouldReceive('getKey')->andReturn(1);
});

it('passes when exactly requirement is met', function () {
    $this->model
        ->shouldReceive('getMedia')
        ->with('images')
        ->andReturn(collect(['existing1', 'existing2']));

    $rule = (new MediaCount($this->model, ['images']))->exactly(2);
    $validator = Validator::make(['gallery' => 2], ['gallery' => [$rule]]);

    expect($validator->passes())->toBeTrue();
});

it('fails when exactly requirement is not met', function () {
    $this->model
        ->shouldReceive('getMedia')
        ->with('images')
        ->andReturn(collect(['existing1']));

    $rule = (new MediaCount($this->model, ['images']))->exactly(2);
    $validator = Validator::make(['gallery' => 1], ['gallery' => [$rule]]);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('gallery'))
        ->toBe(__('medialibrary-extensions::messages.this_collection_must_contain_exactly_:items_items', ['items' => 2]));
});

it('passes when min and max requirements are met', function () {
    $this->model
        ->shouldReceive('getMedia')
        ->with('images')
        ->andReturn(collect(['existing1', 'existing2']));

    $rule = (new MediaCount($this->model, ['images']))->min(1)->max(3);
    $validator = Validator::make(['gallery' => 2], ['gallery' => [$rule]]);

    expect($validator->passes())->toBeTrue();
});

it('fails when min requirement is not met', function () {
    $this->model
        ->shouldReceive('getMedia')
        ->with('images')
        ->andReturn(collect([]));

    $rule = (new MediaCount($this->model, ['images']))->min(1);
    $validator = Validator::make(['gallery' => 0], ['gallery' => [$rule]]);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('gallery'))
        ->toBe(__('medialibrary-extensions::messages.at_least_one_medium_required'));
});

it('fails when max requirement is not met', function () {
    $this->model
        ->shouldReceive('getMedia')
        ->with('images')
        ->andReturn(collect(['e1', 'e2', 'e3']));

    $rule = (new MediaCount($this->model, ['images']))->max(2);
    $validator = Validator::make(['gallery' => 3], ['gallery' => [$rule]]);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('gallery'))
        ->toBe(__('medialibrary-extensions::messages.this_collection_can_contain_up_to_:items_items', ['items' => 2]));
});

it('resolves instance id from mle_instance_map', function () {
    $clientToken = 'test-client';
    $instanceId = 'instance-123';
    
    request()->merge([
        'client_token' => $clientToken,
        'mle_instance_map' => [
            'gallery' => $instanceId,
        ],
    ]);

    TemporaryUploadFactory::new()
        ->forCollection('images')
        ->forClient($clientToken)
        ->state(['instance_id' => $instanceId])
        ->count(2)
        ->create();

    $rule = (new MediaCount(null, ['images']))->exactly(2);
    $validator = Validator::make(['gallery' => 2], ['gallery' => [$rule]]);

    expect($validator->passes())->toBeTrue();
});

it('respects multiple(false) for min message', function () {
    $rule = (new MediaCount(null, ['images']))->min(1)->multiple(false);
    $validator = Validator::make(['gallery' => 0], ['gallery' => [$rule]]);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('gallery'))
        ->toBe(__('medialibrary-extensions::messages.one_medium_required'));
});

it('respects multiple(false) for max message', function () {
    $rule = (new MediaCount($this->model, ['images']))->max(1)->multiple(false);
    
    $this->model
        ->shouldReceive('getMedia')
        ->with('images')
        ->andReturn(collect(['e1', 'e2']));
        
    $validator = Validator::make(['gallery' => 2], ['gallery' => [$rule]]);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('gallery'))
        ->toBe(__('medialibrary-extensions::messages.only_one_medium_allowed'));
});
