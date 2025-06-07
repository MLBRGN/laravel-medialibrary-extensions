// Functionality implemented with assistance from AI (ChatGPT)
// noinspection JSUnresolvedReference
import { fireEvent, trapFocus, releaseFocus } from '@/js/plain/helpers';

document.addEventListener('DOMContentLoaded', () => {

    const modals = document.querySelectorAll('.media-modal');
    const players = {}; // Store player instances by slide ID

    const openModal = (modalId) => {
        const modal = document.getElementById(modalId);
        if (!modal) {
            console.log('modal not found', modalId);
            return;
        }
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        trapFocus(modal);

        fireEvent('modalOpened', modal);

    };

    const closeModal = (modal) => {
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';

        releaseFocus(modal);
        fireEvent('modalClosed', modal);
    };

    // Attach to all close buttons
    document.querySelectorAll('[data-modal-close]').forEach(element => {
        element.addEventListener('click', () => {
            const modal = element.closest('.media-modal');
            if (modal) closeModal(modal);
        });
    });

    // Close on backdrop click
    document.querySelectorAll('.media-modal').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal(modal);
        });
    });

    // Open modal trigger
    document.querySelectorAll('[data-modal-trigger]').forEach(element => {
        element.addEventListener('click', () => {
            const target = element.getAttribute('data-modal-trigger');
            openModal(target);
        });
    });

    // ESC key to close any active modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.media-modal.active').forEach(modal => {
                closeModal(modal);
            });
        }
    });

    // video playback control
    modals.forEach((modal) => {
        const carousel = modal.querySelector('[data-carousel]');
        let autoPlay = modal.hasAttribute('data-video-autoplay');

        function setupYT(videoSlide) {
            const youTubeId = videoSlide.getAttribute('data-youtube-video-id');
            const iframe = videoSlide.querySelector('lite-youtube').shadowRoot.querySelector('iframe');
            // instantiate the new player instance for slide
            players[youTubeId] = new YT.Player(iframe, {
                events: {
                    onReady: () => {
                        // if (autoPlay) {
                        //     startVideoPlayBack(youTubeId);
                        // }
                    },
                },
            });
        }

        // Event listener for when the YouTube iframe loads
        modal.addEventListener('liteYoutubeIframeLoaded', (event) => {
            const targetSlide = event.target.closest('[data-youtube-video-id]');
            if (targetSlide) setupYT(targetSlide);
        });

        modal.addEventListener('modalOpened', () => {
            if (!modal.hasAttribute('data-video-autoplay')) return;

            const activeSlide = modal.querySelector('.media-carousel-item.active');
            if (!activeSlide) return;

            const videoWrapper = activeSlide.querySelector('.media-video-wrapper');
            if (!videoWrapper || !videoWrapper.hasAttribute('data-youtube-video-id')) return;

            const youtubeId = videoWrapper.getAttribute('data-youtube-video-id');
            startVideoPlayBack(youtubeId);
        });

        // Stop the video when the modal is closed
        modal.addEventListener('modalClosed', () => {
            stopAllVideoPlayBack();
        });

        // Handle carousel sliding
        carousel.addEventListener('carouselSlided', (event) => {
            pauseAllVideoPlayBack();

            if (modal.hasAttribute('data-video-autoplay')) {
                // Only get the active carousel item
                const activeSlide = carousel.querySelector('.media-carousel-item.active');

                if (!activeSlide) return;

                const videoWrapper = activeSlide.querySelector('.media-video-wrapper');

                if (videoWrapper && videoWrapper.hasAttribute('data-youtube-video-id')) {
                    const youtubeId = videoWrapper.getAttribute('data-youtube-video-id');
                    startVideoPlayBack(youtubeId);
                }
            }
        });
    });

    function stopAllVideoPlayBack() {
        console.log('stopAllVideoPlayBack');
        Object.values(players).forEach((player) => {
            player.stopVideo()
        });
    }

    function pauseAllVideoPlayBack() {
        console.log('pauseAllVideoPlayBack');
        Object.values(players).forEach((player) => {
            player.pauseVideo()
        });
    }

    function startVideoPlayBack(youTubeId) {
        console.log('startVideoPlayBack');
        let player = players[youTubeId]
        if (player) {
            player.playVideo();
        }
    }

});
