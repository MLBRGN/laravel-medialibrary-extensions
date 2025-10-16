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
                    <li><strong>Model type:</strong> {{ $config['modelType'] ?? 'n/a' }}</li>
                    <li><strong>Model id:</strong> {{ $config['modelId'] ?? 'n/a' }}</li>
                </ul>
            </div>

            <div class="mle-debug-section">
                <h3>⚙️ Component config: General</h3>
                <ul>
                    <li><strong>Id:</strong> {{ $config['id'] }}</li>
                    <li><strong>Frontend theme:</strong> {{ $config['frontendTheme'] }}</li>
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
                    @foreach (['image', 'document', 'video', 'audio', 'youtube'] as $type)
                        @php
                            $collectionName = $config['collections'][$type] ?? null;
                            $count = ($model && $collectionName)
                            ? $model->getMedia($collectionName)->count()
                            : 0;
                        @endphp

                        <li>
                            <strong>{{ ucfirst($type) }}:</strong>
                            {{ $collectionName ?? 'n/a' }}
                            @if ($collectionName)
                                ({{ $count }} {{ Str::plural('item', $count) }})
                            @endif
                        </li>
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
                    <li><strong>XHR enabled:</strong> {{ config('media-library-extensions.use_xhr') ? 'Yes' : 'No' }}</li>
                    <li><strong>Show Status:</strong> {{ config('media-library-extensions.show_status') ? 'Yes' : 'No' }}</li>
                    <li><strong>YouTube support enabled:</strong> {{ config('media-library-extensions.youtube_support_enabled') ? 'Yes' : 'No' }}</li>
                    <li><strong>Allowed Mime types:</strong> {{ collect(config('media-library-extensions.allowed_mimetypes'))->flatten()->join(', ') }}</li>
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
