// File: public/js/contact-animations.js

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {

    // Reveal elements when they come into view
    // Use the class added in the Blade file
    const revealElements = document.querySelectorAll('.reveal-item');

    if (revealElements.length > 0) {
        const observerOptions = {
          threshold: 0.3 // Trigger a bit earlier
        };

        const observer = new IntersectionObserver((entries, observer) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add('show');
              observer.unobserve(entry.target); // Stop observing once shown
            }
          });
        }, observerOptions);

        revealElements.forEach(element => {
          observer.observe(element);
        });
    }

    // Reveal hero title (using ID added in Blade)
    const heroTitle = document.getElementById('hero-title');
    if (heroTitle) {
        // Add class slightly after load to ensure transition runs
        setTimeout(() => {
            heroTitle.classList.add('show');
        }, 100); // Small delay
    }

}); // End DOMContentLoaded