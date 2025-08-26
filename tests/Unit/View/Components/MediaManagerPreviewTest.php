<?php

use Illuminate\Support\Collection;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerPreview;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

beforeEach(function () {
    // Mock TemporaryUpload::forCurrentSession static method for tests using temporary uploads
    //    TemporaryUpload::shouldReceive('forCurrentSession')->andReturn(collect());
});

it('accepts a HasMedia model instance and sets properties accordingly', function () {
    $model = Blog::create(['title' => 'test']);
    //    $mockModel->shouldReceive('getMorphClass')->andReturn('App\Models\Dummy');
    //    $mockModel->shouldReceive('getKey')->andReturn(123);
    //    $mockModel->shouldReceive('getMedia')->andReturn(collect());

    $component = new MediaManagerPreview(
        modelOrClassName: $model,
        imageCollection: 'images',
        documentCollection: 'docs',
        youtubeCollection: 'videos'
    );

    expect($component->model)->toBe($model)
        ->and($component->modelType)->toBe('Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog')
        ->and($component->modelId)->toBe($model->id)
        ->and($component->temporaryUpload)->toBeFalse()
        ->and($component->media)->toBeInstanceOf(Collection::class);
});

it('accepts a string model class name and sets temporaryUpload to true', function () {
    $component = new MediaManagerPreview(modelOrClassName: 'App\Models\DummyClass');

    expect($component->model)->toBeNull()
        ->and($component->modelType)->toBe('App\Models\DummyClass')
        ->and($component->modelId)->toBeNull()
        ->and($component->temporaryUpload)->toBeTrue();
});

it('throws exception if modelOrClassName is invalid type', function () {
    new MediaManagerPreview(modelOrClassName: 12345);
})->throws(Exception::class, 'model-or-class-name must be either a HasMedia model or a string representing the model class')->todo();

it('sets showMenu to true if destroyEnabled, showOrder or setAsFirstEnabled is true', function () {
    $mockModel = Mockery::mock(HasMedia::class);
    $mockModel->shouldReceive('getMorphClass')->andReturn('App\Models\Dummy');
    $mockModel->shouldReceive('getKey')->andReturn(1);
    $mockModel->shouldReceive('getMedia')->andReturn(collect());

    foreach ([['destroyEnabled' => true], ['showOrder' => true], ['setAsFirstEnabled' => true]] as $flags) {
        $component = new MediaManagerPreview(
            modelOrClassName: $mockModel,
            destroyEnabled: $flags['destroyEnabled'] ?? false,
            showOrder: $flags['showOrder'] ?? false,
            setAsFirstEnabled: $flags['setAsFirstEnabled'] ?? false,
        );

        expect($component->showMenu)->toBeTrue();
    }
});

it('sets showMenu to false if all destroyEnabled, showOrder and setAsFirstEnabled are false', function () {
    $mockModel = Mockery::mock(HasMedia::class);
    $mockModel->shouldReceive('getMorphClass')->andReturn('App\Models\Dummy');
    $mockModel->shouldReceive('getKey')->andReturn(1);
    $mockModel->shouldReceive('getMedia')->andReturn(collect());

    $component = new MediaManagerPreview(
        modelOrClassName: $mockModel,
        destroyEnabled: false,
        showOrder: false,
        setAsFirstEnabled: false,
    );

    expect($component->showMenu)->toBeFalse();
});
it('merges media from model collections correctly', function () {
    $media1 = Mockery::mock(Media::class);
    $media1->shouldReceive('getCustomProperty')->with('priority', PHP_INT_MAX)->andReturn(PHP_INT_MAX);

    $media2 = Mockery::mock(Media::class);
    $media2->shouldReceive('getCustomProperty')->with('priority', PHP_INT_MAX)->andReturn(PHP_INT_MAX);

    $collectionImages = collect([$media1]);
    $collectionDocs = collect([$media2]);

    $mockModel = Mockery::mock(HasMedia::class);
    $mockModel->shouldReceive('getMorphClass')->andReturn('App\Models\Dummy');
    $mockModel->shouldReceive('getKey')->andReturn(1);
    $mockModel->shouldReceive('getMedia')->with('images')->andReturn($collectionImages);
    $mockModel->shouldReceive('getMedia')->with('documents')->andReturn($collectionDocs);
    $mockModel->shouldReceive('getMedia')->with('youtube')->andReturn(collect());

    $component = new MediaManagerPreview(
        modelOrClassName: $mockModel,
        imageCollection: 'images',
        documentCollection: 'documents',
        youtubeCollection: 'youtube',
    );

    expect($component->media)->toBeInstanceOf(Collection::class)
        ->and($component->media->count())->toBe(2);
});


