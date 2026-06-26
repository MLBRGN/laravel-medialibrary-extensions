{{-- Laravel Media Library extensions Guidelines for AI Code Assistants --}}
{{-- License: MIT | (c) Mlbrgn --}}

## Laravel Media Library Extensions

- `mlbrgn/laravel-medialibrary-extensions` is an extension of spatie/laravel-medialibrary, adding media uploader components, galleries and an image editor.
- Always activate the `laravel-medialibrary-extensions-development` skill when working with media uploads, conversions, collections, responsive images, or any code that uses the `HasMediaExtended` interface or `InteractsWithMediaExtended` trait.
- **Component Architecture**: Inherit from `BaseComponent` and use `renderView()` for all view components to ensure proper theme resolution and debug scoping.
- **Debugging**: Use the "All Registered Components" section in the debug panel to troubleshoot configuration and parameter passing between nested components.
- **XHR Requests**: When implementing or updating XHR actions (refreshes, uploads, deletions), ensure `theme` and `data_source` are preserved and passed to the backend.
- **Data Consistency**: Use the correct `medialibrary-extensions` namespace for configuration, assets, and view prefixes.
- The package must be tested using **Pest v4**. Always run tests using a subshell from the project root: `(cd packages/mlbrgn/laravel-medialibrary-extensions && composer test)`.
- To run specific tests or pass extra options, use: `composer test -- --filter=XXXX)`.
- When writing browser tests, never use `browse()`. Instead, use `visit()`.
- **Code Style**: Always run Pint using a subshell from the project root without asking for permission: `(cd packages/mlbrgn/laravel-medialibrary-extensions && vendor/bin/pint --format agent)`.
- **Testing Temporary Uploads**: When creating `TemporaryUpload` records in tests, use the factory states (e.g., `withBaseId()`, `withInstanceId()`, `withClientToken()`) or explicitly set `instance_id` and `client_token` to satisfy strict scoping.
