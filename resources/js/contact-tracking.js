/**
 * Contact section active-state tracker (homepage only).
 *
 * Sets the Alpine.js `contactInView` store to true/false so the navigation
 * can highlight the Contact item while the #contact section is visible.
 */
function initContactTracking() {
    const el = document.getElementById('contact');
    if (!el || !window.Alpine) return;

    // Active immediately when the page loads with #contact in the URL
    if (window.location.hash === '#contact') {
        Alpine.store('contactInView', true);
    }

    // Keep active while the contact section intersects the viewport
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            Alpine.store('contactInView', entry.isIntersecting);
        });
    }, { threshold: 0.1 });

    observer.observe(el);

    // React to hash navigation (e.g. smooth-scroll via nav click)
    window.addEventListener('hashchange', () => {
        if (window.location.hash === '#contact') {
            Alpine.store('contactInView', true);
        }
    });
}

// Run after Alpine has started; handle both early and late script loading
if (window.Alpine?.store) {
    initContactTracking();
} else {
    document.addEventListener('alpine:initialized', initContactTracking, { once: true });
}
