# Originals – storage and lifecycle

## Short answer
Originals are stored as physical files on a dedicated “originals” filesystem disk and referenced via flags in the same `media` table entries. There is no separate table for originals.

## Where the original files live
- Disk: `config('medialibrary-extensions.media_disks.originals')` determines which Laravel filesystem disk to use.
- Path convention on that disk: `"<media id>/<file_name>"`.
  - See: `src/Services/OriginalMediaService.php` → `archiveOriginalMedia()` writes to `"{$media->id}/{$media->file_name}"`.
- Demo override: the demo page sets up a public local disk for originals (so you can view them in the browser):
  - `src/Http/Controllers/DemoController.php` configures a `media_originals` disk pointing at `storage/app/public/media_originals` with URL `/storage/media_originals`.

## How and when originals are archived
- On media add: `src/Listeners/MediaHasBeenAddedListener.php`
  - On `MediaHasBeenAddedEvent`, if the model “should store originals,” it calls `OriginalMediaService::archiveOriginalMedia($media)`.
  - It then sets helpful custom properties on the `Media` record: `original_path` (e.g., `"id/filename"`) and `has_original_copy = true`.
- Implementation details: `src/Services/OriginalMediaService.php`
  - Streams from the Spatie Media’s on-disk path (`$media->getPath()`) to the originals disk.
  - Skips if the destination already exists.
  - Marks the media with `custom_properties["is_original"] = true`.

## Database representation (no separate table)
- Originals are not a separate entity; they are standard Spatie `media` rows with flags in `custom_properties`.
- Convenience model: `src/Models/OriginalMedia.php` extends Spatie’s `Media` but keeps `protected $table = 'media'`.
  - `scopeOnlyOriginals()` filters by `custom_properties->is_original = true`.
  - `isOriginal()` helper returns the flag.
  - `derivedMedia()` can locate media that reused this original via `custom_properties->original_id` when set.

## Replacement and restoration flows
- Replacement copies the archived original forward to the new media id:
  - `src/Services/MediaReplacement.php` → `replaceMedium()` calls `OriginalMediaService::copyArchivedOriginal($oldMedia, $newMedia)` which copies `"oldId/oldFilename"` to `"newId/newFilename"` on the originals disk (doesn’t overwrite if present).
- Restore original into active medium file location:
  - `src/Actions/RestoreOriginalMediaAction.php` reads `"{$media->id}/{$media->file_name}"` from the originals disk and writes it back to the media’s actual storage disk/path, then marks conversions for regeneration.

## TL;DR
- Files: originals are archived under `originals disk / <media id>/<file_name>`.
- DB: same `media` table; originals are indicated via `custom_properties` flags (`is_original`, `has_original_copy`, `original_path`).
- Lifecycle: archived on add → optionally copied forward on replace → can be restored later via the restore action.

---

## Replacement semantics and lineage (recommended improvements)

When an image is edited and saved as a replacement, a new media record is created. The archived “original” that belonged to the replaced media should be logically linked to the new record. The current flow (create new media → optionally copy archived original from old → delete old) works, but consider the following to make lineage explicit and restoration deterministic.

### Risks with the naïve implementation
- Lineage loss: Without an explicit link on the replacement, you can’t discover that its true original came from an earlier media item.
- Restore inconsistency: If the replacement was created from a fresh upload, the listener may already have archived that new upload as the “original,” so a later “restore original” would use the edited upload, not the historical original, unless you overwrite it or follow lineage.
- Stale metadata: Blindly copying `custom_properties` may leave `original_path` pointing at the old media’s directory.

### Recommended improvements
1. Track lineage on the replacement
   - Add a property like `original_source_media_id` (or reuse a consistent key) on the new media.
   - If the old media already had this set, propagate that earliest source id; otherwise, set it to the old media’s id.

2. Decide overwrite semantics for the archived original of the replacement
   - Preferred: allow overwriting the replacement’s archived original with the earlier one during replacement for historical correctness. If you keep non-overwrite behavior, ensure your “restore” action can follow `original_source_media_id` to find the true original.

3. Normalize original flags/paths on the replacement
   - After copying, set `has_original_copy = true` and `original_path = "<newId>/<file_name>"` on the new media so the path reflects its own directory.

4. Opportunistic backfill
   - If the old media’s archived original is missing but the file exists and the model allows originals, you may first archive the old media, then perform the copy forward.

### Example changes (illustrative)

In `OriginalMediaService` (optional overwrite parameter):

```php
public function copyArchivedOriginal(Media $oldMedia, Media $newMedia, bool $overwrite = false): void
{
    $disk = Storage::disk(config('medialibrary-extensions.media_disks.originals'));
    $sourcePath = "{$oldMedia->id}/{$oldMedia->file_name}";
    $destinationPath = "{$newMedia->id}/{$newMedia->file_name}";

    if (! $disk->exists($sourcePath)) {
        // Optional: attempt to archive the old media now, then retry
        return;
    }

    if ($disk->exists($destinationPath)) {
        if (! $overwrite) {
            return; // keep the replacement’s own archived original
        }
        $disk->delete($destinationPath);
    }

    if (! $disk->copy($sourcePath, $destinationPath)) {
        throw new \RuntimeException('Failed to copy original.');
    }
}
```

In `MediaReplacement::replaceMedium(...)`, after creating and saving `$newMedia` and after copying the archived original:

```php
// Start from the previous custom properties
$newMedia->custom_properties = $backup->custom_properties ?? [];

// Lineage: prefer earliest known source id
$sourceId = $backup->getCustomProperty('original_source_media_id')
    ?? $oldMedia->getKey();
$newMedia->setCustomProperty('original_source_media_id', $sourceId);

// Reflect the archived original location for the new media
$newMedia->setCustomProperty('has_original_copy', true);
$newMedia->setCustomProperty('original_path', $newMedia->id . '/' . $newMedia->file_name);

$newMedia->save();
```

If you adopt overwrite during copy:

```php
$this->originalMediaService->copyArchivedOriginal($oldMedia, $newMedia, overwrite: true);
```

### Test strategy (PHPUnit)
- Create a model with `shouldStoreOriginals() = true`.
- Add media A, assert archived original at `A/<file>` and flags set.
- Replace A with B using `replaceMedium($a, $editedFile)`:
  - Assert `original_source_media_id` on B points to A (or earliest source if multi-hop).
  - Assert `original_path` on B is `B/<file>`.
  - If overwrite=true, confirm B’s archived original bytes match A’s archived original; if overwrite=false, confirm lineage flag is set and your restoration logic follows it.
  - Assert A is deleted.
- Replace again B → C, confirm lineage carries earliest id and paths are normalized.

### Conclusion
By recording lineage (`original_source_media_id`), deciding on overwrite semantics, and normalizing original flags/paths on the replacement, the link between a replacement medium and its historical original remains explicit and reliable for future restoration and audits.
