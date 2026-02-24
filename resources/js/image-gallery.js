// Image Gallery component behaviour.
//
// Exposes two globals used by the Blade component:
//
//   imageGallery(imagesData)  – Alpine x-data factory for the gallery
//   GalleryPanZoom            – handles zoom/pan inside the lightbox
//
// The Blade component drives all UI state via Alpine; this file supplies
// the Alpine data factory and the zoom/pan engine.

// ─────────────────────────────────────────────────────────────────────────────
// GalleryPanZoom  –  mouse-wheel zoom, drag-pan, and pinch-to-zoom for the
//                    full-screen lightbox image.
// ─────────────────────────────────────────────────────────────────────────────
class GalleryPanZoom {
    constructor(container, img) {
        this.container = container;
        this.img = img;
        this.scale = 1;
        this.tx = 0;
        this.ty = 0;
        this.minScale = 1;
        this.maxScale = 8;

        // drag state
        this._dragging = false;
        this._lastX = 0;
        this._lastY = 0;

        // pinch state
        this._pinchDist = null;
        this._pinchScale = null;
        this._pinchMidX = 0;
        this._pinchMidY = 0;

        this._bound = {
            wheel:      this._onWheel.bind(this),
            mousedown:  this._onMouseDown.bind(this),
            mousemove:  this._onMouseMove.bind(this),
            mouseup:    this._onMouseUp.bind(this),
            touchstart: this._onTouchStart.bind(this),
            touchmove:  this._onTouchMove.bind(this),
            touchend:   this._onTouchEnd.bind(this),
        };

        // wheel events may originate on either the container or the img itself
        container.addEventListener('wheel',      this._bound.wheel,      { passive: false });
        img.addEventListener('wheel',            this._bound.wheel,      { passive: false });

        container.addEventListener('mousedown',  this._bound.mousedown);
        container.addEventListener('touchstart', this._bound.touchstart, { passive: false });
        container.addEventListener('touchmove',  this._bound.touchmove,  { passive: false });
        container.addEventListener('touchend',   this._bound.touchend);
        // mouse move/up on document so dragging outside container still works
        document.addEventListener('mousemove', this._bound.mousemove);
        document.addEventListener('mouseup',   this._bound.mouseup);

        this._applyTransform();
    }

    // ── public ────────────────────────────────────────────────────────────────

    zoomBy(delta, originX, originY) {
        const rect = this.container.getBoundingClientRect();
        const cx = originX !== undefined ? originX : rect.left + rect.width  / 2;
        const cy = originY !== undefined ? originY : rect.top  + rect.height / 2;

        const newScale = Math.min(this.maxScale, Math.max(this.minScale, this.scale + delta));
        if (newScale === this.scale) return;

        // adjust translation so the zoom origin stays fixed on screen
        const ratio = newScale / this.scale;
        this.tx = cx - ratio * (cx - this.tx);
        this.ty = cy - ratio * (cy - this.ty);
        this.scale = newScale;

        this._clampTranslation();
        this._applyTransform();
    }

    reset() {
        this.scale = 1;
        this.tx = 0;
        this.ty = 0;
        this._applyTransform();
    }

    destroy() {
        const c = this.container;
        c.removeEventListener('wheel',      this._bound.wheel);
        c.removeEventListener('mousedown',  this._bound.mousedown);
        c.removeEventListener('touchstart', this._bound.touchstart);
        c.removeEventListener('touchmove',  this._bound.touchmove);
        c.removeEventListener('touchend',   this._bound.touchend);
        document.removeEventListener('mousemove', this._bound.mousemove);
        document.removeEventListener('mouseup',   this._bound.mouseup);
    }

    // ── private ───────────────────────────────────────────────────────────────

    _applyTransform() {
        this.img.style.transform =
            `translate(${this.tx}px, ${this.ty}px) scale(${this.scale})`;
        this.img.style.cursor = this.scale > 1 ? 'grab' : 'default';
        if (this._dragging) this.img.style.cursor = 'grabbing';
    }

    _clampTranslation() {
        if (this.scale <= 1) {
            this.tx = 0;
            this.ty = 0;
            return;
        }
        const rect  = this.container.getBoundingClientRect();
        const iRect = this.img.getBoundingClientRect();
        // how much the zoomed image overflows in each axis
        const overflowX = Math.max(0, (iRect.width  / this.scale * this.scale - rect.width)  / 2);
        const overflowY = Math.max(0, (iRect.height / this.scale * this.scale - rect.height) / 2);

        const imgNatW = this.img.naturalWidth  || this.img.offsetWidth;
        const imgNatH = this.img.naturalHeight || this.img.offsetHeight;

        // compute displayed (fitted) image size before our custom transform
        const containerAR = rect.width / rect.height;
        const imageAR     = imgNatW / (imgNatH || 1);
        let baseW, baseH;
        if (imageAR > containerAR) {
            baseW = rect.width;
            baseH = rect.width / imageAR;
        } else {
            baseH = rect.height;
            baseW = rect.height * imageAR;
        }

        const scaledW = baseW * this.scale;
        const scaledH = baseH * this.scale;

        const maxTx = Math.max(0, (scaledW - rect.width)  / 2);
        const maxTy = Math.max(0, (scaledH - rect.height) / 2);

        this.tx = Math.max(-maxTx, Math.min(maxTx, this.tx));
        this.ty = Math.max(-maxTy, Math.min(maxTy, this.ty));
    }

    _onWheel(e) {
        e.preventDefault();
        const delta = e.deltaY > 0 ? -0.2 : 0.2;
        this.zoomBy(delta, e.clientX, e.clientY);
    }

