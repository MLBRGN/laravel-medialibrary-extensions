// Functionality implemented with assistance from AI (ChatGPT)
import { fireEvent } from '@/js/plain/helpers';

const carousels = new Map();

export function getCarouselController(element) {
    return carousels.get(element);
}

export function initCarousel(carousel) {
    // Remove the existing controller if it exists (for reinit)
    if (carousels.has(carousel)) {
        carousels.get(carousel).pause();
        carousels.delete(carousel);
    }

    // Suppress any first-paint transitions to avoid an initial jank when shown in a modal.
    // Keep suppression while the parent modal (if any) is still initializing to avoid races.
    carousel.classList.add('temp-no-animation');
    const liftSuppression = () => {
        const modal = carousel.closest('[data-mle-modal]');
        // If a wrapping modal exists and is still initializing, try again next frame
        if (modal && modal.hasAttribute('data-mle-initializing')) {
            requestAnimationFrame(liftSuppression);
            return;
        }
        carousel.classList.remove('temp-no-animation');
    };
    // Defer a couple of frames to ensure any programmatic goToSlide has applied
    requestAnimationFrame(() => requestAnimationFrame(liftSuppression));

    const slides = carousel.querySelectorAll('[data-mle-carousel-item]');
    if (!slides.length) return;

    const indicators = carousel.querySelectorAll('[data-mle-carousel-indicators] button');
    const prev = carousel.querySelector('[data-mle-slide="prev"]');
    const next = carousel.querySelector('[data-mle-slide="next"]');

    const ride = carousel.getAttribute('data-mle-carousel-ride') === 'true';
    const rideInterval = Number(carousel.getAttribute('data-mle-carousel-ride-interval') ?? '5000');
    const rideOnlyAfterInteraction = carousel.getAttribute('data-mle-carousel-ride-only-after-interaction') === 'true';

    let currentSlideIndex = 0;
    let hasInteracted = false;
    let intervalId = null;
    let touchStartX = 0, touchEndX = 0, touchStartY = 0, touchEndY = 0;
    const swipeVerticalThreshold = 50;

    const updateCarousel = (toSlideIndex, direction = 'right', skipFireEvent = false, suppressAnimation = false) => {
        // Clean any previous transient animation classes
        slides.forEach((slide) => {
            slide.classList.remove(
                'mle-slide-in-from-left',
                'mle-slide-in-from-right',
                'mle-slide-out-to-left',
                'mle-slide-out-to-right',
                'mle-fade-in',
                'mle-fade-out'
            );
        });

        const currentSlide = slides[currentSlideIndex];
        const nextSlide = slides[toSlideIndex];

        const effect = carousel.getAttribute('data-mle-carousel-effect');
        const animationsDisabled = carousel.classList.contains('temp-no-animation') || carousel.classList.contains('no-animation') || suppressAnimation === true;

        // Helper to finalize the state like Bootstrap does after transition ends
        const commitAfterAnimation = () => {
            // Remove transient classes
            nextSlide.classList.remove('mle-slide-in-from-left', 'mle-slide-in-from-right', 'mle-fade-in');
            currentSlide.classList.remove('mle-slide-out-to-left', 'mle-slide-out-to-right', 'mle-fade-out');

            // Commit logical state
            currentSlide.classList.remove('active');
            nextSlide.classList.add('active');

            indicators.forEach((btn, i) => btn.classList.toggle('active', i === toSlideIndex));
            currentSlideIndex = toSlideIndex;

            if (!skipFireEvent) {
                fireEvent('mleCarouselSlided', carousel, { 'carousel': carousel, 'currentSlide': nextSlide, 'currentSlideIndex': currentSlideIndex });
            }
        };

        // When animations are disabled or only a single slide, switch instantly
        if (animationsDisabled || slides.length <= 1 || !effect) {
            currentSlide.classList.remove('active');
            nextSlide.classList.add('active');
            indicators.forEach((btn, i) => btn.classList.toggle('active', i === toSlideIndex));
            currentSlideIndex = toSlideIndex;
            if (!skipFireEvent) {
                fireEvent('mleCarouselSlided', carousel, { 'carousel': carousel, 'currentSlide': nextSlide, 'currentSlideIndex': currentSlideIndex });
            }
            return;
        }

        // For animated modes, emulate Bootstrap’s deferred commit: animate first, then set .active
        let ended = false;
        let fallbackTimer = null;
        const endOnce = () => {
            if (ended) { return; }
            ended = true;
            if (fallbackTimer) { clearTimeout(fallbackTimer); fallbackTimer = null; }
            nextSlide.removeEventListener('animationend', endOnce);
            nextSlide.removeEventListener('transitionend', endOnce);
            // Use a rAF to ensure styles settle before committing
            requestAnimationFrame(commitAfterAnimation);
        };

        if (effect === 'slide') {
            if (direction === 'right') {
                nextSlide.classList.add('mle-slide-in-from-right');
                currentSlide.classList.add('mle-slide-out-to-left');
            } else {
                nextSlide.classList.add('mle-slide-in-from-left');
                currentSlide.classList.add('mle-slide-out-to-right');
            }
            void nextSlide.offsetWidth; // force reflow
            // Wait for CSS keyframe animation to finish
            nextSlide.addEventListener('animationend', endOnce, { once: false });
            // Fallback if the browser uses transitions
            nextSlide.addEventListener('transitionend', endOnce, { once: false });
            // Safety timeout fallback (slightly above CSS duration)
            fallbackTimer = setTimeout(endOnce, 700);
        } else if (effect === 'fade') {
            // Cross-fade: bring the next slide to the front visually while keeping the logical state until the end
            nextSlide.classList.add('mle-fade-in');
            currentSlide.classList.add('mle-fade-out');
            void nextSlide.offsetWidth; // force reflow
            nextSlide.addEventListener('animationend', endOnce, { once: false });
            nextSlide.addEventListener('transitionend', endOnce, { once: false });
            // Safety timeout fallback (slightly above CSS duration)
            fallbackTimer = setTimeout(endOnce, 600);
        } else {
            // Unknown effect: fallback to instant commit
            commitAfterAnimation();
        }
    };

    const goToSlide = (slideIndex, skipAnimation = false, skipFireEvent = false) => {
        if (slideIndex === currentSlideIndex) return;

        const normalizedIndex = (slideIndex + slides.length) % slides.length;
        const diff = normalizedIndex - currentSlideIndex;
        const direction = (diff + slides.length) % slides.length > slides.length / 2 ? 'left' : 'right';

        // Add a class guard to disable CSS selectors AND pass an explicit suppress flag
        if (skipAnimation) {
            carousel.classList.add('temp-no-animation');
            void carousel.offsetWidth; // force reflow
        }

        updateCarousel(normalizedIndex, direction, skipFireEvent, /* suppressAnimation */ skipAnimation === true);

        if (skipAnimation) {
            carousel.classList.remove('temp-no-animation');
        }
    };

    const handleGesture = () => {
        const swipeThreshold = 40;
        const distanceX = touchEndX - touchStartX;

        if (Math.abs(distanceX) > swipeThreshold && Math.abs(distanceX) > Math.abs(touchEndY - touchStartY)) {
            if (distanceX < 0) goToSlide((currentSlideIndex + 1) % slides.length);
            else goToSlide((currentSlideIndex - 1 + slides.length) % slides.length);
            handleInteraction();
        }

        touchStartX = touchEndX = touchStartY = touchEndY = 0;
    };

    const startAutoRide = () => {
        stopAutoRide();
        intervalId = setInterval(() => goToSlide((currentSlideIndex + 1) % slides.length), rideInterval);
    };

    const stopAutoRide = () => {
        if (intervalId !== null) {
            clearInterval(intervalId);
            intervalId = null;
        }
    };

    const handleInteraction = () => {
        if (!hasInteracted) {
            hasInteracted = true;
            if (ride && rideOnlyAfterInteraction) startAutoRide();
        }
        if (ride && !rideOnlyAfterInteraction) startAutoRide();
    };

    indicators.forEach((btn, i) => btn.addEventListener('click', () => {
        goToSlide(i);
        handleInteraction();
    }));

    prev?.addEventListener('click', () => { goToSlide((currentSlideIndex - 1 + slides.length) % slides.length); handleInteraction(); });
    next?.addEventListener('click', () => { goToSlide((currentSlideIndex + 1) % slides.length); handleInteraction(); });

    carousel.addEventListener('mouseenter', stopAutoRide);
    carousel.addEventListener('mouseleave', () => { if (ride && (!rideOnlyAfterInteraction || hasInteracted)) startAutoRide(); });
    carousel.addEventListener('focusin', stopAutoRide);
    carousel.addEventListener('focusout', () => { if (ride && (!rideOnlyAfterInteraction || hasInteracted)) startAutoRide(); });

    carousel.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') { e.preventDefault(); goToSlide((currentSlideIndex - 1 + slides.length) % slides.length); handleInteraction(); }
        else if (e.key === 'ArrowRight') { e.preventDefault(); goToSlide((currentSlideIndex + 1) % slides.length); handleInteraction(); }
    });

    carousel.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
        touchStartY = e.changedTouches[0].screenY;
    }, { passive: true });

    carousel.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        touchEndY = e.changedTouches[0].screenY;

        if (Math.abs(touchEndY - touchStartY) < swipeVerticalThreshold) handleGesture();
    }, { passive: true });

    if (slides.length > 1 && ride && !rideOnlyAfterInteraction) startAutoRide();

    // controller API
    const controller = {
        goToSlide: (slideIndex, skipAnimation = false, skifFireEvent = false) => goToSlide(slideIndex, skipAnimation, skifFireEvent),
        getCurrentSlideIndex: () => currentSlideIndex,
        goToNextSlide: () => goToSlide((currentSlideIndex + 1) % slides.length),
        goToPreviousSlide: () => goToSlide((currentSlideIndex - 1 + slides.length) % slides.length),
        pause: stopAutoRide,
        resume: startAutoRide,
    };

    carousels.set(carousel, controller);
}

// Reinit on updates
document.addEventListener('mediaManagerPreviewsUpdated', (e) => {
    const mediaManager = e.detail.mediaManager;
    document.querySelectorAll('[data-mle-carousel]').forEach(initCarousel);
});

document.querySelectorAll('[data-mle-carousel]').forEach(initCarousel);
