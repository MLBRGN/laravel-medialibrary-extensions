document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-carousel]').forEach(carousel => {
        const items = carousel.querySelectorAll('.carousel-item');
        const indicators = carousel.querySelectorAll('.carousel-indicators button');
        const prev = carousel.querySelector('[data-slide="prev"]');
        const next = carousel.querySelector('[data-slide="next"]');

        const ride = carousel.getAttribute('data-carousel-ride') === 'true';
        const rideInterval = Number(carousel.getAttribute('data-carousel-ride-interval') ?? '5000');
        const rideOnlyAfterInteraction = carousel.getAttribute('data-carousel-ride-only-after-interaction') === 'true';

        let currentIndex = 0;
        let hasInteracted = false;
        let intervalId = null;

        const updateCarousel = (index) => {
            items.forEach((item, i) => item.classList.toggle('active', i === index));
            indicators.forEach((btn, i) => btn.classList.toggle('active', i === index));
        };

        const goToSlide = (index) => {
            currentIndex = (index + items.length) % items.length;
            updateCarousel(currentIndex);
        };

        const startAutoRide = () => {
            stopAutoRide(); // always clear first to avoid duplicates
            intervalId = setInterval(() => {
                goToSlide((currentIndex + 1) % items.length);
            }, rideInterval);
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
                if (ride && rideOnlyAfterInteraction) {
                    startAutoRide();
                }
            }

            if (ride && !rideOnlyAfterInteraction) {
                startAutoRide();
            }
        };

        indicators.forEach((btn, i) => {
            btn.addEventListener('click', () => {
                goToSlide(i);
                handleInteraction();
            });
        });

        prev?.addEventListener('click', () => {
            goToSlide((currentIndex - 1 + items.length) % items.length);
            handleInteraction();
        });

        next?.addEventListener('click', () => {
            goToSlide((currentIndex + 1) % items.length);
            handleInteraction();
        });

        carousel.addEventListener('mouseenter', stopAutoRide);
        carousel.addEventListener('mouseleave', () => {
            if (ride && (!rideOnlyAfterInteraction || hasInteracted)) {
                startAutoRide();
            }
        });

        if (items.length > 1 && ride && !rideOnlyAfterInteraction) {
            startAutoRide();
        }
    });
});
