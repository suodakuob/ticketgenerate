<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation Bar -->
        <nav class="bg-black border-b border-green-500" x-data="{ open: false }">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-4">
                <div class="flex justify-between h-16">
                    <!-- Logo and Desktop Links -->
                    <div class="flex items-center">
                        <!-- Logo -->
                        <a href="{{ route('home') }}" class=" group flex items-center text-white font-bold text-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                        </svg>
                        Tutore
                        </a>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <a href="{{ route('home') }}" class="inline-flex items-center px-1 py-5 border-b-4 text-sm font-medium {{ request()->routeIs('home') ? 'border-white text-white' : 'border-transparent text-green-100 hover:text-white hover:border-green-300' }}">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                    Home
                                </a>
                                @auth
                                    <a href="{{ route('matches.index') }}" class="inline-flex items-center px-1 py-5 border-b-4 text-sm font-medium {{ request()->routeIs('matches.index') ? 'border-white text-white' : 'border-transparent text-green-100 hover:text-white hover:border-green-300' }}">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                        Matches
                                    </a>
                                    <a href="{{ route('my-tickets') }}" class="inline-flex items-center px-1 py-5 border-b-4 text-sm font-medium {{ request()->routeIs('my-tickets') ? 'border-white text-white' : 'border-transparent text-green-100 hover:text-white hover:border-green-300' }}">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                        </svg>
                                        My Tickets
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-1 py-5 border-b-4 text-sm font-medium {{ request()->routeIs('admin.*') ? 'border-white text-white' : 'border-transparent text-green-100 hover:text-white hover:border-green-300' }}">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                                    </svg>
                                        Admin Panel
                                    </a>
                                @endif
                                @else
                                    <div class="flex items-center">
                                        <span class="text-white/80 text-sm">
                                            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            Please
                                            <a href="{{ route('login') }}" class="text-white hover:text-green-100 font-medium">Login</a>
                                            or
                                            <a href="{{ route('register') }}" class="text-white hover:text-green-100 font-medium">Register</a>
                                            to view all Matches
                                        </span>
                                    </div>
                                @endauth
                            </div>
                        </div>

                   <!-- User Dropdown -->
                   <div class="flex items-center">
                        @auth
                        @props(['darkMode' => false])

                        <div class="relative " x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-3 hover:bg-opacity-30 hover:bg-white rounded-full p-2.5 focus:outline-none">
                                <img class="h-8 w-8 rounded-full object-cover border-2 {{ $darkMode ? 'border-gray-600' : 'border-gray-200' }}"
                                     src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}"
                                     alt="{{ auth()->user()->name }}">
                                <div class="hidden md:flex md:items-center md:space-x-2 ">
                                    <span class="text-sm font-medium {{ $darkMode ? 'text-gray-100' : 'text-white' }}">{{ auth()->user()->name }}</span>
                                    <svg class="h-5 w-5 {{ $darkMode ? 'text-gray-400' : 'text-gray-400' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>

                        <!-- Dropdown Menu -->
                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50"
                                 style="display: none;">
                                <div class="px-4 py-2 border-b">
                                    <p class="text-sm text-gray-500">Signed in as</p>
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->email }}</p>
                                    <p class="text-sm font-medium text-yellow-500 truncate">( {{ auth()->user()->role }} )</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <div class="flex items-center">
                                        <svg class="mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Profile Settings
                                    </div>
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <div class="flex items-center text-red-700 hover:bg-gray-100">
                                            <svg class="mr-3 h-5 w-5 text-red-400 group-hover:text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Sign Out
                                        </div>
                                    </button>
                                </form>
                            </div>
                        </div>

                            @else
                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 text-sm font-semibold text-white bg-black border border-transparent rounded-lg shadow-sm hover:bg-green-700 hover:shadow-md transition-all duration-200 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">Login</a>
                                    <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 text-sm font-semibold text-green-700 bg-white border border-green-300 rounded-lg shadow-sm hover:bg-gray-200 hover:shadow-md transition-all duration-200 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">Register</a>
                                </div>
                            @endauth
                        </div>

                        <!-- Hamburger -->
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
                            <a href="{{ route('matches.index') }}" class="block pl-3 pr-4 py-2 text-white hover:text-white/80 text-base font-medium">
                                Matches
                            </a>
                            <a href="{{ route('my-tickets') }}" class="block pl-3 pr-4 py-2 text-white hover:text-white/80 text-base font-medium">
                                My Tickets
                            </a>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="block pl-3 pr-4 py-2 text-white hover:text-white/80 text-base font-medium">
                                    Admin Panel
                                </a>
                            @endif
                        @else
                            <div class="pl-3 pr-4 py-2 text-white/80 text-sm">
                                Please <a href="{{ route('login') }}" class="text-white hover:text-green-100 font-medium">login</a>
                                or <a href="{{ route('register') }}" class="text-white hover:text-green-100 font-medium">register</a>
                                to view matches
                            </div>
                        @endauth
                    </div>

                    @auth
                        <div class="pt-4 pb-1 border-t border-green-700">
                            <div class="flex items-center px-4">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-white/10 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="ml-3">
                                    <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                                    <div class="font-medium text-sm text-white/80">{{ Auth::user()->email }}</div>
                                </div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <x-responsive-nav-link :href="route('profile.edit')" class="text-white hover:text-white/80">
                                    {{ __('Profile Settings') }}
                                </x-responsive-nav-link>

                                <x-responsive-nav-link href="#" class="text-white hover:text-white/80">
                                    {{ __('Help Center') }}
                                </x-responsive-nav-link>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <x-responsive-nav-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();" class="text-white hover:text-white/80">
                                        {{ __('Sign Out') }}
                                    </x-responsive-nav-link>
                                </form>
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
        <x-chatbot-widget />
        <x-general-chatbot-widget />

    </body>
</html>