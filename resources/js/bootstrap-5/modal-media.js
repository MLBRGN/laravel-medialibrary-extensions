// noinspection JSUnresolvedReference
document.addEventListener('DOMContentLoaded', () => {

    const modals = document.querySelectorAll('.media-modal');
    const players = {}; // Store player instances by slide ID

    modals.forEach((modal) => {
        const carousel = modal.querySelector('.media-carousel');
        let autoPlay = modal.hasAttribute('data-video-autoplay');
        const modalId = modal.id;

        function setupYT(videoSlide) {
            const youTubeId = videoSlide.getAttribute('data-youtube-video-id');
            const playerId = modalId+'-'+youTubeId;
            const iframe = videoSlide.querySelector('lite-youtube').shadowRoot.querySelector('iframe');
            // instantiate the new player instance for slide
            players[playerId] = new YT.Player(iframe, {
                events: {
                    onReady: () => {
                        players[playerId].isReady = true;
                    },
                },
            });
        }

        // Event listener for when the YouTube iframe loads
        modal.addEventListener('liteYoutubeIframeLoaded', (event) => {
            const targetSlide = event.target.closest('[data-youtube-video-id]');
            if (targetSlide) setupYT(targetSlide);
        });

        // when the carousel opens on the first slide and this happens to be a YouTube video,
        // we need to start playing the video on modal open.
        modal.addEventListener('shown.bs.modal', (e) => {
            if (!modal.hasAttribute('data-video-autoplay')) {
                return;
            }

            const modalTrigger = e.relatedTarget;
            const slideToElement = modalTrigger.querySelector('[data-bs-slide-to]');
            if (slideToElement) {
                const slideTo = slideToElement.getAttribute('data-bs-slide-to')
                if (slideTo === '0') {
                    const firstVideoContainer = carousel.querySelector('.carousel-item .media-video-container');
                    const youTubeId = firstVideoContainer.getAttribute('data-youtube-video-id');
                    const playerId = modalId + '-' + youTubeId;
                    startVideoPlayBack(playerId);
                }
            }
        });

        // Stop the video when the modal is hidden
        modal.addEventListener('hidden.bs.modal', () => {

            const carouselElement = modal.querySelector('.media-carousel');
            if (!carouselElement) return;

            const carouselInstance = bootstrap.Carousel.getInstance(carouselElement);
            if (!carouselInstance) return;
            stopAllVideoPlayBack();

            carousel.removeEventListener('slide.bs.carousel', slideEventListener);
            carouselInstance.to(0)// !!! triggers slide event so call stopAllVideoPlayBack after this
            carousel.addEventListener('slide.bs.carousel', slideEventListener);
        });

        const slideEventListener = (event) => {
            pauseAllVideoPlayBack();

            if (modal.hasAttribute('data-video-autoplay')) {
                const videoContainer = event.relatedTarget.querySelector('.media-video-container');
                if (videoContainer && videoContainer.hasAttribute('data-youtube-video-id')) {
                    let youTubeId = videoContainer.getAttribute('data-youtube-video-id');
                    const playerId = modalId+'-'+youTubeId;

                    startVideoPlayBack(playerId);
                }
            }
        }

        // Handle carousel sliding
        carousel.addEventListener('slide.bs.carousel', slideEventListener);

        modal.addEventListener('keydown', (e) => {
            if (!modal.classList.contains('show')) return;

            const carouselElement = modal.querySelector('.media-carousel');
            if (!carouselElement) return;

            const carouselInstance = bootstrap.Carousel.getInstance(carouselElement);
            if (!carouselInstance) return;

            const isInsideCarousel = carousel.contains(e.target);

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

    function controlVideoPlayback(youTubeId, action = 'playVideo', attempt = 0, maxAttempts = 10, timeOut = 200) {
        const actionsMap = {
            playVideo: 'playVideo',
            pauseVideo: 'pauseVideo',
            stopVideo: 'stopVideo'
        };

        const method = actionsMap[action];
        if (!actionsMap[action]) {
            console.warn(`Unknown action: ${action}`);
            return;
        }

        const player = players[youTubeId];

        if (!player || !player.isReady) {
            // console.log('No player, or player not ready yet', youTubeId, 'player', player, 'playerIsReady', player?.isReady);
            if (attempt < maxAttempts) {
                setTimeout(() => controlVideoPlayback(youTubeId, action, attempt + 1, maxAttempts, timeOut * 1.2), timeOut);
            }
            return;
        }

        // console.log(action, youTubeId);
        if (method) player[method]();
    }

    function pauseVideoPlayBack(youTubeId, attempt = 0, maxAttempts = 10, timeOut = 200) {
       controlVideoPlayback(youTubeId, 'pauseVideo', attempt, maxAttempts, timeOut);
    }

    function stopVideoPlayBack(youTubeId, attempt = 0, maxAttempts = 10, timeOut = 200) {
        controlVideoPlayback(youTubeId, 'stopVideo', attempt, maxAttempts, timeOut);
    }

    function startVideoPlayBack(youTubeId, attempt = 0, maxAttempts = 10, timeOut = 200) {
        controlVideoPlayback(youTubeId, 'playVideo', attempt, maxAttempts, timeOut);
    }

    function pauseAllVideoPlayBack() {
        Object.keys(players).forEach((id) => pauseVideoPlayBack(id, 0, 10, 200));
    }

    function stopAllVideoPlayBack() {
        Object.keys(players).forEach((id) => stopVideoPlayBack(id, 0, 10, 200));
    }

    function generatePlayerKey() {
        return 'player-' + Date.now() + '-' + Math.floor(Math.random() * 1e6);
    }

});
