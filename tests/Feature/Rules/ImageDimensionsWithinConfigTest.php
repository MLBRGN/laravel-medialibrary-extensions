<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Mlbrgn\MediaLibraryExtensions\Rules\ImageDimensionsWithinConfig;

beforeEach(function () {
    Config::set('medialibrary-extensions.max_image_width', 1920);
    Config::set('medialibrary-extensions.max_image_height', 1080);
    Config::set('medialibrary-extensions.min_image_width', 320);
    Config::set('medialibrary-extensions.min_image_height', 160);
});

it('passes when image dimensions are within configured limits', function () {
    $file = UploadedFile::fake()->image('image.jpg', 1280, 720);

    $validator = Validator::make(
        ['image' => $file],
        ['image' => [new ImageDimensionsWithinConfig]],
    );

    expect($validator->passes())->toBeTrue();
});

it('fails when image width exceeds the maximum', function () {
    $file = UploadedFile::fake()->image('image.jpg', 1921, 1080);

    $validator = Validator::make(
        ['image' => $file],
        ['image' => [new ImageDimensionsWithinConfig]],
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('image'))
        ->toBe(trans('medialibrary-extensions::messages.image_too_large', [
            'max_width' => 1920,
            'max_height' => 1080,
            'width' => 1921,
            'height' => 1080,
        ]));
});

it('fails when image height exceeds the maximum', function () {
    $file = UploadedFile::fake()->image('image.jpg', 1920, 1081);

    $validator = Validator::make(
        ['image' => $file],
        ['image' => [new ImageDimensionsWithinConfig]],
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('image'))
        ->toBe(trans('medialibrary-extensions::messages.image_too_large', [
            'max_width' => 1920,
            'max_height' => 1080,
            'width' => 1920,
            'height' => 1081,
        ]));
});

it('fails when image width is below the minimum', function () {
    $file = UploadedFile::fake()->image('image.jpg', 319, 720);

    $validator = Validator::make(
        ['image' => $file],
        ['image' => [new ImageDimensionsWithinConfig]],
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('image'))
        ->toBe(trans('medialibrary-extensions::messages.image_too_small', [
            'min_width' => 320,
            'min_height' => 160,
            'width' => 319,
            'height' => 720,
        ]));
});

it('fails when image height is below the minimum', function () {
    $file = UploadedFile::fake()->image('image.jpg', 1280, 159);

    $validator = Validator::make(
        ['image' => $file],
        ['image' => [new ImageDimensionsWithinConfig]],
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('image'))
        ->toBe(trans('medialibrary-extensions::messages.image_too_small', [
            'min_width' => 320,
            'min_height' => 160,
            'width' => 1280,
            'height' => 159,
        ]));
});

it('passes when image dimensions are exactly at the configured limits', function () {
    $file = UploadedFile::fake()->image('image.jpg', 1920, 1080);

    $validator = Validator::make(
        ['image' => $file],
        ['image' => [new ImageDimensionsWithinConfig]],
    );

    expect($validator->passes())->toBeTrue();
});

it('passes when image dimensions are exactly at the minimum limits', function () {
    $file = UploadedFile::fake()->image('image.jpg', 320, 160);

    $validator = Validator::make(
        ['image' => $file],
        ['image' => [new ImageDimensionsWithinConfig]],
    );

    expect($validator->passes())->toBeTrue();
});

it('passes when the value is not an uploaded file', function () {
    $validator = Validator::make(
        ['image' => 'not-an-upload'],
        ['image' => [new ImageDimensionsWithinConfig]],
    );

    expect($validator->passes())->toBeTrue();
});

it('passes when the uploaded file is invalid', function () {
    $file = Mockery::mock(UploadedFile::class);

    $file
        ->shouldReceive('isValid')
        ->once()
        ->andReturnFalse();

    $rule = new ImageDimensionsWithinConfig;

    $failed = false;

    $rule->validate('image', $file, function () use (&$failed) {
        $failed = true;
    });

    expect($failed)->toBeFalse();
});

it('passes when the uploaded file is not an image', function () {
    $file = UploadedFile::fake()->create(
        'document.pdf',
        100,
        'application/pdf',
    );

    $validator = Validator::make(
        ['image' => $file],
        ['image' => [new ImageDimensionsWithinConfig]],
    );

    expect($validator->passes())->toBeTrue();
});
