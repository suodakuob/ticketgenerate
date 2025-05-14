<x-app-layout>
    <style>
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
        .animate-spin {
            animation: spin 1.5s linear infinite;
        }
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }
        .hover\:scale-105:hover {
            transform: scale(1.05);
        }
    </style>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-3xl font-bold mb-4 text-center">
                    The Big Match :  {{ $match->home_team }} vs {{ $match->away_team }}
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
                            <!-- Add 360 view button that will be shown/hidden based on section selection -->
                            <button id="view-360-btn" class="hidden p-2 bg-blue-600 text-white rounded-md shadow-md hover:bg-blue-700 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span class="ml-1">360°</span>
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
                    <div class="flex items-center justify-between mt-6">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('matches.sections', $match) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                View All Sections
                            </a>
                            <form id="reservation-form" method="POST" action="{{ route('tickets.store') }}">
                                @csrf
                                <input type="hidden" name="match_id" value="{{ $match->id }}">
                                <input type="hidden" id="ticketPrice" data-price="{{ $match->ticket_price }}">
                                <input type="hidden" name="section_id" id="form_section_id" value="">

                                <!-- Sélection du nombre de billets -->
                                <div class="flex items-center space-x-2">
                                    <button type="button" id="decrement" class="px-3 py-2 bg-gray-300 rounded" onclick="updateQuantity(-1)">-</button>
                                    <input type="number" name="quantity" id="quantity" min="1" max="10" value="1"
                                        class="text-center w-16 border rounded py-2">
                                    <button type="button" id="increment" class="px-3 py-2 bg-gray-300 rounded" onclick="updateQuantity(1)">+</button>
                                </div>

                                <!-- Prix total affiché -->
                                <p class="mt-2 text-gray-600">Prix total : <span id="totalPrice">Mad {{ number_format($match->ticket_price, 2) }}</span></p>

                                <button type="button" id="reserve-button" disabled
                                    class="mt-4 bg-gray-400 text-white font-bold py-3 px-4 rounded cursor-not-allowed transition disabled:opacity-50">
                                    Sélectionnez une section d'abord
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pass section data to the stadium.js script
        window.sectionData = @json($sectionData);
        window.sectionsUrl = "{{ route('matches.sections', $match) }}";

        // For backwards compatibility with any code expecting matchSectionData
        window.matchSectionData = window.sectionData;

        // Function to update quantity and recalculate total price
        function updateQuantity(change) {
            const quantityInput = document.getElementById('quantity');
            const currentValue = parseInt(quantityInput.value) || 1;
            const newValue = Math.max(1, Math.min(10, currentValue + change));

            // Get the selected section
            const sectionId = document.getElementById('form_section_id').value;
            let maxTickets = 10;

            if (sectionId && sectionData[sectionId]) {
                // Limit by available seats in the section
                maxTickets = Math.min(maxTickets, sectionData[sectionId].available_seats);
            }

            // Don't allow more tickets than available
            quantityInput.value = Math.min(newValue, maxTickets);

            // Update total price
            updateTotalPrice();
        }

        // Update the total price based on quantity and section price
        function updateTotalPrice() {
            const quantity = parseInt(document.getElementById('quantity').value) || 1;
            const sectionId = document.getElementById('form_section_id').value;
            let price = parseFloat(document.getElementById('ticketPrice').dataset.price);

            // Use section price if a section is selected
            if (sectionId && sectionData[sectionId]) {
                price = parseFloat(sectionData[sectionId].price);
            }

            const totalPrice = price * quantity;
            document.getElementById('totalPrice').textContent = 'Mad ' + totalPrice.toFixed(2);
        }

        // 360° view functionality
        function open360Preview(element, url, sectionName) {
            // Check if we already have a 360° view modal
            let modal = document.getElementById('view-360-modal');

            // If modal doesn't exist, create it
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'view-360-modal';
                modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-50';
                modal.innerHTML = `
                    <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-5xl w-full h-[80vh] flex flex-col">
                        <div class="bg-gray-100 px-4 py-3 flex justify-between items-center border-b">
                            <h3 class="text-lg font-medium text-gray-900" id="view-360-title">360° View</h3>
                            <div class="flex items-center space-x-2">
                                <button id="vr-mode-toggle" class="text-gray-500 hover:text-gray-700">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                                <button id="close-360-view" class="text-gray-500 hover:text-gray-700">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex-grow relative" id="view-360-container">
                            <iframe id="view-360-iframe" src="" allowfullscreen style="width:100%; height:100%; border:0;"></iframe>

                            <!-- Pannellum 360 viewer -->
                            <div id="pannellum-container" class="w-full h-full hidden">
                                <div id="pannellum-viewer"></div>
                            </div>

                            <!-- Fallback for images -->
                            <div id="view-360-image-container" class="w-full h-full hidden relative">
                                <img id="view-360-image" src="" alt="360° View" class="max-w-full max-h-full m-auto transition-transform duration-500">
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);

                // Add Pannellum if it doesn't exist
                if (!document.querySelector('link[href*="pannellum.css"]')) {
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = 'https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css';
                    document.head.appendChild(link);

                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js';
                    document.head.appendChild(script);
                }

                // Set up event listeners once the modal is created
                setupModalEventListeners();
            }

            const iframe = document.getElementById('view-360-iframe');
            const imageContainer = document.getElementById('view-360-image-container');
            const pannellumContainer = document.getElementById('pannellum-container');
            const image = document.getElementById('view-360-image');
            const modalTitle = document.getElementById('view-360-title');

            modalTitle.textContent = `360° View - ${sectionName}`;

            // Check if URL is an image
            if (url.match(/\.(jpeg|jpg|gif|png|webp)$/i)) {
                iframe.classList.add('hidden');

                try {
                    // Use Pannellum for 360 viewing
                    pannellumContainer.classList.remove('hidden');
                    imageContainer.classList.add('hidden');

                    // Initialize Pannellum (with delay to ensure the script is loaded)
                    setTimeout(() => {
                        if (window.pannellum) {
                            initPannellumViewer(url);
                        } else {
                            // Fallback to simple image viewer if Pannellum isn't loaded
                            pannellumContainer.classList.add('hidden');
                            imageContainer.classList.remove('hidden');
                            image.src = url;
                            initializeImageControls();
                        }
                    }, 300);
                } catch (error) {
                    console.error('Pannellum error:', error);
                    // Fallback to simple image viewer
                    pannellumContainer.classList.add('hidden');
                    imageContainer.classList.remove('hidden');
                    image.src = url;
                }
            } else {
                // If not an image, use iframe
                pannellumContainer.classList.add('hidden');
                imageContainer.classList.add('hidden');
                iframe.classList.remove('hidden');
                iframe.src = url;
            }

            modal.classList.remove('hidden');
        }

        function setupModalEventListeners() {
            const modal = document.getElementById('view-360-modal');
            const closeBtn = document.getElementById('close-360-view');
            const fullscreenBtn = document.getElementById('fullscreen-360-view');
            const vrModeBtn = document.getElementById('vr-mode-toggle');
            const viewContainer = document.getElementById('view-360-container');

            // Close modal handler
            closeBtn.addEventListener('click', function() {
                modal.classList.add('hidden');
                document.getElementById('view-360-iframe').src = '';
                document.getElementById('view-360-image').src = '';

                // Destroy Pannellum viewer if it exists
                const viewer = document.getElementById('pannellum-viewer');
                if (viewer && viewer.firstChild) {
                    viewer.innerHTML = '';
                }
            });

            // Fullscreen toggle handler
            fullscreenBtn.addEventListener('click', function() {
                if (document.fullscreenElement) {
                    document.exitFullscreen();
                } else {
                    viewContainer.requestFullscreen();
                }
            });

            // Close on outside click
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeBtn.click();
                }
            });
        }

        function initPannellumViewer(imageUrl) {
            // Clear any existing viewer
            const viewerElement = document.getElementById('pannellum-viewer');
            viewerElement.innerHTML = '';
            viewerElement.style.display = 'block';

            // Create new viewer
            pannellum.viewer('pannellum-viewer', {
                type: 'equirectangular',
                panorama: imageUrl,
                autoLoad: true,
                autoRotate: -2,
                compass: true,
                showControls: true,
                showFullscreenCtrl: false,
                mouseZoom: true,
                keyboardZoom: true
            });
        }

        function initializeImageControls() {
            // Image manipulation variables
            let currentRotation = 0;
            let currentScale = 1;
            let isDragging = false;
            let startX, startY, translateX = 0, translateY = 0;

            // Reset view state
            resetViewState();

            const image = document.getElementById('view-360-image');
            const viewContainer = document.getElementById('view-360-container');

            // Add drag functionality
            image.addEventListener('mousedown', startDrag);
            window.addEventListener('mousemove', drag);
            window.addEventListener('mouseup', endDrag);

            // Add zoom on mousewheel
            viewContainer.addEventListener('wheel', handleWheel);
        }

        function resetViewState() {
            const image = document.getElementById('view-360-image');
            if (image) {
                image.style.transform = 'translate(0px, 0px) rotate(0deg) scale(1)';
            }
        }

        function startDrag(e) {
            e.preventDefault();
            isDragging = true;
            startX = e.clientX - translateX;
            startY = e.clientY - translateY;
        }

        function drag(e) {
            if (!isDragging) return;
            e.preventDefault();
            translateX = e.clientX - startX;
            translateY = e.clientY - startY;
            updateImageTransform();
        }

        function endDrag() {
            isDragging = false;
        }

        function handleWheel(e) {
            e.preventDefault();
            const delta = e.deltaY > 0 ? -0.1 : 0.1;
            zoomImage(delta);
        }

        function updateImageTransform() {
            const image = document.getElementById('view-360-image');
            if (image) {
                image.style.transform = `translate(${translateX}px, ${translateY}px) rotate(${currentRotation}deg) scale(${currentScale})`;
            }
        }

        function zoomImage(factor) {
            currentScale = Math.max(0.5, Math.min(5, currentScale + factor));
            updateImageTransform();
        }
    </script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reserveButton = document.getElementById('reserve-button');
        const paymentModal = document.getElementById('payment-modal');
        const processingState = document.getElementById('processing-state');
        const successState = document.getElementById('success-state');
        const errorState = document.getElementById('error-state');
        const errorClose = document.getElementById('error-close');
        const reservationForm = document.getElementById('reservation-form');

        // Function to show modal
        function showModal() {
            paymentModal.classList.remove('hidden');
            paymentModal.classList.add('flex');
        }

        // Function to hide modal
        function hideModal() {
            paymentModal.classList.add('hidden');
            paymentModal.classList.remove('flex');
            // Reset states
            processingState.classList.remove('hidden');
            successState.classList.add('hidden');
            errorState.classList.add('hidden');
        }

        // Handle reserve button click
        reserveButton.addEventListener('click', function(e) {
            e.preventDefault();
            showModal();

            // Make request to Arduino
            fetch('/arduino/read', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.data && data.data.includes('good')) {
                    // Payment successful
                    processingState.classList.add('hidden');
                    successState.classList.remove('hidden');

                    // Optional: Play success sound
                    new Audio('/path/to/success-sound.mp3')?.play().catch(() => {});

                    // Wait a brief moment to show success state, then submit form
                    setTimeout(() => {
                        reservationForm.submit();
                    }, 1000); // Show success state for 1 second before submitting
                } else {
                    // Payment failed
                    processingState.classList.add('hidden');
                    errorState.classList.remove('hidden');
                    // Optional: Play error sound
                    new Audio('/path/to/error-sound.mp3')?.play().catch(() => {});
                }
            })
            .catch(error => {
                processingState.classList.add('hidden');
                errorState.classList.remove('hidden');
                console.error('Error:', error);
            });
        });

        // Handle error close button
        errorClose.addEventListener('click', hideModal);

        // Close modal when clicking outside (only for error state)
        paymentModal.addEventListener('click', function(e) {
            if (e.target === paymentModal && errorState.classList.contains('hidden') === false) {
                hideModal();
            }
        });
    });
    </script>

    <script src="{{ asset('js/stadium.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/stadium.css') }}">
</x-app-layout>

<!-- Add a hidden input to store selected section ID -->
<input type="hidden" name="selected_section_id" id="selected_section_id" value="">

<!-- Payment Processing Modal -->
<div id="payment-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-xl max-w-md w-full mx-4">
        <!-- Processing State -->
        <div id="processing-state" class="text-center">
            <!-- Spinning Animation -->
            <div class="inline-block">
                <svg class="animate-spin h-16 w-16 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <div class="mt-4 space-y-3">
                <h2 class="text-xl font-semibold text-gray-700">Traitement du paiement</h2>
                <p class="text-gray-500">Veuillez patienter pendant la validation...</p>
                <div class="text-sm text-gray-400">Ne fermez pas cette fenêtre</div>
            </div>
        </div>

        <!-- Success State (will show briefly before form submission) -->
        <div id="success-state" class="text-center hidden">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
                <svg class="h-10 w-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div class="mt-4 space-y-3">
                <h2 class="text-xl font-semibold text-gray-700">Paiement réussi!</h2>
                <p class="text-gray-500">Redirection en cours...</p>
            </div>
        </div>

        <!-- Error State -->
        <div id="error-state" class="text-center hidden">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
                <svg class="h-10 w-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <div class="mt-4 space-y-3">
                <h2 class="text-xl font-semibold text-gray-700">Échec du paiement</h2>
                <p class="text-gray-500">Une erreur s'est produite lors du traitement.</p>
                <button id="error-close" class="mt-6 bg-red-500 text-white px-6 py-2 rounded-full hover:bg-red-600 transition-all duration-200 transform hover:scale-105">
                    Réessayer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add this to include Alpine.js if not already included -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
