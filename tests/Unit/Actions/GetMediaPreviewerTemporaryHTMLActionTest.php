<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Blade;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaPreviewerTemporaryHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\View\Components\MediaManagerPreview;

beforeEach(function () {
    $this->mediaService = Mockery::mock(MediaService::class);
    $this->action = new GetMediaPreviewerTemporaryHTMLAction($this->mediaService);
});

it('renders temporary media preview HTML and returns JSON response', function () {
    $model = $this->getTestBlogModel();
    $initiatorId = 'initiator-123';

    $requestData = [
        'initiator_id' => $initiatorId,
        'model_type' => $model->getMorphClass(),
        'image_collection' => 'images',
        'document_collection' => 'docs',
        'youtube_collection' => 'youtube',
        'frontend_theme' => 'bootstrap-5',
        'show_destroy_button' => 'true',
        'show_set_as_first_button' => 'false',
        'show_order' => 'false',
    ];

    $request = GetMediaPreviewerHTMLRequest::create('/dummy', 'GET', $requestData);

    Blade::shouldReceive('renderComponent')
        ->once()
        ->withArgs(function (MediaManagerPreview $component) use ($initiatorId, $requestData) {
            expect($component->id)->toBe($initiatorId);
            expect($component->modelOrClassName)->toBe($requestData['model_type']);
            expect($component->imageCollection)->toBe($requestData['image_collection']);
            expect($component->documentCollection)->toBe($requestData['document_collection']);
            expect($component->youtubeCollection)->toBe($requestData['youtube_collection']);
            expect($component->frontendTheme)->toBe($requestData['frontend_theme']);
            expect($component->showDestroyButton)->toBeTrue();
            expect($component->showSetAsFirstButton)->toBeFalse();
            expect($component->showOrder)->toBeFalse();
            expect($component->temporaryUpload)->toBeTrue();

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
