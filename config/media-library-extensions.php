<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Debug mode
     |--------------------------------------------------------------------------
     |
     | Debug mode, is not honored on production environment sites!
     |
     */

    'debug' => env('MEDIA_LIBRARY_EXTENSIONS_DEBUG', false),

    /*
     |--------------------------------------------------------------------------
     | Demo mode
     |--------------------------------------------------------------------------
     |
     | When enabled uses a separate mysql-database.
     | Used by the demo pages to not interfere with the host application's database
     |
     */

    'demo_mode' => env('MEDIA_LIBRARY_EXTENSIONS_DEMO_MODE', false),

    /*
    |--------------------------------------------------------------------------
    | Frontend theme
    |--------------------------------------------------------------------------
    |
    | The theme to be used for the frontend.
    |
    */

    'frontend_theme' => env('MEDIA_LIBRARY_EXTENSIONS_FRONTEND_THEME', 'bootstrap-5'),

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


    'route_prefix' => env('MEDIA_LIBRARY_EXTENSIONS_ROUTE_PREFIX', 'mlbrgn-mle'),

    /*
    |--------------------------------------------------------------------------
    | Route middleware
    |--------------------------------------------------------------------------
    |
    | middleware to use on package's routes.
    | !!! NOTE web is required for status messages to work
    | By default the routes are only accessible for authenticated users
    |
    */

    'route_middleware' => explode(',', env('MEDIA_LIBRARY_EXTENSIONS_ROUTE_MIDDLEWARE', 'web,auth')),

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
    |  XMLHttpRequest support
    |--------------------------------------------------------------------------
    |
    | Use XMLHttpRequest (ajax) to submit forms.
    | Benefit: this works in nested forms, when setting this value to false,
    | submitting forms inside components won't work when nested in a form
    |
    */

    'use_xhr' =>  env('MEDIA_LIBRARY_EXTENSIONS_USE_XHR', true),

    /*
    |--------------------------------------------------------------------------
    | Max Upload Size (in kilobytes)
    |--------------------------------------------------------------------------
    |
    | Define the maximum allowed upload size
    | Example: 16,384 KB = 16 MB
    |
    */

    'max_upload_size' => env('MEDIA_LIBRARY_EXTENSIONS_MAX_FILE_SIZE', 1024 * 1024 * 16),

    /*
    |--------------------------------------------------------------------------
    | Max items in collection
    |--------------------------------------------------------------------------
    |
    | The maximum items in a single media collection
    |
    */
    'max_items_in_collection' => env('MEDIA_LIBRARY_EXTENSIONS_MAX_ITEMS_IN_COLLECTION', 10),

    /*
    |--------------------------------------------------------------------------
    | Allowed Mime Types
    |--------------------------------------------------------------------------
    |
    | Define allowed mime types for uploads. This makes it easy to reuse.
    |
    */

    'allowed_mimetypes' => [
        'image' => explode(',', env('MEDIA_LIBRARY_EXTENSIONS_ALLOWED_IMAGE_MIMETYPES', 'image/jpeg,image/png,image/gif,image/bmp,image/webp,image/heic,image/avif')),
        'video' => explode(',', env('MEDIA_LIBRARY_EXTENSIONS_ALLOWED_VIDEO_MIMETYPES', 'video/mp4,video/quicktime,video/x-msvideo')),
        'document' => explode(',', env('MEDIA_LIBRARY_EXTENSIONS_ALLOWED_DOCUMENT_MIMETYPES', 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document')),
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

    'max_image_width' => env('MEDIA_LIBRARY_EXTENSIONS_MAX_IMAGE_WIDTH', 1920),
    'max_image_height' => env('MEDIA_LIBRARY_EXTENSIONS_MAX_IMAGE_HEIGHT', 1080),

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
        'delete' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_DELETE', 'bi-trash3'),
        'setup_as_main' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_SETUP_AS_MAIN', 'bi-star'),
        'set-as-main' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_SET_AS_MAIN', 'bi-star-fill'),
        'play_video' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_PLAY_VIDEO', 'bi-play-fill'),
        'close' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_CLOSE', 'bi-x-lg'),
        'next' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_NEXT', 'bi-chevron-right'),
        'prev' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_PREV', 'bi-chevron-left'),
        'pdf-document' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_PDF', 'bi-file-earmark-pdf'),
        'word-document' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_WORD', 'bi-file-earmark-word'),
        'unknown-file-mime-type' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_UNKNOWN', 'bi-file-earmark'),
    ],

    /*
    |--------------------------------------------------------------------------
    |--------------------------------------------------------------------------
    |
    | Carousel options
    |
    */

    'carousel_ride' => env('MEDIA_LIBRARY_EXTENSIONS_CAROUSEL_RIDE', false), // Automatically switch slides
    'carousel_ride_interval' => env('MEDIA_LIBRARY_EXTENSIONS_CAROUSEL_RIDE_INTERVAL', 3000), // Time between slides
    'carousel_ride_only_after_interaction' => env('MEDIA_LIBRARY_EXTENSIONS_CAROUSEL_RIDE_ONLY_AFTER_INTERACTION', false), // Only slide after first interaction with carousel
    'carousel_fade' => env('MEDIA_LIBRARY_EXTENSIONS_CAROUSEL_FADE', false), // slide effect true for "fade" false for "slide" (bootstrap only)

    /*
    |--------------------------------------------------------------------------
    | Show status of requests inside component
    |--------------------------------------------------------------------------
    |
    | Used internally to flash messages to the session, this prefix is used
    | as a prefix to prevent session messages to clash with the host application
    |
    */

    'show_status' => env('MEDIA_LIBRARY_EXTENSIONS_SHOW_STATUS', true),

    /*
    |--------------------------------------------------------------------------
    | `Status session prefix
    |--------------------------------------------------------------------------
    |
    | This prefix is used to prevent status session keys to clash with the
    | host application
    |
    */

    'status_session_prefix' => env('MEDIA_LIBRARY_EXTENSIONS_STATUS_SESSION_PREFIX', 'laravel-medialibrary-extensions.status'),

    /*
    |--------------------------------------------------------------------------
    | YouTube support
    |--------------------------------------------------------------------------
    |
    | enable or disable YouTube support in carousel and modal
    |
    */

    'youtube_support_enabled' => env('MEDIA_LIBRARY_EXTENSIONS_YOUTUBE_SUPPORT_ENABLED', true),

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
