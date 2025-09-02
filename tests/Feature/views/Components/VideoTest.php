<?php

use Illuminate\Support\Facades\Blade;
use Mlbrgn\MediaLibraryExtensions\View\Components\Video;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

it('sets the id correctly for media', function () {
    $media = new Media();
    $media->id = 123;
    $media->mime_type = 'video/mp4';
    $media->setRelation('model', null); // Media needs this sometimes

    $component = new Video($media);

    expect($component->id)->toBe('mle-video-123');
});

it('sets the id correctly for temporary upload', function () {
    $upload = new TemporaryUpload();
    $upload->id = 456;
    $upload->mime_type = 'video/webm';

    $component = new Video($upload);

    expect($component->id)->toBe('mle-video-456');
});

it('renders the correct view', function () {
    $media = new Media();
    $media->id = 1;
    $media->mime_type = 'video/mp4';

    $component = new Video($media);

    $view = $component->render();

    expect($view->name())->toBe('media-library-extensions::components.video');
});

it('renders the expected video tag', function () {
    $media = Mockery::mock(Media::class)->makePartial();
    $media->id = 99;
    $media->mime_type = 'video/mp4';
    $media->shouldReceive('getUrl')->andReturn('/storage/video.mp4');

    $html = Blade::renderComponent(new Video($media));

    expect($html)->toContain('<video')
        ->toContain('id="mle-video-99"')
        ->toContain('<source src="/storage/video.mp4" type="video/mp4">');
});
