<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;
use Mlbrgn\MediaLibraryExtensions\View\Components\ImageEditorModal;
use Mockery;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

uses(TestCase::class);

beforeEach(function () {
    Route::macro('mle_prefix_route', fn ($name) => "mle.$name");

    // Fake route helper
    Route::get('mle.save-updated-medium/{media}', fn () => 'updated medium')->name('mle.save-updated-medium');
    Route::get('mle.save-updated-temporary-upload/{media}', fn () => 'updated temporary')->name('mle.save-updated-temporary-upload');
});

test('constructs with model', function () {
    $model = Mockery::mock(HasMedia::class);
    $model->shouldReceive('getMorphClass')->andReturn('App\\Models\\FakeModel');
    $model->shouldReceive('getKey')->andReturn(42);

    $medium = new Media([
        'id' => 101,
        'collection_name' => 'avatars',
    ]);

    $component = new ImageEditorModal(
        id: 'uploader-0',
        modelOrClassName: $model,
        medium: $medium,
        initiatorId: 'uploader-1',
        title: 'blaat'
    );

    expect($component->model)->toBe($model)
        ->and($component->modelType)->toBe('App\\Models\\FakeModel')
        ->and($component->modelId)->toBe(42)
        ->and($component->temporaryUpload)->toBeFalse()
        ->and($component->config['model_type'])->toBe('App\\Models\\FakeModel')
        ->and($component->config['collection'])->toBe('avatars')
        ->and($component->render())->toBeInstanceOf(View::class);
});

test('constructs with model class name string for temporary upload', function () {
    $medium = new Media([
        'id' => 202,
        'collection_name' => 'covers',
    ]);

    $component = new ImageEditorModal(
        id: 'uploader-1',
        modelOrClassName: 'App\\Models\\TemporaryThing',
        medium: $medium,
        initiatorId: 'uploader-2'
    );

    expect($component->model)->toBeNull()
        ->and($component->modelType)->toBe('App\\Models\\TemporaryThing')
        ->and($component->temporaryUpload)->toBeTrue()
        ->and($component->config['temporary_upload'])->toBeTrue()
        ->and($component->config['medium_id'])->toBe(202)
        ->and($component->render())->toBeInstanceOf(View::class);
});

test('throws when modelOrClassName is null', function () {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('model-or-class-name attribute must be set');

    $medium = new Media([
        'id' => 303,
        'collection_name' => 'documents',
    ]);

    new ImageEditorModal(
        id: 'uploader-2',
        modelOrClassName: null,
        medium: $medium,
        initiatorId: 'fail-test'
    );
});

test('throws when modelOrClassName is an invalid type', function () {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('model-or-class-name must be either a HasMedia model or a string representing the model class');

    $medium = new Media([
        'id' => 404,
        'collection_name' => 'invalids',
    ]);

    new ImageEditorModal(
        id: 'uploader-3',
        modelOrClassName: 123, // Invalid type
        medium: $medium,
        initiatorId: 'fail-test'
    );
})->todo();
