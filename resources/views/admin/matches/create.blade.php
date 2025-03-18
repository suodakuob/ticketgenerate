<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Match') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.matches.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Match Name -->
                        <div>
                            <x-input-label for="name" :value="__('Match Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Teams -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="home_team" :value="__('Home Team')" />
                                <x-text-input id="home_team" class="block mt-1 w-full" type="text" name="home_team" :value="old('home_team')" required />
                                <x-input-error :messages="$errors->get('home_team')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="away_team" :value="__('Away Team')" />
                                <x-text-input id="away_team" class="block mt-1 w-full" type="text" name="away_team" :value="old('away_team')" required />
                                <x-input-error :messages="$errors->get('away_team')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Date and Time -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="match_date" :value="__('Match Date')" />
                                <x-text-input id="match_date" class="block mt-1 w-full" type="date" name="match_date" :value="old('match_date')" required />
                                <x-input-error :messages="$errors->get('match_date')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="match_time" :value="__('Match Time')" />
                                <x-text-input id="match_time" class="block mt-1 w-full" type="time" name="match_time" :value="old('match_time')" required />
                                <x-input-error :messages="$errors->get('match_time')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Stadium -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="stadium" :value="__('Stadium')" />
                                <x-text-input id="stadium" class="block mt-1 w-full" type="text" name="stadium" :value="old('stadium')" required />
                                <x-input-error :messages="$errors->get('stadium')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="stadium_image" :value="__('Stadium Image')" />
                                <input id="stadium_image" name="stadium_image" type="file" accept="image/*" class="block w-full text-sm text-gray-500 mt-1
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100" required />
                                <x-input-error :messages="$errors->get('stadium_image')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Ticket Information -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="ticket_type" :value="__('Ticket Type')" />
                                <select id="ticket_type" name="ticket_type" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="Standard" {{ old('ticket_type') == 'Standard' ? 'selected' : '' }}>Standard</option>
                                    <option value="VIP" {{ old('ticket_type') == 'VIP' ? 'selected' : '' }}>VIP</option>
                                    <option value="Premium" {{ old('ticket_type') == 'Premium' ? 'selected' : '' }}>Premium</option>
                                </select>
                                <x-input-error :messages="$errors->get('ticket_type')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="ticket_price" :value="__('Ticket Price')" />
                                <x-text-input id="ticket_price" class="block mt-1 w-full" type="number" name="ticket_price" :value="old('ticket_price')" step="0.01" min="0" required />
                                <x-input-error :messages="$errors->get('ticket_price')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="available_tickets" :value="__('Available Tickets')" />
                                <x-text-input id="available_tickets" class="block mt-1 w-full" type="number" name="available_tickets" :value="old('available_tickets')" min="0" required />
                                <x-input-error :messages="$errors->get('available_tickets')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Match Status -->
                        <div>
                            <x-input-label for="match_status" :value="__('Match Status')" />
                            <select id="match_status" name="match_status" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="scheduled" {{ old('match_status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="live" {{ old('match_status') == 'live' ? 'selected' : '' }}>Live</option>
                                <option value="completed" {{ old('match_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('match_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            <x-input-error :messages="$errors->get('match_status')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Create Match') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
