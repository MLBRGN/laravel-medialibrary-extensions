<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Actions\RestoreOriginalMediumAction;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\RestoreOriginalMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Listeners\MediaHasBeenAddedListener;


beforeEach(function () {
    $this->action = new RestoreOriginalMediumAction();

    config()->set('media-library-extensions.media_disks.originals', 'originals');
    config()->set('filesystems.disks', [
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => '/storage',
            'visibility' => 'public',
        ],
        'media' => [
            'driver' => 'local',
            'root' => storage_path('app/public/media'),
            'visibility' => 'public',
        ],
        'originals' => [
            'driver' => 'local',
//            'root' => storage_path('app/originals'),
            'root' => storage_path('app/public/media_originals'),
            'visibility' => 'private',
        ],
    ]);

    Storage::fake('public');
    Storage::fake('media');
    Storage::fake('originals');
});

it('returns error if original file not found', function () {
    Event::fake([MediaHasBeenAddedListener::class]);

    $request = new RestoreOriginalMediumRequest();
    $media = $this->getMedia('test.jpg');

    Storage::disk('originals')->delete($media->getPathRelativeToRoot());
    Storage::disk('originals')->assertMissing($media->getPathRelativeToRoot());

    Log::spy();

    $response = $this->action->execute($request, $media);

    expect($response)->toBeInstanceOf(RedirectResponse::class);
    Log::shouldHaveReceived('warning')->once();
});

it('restores the original file successfully', function () {
    $request = new RestoreOriginalMediumRequest();
    $request->setLaravelSession(app('session.store'));
    $request->headers->set('Accept', 'application/json');

    $media = $this->getMedia('test.jpg');

//    dd($media->disk);
    $originalPath = $media->getPathRelativeToRoot();
    Storage::disk('originals')->put($originalPath, 'original-content');

    $response = $this->action->execute($request, $media);

    expect($response)->toBeInstanceOf(JsonResponse::class);

    $data = $response->getData(true);

    expect($data)->toMatchArray([
        'initiatorId' => '',
        'type' => 'success',
        'message' => 'Restored original.',
    ]);

    expect(Storage::disk('public')->exists($originalPath))->toBeTrue();
//    expect(Storage::disk('media')->get($originalPath))->toBe('original-content');
});

it('falls back to media disk if target disk not configured', function () {
    $request = new RestoreOriginalMediumRequest();
    $media = $this->getMedia('test.jpg');
    $media->disk = 'nonexistent';
    $media->save();

    $relativePath = $media->getPathRelativeToRoot();
    Storage::disk('originals')->put($relativePath, 'test-content');

    Log::spy();
    MediaResponse::shouldReceive('success')
        ->once()
        ->andReturn(mock(JsonResponse::class));

    $response = $this->action->execute($request, $media);

    expect(Storage::disk('media')->exists($relativePath))->toBeTrue();
    expect($response)->toBeInstanceOf(JsonResponse::class);
    Log::shouldHaveReceived('warning')
        ->with(Mockery::pattern('/Disk \[nonexistent\]/'))
        ->once();
})->todo();

it('returns error response on exception', function () {
    $request = new RestoreOriginalMediumRequest();
    $media = $this->getMedia('test.jpg');

    // Force a failure by making the file path unreadable
    $media->shouldReceive('getPathRelativeToRoot')
        ->andThrow(new Exception('Simulated failure'));

    Storage::disk('originals')->put('3/fail.jpg', 'irrelevant');

    Log::spy();
    MediaResponse::shouldReceive('error')
        ->once()
        ->andReturn(mock(JsonResponse::class));

    $response = $this->action->execute($request, $media);

    expect($response)->toBeInstanceOf(JsonResponse::class);
    Log::shouldHaveReceived('error')
        ->with(Mockery::pattern('/Simulated failure/'))
        ->once();
})->todo();
