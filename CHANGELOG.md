# Changelog

## [2.3.17](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.3.16...2.3.17) (2026-07-21)


### Bug Fixes

* authorization and media collection check alerted ([2d24c7a](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/2d24c7ab8b518c539a04247444561dc32722b88a))

## [2.3.16](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.3.15...2.3.16) (2026-07-20)


### Bug Fixes

* composer updated ([fbad715](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/fbad715b89a2217f0d93878199ce9324b494141c))
* image-editor.js now loaded by media-library-loader.js ([179a718](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/179a7180eefc4ce3e4bda0b4a70bb9d32ba2b9ae))

## [2.3.15](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.3.14...2.3.15) (2026-07-19)


### Bug Fixes

* all tests passing ([aa12876](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/aa1287631e5c5a50268485bf6c8979de0bf795bc))

## [2.3.14](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.3.13...2.3.14) (2026-07-19)


### Bug Fixes

* added comment ([9065375](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/9065375b6fc5e6277997480c0ddd8cd854c81357))
* browser tests fixes, added empty database checks at start of tests. ([d2205fd](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/d2205fd8d11144be794a1a1c7db93354e3e0fca6))
* carousel fixes ([e544f27](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/e544f270cd919fb93d15966940d1f318eb501bec))
* carousel plain animations ([3227467](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/3227467d58df4e72bf456c4dd79128094fdb4d02))
* commented out changes ([b4c9418](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/b4c9418e9a41f4c998ccf49f9ca7a45bc486d8bb))
* duplicate url segments in temporary upload promotion ([da19eb3](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/da19eb3799cf68e4d579d4c30b72ebdfa981e7a1))
* eager loading image editor listener in media managers ([65005a2](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/65005a2ba673c98b854718efddea1ef407f9400b))
* fixes ([bd17542](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/bd1754234c4abc537a0431f5658e04ccd4b18e16))
* media-library-loader.js and asset-loader-core.js ([a5af658](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/a5af658a9ed1223ad1c5215bb4b3058ba9cf2544))
* pinnen playwright to     "playwright": "1.60.0", 1.61.0 gives "localPaths are not allowed when the client is not local" ([b72c76b](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/b72c76bd0bb6bf3630ce7d58357ec4298ddaf991))
* temporary upload refactor, solved nasty bug that didn't load assets once the page was already loaded but new components needed assets that were not yet loaded, now observing dom, all tests passing, pest and pest browser tests ([3b59201](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/3b5920164ae1658077a72f454d3e68a1e8e2d7d6))
* temporary upload refactor, solved nasty bug that didn't load assets once the page was already loaded but new components needed assets that were not yet loaded, now observing dom, all tests passing, pest and pest browser tests ([dcca2f9](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/dcca2f90f41f0d0b1d0fd64cba195007e33575af))
* updated dist files ([4bb0ce4](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/4bb0ce47079f5298cf6d229558c7e6551e771403))

## [2.3.13](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.3.12...2.3.13) (2026-07-13)


### Bug Fixes

* better validation StoreMultipleRequest.php and StoreSingleRequest.php ([219db06](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/219db06b2d5e22b1929f4ec4f2027cc987380d7b))
* browser testing ([f91cd6c](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/f91cd6c854cf28fce02d8633a6d1072d6f58d769))
* bug in media manager tinymce (no id) ([22ee88f](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/22ee88f477f7beccb09378df8217511dd020afaf))
* image info not showing in media lab ([3169bc3](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/3169bc3f74daf4b1e79813cf98402c88d4c10158))
* namespace issues ([4917e2c](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/4917e2c9cf277c2cbe09c45a5754b7984deae93d))
* nested form fix, disabled save model forms (temporarily) ([e8b5837](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/e8b5837902d50769201a1f51d761c24a4202e02c))
* temporary uploads preview modal not showing temporary uploads because of wrong instance id being used in lookup ([ca7fcbe](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/ca7fcbe5e7ef51a7963215e46018b90fd980fb47))


### Documentation

* added doc about id usage in package ([789b9fa](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/789b9fa5f7a45cfffdb8f4aaa0e016ccad819e09))

## [2.3.12](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.3.11...2.3.12) (2026-05-06)


### Bug Fixes

* updated compatible laravel support versions, added 13 ([ec7c776](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/ec7c776d3b274df9edee0aa5085ee8dcdf7e64d4))

## [2.3.11](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.3.10...2.3.11) (2026-05-05)


### Bug Fixes

