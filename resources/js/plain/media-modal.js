// Functionality implemented with assistance from AI (ChatGPT)
document.addEventListener('DOMContentLoaded', () => {

    const modals = document.querySelectorAll('.media-modal');
    const focusableSelectors = 'a[href], button, textarea, input, select, [tabindex]:not([tabindex="-1"])';
    const players = {}; // Store player instances by slide ID

    const trapFocus = (modal) => {
        const focusableEls = modal.querySelectorAll(focusableSelectors);
        if (focusableEls.length === 0) return;

        const first = focusableEls[0];
        const last = focusableEls[focusableEls.length - 1];

        const handleKeyDown = (e) => {
            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    // Shift + Tab
                    if (document.activeElement === first) {
                        e.preventDefault();
                        last.focus();
                    }
                } else {
                    // Tab
                    if (document.activeElement === last) {
                        e.preventDefault();
                        first.focus();
                    }
                }
            }
        };

        modal.addEventListener('keydown', handleKeyDown);

        // Save handler reference on modal for removal later
        modal._trapFocusHandler = handleKeyDown;

        // Focus first element
        first.focus();
    };

    const releaseFocus = (modal) => {
        if (modal._trapFocusHandler) {
            modal.removeEventListener('keydown', modal._trapFocusHandler);
            delete modal._trapFocusHandler;
        }
    };

    const openModal = (modalId) => {
        console.log('openModal', modalId);
        const modal = document.getElementById(modalId);
        if (!modal) {
            console.log('modal not found', modalId);
            return;
        }
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        trapFocus(modal);
    };

    const closeModal = (modal) => {
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';

        releaseFocus(modal);

        const event = new CustomEvent('modalClosed', {
            bubbles: true,    // Optional: allows the event to bubble up the DOM tree
            detail: { modal }  // Optional: you can pass additional data here
        });
        modal.dispatchEvent(event);
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
        modal.addEventListener('modalClosed', () => {
            stopAllVideoPlayBack();
        });

        // TODO does not work properly, play and pause inconsistent
        // Handle carousel sliding
        carousel.addEventListener('carouselSlided', (event) => {
            pauseAllVideoPlayBack();

            if (modal.hasAttribute('data-video-autoplay')) {
                console.log(event);
                const videoWrapper = event.target.querySelector('.media-video-wrapper');
                if (videoWrapper && videoWrapper.hasAttribute('data-youtube-video-id')) {
                    let youtubeId = videoWrapper.getAttribute('data-youtube-video-id');
                    startVideoPlayBack(youtubeId);
                }
            }
        });
    });

    function stopAllVideoPlayBack() {
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
        console.log('startAllVideoPlayBack');

        let player = players[youTubeId]
        if (player) {
            player.playVideo();
        }
    }
});
