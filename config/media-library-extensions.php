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
    | Originals (for lab functionality)
    |--------------------------------------------------------------------------
    |
    | Store originals whenever a medium is uploaded, so that it's possible
    | to restore the base image used by the media library to the original one
    */

    'store_originals' => env('MEDIA_LIBRARY_EXTENSIONS_STORE_ORIGINALS', true),

    /*
    |--------------------------------------------------------------------------
    | Demo database
    |--------------------------------------------------------------------------
    |
    | Database configuration for demo pages
    |
    */

    'demo_database_name' => 'media_demo',

    /*
    |--------------------------------------------------------------------------
    | Disk configuration
    |--------------------------------------------------------------------------
    |
    | THe extra disks used by this package
    |
    | "temporary":
    | During "create" forms, the model does not yet exist, so uploaded media
    | files cannot be immediately associated with it. Instead, they are stored
    | temporarily until the model is created and the media can be attached to the
    | model.
    |
    | "originals":
    | Used to store originals (when enabled), so that restoring a medium is possible
    | used by the media lab functionality
    |
    | "demo":
    | use by the demo pages as to not pollute the real or temporary media folder
    |
    */

    'media_disks' => [
        'originals' => 'media_originals',
        'demo' => 'media_demo',
        'temporary' => 'media_temporary',
    ],

    'disks' => [
        'media_originals' => [
            'driver' => 'local',
            'root' => storage_path('app/public/media_originals'),
            'url' => env('APP_URL').'/storage/media_originals', // URL to access files
            'visibility' => 'public',
        ],

        'media_demo' => [
            'driver' => 'local',
            'root' => storage_path('app/public/media_demo'),
            'url' => env('APP_URL').'/storage/media_demo', // URL to access files
            'visibility' => 'public',
        ],

        'media_temporary' => [
            'driver' => 'local',
            'root' => storage_path('app/public/media_temporary'),
            'url' => env('APP_URL').'/storage/media_temporary', // URL to access files
            'visibility' => 'public',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Schedule
    |--------------------------------------------------------------------------
    |
    | Temporary uploads can be cleaned up
    |
    */

//    'schedule' => [
//        'cleanup' => [
//            'enabled' => true,
//            'frequency' => 'everyMinute', // or 'daily', 'everyTenMinutes', etc.
//            'pingback_url' => null, // optional pingback url (for use with "oh dear" for example)
//        ],
//    ],

    'schedule' => [
        'cleanup' => [
            'enabled' => true,
            'frequency' => 'everyDay',
            'pingback_success' => env('MLE_CLEANUP_SUCCESS_PING', '')
        ],
    ],

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
            'image/jpeg,'.
            'image/png,'.
            'image/gif,'.
            'image/bmp,'.
            'image/webp,'.
            'image/heic,'.
            'image/avif'
        )),

        'video' => explode(',', env(
            'MEDIA_LIBRARY_EXTENSIONS_ALLOWED_VIDEO_MIMETYPES',
            'video/mp4,'.
            'video/quicktime,'.
            'video/webm'
        )),

        'document' => explode(',', env(
            'MEDIA_LIBRARY_EXTENSIONS_ALLOWED_DOCUMENT_MIMETYPES',
            'application/pdf,'.
            'application/msword,'.
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document,'.
            'application/vnd.ms-excel,'.
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,'.
            'application/vnd.ms-powerpoint,'.
            'application/vnd.openxmlformats-officedocument.presentationml.presentation,'.
            'application/rtf,text/rtf,'.
            'application/vnd.oasis.opendocument.text,'.
            'application/vnd.oasis.opendocument.spreadsheet,'.
            'application/vnd.oasis.opendocument.presentation'
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

        // PDF
        'application/pdf' => 'mimetypes.pdf',

        // Microsoft Word
        'application/msword' => 'mimetypes.doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'mimetypes.docx',

        // Microsoft Excel
        'application/vnd.ms-excel' => 'mimetypes.xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'mimetypes.xlsx',

        // Microsoft PowerPoint
        'application/vnd.ms-powerpoint' => 'mimetypes.ppt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'mimetypes.pptx',

        // RTF
        'application/rtf' => 'mimetypes.rtf',
        'text/rtf' => 'mimetypes.rtf',

        // OpenDocument formats
        'application/vnd.oasis.opendocument.text' => 'mimetypes.odt',
        'application/vnd.oasis.opendocument.spreadsheet' => 'mimetypes.ods',
        'application/vnd.oasis.opendocument.presentation' => 'mimetypes.odp',

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
    'max_image_width' => env('MEDIA_LIBRARY_EXTENSIONS_MAX_IMAGE_WIDTH', 7040), // high end smartphone
    'max_image_height' => env('MEDIA_LIBRARY_EXTENSIONS_MAX_IMAGE_HEIGHT', 3960), // high end smartphone
    'min_image_width' => env('MEDIA_LIBRARY_EXTENSIONS_MIN_IMAGE_WIDTH', 320),
    'min_image_height' => env('MEDIA_LIBRARY_EXTENSIONS_MIN_IMAGE_HEIGHT', 160),

    /*
    |--------------------------------------------------------------------------
    | Default Blade UI Kit Icon Set
    |--------------------------------------------------------------------------
    |
    | If multiple Blade UI icon sets are installed, you can set the preferred
    | one here. If null, the package will auto-detect a supported one.
    |
    */
    'blade_ui_kit_icon_set' => 'bootstrap-icons',

    /*
    |--------------------------------------------------------------------------
    | Icons (uses blade-ui-kit/blade icons
    |--------------------------------------------------------------------------
    |
    | Set icons to be used. Default values are for bootstrap icons, adjust if
    | using another icon set
    |
    */

    'icons' => [

        // Core actions
        'delete' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_DELETE', 'bi-trash3'),
        'setup_as_main' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_SETUP_AS_MAIN', 'bi-star'),
        'set-as-main' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_SET_AS_MAIN', 'bi-star-fill'),
        'restore' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_RESTORE', 'bi-arrow-counterclockwise'),
        'edit' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_EDIT', 'bi-pencil'),
        'check' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_RESTORE', 'bi-check-lg'),
        'close' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_CLOSE', 'bi-x-lg'),
        'x' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_RESTORE', 'bi-x'),

        // Navigation
        'next' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_NEXT', 'bi-chevron-right'),
        'prev' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_PREV', 'bi-chevron-left'),

        // Media
        'play_video' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_PLAY_VIDEO', 'bi-play-fill'),
        'video-file' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_VIDEO', 'bi-file-earmark-play'),
        'audio-file' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_AUDIO', 'bi-file-earmark-music'),

        // Files
        'pdf-document' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_PDF', 'bi-file-earmark-pdf'),
        'wordprocessing-document' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_WORD', 'bi-file-earmark-richtext'),
        'spreadsheet-document' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_SPREADSHEET', 'bi-file-earmark-spreadsheet'),
        'presentation-document' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_PRESENTATION', 'bi-file-earmark-slides'),
        'unknown_file_mimetype' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_UNKNOWN', 'bi-file-earmark'),

        // Misc
        'bug' => env('MEDIA_LIBRARY_EXTENSIONS_ICON_DEBUG', 'bi-bug'),
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
    'status_message_timeout' => env('MEDIA_LIBRARY_EXTENSIONS_STATUS_MESSAGE_TIMEOUT', 4000), // in milliseconds

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
    'use_external_document_viewer' => env('MEDIA_LIBRARY_EXTENSIONS_USE_EXTERNAL_DOCUMENT_VIEWER', ''),

    /*
    |--------------------------------------------------------------------------
    | DEVELOPER ONLY
    |--------------------------------------------------------------------------
    |
    | Only used by developer
    */

    'mle_using_local_package' => env('MLE_USING_LOCAL_PACKAGE', false),

    /*
    |--------------------------------------------------------------------------
    | Dynamic component resolving map
    |--------------------------------------------------------------------------
    |
    | Used to resolve dynamic component for media
    */

    'component_map' => [
        'image' => 'mle-image-responsive',
        'video' => 'mle-video',
        'audio' => 'mle-audio',
        'document' => 'mle-document',
        'youtube-video' => 'mle-video-youtube',
    ],

    /*
    |--------------------------------------------------------------------------
    | Aspect ratio settings (used by media lab)
    |--------------------------------------------------------------------------
    |
    | Used by media lab to display friendly aspect ratio labels
    */

    'available_aspect_ratios' => env('MLE_AVAILABLE_ASPECT_RATIOS', [

        // Free / unrestricted
        ['name' => 'free', 'label' => 'Free', 'value' => -1, 'active' => true],

        // Landscape
        ['name' => '19:10', 'label' => '19:10', 'value' => 19 / 10, 'active' => true], // Modern smartphones, some ultra-wide monitors (~1.9:1)
        ['name' => '16:10', 'label' => '16:10', 'value' => 16 / 10, 'active' => true], // Common laptop/monitor ratio (WUXGA, 1920×1200)
        ['name' => '16:9',  'label' => '16:9',  'value' => 16 / 9,  'active' => true], // Standard HD / Full HD / 4K video
        ['name' => '8:5',   'label' => '8:5',   'value' => 8 / 5,   'active' => true], // Print / banner
        ['name' => '7:5',   'label' => '7:5',   'value' => 7 / 5,   'active' => true], // Photo print
        ['name' => '5:4',   'label' => '5:4',   'value' => 5 / 4,   'active' => true], // Medium-format / SXGA
        ['name' => '5:3',   'label' => '5:3',   'value' => 5 / 3,   'active' => true], // Widescreen photography / 720p video (~1.67:1)
        ['name' => '4:3',   'label' => '4:3',   'value' => 4 / 3,   'active' => true], // Classic TV and photography (~1.33:1)
        ['name' => '3:2',   'label' => '3:2',   'value' => 3 / 2,   'active' => true], // 35mm film and DSLR sensors (~1.5:1)
        ['name' => '3:1',   'label' => '3:1',   'value' => 3 / 1,   'active' => true], // Panoramic
        ['name' => '2:1',   'label' => '2:1',   'value' => 2,       'active' => true], // Modern cinematic / ultra-wide web (~18:9 smartphones)
        ['name' => '6:5',   'label' => '6:5',   'value' => 6 / 5,   'active' => true], // Slightly taller than 5:4, uncommon artistic/print format
        ['name' => '2.35:1', 'label' => '2.35:1', 'value' => 2.35,  'active' => true], // Classic widescreen films (older Cinemascope standard)
        ['name' => '2.20:1', 'label' => '2.20:1', 'value' => 2.20,  'active' => true], // 70mm film format (Todd-AO, Panavision 70)
        ['name' => '3.28:1', 'label' => '3.28:1', 'value' => 3.28,  'active' => true], // Extreme panoramic
        ['name' => '2.50:1', 'label' => '2.50:1', 'value' => 2.50,  'active' => true], // Ultra-wide
        ['name' => '2.39:1', 'label' => '2.39:1', 'value' => 2.39,  'active' => true], // Modern cinema widescreen (Cinemascope / anamorphic)

        // Portrait (rotated +/-90°)
        ['name' => '10:19',  'label' => '10:19',  'value' => 10 / 19,  'active' => true], // Modern smartphones, some ultra-wide monitors (~1.9:1)
        ['name' => '10:16',  'label' => '10:16',  'value' => 10 / 16,  'active' => true], // Common laptop/monitor ratio (WUXGA, 1920×1200)
        ['name' => '9:16',   'label' => '9:16',   'value' => 9 / 16,   'active' => true], // Standard HD / Full HD / 4K video
        ['name' => '5:8',    'label' => '5:8',    'value' => 5 / 8,    'active' => true], // Print / banner
        ['name' => '5:7',    'label' => '5:7',    'value' => 5 / 7,    'active' => true], // Photo print
        ['name' => '4:5',    'label' => '4:5',    'value' => 4 / 5,    'active' => true], // Medium-format / SXGA
        ['name' => '3:5',    'label' => '3:5',    'value' => 3 / 5,    'active' => true], // Widescreen photography / 720p video (~1.67:1)
        ['name' => '3:4',    'label' => '3:4',    'value' => 3 / 4,    'active' => true], // Classic TV and photography (~1.33:1)
        ['name' => '2:3',    'label' => '2:3',    'value' => 2 / 3,    'active' => true], // 35mm film and DSLR sensors (~1.5:1)
        ['name' => '1:3',    'label' => '1:3',    'value' => 1 / 3,    'active' => true], // Panoramic
        ['name' => '1:2',    'label' => '1:2',    'value' => 1 / 2,    'active' => true], // Modern cinematic / ultra-wide web (~18:9 smartphones)
        ['name' => '5:6',    'label' => '5:6',    'value' => 5 / 6,    'active' => true], // Slightly taller than 5:4, uncommon artistic/print format
        ['name' => '1:2.35', 'label' => '1:2.35', 'value' => 1 / 2.35, 'active' => true], // Classic widescreen films (older Cinemascope standard)
        ['name' => '1:2.20', 'label' => '1:2.20', 'value' => 1 / 2.20, 'active' => true], // 70mm film format (Todd-AO, Panavision 70
        ['name' => '1:3.28', 'label' => '1:3.28', 'value' => 1 / 3.28, 'active' => true], // Extreme panoramic
        ['name' => '1:2.50', 'label' => '1:2.50', 'value' => 1 / 2.50, 'active' => true], // Ultra-wide
        ['name' => '1:2.39', 'label' => '1:2.39', 'value' => 1 / 2.39, 'active' => true], // Modern cinema widescreen (Cinemascope / anamorphic)

        // square
        ['name' => '1:1',    'label' => '1:1',    'value' => 1,        'active' => true],

        // Paper / ISO
        ['name' => '√2:1',   'label' => '√2:1',   'value' => 1.414,    'active' => true],
    ]),

    'default_forced_aspect_ratio' => '4:3',

    /*
            // 2.22:1 widescreen cinematic ratio 3 images found
            // 2.26
            // 2.28
            ['name' => '2:1',    'label' => '2:1',    'value' => 2,         'active' => true], // Modern cinematic / 18:9 smartphones / web ultra-wide
            ['name' => '19:10',  'label' => '19:10',  'value' => 19 / 10,   'active' => true], // Modern smartphones, some ultra-wide monitors (~1.9:1)
            ['name' => '16:10',  'label' => '16:10',  'value' => 16 / 10,   'active' => true], // Common laptop/monitor ratio (WUXGA 1920×1200)
            ['name' => '16:9',   'label' => '16:9',   'value' => 16 / 9,    'active' => true], // Standard HD / Full HD / 4K video (~1.78:1)
            ['name' => '5:3',    'label' => '5:3',    'value' => 5 / 3,     'active' => true], // Widescreen photography / 720p video (~1.67:1)
            ['name' => '1.43:1', 'label' => '1.43:1', 'value' => 1.43,      'active' => true], // IMAX film format
            ['name' => '1.37:1', 'label' => '1.37:1', 'value' => 1.37,      'active' => true], // Academy ratio (classic 1930s–50s cinema)
            ['name' => '4:3',    'label' => '4:3',    'value' => 4 / 3,     'active' => true], // Classic TV and photography (~1.33:1)
            ['name' => '3:2',    'label' => '3:2',    'value' => 3 / 2,     'active' => true], // 35mm film and DSLR sensors (~1.5:1)
            ['name' => '5:4',    'label' => '5:4',    'value' => 5 / 4,     'active' => true], // SXGA monitors (1280×1024), medium-format photography
            ['name' => '6:5',    'label' => '6:5',    'value' => 6 / 5,     'active' => true], // Slightly taller than 5:4, uncommon artistic/print format
            ['name' => '1:1',    'label' => '1:1',    'value' => 1,         'active' => true], // Perfect square (social media, design)
            ['name' => '15:16',  'label' => '15:16',  'value' => 15 / 16,   'active' => true], // Near-square portrait (~0.94:1)
            ['name' => '9:10',   'label' => '9:10',   'value' => 9 / 10,    'active' => true], // Portrait ratio (common on mobile / social media)
            ['name' => '3:4',    'label' => '3:4',    'value' => 3 / 4,     'active' => true], // Portrait photography (~0.75:1)

        [
            // CINEMA / FILM
            ['name' => '2.39:1',  'label' => '2.39:1',  'value' => 2.39,      'active' => true], // Cinemascope / anamorphic widescreen
            ['name' => '2.35:1',  'label' => '2.35:1',  'value' => 2.35,      'active' => true], // Classic widescreen cinema
            ['name' => '2.20:1',  'label' => '2.20:1',  'value' => 2.20,      'active' => true], // 70mm film (Todd-AO, Panavision)
            ['name' => '2:1',     'label' => '2:1',     'value' => 2,         'active' => true], // Modern cinematic / 18:9 smartphones
            ['name' => '1.85:1',  'label' => '1.85:1',  'value' => 1.85,      'active' => true], // Standard widescreen cinema
            ['name' => '1.43:1',  'label' => '1.43:1',  'value' => 1.43,      'active' => true], // IMAX film format
            ['name' => '1.37:1',  'label' => '1.37:1',  'value' => 1.37,      'active' => true], // Academy ratio (1930s–50s)

            // DISPLAY / MONITORS / SMARTPHONES
            ['name' => '19:10',   'label' => '19:10',   'value' => 19 / 10,   'active' => true], // Modern smartphones, ultra-wide monitors
            ['name' => '16:10',   'label' => '16:10',   'value' => 16 / 10,   'active' => true], // Laptop/monitor ratio (WUXGA)
            ['name' => '16:9',    'label' => '16:9',    'value' => 16 / 9,    'active' => true], // Standard HD / Full HD / 4K
            ['name' => '5:3',     'label' => '5:3',     'value' => 5 / 3,     'active' => true], // Widescreen photography, 720p video

            // PHOTOGRAPHY / GENERAL
            ['name' => '4:3',     'label' => '4:3',     'value' => 4 / 3,     'active' => true], // Classic photography / SD video
            ['name' => '3:2',     'label' => '3:2',     'value' => 3 / 2,     'active' => true], // 35mm film / DSLR sensors
            ['name' => '5:4',     'label' => '5:4',     'value' => 5 / 4,     'active' => true], // SXGA monitors, medium-format photography
            ['name' => '6:5',     'label' => '6:5',     'value' => 6 / 5,     'active' => true], // Slightly taller than 5:4, uncommon
            ['name' => '1:1',     'label' => '1:1',     'value' => 1,         'active' => true], // Square images (social media, design)

            // PORTRAIT / MOBILE / SOCIAL MEDIA
            ['name' => '9:16',    'label' => '9:16',    'value' => 9 / 16,    'active' => true], // Vertical video / Instagram stories / TikTok
            ['name' => '4:5',     'label' => '4:5',     'value' => 4 / 5,     'active' => true], // Portrait photos for Instagram feed
            ['name' => '3:5',     'label' => '3:5',     'value' => 3 / 5,     'active' => true], // Portrait / mobile marketing
            ['name' => '15:16',   'label' => '15:16',   'value' => 15 / 16,   'active' => true], // Near-square portrait (~0.94:1)
            ['name' => '9:10',    'label' => '9:10',    'value' => 9 / 10,    'active' => true], // Portrait format common in apps / web
            ['name' => '3:4',     'label' => '3:4',     'value' => 3 / 4,     'active' => true], // Portrait photography (~0.75:1)
        ];

         */
];
