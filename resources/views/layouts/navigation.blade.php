<nav x-data="{ open: false }" class="sticky top-0 bg-white border-b border-grey-light z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- Left side -->
            <div class="flex flex-1 items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="/" class="inline-flex items-center pt-1">
                        <img src="{{ asset('images/nav_symbol.svg') }}" alt="BEKKAS" class="h-6 w-auto">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden h-full space-x-8 lg:-my-px lg:flex lg:flex-1 lg:justify-center">
                    @if(request()->is('admin/*') || request()->is('admin'))
                        {{-- ADMIN MENU --}}
                        
                        {{-- CONTENT with dropdown (no link) --}}
                        <div class="relative h-full flex items-center" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <x-nav-button :active="request()->is('admin/products*') || request()->is('admin/projects*')">
                                CONTENT
                            </x-nav-button>
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute left-0 top-full mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                 style="display: none;">
                                <div class="py-1">
                                    <a href="{{ route('admin.products.index') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">Products</a>
                                    <a href="{{ route('admin.projects.index') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">Projects</a>
                                    <a href="{{ route('admin.categories.index') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">Categories</a>
                                    <a href="{{ route('admin.materials.index') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">Materials</a>
                                </div>
                            </div>
                        </div>

                        {{-- ORDERS with dropdown --}}
                        <div class="relative h-full flex items-center" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <x-nav-button :active="request()->is('admin/orders*') || request()->is('admin/order-statuses*')" @click="window.location.href='{{ route('admin.orders.index') }}'">
                                ORDERS
                            </x-nav-button>
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute left-0 top-full mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                 style="display: none;">
                                <div class="py-1">
                                    <a href="{{ route('admin.orders.payloads.index') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">Payloads</a>
                                    <a href="{{ route('admin.orders.checkouts.index') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">Checkouts</a>
                                    <a href="{{ route('admin.orders.payments.index') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">Payments</a>
                                    <a href="{{ route('admin.order-statuses.index') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">Order Statuses</a>
                                    <a href="{{ route('admin.shipping-config.index') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">Shipping</a>
                                </div>
                            </div>
                        </div>

                        {{-- Tickets with dropdown --}}
                        <div class="relative h-full flex items-center" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <x-nav-button :active="request()->is('admin/tickets*') || request()->is('admin/ticket-categories*')" @click="window.location.href='{{ route('admin.tickets.index') }}'">
                                TICKETS
                            </x-nav-button>
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute left-0 top-full mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                 style="display: none;">
                                <div class="py-1">
                                    <a href="{{ route('admin.tickets.create') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">Create Ticket</a>
                                    <a href="{{ route('admin.ticket-categories.index') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">Categories</a>
                                </div>
                            </div>
                        </div>

                        {{-- Users with dropdown --}}
                        <div class="relative h-full flex items-center" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <x-nav-button :active="request()->is('admin/users*')" @click="window.location.href='{{ route('admin.users.index') }}'">
                                USERS
                            </x-nav-button>
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute left-0 top-full mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                 style="display: none;">
                                <div class="py-1">
                                    <a href="{{ route('admin.users.create') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">Create User</a>
                                </div>
                            </div>
                        </div>

                        {{-- Configuration with dropdown --}}
                        <div class="relative h-full flex items-center" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <x-nav-button :active="request()->is('admin/countries*') || request()->is('admin/regions*') || request()->is('admin/taxes*') || request()->is('admin/static-translations*') || request()->is('admin/shipping-tiers*') || request()->is('admin/shipping-config*') || request()->is('admin/configurations*') || request()->is('admin/categories*') || request()->is('admin/materials*')" @click="window.location.href='{{ route('admin.configurations.index') }}'">
                                CONFIGURATIONS
                            </x-nav-button>
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute left-0 top-full mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                 style="display: none;">
                                <div class="py-1">
                                    <a href="{{ route('admin.shipping-tiers.index') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">Shipping Tiers</a>
                                    <a href="{{ route('admin.countries.index') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">Countries</a>
                                    <a href="{{ route('admin.regions.index') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">Regions</a>
                                    <a href="{{ route('admin.taxes.index') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">TAXES</a>
                                    <a href="{{ route('admin.static-translations.index') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">Translations</a>
                                    <a href="{{ route('admin.locales.index') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">Site Locales</a>
                                </div>
                            </div>
                        </div>

                    @else
                        {{-- PUBLIC MENU --}}
                        
                        {{-- STORE --}}
                        @if(config('app.store_enabled'))
                            <div class="relative h-full flex items-center" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                                <x-nav-button :active="request()->routeIs('store.*')" @click="window.location.href='{{ route('store.index') }}'">
                                    {{ t('nav.store') ?: 'Store' }}
                                </x-nav-button>
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute left-0 top-full mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                     style="display: none;">
                                    <div class="py-1">
                                        <a href="{{ route('store.index') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">
                                            {{ t('nav.store.all_products') ?: 'All Products' }}
                                        </a>
                                        <a href="{{ route('store.index', ['is_featured' => 1]) }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">
                                            {{ t('nav.store.featured') ?: 'Featured' }}
                                        </a>
                                        <a href="{{ route('store.index', ['is_promo' => 1]) }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">
                                            {{ t('nav.store.promotion') ?: 'Promotion' }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Custom (with dropdown) --}}
                        <div class="relative h-full flex items-center" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <x-nav-button :active="request()->routeIs('custom.*') || request()->routeIs('portfolio.*')" @click="window.location.href='{{ route('custom.index') }}'">
                                {{ t('nav.custom') ?: 'Custom' }}
                            </x-nav-button>
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute left-0 top-full mt-2 w-52 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                 style="display: none;">
                                <div class="py-1">
                                    <a href="{{ route('custom.index') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">
                                        {{ t('nav.features') ?: 'Features' }}
                                    </a>
                                    <a href="{{ route('custom.index') }}#request" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">
                                        {{ t('nav.services_prices') ?: 'Services & Prices' }}
                                    </a>
                                    <a href="{{ route('portfolio.index') }}" class="block px-8 py-3 text-sm text-grey-dark hover:bg-grey-light">
                                        {{ t('nav.portfolio') ?: 'Portfolio' }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- About --}}
                        <div class="relative h-full flex items-center">
                            <x-nav-button :active="request()->routeIs('about')" @click="window.location.href='{{ route('about') }}'">
                                {{ t('nav.about') ?: 'About Us' }}
                            </x-nav-button>
                        </div>

                        {{-- Contact --}}
                        <div class="relative h-full flex items-center">
                            <button
                                type="button"
                                x-bind:class="$store.contactInView
                                    ? 'uppercase inline-flex items-center h-full px-1 pt-1 border-b-2 border-primary text-sm font-sans font-medium leading-5 text-dark focus:outline-none focus:border-primary transition duration-150 ease-in-out'
                                    : 'uppercase inline-flex items-center h-full px-1 pt-1 border-b-2 border-transparent text-sm font-sans font-medium leading-5 text-grey-medium hover:text-grey-dark hover:border-grey-medium focus:outline-none focus:text-grey-dark focus:border-grey-medium transition duration-150 ease-in-out'"
                                @click="window.location.href='{{ url('/#contact') }}'">
                                {{ t('nav.contact') ?: 'Contact' }}
                            </button>
                        </div>

                    @endif
                </div>
            </div>

            <!-- Right side -->
            @php $isAdminNavigation = request()->is('admin/*') || request()->is('admin'); @endphp
            <div class="hidden lg:flex lg:items-center lg:ms-6 gap-6">
                @unless($isAdminNavigation)
                    <!-- Language Selector -->
                    <div class="flex items-center gap-2">
                        @php
                            $currentLocale = app()->getLocale();
                            $otherLocale = $currentLocale === 'pt-PT' ? 'en-UK' : 'pt-PT';
                            $otherLocaleName = $otherLocale === 'pt-PT' ? 'PT' : 'EN';
                        @endphp
                        <a href="{{ route('language.switch', $otherLocale) }}"
                           class="text-sm text-grey-dark hover:text-dark font-medium hover:text-primary/90 no-underline">
                            {{ $otherLocaleName }}
                        </a>
                    </div>

                    <!-- Favorites Icon -->
                    <div x-data="{}" x-show="$store.favorites.count > 0" class="relative" style="display: none;">
                        <a href="{{ route('favorites.index') }}" class="flex items-center text-grey-dark hover:text-dark hover:text-primary/90 no-underline" aria-label="Favorites">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" stroke="none">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <span class="absolute -top-2 -right-2 inline-flex items-center justify-center rounded-full bg-status-error text-white text-xs px-1.5 py-0.5 min-w-[1.25rem]" x-text="$store.favorites.count"></span>
                        </a>
                    </div>

                    <!-- Cart Icon -->
                    <div x-data="{}" x-show="$store.cart.count > 0 && {{ config('app.store_enabled') ? 'true' : 'false' }}" class="relative" style="display: none;">
                        <a href="{{ route('cart.index') }}" class="flex items-center text-grey-dark hover:text-dark hover:text-primary/90 no-underline" aria-label="Cart">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25h11.118c.51 0 .955-.343 1.087-.835l1.518-5.688a1.125 1.125 0 00-1.087-1.415H5.106" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25L5.106 4.272M7.5 14.25l-2.25 3m0 0h13.5m-13.5 0a1.5 1.5 0 103 0m10.5 0a1.5 1.5 0 103 0" />
                            </svg>
                            <span class="absolute -top-2 -right-2 inline-flex items-center justify-center rounded-full bg-status-error text-white text-xs px-1.5 py-0.5 min-w-[1.25rem]" x-text="$store.cart.count"></span>
                        </a>
                    </div>
                @endunless

                @auth
                    <div class="h-full flex items-center">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="h-full inline-flex items-center px-8 py-3 border border-transparent text-sm leading-4 font-medium text-grey-medium bg-white hover:text-grey-dark focus:outline-none transition">
                                    <div>{{ Auth::user()->name }}</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                        <x-slot name="content">
                            @if(Auth::user()->isAdmin())
                                <x-dropdown-link :href="route('admin.dashboard')">
                                    {{ t('nav.admin') ?: 'Admin' }}
                                </x-dropdown-link>
                            @endif

                            <x-dropdown-link :href="route('profile.edit')">
                                {{ t('nav.profile') ?: 'Profile' }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('tickets.index')">
                                {{ t('nav.my_tickets') ?: 'My Tickets' }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('orders.index')">
                                {{ t('nav.orders') ?: 'Orders' }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                    <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ t('nav.logout') ?: 'Log Out' }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                    </div>
                @else
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}" class="text-sm text-grey-dark hover:text-dark hover:text-primary/90 no-underline">
                            {{ t('nav.login') ?: 'Login' }}
                        </a>
                        <a href="{{ route('register') }}" class="text-sm text-grey-dark hover:text-dark hover:text-primary/90 no-underline">
                            {{ t('nav.register') ?: 'Register' }}
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center lg:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-full text-grey-medium hover:text-grey-medium hover:bg-grey-light focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': ! open }"
                              class="inline-flex"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': ! open, 'inline-flex': open }"
                              class="hidden"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': ! open }" class="hidden lg:hidden">
            <div class="pt-2 pb-3 space-y-1">

            @php
                $toggleBase = 'uppercase w-full flex items-center justify-between ps-3 pe-4 py-2 text-base font-medium focus:outline-none transition duration-150 ease-in-out';
                $toggleActive = 'border-l-4 border-primary text-primary bg-primary/10 focus:border-primary';
                $toggleInactive = 'border-l-4 border-transparent text-grey-dark hover:text-grey-dark hover:bg-white hover:border-grey-medium focus:text-grey-dark focus:bg-white focus:border-grey-medium';
            @endphp
            
            @if(request()->is('admin/*') || request()->is('admin'))
                {{-- ADMIN MENU MOBILE --}}

                {{-- CONTENT --}}
                <div x-data="{ open: {{ (request()->is('admin/products*') || request()->is('admin/projects*') || request()->is('admin/categories*') || request()->is('admin/materials*')) ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="{{ $toggleBase }} {{ (request()->is('admin/products*') || request()->is('admin/projects*') || request()->is('admin/categories*') || request()->is('admin/materials*')) ? $toggleActive : $toggleInactive }}">
                        <span>Content</span>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="pl-4 space-y-1">
                        <x-responsive-nav-link :href="route('admin.products.index')" :active="request()->is('admin/products*')">
                            Products
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.projects.index')" :active="request()->is('admin/projects*')">
                            Projects
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.categories.index')" :active="request()->is('admin/categories*')">
                            Categories
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.materials.index')" :active="request()->is('admin/materials*')">
                            Materials
                        </x-responsive-nav-link>
                    </div>
                </div>

                {{-- ORDERS --}}
                <div x-data="{ open: {{ (request()->is('admin/orders*') || request()->is('admin/order-statuses*') || request()->is('admin/shipping-config*')) ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="{{ $toggleBase }} {{ (request()->is('admin/orders*') || request()->is('admin/order-statuses*') || request()->is('admin/shipping-config*')) ? $toggleActive : $toggleInactive }}">
                        <span>Orders</span>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="pl-4 space-y-1">
                        <x-responsive-nav-link :href="route('admin.orders.index')" :active="request()->is('admin/orders') || request()->is('admin/orders?*')">
                            All Orders
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.orders.payloads.index')" :active="request()->is('admin/orders/payloads*')">
                            Payloads
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.orders.checkouts.index')" :active="request()->is('admin/orders/checkouts*')">
                            Checkouts
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.orders.payments.index')" :active="request()->is('admin/orders/payments*')">
                            Payments
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.order-statuses.index')" :active="request()->is('admin/order-statuses*')">
                            Order Statuses
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.shipping-config.index')" :active="request()->is('admin/shipping-config*')">
                            Shipping
                        </x-responsive-nav-link>
                    </div>
                </div>

                {{-- TICKETS --}}
                <div x-data="{ open: {{ (request()->is('admin/tickets*') || request()->is('admin/ticket-categories*')) ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="{{ $toggleBase }} {{ (request()->is('admin/tickets*') || request()->is('admin/ticket-categories*')) ? $toggleActive : $toggleInactive }}">
                        <span>Tickets</span>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="pl-4 space-y-1">
                        <x-responsive-nav-link :href="route('admin.tickets.index')" :active="request()->is('admin/tickets*')">
                            All Tickets
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.tickets.create')" :active="false">
                            Create Ticket
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.ticket-categories.index')" :active="request()->is('admin/ticket-categories*')">
                            Categories
                        </x-responsive-nav-link>
                    </div>
                </div>

                {{-- Configuration --}}
                <div x-data="{ open: {{ (request()->is('admin/configurations*') || request()->is('admin/shipping-tiers*') || request()->is('admin/countries*') || request()->is('admin/regions*') || request()->is('admin/taxes*') || request()->is('admin/static-translations*') || request()->is('admin/locales*')) ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="{{ $toggleBase }} {{ (request()->is('admin/configurations*') || request()->is('admin/shipping-tiers*') || request()->is('admin/countries*') || request()->is('admin/regions*') || request()->is('admin/taxes*') || request()->is('admin/static-translations*') || request()->is('admin/locales*')) ? $toggleActive : $toggleInactive }}">
                        <span>Configurations</span>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="pl-4 space-y-1">
                        <x-responsive-nav-link :href="route('admin.configurations.index')" :active="request()->is('admin/configurations*')">
                            Base Configurations
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.shipping-tiers.index')" :active="request()->is('admin/shipping-tiers*')">
                            Shipping Tiers
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.countries.index')" :active="request()->is('admin/countries*')">
                            Countries
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.regions.index')" :active="request()->is('admin/regions*')">
                            Regions
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.taxes.index')" :active="request()->is('admin/taxes*')">
                            TAX's
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.static-translations.index')" :active="request()->is('admin/static-translations*')">
                            Translations
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.locales.index')" :active="request()->is('admin/locales*')">
                            Site Locales
                        </x-responsive-nav-link>
                    </div>
                </div>
                
            @else
                {{-- PUBLIC MENU MOBILE --}}
                @if(config('app.store_enabled'))
                    <div x-data="{ open: {{ request()->routeIs('store.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="{{ $toggleBase }} {{ request()->routeIs('store.*') ? $toggleActive : $toggleInactive }}">
                            <span>{{ t('nav.store') ?: 'Store' }}</span>
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" class="pl-4 space-y-1">
                            <x-responsive-nav-link :href="route('store.index')" :active="request()->routeIs('store.index') && !request()->filled('is_featured') && !request()->filled('is_promo')">
                                {{ t('nav.store.all_products') ?: 'All Products' }}
                            </x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('store.index', ['is_featured' => 1])" :active="request()->routeIs('store.index') && request()->filled('is_featured')">
                                {{ t('nav.store.featured') ?: 'Featured' }}
                            </x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('store.index', ['is_promo' => 1])" :active="request()->routeIs('store.index') && request()->filled('is_promo')">
                                {{ t('nav.store.promotion') ?: 'Promotion' }}
                            </x-responsive-nav-link>
                        </div>
                    </div>
                @endif
                
                <div x-data="{ open: {{ (request()->routeIs('custom.*') || request()->routeIs('portfolio.*')) ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="{{ $toggleBase }} {{ (request()->routeIs('custom.*') || request()->routeIs('portfolio.*')) ? $toggleActive : $toggleInactive }}">
                        <span>{{ t('nav.custom') ?: 'Custom' }}</span>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="pl-4 space-y-1">
                        <x-responsive-nav-link :href="route('custom.index')" :active="request()->routeIs('custom.*') && !request()->routeIs('portfolio.*')">
                            {{ t('nav.features') ?: 'Features' }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('custom.index') . '#requests'" :active="false">
                            {{ t('nav.services_prices') ?: 'Services & Prices' }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('portfolio.index')" :active="request()->routeIs('portfolio.*')">
                            {{ t('nav.portfolio') ?: 'Portfolio' }}
                        </x-responsive-nav-link>
                    </div>
                </div>
                <a href="{{ route('about') }}"
                   class="{{ $toggleBase }} {{ request()->routeIs('about') ? $toggleActive : $toggleInactive }}">
                    {{ t('nav.about') ?: 'About Us' }}
                </a>
                <a href="{{ url('/#contact') }}"
                   class="{{ $toggleBase }} inline-flex items-center h-full leading-5"
                   :class="$store.contactInView ? '{{ $toggleActive }}' : '{{ $toggleInactive }}'">
                    {{ t('nav.contact') ?: 'Contact' }}
                </a>
                
                <div x-data="{}" x-show="$store.favorites.count > 0" style="display: none;">
                    <x-responsive-nav-link :href="route('favorites.index')" :active="request()->routeIs('favorites.*')">
                        {{ t('nav.favorites') ?: 'Favorites' }} (<span x-text="$store.favorites.count"></span>)
                    </x-responsive-nav-link>
                </div>
                
                @php $cartCount = array_sum(array_column(session('cart', []), 'quantity')); @endphp
                <div x-data="{}" x-show="$store.cart.count > 0 && {{ config('app.store_enabled') ? 'true' : 'false' }}">
                    <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">
                        {{ t('nav.cart') ?: 'Cart' }} (<span x-text="$store.cart.count"></span>)
                    </x-responsive-nav-link>
                </div>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-grey-light">
            {{-- Language Selector Mobile --}}
            <div class="px-4 py-2">
                @php
                    $currentLocale = app()->getLocale();
                    $otherLocale = $currentLocale === 'pt-PT' ? 'en-UK' : 'pt-PT';
                    $otherLocaleName = $otherLocale === 'pt-PT' ? 'PT' : 'EN';
                @endphp
                <a href="{{ route('language.switch', $otherLocale) }}"
                   class="text-sm text-grey-dark hover:text-dark font-medium hover:text-primary/90 no-underline">
                    {{ $otherLocaleName }}
                </a>
            </div>

            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-grey-dark">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-grey-medium">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    @if(Auth::user()->isAdmin())
                        <x-responsive-nav-link :href="route('admin.dashboard')">
                            {{ t('nav.admin') ?: 'Admin' }}
                        </x-responsive-nav-link>
                    @endif

                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ t('nav.profile') ?: 'Profile' }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.*')">
                        {{ t('nav.my_tickets') ?: 'My Tickets' }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                        {{ t('nav.orders') ?: 'Orders' }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ t('nav.logout') ?: 'Log Out' }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="mt-3 space-y-1 px-4">
                    <x-responsive-nav-link :href="route('login')">
                        {{ t('nav.login') ?: 'Login' }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')">
                        {{ t('nav.register') ?: 'Register' }}
                    </x-responsive-nav-link>
                </div>
            @endauth
        </div>
    </div>
</nav>
