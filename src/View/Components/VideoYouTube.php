<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class VideoYouTube extends Component
{
    public string $youTubeParamsAsString;

    public function __construct(
        public Media|TemporaryUpload $medium,
        public bool $preview = true,
        public string $youtubeId = '',
        public ?array $youtubeParams = [],
    ) {

        $defaultYouTubeParams = config('media-library-extensions.default_youtube_params', [
            'autoplay' => 1, // Starts playing the video automatically when loaded.
            'mute' => 1, // Starts the video muted. Required for autoplay to work in most browsers.
            'loop' => 0, // Loops the video. Must be used with playlist={videoId} to work properly.
            'controls' => 0, // Hides / shows the default YouTube controls
            'modestbranding' => 1, // Reduces YouTube branding
            'playsinline' => 1, // On iOS, plays the video inline instead of opening fullscreen.
            'rel' => 0, // Prevents showing related videos from other channels at the end
            'enablejsapi' => 1,	// Required by "lite-youtube". Enables JavaScript API control of the player.
            'cc_load_policy' => 1, // Forces closed captions to be displayed (if available).
            'cc_lang_pref' => 'en', // Sets the default language for captions (e.g., en, nl).
            'iv_load_policy' => 3, // Shows (1) or hides (3) video annotations like popups or cards.
            'hl' => 'en', // Sets the interface language for the player
            'fs' => 1, // Hides (0) or shows (1) the fullscreen button
        ]);

        $mergedParams = array_merge($defaultYouTubeParams, $youtubeParams ?? []);
        $this->youTubeParamsAsString = http_build_query($mergedParams);

    }

    public function render(): View
    {
        return view('media-library-extensions::components.video-youtube');
    }
}
