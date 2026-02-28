@props(['slides'])

<section x-data="carousel({{ json_encode($slides) }})" x-init="init()" class="relative w-full overflow-hidden bg-light py-12 rounded-xl">
    <!-- outer wrapper adds horizontal padding to let prev/next slides peek inside -->
    <div x-ref="trackContainer" 
         class="relative z-10 w-full flex items-center justify-center overflow-visible px-6"
         style="touch-action: pan-y;"
         :class="dragging ? 'cursor-grabbing' : 'cursor-grab'"
         @pointerdown="startDrag($event)"
         @pointermove.window="onDrag($event)"
         @pointerup.window="endDrag($event)"
         @pointercancel.window="endDrag($event)">
        <div class="flex w-full" :class="{ 'transition-transform duration-1000': animate }"
             :style="trackStyle"
             @transitionend="handleTransitionEnd">
            <template x-for="(slide,index) in displaySlides" :key="index">
                <div class="flex-none" :style="`width: ${slideWidth}%; margin: 0 ${gap/2}%`">
                    <!-- each slide has rounded corners and hides overflow -->
                    <div class="bg-cover bg-center min-h-[50vh] lg:min-h-[75vh] rounded-2xl overflow-hidden"
                         :style="`background-image: url('${slide.image}')`">
                        <div class="min-h-[50vh] lg:min-h-[75vh] flex flex-col items-center justify-end text-center text-white px-6 pb-20 bg-dark/40">
                            <h1 class="text-4xl lg:text-6xl font-bold mb-4" x-text="slide.tagline"></h1>
                            <a :href="slide.buttonUrl" class="inline-block bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-full uppercase font-semibold transition-colors" x-text="slide.buttonText"></a>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- navigation bars below carousel -->
    <div class="absolute bottom-3 left-0 w-full flex justify-center gap-4">
        <template x-for="(slide,index) in slides" :key="index">
            <div class="w-12 h-2 bg-white overflow-hidden rounded-full cursor-pointer" @click="goTo(index)">
                <div class="h-full bg-primary transition-width duration-200" :style="current === index ? `width: ${progress*100}%` : 'width:0'" ></div>
            </div>
        </template>
    </div>
</section>
