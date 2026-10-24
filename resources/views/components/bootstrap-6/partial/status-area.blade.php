<div 
    {{ $attributes->merge(['class' => 'mle-status-area']) }} 
    data-mle-status-area-container 
    id="{{ $getDomId() }}"
>
    <x-mle-partial-spinner 
        id="{{ $id }}" 
        :options="$getOptions()"
    />
    <x-mle-partial-status 
        id="{{ $id }}" 
        :options="$getOptions()"
    />
</div>