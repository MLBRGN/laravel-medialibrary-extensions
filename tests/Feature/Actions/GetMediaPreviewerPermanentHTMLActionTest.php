<?php

use Illuminate\Http\JsonResponse;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaPreviewerPermanentHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

beforeEach(function () {
    $this->mediaService = app(MediaService::class);
    $this->action = new GetMediaPreviewerPermanentHTMLAction($this->mediaService);
});

it('renders media preview HTML (without media) and returns JSON response', function () {
    //    $model = Blog::create(['title' => 'Test title']);
    $model = $this->getTestBlogModel();

    $baseId = 'initiator-789';

    $requestData = [
        'base_id' => $baseId,
        'model_type' => get_class($model),
        'model_id' => $model->id,
        'frontend_theme' => 'bootstrap-5',
        'show_destroy_button' => true,
        'show_set_as_first_button' => false,
        'show_order' => false,
    ];

    $request = GetMediaManagerPreviewerHTMLRequest::create('/dummy', 'GET', $requestData);

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class);
    $data = $response->getData(true);

    expect($data)->toMatchArray([
        'mediaCount' => 0,
        'success' => true,
        'target' => $baseId,
    ]);

    expect($data['html'])->toContain('mle-no-media');
});

it('renders permanent media preview HTML and returns JSON response', function () {
    $model = $this->getTestBlogModel();
    $baseId = 'initiator-123';
    $theme = 'bootstrap-5';

    $requestData = [
        'base_id' => $baseId,
        'model_type' => $model->getMorphClass(),
        'model_id' => $model->getKey(),
        'collections' => json_encode([
            'image' => 'images',
            'document' => 'documents',
            'youtube' => 'youtube',
        ]),
        'options' => json_encode([
            'theme' => $theme,
            'showDestroyButton' => true,
            'showSetAsFirstButton' => false,
            'showOrder' => false,
        ]),
    ];

    $request = GetMediaManagerPreviewerHTMLRequest::create('/dummy', 'GET', $requestData);

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    $data = $response->getData(true);

    expect($data)->toMatchArray([
        'mediaCount' => 0,
        'success' => true,
        'target' => $baseId,
    ]);
});
