import './bootstrap';

// register custom scripts
import './image-scroller';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// import the carousel helper for the home banner; it registers itself globally
import './home-banner';
import './contact-validation';
import './home-splash';
import './section-animations';

// Initialize favorites store //...
Alpine.store('favorites', {
    count: window.initialFavoritesCount || 0
});

// Global flash store — use for client-displayed flash messages (keeps UI consistent)
Alpine.store('flash', {
    show: false,
    type: 'success',
    message: null,
    // internal timer handle for auto-hide
    _autoHide: null,

    showMessage(msg, type = 'success') {
        // clear any pending hide timer
        if (this._autoHide) {
            clearTimeout(this._autoHide);
            this._autoHide = null;
        }

        this.message = msg;
        this.type = type || 'success';
        this.show = true;

        // Do NOT auto-focus the close button. Keeping the button reachable via keyboard
        // (tabindex toggles) preserves accessibility without surprising focus changes.
        // (Users can still focus the button manually or via keyboard navigation.)

        // auto-hide after 6s (store the timer so it can be cancelled)
        this._autoHide = setTimeout(() => {
            this.hide();
            this._autoHide = null;
        }, 6000);
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

        // clear any pending hide timer
        if (this._autoHide) {
            clearTimeout(this._autoHide);
            this._autoHide = null;
        }

        // Finally hide the flash (aria-hidden will update after this)
        this.show = false;
    }
});

Alpine.start();
