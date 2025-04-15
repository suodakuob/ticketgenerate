<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Stadium Sections') }} - {{ $match->name }}
            </h2>
            <a href="{{ route('matches.show', $match) }}"
               class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                {{ __('Back to Match') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Match Details</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Teams</p>
                                <p class="font-medium">{{ $match->home_team }} vs {{ $match->away_team }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Date & Time</p>
                                <p class="font-medium">{{ $match->match_date->format('M d, Y - H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Stadium</p>
                                <p class="font-medium">{{ $match->stadium }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Status</p>
                                <p class="font-medium">{{ ucfirst($match->match_status) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Available Sections</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available Seats</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">360° View</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($sections as $section)
                                    @if($section->is_active && $section->available_seats > 0)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div class="font-medium">{{ $section->name }}</div>
                                                <div class="text-sm text-gray-500">ID: {{ $section->section_id }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $section->section_type == 'Standard' ? 'bg-blue-100 text-blue-800' :
                                                    ($section->section_type == 'VIP' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800') }}">
                                                    {{ $section->section_type }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $section->available_seats }} / {{ $section->capacity }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                £{{ number_format($section->price, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($section->view_360_url)
                                                    <button type="button"
                                                        class="view-360-btn px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 inline-flex items-center text-xs"
                                                        data-url="{{ $section->view_360_url }}"
                                                        data-section-name="{{ $section->name }}">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        View 360°
                                                    </button>
                                                @else
                                                    <span class="text-gray-400">Not available</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <button onclick="openPurchaseModal('{{ $section->section_id }}', '{{ $section->name }}', '{{ $section->price }}')"
                                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                                    Buy Ticket
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($sections->where('is_active', true)->where('available_seats', '>', 0)->count() == 0)
                        <div class="mt-4 bg-yellow-50 border border-yellow-400 text-yellow-700 p-4 rounded">
                            <p>No sections with available seats for this match.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Modal -->
    <div id="purchaseModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-lg w-full">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Purchase Tickets</h3>
            </div>

            <form method="POST" action="{{ route('tickets.purchase', $match) }}">
                @csrf
                <div class="px-4 py-5">
                    <input type="hidden" id="section_id" name="section_id">

                    <div class="mb-4">
                        <p class="font-medium text-gray-700" id="sectionName"></p>
                    </div>

                    <div class="mb-4">
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Number of Tickets</label>
                        <input type="number" id="quantity" name="quantity" min="1" max="10" value="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600">Price per Ticket:</p>
                        <p class="font-medium" id="ticketPrice"></p>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-md">
                        <p class="text-sm text-gray-600">Total Price:</p>
                        <p class="font-bold text-lg" id="totalPrice"></p>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 flex justify-end space-x-3">
                    <button type="button" onclick="closePurchaseModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Confirm Purchase
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 360 View Modal -->
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

                    <!-- Controls -->
                    <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-3">
                        <button class="bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70" onclick="rotateImage('left', 90)">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm9 0h-2c-.55 0-1 .45-1 1s.45 1 1 1h2c.55 0 1-.45 1-1s-.45-1-1-1z"></path>
                            </svg>
                        </button>
                        <button class="bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70" onclick="resetImageRotation()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                        <button class="bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70" onclick="rotateImage('right', 90)">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm9 0h-2c-.55 0-1 .45-1 1s.45 1 1 1h2c.55 0 1-.45 1-1s-.45-1-1-1z"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Zoom controls -->
                    <div class="absolute top-4 right-4 flex flex-col space-y-2">
                        <button class="bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70" onclick="zoomImage(0.1)">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                        <button class="bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70" onclick="zoomImage(-0.1)">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Pannellum for 360 viewing -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css">
    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>

    <style>
        #pannellum-viewer {
            width: 100%;
            height: 100%;
            display: none;
        }

        /* VR mode styles */
        .vr-mode #pannellum-viewer {
            display: flex !important;
        }

        .vr-mode-active {
            background-color: #4CAF50 !important;
            color: white !important;
        }
    </style>

    <script>
        function openPurchaseModal(sectionId, sectionName, price) {
            document.getElementById('section_id').value = sectionId;
            document.getElementById('sectionName').textContent = sectionName;
            document.getElementById('ticketPrice').textContent = '£' + parseFloat(price).toFixed(2);
            updateTotalPrice(price);

            document.getElementById('purchaseModal').classList.remove('hidden');

            // Add event listener to quantity input
            document.getElementById('quantity').addEventListener('input', function() {
                updateTotalPrice(price);
            });
        }

        function closePurchaseModal() {
            document.getElementById('purchaseModal').classList.add('hidden');
        }

        function updateTotalPrice(price) {
            const quantity = document.getElementById('quantity').value;
            const total = parseFloat(price) * parseInt(quantity);
            document.getElementById('totalPrice').textContent = '£' + total.toFixed(2);
        }

        // 360° View functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Get all 360° view buttons
            const view360Btns = document.querySelectorAll('.view-360-btn');
            const modal = document.getElementById('view-360-modal');
            const closeBtn = document.getElementById('close-360-view');
            const fullscreenBtn = document.getElementById('fullscreen-360-view');
            const vrModeBtn = document.getElementById('vr-mode-toggle');
            const viewContainer = document.getElementById('view-360-container');

            // Set up click handlers for all 360° buttons
            view360Btns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const url = this.getAttribute('data-url');
                    const sectionName = this.getAttribute('data-section-name');
                    open360Preview(this, url, sectionName);
                });
            });

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

                resetViewState();
            });

            // Fullscreen toggle handler
            fullscreenBtn.addEventListener('click', function() {
                if (document.fullscreenElement) {
                    document.exitFullscreen();
                } else {
                    viewContainer.requestFullscreen();
                }
            });

            // VR mode toggle
            vrModeBtn.addEventListener('click', function() {
                toggleVRMode();
            });

            // Close on outside click
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeBtn.click();
                }
            });
        });

        // Open 360° preview
        function open360Preview(element, url, sectionName) {
            const modal = document.getElementById('view-360-modal');
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
                    // Use Pannellum for 360 viewing (more reliable)
                    pannellumContainer.classList.remove('hidden');
                    imageContainer.classList.add('hidden');

                    // Initialize Pannellum
                    initPannellumViewer(url);
                } catch (error) {
                    console.error('Pannellum error:', error);
                    // Fallback to simple image viewer
                    pannellumContainer.classList.add('hidden');
                    imageContainer.classList.remove('hidden');
                    image.src = url;
                    initializeImageControls();
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

        // Initialize Pannellum 360 viewer
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

        // Toggle VR mode
        function toggleVRMode() {
            const container = document.getElementById('view-360-container');
            const vrBtn = document.getElementById('vr-mode-toggle');

            if (container.classList.contains('vr-mode')) {
                container.classList.remove('vr-mode');
                vrBtn.classList.remove('vr-mode-active');
            } else {
                container.classList.add('vr-mode');
                vrBtn.classList.add('vr-mode-active');

                // Initialize or reinitialize Pannellum in VR mode if visible
                const pannellumContainer = document.getElementById('pannellum-container');
                if (!pannellumContainer.classList.contains('hidden')) {
                    const currentViewer = document.querySelector('.pnlm-container');
                    if (currentViewer) {
                        const currentImage = currentViewer.querySelector('canvas').toDataURL();
                        initPannellumViewerVR(currentImage);
                    }
                }
            }
        }

        // Initialize Pannellum in VR mode
        function initPannellumViewerVR(imageUrl) {
            // Clear existing viewer
            const viewerElement = document.getElementById('pannellum-viewer');
            viewerElement.innerHTML = '';

            // Create new viewer with VR settings
            pannellum.viewer('pannellum-viewer', {
                type: 'equirectangular',
                panorama: imageUrl,
                autoLoad: true,
                compass: true,
                showControls: true,
                mouseZoom: true,
                keyboardZoom: true,
                hfov: 110, // wider field of view for VR
                vOffset: 0,
                autoRotate: -1,
                autoLoad: true,
                hotSpotDebug: false,
                showFullscreenCtrl: false
            });
        }

        // Image manipulation functions (fallback)
        let currentRotation = 0;
        let currentScale = 1;
        let isDragging = false;
        let startX, startY, translateX = 0, translateY = 0;

        function initializeImageControls() {
            resetViewState();

            const image = document.getElementById('view-360-image');
            const viewContainer = document.getElementById('view-360-container');

            // Add drag functionality
            image.addEventListener('mousedown', startDrag);
            window.addEventListener('mousemove', drag);
            window.addEventListener('mouseup', endDrag);

            // Add touch support
            image.addEventListener('touchstart', handleTouchStart);
            window.addEventListener('touchmove', handleTouchMove);
            window.addEventListener('touchend', handleTouchEnd);

            // Add zoom on mousewheel
            viewContainer.addEventListener('wheel', handleWheel);
        }

        function resetViewState() {
            currentRotation = 0;
            currentScale = 1;
            translateX = 0;
            translateY = 0;
            updateImageTransform();
        }

        function updateImageTransform() {
            const image = document.getElementById('view-360-image');
            if (image) {
                image.style.transform = `translate(${translateX}px, ${translateY}px) rotate(${currentRotation}deg) scale(${currentScale})`;
            }
        }

        function rotateImage(direction, degrees) {
            const step = direction === 'left' ? -degrees : degrees;
            currentRotation += step;
            updateImageTransform();
        }

        function resetImageRotation() {
            currentRotation = 0;
            updateImageTransform();
        }

        function zoomImage(factor) {
            currentScale = Math.max(0.5, Math.min(5, currentScale + factor));
            updateImageTransform();
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

        function handleTouchStart(e) {
            if (e.touches.length === 1) {
                e.stopPropagation();
                isDragging = true;
                startX = e.touches[0].clientX - translateX;
                startY = e.touches[0].clientY - translateY;
            }
        }

        function handleTouchMove(e) {
            if (!isDragging || e.touches.length !== 1) return;
            e.preventDefault();
            translateX = e.touches[0].clientX - startX;
            translateY = e.touches[0].clientY - startY;
            updateImageTransform();
        }

        function handleTouchEnd() {
            isDragging = false;
        }

        function handleWheel(e) {
            e.preventDefault();
            const delta = e.deltaY > 0 ? -0.1 : 0.1;
            zoomImage(delta);
        }
    </script>
</x-app-layout>
