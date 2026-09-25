<div class="mle-component mle-theme-{{ $getConfig('theme') }}">
    @if ($url)
        <img
            {{ $attributes->class([
                'mle-image-responsive',
                'mle-cursor-zoom-in' => $expandableInModal
            ])->merge([
                'data-bs-toggle' => ($expandableInModal && $getConfig('theme') === 'bootstrap-5') ? 'modal' : null,
                'data-bs-target' => ($expandableInModal && $getConfig('theme') === 'bootstrap-5') ? '#' . $id . '-mod' : null,
                'data-mle-modal-trigger' => ($expandableInModal && $getConfig('theme') === 'plain') ? '#' . $id . '-mod' : null,
            ]) }}
            src="{{ $url ?: $placeholder }}"
            @if ($srcset) srcset="{{ $srcset }}" @endif
            @if ($srcset && $sizes) sizes="{{ $sizes }}" @endif
            alt="{{ $alt }}"
            @if ($lazy) loading="lazy" @endif
            data-mle-image
            data-mle-media-preview-image
            id="{{ $getDomId() }}"
        >
    @else
        <img
            {{ $attributes->class(['mle-image-responsive'])->merge(['class' => '']) }}
            src="{{ $placeholder }}"
            alt="Missing image"
            class="mle-opacity-50"
            data-mle-image
            data-mle-media-preview-image
            id="{{ $getDomId() }}"
        >
    @endif
</div>

@if($expandableInModal)
    <x-mle-media-modal
        :id="$id"
        :model-reference="$modelReference"
        :single-media="$medium"
        :collections="$collections"
        :options="$getOptions()"
        :data-source="$dataSource"
        :instance-id="$instanceId"
        :client-token="$clientToken"
    />
@endif
