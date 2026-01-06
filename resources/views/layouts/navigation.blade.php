<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- Left side -->
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="/">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if(request()->is('admin/*') || request()->is('admin'))
                        {{-- ADMIN MENU --}}
                        
                        {{-- Products with dropdown --}}
                        <div class="relative h-full flex items-center" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button class="inline-flex items-center h-full px-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out
                                    {{ request()->is('admin/products*') || request()->is('admin/categories*') || request()->is('admin/materials*') ? 'border-indigo-400 text-gray-900 focus:border-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300' }}">
                                Products
                            </button>
                            <div x-show="open" x-cloak class="absolute left-0 top-full mt-0 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                                <a href="{{ route('admin.products.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">All Products</a>
                                <a href="{{ route('admin.products.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Create Product</a>
                                <a href="{{ route('admin.categories.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Categories</a>
                                <a href="{{ route('admin.materials.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Materials</a>
                            </div>
                        </div>

                        {{-- Tickets with dropdown --}}
                        <div class="relative h-full flex items-center" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button class="inline-flex items-center h-full px-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out
                                    {{ request()->is('admin/tickets*') || request()->is('admin/ticket-categories*') ? 'border-indigo-400 text-gray-900 focus:border-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300' }}">
                                Tickets
                            </button>
                            <div x-show="open" x-cloak class="absolute left-0 top-full mt-0 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                                <a href="{{ route('admin.tickets.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">All Tickets</a>
                                <a href="{{ route('admin.tickets.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Create Ticket</a>
                                <a href="{{ route('admin.ticket-categories.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Categories</a>
                            </div>
                        </div>

                        {{-- Orders with dropdown --}}
                        <div class="relative h-full flex items-center" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button class="inline-flex items-center h-full px-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out
                                    {{ request()->is('admin/orders*') || request()->is('admin/shipping-tiers*') ? 'border-indigo-400 text-gray-900 focus:border-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300' }}">
                                Orders
                            </button>
                            <div x-show="open" x-cloak class="absolute left-0 top-full mt-0 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                                <a href="{{ route('admin.orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">All Orders</a>
                                <a href="{{ route('admin.shipping-tiers.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Shipping Tiers</a>
                            </div>
                        </div>

                        {{-- Translations with dropdown --}}
                        <div class="relative h-full flex items-center" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button class="inline-flex items-center h-full px-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out
                                    {{ request()->is('admin/static-translations*') ? 'border-indigo-400 text-gray-900 focus:border-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300' }}">
                                Translations
                            </button>
                            <div x-show="open" x-cloak class="absolute left-0 top-full mt-0 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                                <a href="{{ route('admin.static-translations.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">All Translations</a>
                                <a href="{{ route('admin.static-translations.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Create Translation</a>
                            </div>
                        </div>

                    @else
                        {{-- PUBLIC MENU --}}
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                            {{ t('nav.products') ?: 'Products' }}
                        </x-nav-link>

                        <x-nav-link :href="route('architecture.index')" :active="request()->routeIs('architecture.*')">
                            {{ t('nav.architecture') ?: 'Architecture' }}
                        </x-nav-link>

                        <x-nav-link :href="route('about')" :active="request()->routeIs('about')">
                            {{ t('nav.about') ?: 'About Us' }}
                        </x-nav-link>

                        <x-nav-link :href="'https://bekkas.pt#contact'" :active="false">
                            {{ t('nav.contact') ?: 'Contact' }}
                        </x-nav-link>

                        <x-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">
                            {{ t('nav.cart') ?: 'Cart' }} ({{ count(session('cart', [])) }})
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Right side -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-6">
                <!-- Language Selector -->
                <div class="flex items-center gap-2">
                    @php
                        $currentLocale = app()->getLocale();
                        $otherLocale = $currentLocale === 'pt-PT' ? 'en-UK' : 'pt-PT';
                        $otherLocaleName = $otherLocale === 'pt-PT' ? 'PT' : 'EN';
                    @endphp
                    <a href="{{ route('language.switch', $otherLocale) }}"
                       class="text-sm text-gray-600 hover:text-gray-900 font-medium">
                        {{ $otherLocaleName }}
                    </a>
                </div>

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
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

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                    <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ t('nav.logout') ?: 'Log Out' }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ t('nav.login') ?: 'Login' }}
                        </a>
                        <a href="{{ route('register') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ t('nav.register') ?: 'Register' }}
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
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
    <div :class="{ 'block': open, 'hidden': ! open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(request()->is('admin/*') || request()->is('admin'))
                {{-- ADMIN MENU MOBILE --}}
                <x-responsive-nav-link :href="route('admin.products.index')" :active="request()->is('admin/products*')">
                    Products
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.tickets.index')" :active="request()->is('admin/tickets*')">
                    Tickets
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.orders.index')" :active="request()->is('admin/orders*')">
                    Orders
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.static-translations.index')" :active="request()->is('admin/static-translations*')">
                    Translations
                </x-responsive-nav-link>
            @else
                {{-- PUBLIC MENU MOBILE --}}
                <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                    {{ t('nav.products') ?: 'Products' }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.*')">
                    {{ t('nav.architecture') ?: 'Architecture' }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('about')" :active="request()->routeIs('about')">
                    {{ t('nav.about') ?: 'About Us' }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="'https://bekkas.pt#contact'" :active="false">
                    {{ t('nav.contact') ?: 'Contact' }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">
                    {{ t('nav.cart') ?: 'Cart' }} ({{ count(session('cart', [])) }})
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            {{-- Language Selector Mobile --}}
            <div class="px-4 py-2">
                @php
                    $currentLocale = app()->getLocale();
                    $otherLocale = $currentLocale === 'pt-PT' ? 'en-UK' : 'pt-PT';
                    $otherLocaleName = $otherLocale === 'pt-PT' ? 'PT' : 'EN';
                @endphp
                <a href="{{ route('language.switch', $otherLocale) }}"
                   class="text-sm text-gray-600 hover:text-gray-900 font-medium">
                    {{ $otherLocaleName }}
                </a>
            </div>

            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
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
                        Login
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')">
                        Register
                    </x-responsive-nav-link>
                </div>
            @endauth
        </div>
    </div>
</nav>
