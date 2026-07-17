<?php

use Illuminate\Support\Facades\Validator;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\SetTemporaryUploadAsFirstRequest;
use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;

beforeEach(function () {
    $this->request = new SetTemporaryUploadAsFirstRequest;
});

it('authorizes temporary upload mode correctly', function () {
    $data = [
        'temporary_upload_mode' => true,
        'model_type' => Alien::class,
        'model_id' => '1',
        'target_media_collection' => 'alien-multiple-images',
        'medium_id' => '123',
        'base_id' => 'user123',
        'collections' => ['image' => 'alien-multiple-images'],
    ];

    $this->request->merge($data);

    expect($this->request->authorize())->toBeTrue();
});

it('fails authorization if model_type is invalid', function () {
    $data = [
        'temporary_upload_mode' => true,
        'model_type' => 'InvalidClass',
        'model_id' => '1',
        'target_media_collection' => 'alien-multiple-images',
        'medium_id' => '123',
        'base_id' => 'user123',
        'collections' => ['image' => 'alien-multiple-images'],
    ];

    $this->request->merge($data);

    expect($this->request->authorize())->toBeFalse();
});

// TODO fails?
it('fails authorization if temporary_upload_mode is false and model is missing', function () {
    $data = [
        'temporary_upload_mode' => false,
        'model_type' => Alien::class,
        'model_id' => 'non-existent',
        'target_media_collection' => 'alien-multiple-images',
        'medium_id' => '123',
        'base_id' => 'user123',
        'collections' => ['image' => 'alien-multiple-images'],
    ];

    $this->request->merge($data);

    expect($this->request->authorize())->toBeFalse();
});

it('fails authorization if collections are not allowed', function () {
    // We need a model that actually restricts collections
    $mockModel = new class extends Alien
    {
        public function allowedMediaCollections(): array
        {
            return ['allowed-one'];
        }
    };

    $data = [
        'temporary_upload_mode' => true,
        'model_type' => get_class($mockModel),
        'model_id' => '1',
        'target_media_collection' => 'invalid-collection',
        'medium_id' => '123',
        'base_id' => 'user123',
        'collections' => ['image' => 'invalid-collection'],
    ];

    $this->request->merge($data);

    expect($this->request->authorize())->toBeFalse();
});

it('fails authorization if temporary_upload_mode is missing and model_id is null', function () {
    $data = [
        'model_type' => Alien::class,
        'model_id' => null, // empty
        'target_media_collection' => 'alien-multiple-images',
        'medium_id' => '123',
        'base_id' => 'user123',
        'collections' => ['image' => 'alien-multiple-images'],
    ];

    $this->request->merge($data);

    // This is the case reported by the user
    expect($this->request->authorize())->toBeFalse();
});

it('authorizes correctly when temporary_upload_mode is explicitly true and model_id is null', function () {
    $data = [
        'temporary_upload_mode' => true,
        'model_type' => Alien::class,
        'model_id' => null,
        'target_media_collection' => 'alien-multiple-images',
        'medium_id' => '123',
        'base_id' => 'user123',
        'collections' => ['image' => 'alien-multiple-images'],
    ];

    $this->request->merge($data);

    expect($this->request->authorize())->toBeTrue();
});

it('passes validation with required fields and at least one collection', function () {
    $data = [
        'model_type' => Alien::class,
        'model_id' => '1',
        'target_media_collection' => 'alien-multiple-images',
        'medium_id' => '123',
        'base_id' => 'user123',
        'collections' => ['image1' => 'images'],
    ];

    $validator = Validator::make($data, $this->request->rules());

    expect($validator->passes())->toBeTrue();
});

it('passes validation when model_id is null', function () {
    $data = [
        'model_type' => 'App\Models\Post',
        'model_id' => null,
        'target_media_collection' => 'images',
        'medium_id' => '123',
        'base_id' => 'initiator123',
        'collections' => ['image1' => 'images'],
    ];

    $validator = Validator::make($data, (new SetTemporaryUploadAsFirstRequest)->rules());

    expect($validator->passes())->toBeTrue();
});

it('fails validation when no collections are provided', function () {
    $data = [
        'target_media_collection' => 'images',
        'medium_id' => '123',
        'base_id' => 'user123',
        // 'collections' is intentionally missing
    ];

    $validator = Validator::make($data, $this->request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('collections'))->toBeTrue();

});

it('fails validation when required fields are missing', function () {
    $data = [];

    $validator = Validator::make($data, $this->request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('target_media_collection'))->toBeTrue();
    expect($validator->errors()->has('medium_id'))->toBeTrue();
    expect($validator->errors()->has('base_id'))->toBeTrue();
});
