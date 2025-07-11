// noinspection JSUnresolvedReference
document.addEventListener('DOMContentLoaded', () => {

    const modals = document.querySelectorAll('.media-modal');
    const players = {}; // Store player instances by slide ID

    modals.forEach((modal) => {
        const carousel = modal.querySelector('.media-carousel');
        let autoPlay = modal.hasAttribute('data-video-autoplay');

        function setupYT(videoSlide) {
            const youTubeId = videoSlide.getAttribute('data-youtube-video-id');
            const iframe = videoSlide.querySelector('lite-youtube').shadowRoot.querySelector('iframe');
            // instantiate the new player instance for slide
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

        // Event listener for when the YouTube iframe loads
        modal.addEventListener('liteYoutubeIframeLoaded', (event) => {
            const targetSlide = event.target.closest('[data-youtube-video-id]');
            if (targetSlide) setupYT(targetSlide);
        });

        // Stop the video when the modal is hidden
        modal.addEventListener('hidden.bs.modal', () => {
            stopAllVideoPlayBack();
        });

        // Handle carousel sliding
        carousel.addEventListener('slide.bs.carousel', (event) => {
            pauseAllVideoPlayBack();

            if (modal.hasAttribute('data-video-autoplay')) {
                const videoContainer = event.relatedTarget.querySelector('.media-video-container');
                if (videoContainer && videoContainer.hasAttribute('data-youtube-video-id')) {
                    let youtubeId = videoContainer.getAttribute('data-youtube-video-id');
                    startVideoPlayBack(youtubeId);
                }
            }
        });

        modal.addEventListener('keydown', (e) => {
            if (!modal.classList.contains('show')) return;

            const carouselElement = modal.querySelector('.media-carousel');
            if (!carousel) return;

            const carouselInstance = bootstrap.Carousel.getInstance(carouselElement);
            if (!carouselInstance) return;

            const isInsideCarousel = carousel.contains(e.target);

            console.log(isInsideCarousel);

            if ((e.key === 'ArrowLeft' || e.key === 'ArrowRight') && isInsideCarousel) {
                return; // let the carousel's listener handle it
            }

            // If outside the carousel (e.g., focus on close button), we handle the keys here
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                e.stopPropagation();
                carouselInstance.prev();
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                e.stopPropagation();
                carouselInstance.next();
            }
        });
    });

    function stopAllVideoPlayBack() {
        Object.values(players).forEach((player) => {
            player.stopVideo()
        });
    }

    function pauseAllVideoPlayBack() {
        Object.values(players).forEach((player) => {
            player.pauseVideo()
        });
    }

    function startVideoPlayBack(youTubeId) {
        let player = players[youTubeId]
        if (player) {
            player.playVideo();
        }
    }

});
