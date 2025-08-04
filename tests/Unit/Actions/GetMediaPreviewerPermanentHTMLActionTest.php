<?php

use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

beforeEach(function () {
    $this->mediaService = Mockery::mock(Mlbrgn\MediaLibraryExtensions\Services\MediaService::class);
    $this->action = new Mlbrgn\MediaLibraryExtensions\Actions\GetMediaPreviewerPermanentHTMLAction($this->mediaService);
});

it('renders media preview HTML and returns JSON response', function () {
    $model = Blog::create(['title' => 'Test title']);
    $initiatorId = 'initiator-789';

    $requestData = [
        'initiator_id' => $initiatorId,
        'model_type' => get_class($model),
        'model_id' => $model->id,
        'image_collection' => 'images',
        'document_collection' => 'docs',
        'youtube_collection' => 'youtube',
        'frontend_theme' => 'bootstrap-5',
        'destroy_enabled' => true,
        'set_as_first_enabled' => false,
        'show_media_url' => true,
        'show_order' => false,
    ];

    $request = Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaPreviewerHTMLRequest::create('/dummy', 'GET', $requestData);

    // Return the dummy model instance
    $this->mediaService
        ->shouldReceive('resolveModel')
        ->once()
        ->with($requestData['model_type'], $requestData['model_id'])
        ->andReturn($model);

    Blade::shouldReceive('renderComponent')
        ->once()
        ->andReturn('<div>Rendered Media Preview</div>');

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(Illuminate\Http\JsonResponse::class);
    $data = $response->getData(true);

    expect($data)->toMatchArray([
        'html' => '<div>Rendered Media Preview</div>',
        'success' => true,
        'target' => $initiatorId,
    ]);
});

