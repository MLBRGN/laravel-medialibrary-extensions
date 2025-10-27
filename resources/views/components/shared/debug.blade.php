@php
    use Mlbrgn\MediaLibraryExtensions\Models\Media;
    use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
@endphp

@if(config('media-library-extensions.debug') && !app()->environment('production'))
    <div class="mle-debug-wrapper">
{{--        <button type="button" class="mle-debug-toggle" aria-expanded="false" aria-controls="{{ $id }}-debug-content">--}}
{{--            🐞 Show Debug Info--}}
{{--        </button>--}}

        <div class="mle-debug hidden" id="{{ $id }}-debug-content">
            <h2>📦 Media Library Extensions Debug Info</h2>

            <div class="mle-debug-section">
                <h3>🗄️ Component config: Model</h3>
                <ul>
                    <li><strong>Model type:</strong> {{ $modelType ?? 'n/a' }}</li>
                    <li><strong>Model id:</strong> {{ $modelId ?? 'n/a' }}</li>
                </ul>
            </div>

            <div class="mle-debug-section">
                <h3>⚙️ Component config: General</h3>
                <ul>
                    <li><strong>Id:</strong> {{ $id }}</li>
                    <li><strong>Frontend theme:</strong> {{ $getConfig('frontendTheme') }}</li>
                </ul>
            </div>

            <div class="mle-debug-section">
                <h3>🌐 Component config: Routes</h3>
                <ul>
                    <li><strong>Media upload route:</strong> <code>{{ $getConfig('mediaUploadRoute') }}</code></li>
                    <li><strong>YouTube upload route:</strong> <code>{{ $getConfig('youtubeUploadRoute') }}</code></li>
                    <li><strong>MM Preview update route:</strong> <code>{{ $getConfig('mediaManagerPreviewUpdateRoute') }}</code></li>
                    <li><strong>MML Preview update route:</strong> <code>{{ $getConfig('mediaManagerLabPreviewUpdateRoute') }}</code></li>
                </ul>
            </div>

            <div class="mle-debug-section">
                <h3>🎞️ Component config: Collections</h3>
                <ul>
                    @foreach (['image', 'document', 'video', 'audio', 'youtube'] as $type)
                        @php
                            $collectionName = $getConfig('collections')[$type] ?? null;
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
                    <li><strong>Show destroy button:</strong> {{ $getConfig('showDestroyButton') ? 'true' : 'false' }}</li>
                    <li><strong>Show "Set-as-first" button:</strong> {{ $getConfig('showSetAsFirstButton') ? 'true' : 'false' }}</li>
                    <li><strong>Show "media-edit" button:</strong> {{ $getConfig('showMediaEditButton') ? 'true' : 'false' }}</li>
                    <li><strong>Show order:</strong> {{ $getConfig('showOrder') ? 'true' : 'false' }}</li>
                    <li><strong>Show menu:</strong> {{ $getConfig('showMenu') ? 'true' : 'false' }}</li>
                    <li><strong>Temporary upload:</strong> {{ $getConfig('temporaryUploadMode') === 'true' ? 'Yes' : 'No' }}</li>
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

            <div class="mle-debug-section">
                <h3>🎛️ Raw config dump</h3>
                <pre>{{ json_encode($getSanitizedConfig(), JSON_PRETTY_PRINT) }}</pre>
            </div>
{{--            <div class="mle-debug-section">--}}
{{--                <h3>🎛️ Raw config dump</h3>--}}
{{--                <pre>{{ json_encode($config, JSON_PRETTY_PRINT) }}</pre>--}}
{{--            </div>--}}

            <div class="mle-debug-section">
                <h3>🎛️ Raw options dump</h3>
                <pre>{{ json_encode(collect($options)->sortKeys()->all(), JSON_PRETTY_PRINT) }}</pre>
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
