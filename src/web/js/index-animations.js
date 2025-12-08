/**
 * UIS Homepage Scroll Animations
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // INTERSECTION OBSERVER FOR SCROLL ANIMATIONS

    // Options for the observer
    const observerOptions = {
        root: null, // Use viewport as root
        rootMargin: '0px 0px -50px 0px', // Trigger slightly before element is in view
        threshold: 0.1 // Trigger when 10% of element is visible
    };
    
    // Callback function when elements intersect
    const observerCallback = (entries, observer) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                // add a small delay based on index for staggered effect
                setTimeout(() => {
                    entry.target.classList.add('visible');
                }, index * 100);
            }
        });
    };
    
    // Create the observer
    const observer = new IntersectionObserver(observerCallback, observerOptions);
    
    // Observe all sections
    const sections = document.querySelectorAll('section');
    sections.forEach(section => {
        observer.observe(section);
    });
    
    // SMOOTH SCROLL FOR NAV LINKS  
    const navLinks = document.querySelectorAll('.sm-nav a[href^="#"], nav a[href^="#"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                // Add visible class to target section immediately
                targetElement.classList.add('visible');
                
                // Smooth scroll to target
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // HEADER PARALLAX EFFECT
    const header = document.querySelector('header');
    
    if (header) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const rate = scrolled * 0.3;
            
            // Only apply if not scrolled too far (performance)
            if (scrolled < 500) {
                header.style.backgroundPositionY = rate + 'px';
            }
        }, { passive: true });
    }
    
    // TABLE ROW HOVER SOUND EFFECT
    const tableRows = document.querySelectorAll('table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            // Add a subtle scale animation
            this.style.transform = 'scale(1.02)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // COUNTER ANIMATION FOR STATS 
    
    function animateCounter(element, target, duration = 2000) {
        let start = 0;
        const increment = target / (duration / 16);
        
        const timer = setInterval(() => {
            start += increment;
            if (start >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(start);
            }
        }, 16);
    }
    
    
    const headerElement = document.querySelector('header');
    if (headerElement && !document.querySelector('.scroll-indicator')) {
        const scrollIndicator = document.createElement('div');
        scrollIndicator.className = 'scroll-indicator';
        scrollIndicator.title = 'Scroll down';
        scrollIndicator.addEventListener('click', function() {
            const firstSection = document.querySelector('section');
            if (firstSection) {
                firstSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
        headerElement.appendChild(scrollIndicator);
    }
    
    console.log('UIS Homepage animations initialized');
});