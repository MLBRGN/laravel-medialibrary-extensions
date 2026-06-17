<?php

namespace Mlbrgn\MediaLibraryExtensions\Support;

/**
 * Manages stable instance IDs for upload components.
 *
 * Temporary uploads must be scoped to a specific component so that multiple
 * upload components on the same page do not interfere with each other.
 *
 * The instance ID is stored in the session to keep it stable across page
 * refreshes or validation errors. Otherwise a new ID would be generated on
 * every request and previously uploaded temporary files would no longer
 * be visible to the component.
 *
 * Each component provides an `$instanceKey`. The session stores a map of
 * `{instanceKey => instanceId}` and reuses the ID if it already exists,
 * otherwise a new ULID is generated.
 */
class InstanceManager
{
    public static function getInstanceId(string $originalId): string
    {
        // Use a deterministic ULID-like string based on the originalId to maintain
        // stability across page refreshes without relying on sessions.
        // We use SHA-1 to hash the originalId and then format it as a valid-looking ULID.
        $hash = sha1($originalId);

        return strtoupper(substr($hash, 0, 26));
    }
}
