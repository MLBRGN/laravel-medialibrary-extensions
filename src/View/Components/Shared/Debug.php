<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Shared;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Component;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\ResolvedModel;
use Mlbrgn\MediaLibraryExtensions\Support\DebugManager;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Debug extends Component
{
    use InteractsWithOptionsAndConfig;

    public bool $iconExists = false;

    public array $errors = [];

    public Collection $collections;

    public string $id;

    public string $modelType;
    public ?int $modelId;
    public bool $temporaryUploadMode;
    public ?Model $model;

    private ResolvedModel $resolvedModel;

    public function __construct(
        // New preferred prop; legacy supported via sync below
        public mixed $modelReference = null,
        public mixed $modelOrClassName = null,// either a model that implements HasMedia or it's class name
        array $config = [],
        array $options = [],
        public ?string $dataSource = 'default',
    ) {

        $this->config = $config;
        $this->options = $options;
        $this->id = uniqid();

        $mediaService = app(MediaService::class);
        // Normalize props for BC
        $effectiveRef = $this->modelReference ?? $this->modelOrClassName;
        if ($effectiveRef === null) {
            // Preserve legacy behavior: missing model context is a programmer error
            throw new \TypeError('model-or-class-name must be either a HasMedia model or a string representing the model class');
        }

        $this->resolvedModel = $mediaService->resolveModelOrClassName($effectiveRef, $dataSource);

        $this->model = $this->resolvedModel->model;
        $this->modelType = $this->resolvedModel->modelType;
        $this->modelId = $this->resolvedModel->modelId;
        $this->temporaryUploadMode = $this->resolvedModel->temporaryUploadMode;

        $this->iconExists = collect(Blade::getClassComponentAliases())
            ->keys()
            ->contains(config('medialibrary-extensions.icons.delete'));

        if (! $this->iconExists) {
            $this->errors[] = __('medialibrary-extensions::messages.no_blade_ui_kit_icon_package_detected_download_at_:link', [
                'link' => '<a href="https://github.com/driesvints/blade-icons" target="_blank">Blade UI Kit icon package</a>',
            ]);
        }

        // Optional: guard against model being null to avoid exception
        if ($this->resolvedModel->model) {
            $this->collections = Media::where('model_type', $this->resolvedModel->model->getMorphClass())
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
        // Determine the active database details for the resolved model (if any)
        $connectionName = null;
        $databaseName = null;

        if ($this->resolvedModel->model) {
            try {
                $connectionName = $this->resolvedModel->model->getConnectionName()
                    ?: $this->resolvedModel->model->getConnection()->getName();
            } catch (\Throwable $e) {
                $connectionName = null;
            }

            try {
                $databaseName = $this->resolvedModel->model->getConnection()->getDatabaseName();
            } catch (\Throwable $e) {
                $databaseName = null;
            }
        }

        $dataSource = $this->dataSource; // passed into the component

        return collect(['image', 'document', 'video', 'audio', 'youtube'])
            ->map(function ($type) use ($dataSource, $connectionName, $databaseName) {
                $collectionName = $this->getConfig('collections')[$type] ?? null;

                $count = ($this->resolvedModel->model && $collectionName)
                    ? $this->resolvedModel->model->getMedia($collectionName)->count()
                    : 0;

                return [
                    'type' => $type,
                    'collection' => is_array($collectionName) ? implode(', ', $collectionName) : ($collectionName ?? 'n/a'),
                    'count' => $count,
                    'data_source' => $dataSource,
                    'database' => $databaseName ?? 'n/a',
                    'connection' => $connectionName ?? 'n/a',
                ];
            });
    }

    /**
     * Return a concise set of the most important component properties for debugging.
     * This mirrors what the frontend component receives from its config/options.
     *
     * @return array{
     *     clientToken: mixed,
     *     collections: array<string, mixed>|null,
     *     dataSource: string|null,
     *     id: string|null,
     *     instanceId: string|null,
     *     isAtMax: bool|null,
     *     isEmpty: bool|null,
     *     maxMediaCount: int|null,
     *     theme: string|null,
     *     totalMediaCount: int|null,
     *     useXhr: bool|null
     * }
     */
    public function getMainComponentProperties(): array
    {
        // Prefer values coming from the component config; fall back to known alternatives
        $props = [
            'clientToken' => $this->getConfig('clientToken'),
            'collections' => $this->getConfig('collections') ?? null,
            'dataSource' => $this->getConfig('dataSource') ?? $this->dataSource,
            'id' => $this->getConfig('id'), // logical component id
            'instanceId' => $this->getConfig('instanceId'),
            'isAtMax' => $this->getConfig('isAtMax'),
            'isEmpty' => $this->getConfig('isEmpty'),
            'maxMediaCount' => $this->getConfig('maxMediaCount'),
            'theme' => $this->getConfig('theme'),
            'totalMediaCount' => $this->getConfig('totalMediaCount'),
            'useXhr' => $this->getConfig('useXhr') ?? $this->getConfig('use_xhr'),
        ];

        // Sanitize nested complex values for safe debug output
        return $this->getSanitizedConfig($props);
    }

    public function render(): View
    {
        return view('medialibrary-extensions::components.shared.debug');
    }
}
