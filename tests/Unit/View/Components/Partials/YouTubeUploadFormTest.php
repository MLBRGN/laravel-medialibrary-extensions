<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View as ViewInstance;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\YouTubeUploadForm;

it('initializes with a HasMedia model', function () {

    $model = $this->getTestBlogModel();

    $component = new YouTubeUploadForm(
        id: 'component-yt',
        modelOrClassName: $model,
        medium: null,
        //        mediaCollection: null,
        collections: [
            'image' => null,
            'youtube' => 'youtube',
            'document' => null,
            'video' => 'videos',
            'audio' => 'audios',
        ],
        options: [
            'allowedMimeTypes' => 'video/*',
            'multiple' => true,
            'showDestroyButton' => false,
            'showSetAsFirstButton' => false,
            'useXhr' => null,
        ]

    );

    expect($component->model)->toBe($model)
        ->and($component->modelType)->toBe($model->getMorphClass())
        ->and($component->modelId)->toBe($model->getKey());
    //        ->and($component->mediaPresent)->toBeFalse();
    //        ->and($component->mediaUploadRoute)->toBe('/fake-upload-route')
    //        ->and($component->previewUpdateRoute)->toBe('/fake-preview-update');
});

it('initializes with a model class string', function () {
    $model = $this->getTestBlogModel();
    $component = new YouTubeUploadForm(
        id: 'component-yt',
        modelOrClassName: $model->getMorphClass(),
        medium: null,
        //        mediaCollection: null,
        collections: [
            'image' => null,
            'youtube' => 'youtube',
            'document' => null,
            'video' => 'videos',
            'audio' => 'audios',
        ],
        options: [
            'allowedMimeTypes' => '',
            'multiple' => false,
            'showDestroyButton' => true,
            'showSetAsFirstButton' => true,
            'useXhr' => true,
        ]
    );

    expect($component->model)->toBeNull()
        ->and($component->modelType)->toBe($model->getMorphClass())
        ->and($component->modelId)->toBeNull()
//        ->and($component->mediaPresent)->toBeFalse()
        ->and($component->getConfig('useXhr'))->toBeTrue();
});

it('throws if modelOrClassName is non existing class name', function () {
    new YouTubeUploadForm(
        id: 'comp',
        modelOrClassName: 'someDummyClassName',
        medium: null,
//        frontendTheme: null,
        //        mediaCollection: null,
        collections: [
            'image' => null,
            'youtube' => 'youtube',
            'document' => null,
            'video' => 'videos',
            'audio' => 'audios',
        ],
        options: [
            'allowedMimeTypes' => '',
            'multiple' => false,
            'showDestroyButton' => false,
            'showSetAsFirstButton' => false,
            'useXhr' => null,
        ]
    );
})->throws(Exception::class);

it('throws if modelOrClassName class does not extend HasMedia', function () {
    $model = $this->getTestModelNotExtendingHasMedia();

    new YouTubeUploadForm(
        id: 'comp',
        modelOrClassName: $model,
        medium: null,
//        frontendTheme: null,
        //        mediaCollection: null,
        collections: [
            'image' => null,
            'youtube' => 'youtube',
            'document' => null,
            'video' => 'videos',
            'audio' => 'audios',
        ],
        options: [
            'allowedMimeTypes' => '',
            'multiple' => false,
            'showDestroyButton' => false,
            'showSetAsFirstButton' => false,
            'useXhr' => null,
        ]
    );
})->throws(TypeError::class);

it('renders the correct partial view', function () {
    $model = $this->getTestBlogModel();
    $theme = 'custom';
    $expectedView = "media-library-extensions::components.$theme.partial.youtube-upload-form";

    ViewFacade::shouldReceive('make')
        ->with($expectedView, [], [])
        ->once()
        ->andReturn(Mockery::mock(ViewInstance::class));

    $component = new YouTubeUploadForm(
        id: 'yt-comp',
        modelOrClassName: $model->getMorphClass(),
        medium: null,
        //        mediaCollection: null,
        collections: [
            'image' => null,
            'youtube' => 'youtube',
            'document' => null,
            'video' => 'videos',
            'audio' => 'audios',
        ],
        options: [
            'allowedMimeTypes' => '',
            'multiple' => false,
            'showDestroyButton' => false,
            'showSetAsFirstButton' => false,
            'useXhr' => false,
        ]
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(ViewInstance::class);
})->todo();
