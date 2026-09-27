document.querySelectorAll(".mle-component img").forEach(img => {
    const replaceImageWithFallback = (img) => {
        if (img.dataset.mleFallbackApplied) return;
        img.dataset.mleFallbackApplied = 'true';

        const fallbackUrl = (window.mleAssetBase || '/vendor/mlbrgn/laravel-medialibrary-extensions') + '/images/fallback.png';
        img.onerror = null;
        img.src = fallbackUrl;
    }
    // img.addEventListener("error", imageFallbackListener, { once: true });
    img.addEventListener("error", () => {
        console.log('could not load image (error), falling back', 'image src: ', img.src)
        replaceImageWithFallback(img);
    });


    // If it "loads" but is not displayable (natural size = 0)
    img.addEventListener("load", () => {
        if (img.naturalWidth === 0 || img.naturalHeight === 0) {
            console.warn("Image decoded incorrectly, swapping to fallback:", img.src);
            replaceImageWithFallback(img);
        }
    });

    // Extra safeguard: run once after DOM is ready (covers cached broken images)
    if (img.complete && (img.naturalWidth === 0 || img.naturalHeight === 0)) {
        // console.log('could not load image, second check', img.src)
        replaceImageWithFallback(img);
    }
});

function trans (key) {
    return window.mediaLibraryTranslations?.[key] || key;
}

