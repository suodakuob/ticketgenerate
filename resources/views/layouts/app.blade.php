<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Football Tickets') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased h-full">
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-green-600 border-b border-green-500 w-full">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16 w-full">
                        <div class="flex w-full">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center w-full">
                                <a href="{{ route('home') }}" class="text-white font-bold text-xl">
                                    tutorefootball
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex w-full">
                                <a href="{{ route('home') }}" 
                                   class="{{ request()->routeIs('home') ? 'border-white text-white' : 'border-transparent text-green-100 hover:text-white hover:border-green-300' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Home
                                </a>
                                @auth
                                    <a href="{{ route('my-tickets') }}" 
                                       class="{{ request()->routeIs('my-tickets') ? 'border-white text-white' : 'border-transparent text-green-100 hover:text-white hover:border-green-300' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                        My Tickets
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('admin.dashboard') }}"
                                           class="{{ request()->routeIs('admin.*') ? 'border-white text-white' : 'border-transparent text-green-100 hover:text-white hover:border-green-300' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                            Admin Panel
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            @auth
                                <div class="flex items-center">
                                    <div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
                                        <div>
                                            <button type="button" @click="open = !open" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white hover:text-white/80 focus:outline-none transition ease-in-out duration-150 gap-x-2" id="user-menu-button">
                                                <img class="h-8 w-8 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}" alt="{{ auth()->user()->name }}">
                                                <span>{{ auth()->user()->name }}</span>
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div x-show="open"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" id="dropdown-menu"
                                             role="menu"
                                             aria-orientation="vertical"
                                             aria-labelledby="user-menu-button"
                                             tabindex="-1">
                                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                                Profile
                                            </a>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                                    Log Out
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center space-x-4">
                                <a href="{{ route('login') }}" 
                                   class="inline-flex items-center px-6 py-3 text-sm font-semibold text-white bg-green-600 border border-transparent rounded-lg shadow-sm hover:bg-green-700 hover:shadow-md transition-all duration-200 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    Login
                                </a>
                                    @if (Route::has('register'))
                                    <a href="{{ route('register') }}" 
                                   class="inline-flex items-center px-6 py-3 text-sm font-semibold text-green-700 bg-white border border-green-300 rounded-lg shadow-sm hover:bg-gray-50 hover:shadow-md transition-all duration-200 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    Register
                                </a>
                                    @endif
                                </div>
                            @endauth
                        </div>

                        <!-- Mobile menu button -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-white/80 hover:bg-green-500 focus:outline-none focus:bg-green-500 focus:text-white transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu -->
                <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <a href="{{ route('home') }}" class="block pl-3 pr-4 py-2 text-white hover:text-white/80 text-base font-medium">
                            Home
                        </a>
                        @auth
                            <a href="{{ route('my-tickets') }}" class="block pl-3 pr-4 py-2 text-white hover:text-white/80 text-base font-medium">
                                My Tickets
                            </a>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="block pl-3 pr-4 py-2 text-white hover:text-white/80 text-base font-medium">
                                    Admin Panel
                                </a>
                            @endif
                        @endauth
                    </div>

                    @auth
                        <div class="pt-4 pb-1 border-t border-green-500">
                            <div class="flex items-center px-4">
                                <div class="flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}" alt="{{ auth()->user()->name }}">
                                </div>
                                <div class="ml-3">
                                    <div class="font-medium text-base text-white">{{ auth()->user()->name }}</div>
                                    <div class="font-medium text-sm text-green-200">{{ auth()->user()->email }}</div>
                                </div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <a href="{{ route('profile.edit') }}" class="block pl-3 pr-4 py-2 text-white hover:text-white/80 text-base font-medium">
                                    Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left pl-3 pr-4 py-2 text-white hover:text-white/80 text-base font-medium">
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="pt-4 pb-1 border-t border-green-500">
                            <div class="space-y-1">
                                <a href="{{ route('login') }}" class="block pl-3 pr-4 py-2 text-white hover:text-white/80 text-base font-medium">
                                    Login
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="block pl-3 pr-4 py-2 text-white hover:text-white/80 text-base font-medium">
                                        Register
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endauth
                </div>
            </nav>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Dropdown Script -->
        <script>
            function toggleDropdown() {
                const dropdown = document.getElementById('dropdown-menu');
                dropdown.classList.toggle('hidden');
            }

            // Close dropdown when clicking outside
            window.addEventListener('click', function(e) {
                const dropdown = document.getElementById('dropdown-menu');
                const button = document.getElementById('user-menu-button');
                if (!button.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        </script>
    </body>
</html>
