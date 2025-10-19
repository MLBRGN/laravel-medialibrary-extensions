<?php

use Illuminate\Support\Facades\Config;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\UploadForm;

it('throws translated exception if invalid class name is provided', closure: function () {
    $component = new UploadForm(
        id: 'upload1',
        modelOrClassName: 'someDummyClassName',
        medium: null,
        collections: [
            'image' => 'images',
            'youtube' => 'youtube',
            'document' => 'docs',
            'video' => 'videos',
            'audio' => 'audio',
        ],
        options: [
            'frontendTheme' => 'bootstrap-5',
        ],
        multiple: true,
        readonly: true,
        disabled: true,
        //        frontendTheme: 'plain',
    );

    $component->render();
})->throws(InvalidArgumentException::class);

it('throws exception if given a model that does not implement HasMedia', function () {
    // A valid existing class, but not implementing HasMedia
    $model = $this->getTestModelNotExtendingHasMedia();

    $component = new UploadForm(
        id: 'upload-invalid-class',
        modelOrClassName: $model,
        medium: null,
        collections: [
            'image' => 'images',
            'youtube' => 'youtube',
            'document' => 'docs',
            'video' => 'videos',
            'audio' => 'audio',
        ],
        options: [],
    );

    $component->render();
    // });
})->throws(TypeError::class);

it('honors frontend theme', function () {
    Config::set('media-library-extensions.frontend_theme', 'default-theme');

    // A valid existing class, but not implementing HasMedia
    $model = $this->getTestBlogModel();

    $component = new UploadForm(
        id: 'upload-invalid-class',
        modelOrClassName: $model,
        medium: null,
        collections: [
            'image' => 'images',
            'youtube' => 'youtube',
            'document' => 'docs',
            'video' => 'videos',
            'audio' => 'audio',
        ],
        options: ['frontendTheme' => 'something-else'],
    );

    expect($component->getConfig('frontendTheme'))->toBe('something-else');
});

it('uses allowedMimeTypes from config if allowedMimeTypes not provided', function () {
    $model = $this->getTestBlogModel();

    $mimeTypesString = '';
    $component = new UploadForm(
        id: 'upload-empty-mime',
        modelOrClassName: $model,
        medium: null,
        collections: [
            'image' => 'images',
            'youtube' => '',
            'document' => 'documents',
            'video' => '',
            'audio' => '',
        ],
        options: [
            'allowedMimeTypes' => $mimeTypesString, //
        ],
    );

    $component->render();

    expect($component->getConfig('allowedMimeTypes'))->toBe('');
    expect($component->getConfig('allowedMimeTypesHuman'))->toBe('');

});

it('sets allowedMimeTypes and allowedMimeTypesHuman from options', function () {
    $model = $this->getTestBlogModel();

    $mimeTypesString = 'image/png, image/jpeg';
    $component = new UploadForm(
        id: 'upload-empty-mime',
        modelOrClassName: $model,
        medium: null,
        collections: [
            'image' => 'images',
            'youtube' => '',
            'document' => 'documents',
            'video' => '',
            'audio' => '',
        ],
        options: [
            'allowedMimeTypes' => $mimeTypesString, //
        ],
    );

    $component->render();

    expect($component->getConfig('allowedMimeTypes'))->toBe($mimeTypesString);
    expect($component->getConfig('allowedMimeTypesHuman'))->toBe('PNG, JPEG');

});

