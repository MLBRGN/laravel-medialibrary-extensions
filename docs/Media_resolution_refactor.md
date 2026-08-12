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

# 2. Move all model resolution into MediaModelResolver

Currently model resolution is split across several places.

MediaManagerRequest currently:

- resolves morph aliases
- validates model classes
- resolves models
- catches lookup exceptions

MediaService also:

- creates models
- resolves models
- validates model classes

These responsibilities should live in one place.

---

# 3. MediaManagerRequest should become simple

The Request should only be responsible for:

- reading request input
- authorization
- validation

It should no longer know about:

- Relation::getMorphedModel()
- class_exists()
- HasMediaExtended validation
- database connections
- model lookup exceptions

Instead it should simply ask `MediaModelResolver`.

Example:

```php
// Prefer delegating to the resolver
$model = app(\Mlbrgn\MediaLibraryExtensions\Services\MediaModelResolver::class)
    ->resolveRequestModel(
        modelType: $request->input('model_type'),
        modelId: $request->input('model_id'),
        dataSource: $request->input('data_source') ?? 'default',
    );
```

or

```php
// For temporary uploads, instantiate with the correct connection
$temporaryUpload = app(\Mlbrgn\MediaLibraryExtensions\Services\MediaModelResolver::class)
    ->instantiateTemporaryUpload(
        dataSource: $request->input('data_source') ?? 'default'
    );
```

---

# 4. Clarify MediaModelResolver responsibilities

MediaModelResolver currently groups related responsibilities used throughout the package.

Suggested sections:

```
Model Resolution
----------------
resolveModelClass($modelType)
resolveModelReference($modelReference|HasMediaExtended, $dataSource): ResolvedModel
resolveRequestModel($modelType, $modelId, $dataSource): ?HasMediaExtended
resolveModelById($modelClass, $id, $dataSource): ?HasMediaExtended

Media/Upload Retrieval
----------------------
resolveMediumById($modelClass, $id, $dataSource): Media|TemporaryUpload|null
findMedium($id, $dataSource): ?Media
findTemporaryUpload($id, $dataSource): ?TemporaryUpload

Instantiation
-------------
instantiateTemporaryUpload($dataSource): TemporaryUpload
```

This makes the class much easier to navigate.

---

# 5. Public surface and naming (current)

| Method | Responsibility |
|---------|----------------|
| resolveModelClass() | Resolve morph alias or class string to a concrete model class implementing `HasMediaExtended` |
| resolveModelReference() | Normalize a model or class reference into a `ResolvedModel` and set `temporaryUploadMode` accordingly |
| resolveRequestModel() | Safely resolve a model from request inputs, returning `null` when invalid or not found (and logging) |
| resolveModelById() | Load an existing model by id applying the correct connection |
| resolveMediumById()/findMedium()/findTemporaryUpload() | Load media or temporary uploads by id with connection handling |
| instantiateTemporaryUpload() | Create a `TemporaryUpload` instance with the resolved connection |

---

# 6. Remove duplicated model setup

Both `make()` and `resolveModelById()` currently:

- validate the model class
- instantiate the model
- assign the database connection

This setup should exist only once.

Example:

```
makeModel()
↓

instantiate model
assign connection

↓

return model
```

Then:

```
findModel()

↓

makeModel()

↓

findOrFail()
```

---

# 7. Reduce branching throughout the package

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

# 8. Long-term goal

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
