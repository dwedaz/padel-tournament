<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Game Details: ' . $game->name) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Game Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $game->formatted_name }}</h3>
                            <p class="text-gray-600 mt-1">Match {{ $game->game_set }} - Set {{ $game->set }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $game->created_at->format('F d, Y \a\t H:i') }}</p>
                        </div>
                        <div class="space-x-2">
                            <a href="{{ route('games.edit', $game) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Edit Game
                            </a>
                            <a href="{{ route('games.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Back to Games
                            </a>
                        </div>
                    </div>

                    <!-- Score Display -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <div class="flex items-center justify-center space-x-8">
                            <!-- Team 1 -->
                            <div class="text-center">
                                <div class="text-sm text-gray-500 mb-2">{{ $game->team1->group->name }}</div>
                                <div class="text-xl font-bold text-blue-600 mb-2">{{ $game->team1->name }}</div>
                                <div class="text-4xl font-bold text-blue-600">{{ $game->team1_score }}</div>
                                @if($game->who_is_serving === 'team1')
                                    <div class="mt-2">
                                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                            Serving
                                        </span>
                                    </div>
                                @endif
                                @if($game->winner_id == $game->team1->id)
                                    <div class="mt-2">
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                                            Winner
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- VS -->
                            <div class="text-2xl font-bold text-gray-400">VS</div>

                            <!-- Team 2 -->
                            <div class="text-center">
                                <div class="text-sm text-gray-500 mb-2">{{ $game->team2->group->name }}</div>
                                <div class="text-xl font-bold text-red-600 mb-2">{{ $game->team2->name }}</div>
                                <div class="text-4xl font-bold text-red-600">{{ $game->team2_score }}</div>
                                @if($game->who_is_serving === 'team2')
                                    <div class="mt-2">
                                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                            Serving
                                        </span>
                                    </div>
                                @endif
                                @if($game->winner_id == $game->team2->id)
                                    <div class="mt-2">
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                                            Winner
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($game->status)
                            <div class="text-center mt-4">
                                <span class="px-4 py-2 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                                    Status: {{ $game->status }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Game Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-lg font-medium mb-4">Game Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Match Type</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $game->formatted_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Match</label>
                            <p class="mt-1 text-sm text-gray-900">Match {{ $game->game_set }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Set</label>
                            <p class="mt-1 text-sm text-gray-900">Set {{ $game->set }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $game->status ?: 'Not set' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Currently Serving</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($game->who_is_serving === 'team1')
                                    {{ $game->team1->name }}
                                @elseif($game->who_is_serving === 'team2')
                                    {{ $game->team2->name }}
                                @else
                                    Not set
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Created At</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $game->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $game->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Games Between Same Teams -->
            @if($otherGames->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-lg font-medium mb-4">Other Games Between {{ $game->team1->name }} and {{ $game->team2->name }}</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                        Set
                                    </th>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                        Match Type
                                    </th>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                        Score
                                    </th>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($otherGames as $otherGame)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                M{{ $otherGame->game_set }}-S{{ $otherGame->set }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $otherGame->formatted_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm">
                                                @if($otherGame->team1_id === $game->team1_id)
                                                    <span class="font-medium text-blue-600">{{ $otherGame->team1_score }}</span>
                                                    <span class="text-gray-400 mx-2">-</span>
                                                    <span class="font-medium text-red-600">{{ $otherGame->team2_score }}</span>
                                                @else
                                                    <span class="font-medium text-red-600">{{ $otherGame->team1_score }}</span>
                                                    <span class="text-gray-400 mx-2">-</span>
                                                    <span class="font-medium text-blue-600">{{ $otherGame->team2_score }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $otherGame->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('games.show', $otherGame) }}" class="text-indigo-600 hover:text-indigo-900">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
