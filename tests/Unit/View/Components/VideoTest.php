<?php

use Mlbrgn\MediaLibraryExtensions\View\Components\Video;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Blade;

it('can be instantiated with a medium', function () {
    $medium = $this->getMediaModelWithMedia(['audio' => 1]);
    $html = Blade::render('<x-media-library-extensions::video id="test-video" :medium="$medium" />', [
        'medium' => $medium,
    ]);

    expect($html)->toContain($medium->id);
});

it('can be instantiated with a TemporaryUpload', function () {
    Storage::fake('media');
    $temporaryUpload = $this->getTemporaryUpload();

    $html = Blade::render('<x-media-library-extensions::video id="test-video" :medium="$medium" />', [
        'medium' => $temporaryUpload,
    ]);

    expect($html)->toContain($temporaryUpload->id);
});

it('renders the correct Blade view', function () {
    $media = new Media(['id' => 303]);
    $component = new Video($media);

    $view = $component->render();
    expect($view->name())->toBe('media-library-extensions::components.video');
});
