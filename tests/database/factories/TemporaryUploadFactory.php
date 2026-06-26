<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;

class TemporaryUploadFactory extends Factory
{
    protected $model = TemporaryUpload::class;

    public function definition()
    {
        $fileName = $this->faker->word().'.jpg';
        $directory = 'tmp/uploads';
        $userId = $this->faker->numberBetween(1, 10);
        $clientToken = Str::uuid();

        return [
            'disk' => 'public',
            'path' => "{$directory}/{$fileName}",
            'name' => pathinfo($fileName, PATHINFO_FILENAME),
            'file_name' => $fileName,
            'collection_name' => $this->faker->randomElement(['images', 'videos', 'documents']),
            'mime_type' => 'image/jpeg',
            'size' => $this->faker->numberBetween(1000, 5000),
            'user_id' => $userId,
            'client_token' => $clientToken,
            'order_column' => 0,
            'custom_properties' => [
                'priority' => 0,
            ],
        ];
    }

    public function forCollection(string $collectionName)
    {
        return $this->state(fn () => ['collection_name' => $collectionName]);
    }

    public function withPriority(int $priority)
    {
        return $this->state(fn () => [
            'order_column' => $priority,
            'custom_properties' => ['priority' => $priority],
        ]);
    }

    public function withBaseId(string $baseId)
    {
        return $this->state(fn () => [
            'instance_id' => InstanceManager::getInstanceId($baseId),
        ]);
    }

    public function withInstanceId(string $instanceId)
    {
        return $this->state(fn () => [
            'instance_id' => $instanceId,
        ]);
    }

    public function withClientToken(string $clientToken)
    {
        return $this->state(fn () => [
            'client_token' => $clientToken,
        ]);
    }
}
