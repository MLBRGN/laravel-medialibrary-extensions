<div {{ $attributes->merge(['class' => 'mle-document']) }}>
    <p>
        {{ getHumanMimeTypeLabel($medium->mime_type) }}
    </p>
</div>