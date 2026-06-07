# Media library extensions

This package adds functionality toMedia Library package by Spatie:

- Providing a view to upload multiple media
- Providing a view to upload single media

## Key Features
- **Extended Media Support**: Use `HasMediaExtended` and `InteractsWithMediaExtended` for advanced features.
- **Temporary Uploads**: Handle files uploaded before saving models with the `TemporaryUpload` system.
- **YouTube Integration**: Easily store and manage YouTube videos as media items.
- **Multi-Database/Data Sources**: Dynamic connection switching for media operations.
- **UI Components**: Media Managers (Single/Multiple/TinyMCE), Image Editor, Carousels, and Responsive Images.
- **Media Organization**: Reorder media and set specific items as "first" easily.

## Requirements

This package assumes that spatie/laravel-medialibrary is installed 
and its default migration has been run to create the media table.

for the image editor to work the NPM package "@mlbrgn/medialibrary-extensions" needs to be installed.

## Install

1) Install the laravel package:

```shell
  composer require mlbrgn/laravel-medialibrary-extensions
```

2) Run install command

```shell
  php artisan medialibrary-extensions:install
```

3) add @import "media-library-extensions" to your app.js (or similar)

```js
import {ImageEditor} from '@mlbrgn/medialibrary-extensions'
```

you can optionally set the path to the translations you want to use, but make sure
the translations live in the path you specified.

NOTE: Don't forget to run "npm run build" for the image editor to be built!

```js

ImageEditor.translationsPath = '/js/vendor/image-editor/lang';
```

The install command will publish assets, config, translation, views, and a policy.
Also it will install the required @mlbrgn/medialibrary-extensions package

### Manual install (without the install command)

You can also manually install by installing the @mlbrgn/medialibrary-extensions package

```shell
  npm install @mlbrgn/medialibrary-extensions
```

And publish the required assets

```shell
php artisan vendor:publish --provider="Mlbrgn\MediaLibraryExtensions\Providers\MediaLibraryExtensionsServiceProvider" --tag="assets"
````
add @import for "media-library-extensions" to your app.js (or similar)

```js
import {ImageEditor} from '@mlbrgn/medialibrary-extensions'
```

you can optionally set the path to the translations you want to use, but make sure
the translations live in the path you specified.

NOTE: Don't forget to run "npm run build" for the imageeditor to be built!


```js

ImageEditor.translationsPath = '/js/vendor/image-editor/lang';
```

## Theme

The default themes are:
- plain
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
  php artisan vendor:publish --provider="Mlbrgn\MediaLibraryExtensions\Providers\MediaLibraryExtensionsServiceProvider" --tag="medialibrary-extensions-config"
```

to publish a single tag (group of assets):

```shell
php artisan vendor:publish --provider="Mlbrgn\MediaLibraryExtensions\Providers\MediaLibraryExtensionsServiceProvider" --tag="config"
````

## Icons

For icons to work, you will have to install a Blade UIKit/Blade-icons package.

The package is configured to use Bootstrap icons by default. To display them properly install

```shell
   composer require davidhsianturi/blade-bootstrap-icons
```
You can override the icons in the published configuration file of this package and install another Blade UIKit/Blade-icons package

```shell
php artisan vendor:publish --tag=medialibrary-extensions-config
php artisan vendor:publish --tag=medialibrary-extensions-views
php artisan vendor:publish --tag=medialibrary-extensions-assets
php artisan vendor:publish --tag=medialibrary-extensions-policy
php artisan vendor:publish --tag=media-library-extensions-translations
```

## Testing

This package uses [Pest PHP](https://pestphp.com/) for testing. For more information on how to write tests for this package, see the [TESTING.md](TESTING.md) guide.
