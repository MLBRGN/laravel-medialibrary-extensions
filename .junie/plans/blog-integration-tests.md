---
sessionId: session-260901-220900-106r
---

# Requirements

### Overview & Goals
The goal is to restore the integration and browser testing functionality to its previous stable state, while retaining critical bug fixes (such as morph map resolution). We will remove environment-specific skips from browser tests, simplify the model resolution logic to minimize disruption to existing tests, and ensure the test infrastructure is robust yet unobtrusive.

### Scope
- **In Scope**:
    - Restoring execution of browser tests by removing environment-specific `markTestSkipped` calls.
    - Simplifying `MediaModelResolver` to handle morph maps without forcing changes to all existing test expectations.
    - Aligning `BrowserTestCase` and `PackageInfrastructure` for consistent bootstrapping.
    - Verifying all 525+ regular Pest tests and the full suite of browser tests.
- **Out of Scope**:
    - Fixing the agent's internal headless browser multipart limitations (these tests will be restored for the user to run locally).

# Technical Design

### Current Implementation
Recent changes introduced morph map aliases for test models and updated `MediaModelResolver` to prefer these aliases. While this fixed XHR bugs, it required significant updates to existing feature tests and snapshots. Browser tests were also marked as skipped due to agent-specific infrastructure issues.

### Proposed Changes

### * Step 1: MediaModelResolver Refinement
- Keep the ability to resolve morph map aliases to class names.
- Revert the change that *prefers* aliases in the `modelType` property of `ResolvedModel` if it's not strictly necessary for the XHR fix. This should allow existing tests that expect full class names to pass without modification.
- Ensure `resolveModelReference` remains compatible with existing call sites.

###   Step 2: Browser Test Restoration
- Remove `markTestSkipped()` calls in `tests/Browser/BlogIntegrationTest.php` and `tests/Browser/DemoPageTest.php` that were added due to environment limitations.
- Keep the stability fixes (waiting logic, `page()->close()`) that genuinely improve test reliability.

###   Step 3: Infrastructure Alignment
- Review `PackageInfrastructure::register()` to ensure it doesn't over-write configurations in ways that conflict with `BrowserTestCase::setUp()`.
- Ensure `Blog` and `Alien` models are correctly mapped in both testing and demo contexts.

###   Step 4: Test Cleanup
- Revert changes to `MediaManagerSingleTest.php`, `MediaManagerMultipleTest.php`, and `MediaManagerTest.php` if the `MediaModelResolver` refinement allows them to use full class names again.

### Key Decisions
- **Decision: Prefer Class Names over Aliases in Internal State**
    - Rationale: While morph maps are great for storage and XHR payloads, the internal `modelType` property in components often expects the full class name in existing tests. By resolving to the class name but allowing aliases in inputs, we get the best of both worlds with minimal regression.

# Testing

### Validation Approach
- Run `./vendor/bin/pest` to ensure all 525+ feature/unit tests pass.
- Run `./vendor/bin/pest --group=browser` and monitor for failures that were previously skipped.
- Manually verify the Blog Showcase and Demo pages.

### Key Scenarios
- **XHR with Morph Alias**: Verify that sending `model_type => 'blog'` still works correctly for all media actions.
- **Full Class Name Support**: Verify that components initialized with `Blog::class` still function and pass existing assertions.
- **Browser Uploads**: Verify that `attach()` works in a standard local environment (even if it fails in the agent's environment).