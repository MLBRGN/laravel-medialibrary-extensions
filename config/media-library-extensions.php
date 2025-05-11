<?php

return [

    'prefix' => env('MEDIA_LIBRARY_EXTENSION_PREFIX', 'mlbrgn-mle'),

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

    'max_image_width' => env('MLE_MAX_IMAGE_WIDTH', 1920),
    'max_image_height' => env('MLE_MAX_IMAGE_HEIGHT', 1080),

];
