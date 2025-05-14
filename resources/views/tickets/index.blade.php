<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>My Tickets - Ticket360</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen">

    <div class="py-6 px-6">
        <div class="max-w-7xl mx-auto">

            <!-- 🔝 Top navigation buttons -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
                <a href="{{ route('home') }}"
                   class="inline-block px-5 py-2 bg-gray-800 text-white rounded-md font-semibold shadow hover:bg-gray-900 transition">
                    🏠 Revenir à l'accueil
                </a>
                <div class="flex gap-4">
                    <a href="{{ route('matches.index') }}"
                       class="inline-block px-5 py-2 bg-blue-600 text-white rounded-md font-semibold shadow hover:bg-blue-700 transition">
                        ⚽ Voir les matchs
                    </a>
                    <a href="{{ route('tickets.clear') }}"
                       class="inline-block px-5 py-2 bg-red-600 text-white rounded-md font-semibold shadow hover:bg-red-700 transition"
                       onclick="return confirm('Are you sure you want to clear your ticket list?');">
                        🗑️ Clear Tickets
                    </a>
                </div>
            </div>

            <h2 class="text-3xl font-bold mb-6">My Tickets</h2>

            {{-- Affichage des messages flash (succès, erreur) --}}
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Succès!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
             @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Erreur!</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            {{-- ✅ FORMULAIRE DE RECHERCHE AJOUTÉ ICI ✅ --}}
            <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                <h3 class="text-xl font-semibold mb-4">Rechercher un Ticket</h3>
                <form action="{{ route('my-tickets') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
                    <div class="flex-grow w-full sm:w-auto">
                        <label for="ticket_number_search" class="block text-sm font-medium text-gray-700 mb-1">Numéro de Ticket :</label>
                        <input type="text"
                               name="ticket_number_search"
                               id="ticket_number_search"
                               value="{{ request('ticket_number_search') }}" {{-- Garde la valeur après la recherche --}}
                               placeholder="Entrez le numéro de ticket"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                         <button type="submit"
                                 class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md font-semibold shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 mt-4 w-full sm:w-auto justify-center">
                             <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                             Rechercher
                         </button>
                    </div>
                </form>
                 @if(request('ticket_number_search'))
                     <p class="text-sm text-gray-600 mt-2">
                         Affichage des tickets pour le numéro: <span class="font-semibold">{{ request('ticket_number_search') }}</span>.
                         <a href="{{ route('my-tickets') }}" class="text-blue-600 hover:underline">Afficher tous les tickets</a>
                     </p>
                 @endif
            </div>
            {{-- ✅ FIN DU FORMULAIRE DE RECHERCHE ✅ --}}


            @if($tickets->isEmpty())
                {{-- Message modifié pour la recherche --}}
                <p class="text-gray-600">
                    @if(request('ticket_number_search'))
                        Aucun ticket trouvé avec le numéro "{{ request('ticket_number_search') }}".
                    @else
                        You haven't purchased any tickets yet.
                    @endif
                </p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($tickets as $ticket)
                        <div class="border rounded-lg overflow-hidden bg-white shadow-md hover:shadow-lg transition
                              {{ isset($recentlyViewedId) && $ticket->id == $recentlyViewedId ? 'ring-2 ring-blue-500' : '' }}">
                            <div class="bg-green-600 text-white px-4 py-2">
                                <h3 class="text-lg font-semibold">
                                    {{ $ticket->match->home_team }} vs {{ $ticket->match->away_team }}
                                </h3>
                                @if(isset($recentlyViewedId) && $ticket->id == $recentlyViewedId)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                        Recently Viewed
                                    </span>
                                @endif
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
                                @if($ticket->ticket_number)
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600">Ticket Number</p>
                                        <p class="font-medium">{{ $ticket->ticket_number }}</p>
                                    </div>
                                @endif
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">Status</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $ticket->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($ticket->status) }}
                                    </span>
                                </div>

                                {{-- Le bouton Voir le E-Ticket --}}
                                @if($ticket->status === 'confirmed')
                                    <a href="{{ route('tickets.show', ['ticket' => $ticket->id]) }}"
                                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                                        👁️ Voir le E-Ticket
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
    <x-chatbot-widget />
    <x-general-chatbot-widget />

</body>
</html>
