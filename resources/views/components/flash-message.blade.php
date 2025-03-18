@if (session()->has('success') || session()->has('error'))
    <div x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 3000)"
         @class([
             'fixed top-4 right-4 z-50 rounded-lg p-4 shadow-lg transition-all duration-500',
             'bg-green-100 text-green-800 border border-green-200' => session()->has('success'),
             'bg-red-100 text-red-800 border border-red-200' => session()->has('error')
         ])>
        <div class="flex items-center">
            @if(session()->has('success'))
                <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            @else
                <svg class="w-5 h-5 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            @endif
            <p class="text-sm font-medium">
                {{ session('success') ?? session('error') }}
            </p>
            <button @click="show = false" class="ml-4 text-gray-500 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
@endif