it('initializes correctly when given a HasMedia model instance', function () {
    // Arrange
    $model = $this->getTestBlogModel();

    $component = new UploadForm(
        id: 'upload3',
        modelOrClassName: $model,
        medium: null,
        collections: [
            'image' => 'images',
            'youtube' => 'youtube',
            'document' => 'docs',
            'video' => 'videos',
            'audio' => 'audio',
        ],
        options: [
            'frontendTheme' => 'plain',
            'allowedMimeTypes' => 'image/jpeg, image/png',
            'showDestroyButton' => true,
            'showSetAsFirstButton' => true,
            'useXhr' => null,
        ],
        multiple: true,
    );

    // Act
    $view = $component->render();

    // Assert: model binding
    expect($component->model)
        ->toBe($model)
        ->and($component->modelType)->toBe($model->getMorphClass())
        ->and($component->modelId)->toBe($model->getKey());

    // Assert: config resolution (mime types, theme, xhr usage)
    $allowedMimeTypes = $component->getConfig('allowedMimeTypes');
    $allowedMimeTypesHuman = $component->getConfig('allowedMimeTypesHuman');

    //    dd($component->config);
    //    dd($component->getConfig('useXhr'));
    expect($allowedMimeTypes)->toBeString()->not->toBeEmpty()
        ->and($allowedMimeTypesHuman)->toBeString()->not->toBeEmpty()
        ->and($component->getConfig('frontendTheme'))->toBe('plain')
        ->and($component->getConfig('useXhr'))->toBe(config('media-library-extensions.use_xhr'))
        ->and($view)->toBeInstanceOf(View::class)
        ->and($view->name())->toContain('upload-form');

})->todo();

// it('sets model properties correctly when given a HasMedia model instance', function () {
//    $model = $this->getTestBlogModel();
//
//    $component = new UploadForm(
//        id: 'upload3',
//        modelOrClassName: $model,
//        medium: null,
//        collections: [
//            'image' => 'images',
//            'youtube' => 'youtube',
//            'document' => 'docs',
//            'video' => 'videos',
//            'audio' => 'audio',
//        ],
//        options: [
//            'frontendTheme'=> 'plain',
//            'allowedMimeTypes' => '',
//            'showDestroyButton' => true,
//            'showSetAsFirstButton' => true,
//            'useXhr' => null,
//        ],
//        multiple: true,
//    );
//
//    $view = $component->render();
//
//    expect($component->model)->toBe($model)
//        ->and($component->modelType)->toBe($model->getMorphClass())
//        ->and($component->modelId)->toBe($model->getKey())
// //        ->and($component->mediaPresent)->toBeFalse()
//        ->and($component->getConfig('allowedMimeTypesHuman'))->not()->toBeEmpty()
//        ->and($component->getConfig('allowedMimeTypes'))->not()->toBeEmpty()
//        ->and($component->getConfig('useXhr'))->toBe(config('media-library-extensions.use_xhr'))
//        ->and($view)->toBeInstanceOf(View::class);
// });

it('sets model properties correctly when given a string model class name', function () {
    $model = $this->getTestBlogModel();
    $component = new UploadForm(
        id: 'upload4',
        modelOrClassName: $model->getMorphClass(),
        medium: null,
        collections: [
            'image' => 'images',
            'youtube' => 'youtube',
            'document' => 'docs',
            'video' => 'videos',
            'audio' => 'audio',
        ],
        options: [
            'frontendTheme' => 'plain',
            'allowedMimeTypes' => 'image/jpeg,image/png',
            'showDestroyButton' => false,
            'showSetAsFirstButton' => false,
            'useXhr' => true,
        ],
        multiple: false,
    );
    // Act
    $view = $component->render();

    // Assert: config resolution (mime types, theme, xhr usage)
    $allowedMimeTypes = $component->getConfig('allowedMimeTypes');
    $allowedMimeTypesHuman = $component->getConfig('allowedMimeTypesHuman');

    expect($component->model)->toBeNull()
        ->and($component->modelType)->toBe($model->getMorphClass())
        ->and($component->modelId)->toBeNull()
        ->and($allowedMimeTypes)->toBeString()->not->toBeEmpty()
        ->and($allowedMimeTypesHuman)->toBeString()->not->toBeEmpty()
        ->and($component->getConfig('frontendTheme'))->toBe('plain')
        ->and($component->getConfig('useXhr'))->toBe(config('media-library-extensions.use_xhr'))
        ->and($view)->toBeInstanceOf(View::class)
        ->and($view->name())->toContain('upload-form');
});
