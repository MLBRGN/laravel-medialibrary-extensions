@php
    use Mlbrgn\MediaLibraryExtensions\Models\Media;
    use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
@endphp

@if(config('media-library-extensions.debug') && !app()->environment('production'))
    <div class="mle-debug-wrapper">
        <button type="button" class="mle-debug-toggle" aria-expanded="false" aria-controls="{{ $id }}-debug-content">
            🐞 Show Debug Info
        </button>

        <div class="mle-debug hidden" id="{{ $id }}-debug-content">
            <h2>📦 Media Library Extensions Debug Info</h2>

            <div class="mle-debug-section">
                <h3>🗄️ Component config: Model</h3>
                <ul>
                    <li><strong>Model Type:</strong> {{ $config['modelType'] ?? 'n/a' }}</li>
                    <li><strong>Model ID:</strong> {{ $config['modelId'] ?? 'n/a' }}</li>
                </ul>
            </div>

            <div class="mle-debug-section">
                <h3>⚙️ Component config: General</h3>
                <ul>
                    <li><strong>Id:</strong> {{ $config['id'] }}</li>
                    <li><strong>Frontend Theme:</strong> {{ $config['frontendTheme'] }}</li>
                </ul>
            </div>

            <div class="mle-debug-section">
                <h3>🌐 Component config: Routes</h3>
                <ul>
                    <li><strong>Media upload route:</strong> <code>{{ $config['mediaUploadRoute'] }}</code></li>
                    <li><strong>YouTube upload route:</strong> <code>{{ $config['youtubeUploadRoute'] }}</code></li>
                    <li><strong>Preview update route:</strong> <code>{{ $config['previewUpdateRoute'] }}</code></li>
                </ul>
            </div>

            <div class="mle-debug-section">
                <h3>🎞️ Component config: Collections</h3>
                <ul>
                    <li><strong>Image Collection:</strong> {{ $config['imageCollection'] ?? 'n/a' }}</li>
                    <li><strong>Document Collection:</strong> {{ $config['documentCollection'] ?? 'n/a' }}</li>
                    <li><strong>Video Collection:</strong> {{ $config['videoCollection'] ?? 'n/a' }}</li>
                    <li><strong>Audio Collection:</strong> {{ $config['audioCollection'] ?? 'n/a' }}</li>
                    <li><strong>YouTube Collection:</strong> {{ $config['youtubeCollection'] ?? 'n/a' }}</li>
                    @foreach($collections as $collection)
                        <li><strong>{{ $collection }}</strong>: {{ $model->getMedia($collection)->count() }} items</li>
                    @endforeach
                </ul>
            </div>

            <div class="mle-debug-section">
                <h3>🎛️ Component config: Enabled features</h3>
                <ul>
                    <li><strong>Show destroy button:</strong> {{ $config['showDestroyButton'] ? 'true' : 'false' }}</li>
                    <li><strong>Show "Set-as-first" button:</strong> {{ $config['showSetAsFirstButton'] ? 'true' : 'false' }}</li>
                    <li><strong>Show "media-edit" button:</strong> {{ $config['showMediaEditButton'] ? 'true' : 'false' }}</li>
                    <li><strong>Show order:</strong> {{ $config['showOrder'] ? 'true' : 'false' }}</li>
                    <li><strong>Show menu:</strong> {{ $config['showMenu'] ? 'true' : 'false' }}</li>
                    <li><strong>Temporary upload:</strong> {{ $config['temporaryUploadMode'] === 'true' ? 'Yes' : 'No' }}</li>
                </ul>
            </div>

            <div class="mle-debug-section">
                <h3>🧊️ Database</h3>
                <ul>
                    {{-- Example debug info for database models --}}
                    {{-- Uncomment or customize as needed --}}
                    {{-- <li><strong>Media model:</strong> {{ get_class(app(\Spatie\MediaLibrary\MediaCollections\Models\Media::class)) ?? 'unknown' }}</li> --}}
                    {{-- <li><strong>TemporaryUpload model connection:</strong> {{ app(TemporaryUpload::class)->getConnectionName() ?? 'unknown' }}</li> --}}
                    {{-- <li><strong>Connection:</strong> {{ app(Media::class)->getConnectionName() ?? config('database.default') }}</li> --}}
                </ul>
            </div>

            <div class="mle-debug-section">
                <h3>🎛️ Config file values</h3>
                <ul>
                    <li><strong>XHR Enabled:</strong> {{ config('media-library-extensions.use_xhr') ? 'Yes' : 'No' }}</li>
                    <li><strong>Show Status:</strong> {{ config('media-library-extensions.show_status') ? 'Yes' : 'No' }}</li>
                    <li><strong>YouTube Support Enabled:</strong> {{ config('media-library-extensions.youtube_support_enabled') ? 'Yes' : 'No' }}</li>
                    <li><strong>Allowed Mime Types:</strong> {{ collect(config('media-library-extensions.allowed_mimetypes'))->flatten()->join(', ') }}</li>
                </ul>
            </div>

            @if(collect($errors)->isNotEmpty())
                <div class="mle-debug-errors">
                    <h3>⚠️ {{ __('media-library-extensions::messages.warning') }}</h3>
                    <ul>
                        @foreach($errors as $error)
                            <li>{!! $error !!}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
@endif

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.mle-debug-wrapper').forEach(function (wrapper) {
                const toggleBtn = wrapper.querySelector('.mle-debug-toggle');
                const debugSection = wrapper.querySelector('.mle-debug');

                if (toggleBtn && debugSection) {
                    toggleBtn.addEventListener('click', function () {
                        const isExpanded = toggleBtn.getAttribute('aria-expanded') === 'true';
                        toggleBtn.setAttribute('aria-expanded', String(!isExpanded));
                        debugSection.classList.toggle('hidden');
                        toggleBtn.textContent = isExpanded
                            ? '🐞 Show Debug Info'
                            : '🐞 Hide Debug Info';
                    });
                }
            });
        });
    </script>
@endonce