it('merges temporary uploads when temporaryUploads is true', function () {
    // Mock TemporaryUpload::forCurrentSession to return collections with different counts
    TemporaryUpload::shouldReceive('forCurrentSession')->with('images')->andReturn(collect(['img1', 'img2']));
    TemporaryUpload::shouldReceive('forCurrentSession')->with('youtube')->andReturn(collect(['vid1']));
    TemporaryUpload::shouldReceive('forCurrentSession')->with('documents')->andReturn(collect(['doc1', 'doc2', 'doc3']));

    $component = new MediaManagerPreview(
        modelOrClassName: 'App\Models\DummyClass',
        imageCollection: 'images',
        documentCollection: 'documents',
        youtubeCollection: 'youtube',
        temporaryUploads: true,
    );

    expect($component->media)->toBeInstanceOf(Collection::class)
        ->and($component->media->count())->toBe(6);
})->todo();

it('returns the correct view', function () {
    $mockModel = Mockery::mock(HasMedia::class);
    $mockModel->shouldReceive('getMorphClass')->andReturn('App\Models\Dummy');
    $mockModel->shouldReceive('getKey')->andReturn(1);
    $mockModel->shouldReceive('getMedia')->andReturn(collect());

    $component = new MediaManagerPreview(modelOrClassName: $mockModel);

    $view = $component->render();

    expect($view)->toBeInstanceOf(Illuminate\View\View::class);
    expect($view->name())->toBe('media-library-extensions::components.bootstrap-5.media-manager-preview');
});

it('render returns the correct view when only class name provided', function () {
    //    $mockModel = Mockery::mock(HasMedia::class);
    //    $mockModel->shouldReceive('getMorphClass')->andReturn('App\Models\Dummy');
    //    $mockModel->shouldReceive('getKey')->andReturn(1);
    //    $mockModel->shouldReceive('getMedia')->andReturn(collect());

    $component = new MediaManagerPreview(modelOrClassName: 'Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog');

    $view = $component->render();

    expect($component->destroyEnabled)->toBeFalse()
        ->and($component->setAsFirstEnabled)->toBeFalse()
        ->and($component->showOrder)->toBeFalse()
        ->and($component->temporaryUpload)->toBeTrue();
    //        ->and($component->frontendTheme)->toBe('bootstrap-5');
    expect($view)->toBeInstanceOf(Illuminate\View\View::class);
    expect($view->name())->toBe('media-library-extensions::components.bootstrap-5.media-manager-preview');
});

it('renders view and matches snapshot (plain)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);
    $html = Blade::render(
        '<x-mle-media-manager-preview id="test-media-modal" :model-or-class-name="$modelOrClassName" image_collection="images" :frontend-theme="$frontendTheme"/>',
        [
            'modelOrClassName' => $model,
            'frontendTheme' => 'plain'
        ]
    );
    expect($html)->toMatchSnapshot();
});

it('renders view and matches snapshot (bootstrap-5)', function () {
    $model = $this->getModelWithMedia(['image' => 2, 'document' => '1', 'audio' => 1, 'video' => 1]);
    $html = Blade::render(
        '<x-mle-media-manager-preview id="test-media-modal" :model-or-class-name="$modelOrClassName" image_collection="images" :frontend-theme="$frontendTheme"/>',
        [
            'modelOrClassName' => $model,
            'frontendTheme' => 'bootstrap-5'
        ]
    );
    expect($html)->toMatchSnapshot();
});
