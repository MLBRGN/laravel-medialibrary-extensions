<?php

use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

beforeEach(function () {
    $this->mediaService = Mockery::mock(Mlbrgn\MediaLibraryExtensions\Services\MediaService::class);
    $this->action = new Mlbrgn\MediaLibraryExtensions\Actions\GetMediaPreviewerPermanentHTMLAction($this->mediaService);
});

it('renders media preview HTML and returns JSON response', function () {
    //    $model = Blog::create(['title' => 'Test title']);
    $model = $this->getTestBlogModel();
    $initiatorId = 'initiator-789';

    $requestData = [
        'initiator_id' => $initiatorId,
        'model_type' => get_class($model),
        'model_id' => $model->id,
        //        'image_collection' => 'images',// TODO
        //        'document_collection' => 'docs',
        //        'youtube_collection' => 'youtube',
        'frontend_theme' => 'bootstrap-5',
        'show_destroy_button' => true,
        'show_set_as_first_button' => false,
        'show_order' => false,
    ];

    $request = Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaPreviewerHTMLRequest::create('/dummy', 'GET', $requestData);

    // Return the Dummy model instance
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
