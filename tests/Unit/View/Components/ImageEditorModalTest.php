<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\View\Components\ImageEditorModal;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use UnexpectedValueException;

beforeEach(function () {
    Route::macro('mle_prefix_route', fn ($name) => "mle.$name");

    // Fake route helper
    Route::get('mle.save-updated-medium/{media}', fn () => 'updated medium')->name('mle.save-updated-medium');
    Route::get('mle.save-updated-temporary-upload/{media}', fn () => 'updated temporary')->name('mle.save-updated-temporary-upload');
});

it('renders image editor modal component (permanent media)', function () {
    $options = [

    ];
    $model = $this->getModelWithMedia(['image' => 3 ]);
    $medium = $model->getFirstMedia('image_collection');

    $html = Blade::render('<x-mle-image-editor-modal
                    id="blog-images"
                    title="My title"
                    initiator-id="blog-images"
                    :medium="$medium"
                    :model-or-class-name="$modelClass"
                    :options="$options"
                />', [
        'modelClass' => $model,
        'medium' => $medium,
        'options' => $options,
    ]);

    expect($html)
        ->toContain('id="blog-images-iem-'.$medium->id.'"')
        ->toContain((string) $medium->id)
        ->toContain('My title')
        ->and($html)->toMatchSnapshot();

});

it('renders image editor modal component (temporary media)', function () {
    $options = [

    ];
    $model = $this->getModelWithMedia(['image' => 3 ]);
    $medium = $model->getFirstMedia('image_collection');

    $html = Blade::render('<x-mle-image-editor-modal
                    id="blog-images"
                    title="My title"
                    initiator-id="blog-images"
                    :medium="$medium"
                    :model-or-class-name="$modelClass"
                    :options="$options"
                />', [
        'modelClass' => $model->getMorphClass(),
        'medium' => $medium,
        'options' => $options,
    ]);

    expect($html)
        ->toContain('id="blog-images-iem-'.$medium->id.'"')
        ->toContain((string) $medium->id)
        ->toContain('My title')
        ->and($html)->toMatchSnapshot();

})->todo();

it('constructs with model and sets properties', function () {
    $model = $this->getModelWithMedia();

    $medium = $model->getFirstMedia('image_collection');

    $component = new ImageEditorModal(
        id: 'uploader-0',
        modelOrClassName: $model,
        medium: $medium,
        initiatorId: 'uploader-1',
        title: 'blaat',
        options: []
    );

    expect($component->model)->toBe($model)
        ->and($component->modelType)->toBe($model->getMorphClass())
        ->and($component->modelId)->toBe($model->getKey())
        ->and($component->temporaryUploadMode)->toBeFalse()
        ->and($component->config['modelType'])->toBe($model->getMorphClass())
        ->and($component->id)->toBe('uploader-0'.'-iem-'.$medium->id)
//        ->and($component->config['collection'])->toBe('avatars')
        ->and($component->render())->toBeInstanceOf(View::class);
});

it('constructs with model class name string for temporary upload', function () {
    $model = $this->getModelWithMedia();
    $medium = $model->getFirstMedia('image_collection');

    $component = new ImageEditorModal(
        id: 'uploader-1',
        modelOrClassName: $model->getMorphClass(),
        medium: $medium,
        initiatorId: 'uploader-2',
        options: []
    );

    expect($component->model)->toBeNull()
        ->and($component->modelType)->toBe($model->getMorphClass())
        ->and($component->temporaryUploadMode)->toBeTrue()
        ->and($component->config['temporaryUploadMode'])->toBeTrue()
        ->and($component->config['mediumId'])->toBe($medium->id)
        ->and($component->render())->toBeInstanceOf(View::class);
});

it('throws when modelOrClassName is null', function () {
    $this->expectException(\TypeError::class);
    //    $this->expectExceptionMessage('model-or-class-name attribute must be set');

    $model = $this->getModelWithMedia();
    $medium = $model->getFirstMedia('image_collection');

    new ImageEditorModal(
        id: 'uploader-2',
        modelOrClassName: null,
        medium: $medium,
        initiatorId: 'fail-test',
        options: []
    );
});

it('throws when modelOrClassName is an invalid type', function () {
    $this->expectException(\TypeError::class);
    $this->expectExceptionMessage('model-or-class-name must be either a HasMedia model or a string representing the model class');

    $model = $this->getTestModelNotExtendingHasMedia();
    $medium = new Media([
        'id' => 404,
        'collection_name' => 'invalids',
    ]);

    new ImageEditorModal(
        id: 'uploader-3',
        modelOrClassName: $model,
        medium: $medium, // Invalid type
        initiatorId: 'fail-test',
        options: []
    );
});

it('throws when modelOrClassName is an class name', function () {
    $this->expectException(UnexpectedValueException::class);
    //    $this->expectExceptionMessage('model-or-class-name must be either a HasMedia model or a string representing the model class');

    $model = $this->getTestModelNotExtendingHasMedia();
    $medium = new Media([
        'id' => 404,
        'collection_name' => 'invalids',
    ]);

    new ImageEditorModal(
        id: 'uploader-3',
        modelOrClassName: $model->getMorphClass(), // Invalid type
        medium: $medium,
        initiatorId: 'fail-test',
        options: []
    );
});
