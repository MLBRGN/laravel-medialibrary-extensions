<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Document extends Component
{
    public array $icon;

    public function __construct(
        public Media|TemporaryUpload $medium,
        public string $alt = ''// set alt to empty for when none provided
    ) {

        $mimetype = $medium->mime_type;

        $iconMap = [
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

        $this->icon = $iconMap[$mimetype] ?? [
            'name' => config('media-library-extensions.icons.unknown-file-mime-type'),
            'title' => __('media-library-extensions::messages.unknown-file-mime-type'),
        ];

    }

    public function render(): View
    {
        return view('media-library-extensions::components.document');
    }
}
