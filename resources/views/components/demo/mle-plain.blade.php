<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Component tests: theme plain</title>
        <style>
            body {
                font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            }
    
            h1, h2, h3, h4, h5, h6 {
                margin-top: 0;
                margin-bottom: .5rem;
                font-weight: 500;
                line-height: 1.2;
            }
            h1 {
                color:#0d6efd;
                font-size: 2.5rem;
            }
            h2 {
                margin-block: 1.5em;
                font-size: 2rem;
            }
            
            .demo-media-carousel {
                margin-block: 3rem;
            }
    
            @media (min-width: 1400px) {
                .mle-container-lg {
                    max-width: 1320px;
                    margin-right: auto;
                    margin-left: auto;
                }
            }
        </style>
        <link rel="icon" type="image/x-icon" href="{{ route('mle.favicon') }}">
    </head>
    <body>
        <div class="mle-container-lg">
            <h1 class="text-primary">Component tests: theme plain</h1>
        
            <h2>Media Manager Single</h2>
            
            <x-mle-media-manager-single
                id="alien-sinlge"
                :model-or-class-name="$model"
                :collections="[
                        'image' => 'alien-single-image',
                        'document' =>'alien-single-document',
                        'youtube' =>'alien-single-youtube-video',
                        'video' =>'alien-single-video',
                        'audio' =>'alien-single-audio',
                    ]"
                :options="[
                        'frontendTheme' => 'plain',
                    ]"
            />
        
            <h2>Media Manager Single (Temporary uploads)</h2>
        
            <x-mle-media-manager-single
                id="aliens-single-temporary-uploads"
                model-or-class-name="Mlbrgn\MediaLibraryExtensions\Models\demo\Alien"
                :collections="[
                        'image' => 'alien-single-image',
                        'document' =>'alien-single-document',
                        'youtube' =>'alien-single-youtube-video',
                        'video' =>'alien-single-video',
                        'audio' =>'alien-single-audio',
                    ]"
                class="mt-5"
                :options="[
                        'frontendTheme' => 'plain',
                    ]"
            />
        
            <h2>Media Manager Multiple</h2>
            
            <x-mle-media-manager-multiple
                id="alien-multiple"
                :model-or-class-name="$model"
                :collections="[
                        'image' => 'alien-multiple-image',
                        'document' =>'alien-multiple-document',
                        'youtube' =>'alien-multiple-youtube-video',
                        'video' =>'alien-multiple-video',
                        'audio' =>'alien-multiple-audio',
                    ]"
                :options="[
                        'showOrder' => true,
                        'frontendTheme' => 'plain',
                    ]"
            />
        
            <h2>Media Manager Multiple (Temporary uploads)</h2>
        
            <x-mle-media-manager-multiple
                id="alien-multiple-temporary-uploads"
                model-or-class-name="Mlbrgn\MediaLibraryExtensions\Models\demo\Alien"
                :collections="[
                        'image' => 'alien-multiple-image',
                        'document' =>'alien-multiple-document',
                        'youtube' =>'alien-multiple-youtube-video',
                        'video' =>'alien-multiple-video',
                        'audio' =>'alien-multiple-audio',
                    ]"
                class="mt-5"
                :options="[
                        'showOrder' => true,
                        'frontendTheme' => 'plain',
                    ]"
            />
        
            <h2 class="my-5">Media Manager YouTube</h2>
        
            <x-mle-media-manager-multiple
                id="alien-media-manager-youtube"
                :model-or-class-name="$model"
                :collections="[
                        'image' => '',
                        'document' => '',
                        'youtube' =>'alien-multiple-youtube-videos',
                        'video' =>'',
                        'audio' =>'',
                    ]"
                :options="[
                        'showOrder' => true,
                        'frontendTheme' => 'plain',
                    ]"
            />
        
            <h2 class="my-5">Media Manager YouTube (Temporary uploads)</h2>
        
            <x-mle-media-manager-multiple
                id="alien-media-manager-youtube-temporary"
                model-or-class-name="Mlbrgn\MediaLibraryExtensions\Models\demo\Alien"
                :collections="[
                        'image' => '',
                        'document' => '',
                        'youtube' =>'alien-multiple-youtube-videos',
                        'video' =>'',
                        'audio' =>'',
                    ]"
                class="mt-5"
                :options="[
                        'showOrder' => true,
                        'frontendTheme' => 'plain',
                    ]"
            />
            
            <h2>Media Carousel</h2>
        
            <p>{{ __('media-library-extensions::messages.note_carousel_only_updates_on_refresh_of_page') }}</p>
        
            <x-mle-media-carousel
                id="alien-media-carousel"
                :model-or-class-name="$model"
                :media-collections="[
                            'alien-single-image', 
                            'alien-single-document', 
                            'alien-single-youtube-video',
                            'alien-single-video',
                            'alien-single-audio',
                            'alien-multiple-images', 
                            'alien-multiple-documents', 
                            'alien-multiple-youtube-videos',
                            'alien-multiple-videos',
                            'alien-multiple-audio',
                        ]"
                class="demo-media-carousel"
            />
        
            <h2 class="my-5">Media Carousel (Temporary)</h2>
        
            <p>{{ __('media-library-extensions::messages.note_carousel_only_updates_on_refresh_of_page') }}</p>
        
            <x-mle-media-carousel
                id="alien-media-carousel-temporary-uploads"
                model-or-class-name="Mlbrgn\MediaLibraryExtensions\Models\demo\Alien"
                :media-collections="[
                                'alien-single-image', 
                                'alien-single-document', 
                                'alien-single-youtube-video',
                                'alien-single-video',
                                'alien-single-audio',
                                'alien-multiple-images', 
                                'alien-multiple-documents', 
                                'alien-multiple-youtube-videos',
                                'alien-multiple-videos',
                                'alien-multiple-audio',
                            ]"
                class="my-5"
            />
        
            <h2 class="my-5">Media first available</h2>
        
            <x-mle-first-available
                id="media-first-available"
                :model-or-class-name="$model"
                :media-collections="['alien-single-audio', 'alien-single-video', 'alien-single-document', 'alien-single-image', 'alien-single-youtube-video']"
            />
        </div>
    </body>
</html>
