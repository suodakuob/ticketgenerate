<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-3xl font-bold mb-4">
                        {{ $match->home_team }} vs {{ $match->away_team }}
                    </h1>

                    <!-- Plan du stade interactif -->
                    <div class="relative">
                        <!-- ✅ Boutons de Zoom -->
                        <div class="absolute top-4 right-4 z-10 flex flex-col space-y-2">
                            <button id="zoom-in" class="p-2 bg-blue-500 text-black rounded-md shadow-md hover:bg-green-600">
                                ➕ Zoom In
                            </button>
                            <button id="zoom-out" class="p-2 bg-red-500 text-white rounded-md shadow-md hover:bg-red-600">
                                ➖ Zoom Out
                            </button>
                        </div>

                        <div class="stadium-svg-container mx-auto">
                            @include('components.stadium.stadium-svg-plan')
                        </div>
                    </div>

                    <!-- Infos des sections sélectionnées -->
                    <div id="section-info" class="mt-4 p-4 bg-gray-100 rounded-md">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Billets sélectionnés</h3>
                        <p class="text-gray-700">Sélectionnez une zone pour voir les détails</p>
                    </div>

                    <!-- Formulaire de réservation -->
                    <h2 class="text-xl font-semibold my-4">Réserver vos billets</h2>

                    <form action="{{ route('tickets.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="match_id" value="{{ $match->id }}">
                        <input type="hidden" id="ticketPrice" data-price="{{ $match->ticket_price }}">

                        <!-- Sélection du nombre de billets -->
                        <div class="flex items-center space-x-2">
                            <button type="button" id="decrement" class="px-3 py-2 bg-gray-300 rounded">-</button>
                            <input type="number" name="quantity" id="quantity" min="1" max="{{ $match->available_tickets }}" value="1"
                                class="text-center w-16 border rounded py-2">
                            <button type="button" id="increment" class="px-3 py-2 bg-gray-300 rounded">+</button>
                        </div>

                        <!-- Prix total affiché -->
                        <p class="mt-2 text-gray-600">Prix total : <span id="totalPrice">${{ number_format($match->ticket_price, 2) }}</span></p>

                        <button type="submit"
                            class="mt-4 bg-green-600 text-white font-bold py-3 px-4 rounded hover:bg-green-700 transition">
                            Réserver Maintenant
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Inclure les scripts -->
<script src="{{ asset('js/stadium.js') }}" defer></script>
<script src="{{ asset('js/tickets.js') }}" defer></script>
<link rel="stylesheet" href="{{ asset('css/stadium.css') }}">