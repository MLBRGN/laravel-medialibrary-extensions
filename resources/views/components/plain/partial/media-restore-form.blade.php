@php
    $nonXhrOnSubmit = $getConfig('useXhr') ? null : "(function(el){el.setAttribute('data-mle-busy','1');var c=el.closest('[data-mle-media-manager]') || el.closest('[data-mle-media-lab]');if(!c)return;var a=c.querySelector('[data-mle-status-area-container]');if(!a)return;var m=document.createElement('div');m.setAttribute('data-mle-status-message','');m.textContent='" . e(__('medialibrary-extensions::messages.please_wait')) . "';a.innerHTML='';a.appendChild(m);})(this)";
@endphp

<x-mle-shared-conditional-form
    :use-xhr="$getConfig('useXhr')"
    :form-attributes="[
        'action' => $getConfig('routes.mediumRestore') . '#' . $id,
        'method' => 'POST',
        'onsubmit' => $nonXhrOnSubmit,
        'data-mle-form'
    ]"
    :div-attributes="[
        'data-mle-xhr-form' => $getConfig('useXhr'), 
    ]"
    method="post"
    class="mle-media-lab-restore-form"
    id="{{ $getDomId() }}"
    data-mle-restore-form
>
    <input type="hidden"
           name="medium_id"
           value="{{ $media->id }}">
    <input type="hidden"
           name="model_type"
           value="{{ $modelType }}">
    <input type="hidden"
           name="model_id"
           value="{{ $modelId }}">
    <input type="hidden"
           name="base_id"
           value="{{ $id }}">
{{--    <input type="hidden"--}}
{{--           name="collection"--}}
{{--           value="{{ $medium->collection_name }}">--}}
    <input type="hidden"
           name="temporary_upload_mode"
           value="{{ $temporaryUploadMode ? 'true' : 'false' }}">
    <input type="hidden"
           name="data_source"
           value="{{ $getConfig('dataSource') }}">
    <button
        type="submit"
        class="mle-button mle-button-submit mle-button-icon"
        title="{{ __('medialibrary-extensions::messages.restore_original') }}"
        data-mle-action="medium-restore"
        data-mle-route="{{ $getConfig('routes.mediumRestore') }}"
        data-mle-medium-id="{{ $media->id }}"
    >
        <x-mle-shared-icon
            name="{{ config('medialibrary-extensions.icons.restore') }}"
            :title="__('medialibrary-extensions::messages.restore_original')"
        />
    </button>
</x-mle-shared-conditional-form>
