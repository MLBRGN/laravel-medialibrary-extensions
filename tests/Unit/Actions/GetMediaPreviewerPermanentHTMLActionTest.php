<?php

use Illuminate\Http\JsonResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;
use Mlbrgn\MediaLibraryExtensions\View\Components\Preview\MediaPreviewGrid;
use Mlbrgn\MediaLibraryExtensions\View\Components\Preview\MediaPreviews;

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

    $request = Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerPreviewerHTMLRequest::create('/dummy', 'GET', $requestData);

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

it('renders permanent media preview HTML and returns JSON response', function () {
    $model = $this->getTestBlogModel();
    $initiatorId = 'initiator-123';
    $frontendTheme = 'bootstrap-5';

    $requestData = [
        'initiator_id' => $initiatorId,
        'model_type' => $model->getMorphClass(),
        'model_id' => $model->getKey(),
        'collections' => json_encode([
            'image' => 'images',
            'document' => 'documents',
            'youtube' => 'youtube',
        ]),
        'options' => json_encode([
            'frontendTheme' => 'bootstrap-5',
            'showDestroyButton' => true,
            'showSetAsFirstButton' => false,
            'showOrder' => false,
        ]),
    ];

    $request = GetMediaManagerPreviewerHTMLRequest::create('/dummy', 'GET', $requestData);

    // mock the MediaService behavior
    $this->mediaService
        ->shouldReceive('resolveModel')
        ->once()
        ->with($model->getMorphClass(), $model->getKey())
        ->andReturn($model);

    Blade::shouldReceive('renderComponent')
        ->once()
        ->withArgs(function (MediaPreviews $component) use ($initiatorId, $requestData, $frontendTheme, $model) {
            expect($component->id)->toBe($initiatorId);
            expect($component->modelOrClassName)->toBe($model);
            expect($component->getConfig('frontendTheme'))->toBe($frontendTheme);
            expect($component->getConfig('showDestroyButton'))->toBeTrue();
            expect($component->getConfig('showSetAsFirstButton'))->toBeFalse();
            expect($component->getConfig('showOrder'))->toBeFalse();
            expect($component->getConfig('temporaryUploadMode'))->toBeFalse();

            return true;
        })

        ->andReturn('<div>Rendered Temporary Media Preview</div>');

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    $data = $response->getData(true);

    expect($data)->toMatchArray([
        'html' => '<div>Rendered Temporary Media Preview</div>',
        'success' => true,
        'target' => $initiatorId,
    ]);
});
