@php use Mlbrgn\MediaLibraryExtensions\Models\Media;use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload; @endphp
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
                    <li><strong>Model Type:</strong> {{ $config['model_type'] ?? 'n/a' }}</li>
                    <li><strong>Model ID:</strong> {{ $config['model_id'] ?? 'n/a' }}</li>
                </ul>
            </div>

            <div class="mle-debug-section">
                <h3>⚙️ Component config: General</h3>
                <ul>
                    <li><strong>Id:</strong> {{ $config['id'] }}</li>
                    <li><strong>Frontend Theme:</strong> {{ $config['frontend_theme'] }}</li>
                </ul>
            </div>

            <div class="mle-debug-section">
                <h3>🌐 Component config: Routes</h3>
                <ul>
                    <li><strong>Media upload route:</strong> <code>{{ $config['media_upload_route'] }}</code></li>
                    <li><strong>YouTube upload route:</strong> <code>{{ $config['youtube_upload_route'] }}</code></li>
                    <li><strong>Preview update route:</strong> <code>{{ $config['preview_update_route'] }}</code></li>
                </ul>
            </div>

            <div class="mle-debug-section">
                <h3>🎞️ Component config: Collections</h3>
                <ul>
                    <li><strong>Image Collection:</strong> {{ $config['image_collection'] ?? 'n/a' }}</li>
                    <li><strong>Document Collection:</strong> {{ $config['document_collection'] ?? 'n/a' }}</li>
                    <li><strong>Video Collection:</strong> {{ $config['video_collection'] ?? 'n/a' }}</li>
                    <li><strong>Audio Collection:</strong> {{ $config['audio_collection'] ?? 'n/a' }}</li>
                    <li><strong>YouTube Collection:</strong> {{ $config['youtube_collection'] ?? 'n/a' }}</li>
                    @foreach($collections as $collection)
                        <li><strong>{{ $collection }}</strong>: {{ $model->getMedia($collection)->count() }} items</li>
                    @endforeach
                </ul>
            </div>

            <div class="mle-debug-section">
                <h3>🎛️ Component config: Enabled features</h3>
                <ul>
                    <li><strong>Destroy enabled:</strong> {{ $config['destroy_enabled'] ? 'true' : 'false' }}</li>
                    <li><strong>"Set-as-first"
                            Enabled:</strong> {{ $config['set_as_first_enabled'] ? 'true' : 'false' }}</li>
                    <li><strong>Show media-URL:</strong> {{ $config['show_media_url'] ? 'true' : 'false' }}</li>
                    <li><strong>Show order:</strong> {{ $config['show_order'] ? 'true' : 'false' }}</li>
                    <li><strong>Show menu:</strong> {{ $config['show_menu'] ? 'true' : 'false' }}</li>
                    <li><strong>Temporary upload:</strong> {{ $config['temporary_upload'] === 'true' ? 'Yes' : 'No' }}
                    </li>
                </ul>
            </div>

            <div class="mle-debug-section">
                <h3>🧊️ Database</h3>
                <ul>
                    {{--                    @if(config('media-library-extensions.demo_pages_enabled'))--}}
                    {{--                        <li><strong>Database connection (demo model):</strong> {{ $model->getConnectionName() ?? config('database.default') }}</li>--}}
{{--                    --}}{{--                    @endif--}}
{{--                    <li><strong>Media--}}
{{--                            model:</strong> {{ get_class(app(\Spatie\MediaLibrary\MediaCollections\Models\Media::class)) ?? 'unknown' }}--}}
{{--                    </li>--}}
{{--                    <li><strong>TemporaryUpload model db--}}
{{--                            connection):</strong> {{ app(TemporaryUpload::class)->getConnectionName() ?? 'unknown' }}--}}
{{--                    </li>--}}
{{--                    <li>--}}
{{--                        <strong>Connection:</strong> {{ app(Media::class)->getConnectionName() ?? config('database.default') }}--}}
{{--                    </li>--}}
                    {{--                    <li><strong>Database:</strong> {{ app(Media::class)->getConnection()->getDatabaseName() }}</li>--}}
                </ul>
            </div>

            <div class="mle-debug-section">
                <h3>🎛️ Config file values</h3>
                <ul>
                    <li><strong>XHR Enabled:</strong> {{ config('media-library-extensions.use_xhr') ? 'Yes' : 'No' }}
                    </li>
                    <li><strong>Show
                            Status:</strong> {{ config('media-library-extensions.show_status') ? 'Yes' : 'No' }}</li>
                    <li><strong>YouTube Support
                            Enabled:</strong> {{ config('media-library-extensions.youtube_support_enabled') ? 'Yes' : 'No' }}
                    </li>
                    <li><strong>Allowed Mime
                            Types:</strong> {{ collect(config('media-library-extensions.allowed_mimetypes'))->flatten()->join(', ') }}
                    </li>
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
