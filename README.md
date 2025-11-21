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
- assets (CSS and JavaScript files with content hashes for cache busting)

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

### Updating Assets

If you need to update the published assets after a package update, run:

```shell
php artisan vendor:publish --tag=media-library-extensions-assets --force
```

This will republish all assets with their new content hashes, ensuring browsers load the latest versions instead of cached ones.

## Testing

This package uses [Pest PHP](https://pestphp.com/) for testing. For more information on how to write tests for this package, see the [TESTING.md](TESTING.md) guide.
