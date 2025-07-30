@forelse ($media as $medium)
    <div
        {{ $attributes->class([
            'mlbrgn-mle-component',
             'theme-'.$theme,
             'media-manager-preview-media-container'
        ]) }}
        data-set-as-first-route="{{ route(mle_prefix_route('set-as-first'), $medium) }}"
        data-destroy-route="{{ route(mle_prefix_route('medium-destroy'), $medium) }}"
    >
        @if($medium->hasExtraProperty('youtube-id'))
            <div
                class="media-manager-preview-item-container"
                data-bs-toggle="modal"
                data-bs-target="#{{$id}}-modal"
            >
                <x-mle-video-youtube
                    class="mle-video-responsive mle-cursor-zoom-in"
                    :medium="$medium"
                    :preview="true"
                    :youtube-id="$medium->getCustomProperty('youtube-id')"
                    :youtube-params="[]"
                    data-bs-target="#{{$id}}-modal-carousel"
                    data-bs-slide-to="{{ $loop->index }}"
                />
            </div>
        @else
            @if($medium->isDocument())
                <div
                    data-bs-toggle="modal"
                    data-bs-target="#{{$id}}-modal"
                    class="media-manager-preview-item-container"
                >
                    <x-mle-document :medium="$medium"
                                    class="previewed-document mle-cursor-zoom-in"
                                    data-bs-target="#{{ $id }}-modal-carousel"
                                    data-bs-slide-to="{{ $loop->index }}"
                    />
                </div>
            @elseif($medium->isImage())
                <div
                    data-bs-toggle="modal"
                    data-bs-target="#{{$id}}-modal"
                    class="media-manager-preview-item-container"
                >
                <img src="{{ $medium->getUrlAttribute() }}" class="media-manager-image-preview mle-cursor-zoom-in" alt="{{ $medium->original_filename }}"/>
                  
                </div>
{{--                <x-mle-image-editor-modal id="{{ $id }}" :medium="$medium" :model="$model"/>--}}
            @else
                no suitable type
            @endif
        @endif
{{--        @php--}}
{{--            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));--}}
{{--        @endphp--}}

{{--        @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']))--}}
{{--            <div class="media-manager-preview-item-container">--}}
{{--                <img--}}
{{--                    src="{{ $file['url'] }}"--}}
{{--                    alt="{{ $file['name'] }}"--}}
{{--                    class="media-manager-image-preview mle-cursor-zoom-in"--}}
{{--                    draggable="false"--}}
{{--                />--}}
{{--            </div>--}}
{{--        @elseif (in_array($ext, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt']))--}}
{{--            <div class="media-manager-preview-item-container">--}}
{{--                <a href="{{ $file['url'] }}" target="_blank" class="previewed-document">--}}
{{--                    {{ $file['name'] }}--}}
{{--                </a>--}}
{{--            </div>--}}
{{--        @else--}}
{{--            <div>No preview available for {{ $file['name'] }}</div>--}}
{{--        @endif--}}

{{--        @if ($destroyEnabled)--}}
{{--            --}}{{-- TODO: Add delete button or other UI for temporary files if desired --}}
{{--        @endif--}}
    </div>
@empty
    <div class="mlbrgn-mle-component media-manager-preview-media-container media-manager-no-media">
        <span class="mle-no-media">{{ __('media-library-extensions::messages.no_media') }}</span>
    </div>
@endforelse
