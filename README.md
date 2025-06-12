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
