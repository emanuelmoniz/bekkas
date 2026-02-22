// carousel logic extracted from welcome.blade.php
export function carousel(initialSlides) {
    return {
        // rawIndex counts including clones at both ends
        rawIndex: 1,
        slides: initialSlides,
        get displaySlides() {
            // clone last at start and first at end
            const last = this.slides[this.slides.length - 1];
            return [last, ...this.slides, this.slides[0]];
        },
        current: 0,               // index within real slides
        // measurements chosen so that the "active" slide is centred
        // and a portion of the previous / next slides is visible at the
        // left and right edges respectively.
        slideWidth: 70, // percentage of container occupied by a single slide
        gap: 6, // percent total horizontal gap (shared either side)
        progress: 0,
        animate: true,
        interval: null,
        tickInterval: null,
        // drag/swipe tracking
        dragging: false,
        dragStartX: 0,
        dragOffset: 0,
        container: null,
        init() {
            // ensure initial current corresponds
            this.current = 0;
            // keep reference to container for drag calculations
            this.container = this.$refs.trackContainer;
            this.start();
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
            this.rawIndex++;
            // update current modulo length
            this.current = (this.rawIndex - 1 + this.slides.length) % this.slides.length;
            this.progress = 0;
            // if advanced onto trailing clone we will perform a reset when the
            // animation completes; the @transitionend listener triggers the
            // actual jump so we avoid any timing mismatch or visible flicker.
        },
        prev() {
            this.rawIndex--;
            this.current = (this.rawIndex - 1 + this.slides.length) % this.slides.length;
            this.progress = 0;
            if (this.rawIndex === 0) {
                // handle clone at left edge by resetting to real last slide
                setTimeout(() => {
                    this.animate = false;
                    this.rawIndex = this.slides.length;
                    this.$nextTick(() => { this.animate = true; });
                }, 1000);
            }
        },
        handleTransitionEnd() {
            if (this.rawIndex === this.slides.length + 1) {
                // temporarily disable animation before resetting index
                this.animate = false;
                this.rawIndex = 1;
                // restore transition on next tick
                this.$nextTick(() => { this.animate = true; });
            }
        },
        startDrag(evt) {
            // allow horizontal dragging; prevent vertical scroll only when
            // pointer is touching the track
            evt.preventDefault();
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
            this.rawIndex = idx + 1;
            this.current = idx;
            this.progress = 0;
            this.start();
        },
        get trackStyle() {
            const percent = -((this.slideWidth + this.gap)*this.rawIndex - ((100 - this.slideWidth)/2));
            if (this.dragging && this.dragOffset) {
                return `transform: translateX(calc(${percent}% + ${this.dragOffset}px));`;
            }
            return `transform: translateX(${percent}%);`;
        }
    };
}

// attach to global so Alpine's x-data can see it
window.carousel = carousel;
