@if ($componentToRender)
    <div class="media-manager-preview-item-container"
         data-modal-trigger="#{{$id}}-mod"
         data-slide-to="{{ $loopIndex }}"
    >
        @if($slot->isNotEmpty())
            {{ $slot }}
        @else
            <x-dynamic-component
                :component="$componentToRender"
                class="{{ $mediumType === 'image' 
                    ? 'media-manager-image-preview mle-cursor-zoom-in' 
                    : 'mle-cursor-zoom-in' }}"
                :medium="$medium"
                :options="$options"
                :draggable="$mediumType === 'image' ? 'false' : null"
                :preview="in_array($mediumType, ['youtube-video'])"
            />
        @endisset
    </div>

    @if ($mediumType === 'image')
        <x-mle-image-editor-modal
            id="{{ $id }}"
            :model-or-class-name="$modelOrClassName"
            :medium="$medium"
            :single-medium="$singleMedium"
            :collections="$collections"
            :options="$options"
            :initiator-id="$id"
            title="Edit Image"
        />
    @endif
@else
    <span class="mle-unsupported">
        {{ __('media-library-extensions::messages.non_supported_file_format') }}
    </span>
@endif

{{--@switch(true)--}}
{{--    @case(isMediaType($medium, 'youtube-video'))--}}
{{--        <div class="media-manager-preview-item-container"--}}
{{--             data-modal-trigger="#{{$id}}-mod"--}}
{{--             data-slide-to="{{ $loopIndex }}"--}}
{{--        >--}}
{{--            <x-mle-video-youtube--}}
{{--                class="mle-video-youtube mle-video-responsive mle-cursor-zoom-in"--}}
{{--                :medium="$medium"--}}
{{--                :preview="true"--}}
{{--                :options="$options"--}}
{{--            />--}}
{{--        </div>--}}
{{--        @break--}}

{{--    @case(isMediaType($medium, 'document'))--}}
{{--        <div class="media-manager-preview-item-container"--}}
{{--             data-modal-trigger="#{{$id}}-mod"--}}
{{--             data-slide-to="{{ $loopIndex }}"--}}
{{--        >--}}
{{--            <x-mle-document--}}
{{--                class="previewed-document mle-cursor-zoom-in"--}}
{{--                :medium="$medium"--}}
{{--                :options="$options"--}}
{{--            />--}}
{{--        </div>--}}
{{--        @break--}}

{{--    @case(isMediaType($medium, 'video'))--}}
{{--        <div class="media-manager-preview-item-container"--}}
{{--             data-modal-trigger="#{{$id}}-mod"--}}
{{--             data-slide-to="{{ $loopIndex }}"--}}
{{--        >--}}
{{--            <x-mle-video--}}
{{--                class="mle-cursor-zoom-in"--}}
{{--                :medium="$medium"--}}
{{--                :options="$options"--}}
{{--            />--}}
{{--        </div>--}}
{{--        @break--}}

{{--    @case(isMediaType($medium, 'audio'))--}}
{{--        <div class="media-manager-preview-item-container"--}}
{{--             data-modal-trigger="#{{$id}}-mod"--}}
{{--             data-slide-to="{{ $loopIndex }}"--}}
{{--        >--}}
{{--            <x-mle-audio--}}
{{--                class="mle-cursor-zoom-in"--}}
{{--                :medium="$medium"--}}
{{--                :options="$options"--}}
{{--            />--}}
{{--        </div>--}}
{{--        @break--}}

{{--    @case(isMediaType($medium, 'image'))--}}
{{--        <div class="media-manager-preview-item-container"--}}
{{--             data-modal-trigger="#{{$id}}-mod"--}}
{{--             data-slide-to="{{ $loopIndex }}"--}}
{{--        >--}}
{{--            <x-mle-image-responsive--}}
{{--                class="media-manager-image-preview mle-cursor-zoom-in"--}}
{{--                :medium="$medium"--}}
{{--                :options="$options"--}}
{{--                draggable="false"--}}
{{--            />--}}
{{--        </div>--}}

{{--        <x-mle-image-editor-modal--}}
{{--            id="{{ $id }}"--}}
{{--            :model-or-class-name="$modelOrClassName"--}}
{{--            :medium="$medium"--}}
{{--            :single-medium="$singleMedium"--}}
{{--            :collections="$collections"--}}
{{--            :options="$options"--}}
{{--            :initiator-id="$id"--}}
{{--            :disabled="$disabled"--}}
{{--            title="Edit Image"--}}
{{--        />--}}
{{--        @break--}}

{{--    @default--}}
{{--        <span class="mle-unsupported">{{ __('media-library-extensions::messages.non_supported_file_format') }}</span>--}}
{{--@endswitch--}}
