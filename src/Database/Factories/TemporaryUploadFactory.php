<?php

namespace Mlbrgn\MediaLibraryExtensions\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

/**
 * @extends Factory<\Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload>
 */
class TemporaryUploadFactory extends Factory
{
    protected $model = TemporaryUpload::class;

    public function definition(): array
    {
        // Ensure there's a valid session before creating uploads
        if (! session()->isStarted()) {
            session()->start();
        }

        $collection = $this->faker->randomElement(['images', 'documents', 'audio', 'video']);
        $extension = match ($collection) {
            'audio' => 'mp3',
            'video' => 'mp4',
            'documents' => 'pdf',
            default => 'jpg',
        };

        $fileName = $this->faker->uuid.'.'.$extension;

        return [
            'disk' => 'public',
            'path' => "uploads/{$collection}/{$fileName}",
            'name' => pathinfo($fileName, PATHINFO_FILENAME),
            'file_name' => $fileName,
            'collection_name' => $collection,
            'mime_type' => $this->mimeTypeFor($extension),
            'size' => $this->faker->numberBetween(500, 5_000_000),
            'session_id' => session()->getId(),
            'user_id' => null,
            'custom_properties' => [],
            'order_column' => $this->faker->numberBetween(1, 10),
        ];
    }

    /**
     * Helper to pick a MIME type for the given extension.
     */
    protected function mimeTypeFor(string $extension): string
    {
        return match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'pdf' => 'application/pdf',
            'mp3' => 'audio/mpeg',
            'mp4' => 'video/mp4',
            default => 'application/octet-stream',
        };
    }

    /**
     * State: assign a specific collection name.
     */
    public function forCollection(string $collection): static
    {
        return $this->state(fn () => ['collection_name' => $collection]);
    }

    /**
     * State: assign a specific session ID.
     */
    public function forSession(string $sessionId): static
    {
        return $this->state(fn () => ['session_id' => $sessionId]);
    }
}
