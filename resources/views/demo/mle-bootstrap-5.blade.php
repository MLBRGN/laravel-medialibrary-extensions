@php
    use Mlbrgn\LaravelFormComponents\View\Components\Form;
    use App\Models\Blog;
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Bootstrap-5 Component tests</title>
        <link rel="icon" type="image/x-icon" href="{{ route('mle.favicon') }}">
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            rel="stylesheet"
            {{--            integrity="sha384-QWTKZyjpPEjISv5WaRU5M6QdFVb2l9gCk0GZg6CJWjvvoE5yOAy+n9C80+XW9HdT"--}}
            crossorigin="anonymous"
        >
        <style>
            .mle-demo-media-first-available-container {
                height:200px;
                overflow: hidden;
            }
        </style>
        <script type="module">
            if (!window.imageEditorLoaded) {
                const script = document.createElement('script');
                script.type = 'module';
                script.src = "{{ asset('vendor/mlbrgn/media-library-extensions/js/root/image-editor.js') }}";
                document.head.appendChild(script);
                window.imageEditorLoaded = true;

                console.log('imageEditorLoaded');
            }
        </script>
    </head>
    <body>
        <div class="container-lg mt-5">
            <h1 class="text-primary">Bootstrap-5 Component tests</h1>
            <h2 class="my-5">Media Manager Single</h2>
        
            <x-mle-media-manager-single
                id="alien-single"
                :model-or-class-name="$model"
                {{--            :single-medium="$model->getMedia('alien-single-image')->first()"--}}
                :collections="[
                    'image' => 'alien-single-image',
                    'document' =>'alien-single-document',
                    'youtube' =>'alien-single-youtube-video',
                    'video' =>'alien-single-video',
                    'audio' =>'alien-single-audio',
                ]"
                :options="[
                    'frontendTheme' => 'bootstrap-5',
                ]"
                class="mt-5"
            />
        
            <h2 class="my-5">Media Manager Single (Temporary uploads)</h2>
            
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
                    'frontendTheme' => 'bootstrap-5',
                ]"
            />
            
            <h2 class="my-5">Media Manager Multiple</h2>
            
            <x-mle-media-manager-multiple
                id="alien-multiple"
                :model-or-class-name="$model"
                :collections="[
                    'image' => 'alien-multiple-images',
                    'document' =>'alien-multiple-documents',
                    'youtube' =>'alien-multiple-youtube-videos',
                    'video' =>'alien-multiple-videos',
                    'audio' =>'alien-multiple-audios',
                ]"
                :options="[
                    'showOrder' => true,
                    'frontendTheme' => 'bootstrap-5',
                ]"
                class="mt-5"
            />
            
            <h2 class="my-5">Media Manager Multiple (Temporary uploads)</h2>
            
            <x-mle-media-manager-multiple
                id="alien-multiple-temporary-uploads"
                model-or-class-name="Mlbrgn\MediaLibraryExtensions\Models\demo\Alien"
                :collections="[
                    'image' => 'alien-multiple-images',
                    'document' =>'alien-multiple-documents',
                    'youtube' =>'alien-multiple-youtube-videos',
                    'video' =>'alien-multiple-videos',
                    'audio' =>'alien-multiple-audios',
                ]"
                class="mt-5"
                :options="[
                    'showOrder' => true,
                    'frontendTheme' => 'bootstrap-5',
                ]"
            />
            
            <h2 class="my-5">Media Manager YouTube only</h2>
            
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
                class="mt-5"
                :options="[
                    'showOrder' => true,
                    'frontendTheme' => 'bootstrap-5',
                ]"
            />
            
            <h2 class="my-5">Media Manager YouTube only (Temporary uploads)</h2>
            
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
                :options="[
                    'showOrder' => true,
                    'frontendTheme' => 'bootstrap-5',
                ]"
                class="mt-5"
            />
            
            <h2 class="my-5">Media Carousel</h2>
            
            <p>{{ __('media-library-extensions::messages.note_carousel_only_updates_on_refresh_of_page') }}</p>
            
            <x-mle-media-carousel
                id="alien-media-carousel"
                :model-or-class-name="$model"
                :collections="[
                    'alien-single-image', 
                    'alien-single-document', 
                    'alien-single-youtube-video',
                    'alien-single-video',
                    'alien-single-audio',
                    'alien-multiple-images', 
                    'alien-multiple-documents', 
                    'alien-multiple-youtube-videos',
                    'alien-multiple-videos',
                    'alien-multiple-audios',
                ]"
                class="my-5"
            />
            
            <h2 class="my-5">Media Carousel (Temporary)</h2>
            
            <p>{{ __('media-library-extensions::messages.note_carousel_only_updates_on_refresh_of_page') }}</p>
            
            <x-mle-media-carousel
                id="alien-media-carousel-temporary-uploads"
                model-or-class-name="Mlbrgn\MediaLibraryExtensions\Models\demo\Alien"
                :collections="[
                    'image' => ['alien-single-image', 'alien-multiple-images'],
                    'document' => ['alien-single-document', 'alien-multiple-documents'], 
                    'youtube' => ['alien-single-youtube-video', 'alien-multiple-youtube-videos'],
                    'video' => ['alien-single-video', 'alien-multiple-videos'],
                    'audio' => ['alien-single-audio', 'alien-multiple-audios'],
                ]"
                class="my-5"
            />

            <h2 class="my-5">Media lab</h2>

            <x-mle-media-lab
                id="demo-lab"
                :medium="$medium"
                :options="['frontendTheme' => 'bootstrap-5']"
            />
            
            <h2 class="my-5">Media first available</h2>
            
            <div class="mle-demo-media-first-available-container">
                <x-mle-first-available
                    id="media-first-available"
                    :model-or-class-name="$model"
                    :collections="[
                        'image' => 'alien-single-image', 
                        'document' => 'alien-single-document', 
                        'youtube' => 'alien-single-youtube-video',
                        'video' => 'alien-single-video', 
                        'audio' => 'alien-single-audio', 
                    ]"
                    class="mle-width-100 mle-height-100"
                />
            </div>
            
            @if (app()->environment('local') && class_exists(Form::class))
                <h2 class="my-5">Mlbrgn Form components custom file picker integration</h2>
                @php
                    $blog = Blog::all()->first();
                @endphp
                <x-form-form 
                    action="{{ route('admin.blogs.update', $blog) }}" 
                    method="put" 
                    enctype="multipart/form-data"
                    class="my-5"
                >
                    <x-form-html-editor
                        name="content"
                        label="Content *"
                        :tinymce-config="[]"
                        :extra-form-data="[
                            'model_type' => $blog->getMorphClass(),
                            'model_id' => $blog->getKey(),
                            'collection_name' => 'blog-images-extra',
                            'collections' => ['image' => 'blog-images-extra']
                        ]"
                        data-mle-model-type="{{ $blog->getMorphClass() }}"
                        data-mle-model-id="{{ $blog->getKey() }}"
                        :data-mle-collections="json_encode([
                            'image' => 'blog-images-extra',
                        ])"
                    />
                </x-form-form>
            @else
                form components not available, skipping demo
            @endif
        </div>
        {{-- Bootstrap 5 JS Bundle with Popper --}}
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
                {{--                integrity="sha384-SrVJJmZaeJbk2nXpcoZ8jP+gNcTo6MSuEiwF5Bd9TIUO6Up9qX3YqZJXfKh1WTRi" --}}
                crossorigin="anonymous"></script>
    </body>
</html>
