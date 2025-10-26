<div class="mlbrgn-mle-component theme-bootstrap-5 mle-media-lab">
    <div class="media-preview-grid" data-media-preview-grid>
        <x-mle-lab-preview
            class="mle-media-lab-original"
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
                <span>global order: {{  $medium->getCustomProperty('global_order') }}</span>
            </x-slot>

            <x-slot name="menuEnd">
                <form action="{{ route('admin.media.restore-original', $medium) }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="mle-button mle-button-submit mle-button-icon btn btn-primary"
                        title="{{ __('media-library-extensions::messages.restore_original') }}"
                    >
                        <x-mle-shared-icon
                            name="{{ config('media-library-extensions.icons.restore') }}"
                            :title="__('media-library-extensions::messages.restore_original')"
                        />
                    </button>
                </form>
            </x-slot>
        </x-mle-lab-preview>
        
        <div class="mle-media-lab-base">
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
        
        <x-mle-lab-preview
            class="mle-media-lab-conversions"
            title="{{ __('media-library-extensions::messages.conversion') }}"
            :model-or-class-name="$medium->model"
        >
            @foreach(array_keys($medium->extra_meta['conversions']) as $conversionName)
                <x-mle-image-responsive
                    :medium="$medium"
                    :conversions="[$conversionName]"
                    class="mx-auto media-preview-image"
                />
            @endforeach
        </x-mle-lab-preview>
    </div>
</div>