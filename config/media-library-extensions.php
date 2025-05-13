<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Frontend theme
    |--------------------------------------------------------------------------
    |
    | The theme to be used for the frontend. Possible values:
    |
    */

    'frontend-theme' => 'bootstrap-5',
    //    'frontend-theme' => 'plain',

    /*
    |--------------------------------------------------------------------------
    | Supported frontend themes
    |--------------------------------------------------------------------------
    |
    | "plain" => plain html, css and javascript
    | "bootstrap-5" => bootstrap 5 css and javascript
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

    'route-prefix' => env('MEDIA_LIBRARY_EXTENSION_ROUTE_PREFIX', 'mlbrgn-mle'),

    /*
    |--------------------------------------------------------------------------
    | Max Upload Sizes (in kilobytes)
    |--------------------------------------------------------------------------
    |
    | Define the maximum allowed upload sizes for various media types.
    | Example: 16384 KB = 16 MB
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
            'media-manager-single-preview-menu' => 'media-manager-preview-menu media-manager-single-preview-menu d-flex justify-content-end px-2 align-items-center',
            'media-manager-single-preview-form' => 'media-manager-preview-form media-manager-single-preview-form',
            'media-manager-multiple-preview-menu' => 'media-manager-preview-menu media-manager-multiple-preview-menu d-flex justify-content-end px-2 align-items-center',
            'media-manager-multiple-preview-form' => 'media-manager-preview-form media-manager-multiple-preview-form',
            'media-manager-no-media' => 'media-manager-no-media my-3',
            'media-manager-headings' => 'media-manager-heading mb-4',
            'media-manager-upload-wrapper' => 'upload-wrapper col-12 col-sm-4 p-5',
            'media-manager-preview-image' => 'media-manager-preview-image',
            'media-manager-button-upload' => 'btn btn-success',
            'media-manager-button-delete' => 'btn btn-danger',
            'media-manager-button-icon-delete' => 'button-icon-delete btn btn-delete btn-icon btn-icon-delete btn-sm',
            'media-manager-input-file' => 'media-manager-input-file form-control mb-2',
        ],
    ],

];
