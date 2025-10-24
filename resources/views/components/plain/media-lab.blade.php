<div class="mle-media-lab">
    <div class="mle-media-lab-original">
        <figure class="inline-block m-2 text-center">
            @if(method_exists($medium->model, 'getArchivedOriginalUrlFor'))
                <img src="{{ $medium->model->getArchivedOriginalUrlFor($medium) }}" alt="" class="dummy-mm-item"/>
                <form action="{{ route('admin.media.restore-original', $medium) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-warning">
                        Herstel origineel
                    </button>
                </form>
            @else
                Geen origineel opgeslagen
            @endif
            <figcaption class="text-sm text-gray-600 mt-1">
                @if($medium->extra_meta['original_width'] && $medium->extra_meta['original_height'])
                    <div>
                        {{ $medium->extra_meta['original_width'] }} x {{ $medium->extra_meta['original_height'] }} px.
                        ({{ number_format($medium->extra_meta['original_aspect'], 2) }})
                    </div>
                @endif
            </figcaption>
        </figure>
    </div>
    <div class="mle-media-lab-base">
        <x-mle-media-manager-single
            class=""
            id="medium-{{$medium->id}}"
            :model-or-class-name="$medium->model"
            :collections="['image' => $medium->collection_name]"
            :options="[
                                    'showDestroyButton' => false,
                                    'showSetAsFirstButton' => false,
                                    'showMediaEditButton' => true,
                                    'showMenu' => true,
//                                    'showUploadForm' => false,
                                    'showUploadForms' => false,
                                ]"
            :single-medium="$medium"
        />
    </div>
    <div class="mle-media-lab-conversions">
        @foreach(array_keys($medium->extra_meta['conversions']) as $conversionName)
            <figure class="inline-block m-2 text-center">
                <x-mle-image-responsive
                    :medium="$medium"
                    :conversions="[$conversionName]"
                    class="mx-auto dummy-mm-item"
                />
                <figcaption class="text-sm text-gray-600 mt-1">
                    {{ $conversionName }}
                    ({{ number_format($medium->extra_meta['conversions'][$conversionName], 2) }})
                </figcaption>
            </figure>
        @endforeach
    </div>
</div>
