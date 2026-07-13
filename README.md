# Laravel Media Library Extensions

This package adds functionality toMedia Library package by Spatie:

- Providing a view to upload multiple media
- Providing a view to upload single media

## Requirements

This package assumes that [spatie/laravel-medialibrary is installed](https://spatie.be/docs/laravel-medialibrary/v11/installation-setup)
and its default migration has been run to create the media table.

## Installation

1) Install the package using:

```shell
  composer require mlbrgn/laravel-medialibrary-extensions
```

2) Run install command

```shell
  php artisan medialibrary-extensions:install
```

You can configure the path to the translations the Image Editor, provided by this package, uses by adding the following to your app.js (or similar).

```js
ImageEditor.translationsPath = '/js/vendor/image-editor/lang';
```

The install command will publish the needed assets, config, translation, views, and a policy.

### Manual install (without using the install command)

You can also manually install by installing the @mlbrgn/laravel-medialibrary-extensions package

```shell
  npm install @mlbrgn/laravel-medialibrary-extensions
```

And publish the required assets

```shell
php artisan vendor:publish --provider="Mlbrgn\MediaLibraryExtensions\Providers\MediaLibraryExtensionsServiceProvider" --tag="assets"
````

## Themes

The provided themes are:
- plain (vanilla css and js)
- bootstrap-5

## Publishing

Several assets can be published:
- config
- views
- assets
- policy
- translations

to publish all assets:

```shell
  php artisan vendor:publish --provider="Mlbrgn\MediaLibraryExtensions\Providers\MediaLibraryExtensionsServiceProvider" --tag="media-library-extensions-config"
```

to publish a single tag (group of assets):

```shell
php artisan vendor:publish --tag=media-library-extensions-config
php artisan vendor:publish --tag=media-library-extensions-views
php artisan vendor:publish --tag=media-library-extensions-assets
php artisan vendor:publish --tag=media-library-extensions-policy
php artisan vendor:publish --tag=media-library-extensions-translations
```

## Icons

For icons to work, you will have to install a Blade UI Kit package.

The package is configured to use the [Blade Bootstrap Icons](https://github.com/davidhsianturi/blade-bootstrap-icons) by default. To display them properly install

```shell
   composer require davidhsianturi/blade-bootstrap-icons
```

You can override the icons in the published configuration file of this package and install another Blade UIKit/Blade-icons package

## Testing

This package uses [Pest PHP](https://pestphp.com/) for testing. For more information on how to write tests for this package, see the [TESTING.md](TESTING.md) guide.
