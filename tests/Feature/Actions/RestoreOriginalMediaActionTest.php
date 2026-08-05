<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Actions\RestoreOriginalMediaAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\RestoreOriginalMediumRequest;
use Mlbrgn\MediaLibraryExtensions\Listeners\MediaHasBeenAddedListener;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Support\PackageInfrastructure;

beforeEach(function () {
    $this->mediaService = app(MediaService::class);
    $this->action = app(RestoreOriginalMediaAction::class);

    Storage::fake('public');
    Storage::fake('media');
    Storage::fake('originals');
});

it('returns error if media not found', function () {
    $request = RestoreOriginalMediumRequest::create('/restore', 'POST', [
        'data_source' => 'default',
    ]);
    $request->setLaravelSession(app('session.store'));
    $request->headers->set('Accept', 'application/json');

    $response = $this->action->execute($request, 9999);

    expect($response)->toBeInstanceOf(JsonResponse::class);
    $data = $response->getData(true);
    expect($data['type'])->toBe('error');
    expect($data['message'])->toBe(__('medialibrary-extensions::messages.medium_not_found'));
});

it('returns error if original file not found', function () {
    Event::fake([MediaHasBeenAddedListener::class]);

    $media = $this->getMedia('test.jpg');
    $request = RestoreOriginalMediumRequest::create('/restore', 'POST', [
        'data_source' => 'default',
    ]);
    $request->setLaravelSession(app('session.store'));
    $request->headers->set('Accept', 'application/json');

    $originalsDisk = config('medialibrary-extensions.media_disks.originals');
    $originalPath = "{$media->id}/{$media->file_name}";
    // Ensure the helper created the original, then remove it to simulate missing original
    expect(Storage::disk($originalsDisk)->exists($originalPath))->toBeTrue();
    Storage::disk($originalsDisk)->delete($originalPath);
    expect(Storage::disk($originalsDisk)->exists($originalPath))->toBeFalse();

    $response = $this->action->execute($request, $media->id);

    expect($response)->toBeInstanceOf(JsonResponse::class);
    $data = $response->getData(true);
    expect($data['type'])->toBe('error');
    expect($data['message'])->toBe(__('medialibrary-extensions::messages.no_original_saved'));
});

it('restores the original file successfully', function () {
    $media = $this->getMedia('test.jpg');
    $request = RestoreOriginalMediumRequest::create('/restore', 'POST', [
        'data_source' => 'default',
    ]);
    $request->setLaravelSession(app('session.store'));
    $request->headers->set('Accept', 'application/json');

    $originalPath = "{$media->id}/{$media->file_name}";
    Storage::disk('originals')->put($originalPath, 'original-content');

    $response = $this->action->execute($request, $media->id);

    expect($response)->toBeInstanceOf(JsonResponse::class);
    $data = $response->getData(true);
    expect($data['type'])->toBe('success');

    expect(Storage::disk('public')->exists($originalPath))->toBeTrue();
});

it('restores original media from a custom data source', function () {
    $model = $this->getTestBlogModel();
    $demoModel = $model->replicate();
    $altConnection = PackageInfrastructure::connection('test', 'alt');
    $demoModel->setConnection($altConnection);
    $demoModel->save();

    $file = UploadedFile::fake()->image('original.jpg');
    $medium = $demoModel->addMedia($file)->toMediaCollection('images');

    $originalsDisk = config('medialibrary-extensions.media_disks.originals');
    $originalPath = "{$medium->id}/{$medium->file_name}";
    Storage::disk($originalsDisk)->put($originalPath, 'original-content');

    $targetDisk = $medium->disk;
    $targetPath = $medium->getPathRelativeToRoot();
    Storage::disk($targetDisk)->put($targetPath, 'modified content');

    $request = RestoreOriginalMediumRequest::create('/restore', 'POST', [
        'data_source' => 'test_alt',
    ]);
    $request->setLaravelSession(app('session.store'));
    $request->headers->set('Accept', 'application/json');

    $response = $this->action->execute($request, $medium->id);

    expect($response->status())->toBe(200);
    $data = $response->getData(true);
    expect($data['type'])->toBe('success');

    expect(Storage::disk($targetDisk)->get($targetPath))->toBe('original-content');
});

it('falls back to media disk if target disk not configured', function () {
    $media = $this->getMedia('test.jpg');
    $media->disk = 'nonexistent';
    $media->save();

    $originalPath = "{$media->id}/{$media->file_name}";
    Storage::disk('originals')->put($originalPath, 'test-content');

    $request = RestoreOriginalMediumRequest::create('/restore', 'POST', [
        'data_source' => 'default',
    ]);
    $request->setLaravelSession(app('session.store'));
    $request->headers->set('Accept', 'application/json');

    Log::spy();
    $response = $this->action->execute($request, $media->id);

    $targetPath = $media->getPathRelativeToRoot();
    expect(Storage::disk('media')->exists($targetPath))->toBeTrue();
    expect($response)->toBeInstanceOf(JsonResponse::class);
    Log::shouldHaveReceived('warning')
        ->with(Mockery::pattern('/Disk \[nonexistent\]/'))
        ->once();
});

//    ->todo('This test is not working');
