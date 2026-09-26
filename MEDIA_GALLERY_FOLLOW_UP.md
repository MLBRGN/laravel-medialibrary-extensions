### MediaGallery Follow-up Plan

This plan addresses technical debt, testing standardization, and consistency issues identified during the initial implementation of the `MediaGallery` component.

#### ✅ Must-Do Items (High Priority)

1.  **Refactor Media Resolution for Consistency**
    *   **Description**: Replace the custom media aggregation and sorting logic in `MediaGallery::resolveMedia()` with a call to the `MediaRetriever` service.
    *   **Goal**: Ensure media is fetched and ordered identically across the Gallery, Carousel, and Modal components, preventing potential `data-bs-slide-to` mismatches and simplifying maintenance.

2.  **Standardize Feature Tests with Snapshots**
    *   **Description**: Update `tests/Feature/Components/MediaGalleryTest.php` to use `toMatchSnapshot()` for HTML verification.
    *   **Goal**: Align with the project's established testing patterns (as seen in `MediaModalTest.php`) and ensure the complex structure of CSS variables and layout classes remains stable.

3.  **Use Standard Test Helpers**
    *   **Description**: Refactor `MediaGalleryTest.php` to use the `$this->getModelWithMedia()` helper instead of manual media creation.
    *   **Goal**: Improve test readability and maintain consistency with other component tests in the repository.

#### 🛠️ Recommended Improvements (Medium Priority)

4.  **Multi-Instance Isolation Verification**
    *   **Description**: Add a test scenario (Feature or Browser) that renders two `MediaGallery` components for different collections on a single page.
    *   **Goal**: Explicitly verify that DOM IDs (e.g., `*-gal` and `*-mod`) are correctly isolated and that modal triggers do not collide when multiple galleries exist.

5.  **Autoplay Configuration Hardening**
    *   **Description**: Ensure the `videoAutoPlay` option is explicitly handled in the `MediaGallery` to `MediaModal` hand-off.
    *   **Goal**: Provide users with clear control over video behavior within the gallery-triggered modal.

6.  **Documentation & Examples**
    *   **Description**: Add `MediaGallery` usage examples to the `README.md` or a dedicated documentation file.
    *   **Goal**: Showcase the `layout` (grid/flex), `columns`, and `gap` attributes to help developers adopt the new component quickly.
