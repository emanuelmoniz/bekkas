import './bootstrap';

// register custom scripts
import './image-scroller';
import './image-gallery';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// import the carousel helper for the home banner; it registers itself globally
import './home-banner';
import './contact-validation';
import './home-splash';
import './section-animations';
import { favoriteToggle } from './favorite-toggle';

// Register Alpine data components
Alpine.data('favoriteToggle', favoriteToggle);

// Initialize favorites store //...
Alpine.store('favorites', {
    count: window.initialFavoritesCount || 0
});

// Optional cart count store for UI components that might need it
Alpine.store('cart', {
    count: window.initialCartCount || 0
});

// Contact section in-view store — updated by IntersectionObserver on the homepage
Alpine.store('contactInView', false);

// Global flash store — use for client-displayed flash messages (keeps UI consistent)
Alpine.store('flash', {
    show: false,
    type: 'success',
    message: null,

    showMessage(msg, type = 'success') {
        this.message = msg;
        this.type = type || 'success';
        this.show = true;

        // Scroll the flash container into view so users away from the top
        // of the page can see validation/errors triggered by actions.
        try {
            const el = document.querySelector('[data-flash-root]');
            if (el) {
                const doScroll = () => {
                    try {
                        // Prefer a header/nav with sticky positioning used by the layout
                        const header = document.querySelector('nav.sticky, header.sticky, .sticky.top-0, .site-header');
                        const headerHeight = header ? Math.ceil(header.getBoundingClientRect().height) : 0;
                        const margin = 8; // small gap between header and flash
                        const rect = el.getBoundingClientRect();
                        const target = window.scrollY + rect.top - headerHeight - margin;
                        window.scrollTo({ top: Math.max(0, target), behavior: 'smooth' });
                    } catch (e) {
                        try { el.scrollIntoView({ behavior: 'smooth', block: 'start' }); } catch (er) { el.scrollIntoView(); }
                    }
                };

                if (window.Alpine && typeof window.Alpine.nextTick === 'function') {
                    window.Alpine.nextTick(doScroll);
                } else {
                    requestAnimationFrame(doScroll);
                }
            }
        } catch (e) {
            // ignore scrolling failures
        }

        // Do NOT auto-focus the close button. Keeping the button reachable via keyboard
        // (tabindex toggles) preserves accessibility without surprising focus changes.
        // (Users can still focus the button manually or via keyboard navigation.)
    },

    hide() {
        // If a focusable element inside the alert currently has focus, synchronously move focus out
        try {
            const active = document.activeElement;
            if (active && active.closest) {
                // If the focused element is inside the flash root, move focus synchronously
                const flashRoot = active.closest('[data-flash-root]') || active.closest('[role="alert"]');
                if (flashRoot) {
                    // Prefer focusing an existing landmark (<main>) — make it temporarily focusable if needed
                    const main = document.querySelector('main');
                    const focusTarget = main || document.body;

                    // Make the target programmatically focusable if necessary, focus it, then restore DOM
                    const addedTabIndex = !focusTarget.hasAttribute('tabindex');
                    if (addedTabIndex) focusTarget.setAttribute('tabindex', '-1');
                    try { focusTarget.focus({ preventScroll: true }); } catch (e) { try { focusTarget.focus(); } catch (er) { /* ignore */ } }
                    if (addedTabIndex) focusTarget.removeAttribute('tabindex');

                    // blur the previously-focused element to ensure no descendant retains focus
                    try { active.blur(); } catch (e) { /* ignore */ }
                }
            }
        } catch (e) {
            // defensive: ignore focus-management failures
        }

        // Finally hide the flash (aria-hidden will update after this)
        this.show = false;
    }
});

Alpine.start();
