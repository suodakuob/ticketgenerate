<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Match Details: {{ $match->home_team }} vs {{ $match->away_team }}</title>

    <!-- Add Tailwind CSS -->
    {{-- In a real app, you'd likely use compiled assets: <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Include the original inline styles -->
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
        /* Add any other global styles your layout component might have provided */
         body {
             font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Noto Color Emoji";
             line-height: 1.5;
             background-color: #f3f4f6; /* Added a default background color similar to Laravel layouts */
         }
    </style>

    <!-- Link the original stadium.css -->
    <link rel="stylesheet" href="{{ asset('css/stadium.css') }}">

    {{-- Add Pannellum CSS if needed (alternatively, load dynamically in JS) --}}
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"> --}}

</head>
<body class="antialiased"> {{-- Added antialiased class --}}

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Retourner aux matches button -->
                    <div class="mb-6">
                        <a href="{{ url('/matches') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Retourner aux matches
                        </a>
                    </div>


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
                         <!-- Add a hidden input to store selected section ID (used by stadium.js) -->
                        <input type="hidden" name="selected_section_id" id="selected_section_id" value="">
                    </div>


                    <!-- Formulaire de réservation -->
                    <div class="flex flex-col sm:flex-row items-center justify-between mt-6 space-y-4 sm:space-y-0 sm:space-x-4">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('matches.sections', $match) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                View All Sections
                            </a>

                        </div>
                        <form id="reservation-form" method="POST" action="{{ route('tickets.store') }}" class="w-full sm:w-auto flex flex-col items-center sm:items-end space-y-3">
                                @csrf
                                <input type="hidden" name="match_id" value="{{ $match->id }}">
                                <input type="hidden" id="ticketPrice" data-price="{{ $match->ticket_price }}">
                                <input type="hidden" name="section_id" id="form_section_id" value="">

                                <!-- Sélection du nombre de billets -->
                                <div class="flex items-center space-x-2">
                                    <label for="quantity" class="block text-sm font-medium text-gray-700">Quantité:</label>
                                    <button type="button" id="decrement" class="px-3 py-2 bg-gray-300 rounded" onclick="updateQuantity(-1)">-</button>
                                    <input type="number" name="quantity" id="quantity" min="1" max="10" value="1"
                                        class="text-center w-16 border rounded py-2">
                                    <button type="button" id="increment" class="px-3 py-2 bg-gray-300 rounded" onclick="updateQuantity(1)">+</button>
                                </div>

                                <!-- Prix total affiché -->
                                <p class="mt-2 text-gray-600">Prix total : <span id="totalPrice">Mad {{ number_format($match->ticket_price, 2) }}</span></p>

                                <button type="button" id="reserve-button" disabled
                                    class="w-full sm:w-auto mt-4 bg-gray-400 text-white font-bold py-3 px-4 rounded cursor-not-allowed transition disabled:opacity-50">
                                    Sélectionnez une section d'abord
                                </button>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Processing Modal -->
    <div id="payment-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50"> {{-- Increased z-index --}}
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

     <!-- 360 view Modal Structure -->
    <div id="view-360-modal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-5xl w-full h-[80vh] flex flex-col">
            <div class="bg-gray-100 px-4 py-3 flex justify-between items-center border-b">
                <h3 class="text-lg font-medium text-gray-900" id="view-360-title">360° View</h3>
                <div class="flex items-center space-x-2">
                    <button id="fullscreen-360-view" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"></path>
                        </svg>
                    </button>
                    {{-- VR button - Keep placeholder, functionality depends on Pannellum or iframe content --}}
                     <button id="vr-mode-toggle" class="text-gray-500 hover:text-gray-700 hidden"> {{-- Hidden by default, show if Pannellum is used --}}
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
                 {{-- iframe for external URLs --}}
                <iframe id="view-360-iframe" src="" allowfullscreen style="width:100%; height:100%; border:0;" class="hidden"></iframe>

                {{-- Pannellum 360 viewer --}}
                <div id="pannellum-container" class="w-full h-full hidden">
                    <div id="pannellum-viewer" class="w-full h-full"></div> {{-- Pannellum viewer div --}}
                </div>

                {{-- Fallback for images that are not 360 panoramas --}}
                <div id="view-360-image-container" class="w-full h-full hidden relative overflow-hidden"> {{-- Added overflow-hidden --}}
                    <img id="view-360-image" src="" alt="360° View" class="max-w-full max-h-full m-auto object-contain transition-transform duration-500 cursor-grab"> {{-- Added object-contain and cursor-grab --}}
                </div>
            </div>
        </div>
    </div>


    {{-- Include original inline scripts --}}
    <script>
        // Pass section data to the stadium.js script
        window.sectionData = @json($sectionData);
        window.sectionsUrl = "{{ route('matches.sections', $match) }}";

        // For backwards compatibility with any code expecting matchSectionData
        window.matchSectionData = window.sectionData;

        // Function to update quantity and recalculate total price
        function updateQuantity(change) {
            const quantityInput = document.getElementById('quantity');
            let currentValue = parseInt(quantityInput.value) || 1;
            let newValue = Math.max(1, Math.min(10, currentValue + change)); // Basic min/max 1-10

            // Get the selected section
            const sectionId = document.getElementById('form_section_id').value;
            let maxTicketsAvailable = 10; // Default max

            if (sectionId && window.sectionData && window.sectionData[sectionId]) {
                // Limit by available seats in the section if sectionData is loaded
                 maxTicketsAvailable = window.sectionData[sectionId].available_seats;
            }

            // Don't allow more tickets than available seats in the selected section (and global max 10)
            quantityInput.value = Math.min(newValue, maxTicketsAvailable);

            // Update total price
            updateTotalPrice();
        }

        // Update the total price based on quantity and section price
        function updateTotalPrice() {
            const quantity = parseInt(document.getElementById('quantity').value) || 1;
            const sectionId = document.getElementById('form_section_id').value;
            let price = parseFloat(document.getElementById('ticketPrice').dataset.price); // Default price from match

            // Use section price if a section is selected and sectionData is available
            if (sectionId && window.sectionData && window.sectionData[sectionId]) {
                price = parseFloat(window.sectionData[sectionId].price);
            }

            const totalPrice = price * quantity;
            document.getElementById('totalPrice').textContent = 'Mad ' + totalPrice.toFixed(2);
        }

        // --- 360° view functionality ---

        // Ensure Pannellum script and CSS are loaded dynamically if not already in head
        function loadPannellum() {
             return new Promise((resolve, reject) => {
                 // Check if Pannellum is already loaded
                 if (window.pannellum) {
                     resolve();
                     return;
                 }

                 // Check if scripts/links are already being added or are in head
                 const scriptExists = document.querySelector('script[src*="pannellum.js"]');
                 const linkExists = document.querySelector('link[href*="pannellum.css"]');

                 if (!linkExists) {
                     const link = document.createElement('link');
                     link.rel = 'stylesheet';
                     link.href = 'https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css';
                     document.head.appendChild(link);
                 }

                 if (!scriptExists) {
                     const script = document.createElement('script');
                     script.src = 'https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js';
                     script.onload = resolve;
                     script.onerror = reject;
                     document.head.appendChild(script);
                 } else {
                     // If script tag exists but script isn't ready yet, wait for it
                      scriptExists.onload = resolve;
                      scriptExists.onerror = reject;
                 }
             });
         }


        function open360Preview(element, url, sectionName) {
            let modal = document.getElementById('view-360-modal');
            // Modal structure is now static in the HTML, no need to create it here

            const iframe = document.getElementById('view-360-iframe');
            const imageContainer = document.getElementById('view-360-image-container');
            const pannellumContainer = document.getElementById('pannellum-container');
            const pannellumViewerElement = document.getElementById('pannellum-viewer');
             const vrButton = document.getElementById('vr-mode-toggle');
            const image = document.getElementById('view-360-image');
            const modalTitle = document.getElementById('view-360-title');

             // Reset displays
             iframe.classList.add('hidden');
             imageContainer.classList.add('hidden');
             pannellumContainer.classList.add('hidden');
             vrButton.classList.add('hidden'); // Hide VR button by default
              // Destroy previous Pannellum instance if it exists
             if (pannellumViewerElement && pannellumViewerElement.pannellumViewer) {
                 pannellumViewerElement.pannellumViewer.destroy();
                 pannellumViewerElement.pannellumViewer = null; // Clear reference
             }
             pannellumViewerElement.innerHTML = ''; // Clear DOM content

            modalTitle.textContent = `360° View - ${sectionName}`;


             // Check if URL is an image likely for Pannellum or simple viewer
            if (url.match(/\.(jpeg|jpg|gif|png|webp|tif|tiff|bmp|svg)$/i)) { // Added more image formats
                // Try loading Pannellum
                 loadPannellum().then(() => {
                    if (window.pannellum) {
                         // Check if the image is likely a panoramic equirectangular image
                         // (This is a heuristic, not foolproof)
                        const isPanoramic = url.toLowerCase().includes('360') || url.toLowerCase().includes('panorama'); // Simple check
                         // Or ideally, fetch image metadata if possible

                         if (isPanoramic) {
                             pannellumContainer.classList.remove('hidden');
                              vrButton.classList.remove('hidden'); // Show VR button for Pannellum
                             initPannellumViewer(url);
                         } else {
                             // Fallback to simple image viewer for regular images
                             imageContainer.classList.remove('hidden');
                             image.src = url;
                             initializeImageControls(); // Add pan/zoom controls
                         }
                    } else {
                         console.warn('Pannellum failed to load, using image fallback.');
                         // Fallback to simple image viewer if Pannellum isn't loaded
                         imageContainer.classList.remove('hidden');
                         image.src = url;
                         initializeImageControls(); // Add pan/zoom controls
                    }
                 }).catch(error => {
                     console.error('Error loading Pannellum:', error);
                     // Fallback to simple image viewer on Pannellum load error
                     imageContainer.classList.remove('hidden');
                     image.src = url;
                     initializeImageControls(); // Add pan/zoom controls
                 });

            } else {
                // If not a recognized image extension, assume it's a URL for an iframe (e.g., YouTube, Street View)
                iframe.classList.remove('hidden');
                iframe.src = url;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex'); // Ensure it's displayed as flex for centering
             // Add event listeners if not already set up
             if (!modal.dataset.listenersAdded) {
                 setupModalEventListeners();
                 modal.dataset.listenersAdded = 'true'; // Mark as initialized
             }
        }

        function setupModalEventListeners() {
            const modal = document.getElementById('view-360-modal');
            const closeBtn = document.getElementById('close-360-view');
            const fullscreenBtn = document.getElementById('fullscreen-360-view');
             const vrModeBtn = document.getElementById('vr-mode-toggle'); // VR button
            const viewContainer = document.getElementById('view-360-container');

            // Close modal handler
            closeBtn.addEventListener('click', function() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.getElementById('view-360-iframe').src = ''; // Stop iframe content
                document.getElementById('view-360-image').src = ''; // Clear image source

                // Destroy Pannellum viewer if it exists
                const viewerElement = document.getElementById('pannellum-viewer');
                if (viewerElement && viewerElement.pannellumViewer) {
                    viewerElement.pannellumViewer.destroy();
                    viewerElement.pannellumViewer = null; // Clear reference
                }
                 viewerElement.innerHTML = ''; // Clear the DOM

                 // Reset image controls state if fallback was used
                 resetImageControls();
            });

            // Fullscreen toggle handler
            fullscreenBtn.addEventListener('click', function() {
                if (document.fullscreenElement) {
                    document.exitFullscreen();
                } else {
                     // Request fullscreen on the modal itself or the view container
                     if (modal.requestFullscreen) {
                        modal.requestFullscreen();
                    } else if (modal.mozRequestFullScreen) { /* Firefox */
                        modal.mozRequestFullScreen();
                    } else if (modal.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
                        modal.webkitRequestFullscreen();
                    } else if (modal.msRequestFullscreen) { /* IE/Edge */
                        modal.msRequestFullscreen();
                    }
                }
            });

            // VR Mode toggle handler (Pannellum specific)
             vrModeBtn.addEventListener('click', function() {
                 const viewerElement = document.getElementById('pannellum-viewer');
                 if (viewerElement && viewerElement.pannellumViewer) {
                     viewerElement.pannellumViewer.toggleVrMode();
                 } else {
                      console.warn("VR mode not available for current viewer type.");
                 }
             });


            // Close on outside click
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeBtn.click();
                }
            });

             // Optional: Close on Escape key
             document.addEventListener('keydown', function(e) {
                 if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                     closeBtn.click();
                 }
             });
        }

        function initPannellumViewer(imageUrl) {
            // Clear any existing viewer and remove old instance
            const viewerElement = document.getElementById('pannellum-viewer');
            if (viewerElement && viewerElement.pannellumViewer) {
                viewerElement.pannellumViewer.destroy();
            }
            viewerElement.innerHTML = ''; // Clear the DOM
            viewerElement.style.display = 'block';

            // Create new viewer instance and store it
            viewerElement.pannellumViewer = pannellum.viewer('pannellum-viewer', {
                type: 'equirectangular',
                panorama: imageUrl,
                autoLoad: true,
                autoRotate: -2,
                compass: true,
                showControls: true,
                showFullscreenCtrl: false, // Using custom button
                mouseZoom: true,
                keyboardZoom: true,
                // Add other Pannellum options as needed
                // Example: 'hotSpots': []
            });
        }

        // --- Image Controls for Fallback Viewer ---
        // Variables for image manipulation (used if not using Pannellum or iframe)
        let imageTranslateX = 0;
        let imageTranslateY = 0;
        let imageScale = 1;
        let isImageDragging = false;
        let imageStartX, imageStartY;
        const imageElement = document.getElementById('view-360-image');
        const imageViewContainer = document.getElementById('view-360-container'); // Container for wheel events


        function initializeImageControls() {
             if (!imageElement || !imageViewContainer) return;

             // Remove previous listeners to prevent duplicates
             imageElement.removeEventListener('mousedown', startImageDrag);
             window.removeEventListener('mousemove', dragImage);
             window.removeEventListener('mouseup', endImageDrag);
             imageViewContainer.removeEventListener('wheel', handleImageWheel);
             imageElement.removeEventListener('dblclick', resetImageControls); // Remove double-click

             // Add drag functionality
             imageElement.addEventListener('mousedown', startImageDrag);
             window.addEventListener('mousemove', dragImage);
             window.addEventListener('mouseup', endImageDrag);

             // Add zoom on mousewheel
             imageViewContainer.addEventListener('wheel', handleImageWheel);

             // Add double-click to reset
             imageElement.addEventListener('dblclick', resetImageControls);

             // Set initial cursor
             imageElement.style.cursor = 'grab';
        }

        function resetImageControls() {
             if (!imageElement) return;
             imageTranslateX = 0;
             imageTranslateY = 0;
             imageScale = 1;
             updateImageTransform();
             imageElement.style.cursor = 'grab';
        }

        function startImageDrag(e) {
             if (e.button !== 0) return; // Only drag with left mouse button
             e.preventDefault();
             isImageDragging = true;
             imageStartX = e.clientX - imageTranslateX;
             imageStartY = e.clientY - imageTranslateY;
             imageElement.style.cursor = 'grabbing'; // Visual feedback
        }

        function dragImage(e) {
             if (!isImageDragging) return;
             e.preventDefault();
             imageTranslateX = e.clientX - imageStartX;
             imageTranslateY = e.clientY - imageStartY;
             updateImageTransform();
        }

        function endImageDrag() {
             isImageDragging = false;
             if(imageElement) {
                 imageElement.style.cursor = (imageScale > 1 || (imageTranslateX !== 0 || imageTranslateY !== 0)) ? 'grab' : 'default'; // Visual feedback
             }
        }

        function handleImageWheel(e) {
             e.preventDefault();
             const delta = e.deltaY > 0 ? -0.1 : 0.1;
             zoomImage(delta);
        }

        function updateImageTransform() {
             if (imageElement) {
                 imageElement.style.transform = `translate(${imageTranslateX}px, ${imageTranslateY}px) scale(${imageScale})`;
                  // Update cursor based on state
                  if (!isImageDragging) {
                       if (imageScale > 1.01 || Math.abs(imageTranslateX) > 5 || Math.abs(imageTranslateY) > 5) { // Threshold for showing grab cursor
                           imageElement.style.cursor = 'grab';
                       } else {
                           imageElement.style.cursor = 'default';
                       }
                  }
             }
        }

        function zoomImage(factor) {
             imageScale = Math.max(0.1, Math.min(10, imageScale + factor)); // Limit zoom range
             updateImageTransform();
        }

        // --- Payment Modal Script ---
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
                 // Reset states to processing whenever shown
                processingState.classList.remove('hidden');
                successState.classList.add('hidden');
                errorState.classList.add('hidden');
            }

            // Function to hide modal
            function hideModal() {
                paymentModal.classList.add('hidden');
                paymentModal.classList.remove('flex');
                // Note: States are reset in showModal before next use
            }

            // Handle reserve button click
            reserveButton.addEventListener('click', function(e) {
                e.preventDefault();

                // Check if a section is actually selected before showing modal
                const sectionId = document.getElementById('form_section_id').value;
                if (!sectionId) {
                     alert('Veuillez sélectionner une section d\'abord.'); // Simple alert
                     return;
                }

                 // Also check if quantity is valid
                 const quantity = parseInt(document.getElementById('quantity').value);
                 if (isNaN(quantity) || quantity < 1 || quantity > 10) {
                     alert('Veuillez entrer une quantité valide (entre 1 et 10).');
                     return;
                 }
                  if (window.sectionData && window.sectionData[sectionId] && quantity > window.sectionData[sectionId].available_seats) {
                      alert(`Il n'y a que ${window.sectionData[sectionId].available_seats} billets disponibles pour cette section.`);
                      return;
                  }


                showModal();

                // Make request to Arduino (Simulated)
                // This fetch call simulates the payment process.
                // A real-world application would involve server-side payment processing.
                fetch('/arduino/read', { // Replace with your actual Arduino endpoint
                    method: 'GET', // Use GET for simple reading, POST if sending data
                    headers: {
                        'Accept': 'application/json',
                        // 'Content-Type': 'application/json', // If sending data
                        // 'X-CSRF-TOKEN': '{{ csrf_token() }}' // If using POST and CSRF protected routes
                    },
                     // body: JSON.stringify({ section_id: sectionId, quantity: quantity }) // Example for POST
                })
                .then(response => {
                     if (!response.ok) {
                         // Handle HTTP errors (e.g., 404, 500)
                         throw new Error(`HTTP error! status: ${response.status}`);
                     }
                     return response.json();
                 })
                .then(data => {
                    console.log('Arduino response:', data); // Log response

                    // Assuming 'good' in data.data indicates success from Arduino
                    // Adjust the condition based on your actual Arduino output format
                    const arduinoSuccess = data.status === 'success' && data.data &&
                                           typeof data.data === 'string' && data.data.toLowerCase().includes('good');

                    if (arduinoSuccess) {
                        // Payment successful (based on Arduino response)
                        processingState.classList.add('hidden');
                        successState.classList.remove('hidden');

                        // Optional: Play success sound (ensure path is correct)
                        // new Audio('/path/to/success-sound.mp3')?.play().catch(e => console.error("Error playing sound:", e));

                        // Wait a brief moment to show success state, then submit form
                        setTimeout(() => {
                            reservationForm.submit(); // Submit the reservation form
                        }, 1500); // Show success state for 1.5 seconds before submitting
                    } else {
                        // Payment failed (Arduino response not indicating 'good')
                        processingState.classList.add('hidden');
                        errorState.classList.remove('hidden');
                        console.error('Payment simulation failed:', data); // Log failure details
                        // Optional: Play error sound (ensure path is correct)
                        // new Audio('/path/to/error-sound.mp3')?.play().catch(e => console.error("Error playing sound:", e));
                    }
                })
                .catch(error => {
                    console.error('Fetch Error:', error); // Log fetch error (network issues, server error, etc.)
                    processingState.classList.add('hidden');
                    errorState.classList.remove('hidden');
                    // Optional: Play error sound
                    // new Audio('/path/to/error-sound.mp3')?.play().catch(e => console.error("Error playing sound:", e));
                });
            });

            // Handle error close button ("Réessayer")
            errorClose.addEventListener('click', function() {
                 // Simply hide the modal. User can click the main reserve button again.
                 hideModal();
                 // If you want "Réessayer" to re-trigger the Arduino call,
                 // uncomment the line below, but be mindful of potential loops.
                 // reserveButton.click();
            });


            // Close modal when clicking outside
             paymentModal.addEventListener('click', function(e) {
                 // Close only if the click target is the modal background itself
                 if (e.target === paymentModal) {
                     // Allow closing only if it's the error state
                     if (!errorState.classList.contains('hidden')) {
                         hideModal();
                     }
                     // Prevent closing during processing or success by stopping propagation
                     else if (!processingState.classList.contains('hidden') || !successState.classList.contains('hidden')) {
                         e.stopPropagation();
                     }
                 }
             });

              // Optional: Close on Escape key for the payment modal (if not handled globally)
              document.addEventListener('keydown', function(e) {
                 if (e.key === 'Escape' && !paymentModal.classList.contains('hidden') && !processingState.classList.contains('hidden')) {
                      // Prevent closing with escape during processing (optional)
                      e.stopPropagation();
                 } else if (e.key === 'Escape' && !paymentModal.classList.contains('hidden') && !errorState.classList.contains('hidden')) {
                      // Allow closing error state with escape
                      hideModal();
                 }
              });

               // Initial call to set total price based on default quantity (1) and base match price
               updateTotalPrice();
        });
    </script>


    {{-- Include external stadium.js --}}
    <script src="{{ asset('js/stadium.js') }}"></script>

    {{-- Add this to include Alpine.js (used for transition-all and other utilities potentially) --}}
    {{-- Added 'defer' attribute to ensure it runs after the DOM is parsed --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</body>
</html>