* added instance_id to temporary_uploads_table ([e4df85b](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/e4df85b84e4fc44fd6c117f7345084c8a6dc6862))
* logging getRouteFromAction function with console.log ([f02864a](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/f02864ad563423b5c5335be36d071d6e6ff96529))

## [2.3.10](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.3.9...2.3.10) (2026-05-05)


### Bug Fixes

* routes now in routes key in config, pint fixes ([d5c2df0](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/d5c2df079b2bc32221f4fd082c2afa2848906b33))

## [2.3.9](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.3.8...2.3.9) (2026-05-05)


### Bug Fixes

* added aria-label to image-editor-update-form-file ([7d33a1d](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/7d33a1df2b73702caca7778174d968f55c01951f))
* added instanceId support ([0bed2e1](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/0bed2e108fd91b8ffb662ac1ec07e21c07efbcd5))
* prevent leaking of model and other sensitive data in config ([14f4226](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/14f4226c02c5b9f9a062ef34d1a5058e3b6e76f3))
* temporary uploads replace after edit now working again by adding instanceId support ([04a0870](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/04a0870b0de425b54937b3831c96bd0248e267fb))

## [2.3.8](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.3.7...2.3.8) (2026-03-21)


### Bug Fixes

* prefixed favicon route ([bed8054](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/bed8054fd3c0634f95fa3415d8a5966f7289c4cf))

## [2.3.7](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.3.6...2.3.7) (2026-03-21)


### Bug Fixes

* missing favicon in bootstrap preview ([c5164d3](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/c5164d352923eae30e2f9e6d4bed10cf8a22a96e))

## [2.3.6](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.3.5...2.3.6) (2026-03-21)


### Bug Fixes

* updated dependencies ([18a4292](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/18a42923c1bccb76da145703d5e1b2a29054bf07))

## [2.3.5](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.3.4...2.3.5) (2026-03-21)


### Bug Fixes

* renamed variables, refactored, improved asset loading ([bc27d1a](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/bc27d1a7c4188632312f9fa30ff5139b6a76aac0))
* trying nonce support ([58cee34](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/58cee34dfa6715df8f59dd7a3c92495d23f092ab))

## [2.3.4](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.3.3...2.3.4) (2026-03-19)


### Bug Fixes

* prepare support for csp ([2f3fb22](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/2f3fb22832377a43142c557f29edb58dc5d7e0e8))

## [2.3.3](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.3.2...2.3.3) (2026-01-21)


### Bug Fixes

* bug in placeholder image responsive, wrong path ([384dd61](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/384dd61e9add0ce611ef385d885275d47b493e48))

## [2.3.2](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.3.1...2.3.2) (2026-01-21)


### Bug Fixes

* wrong path image publishing, fixed ([b1bdbca](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/b1bdbca9ac62589621f94b9da00993b74f56d6cc))

## [2.3.1](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.3.0...2.3.1) (2026-01-21)


### Bug Fixes

* placeholder image added and now also publishing the images directory, which contains the placeholder ([754c783](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/754c783e26dbfb8b1f276605e70841af3adb3877))

## [2.3.0](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.2.1...2.3.0) (2026-01-21)


### Features

* ImageResponsive.php placeholder support ([8bff969](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/8bff9696d5864c9152d122ea9dd026fba117f569))


### Bug Fixes

* dynamic-loader.js refactor so that multiple configs are merged and only load deferred ([c11d687](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/c11d687361eda575235e0290b2db34d065bc234b))

## [2.2.1](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.2.0...2.2.1) (2026-01-17)


### Bug Fixes

* removed dumps ([6938932](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/6938932975f1c13555458f2ee4f59e614a4e8198))

## [2.2.0](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.1.13...2.2.0) (2026-01-17)


### Features

* added aliases to commands ([6e0f8f4](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/6e0f8f46612a2d129ebceca57fb02aeb819518c7))
* added check in service provider to see if storage link exists. ([392d666](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/392d666415c5b3e8b304930a35dc147b09a720f8))
* added pinback support for scheduled tasks ([902b5ee](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/902b5ee8a8a637a14593785ee94fd3e3487b262c))


### Bug Fixes

