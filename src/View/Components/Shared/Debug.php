<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Shared;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Component;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Debug extends Component
{
    use ResolveModelOrClassName;

    public bool $iconExists = false;

    public array $errors = [];

    public Collection $collections;

    public string $id;

    public function __construct(
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public ?string $frontendTheme = null,
        public array $config = [],
        public array $options = [],
    ) {

        $this->id = uniqid();

        $this->resolveModelOrClassName($modelOrClassName);

        $this->iconExists = collect(Blade::getClassComponentAliases())
            ->keys()
            ->contains(config('media-library-extensions.icons.delete'));

        if (! $this->iconExists) {
            $this->errors[] = __('media-library-extensions::messages.no_blade_ui_kit_icon_package_detected_download_at_:link', [
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

    public function render(): View
    {
        return view('media-library-extensions::components.shared.debug');
    }
}
