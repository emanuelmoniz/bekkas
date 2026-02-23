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
    let paused = false;

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

    // pause/resume on pointer enter/leave; covers mouse and touch
    container.addEventListener('pointerenter', pause);
    container.addEventListener('pointerleave', resume);

    schedule();
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-image-scroller]').forEach(initialiseScroller);
});
