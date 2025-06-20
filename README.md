# Media library extensions

This package adds functionality toMedia Library package by Spatie:

- Providing a view to upload multiple media
- Providing a view to upload single media

## Requirements

This package assumes that spatie/laravel-medialibrary is installed 
and its default migration has been run to create the media table.

## Theme

The default themes are:
- plain
- bootstrap-5

## Publishing

Several resources can be published:
- config
- views

## Icons

For icons to work, you will have to install a Blade UIKit/Blade-icons package.

The package is configured to use Bootstrap icons by default. To display them properly install

```shell
   composer require davidhsianturi/blade-bootstrap-icons
```
You can override the icons in the published configuration file of this package and install another Blade UIKit/Blade-icons package

```shell
php artisan vendor:publish --tag=media-library-extensions-config
php artisan vendor:publish --tag=media-library-extensions-views
php artisan vendor:publish --tag=media-library-extensions-assets
php artisan vendor:publish --tag=media-library-extensions-policy
php artisan vendor:publish --tag=media-library-extensions-translations
```
## Configuration usin environment variables

### Debug mode (enable for development)
MEDIA_LIBRARY_EXTENSIONS_DEBUG=false

### Enable demo mode (uses separate demo database)
MEDIA_LIBRARY_EXTENSIONS_DEMO_MODE=false

### Frontend theme (options: plain, bootstrap-5)
MEDIA_LIBRARY_EXTENSIONS_FRONTEND_THEME=bootstrap-5

### Route prefix for package routes
MEDIA_LIBRARY_EXTENSIONS_ROUTE_PREFIX=mlbrgn-mle

### Middleware for package routes (comma-separated)
MEDIA_LIBRARY_EXTENSIONS_ROUTE_MIDDLEWARE=web,auth

### Use XMLHttpRequest for nested form support
MEDIA_LIBRARY_EXTENSIONS_USE_XHR=true

### Max file upload size in kilobytes (16 MB = 16384)
MEDIA_LIBRARY_EXTENSIONS_MAX_FILE_SIZE=16384

### Max number of media items per collection
MEDIA_LIBRARY_EXTENSIONS_MAX_ITEMS_IN_COLLECTION=10

### Allowed mimetypes (comma-separated)
MEDIA_LIBRARY_EXTENSIONS_ALLOWED_IMAGE_MIMETYPES=image/jpeg,image/png,image/gif,image/bmp,image/webp,image/heic,image/avif
MEDIA_LIBRARY_EXTENSIONS_ALLOWED_VIDEO_MIMETYPES=video/mp4,video/quicktime,video/x-msvideo
MEDIA_LIBRARY_EXTENSIONS_ALLOWED_DOCUMENT_MIMETYPES=application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document

### Max dimensions for images
MEDIA_LIBRARY_EXTENSIONS_MAX_IMAGE_WIDTH=1920
MEDIA_LIBRARY_EXTENSIONS_MAX_IMAGE_HEIGHT=1080

### Override default icons (Bootstrap Icons by default)
MEDIA_LIBRARY_EXTENSIONS_ICON_DELETE=bi-trash3
MEDIA_LIBRARY_EXTENSIONS_ICON_SETUP_AS_MAIN=bi-star
MEDIA_LIBRARY_EXTENSIONS_ICON_SET_AS_MAIN=bi-star-fill
MEDIA_LIBRARY_EXTENSIONS_ICON_PLAY_VIDEO=bi-play-fill
MEDIA_LIBRARY_EXTENSIONS_ICON_CLOSE=bi-x-lg
MEDIA_LIBRARY_EXTENSIONS_ICON_NEXT=bi-chevron-right
MEDIA_LIBRARY_EXTENSIONS_ICON_PREV=bi-chevron-left
MEDIA_LIBRARY_EXTENSIONS_ICON_PDF=bi-file-earmark-pdf
MEDIA_LIBRARY_EXTENSIONS_ICON_WORD=bi-file-earmark-word
MEDIA_LIBRARY_EXTENSIONS_ICON_UNKNOWN=bi-file-earmark

### Carousel behavior
MEDIA_LIBRARY_EXTENSIONS_CAROUSEL_RIDE=false
MEDIA_LIBRARY_EXTENSIONS_CAROUSEL_RIDE_INTERVAL=3000
MEDIA_LIBRARY_EXTENSIONS_CAROUSEL_RIDE_ONLY_AFTER_INTERACTION=false
MEDIA_LIBRARY_EXTENSIONS_CAROUSEL_FADE=false

### Show status messages inside component
MEDIA_LIBRARY_EXTENSIONS_SHOW_STATUS=true

### Session prefix for flash messages (prevents naming conflicts)
MEDIA_LIBRARY_EXTENSIONS_STATUS_SESSION_PREFIX=laravel-medialibrary-extensions.status

### Enable YouTube support
MEDIA_LIBRARY_EXTENSIONS_YOUTUBE_SUPPORT_ENABLED=true
