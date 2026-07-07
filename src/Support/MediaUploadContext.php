<?php
namespace Mlbrgn\MediaLibraryExtensions\Support;

class MediaUploadContext
{
    protected ?string $instanceId = null;

    protected ?string $clientToken = null;

    public function set(string $instanceId, string $clientToken): void
    {
        $this->instanceId = $instanceId;
        $this->clientToken = $clientToken;
    }

    public function instanceId(): ?string
    {
        return $this->instanceId;
    }

    public function clientToken(): ?string
    {
        return $this->clientToken;
    }

    public function hasContext(): bool
    {
        return $this->instanceId !== null
            && $this->clientToken !== null;
    }
}
