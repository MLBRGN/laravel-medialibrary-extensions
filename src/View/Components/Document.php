<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Document extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public array $icon;

    /** @var string[] */
    public array $officeMimes = [
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/rtf',
        'text/rtf',
        'application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.spreadsheet',
        'application/vnd.oasis.opendocument.presentation',
    ];

    public function __construct(
        public Media|TemporaryUpload $medium,
        public bool $previewMode = true,
        array $options = [],
        public string $alt = ''
    ) {
        parent::__construct();
        $this->domId = 'mle-document-'.$medium->id;
        $this->options = $options;
        $mimetype = $medium->mime_type;

        $iconMap = [
            // Videos
            'video/mp4' => [
                'name' => config('medialibrary-extensions.icons.video-file'),
                'title' => __('medialibrary-extensions::messages.mp4-video'),
            ],
            'video/quicktime' => [
                'name' => config('medialibrary-extensions.icons.video-file'),
                'title' => __('medialibrary-extensions::messages.quicktime-video'),
            ],
            'video/webm' => [
                'name' => config('medialibrary-extensions.icons.video-file'),
                'title' => __('medialibrary-extensions::messages.webm-video'),
            ],

            // Documents
            'application/pdf' => [
                'name' => config('medialibrary-extensions.icons.pdf-document'),
                'title' => __('medialibrary-extensions::messages.pdf-document'),
            ],
            'application/msword' => [
                'name' => config('medialibrary-extensions.icons.wordprocessing-document'),
                'title' => __('medialibrary-extensions::messages.word-document'),
            ],
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => [
                'name' => config('medialibrary-extensions.icons.wordprocessing-document'),
                'title' => __('medialibrary-extensions::messages.word-document'),
            ],
            'application/vnd.ms-excel' => [
                'name' => config('medialibrary-extensions.icons.spreadsheet-document'),
                'title' => __('medialibrary-extensions::messages.excel-document'),
            ],
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => [
                'name' => config('medialibrary-extensions.icons.spreadsheet-document'),
                'title' => __('medialibrary-extensions::messages.excel-document'),
            ],
            'application/vnd.ms-powerpoint' => [
                'name' => config('medialibrary-extensions.icons.presentation-document'),
                'title' => __('medialibrary-extensions::messages.powerpoint-document'),
            ],
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => [
                'name' => config('medialibrary-extensions.icons.presentation-document'),
                'title' => __('medialibrary-extensions::messages.powerpoint-document'),
            ],

            // Audio
            'audio/mpeg' => [
                'name' => config('medialibrary-extensions.icons.audio-file'),
                'title' => __('medialibrary-extensions::messages.mp3-audio'),
            ],
            'audio/ogg' => [
                'name' => config('medialibrary-extensions.icons.audio-file'),
                'title' => __('medialibrary-extensions::messages.ogg-audio'),
            ],
            'audio/wav' => [
                'name' => config('medialibrary-extensions.icons.audio-file'),
                'title' => __('medialibrary-extensions::messages.wav-audio'),
            ],
            'audio/webm' => [
                'name' => config('medialibrary-extensions.icons.audio-file'),
                'title' => __('medialibrary-extensions::messages.webm-audio'),
            ],
        ];

        $this->icon = $iconMap[$mimetype] ?? [
            'name' => config('medialibrary-extensions.icons.unknown_file_mimetype'),
            'title' => __('medialibrary-extensions::messages.unknown_file_mimetype'),
        ];

        $this->resolveConfig();
    }

    protected function domIdSuffix(): string {
        return 'document';
    }

    public function render(): View
    {
        return $this->renderView('', null, false, 'medialibrary-extensions::components.document');
    }
}
