@props(['match'])

<div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
    <!-- Stadium Header -->
    <div class="bg-green-600 p-4 rounded-t-lg text-white flex justify-between items-center">
        <span class="font-semibold">{{ $match->stadium }}</span>
        <span class="text-sm">{{ $match->available_tickets }} seats left</span>
    </div>

    <!-- Stadium Image -->
    @if($match->stadium_image)
        <div class="w-full h-48 overflow-hidden">
            <img src="{{ asset('storage/' . $match->stadium_image) }}" 
                 alt="{{ $match->stadium }}" 
                 class="w-full h-full object-cover">
        </div>
    @else
        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2zM9 7h6M9 11h6M9 15h6"/>
            </svg>
        </div>
    @endif

    <!-- Match Content -->
    <div class="p-6">
        <!-- Teams Section -->
        <div class="flex justify-between items-center mb-6">
            <div class="text-center flex-1">
                <h3 class="text-xl font-bold text-gray-900">{{ $match->home_team }}</h3>
            </div>
            <div class="text-center px-4">
                <div class="text-gray-600 font-medium">VS</div>
                <div class="text-sm text-gray-500 mt-1">
                    {{ \Carbon\Carbon::parse($match->match_date)->format('g:i') }}
                    <div class="text-sm text-gray-500">PM</div>
                </div>
            </div>
            <div class="text-center flex-1">
                <h3 class="text-xl font-bold text-gray-900">{{ $match->away_team }}</h3>
            </div>
        </div>

        <!-- Date and Price -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center text-gray-600">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ \Carbon\Carbon::parse($match->match_date)->format('F j, Y') }}
            </div>
            <div class="text-green-600 font-bold">
                ${{ number_format($match->ticket_price, 2) }}
            </div>
        </div>

        <!-- Book Button -->
        @auth
            <a href="{{ route('matches.show', $match) }}" 
               class="block w-full text-center bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors duration-200">
                Book Now
            </a>
        @else
            <a href="{{ route('login') }}" 
               class="block w-full text-center bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors duration-200">
                Book Now
            </a>
        @endauth
    </div>
</div>
