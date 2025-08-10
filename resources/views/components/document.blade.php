@php
    use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;$mime = $medium->mime_type;

    $iconMap = [
        
            // Images
//    'image/jpeg' => [
//        'name' => config('media-library-extensions.icons.jpeg-image'),
//        'title' => __('media-library-extensions::messages.jpeg-image'),
//    ],
//    'image/png' => [
//        'name' => config('media-library-extensions.icons.png-image'),
//        'title' => __('media-library-extensions::messages.png-image'),
//    ],
//    'image/gif' => [
//        'name' => config('media-library-extensions.icons.gif-image'),
//        'title' => __('media-library-extensions::messages.gif-image'),
//    ],
//    'image/bmp' => [
//        'name' => config('media-library-extensions.icons.bmp-image'),
//        'title' => __('media-library-extensions::messages.bmp-image'),
//    ],
//    'image/webp' => [
//        'name' => config('media-library-extensions.icons.webp-image'),
//        'title' => __('media-library-extensions::messages.webp-image'),
//    ],
//    'image/heic' => [
//        'name' => config('media-library-extensions.icons.heic-image'),
//        'title' => __('media-library-extensions::messages.heic-image'),
//    ],
//    'image/avif' => [
//        'name' => config('media-library-extensions.icons.avif-image'),
//        'title' => __('media-library-extensions::messages.avif-image'),
//    ],

    // Videos
    'video/mp4' => [
        'name' => config('media-library-extensions.icons.video-file'),
        'title' => __('media-library-extensions::messages.mp4-video'),
    ],
    'video/quicktime' => [
        'name' => config('media-library-extensions.icons.video-file'),
        'title' => __('media-library-extensions::messages.quicktime-video'),
    ],
    'video/webm' => [
        'name' => config('media-library-extensions.icons.video-file'),
        'title' => __('media-library-extensions::messages.webm-video'),
    ],

    // Documents
    'application/pdf' => [
        'name' => config('media-library-extensions.icons.pdf-document'),
        'title' => __('media-library-extensions::messages.pdf-document'),
    ],
    'application/msword' => [
        'name' => config('media-library-extensions.icons.wordprocessing-document'),
        'title' => __('media-library-extensions::messages.word-document'),
    ],
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => [
        'name' => config('media-library-extensions.icons.wordprocessing-document'),
        'title' => __('media-library-extensions::messages.word-document'),
    ],
    'application/vnd.ms-excel' => [
        'name' => config('media-library-extensions.icons.spreadsheet-document'),
        'title' => __('media-library-extensions::messages.excel-document'),
    ],
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => [
        'name' => config('media-library-extensions.icons.spreadsheet-document'),
        'title' => __('media-library-extensions::messages.excel-document'),
    ],
    'application/vnd.ms-powerpoint' => [
        'name' => config('media-library-extensions.icons.presentation-document'),
        'title' => __('media-library-extensions::messages.powerpoint-document'),
    ],
    'application/vnd.openxmlformats-officedocument.presentationml.presentation' => [
        'name' => config('media-library-extensions.icons.presentation-document'),
        'title' => __('media-library-extensions::messages.powerpoint-document'),
    ],

    // Audio
    'audio/mpeg' => [
        'name' => config('media-library-extensions.icons.audio-file'),
        'title' => __('media-library-extensions::messages.mp3-audio'),
    ],
    'audio/ogg' => [
        'name' => config('media-library-extensions.icons.audio-file'),
        'title' => __('media-library-extensions::messages.ogg-audio'),
    ],
    'audio/wav' => [
        'name' => config('media-library-extensions.icons.audio-file'),
        'title' => __('media-library-extensions::messages.wav-audio'),
    ],
    'audio/webm' => [
        'name' => config('media-library-extensions.icons.audio-file'),
        'title' => __('media-library-extensions::messages.webm-audio'),
    ],
    ];

    $icon = $iconMap[$mime] ?? [
        'name' => config('media-library-extensions.icons.unknown-file-mime-type'),
        'title' => __('media-library-extensions::messages.unknown-file-mime-type'),
    ];
@endphp

<div {{ $attributes->merge(['class' => 'mle-document']) }}>
    <div class="mle-document-preview">
        @if($medium instanceof TemporaryUpload)
            <a href="{{ $medium->getFullUrl() }}" target="_blank">
                <x-mle-partial-icon
                    :name="$icon['name']"
                    :title="$icon['title']"
                />
            </a>
        @else
            <a href="{{ $medium->getUrl() }}" target="_blank">
                <x-mle-partial-icon
                    :name="$icon['name']"
                    :title="$icon['title']"
                />
                {{--        <p>{{ getHumanMimeTypeLabel($mime) }}</p>--}}
                {{--        {{ __('media-library-extensions::messages.show_document') }}--}}
            </a>
        @endif
    </div>
</div>
