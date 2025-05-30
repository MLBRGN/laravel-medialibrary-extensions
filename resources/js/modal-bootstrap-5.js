document.addEventListener('DOMContentLoaded', () => {

    const modals = document.querySelectorAll('.media-preview-modal');
    const players = {}; // Store player instances by slide ID
    function stopAllVideoPlayBack() {
        Object.values(players).forEach((player) => player.stopVideo && player.stopVideo());
    }

    function startVideoPlayBack(youTubeId) {
        players[youTubeId] && players[youTubeId].playVideo();
    }

    modals.forEach((modal) => {
        const carousel = modal.querySelector('.carousel');
        let autoPlay = modal.hasAttribute('autoplay');

        function setupYT(videoSlide) {
            const youTubeId = videoSlide.getAttribute('data-youtube-video-id');
            const iframe = videoSlide.querySelector('lite-youtube').shadowRoot.querySelector('iframe');

            // instantiate new player instance for slide
            players[youTubeId] = new YT.Player(iframe, {
                events: {
                    onReady: () => {
                        if (autoPlay) {
                            startVideoPlayBack(youTubeId);
                        }
                    },
                },
            });
        }

        // Event listener for when YouTube iframe loads
        modal.addEventListener('liteYoutubeIframeLoaded', (event) => {
            const targetSlide = event.target.closest('[data-youtube-video]');
            if (targetSlide) setupYT(targetSlide);
        });

        // Stop the video when the modal is hidden
        modal.addEventListener('hidden.bs.modal', () => {
            stopAllVideoPlayBack();
        });

        // Handle carousel sliding
        carousel.addEventListener('slide.bs.carousel', (event) => {
            stopAllVideoPlayBack();

            if (modal.hasAttribute('autoplay')) {
                if (event.relatedTarget.hasAttribute('data-youtube-video')) {
                    let youtubeId = event.relatedTarget.getAttribute('data-youtube-video-id');
                    startVideoPlayBack(youtubeId);
                }
            }
        });
    });
});
