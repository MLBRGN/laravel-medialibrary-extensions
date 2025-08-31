<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View as ViewInstance;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\YouTubeUploadForm;
use Spatie\MediaLibrary\HasMedia;
use Mockery\MockInterface;

it('initializes with a HasMedia model', function () {

    $model = $this->getTestBlogModel();

    $component = new YouTubeUploadForm(
        youtubeCollection: 'youtube',
        id: 'component-yt',
        frontendTheme: 'default',
        mediaCollection: null,
        imageCollection: null,
        documentCollection: null,
        videoCollection: 'videos',
        audioCollection: 'audios',
        modelOrClassName: $model,
        allowedMimeTypes: 'video/*',
        multiple: true,
        destroyEnabled: false,
        setAsFirstEnabled: false,
        useXhr: null
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
        youtubeCollection: 'youtube',
        id: 'component-yt',
        frontendTheme: 'custom',
        mediaCollection: null,
        imageCollection: null,
        documentCollection: null,
        videoCollection: 'videos',
        audioCollection: 'audios',
        modelOrClassName: $model->getMorphClass(),
        allowedMimeTypes: '',
        multiple: false,
        destroyEnabled: true,
        setAsFirstEnabled: true,
        useXhr: true
    );

    expect($component->model)->toBeNull()
        ->and($component->modelType)->toBe($model->getMorphClass())
        ->and($component->modelId)->toBeNull()
//        ->and($component->mediaPresent)->toBeFalse()
        ->and($component->useXhr)->toBeTrue();
});

it('throws if modelOrClassName is non existing class name', function () {
    new YouTubeUploadForm(
        youtubeCollection: null,
        id: 'comp',
        frontendTheme: null,
        mediaCollection: null,
        imageCollection: null,
        documentCollection: null,
        videoCollection: 'videos',
        audioCollection: 'audios',
        modelOrClassName: 'someDummyClassName',
        allowedMimeTypes: '',
        multiple: false,
        destroyEnabled: false,
        setAsFirstEnabled: false,
        useXhr: null
    );
})->throws(Exception::class);

it('throws if modelOrClassName class does not extend HasMedia', function () {
    $model = $this->getTestModelNotExtendingHasMedia();

    new YouTubeUploadForm(
        youtubeCollection: null,
        id: 'comp',
        frontendTheme: null,
        mediaCollection: null,
        imageCollection: null,
        documentCollection: null,
        videoCollection: 'videos',
        audioCollection: 'audios',
        modelOrClassName: $model, // invalid
        allowedMimeTypes: '',
        multiple: false,
        destroyEnabled: false,
        setAsFirstEnabled: false,
        useXhr: null
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
        youtubeCollection: 'youtube',
        id: 'yt-comp',
        frontendTheme: $theme,
        mediaCollection: null,
        imageCollection: null,
        documentCollection: null,
        videoCollection: 'videos',
        audioCollection: 'audios',
        modelOrClassName: $model->getMorphClass(),
        allowedMimeTypes: '',
        multiple: false,
        destroyEnabled: false,
        setAsFirstEnabled: false,
        useXhr: false
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(ViewInstance::class);
});
