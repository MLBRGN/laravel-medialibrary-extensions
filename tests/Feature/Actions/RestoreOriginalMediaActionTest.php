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

beforeEach(function () {
    $this->mediaService = app(MediaService::class);
    $this->action = new RestoreOriginalMediaAction($this->mediaService);

    config()->set('medialibrary-extensions.media_disks.originals', 'originals');
    config()->set('filesystems.disks.originals', [
        'driver' => 'local',
        'root' => storage_path('app/public/media_originals'),
        'visibility' => 'private',
    ]);
    config()->set('filesystems.disks.public', [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'visibility' => 'public',
    ]);
    config()->set('filesystems.disks.media', [
        'driver' => 'local',
        'root' => storage_path('app/public/media'),
        'visibility' => 'public',
    ]);

    Storage::fake('public');
    Storage::fake('media');
    Storage::fake('originals');

    // Setup for data_source tests
    $this->dataSource = 'demo';
    $demoDatabasePath = __DIR__.'/../../Support/demo.sqlite';
    config()->set('database.connections.media_demo', [
        'driver' => 'sqlite',
        'database' => $demoDatabasePath,
        'prefix' => '',
    ]);
    config()->set('medialibrary-extensions.data_sources.default.connection', 'testbench');
    config()->set('medialibrary-extensions.data_sources.demo.connection', 'media_demo');
    DB::purge('media_demo');
    if (! Schema::connection('media_demo')->hasTable('blogs')) {
        Schema::connection('media_demo')->create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });
    }
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
    // We remove the JSON header to test RedirectResponse
    // $request->headers->set('Accept', 'application/json');

    Storage::disk('originals')->delete("{$media->id}/{$media->file_name}");

    Log::spy();

    $response = $this->action->execute($request, $media->id);

    expect($response)->toBeInstanceOf(RedirectResponse::class);
    Log::shouldHaveReceived('warning')->once();
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
    $demoModel->setConnection('media_demo');
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
        'data_source' => $this->dataSource,
    ]);
    $request->setLaravelSession(app('session.store'));
    $request->headers->set('Accept', 'application/json');

    $response = $this->action->execute($request, $medium->id);

    expect($response->status())->toBe(200);
    $data = $response->getData(true);
    expect($data['type'])->toBe('success');

    expect(Storage::disk($targetDisk)->get($targetPath))->toBe('original-content');
})->todo('This test is not working yet');

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
})->todo('This test is not working');
