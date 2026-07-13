# Testing

This guide covers how to test the extended media features, specifically temporary upload promotion.

---

## 1. Local Package Development

If you are developing the package itself, use the provided test suite:

```shell
composer test
```

To run a specific test:

```shell
composer test -- --filter=TemporaryUploadPromoterTest
```

---

## 2. Testing Temporary Upload Promotion

Testing promotion involves simulating the transition from a temporary file (stored via XHR) to a permanent media attachment.

### How it Works
The promotion is handled by the `TemporaryUploadPromoter` service, which is automatically triggered by the `InteractsWithMediaExtended` trait during the model's `created` or `updated` Eloquent events.

1.  **XHR Upload**: A file is uploaded to a temporary disk, and a `TemporaryUpload` record is created with a `client_token`.
2.  **HTML Insertion**: The temporary URL is inserted into an HTML editor field.
3.  **Model Save**: When the model is saved, the promoter finds all temporary uploads matching the `client_token`, attaches them to the model, and replaces temporary URLs in the HTML fields with permanent media URLs.

---

### Step-by-Step Manual Test
To test this manually on the `DemoPage`:

1.  **Upload a File**: Use one of the media uploader components. This triggers an XHR request to `/media-manager-upload-multiple`.
2.  **Verify Temporary URL**: Check the network tab or the component's state to get the temporary URL (e.g., `/storage/media_temporary/your-file.jpg`).
3.  **Insert into HTML**: If you have an HTML editor (like TinyMCE), ensure the model has `getHtmlEditorFields()` defined.
4.  **Save the Model**: Trigger a save on the model. Ensure the `client_token` is sent with the request (the package handles this via cookies or hidden inputs).

---

### Automated Feature Test (Pest)
You can create a feature test to verify this behavior without a browser.

```php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\TemporaryUploadPromoter;
use Mlbrgn\MediaLibraryExtensions\Tests\Support\Models\TestPost;

it('promotes temporary uploads to permanent media', function () {
    Storage::fake('public');
    $tempDisk = config('medialibrary-extensions.media_disks.temporary');
    
    // 1. Create a model with a temporary URL in its HTML content
    $filename = 'demo-image.jpg';
    $post = TestPost::create([
        'content' => "<img src=\"/storage/media_temporary/{$filename}\">",
    ]);

    // 2. Simulate a successful XHR temporary upload
    $clientToken = (string) Str::ulid();
    TemporaryUpload::create([
        'disk' => $tempDisk,
        'path' => $filename,
        'name' => 'demo-image',
        'file_name' => $filename,
        'collection_name' => 'images',
        'client_token' => $clientToken,
        'mime_type' => 'image/jpeg',
        'size' => 1024,
    ]);
    Storage::disk($tempDisk)->put($filename, 'fake-content');

    // 3. Trigger promotion (this normally happens on model save)
    app(TemporaryUploadPromoter::class)->promoteAllForModel($post, null, $clientToken);

    // 4. Verify results
    $post->refresh();
    $media = $post->getFirstMedia('images');

    // Check if URL was replaced in HTML
    expect($post->content)->toContain($media->getUrl());
    expect($post->content)->not->toContain('media_temporary');

    // Check if temporary file and record were cleaned up
    expect(TemporaryUpload::count())->toBe(0);
    Storage::disk($tempDisk)->assertMissing($filename);
});
```

---

### Key Considerations
*   **`client_token`**: Ensure the `client_token` used during upload matches the one passed to `promoteAllForModel`.
*   **`getHtmlEditorFields()`**: The promoter only scans fields returned by this method in your model.
*   **Events**: If you are testing via Eloquent, simply calling `$model->save()` is enough to trigger the promotion if the trait is used.
