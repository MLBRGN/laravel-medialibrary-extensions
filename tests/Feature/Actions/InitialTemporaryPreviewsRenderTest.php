<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Blade;
use Mlbrgn\MediaLibraryExtensions\Actions\GetMediaPreviewerTemporaryHTMLAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\GetMediaManagerPreviewerHTMLRequest;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;

beforeEach(function () {
    $this->mediaService = app(MediaService::class);
    $this->action = new GetMediaPreviewerTemporaryHTMLAction($this->mediaService);
});

it('finds temporary uploads on initial render using cookie client_token and derived instance_id', function () {
    $model = $this->getTestBlogModel();

    $baseId = 'base-initial-123';
    $instanceId = InstanceManager::getInstanceId($baseId);
    $clientToken = 'client-token-initial-xyz';

    // Seed two temporary uploads across collections for this instance and client
    foreach ([
        ['collection' => 'images', 'name' => 'photo-a.jpg'],
        ['collection' => 'documents', 'name' => 'doc-a.pdf'],
    ] as $i => $seed) {
        TemporaryUpload::query()->create([
            'disk' => config('medialibrary-extensions.media_disks.temporary'),
            'path' => $seed['name'],
            'name' => $seed['name'],
            'file_name' => $seed['name'],
            'collection_name' => $seed['collection'],
            'mime_type' => $seed['collection'] === 'images' ? 'image/jpeg' : 'application/pdf',
            'size' => 123 + $i,
            'user_id' => null,
            'client_token' => $clientToken,
            'instance_id' => $instanceId,
            'order_column' => $i,
            'custom_properties' => [
                'collections' => [
                    'image' => 'images',
                    'document' => 'documents',
                    'youtube' => 'youtube',
                ],
                'priority' => $i,
            ],
        ]);
    }

    $requestData = [
        'base_id' => $baseId,
        'data_source' => 'default',
        'model_type' => $model->getMorphClass(),
        'collections' => json_encode([
            'image' => 'images',
            'document' => 'documents',
            'youtube' => 'youtube',
        ]),
        'options' => json_encode([
            'theme' => 'bootstrap-5',
        ]),
        'temporary_upload_mode' => 'true',
        'selectable' => 'false',
        'multiple' => 'true',
        'disabled' => 'false',
        'readonly' => 'false',
        'include_debug' => 'false',
    ];

    // Pass client token via cookie (simulating initial render after page load)
    $cookies = ['mle_client_token' => $clientToken];
    $request = GetMediaManagerPreviewerHTMLRequest::create('/dummy', 'GET', $requestData, $cookies);

    // Do not assert on Blade output HTML structure here; we focus on mediaCount and identifiers.
    Blade::shouldReceive('renderComponent')->andReturn('<div>previews</div>');

    $response = $this->action->execute($request);

    expect($response)->toBeInstanceOf(JsonResponse::class);
    $data = $response->getData(true);

    expect($data['success'])->toBeTrue();
    expect($data['mediaCount'])->toBe(2);
    expect($data['instanceId'])->toBe($instanceId);
    expect($data['target'])->toBe($baseId);
});

it('returns 403 when no client token is provided for initial render', function () {
    $model = $this->getTestBlogModel();
    $baseId = 'base-missing-token-1';

    $requestData = [
        'base_id' => $baseId,
        'data_source' => 'default',
        'model_type' => $model->getMorphClass(),
        'collections' => json_encode([
            'image' => 'images',
        ]),
        'options' => json_encode([
            'theme' => 'bootstrap-5',
        ]),
        'temporary_upload_mode' => 'true',
        'selectable' => 'false',
        'multiple' => 'true',
        'disabled' => 'false',
        'readonly' => 'false',
        'include_debug' => 'false',
    ];

    $request = GetMediaManagerPreviewerHTMLRequest::create('/dummy', 'GET', $requestData);

    Blade::shouldReceive('renderComponent')->never();

    $response = $this->action->execute($request);

    expect($response->getStatusCode())->toBe(403);
    $data = $response->getData(true);
    expect($data['success'])->toBeFalse();
    expect($data['mediaCount'])->toBe(0);
});
