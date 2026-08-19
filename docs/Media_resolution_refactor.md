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

`MediaManagerRequest` now delegates model resolution to `MediaModelResolver` and focuses on input parsing, authorization, and validation.

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

# 4. ✅ Duplicated model setup removed (completed)

Model setup (class validation, connection assignment, and lookup path) is now centralized in `MediaModelResolver`.

Current state:

- Request-side model class validation delegates through resolver-backed helpers
- Validation model resolution avoids caller-side class/morph fallback duplication
- Connection-aware lookups/instantiation stay in resolver methods

---

# 5. ✅ Model-type branching reduced in callers (completed)

Many places currently check:

```php
if ($modelReference instanceof HasMedia) {
...
}

if (is_string($modelReference)) {
...
}
```

This model-type decision is now centralized in `MediaModelResolver` for the main request/component flows.

Callers should no longer care whether they received:

- a model
- or a model class

They should simply request the operation they need.

Current state:

- Callers rely on resolved model state (`model`, `modelType`, `temporaryUploadMode`) instead of branching on raw `modelReference` input type
- Remaining branching in components is behavior-oriented (temporary vs permanent mode), not class-vs-instance resolution logic

---

# 6. ✅ Long-term API reduction applied (completed)

The model-resolution contract is now intentionally small and caller-facing flows use resolver entry points instead of ad-hoc class-or-instance checks.

Current public surface used by request/action/component flows:

```
resolveModelClass()
resolveModelReference()
resolveRequestModel()
resolveModelById()
resolveMediumById()
findMedium()
findTemporaryUpload()
instantiateTemporaryUpload()
```

Current state:

- `MediaModelResolver::findById()` is internal (`private`) and no longer part of the public contract
- Request classes and actions delegate model/media loading through resolver methods instead of duplicating lookup/setup logic
- Temporary/permanent behavior branching remains where needed, but model input type resolution is centralized in the resolver

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
