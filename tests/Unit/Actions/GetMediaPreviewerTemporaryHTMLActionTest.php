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
    $initiatorId = 'initiator-123';

    $requestData = [
        'initiator_id' => $initiatorId,
        'model_type' => 'SomeModelClass',
        'image_collection' => 'images',
        'document_collection' => 'docs',
        'youtube_collection' => 'youtube',
        'frontend_theme' => 'bootstrap-5',
        'destroy_enabled' => true,
        'set_as_first_enabled' => false,
        'show_media_url' => true,
        'show_order' => false,
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
            expect($component->destroyEnabled)->toBe($requestData['destroy_enabled']);
            expect($component->setAsFirstEnabled)->toBe($requestData['set_as_first_enabled']);
            expect($component->showMediaUrl)->toBe($requestData['show_media_url']);
            expect($component->showOrder)->toBe($requestData['show_order']);
            expect($component->temporaryUploads)->toBeTrue();

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
})->todo();
