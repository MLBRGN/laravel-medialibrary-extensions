<?php

use Illuminate\Support\Facades\Validator;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Rules\AllowedMediaCollections;

beforeEach(function () {
    $this->model = Mockery::mock(HasMediaExtended::class);
});

it('passes when all requested collections are allowed', function () {
    $this->model
        ->shouldReceive('allowedMediaCollections')
        ->once()
        ->andReturn([
            'images',
            'documents',
            'videos',
        ]);

    $validator = Validator::make(
        ['collections' => ['images', 'documents']],
        ['collections' => [new AllowedMediaCollections($this->model)]],
    );

    expect($validator->passes())->toBeTrue();
});

it('fails when a requested collection is not allowed', function () {
    $this->model
        ->shouldReceive('allowedMediaCollections')
        ->once()
        ->andReturn([
            'images',
            'documents',
        ]);

    $validator = Validator::make(
        ['collections' => ['images', 'videos']],
        ['collections' => [new AllowedMediaCollections($this->model)]],
    );

    expect($validator->fails())->toBeTrue()
        ->and(
            $validator->errors()->first('collections')
        )->toBe(
            __('medialibrary-extensions::messages.selected_media_collection_not_allowed')
        );
});

it('passes when the requested collections contain duplicates or empty values', function () {
    $this->model
        ->shouldReceive('allowedMediaCollections')
        ->once()
        ->andReturn([
            'images',
        ]);

    $validator = Validator::make(
        ['collections' => [
            'images',
            'images',
            '',
            null,
        ]],
        ['collections' => [new AllowedMediaCollections($this->model)]],
    );

    expect($validator->passes())->toBeTrue();
});

it('passes when no allowed collections are configured', function () {
    $this->model
        ->shouldReceive('allowedMediaCollections')
        ->once()
        ->andReturn([]);

    $validator = Validator::make(
        ['collections' => [
            'images',
            'documents',
            'videos',
        ]],
        ['collections' => [new AllowedMediaCollections($this->model)]],
    );

    expect($validator->passes())->toBeTrue();
});

it('passes when no collections are requested', function () {
    $this->model
        ->shouldReceive('allowedMediaCollections')
        ->once()
        ->andReturn([
            'images',
            'documents',
        ]);

    $validator = Validator::make(
        ['collections' => []],
        ['collections' => [new AllowedMediaCollections($this->model)]],
    );

    expect($validator->passes())->toBeTrue();
});
