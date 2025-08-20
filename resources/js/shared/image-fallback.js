document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".mlbrgn-mle-component img").forEach(img => {

        const replaceImageWithFallback = (img) => {
            const div = document.createElement('div');
            div.className = 'mle-non-displayable-img';
            div.innerHTML = 'Image loading / decoding failed'
            img.parentNode.replaceChild(div, img);
        }
        // img.addEventListener("error", imageFallbackListener, { once: true });
        img.addEventListener("error", () => {
            console.log('could not load image')
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
            console.log('could not load image, second check', img.src)
            replaceImageWithFallback(img);
        }
    });
});
