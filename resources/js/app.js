import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Initialize favorites store //...
Alpine.store('favorites', {
    count: window.initialFavoritesCount || 0
});

Alpine.start();
