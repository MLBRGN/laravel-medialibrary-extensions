@if(config('media-library-extensions.debug'))
    <div class="mle-debug-wrapper">
        <button type="button" class="mle-debug-toggle" aria-expanded="false" aria-controls="mle-debug-content">
            📦 Show Debug Info
        </button>

        <div class="mle-debug hidden" id="mle-debug-content">
            <h2>📦 Media Library Extensions Debug Info</h2>
            <ul>
                <li><strong>Theme:</strong> {{ $theme }}</li>
                <li><strong>XHR enabled:</strong> {{ config('media-library-extensions.use_xhr') ? 'Yes' : 'No' }}</li>
                <li><strong>Demo mode enabled:</strong> {{ config('media-library-extensions.demo_mode') ? 'Yes' : 'No' }}</li>
                <li><strong>Show status:</strong> {{ config('media-library-extensions.show_status') ? 'Yes' : 'No' }}</li>
                <li><strong>YouTube support enabled:</strong> {{ config('media-library-extensions.youtube_support_enabled') ? 'Yes' : 'No' }}</li>
                <li><strong>Allowed mimetypes:</strong> {{ collect(config('media-library-extensions.allowed_mimetypes'))->flatten()->join(', ') }}</li>
                @foreach($collections as $collection)
                    <li><strong>{{ $collection }}</strong>: {{ $model->getMedia($collection)->count() }} items</li>
                @endforeach
            </ul>

            @if(collect($errors)->count() > 0)
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
                            ? '📦 Show Debug Info'
                            : '📦 Hide Debug Info';
                    });
                }
            });
        });
    </script>
@endonce
