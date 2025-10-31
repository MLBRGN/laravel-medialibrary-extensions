<?php

use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerTinymce;

it('throws exception when no media collections provided', function () {
    expect(fn() => new MediaManagerTinymce(
        id: 'test1',
        modelOrClassName: stdClass::class,
        collections: []
    ))->toThrow(Exception::class);
});

it('sets correct upload routes and field names for single upload', function () {
    $model = $this->getTestBlogModel();
    $component = new MediaManagerTinymce(
        id: 'abc',
        modelOrClassName: $model,
        collections: ['image' => 'images'],
        options: [
            'uploadFieldName' => 'file_single',
        ],
        multiple: false
    );

    $config = $component->config;

    expect($config['mediaUploadRoute'])->toBe(route(mle_prefix_route('media-upload-single')))
        ->and($config['uploadFieldName'])->toBe('file_single')
        ->and($component->id)->toBe('abc-mms');
});

it('sets correct upload routes and field names for multiple upload', function () {
    $model = $this->getTestBlogModel();

    $component = new MediaManagerTinymce(
        id: 'abc',
        modelOrClassName: $model,
        collections: ['image' => 'images'],
        options: [
            'uploadFieldName' => 'file_multiple',
        ],
        multiple: true
    );

    $config = $component->config;

    expect($config['mediaUploadRoute'])->toBe(route(mle_prefix_route('media-upload-multiple')))
        ->and($config['uploadFieldName'])->toBe('file_multiple')
        ->and($component->id)->toBe('abc-mmm');
});

it('disables upload-related options when readonly or disabled', function () {
    $model = $this->getTestBlogModel();
    $component = new MediaManagerTinymce(
        id: 'x1',
        modelOrClassName: $model,
        collections: ['image' => 'images'],
        readonly: true
    );

    $config = $component->config;

    expect($component->getConfig('showUploadForm'))->toBeFalse()
        ->and($component->getConfig('showDestroyButton'))->toBeFalse()
        ->and($component->getConfig('showSetAsFirstButton'))->toBeFalse();
});

it('disables YouTube upload when no youtube collection exists', function () {
    $model = $this->getTestBlogModel();
    $component = new MediaManagerTinymce(
        id: 'yt1',
        modelOrClassName: $model,
        collections: ['image' => 'images']
    );

    expect($component->getConfig('showYouTubeUploadForm'))->toBeFalse();
});

it('calls correct view with configured frontend theme plain', function () {
    $model = $this->getTestBlogModel();
    $component = new MediaManagerTinymce(
        id: 'yt1',
        modelOrClassName: $model,
        collections: ['image' => 'images'],
        options: [
            'uploadFieldName' => 'file_multiple',
            'frontendTheme' => 'plain',
            'showSetAsFirstButton' => false,
        ],
    );

    $view = $component->render();

    expect($component->getConfig('showDestroyButton'))->toBeTrue()
        ->and($component->getConfig('showSetAsFirstButton'))->toBeFalse()
        ->and($component->getConfig('showMediaEditButton'))->toBeTrue()
        ->and($component->getConfig('showOrder'))->toBeFalse()
        ->and($component->getConfig('temporaryUploadMode'))->toBeFalse()
        ->and($component->getConfig('frontendTheme'))->toBe('plain');

    expect($view)->toBeInstanceOf(Illuminate\View\View::class);
    expect($view->name())->toBe('media-library-extensions::components.plain.media-manager-tinymce');
});

it('calls correct view with configured frontend theme bootstrap-5', function () {
    $model = $this->getTestBlogModel();
    $component = new MediaManagerTinymce(
        id: 'yt1',
        modelOrClassName: $model->getMorphClass(),
        collections: ['image' => 'images'],
        options: [
            'uploadFieldName' => 'file_multiple',
            'frontendTheme' => 'bootstrap-5',
            'showDestroyButton' => false,
            'showSetAsFirstButton' => true,
            'showMediaEditButton' => false,
            'showOrder' => true,
            'showUploadForm' => false,
        ],
    );

    $view = $component->render();

    expect($component->getConfig('showDestroyButton'))->toBeFalse()
        ->and($component->getConfig('showSetAsFirstButton'))->toBeTrue()
        ->and($component->getConfig('showMediaEditButton'))->toBeFalse()
        ->and($component->getConfig('showOrder'))->toBeTrue()
        ->and($component->getConfig('temporaryUploadMode'))->toBeTrue()
        ->and($component->getConfig('frontendTheme'))->toBe('bootstrap-5');
    expect($view)->toBeInstanceOf(Illuminate\View\View::class);
    expect($view->name())->toBe('media-library-extensions::components.bootstrap-5.media-manager-tinymce');
});

