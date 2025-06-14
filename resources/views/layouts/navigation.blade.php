<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Bagian Kiri: Logo -->
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <img src="{{ asset('landing_assets/image/icon.svg') }}" alt="Observa Icon" class="h-9 w-auto">
                        <span class="hidden md:inline-block text-xl font-bold tracking-widest">
                            <span class="text-gray-800">OBSE</span><span class="text-yellow-500">RVA</span>
                        </span>
                    </a>
                </div>
            </div>

            <!-- Bagian Kanan: Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-900 focus:outline-none transition ease-in-out duration-150">
                            <img src="{{ session('firebase_user_photo_url', 'https://www.gravatar.com/avatar/?d=mp') }}" alt="User Profile" class="h-8 w-8 rounded-full object-cover mr-2">
                            <div>{{ session('firebase_user_name', 'User') }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- User Email -->
                        <div class="px-4 py-3 border-b border-gray-200">
                            <p class="text-sm text-gray-600 truncate">
                                {{ session('firebase_user_email', 'email@example.com') }}
                            </p>
                        </div>

                        <!-- Download App Link -->
                        <x-dropdown-link href="https://play.google.com/store/apps/details?id=com.observa.app" target="_blank">
                            <div class="flex items-center">
                                <i class="fab fa-google-play w-4 text-center mr-2 text-gray-500"></i>
                                <span>Unduh Aplikasi</span>
                            </div>
                        </x-dropdown-link>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                <div class="flex items-center text-red-600">
                                    <i class="fas fa-sign-out-alt w-4 text-center mr-2"></i>
                                    <!-- PERBAIKAN: Mengganti 'Log Out' dengan 'Keluar' -->
                                    <span>{{ __('Keluar') }}</span>
                                </div>
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-700 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-4 pb-3 border-t border-gray-200">
            <div class="px-4 mb-3">
                <div class="font-medium text-base text-gray-800">{{ session('firebase_user_name', 'User') }}</div>
                <div class="font-medium text-sm text-gray-500">{{ session('firebase_user_email', 'email@example.com') }}</div>
            </div>

            <div class="space-y-1">
                <x-responsive-nav-link href="https://play.google.com/store/apps/details?id=com.observa.app" target="_blank">
                    <div class="flex items-center">
                        <i class="fab fa-google-play w-5 text-center mr-3 text-gray-500"></i>
                        <span>Unduh Aplikasi</span>
                    </div>
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        <div class="flex items-center text-red-600">
                             <i class="fas fa-sign-out-alt w-5 text-center mr-3"></i>
                             <!-- PERBAIKAN: Mengganti 'Log Out' dengan 'Keluar' -->
                             <span>{{ __('Keluar') }}</span>
                        </div>
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
