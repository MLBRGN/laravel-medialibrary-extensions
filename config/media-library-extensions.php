<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Debug mode
     |--------------------------------------------------------------------------
     |
     | Debug mode, disable on production
     |
     */

    'debug' => false,

    /*
    |--------------------------------------------------------------------------
    | Frontend theme
    |--------------------------------------------------------------------------
    |
    | The theme to be used for the frontend.
    |
    */

    'frontend_theme' => 'bootstrap-5',
//        'frontend_theme' => 'plain',

    /*
    |--------------------------------------------------------------------------
    | Supported frontend themes
    |--------------------------------------------------------------------------
    |
    | "plain" => plain HTML, CSS and JavaScript
    | "bootstrap-5" – Use Bootstrap 5. Requires Bootstrap to be installed in the host/consuming project.
    |
    */

    'supported_frontend_themes' => ['plain', 'bootstrap-5'],

    /*
    |--------------------------------------------------------------------------
    | Route prefix
    |--------------------------------------------------------------------------
    |
    | Prefix added to routes to prevent collisions
    |
    */

    'route_prefix' => env('MEDIA_LIBRARY_EXTENSION_ROUTE_PREFIX', 'mlbrgn-mle'),

    /*
    |--------------------------------------------------------------------------
    | Upload field names
    |--------------------------------------------------------------------------
    |
    | The field names used for uploading media
    |
    */

    'upload_field_name_single' => 'medium',
    'upload_field_name_multiple' => 'media',
    'upload_field_name_youtube' => 'youtube_url',

    /*
    |--------------------------------------------------------------------------
    | Max Upload Size (in kilobytes)
    |--------------------------------------------------------------------------
    |
    | Define the maximum allowed upload size
    | Example: 16,384 KB = 16 MB
    |
    */

    'max_upload_size' => env('MLE_MAX_FILE_SIZE', 1024 * 1024 * 16),

    /*
    |--------------------------------------------------------------------------
    | Max items in collection
    |--------------------------------------------------------------------------
    |
    | The maximum items in a single media collection
    |
    */
    'max_items_in_collection' => 10,

    /*
    |--------------------------------------------------------------------------
    | Allowed Mime Types
    |--------------------------------------------------------------------------
    |
    | Define allowed mime types for uploads. This makes it easy to reuse.
    |
    */

    'allowed_mimetypes' => [
        'image' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/bmp',
            'image/webp',
            'image/heic',
            'image/avif',
        ],
        'video' => [
            'video/mp4',
            'video/quicktime',
            'video/x-msvideo',
        ],
        'document' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
    ],

    'mimeTypeLabels' => [
        'image/jpeg' => 'JPEG',
        'image/png' => 'PNG',
        'image/gif' => 'GIF',
        'image/bmp' => 'BMP',
        'image/webp' => 'WebP',
        'image/heic' => 'HEIC',
        'image/avif' => 'AVIF',
        'video/mp4' => 'MP4 video',
        'video/quicktime' => 'QuickTime video',
        'video/x-msvideo' => 'AVI video',
        'application/pdf' => 'PDF document',
        'application/msword' => 'Word document (.doc)',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Word document (.docx)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Image dimension restrictions
    |--------------------------------------------------------------------------
    |
    | Define max image dimensions
    |
    */

    'max_image_width' => env('MLE_MAX_IMAGE_WIDTH', 1920),
    'max_image_height' => env('MLE_MAX_IMAGE_HEIGHT', 1080),

    /*
    |--------------------------------------------------------------------------
    | Icons (uses blade-ui-kit/blade icons
    |--------------------------------------------------------------------------
    |
    | Set icons to be used.
    | Defaults to Bootstrap icons
    |
    */

    'icons' => [
        'delete' => 'bi-trash3',
        'setup_as_main' => 'bi-star',
        'set-as-main' => 'bi-star-fill',
        'play_video' => 'bi-play-fill',
        'close' => 'bi-x-lg',
        'next' => 'bi-chevron-right',
        'prev' => 'bi-chevron-left',
    ],

    /*
    |--------------------------------------------------------------------------
    |--------------------------------------------------------------------------
    |
    | Carousel options
    |
    */

    'carousel_ride' => false, // Automatically switch slides
    'carousel_ride_interval' => 3000, // Time between slides
    'carousel_ride_only_after_interaction' => false, // Only slide after first interaction with carousel
    'carousel_fade' => false, // slide effect true for "fade" false for "slide" (bootstrap only)

    /*
    |--------------------------------------------------------------------------
    | Show status of requests inside component
    |--------------------------------------------------------------------------
    |
    | Used internally to flash messages to the session, this prefix is used
    | as a prefix to prevent session messages to clash with the host application
    |
    */

    'show_status_in_components' => true,

    /*
    |--------------------------------------------------------------------------
    | `Status session prefix
    |--------------------------------------------------------------------------
    |
    | This prefix is used to prevent status session keys to clash with the
    | host application
    |
    */

    'status_session_prefix' => 'laravel-medialibrary-extensions.status',

    /*
    |--------------------------------------------------------------------------
    | YouTube support
    |--------------------------------------------------------------------------
    |
    | enable or disable YouTube support in carousel and modal
    |
    */

    'youtube_support_enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Default YouTube params
    |--------------------------------------------------------------------------
    |
    | The parameters sent to the YouTube player (configuration of YouTube player)
    |
    */

    'default_youtube_params' => [
        'autoplay' => 1,
        'mute' => 0,
        'loop' => 0,
        'controls' => 0,
        'modestbranding' => 1,
        'playsinline' => 1,
        'rel' => 0,
        'enablejsapi' => 1,
    ]

];
