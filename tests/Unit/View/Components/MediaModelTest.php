<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaModal;
use Mockery;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

//it('initializes with a single media collection', function () {
//    $media = collect([Mockery::mock(Media::class), Mockery::mock(Media::class)]);
//
//    $model = $this->getTestBlogModel();
////    $mockModel = Mockery::mock(HasMedia::class);
////    $mockModel->shouldReceive('getMedia')
////        ->with('images')
////        ->andReturn(MediaCollection::make($media));
//
//    $component = new MediaModal(
//        modelOrClassName: $model,
//        mediaCollection: 'images',
//        mediaCollections: null,
//        title: 'Single Collection Test'
//    );
//
////    expect($component->mediaItems)
////        ->toBeInstanceOf(MediaCollection::class)
////        ->and($component->mediaItems->count())->toBe(2);
//});
//
//it('initializes with multiple media collections', function () {
//    $media1 = collect([Mockery::mock(Media::class)]);
//    $media2 = collect([Mockery::mock(Media::class), Mockery::mock(Media::class)]);
//
//    $mockModel = Mockery::mock(HasMedia::class);
//    $mockModel->shouldReceive('getMedia')->with('images')->andReturn(MediaCollection::make($media1));
//    $mockModel->shouldReceive('getMedia')->with('docs')->andReturn(MediaCollection::make($media2));
//
//    $component = new MediaModal(
//        modelOrClassName: $mockModel,
//        mediaCollection: null,
//        mediaCollections: ['images', 'docs'],
//        title: 'Multiple Collections Test'
//    );
//
//    expect($component->mediaItems)
//        ->toBeInstanceOf(MediaCollection::class)
//        ->and($component->mediaItems->count())->toBe(3);
//});
//
//it('initializes with an empty collection if model is null', function () {
//    $component = new MediaModal(
//        modelOrClassName: null,
//        mediaCollection: 'images',
//        mediaCollections: null,
//        title: 'No Model Test'
//    );
//
//    expect($component->mediaItems)->toBeInstanceOf(MediaCollection::class)
//        ->and($component->mediaItems)->toHaveCount(0);
//});
//
//it('initializes with an empty collection if no mediaCollection or mediaCollections is provided', function () {
//    $mockModel = Mockery::mock(HasMedia::class);
//
//    $component = new MediaModal(
//        modelOrClassName: $mockModel,
//        mediaCollection: null,
//        mediaCollections: null,
//        title: 'No Collections Test'
//    );
//
//    expect($component->mediaItems)->toBeInstanceOf(MediaCollection::class)
//        ->and($component->mediaItems)->toHaveCount(0);
//});

it('appends -mod to the id', function () {
    $model = $this->getTestBlogModel();
    $component = new MediaModal(
        modelOrClassName: $model,
        mediaCollection: 'images',
        mediaCollections: null,
        title: 'ID Test',
        id: 'media1'
    );

    expect($component->id)->toBe('media1-mod');
});

it('returns the correct view on render', function () {
 $model = $this->getTestBlogModel();
    $component = new MediaModal(
        modelOrClassName: $model,
        mediaCollection: 'images',
        mediaCollections: null,
        title: 'Render Test'
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class)
        ->and($view->name())->toBe('media-library-extensions::components.bootstrap-5.media-modal');
});
