// animation helper for sequential slide-up of any grouped items
// usage: surround a set of elements with a container having class
// `animate-sequence` and give each child the class `anim-item`. An
// optional `data-index` lets you override the automatic ordering.

document.addEventListener('DOMContentLoaded', () => {
    const DELAY_STEP = 200; // ms between each item

    // find all containers that should animate their children in sequence
    const containers = document.querySelectorAll('.animate-sequence');
    if (!containers.length) return;

    containers.forEach(container => {
        const items = Array.from(container.querySelectorAll('.anim-item'));
        if (!items.length) return;

        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const dataIdx = parseInt(el.dataset.index, 10);
                    // default to position in NodeList if no valid data-index
                    const idx = !isNaN(dataIdx) ? dataIdx : items.indexOf(el);

                    setTimeout(() => {
                        el.classList.add('animate-in');
                    }, idx * DELAY_STEP);

                    obs.unobserve(el);
                }
            });
        }, { threshold: 0.3 });

        items.forEach(item => observer.observe(item));
    });
});
