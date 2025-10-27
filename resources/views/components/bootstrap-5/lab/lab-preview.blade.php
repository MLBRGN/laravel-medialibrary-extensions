<div {{ $attributes->merge() }}>
   
    <div class="media-lab-title">
        {{ $title }}
    </div>
    <div class="mlbrgn-mle-component theme-bootstrap-5 media-preview-container"
         data-media-preview-container=""
    >
        <div class="media-preview-item-container"
             data-bs-toggle="modal"
             data-bs-target="#alien-multiple-mmm-mod"
        >
            {{ $slot }}
            <x-mle-partial-status-area
                id="{{ $id }}"
                :initiator-id="$id"
                :media-manager-id="$id"
                :options="[]"
            />
        </div>
    
        <div class="media-preview-menu">
            <div class="media-preview-menu-start">
                {{ $menuStart ?? '' }}
            </div>
            <div class="media-preview-menu-end">
                {{ $menuEnd ?? '' }}
            </div>
        </div>
    </div>
</div>
