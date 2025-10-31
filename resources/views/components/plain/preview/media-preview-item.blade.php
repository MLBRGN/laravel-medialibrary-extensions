@if ($componentToRender)
    <div class="media-preview-item-container"
         data-modal-trigger="#{{$id}}-mod"
         data-slide-to="{{ $loopIndex }}"
    >
{{--        @if($slot->isNotEmpty())--}}
{{--            {{ $slot }}--}}
{{--        @else--}}
            <x-dynamic-component
                :component="$componentToRender"
                class="{{ $mediumType === 'image' 
                    ? 'media-preview-image mle-cursor-zoom-in' 
                    : 'mle-cursor-zoom-in' }}"
                :medium="$medium"
                :options="$options"
                :draggable="$mediumType === 'image' ? 'false' : null"
                :preview="true"
{{--                :preview="in_array($mediumType, ['youtube-video'])"--}}
            />
{{--        @endisset--}}
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