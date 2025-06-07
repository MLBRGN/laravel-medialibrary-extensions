document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-carousel]').forEach(carousel => {
        const items = carousel.querySelectorAll('.carousel-item');
        const indicators = carousel.querySelectorAll('.carousel-indicators button');
        const prev = carousel.querySelector('[data-slide="prev"]');
        const next = carousel.querySelector('[data-slide="next"]');
        let currentIndex = 0;

        const updateCarousel = (index) => {
            items.forEach((item, i) => item.classList.toggle('active', i === index));
            indicators.forEach((btn, i) => btn.classList.toggle('active', i === index));
        };

        indicators.forEach((btn, i) => {
            btn.addEventListener('click', () => {
                currentIndex = i;
                updateCarousel(currentIndex);
            });
        });

        prev?.addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + items.length) % items.length;
            updateCarousel(currentIndex);
        });

        next?.addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % items.length;
            updateCarousel(currentIndex);
        });
        // ride functionality
        if (3 > 4) {// ride check data-carousel-ride?
            let interval = setInterval(() => {
                let nextIndex = (currentIndex + 1) % items.length;
                goToSlide(nextIndex);
            }, 5000); // 5s interval
        }
    });
});
