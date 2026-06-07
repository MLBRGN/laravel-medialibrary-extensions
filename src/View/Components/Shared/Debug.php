<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Shared;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Component;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Support\DebugManager;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Debug extends Component
{
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    public bool $iconExists = false;

    public array $errors = [];

    public Collection $collections;

    public string $id;

    public function __construct(
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        array $config = [],
        array $options = [],
    ) {

        $this->config = $config;
        $this->options = $options;
        $this->id = uniqid();

        $this->resolveModelOrClassName($modelOrClassName);

        $this->iconExists = collect(Blade::getClassComponentAliases())
            ->keys()
            ->contains(config('medialibrary-extensions.icons.delete'));

        if (! $this->iconExists) {
            $this->errors[] = __('medialibrary-extensions::messages.no_blade_ui_kit_icon_package_detected_download_at_:link', [
                'link' => '<a href="https://github.com/driesvints/blade-icons" target="_blank">Blade UI Kit icon package</a>',
            ]);
        }

        // Optional: guard against model being null to avoid exception
        if ($this->model) {
            $this->collections = Media::where('model_type', $this->model->getMorphClass())
                ->get()
                ->pluck('collection_name')
                ->unique();
        } else {
            $this->collections = collect();
        }
    }

    public function getComponents(): array
    {
        return DebugManager::getRegisteredComponents($this->getConfig('id'));
    }

    /**
     * Recursively sanitize a config array so nested objects are replaced by class name placeholders.
     */
    public function getSanitizedConfig(array $array): array
    {
        // Sort keys alphabetically
        ksort($array);

        return array_map(function ($value) {
            if (is_object($value)) {
                // Replace objects with their class names for safe debugging
                return '['.get_class($value).']';
            } elseif (is_array($value)) {
                // Recurse into nested arrays
                return $this->getSanitizedConfig($value);
            }

            return $value;
        }, $array);
    }

    public function getCollectionDebugData(): Collection
    {
        return collect(['image', 'document', 'video', 'audio', 'youtube'])
            ->map(function ($type) {
                $collectionName = $this->getConfig('collections')[$type] ?? null;

                $count = ($this->model && $collectionName)
                    ? $this->model->getMedia($collectionName)->count()
                    : 0;

                return [
                    'type' => $type,
                    'collection' => is_array($collectionName) ? implode(', ', $collectionName) : ($collectionName ?? 'n/a'),
                    'count' => $count,
                ];
            });
    }

    public function render(): View
    {
        return view('medialibrary-extensions::components.shared.debug');
    }
}
