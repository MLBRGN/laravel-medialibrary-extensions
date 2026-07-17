<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Mlbrgn\MediaLibraryExtensions\View\Components\ImageEditorModal;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use UnexpectedValueException;

beforeEach(function () {
    Route::macro('mle_prefix_route', fn ($name) => "mle.$name");

    // Fake route helper
    Route::get('mle.save-updated-media/{media}', fn () => 'updated medium')->name('mle.save-updated-media');
    Route::get('mle.save-updated-temporary-upload/{media}', fn () => 'updated temporary')->name('mle.save-updated-temporary-upload');
});

it('renders image editor modal component (permanent media)', function () {
    $options = [

    ];
    $model = $this->getModelWithMedia(['image' => 3]);
    $medium = $model->getFirstMedia('image_collection');

    $html = Blade::render('<x-mle-image-editor-modal
                    id="blog-images"
                    :model-or-class-name="$modelClass"
                    :medium="$medium"
                    :collections="$collections"
                    :options="$options"
                    initiator-id="blog-images"
                    title="My title"

                />', [
        'modelClass' => $model,
        'medium' => $medium,
        'options' => $options,
        'collections' => ['image' => 'images'],
    ]);

    expect($html)
        ->toContain('id="blog-images-iem-'.$medium->id.'"')
        ->toContain((string) $medium->id)
        ->toContain('My title')
        ->toContain('"instanceId":');
});

it('renders image editor modal component (temporary media)', function () {
    $options = [

    ];
    $model = $this->getModelWithMedia(['image' => 3]);
    $medium = $model->getFirstMedia('image_collection');

    $html = Blade::render('<x-mle-image-editor-modal
                    id="blog-images"
                    title="My title"
                    initiator-id="blog-images"
                    :medium="$medium"
                    :model-or-class-name="$modelClass"
                    :collections="[\'image\' => \'images\']"
                    :options="$options"
                />', [
        'modelClass' => $model->getMorphClass(),
        'medium' => $medium,
        'options' => $options,
    ]);

    // Normalize unstable tokens for snapshot stability
    $html = preg_replace('/("clientToken":")(.*?)(")/i', '$1<token>$3', $html);
    $html = preg_replace('/("instanceId":")(.*?)(")/i', '$1<instance>$3', $html);

    expect($html)
        ->toContain('data-mle-image-editor-modal')
        ->toContain('id="blog-images-iem-'.$medium->id.'"')
        ->toContain((string) $medium->id)
        ->toContain('My title')
        ->toContain('id="config-blog-images"')
        ->toContain('"collections":{"image":"images"}')
        ->toContain('"instanceId":"<instance>"')
        ->toContain('"clientToken":"<token>"');

});

it('constructs with model and sets properties', function () {
    $model = $this->getModelWithMedia();

    $medium = $model->getFirstMedia('image_collection');

    $component = new ImageEditorModal(
        id: 'uploader-0',
        modelOrClassName: $model,
        medium: $medium,
        singleMedia: null,
        collections: ['image' => 'images'],
        options: [],

        title: 'blaat'
    );

    expect($component->model)->toBe($model)
        ->and($component->modelType)->toBe($model->getMorphClass())
        ->and($component->modelId)->toBe($model->getKey())
        ->and($component->temporaryUploadMode)->toBeFalse()
        ->and($component->getConfig('modelType'))->toBe($model->getMorphClass())
        ->and($component->getDomId())->toBe('uploader-0-iem-'.$medium->id)
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
        singleMedia: null,// TODO if i don't pass this test fails
        collections: ['image' => 'images'],
        options: [],

    );

    expect($component->model)->toBeNull()
        ->and($component->modelType)->toBe($model->getMorphClass())
        ->and($component->temporaryUploadMode)->toBeTrue()
        ->and($component->getConfig('temporaryUploadMode'))->toBeTrue()
        ->and($component->getConfig('mediumId'))->toBe($medium->id)
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
        collections: ['image' => 'images'],
        options: [],

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
        singleMedia: null,
        collections: ['image' => 'images'],
        options: [],

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
        singleMedia: null,
        collections: ['image' => 'images'],
        options: [],

    );
});
