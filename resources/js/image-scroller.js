// Basic image scroller behaviour. Expects a container with
// `data-image-scroller` attribute containing a JSON-encoded configuration
// object (currently only 'interval' is read). The component markup is
// produced by the Blade component and is a structure like:
//
// <div data-image-scroller="{...}">
//   <div class="scroller">
//     <div class="slide" style="background-image:url(...)"/>
//     ...
//   </div>
// </div>
//
// Each slide is 100% width of the outer container; the inner scroller is
// moved horizontally with a CSS transform. When the last slide is reached the
// script briefly duplicates the first slide to permit a seamless loop, then
// jumps back to the beginning without animation.

function initialiseScroller(container) {
    let config;
    try {
        config = JSON.parse(container.getAttribute('data-image-scroller'));
    } catch (e) {
        return; // invalid configuration
    }

    const interval = (config.interval || 3000) | 0;
    const autoplay = config.autoplay !== false; // default true
    const scroller = container.querySelector('.scroller');
    const slides = Array.from(container.querySelectorAll('.slide'));
    if (!scroller || slides.length <= 1) {
        return;
    }

    // clone first slide so the transition from last->first looks continuous
    const firstClone = slides[0].cloneNode(true);
    scroller.appendChild(firstClone);
    const total = slides.length;
    let index = 0;
    let timer = null;
    // when autoplay is disabled we start paused until user interaction
    let paused = !autoplay;

    function schedule() {
        timer = setTimeout(step, interval);
    }

    function step() {
        if (paused) {
            // do not advance while paused; re-schedule and bail
            schedule();
            return;
        }

        index += 1;
        scroller.style.transition = 'transform 0.5s ease';
        scroller.style.transform = `translateX(-${index * 100}%)`;

        if (index === total) {
            // after transition completes, reset without animation
            setTimeout(() => {
                scroller.style.transition = 'none';
                scroller.style.transform = 'translateX(0)';
                index = 0;
            }, 500);
        }

        schedule();
    }

    function pause() {
        paused = true;
        if (timer) {
            clearTimeout(timer);
            timer = null;
        }
    }

    function resume() {
        if (!paused) return;
        paused = false;
        schedule();
    }

    // event handling differs depending on autoplay setting
    // target elements: the scroller itself and any enclosing link (card) element.
    const targets = [container];
    const linkAncestor = container.closest('a');
    if (linkAncestor && !targets.includes(linkAncestor)) {
        targets.push(linkAncestor);
    }

    const addListeners = (elem) => {
        if (autoplay) {
            // default: auto scroll continuously, pause on hover/touch
            elem.addEventListener('pointerenter', pause);
            elem.addEventListener('pointerleave', resume);
        } else {
            // non-autoplay: remain still until user interacts, then scroll until leave
            elem.addEventListener('pointerenter', resume);
            elem.addEventListener('pointerleave', pause);
        }
    };

    targets.forEach(addListeners);

    if (autoplay) {
        schedule();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-image-scroller]').forEach(initialiseScroller);
});
