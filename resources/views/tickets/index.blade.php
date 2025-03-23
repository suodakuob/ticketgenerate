<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-6">My Tickets</h2>

                    @if($tickets->isEmpty())
                        <p class="text-gray-600">You haven't purchased any tickets yet.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($tickets as $ticket)
                                <div class="border rounded-lg overflow-hidden bg-white shadow-md hover:shadow-lg transition">
                                    <div class="bg-green-600 text-white px-4 py-2">
                                        <h3 class="text-lg font-semibold">{{ $ticket->match->home_team }} vs {{ $ticket->match->away_team }}</h3>
                                    </div>
                                    <div class="p-4">
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-600">Date & Time</p>
                                            <p class="font-medium">{{ $ticket->match->match_date->format('F j, Y g:i A') }}</p>
                                        </div>
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-600">Stadium</p>
                                            <p class="font-medium">{{ $ticket->match->stadium }}</p>
                                        </div>
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-600">Ticket Number</p>
                                            <p class="font-medium">{{ $ticket->ticket_number }}</p>
                                        </div>
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-600">Status</p>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $ticket->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($ticket->status) }}
                                            </span>
                                        </div>
                                        @if($ticket->status === 'confirmed')
                                        <a href="{{ route('tickets.download', ['ticket' => $ticket->id]) }}" 
   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
    Download E-Ticket
</a>

@endif

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>