* added annotation for demo bootstrap, to ignore JSUnresolvedLibraryURL ([9d0cc8d](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/9d0cc8dcd49e8ca353bc3c6a4ff5c9155755849c))
* added comment to test please-release ([77772b9](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/77772b9dc19b2e1567c830e4aa3c07d385730a8b))
* better bootstrap resolving using bootstrap-resolver.js ([1794f46](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/1794f46c14c6597ef92f40b8c6fbaf126a4efb3b))
* bootstrap not bundled with extensions, host app should import bootstrap and expose it using window.bootstrap ([d0bb6b1](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/d0bb6b10ac265e516fc9abb5f6b9df34548e88a7))
* changed default frequency of cleanup to daily ([9b454e8](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/9b454e8948045eee9b1978a261c762cbfa419ba9))
* changed demo views now using blade components are recognized by IDE ([8720eb5](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/8720eb5c68f23fcfe411221fdc3e79b63574dbb9))
* CSP, added dynamic-loader.js to make component comply with CSP when enabled ([b1c2da5](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/b1c2da56d1059ca426d71b7694d9b5d6c2ca14b7))
* disabled storage link check, caused server error ([2fa323c](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/2fa323c872e019549c67eabc80c64c7147e45ad0))
* duplicate database table mle_temporary_uploads ([b591677](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/b591677b0fd73a001e23e74e9f10d573c005fefe))
* duplicate migrations, added loadMigrationsFrom to setup method in TestCase instead of the service provider. fixed test (cachebusting on images caused snapshots to not match) ([c88b5fb](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/c88b5fb3a6789068a93a301f3e35f254cb3c5c4f))
* image editor loading again. ([b944123](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/b94412352f41ae3bd5d00118481988c77efcf8ed))
* list of allowed values for frequency ([a61b221](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/a61b22104c0f09610c989e239b907cdfd04308b7))
* only using safe filename (slugged) so that unicode characters are removed to make image replacement in html easier and relialbe ([9560edc](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/9560edc88f07db550832a54f4c58d6bd03346f23))
* path to tinymce-custom-file-picker.js ([841837e](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/841837ef7852a8fdcae5ae33b70c55c13f775512))
* publish assets without namespace and update docs with publishing examples ([8afda79](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/8afda7973eda74ebb8d33975c0ac01bf644352f3))
* removed frameborder from iframe (deprecated) using border:0 in css class now ([e08ca4b](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/e08ca4b1a856578dc24500ac60944e32826f9779))
* removed inline styles (CSP compatibility) ([e08ca4b](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/e08ca4b1a856578dc24500ac60944e32826f9779))
* TemporaryUploadPromoter.php mixed absolute and relative urls and thus not working ([ce6103f](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/ce6103fb6dd0beafb408e8833d2fcb761b074512))
* testing TemporaryUploadPromoter and considering disk config ([9bc37c9](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/9bc37c9e3deee06a0c070d0e478bca6091dfa1a3))
* updated dependencies ([82737eb](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/82737eb86c81b867adad5f3cc945638a0483c36d))
* updated php version in composer.json ([0435d62](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/0435d62914296b0c961c90402040b51226bd2c7f))
* updated release-please config ([d701693](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/d7016936bbf53b91395e56a9c45d2980a5c88fae))

## [2.1.13](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.1.12...2.1.13) (2026-01-17)


### Bug Fixes

* only using safe filename (slugged) so that unicode characters are removed to make image replacement in html easier and relialbe ([9560edc](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/9560edc88f07db550832a54f4c58d6bd03346f23))

## [2.1.12](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.1.11...2.1.12) (2026-01-16)


### Bug Fixes

* testing TemporaryUploadPromoter and considering disk config ([9bc37c9](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/9bc37c9e3deee06a0c070d0e478bca6091dfa1a3))

## [2.1.11](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.1.10...2.1.11) (2026-01-16)


### Bug Fixes

* TemporaryUploadPromoter.php mixed absolute and relative urls and thus not working ([ce6103f](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/ce6103fb6dd0beafb408e8833d2fcb761b074512))

## [2.1.10](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.1.9...2.1.10) (2025-12-16)


### Bug Fixes

* duplicate migrations, added loadMigrationsFrom to setup method in TestCase instead of the service provider. fixed test (cachebusting on images caused snapshots to not match) ([c88b5fb](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/c88b5fb3a6789068a93a301f3e35f254cb3c5c4f))

## [2.1.9](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.1.8...2.1.9) (2025-12-13)


### Bug Fixes

* duplicate database table mle_temporary_uploads ([b591677](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/b591677b0fd73a001e23e74e9f10d573c005fefe))

## [2.1.8](https://github.com/MLBRGN/laravel-medialibrary-extensions/compare/2.1.7...2.1.8) (2025-12-10)


### Bug Fixes

* added annotation for demo bootstrap, to ignore JSUnresolvedLibraryURL ([9d0cc8d](https://github.com/MLBRGN/laravel-medialibrary-extensions/commit/9d0cc8dcd49e8ca353bc3c6a4ff5c9155755849c))

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
