<div {{ $attributes->class('mle-document') }}>
    <div class="mle-document-preview">
        <x-mle-shared-icon
            class="mle-document-bg-icon"
            :name="$icon['name']"
            :title="$icon['title']"
        />
        <div class="mle-document-info">
            <p>
                {{ Str::limit($medium->file_name, 15) }}
            </p>
            <p>
                {{ mle_human_filesize($medium->size) }}
            </p>
                <x-mle-shared-icon
                    class="mle-document-fg-icon"
                    :name="$icon['name']"
                    :title="$icon['title']"
                />
        </div>
    </div>
</div>

{{--<div--}}
{{--    class="media-manager-preview-item-container flex flex-col justify-between bg-white border rounded-2xl shadow hover:shadow-md transition p-4 cursor-pointer"--}}
{{--    data-bs-toggle="modal"--}}
{{--    data-bs-target="#{{ $id }}-mod"--}}
{{--    data-slide-index="{{ $loop->index }}"--}}
{{-->--}}
{{--    --}}{{-- File Icon --}}
{{--    <div class="flex flex-col items-center justify-center flex-1">--}}
{{--        <x-icon name="file" class="w-12 h-12 {{ $iconColor }}" />--}}

{{--        <div class="mt-2 text-sm text-gray-700 truncate w-full text-center">--}}
{{--            {{ Str::limit($medium->file_name, 20) }}--}}
{{--        </div>--}}

{{--        <div class="text-xs text-gray-500">--}}
{{--            {{ human_filesize($medium->size) }}--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    --}}{{-- Footer with actions --}}
{{--    <div class="flex justify-between items-center mt-3 text-gray-600">--}}
{{--        <button class="p-1 hover:text-yellow-500">--}}
{{--            <x-icon name="star" class="w-5 h-5" />--}}
{{--        </button>--}}
{{--        <button class="p-1 hover:text-red-600">--}}
{{--            <x-icon name="trash" class="w-5 h-5" />--}}
{{--        </button>--}}
{{--    </div>--}}
{{--</div>--}}
