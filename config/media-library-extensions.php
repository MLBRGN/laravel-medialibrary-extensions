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
     | Demo pages
     |--------------------------------------------------------------------------
     |
     | Enable / Disable demo pages
     |
     */

    'demo_pages_enabled' => env('MEDIA_LIBRARY_EXTENSIONS_DEMO_PAGES_ENABLED', false),

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
    | Temporary Uploads
    |--------------------------------------------------------------------------
    |
    | During "create" forms, the model does not yet exist, so uploaded media
    | files cannot be immediately associated with it. Instead, they are stored
    | temporarily until the model is created and the media can be attached.
    |
    | You can configure the storage disk and path used for these temporary files.
    |
    | IMPORTANT: The configured disk and path must be publicly accessible if you
    | want to display preview images or file thumbnails during the upload process.
    | Typically, this means using the `public` disk and ensuring a valid symlink
    | (via `php artisan storage:link`) exists from public/storage.
    |
    */

    'temporary_upload_disk' => env('MEDIA_LIBRARY_EXTENSIONS_TEMPORARY_UPLOAD_DISK', 'public'),
    'temporary_upload_path' => env('MEDIA_LIBRARY_EXTENSIONS_TEMPORARY_UPLOAD_PATH', 'temp/media-library-extensions'),

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

    'use_xhr' => env('MEDIA_LIBRARY_EXTENSIONS_USE_XHR', true),

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
    'max_items_in_shared_media_collections' => env('MEDIA_LIBRARY_EXTENSIONS_MAX_ITEMS_IN_SHARED_MEDIA_COLLECTIONS', 10),

    /*
    |--------------------------------------------------------------------------
    | Allowed Mime Types
    |--------------------------------------------------------------------------
    |
    | Define allowed mime types for uploads. This makes it easy to reuse.
    |
    | Legacy formats (not recommended anymore for use on the web)
    | image/bmp, image/x-ms-bmp
    | image/tiff,
    | image/x-icon, image/vnd.microsoft.icon (Favicons)
    |
    | Experimental formats:
    | image/jxl JPEG XL (only safari)
    |
    */

    'allowed_mimetypes' => [
        'image' => explode(',', env(
            'MEDIA_LIBRARY_EXTENSIONS_ALLOWED_IMAGE_MIMETYPES',
            'image/jpeg,image/png,image/gif,image/bmp,image/webp,image/heic,image/avif'
        )),

        'video' => explode(',', env(
            'MEDIA_LIBRARY_EXTENSIONS_ALLOWED_VIDEO_MIMETYPES',
            'video/mp4,video/quicktime,video/webm'
        )),

        'document' => explode(',', env(
            'MEDIA_LIBRARY_EXTENSIONS_ALLOWED_DOCUMENT_MIMETYPES',
            'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation'
        )),

        'audio' => explode(',', env(
            'MEDIA_LIBRARY_EXTENSIONS_ALLOWED_AUDIO_MIMETYPES',
            'audio/mpeg,audio/ogg,audio/wav,audio/webm'
        )),
    ],

    'mimetype_labels' => [
        // Images
        'image/jpeg' => 'mimetypes.jpeg',
        'image/png' => 'mimetypes.png',
        'image/gif' => 'mimetypes.gif',
        'image/bmp' => 'mimetypes.bmp',
        'image/webp' => 'mimetypes.webp',
        'image/heic' => 'mimetypes.heic',
        'image/avif' => 'mimetypes.avif',

        // Video
        'video/mp4' => 'mimetypes.video_mp4',
        'video/quicktime' => 'mimetypes.video_quicktime',
        'video/webm' => 'mimetypes.video_webm',

        // Documents
        'application/pdf' => 'mimetypes.pdf',
        'application/msword' => 'mimetypes.doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'mimetypes.docx',
        'application/vnd.ms-excel' => 'mimetypes.xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'mimetypes.xlsx',
        'application/vnd.ms-powerpoint' => 'mimetypes.ppt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'mimetypes.pptx',

        // Audio
        'audio/mpeg' => 'mimetypes.audio_mp3',
        'audio/ogg' => 'mimetypes.audio_ogg',
        'audio/wav' => 'mimetypes.audio_wav',
        'audio/webm' => 'mimetypes.audio_webm',
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
        'wordprocessing-document' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_WORD', 'bi-file-earmark-richtext'),
        'spreadsheet-document' => env('MEDIA_LIBRARY_EXTENSIONS_EDIT', 'bi-file-earmark-spreadsheet'),
        'presentation-document' => env('MEDIA_LIBRARY_EXTENSIONS_EDIT', 'bi-file-earmark-slides'),
        'unknown_file_mimetype' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_UNKNOWN', 'bi-file-earmark'),
        'edit' => env('MEDIA_LIBRARY_EXTENSIONS_EDIT', 'bi-pencil'),
        'video-file' => env('MEDIA_LIBRARY_EXTENSIONS_EDIT', 'bi-file-earmark-play'),
        'audio-file' => env('MEDIA_LIBRARY_EXTENSIONS_EDIT', 'bi-file-earmark-music'),
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
    |--------------------------------------------------------------------------
    |
    | Media preview modal options
    |
    */
    'preview_modal_embed_pdf' => env('MEDIA_LIBRARY_EXTENSIONS_PREVIEW_MODAL_EMBED_PDF', false),

    /*
    |--------------------------------------------------------------------------
    | Status messages
    |--------------------------------------------------------------------------
    |
    | show_status: Used internally to flash messages to the session, this prefix is used
    | as a prefix to prevent session messages to clash with the host application
    | status_message_timeout: Control the duration the status message stays on screen
    */

    'show_status' => env('MEDIA_LIBRARY_EXTENSIONS_SHOW_STATUS', true),
    'status_message_timeout' => env('MEDIA_LIBRARY_EXTENSIONS_STATUS_MESSAGE_TIMEOUT', 4000),// in milliseconds

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
    | Temporary files
    |--------------------------------------------------------------------------
    |
    | when no model exists at the moment of uploading (when the model is not yet created)
    | temporary files are used
    |
    */

    'temp_database_name' => 'media_demo',
    'temp_media_path' => 'tmp-media',
    'temp_media_lifetime' => 24, // hours

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
    ],

    /*
    |--------------------------------------------------------------------------
    | Media manager selection
    |--------------------------------------------------------------------------
    |
    | Single select - only allow 1 medium to be selected at a time (radio instead
    } of checkbox)
    |
    */

    'single_select' => env('MEDIA_LIBRARY_EXTENSIONS_SINGLE_SELECT', true),

    /*
   |--------------------------------------------------------------------------
   | Use external document viewer
   |--------------------------------------------------------------------------
   |
   | values:
   | google-docs = Google Docs Viewer (DOC, DOCX, ODT, PPT, XLS, etc.)
   | microsoft-office = Microsoft Office Online Viewer (supports DOCX, PPTX, XLSX)
   | pass empty string for no external document viewer
   |
   */
    'use_external_document_viewer' => env('MEDIA_LIBRARY_EXTENSIONS_USE_EXTERNAL_DOCUMENT_VIEWER', '')
];
