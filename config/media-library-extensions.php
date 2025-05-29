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
    //    'frontend_theme' => 'plain',

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
    | Max Upload Sizes (in kilobytes)
    |--------------------------------------------------------------------------
    |
    | Define the maximum allowed upload sizes for various media types.
    | Example: 16,384 KB = 16 MB
    |
    */

    'max_upload_sizes' => [
        'image' => env('MLE_MAX_FILE_SIZE', 1024 * 1024 * 16),
        'video' => env('MLE_MAX_FILE_SIZE', 1024 * 1024 * 16),
        'document' => env('MLE_MAX_FILE_SIZE', 1024 * 1024 * 16),
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Mime Types
    |--------------------------------------------------------------------------
    |
    | Define allowed mime types for uploads. This makes it easy to reuse.
    |
    */

    'allowed_mimes' => [
        'image' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/bmp',
            'image/heic',
            'image/avif',
        ],
        'video' => [
            'mp4',
            'mov',
            'avi',
        ],
        'document' => [
            'pdf',
            'doc',
            'docx',
        ],
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
        'setup-as-main' => 'bi-star',
        'set-as-main' => 'bi-star-fill',
    ],

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

];
