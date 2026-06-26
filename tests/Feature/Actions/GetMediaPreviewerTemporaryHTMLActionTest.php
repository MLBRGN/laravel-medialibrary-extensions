<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Blade;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaPreviewerTemporaryHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\View\Components\Preview\MediaPreviews;

beforeEach(function () {
    $this->mediaService = app(MediaService::class);
    $this->action = new GetMediaPreviewerTemporaryHTMLAction($this->mediaService);
});

it('renders temporary media preview HTML and returns JSON response', function () {
    $model = $this->getTestBlogModel();
    $baseId = 'initiator-123';
    $frontendTheme = 'bootstrap-5';

    $requestData = [
        'base_id' => $baseId,
        'instance_id' => 'test-instance',
        'client_token' => 'test-session',
        'data_source' => 'default',
        'model_type' => $model->getMorphClass(),
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

    Blade::shouldReceive('renderComponent')
        ->once()
        ->withArgs(function (MediaPreviews $component) use ($baseId, $requestData, $frontendTheme) {
            expect($component->id)->toBe($baseId);
            expect($component->modelOrClassName)->toBe($requestData['model_type']);
            expect($component->getConfig('frontendTheme'))->toBe($frontendTheme);
            expect($component->getConfig('showDestroyButton'))->toBeTrue();
            expect($component->getConfig('showSetAsFirstButton'))->toBeFalse();
            expect($component->getConfig('showOrder'))->toBeFalse();
            expect($component->getConfig('temporaryUploadMode'))->toBeTrue();

            return true;
        })
        ->andReturn('<div>Rendered Temporary Media Preview</div>');

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    $data = $response->getData(true);

    expect($data)->toMatchArray([
        'html' => '<div>Rendered Temporary Media Preview</div>',
        'success' => true,
        'target' => $baseId,
    ]);
});
