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
    } host application
    |
    */

    'status_session_prefix' => 'media-library-extensions.status',

    /*
    |--------------------------------------------------------------------------
    | Classes
    |--------------------------------------------------------------------------
    |
    | Define classes
    |
    */

    'classes' => [
        'plain' => [
            'media-manager-single-wrapper' => 'media-manager media-manager-single',
            'media-manager-multiple-wrapper' => 'media-manager media-manager-multiple',
            'media-manager-single-row' => 'media-manager-row media-manager-single-row',
            'media-manager-multiple-row' => 'media-manager-row media-manager-multiple-row',
            'media-manager-single-form' => 'media-manager-form media-manager-single-form',
            'media-manager-multiple-form' => 'media-manager-form media-manager-multiple-form',
            'media-manager-single-preview-wrapper' => 'media-manager-preview-wrapper media-manager-single-preview-wrapper',
            'media-manager-multiple-preview-wrapper' => 'media-manager-preview-wrapper media-manager-multiple-preview-wrapper',
            'media-manager-single-preview-medium' => 'media-manager-preview-medium media-manager-single-preview-medium',
            'media-manager-single-preview-medium-link' => 'media-manager-preview-medium-link media-manager-single-preview-medium-link cursor-zoom-in',
            'media-manager-single-preview-menu' => 'media-manager-preview-menu media-manager-single-preview-menu',
            'media-manager-single-preview-form' => 'media-manager-preview-form media-manager-single-preview-form',
            'media-manager-multiple-preview-menu' => 'media-manager-preview-menu media-manager-multiple-preview-menu',
            'media-manager-multiple-preview-form' => 'media-manager-preview-form media-manager-multiple-preview-form',
            'media-manager-no-media' => 'media-manager-no-media',
            'media-manager-headings' => 'media-manager-heading',
            'media-manager-upload-wrapper' => 'upload-wrapper',
            'media-manager-preview-image' => 'media-manager-preview-image',
            'media-manager-button-upload' => 'button-upload',
            'media-manager-button-delete' => 'button-delete',
            'media-manager-button-icon-delete' => 'button-icon-delete',
            'media-manager-input-file' => 'media-manager-input-file',
            'media-manager-preview-modal' => 'media-manager-preview-modal',
            'media-manager-preview-modal-carousel' => 'media-manager-preview-modal-carousel carousel slide',
            'media-manager-preview-modal-carousel-inner' => 'carousel-inner',
            'media-manager-preview-modal-carousel-indicators' => 'carousel-indicators',
            'media-manager-preview-modal-carousel-item' => 'carousel-item',
            'media-manager-preview-modal-carousel-item-wrapper' => 'carousel-item-wrapper',
            'media-manager-preview-modal-carouse-item-image' => 'carousel-image',
            'media-manager-preview-modal-carousel-control-prev' => 'carousel-control-prev',
            'media-manager-preview-modal-carousel-control-next' => 'carousel-control-next',
            'media-manager-preview-modal-carousel-control-prev-icon' => 'carousel-control-prev-icon',
            'media-manager-preview-modal-carousel-control-next-icon' => 'carousel-control-next-icon',
            'media-manager-preview-images' => 'media-manager-preview-images',
            'media-manager-preview-image-container' => 'media-manager-preview-image-container',
            'media-manager-menu-form' => 'media-manager-menu-form',
            'button-close' => 'btn-close',
            'visually-hidden' => 'visually-hidden',
            'icon' => 'icon',
            'media-manager-modal' => 'media-manager-modal modal fade',
            'media-manager-modal-body' => 'media-manager-modal-body',
            'no-padding' => 'no-padding',
        ],
        'bootstrap-5' => [
            'media-manager-single-wrapper' => 'media-manager media-manager-single container-fluid',
            'media-manager-multiple-wrapper' => 'media-manager media-manager-multiple container-fluid',
            'media-manager-single-row' => 'media-manager-row media-manager-single-row row',
            'media-manager-multiple-row' => 'media-manager-row media-manager-multiple-row row',
            'media-manager-single-form' => 'media-manager-form media-manager-single-form col-12 col-md-4',
            'media-manager-multiple-form' => 'media-manager-form media-manager-multiple-form col-12 col-md-4',
            'media-manager-single-preview-wrapper' => 'media-manager-preview-wrapper media-manager-single-preview-wrapper col-12 col-md-8 text-center',
            'media-manager-multiple-preview-wrapper' => 'media-manager-preview-wrapper media-manager-multiple-preview-wrapper col-12 col-sm-8',
            'media-manager-single-preview-medium' => 'media-manager-preview-medium media-manager-single-preview-medium image-fluid',
            'media-manager-single-preview-medium-link' => 'media-manager-preview-medium-link media-manager-single-preview-medium-link cursor-zoom-in',
            'media-manager-single-preview-menu' => 'media-manager-preview-menu media-manager-single-preview-menu',
            'media-manager-single-preview-form' => 'media-manager-preview-form media-manager-single-preview-form',
            'media-manager-multiple-preview-menu' => 'media-manager-preview-menu media-manager-multiple-preview-menu',
            'media-manager-multiple-preview-form' => 'media-manager-preview-form media-manager-multiple-preview-form',
            'media-manager-no-media' => 'media-manager-no-media',
            'media-manager-headings' => 'media-manager-heading',
            'media-manager-upload-wrapper' => 'upload-wrapper col-12 col-sm-4',
            'media-manager-preview-image' => 'media-manager-preview-image',
            'media-manager-button-upload' => 'btn btn-success',
            'media-manager-button-delete' => 'btn btn-danger',
            'media-manager-button-icon-delete' => 'button-icon-delete btn btn-delete btn-icon btn-icon-delete btn-sm',
            'media-manager-input-file' => 'media-manager-input-file form-control',
            'media-manager-preview-modal' => 'media-manager-preview-modal',
            'media-manager-preview-modal-carousel' => 'media-manager-preview-modal-carousel carousel slide',
            'media-manager-preview-modal-carousel-inner' => 'carousel-inner',
            'media-manager-preview-modal-carousel-indicators' => 'carousel-indicators',
            'media-manager-preview-modal-carousel-item' => 'carousel-item',
            'media-manager-preview-modal-carousel-item-wrapper' => 'carousel-item-wrapper',
            'media-manager-preview-modal-carouse-item-image' => 'carousel-image',
            'media-manager-preview-modal-carousel-control-prev' => 'carousel-control-prev',
            'media-manager-preview-modal-carousel-control-next' => 'carousel-control-next',
            'media-manager-preview-modal-carousel-control-prev-icon' => 'carousel-control-prev-icon',
            'media-manager-preview-modal-carousel-control-next-icon' => 'carousel-control-next-icon',
            'media-manager-preview-images' => 'media-manager-preview-images',
            'media-manager-preview-image-container' => 'media-manager-preview-image-container',
            'media-manager-menu-form' => 'media-manager-menu-form',
            'button-close' => 'btn-close',
            'visually-hidden' => 'visually-hidden',
            'icon' => 'icon',
            'media-manager-modal' => 'media-manager-modal modal fade',
            'media-manager-modal-body' => 'media-manager-modal-body modal-body',
            'no-padding' => 'no-padding',
        ],
    ],

];
