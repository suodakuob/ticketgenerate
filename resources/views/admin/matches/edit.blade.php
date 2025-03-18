<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Match') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.matches.update', $match) }}" class="space-y-6" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Match Name -->
                        <div>
                            <x-input-label for="name" :value="__('Match Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
                                        :value="old('name', $match->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Home Team -->
                        <div>
                            <x-input-label for="home_team" :value="__('Home Team')" />
                            <x-text-input id="home_team" name="home_team" type="text" class="mt-1 block w-full" 
                                        :value="old('home_team', $match->home_team)" required />
                            <x-input-error :messages="$errors->get('home_team')" class="mt-2" />
                        </div>

                        <!-- Away Team -->
                        <div>
                            <x-input-label for="away_team" :value="__('Away Team')" />
                            <x-text-input id="away_team" name="away_team" type="text" class="mt-1 block w-full" 
                                        :value="old('away_team', $match->away_team)" required />
                            <x-input-error :messages="$errors->get('away_team')" class="mt-2" />
                        </div>

                        <!-- Match Date -->
                        <div>
                            <x-input-label for="match_date" :value="__('Match Date')" />
                            <x-text-input id="match_date" name="match_date" type="date" class="mt-1 block w-full" 
                                        :value="old('match_date', $match->match_date)" required />
                            <x-input-error :messages="$errors->get('match_date')" class="mt-2" />
                        </div>

                        <!-- Match Time -->
                        <div>
                            <x-input-label for="match_time" :value="__('Match Time')" />
                            <x-text-input id="match_time" name="match_time" type="time" class="mt-1 block w-full" 
                                        :value="old('match_time', $match->match_time)" required />
                            <x-input-error :messages="$errors->get('match_time')" class="mt-2" />
                        </div>

                        <!-- Stadium -->
                        <div>
                            <x-input-label for="stadium" :value="__('Stadium')" />
                            <x-text-input id="stadium" name="stadium" type="text" class="mt-1 block w-full" 
                                        :value="old('stadium', $match->stadium)" required />
                            <x-input-error :messages="$errors->get('stadium')" class="mt-2" />
                        </div>

                        <!-- Stadium Image -->
                        <div>
                            <x-input-label for="stadium_image" :value="__('Stadium Image')" />
                            <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10">
                                <div class="text-center">
                                    @if($match->stadium_image)
                                        <div class="mb-4">
                                            <img src="{{ asset('storage/' . $match->stadium_image) }}" 
                                                 alt="Current stadium image" 
                                                 class="mx-auto h-32 w-auto object-cover rounded-lg shadow-md">
                                            <p class="mt-2 text-sm text-gray-500">Current image: {{ basename($match->stadium_image) }}</p>
                                        </div>
                                    @endif
                                    <div class="mt-4 flex text-sm leading-6 text-gray-600">
                                        <label for="stadium_image" class="relative cursor-pointer rounded-md bg-white font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                            <span>Upload a new image</span>
                                            <input id="stadium_image" name="stadium_image" type="file" class="sr-only" accept="image/*">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs leading-5 text-gray-600">PNG, JPG, GIF up to 2MB</p>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('stadium_image')" class="mt-2" />
                        </div>

                        <!-- Ticket Type -->
                        <div>
                            <x-input-label for="ticket_type" :value="__('Ticket Type')" />
                            <select id="ticket_type" name="ticket_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="Standard" {{ old('ticket_type', $match->ticket_type) === 'Standard' ? 'selected' : '' }}>Standard</option>
                                <option value="VIP" {{ old('ticket_type', $match->ticket_type) === 'VIP' ? 'selected' : '' }}>VIP</option>
                                <option value="Premium" {{ old('ticket_type', $match->ticket_type) === 'Premium' ? 'selected' : '' }}>Premium</option>
                            </select>
                            <x-input-error :messages="$errors->get('ticket_type')" class="mt-2" />
                        </div>

                        <!-- Available Tickets -->
                        <div>
                            <x-input-label for="available_tickets" :value="__('Available Tickets')" />
                            <x-text-input id="available_tickets" name="available_tickets" type="number" min="0" class="mt-1 block w-full" 
                                        :value="old('available_tickets', $match->available_tickets)" required />
                            <x-input-error :messages="$errors->get('available_tickets')" class="mt-2" />
                        </div>

                        <!-- Ticket Price -->
                        <div>
                            <x-input-label for="ticket_price" :value="__('Ticket Price')" />
                            <div class="flex items-center">
                                <span class="text-gray-500 mr-2">£</span>
                                <x-text-input id="ticket_price" name="ticket_price" type="number" step="0.01" min="0" class="mt-1 block w-full" 
                                            :value="old('ticket_price', $match->ticket_price)" required />
                            </div>
                            <x-input-error :messages="$errors->get('ticket_price')" class="mt-2" />
                        </div>

                        <!-- Match Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('description', $match->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Match Status -->
                        <div>
                            <x-input-label for="match_status" :value="__('Match Status')" />
                            <select id="match_status" name="match_status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="scheduled" {{ old('match_status', $match->match_status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="live" {{ old('match_status', $match->match_status) === 'live' ? 'selected' : '' }}>Live</option>
                                <option value="completed" {{ old('match_status', $match->match_status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('match_status', $match->match_status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            <x-input-error :messages="$errors->get('match_status')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-secondary-button onclick="window.history.back()" type="button" class="mr-3">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Update Match') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
