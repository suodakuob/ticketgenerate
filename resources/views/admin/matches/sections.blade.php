<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Sections') }} - {{ $match->name }}
            </h2>
            <a href="{{ route('admin.matches.index') }}"
               class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('Back to Matches') }}
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

                <form method="POST" action="{{ route('admin.matches.sections.store', $match) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Existing Sections</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section ID</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">360° URL</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($sections as $index => $section)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <input type="text" name="sections[{{ $index }}][section_id]" value="{{ $section->section_id }}"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                    readonly>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <input type="text" name="sections[{{ $index }}][name]" value="{{ $section->name }}"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                    required>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <input type="number" name="sections[{{ $index }}][capacity]" value="{{ $section->capacity }}"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                    required min="1">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <input type="number" name="sections[{{ $index }}][available_seats]" value="{{ $section->available_seats }}"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                    required min="0">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <input type="number" name="sections[{{ $index }}][price]" value="{{ $section->price }}"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                    required min="0" step="0.01">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <select name="sections[{{ $index }}][section_type]"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                    required>
                                                    <option value="Standard" {{ $section->section_type == 'Standard' ? 'selected' : '' }}>Standard</option>
                                                    <option value="VIP" {{ $section->section_type == 'VIP' ? 'selected' : '' }}>VIP</option>
                                                    <option value="Premium" {{ $section->section_type == 'Premium' ? 'selected' : '' }}>Premium</option>
                                                </select>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <input type="hidden" name="sections[{{ $index }}][view_360_url]" value="{{ $section->view_360_url }}">

                                                <div class="mt-2 flex flex-col items-center">
                                                    @if($section->view_360_url)
                                                        <div class="mb-2 relative w-full bg-gray-100 rounded-lg p-2 flex items-center justify-center">
                                                            <img src="{{ asset('images/360-icon.svg') }}" alt="360 Preview" class="w-16 h-16 cursor-pointer hover:opacity-80 open-360-view"
                                                                data-url="{{ $section->view_360_url }}" data-section-name="{{ $section->name }}" onclick="open360Preview(this)">
                                                            <div class="ml-2 flex flex-col flex-grow">
                                                                <span class="text-xs text-gray-500">{{ Str::limit($section->view_360_url, 25) }}</span>
                                                                {{-- <button type="button" class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 inline-flex items-center" onclick="open360Preview(this)" data-url="{{ $section->view_360_url }}" data-section-name="{{ $section->name }}">
                                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                                    </svg>
                                                                    View 360°
                                                                </button> --}}
                                                            </div>
                                                            <div class="flex space-x-2">
                                                                <button type="button"
                                                                    class="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700"
                                                                    onclick="uploadView360Image({{ $index }})">
                                                                    Change
                                                                </button>
                                                                {{-- <button type="button"
                                                                    class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700"
                                                                    onclick="clearView360Url(this, {{ $index }})">
                                                                    ✕
                                                                </button> --}}
                                                            </div>
                                                        </div>
                                                    @else
                                                        <button type="button"
                                                            onclick="uploadView360Image({{ $index }})"
                                                            class="w-full px-4 py-2 bg-blue-50 border border-blue-200 text-blue-600 rounded-md hover:bg-blue-100 flex items-center justify-center">
                                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                            Upload 360° Image
                                                        </button>
                                                    @endif
                                                    <input type="file" id="view-360-upload-{{ $index }}"
                                                        class="hidden" accept="image/*"
                                                        onchange="handleView360Upload(this, {{ $index }})">
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <input type="checkbox" name="sections[{{ $index }}][is_active]" {{ $section->is_active ? 'checked' : '' }}
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Sections</h3>

                        <div id="new-sections-container">
                            <!-- New sections will be added here by JavaScript -->
                        </div>

                        <button type="button" id="add-section-btn"
                            class="mt-4 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            Add Section
                        </button>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
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
                    <button id="close-360-view" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="flex-grow relative" id="view-360-container">
                <iframe id="view-360-iframe" src="" allowfullscreen style="width:100%; height:100%; border:0;"></iframe>

                <!-- Three.js container -->
                <div id="three-js-container" class="w-full h-full hidden">
                    <!-- Loading indicator -->
                    <div id="loading-indicator" class="absolute inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-10">
                        <div class="animate-spin rounded-full h-32 w-32 border-t-2 border-b-2 border-blue-500"></div>
                    </div>
                    <!-- Controls overlay -->
                    <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-4 z-10">
                        <button class="bg-black bg-opacity-60 text-white p-3 rounded-full hover:bg-opacity-80" id="reset-camera">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                        <button class="bg-black bg-opacity-60 text-white p-3 rounded-full hover:bg-opacity-80" id="zoom-in">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                        <button class="bg-black bg-opacity-60 text-white p-3 rounded-full hover:bg-opacity-80" id="zoom-out">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Pannellum 360 viewer (more reliable) -->
                <div id="pannellum-container" class="w-full h-full hidden">
                    <div id="pannellum-viewer"></div>
                </div>

                <!-- Fallback for images (when both viewers fail) -->
                <div id="view-360-image-container" class="w-full h-full hidden relative">
                    <img id="view-360-image" src="" alt="360° View" class="max-w-full max-h-full m-auto transition-transform duration-500">

                    <!-- Controls -->
                    <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-3">
                        <button class="bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70" onclick="rotateImage('left', 90)">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm9 0h-2c-.55 0-1 .45-1 1s.45 1 1 1h2c.55 0 1-.45 1-1s-.45-1-1-1zm-17 0H2c-.55 0-1 .45-1 1s.45 1 1 1h2c.55 0 1-.45 1-1s-.45-1-1-1zm15.5-4.5c-.39-.39-1.02-.39-1.41 0l-1.42 1.42c-.39.39-.39 1.02 0 1.41.39.39 1.02.39 1.41 0l1.42-1.42c.39-.38.39-1.01 0-1.41zM6.41 4.93L5 6.34c-.39.39-.39 1.02 0 1.41.39.39 1.02.39 1.41 0l1.42-1.42c.39-.38.39-1.01 0-1.4-.39-.39-1.02-.39-1.42 0zm13.59 14.48l-1.42-1.42c-.39-.39-1.02-.39-1.41 0-.39.39-.39 1.02 0 1.41l1.42 1.42c.39.39 1.02.39 1.41 0 .39-.39.39-1.02 0-1.41zm-16-1.42l-1.42 1.42c-.39.39-.39 1.02 0 1.41.39.39 1.02.39 1.41 0l1.42-1.42c.39-.39.39-1.02 0-1.41-.39-.38-1.02-.38-1.41 0zM12 20c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path>
                            </svg>
                        </button>
                        <button class="bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70" onclick="resetImageRotation()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                        <button class="bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70" onclick="rotateImage('right', 90)">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm9 0h-2c-.55 0-1 .45-1 1s.45 1 1 1h2c.55 0 1-.45 1-1s-.45-1-1-1zm-17 0H2c-.55 0-1 .45-1 1s.45 1 1 1h2c.55 0 1-.45 1-1s-.45-1-1-1zm15.5-4.5c-.39-.39-1.02-.39-1.41 0l-1.42 1.42c-.39.39-.39 1.02 0 1.41.39.39 1.02.39 1.41 0l1.42-1.42c.39-.38.39-1.01 0-1.41zM6.41 4.93L5 6.34c-.39.39-.39 1.02 0 1.41.39.39 1.02.39 1.41 0l1.42-1.42c.39-.38.39-1.01 0-1.4-.39-.39-1.02-.39-1.42 0zm13.59 14.48l-1.42-1.42c-.39-.39-1.02-.39-1.41 0-.39.39-.39 1.02 0 1.41l1.42 1.42c.39.39 1.02.39 1.41 0 .39-.39.39-1.02 0-1.41zm-16-1.42l-1.42 1.42c-.39.39-.39 1.02 0 1.41.39.39 1.02.39 1.41 0l1.42-1.42c.39-.39.39-1.02 0-1.41-.39-.38-1.02-.38-1.41 0zM12 20c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path>
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

    <!-- Load Three.js libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>

    <!-- Add Pannellum for more reliable 360 viewing -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css">
    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>

    <style>
        #pannellum-viewer {
            width: 100%;
            height: 100%;
            display: none;
        }
    </style>

    <script>
        // Define global 360° view functions to make them accessible from HTML
        function open360Preview(element) {
            const modal = document.getElementById('view-360-modal');
            const iframe = document.getElementById('view-360-iframe');
            const imageContainer = document.getElementById('view-360-image-container');
            const threeJsContainer = document.getElementById('three-js-container');
            const pannellumContainer = document.getElementById('pannellum-container');
            const loadingIndicator = document.getElementById('loading-indicator');
            const image = document.getElementById('view-360-image');
            const modalTitle = document.getElementById('view-360-title');

            const url = element.getAttribute('data-url') || element.closest('[data-url]').getAttribute('data-url');
            const sectionName = element.getAttribute('data-section-name') || element.closest('[data-section-name]').getAttribute('data-section-name');

            modalTitle.textContent = `360° View - ${sectionName}`;

            // Check if URL is an image
            if (url.match(/\.(jpeg|jpg|gif|png)$/i)) {
                iframe.classList.add('hidden');
                threeJsContainer.classList.add('hidden');

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
                threeJsContainer.classList.add('hidden');
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
                compass: false,
                showControls: true,
                showFullscreenCtrl: false,
                mouseZoom: true,
                keyboardZoom: true
            });
        }

        // Check if WebGL is available
        function isWebGLAvailable() {
            try {
                const canvas = document.createElement('canvas');
                return !!(window.WebGLRenderingContext &&
                    (canvas.getContext('webgl') || canvas.getContext('experimental-webgl')));
            } catch (e) {
                return false;
            }
        }

        function uploadView360Image(index) {
            const fileInput = document.getElementById(`view-360-upload-${index}`);
            if (!fileInput) {
                console.error(`File input with ID view-360-upload-${index} not found`);
                return;
            }
            fileInput.click();
        }

        function handleView360Upload(fileInput, index) {
            const file = fileInput.files[0];
            if (!file) return;

            console.log('Starting upload for file:', file.name, 'index:', index);

            // Find parent container and ensure it exists
            const container = fileInput.closest('div');
            if (!container) {
                console.error('Container element not found');
                return;
            }

            const parentElement = container.parentElement;
            if (!parentElement) {
                console.error('Parent element not found');
                return;
            }

            const urlInput = parentElement.querySelector('input[type="hidden"]');
            if (!urlInput) {
                console.error('URL input element not found');
                return;
            }

            console.log('Found hidden input element:', urlInput);

            // Create loading indicator
            const loadingElement = document.createElement('div');
            loadingElement.className = 'w-full flex items-center justify-center p-4 bg-gray-50 rounded-lg';
            loadingElement.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Uploading image...</span>
            `;

            // Show loading
            container.innerHTML = '';
            container.appendChild(loadingElement);

            // Save a reference to the file input for potential reuse
            const fileInputId = fileInput.id;

            // Prepare form data
            const formData = new FormData();
            formData.append('file', file);
            formData.append('section_index', index);
            formData.append('_token', '{{ csrf_token() }}');

            console.log('Preparing to upload to:', '{{ route("admin.sections.upload360", $match) }}');

            // Upload file
            fetch('{{ route("admin.sections.upload360", $match) }}', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Upload response:', data);
                if (data.success) {
                    // Update URL input
                    urlInput.value = data.url;
                    console.log('Set URL input value to:', data.url);

                    // Mark input as changed to make sure it gets submitted with the form
                    const event = new Event('change', { bubbles: true });
                    urlInput.dispatchEvent(event);
                    console.log('Dispatched change event');

                    // Store the URL in a data attribute for easier access
                    urlInput.setAttribute('data-original-url', data.url);

                    // Update preview
                    container.innerHTML = `
                        <div class="mb-2 relative w-full bg-gray-100 rounded-lg p-2 flex items-center justify-center">
                            <img src="{{ asset('images/360-icon.svg') }}" alt="360 Preview" class="w-16 h-16 cursor-pointer hover:opacity-80 open-360-view"
                                data-url="${data.url}" data-section-name="Section Preview">
                            <div class="ml-2 flex flex-col flex-grow">
                                <span class="text-xs text-gray-500">${data.url.substring(0, 25)}${data.url.length > 25 ? '...' : ''}</span>
                                <button type="button" class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 inline-flex items-center" onclick="open360Preview(this)" data-url="${data.url}" data-section-name="Section Preview">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View 360°
                                </button>
                            </div>
                            <div class="flex space-x-2">
                                <button type="button"
                                    class="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700"
                                    onclick="uploadView360Image(${index})">
                                    Change
                                </button>
                                <button type="button"
                                    class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700"
                                    onclick="clearView360Url(this, ${index})">
                                    ✕
                                </button>
                            </div>
                        </div>
                        <input type="file" id="${fileInputId}"
                            class="hidden" accept="image/*"
                            onchange="handleView360Upload(this, ${index})">
                    `;

                    // Reattach event listeners
                    document.querySelectorAll('.open-360-view').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            open360Preview(this);
                        });
                    });
                } else {
                    // Show error
                    console.error('Upload failed:', data.message);
                    container.innerHTML = `
                        <div class="p-2 bg-red-50 text-red-600 rounded-lg mb-2">Upload failed: ${data.message || 'Unknown error'}</div>
                        <button type="button"
                            onclick="uploadView360Image(${index})"
                            class="w-full px-4 py-2 bg-blue-50 border border-blue-200 text-blue-600 rounded-md hover:bg-blue-100 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Try Again
                        </button>
                        <input type="file" id="${fileInputId}"
                            class="hidden" accept="image/*"
                            onchange="handleView360Upload(this, ${index})">
                    `;
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                // Show error
                container.innerHTML = `
                    <div class="p-2 bg-red-50 text-red-600 rounded-lg mb-2">Upload error: ${error.message || 'Unknown error'}</div>
                    <button type="button"
                        onclick="uploadView360Image(${index})"
                        class="w-full px-4 py-2 bg-blue-50 border border-blue-200 text-blue-600 rounded-md hover:bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Try Again
                    </button>
                    <input type="file" id="${fileInputId}"
                        class="hidden" accept="image/*"
                        onchange="handleView360Upload(this, ${index})">
                `;
            });
        }

        function clearView360Url(button, index) {
            console.log('Clearing 360 URL for index:', index);

            const container = button.closest('div').parentElement;
            if (!container) {
                console.error('Container element not found');
                return;
            }

            const parentElement = container.parentElement;
            if (!parentElement) {
                console.error('Parent element not found');
                return;
            }

            const urlInput = parentElement.querySelector('input[type="hidden"]');
            if (!urlInput) {
                console.error('URL input element not found');
                return;
            }

            console.log('Current URL value:', urlInput.value);

            // Clear the value
            urlInput.value = '';

            // Generate a unique ID for the file input
            const fileInputId = `view-360-upload-${index}`;

            console.log('Cleared URL value, file input ID:', fileInputId);

            // Replace with upload button
            container.innerHTML = `
                <button type="button"
                    onclick="uploadView360Image(${index})"
                    class="w-full px-4 py-2 bg-blue-50 border border-blue-200 text-blue-600 rounded-md hover:bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Upload 360° Image
                </button>
                <input type="file" id="${fileInputId}"
                    class="hidden" accept="image/*"
                    onchange="handleView360Upload(this, ${index})">
            `;

            // Trigger change event on the hidden input to ensure form submission notices
            const event = new Event('change', { bubbles: true });
            urlInput.dispatchEvent(event);
            console.log('Dispatched change event after clearing URL');
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
            image.style.transform = `translate(${translateX}px, ${translateY}px) rotate(${currentRotation}deg) scale(${currentScale})`;
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
                // Don't prevent default on iOS as it causes issues
                // Just stop propagation to prevent other handlers
                e.stopPropagation();

                isDragging = true;
                startX = e.touches[0].clientX - translateX;
                startY = e.touches[0].clientY - translateY;
            }
        }

        function handleTouchMove(e) {
            if (!isDragging || e.touches.length !== 1) return;

            // Prevent scrolling only when dragging
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

        document.addEventListener('DOMContentLoaded', function() {
            const newSectionsContainer = document.getElementById('new-sections-container');
            const addButton = document.getElementById('add-section-btn');
            let newSectionIndex = {{ count($sections) }};

            // Get the form element and add a submit listener to check for uploads
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                // Log form data before submit for debugging
                const formData = new FormData(form);
                for (let [key, value] of formData.entries()) {
                    if (key.includes('view_360_url')) {
                        console.log('Form data:', key, value);
                    }
                }

                // Show success message when form is submitted
                console.log('Form submitted!');
            });

            // Setup modal close button
            document.getElementById('close-360-view').addEventListener('click', function() {
                const modal = document.getElementById('view-360-modal');
                modal.classList.add('hidden');

                // Stop any 360 players
                document.getElementById('view-360-iframe').src = '';
                document.getElementById('view-360-image').src = '';

                // Destroy Pannellum viewer if it exists
                const viewer = document.getElementById('pannellum-viewer');
                if (viewer && viewer.firstChild) {
                    viewer.innerHTML = '';
                }

                resetViewState();
            });

            addButton.addEventListener('click', function() {
                const newSection = `
                    <div class="grid grid-cols-8 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Section ID</label>
                            <input type="text" name="sections[${newSectionIndex}][section_id]"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="sections[${newSectionIndex}][name]"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Capacity</label>
                            <input type="number" name="sections[${newSectionIndex}][capacity]"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required min="1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Available</label>
                            <input type="number" name="sections[${newSectionIndex}][available_seats]"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required min="0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Price</label>
                            <input type="number" name="sections[${newSectionIndex}][price]"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required min="0" step="0.01">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type</label>
                            <select name="sections[${newSectionIndex}][section_type]"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required>
                                <option value="Standard">Standard</option>
                                <option value="VIP">VIP</option>
                                <option value="Premium">Premium</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">360° URL</label>
                            <input type="hidden" name="sections[${newSectionIndex}][view_360_url]">
                            <div class="mt-2 flex flex-col items-center">
                                <button type="button"
                                    onclick="uploadView360Image(${newSectionIndex})"
                                    class="w-full px-4 py-2 bg-blue-50 border border-blue-200 text-blue-600 rounded-md hover:bg-blue-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Upload 360° Image
                                </button>
                                <input type="file" id="view-360-upload-${newSectionIndex}"
                                    class="hidden" accept="image/*"
                                    onchange="handleView360Upload(this, ${newSectionIndex})">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Active</label>
                            <input type="checkbox" name="sections[${newSectionIndex}][is_active]" checked
                                class="mt-2 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
                        </div>
                    </div>
                `;

                newSectionsContainer.insertAdjacentHTML('beforeend', newSection);
                newSectionIndex++;
            });

            // Modal elements
            const modal = document.getElementById('view-360-modal');
            const closeBtn = document.getElementById('close-360-view');
            const fullscreenBtn = document.getElementById('fullscreen-360-view');
            const viewContainer = document.getElementById('view-360-container');

            // Close modal on outside click
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                    document.getElementById('view-360-iframe').src = '';
                    document.getElementById('view-360-image').src = '';

                    // Destroy Pannellum viewer if it exists
                    const viewer = document.getElementById('pannellum-viewer');
                    if (viewer && viewer.firstChild) {
                        viewer.innerHTML = '';
                    }

                    resetViewState();
                }
            });

            // Handle fullscreen toggle
            fullscreenBtn.addEventListener('click', function() {
                if (document.fullscreenElement) {
                    document.exitFullscreen();
                } else {
                    viewContainer.requestFullscreen();
                }
            });

            // Add event listeners to existing 360 preview buttons
            document.querySelectorAll('.open-360-view').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    open360Preview(this);
                });
            });
        });
    </script>
</x-admin-layout>
