<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <div class="flex h-screen bg-gray-100">
            <!-- Sidebar -->
            <div class="hidden md:flex md:flex-shrink-0">
                <div class="flex flex-col w-64">
                    <div class="flex flex-col flex-grow py-4 overflow-y-auto bg-black border-r">
                        <div class="flex flex-col flex-shrink-0 px-4 pb-4">
                            <a href="{{ route('admin.dashboard') }}" class="group flex items-center text-lg font-semibold tracking-tighter text-white focus:outline-none focus:ring">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                            </svg>
                                tutorefootball
                            </a>
                        </div>

                        <a href="{{ route('home') }}" class="flex items-center px-4 py-4 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('home') ? 'text-white bg-green-800' : 'text-green-100 hover:bg-green-800' }} rounded-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Home
                        </a>

                        <nav class="flex-1 space-y-1 mt-2">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('admin.dashboard') ? 'text-white bg-green-800' : 'text-green-100 hover:bg-green-800' }} rounded-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Dashboard
                            </a>

                            <a href="{{ route('admin.matches.index') }}" class="flex items-center px-4 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('admin.matches.*') ? 'text-white bg-green-800' : 'text-green-100 hover:bg-green-800' }} rounded-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Matches
                            </a>

                            <a href="{{ route('admin.bookings.index') }}" class="flex items-center px-4 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('admin.bookings.*') ? 'text-white bg-green-800' : 'text-green-100 hover:bg-green-800' }} rounded-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Bookings
                            </a>

                            <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('admin.users.*') ? 'text-white bg-green-800' : 'text-green-100 hover:bg-green-800' }} rounded-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Users
                            </a>
                        </nav>

                        <div class="flex flex-shrink-0 px-4 py-1 border-t-4 border-green-800">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <img class="w-8 h-8 rounded-full" src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}" alt="{{ auth()->user()->name }}">
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-green-400">{{ ucfirst(auth()->user()->role) }}</p>
                                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                                        @csrf
                                        <button type="submit" class="text-xs text-green-200 hover:text-white">
                                            Logout

                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div class="md:hidden fixed inset-0 z-40 flex" x-show="mobileMenuOpen" style="display: none;">
                <!-- Overlay -->
                <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="mobileMenuOpen = false"></div>

                <!-- Menu -->
                <div class="relative flex-1 flex flex-col max-w-xs w-full bg-green-700">
                    <div class="absolute top-0 right-0 -mr-12 pt-2">
                        <button class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="mobileMenuOpen = false">
                            <span class="sr-only">Close sidebar</span>
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="flex-1 overflow-auto focus:outline-none">
                <main>
                    @if (isset($header))
                        <header class="bg-white shadow">
                            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                                <div class="flex justify-between items-center">
                                    {{ $header }}
                                    <x-user-dropdown />
                                </div>
                            </div>
                        </header>
                    @endif

                    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Mobile menu button -->
    <div class="md:hidden fixed bottom-4 right-4">
        <button type="button" @click="mobileMenuOpen = true" class="bg-green-600 p-2 rounded-full text-white shadow-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>
</body>
</html>
