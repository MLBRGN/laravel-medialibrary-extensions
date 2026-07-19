@php
    /** @noinspection ALL */
    use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;
    
    $showMmsPermanent = true;
    $showMmsTemporary = true;
    $showMmmPermanent = true;
    $showMmmTemporary = true;
    $showMediaCarousel = true;
    $showMediaLab = true;
    $showMediaFirstAvailable = true;
    $showFormCustomFilePicker = true;
    
//    $showMmsPermanent = true;
//    $showMmsTemporary = true;
//    $showMmmPermanent = false;
//    $showMmmTemporary = false;
//    $showMediaCarousel = false;
//    $showMediaLab = false;
//    $showMediaFirstAvailable = false;
//    $showFormCustomFilePicker = false;

@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Laravel Media Library Extensions Component tests</title>
        @if($theme === 'bootstrap-5')
            <link
                href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
                rel="stylesheet"
                crossorigin="anonymous"
            >
        @endif
        <style>
            body {
                font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                padding-bottom: 5rem;
            }
    
            .mle-unified-container {
                max-width: 1320px;
                margin: 3rem auto;
                padding: 0 1rem;
            }
    
            h1 {
                color: #0d6efd;
                font-size: 2.5rem;
                margin-bottom: 2rem;
            }
    
            h2 {
                margin-top: 3rem;
                margin-bottom: 1.5rem;
                font-size: 2rem;
                border-bottom: 1px solid #eee;
                padding-bottom: 0.5rem;
            }
    
            .mle-demo-controls {
                background: #f8f9fa;
                padding: 1.5rem;
                border-radius: 0.5rem;
                margin-bottom: 3rem;
                border: 1px solid #dee2e6;
            }
    
            .mle-demo-controls-group {
                margin-bottom: 1rem;
                display: flex;
                align-items: center;
                gap: 1rem;
                flex-wrap: wrap;
            }
    
            .mle-demo-controls-label {
                font-weight: bold;
                min-width: 150px;
            }
    
            .mle-demo-btn {
                display: inline-block;
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
                text-decoration: none;
                border-radius: 0.25rem;
                border: 1px solid #0d6efd;
                transition: all 0.2s;
            }
    
            .mle-demo-btn-primary {
                background-color: #0d6efd;
                color: white !important;
            }
    
            .mle-demo-btn-outline {
                background-color: transparent;
                color: #0d6efd !important;
            }
    
            .mle-demo-btn:hover {
                filter: brightness(90%);
            }
    
            .mle-demo-media-first-available-container {
                height: 200px;
                overflow: hidden;
                border: 1px solid #eee;
            }
        </style>
{{--        <link rel="icon" type="image/x-icon" href="{{ route('mlbrgn.mle.favicon') }}">--}}
        @php
            $nonce = mlbrgn_csp_nonce();
        @endphp
        <script
            type="module"
            @isset($nonce) nonce="{{ $nonce }}" @endisset
        >
            if (!window.imageEditorLoaded) {
                const script = document.createElement('script');
                script.type = 'module';
                script.src = "{{ asset(config('medialibrary-extensions.asset_path') . '/js/image-editor.js') }}";
                document.head.appendChild(script);
                window.imageEditorLoaded = true;
            }
        </script>
        {{-- for form components loader to work it needs config to be set --}}
        <script class="mlbrgn-form-components-config" type="application/json">
            {
              "assetBasePath": "/vendor/mlbrgn/laravel-form-components"
            }
        </script>
    </head>
    <body>
    <div class="mle-unified-container">
        <h1>Laravel Media Library Extensions Component tests</h1>
    
        <div class="mle-demo-controls">
            <div class="mle-demo-controls-group">
                <span class="mle-demo-controls-label">Theme:</span>
                <a href="{{ request()->fullUrlWithQuery(['theme' => 'bootstrap-5']) }}" class="mle-demo-btn {{ $theme === 'bootstrap-5' ? 'mle-demo-btn-primary' : 'mle-demo-btn-outline' }}" data-test="btn-theme-bootstrap-5">Bootstrap 5</a>
                <a href="{{ request()->fullUrlWithQuery(['theme' => 'plain']) }}" class="mle-demo-btn {{ $theme === 'plain' ? 'mle-demo-btn-primary' : 'mle-demo-btn-outline' }}" data-test="btn-theme-plain">Plain</a>
            </div>
    
            <div class="mle-demo-controls-group">
                <span class="mle-demo-controls-label">Data Source:</span>
                <a href="{{ request()->fullUrlWithQuery(['data_source' => 'demo_alt']) }}" class="mle-demo-btn {{ $dataSource === 'demo_alt' ? 'mle-demo-btn-primary' : 'mle-demo-btn-outline' }}" data-test="btn-data-source-demo">Demo alt</a>
                <a href="{{ request()->fullUrlWithQuery(['data_source' => 'demo_default']) }}" class="mle-demo-btn {{ $dataSource === 'demo_default' ? 'mle-demo-btn-primary' : 'mle-demo-btn-outline' }}" data-test="btn-data-source-default">Demo default</a>
            </div>
    
            <div class="mle-demo-controls-group">
                <span class="mle-demo-controls-label">Use XHR:</span>
                <a href="{{ request()->fullUrlWithQuery(['use_xhr' => '1']) }}" class="mle-demo-btn {{ $useXhr ? 'mle-demo-btn-primary' : 'mle-demo-btn-outline' }}" data-test="btn-use-xhr-yes">Yes</a>
                <a href="{{ request()->fullUrlWithQuery(['use_xhr' => '0']) }}" class="mle-demo-btn {{ !$useXhr ? 'mle-demo-btn-primary' : 'mle-demo-btn-outline' }}" data-test="btn-use-xhr-no">No</a>
            </div>
        </div>

        @if ($showMmsPermanent)
            <h2>Media Manager Single</h2>
            <x-mle-media-manager-single
                id="alien-single-permanent"
                :model-or-class-name="$model"
                :collections="[
                                'image' => 'alien-single-image', 
                                'document' =>'alien-single-document', 
                                'youtube' =>'alien-single-youtube-video', 
                                'video' =>'alien-single-video', 
                                'audio' =>'alien-single-audio'
                ]"
                :options="[
                            'theme' => $theme, 
                            'dataSource' => $dataSource, 
                            'useXhr' => $useXhr,
                ]"
                :data-source="$dataSource"
            />
        @endif

        @if ($showMmsTemporary)
            <h2>Media Manager Single (Temporary)</h2>

                <x-mle-media-manager-single
                    id="alien-single-temporary"
                    model-or-class-name="Mlbrgn\MediaLibraryExtensions\Models\demo\Alien"
                    :collections="[
                                    'image' => 'alien-single-image',
                                    'document' =>'alien-single-document',
                                    'youtube' =>'alien-single-youtube-video',
                                    'video' =>'alien-single-video',
                                    'audio' =>'alien-single-audio',
                                ]"
                    :options="[
                                'theme' => $theme, 
                                'dataSource' => $dataSource, 
                                'useXhr' => $useXhr
                    ]"
                    :data-source="$dataSource"
                    
                />
{{--                <form action="{{ route('store-alien') }}" method="post">--}}
{{--                    @csrf--}}
{{--                    <input type="hidden" name="name" value="dummy">--}}
{{--                    <input type="hidden" name="instance_id" value="{{ \Mlbrgn\MediaLibraryExtensions\Support\InstanceManager::getInstanceId('alien-single-temporary') }}">--}}
{{--                    <input type="hidden" name="data_source" value="{{ $dataSource }}">--}}
{{--                    <input type="hidden" name="client_token" value="" data-mle-client-token>--}}
{{--                    <input type="hidden" name="data_source" value="{{ $dataSource }}">--}}
{{--                    <button type="submit" class="mle-demo-btn {{ $theme === 'bootstrap-5' ? 'mle-demo-btn-primary' : 'mle-demo-btn-outline' }}" data-test="btn-theme-bootstrap-5">Save model (and promote temporary media)</button>--}}
{{--                </form>--}}
        @endif

        @if ($showMmmPermanent)
            <h2>Media Manager Multiple</h2>
            <x-mle-media-manager-multiple
                id="alien-multiple-permanent"
                :model-or-class-name="$model"
                :collections="[
                    'image' => 'alien-multiple-images', 
                    'document' =>'alien-multiple-documents', 
                    'youtube' =>'alien-multiple-youtube-videos', 
                    'video' =>'alien-multiple-videos', 
                    'audio' =>'alien-multiple-audios'
                ]"
                :options="[
                    'showOrder' => true, 
                    'theme' => $theme, 
                    'dataSource' => $dataSource, 
                    'useXhr' => $useXhr,
                    // Respect global/demo-configured cap so tests and runtime stay in sync
                    'maxMediaCount' => config('medialibrary-extensions.max_items_in_shared_media_collections')

                ]"
                :data-source="$dataSource"
                
            />
        @endif
    
        @if($showMmmTemporary)
            <h2>Media Manager Multiple (Temporary)</h2>

                <x-mle-media-manager-multiple
                    id="alien-multiple-temporary"
                    model-or-class-name="Mlbrgn\MediaLibraryExtensions\Models\demo\Alien"
                    :collections="[
                        'image' => 'alien-multiple-images', 
                        'document' =>'alien-multiple-documents', 
                        'youtube' =>'alien-multiple-youtube-videos', 
                        'video' =>'alien-multiple-videos', 
                        'audio' =>'alien-multiple-audios'
                    ]"
                    :options="[
                        'showOrder' => true, 
                        'theme' => $theme, 
                        'dataSource' => $dataSource, 
                        'useXhr' => $useXhr,
                        // Respect global/demo-configured cap so tests and runtime stay in sync
                        'maxMediaCount' => config('medialibrary-extensions.max_items_in_shared_media_collections')
                    ]"
                    :data-source="$dataSource"
                />
{{--                <form action="{{ route('store-alien') }}" method="post">--}}
{{--                    @csrf--}}
{{--                    <input type="hidden" name="name" value="dummy">--}}
{{--                    <input type="hidden" name="instance_id" value="{{ \Mlbrgn\MediaLibraryExtensions\Support\InstanceManager::getInstanceId('alien-multiple-temporary') }}">--}}
{{--                    <input type="hidden" name="data_source" value="{{ $dataSource }}">--}}
{{--                    <input type="hidden" name="client_token" value="" data-mle-client-token>--}}
{{--                    <input type="hidden" name="data_source" value="{{ $dataSource }}">--}}
{{--                    <button type="submit" class="mle-demo-btn {{ $theme === 'bootstrap-5' ? 'mle-demo-btn-primary' : 'mle-demo-btn-outline' }}" data-test="btn-theme-bootstrap-5">Save model (and promote temporary media)</button>--}}
{{--                </form>--}}
        @endif
    
        @if($showMediaCarousel)
            <h2>Media Carousel</h2>
            <p>{{ __('medialibrary-extensions::messages.note_carousel_only_updates_on_refresh_of_page') }}</p>
            <p>only shows temporary uploads for media manager multiple</p>
        
            <button id="carouselRefreshButton" type="button" class="btn btn-primary mb-3">Refresh carousel</button>
            <x-mle-media-carousel
                id="alien-carousel"
                :model-or-class-name="$model"
                :collections="[
                    'image' => ['alien-multiple-images', 'alien-single-image'], 
                    'document' => ['alien-multiple-documents', 'alien-single-document'], 
                    'youtube' => ['alien-multiple-youtube-videos', 'alien-single-youtube-video'], 
                    'video' => ['alien-multiple-videos', 'alien-single-video'], 
                    'audio' => ['alien-multiple-audios', 'alien-single-audio'],
                ]"
                :options="[
                    'theme' => $theme, 
                    'dataSource' => $dataSource
                ]"
                :data-source="$dataSource"
                :instance-id="null"
            />
        @endif
    
        @if ($showMediaLab)
            <h2>Media Lab</h2>
            @php
                $mediaService = app(\Mlbrgn\MediaLibraryExtensions\Services\MediaService::class);
            @endphp
            @isset($media)
                <x-mle-media-lab
                    id="alien-laboratory"
                    :media="$media"
                    :options="[
                        'theme' => $theme, 
                        'dataSource' => $dataSource, 
                        'useXhr' => $useXhr
                    ]"
                    :data-source="$dataSource"
                />
            @else
                Media lab not showing, no media.
            @endisset
        @endif
    
       
        @if ($showMediaFirstAvailable)
            <h2>Media First Available</h2>
            <div class="mle-demo-media-first-available-container">
                <x-mle-first-available
                    id="media-first-available"
                    :model-or-class-name="$model"
                    :collections="[
                        'image' => 'alien-single-image',
                        'document' => 'alien-single-document',
                        'youtube' => 'alien-single-youtube-video',
                        'video' => 'alien-single-video',
                        'audio' => 'alien-single-audio',
                    ]"
                    :options="[
                        'dataSource' => $dataSource,
                    ]"
                    class="mle-width-100 mle-height-100"
                />
            </div>
        @endif
        
        @if($showFormCustomFilePicker)
            @if ((app()->environment('local') || app()->environment('testing')) && class_exists(\Mlbrgn\LaravelFormComponents\Providers\FormComponentsServiceProvider::class))
                <h2 class="my-5">Mlbrgn Form components custom file picker integration</h2>
                <x-form-form
                    method="put"
                    enctype="multipart/form-data"
                    class="my-5"
                >
                    <x-form-html-editor
                        name="content"
                        label="Content *"
                        :tinymce-config="[]"
                        :extra-form-data="[
                                        'model_type' => $model->getMorphClass(),
                                        'model_id' => $model->getKey(),
                                        'collection_name' => 'alien-media-html-editor',
                                        'collections' => ['image' => 'alien-media-html-editor'],
                                        'data_source' => $dataSource,
                                    ]"
                        data-mle-model-type="{{ $model->getMorphClass() }}"
                        data-mle-model-id="{{ $model->getKey() }}"
                        data-mle-data-source="{{ $dataSource }}"
                        :data-mle-collections="json_encode([
                            'image' => 'alien-media-html-editor',
                        ])"
                    />
                </x-form-form>
            @else
                form components not available, skipping demo
            @endif
        @endif
        
    </div>
    
    @if($theme === 'bootstrap-5')
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            crossorigin="anonymous"
        ></script>
    @endif
    
    <script type="module">
        document.getElementById('carouselRefreshButton').addEventListener('click', () => {
            alert('refreshing carousel');
        });
    </script>
    @stack('scripts')
    </body>
</html>
