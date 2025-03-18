@props(['darkMode' => false])

<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="flex items-center space-x-3 hover:bg-opacity-10 hover:bg-gray-500 rounded-full p-2.5 focus:outline-none">
        <img class="h-8 w-8 rounded-full object-cover border-2 {{ $darkMode ? 'border-gray-600' : 'border-gray-200' }}" 
             src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}" 
             alt="{{ auth()->user()->name }}">
        <div class="hidden md:flex md:items-center md:space-x-2">
            <span class="text-sm font-medium {{ $darkMode ? 'text-gray-100' : 'text-gray-900' }}">{{ auth()->user()->name }}</span>
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
