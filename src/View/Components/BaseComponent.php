<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

abstract class BaseComponent extends Component
{
    public string $theme;

    public ?array $status = [];

    public string $frontend;

    public function __construct(
        public string $id,
        public ?string $frontendTheme = null

    ) {
        $this->theme = config('media-library-extensions.frontend_theme', 'plain');
        $this->status = session(status_session_prefix());

        if (empty($this->id)) {
            $this->id = 'component-'.uniqid();
        }

        $this->frontend = $frontendTheme ?? config('media-library-extensions.frontend_theme', 'plain');

    }

    public function getView($viewName): View
    {
        $viewPath = "media-library-extensions::components.$this->frontend.$viewName";

        if (! view()->exists($viewPath)) {
            $viewPath = "media-library-extensions::components.plain.$viewName";
        }

        return view($viewPath);
    }

    // prevent n+1 queries
    //    protected function ensureMediaIsLoaded(Model $model): Model
    //    {
    //
    //        return $model->relationLoaded('media')
    //            ? $model
    //            : $model->newQuery()->with('media')->findOrFail($model->getKey());
    //
    //    }
}
