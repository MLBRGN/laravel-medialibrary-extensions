<?php

use Illuminate\Support\Facades\Blade;
use Mlbrgn\MediaLibraryExtensions\View\Components\Video;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

it('initializes correctly and sets id', function () {

    $medium = $this->getMediaModelWithMedia(['audio' => 1]);

    $component = new Video(
        $medium
    );

    expect($component->id)->toBe('mle-video-'.$medium->id);
});

it('can be instantiated with a medium and match snapshot', function () {
    $medium = $this->getMediaModelWithMedia(['audio' => 1]);
    $html = Blade::render('<x-mle-video id="test-video" :medium="$medium" />', [
        'medium' => $medium,
    ]);

    expect($html)->toContain($medium->id);
    expect($html)->toMatchSnapshot();
});

it('can be instantiated with a TemporaryUpload and match snapshot', function () {
    Storage::fake('media');
    $temporaryUpload = $this->getTemporaryUpload();

    $html = Blade::render('<x-mle-video id="test-video" :medium="$medium" />', [
        'medium' => $temporaryUpload,
    ]);

    expect($html)->toContain($temporaryUpload->id);
    //    expect($html)->toMatchSnapshot();
});

it('renders the correct Blade view', function () {
    $media = new Media(['id' => 303]);
    $component = new Video($media);

    $view = $component->render();
    expect($view->name())->toBe('media-library-extensions::components.video');
});
