<?php

use Illuminate\Http\JsonResponse;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeVideoAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeVideoPermanentAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeVideoTemporaryAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreYouTubeVideoRequest;

beforeEach(function () {
    $this->permanentAction = Mockery::mock(StoreYouTubeVideoPermanentAction::class);
    $this->temporaryAction = Mockery::mock(StoreYouTubeVideoTemporaryAction::class);

    $this->action = new StoreYouTubeVideoAction(
        $this->permanentAction, // first param: permanent
        $this->temporaryAction  // second param: temporary
    );
});

it('delegates to temporary action when temporary_upload is true', function () {
    $request = Mockery::mock(StoreYouTubeVideoRequest::class);
    $request->shouldReceive('boolean')
        ->with('temporary_upload')
        ->andReturn(true);

    $expectedResponse = Mockery::mock(JsonResponse::class);

    $this->temporaryAction
        ->shouldReceive('execute')
        ->once()
        ->with($request)
        ->andReturn($expectedResponse);

    $result = $this->action->execute($request);

    expect($result)->toBe($expectedResponse);
});

it('delegates to permanent action when temporary_upload is false', function () {
    $request = Mockery::mock(StoreYouTubeVideoRequest::class);
    $request->shouldReceive('boolean')
        ->with('temporary_upload')
        ->andReturn(false);

    $expectedResponse = Mockery::mock(JsonResponse::class);

    $this->permanentAction
        ->shouldReceive('execute')
        ->once()
        ->with($request)
        ->andReturn($expectedResponse);

    $result = $this->action->execute($request);

    expect($result)->toBe($expectedResponse);
});
