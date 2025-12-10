# Changelog

## [2.1.7](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.1.6...2.1.7) (2025-12-10)


### Bug Fixes

* removed frameborder from iframe (deprecated) using border:0 in css class now ([e08ca4b](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/e08ca4b1a856578dc24500ac60944e32826f9779))
* removed inline styles (CSP compatibility) ([e08ca4b](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/e08ca4b1a856578dc24500ac60944e32826f9779))

## [2.1.6](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.1.5...2.1.6) (2025-12-10)


### Bug Fixes

* better bootstrap resolving using bootstrap-resolver.js ([1794f46](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/1794f46c14c6597ef92f40b8c6fbaf126a4efb3b))

## [2.1.5](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.1.4...2.1.5) (2025-12-10)


### Bug Fixes

* bootstrap not bundled with extensions, host app should import bootstrap and expose it using window.bootstrap ([d0bb6b1](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/d0bb6b10ac265e516fc9abb5f6b9df34548e88a7))
* CSP, added dynamic-loader.js to make component comply with CSP when enabled ([b1c2da5](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/b1c2da56d1059ca426d71b7694d9b5d6c2ca14b7))

## [2.1.4](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.1.3...2.1.4) (2025-12-07)


### Bug Fixes

* changed demo views now using blade components are recognized by IDE ([8720eb5](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/8720eb5c68f23fcfe411221fdc3e79b63574dbb9))

## [2.1.3](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.1.2...2.1.3) (2025-12-06)


### Bug Fixes

* changed default frequency of cleanup to daily ([9b454e8](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/9b454e8948045eee9b1978a261c762cbfa419ba9))

## [2.1.2](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.1.1...2.1.2) (2025-12-06)


### Bug Fixes

* disabled storage link check, caused server error ([2fa323c](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/2fa323c872e019549c67eabc80c64c7147e45ad0))

## [2.1.1](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.1.0...2.1.1) (2025-12-06)


### Bug Fixes

* list of allowed values for frequency ([a61b221](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/a61b22104c0f09610c989e239b907cdfd04308b7))

## [2.1.0](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.0.9...2.1.0) (2025-12-06)


### Features

* added check in service provider to see if storage link exists. ([392d666](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/392d666415c5b3e8b304930a35dc147b09a720f8))
* added pinback support for scheduled tasks ([902b5ee](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/902b5ee8a8a637a14593785ee94fd3e3487b262c))

## [2.0.9](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.0.8...2.0.9) (2025-11-27)


### Bug Fixes

* updated dependencies ([82737eb](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/82737eb86c81b867adad5f3cc945638a0483c36d))
* updated php version in composer.json ([0435d62](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/0435d62914296b0c961c90402040b51226bd2c7f))

## [2.0.8](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.0.7...2.0.8) (2025-11-26)


### Bug Fixes

* added comment to test please-release ([77772b9](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/77772b9dc19b2e1567c830e4aa3c07d385730a8b))
* updated release-please config ([d701693](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/d7016936bbf53b91395e56a9c45d2980a5c88fae))

## [2.1.0](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/vendor/laravel-package-2.0.7...vendor/laravel-package-2.1.0) (2025-11-26)


### Features

* added aliases to commands ([6e0f8f4](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/6e0f8f46612a2d129ebceca57fb02aeb819518c7))


### Bug Fixes

* added comment to test please-release ([77772b9](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/77772b9dc19b2e1567c830e4aa3c07d385730a8b))
* image editor loading again. ([b944123](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/b94412352f41ae3bd5d00118481988c77efcf8ed))
* path to tinymce-custom-file-picker.js ([841837e](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/841837ef7852a8fdcae5ae33b70c55c13f775512))
* publish assets without namespace and update docs with publishing examples ([8afda79](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/8afda7973eda74ebb8d33975c0ac01bf644352f3))

## [2.2.0](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/vendor/laravel-package-2.1.0...vendor/laravel-package-2.2.0) (2025-11-26)


### Features

* added aliases to commands ([6e0f8f4](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/6e0f8f46612a2d129ebceca57fb02aeb819518c7))


### Bug Fixes

* image editor loading again. ([b944123](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/b94412352f41ae3bd5d00118481988c77efcf8ed))
* path to tinymce-custom-file-picker.js ([841837e](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/841837ef7852a8fdcae5ae33b70c55c13f775512))
* publish assets without namespace and update docs with publishing examples ([8afda79](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/8afda7973eda74ebb8d33975c0ac01bf644352f3))

## [2.1.0](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/vendor/laravel-package-v2.0.7...vendor/laravel-package-v2.1.0) (2025-11-26)


### Features

* added aliases to commands ([6e0f8f4](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/6e0f8f46612a2d129ebceca57fb02aeb819518c7))


### Bug Fixes

* image editor loading again. ([b944123](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/b94412352f41ae3bd5d00118481988c77efcf8ed))
* path to tinymce-custom-file-picker.js ([841837e](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/841837ef7852a8fdcae5ae33b70c55c13f775512))
* publish assets without namespace and update docs with publishing examples ([8afda79](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/8afda7973eda74ebb8d33975c0ac01bf644352f3))
