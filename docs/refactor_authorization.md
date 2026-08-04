# Media Authorization Refactor

## Goals

The media library package should be responsible for answering one question:

> "May this user perform this media action on this model?"

It should **not** know:

- which policy is used
- how abilities are named
- whether authorization is based on Gate, policies, roles, permissions, ownership, tenants, etc.

Authorization strategy should be entirely application-defined.

---

# Current Situation

Currently authorization works roughly like this:

```
Request
    ↓
Model::canPerformMediaAction()
    ↓
Gate::getPolicyFor()
    ↓
uploadMedia()
deleteMedia()
editMedia()
```

This has several drawbacks.

- The model depends on Gate.
- The package assumes policy method names.
- One model can only realistically have one authorization strategy.
- Context-specific authorization (Profile vs Admin, etc.) is difficult.

---

# Proposed Architecture

Introduce a new contract.

```php
interface MediaActionsAuthorizer
{
    public function authorize(
        string $action,
        ?Authenticatable $user,
        HasMediaExtended $model,
    ): bool;
}
```

The package becomes responsible only for:

- resolving the model
- checking supported media actions
- asking the authorizer

Example:

```
Request
    ↓
supportsMediaAction()
    ↓
MediaActionsAuthorizer
```

---

# Default Implementation

The package provides a default implementation using Laravel Gate.

```php
final class DefaultMediaActionsAuthorizer
    implements MediaActionsAuthorizer
{
    public function authorize(...)
    {
        return Gate::forUser($user)
            ->allows($action.'Media', $model);
    }
}
```

This preserves existing behaviour.

---

# Custom Implementations

Applications may replace the implementation.

```php
$this->app->singleton(
    MediaActionsAuthorizer::class,
    AppMediaActionsAuthorizer::class,
);
```

The custom implementation may:

- use Gate
- use policies
- use roles
- use permissions
- use ownership
- use tenancy
- use subscriptions
- ignore Gate completely

The package does not care.

---

# Supported Actions

The package still determines whether a model supports a given media action.

Current implementation:

```php
protected const ACTION_MAP = [
    'upload' => 'allowsMediaUploads',
    'edit' => 'allowsMediaEdits',
    'delete' => 'allowsMediaDeletes',
];
```

Before authorizing:

```
supports action?
        │
      yes
        │
authorize
```

Authorization should never be asked for an unsupported action.

---

# Remove Authorization From Models

Current:

```
Model
    ↓
Gate
```

Proposed:

```
Request
    ↓
MediaActionsAuthorizer
```

Models should no longer know about Gate or policies.

Their only responsibility is declaring supported media actions.

---

# Future Improvements

## supportsMediaAction()

Instead of exposing ACTION_MAP externally, the model could expose

```php
supportsMediaAction(string $action): bool;
```

allowing ACTION_MAP to become an implementation detail.

---

## Rich Authorization Responses

Instead of returning bool, the interface could eventually return

```php
Illuminate\Auth\Access\Response
```

allowing custom denial messages.

---

# Request Availability

Custom implementations should **not** depend on an HTTP request existing.

Although requests will normally be available during uploads, edits and deletes, the interface should remain reusable for:

- Artisan commands
- queued jobs
- tests
- future package extensions

If an application needs the current request it may retrieve it itself:

```php
if (app()->bound('request')) {
    $request = request();

    // inspect route, input, etc.
}
```

The interface itself should remain transport-agnostic.

---

# Benefits

- Package no longer depends on Gate.
- Package no longer assumes policy names.
- Applications have complete control over authorization.
- Models no longer contain authorization logic.
- Easier to test.
- Easier to extend.
- Backwards compatible via the default Gate-based implementation.
