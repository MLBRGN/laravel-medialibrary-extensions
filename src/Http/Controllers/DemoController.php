<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\demo\StoreAlienRequest;
use Mlbrgn\MediaLibraryExtensions\Http\Requests\demo\StoreIsolationRequest;
use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;
use Mlbrgn\MediaLibraryExtensions\Services\DataSourceResolver;

class DemoController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        abort_unless(
            config('medialibrary-extensions.demo_pages_enabled'),
            404
        );

        if ($request->query('isolation_test')) {
            return $this->isolationTest($request);
        }

        // Ensure the demo page URL always carries an explicit data_source to keep
        // the DB context consistent across refreshes and redirects.
        // Default to 'default' (host-app sandbox) when not provided.
        if ($request->query('data_source') === null) {
            $redirectParams = array_merge($request->query(), ['data_source' => 'demo_default']);

            Log::info('DemoController@index: missing data_source, redirecting with default', [
                'from_url' => $request->fullUrl(),
                'to_params' => $redirectParams,
            ]);

            return redirect()->route('mle-demo', $redirectParams);
        }

        config(['medialibrary-extensions.disks.media_originals' => [
            'driver' => 'local',
            'root' => storage_path('app/public/media_originals'),
            'url' => config('app.url').'/storage/media_originals', // URL to access files
            'visibility' => 'public',
        ],
        ]);

        $theme = $request->query('theme', config('medialibrary-extensions.frontend_theme', 'bootstrap-5'));
        $useXhr = $request->boolean('use_xhr', config('medialibrary-extensions.use_xhr', true));
        // Default to 'default' so a plain redirect back to the demo page (without query params)
        // keeps using the host-app sandbox connection and shows recently promoted media.
        $dataSource = $request->query('data_source', 'default');

        // Apply to config so components pick it up as default if not overridden in options
        config([
            'medialibrary-extensions.frontend_theme' => $theme,
            'medialibrary-extensions.use_xhr' => $useXhr,
        ]);

        // Log the incoming context for diagnostics
        try {
            $resolvedConnection = app(DataSourceResolver::class)->resolveConnection($dataSource);
            
            // Ensure all model queries (including Spatie Media) use the resolved demo connection by default
            if ($resolvedConnection) {
                config(['database.default' => $resolvedConnection]);
                app('db')->setDefaultConnection($resolvedConnection);
            }
        } catch (\Throwable $e) {
            Log::warning('DemoController@index: failed to resolve connection', [
                'data_source' => $dataSource,
                'error' => $e->getMessage(),
            ]);
        }

        $model = $this->getDemoModel($dataSource, $request->query('id'));

        // Prefer a specifically prepared Lab medium; otherwise reuse existing uploads
        $media = $model->getMedia('alien-media-lab')->first() ?: $model->media->first();

        // Quick media count log for verification in tests
        try {
            Log::info('DemoController@index: media counts after (re)load', [
                'data_source' => $dataSource,
                'resolved_connection' => $resolvedConnection,
                'counts' => [
                    'alien-multiple-images' => $model->getMedia('alien-multiple-images')->count(),
                    'total' => $model->getMedia()->count(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('DemoController@index: media count failed', [
                'error' => $e->getMessage(),
            ]);
        }

        return view('medialibrary-extensions::demo.mle-unified', [
            'model' => $model,
            'media' => $media,
            'dataSource' => $dataSource,
            'theme' => $theme,
            'useXhr' => $useXhr,
        ]);
    }

    public function store(StoreAlienRequest $request): RedirectResponse
    {
        // Normalize upload context so promotion can find temporary uploads in browser tests
        // Prefer explicit request input; otherwise fall back to the cookie set by the uploader JS
        $clientTokenFromInput = (string) ($request->input('client_token') ?? '');
        $clientTokenFromCookie = (string) ($request->cookie('mle_client_token') ?? '');
        $effectiveClientToken = $clientTokenFromInput !== '' ? $clientTokenFromInput : $clientTokenFromCookie;

        $instanceIdFromInput = (string) ($request->input('instance_id') ?? '');

        // If we have an effective token or instance id, merge back into the request so
        // InteractsWithMediaExtended picks it up during the model "created" event.
        if ($effectiveClientToken !== '' && $request->input('client_token') !== $effectiveClientToken) {
            $request->merge(['client_token' => $effectiveClientToken]);
        }
        if ($instanceIdFromInput !== '' && $request->input('instance_id') !== $instanceIdFromInput) {
            $request->merge(['instance_id' => $instanceIdFromInput]);
        }

        try {
            \Log::info('DemoController@store: resolved upload context for promotion', [
                'client_token_input' => $clientTokenFromInput ?: null,
                'client_token_cookie' => $clientTokenFromCookie ?: null,
                'client_token_effective' => $effectiveClientToken ?: null,
                'instance_id_input' => $instanceIdFromInput ?: null,
            ]);
        } catch (\Throwable $e) {
            // ignore logging errors
        }

        // Ensure the model persists to the correct demo database, even if the
        // demo middleware did not (or could not) switch the default connection.
        // We explicitly set the connection based on the incoming data_source.
        $alien = new Alien($request->validated());
        try {
            $dataSourceForStore = (string) $request->input('data_source', $request->query('data_source', 'default'));
            $resolvedForStore = app(DataSourceResolver::class)->resolveConnection($dataSourceForStore);
            if (! empty($resolvedForStore)) {
                $alien->setConnection($resolvedForStore);
                // Ensure media model also uses the same connection
                config(['database.default' => $resolvedForStore]);
                app('db')->setDefaultConnection($resolvedForStore);
            }
        } catch (\Throwable $e) {
            \Log::warning('DemoController@store: failed to set connection explicitly, falling back to default', [
                'error' => $e->getMessage(),
            ]);
        }

        $alien->save();

        // Preserve demo context on redirect so the index picks the same data source and options.
        $redirectParams = [
            'data_source' => $request->input('data_source', $request->query('data_source', 'default')),
            // Ensure the redirected page shows the model that just received media
            'id' => $alien->id,
        ];

        // If theme / use_xhr were present, carry them as well.
        if ($request->has('theme')) {
            $redirectParams['theme'] = (string) $request->input('theme');
        } elseif ($request->query('theme')) {
            $redirectParams['theme'] = (string) $request->query('theme');
        }

        if ($request->has('use_xhr')) {
            $redirectParams['use_xhr'] = (string) $request->input('use_xhr');
        } elseif ($request->query('use_xhr')) {
            $redirectParams['use_xhr'] = (string) $request->query('use_xhr');
        }

        return redirect()->route('mle-demo', $redirectParams);
    }

    public function isolationTest(Request $request): View
    {
        $theme = $request->query('theme', config('medialibrary-extensions.frontend_theme', 'bootstrap-5'));
        $useXhr = $request->boolean('use_xhr', config('medialibrary-extensions.use_xhr', true));
        $dataSource = $request->query('data_source', 'demo_default');

        config([
            'medialibrary-extensions.frontend_theme' => $theme,
            'medialibrary-extensions.use_xhr' => $useXhr,
        ]);

        $resolvedConnection = app(DataSourceResolver::class)->resolveConnection($dataSource);
        if ($resolvedConnection) {
            config(['database.default' => $resolvedConnection]);
            app('db')->setDefaultConnection($resolvedConnection);
        }

        $model = $this->getDemoModel($dataSource, $request->query('id'));
        $media = $model->getMedia('alien-media-lab')->first() ?: $model->media->first();

        return view('medialibrary-extensions::demo.mle-isolation', [
            'model' => $model,
            'media' => $media,
            'dataSource' => $dataSource,
            'theme' => $theme,
            'useXhr' => $useXhr,
        ]);
    }

    public function submitIsolation(Request $request): RedirectResponse
    {
        try {
            app(StoreIsolationRequest::class);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        }

        $data = $request->all();
        
        // Remove sensitive or irrelevant fields for display
        unset($data['_token']);

        return redirect()->back()->with('submitted_data', $data);
    }

    protected function getDemoModel(?string $dataSource = 'default', mixed $id = null): Alien
    {
        $model = new Alien;

        if ($dataSource) {
            $connection = app(DataSourceResolver::class)->resolveConnection($dataSource);
            $model->setConnection($connection);
        }

        $query = $model->newQuery()->with('media');

        if (filled($id)) {
            $existingModel = $query->find($id);
        } else {
            $existingModel = $query->first();
        }

        if (! $existingModel) {
            throw new Exception('no Alien model found');
        }

        return $existingModel;
    }
}
