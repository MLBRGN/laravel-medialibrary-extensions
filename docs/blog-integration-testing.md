# Blog Integration Testing

This document outlines the strategy and implementation for integration testing using a "Blog" model. These tests complement the existing demo page tests by providing a structured, multi-component integration scenario.

## Overview
The goal is to ensure that all media management components (Single, Multiple, Carousel, Media Lab) work together seamlessly when bound to a standard Eloquent model.

## Model Structure: `Blog`
The `Blog` model (located in `tests/Models/Blog.php`) is configured with the following media collections:

- `blog-main`: A single image collection for the featured image of a post.
- `blog-gallery`: A multiple image collection for a post's gallery.
- `blog-youtube`: A single image collection restricted to YouTube video URLs.
- `blog-lab`: A single image collection used for testing Media Lab (cropping and conversions).

## Showcase Page: `/blog-showcase`
A dedicated showcase page is used to render these components. It supports:
- **Themes**: Bootstrap 5 and Plain (controlled via `theme` query param).
- **Data Source**: XHR and standard form submission (controlled via `use_xhr` query param).

## Testing Strategy

### Automated Browser Tests
We use **Pest Browser** (powered by Laravel Dusk) to perform end-to-end testing of the showcase page.

#### Scenarios
1. **Featured Image (Single)**
   - Uploading a new image.
   - Replacing an existing image.
   - Deleting the image.
   - Verification: Check database and filesystem for media presence/absence.

2. **Gallery (Multiple)**
   - Uploading multiple images at once.
   - Reordering images via drag-and-drop.
   - Deleting specific images from the gallery.
   - Verification: Check `order_column` in the `media` table.

3. **YouTube Integration**
   - Pasting a YouTube URL into the uploader.
   - Verifying that the thumbnail is correctly downloaded and associated.

4. **Media Lab**
   - Opening the Media Lab for an uploaded image.
   - Applying a crop/conversion.
   - Saving and verifying the resulting media update.

5. **Cross-Compatibility**
   - Running all above scenarios across both **Bootstrap 5** and **Plain** themes.
   - Running all above scenarios using both **XHR** and **Non-XHR** (standard form) methods.

## Execution
Tests are located in `tests/Browser/BlogIntegrationTest.php`.
To run the browser tests:
```bash
composer test-browser
```
