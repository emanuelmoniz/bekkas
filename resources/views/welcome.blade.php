@section('title', config('app.name', 'BEKKAS'))

@push('head')
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/home-splash.css'])
    @endif
@endpush

@push('recaptcha')
    @include('partials.recaptcha-loader')
@endpush

<x-app-layout>
    <div id="home-splash" class="home-splash-overlay" role="dialog" aria-label="{{ config('app.name', 'BEKKAS') }} splash">
        <img src="{{ asset('images/hero_logo.svg') }}" alt="{{ config('app.name', 'BEKKAS') }}" class="home-splash-logo" />
    </div>
        @php $storeEnabled = config('app.store_enabled'); @endphp
        <!-- BANNER SECTION -->
        @php
            // define slides for the homepage carousel; text/button reuse existing translation keys
            $homeTagline1 = t('home.banner.tagline1') ?: 'Printing Life layer by layer';
            $homeSubTagline1 = t('home.banner.subtagline1') ?: 'Printing Life layer by layer';
            $homeButton1  = t('home.banner.button1')  ?: 'OUR SERVICES';
            $homeButtonURL1 = '#services';
            $homeButtonEnabled1 = true;
            $homeTagline2 = t('home.banner.tagline2') ?: 'Printing Life layer by layer';
            $homeSubTagline2 = t('home.banner.subtagline2') ?: 'Printing Life layer by layer';
            if ($storeEnabled) {
                $homeButton2  = t('home.banner.button2')  ?: 'STORE';
                $homeButtonURL2 = route('store.index');
                $homeButtonEnabled2 = true;
            } else {
                $homeButton2  = t('home.services.store.soon') ?: 'SOON';
                $homeButtonURL2 = '#';
                $homeButtonEnabled2 = false;
            }
            $homeTagline3 = t('home.banner.tagline3') ?: 'Printing Life layer by layer';
            $homeSubTagline3 = t('home.banner.subtagline3') ?: 'Printing Life layer by layer';
            $homeButton3  = t('home.banner.button3')  ?: 'CUSTOM';
            $homeButtonURL3 = route('custom.index');
            $homeButtonEnabled3 = true;
            $slides = [
                [
                    // local placeholders copied during build
                    'image' => asset('images/slide1.jpg'),
                    'tagline' => $homeTagline1,
                    'subtagline' => $homeSubTagline1,
                    'buttonText' => $homeButton1,
                    'buttonEnabled' => $homeButtonEnabled1,
                    'buttonUrl' => $homeButtonURL1,
                ],
                [
                    'image' => asset('images/slide2.jpg'),
                    'tagline' => $homeTagline2,
                    'subtagline' => $homeSubTagline2,
                    'buttonText' => $homeButton2,
                    'buttonEnabled' => $homeButtonEnabled2,
                    'buttonUrl' => $homeButtonURL2,
                ],
                [
                    'image' => asset('images/slide3.jpg'),
                    'tagline' => $homeTagline3,
                    'subtagline' => $homeSubTagline3,
                    'buttonText' => $homeButton3,
                    'buttonEnabled' => $homeButtonEnabled3,
                    'buttonUrl' => $homeButtonURL3,
                ],
            ];
        @endphp

        <x-home-banner :slides="$slides" />

        <!-- SERVICES SECTION -->
        <section id="services" class="py-16 lg:py-24 bg-light animate-sequence">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
                    
                    <!-- Products Card -->

                    @if($storeEnabled)
                        <a href="{{ route('store.index') }}" class="group anim-item text-accent-primary hover:text-accent-primary/90 no-underline" data-index="0">
                    @else
                        <div class="group anim-item text-accent-primary no-underline cursor-not-allowed opacity-80" data-index="0" aria-disabled="true">
                    @endif
                            <div class="bg-white rounded-lg overflow-hidden shadow-lg transition-shadow h-full flex flex-col {{ $storeEnabled ? 'hover:shadow-xl cursor-pointer' : '' }}">
                                {{-- scroll images for store products --}}
                                <x-image-scroller class="w-full aspect-square" :config="[
                                    'interval' => 1500,
                                    'products' => [
                                        'featured' => true,
                                        'active' => true,
                                        'per_item' => 1,
                                    ],
                                ]" />
                                <div class="p-6 py-8 flex flex-col flex-grow items-center text-center">
                                    <h3 class="uppercase text-2xl font-bold mb-4 text-dark">{{ t('home.services.store.title') ?: 'STORE' }}</h3>
                                    <p class="text-grey-dark mb-6 flex-grow">{{ t('home.services.store.description') ?: 'Day to day life objects, gifts, souvenires' }}</p>
                                    @if($storeEnabled)
                                        <x-primary-cta>
                                            {{ t('home.services.store.button') ?: 'Store' }}
                                        </x-primary-cta>
                                    @else
                                        <x-optional-cta as="button" disabled>
                                            {{ t('home.services.store.soon') ?: 'Brevemente' }}
                                        </x-optional-cta>
                                    @endif
                                </div>
                            </div>
                    @if($storeEnabled)
                        </a>
                    @else
                        </div>
                    @endif

                    <!-- Custom Card -->
                    <a href="{{ route('custom.index') }}" class="group anim-item text-accent-primary hover:text-accent-primary/90 no-underline" data-index="1">
                        <div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow cursor-pointer h-full flex flex-col">
                            {{-- scroll images for custom projects --}}
                            <x-image-scroller class="w-full aspect-square" :config="[
                                'interval' => 3000,
                                'max' => null,
                                'projects' => [
                                    'active' => true,
                                    'featured' => true,
                                    'per_item' => 1,
                                ],
                            ]" />
                            <div class="p-6 py-8 flex flex-col flex-grow items-center text-center">
                                <h3 class="uppercase text-2xl font-bold mb-4 text-dark">{{ t('home.services.custom.title') ?: 'CUSTOM' }}</h3>
                                <p class="text-grey-dark mb-6 flex-grow">{{ t('home.services.custom.description') ?: 'Printing service for architects and architecture students including modeling and file preparation.' }}</p>
                                <x-primary-cta>
                                    {{ t('home.services.custom.button') ?: 'More info' }}
                                </x-primary-cta>
                            </div>
                        </div>
                    </a>

                </div>
            </div>
        </section>

        <!-- CONTACT SECTION -->
        <section id="contact" class="py-16 lg:py-24 bg-secondary">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 animate-sequence">
                    
                    <!-- Contact Info -->
                    <div class="space-y-8 anim-item" data-index="0">

                        <h2 class="text-4xl font-bold mb-12 uppercase text-dark">{{ t('home.contact.title') ?: 'Get in Touch' }}</h2>

                        <div>
                            <h3 class="text-lg font-semibold text-dark mb-2">{{ t('home.contact.location') ?: 'Location' }}</h3>
                            <p class="text-grey-dark">Lisbon, Portugal</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-dark mb-2">{{ t('home.contact.phone') ?: 'Phone' }}</h3>
                            <a href="https://wa.me/351922015060" target="_blank" rel="noopener noreferrer" class="text-accent-primary hover:text-accent-primary font-medium hover:text-accent-primary/90 no-underline">
                                +351 922 015 060 (WhatsApp)
                            </a>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-dark mb-2">{{ t('home.contact.email') ?: 'Email' }}</h3>
                            <a href="mailto:{{ config('mail.contact_address', config('mail.admin_address', 'info@bekkas.pt')) }}" class="text-accent-primary hover:text-accent-primary font-medium hover:text-accent-primary/90 no-underline">
                                {{ config('mail.contact_address', config('mail.admin_address', 'info@bekkas.pt')) }}
                            </a>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-dark mb-2">{{ t('home.contact.social') ?: 'Follow Us' }}</h3>
                            <div class="mb-2">
                                <a href="https://instagram.com/bekkas_pt" target="_blank" rel="noopener noreferrer" class="text-accent-primary hover:text-accent-primary font-medium hover:text-accent-primary/90 no-underline">
                                    Instagram
                                </a>
                            </div>
                            <div>
                                <a href="https://makerworld.com/en/@AZSeashell" target="_blank" rel="noopener noreferrer" class="text-accent-primary hover:text-accent-primary font-medium hover:text-accent-primary/90 no-underline">
                                    Makerworld
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="bg-white rounded-lg shadow-lg p-8 anim-item" data-index="1">
                        @include('partials.contact-form')
                    </div>

                </div>
            </div>
        </section>

    @push('scripts')
        @vite('resources/js/contact-tracking.js')
    @endpush
</x-app-layout>
