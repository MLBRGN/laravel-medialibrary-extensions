@if(config('media-library-extensions.debug'))
    <div class="mle-debug">
        <span>Theme: {{ $theme }}.</span>
        <span>XHR enabled: {{ config('media-library-extensions.use_xhr') ? 'Yes' : 'No' }}.</span>
        <span>Demo mode enabled: {{ config('media-library-extensions.demo_mode') ? 'Yes' : 'No' }}.</span>
        <span>Show status: {{ config('media-library-extensions.show_status') ? 'Yes' : 'No' }}.</span>
        <span>YouTube support enabled: {{ config('media-library-extensions.youtube_support_enabled') ? 'Yes' : 'No' }}.</span>
        <span>Allowed mimetypes: {{ collect(config('media-library-extensions.allowed_mimetypes'))->flatten()->join(', ') }}.</span>

        @if(collect($errors)->count() > 0)
            <div>
                <p>⚠️{{ __('media-library-extensions::messages.warning') }}</p>
                <ul>
                    @foreach($errors as $error)
                        <li>
                            {!! $error !!}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif