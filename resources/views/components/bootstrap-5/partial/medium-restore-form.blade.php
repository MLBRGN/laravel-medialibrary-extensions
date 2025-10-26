<x-mle-shared-conditional-form
    :use-xhr="$getConfig('useXhr')"
    :form-attributes="[
        'action' => $getConfig('mediumRestoreRoute'),
        'method' => 'POST',
        'data-form'
    ]"
    :div-attributes="[
        'data-xhr-form' => $getConfig('useXhr'), 
        'id' => $id.'-media-restore-form'
    ]"
    method="post"
    class="media-lab-restore-form"
>
    <button
        type="submit"
        class="mle-button mle-button-submit mle-button-icon btn btn-primary"
        title="{{ __('media-library-extensions::messages.restore_original') }}"
        data-action="medium-restore"
        data-route="{{ $getConfig('mediumRestoreRoute') }}"
    >
        <x-mle-shared-icon
            name="{{ config('media-library-extensions.icons.restore') }}"
            :title="__('media-library-extensions::messages.restore_original')"
        />
    </button>
</x-mle-shared-conditional-form>
