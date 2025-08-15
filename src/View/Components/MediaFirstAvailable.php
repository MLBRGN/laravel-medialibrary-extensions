<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\View\Component;
use Illuminate\View\View;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaFirstAvailable extends Component
{
    public ?Media $medium = null;
    public HasMedia|null $model = null;

    public ?string $modelType = null;

    public mixed $modelId = null;

    public bool $temporaryUpload = false;

    public function __construct(
        public HasMedia|string $modelOrClassName,
        public ?array $mediaCollections = [],
        public ?string $frontendTheme = null,
        public string $id = '',
        public array $options = [],
    ) {

//        $defaultYouTubeParams = config('media-library-extensions.default_youtube_params', [
//            'autoplay' => 1, // Starts playing the video automatically when loaded.
//            'mute' => 1, // Starts the video muted. Required for autoplay to work in most browsers.
//            'loop' => 0, // Loops the video. Must be used with playlist={videoId} to work properly.
//            'controls' => 0, // Hides / shows the default YouTube controls
//            'modestbranding' => 1, // Reduces YouTube branding
//            'playsinline' => 1, // On iOS, plays the video inline instead of opening fullscreen.
//            'rel' => 0, // Prevents showing related videos from other channels at the end
//            'enablejsapi' => 1,	// Required by "lite-youtube". Enables JavaScript API control of the player.
//            'cc_load_policy' => 1, // Forces closed captions to be displayed (if available).
//            'cc_lang_pref' => 'en', // Sets the default language for captions (e.g., en, nl).
//            'iv_load_policy' => 3, // Shows (1) or hides (3) video annotations like popups or cards.
//            'hl' => 'en', // Sets the interface language for the player
//            'fs' => 1, // Hides (0) or shows (1) the fullscreen button
//        ]);

//        $mergedParams = array_merge($defaultYouTubeParams, $youtubeParams ?? []);
//        $this->youTubeParamsAsString = http_build_query($mergedParams);

        if ($modelOrClassName instanceof HasMedia) {
            $this->model = $modelOrClassName;
            $this->modelType = $modelOrClassName->getMorphClass();
            $this->modelId = $modelOrClassName->getKey();

            // Find the first medium from the ordered collections
            $this->medium = collect($this->mediaCollections ?? [])
                ->map(fn (string $collection) => $this->model->getFirstMedia($collection))
                ->filter()// remove falsy values
                ->first();
        } elseif (is_string($modelOrClassName)) {
            throw new Exception('Temporary uploads Not implemented yet');
//            $this->model = null;
//            $this->modelType = $modelOrClassName;
//            $this->modelId = null;
//            $this->temporaryUpload = true;
        } else {
            throw new Exception('model-or-class-name must be either a HasMedia model or a string representing the model class');
        }

        $this->frontendTheme = $frontendTheme ?? config('media-library-extensions.frontend_theme');
    }

    public function render(): View
    {
        return view('media-library-extensions::components.media-first-available');
    }
}
