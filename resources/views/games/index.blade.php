<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Games') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Tournament Games</h3>
                        <a href="{{ route('games.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Create New Game
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Filters Section -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <form method="GET" action="{{ route('games.index') }}" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <!-- Search -->
                                <div>
                                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                           placeholder="Team names, match type..." 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <!-- Status Filter -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">All Statuses</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Match Type Filter -->
                                <div>
                                    <label for="match_type" class="block text-sm font-medium text-gray-700 mb-1">Match Type</label>
                                    <select name="match_type" id="match_type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">All Match Types</option>
                                        @foreach($matchTypes as $matchType)
                                            <option value="{{ $matchType }}" {{ request('match_type') == $matchType ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('-', ' ', $matchType)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Field Filter -->
                                <div>
                                    <label for="field_id" class="block text-sm font-medium text-gray-700 mb-1">Field</label>
                                    <select name="field_id" id="field_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">All Fields</option>
                                        @foreach($fields as $field)
                                            <option value="{{ $field->id }}" {{ request('field_id') == $field->id ? 'selected' : '' }}>
                                                {{ $field->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Team Filter -->
                                <div>
                                    <label for="team_id" class="block text-sm font-medium text-gray-700 mb-1">Team</label>
                                    <select name="team_id" id="team_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">All Teams</option>
                                        @foreach($teams as $team)
                                            <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                                                {{ $team->name }} ({{ $team->group->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Sort By -->
                                <div>
                                    <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                                    <select name="sort_by" id="sort_by" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date Created</option>
                                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Match Type</option>
                                        <option value="game_set" {{ request('sort_by') == 'game_set' ? 'selected' : '' }}>Match Number</option>
                                        <option value="set" {{ request('sort_by') == 'set' ? 'selected' : '' }}>Set Number</option>
                                        <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                                    </select>
                                </div>

                                <!-- Sort Order -->
                                <div>
                                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                                    <select name="sort_order" id="sort_order" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                                        <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                    </select>
                                </div>

                                <!-- Per Page -->
                                <div>
                                    <label for="per_page" class="block text-sm font-medium text-gray-700 mb-1">Per Page</label>
                                    <select name="per_page" id="per_page" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15</option>
                                        <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Apply Filters
                                </button>
                                <a href="{{ route('games.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Clear Filters
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Filter Summary -->
                    @if(request()->hasAny(['search', 'status', 'match_type', 'field_id', 'team_id']))
                        <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div class="flex flex-wrap gap-2">
                                    <span class="text-sm font-medium text-blue-700">Active filters:</span>
                                    @if(request('search'))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Search: "{{ request('search') }}"
                                        </span>
                                    @endif
                                    @if(request('status'))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Status: {{ ucfirst(request('status')) }}
                                        </span>
                                    @endif
                                    @if(request('match_type'))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Type: {{ ucfirst(str_replace('-', ' ', request('match_type'))) }}
                                        </span>
                                    @endif
                                    @if(request('field_id'))
                                        @php $selectedField = $fields->firstWhere('id', request('field_id')); @endphp
                                        @if($selectedField)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Field: {{ $selectedField->name }}
                                            </span>
                                        @endif
                                    @endif
                                    @if(request('team_id'))
                                        @php $selectedTeam = $teams->firstWhere('id', request('team_id')); @endphp
                                        @if($selectedTeam)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Team: {{ $selectedTeam->name }}
                                            </span>
                                        @endif
                                    @endif
                                </div>
                                <a href="{{ route('games.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                    Clear all
                                </a>
                            </div>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                        Match
                                    </th>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                        Teams
                                    </th>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                        Score
                                    </th>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                        Field
                                    </th>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                        Set
                                    </th>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($games as $game)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $game->formatted_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $game->created_at->format('M d, Y H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <div class="text-sm">
                                                    <span class="font-medium text-blue-600">{{ $game->team1->name }}</span>
                                                    <span class="text-gray-400">vs</span>
                                                    <span class="font-medium text-red-600">{{ $game->team2->name }}</span>
                                                </div>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $game->team1->group->name }} vs {{ $game->team2->group->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-lg font-bold">
                                                <span class="text-blue-600">{{ $game->team1_score }}</span>
                                                <span class="text-gray-400 mx-2">-</span>
                                                <span class="text-red-600">{{ $game->team2_score }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($game->status)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    {{ $game->status }}
                                                </span>
                                            @endif
                                            @if($game->who_is_serving)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Serving: {{ $game->who_is_serving === 'team1' ? $game->team1->name : $game->team2->name }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($game->field)
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8">
                                                        <div class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center text-white font-bold text-xs">
                                                            {{ strtoupper(substr($game->field->name, 0, 1)) }}
                                                        </div>
                                                    </div>
                                                    <div class="ml-2">
                                                        <div class="text-sm font-medium text-gray-900">{{ $game->field->name }}</div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-sm">No Field</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Set {{ $game->set }} - Match {{ $game->game_set }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('games.show', $game) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                            <a href="{{ route('games.edit', $game) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Edit</a>
                                            <form action="{{ route('games.destroy', $game) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" 
                                                        onclick="return confirm('Are you sure you want to delete this game?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No games found. Create your first game!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Links -->
                    <div class="mt-6">
                        {{ $games->links() }}
                    </div>

                    <!-- Results Summary -->
                    <div class="mt-4 flex justify-between items-center text-sm text-gray-600">
                        <div>
                            <p><strong>Showing {{ $games->firstItem() ?? 0 }} to {{ $games->lastItem() ?? 0 }} of {{ $games->total() }} games</strong></p>
                        </div>
                        @if(request()->hasAny(['search', 'status', 'match_type', 'field_id', 'team_id']))
                            <div>
                                <p class="text-blue-600">Filters applied</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit form when select fields change (except search)
            const selects = ['status', 'match_type', 'field_id', 'team_id', 'sort_by', 'sort_order', 'per_page'];
            
            selects.forEach(function(selectId) {
                const select = document.getElementById(selectId);
                if (select) {
                    select.addEventListener('change', function() {
                        this.form.submit();
                    });
                }
            });

            // Search input with debounce
            const searchInput = document.getElementById('search');
            let searchTimeout;
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const form = this.form;
                    
                    searchTimeout = setTimeout(function() {
                        form.submit();
                    }, 500); // Wait 500ms after user stops typing
                });

                // Allow immediate search on Enter
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        clearTimeout(searchTimeout);
                        this.form.submit();
                    }
                });
            }

            // Add loading state to form
            const form = document.querySelector('form[action*="games"]');
            if (form) {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = 'Loading...';
                    }
                });
            }
        });
    </script>
</x-app-layout>
