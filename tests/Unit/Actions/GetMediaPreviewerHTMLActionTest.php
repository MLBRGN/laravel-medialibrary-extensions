<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaPreviewerHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaPreviewerPermanentHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaPreviewerTemporaryHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaPreviewerHTMLRequest;

beforeEach(function () {
    $this->permanentAction = Mockery::mock(GetMediaPreviewerPermanentHTMLAction::class);
    $this->temporaryAction = Mockery::mock(GetMediaPreviewerTemporaryHTMLAction::class);

    $this->action = new GetMediaPreviewerHTMLAction(
        $this->permanentAction,
        $this->temporaryAction
    );
});

it('calls the temporary previewer action when temporary_upload_mode is true', function () {
    $request = GetMediaPreviewerHTMLRequest::create('/dummy-url', 'GET', [
        'temporary_upload_mode' => 'true',
    ]);

    $expectedResponse = new JsonResponse(['html' => '<div>temp preview</div>']);

    $this->temporaryAction
        ->shouldReceive('execute')
        ->once()
        ->with($request)
        ->andReturn($expectedResponse);

    $this->permanentAction
        ->shouldNotReceive('execute');

    $response = $this->action->execute($request);

    expect($response)->toBe($expectedResponse);
});

it('calls the permanent previewer action when temporary_upload_mode is not true', function () {
    $request = GetMediaPreviewerHTMLRequest::create('/dummy-url', 'GET', [
        'temporary_upload_mode' => 'false',
    ]);

    $expectedResponse = new Response('<div>permanent preview</div>');

    $this->permanentAction
        ->shouldReceive('execute')
        ->once()
        ->with($request)
        ->andReturn($expectedResponse);

    $this->temporaryAction
        ->shouldNotReceive('execute');

    $response = $this->action->execute($request);

    expect($response)->toBe($expectedResponse);
});

it('calls the permanent previewer action when temporary_upload_mode is absent', function () {
    $request = GetMediaPreviewerHTMLRequest::create('/dummy-url', 'GET');

    $expectedResponse = new JsonResponse(['html' => '<div>permanent default</div>']);

    $this->permanentAction
        ->shouldReceive('execute')
        ->once()
        ->with($request)
        ->andReturn($expectedResponse);

    $this->temporaryAction
        ->shouldNotReceive('execute');

    $response = $this->action->execute($request);

    expect($response)->toBe($expectedResponse);
});
