<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreMultipleMediaAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreMultiplePermanentAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreMultipleTemporaryAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadMultipleRequest;

beforeEach(function () {
    $this->temporaryAction = Mockery::mock(StoreMultipleTemporaryAction::class);
    $this->permanentAction = Mockery::mock(StoreMultiplePermanentAction::class);
    $this->action = new StoreMultipleMediaAction(
        $this->permanentAction,
        $this->temporaryAction
    );
});

it('delegates to temporary action when temporary_upload is true', function () {
    $request = Mockery::mock(MediaManagerUploadMultipleRequest::class);

    // Simulate Laravel's __get() handling for "temporary_upload"
    $request->shouldReceive('all')->andReturn(['temporary_upload' => 'true']);

    $expectedResponse = Mockery::mock(JsonResponse::class);
    $this->temporaryAction
        ->shouldReceive('execute')
        ->once()
        ->with($request)
        ->andReturn($expectedResponse);

    $result = $this->action->execute($request);

    expect($result)->toBe($expectedResponse);
});
it('delegates to permanent action when temporary_upload is not true', function () {
    $request = Mockery::mock(MediaManagerUploadMultipleRequest::class);
    $request->shouldReceive('all')->andReturn(['temporary_upload' => 'false']);

    $expectedResponse = Mockery::mock(RedirectResponse::class);
    $this->permanentAction
        ->shouldReceive('execute')
        ->once()
        ->with($request)
        ->andReturn($expectedResponse);

    $result = $this->action->execute($request);

    expect($result)->toBe($expectedResponse);
});
