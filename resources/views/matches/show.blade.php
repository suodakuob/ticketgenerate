<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Match Details -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">
                            {{ $match->home_team }} vs {{ $match->away_team }}
                        </h1>

                        <!-- Stadium Image -->
                        @if($match->stadium_image)
                            <div class="w-full h-64 overflow-hidden rounded-lg mb-6">
                                <img src="{{ asset('storage/' . $match->stadium_image) }}"
                                     alt="{{ $match->stadium }}"
                                     class="w-full h-full object-cover">
                            </div>
                        @endif

                        <!-- Stadium Image (Plan SVG) -->
                        <div class="w-full h-auto mb-6 mx-auto">
                            @include('components.stadium.stadium-svg-plan')
                        </div>

                        <link rel="stylesheet" href="{{ asset('css/svg.css') }}">

                        <div id="section-info" class="mt-4 p-4 bg-gray-100 rounded-md">
                            <h3 id="section-info-title" class="text-lg font-semibold text-gray-900 mb-2">Section Information</h3>
                            <p id="section-name" class="text-gray-700 mb-1"></p>
                            <p id="section-availability" class="text-gray-700 mb-1"></p>
                            <p id="section-price" class="text-gray-700"></p>
                            <!-- On pourra ajouter une description ici plus tard -->
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const sectionData = {  // Objet JavaScript pour les données des sections
                                    'section-l3': {
                                        name: 'Tribune Latérale 3',
                                        availableTickets: 50,
                                        price: 75.00
                                    },
                                    'section-l2': {
                                        name: 'Tribune Latérale 2',
                                        availableTickets: 25,
                                        price: 120.00
                                    },
                                    'section-l1': {
                                        name: 'Tribune Latérale 1',
                                        availableTickets: 10,
                                        price: 200.00
                                    },
                                    'section-k': {
                                        name: 'Virage K',
                                        availableTickets: 150,
                                        price: 50.00
                                    },
                                    'section-r': {
                                        name: 'Virage R',
                                        availableTickets: 120,
                                        price: 55.00
                                    },
                                    'section-j': {
                                        name: 'Tribune Principale Basse J',
                                        availableTickets: 5,
                                        price: 300.00
                                    },
                                    'section-g': {
                                        name: 'Tribune Principale Haute G',
                                        availableTickets: 30,
                                        price: 250.00
                                    },
                                    'section-h': {
                                        name: 'Tribune Principale Milieu H',
                                        availableTickets: 15,
                                        price: 350.00
                                    },
                                    // ... tu peux ajouter des données pour d'autres sections ici ...
                                };

                                const sections = document.querySelectorAll('.stadium-section'); // Sélectionne toutes les sections
                                const sectionInfoDiv = document.getElementById('section-info');
                                const quantityInput = document.getElementById('quantity'); // Cibler le champ de quantité
                                const selectedSections = []; // Tableau pour stocker les sections sélectionnées (NOUVEAU)

                                sections.forEach(section => {
                                    section.addEventListener('click', function() {
                                        const sectionLabel = this.getAttribute('inkscape:label'); // Récupère le label de la section
                                        const sectionClass = this.classList[1]; // Récupère la classe spécifique (section-l3, section-l2, ...)
                                        const data = sectionData[sectionClass]; // Récupère les données correspondantes dans sectionData

                                        // NOUVEAU - Gestion de la sélection multiple:
                                        const sectionIndex = selectedSections.indexOf(sectionClass); // Vérifier si la section est déjà sélectionnée

                                        if (sectionIndex === -1) { // Si la section n'est PAS déjà sélectionnée
                                            selectedSections.push(sectionClass); // Ajouter la section au tableau des sections sélectionnées
                                            this.classList.add('section-selected'); // Ajouter une classe CSS pour l'effet visuel de sélection
                                        } else { // Si la section est DÉJÀ sélectionnée
                                            selectedSections.splice(sectionIndex, 1); // Retirer la section du tableau
                                            this.classList.remove('section-selected'); // Retirer la classe CSS de sélection
                                        }

                                        // Mettre à jour le champ "Number of Tickets" avec le nombre de sections sélectionnées (NOUVEAU)
                                        quantityInput.value = selectedSections.length;


                                        if (data) {
                                            sectionInfoDiv.innerHTML = `
                                                <h3 id="section-info-title" class="text-lg font-semibold text-gray-900 mb-2">${data.name}</h3>
                                                <p id="section-name" class="text-gray-700 mb-1">Section: <span class="font-medium">${sectionLabel}</span></p>
                                                <p id="section-availability" class="text-gray-700 mb-1">Billets disponibles: <span class="font-medium">${data.availableTickets}</span></p>
                                                <p id="section-price" class="text-gray-700">Prix par billet: <span class="font-medium">$${data.price.toFixed(2)}</span></p>
                                            `;
                                        } else {
                                            sectionInfoDiv.innerHTML = `<p class="text-gray-700">Section cliquée, mais pas d'informations disponibles.</p>`;
                                        }
                                    });
                                });
                            });
                        </script>
                        @push('scripts')
                        <script>
                            // ... le reste de ton code JavaScript existant ...
                        </script>
                        @endpush

                        <!-- ... le reste de ton template Blade ... -->

                        <!-- Match Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <h2 class="text-xl font-semibold mb-4">Match Information</h2>
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-gray-600">Stadium:</span>
                                        <span class="font-medium ml-2">{{ $match->stadium }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Date:</span>
                                        <span class="font-medium ml-2">{{ \Carbon\Carbon::parse($match->match_date)->format('F j, Y') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Time:</span>
                                        <span class="font-medium ml-2">{{ \Carbon\Carbon::parse($match->match_date)->format('g:i A') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Available Tickets:</span>
                                        <span class="font-medium ml-2">{{ $match->available_tickets }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Price per Ticket:</span>
                                        <span class="font-medium ml-2">${{ number_format($match->ticket_price, 2) }}</span>
                                    </div>

                                    @if(isset($ticket) && $ticket->qr_code)
                                    <img src="{{ asset('storage/' . $ticket->qr_code) }}" alt="QR Code" class="mt-4 w-32 h-32">
                                    @endif
                                </div>
                            </div>

                            <!-- Booking Form -->
                            <div>
                                <h2 class="text-xl font-semibold mb-4">Book Tickets</h2>
                                @if($match->available_tickets > 0)
                                    <form action="{{ route('tickets.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="match_id" value="{{ $match->id }}">

                                        <div class="mb-4">
    <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">
        Number of Tickets
    </label>
    <div class="flex items-center">
        <button type="button" id="decrement"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-l">
            -
        </button>
        <input type="number" name="quantity" id="quantity" min="1" max="{{ $match->available_tickets }}" value="1"
               class="shadow appearance-none border text-center w-20 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
        <button type="button" id="increment"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-r">
            +
        </button>
    </div>
    <p id="available-tickets" class="text-sm text-gray-500 mt-1">
        Available Tickets: {{ $match->available_tickets }}
    </p>
</div>

<script>
    const quantityInput = document.getElementById('quantity');
    const decrementButton = document.getElementById('decrement');
    const incrementButton = document.getElementById('increment');
    const availableTickets = parseInt("{{ $match->available_tickets }}");  // Get available tickets as a number

    decrementButton.addEventListener('click', () => {
        let currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
        }
    });

    incrementButton.addEventListener('click', () => {
        let currentValue = parseInt(quantityInput.value);
        if (currentValue < availableTickets && currentValue < 10) { // Limit to available and max 10
            quantityInput.value = currentValue + 1;
        }
    });
</script>

                                        <div class="mb-4">
                                            <p class="text-sm text-gray-600">
                                                Total Price: $<span id="totalPrice">{{ number_format($match->ticket_price, 2) }}</span>
                                            </p>
                                        </div>

                                        <button type="submit"
                                                class="w-full bg-green-600 text-white font-bold py-3 px-4 rounded hover:bg-green-700 focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                                            Book Now
                                        </button>
                                    </form>

                                    @push('scripts')
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const ticketPrice = {{ $match->ticket_price }};
                                            const quantitySelect = document.getElementById('quantity');
                                            const totalPriceSpan = document.getElementById('totalPrice');

                                            function updateTotalPrice() {
                                                const quantity = parseInt(quantitySelect.value);
                                                const total = (quantity * ticketPrice).toFixed(2);
                                                totalPriceSpan.textContent = total;
                                            }

                                            quantitySelect.addEventListener('change', updateTotalPrice);
                                            // Initialize total price
                                            updateTotalPrice();
                                        });
                                    </script>
                                    @endpush
                                @else
                                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-yellow-700">
                                                    Sorry, this match is sold out!
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>