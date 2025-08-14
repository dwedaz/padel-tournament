<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Team Details: ' . $team->name) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-16 w-16">
                                <div class="h-16 w-16 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-xl">
                                    {{ strtoupper(substr($team->name, 0, 2)) }}
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-2xl font-medium">{{ $team->name }}</h3>
                                <p class="text-gray-600">{{ $team->group->name }}</p>
                            </div>
                        </div>
                        <div class="space-x-2">
                            <a href="{{ route('teams.edit', $team) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Edit Team
                            </a>
                            <a href="{{ route('teams.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Back to Teams
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Team Information -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="text-lg font-medium mb-4">Team Information</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Team Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $team->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Group</label>
                                    <p class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $team->group->name }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Created At</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $team->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $team->updated_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Game Statistics -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h4 class="text-lg font-medium mb-4">Game Statistics</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Home Games</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $team->homeGames->count() }} games</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Away Games</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $team->awayGames->count() }} games</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Total Games</label>
                                    <p class="mt-1 text-sm text-gray-900 font-semibold">
                                        {{ $team->homeGames->count() + $team->awayGames->count() }} games
                                    </p>
                                </div>
                                @php
                                    $allTeamGames = $team->homeGames->merge($team->awayGames);
                                    $wins = $allTeamGames->filter(function($game) use ($team) {
                                        return $game->winner_id == $team->id;
                                    })->count();
                                    $losses = $allTeamGames->filter(function($game) use ($team) {
                                        return $game->winner_id && $game->winner_id != $team->id;
                                    })->count();
                                @endphp
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Wins</label>
                                    <p class="mt-1 text-sm font-semibold">
                                        <span class="text-green-600">{{ $wins }} wins</span>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Losses</label>
                                    <p class="mt-1 text-sm font-semibold">
                                        <span class="text-red-600">{{ $losses }} losses</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Matches -->
                    <div class="mt-8">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-lg font-medium">Recent Matches</h4>
                            @if($paginatedMatches->total() > 0)
                                <p class="text-sm text-gray-600">
                                    Showing {{ $paginatedMatches->firstItem() }}-{{ $paginatedMatches->lastItem() }} 
                                    of {{ $paginatedMatches->total() }} matches
                                </p>
                            @endif
                        </div>
                        
                        @if($paginatedMatches->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                                Match Type
                                            </th>
                                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                                Opponent
                                            </th>
                                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                                Set
                                            </th>
                                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                                Match Score
                                            </th>
                                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                                Games Played
                                            </th>
                                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                                Result
                                            </th>
                                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                                                Date
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($paginatedMatches as $match)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $match['match_type'] }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <div>
                                                        <div class="font-medium">vs {{ $match['opponent_name'] }}</div>
                                                        <div class="text-xs text-gray-500">{{ $match['opponent_group'] }}</div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        Set {{ $match['set'] }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="font-semibold text-lg">{{ $match['team_wins'] }}</span>
                                                        <span class="text-gray-400">-</span>
                                                        <span class="font-semibold text-lg">{{ $match['opponent_wins'] }}</span>
                                                        @if($match['match_winner'] === 'ongoing')
                                                            <span class="text-xs text-yellow-600 ml-1">(Best of 7)</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ count($match['games']) }} games
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $match['result_class'] }}">
                                                        {{ $match['result_text'] }}
                                                        @if($match['match_winner'] === 'team')
                                                            {{ $match['team_wins'] }}-{{ $match['opponent_wins'] }}
                                                        @elseif($match['match_winner'] === 'opponent')
                                                            {{ $match['team_wins'] }}-{{ $match['opponent_wins'] }}
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $match['latest_date']->format('d M Y') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            @if($paginatedMatches->hasPages())
                                <div class="mt-4">
                                    {{ $paginatedMatches->links() }}
                                </div>
                            @endif
                        @else
                            <p class="text-gray-500">No matches played yet.</p>
                        @endif
                    </div>

                    <!-- Head to Head Summary -->
                    @if($allTeamGames->count() > 0)
                    <div class="mt-8">
                        <h4 class="text-lg font-medium mb-4">Head to Head Summary</h4>
                        @php
                            // Group games by opponent and calculate win/loss records
                            $opponentStats = [];
                            
                            foreach ($allTeamGames as $game) {
                                $opponentId = $game->team1_id == $team->id ? $game->team2_id : $game->team1_id;
                                $opponentName = $game->team1_id == $team->id ? $game->team2->name : $game->team1->name;
                                
                                if (!isset($opponentStats[$opponentId])) {
                                    $opponentStats[$opponentId] = [
                                        'name' => $opponentName,
                                        'wins' => 0,
                                        'losses' => 0,
                                        'draws' => 0,
                                        'total' => 0
                                    ];
                                }
                                
                                $opponentStats[$opponentId]['total']++;
                                
                                if ($game->winner_id) {
                                    if ($game->winner_id == $team->id) {
                                        $opponentStats[$opponentId]['wins']++;
                                    } else {
                                        $opponentStats[$opponentId]['losses']++;
                                    }
                                } else {
                                    $opponentStats[$opponentId]['draws']++;
                                }
                            }
                            
                            // Sort by opponent name
                            uasort($opponentStats, function($a, $b) {
                                return strcmp($a['name'], $b['name']);
                            });
                        @endphp
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($opponentStats as $stats)
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h5 class="font-medium text-gray-900">{{ $stats['name'] }}</h5>
                                        <span class="text-sm text-gray-500">{{ $stats['total'] }} games</span>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">Wins:</span>
                                            <span class="font-semibold text-green-600">{{ $stats['wins'] }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">Losses:</span>
                                            <span class="font-semibold text-red-600">{{ $stats['losses'] }}</span>
                                        </div>
                                        @if($stats['draws'] > 0)
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">Draws:</span>
                                            <span class="font-semibold text-gray-600">{{ $stats['draws'] }}</span>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Win percentage bar -->
                                    @php
                                        $winPercentage = $stats['total'] > 0 ? ($stats['wins'] / $stats['total']) * 100 : 0;
                                    @endphp
                                    <div class="mt-3">
                                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                                            <span>Win Rate</span>
                                            <span>{{ number_format($winPercentage, 1) }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $winPercentage }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
