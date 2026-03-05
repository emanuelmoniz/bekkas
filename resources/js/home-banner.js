// carousel logic extracted from welcome.blade.php
export function carousel(initialSlides) {
    return {
        // rawIndex counts including clones at both ends
        // rawIndex counts including clones at both ends.  With two
        // clones per side the first real slide lives at position 2; for very
        // short lists we fall back to the original single‑clone layout and
        // start at position 1 instead.
        rawIndex: 2,
        slides: initialSlides,
        get cloneOffset() {
            // number of clone elements inserted on each side
            return this.slides.length <= 2 ? 1 : 2;
        },
        get displaySlides() {
            if (this.slides.length <= 2) {
                const last = this.slides[this.slides.length - 1];
                return [last, ...this.slides, this.slides[0]];
            }
            const lastTwo = this.slides.slice(-2);
            const firstTwo = this.slides.slice(0, 2);
            return [...lastTwo, ...this.slides, ...firstTwo];
        },
        current: 0,               // index within real slides
        // measurements chosen so that the "active" slide is centred
        // and a portion of the previous / next slides is visible at the
        // left and right edges respectively.
        slideWidth: 75, // percentage of container occupied by a single slide
        gap: 2.5, // percent total horizontal gap (shared either side)
        progress: 0,
        animate: true,
        interval: null,
        tickInterval: null,
        // whether we're currently mid‑transition; used to ignore extra
        // calls (prevents rawIndex creeping beyond the clone boundaries)
        transitioning: false,
        // drag/swipe tracking
        dragging: false,
        dragStartX: 0,
        dragOffset: 0,
        container: null,
        init() {
            // ensure initial current corresponds
            this.current = 0;
            // start from first real slide according to offset but avoid
            // animating the initial jump from the cloned slides into place.
            // Disable animation, set the rawIndex, then re-enable animation
            // after the next tick so the initial position appears instantly.
            this.animate = false;
            this.rawIndex = this.cloneOffset;
            // keep reference to container for drag calculations
            this.container = this.$refs.trackContainer;
            this.$nextTick(() => {
                this.animate = true;
                this.start();
            });
        },
        start() {
            if (this.interval) clearInterval(this.interval);
            if (this.tickInterval) clearInterval(this.tickInterval);
            this.progress = 0;
            const duration = 5000;
            this.interval = setInterval(() => { this.next(); }, duration);
            const tick = 50;
            this.tickInterval = setInterval(() => {
                this.progress += tick / duration;
                if (this.progress > 1) this.progress = 0;
            }, tick);
        },
        next() {
            // ignore requests while we're still animating a previous move
            if (this.transitioning) return;
            this.transitioning = true;
            // advance raw index but clamp so it never grows unbounded
            // rightmost allowed index depends on the number of clones at each
            // end; total entries = slides.length + cloneOffset*2, indices run
            // 0..total-1.
            const maxIndex = this.slides.length + this.cloneOffset * 2 - 1;
            this.rawIndex = Math.min(this.rawIndex + 1, maxIndex);
            this.current = (this.rawIndex - this.cloneOffset + this.slides.length) % this.slides.length;
            this.progress = 0;
            // if we land on a clone the reset will happen in
            // handleTransitionEnd, which also clears `transitioning`.
        },
        prev() {
            if (this.transitioning) return;
            this.transitioning = true;
            this.rawIndex = Math.max(this.rawIndex - 1, 0);
            this.current = (this.rawIndex - this.cloneOffset + this.slides.length) % this.slides.length;
            this.progress = 0;
            // if we've moved into the left clones, let `handleTransitionEnd`
            // perform the non-animated jump back into the real slides so the
            // reset is consistent with right-hand clone handling.
        },
        handleTransitionEnd() {
            // allow new movement now that the transition finished
            this.transitioning = false;
            // if we've landed on one of the right‑hand clones, jump back into
            // the real slides preserving the offset of how far into the clones
            // we were (0 or 1) so that peeking two slides ahead still works.
            const rightStart = this.slides.length + this.cloneOffset;
            if (this.rawIndex >= rightStart) {
                const offset = this.rawIndex - rightStart;
                this.animate = false;
                this.rawIndex = this.cloneOffset + offset;
                this.current = offset;
                this.$nextTick(() => { this.animate = true; });
            }
            // symmetric handling for left-hand clones: jump to the
            // corresponding real slides without animating so the user does
            // not see a slide animation from fake to real slides.
            if (this.rawIndex < this.cloneOffset) {
                // offset from the inner-most clone (0 when touching the
                // clone that's a copy of the last slide)
                const offsetFromInner = this.cloneOffset - 1 - this.rawIndex;
                this.animate = false;
                if (offsetFromInner === 0) {
                    // clone of last slide -> map to real last
                    this.rawIndex = this.slides.length + this.cloneOffset - 1;
                } else {
                    // map further-left clones back accordingly
                    this.rawIndex = this.slides.length + this.cloneOffset - 2;
                }
                this.current = (this.rawIndex - this.cloneOffset + this.slides.length) % this.slides.length;
                this.$nextTick(() => { this.animate = true; });
            }
        },
        startDrag(evt) {
            // allow horizontal dragging; prevent vertical scroll only when
            // pointer is touching the track
            evt.preventDefault();
            // interrupt any current transition so the user can take control
            this.transitioning = false;
            // disable animated transition while user is actively moving
            this.animate = false;
            this.dragging = true;
            this.dragStartX = evt.clientX;
            this.dragOffset = 0;
            // capture pointer so we continue to get move/up events even if the
            // finger leaves the element bounds
            if (evt.pointerId && evt.target && evt.target.setPointerCapture) {
                try { evt.target.setPointerCapture(evt.pointerId); } catch(e) {}
            }
            // pause autoplay while dragging
            if (this.interval) clearInterval(this.interval);
        },


        onDrag(evt) {
            if (!this.dragging) return;
            this.dragOffset = evt.clientX - this.dragStartX;
        },
        endDrag(evt) {
            if (!this.dragging) return;
            this.dragging = false;
            // release pointer capture if we grabbed it
            if (evt.pointerId && evt.target && evt.target.releasePointerCapture) {
                try { evt.target.releasePointerCapture(evt.pointerId); } catch(e) {}
            }
            const delta = this.dragOffset;
            this.dragOffset = 0;
            const width = this.container ? this.container.clientWidth : 0;
            const threshold = width * (this.slideWidth/100) / 3;
            if (delta > threshold) {
                this.prev();
            } else if (delta < -threshold) {
                this.next();
            }
            // re-enable animation so subsequent transitions look smooth
            this.animate = true;
            this.start();
        },


        goTo(idx) {
            // jumping manually also counts as a transition; prevent spammed
            // clicks from driving rawIndex out of bounds.
            if (this.transitioning) return;
            this.transitioning = true;
            this.rawIndex = idx + this.cloneOffset;
            this.current = idx;
            this.progress = 0;
            this.start();
        },
        get trackStyle() {
            const percent = -((this.slideWidth + this.gap)*this.rawIndex - ((100 - this.slideWidth - this.gap)/2));
            if (this.dragging && this.dragOffset) {
                return `transform: translateX(calc(${percent}% + ${this.dragOffset}px));`;
            }
            return `transform: translateX(${percent}%);`;
        }
    };
}

// attach to global so Alpine's x-data can see it
window.carousel = carousel;
