@section('title', config('app.name', 'BEKKAS') . ' - Architecture Services')

<x-app-layout>

        <!-- BANNER SECTION
        <section class="relative w-full h-screen flex items-center justify-center overflow-hidden bg-dark">

            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1552664730-d307ca884978?w=1200&h=800&fit=crop')">
                <div class="absolute inset-0 bg-dark/40"></div>
            </div>
            

            <div class="relative z-10 text-center text-white px-6">
                <h1 class="text-5xl lg:text-7xl font-bold mb-6">{{ t('custom.banner.title') ?: 'Custom Services' }}</h1>
                <p class="text-xl lg:text-2xl mb-8 max-w-2xl mx-auto">{{ t('custom.banner.subtitle') ?: 'Professional 3D printing solutions for architects and designers' }}</p>
                <x-primary-cta as="a" href="#request">
                    {{ t('custom.banner.button') ?: 'Request Service' }}
                </x-primary-cta>
            </div>
        </section> -->

        <!-- FEATURES SECTION -->
        <section class="py-16 lg:py-24 bg-light">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <x-feature-card
                        :image="asset('images/slide1.jpg')"
                        :title="t('custom.features.modeling') ?: '3D Modeling'"
                        :description="t('custom.features.modeling_desc') ?: 'Professional 3D model shaping, preparation and optimization for printing.'"
                        :bullets="array_filter([
                            t('custom.features.modeling_b1') ?: 'FreeCad | Fusion | Revit | Archicad',
                            t('custom.features.modeling_b2') ?: 'Blender | Archicad (soon)',
                            t('custom.features.modeling_b3') ?: 'Check with us other software',
                        ])"
                    />
                    <x-feature-card
                        :image="asset('images/slide2.jpg')"
                        :title="t('custom.features.materials') ?: 'Multiple Materials'"
                        :description="t('custom.features.materials_desc') ?: 'Various materials and finishes to suit your project needs.'"
                        :bullets="array_filter([
                            t('custom.features.materials_b1') ?: 'PLA | PETG | TPU',
                            t('custom.features.materials_b2') ?: 'Translucent Options',
                            t('custom.features.materials_b3') ?: 'Contact us for others',
                        ])"
                    />
                    <x-feature-card
                        :image="asset('images/slide3.jpg')"
                        :title="t('custom.features.support') ?: 'Expert Support'"
                        :description="t('custom.features.support_desc') ?: 'Dedicated support from design consultation to final delivery.'"
                        :bullets="array_filter([
                            t('custom.features.support_b1') ?: 'Architecture Background',
                            t('custom.features.support_b2') ?: 'IT Background',
                            t('custom.features.support_b3') ?: 'Flexible and Personalized',
                        ])"
                    />
                </div>
            </div>
        </section>

        <!-- REQUEST SECTION -->
        <section id="request" class="py-16 lg:py-24 bg-secondary">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <h2 class="uppercase text-4xl font-bold mb-4 text-center text-dark">
                    {{ t('custom.request.title') ?: 'Request a Custom Service' }}
                </h2>
                <p class="text-center text-lg text-grey-dark mb-2 max-w-2xl mx-auto">
                    {{ t('custom.request.subtitle1') ?: 'Choose the service that best matches your situation.' }}
                </p>
                <p class="text-center text-lg text-grey-dark mb-2 max-w-2xl mx-auto">
                    {{ t('custom.request.subtitle2') ?: 'Read carefully the features and informations of each option.' }}
                </p>
                <p class="text-center text-lg text-grey-dark mb-12 max-w-2xl mx-auto">
                    {{ t('custom.request.subtitle3') ?: 'Each option opens a ticket with the right category already selected.' }}
                </p>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    {{-- Card 1: R&D --}}
                    <x-request-card
                        :href="route('tickets.create', ['category' => 'rnd'])"
                        :tier="t('custom.request.rnd_tier') ?: 'R&D + Preparation + Print'"
                        :title="t('custom.request.rnd_title') ?: 'I have an idea'"
                        :description="t('custom.request.rnd_desc') ?: 'You have a concept but no file. We model, prepare and print everything from scratch according to your specifications.'"
                        :bullets="array_filter([
                            t('custom.request.rnd_bullet1') ?: 'Full product development',
                            t('custom.request.rnd_bullet2') ?: 'Quote includes R&D, prep & print',
                            t('custom.request.rnd_bullet3') ?: 'Price based on complexity + print time',
                        ])"
                        :attach="t('custom.request.rnd_attach') ?: 'Attach: photos, sketches, reference images, any existing files.'"
                        :button="t('custom.request.cta_button') ?: 'Request this service'"
                    />

                    {{-- Card 2: Preparation --}}
                    <x-request-card
                        :href="route('tickets.create', ['category' => 'preparation'])"
                        :tier="t('custom.request.prep_tier') ?: 'Preparation + Print'"
                        :title="t('custom.request.prep_title') ?: 'I have a 3D model'"
                        :description="t('custom.request.prep_desc') ?: 'You have a 3D file (e.g. an architecture or design project) but it needs to be optimised and prepared before printing.'"
                        :bullets="array_filter([
                            t('custom.request.prep_bullet1') ?: 'File simplification & repair',
                            t('custom.request.prep_bullet2') ?: 'Preparation: €30/hr (15-min billing)',
                            t('custom.request.prep_bullet3') ?: 'Print: €20/hr · 20% student discount',
                        ])"
                        :attach="t('custom.request.prep_attach') ?: 'Attach: 3D file(s) + scale, colour, material specs.'"
                        :button="t('custom.request.cta_button') ?: 'Request this service'"
                    />

                    {{-- Card 3: Print --}}
                    <x-request-card
                        :href="route('tickets.create', ['category' => 'print'])"
                        :tier="t('custom.request.print_tier') ?: 'Print only'"
                        :title="t('custom.request.print_title') ?: 'I have a print-ready file'"
                        :description="t('custom.request.print_desc') ?: 'Your file is ready to print. Send it over and we will quote and print it. Also ideal for models found online.'"
                        :bullets="array_filter([
                            t('custom.request.print_bullet1') ?: '€20/hr · billed in 15-min sets',
                            t('custom.request.print_bullet2') ?: 'PLA included · other materials on request',
                            t('custom.request.print_bullet3') ?: '20% student discount',
                        ])"
                        :attach="t('custom.request.print_attach') ?: 'Attach: print-ready file(s) + scale, colour, material, layer height, infill.'"
                        :button="t('custom.request.cta_button') ?: 'Request this service'"
                    />

                </div>
            </div>
        </section>

        <!-- PROJECTS SECTION -->
        <section class="py-16 lg:py-24 bg-light">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <h2 class="uppercase text-4xl font-bold mb-12 text-center text-dark">
                    {{ t('custom.projects.title') ?: 'Our past Projects' }}
                </h2>

                <x-project-slider
                    :isFeatured="true"
                    :max="8"
                    order="newest"
                />

                <div class="mt-10 flex justify-center">
                    <a href="{{ route('portfolio.index') }}"
                       class="inline-flex items-center gap-2 border-2 border-dark text-dark hover:bg-dark hover:text-white px-8 py-3 rounded-full uppercase font-semibold text-sm transition-colors duration-200">
                        {{ t('custom.projects.portfolio_button') ?: 'Check our full portfolio' }}
                    </a>
                </div>

            </div>
        </section>

    </x-app-layout>
