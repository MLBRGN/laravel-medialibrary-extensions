<div
    {{ $attributes->class([
        'mle-component',
        'mle-theme-' . $getConfig('theme'),
        'mle-media-gallery',
        'mle-media-gallery-' . $layout,
    ])->merge() }}
    id="{{ $getDomId() }}"
    style="--mle-gallery-columns: {{ $columns }}; --mle-gallery-gap: {{ $gap }};"
    data-mle-media-gallery
>
    @foreach($media as $medium)
        <div class="mle-media-gallery-item {{ $itemClass }} {{ $expandable ? 'mle-cursor-zoom-in' : '' }}"
             @if($expandable)
             data-mle-modal-trigger="#{{ $id }}-mod"
             data-mle-slide-to="{{ $loop->index }}"
             @endif
        >
            <x-mle-media-viewer
                :id="$id"
                :medium="$medium"
                :options="$getOptions()"
                :preview-mode="true"
                :expandable-in-modal="false"
                :data-source="$getConfig('dataSource')"
            />
        </div>
    @endforeach
</div>

@if($expandable && $totalMediaCount > 0)
    <x-mle-media-modal
        :id="$id"
        :model-reference="$modelReference"
        :collections="$collections"
        :options="$getOptions()"
        :data-source="$getConfig('dataSource')"
        :instance-id="$instanceId"
        :client-token="$clientToken"
    />
@endif
