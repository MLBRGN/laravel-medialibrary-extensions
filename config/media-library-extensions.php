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
        'image' => ['jpeg', 'jpg', 'png', 'gif', 'bmp', 'svg', 'heic', 'avif'],
        'video' => ['mp4', 'mov', 'avi'],
        'document' => ['pdf', 'doc', 'docx'],
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
            'media-manager-single-wrapper' => 'media-manager media-manager-single evertjan',
            'media-manager-single-form' => 'media-manager-single-form',
            'media-manager-headings' => 'media-manager-heading',
            'media-manager-upload-wrapper' => 'upload-wrapper',
            'media-manager-preview-image' => 'media-manager-preview-image',
            'media-manager-button-upload' => 'button-upload',
            'media-manager-button-delete' => 'button-delete',
            'media-manager-input-file' => 'media-manager-input-file',
            'media-manager-single-image-preview-wrapper' => 'media-manager-single-image-preview-wrapper',
        ],
        'bootstrap-5' => [
            'media-manager-single-wrapper' => 'media-manager media-manager-single',
            'media-manager-single-form' => 'media-manager-single-form d-flex flex-column align-items-start gap-3 mb-3',
            'media-manager-headings' => 'media-manager-heading  mb-4',
            'media-manager-upload-wrapper' => 'upload-wrapper col-12 col-sm-4 p-5',
            'media-manager-preview-image' => 'media-manager-preview-image',
            'media-manager-button-upload' => 'btn btn-success',
            'media-manager-button-delete' => 'btn btn-danger',
            'media-manager-input-file' => 'media-manager-input-file form-control mb-2',
            'media-manager-single-image-preview-wrapper' => 'media-manager-single-image-preview-wrapper col-md-8',
        ],
    ],

];
