@php
    $mime = $medium->mime_type;

    $iconMap = [
        'application/pdf' => [
            'name' => config('media-library-extensions.icons.pdf-document'),
            'title' => __('media-library-extensions::messages.pdf-document'),
        ],
        'application/msword' => [
            'name' => config('media-library-extensions.icons.word-document'),
            'title' => __('media-library-extensions::messages.word-document'),
        ],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => [
            'name' => config('media-library-extensions.icons.word-document'),
            'title' => __('media-library-extensions::messages.word-document'),
        ],
    ];

    $icon = $iconMap[$mime] ?? [
        'name' => config('media-library-extensions.icons.unknown-file-mime-type'),
        'title' => __('media-library-extensions::messages.unknown-file-mime-type'),
    ];
@endphp

<div {{ $attributes->merge(['class' => 'mle-document']) }}>
    <div class="mle-document-preview">
        <a href="{{ $medium->getUrl() }}" target="_blank">
            <x-mle-partial-icon
                :name="$icon['name']"
                :title="$icon['title']"
            />
{{--        <p>{{ getHumanMimeTypeLabel($mime) }}</p>--}}
{{--        {{ __('media-library-extensions::messages.show_document') }}--}}
        </a>
    </div>
</div>
