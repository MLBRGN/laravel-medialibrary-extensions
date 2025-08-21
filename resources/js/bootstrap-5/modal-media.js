// noinspection JSUnresolvedReference

document.addEventListener('DOMContentLoaded', () => {

    let ytlPlayers = {}; // Store YouTube-lite players
    let nativeMediaPlayers = {};// store native media players (audio / video)

    const initializeMediaModal = function (modal) {
        if (modal.dataset.imageEditorInitialized) return;

        const carousel = modal.querySelector('.media-carousel');
        const modalId = modal.id;

        function setupYT (videoSlide) {
            const youTubeId = videoSlide.getAttribute('data-youtube-video-id');
            const playerId = modalId + '-' + youTubeId;
            const iframe = videoSlide.querySelector('lite-youtube').shadowRoot.querySelector('iframe');
            // instantiate the new player instance for slide
            ytlPlayers[playerId] = new YT.Player(iframe, {
                events: {
                    onReady: () => {
                        ytlPlayers[playerId].isReady = true;
                    },
                },
            });
        }

        function setupNativeMedia(slide) {

            const audio = slide.querySelector('audio');
            const video = slide.querySelector('video');
            let nativeMediaPlayerId = null;
            if (audio) {
                nativeMediaPlayerId = audio.id;
                nativeMediaPlayers[nativeMediaPlayerId] = audio;
            }
            if (video) {
                nativeMediaPlayerId = video.id;
                nativeMediaPlayers[nativeMediaPlayerId] = video;
            }

            return nativeMediaPlayerId;
        }

        function controlNativeMedia(nativeMediaPlayerId, action) {
            const media = nativeMediaPlayers[nativeMediaPlayerId];
            if (!media) return;

            try {
                if (action === 'play') media.play();
                else if (action === 'pause') media.pause();
            } catch (err) {
                console.warn(`Failed to ${action} media:`, err);
            }
        }

        // Event listener for when the YouTube iframe loads
        modal.addEventListener('liteYoutubeIframeLoaded', (event) => {
            const targetSlide = event.target.closest('[data-youtube-video-id]');
            if (targetSlide) setupYT(targetSlide);
        });

        // when the carousel opens on the first slide and this is a YT video an audio or a video element,
        // we need to start autoplaying on modal open.
        modal.addEventListener('shown.bs.modal', (e) => {
            // nothing to do, return
            if (!modal.hasAttribute('data-autoplay')) return;

            const modalTrigger = e.relatedTarget;
            if (!modalTrigger) return;

            const slideToElement = modalTrigger.querySelector('[data-bs-slide-to]');
            if (!slideToElement) return;

            const slideTo = slideToElement.getAttribute('data-bs-slide-to');
            if (slideTo !== '0') return;

            const firstSlide = carousel.querySelector('.carousel-item:first-child');
            if (!firstSlide) return;

            const audioElement = firstSlide.querySelector('[data-mle-audio]');
            const videoElement = firstSlide.querySelector('[data-mle-video]');
            const youtubeVideoContainer = firstSlide.querySelector('[data-mle-youtube-video]');

            // TODO messy, use controlNativeMedia
            if (audioElement) {
                try {
                    audioElement.play();
                } catch (err) {
                    console.warn('Audio autoplay failed:', err);
                }
            }

            if (videoElement) {
                try {
                    videoElement.play();
                } catch (err) {
                    console.warn('Video autoplay failed:', err);
                }
            }

            if (youtubeVideoContainer) {
                const youTubeId = youtubeVideoContainer.getAttribute('data-youtube-video-id');
                if (!youTubeId) return;

                const playerId = `${modalId}-${youTubeId}`;
                controlYouTubePlayback(playerId, 'playVideo');
            }
        });

        // Stop the video when the modal is hidden
        modal.addEventListener('hidden.bs.modal', () => {

            const carouselElement = modal.querySelector('.media-carousel');
            if (!carouselElement) return;

            const carouselInstance = bootstrap.Carousel.getInstance(carouselElement);
            if (!carouselInstance) return;
            stopAllPlayBack();

            carousel.removeEventListener('slide.bs.carousel', slideEventListener);
            carouselInstance.to(0)
            carousel.addEventListener('slide.bs.carousel', slideEventListener);
        });

        const slideEventListener = (event) => {

            if (!modal.hasAttribute('data-autoplay')) return;

            pauseAllPlayBack()

            const slide = event.relatedTarget;
            if (!slide) return;

            const nativeMediaPlayerId = setupNativeMedia(slide);
            controlNativeMedia(nativeMediaPlayerId, 'play');

            const ytContainer = slide.querySelector('[data-mle-youtube-video]');
            if (ytContainer && ytContainer.hasAttribute('data-youtube-video-id')) {
                let youTubeId = ytContainer.getAttribute('data-youtube-video-id');
                const playerId = modalId + '-' + youTubeId;

                controlYouTubePlayback(playerId, 'playVideo');
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

        // Mark as initialized
        modal.dataset.imageEditorInitialized = 'true';

        function controlYouTubePlayback(playerId, action = 'playVideo', attempt = 0, maxAttempts = 10, timeOut = 200) {
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

            const player = ytlPlayers[playerId];

            if (!player || !player.isReady) {
                // console.log('No player, or player not ready yet', playerId, 'player', player, 'playerIsReady', player?.isReady);
                if (attempt < maxAttempts) {
                    setTimeout(() => controlYouTubePlayback(playerId, action, attempt + 1, maxAttempts, timeOut * 1.2), timeOut);
                }
                return;
            }

            if (method) player[method]();
        }

        function pauseAllPlayBack() {
            Object.keys(ytlPlayers).forEach((playerId) => controlYouTubePlayback(playerId, 'pauseVideo'));
            Object.keys(nativeMediaPlayers).forEach(id => controlNativeMedia(id, 'pause'));
        }

        function stopAllPlayBack() {
            Object.keys(ytlPlayers).forEach((playerId) => controlYouTubePlayback(playerId, 'stopVideo'));
            Object.keys(nativeMediaPlayers).forEach(id => controlNativeMedia(id, 'pause'));
        }
    }

    // listen to preview updated to reinitialize functionality
    document.addEventListener('mediaManagerPreviewsUpdated', (e) => {
        ytlPlayers = {};
        nativeMediaPlayers = {};
        const mediaManager = e.detail.mediaManager;
        mediaManager.querySelectorAll('.media-modal').forEach(initializeMediaModal);
    });

    document.querySelectorAll('.media-modal').forEach(initializeMediaModal);

});