    _onMouseDown(e) {
        if (this.scale <= 1) return;
        this._dragging = true;
        this._lastX = e.clientX;
        this._lastY = e.clientY;
        this.img.style.cursor = 'grabbing';
        e.preventDefault();
    }

    _onMouseMove(e) {
        if (!this._dragging) return;
        this.tx += e.clientX - this._lastX;
        this.ty += e.clientY - this._lastY;
        this._lastX = e.clientX;
        this._lastY = e.clientY;
        this._clampTranslation();
        this._applyTransform();
    }

    _onMouseUp() {
        if (!this._dragging) return;
        this._dragging = false;
        this._applyTransform();
    }

    _touchDist(touches) {
        const dx = touches[0].clientX - touches[1].clientX;
        const dy = touches[0].clientY - touches[1].clientY;
        return Math.hypot(dx, dy);
    }

    _touchMid(touches) {
        return {
            x: (touches[0].clientX + touches[1].clientX) / 2,
            y: (touches[0].clientY + touches[1].clientY) / 2,
        };
    }

    _onTouchStart(e) {
        if (e.touches.length === 2) {
            e.preventDefault();
            this._pinchDist  = this._touchDist(e.touches);
            this._pinchScale = this.scale;
            const mid = this._touchMid(e.touches);
            this._pinchMidX = mid.x;
            this._pinchMidY = mid.y;
            this._dragging = false;
        } else if (e.touches.length === 1 && this.scale > 1) {
            this._dragging = true;
            this._lastX = e.touches[0].clientX;
            this._lastY = e.touches[0].clientY;
        }
    }

    _onTouchMove(e) {
        if (e.touches.length === 2 && this._pinchDist !== null) {
            e.preventDefault();
            const dist  = this._touchDist(e.touches);
            const ratio = dist / this._pinchDist;
            const newScale = Math.min(this.maxScale, Math.max(this.minScale, this._pinchScale * ratio));
            const scaleRatio = newScale / this.scale;
            this.tx = this._pinchMidX - scaleRatio * (this._pinchMidX - this.tx);
            this.ty = this._pinchMidY - scaleRatio * (this._pinchMidY - this.ty);
            this.scale = newScale;
            this._clampTranslation();
            this._applyTransform();
        } else if (e.touches.length === 1 && this._dragging) {
            e.preventDefault();
            this.tx += e.touches[0].clientX - this._lastX;
            this.ty += e.touches[0].clientY - this._lastY;
            this._lastX = e.touches[0].clientX;
            this._lastY = e.touches[0].clientY;
            this._clampTranslation();
            this._applyTransform();
        }
    }

    _onTouchEnd(e) {
        if (e.touches.length < 2) {
            this._pinchDist  = null;
            this._pinchScale = null;
        }
        if (e.touches.length === 0) {
            this._dragging = false;
            this._applyTransform();
        }
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Alpine data factory
// ─────────────────────────────────────────────────────────────────────────────
window.imageGallery = function imageGallery(imagesData) {
    return {
        images:        imagesData || [],
        selectedIndex: 0,
        thumbOffset:   0,
        lightboxOpen:  false,
        _panZoom:      null,

        // ── computed ─────────────────────────────────────────────────────────

        get current() {
            return this.images[this.selectedIndex] || { url: '', original: null };
        },
        get currentOriginal() {
            return this.current.original || this.current.url;
        },
        get canPrev() {
            return this.selectedIndex > 0;
        },
        get canNext() {
            return this.selectedIndex < this.images.length - 1;
        },
        get canThumbPrev() {
            return this.thumbOffset > 0;
        },
        get canThumbNext() {
            return this.thumbOffset + 4 < this.images.length;
        },
        get visibleThumbs() {
            return this.images.slice(this.thumbOffset, this.thumbOffset + 4);
        },

        // ── actions ──────────────────────────────────────────────────────────

        selectImage(index) {
            this.selectedIndex = index;
            // keep selected thumb in the visible window
            if (index < this.thumbOffset) {
                this.thumbOffset = index;
            } else if (index >= this.thumbOffset + 4) {
                this.thumbOffset = index - 3;
            }
            // reset zoom when the image changes inside the lightbox
            if (this._panZoom) this._panZoom.reset();
        },

        prevImage() {
            if (this.canPrev) this.selectImage(this.selectedIndex - 1);
        },

        nextImage() {
            if (this.canNext) this.selectImage(this.selectedIndex + 1);
        },

        thumbPrev() {
            if (this.canThumbPrev) this.thumbOffset -= 1;
        },

        thumbNext() {
            if (this.canThumbNext) this.thumbOffset += 1;
        },

        openLightbox() {
            this.lightboxOpen = true;
        },

        init() {
            // watch for when the lightbox becomes visible so we can attach
            // pan/zoom logic.  we also tear it down when it closes.
            this.$watch('lightboxOpen', (open) => {
                if (open) {
                    this.$nextTick(() => {
                        const container = this.$refs.lightboxContainer;
                        const img       = this.$refs.lightboxImg;
                        if (container && img) {
                            this._panZoom = new GalleryPanZoom(container, img);
                        }
                    });
                } else if (this._panZoom) {
                    this._panZoom.destroy();
                    this._panZoom = null;
                }
            });
        },

        closeLightbox() {
            this.lightboxOpen = false;
            if (this._panZoom) {
                this._panZoom.destroy();
                this._panZoom = null;
            }
        },

        zoomIn()    { if (this._panZoom) this._panZoom.zoomBy(0.5);  },
        zoomOut()   { if (this._panZoom) this._panZoom.zoomBy(-0.5); },
        zoomReset() { if (this._panZoom) this._panZoom.reset();       },
    };
};

window.GalleryPanZoom = GalleryPanZoom;
