// main.js — CS2 Knife Wiki
// Minimal JS for UI enhancements

document.addEventListener('DOMContentLoaded', () => {

    // Animate knife cards on scroll
    const cards = document.querySelectorAll('.knife-card, .detail-card, .stat-box');
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, i) => {
                if (entry.isIntersecting) {
                    entry.target.style.animationDelay = (i * 0.07) + 's';
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            observer.observe(card);
        });
    }

    // Highlight active nav link
    const currentPath = window.location.pathname.split('/').pop();
    document.querySelectorAll('.cs2-nav-link').forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPath || (currentPath === '' && href === 'index.php')) {
            link.style.color = 'var(--accent, #e4a317)';
        }
    });

});
