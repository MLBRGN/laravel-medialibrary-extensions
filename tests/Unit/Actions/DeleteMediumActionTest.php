<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit\View\Components;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Actions\DeleteMediumAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerDestroyRequest;
use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;
use Mockery;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Lang;
use function PHPUnit\Framework\assertArrayIsEqualToArrayOnlyConsideringListOfKeys;

beforeEach(function () {
    // Mock the translation string used in your action
//    Lang::shouldReceive('__')
//        ->with('media-library-extensions::messages.medium_removed')
//        ->andReturn('Medium has been removed');
});

it('deletes the media and returns a success JSON response when request expects JSON', function () {
    // Create a fake Media model mock
    $media = Mockery::mock(Media::class);
    $media->shouldReceive('delete')->once();

    // Create a request mock that expects JSON and has initiator_id
    $request = Mockery::mock(MediaManagerDestroyRequest::class)->makePartial();
    $request->shouldReceive('expectsJson')->andReturn(true);
    $request->initiator_id = 'initiator-123';

    $action = new DeleteMediumAction();
    $response = $action->execute($request, $media);

    expect($response)->toBeInstanceOf(JsonResponse::class);
    expect($response->getData(true))->toMatchArray([
        'initiatorId' => 'initiator-123',
        'type' => 'success',
        'message' => __('media-library-extensions::messages.medium_removed'),
    ]);
});

it('deletes the media and returns a redirect response when request does NOT expect JSON', function () {
    $media = Mockery::mock(Media::class);
    $media->shouldReceive('delete')->once();

    $request = Mockery::mock(MediaManagerDestroyRequest::class)->makePartial();
    $request->shouldReceive('expectsJson')->andReturn(false);
    $request->initiator_id = 'initiator-456';

    $action = new DeleteMediumAction();
    $response = $action->execute($request, $media);

    expect($response)->toBeInstanceOf(RedirectResponse::class);

    $flashData = $response->getSession()->get('laravel-medialibrary-extensions.status');

    expect($flashData)->not()->toBeNull();

    expect($flashData)->toMatchArray([
        'initiatorId' => 'initiator-456',
        'type' => 'success',
        'message' => __('media-library-extensions::messages.medium_removed'),

    ]);

});
