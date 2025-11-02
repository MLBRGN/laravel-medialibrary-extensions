<x-mle-shared-conditional-form
    :use-xhr="$getConfig('useXhr')"
    :form-attributes="[
        'action' => $getConfig('mediumRestoreRoute'),
        'method' => 'POST',
        'data-mle-form'
    ]"
    :div-attributes="[
        'data-mle-xhr-form' => $getConfig('useXhr'), 
        'id' => $id.'-media-restore-form'
    ]"
    method="post"
    class="mle-media-lab-restore-form"
>
    <button
        type="submit"
        class="mle-button mle-button-submit mle-button-icon btn btn-primary"
        title="{{ __('media-library-extensions::messages.restore_original') }}"
        data-mle-action="medium-restore"
        data-mle-route="{{ $getConfig('mediumRestoreRoute') }}"
        data-mle-medium-id="{{ $medium->id }}"
    >
        <x-mle-shared-icon
            name="{{ config('media-library-extensions.icons.restore') }}"
            :title="__('media-library-extensions::messages.restore_original')"
        />
    </button>
</x-mle-shared-conditional-form>
