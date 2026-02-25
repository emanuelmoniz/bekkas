// animation helper for sequential slide-up of any grouped items
// usage: surround a set of elements with a container having the class
// `animate-sequence` and give each child the class `anim-item`.

document.addEventListener('DOMContentLoaded', () => {
    const DELAY_STEP = 200; // ms between each item in a visible batch

    const containers = document.querySelectorAll('.animate-sequence');
    if (!containers.length) return;

    containers.forEach(container => {
        const items = Array.from(container.querySelectorAll('.anim-item'));
        if (!items.length) return;

        const observer = new IntersectionObserver((entries, obs) => {
            // Sort newly-visible items top-to-bottom so stagger order matches
            // their visual position, regardless of any data-index attribute.
            const visible = entries
                .filter(e => e.isIntersecting)
                .sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top);

            visible.forEach((entry, batchIdx) => {
                setTimeout(() => {
                    entry.target.classList.add('animate-in');
                }, batchIdx * DELAY_STEP);

                obs.unobserve(entry.target);
            });
        }, { threshold: 0.05 });

        items.forEach(item => observer.observe(item));
    });
});
