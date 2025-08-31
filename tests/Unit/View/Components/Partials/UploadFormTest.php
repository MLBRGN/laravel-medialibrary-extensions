<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\UploadForm;

it('throws translated exception if invalid class name is provided', function () {
    $component = new UploadForm(
        id: 'upload1',
        frontendTheme: 'plain',
        imageCollection: 'images',
        documentCollection: 'docs',
        youtubeCollection: 'youtube',
        videoCollection: 'videos',
        audioCollection: 'audios',
        modelOrClassName: 'someDummyClassName',
    );

    $component->render();
})->throws(InvalidArgumentException::class);

it('throws exception if given a model that does not implement HasMedia', function () {
    // A valid existing class, but not implementing HasMedia
    $model = $this->getTestModelNotExtendingHasMedia();

    $component = new UploadForm(
        id: 'upload-invalid-class',
        frontendTheme: 'plain',
        imageCollection: 'images',
        documentCollection: 'docs',
        youtubeCollection: 'youtube',
        videoCollection: 'videos',
        audioCollection: 'audios',
        modelOrClassName: $model,
    );

    $component->render();
//});
})->throws(TypeError::class);


it('honors frontend theme', function () {
    Config::set('media-library-extensions.frontend_theme', 'default-theme');

    // A valid existing class, but not implementing HasMedia
    $model = $this->getTestBlogModel();

    $component = new UploadForm(
        id: 'upload-invalid-class',
        frontendTheme: 'something-else',
        imageCollection: 'images',
        documentCollection: 'docs',
        youtubeCollection: 'youtube',
        videoCollection: 'videos',
        audioCollection: 'audios',
        modelOrClassName: $model,
    );

       expect($component->frontendTheme)->toBe('something-else');
});

//it('sets mediaPresent to true if model has media in the given image collection', function () {
//    $model = $this->getTestBlogModel();
//
//    // Mock hasMedia() to return true for the test image collection
//    $model = Mockery::mock($model)->makePartial();
//    $model->shouldReceive('hasMedia')
//        ->with('images')
//        ->andReturn(true);
//
//    $component = new UploadForm(
//        id: 'upload-media-present',
//        frontendTheme: 'plain',
//        imageCollection: 'images',
//        documentCollection: 'docs',
//        videoCollection: 'videos',
//        audioCollection: 'audios',
//        youtubeCollection: 'youtube',
//        modelOrClassName: $model,
//    );
//
//    $component->render();
//
//    expect($component->mediaPresent)->toBeTrue();
//});

it('uses allowedMimeTypes from config if allowedMimeTypes is empty', function () {
    $model = $this->getTestBlogModel();

    config()->set('media-library-extensions.allowed_mimetypes', ['image/jpeg', 'image/png']);
    config()->set('media-library-extensions.mimetype_labels', [
        'image/jpeg' => 'JPEG Image',
        'image/png' => 'PNG Image',
    ]);

    $component = new UploadForm(
        id: 'upload-empty-mime',
        frontendTheme: 'plain',
        imageCollection: 'images',
        documentCollection: 'docs',
        videoCollection: 'videos',
        audioCollection: 'audios',
        youtubeCollection: 'youtube',
        modelOrClassName: $model,
        allowedMimeTypes: '', // empty here
    );

    $component->render();

    expect($component->allowedMimeTypes)->toBe('image/jpeg, image/png');
    expect($component->allowedMimeTypesHuman)->toBe('JPEG Image, PNG Image');
})->todo();

it('sets model properties correctly when given a HasMedia model instance', function () {
    $model = $this->getTestBlogModel();

    $component = new UploadForm(
        id: 'upload3',
        frontendTheme: 'plain',
        imageCollection: 'images',
        documentCollection: 'docs',
        videoCollection: 'videos',
        audioCollection: 'audios',
        youtubeCollection: 'youtube',
        modelOrClassName: $model,
        allowedMimeTypes: '',
        multiple: true,
        destroyEnabled: true,
        setAsFirstEnabled: true,
        useXhr: null,
    );

    $view = $component->render();

    expect($component->model)->toBe($model)
        ->and($component->modelType)->toBe($model->getMorphClass())
        ->and($component->modelId)->toBe($model->getKey())
//        ->and($component->mediaPresent)->toBeFalse()
        ->and($component->allowedMimeTypesHuman)->not()->toBeEmpty()
        ->and($component->allowedMimeTypes)->not()->toBeEmpty()
        ->and($component->useXhr)->toBe(config('media-library-extensions.use_xhr'))
        ->and($view)->toBeInstanceOf(View::class);
});

it('sets model properties correctly when given a string model class name', function () {
    $model = $this->getTestBlogModel();
    $component = new UploadForm(
        id: 'upload4',
        frontendTheme: 'plain',
        imageCollection: 'images',
        documentCollection: 'docs',
        videoCollection: 'videos',
        audioCollection: 'audios',
        youtubeCollection: 'youtube',
        modelOrClassName: $model->getMorphClass(),
        allowedMimeTypes: 'image/jpeg,image/png',
        multiple: false,
        destroyEnabled: false,
        setAsFirstEnabled: false,
        useXhr: true,
    );

    $view = $component->render();

    expect($component->model)->toBeNull()
        ->and($component->modelType)->toBe($model->getMorphClass())
        ->and($component->modelId)->toBeNull()
        ->and($component->allowedMimeTypesHuman)->not()->toBeEmpty()
        ->and($component->allowedMimeTypes)->toBe('image/jpeg,image/png')
        ->and($component->useXhr)->toBeTrue()
        ->and($view)->toBeInstanceOf(View::class);
});
