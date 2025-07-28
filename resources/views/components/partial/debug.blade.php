@if(config('media-library-extensions.debug') && !app()->environment('production'))
    <div class="mle-debug-wrapper">
        <button type="button" class="mle-debug-toggle" aria-expanded="false" aria-controls="mle-debug-content">
            🐞 Show Debug Info
        </button>

        <div class="mle-debug hidden" id="mle-debug-content">
            <h2>📦 Media Library Extensions Debug Info</h2>

            <div class="mle-debug-section">
                <h3>🗄️ Model</h3>
                <ul>
                    <li><strong>Temporary upload:</strong> {{ $config['temporary_upload'] ? 'Yes' : 'No' }}</li>
                    <li><strong>Model Type:</strong> {{ $config['model_type'] ?? 'n/a' }}</li>
                    <li><strong>Model ID:</strong> {{ $config['model_id'] ?? 'n/a' }}</li>
                </ul>
            </div>

            <div class="mle-debug-section">
                <h3>🎞️ Collections</h3>
                <ul>
                    <li><strong>Image Collection:</strong> {{ $config['image_collection'] ?? 'n/a' }}</li>
                    <li><strong>Document Collection:</strong> {{ $config['document_collection'] ?? 'n/a' }}</li>
                    <li><strong>YouTube Collection:</strong> {{ $config['youtube_collection'] ?? 'n/a' }}</li>
                    @foreach($collections as $collection)
                        <li><strong>{{ $collection }}</strong>: {{ $model->getMedia($collection)->count() }} items</li>
                    @endforeach
                </ul>
            </div>

            <div class="mle-debug-section">
                <h3>⚙️ Component Settings</h3>
                <ul>
                    <li><strong>Destroy Enabled:</strong> {{ $config['destroy_enabled'] ? 'true' : 'false' }}</li>
                    <li><strong>Set-as-First Enabled:</strong> {{ $config['set_as_first_enabled'] ? 'true' : 'false' }}</li>
                    <li><strong>Show Media URL:</strong> {{ $config['show_media_url'] ? 'true' : 'false' }}</li>
                    <li><strong>Show Order:</strong> {{ $config['show_order'] ? 'true' : 'false' }}</li>
                    <li><strong>Frontend Theme:</strong> {{ $config['frontend_theme'] ?? 'not set' }}</li>
                </ul>
            </div>

            <div class="mle-debug-section">
                <h3>🌐 Routes</h3>
                <ul>
                    <li><strong>Media Upload Route:</strong> <code>{{ $config['media_upload_route'] }}</code></li>
                    <li><strong>YouTube Upload Route:</strong> <code>{{ $config['youtube_upload_route'] }}</code></li>
                    <li><strong>Preview Refresh Route:</strong> <code>{{ $config['preview_refresh_route'] }}</code></li>
                </ul>
            </div>

            <div class="mle-debug-section">
                <h3>🎛️ (Environment) Config</h3>
                <ul>
                    <li><strong>Theme:</strong> {{ $theme }}</li>
                    <li><strong>XHR Enabled:</strong> {{ config('media-library-extensions.use_xhr') ? 'Yes' : 'No' }}</li>
                    <li><strong>Demo Mode:</strong> {{ config('media-library-extensions.demo_mode') ? 'Yes' : 'No' }}</li>
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

{{--<div class="mle-debug border rounded p-2 mb-3 bg-light small text-muted">--}}
{{--    <h6 class="mb-2">Media Manager Debug</h6>--}}
{{--    <ul class="list-unstyled mb-0">--}}
{{--        <li><strong>ID:</strong> {{ $id }}</li>--}}
{{--        <li><strong>Model:</strong> {{ get_class($model) }} (ID: {{ $model->getKey() }})</li>--}}
{{--        <li><strong>Image Collection:</strong> {{ $imageCollection }}</li>--}}
{{--        <li><strong>Document Collection:</strong> {{ $documentCollection }}</li>--}}
{{--        <li><strong>YouTube Collection:</strong> {{ $youtubeCollection }}</li>--}}
{{--        <li><strong>Upload Enabled:</strong> {{ $uploadEnabled ? 'true' : 'false' }}</li>--}}
{{--        <li><strong>Destroy Enabled:</strong> {{ $destroyEnabled ? 'true' : 'false' }}</li>--}}
{{--        <li><strong>Set-as-First Enabled:</strong> {{ $setAsFirstEnabled ? 'true' : 'false' }}</li>--}}
{{--        <li><strong>Show Media URL:</strong> {{ $showMediaUrl ? 'true' : 'false' }}</li>--}}
{{--        <li><strong>Show Order:</strong> {{ $showOrder ? 'true' : 'false' }}</li>--}}
{{--        <li><strong>Frontend Theme:</strong> {{ $theme }}</li>--}}
{{--        <li><strong>Use XHR:</strong> {{ $useXhr ? 'true' : 'false' }}</li>--}}
{{--        <li><strong>Media Upload Route:</strong> <code>{{ $mediaUploadRoute }}</code></li>--}}
{{--        <li><strong>YouTube Upload Route:</strong> <code>{{ $youtubeUploadRoute }}</code></li>--}}
{{--        <li><strong>Preview Refresh Route:</strong> <code>{{ $previewRefreshRoute }}</code></li>--}}
{{--    </ul>--}}
{{--</div>--}}
