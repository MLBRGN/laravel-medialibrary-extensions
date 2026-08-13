# Media Resolution Refactor Proposal

## Goal

Simplify the package by giving every class a single responsibility.

The current implementation mixes several concepts:

- resolving a model class from a morph alias
- validating model classes
- instantiating models
- loading existing models
- assigning database connections
- determining temporary upload mode

These responsibilities are currently spread across Blade components, Form Requests and a service class.

The goal is to make **MediaModelResolver the single place responsible for model resolution**.

---

## Current status (implementation snapshot)

The package now includes a dedicated resolver service: `Mlbrgn\\MediaLibraryExtensions\\Services\\MediaModelResolver` that centralizes core responsibilities:

- Resolve model type (class or morph alias) to a concrete class: `resolveModelClass()`
- Normalize a model instance or class string into a single shape: `resolveModelReference()` which returns a `ResolvedModel`
- Resolve request-provided inputs safely: `resolveRequestModel()` (guards invalid input and logs issues)
- Find existing records by id with proper connection: `resolveModelById()`, `resolveMediumById()`, `findMedium()`, `findTemporaryUpload()`
- Instantiate temporary uploads with the resolved connection: `instantiateTemporaryUpload()`

Connection selection is delegated to `DataSourceResolver`, ensuring all lookups/instantiations use the correct database connection.

`MediaManagerRequest` already delegates most model resolution to `MediaModelResolver`; it can be further simplified to only parse input, authorize, and validate.

# 1. ✅ Rename `modelOrClassName` to `modelReference` (completed)

The public API now exposes:

```php
public Model|string $modelReference;
```

This name is concept-focused because both values represent the same thing:

- an existing model
- or a reference to the model that will eventually own the uploaded media.

Example:

```blade
<x-media-manager :model-reference="$blog" />

<x-media-manager :model-reference="App\Models\Blog::class" />
```

This makes the public API easier to understand without exposing implementation details.

Step 1 status:

- `modelOrClassName` / `$modelOrClassName` were replaced by `modelReference` / `$modelReference`
- Blade usage uses the kebab-case attribute `model-reference`

---

# 2. ✅ Public surface and naming aligned (completed)

`MediaModelResolver` now exposes a coherent public surface for model/media resolution.

| Method | Responsibility |
|---------|----------------|
| resolveModelClass() | Resolve morph alias or class string to a concrete model class implementing `HasMediaExtended` |
| resolveModelReference() | Normalize a model or class reference into a `ResolvedModel` and set `temporaryUploadMode` accordingly |
| resolveRequestModel() | Safely resolve a model from request inputs, returning `null` when invalid or not found (and logging) |
| resolveModelById() | Load an existing model by id applying the correct connection |
| resolveMediumById()/findMedium()/findTemporaryUpload() | Load media or temporary uploads by id with connection handling |
| instantiateTemporaryUpload() | Create a `TemporaryUpload` instance with the resolved connection |

---

# 3. ✅ `MediaManagerRequest` simplified (completed)

`MediaManagerRequest` now delegates model resolution to `MediaModelResolver` and focuses on request concerns.

The Request should only be responsible for:

- reading request input
- authorization
- validation

Current state:

- Request model loading delegates through `resolveRequestModel()`
- Validation model/class resolution delegates through resolver-backed helpers
- Authorization flow gates invalid model input early and avoids request-side morph/class fallback duplication

Example target usage:

```php
$model = app(\Mlbrgn\MediaLibraryExtensions\Services\MediaModelResolver::class)
    ->resolveRequestModel(
        modelType: $request->input('model_type'),
        modelId: $request->input('model_id'),
        dataSource: $request->input('data_source') ?? 'default',
    );
```

or

```php
$temporaryUpload = app(\Mlbrgn\MediaLibraryExtensions\Services\MediaModelResolver::class)
    ->instantiateTemporaryUpload(
        dataSource: $request->input('data_source') ?? 'default'
    );
```

---

# 4. Remaining work: remove duplicated model setup

Model setup (class validation, connection assignment, and lookup path) should remain centralized in `MediaModelResolver`.

Any duplicate model setup in callers should be removed.

---

# 5. Remaining work: reduce branching throughout the package

Many places currently check:

```php
if ($modelReference instanceof HasMedia) {
...
}

if (is_string($modelReference)) {
...
}
```

The goal is to centralize these decisions inside MediaModelResolver.

Callers should no longer care whether they received:

- a model
- or a model class

They should simply request the operation they need.

---

# 6. Long-term goal

The package should eventually have a very small public API for model handling.

Example:

```
resolveModelClass()

resolveModelReference()

resolveRequestModel()

resolveModelById()

resolveMediumById()/findMedium()/findTemporaryUpload()
```

Everything else should become implementation details.

---

# Benefits

- Smaller Request classes
- Cleaner Blade components
- One place responsible for model resolution (MediaModelResolver)
- Less duplicated validation
- Less duplicated connection handling
- More descriptive method names
- Easier testing
- Easier maintenance
- Clear separation of responsibilities
