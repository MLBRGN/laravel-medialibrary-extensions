<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreSingleMediumAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreSinglePermanentAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreSingleTemporaryAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\MediaManagerUploadSingleRequest;

beforeEach(function () {
    $this->temporaryAction = Mockery::mock(StoreSingleTemporaryAction::class);
    $this->permanentAction = Mockery::mock(StoreSinglePermanentAction::class);
    $this->action = new StoreSingleMediumAction(
        $this->permanentAction,
        $this->temporaryAction
    );
});

it('delegates to temporary action when temporary_upload is true', function () {
    $request = Mockery::mock(MediaManagerUploadSingleRequest::class);

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
    $request = Mockery::mock(MediaManagerUploadSingleRequest::class);
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
