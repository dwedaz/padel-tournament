<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Game - Set ' . request('set') . ' - Match ' . request('game_set')) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('games.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Team 1 -->
                            <div>
                                <label for="team1_id" class="block text-sm font-medium text-gray-700">Team 1</label>
                                <select name="team1_id" 
                                        id="team1_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('team1_id') border-red-500 @enderror"
                                        required>
                                    <option value="">Select Team 1</option>
                                    @php
                                        $teamsByGroup = $teams->groupBy('group.name');
                                    @endphp
                                    @foreach($teamsByGroup as $groupName => $groupTeams)
                                        <optgroup label="{{ $groupName }}">
                                            @foreach($groupTeams as $team)
                                                <option value="{{ $team->id }}" {{ old('team1_id') == $team->id ? 'selected' : '' }}>
                                                    {{ $team->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('team1_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Team 2 -->
                            <div>
                                <label for="team2_id" class="block text-sm font-medium text-gray-700">Team 2</label>
                                <select name="team2_id" 
                                        id="team2_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('team2_id') border-red-500 @enderror"
                                        required>
                                    <option value="">Select Team 2</option>
                                    @foreach($teamsByGroup as $groupName => $groupTeams)
                                        <optgroup label="{{ $groupName }}">
                                            @foreach($groupTeams as $team)
                                                <option value="{{ $team->id }}" {{ old('team2_id') == $team->id ? 'selected' : '' }}>
                                                    {{ $team->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('team2_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Game Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Game Type</label>
                            <select name="name" 
                                    id="name" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                                    required>
                                <option value="">Select Game Type</option>
                                <option value="qualification" {{ old('name') === 'qualification' ? 'selected' : '' }}>Qualification</option>
                                <option value="semi-final" {{ old('name') === 'semi-final' ? 'selected' : '' }}>Semi-Final</option>
                                <option value="final" {{ old('name') === 'final' ? 'selected' : '' }}>Final</option>
                            </select>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Field -->
                        <div>
                            <label for="field_id" class="block text-sm font-medium text-gray-700">Field</label>
                            <select name="field_id" 
                                    id="field_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('field_id') border-red-500 @enderror"
                                    required>
                                <option value="">Select Field</option>
                                @foreach($fields as $field)
                                    <option value="{{ $field->id }}" {{ old('field_id') == $field->id ? 'selected' : '' }}>
                                        {{ $field->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('field_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Team 1 Score -->
                            <div>
                                <label for="team1_score" class="block text-sm font-medium text-gray-700">Team 1 Score</label>
                                <input type="number" 
                                       name="team1_score" 
                                       id="team1_score" 
                                       value="{{ old('team1_score', 0) }}"
                                       min="0" 
                                       max="999"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('team1_score') border-red-500 @enderror">
                                @error('team1_score')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Team 2 Score -->
                            <div>
                                <label for="team2_score" class="block text-sm font-medium text-gray-700">Team 2 Score</label>
                                <input type="number" 
                                       name="team2_score" 
                                       id="team2_score" 
                                       value="{{ old('team2_score', 0) }}"
                                       min="0" 
                                       max="999"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('team2_score') border-red-500 @enderror">
                                @error('team2_score')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Set Number -->
                            <div>
                                <label for="set" class="block text-sm font-medium text-gray-700">Set Number</label>
                                <input type="number" 
                                       name="set" 
                                       id="set" 
                                       value="{{ old('set', 1) }}"
                                       min="1" 
                                       max="99"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('set') border-red-500 @enderror"
                                       required>
                                @error('set')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status (Optional)</label>
                                <input type="text" 
                                       name="status" 
                                       id="status" 
                                       value="{{ old('status') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('status') border-red-500 @enderror"
                                       placeholder="e.g., Tie Break, In Progress">
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Who is Serving -->
                            <div>
                                <label for="who_is_serving" class="block text-sm font-medium text-gray-700">Who is Serving (Optional)</label>
                                <select name="who_is_serving" 
                                        id="who_is_serving" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('who_is_serving') border-red-500 @enderror">
                                    <option value="">Not set</option>
                                    <option value="team1" {{ old('who_is_serving') === 'team1' ? 'selected' : '' }}>Team 1</option>
                                    <option value="team2" {{ old('who_is_serving') === 'team2' ? 'selected' : '' }}>Team 2</option>
                                </select>
                                @error('who_is_serving')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Winner Team -->
                            <div>
                                <label for="winner_id" class="block text-sm font-medium text-gray-700">Winner (Optional)</label>
                                <select name="winner_id" 
                                        id="winner_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('winner_id') border-red-500 @enderror">
                                    <option value="">Not decided</option>
                                    @php
                                        $teamsByGroup = $teams->groupBy('group.name');
                                    @endphp
                                    @foreach($teamsByGroup as $groupName => $groupTeams)
                                        <optgroup label="{{ $groupName }}">
                                            @foreach($groupTeams as $team)
                                                <option value="{{ $team->id }}" {{ old('winner_id') == $team->id ? 'selected' : '' }}>
                                                    {{ $team->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('winner_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Empty space for alignment -->
                            <div></div>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        <strong>Note:</strong> The match number (game_set) will be automatically assigned. 
                                        If the same teams and set number combination already exists, the match number will be incremented automatically.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline">
                                Create Game
                            </button>
                            <a href="{{ route('games.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
