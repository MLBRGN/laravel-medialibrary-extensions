<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeVideoPermanentAction;
use Mlbrgn\MediaLibraryExtensions\Actions\StoreYouTubeVideoTemporaryAction;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\StoreYouTubeVideoRequest;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

it('stores a youtube video temporary on a custom data source', function () {
    $dataSource = 'demo';
    $clientToken = 'test-session';

    $testDemoConnection = 'mle_test_demo';
    $testDemoHostConnection = 'mle_test_host_app';

    // Verify we are starting clean on media_demo
    expect(DB::connection($testDemoConnection)->table('mle_temporary_uploads')->count())->toBe(0);

    $request = StoreYouTubeVideoRequest::create('/upload-youtube', 'POST', [
        'temporary_upload_mode' => 'true',
        'base_id' => 'test-base',
        'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'collections' => ['video' => 'videos'],
        'youtube_collection' => 'videos',
        'data_source' => $dataSource,
        'multiple' => 'true',
    ]);
    $request->setLaravelSession(app('session.store'));
    $request->session()->setId($clientToken);
    $request->headers->set('Accept', 'application/json');

    // Manually normalize data_source
    if ($request->input('data_source') === 'null') {
        $request->merge(['data_source' => null]);
    }

    $action = app(StoreYouTubeVideoTemporaryAction::class);
    $response = $action->execute($request);

    expect($response->status())->toBe(200);

    // Verify it was stored in the demo database
    $count = DB::connection($testDemoConnection)->table('mle_temporary_uploads')->count();
    expect($count)->toBe(1);

    // Verify it was NOT stored in the default database
    expect(DB::connection($testDemoHostConnection)->table('mle_temporary_uploads')->count())->toBe(0);

    $uploaded = DB::connection($testDemoConnection)->table('mle_temporary_uploads')->first();
    expect($uploaded->collection_name)->toBe('videos');
});

it('stores a youtube video permanent on a custom data source', function () {
    $dataSource = 'demo';

    $testDemoConnection = 'mle_test_demo';
    $testDemoHostConnection = 'mle_test_host_app';

    // Create a model in the demo database
    $model = Blog::on($testDemoConnection)->create(['title' => 'Demo Blog']);
    $modelId = $model->id;

    // Verify we are starting clean on media_demo media table
    expect(DB::connection($testDemoConnection)->table('media')->count())->toBe(0);

    $request = StoreYouTubeVideoRequest::create('/upload-youtube', 'POST', [
        'temporary_upload_mode' => 'false',
        'base_id' => 'test-base',
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
    $count = DB::connection($testDemoConnection)->table('media')->count();
    expect($count)->toBe(1);

    // Verify it was NOT stored in the default database
    expect(DB::connection($testDemoHostConnection)->table('media')->count())->toBe(0);
})->todo();
