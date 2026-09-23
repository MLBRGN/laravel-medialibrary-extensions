<?php

namespace Mlbrgn\MediaLibraryExtensions\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Traits\ChecksMediaLimits;
use Spatie\MediaLibrary\HasMedia;

abstract class AbstractMediaCountRule implements ValidationRule
{
    use ChecksMediaLimits;

    protected HasMediaExtended|string|null $model;

    protected array $collections;

    protected ?string $instanceId;

    protected ?string $clientToken;

    protected ?string $dataSource;

    protected bool $multiple = true;

    /**
     * Create a new rule instance.
     *
     * @param HasMediaExtended|string|null $model The model instance or model class name.
     * @param array $collections The media collections to check.
     * @param string|null $instanceId The component instance ID (optional).
     * @param string|null $dataSource The data source to use (optional).
     * @param string|null $clientToken The client token (optional).
     */
    public function __construct(
        HasMediaExtended|string|null $model,
        array $collections,
        ?string $instanceId = null,
        ?string $dataSource = 'default',
        ?string $clientToken = null
    ) {
        $this->model = $model;
        $this->collections = $collections;
        $this->instanceId = $instanceId;
        $this->dataSource = $dataSource;
        $this->clientToken = $clientToken;
    }

    /**
     * Set whether this is for a multiple media manager.
     */
    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Resolve the instance ID from the request if not explicitly provided.
     */
    protected function resolveInstanceId(string $attribute): ?string
    {
        return $this->instanceId
            ?? request()->input("mle_instance_map.{$attribute}")
            ?? request()->input('instance_id');
    }

    /**
     * Resolve the client token from the request if not explicitly provided.
     */
    protected function resolveClientToken(): ?string
    {
        return $this->clientToken
            ?? request()->header('X-MLE-Client-Token')
            ?? request()->input('client_token');
    }

    /**
     * Get the authoritative media count from all sources.
     */
    protected function getEffectiveCount(string $attribute, mixed $value): int
    {
        return $this->getEffectiveMediaCount(
            collections: $this->collections,
            model: $this->model,
            instanceId: $this->resolveInstanceId($attribute),
            clientToken: $this->resolveClientToken(),
            dataSource: $this->dataSource,
            value: $value
        );
    }
}
