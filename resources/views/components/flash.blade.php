@if (session()->has("{$flashPrefix}.success"))
    <x-alert type="success">
        {{ session("{$flashPrefix}.success") }}
    </x-alert>
@endif

@if (session()->has("{$flashPrefix}.error"))
    <x-alert type="error">
        {{ session("{$flashPrefix}.error") }}
    </x-alert>
@endif
