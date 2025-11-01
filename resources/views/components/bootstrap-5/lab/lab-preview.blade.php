<div {{ $attributes->merge([ 'class' => 'mle-media-lab-preview' ]) }}>
    <div class="mle-media-lab-title">
        {{ $title }}
    </div>
    <div @class(['
        mlbrgn-mle-component', 
        'theme-'.$getConfig('frontendTheme'), 
        'media-preview-container'
    ])
         data-media-preview-container=""
    >
        <div class="media-preview-item-container"
             data-bs-toggle="modal"
             data-bs-target="#alien-multiple-mmm-mod"
        >
            {{ $slot }}
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
    <div class="mle-media-lab-info">
        {{ $imageInfo ?? '' }}
    </div>
</div>
