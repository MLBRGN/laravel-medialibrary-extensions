---
sessionId: session-260906-233705-yypm
---

# Requirements

### Overview & Goals
The current browser test suite suffers from significant slowness, primarily due to:
1.  **Hardcoded Delays**: Widespread use of `pressAndWaitFor()` and `wait()` with fixed durations (e.g., 0.5s, 1.0s) adds unnecessary overhead, especially when multiplied by large test matrices.
2.  **Redundant Retries**: Tests marked as `flaky()` likely suffer from race conditions that were "fixed" by adding more sleeps, further slowing down the suite.

The goal is to replace arbitrary sleeps with smart, assertion-based waits provided by the `pest-plugin-browser`'s `AwaitableWebpage` functionality.

### Scope
- **In Scope**:
    - All files in `tests/Browser/` using `wait()` or `pressAndWaitFor()`.
- **Out of Scope**:
    - Implementing parallel execution or process isolation.
    - Changing the underlying testing framework (Pest/Playwright).
    - Refactoring business logic or UI components.

# Technical Design

### Current Implementation
- **Waiting**: Relies on `pressAndWaitFor()`, a method in the `pest-plugin-browser` that literally just executes a `wait($seconds)` after clicking.
- **Sleeps**: Widespread use of `wait(0.5)` or `wait(1.0)` to allow for XHR requests or animations to complete before making assertions.

### Proposed Changes
#### Smart Waiting Strategy
We will replace hardcoded sleeps with implicit waits provided by `AwaitableWebpage`. This class wraps assertions in a retry loop (default 1s-5s) that succeeds as soon as the condition is met.

- **Refactoring `pressAndWaitFor`**: 
    - `pressAndWaitFor($selector, 0.5) -> assertSee('Success')` becomes `press($selector) -> assertSee('Success')`.
    - If `pressAndWaitFor` is used without an immediate assertion, we will either add a relevant assertion or change it to `press($selector)`.
- **Refactoring standalone `wait`**:
    - `wait(1.0) -> assertPresent($modal)` becomes just `assertPresent($modal)`.
    - `wait(0.5)` calls used to "stabilize" the UI will be removed in favor of `AwaitableWebpage`'s retry logic.

### File Structure Changes
- `tests/Browser/*.php`: Multiple test files refactored to remove hardcoded waits.

### Risks & Mitigations
- **Race Conditions**: Removing sleeps might reveal underlying race conditions. 
    - *Mitigation*: Rely on `AwaitableWebpage`'s retry logic and adjust timeouts if necessary rather than adding back fixed sleeps.

# Testing

### Validation Approach
Verification will be performed by running the test suite in serial mode and profiling the execution time.

### Key Scenarios
1.  **Performance Improvement**: Compare total wall-clock time of `pest --group=browser` before and after optimization.
2.  **Stability**: Ensure that tests previously marked as `flaky()` or those that relied on sleeps still pass consistently.

### Edge Cases
- **Non-XHR Page Loads**: Ensure that when a button click causes a full page reload, the test still waits for the new page to load correctly before asserting.
- **TinyMCE Initialization**: Some waits are used for JS editor initialization; these must be carefully replaced with assertions like `assertPresent('.tox-tinymce')`.

# Delivery Steps

###   Step 1: Refactor `pressAndWaitFor` to use smart assertions
Search and replace all occurrences of `pressAndWaitFor($selector, $time)` with `press($selector)` in tests where it is followed by an assertion.

- Target files: `MediaManagerSingleTest.php`, `ScopingAndIsolationTest.php`, `DemoPageTest.php`, `BlogIntegrationTest.php`.
- Remove the `$time` parameter and rely on the subsequent assertion to trigger the wait.

###   Step 2: Replace standalone `wait` calls with smart waiting
Remove standalone `wait($seconds)` calls and ensure the following assertions cover the necessary wait time.

- Target major test files: `BlogWorkflowTest.php`, `HtmlEditorTest.php`, `MediaCarouselTest.php`, `MediaLabTest.php`, `MediaManagerMultipleTest.php`, `MediaManagerSingleTest.php`.
- Pay special attention to conditionals where `$xhr` checks are used to toggle `wait()`.

###   Step 3: Validate performance gains and stability
Confirm that the changes result in a faster, more stable test suite.

- Run the full browser test suite using `php vendor/bin/pest --group=browser --profile`.
- Compare the new execution time against the baseline to quantify performance gains.
- Verify that tests are stable without the hardcoded delays.