@php
    /** @noinspection ALL */
    use Mlbrgn\LaravelFormComponents\View\Components\Form;
    use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Laravel Media Library Extensions Component tests</title>
        @if($frontendTheme === 'bootstrap-5')
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
        <link rel="icon" type="image/x-icon" href="{{ route('mlbrgn.mle.favicon') }}">
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
                console.log('imageEditorLoaded');
            }
        </script>
    </head>
    <body>
    <div class="mle-unified-container">
        <h1>Laravel Media Library Extensions Component tests</h1>
    
        <div class="mle-demo-controls">
            <div class="mle-demo-controls-group">
                <span class="mle-demo-controls-label">Theme:</span>
                <a href="{{ request()->fullUrlWithQuery(['theme' => 'bootstrap-5']) }}" class="mle-demo-btn {{ $frontendTheme === 'bootstrap-5' ? 'mle-demo-btn-primary' : 'mle-demo-btn-outline' }}">Bootstrap 5</a>
                <a href="{{ request()->fullUrlWithQuery(['theme' => 'plain']) }}" class="mle-demo-btn {{ $frontendTheme === 'plain' ? 'mle-demo-btn-primary' : 'mle-demo-btn-outline' }}">Plain</a>
            </div>
    
            <div class="mle-demo-controls-group">
                <span class="mle-demo-controls-label">Data Source:</span>
                <a href="{{ request()->fullUrlWithQuery(['data_source' => 'demo']) }}" class="mle-demo-btn {{ $dataSource === 'demo' ? 'mle-demo-btn-primary' : 'mle-demo-btn-outline' }}">Demo</a>
                <a href="{{ request()->fullUrlWithQuery(['data_source' => 'default']) }}" class="mle-demo-btn {{ empty($dataSource) ? 'mle-demo-btn-primary' : 'mle-demo-btn-outline' }}">Default</a>
            </div>
    
            <div class="mle-demo-controls-group">
                <span class="mle-demo-controls-label">Use XHR:</span>
                <a href="{{ request()->fullUrlWithQuery(['use_xhr' => '1']) }}" class="mle-demo-btn {{ $useXhr ? 'mle-demo-btn-primary' : 'mle-demo-btn-outline' }}">Yes</a>
                <a href="{{ request()->fullUrlWithQuery(['use_xhr' => '0']) }}" class="mle-demo-btn {{ !$useXhr ? 'mle-demo-btn-primary' : 'mle-demo-btn-outline' }}">No</a>
            </div>
        </div>
    
        <h2>Media Manager Single</h2>
        <x-mle-media-manager-single
            id="alien-single"
            :model-or-class-name="$model"
            :collections="[
                            'image' => 'alien-single-image', 
                            'document' =>'alien-single-document', 
                            'youtube' =>'alien-single-youtube-video', 
                            'video' =>'alien-single-video', 
                            'audio' =>'alien-single-audio'
            ]"
            :options="[
                        'frontendTheme' => $frontendTheme, 
                        'dataSource' => $dataSource, 'useXhr' => $useXhr
            ]"
        />
    
        <h2>Media Manager Single (Temporary)</h2>
        <x-mle-media-manager-single
            id="aliens-single-temporary"
            model-or-class-name="Mlbrgn\MediaLibraryExtensions\Models\demo\Alien"
            :collections="[
                            'image' => 'alien-single-image',
                            'document' =>'alien-single-document',
                            'youtube' =>'alien-single-youtube-video',
                            'video' =>'alien-single-video',
                            'audio' =>'alien-single-audio',
                        ]"
            :options="[
                        'frontendTheme' => $frontendTheme, 
                        'dataSource' => $dataSource, 
                        'useXhr' => $useXhr
            ]"
        />
    
        <h2>Media Manager Multiple</h2>
        
        <x-mle-media-manager-multiple
            id="alien-multiple"
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
                'frontendTheme' => $frontendTheme, 
                'dataSource' => $dataSource, 
                'useXhr' => $useXhr
            ]"
        />
    
        <h2>Media Manager Multiple (Temporary)</h2>
        <x-mle-media-manager-multiple
            id="aliens-multiple-temporary"
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
                'frontendTheme' => $frontendTheme, 
                'dataSource' => $dataSource, 
                'useXhr' => $useXhr
            ]"
        />
    
        <h2>Media Carousel</h2>
    
        <p>{{ __('medialibrary-extensions::messages.note_carousel_only_updates_on_refresh_of_page') }}</p>
       
        <x-mle-media-carousel
            id="alien-carousel"
            :model-or-class-name="$model"
            :collections="[
                'alien-media-lab'
            ]"
            :options="[
                'frontendTheme' => $frontendTheme, 
                'dataSource' => $dataSource
            ]"
        />
    
        <h2>Media Lab</h2>
        
        <x-mle-media-lab
            id="alien-lab"
            :media="$media"
            :options="[
                'frontendTheme' => $frontendTheme, 
                'dataSource' => $dataSource, 
                'useXhr' => $useXhr
            ]"
        />
    
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
        @if (app()->environment('local') && class_exists(Form::class))
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
                                    'collection_name' => 'alien-media-lab',
                                    'collections' => ['image' => 'alien-media-lab'],
                                    'data_source' => $dataSource,
                                ]"
                    data-mle-model-type="{{ $model->getMorphClass() }}"
                    data-mle-model-id="{{ $model->getKey() }}"
                    data-mle-data-source="{{ $dataSource }}"
                    :data-mle-collections="json_encode([
                                    'image' => 'alien-media-lab',
                                ])"
                />
            </x-form-form>
        @else
            form components not available, skipping demo
        @endif
    </div>
    
    @if($frontendTheme === 'bootstrap-5')
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            crossorigin="anonymous"
        ></script>
    @endif
    </body>
</html>
