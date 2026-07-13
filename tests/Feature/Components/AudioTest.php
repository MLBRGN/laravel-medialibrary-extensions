<?php

use Illuminate\Support\Facades\Blade;
use Mlbrgn\MediaLibraryExtensions\View\Components\Audio;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

it('initializes correctly and uses correct suffix', function () {

    $medium = $this->getMediaModelWithMedia(['audio' => 1]);

    $component = new Audio(
        $medium
    );

    expect($component->getDomId())->toBe($component->id.'-audio');
});

it('can be instantiated with a medium', function () {
    $medium = $this->getMediaModelWithMedia(['audio' => 1]);

    $html = Blade::render('<x-mle-audio id="test-audio" :medium="$medium" />', [
        'medium' => $medium,
    ]);

    expect($html)->toContain('test-audio-audio');
    expect($html)->toMatchSnapshot();
});

it('can be instantiated with a TemporaryUpload', function () {
    Storage::fake('media');
    $temporaryUpload = $this->getTemporaryUpload();

    $html = Blade::render('<x-mle-audio id="test-audio" :medium="$medium" />', [
        'medium' => $temporaryUpload,
    ]);

    expect($html)->toContain('test-audio-audio');
    expect($html)->toMatchSnapshot();
});

it('renders the correct Blade view', function () {
    $media = new Media(['id' => 789]);
    $component = new Audio($media);

    $view = $component->render();
    expect($view->name())->toBe('medialibrary-extensions::components.audio');
});
