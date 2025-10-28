<div class="mle-media-lab-preview-base" data-media-lab-preview-base>
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