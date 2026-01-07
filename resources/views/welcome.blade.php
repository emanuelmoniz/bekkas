<!DOCTYPE html>
<html lang="$(app()->getLocale())"">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>BEKKAS - 3D Printing Services</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <x-favorites-init />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <!-- Google reCAPTCHA -->
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </head>
    <body class="bg-white dark:bg-[#161615] text-gray-900 dark:text-gray-100">
        @include('layouts.navigation')

        <!-- BANNER SECTION -->
        <section class="relative w-full h-screen flex items-center justify-center overflow-hidden bg-gray-900">
            <!-- Background Image -->
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1578119289844-26cbf8b9a17f?w=1200&h=800&fit=crop')">
                <div class="absolute inset-0 bg-black/40"></div>
            </div>
            
            <!-- Content -->
            <div class="relative z-10 text-center text-white px-6">
                <h1 class="text-5xl md:text-7xl font-bold mb-6">{{ t('home.banner.tagline') ?: 'Printing Life layer by layer' }}</h1>
                <a href="#services" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded font-semibold transition-colors">
                    {{ t('home.banner.button') ?: 'OUR SERVICES' }}
                </a>
            </div>
        </section>

        <!-- SERVICES SECTION -->
        <section id="services" class="py-16 md:py-24 bg-white dark:bg-[#161615] px-6">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">
                    
                    <!-- Products Card -->
                    <a href="{{ route('products.index') }}" class="group">
                        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow cursor-pointer h-full flex flex-col">
                            <div class="w-full h-64 md:h-80 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1569163139394-de4798aa62b1?w=500&h=400&fit=crop')"></div>
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="text-2xl font-bold mb-3 text-gray-900 dark:text-white">{{ t('home.services.products.title') ?: 'PRODUCTS' }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-6 flex-grow">{{ t('home.services.products.description') ?: 'Day to day life objects, gifts, souvenires' }}</p>
                                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded font-medium transition-colors">
                                    {{ t('home.services.products.button') ?: 'Store' }}
                                </button>
                            </div>
                        </div>
                    </a>

                    <!-- Architecture Card -->
                    <a href="{{ route('tickets.index') }}" class="group">
                        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow cursor-pointer h-full flex flex-col">
                            <div class="w-full h-64 md:h-80 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1552664730-d307ca884978?w=500&h=400&fit=crop')"></div>
                            <div class="p-6 flex flex-col flex-grow">
                                <h3 class="text-2xl font-bold mb-3 text-gray-900 dark:text-white">{{ t('home.services.architecture.title') ?: 'ARCHITECTURE' }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-6 flex-grow">{{ t('home.services.architecture.description') ?: 'Printing service for architects and architecture students including modeling and file preparation.' }}</p>
                                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded font-medium transition-colors">
                                    {{ t('home.services.architecture.button') ?: 'More info' }}
                                </button>
                            </div>
                        </div>
                    </a>

                </div>
            </div>
        </section>

        <!-- CONTACT SECTION -->
        <section id="contact" class="py-16 md:py-24 bg-gray-50 dark:bg-gray-900 px-6">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-4xl font-bold mb-12 text-center text-gray-900 dark:text-white">{{ t('home.contact.title') ?: 'Get in Touch' }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    
                    <!-- Contact Info -->
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ t('home.contact.location') ?: 'Location' }}</h3>
                            <p class="text-gray-600 dark:text-gray-400">Lisbon, Portugal</p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ t('home.contact.phone') ?: 'Phone' }}</h3>
                            <a href="https://wa.me/351965707800" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                +351 965 707 800 (WhatsApp)
                            </a>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ t('home.contact.email') ?: 'Email' }}</h3>
                            <a href="mailto:info@bekkas.pt" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                info@bekkas.pt
                            </a>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ t('home.contact.social') ?: 'Follow Us' }}</h3>
                            <div class="flex gap-4">
                                <a href="https://instagram.com/bekkas_pt" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                    Instagram
                                </a>
                                <a href="https://www.makerworld.com/en/makers/bekkas" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                    Makerworld
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
                        @if(session('success'))
                            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('contact.store') }}" class="space-y-6">
                            @csrf

                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ t('contact.name') ?: 'Name' }}
                                </label>
                                <input type="text" id="name" name="name" required
                                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ t('contact.email') ?: 'Email' }}
                                </label>
                                <input type="email" id="email" name="email" required
                                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ t('contact.message') ?: 'Message' }}
                                </label>
                                <textarea id="message" name="message" rows="5" required
                                          class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"></textarea>
                            </div>

                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                            @error('g-recaptcha-response')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror

                            <button type="submit" 
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                                {{ t('contact.send') ?: 'Send Message' }}
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </section>

        @include('layouts.footer')
    </body>
</html>
