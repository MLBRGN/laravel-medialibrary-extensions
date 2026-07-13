{{-- Laravel Media Library extensions Guidelines for AI Code Assistants --}}
{{-- License: MIT | (c) Mlbrgn --}}

## Laravel Media Library Extensions

- `mlbrgn/laravel-medialibrary-extensions` is an extension of spatie/laravel-medialibrary, adding media uploader components, galleries and an image editor.
- Always activate the `laravel-medialibrary-extensions-development` skill when working with media uploads, conversions, collections, responsive images, or any code that uses the `HasMediaExtended` interface or `InteractsWithMediaExtended` trait.
- **Component Architecture**: Inherit from `BaseComponent` and use `renderView()` for all view components to ensure proper theme resolution and debug scoping.
- **Debugging**: Use the "All Registered Components" section in the debug panel to troubleshoot configuration and parameter passing between nested components.
- **XHR Requests**: When implementing or updating XHR actions (refreshes, uploads, deletions), ensure `theme` and `data_source` are preserved and passed to the backend.
- **Data Consistency**: Use the correct `medialibrary-extensions` namespace for configuration, assets, and view prefixes.
- The package must be tested using **Pest v4**. Always run tests from the package directory using its Composer scripts. Canonical commands (from repo root):
  - Run all tests: `composer --working-dir=packages/mlbrgn/laravel-medialibrary-extensions test`
  - Run a single file: `composer --working-dir=packages/mlbrgn/laravel-medialibrary-extensions test -- tests/Feature/YourTest.php`
  - Filter by name: `composer --working-dir=packages/mlbrgn/laravel-medialibrary-extensions test -- --filter="your filter"`
  - Browser tests only: `composer --working-dir=packages/mlbrgn/laravel-medialibrary-extensions test-browser`
  - Update snapshots: `composer --working-dir=packages/mlbrgn/laravel-medialibrary-extensions test -- --update-snapshots`
  - Avoid running `php artisan test` from the monorepo root for this package.
  - Troubleshooting: If the runner prints `No tests found.`, remove any `->only()` usages in Pest tests or browser tests (e.g. `it(...)->only()`, `test(...)->only()`, `describe(...)->only()`, or `->group('browser')->only()`). These limit discovery and can cause zero tests to run.
  - Always verify the working directory before running tests. Prefer the `--working-dir` flag to avoid cwd mistakes.
  - Browser tests write their Laravel log to: `packages/mlbrgn/laravel-medialibrary-extensions/tests/.logs/laravel.log` (NOT the app-level `storage/logs/laravel.log`). Inspect this file when diagnosing browser tests.
   - Browser tests environment: Do NOT rely on session or cookies for critical data flow (e.g., `mle_client_token`). Always send required values explicitly in request payloads or hidden inputs. Cookies may not be present/propagated between XHR and subsequent form POSTs in the browser test harness.
  
  When writing browser tests, never use `browse()`. Instead, use `visit()`.

  **Code Style**: Prefer running Pint scoped to this package. From repo root either:
  - `composer --working-dir=packages/mlbrgn/laravel-medialibrary-extensions pint` (if script exists), or
  - `vendor/bin/pint --format agent` (will format dirty files project-wide).
- **Testing Temporary Uploads**: When creating `TemporaryUpload` records in tests, use the factory states (e.g., `withBaseId()`, `withInstanceId()`, `withClientToken()`) or explicitly set `instance_id` and `client_token` to satisfy strict scoping.
  - For browser tests specifically, ensure the same `client_token` used by XHR uploads is also posted with the model create request (e.g., keep a hidden `client_token` input and populate it explicitly). Do not depend on a cookie being present during the POST.
