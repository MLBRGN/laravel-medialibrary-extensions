<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Component;
use Illuminate\View\View;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Debug extends Component
{
    public bool $iconExists = false;
    public array $errors = [];
    public Collection $collections;

    public function __construct(
        public ?HasMedia $model = null,
        public string|null $modelType = null,
        public string|null $modelId = null,
        public ?string $theme = null,
        public array $config = [],
    ) {
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
            $this->collections = Media::where('model_type', $model->getMorphClass())
                ->get()
                ->pluck('collection_name')
                ->unique();
        } else {
            $this->collections = collect();
        }
    }

    public function render(): View
    {
        return view('media-library-extensions::components.partial.debug');
    }
}
