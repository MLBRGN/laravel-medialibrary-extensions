<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Mlbrgn\MediaLibraryExtensions\Actions\DestroyMediumAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\DestroyRequest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\ParameterBag;

it('deletes a medium and reorders priorities (JSON)', function () {
    // Arrange: attach some media
    $model = $this->getTestBlogModel();

    $testImage = $this->getFixtureUploadedFile('test.png');
    $testImage2 = $this->getFixtureUploadedFile('test2.png');
    $first = $model->addMedia($testImage)
        ->preservingOriginal()
        ->withCustomProperties(['priority' => 5])
        ->toMediaCollection('images');

    $second = $model->addMedia($testImage2)
        ->preservingOriginal()
        ->withCustomProperties(['priority' => 1])
        ->toMediaCollection('images');

    $request = DestroyRequest::create('/', 'DELETE', [
        'initiator_id' => 'foo',
        'media_manager_id' => 'bar',
        'collections' => ['image' => 'images'],
    ]);

    // Simulate an AJAX/JSON request
    $request->headers->set('Accept', 'application/json');
    $request->setJson(new ParameterBag($request->all()));

    $action = new DestroyMediumAction;

    // Act
    $response = $action->execute($request, $first);
    //    dd($response->getData());

    // Assert response
    expect($response->getStatusCode())->toBe(200);
    expect($response->getData(true))
        ->toHaveKey('message')
        ->toMatchArray(['initiatorId' => 'foo']);
    //        ->toMatchArray(['initiatorId' => 'foo', 'mediaManagerId' => 'bar']);

    // The deleted medium should be gone
    expect(Media::find($first->id))->toBeNull();

    // Priorities should be re-ordered starting from 0
    $remaining = $model->getMedia('images');
    expect($remaining)->toHaveCount(1);
    expect($remaining->first()->getCustomProperty('priority'))->toBe(0);
});

it('skips reorder if no collections are passed', function () {
    $model = $this->getTestBlogModel();

    // Arrange: create a single media item
    $media = $model->addMedia(fake()->image())
        ->preservingOriginal()
        ->withCustomProperties(['priority' => 99])
        ->toMediaCollection('images');

    // Act: create a request with no collections
    $request = DestroyRequest::create('/', 'DELETE', [
        'initiator_id' => 'foo',
        'media_manager_id' => 'bar',
        // collections intentionally omitted
    ]);

    // Make sure Laravel treats this as a JSON request
    $request->headers->set('Accept', 'application/json');
    $request->setJson(new ParameterBag($request->all()));

    $action = new DestroyMediumAction;

    // Execute delete action
    $response = $action->execute($request, $media);

    // Assert: medium is deleted
    expect(Media::find($media->id))->toBeNull();

    // Assert: response is JSON 200
    expect($response->getStatusCode())->toBe(200);
    $data = $response->getData(true);
    expect($data)->toHaveKey('message');
    expect($data['initiatorId'])->toBe('foo');
    expect($data['mediaManagerId'])->toBe('bar');
})->todo();

// beforeEach(function () {
//    // Mock the translation string used in your action
// //    Lang::shouldReceive('__')
// //        ->with('media-library-extensions::messages.medium_removed')
// //        ->andReturn('Medium has been removed');
// });
//
// it('deletes the media and returns a success JSON response when request expects JSON', function () {
//    // Create a fake Media model mock
//    $media = Mockery::mock(Media::class);
//    $media->shouldReceive('delete')->once();
//
//    // Create a request mock that expects JSON and has initiator_id
//    $request = Mockery::mock(MediaManagerDestroyRequest::class)->makePartial();
//    $request->shouldReceive('expectsJson')->andReturn(true);
//    $request->initiator_id = 'initiator-123';
//
//    $action = new DeleteMediumAction();
//    $response = $action->execute($request, $media);
//
//    expect($response)->toBeInstanceOf(JsonResponse::class);
//    expect($response->getData(true))->toMatchArray([
//        'initiatorId' => 'initiator-123',
//        'type' => 'success',
//        'message' => __('media-library-extensions::messages.medium_removed'),
//    ]);
// });
//
// it('deletes the media and returns a redirect response when request does NOT expect JSON', function () {
//    $media = Mockery::mock(Media::class);
//    $media->shouldReceive('delete')->once();
//
//    $request = Mockery::mock(MediaManagerDestroyRequest::class)->makePartial();
//    $request->shouldReceive('expectsJson')->andReturn(false);
//    $request->initiator_id = 'initiator-456';
//
//    $action = new DeleteMediumAction();
//    $response = $action->execute($request, $media);
//
//    expect($response)->toBeInstanceOf(RedirectResponse::class);
//
//    $flashData = $response->getSession()->get('laravel-medialibrary-extensions.status');
//
//    expect($flashData)->not()->toBeNull();
//
//    expect($flashData)->toMatchArray([
//        'initiatorId' => 'initiator-456',
//        'type' => 'success',
//        'message' => __('media-library-extensions::messages.medium_removed'),
//
//    ]);
//
// });
