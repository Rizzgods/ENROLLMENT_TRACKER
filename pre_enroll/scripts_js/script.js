var swiper = new Swiper('.swiper-container', {
    slidesPerView: 1,
    spaceBetween: 5, // Reduced space between slides

    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },

    breakpoints: {
        640: {
            slidesPerView: 2,
            spaceBetween: 10, // Reduced space
        },
        768: {
            slidesPerView: 3,
            spaceBetween: 15, // Reduced space
        },
        1024: {
            slidesPerView: 4,
            spaceBetween: 20, // Reduced space
        },
    },
});

// Smooth scrolling for anchor links
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });
});