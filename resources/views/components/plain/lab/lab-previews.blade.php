<div class="media-preview-grid" data-media-preview-grid>
    <x-mle-lab-preview
        class="mle-media-lab-preview-original"
        title="{{ __('media-library-extensions::messages.original') }}"
        :model-or-class-name="$medium->model"
    >
        @if(method_exists($medium->model, 'getArchivedOriginalUrlFor'))
            <img src="{{ $medium->model->getArchivedOriginalUrlFor($medium) }}"
                 alt=""
                 class="media-preview-image"
            >
        @else
            Geen origineel opgeslagen
        @endif

        <x-slot name="menuStart">

        </x-slot>

        <x-slot name="menuEnd">
            <x-mle-partial-medium-restore-form
                :model-or-class-name="$medium->model"
                :medium="$medium"
            />
        </x-slot>
    </x-mle-lab-preview>

    <div class="mle-media-lab-preview-base">
        <div class="media-lab-title">
            {{ __('media-library-extensions::messages.base') }}
        </div>
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
                            'showUploadForms' => false,
                        ]"
            :single-medium="$medium"
        />
    </div>

{{--    <x-mle-lab-preview--}}
{{--        class="mle-media-lab-conversions"--}}
{{--        title="{{ __('media-library-extensions::messages.conversion') }}"--}}
{{--        :model-or-class-name="$medium->model"--}}
{{--    >--}}
{{--        @foreach(array_keys($medium->extra_meta['conversions']) as $conversionName)--}}
{{--            <x-mle-image-responsive--}}
{{--                :medium="$medium"--}}
{{--                :conversions="[$conversionName]"--}}
{{--                class="mx-auto media-preview-image"--}}
{{--            />--}}
{{--        @endforeach--}}
{{--    </x-mle-lab-preview>--}}
</div>