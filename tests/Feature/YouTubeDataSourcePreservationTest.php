<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeVideoPermanentAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeVideoTemporaryAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreYouTubeVideoRequest;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

beforeEach(function () {
    Config::set('medialibrary-extensions.youtube_support_enabled', true);
    Config::set('medialibrary-extensions.data_sources.demo', [
        'connection' => 'media_demo',
    ]);

    // Ensure media_demo has the same tables as testbench
    Artisan::call('migrate:fresh', [
        '--database' => 'media_demo',
        '--path' => [
            'database/migrations',
            'tests/Database/Migrations',
        ],
        '--realpath' => true,
    ]);
});

it('stores a youtube video temporary on a custom data source', function () {
    $dataSource = 'demo';
    $sessionId = 'test-session';

    // Verify we are starting clean on media_demo
    expect(DB::connection('media_demo')->table('mle_temporary_uploads')->count())->toBe(0);

    $request = StoreYouTubeVideoRequest::create('/upload-youtube', 'POST', [
        'temporary_upload_mode' => 'true',
        'initiator_id' => 'test-initiator',
        'media_manager_id' => 'test-manager',
        'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'collections' => ['video' => 'videos'],
        'youtube_collection' => 'videos',
        'data_source' => $dataSource,
        'multiple' => 'true',
    ]);
    $request->setLaravelSession(app('session.store'));
    $request->session()->setId($sessionId);
    $request->headers->set('Accept', 'application/json');

    // Manually normalize data_source
    if ($request->input('data_source') === 'null') {
        $request->merge(['data_source' => null]);
    }

    $action = app(StoreYouTubeVideoTemporaryAction::class);
    $response = $action->execute($request);

    expect($response->status())->toBe(200);

    // Verify it was stored in the demo database
    $count = DB::connection('media_demo')->table('mle_temporary_uploads')->count();
    expect($count)->toBe(1);

    // Verify it was NOT stored in the default database
    expect(DB::connection('testbench')->table('mle_temporary_uploads')->count())->toBe(0);

    $uploaded = DB::connection('media_demo')->table('mle_temporary_uploads')->first();
    expect($uploaded->collection_name)->toBe('videos');
});

it('stores a youtube video permanent on a custom data source', function () {
    $dataSource = 'demo';

    // Create a model in the demo database
    $model = Blog::on('media_demo')->create(['title' => 'Demo Blog']);
    $modelId = $model->id;

    // Verify we are starting clean on media_demo media table
    expect(DB::connection('media_demo')->table('media')->count())->toBe(0);

    $request = StoreYouTubeVideoRequest::create('/upload-youtube', 'POST', [
        'temporary_upload_mode' => 'false',
        'initiator_id' => 'test-initiator',
        'media_manager_id' => 'test-manager',
        'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'collections' => ['video' => 'videos'],
        'youtube_collection' => 'videos',
        'data_source' => $dataSource,
        'model_type' => get_class($model),
        'model_id' => $modelId,
        'multiple' => 'true',
    ]);
    $request->setLaravelSession(app('session.store'));
    $request->headers->set('Accept', 'application/json');

    // Manually normalize data_source
    if ($request->input('data_source') === 'null') {
        $request->merge(['data_source' => null]);
    }

    $action = app(StoreYouTubeVideoPermanentAction::class);
    $response = $action->execute($request);

    expect($response->status())->toBe(200);

    // Verify it was stored in the demo database
    $count = DB::connection('media_demo')->table('media')->count();
    expect($count)->toBe(1);

    // Verify it was NOT stored in the default database
    expect(DB::connection('testbench')->table('media')->count())->toBe(0);
});
